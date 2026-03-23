<?php

namespace App\Http\Controllers;

use App\Models\tbl_users;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\OtpHelper;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod("post")) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function validate_user(Request $request)
    {
        $status = 'failed';
        $redirct_url = "";
        $errors = null;
        $session_token = null;

        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required',
                'loginType' => 'required|in:admin_type,candidate_type',
                'captcha' => 'required|captcha'
            ],
            [
                'captcha.captcha' => 'कैप्चा मान्य नहीं है, इसे दोबारा भरें ।',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'msg' => 'Validation error',
                'errors' => $validator->errors()
            ]);
        }
        // dd($request->password);

        if ($errors == null) {
            if ($request->loginType === 'admin_type' && !in_array($request->username, ['Super_admin', 'super_admin'])) {
                $query = User::where('Mobile_Number', $request->username)
                    ->where('Role', '!=', 'Candidate');
            } else {
                $query = User::where('Mobile_Number', $request->username)
                    ->where('Role', '=', 'Candidate');
            }
            $userCheck = $query->get();
            // if ($request->loginType === 'admin_type') {
            if ($userCheck->isNotEmpty()) {
                $is_super_admin = false;
                if ($userCheck[0]->Role == 'Super_admin' && $userCheck[0]->admin_district_id == 0) {
                    // For Super_admin with district_id 0, skip session conflict check
                    $is_super_admin = true;
                    session(['is_super_admin' => true]);
                } else {
                    session()->forget('is_super_admin');
                }


                // dd($password);



                if ($userCheck[0]->Role == 'Candidate') {

                    $request->session()->put([
                        'sess_id' => $userCheck[0]->ID,
                        'uid' => $userCheck[0]->ID,
                        'sess_fname' => $userCheck[0]->Full_Name,
                        'sess_mobile' => $userCheck[0]->Mobile_Number,
                        'sess_role' => $userCheck[0]->Role,
                        'admin_pic' => $userCheck[0]->admin_pic,
                        'district_id' => $userCheck[0]->admin_district_id,
                        'user_id' => $userCheck[0]->Applicant_ID,
                        'st_login_skip_token' => false
                    ]);

                    // Generate a secure session token and store in master_user using a raw query
                    try {
                        $token = bin2hex(random_bytes(32));
                        $session_token = $token;
                        DB::statement('UPDATE master_user SET session_token = ? WHERE ID = ?', [$token, $userCheck[0]->ID]);
                        // also store token in session for quick access
                        $request->session()->put('session_token', $token);
                    } catch (\Exception $e) {
                        // If token generation or DB update fails, continue without blocking login flow
                        $session_token = null;
                    }

                    // OTP not verified
                    $status = 'OtpVerify';
                    $mobile_number = $request->username;
                    $encryptedMobile = Crypt::encryptString($mobile_number);

                    return response()->json([
                        'status' => $status,
                        'encrypted_mobile' => $encryptedMobile,
                        'session_token' => $session_token
                    ]);
                } else if ($userCheck[0]->Role == 'Super_admin' || $userCheck[0]->Role == 'Admin' || $userCheck[0]->Role == 'Supervisor' || $userCheck[0]->Role == 'CDPO') {

                    $dist_name = DB::table('master_district')
                        ->select('name')
                        ->where('District_Code_LGD', '=', $userCheck[0]->admin_district_id)
                        ->value('name'); // This will directly return the name as a string

                    $project_name = DB::table('master_projects')
                        ->select('project')
                        ->where('project_code', '=', $userCheck[0]->project_id)
                        ->value('project'); // This will directly return the name as a string


                    // Before finalizing login, check if user has an existing session token in DB
                    $dbToken = DB::table('master_user')->where('ID', $userCheck[0]->ID)->value('session_token');
                    $currentSessionToken = $request->session()->get('session_token');

                    // If DB token exists and is different from current session token -> need confirmation
                    $request->session()->put('UserLoginData', [
                        'username' => $request->username,
                        'loginType' => $request->loginType,
                        'ip' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    ]);
                    session(['AlreadyLogin' => '0', 'UserCheck' => $userCheck[0]->ID]);

                    $is_super_admin = false;
                    if ($userCheck[0]->Role == 'Super_admin' && $userCheck[0]->admin_district_id == 0) {
                        // For Super_admin with district_id 0, skip session conflict check
                        $is_super_admin = true;
                        session(['is_super_admin' => true]);
                    } else {
                        session()->forget('is_super_admin');
                    }

                    if (!empty($dbToken) && $dbToken !== $currentSessionToken) {
                        // Store pending login UID in session so confirmation page/handler can finalize
                        $request->session()->put('pending_login_uid', $userCheck[0]->ID);


                        session(['AlreadyLogin' => '1']);
                        // OTP not verified
                        $status = 'OtpVerify';
                        $mobile_number = $request->username;
                        $encryptedMobile = Crypt::encryptString($mobile_number);

                        return response()->json([
                            'status' => $status,
                            'encrypted_mobile' => $encryptedMobile,
                            'session_token' => $session_token,
                            'is_super_admin' => $is_super_admin,
                        ]);
                    }

                    $status = 'OtpVerify';
                    $mobile_number = $request->username;
                    $encryptedMobile = Crypt::encryptString($mobile_number);

                    return response()->json([
                        'status' => $status,
                        'encrypted_mobile' => $encryptedMobile,
                        'session_token' => $session_token,
                        'is_super_admin' => $is_super_admin,
                    ]);
                }
            } else {
                // No matching user
                $msg = 'कृपया सही यूजर आईडी व मोबाइल न. दर्ज करें |';
            }
            // }


            if ($status == 'failed') {
                $captcha = (new CaptchaServiceController)->reloadCaptcha();
            }

            return response()->json(array('status' => $status, 'msg' => @$msg, 'errors' => $errors, 'captcha' => @$captcha->original['captcha'], 'url' => @$redirct_url, 'session_token' => @$session_token));
        }
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    public function role_wise_redirection()
    {
        $role = session('sess_role');

        if ($role === 'Candidate') {
            return '/candidate/candidate-dashboard';
        }

        if ($role === 'Super_admin') {
            return '/admin/admin-dashboard';
        }

        if (in_array($role, ['Admin', 'Supervisor', 'CDPO'])) {
            return '/examinor/examinor-dashboard';
        }

        return '/logout';
    }


    public function logout(Request $request)
    {
        // Capture uid first so we can clear DB token
        $uid = $request->session()->get('uid') ?? null;

        // Clear specific session variables
        Session::forget('sess_role');
        Session::forget('is_verified');
        Session::forget('sess_id');
        Session::forget('sess_fname');
        Session::forget('district_id');
        Session::forget('designation');
        Session::forget('project_name');
        Session::forget('dist_name');
        Session::forget('role');
        Session::forget('uid');

        // Also clear session_token in DB for the logged out user (before flushing session)
        try {
            if ($uid) {
                DB::statement('UPDATE master_user SET session_token = NULL WHERE ID = ?', [$uid]);
            }
        } catch (\Exception $e) {
            // ignore DB errors during logout
        }

        // Clear all session data
        Session::flush();

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    public function changePassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password',
            ], [
                'current_password.required' => 'कृपया वर्तमान पासवर्ड दर्ज करें।',
                'new_password.required' => 'कृपया नया पासवर्ड दर्ज करें।',
                'new_password.min' => 'नया पासवर्ड कम से कम 6 अक्षरों का होना चाहिए।',
                'confirm_password.required' => 'कृपया नया पासवर्ड पुष्टि करें।',
                'confirm_password.same' => 'नया पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।',
            ]);

            $uid = session('uid');

            if (!$uid) {
                return response()->json([
                    'error' => 'यूजर नहीं मिला। कृपया लॉगिन करें।'
                ], 401);
            }

            $user = DB::table('master_user')->where('ID', $uid)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'यूज़र डेटा नहीं मिला।'
                ], 404);
            }

            // Match hashed passwords (SHA-512)
            $inputCurrentPassword = $request->current_password;
            if ($inputCurrentPassword !== $user->Password) {
                // dd($inputCurrentPassword, $user->Password);
                return response()->json([
                    'error' => 'वर्तमान पासवर्ड गलत है। कृपया सही पासवर्ड दर्ज करें।'
                ], 422);
            }

            DB::beginTransaction();
            try {
                DB::table('master_user')
                    ->where('ID', $uid)
                    ->update([
                        'Password' => $request->new_password,
                        'is_password_change' => '1'
                    ]);


                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'पासवर्ड सफलतापूर्वक अपडेट हो गया है।'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'कुछ त्रुटि हुई है। कृपया पुनः प्रयास करें।'
                ], 500);
            }
        }

        return view('change_password');
    }

    public function privacy()
    {
        return view("login/terms");
    }

    public function reset_old_password($mobile_no)
    {
        $sts = false;
        $newPassword = "wcd@2025";
        $user = DB::table("master_user")->where("Mobile_Number", $mobile_no)->first();
        if (!$user) {
            return "<script>alert('User not found!'); window.history.back();</script>";
        } else {

            $encryptionKey = Config::get('salt.STATIC_SALT'); // same key jo tum JS me use karte ho
            $securePwd = $encryptionKey . $newPassword . $encryptionKey;
            $hashedPassword = hash('sha512', $securePwd);

            DB::beginTransaction();
            try {
                $sts = DB::table('master_user')->where('Mobile_Number', $mobile_no)
                    ->update([
                        'Password' => $hashedPassword,
                        'is_password_change' => '0'
                    ]);
                if ($sts) {
                    DB::commit();
                    return "<script>
                            alert('Password reset successfully! Your new password is: {$newPassword}');
                            // setTimeout(function() {
                                window.location.href = '/login';
                            // }, 3000);
                        </script>";
                } else {
                    return "<script>alert('Some error occurred! Please try again.'); window.history.back();</script>";
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return "<script>alert('Some error occurred! Please try again.'); window.history.back();</script>";
            }
        }
    }

    /**
     * Show confirmation page when another device has an active session
     */
    public function confirmLogin()
    {
        return view('login.confirm_login');
    }

    /**
     * Handle user's confirmation choice
     * If user agrees, invalidate old token and finalize login
     */
    public function confirmLoginHandler(Request $request)
    {
        $action = $request->input('action'); // yes / no
        $pendingUid = session('UserCheck');
        // dd($action, $pendingUid);
        if (empty($pendingUid)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pending login found.'
            ], 400);
        }

        if ($action === 'no') {

            session()->forget('UserCheck');

            return response()->json([
                'status' => 'error',
                'message' => 'नया लॉगिन रद्द कर दिया गया है।'
            ]);
        }

        // ===== action === yes =====

        // ## Invalidate old sessions
        try {
            DB::table('master_user')
                ->where('ID', $pendingUid)
                ->update(['session_token' => null]);
        } catch (\Exception $e) {
            // ignore
        }

        // ## Load user
        $user = DB::table('master_user')->where('ID', $pendingUid)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        // ### Finalize login (NO REQUEST)
        $this->finalizeLogin($user);

        // #### Clear pending flag
        session()->forget('pending_login_uid');
        session()->forget('UserCheck');
        session()->forget('AlreadyLogin');

        // ##### Role-wise redirect (SESSION BASED)
        $redirect_url = $this->role_wise_redirection();
        return response()->json([
            'status' => 'success',
            'url' => $redirect_url
        ]);
    }


    /**
     * finalizeLogin - sets session values and generates session token
     * If same browser (session token matches DB) it will simply set session data.
     */
    public function finalizeLogin($userRecord)
    {
        $dist_name = DB::table('master_district')
            ->where('District_Code_LGD', $userRecord->admin_district_id)
            ->value('name');

        $project_name = DB::table('master_projects')
            ->where('project_code', $userRecord->project_id)
            ->value('project');

        $project_name = $userRecord->admin_district_id == 0 ? 'राज्य' : $project_name;

        $currentMonth = date('n');
        $currentYear = date('Y');

        $finYear = ($currentMonth >= 4)
            ? $currentYear . '-' . ($currentYear + 1)
            : ($currentYear - 1) . '-' . $currentYear;

        $fin_id = DB::table('master_financial_years')
            ->where('fin_year', $finYear)
            ->value('id');

        // ## GLOBAL SESSION HELPER (NO REQUEST NEEDED)
        session([
            'sess_id' => $userRecord->ID,
            'uid' => $userRecord->ID,
            'sess_fname' => $userRecord->Full_Name,
            'sess_mobile' => $userRecord->Mobile_Number,
            'sess_role' => $userRecord->Role,
            'admin_pic' => $userRecord->admin_pic,
            'district_id' => $userRecord->admin_district_id,
            'designation' => $userRecord->Designation,
            'dist_name' => $dist_name,
            'project_code' => $userRecord->project_id,
            'project_name' => $project_name,
            'sector_id' => $userRecord->sector_id,
            'is_verified' => 1,
            'is_password_changed' => $userRecord->is_password_change,
            'fin_id' => $fin_id,
            // 'st_login_skip_token' => false
        ]);
        if (session('st_login_skip_token') === null) {
            session(['st_login_skip_token' => false]);
        }

        // session token
        try {
            // dd(session('st_login_skip_token'));
            if (session('st_login_skip_token') == false) {
                $token = bin2hex(random_bytes(32));
                DB::table('master_user')
                    ->where('ID', $userRecord->ID)
                    ->update(['session_token' => $token]);

                session(['session_token' => $token]);
            }

        } catch (\Exception $e) {
        }
    }

    /**
     * Show project change page for CDPO (role = CDPO) when multiple projects exist.
     */
    public function changeProject(Request $request)
    {

        $userMobile = Session::get('sess_mobile');
        $role = Session::get('sess_role');
        if (!in_array($role, ['CDPO', 'Super_admin'])) {
            return redirect()->back()->with('error', 'यह सुविधा केवल CDPO के लिए उपलब्ध है।');
        }

        $projects = DB::table('master_user')
            ->select('p.project_code as project_id', 'p.project as project', 'p.district as district')
            ->leftJoin('master_projects as p', 'p.project_code', '=', 'master_user.project_id')
            ->where('Mobile_Number', $userMobile)
            ->get();

        if ($projects->count() <= 1) {
            return redirect('/examinor/examinor-dashboard')->with('error', 'आपके पास केवल एक प्रोजेक्ट है।');
        }

        // If requested via AJAX (for modal), return data only
        if ($request->ajax() || $request->boolean('modal')) {
            return response()->json([
                'success' => true,
                'projects' => $projects,
                'current_project' => session('project_name'),
                'captcha' => captcha_img(),
            ]);
        }

        return view('examinor.change_project', [
            'projects' => $projects,
            'captcha_img' => captcha_img()
        ]);
    }

    /**
     * Process project change for CDPO.
     */
    public function processChangeProject(Request $request)
    {
        $role = Session::get('sess_role');
        if (!in_array($role, ['CDPO', 'Super_admin'])) {
            return redirect()->back()->with('error', 'यह सुविधा केवल CDPO के लिए उपलब्ध है।');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'project_code' => 'required',
                'captcha' => 'required|captcha'
            ],
            [
                'captcha.captcha' => 'कैप्चा मान्य नहीं है, इसे दोबारा भरें ।'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $districtId = Session::get('district_id');
        $userId = Session::get('uid');
        $userMobile = Session::get('sess_mobile');

        // Load selected project directly from master_projects (lighter than master_awcs)
        $project = DB::table('master_projects')
            ->select('project_code', 'project', 'district', 'district_lgd_code')
            ->where('project_code', $request->project_code)
            ->first();

        if (!$project) {
            return redirect()->back()->with('error', 'चयनित प्रोजेक्ट के लिए डेटा नहीं मिला।');
        }

        // Fetch current user
        $user = DB::table('master_user')
            ->where('Mobile_Number', $userMobile)
            ->where('project_id', $request->project_code)
            ->first();
        if (!$user) {
            return redirect()->back()->with('error', 'यूज़र डेटा नहीं मिला। कृपया पुनः लॉगिन करें।');
        }

        // Persist the new project (and district if provided) to user
        $newDistrict = $project->district_lgd_code ?? $user->admin_district_id;

        // Refresh session like a fresh login, with updated project/district
        $user->project_id = $project->project_code;
        $user->admin_district_id = $newDistrict;

        // Regenerate session ID to avoid fixation
        $request->session()->regenerate(true);

        $this->finalizeLogin($user);

        $redirectUrl = '/admin/admin-dashboard';
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'प्रोजेक्ट सफलतापूर्वक बदल गया और सत्र रिफ्रेश कर दिया गया है।',
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->with('success', 'प्रोजेक्ट सफलतापूर्वक बदल गया और सत्र रिफ्रेश कर दिया गया है।');
    }

    /**
     * Return list of sectors tied to the logged-in user's mobile number.
     */
    public function getUserSectors(Request $request)
    {
        $mobile = Session::get('sess_mobile');

        if (!$mobile) {
            return response()->json([
                'message' => 'मोबाइल नंबर नहीं मिला, कृपया पुनः लॉगिन करें।'
            ], 401);
        }

        $sectors = DB::table('master_user as mu')
            ->leftJoin('master_awcs as awc', 'mu.sector_id', '=', 'awc.sector_code')
            ->select(
                'mu.sector_id as sector_code',
                'awc.sector',
                'awc.project',
                'awc.project_code',
                'awc.district',
                'awc.district_lgd_code',
                'awc.district_code'
            )
            ->where('mu.Mobile_Number', $mobile)
            ->whereNotNull('mu.sector_id')
            ->groupBy(
                'mu.sector_id',
                'awc.sector',
                'awc.project',
                'awc.project_code',
                'awc.district',
                'awc.district_lgd_code',
                'awc.district_code'
            )
            ->orderBy('awc.sector')
            ->get();

        if ($sectors->isEmpty()) {
            return response()->json([
                'count' => 0,
                'sectors' => [],
                'message' => 'इस मोबाइल नंबर के लिए कोई सेक्टर नहीं मिला।'
            ], 404);
        }

        return response()->json([
            'count' => $sectors->count(),
            'sectors' => $sectors
        ]);
    }
}
