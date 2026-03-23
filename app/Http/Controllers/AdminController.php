<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\master_district;
use App\Models\user_detail;
use App\Models\DocumentVerification;

use App\Utilities\DataUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\OtpHelper;
use function Laravel\Prompts\table;

class AdminController extends Controller
{
    // super_admin with District code conditions are for CDPO login.  --> it should be project wise.
    // Super_admin with No district code conditions are for state login.
    // Admin with District code conditions are for DPO login which can do only scrutiny. --> it should be district wise.

    public function dashboard()
    {
        $username = Session::get('sess_fname');
        $admin_pic = Session::get('admin_pic');
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        $apiData = DB::select("SELECT * FROM api_master_data WHERE district_code = ? group by awc_code", [$district_id]);
        // dd($apiData);
        // dd(session()->all());
        if ($role === 'Super_admin' && $district_id) {

            DB::enableQueryLog();
            $records = DB::table('post_vacancy_map as pvm')
                ->join('master_post as mp', 'pvm.fk_post_id', '=', 'mp.post_id')
                ->where('mp.project_code', $project_code)
                ->pluck('pvm.no_of_vacancy');
            $news_num = 0;

            // dd($records);
            foreach ($records as $json) {
                $news_num += $this->calculateVacancySum($json);
            }

            $total_Pending_Applications = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('is_final_submit', '1')
                ->where('status', 'Submitted')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->where('master_post.project_code', $project_code)
                ->count();

            $advertisementPostData = DB::table('master_advertisement')
                ->leftJoin('master_post', 'master_advertisement.Advertisement_ID', '=', 'master_post.Advertisement_ID')
                ->select(
                    'master_advertisement.Advertisement_Title as advertisement_title',
                    DB::raw('COUNT(master_post.post_id) as post_count')
                )
                ->where('master_post.project_code', $project_code) // Filter by district
                ->groupBy('master_advertisement.Advertisement_ID', 'master_advertisement.Advertisement_Title')
                ->orderBy('master_advertisement.Advertisement_Title', 'asc')
                ->get();

            $totalAdvertisment = DB::table('master_advertisement')
                ->where('project_code', $project_code)
                ->count();

            $totalPosts = DB::table('master_post')
                ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                ->where('master_advertisement.district_lgd_code', $district_id)
                ->where('master_post.project_code', $project_code)
                ->count();


            $dist_applications = DB::table('master_district')
                ->leftJoin('tbl_user_post_apply', function ($join) {
                    $join->on('tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                        ->where('tbl_user_post_apply.is_final_submit', '=', 1);
                    if (!empty($district_id)) {
                        $join->where('tbl_user_post_apply.fk_district_id', '=', $district_id);
                    }
                })
                ->select(
                    'master_district.name as Dist_name',
                    DB::raw('COUNT(tbl_user_post_apply.apply_id) AS total_applications')
                )
                ->groupBy('master_district.District_Code_LGD', 'master_district.name')
                ->orderBy('master_district.name')
                ->get();




            $application_count = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('is_final_submit', '1')
                ->where('master_post.project_code', $project_code)->count();

            $post_count = DB::table('master_post')
                ->where('master_post.project_code', $project_code)
                ->count();

            $total_Applications = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $total_verified_Applications = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('is_final_submit', '1')
                ->where('status', 'Verified')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $total_rejected_Applications = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('is_final_submit', '1')
                ->where('status', 'Rejected')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();


            $qualiChartData = DB::select("SELECT 
                                                    qm.Quali_ID AS fk_Quali_ID,
                                                    qm.Quali_Name AS qualification_name,
                                                    COALESCE(COUNT(upa.apply_id), 0) AS application_count
                                                FROM 
                                                    master_qualification qm
                                                LEFT JOIN (
                                                    SELECT 
                                                        t1.fk_apply_id, 
                                                        t1.fk_Quali_ID
                                                    FROM 
                                                        record_applicant_edu_map t1
                                                    INNER JOIN (
                                                        SELECT 
                                                            fk_apply_id, 
                                                            MAX(fk_Quali_ID) AS max_quali
                                                        FROM 
                                                            record_applicant_edu_map
                                                        GROUP BY 
                                                            fk_apply_id
                                                    ) t2 
                                                    ON t1.fk_apply_id = t2.fk_apply_id AND t1.fk_Quali_ID = t2.max_quali
                                                ) AS edu  ON qm.Quali_ID = edu.fk_Quali_ID
                                                LEFT JOIN ( tbl_user_post_apply upa
                                                    INNER JOIN master_post pm  ON pm.post_id = upa.fk_post_id AND pm.project_code = ?  ) ON upa.apply_id = edu.fk_apply_id 
                                                AND upa.fk_district_id = ? AND upa.is_final_submit = 1
                                                WHERE 
                                                    qm.Quali_ID NOT IN (1)
                                                GROUP BY 
                                                    qm.Quali_ID, qm.Quali_Name
                                                ORDER BY 
                                                    fk_Quali_ID ASC
                                            ", [$project_code, $district_id]);


            $category_count = DB::select("SELECT 
                                                    mc.caste_title,
                                                    COALESCE(COUNT(upa.fk_applicant_id),0) AS total
                                                FROM master_tbl_caste mc
                                                LEFT JOIN record_user_detail_map ud
                                                    ON mc.caste_title = ud.Caste
                                                LEFT JOIN (tbl_user_post_apply upa INNER JOIN master_post mp ON upa.fk_post_id = mp.post_id AND mp.project_code = ? )
                                                    ON ud.fk_apply_id = upa.apply_id AND upa.fk_district_id = ? AND upa.is_final_submit = 1
                                                GROUP BY mc.caste_title
                                                ORDER BY mc.caste_title
                                            ", [$project_code, $district_id]);



            $category_data = DB::select("
                                SELECT 
                                    master_tbl_caste.caste_title,
                                    SUM(CASE WHEN tbl_user_detail.Caste = master_tbl_caste.caste_title THEN 1 ELSE 0 END) AS matched_applications
                                FROM tbl_user_post_apply
                                INNER JOIN tbl_user_detail 
                                    ON tbl_user_post_apply.fk_applicant_id = tbl_user_detail.ID
                                INNER JOIN master_tbl_caste 
                                    ON master_tbl_caste.caste_title = tbl_user_detail.Caste
                                WHERE tbl_user_post_apply.fk_district_id = ?
                                GROUP BY master_tbl_caste.caste_title
                                ORDER BY master_tbl_caste.caste_title
                            ", [$district_id]);

            $pending_count = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('is_final_submit', '1')
                ->where('status', 'Submitted')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $verified_count = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('is_final_submit', '1')
                ->where('status', 'Verified')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $rejected_count = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('is_final_submit', '1')
                ->where('status', 'Rejected')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $registrationList = DB::select("select * from master_user where Role='Candidate'");
            $userCount = DB::select("select count(*) as user_count from master_user where Role='Candidate'");


            // dd($registrationList);
        } elseif ($role === 'Super_admin') {

            DB::enableQueryLog();
            // $news_num = DB::table('post_vacancy_map')
            //     ->join('master_post', 'post_vacancy_map.fk_post_id', '=', 'master_post.post_id')
            //     // ->where('master_post.project_code', $project_code)
            //     ->sum('no_of_vacancy');

            $records = DB::table('post_vacancy_map as pvm')
                ->join('master_post as mp', 'pvm.fk_post_id', '=', 'mp.post_id')
                // ->where('mp.project_code', $project_code)
                ->pluck('pvm.no_of_vacancy');
            $news_num = 0;

            // dd($records);
            foreach ($records as $json) {
                $news_num += $this->calculateVacancySum($json);
            }



            $application_list = DB::table('tbl_user_detail')
                ->select('tbl_user_detail.ID AS RowID', 'master_post.title AS post_title', 'tbl_user_detail.*', 'master_user.*', 'tbl_user_detail.Pref_Districts AS pref_district_name', 'tbl_applicant_experience_details.*', 'tbl_applicant_education_qualification.*')
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_applicant_experience_details', 'tbl_user_detail.ID', '=', 'tbl_applicant_experience_details.Applicant_ID')
                ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->groupBy('tbl_applicant_education_qualification.fk_applicant_id')
                ->limit(5)
                ->get();


            $dist_applications = DB::table('master_district')
                ->leftJoin('tbl_user_post_apply', function ($join) {
                    $join->on('tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                        ->where('tbl_user_post_apply.is_final_submit', '=', 1);
                })
                ->select(
                    'master_district.name as Dist_name',
                    DB::raw('COUNT(tbl_user_post_apply.apply_id) AS total_applications')
                )
                ->groupBy('master_district.District_Code_LGD', 'master_district.name')
                ->orderBy('master_district.name')
                ->get();




            $application_count = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')->count();

            $post_count = DB::table('master_post')->count();




            // $total_Applications = DB::table('tbl_user_post_apply')
            //     ->where('is_final_submit', '1')
            //     ->count();

            $total_Applications = DB::table('tbl_user_post_apply')
                ->Join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('is_final_submit', '1')
                ->count();

            $total_verified_Applications = DB::table('tbl_user_post_apply')
                ->Join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('is_final_submit', '1')
                ->where('status', 'Verified')
                ->count();

            $total_rejected_Applications = DB::table('tbl_user_post_apply')
                ->Join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('is_final_submit', '1')
                ->where('status', 'Rejected')
                ->count();

            $total_Pending_Applications = DB::table('tbl_user_post_apply')
                ->Join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('is_final_submit', '1')
                ->where('status', 'Submitted')
                // ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $qualiChartData = DB::select("SELECT 
                                                        qm.Quali_ID AS fk_Quali_ID,
                                                        qm.Quali_Name AS qualification_name,
                                                        COUNT(CASE 
                                                        WHEN mp.post_id IS NOT NULL 
                                                        THEN upa.apply_id 
                                                    END) AS application_count
                                                    FROM 
                                                        master_qualification qm
                                                    LEFT JOIN (
                                                        SELECT 
                                                            t1.fk_apply_id, 
                                                            t1.fk_Quali_ID
                                                        FROM 
                                                            record_applicant_edu_map t1
                                                        INNER JOIN (
                                                            SELECT 
                                                                fk_apply_id, 
                                                                MAX(fk_Quali_ID) AS max_quali
                                                            FROM 
                                                                record_applicant_edu_map
                                                            GROUP BY 
                                                                fk_apply_id
                                                        ) t2 ON t1.fk_apply_id = t2.fk_apply_id AND t1.fk_Quali_ID = t2.max_quali
                                                    ) AS edu ON qm.Quali_ID = edu.fk_Quali_ID
                                                    
                                                    LEFT JOIN tbl_user_post_apply upa 
                                                        ON upa.apply_id = edu.fk_apply_id AND upa.is_final_submit =1
                                                        left join master_post mp on mp.`post_id`=upa.`fk_post_id`
                                                        WHERE 
                                                     qm.Quali_ID NOT IN (1)
                                                     
                                                    GROUP BY 
                                                        qm.Quali_ID, qm.Quali_Name
                                                    ORDER BY 
                                                        fk_Quali_ID ASC;

                                                ");

            $category_count = DB::select("SELECT 
                                                    mc.caste_title,
                                                    COALESCE(COUNT(upa.fk_applicant_id),0) AS total
                                                FROM master_tbl_caste mc
                                                LEFT JOIN record_user_detail_map ud
                                                    ON mc.caste_title = ud.Caste
                                                LEFT JOIN (tbl_user_post_apply upa INNER JOIN master_post mp ON upa.fk_post_id = mp.post_id )
                                                    ON ud.fk_apply_id = upa.apply_id AND upa.is_final_submit = 1
                                                GROUP BY mc.caste_title
                                                ORDER BY mc.caste_title
                                            ");



            // $category_data = DB::select("
            //                     SELECT 
            //                         master_tbl_caste.caste_title,
            //                         SUM(CASE WHEN tbl_user_detail.Caste = master_tbl_caste.caste_title THEN 1 ELSE 0 END) AS matched_applications
            //                     FROM tbl_user_post_apply
            //                     INNER JOIN tbl_user_detail 
            //                         ON tbl_user_post_apply.fk_applicant_id = tbl_user_detail.ID
            //                     INNER JOIN master_tbl_caste 
            //                         ON master_tbl_caste.caste_title = tbl_user_detail.Caste
            //                     WHERE tbl_user_post_apply.fk_district_id = ?
            //                     GROUP BY master_tbl_caste.caste_title
            //                     ORDER BY master_tbl_caste.caste_title
            //                 ", [$district_id]);


            $category_data = DB::select("
                                        SELECT 
                                            master_tbl_caste.caste_title,
                                            SUM(CASE WHEN tbl_user_detail.Caste = master_tbl_caste.caste_title THEN 1 ELSE 0 END) AS matched_applications
                                        FROM tbl_user_post_apply
                                        INNER JOIN tbl_user_detail 
                                            ON tbl_user_post_apply.fk_applicant_id = tbl_user_detail.ID
                                        INNER JOIN master_tbl_caste 
                                            ON master_tbl_caste.caste_title = tbl_user_detail.Caste
                                        GROUP BY master_tbl_caste.caste_title
                                        ORDER BY master_tbl_caste.caste_title
                                    ");

            $advertisementPostData = DB::table('master_advertisement')
                ->leftJoin('master_post', 'master_advertisement.Advertisement_ID', '=', 'master_post.Advertisement_ID')
                ->select(
                    'master_advertisement.Advertisement_Title as advertisement_title',
                    DB::raw('COUNT(master_post.post_id) as post_count')
                )
                // ->where('master_post.project_code', $project_code) // Filter by district
                ->groupBy('master_advertisement.Advertisement_ID', 'master_advertisement.Advertisement_Title')
                ->orderBy('master_advertisement.Advertisement_Title', 'asc')
                ->get();

            $totalAdvertisment = DB::table('master_advertisement')
                // ->where('project_code', $project_code)
                ->count();

            $totalPosts = DB::table('master_post')
                ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                // ->where('master_advertisement.district_lgd_code', $district_id)
                ->count();

            $pending_count = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('status', 'Submitted')
                ->count();

            $verified_count = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('status', 'Verified')
                ->count();

            $rejected_count = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('status', 'Rejected')
                ->count();

            $registrationList = DB::select("select *, count(*) as user_count from master_user where Role='Candidate'");
            $userCount = DB::select("select count(*) as user_count from master_user where Role='Candidate'");
            // dd($registrationList);
        }

        $data['news_num'] = $news_num ?? 0;
        $data['post_count'] = $post_count ?? 0;
        $data['pending_count'] = $pending_count ?? 0;
        $data['application_list'] = $application_list ?? [];
        $data['application_count'] = $application_count ?? 0;
        $data['verified_count'] = $verified_count ?? 0;
        $data['rejected_count'] = $rejected_count ?? 0;
        $data['total_Applications'] = $total_Applications ?? 0;
        $data['total_verified_Applications'] = $total_verified_Applications ?? 0;
        $data['total_rejected_Applications'] = $total_rejected_Applications ?? 0;
        $data['dist_applications'] = $dist_applications ?? 0;
        $data['qualiChartData'] = $qualiChartData ?? [];
        $data['totalAdvertisment'] = $totalAdvertisment ?? 0;
        $data['totalPosts'] = $totalPosts ?? 0;
        $data['advertisementPostData'] = $advertisementPostData ?? 0;
        $data['total_Pending_Applications'] = $total_Pending_Applications ?? 0;
        $data['registrationList'] = $registrationList ?? [];
        $data['userCount'] = $userCount[0]->user_count ?? 0;

        // Use main dashboard view
        return view('admin/dashboard', compact('data', 'category_count', 'category_data', 'registrationList'));
    }

    public function storeVerification(Request $request)
    {
        $fk_user_id = session('uid');
        $validator = Validator::make($request->all(), [
            'applicant_id' => 'required|string',
            'fk_post_id' => 'required|integer',
            'fk_apply_id' => 'required|integer',
            'document_key' => 'required|string',
            'document_name' => 'required|string',
            'is_verified' => 'required|boolean',
            'remark' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $verification = DocumentVerification::updateOrCreate(
                [
                    'applicant_id' => $request->applicant_id,
                    'document_key' => $request->document_key,
                    'fk_post_id' => $request->fk_post_id,
                ],
                [
                    'fk_user_id' => $fk_user_id,
                    'fk_apply_id' => $request->fk_apply_id,
                    'document_name' => $request->document_name,
                    'is_verified' => $request->is_verified,
                    'remark' => $request->remark,
                    'verified_by' => Session::get('uid'),
                ]
            );

            return response()->json(['success' => true, 'message' => 'Verification saved successfully.', 'data' => $verification]);
        } catch (Exception $e) {
            Log::error('Error saving verification: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while saving the verification.'], 500);
        }
    }

    public function ssp_portal_vacancy_data()
    {
        return view('admin/ssp_portal_data');
    }

    /**
     * Get SSP Portal Vacancy Data via AJAX
     */
    public function getSspPortalData(Request $request)
    {
        try {
            $district_id = Session::get('district_id', 0);
            $project_code = Session::get('project_code', 0);
            $role = Session::get('sess_role');

            // Fetch data from api_master_data table
            $query = DB::table('api_master_data')
                ->select(
                    'district_name',
                    'project_name',
                    'sector_name',
                    'awc_name',
                    'aww_position',
                    'awh_position'
                );

            // Apply district filter if user has district_id
            if ($role === 'Super_admin' && $district_id) {
                if ($district_id != 0)
                    $query->where('project_code', $project_code);
            } elseif ($role === 'Admin') {
                $query->where('district_code', $district_id);
            }

            // Group by awc_code to avoid duplicates
            $apiData = $query->groupBy('awc_code')
                ->orderBy('district_name', 'asc')
                ->orderBy('project_name', 'asc')
                ->orderBy('sector_name', 'asc')
                ->orderBy('awc_name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $apiData,
                'total_records' => count($apiData)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'डेटा लोड करने में त्रुटि: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export SSP Portal Vacancy Data to Excel
     */
    public function exportSspPortalData()
    {
        try {
            $district_id = Session::get('district_id', 0);
            $project_code = Session::get('project_code', 0);
            $role = Session::get('sess_role');

            // Build query for export
            $query = DB::table('api_master_data')
                ->select(
                    DB::raw('ROW_NUMBER() OVER (ORDER BY district_name, project_name, sector_name, awc_name) as serial_no'),
                    'district_name',
                    'project_name',
                    'sector_name',
                    'awc_code',
                    'awc_name',
                    'aww_position',
                    'awh_position',
                    DB::raw('DATE_FORMAT(inserted_at, "%d-%m-%Y %H:%i:%s") as data_load_date')
                );

            // Apply role-based filtering
            if ($role === 'Super_admin' && $district_id) {
                if ($district_id != 0)
                    $query->where('project_code', $project_code);
            } elseif ($role === 'Admin') {
                $query->where('district_code', $district_id);
            }

            // Group by awc_code to avoid duplicates
            $query->groupBy('awc_code', 'district_name', 'project_name', 'sector_name', 'awc_name', 'aww_position', 'awh_position', 'inserted_at')
                ->orderBy('district_name', 'asc')
                ->orderBy('project_name', 'asc')
                ->orderBy('sector_name', 'asc')
                ->orderBy('awc_name', 'asc');

            $mainHeading = "महिला एवं बाल विकास विभाग - SSP Portal Vacancy Data";

            $headings = [
                'क्रमांक',
                'जिला नाम',
                'प्रोजेक्ट नाम',
                'सेक्टर नाम',
                'AWC कोड',
                'AWC नाम',
                'AWW Position',
                'AWH Position',
                'डेटा लोड तिथि'
            ];

            $filename = 'SSP_Portal_Vacancy_Data_' . date('d-m-Y_His') . '.xlsx';

            // Export using DataUtility
            return DataUtility::exportToExcel($query, $filename, $headings, $mainHeading);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Excel निर्यात में त्रुटि: ' . $e->getMessage());
        }
    }

    /**
     * Get Advertisement-wise Analytics with Posts and Applications
     * Used for Enhanced Dashboard AJAX call
     */
    public function getAdvertisementAnalytics()
    {
        try {
            $role = Session::get('sess_role');
            $district_id = Session::get('district_id', 0);
            $project_code = Session::get('project_code', 0);

            // Build query based on role
            $query = DB::table('master_advertisement as ma')
                ->select(
                    'ma.Advertisement_ID',
                    'ma.Advertisement_Title',
                    'ma.Advertisement_Date',
                    DB::raw('COUNT(DISTINCT mp.post_id) as total_posts'),
                    DB::raw('COUNT(DISTINCT upa.apply_id) as total_applications'),
                    DB::raw('SUM(CASE WHEN upa.status = "Verified" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN upa.status = "Rejected" THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN upa.status = "Submitted" THEN 1 ELSE 0 END) as pending_count')
                )
                ->leftJoin('master_post as mp', 'ma.Advertisement_ID', '=', 'mp.Advertisement_ID')
                ->leftJoin('tbl_user_post_apply as upa', function ($join) {
                    $join->on('mp.post_id', '=', 'upa.fk_post_id')
                        ->where('upa.is_final_submit', '=', 1);
                });
            // ->where('ma.Advertisement_ID', 'mp.Advertisement_ID');

            // dd($role);
            // Apply filters based on role
            if ($role === 'Super_admin' && $district_id) {
                $query->where('ma.district_lgd_code', $district_id)
                    ->where('ma.project_code', $project_code);
            } elseif ($role === 'Admin') {
                $query->where('ma.district_lgd_code', $district_id);
            }

            $advertisements = $query->groupBy('ma.Advertisement_ID', 'ma.Advertisement_Title', 'ma.Advertisement_Date')
                ->orderBy('ma.Advertisement_Date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $advertisements
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'डेटा लोड करने में त्रुटि: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Post-wise Analytics for a specific Advertisement
     * AJAX endpoint for nested table
     */
    public function getPostAnalytics($advertisementId)
    {
        try {
            $role = Session::get('sess_role');
            $district_id = Session::get('district_id', 0);
            $project_code = Session::get('project_code', 0);

            $appsSubQuery = DB::table('tbl_user_post_apply')
                ->select(
                    'fk_post_id',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "Verified" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN status = "Rejected" THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN status = "Submitted" THEN 1 ELSE 0 END) as pending_count')
                )
                ->where('is_final_submit', 1)
                ->groupBy('fk_post_id');

            $query = DB::table('master_post as mp')
                ->leftJoin('post_vacancy_map as pvm', 'mp.post_id', '=', 'pvm.fk_post_id')
                ->leftJoin('master_projects as mproj', 'mp.project_code', '=', 'mproj.project_code')
                ->leftJoin(DB::raw("({$appsSubQuery->toSql()}) as upa"), 'mp.post_id', '=', 'upa.fk_post_id')
                ->mergeBindings($appsSubQuery)
                ->select(
                    'mp.post_id',
                    'mp.title as post_name',
                    DB::raw('COALESCE(mproj.project, "") as project_name'),
                    DB::raw('GROUP_CONCAT(DISTINCT pvm.no_of_vacancy) as vacancies'),
                    DB::raw('COALESCE(upa.total_applications,0) as total_applications'),
                    DB::raw('COALESCE(upa.approved_count,0) as approved_count'),
                    DB::raw('COALESCE(upa.rejected_count,0) as rejected_count'),
                    DB::raw('COALESCE(upa.pending_count,0) as pending_count')
                )
                ->where('mp.Advertisement_ID', $advertisementId)
                ->groupBy('mp.post_id', 'mp.title', 'mproj.project', 'upa.total_applications', 'upa.approved_count', 'upa.rejected_count', 'upa.pending_count');

            // Role filter
            if ($role === 'Super_admin' && $district_id) {
                $query->where('mp.project_code', $project_code);
            } elseif ($role === 'Admin' && $district_id) {
                $query->where('mproj.district_lgd_code', $district_id);
            }

            $posts = $query->groupBy('mp.post_id', 'mp.title', 'mproj.project')
                ->orderBy('mp.title', 'asc')
                ->get();



            foreach ($posts as $json) {
                $json->vacancies = $this->calculateVacancySum($json->vacancies);
            }

            // dd($posts);
            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'पोस्ट डेटा लोड करने में त्रुटि: ' . $e->getMessage()
            ], 500);
        }
    }

    public function upload_post(Request $request)
    {
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        $districts = DB::table('master_district')->orderBy('name', 'asc')->get();
        $qualifications = DB::table('master_qualification')->get();
        $categories = DB::table('master_post_category')->where('cat_id', '1')->get();
        $advertisements = DB::table('master_advertisement')->where('project_code', $project_code)->get();
        $caste_category = DB::table('master_tbl_caste')->get();
        $subjects = DB::table('master_subjects')->get();
        $skills = DB::table('master_skills')->get();
        $org_types = DB::table('master_tbl_organization_type')->get();
        $master_areas = DB::table('master_area')->get();
        $master_post = DB::table('master_post_config')->get();

        // Check if 'static' parameter exists in the URL
        if ($request->has('static') && $request->get('static') == '1') {
            return view('admin/static_forms/create_static_post', compact('master_areas', 'districts', 'qualifications', 'categories', 'advertisements', 'caste_category', 'org_types', 'subjects', 'skills'));
        }

        return view('admin/upload_post', compact('master_areas', 'districts', 'qualifications', 'categories', 'advertisements', 'caste_category', 'org_types', 'subjects', 'skills', 'master_post'));
        // return view('admin/upload_post');
    }

    public function getSubjectsByQualification($id)
    {
        $subjects = DB::table('master_subjects')
            ->where('fk_Quali_ID', $id)
            ->orderBy('subject_name', 'asc')
            ->get();


        return response()->json($subjects);
    }

    public function getPostConfigById($id)
    {
        try {
            $postConfig = DB::table('master_post_config')->where('id', $id)->first();

            if (!$postConfig) {
                return response()->json(['success' => false, 'message' => 'पोस्ट नहीं मिली'], 404);
            }

            // Decode JSON fields only (according to new table structure)
            $postConfig->fk_district_id = $postConfig->fk_district_id ? json_decode($postConfig->fk_district_id) : null;
            $postConfig->fk_organization_type_id = $postConfig->fk_organization_type_id ? json_decode($postConfig->fk_organization_type_id) : null;
            $postConfig->minimum_experiance_year = $postConfig->minimum_experiance_year ? json_decode($postConfig->minimum_experiance_year) : null;
            $postConfig->fk_ques_id = $postConfig->fk_ques_id ? json_decode($postConfig->fk_ques_id) : null;
            $postConfig->fk_skill_id = $postConfig->fk_skill_id ? json_decode($postConfig->fk_skill_id) : null;
            $postConfig->fk_qualification_id = $postConfig->fk_qualification_id ? json_decode($postConfig->fk_qualification_id) : null;
            $postConfig->fk_subject_id = $postConfig->fk_subject_id ? json_decode($postConfig->fk_subject_id) : null;

            // Note: gp_nnn_code, ward_no, std_nnn_code, and fk_area_id are no longer stored in master_post_config
            // These fields should be filled manually by the user in upload_post form
            // They are specific to each post instance, not to the master configuration

            return response()->json(['success' => true, 'data' => $postConfig]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'डेटा लोड करने में त्रुटि', 'error' => $e->getMessage()], 500);
        }
    }

    public function getPostTypeQuestions($post_type_id)
    {

        // dd($post_type_id);
        $questionIds = DB::table('master_post_questions')
            ->where('fk_post_type_id', $post_type_id)
            ->where('is_active', '1')
            ->pluck('ques_ID'); // assuming column name is ques_id

        return response()->json(['lockedQuestions' => $questionIds]);
    }

    public function getWardOrBlock(Request $request, $area_name = null)
    {
        $project_code = Session::get('project_code');
        $district_id = Session::get('district_id');

        if ($area_name === 'Urban' && !$request->has('city')) {
            $projects = DB::table('master_awcs')
                ->join('master_nnn', 'master_awcs.gp_nnn_code', '=', 'master_nnn.std_nnn_code')
                ->select('master_nnn.nnn_name', 'master_nnn.std_nnn_code')
                ->where('master_awcs.project_code', $project_code)
                ->where('master_awcs.AREA', $area_name)
                ->distinct()
                ->get();

            return response()->json($projects);
        } elseif ($area_name === 'Rural' && !$request->has('gp')) {
            $projects = DB::table('master_awcs')
                ->select('master_panchayats.panchayat_name_hin', 'master_awcs.gp_nnn_code')
                ->join('master_panchayats', 'master_awcs.gp_nnn_code', '=', 'master_panchayats.panchayat_lgd_code')
                ->where('master_awcs.project_code', $project_code)
                ->where('master_awcs.AREA', $area_name)
                ->distinct()
                ->get();


            return response()->json($projects);
        } elseif ($area_name === 'Urban' && $request->has('city')) {
            $cityCode = $request->get('city');

            $wards = DB::table('master_ward')
                ->select('ID as id', 'std_nnn_code', 'ward_name', 'ward_no')
                ->where('std_nnn_code', $cityCode)
                ->distinct()
                ->get();


            return response()->json($wards);
        } elseif ($area_name === 'Rural' && $request->has('gp')) {
            $gpCode = $request->get('gp');

            $village = DB::table('master_villages')
                ->select('village_code', 'village_name', 'village_name_hin')
                ->where('panchayat_lgd_code', $gpCode)
                ->distinct()
                ->get();

            // dd($village);


            return response()->json($village);
        }


        return response()->json([]);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        $fin_id = Session::get('fin_id', 0);
        $uid = Session::get('uid', 0);
        //  Request Validate karna (Required fields ke liye)
        $validator = Validator::make($request->all(), [
            'post_name' => 'required|string|max:255',
            'Advertisement_ID' => 'required',
            'master_area' => 'required|string',
            'fk_district_id' => 'nullable|array',
            'fk_district_id.*' => 'integer',
            'min_age' => 'required|integer|min:18|max:50',
            'max_age' => 'required|integer|min:18|max:50',
            'min_Qualification' => 'required|integer',
            'rules' => 'required|string',
            // 'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'questions' => 'required|array',
            'village_name' => 'nullable|array',
            'village_name.*' => 'string',
            'location_groups' => 'required_without_all:total_vacancy,urban_groups|array|min:1',
            'location_groups.*.gp_nnn_code' => 'required_with:location_groups|string',
            'location_groups.*.village_codes' => 'required_with:location_groups|array|min:1',
            'location_groups.*.village_codes.*' => 'string',
            'location_groups.*.total_vacancy' => 'required_with:location_groups|integer|min:1',
            'location_groups.*.is_janman_area' => 'required_with:location_groups|in:0,1',
            'urban_groups' => 'nullable|array',
            'urban_groups.*.city_nnn_code' => 'required_with:urban_groups|string',
            'urban_groups.*.ward_codes' => 'required_with:urban_groups|array|min:1',
            'urban_groups.*.ward_codes.*' => 'string',
            'urban_groups.*.total_vacancy' => 'required_with:urban_groups|integer|min:1',
        ]);

        $validator->after(function ($validator) use ($request) {
            $locationGroups = $request->input('location_groups', []);
            $totalVacancy = $this->utf($request->input('total_vacancy'));
            $urbanGroups = $request->input('urban_groups', []);

            if ($request->master_area === 'Rural' && (empty($locationGroups) || !is_array($locationGroups))) {
                $validator->errors()->add('location_groups', 'कृपया कम से कम एक ग्राम पंचायत/गाँव प्रविष्टि जोड़ें या कुल रिक्तियाँ भरें।');
            }

            if ($request->master_area === 'Urban' && (empty($urbanGroups) || !is_array($urbanGroups))) {
                $validator->errors()->add('urban_groups', 'कृपया कम से कम एक शहर/वार्ड प्रविष्टि जोड़ें।');
            }
        });


        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        //  Transaction Start
        DB::beginTransaction();

        try {
            $filePath = null;
            $fileName = null;

            //  File Upload Agar File Di Gayi Hai
            if ($request->file('file')) {
                $uploadedFile = $request->file('file');
                $fileName = $uploadedFile->getClientOriginalName();

                $uploadFile = UtilController::upload_file(
                    $uploadedFile,
                    'file',
                    'uploads',
                    ['jpeg', 'jpg', 'png', 'pdf'],
                    ['image/jpeg', 'image/png', 'application/pdf']
                );
            }

            $locationGroupsInput = $request->input('location_groups', []);
            $urbanGroupsInput = $request->input('urban_groups', []);
            $hasLocationGroups = is_array($locationGroupsInput) && count($locationGroupsInput) > 0;
            $hasUrbanGroups = ($request->master_area === 'Urban') && is_array($urbanGroupsInput) && count($urbanGroupsInput) > 0;

            $normalizedLocationGroups = [];
            $gpCodes = [];
            $villageCodes = [];
            $totalVacancyFromGroups = 0;
            $isJanmanAreaFromGroups = 0;
            $totalVacancyList = [];
            $janmanFlagList = [];
            $totalVacancyList = [];
            $janmanFlagList = [];

            $urbanCities = [];
            $urbanWardMatrix = [];
            $urbanWardFlatIds = [];
            $urbanVacancyList = [];
            $totalVacancyFromUrbanGroups = 0;
            $normalizedUrbanGroups = [];

            if ($hasLocationGroups) {
                foreach ($locationGroupsInput as $group) {
                    $gpCode = $group['gp_nnn_code'] ?? null;
                    $villages = isset($group['village_codes']) && is_array($group['village_codes']) ? array_values(array_filter($group['village_codes'])) : [];
                    $vacancy = isset($group['total_vacancy']) ? (int) $group['total_vacancy'] : 0;
                    $janmanFlag = isset($group['is_janman_area']) ? (int) $group['is_janman_area'] : 0;

                    if (empty($gpCode) || empty($villages) || $vacancy <= 0) {
                        continue;
                    }

                    $normalizedLocationGroups[] = [
                        'gp_nnn_code' => $gpCode,
                        'village_codes' => $villages,
                        'total_vacancy' => $vacancy,
                        'is_janman_area' => $janmanFlag,
                    ];

                    $gpCodes[] = $gpCode;
                    $villageCodes = array_merge($villageCodes, $villages);
                    $totalVacancyFromGroups += $vacancy;
                    $totalVacancyList[] = $vacancy;
                    $janmanFlagList[] = $janmanFlag;
                    if ($janmanFlag === 1) {
                        $isJanmanAreaFromGroups = 1;
                    }
                }

                $villageCodes = array_values(array_unique($villageCodes));
            }

            if ($hasUrbanGroups) {
                foreach ($urbanGroupsInput as $group) {
                    $cityCode = $group['city_nnn_code'] ?? null;
                    $wards = isset($group['ward_codes']) && is_array($group['ward_codes']) ? array_values(array_filter($group['ward_codes'])) : [];
                    $vacancy = isset($group['total_vacancy']) ? (int) $group['total_vacancy'] : 0;

                    if (empty($cityCode) || empty($wards) || $vacancy <= 0) {
                        continue;
                    }

                    // Expect ward_codes as master_ward IDs; normalize to int and unique
                    $wards = array_values(array_unique(array_map('intval', $wards)));

                    $urbanCities[] = $cityCode;
                    $urbanWardMatrix[] = $wards;
                    $urbanWardFlatIds = array_merge($urbanWardFlatIds, $wards);
                    $urbanVacancyList[] = $vacancy;
                    $totalVacancyFromUrbanGroups += $vacancy;

                    $normalizedUrbanGroups[] = [
                        'city_nnn_code' => $cityCode,
                        'ward_codes' => $wards,
                        'total_vacancy' => $vacancy,
                    ];
                }

                $urbanWardFlatIds = array_values(array_unique($urbanWardFlatIds));
            }

            // Only store Gram Panchayat codes inside gp_nnn_code; keep other data as before, but persist totals/janman as JSON arrays
            $cleanStdNnnCode = $request->city_name;
            if ($cleanStdNnnCode === '-- चुनें --' || $cleanStdNnnCode === '' || $cleanStdNnnCode === null) {
                $cleanStdNnnCode = null; // prefer null/empty instead of placeholder
            }

            if ($hasUrbanGroups) {
                $cleanStdNnnCode = json_encode($urbanCities);
            }

            $gp_nnn_code_value = $hasLocationGroups ? json_encode(array_values(array_unique($gpCodes))) : ($request->gp_name ?? null);
            $village_code_value = $hasLocationGroups ? json_encode($villageCodes) : (isset($request->village_name) ? json_encode($request->village_name) : null);
            $ward_value = $hasUrbanGroups ? json_encode($urbanWardFlatIds) : (isset($request->ward_name) ? json_encode($request->ward_name) : null);
            $is_janman_area = $hasLocationGroups
                ? json_encode($janmanFlagList)
                : ($hasUrbanGroups ? null : $request->isJanmanArea);

            $totalVacancyToStore = $hasLocationGroups
                ? $totalVacancyFromGroups
                : ($hasUrbanGroups ? $totalVacancyFromUrbanGroups : $request->total_vacancy);

            $area_id = DB::table('master_area')->where('area_name', $request->master_area)->first()->area_id;
            //  master_post mein data insert
            $postMasterId = DB::table('master_post')->insertGetId([
                'title' => $request->post_name,
                'Advertisement_ID' => $request->Advertisement_ID,
                'fk_area_id' => $area_id,
                'project_code' => $project_code,
                'std_nnn_code' => $cleanStdNnnCode,
                'gp_nnn_code' => $gp_nnn_code_value,
                'village_code' => $village_code_value,
                'ward_no' => $ward_value,
                'cat_id' => '1', // hardcoded for now
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'max_age_relax' => $request->max_age_relax,
                'Quali_ID' => $request->min_Qualification,
                'guidelines' => $request->rules,
                'file_name' => $fileName,
                'File_Path' => $uploadFile ?? null,
                'is_janman_area' => $is_janman_area,
                'Created_By' => $uid,
                'IP_Address' => request()->ip(),
                'fk_fin_id' => $fin_id
            ]);

            $ipAddress = request()->ip();

            //  Agar District diya gaya hai tabhi insert ho
            DB::table('post_map')->insert([
                'fk_post_id' => $postMasterId,
                'fk_district_id' => $district_id,
                'created_at' => now(),
            ]);

            //  Questions mapping (Mandatory)
            // Sort the questions array in ascending order
            $questions = $request->questions;
            sort($questions);
            foreach ($questions as $questionId) {
                DB::insert("INSERT INTO post_question_map (fk_ques_id, fk_post_id, ip_address) VALUES (?, ?, ?)", [
                    $questionId,
                    $postMasterId,
                    $ipAddress
                ]);
            }

            //  post_vacancy_map (general ya caste-wise)
            if (!empty($totalVacancyToStore)) {
                $noOfVacancyValue = $totalVacancyToStore;

                if ($hasLocationGroups && count($totalVacancyList) > 0) {
                    $noOfVacancyValue = json_encode($totalVacancyList);
                } elseif ($hasUrbanGroups && count($urbanVacancyList) > 0) {
                    $noOfVacancyValue = json_encode($urbanVacancyList);
                }

                DB::insert("INSERT INTO post_vacancy_map (fk_post_id, fk_caste_id, no_of_vacancy, ip_address) VALUES (?, ?, ?, ?)", [
                    $postMasterId,
                    0,
                    $noOfVacancyValue,
                    $ipAddress
                ]);
            } else {
                $vacancies = $request->input('vacancy', []);
                foreach ($vacancies as $key => $val) {
                    DB::insert("INSERT INTO post_vacancy_map (fk_post_id, fk_caste_id, no_of_vacancy, ip_address) VALUES (?, ?, ?, ?)", [
                        $postMasterId,
                        $key,
                        $val,
                        $ipAddress
                    ]);
                }
            }


            // Subjects agar diye gaye hain tabhi insert karein
            if ($request->filled('subjects')) {
                foreach ($request->subjects as $subjectId) {
                    DB::insert("INSERT INTO post_subject_map (fk_post_id, fk_qualification_id, fk_subject_id, ip_address) VALUES (?, ?, ?, ?)", [
                        $postMasterId,
                        $request->min_Qualification,
                        $subjectId,
                        $ipAddress
                    ]);
                }
            }

            // Skills agar diye gaye hain tabhi insert karein
            if ($request->filled('skills')) {
                foreach ($request->skills as $skillId) {
                    DB::insert("INSERT INTO post_skills_map (fk_post_id, fk_skill_id, ip_address) VALUES (?, ?, ?)", [
                        $postMasterId,
                        $skillId,
                        $ipAddress
                    ]);
                }
            }

            // Organization types agar diye gaye hain tabhi insert karein
            if ($request->filled('org_types') && is_array($request->org_types)) {
                foreach ($request->org_types as $key => $org_type) {
                    $experience = $request->experience[$key] ?? null; // default 0 if not set
                    DB::insert("INSERT INTO post_organization_map (fk_post_id, fk_organization_type_id, minimum_experiance_year, ip_address) VALUES (?, ?, ?, ?)", [
                        $postMasterId,
                        $org_type,
                        $experience,
                        $ipAddress
                    ]);
                }
            }

            // NEW CODE: Transfer static weightage if post is from master_post_config
            if ($request->filled('post_id')) {
                $post_config_id = (int) $request->post_id;
                $ip_address = $request->ip();
                $user_id = (int) Session::get('sess_user_id', 0);

                // Instantiate StaticWeightageController
                $staticWeightageController = new \App\Http\Controllers\StaticWeightageController();

                try {
                    // Transfer weightage data
                    $staticWeightageController->transferStaticWeightageToUploadedPost(
                        $post_config_id,
                        $postMasterId,
                        $ip_address,
                        $user_id
                    );
                } catch (\Exception $e) {
                    // Rollback post creation if weightage transfer fails
                    throw new \Exception('वेटेज डेटा ट्रांसफर करने में त्रुटि: ' . $e->getMessage());
                }
            }


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'पोस्ट सफलतापूर्वक बनाया गया।',
                'post_ids' => $postMasterId,
            ]);
        } catch (\Exception $e) {
            //  Koi bhi error aaye to rollback karein
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'कुछ गलत हो गया',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Store Static Post Configuration
    public function store_static_post(Request $request)
    {
        // Validation - Updated according to new table structure
        $validator = Validator::make($request->all(), [
            'post_name' => 'required|string|max:255|unique:master_post_config,post_name',
            'min_age' => 'required|integer|min:18|max:50',
            'max_age' => 'required|integer|min:18|max:50',
            'max_age_relax' => 'nullable|integer|min:18|max:50',
            'min_Qualification' => 'required|integer',
            'rules' => 'required|string',
            // 'file' => 'required|file|mimes:pdf|max:2048',
            'questions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Start Transaction
        DB::beginTransaction();

        try {
            $filePath = null;
            $fileName = null;

            // File Upload
            if ($request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                $fileName = $uploadedFile->getClientOriginalName();

                $filePath = UtilController::upload_file(
                    $uploadedFile,
                    'file',
                    'uploads',
                    ['jpeg', 'jpg', 'png', 'pdf'],
                    ['image/jpeg', 'image/png', 'application/pdf']
                );
            }

            // Organization types and experience
            $fk_organization_type_id = null;
            $minimum_experiance_year = null;
            if ($request->filled('org_types') && is_array($request->org_types)) {
                $fk_organization_type_id = json_encode(array_values($request->org_types));
                $minimum_experiance_year = json_encode(array_values($request->experience ?? []));
            }

            // Questions
            $fk_ques_id = null;
            if ($request->filled('questions') && is_array($request->questions)) {
                $questions = $request->questions;
                sort($questions);
                $fk_ques_id = json_encode($questions);
            }

            // Skills
            $fk_skill_id = $request->filled('skills') ? json_encode($request->skills) : null;

            // Subjects with qualification
            $fk_qualification_id = $request->filled('subjects') ? json_encode(array_fill(0, count($request->subjects), $request->min_Qualification)) : null;
            $fk_subject_id = $request->filled('subjects') ? json_encode($request->subjects) : null;

            // Insert into master_post_config - Updated according to new table structure
            $postConfigId = DB::table('master_post_config')->insertGetId([
                'post_name' => $request->post_name,
                'title' => $request->post_name,
                'cat_id' => null,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'max_age_relax' => $request->max_age_relax ?? null,
                'quali_id' => $request->min_Qualification,
                'guidelines' => $request->rules,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'is_weightage' => '0',
                'fk_district_id' => null,
                'fk_organization_type_id' => $fk_organization_type_id,
                'minimum_experiance_year' => $minimum_experiance_year,
                'fk_ques_id' => $fk_ques_id,
                'fk_skill_id' => $fk_skill_id,
                'fk_qualification_id' => $fk_qualification_id,
                'fk_subject_id' => $fk_subject_id,
                'is_active' => '1',
                'ip_address' => request()->ip(),
                'created_by' => Session::get('sess_user_id'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'अपरिवर्तनीय पोस्ट सफलतापूर्वक बनाई गई।',
                'post_config_id' => $postConfigId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'कुछ गलत हो गया',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Static Post List
    public function static_post_list()
    {
        $static_posts = DB::table('master_post_config')
            ->leftJoin('master_qualification', 'master_post_config.quali_id', '=', 'master_qualification.Quali_ID')
            ->select('master_post_config.*', 'master_qualification.Quali_Name')
            ->orderBy('master_post_config.created_at', 'desc')
            ->get();

        return view('admin.static_forms.static_post_list', compact('static_posts'));
    }

    // Static Post Details
    public function static_post_details($id)
    {
        try {
            $post = DB::table('master_post_config')->where('id', $id)->first();

            if (!$post) {
                return response()->json(['success' => false, 'message' => 'पोस्ट नहीं मिली'], 404);
            }

            // Decode JSON fields and get related data
            $post->fk_subject_id = $post->fk_subject_id ? json_decode($post->fk_subject_id) : [];
            $post->fk_skill_id = $post->fk_skill_id ? json_decode($post->fk_skill_id) : [];
            $post->fk_organization_type_id = $post->fk_organization_type_id ? json_decode($post->fk_organization_type_id) : [];
            $post->minimum_experiance_year = $post->minimum_experiance_year ? json_decode($post->minimum_experiance_year) : [];
            $post->fk_ques_id = $post->fk_ques_id ? json_decode($post->fk_ques_id) : [];

            // Get qualification
            if ($post->quali_id) {
                $post->qualification = DB::table('master_qualification')->where('Quali_ID', $post->quali_id)->first();
            }

            // Get subjects
            if (!empty($post->fk_subject_id)) {
                $post->subjects = DB::table('master_subjects')->whereIn('subject_id', $post->fk_subject_id)->get();
            } else {
                $post->subjects = [];
            }

            // Get skills
            if (!empty($post->fk_skill_id)) {
                $post->skills = DB::table('master_skills')->whereIn('skill_id', $post->fk_skill_id)->get();
            } else {
                $post->skills = [];
            }

            // Get organization types
            if (!empty($post->fk_organization_type_id)) {
                $post->org_types = DB::table('master_tbl_organization_type')->whereIn('org_id', $post->fk_organization_type_id)->get();
                $post->experiences = $post->minimum_experiance_year;
            } else {
                $post->org_types = [];
                $post->experiences = [];
            }

            // Get questions
            if (!empty($post->fk_ques_id)) {
                $post->questions = DB::table('master_questions')->whereIn('ques_ID', $post->fk_ques_id)->get();
            } else {
                $post->questions = [];
            }

            return response()->json(['success' => true, 'data' => $post]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'डेटा लोड करने में त्रुटि', 'error' => $e->getMessage()], 500);
        }
    }

    // View Static Post (Readonly Mode)
    public function view_static_post($id)
    {
        try {
            $post = DB::table('master_post_config')->where('id', $id)->first();

            if (!$post) {
                return redirect()->back()->with('error', 'पोस्ट नहीं मिली');
            }

            // Decode JSON fields
            $post->fk_subject_id = $post->fk_subject_id ? json_decode($post->fk_subject_id) : [];
            $post->fk_skill_id = $post->fk_skill_id ? json_decode($post->fk_skill_id) : [];
            $post->fk_organization_type_id = $post->fk_organization_type_id ? json_decode($post->fk_organization_type_id) : [];
            $post->minimum_experiance_year = $post->minimum_experiance_year ? json_decode($post->minimum_experiance_year) : [];
            $post->fk_ques_id = $post->fk_ques_id ? json_decode($post->fk_ques_id) : [];

            // Get all master data
            $qualifications = DB::table('master_qualification')->get();
            $subjects = DB::table('master_subjects')->get();
            $skills = DB::table('master_skills')->get();
            $org_types = DB::table('master_tbl_organization_type')->get();
            $questions = DB::table('master_post_questions')->get();

            $mode = 'view'; // View mode - readonly

            return view('admin.static_forms.create_static_post', compact('post', 'qualifications', 'subjects', 'skills', 'org_types', 'questions', 'mode'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'कुछ गलत हो गया');
        }
    }

    // Edit Static Post
    public function edit_static_post($id)
    {
        // dd($id);
        try {
            $post = DB::table('master_post_config')->where('id', $id)->first();

            if (!$post) {
                return redirect()->back()->with('error', 'पोस्ट नहीं मिली');
            }

            // Decode JSON fields
            $post->fk_subject_id = $post->fk_subject_id ? json_decode($post->fk_subject_id) : [];
            $post->fk_skill_id = $post->fk_skill_id ? json_decode($post->fk_skill_id) : [];
            $post->fk_organization_type_id = $post->fk_organization_type_id ? json_decode($post->fk_organization_type_id) : [];
            $post->minimum_experiance_year = $post->minimum_experiance_year ? json_decode($post->minimum_experiance_year) : [];
            $post->fk_ques_id = $post->fk_ques_id ? json_decode($post->fk_ques_id) : [];

            // Get all master data
            $qualifications = DB::table('master_qualification')->get();
            $subjects = DB::table('master_subjects')->get();
            $skills = DB::table('master_skills')->get();
            $org_types = DB::table('master_tbl_organization_type')->get();
            $questions = DB::table('master_post_questions')->get();

            $mode = 'edit'; // Edit mode - editable

            return view('admin.static_forms.create_static_post', compact('post', 'qualifications', 'subjects', 'skills', 'org_types', 'questions', 'mode'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'कुछ गलत हो गया');
        }
    }

    // Update Static Post
    public function update_static_post(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'post_name' => 'required|string|max:255|unique:master_post_config,title,' . $id,
            'min_age' => 'required|integer|min:18|max:50',
            'max_age' => 'required|integer|min:18|max:50',
            'max_age_relax' => 'nullable|integer|min:0|max:60',
            'min_Qualification' => 'required|integer',
            'rules' => 'required|string',
            // 'file' => 'nullable|file|mimes:pdf|max:2048',
            'questions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $post = DB::table('master_post_config')->where('id', $id)->first();

            if (!$post) {
                return response()->json(['success' => false, 'message' => 'पोस्ट नहीं मिली'], 404);
            }

            // Handle file upload
            $filePath = $post->file_path;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/static_posts'), $fileName);
                $filePath = 'uploads/static_posts/' . $fileName;

                // Delete old file if exists
                if ($post->file_path && file_exists(public_path($post->file_path))) {
                    unlink(public_path($post->file_path));
                }
            }

            // Prepare JSON fields
            $subjectIds = $request->subjects ? json_encode($request->subjects) : null;
            $skillIds = $request->skills ? json_encode($request->skills) : null;
            $orgTypeIds = $request->org_types ? json_encode($request->org_types) : null;
            $experienceYears = $request->experience ? json_encode($request->experience) : null;
            $questionIds = json_encode($request->questions);

            // Update post
            DB::table('master_post_config')->where('id', $id)->update([
                'title' => $request->post_name,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'max_age_relax' => $request->max_age_relax,
                'quali_id' => $request->min_Qualification,
                'fk_subject_id' => $subjectIds,
                'fk_skill_id' => $skillIds,
                'fk_organization_type_id' => $orgTypeIds,
                'minimum_experiance_year' => $experienceYears,
                'fk_ques_id' => $questionIds,
                'guidelines' => $request->rules,
                'file_path' => $filePath,
                'is_weightage' => $request->has('is_weightage') ? '1' : '0',
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'अपरिवर्तनीय पोस्ट सफलतापूर्वक अपडेट की गई।'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'कुछ गलत हो गया',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete Static Post
    public function delete_static_post($id)
    {
        try {
            $post = DB::table('master_post_config')->where('id', $id)->first();

            if (!$post) {
                return response()->json(['success' => false, 'message' => 'पोस्ट नहीं मिली'], 404);
            }

            // Delete the post
            DB::table('master_post_config')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'अपरिवर्तनीय पोस्ट सफलतापूर्वक हटा दी गई।'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'कुछ गलत हो गया',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showPosts()
    {

        $qualifications = DB::select("SELECT Quali_ID, Quali_Name FROM master_qualification");
        return view('admin.show-posts', compact('qualifications')); // Sirf view return kar raha hai, AJAX se data aayega
    }

    public function disablePosts(Request $request)
    {
        $validated = $request->validate([
            'post_ids' => 'required|array|min:1',
            'post_ids.*' => 'required|integer'
        ]);

        $postIds = collect($validated['post_ids'])
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($postIds)) {
            return response()->json([
                'success' => false,
                'message' => 'कोई वैध पोस्ट चयनित नहीं है।'
            ], 422);
        }

        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        $query = DB::table('master_post')->whereIn('post_id', $postIds);

        if (($role === 'Super_admin' && $district_id) || $role === 'Admin') {
            $query->where('project_code', $project_code);
        }

        $affectedRows = $query->update([
            'is_disable' => 0,
            'disable_capture_date' => now()
        ]);

        if ($affectedRows < 1) {
            return response()->json([
                'success' => false,
                'message' => 'चयनित पोस्ट delete नहीं हो सकी।'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'चयनित पोस्ट सफलतापूर्वक डिलीट  कर दी गई।',
            'updated_count' => $affectedRows
        ]);
    }

    public function disableAdvertisements(Request $request)
    {
        $validated = $request->validate([
            'advertisement_ids' => 'required|array|min:1',
            'advertisement_ids.*' => 'required|integer'
        ]);

        $advertisementIds = collect($validated['advertisement_ids'])
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($advertisementIds)) {
            return response()->json([
                'success' => false,
                'message' => 'कोई वैध विज्ञापन चयनित नहीं है।'
            ], 422);
        }

        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        $query = DB::table('master_advertisement')->whereIn('Advertisement_ID', $advertisementIds);

        if ($role === 'Super_admin' && $district_id) {
            $query->where('project_code', $project_code);
        } elseif ($role === 'Admin') {
            $query->where('district_lgd_code', $district_id);
        }

        $affectedRows = $query->update([
            'is_disable' => 0,
            'disable_capture_date' => now()
        ]);

        if ($affectedRows < 1) {
            return response()->json([
                'success' => false,
                'message' => 'चयनित विज्ञापन delete नहीं हो सके।'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'चयनित विज्ञापन सफलतापूर्वक डिलीट कर दिए गए।',
            'updated_count' => $affectedRows
        ]);
    }

    public function getQuestions(Request $request)
    {
        // यदि parentId रिक्वेस्ट में है, तो चाइल्ड प्रश्न लाएं
        $parentId = $request->input('parentId');
        if ($parentId) {
            $questions = DB::table('master_post_questions')
                ->select('ques_ID', 'ques_name')
                ->where('is_active', '1') // केवल सक्रिय प्रश्न लाएं
                ->where('parent_id', $parentId)
                ->get();
        } else {
            // अन्यथा, केवल पैरेंट प्रश्न लाएं (NULL parent_id)
            $questions = DB::table('master_post_questions')
                ->select('ques_ID', 'ques_name')
                ->where('is_active', '1') // केवल सक्रिय प्रश्न लाएं
                ->whereNull('parent_id') // Corrected IS NULL
                ->get();
        }

        return response()->json($questions);
    }
    public function getPosts(Request $request)
    {
        // dd($request);
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        if ($request->ajax()) {

            if ($role === 'Super_admin' && $district_id) {
                $data = DB::table('master_post')
                    ->leftJoin('master_post_category', 'master_post.cat_id', '=', 'master_post_category.cat_id')
                    ->leftJoin('post_map', 'master_post.post_id', '=', 'post_map.fk_post_id')
                    ->leftJoin('master_district', 'post_map.fk_district_id', '=', 'master_district.District_Code_LGD')
                    ->leftJoin('master_qualification', 'master_post.Quali_ID', '=', 'master_qualification.Quali_ID')

                    ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                    ->leftJoin('master_area', 'master_post.fk_area_id', '=', 'master_area.area_id')
                    // GP JSON-aware join
                    ->leftJoin('master_panchayats', function ($join) {
                        $join->on(DB::raw("JSON_CONTAINS(master_post.gp_nnn_code, JSON_QUOTE(CAST(master_panchayats.panchayat_lgd_code AS CHAR)))"), '=', DB::raw('1'))
                            ->orOn('master_post.gp_nnn_code', '=', 'master_panchayats.panchayat_lgd_code');
                    })
                    // City join (supports JSON array via FIND_IN_SET)
                    ->leftJoin('master_nnn', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(master_nnn.std_nnn_code, REPLACE(REPLACE(REPLACE(master_post.std_nnn_code, "[", ""), "]", ""), "\"", ""))'), '>', DB::raw('0'));
                    })
                    // Villages are JSON array
                    ->leftJoin('master_villages', function ($join) {
                        $join->on(DB::raw("JSON_CONTAINS(master_post.village_code, JSON_QUOTE(CAST(master_villages.village_code AS CHAR)))"), '=', DB::raw("1"));
                    })
                    // Wards are JSON array of IDs (use FIND_IN_SET for MariaDB compatibility)
                    ->leftJoin('master_ward', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(master_ward.ID, REPLACE(REPLACE(REPLACE(master_post.ward_no, "[", ""), "]", ""), "\"", ""))'), '>', DB::raw('0'));
                    })
                    ->leftJoin('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->select(
                        'master_post.post_id',
                        'master_post.title AS post_name',
                        'master_post.is_disable',
                        'master_post_category.cat_name AS category_name',
                        'master_projects.project AS project_name',
                        'master_area.area_name_hi as area_name',
                        DB::raw("GROUP_CONCAT(DISTINCT master_panchayats.panchayat_name_hin ORDER BY master_panchayats.panchayat_name_hin SEPARATOR ', ') AS panchayat_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_nnn.nnn_name ORDER BY master_nnn.nnn_name SEPARATOR ', ') AS city_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_villages.village_name_hin ORDER BY master_villages.village_name_hin SEPARATOR ', ') AS village_names"),
                        DB::raw("GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ' | ') AS ward_names"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_district.name ORDER BY master_district.name SEPARATOR ',') AS district_names"),
                        'master_post.max_age AS max_age',
                        'master_advertisement.Advertisement_Title AS advertisement_title',
                        'master_advertisement.Advertisement_Date AS date_from',
                        'master_advertisement.Date_For_Age AS date_to',
                        'master_qualification.Quali_Name AS qualification_name',
                        DB::raw("GROUP_CONCAT(DISTINCT post_vacancy_map.no_of_vacancy SEPARATOR ', ') AS vacancy_raw"),
                    )
                    // ->addSelect(DB::raw("(
                    //     SELECT GROUP_CONCAT(pvm.no_of_vacancy)
                    //     FROM post_vacancy_map pvm
                    //     WHERE pvm.fk_post_id = master_post.post_id
                    // ) AS vacancy_raw"))
                    ->leftJoin('post_vacancy_map', 'post_vacancy_map.fk_post_id', '=', 'master_post.post_id')
                    ->where('master_post.project_code', $project_code)
                    ->when($district_id, function ($query) use ($district_id) {
                        $query->where('post_map.fk_district_id', $district_id);
                    })
                    ->groupBy(
                        'master_post.post_id',
                        'master_post.title',
                        'master_post_category.cat_name',
                        'master_advertisement.Advertisement_Date',
                        'master_post.max_age',
                        'master_qualification.Quali_Name'
                    );




                // Filters
                if (!empty($request->post_name)) {
                    $data->where('master_post.title', 'like', '%' . $request->post_name . '%');
                }

                if (!empty($request->project)) {
                    $data->where('master_projects.project', 'like', '%' . $request->project . '%');
                }

                if (!empty($request->max_age)) {
                    $data->where('master_post.max_age', $request->max_age);
                }

                if (!empty($request->qualification)) {
                    $data->where('master_qualification.Quali_Name', 'like', '%' . $request->qualification . '%');
                }

                if (!empty($request->advertisement_title)) {
                    $data->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->advertisement_title . '%');
                }

                if (!empty($request->filterCity)) {
                    $data->where('master_nnn.nnn_name', 'like', '%' . $request->filterCity . '%');
                }

                if (!empty($request->filterGp)) {
                    $data->where('master_panchayats.panchayat_name_hin', 'like', '%' . $request->filterGp . '%');
                }

                if (!empty($request->filterWard)) {
                    $data->where('master_ward.ward_name', 'like', '%' . $request->filterWard . '%');
                }

                if (!empty($request->filterVillage)) {
                    $data->where('master_villages.village_name_hin', 'like', '%' . $request->filterVillage . '%');
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('select_checkbox', function ($row) {
                        return '<input type="checkbox" class="post-select-checkbox" data-post-id="' . (int) $row->post_id . '" data-post-name="' . e($row->post_name) . '">';
                    })
                    // ->addColumn('vacancy_count', function ($row) {
                    //     $raw = $row->vacancy_raw ?? '';
                    //     $sum = 0;
                    //     foreach (explode(',', (string) $raw) as $part) {
                    //         $part = trim($part);
                    //         if ($part === '') {
                    //             continue;
                    //         }
                    //         $decoded = json_decode($part, true);
                    //         if (is_array($decoded)) {
                    //             foreach ($decoded as $val) {
                    //                 $sum += (int) $val;
                    //             }
                    //         } else {
                    //             $sum += (int) $part;
                    //         }
                    //     }
                    //     return $sum;
                    // })
                    ->addColumn('vacancy_count', function ($row) {
                        return $this->calculateVacancySum($row->vacancy_raw ?? '');
                    })
                    ->editColumn('district_names', function ($row) {
                        return $row->district_names ? $row->district_names : 'सभी जिले';
                    })
                    ->addColumn('disable_status', function ($row) {
                        return ((int) ($row->is_disable ?? 1) === 0)
                            ? '<span class="badge bg-danger">Deleted</span>'
                            : '<span class="badge bg-success">Active</span>';
                    })
                    ->addColumn('view_details', function ($row) {
                        $viewUrl = route('posts.edit', [md5($row->post_id), 'view']);
                        return '<a href="' . $viewUrl . '" class="btn btn-sm btn-info text-white" title="विवरण देखें">
                             <i class="bi bi-eye"></i>
                        </a>';
                    })
                    ->addColumn('action', function ($row) {
                        $today = date('Y-m-d');
                        // $editUrl = route('posts.edit', [md5($row->post_id), 'edit']);
                        // $buttons = '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="संपादित करें">
                        //         <i class="bi bi-pencil-square"></i>
                        //     </a>';
                        $buttons = '';

                        if ($row->date_from > $today) {
                            $editUrl = route('posts.edit', [md5($row->post_id), 'edit']);
                            $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="संपादित करें">
                                <i class="bi bi-pencil-square"></i>
                            </a>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['select_checkbox', 'action', 'view_details', 'disable_status'])
                    ->make(true);
            } elseif ($role === 'Super_admin') {

                $data = DB::table('master_post')
                    ->leftJoin('master_post_category', 'master_post.cat_id', '=', 'master_post_category.cat_id')
                    ->leftJoin('post_map', 'master_post.post_id', '=', 'post_map.fk_post_id')
                    ->leftJoin('master_district', 'post_map.fk_district_id', '=', 'master_district.District_Code_LGD')
                    ->leftJoin('master_qualification', 'master_post.Quali_ID', '=', 'master_qualification.Quali_ID')

                    ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                    ->leftJoin('master_area', 'master_post.fk_area_id', '=', 'master_area.area_id')
                    // GP JSON-aware join
                    ->leftJoin('master_panchayats', function ($join) {
                        $join->on(DB::raw("JSON_CONTAINS(master_post.gp_nnn_code, JSON_QUOTE(CAST(master_panchayats.panchayat_lgd_code AS CHAR)))"), '=', DB::raw('1'))
                            ->orOn('master_post.gp_nnn_code', '=', 'master_panchayats.panchayat_lgd_code');
                    })
                    // City join (supports JSON array via FIND_IN_SET)
                    ->leftJoin('master_nnn', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(master_nnn.std_nnn_code, REPLACE(REPLACE(REPLACE(master_post.std_nnn_code, "[", ""), "]", ""), "\"", ""))'), '>', DB::raw('0'));
                    })
                    // Villages are JSON array
                    ->leftJoin('master_villages', function ($join) {
                        $join->on(DB::raw("JSON_CONTAINS(master_post.village_code, JSON_QUOTE(CAST(master_villages.village_code AS CHAR)))"), '=', DB::raw("1"));
                    })
                    // Wards are JSON array of IDs (MariaDB-safe)
                    ->leftJoin('master_ward', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(master_ward.ID, REPLACE(REPLACE(REPLACE(master_post.ward_no, "[", ""), "]", ""), "\"", ""))'), '>', DB::raw('0'));
                    })
                    ->leftJoin('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->select(
                        'master_post.post_id',
                        'master_post.title AS post_name',
                        'master_post.is_disable',
                        'master_post_category.cat_name AS category_name',
                        'master_projects.project AS project_name',
                        'master_area.area_name_hi as area_name',
                        DB::raw("GROUP_CONCAT(DISTINCT master_panchayats.panchayat_name_hin ORDER BY master_panchayats.panchayat_name_hin SEPARATOR ', ') AS panchayat_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_nnn.nnn_name ORDER BY master_nnn.nnn_name SEPARATOR ', ') AS city_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_villages.village_name_hin ORDER BY master_villages.village_name_hin SEPARATOR ', ') AS village_names"),
                        // DB::raw("GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ', ') AS ward_names"),
                        DB::raw("GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ' | ') AS ward_names"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_district.name ORDER BY master_district.name SEPARATOR ', ') AS district_names"),
                        'master_post.max_age AS max_age',
                        'master_advertisement.Advertisement_Title AS advertisement_title',
                        'master_advertisement.Advertisement_Date AS date_from',
                        'master_advertisement.Date_For_Age AS date_to',
                        'master_qualification.Quali_Name AS qualification_name',
                        DB::raw("GROUP_CONCAT(DISTINCT post_vacancy_map.no_of_vacancy SEPARATOR ', ') AS vacancy_raw"),
                    )
                    ->leftJoin('post_vacancy_map', 'post_vacancy_map.fk_post_id', '=', 'master_post.post_id')
                    ->groupBy(
                        'master_post.post_id',
                        'master_post.title',
                        'master_post_category.cat_name',
                        'master_advertisement.Advertisement_Date',
                        'master_post.max_age',
                        'master_qualification.Quali_Name'
                    );


                // dd($data->get());
                // Filters
                if (!empty($request->post_name)) {
                    $data->where('master_post.title', 'like', '%' . $request->post_name . '%');
                }

                if (!empty($request->category)) {
                    $data->where('master_post_category.cat_name', 'like', '%' . $request->category . '%');
                }

                if (!empty($request->district)) {
                    $data->where('master_district.name', 'like', '%' . $request->district . '%');
                }

                if (!empty($request->max_age)) {
                    $data->where('master_post.max_age', $request->max_age);
                }

                if (!empty($request->qualification)) {
                    $data->where('master_qualification.Quali_Name', 'like', '%' . $request->qualification . '%');
                }

                if (!empty($request->advertisement_title)) {
                    $data->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->advertisement_title . '%');
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('select_checkbox', function ($row) {
                        return '<input type="checkbox" class="post-select-checkbox" data-post-id="' . (int) $row->post_id . '" data-post-name="' . e($row->post_name) . '">';
                    })
                    ->addColumn('vacancy_count', function ($row) {
                        return $this->calculateVacancySum($row->vacancy_raw ?? '');
                    })
                    ->editColumn('district_names', function ($row) {
                        return $row->district_names ? $row->district_names : 'सभी जिले';
                    })
                    ->addColumn('disable_status', function ($row) {
                        return ((int) ($row->is_disable ?? 1) === 0)
                            ? '<span class="badge bg-danger">Deleted</span>'
                            : '<span class="badge bg-success">Active</span>';
                    })
                    ->addColumn('view_details', function ($row) {
                        $viewUrl = route('posts.edit', [md5($row->post_id), 'view']);
                        return '<a href="' . $viewUrl . '" class="btn btn-sm btn-outline-info" title="विवरण देखें">
                            <i class="bi bi-info-circle"></i> देखें
                        </a>';
                    })
                    ->addColumn('action', function ($row) {
                        $today = date('Y-m-d');
                        // $viewUrl = route('posts.edit', [md5($row->post_id), 'view']);
                        // $buttons = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info me-1" title="देखें">
                        //     <i class="bi bi-eye"></i>
                        // </a>';
                        $buttons = '';

                        if ($row->date_from > $today) {
                            $editUrl = route('posts.edit', [md5($row->post_id), 'edit']);
                            $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="संपादित करें">
                                <i class="bi bi-pencil-square"></i>
                            </a>';
                        } else {
                            $buttons = '-';
                        }

                        return $buttons;
                    })
                    ->rawColumns(['select_checkbox', 'action', 'view_details', 'disable_status'])
                    ->make(true);
            } elseif ($role === 'Admin') {
                $data = DB::table('master_post')
                    ->leftJoin('master_post_category', 'master_post.cat_id', '=', 'master_post_category.cat_id')
                    ->leftJoin('post_map', 'master_post.post_id', '=', 'post_map.fk_post_id')
                    ->leftJoin('master_district', 'post_map.fk_district_id', '=', 'master_district.District_Code_LGD')
                    ->leftJoin('master_qualification', 'master_post.Quali_ID', '=', 'master_qualification.Quali_ID')

                    ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                    ->leftJoin('master_area', 'master_post.fk_area_id', '=', 'master_area.area_id')
                    // GP JSON-aware join
                    ->leftJoin('master_panchayats', function ($join) {
                        $join->on(DB::raw("JSON_CONTAINS(master_post.gp_nnn_code, JSON_QUOTE(CAST(master_panchayats.panchayat_lgd_code AS CHAR)))"), '=', DB::raw('1'))
                            ->orOn('master_post.gp_nnn_code', '=', 'master_panchayats.panchayat_lgd_code');
                    })
                    // City join (supports JSON array via FIND_IN_SET)
                    ->leftJoin('master_nnn', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(master_nnn.std_nnn_code, REPLACE(REPLACE(REPLACE(master_post.std_nnn_code, "[", ""), "]", ""), "\"", ""))'), '>', DB::raw('0'));
                    })
                    // Villages are JSON array
                    ->leftJoin('master_villages', function ($join) {
                        $join->on(DB::raw("JSON_CONTAINS(master_post.village_code, JSON_QUOTE(CAST(master_villages.village_code AS CHAR)))"), '=', DB::raw("1"));
                    })
                    // Wards are JSON array of IDs (MariaDB-safe)
                    ->leftJoin('master_ward', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(master_ward.ID, REPLACE(REPLACE(REPLACE(master_post.ward_no, "[", ""), "]", ""), "\"", ""))'), '>', DB::raw('0'))
                            ->whereColumn('master_ward.std_nnn_code', 'master_post.std_nnn_code');
                    })
                    ->leftJoin('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->select(
                        'master_post.post_id',
                        'master_post.title AS post_name',
                        'master_post.is_disable',
                        'master_post_category.cat_name AS category_name',
                        'master_projects.project AS project_name',
                        'master_area.area_name_hi as area_name',
                        DB::raw("GROUP_CONCAT(DISTINCT master_panchayats.panchayat_name_hin ORDER BY master_panchayats.panchayat_name_hin SEPARATOR ', ') AS panchayat_name"),
                        DB::raw("master_nnn.nnn_name AS city_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_villages.village_name_hin ORDER BY master_villages.village_name_hin SEPARATOR ', ') AS village_names"),
                        // DB::raw("GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ', ') AS ward_names"),
                        DB::raw("GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ' | ') AS ward_names"),
                        DB::raw("GROUP_CONCAT(DISTINCT master_district.name ORDER BY master_district.name SEPARATOR ', ') AS district_names"),
                        'master_post.max_age AS max_age',
                        'master_advertisement.Advertisement_Title AS advertisement_title',
                        'master_advertisement.Advertisement_Date AS date_from',
                        'master_advertisement.Date_For_Age AS date_to',
                        'master_qualification.Quali_Name AS qualification_name',
                        DB::raw("GROUP_CONCAT(DISTINCT post_vacancy_map.no_of_vacancy SEPARATOR ', ') AS vacancy_raw"),
                    )
                    ->leftJoin('post_vacancy_map', 'post_vacancy_map.fk_post_id', '=', 'master_post.post_id')
                    ->when($district_id, function ($query) use ($district_id) {
                        $query->where('post_map.fk_district_id', $district_id);
                    })
                    ->groupBy(
                        'master_post.post_id',
                        'master_post.title',
                        'master_post_category.cat_name',
                        'master_advertisement.Advertisement_Date',
                        'master_post.max_age',
                        'master_qualification.Quali_Name'
                    );




                // Filters
                if (!empty($request->post_name)) {
                    $data->where('master_post.title', 'like', '%' . $request->post_name . '%');
                }

                if (!empty($request->project)) {
                    $data->where('master_projects.project', 'like', '%' . $request->project . '%');
                }

                if (!empty($request->max_age)) {
                    $data->where('master_post.max_age', $request->max_age);
                }

                if (!empty($request->qualification)) {
                    $data->where('master_qualification.Quali_Name', 'like', '%' . $request->qualification . '%');
                }

                if (!empty($request->advertisement_title)) {
                    $data->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->advertisement_title . '%');
                }

                if (!empty($request->filterCity)) {
                    $data->where('master_nnn.nnn_name', 'like', '%' . $request->filterCity . '%');
                }

                if (!empty($request->filterGp)) {
                    $data->where('master_panchayats.panchayat_name_hin', 'like', '%' . $request->filterGp . '%');
                }

                if (!empty($request->filterWard)) {
                    $data->where('master_ward.ward_name', 'like', '%' . $request->filterWard . '%');
                }

                if (!empty($request->filterVillage)) {
                    $data->where('master_villages.village_name_hin', 'like', '%' . $request->filterVillage . '%');
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('select_checkbox', function ($row) {
                        return '<input type="checkbox" class="post-select-checkbox" data-post-id="' . (int) $row->post_id . '" data-post-name="' . e($row->post_name) . '">';
                    })
                    ->addColumn('vacancy_count', function ($row) {
                        return $this->calculateVacancySum($row->vacancy_raw ?? '');
                    })
                    ->editColumn('district_names', function ($row) {
                        return $row->district_names ? $row->district_names : 'सभी जिले';
                    })
                    ->addColumn('disable_status', function ($row) {
                        return ((int) ($row->is_disable ?? 1) === 0)
                            ? '<span class="badge bg-danger">Deleted</span>'
                            : '<span class="badge bg-success">Active</span>';
                    })
                    ->addColumn('view_details', function ($row) {
                        $viewUrl = route('posts.edit', [md5($row->post_id), 'view']);
                        return '<a href="' . $viewUrl . '" class="btn btn-sm btn-outline-info" title="विवरण देखें">
                            <i class="bi bi-info-circle"></i> देखें
                        </a>';
                    })
                    ->addColumn('action', function ($row) {
                        $today = date('Y-m-d');
                        // $viewUrl = route('posts.edit', [md5($row->post_id), 'view']);
                        // $buttons = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info me-1" title="देखें">
                        //     <i class="bi bi-eye"></i>
                        // </a>';
                        $buttons = '';

                        if ($row->date_from > $today) {
                            $editUrl = route('posts.edit', [md5($row->post_id), 'edit']);
                            $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="संपादित करें">
                                <i class="bi bi-pencil-square"></i>
                            </a>';
                        }

                        return $buttons;
                    })
                    ->rawColumns(['select_checkbox', 'action', 'view_details', 'disable_status'])
                    ->make(true);
            }
        }
    }

    // ==== START CHANGES ====
    /**
     * Calculate vacancy sum from raw DB value
     * Handles cases like: [3,1], 4, [2,3],[1]
     */
    private function calculateVacancySum($vacancyRaw)
    {
        if (empty($vacancyRaw)) {
            return 0;
        }

        // Try direct JSON decode first
        $decoded = json_decode($vacancyRaw, true);

        if (is_array($decoded)) {
            return array_sum(array_map('intval', $decoded));
        }

        // Fallback for comma-separated plain values
        $parts = explode(',', $vacancyRaw);

        $sum = 0;
        foreach ($parts as $part) {
            $sum += (int) trim($part);
        }

        return $sum;
    }

    // ==== END CHANGES ====

    public function edit($id, $mode = 'edit')
    {
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        $districts = DB::table('master_district')->orderBy('name', 'asc')->get();
        $qualifications = DB::table('master_qualification')->orderBy('Quali_Name', 'asc')->get();
        $categories = DB::table('master_post_category')->get();

        $caste_category = DB::table('master_tbl_caste')->get();
        $subjects = DB::table('master_subjects')->get();
        $skills = DB::table('master_skills')->get();
        $org_types = DB::table('master_tbl_organization_type')->get();
        $master_areas = DB::table('master_area')->get();




        $post = DB::table('master_post')
            ->whereRaw("MD5(post_id) = ?", [$id])
            ->first();


        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Ensure advertisement dropdown always has the current post's advertisement, even if session project_code is missing/mismatched
        $advertisementsQuery = DB::table('master_advertisement')->orderBy('Advertisement_Title', 'asc');

        if (!empty($project_code)) {
            $advertisementsQuery->where('project_code', $project_code);
        }

        if (!empty($post->Advertisement_ID)) {
            $advertisementsQuery->orWhere('Advertisement_ID', $post->Advertisement_ID);
        }

        $advertisements = $advertisementsQuery->get();

        $nnn = null;
        if ($post->std_nnn_code) {
            $nnn = DB::table('master_nnn')
                ->where('std_nnn_code', $post->std_nnn_code)
                ->first();
        }


        $gp_nnn_code_array = [];
        $village_code_array = [];
        $location_groups = [];
        $total_vacancy_array = [];
        $is_janman_area_array = [];

        if ($post->gp_nnn_code) {
            $decodedGp = json_decode($post->gp_nnn_code, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedGp) && isset($decodedGp[0]) && is_array($decodedGp[0]) && array_key_exists('gp_nnn_code', $decodedGp[0])) {
                // Legacy: stored full location group objects
                $location_groups = $decodedGp;
                foreach ($decodedGp as $group) {
                    if (!empty($group['gp_nnn_code'])) {
                        $gp_nnn_code_array[] = $group['gp_nnn_code'];
                    }
                    if (!empty($group['village_codes']) && is_array($group['village_codes'])) {
                        $village_code_array = array_merge($village_code_array, $group['village_codes']);
                    }
                }
                $gp_nnn_code_array = array_values(array_unique($gp_nnn_code_array));
                $village_code_array = array_values(array_unique($village_code_array));
            } elseif (json_last_error() === JSON_ERROR_NONE && is_array($decodedGp)) {
                // New format: array of GP codes only
                $gp_nnn_code_array = array_values(array_filter($decodedGp));
                if ($post->village_code) {
                    $villageDecoded = json_decode($post->village_code, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($villageDecoded)) {
                        $village_code_array = array_values(array_filter($villageDecoded));
                    }
                }
            } else {
                $gp_nnn_code_array = [$post->gp_nnn_code]; // Convert single value to array for consistency in view
                if ($post->village_code) {
                    $village_code_array = json_decode($post->village_code, true);
                }
            }
        }

        // Decode total_vacancy and is_janman_area if stored as JSON arrays (guards for absent columns)
        if (isset($post->total_vacancy) && $post->total_vacancy) {
            $decodedTotalVacancy = json_decode($post->total_vacancy, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedTotalVacancy)) {
                $total_vacancy_array = array_values(array_filter($decodedTotalVacancy, function ($v) {
                    return $v !== null && $v !== '';
                }));
            }
        }

        if (isset($post->is_janman_area) && $post->is_janman_area) {
            $decodedJanman = json_decode($post->is_janman_area, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedJanman)) {
                $is_janman_area_array = array_values($decodedJanman);
            }
        }

        $gp_list = [];

        if (!empty($gp_nnn_code_array)) {
            $gp_list = DB::table('master_panchayats')
                ->whereIn('panchayat_lgd_code', $gp_nnn_code_array)
                ->get();
        }

        // Load village list if villages exist
        $village_list = [];
        if (!empty($village_code_array)) {
            $village_list = DB::table('master_villages')
                ->whereIn('village_code', $village_code_array)
                ->get();
        }


        $ward_id_array = [];
        $ward_matrix_by_city = [];

        if ($post->ward_no) {
            $decodedWard = json_decode($post->ward_no, true); // JSON ko PHP array me convert karein
            if (json_last_error() === JSON_ERROR_NONE) {
                $flat = is_array($decodedWard) ? $decodedWard : [$decodedWard];
                $ward_id_array = array_values(array_unique(array_map('intval', $flat)));
            }
        }

        $ward_list = [];
        $ward_by_city = [];

        if (!empty($ward_id_array)) {
            $ward_list = DB::table('master_ward')
                ->whereIn('ID', $ward_id_array)
                ->get();

            $ward_by_city = $ward_list->groupBy('std_nnn_code')->map(function ($group) {
                return $group->pluck('ID')->values()->toArray();
            })->toArray();
        }

        $advertisement_name = DB::table('master_advertisement')
            ->where('Advertisement_ID', $post->Advertisement_ID)
            ->value('Advertisement_Title');

        $cat_name = DB::table('master_post_category')
            ->where('cat_id', $post->cat_id) // Assuming 'id' is the primary key of master_post_category
            ->value('cat_name'); // Fetch only the category name

        //  Get all questions
        // $allQuestions = DB::table('master_post_questions')->get();
        $parentQuestions = DB::table('master_post_questions')
            ->select('ques_ID', 'ques_name')
            ->where('is_active', '1')
            ->whereNull('parent_id') // केवल पैरेंट प्रश्नों को ही लाएं
            ->get();

        //  Get selected question IDs for this post
        $selectedQuestionIds = DB::table('post_question_map')
            ->where('fk_post_id', $post->post_id)
            ->whereNull('deleted_at')
            ->pluck('fk_ques_id')
            ->toArray();

        $selected_districts = DB::table('post_map')
            ->whereRaw('fk_post_id= ? ', [$post->post_id])
            ->pluck('fk_district_id')->toArray(); // पहले सेलेक्टेड ज़िलों की लिस्ट

        $select_no_of_vacancy = DB::table('post_vacancy_map')
            ->whereRaw('fk_post_id= ? ', [$post->post_id])
            ->pluck('no_of_vacancy');


        // new changes
        // Select caste & their respective vacancy
        $vacancyData = DB::table('post_vacancy_map')
            ->where('fk_post_id', $post->post_id)
            ->select('fk_caste_id', 'no_of_vacancy')
            ->get();

        $selected_castes = [];
        $totalVacancy = 0;
        $isCasteWise = false;
        $generalVacancyArray = null;

        foreach ($vacancyData as $row) {
            if ($row->fk_caste_id == 0) {
                $decoded = json_decode($row->no_of_vacancy, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $generalVacancyArray = array_values(array_filter($decoded, function ($v) {
                        return $v !== null && $v !== '';
                    }));
                    $totalVacancy = array_sum($generalVacancyArray);
                } else {
                    $totalVacancy = (int) $row->no_of_vacancy;
                    $generalVacancyArray = [$totalVacancy];
                }
            } else {
                $isCasteWise = true;
                $selected_castes[$row->fk_caste_id] = $row->no_of_vacancy;
            }
        }

        // Prefer total vacancy from post_vacancy_map for hydration (JSON-aware)
        if ($generalVacancyArray !== null) {
            $total_vacancy_array = $generalVacancyArray;
        }

        // Urban groups (multi city/ward) – build after vacancy data so totals are available
        $city_code_array = [];
        if ($post->std_nnn_code) {
            $decodedCity = json_decode($post->std_nnn_code, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedCity)) {
                $city_code_array = array_values(array_filter($decodedCity));
            } else {
                $city_code_array = [$post->std_nnn_code];
            }
        }

        $urban_groups = [];
        $ward_matrix_by_city = [];
        foreach ($city_code_array as $cityCode) {
            $ward_matrix_by_city[] = $ward_by_city[$cityCode] ?? [];
        }
        $ward_matrix_for_groups = $ward_matrix_by_city;
        $ward_matrix = $ward_matrix_by_city;

        foreach ($city_code_array as $idx => $cityCode) {
            $wardEntry = $ward_matrix_for_groups[$idx] ?? $ward_matrix_for_groups;
            $wardCodes = [];

            if (is_array($wardEntry)) {
                $wardCodes = $wardEntry;
            } elseif (!empty($wardEntry)) {
                $wardCodes = [$wardEntry];
            }

            $urban_groups[] = [
                'city_nnn_code' => $cityCode,
                'ward_codes' => array_values(array_filter($wardCodes, function ($w) {
                    return $w !== null && $w !== '';
                })),
                'total_vacancy' => $total_vacancy_array[$idx] ?? ($total_vacancy_array[0] ?? null),
                'is_janman_area' => $is_janman_area_array[$idx] ?? ($is_janman_area_array[0] ?? null),
            ];
        }

        // Build normalized location group array for blade hydration
        $normalizedLocationGroups = [];

        if (!empty($location_groups)) {
            // Legacy groups already stored as objects; ensure totals/janman are attached when available
            foreach ($location_groups as $idx => $group) {
                $normalizedLocationGroups[] = [
                    'gp_nnn_code' => $group['gp_nnn_code'] ?? null,
                    'village_codes' => $group['village_codes'] ?? [],
                    'total_vacancy' => $group['total_vacancy'] ?? ($total_vacancy_array[$idx] ?? ($total_vacancy_array[0] ?? null)),
                    'is_janman_area' => $group['is_janman_area'] ?? ($is_janman_area_array[$idx] ?? ($is_janman_area_array[0] ?? null)),
                ];
            }
        } elseif (!empty($gp_nnn_code_array)) {
            foreach ($gp_nnn_code_array as $idx => $gpCode) {
                $normalizedLocationGroups[] = [
                    'gp_nnn_code' => $gpCode,
                    'village_codes' => $village_code_array ?? [],
                    'total_vacancy' => $total_vacancy_array[$idx] ?? ($total_vacancy_array[0] ?? null),
                    'is_janman_area' => $is_janman_area_array[$idx] ?? ($is_janman_area_array[0] ?? null),
                ];
            }
        }
        $location_groups = $normalizedLocationGroups;


        $selected_subjects = DB::table('post_subject_map')
            ->whereRaw('fk_post_id = ? ', [$post->post_id])
            ->pluck('fk_subject_id')
            ->toArray();  // Array of selected subject_ids


        $selected_skills = DB::table('post_skills_map')
            ->whereRaw('fk_post_id = ? ', [$post->post_id])
            ->pluck('fk_skill_id')
            ->toArray();

        $selected_organization_map = DB::table('post_organization_map')
            ->where('fk_post_id', $post->post_id)
            ->whereNotNull('post_organization_map.fk_organization_type_id')
            ->select('fk_organization_type_id', 'minimum_experiance_year')
            ->get()
            ->toArray();

        // dd($ward_no_array, $gp_nnn_code_array);
        // dd($post->File_Path);
        return view('admin.edit-post', [
            'post' => $post ?? null,
            'advertisements' => $advertisements ?? [],
            'advertisement_name' => $advertisement_name ?? null,
            'selected_districts' => $selected_districts ?? [],
            'districts' => $districts ?? [],
            'qualifications' => $qualifications ?? [],
            'categories' => $categories ?? [],
            'cat_name' => $cat_name ?? null,
            'file_path' => $post->File_Path ?? null,
            'parentQuestions' => $parentQuestions ?? [], // यहाँ $allQuestions को $parentQuestions से बदला गया है
            'selectedQuestionIds' => $selectedQuestionIds ?? [], // null की जगह खाली array पास करें
            'postId' => $postId ?? null,
            'select_no_of_vacancy' => $select_no_of_vacancy ?? null,
            'caste_category' => $caste_category ?? [],
            'subjects' => $subjects ?? [],
            'skills' => $skills ?? [],
            'org_types' => $org_types ?? [],
            'selected_subjects' => $selected_subjects ?? [],
            'selected_skills' => $selected_skills ?? [],
            'selected_organization_map' => $selected_organization_map ?? [],
            'selected_castes' => $selected_castes ?? [],
            'totalVacancy' => $totalVacancy ?? null,
            'isCasteWise' => $isCasteWise,
            'master_areas' => $master_areas ?? [],
            'gp_list' => $gp_list ?? [],
            'village_list' => $village_list ?? [],
            'nnn' => $nnn ?? null,
            'ward_list' => $ward_list ?? [],
            'gp_nnn_code_array' => $gp_nnn_code_array ?? [],
            'village_code_array' => $village_code_array ?? [],
            'total_vacancy_array' => $total_vacancy_array ?? [],
            'is_janman_area_array' => $is_janman_area_array ?? [],
            'nnn_exists' => $nnn !== null,
            'ward_no_array' => $ward_no_array ?? [],
            'ward_matrix' => $ward_matrix ?? [],
            'location_groups' => $location_groups ?? [],
            'urban_groups' => $urban_groups ?? [],
            'city_code_array' => $city_code_array ?? [],
            'readonly' => ($mode === 'view')

        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $uid = Session::get('uid', 0);
    //     $validator = Validator::make($request->all(), [
    //         'post_name' => 'required|string|max:255',
    //         'Advertisement_ID' => 'required',
    //         'master_area' => 'required|string',
    //         'fk_category_id' => 'required|integer',
    //         'fk_district_id' => 'nullable|array',
    //         'fk_district_id.*' => 'integer',
    //         'min_age' => 'required|integer|min:18|max:50',
    //         'max_age' => 'required|integer|min:18|max:50',
    //         'min_Qualification' => 'required|integer',
    //         'rules' => 'required|string',
    //         'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
    //         'questions' => 'required|array',
    //         'location_groups' => 'required_without:total_vacancy|array|min:1',
    //         'location_groups.*.gp_nnn_code' => 'required_without:total_vacancy|string',
    //         'location_groups.*.village_codes' => 'required_without:total_vacancy|array|min:1',
    //         'location_groups.*.village_codes.*' => 'string',
    //         'location_groups.*.total_vacancy' => 'required_without:total_vacancy|integer|min:1',
    //         'location_groups.*.is_janman_area' => 'required_without:total_vacancy|in:0,1',
    //     ]);

    //     $validator->after(function ($validator) use ($request) {
    //         $locationGroups = $request->input('location_groups', []);
    //         $totalVacancy = $request->input('total_vacancy');

    //         if (empty($totalVacancy) && (empty($locationGroups) || !is_array($locationGroups))) {
    //             $validator->errors()->add('location_groups', 'कृपया कम से कम एक ग्राम पंचायत/गाँव प्रविष्टि जोड़ें या कुल रिक्तियाँ भरें।');
    //         }
    //     });

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         DB::beginTransaction();
    //         $now = Carbon::now(); // Get current timestamp once

    //         // --- Update Post Master ---
    //         $oldFile = DB::table('master_post')->where('post_id', $id)->value('File_Path');
    //         $filePath = $oldFile;
    //         $fileName = DB::table('master_post')->where('post_id', $id)->value('file_name');

    //         if ($request->hasFile('file')) {
    //             $uploadedFile = $request->file('file');
    //             $fileName = $uploadedFile->getClientOriginalName();
    //             // Ensure UtilController is correctly imported or accessible
    //             $uploadFile = UtilController::upload_file(
    //                 $uploadedFile,
    //                 'file',
    //                 'uploads',
    //                 ['jpeg', 'jpg', 'png', 'pdf', 'doc', 'docx'], // Added doc, docx to allowed extensions
    //                 ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'] // Added mimes for doc, docx
    //             );
    //             $filePath = $uploadFile;
    //         }
    //         $area_id = DB::table('master_area')->where('area_name', $request->master_area)->first()->area_id;
    //         $immutablePost = DB::table('master_post')->where('post_id', $id)->first();
    //         $lockAutoFields = true;

    //         // Normalize location groups (Rural repeatable blocks)
    //         $locationGroupsInput = $request->input('location_groups', []);
    //         $hasLocationGroups = is_array($locationGroupsInput) && count($locationGroupsInput) > 0;

    //         $normalizedLocationGroups = [];
    //         $gpCodes = [];
    //         $villageCodes = [];
    //         $totalVacancyFromGroups = 0;
    //         $isJanmanAreaFromGroups = 0;
    //         $totalVacancyList = [];
    //         $janmanFlagList = [];

    //         if ($hasLocationGroups) {
    //             foreach ($locationGroupsInput as $group) {
    //                 $gpCode = $group['gp_nnn_code'] ?? null;
    //                 $villages = isset($group['village_codes']) && is_array($group['village_codes']) ? array_values(array_filter($group['village_codes'])) : [];
    //                 $vacancy = isset($group['total_vacancy']) ? (int) $group['total_vacancy'] : 0;
    //                 $janmanFlag = isset($group['is_janman_area']) ? (int) $group['is_janman_area'] : 0;

    //                 if (empty($gpCode) || empty($villages) || $vacancy <= 0) {
    //                     continue;
    //                 }

    //                 $normalizedLocationGroups[] = [
    //                     'gp_nnn_code' => $gpCode,
    //                     'village_codes' => $villages,
    //                     'total_vacancy' => $vacancy,
    //                     'is_janman_area' => $janmanFlag,
    //                 ];

    //                 $gpCodes[] = $gpCode;
    //                 $villageCodes = array_merge($villageCodes, $villages);
    //                 $totalVacancyFromGroups += $vacancy;
    //                 $totalVacancyList[] = $vacancy;
    //                 $janmanFlagList[] = $janmanFlag;
    //                 if ($janmanFlag === 1) {
    //                     $isJanmanAreaFromGroups = 1;
    //                 }
    //             }

    //             $villageCodes = array_values(array_unique($villageCodes));
    //         }

    //         $gp_nnn_code_value = $hasLocationGroups ? json_encode($normalizedLocationGroups) : ($request->gp_name ?? null);
    //         $village_code_value = $hasLocationGroups ? json_encode($villageCodes) : (isset($request->village_name) ? json_encode($request->village_name) : null);
    //         $is_janman_area = $hasLocationGroups ? json_encode($janmanFlagList) : $request->isJanmanArea;
    //         $totalVacancyToStore = $hasLocationGroups ? $totalVacancyFromGroups : $request->total_vacancy;
    //         $total_vacancy_value = $hasLocationGroups ? null : $request->total_vacancy;

    //         DB::table('master_post')
    //             ->where('post_id', $id)
    //             ->update([
    //                 'title' => $immutablePost->title ?? $request->post_name,
    //                 'Advertisement_ID' => $request->Advertisement_ID,
    //                 'fk_area_id' => $area_id,
    //                 'std_nnn_code' => $request->city_name ?? null,
    //                 'gp_nnn_code' => $gp_nnn_code_value,
    //                 'village_code' => $village_code_value,
    //                 'ward_no' => isset($request->ward_name) ? json_encode($request->ward_name) : null,
    //                 'cat_id' => $immutablePost->cat_id ?? $request->fk_category_id,
    //                 'min_age' => $immutablePost->min_age ?? $request->min_age,
    //                 'max_age' => $immutablePost->max_age ?? $request->max_age,
    //                 'max_age_relax' => $immutablePost->max_age_relax ?? $request->max_age_relax,
    //                 'Quali_ID' => $immutablePost->Quali_ID ?? $request->min_Qualification,
    //                 'guidelines' => $immutablePost->guidelines ?? $request->rules,
    //                 'file_name' => $fileName,
    //                 'File_Path' => $filePath,
    //                 'is_janman_area' => $is_janman_area,
    //                 'total_vacancy' => $total_vacancy_value,
    //                 'updated_at' => $now, // Manually update updated_at if not using Eloquent
    //                 'updated_by' => $uid,
    //             ]);


    //         if (!$lockAutoFields) {
    //             DB::table('post_question_map')->where('fk_post_id', $id)->update(['deleted_at' => $now]); // Mark all current as deleted

    //             $incomingQuestionIds = $request->input('questions', []);
    //             $existingQuestionMaps = DB::table('post_question_map')
    //                 ->where('fk_post_id', $id)
    //                 ->pluck('fk_ques_id')
    //                 ->toArray();

    //             foreach ($incomingQuestionIds as $quesId) {
    //                 // Check if this question was already associated (even if soft-deleted)
    //                 if (in_array($quesId, $existingQuestionMaps)) {
    //                     // If it existed, restore it (mark as not deleted)
    //                     DB::table('post_question_map')
    //                         ->where('fk_post_id', $id)
    //                         ->where('fk_ques_id', $quesId)
    //                         ->update(['deleted_at' => null, 'updated_at' => $now]);
    //                 } else {
    //                     // If it's a completely new question, insert it
    //                     DB::table('post_question_map')->insert([
    //                         'fk_ques_id' => $quesId,
    //                         'fk_post_id' => $id,
    //                         'ip_address' => $request->ip(),
    //                         'created_at' => $now,
    //                         'updated_at' => $now,
    //                         'deleted_at' => null, // Explicitly not deleted
    //                     ]);
    //                 }
    //             }
    //         }


    //         // --- Vacancy Map (Full Sync) ---
    //         $incomingVacancyData = $request->input('vacancy', []); // Caste-wise vacancies
    //         $totalVacancy = $totalVacancyToStore; // Overall total vacancy derived from groups or single input

    //         $currentVacancyMaps = DB::table('post_vacancy_map')
    //             ->where('fk_post_id', $id)
    //             ->pluck('no_of_vacancy', 'fk_caste_id') // Get current vacancies as [caste_id => count]
    //             ->toArray();

    //         $vacanciesToProcess = [];

    //         if (!empty($totalVacancy)) {
    //             // If total_vacancy is provided, it replaces all caste-wise.
    //             // Assuming fk_caste_id 0 is for total.
    //             $vacanciesToProcess[0] = (int) $totalVacancy;
    //         } else {
    //             // If caste-wise vacancies are provided
    //             foreach ($incomingVacancyData as $casteId => $count) {
    //                 if (!empty($count)) { // Only process if count is not empty
    //                     $vacanciesToProcess[(int) $casteId] = (int) $count;
    //                 }
    //             }
    //         }

    //         $currentCasteIds = array_keys($currentVacancyMaps);
    //         $incomingCasteIds = array_keys($vacanciesToProcess);

    //         $casteIdsToAdd = array_diff($incomingCasteIds, $currentCasteIds);
    //         $casteIdsToUpdate = array_intersect($incomingCasteIds, $currentCasteIds);
    //         $casteIdsToRemove = array_diff($currentCasteIds, $incomingCasteIds);

    //         // Delete removed vacancies
    //         if (!empty($casteIdsToRemove)) {
    //             DB::table('post_vacancy_map')
    //                 ->where('fk_post_id', $id)
    //                 ->whereIn('fk_caste_id', $casteIdsToRemove)
    //                 ->delete();
    //         }

    //         $dataToInsertVacancies = [];
    //         foreach ($vacanciesToProcess as $casteId => $count) {
    //             if (in_array($casteId, $casteIdsToAdd)) {
    //                 // New vacancy for this caste
    //                 $dataToInsertVacancies[] = [
    //                     'fk_post_id' => $id,
    //                     'fk_caste_id' => $casteId,
    //                     'no_of_vacancy' => $count,
    //                     'ip_address' => $request->ip(),
    //                     'created_at' => $now,
    //                     'updated_at' => $now,
    //                 ];
    //             } elseif (in_array($casteId, $casteIdsToUpdate)) {
    //                 // Existing vacancy, update if count changed
    //                 if ($currentVacancyMaps[$casteId] != $count) {
    //                     DB::table('post_vacancy_map')
    //                         ->where('fk_post_id', $id)
    //                         ->where('fk_caste_id', $casteId)
    //                         ->update([
    //                             'no_of_vacancy' => $count,
    //                             'updated_at' => $now,
    //                             'ip_address' => $request->ip(), // Update IP too if you track changes
    //                         ]);
    //                 }
    //             }
    //         }

    //         if (!empty($dataToInsertVacancies)) {
    //             DB::table('post_vacancy_map')->insert($dataToInsertVacancies);
    //         }


    //         // --- Subject Map (Full Sync) ---
    //         if (!$lockAutoFields) {
    //             $incomingSubjectIds = $request->input('subjects', []);
    //             $minQualificationId = $request->input('min_Qualification'); // Important: Qualification is part of the unique key

    //             $currentSubjectMaps = DB::table('post_subject_map')
    //                 ->where('fk_post_id', $id)
    //                 ->where('fk_qualification_id', $minQualificationId)
    //                 ->pluck('fk_subject_id')
    //                 ->toArray();

    //             $subjectsToAdd = array_diff($incomingSubjectIds, $currentSubjectMaps);
    //             $subjectsToRemove = array_diff($currentSubjectMaps, $incomingSubjectIds);

    //             // Delete subjects no longer in the request
    //             if (!empty($subjectsToRemove)) {
    //                 DB::table('post_subject_map')
    //                     ->where('fk_post_id', $id)
    //                     ->where('fk_qualification_id', $minQualificationId)
    //                     ->whereIn('fk_subject_id', $subjectsToRemove)
    //                     ->delete();
    //             }

    //             // Insert new subjects
    //             if (!empty($subjectsToAdd)) {
    //                 $dataToInsertSubjects = [];
    //                 foreach ($subjectsToAdd as $subId) {
    //                     $dataToInsertSubjects[] = [
    //                         'fk_post_id' => $id,
    //                         'fk_qualification_id' => $minQualificationId,
    //                         'fk_subject_id' => $subId,
    //                         'ip_address' => $request->ip(),
    //                         'created_at' => $now,
    //                         'updated_at' => $now,
    //                     ];
    //                 }
    //                 DB::table('post_subject_map')->insert($dataToInsertSubjects);
    //             }
    //         }


    //         // --- Skills Map (Full Sync) ---
    //         if (!$lockAutoFields) {
    //             $incomingSkillIds = $request->input('skills', []);

    //             $currentSkillMaps = DB::table('post_skills_map')
    //                 ->where('fk_post_id', $id)
    //                 ->pluck('fk_skill_id')
    //                 ->toArray();

    //             $skillsToAdd = array_diff($incomingSkillIds, $currentSkillMaps);
    //             $skillsToRemove = array_diff($currentSkillMaps, $incomingSkillIds);

    //             // Delete skills no longer in the request
    //             if (!empty($skillsToRemove)) {
    //                 DB::table('post_skills_map')
    //                     ->where('fk_post_id', $id)
    //                     ->whereIn('fk_skill_id', $skillsToRemove)
    //                     ->delete();
    //             }

    //             // Insert new skills
    //             if (!empty($skillsToAdd)) {
    //                 $dataToInsertSkills = [];
    //                 foreach ($skillsToAdd as $skillId) {
    //                     $dataToInsertSkills[] = [
    //                         'fk_post_id' => $id,
    //                         'fk_skill_id' => $skillId,
    //                         'ip_address' => $request->ip(),
    //                         'created_at' => $now,
    //                         'updated_at' => $now,
    //                     ];
    //                 }
    //                 DB::table('post_skills_map')->insert($dataToInsertSkills);
    //             }
    //         }

    //         // --- Organization Map (Full Sync) ---
    //         if (!$lockAutoFields) {
    //             $incomingOrgTypes = $request->input('org_types', []);
    //             $incomingExperiences = $request->input('experience', []);

    //             // Map incoming data for easier comparison
    //             $incomingOrgMap = [];
    //             foreach ($incomingOrgTypes as $key => $orgTypeId) {
    //                 $orgTypeId = (int) $orgTypeId; // Ensure integer type
    //                 $experience = (int) ($incomingExperiences[$key] ?? 0); // Ensure integer type, default to 0
    //                 if ($orgTypeId > 0) { // Only process valid org type IDs
    //                     $incomingOrgMap[$orgTypeId] = $experience;
    //                 }
    //             }

    //             // Get current organization maps keyed by fk_organization_type_id
    //             $currentOrgMaps = DB::table('post_organization_map')
    //                 ->where('fk_post_id', $id)
    //                 ->get(['fk_organization_type_id', 'minimum_experiance_year'])
    //                 ->keyBy('fk_organization_type_id') // Key the collection by organization type ID
    //                 ->toArray(); // Convert to array for easier manipulation

    //             $dataToInsertOrgs = [];

    //             foreach ($incomingOrgMap as $orgTypeId => $experience) {
    //                 if (isset($currentOrgMaps[$orgTypeId])) {
    //                     // This organization type exists in the database for this post
    //                     // currentOrgMaps[$orgTypeId] will be an array here due to get()->keyBy()->toArray()
    //                     if ($currentOrgMaps[$orgTypeId]->minimum_experiance_year != $experience) {
    //                         // Experience has changed, so update the record
    //                         DB::table('post_organization_map')
    //                             ->where('fk_post_id', $id)
    //                             ->where('fk_organization_type_id', $orgTypeId)
    //                             ->update([
    //                                 'minimum_experiance_year' => $experience,
    //                                 'updated_at' => $now,
    //                                 'ip_address' => $request->ip(),
    //                             ]);
    //                     }
    //                     // Remove this item from $currentOrgMaps as it has been processed (either updated or matched)
    //                     unset($currentOrgMaps[$orgTypeId]);
    //                 } else {
    //                     // This organization type is new for this post, prepare for insert
    //                     $dataToInsertOrgs[] = [
    //                         'fk_post_id' => $id,
    //                         'fk_organization_type_id' => $orgTypeId,
    //                         'minimum_experiance_year' => $experience,
    //                         'ip_address' => $request->ip(),
    //                         'created_at' => $now,
    //                         'updated_at' => $now,
    //                     ];
    //                 }
    //             }

    //             // Any remaining entries in $currentOrgMaps were not in the incoming request, so they should be deleted
    //             if (!empty($currentOrgMaps)) {
    //                 $orgTypeIdsToDelete = array_keys($currentOrgMaps);
    //                 DB::table('post_organization_map')
    //                     ->where('fk_post_id', $id)
    //                     ->whereIn('fk_organization_type_id', $orgTypeIdsToDelete)
    //                     ->delete();
    //             }

    //             // Perform batch insert for all new organization types
    //             if (!empty($dataToInsertOrgs)) {
    //                 DB::table('post_organization_map')->insert($dataToInsertOrgs);
    //             }
    //         }

    //         DB::commit();

    //         return response()->json(['status' => 'success', 'message' => 'पोस्ट सफलतापूर्वक अपडेट किया गया।']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'अपडेट विफल रहा।',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }



    public function update(Request $request, $id)
    {
        $uid = Session::get('uid', 0);
        $fin_id = Session::get('fin_id', 0); // fin_id add kiya gaya
        $district_id = Session::get('district_id', 0);

        $validator = Validator::make($request->all(), [
            'post_name' => 'required|string|max:255',
            'Advertisement_ID' => 'required',
            'master_area' => 'required|string',
            'fk_district_id' => 'nullable|array',
            'fk_district_id.*' => 'integer',
            'min_age' => 'required|integer|min:18|max:50',
            'max_age' => 'required|integer|min:18|max:50',
            'min_Qualification' => 'required|integer',
            'rules' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'questions' => 'required|array',
            'location_groups' => 'required_without_all:total_vacancy,urban_groups|array|min:1',
            'location_groups.*.gp_nnn_code' => 'required_with:location_groups|string',
            'location_groups.*.village_codes' => 'required_with:location_groups|array|min:1',
            'location_groups.*.village_codes.*' => 'string',
            'location_groups.*.total_vacancy' => 'required_with:location_groups|integer|min:1',
            'location_groups.*.is_janman_area' => 'required_with:location_groups|in:0,1',
            'urban_groups' => 'nullable|array',
            'urban_groups.*.city_nnn_code' => 'required_with:urban_groups|string',
            'urban_groups.*.ward_codes' => 'required_with:urban_groups|array|min:1',
            'urban_groups.*.ward_codes.*' => 'string',
            'urban_groups.*.total_vacancy' => 'required_with:urban_groups|integer|min:1',
        ]);

        $validator->after(function ($validator) use ($request) {
            $locationGroups = $request->input('location_groups', []);
            $totalVacancy = $request->input('total_vacancy');
            $urbanGroups = $request->input('urban_groups', []);

            if (empty($totalVacancy) && (empty($locationGroups) || !is_array($locationGroups))) {
                $validator->errors()->add('location_groups', 'कृपया कम से कम एक ग्राम पंचायत/गाँव प्रविष्टि जोड़ें या कुल रिक्तियाँ भरें।');
            }

            if ($request->master_area === 'Urban' && (empty($urbanGroups) || !is_array($urbanGroups))) {
                $validator->errors()->add('urban_groups', 'कृपया कम से कम एक शहर/वार्ड प्रविष्टि जोड़ें।');
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $now = now();
            $ipAddress = $request->ip();

            // --- File Handling (Same as Store) ---
            $postRecord = DB::table('master_post')->where('post_id', $id)->first();
            $fileName = $postRecord->file_name;
            $filePath = $postRecord->File_Path;

            if ($request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                $fileName = $uploadedFile->getClientOriginalName();
                $filePath = UtilController::upload_file(
                    $uploadedFile,
                    'file',
                    'uploads',
                    ['jpeg', 'jpg', 'png', 'pdf', 'doc', 'docx'],
                    ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                );
            }

            // --- Location Groups Processing (Store Logic Sync) ---
            $locationGroupsInput = $request->input('location_groups', []);
            $urbanGroupsInput = $request->input('urban_groups', []);
            $hasLocationGroups = is_array($locationGroupsInput) && count($locationGroupsInput) > 0;
            $hasUrbanGroups = ($request->master_area === 'Urban') && is_array($urbanGroupsInput) && count($urbanGroupsInput) > 0;

            $gpCodes = [];
            $villageCodes = [];
            $totalVacancyFromGroups = 0;
            $totalVacancyList = [];
            $janmanFlagList = [];

            $urbanCities = [];
            $urbanWardList = [];
            $urbanVacancyList = [];
            $totalVacancyFromUrbanGroups = 0;

            if ($hasLocationGroups) {
                foreach ($locationGroupsInput as $group) {
                    $gpCode = $group['gp_nnn_code'] ?? null;
                    $villages = isset($group['village_codes']) && is_array($group['village_codes']) ? array_values(array_filter($group['village_codes'])) : [];
                    $vacancy = isset($group['total_vacancy']) ? (int) $group['total_vacancy'] : 0;
                    $janmanFlag = isset($group['is_janman_area']) ? (int) $group['is_janman_area'] : 0;

                    if (empty($gpCode) || empty($villages) || $vacancy <= 0)
                        continue;

                    $gpCodes[] = $gpCode;
                    $villageCodes = array_merge($villageCodes, $villages);
                    $totalVacancyFromGroups += $vacancy;
                    $totalVacancyList[] = $vacancy;
                    $janmanFlagList[] = $janmanFlag;
                }
                $villageCodes = array_values(array_unique($villageCodes));
            }

            if ($hasUrbanGroups) {
                foreach ($urbanGroupsInput as $group) {
                    $cityCode = $group['city_nnn_code'] ?? null;
                    $wards = isset($group['ward_codes']) && is_array($group['ward_codes']) ? array_values(array_filter($group['ward_codes'])) : [];
                    $vacancy = isset($group['total_vacancy']) ? (int) $group['total_vacancy'] : 0;

                    if (empty($cityCode) || empty($wards) || $vacancy <= 0)
                        continue;

                    $wards = array_values(array_unique(array_map('intval', $wards)));

                    $urbanCities[] = $cityCode;
                    $urbanWardList[] = $wards;
                    $urbanVacancyList[] = $vacancy;
                    $totalVacancyFromUrbanGroups += $vacancy;
                }
            }

            // Store Logic Data Formatting
            $cleanStdNnnCode = $request->city_name;
            if ($cleanStdNnnCode === '-- चुनें --' || $cleanStdNnnCode === '' || $cleanStdNnnCode === null) {
                $cleanStdNnnCode = null;
            }

            if ($hasUrbanGroups) {
                $cleanStdNnnCode = json_encode($urbanCities);
            }

            $gp_nnn_code_value = $hasLocationGroups ? json_encode(array_values(array_unique($gpCodes))) : ($request->gp_name ?? null);
            $village_code_value = $hasLocationGroups ? json_encode($villageCodes) : (isset($request->village_name) ? json_encode($request->village_name) : null);
            $urbanWardFlatIds = [];
            foreach ($urbanWardList as $wardGroup) {
                $urbanWardFlatIds = array_merge($urbanWardFlatIds, $wardGroup);
            }
            $urbanWardFlatIds = array_values(array_unique($urbanWardFlatIds));

            $ward_value = $hasUrbanGroups ? json_encode($urbanWardFlatIds) : (isset($request->ward_name) ? json_encode($request->ward_name) : null);
            $is_janman_area = $hasLocationGroups
                ? json_encode($janmanFlagList)
                : ($hasUrbanGroups ? null : $request->isJanmanArea);

            $totalVacancyToStore = $hasLocationGroups
                ? $totalVacancyFromGroups
                : ($hasUrbanGroups ? $totalVacancyFromUrbanGroups : $request->total_vacancy);

            $area_id = DB::table('master_area')->where('area_name', $request->master_area)->first()->area_id;

            // --- Update master_post ---
            DB::table('master_post')->where('post_id', $id)->update([
                'title' => $request->post_name,
                'Advertisement_ID' => $request->Advertisement_ID,
                'fk_area_id' => $area_id,
                'std_nnn_code' => $cleanStdNnnCode,
                'gp_nnn_code' => $gp_nnn_code_value,
                'village_code' => $village_code_value,
                'ward_no' => $ward_value,
                'cat_id' => $request->fk_category_id ?? '1',
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'max_age_relax' => $request->max_age_relax,
                'Quali_ID' => $request->min_Qualification,
                'guidelines' => $request->rules,
                'file_name' => $fileName,
                'File_Path' => $filePath,
                'is_janman_area' => $is_janman_area,
                'updated_by' => $uid,
                'updated_at' => $now,
                'fk_fin_id' => $fin_id
            ]);

            // --- Questions Mapping (Sync) ---
            DB::table('post_question_map')->where('fk_post_id', $id)->delete();
            $questions = $request->questions;
            sort($questions);
            foreach ($questions as $questionId) {
                DB::table('post_question_map')->insert([
                    'fk_ques_id' => $questionId,
                    'fk_post_id' => $id,
                    'ip_address' => $ipAddress,
                    'created_at' => $now
                ]);
            }

            // --- Vacancy Map (Store Logic Sync) ---
            // --- Vacancy Map (Store Logic Sync Fix) ---
            DB::table('post_vacancy_map')->where('fk_post_id', $id)->delete();

            if (!empty($totalVacancyToStore)) {
                $noOfVacancyValue = $totalVacancyToStore;

                // In dono blocks ka hona zaroori hai sync ke liye
                if ($hasLocationGroups && count($totalVacancyList) > 0) {
                    $noOfVacancyValue = json_encode($totalVacancyList);
                } elseif ($hasUrbanGroups && count($urbanVacancyList) > 0) { // Ye block update me add karein
                    $noOfVacancyValue = json_encode($urbanVacancyList);
                }

                DB::table('post_vacancy_map')->insert([
                    'fk_post_id' => $id,
                    'fk_caste_id' => 0,
                    'no_of_vacancy' => $noOfVacancyValue,
                    'ip_address' => $ipAddress,
                    'created_at' => $now
                ]);
            } else {
                $vacancies = $request->input('vacancy', []);
                foreach ($vacancies as $key => $val) {
                    DB::table('post_vacancy_map')->insert([
                        'fk_post_id' => $id,
                        'fk_caste_id' => $key,
                        'no_of_vacancy' => $val,
                        'ip_address' => $ipAddress,
                        'created_at' => $now
                    ]);
                }
            }

            // --- Subjects, Skills, Org Maps (Cleanup and Re-insert like Store) ---
            DB::table('post_subject_map')->where('fk_post_id', $id)->delete();
            if ($request->filled('subjects')) {
                foreach ($request->subjects as $subjectId) {
                    DB::table('post_subject_map')->insert([
                        'fk_post_id' => $id,
                        'fk_qualification_id' => $request->min_Qualification,
                        'fk_subject_id' => $subjectId,
                        'ip_address' => $ipAddress,
                        'created_at' => $now
                    ]);
                }
            }

            DB::table('post_skills_map')->where('fk_post_id', $id)->delete();
            if ($request->filled('skills')) {
                foreach ($request->skills as $skillId) {
                    DB::table('post_skills_map')->insert([
                        'fk_post_id' => $id,
                        'fk_skill_id' => $skillId,
                        'ip_address' => $ipAddress,
                        'created_at' => $now
                    ]);
                }
            }

            DB::table('post_organization_map')->where('fk_post_id', $id)->delete();
            if ($request->filled('org_types') && is_array($request->org_types)) {
                foreach ($request->org_types as $key => $org_type) {
                    $experience = $request->experience[$key] ?? null;
                    DB::table('post_organization_map')->insert([
                        'fk_post_id' => $id,
                        'fk_organization_type_id' => $org_type,
                        'minimum_experiance_year' => $experience,
                        'ip_address' => $ipAddress,
                        'created_at' => $now
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'पोस्ट सफलतापूर्वक अपडेट किया गया।']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'अपडेट विफल रहा।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function application_list($pref_dist = null)
    {
        // dd($pref_dist);
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id');
        $project_code = Session::get('project_code', 0);

        if ($role === 'Super_admin' && $district_id) {

            $post_lists = DB::select("select * from master_post where project_code = ?", [$project_code]);
            $advertisment_lists = DB::select("select * from master_advertisement where district_lgd_code = ? and project_code = ?", [$district_id, $project_code]);
        } elseif ($role === 'Super_admin') {
            $advertisment_lists = DB::select("select * from master_advertisement");

            $post_lists = DB::select("select * from master_post where project_code = ?", [$project_code]);
        } elseif ($role === "Admin") {
            $advertisment_lists = DB::select("select * from master_advertisement where district_lgd_code = ? and project_code = ?", [$district_id, $project_code]);

            $post_lists = DB::select("select * from master_post where project_code = ?", [$project_code]);
        }


        return view('admin.application_list', compact('advertisment_lists', 'post_lists'));
    }


    public function view_application_detail(Request $request, $applicant_id = 0, $application_id = 0, $pref_dist = null)
    {
        // dd($request->district_id);
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id');
        $project_code = Session::get('project_code', 0);

        $cdpoIdParam = $request->get('cdpo_id'); // Project code
        $districtIdParam = $request->get('pref_dist'); // District code

        // dd($districtIdParam);

        if ($request->ajax()) {
            $cdpoIdParam = $request->get('cdpo_id'); // Project code
            $districtIdParam = $request->get('pref_dist'); // District code

            // dd($cdpoIdParam);
            if ($role === 'Admin') {

                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number


                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'master_advertisement.Advertisement_Title AS Advertisement_Title',
                        'master_post.title AS Title',
                        'master_panchayats.panchayat_name_hin AS Gram_Panchayat',
                        'master_nnn.nnn_name AS Nagar_Nikay',
                        'master_user.Mobile_Number AS Mobile_Number',
                        'tbl_user_post_apply.apply_date AS App_Date',
                        'tbl_user_post_apply.status AS Application_Status',
                        // 'tbl_user_post_apply.self_attested_file AS self_attested_file',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->leftJoin('master_panchayats', 'tbl_user_post_apply.post_gp', '=', 'master_panchayats.panchayat_lgd_code')
                    ->leftJoin('master_nnn', 'tbl_user_post_apply.post_nagar', '=', 'master_nnn.std_nnn_code')
                    ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                    ->where('tbl_user_post_apply.fk_district_id', $district_id)
                    ->orderBy('tbl_user_detail.ID', 'asc');

                // dd($query);
                if (!$query) {
                    return back()->with('error', 'Applicant details not found!');
                }

                // Filters
                if (!empty($request->applicant_name)) {
                    $query->where('tbl_user_detail.First_Name', 'like', '%' . $request->applicant_name . '%');
                }

                if (!empty($request->post_tittle)) {
                    $query->where('master_post.title', 'like', '%' . $request->post_tittle . '%');
                }

                if (!empty($request->advertisement_title)) {
                    $query->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->advertisement_title . '%');
                }
                if (!empty($request->apply_date)) {
                    $query->where('tbl_user_post_apply.apply_date', 'like', '%' . $request->apply_date . '%');
                }
                if (!empty($request->status)) {
                    $query->where('tbl_user_post_apply.status', 'like', '%' . $request->status . '%');
                }
                if (!empty($request->gram_panchayat)) {
                    $query->where('master_panchayats.panchayat_name_hin', 'like', '%' . $request->gram_panchayat . '%');
                }
                if (!empty($request->nagar_nikay)) {
                    $query->where('master_nnn.nnn_name', 'like', '%' . $request->nagar_nikay . '%');
                }

                // Handle notification-specific filtering
                if (!empty($request->from_notification) && $request->from_notification == '1') {
                    if (!empty($request->advertisement_id)) {
                        $query->where('master_advertisement.Advertisement_ID', $request->advertisement_id);
                    }
                    if (!empty($request->days_after_expiry)) {
                        // Filter for applications that are still pending after advertisement expiry
                        $query->whereRaw('DATEDIFF(CURDATE(), master_advertisement.Date_For_Age) > 21')
                            ->where('tbl_user_post_apply.status', 'Submitted');
                    }
                }

                $query = $query->get();


                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $encryptedId = $row->EncryptedID;
                        $encryptedAppId = $row->Application_Id;

                        $viewBtn = '<a href="/admin/final-application-detail/' . $encryptedId . '/' . $encryptedAppId . '">
                        <button class="btn btn-info btn-sm text-white">
                            <i class="ri-eye-line"></i>
                        </button>
                    </a>';

                        return $viewBtn;
                    })
                    // ->rawColumns(['action', 'self_attested_file']) //  HTML render hone ke liye zaroori hai
                    ->make(true);
            } elseif ($role === 'Super_admin' && $district_id) {

                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number


                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'master_advertisement.Advertisement_Title AS Advertisement_Title',
                        'master_post.title AS Title',
                        'master_panchayats.panchayat_name_hin AS Gram_Panchayat',
                        'master_nnn.nnn_name AS Nagar_Nikay',
                        'master_user.Mobile_Number AS Mobile_Number',
                        'tbl_user_post_apply.apply_date AS App_Date',
                        'tbl_user_post_apply.status AS Application_Status',
                        // 'tbl_user_post_apply.self_attested_file AS self_attested_file',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->leftJoin('master_panchayats', 'tbl_user_post_apply.post_gp', '=', 'master_panchayats.panchayat_lgd_code')
                    ->leftJoin('master_nnn', 'tbl_user_post_apply.post_nagar', '=', 'master_nnn.std_nnn_code')
                    ->where('master_post.project_code', $project_code)
                    ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                    ->orderBy('tbl_user_detail.ID', 'asc');



                if (!$query) {
                    return back()->with('error', 'Applicant details not found!');
                }

                // Filters
                if (!empty($request->applicant_name)) {
                    $query->where('tbl_user_detail.First_Name', 'like', '%' . $request->applicant_name . '%');
                }

                if (!empty($request->post_tittle)) {
                    $query->where('master_post.title', 'like', '%' . $request->post_tittle . '%');
                }

                if (!empty($request->advertisement_title)) {
                    $query->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->advertisement_title . '%');
                }
                if (!empty($request->apply_date)) {
                    $query->where('tbl_user_post_apply.apply_date', 'like', '%' . $request->apply_date . '%');
                }
                if (!empty($request->status)) {
                    $query->where('tbl_user_post_apply.status', 'like', '%' . $request->status . '%');
                }
                if (!empty($request->gram_panchayat)) {
                    $query->where('master_panchayats.panchayat_name_hin', 'like', '%' . $request->gram_panchayat . '%');
                }
                if (!empty($request->nagar_nikay)) {
                    $query->where('master_nnn.nnn_name', 'like', '%' . $request->nagar_nikay . '%');
                }

                $query = $query->get();

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $encryptedId = $row->EncryptedID;
                        $encryptedAppId = $row->Application_Id;

                        $viewBtn = '<a href="/admin/final-application-detail/' . $encryptedId . '/' . $encryptedAppId . '">
                        <button class="btn btn-info btn-sm text-white">
                            <i class="ri-eye-line"></i>
                        </button>
                    </a>';

                        return $viewBtn;
                    })
                    // ->rawColumns(['action', 'self_attested_file']) //  HTML render hone ke liye zaroori hai
                    ->make(true);
            } elseif ($role === 'Super_admin') {

                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number


                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'master_advertisement.Advertisement_Title AS Advertisement_Title',
                        'master_post.title AS Title',
                        'master_panchayats.panchayat_name_hin AS Gram_Panchayat',
                        'master_nnn.nnn_name AS Nagar_Nikay',
                        'master_user.Mobile_Number AS Mobile_Number',
                        'tbl_user_post_apply.apply_date AS App_Date',
                        'tbl_user_post_apply.status AS Application_Status',
                        // 'tbl_user_post_apply.self_attested_file AS self_attested_file',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->leftJoin('master_panchayats', 'tbl_user_post_apply.post_gp', '=', 'master_panchayats.panchayat_lgd_code')
                    ->leftJoin('master_nnn', 'tbl_user_post_apply.post_nagar', '=', 'master_nnn.std_nnn_code')
                    ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                    ->orderBy('tbl_user_detail.ID', 'asc');
                // dd($districtIdParam);
                if (is_numeric($districtIdParam)) {
                    // dd($districtIdParam);
                    $query->where('tbl_user_post_apply.fk_district_id', '=', $districtIdParam);
                }

                if ($cdpoIdParam) {
                    // dd($cdpoIdParam);
                    $query->where('tbl_user_post_apply.post_projects', '=', $cdpoIdParam);
                }

                if (!$query) {
                    return back()->with('error', 'Applicant details not found!');
                }

                // Filters
                if (!empty($request->applicant_name)) {
                    $query->where('tbl_user_detail.First_Name', 'like', '%' . $request->applicant_name . '%');
                }

                if (!empty($request->post_tittle)) {
                    $query->where('master_post.title', 'like', '%' . $request->post_tittle . '%');
                }

                if (!empty($request->advertisement_title)) {
                    $query->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->advertisement_title . '%');
                }
                if (!empty($request->apply_date)) {
                    $query->where('tbl_user_post_apply.apply_date', 'like', '%' . $request->apply_date . '%');
                }
                if (!empty($request->status)) {
                    $query->where('tbl_user_post_apply.status', 'like', '%' . $request->status . '%');
                }
                if (!empty($request->gram_panchayat)) {
                    $query->where('master_panchayats.panchayat_name_hin', 'like', '%' . $request->gram_panchayat . '%');
                }
                if (!empty($request->nagar_nikay)) {
                    $query->where('master_nnn.nnn_name', 'like', '%' . $request->nagar_nikay . '%');
                }

                // Handle notification-specific filtering
                if (!empty($request->from_notification) && $request->from_notification == '1') {
                    if (!empty($request->advertisement_id)) {
                        $query->where('master_advertisement.Advertisement_ID', $request->advertisement_id);
                    }
                    if (!empty($request->days_after_expiry)) {
                        // Filter for applications that are still pending after advertisement expiry
                        $query->whereRaw('DATEDIFF(CURDATE(), master_advertisement.Date_For_Age) > 21')
                            ->where('tbl_user_post_apply.status', 'Submitted');
                    }
                }

                $query = $query->get();
                // dd($query);

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $encryptedId = $row->EncryptedID;
                        $encryptedAppId = $row->Application_Id;

                        $viewBtn = '<a href="/admin/final-application-detail/' . $encryptedId . '/' . $encryptedAppId . '">
                        <button class="btn btn-info btn-sm text-white">
                            <i class="ri-eye-line"></i>
                        </button>
                    </a>';

                        return $viewBtn;
                    })
                    // ->rawColumns(['action', 'self_attested_file']) //  HTML render hone ke liye zaroori hai
                    ->make(true);
            }
        }


        $post_area = DB::select("SELECT post_area FROM tbl_user_post_apply WHERE MD5(apply_id) = ?", [$application_id]);
        $postArea_Check = $post_area[0]->post_area;
        $applicant_details = '';

        if ($post_area && in_array($postArea_Check, [1, 2])) {

            $applicant_details = DB::table('tbl_user_detail')
                ->select(
                    'tbl_user_detail.ID AS RowID',
                    'master_post.title',
                    'tbl_user_detail.*',
                    'master_user.*',
                    'tbl_user_post_apply.*',
                    'master_district.name As Dist_name',
                    'tbl_user_post_apply.apply_id as apply_id'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                ->whereRaw("MD5(tbl_user_detail.ID) = ?", [$applicant_id])
                ->whereRaw("MD5(tbl_user_post_apply.apply_id) = ?", [$application_id]);

            if ($postArea_Check == 1) {
                $applicant_details
                    ->addSelect(
                        'master_blocks.block_name_hin',
                        'master_panchayats.panchayat_name_hin',
                        'master_villages.village_name_hin'
                    )
                    ->join('master_blocks', 'tbl_user_post_apply.post_block', '=', 'master_blocks.block_lgd_code')
                    ->join('master_panchayats', 'tbl_user_post_apply.post_gp', '=', 'master_panchayats.panchayat_lgd_code')
                    ->join('master_villages', 'tbl_user_post_apply.post_village', '=', 'master_villages.village_code');
            } elseif ($postArea_Check == 2) {
                $applicant_details
                    ->addSelect(
                        'master_nnn.nnn_name',
                        'master_ward.ward_name'
                    )
                    ->join('master_nnn', 'tbl_user_post_apply.post_nagar', '=', 'master_nnn.std_nnn_code')
                    ->join('master_ward', function ($join) {
                        $join->on('tbl_user_post_apply.post_ward', '=', 'master_ward.ID')
                            ->on('master_nnn.std_nnn_code', '=', 'master_ward.std_nnn_code');
                    });
            }

            $applicant_details = $applicant_details->first();

            // dd($applicant_details);
        }



        // Fetch multiple Education separately
        $education_details = DB::table('tbl_applicant_education_qualification')
            ->select('tbl_applicant_education_qualification.*', 'master_qualification.Quali_Name')
            ->join('master_qualification', 'tbl_applicant_education_qualification.fk_Quali_ID', '=', 'master_qualification.Quali_ID')
            //  ->join('master_subjects', 'tbl_applicant_education_qualification.fk_subject_id', '=', 'master_subjects.subject_id')
            ->where('tbl_applicant_education_qualification.fk_applicant_id', '=', $applicant_details->RowID)
            ->get();


        // Fetch multiple experiences separately
        $experience_details = DB::table('tbl_applicant_experience_details')
            ->where('Applicant_ID', $applicant_details->RowID ? $applicant_details->RowID : 0)
            ->get();


        // $Post_questionAnswer = DB::table('tbl_user_post_question_answer as answer')
        //     ->select('apply.fk_post_id', 'questions.ques_ID', 'questions.ques_name', 'answer.answer','questions.ans_type')
        //     ->join('tbl_user_post_apply as apply', function ($join) {
        //         $join->on('answer.post_id', '=', 'apply.fk_post_id')
        //             ->on('answer.applicant_id', '=', 'apply.fk_applicant_id');
        //     })
        //     ->leftJoin('post_question_map as question_map', 'answer.post_map_id', '=', 'question_map.post_map_id')
        //     ->leftJoin('master_post_questions as questions', 'question_map.fk_ques_id', '=', 'questions.ques_ID')
        //     ->where('apply.apply_id', $applicant_details->apply_id)
        //     ->whereNull('question_map.deleted_at')
        //     ->get();

        $Post_questionAnswer = DB::table('tbl_user_post_question_answer as answer')
            ->select(
                'apply.fk_post_id',
                'questions.ques_ID',
                'questions.ques_name',
                'questions.ans_type',
                'answer.date_From',
                'answer.date_To',
                DB::raw("CASE 
                    WHEN questions.ans_type = 'M' 
                    THEN GROUP_CONCAT(answer.answer ORDER BY answer.answer SEPARATOR ', ')
                    ELSE answer.answer
                 END as answer_list")
            )
            ->join('tbl_user_post_apply as apply', function ($join) {
                $join->on('answer.post_id', '=', 'apply.fk_post_id')
                    ->on('answer.applicant_id', '=', 'apply.fk_applicant_id');
            })
            ->leftJoin('post_question_map as question_map', 'answer.post_map_id', '=', 'question_map.post_map_id')
            ->leftJoin('master_post_questions as questions', 'question_map.fk_ques_id', '=', 'questions.ques_ID')
            ->where('apply.apply_id', $applicant_details->apply_id)
            ->whereNull('question_map.deleted_at')
            ->groupBy('apply.fk_post_id', 'questions.ques_ID', 'questions.ques_name', 'questions.ans_type')
            ->get();




        $Skill_Ans = DB::table('tbl_post_skill_answer as ans')
            ->join('master_skills as ms', 'ans.fk_skill_id', '=', 'ms.skill_id')
            ->join('tbl_user_post_apply as apply', 'ans.fk_apply_id', '=', 'apply.apply_id')
            ->whereRaw('MD5(ans.fk_apply_id) = ?', [$application_id])
            ->select('ans.skill_ans_id', 'ms.skill_name as SkillName', 'ans.skill_answers as SkillAnswer')
            ->get();

        // dd($Post_questionAnswer);




        return view('admin.view_app_detail', compact('applicant_details', 'experience_details', 'education_details', 'Post_questionAnswer', 'Skill_Ans', 'postArea_Check'));
    }

    public function all_application_list(Request $request, $pref_dist = null)
    {
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");
        $project_code = Session::get('project_code', 0);

        $cdpoIdParam = $request->get('cdpo_id'); // Project code
        $awcIdParam = $request->get('awc_id'); // Village code

        // dd($cdpoIdParam, $awcIdParam, $districtIdParam);

        $advertisment_lists = DB::select("select * from master_advertisement");
        $post_lists = DB::select("select * from master_post");

        if ($role === 'Super_admin') {
            if ($pref_dist || $district_id) {
                if ($district_id) {
                    $all_application_lists = DB::table('tbl_user_detail')
                        ->select(
                            'tbl_user_detail.ID AS RowID',
                            DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                            'tbl_user_detail.ID',
                            'tbl_user_detail.Created_On AS Application_date',
                            'master_user.Mobile_Number',
                            'tbl_user_post_apply.status AS Application_Status',
                            'tbl_user_post_apply.apply_id',
                            'master_district.name AS pref_district_name',
                            'master_post.title',
                            DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                            DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                        )
                        ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                        ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                        ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                        ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                        ->where('tbl_user_post_apply.is_final_submit', '1')
                        ->where('master_post.project_code', $project_code)
                        ->where('tbl_user_post_apply.status', 'Submitted')
                        ->where('tbl_user_post_apply.fk_district_id', $district_id)
                        ->get();
                } else {

                    // DB::enableQueryLog(); // Enable query log for debugging
                    $all_application_lists = DB::table('tbl_user_detail')
                        ->select(
                            'tbl_user_detail.ID AS RowID',
                            DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                            'tbl_user_detail.ID',
                            'tbl_user_detail.Created_On AS Application_date',
                            'master_user.Mobile_Number',
                            'tbl_user_post_apply.status AS Application_Status',
                            'tbl_user_post_apply.apply_id',
                            'master_district.name AS pref_district_name',
                            'master_post.title',
                            DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                            DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                        )
                        ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                        ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                        ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                        ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                        ->where('tbl_user_post_apply.status', 'Submitted')
                        ->where('tbl_user_post_apply.is_final_submit', '1')
                        ->where('tbl_user_post_apply.fk_district_id', $pref_dist); // ✅ Condition on tbl_user_post_apply

                    // dd($cdpoIdParam);
                    // Apply drill-down filters if present
                    if ($cdpoIdParam) {
                        // dd($cdpoIdParam);
                        $all_application_lists->where('master_post.project_code', $cdpoIdParam);
                    }
                    $all_application_lists = $all_application_lists->get();

                    // dd(DB::getQueryLog()); // Dump the query log for debugging
                }
            } else {
                $all_application_lists = DB::table('tbl_user_detail')
                    ->select(
                        'tbl_user_detail.ID AS RowID',
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'tbl_user_detail.ID',
                        'tbl_user_detail.Created_On AS Application_date',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status',
                        'tbl_user_post_apply.apply_id',
                        'master_post.title',
                        DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', '=', 'Submitted')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->get();
            }
        } elseif ($role === 'Admin') {
            $all_application_lists = DB::table('tbl_user_detail')
                ->select(
                    'tbl_user_detail.ID AS RowID',
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'tbl_user_detail.ID',
                    'tbl_user_detail.Created_On AS Application_date',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status',
                    'tbl_user_post_apply.apply_id',
                    'master_district.name AS pref_district_name',
                    'master_post.title',
                    DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                    DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.status', 'Submitted')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id) // ✅ Condition on tbl_user_post_apply
                ->get();
        }
        return view('admin.application_list', compact('all_application_lists', 'advertisment_lists', 'post_lists'));
    }

    public function dist_application_list(Request $request, $pref_dist = null)
    {
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id');

        // Get drill-down filter parameters from query string
        $districtIdParam = $request->get('district_id');
        $cdpoIdParam = $request->get('cdpo_id'); // Project code
        $awcIdParam = $request->get('awc_id'); // Village code

        if ($role === 'Super_admin') {
            if ($pref_dist || $districtIdParam) {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        DB::raw('MD5(tbl_user_detail.ID) AS EncryptedID'),
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'master_advertisement.Advertisement_Title AS Advertisement_Title',
                        'master_post.title AS Title',
                        'master_user.Mobile_Number AS Mobile_Number',
                        'tbl_user_post_apply.apply_date AS App_Date',
                        'tbl_user_post_apply.status AS Application_Status',
                        DB::raw('MD5(tbl_user_post_apply.apply_id) AS Application_Id'),
                        'master_district.name AS District_Name'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                    ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->where('tbl_user_post_apply.is_final_submit', '1');

                // Apply district filter - support both MD5 and direct district_id
                if ($pref_dist) {
                    $query->whereRaw('MD5(tbl_user_post_apply.fk_district_id) = ?', [$pref_dist]);
                } elseif ($districtIdParam) {
                    $query->where('tbl_user_post_apply.fk_district_id', $districtIdParam);
                }

                // Apply project filter if provided
                if ($cdpoIdParam) {
                    $query->where('master_post.project_code', $cdpoIdParam);
                }


                $application_lists = $query->orderBy('tbl_user_detail.ID', 'asc')->get();


                // Add action button for each row
                foreach ($application_lists as $row) {
                    $encryptedId = $row->EncryptedID;
                    $encryptedAppId = $row->Application_Id;

                    $row->action = '<a href="/admin/final-application-detail/' . $encryptedId . '/' . $encryptedAppId . '">
                   <button class="btn btn-info btn-sm">View Application</button>
                </a>';
                }

                // Return in DataTables format
                return response()->json([
                    "draw" => intval($request->input('draw')),
                    "recordsTotal" => count($application_lists),
                    "recordsFiltered" => count($application_lists),
                    "data" => $application_lists
                ]);
            } else {
                $application_lists = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        DB::raw('MD5(tbl_user_detail.ID) AS EncryptedID'),
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'master_advertisement.Advertisement_Title AS Advertisement_Title',
                        'master_post.title AS Title',
                        'master_user.Mobile_Number AS Mobile_Number',
                        'tbl_user_post_apply.apply_date AS App_Date',
                        'tbl_user_post_apply.status AS Application_Status',
                        DB::raw('MD5(tbl_user_post_apply.apply_id) AS Application_Id'),
                        'master_district.name AS District_Name'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    // yeh post_map join hata do agar zarurat nahi ho
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                    ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->orderBy('tbl_user_detail.ID', 'asc')
                    ->get();


                // Add action button for each row
                foreach ($application_lists as $row) {
                    $encryptedId = $row->EncryptedID;
                    $encryptedAppId = $row->Application_Id;

                    $row->action = '<a href="/admin/final-application-detail/' . $encryptedId . '/' . $encryptedAppId . '">
                   <button class="btn btn-info btn-sm">View Application</button>
                </a>';
                }

                // Return in DataTables format
                return response()->json([
                    "draw" => intval($request->input('draw')),
                    "recordsTotal" => count($application_lists),
                    "recordsFiltered" => count($application_lists),
                    "data" => $application_lists
                ]);
            }
        }

        // If no permission or district, return empty
        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ]);
    }

    public function verified_list(Request $request, $pref_dist = null)
    {
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");
        $project_code = Session::get('project_code', 0);

        $cdpoIdParam = $request->get('cdpo_id'); // Project code
        $awcIdParam = $request->get('awc_id'); // Village code

        // dd($cdpoIdParam, $awcIdParam, $districtIdParam);


        if ($role === 'Super_admin') {
            if ($pref_dist || $district_id) {
                if ($district_id) {
                    $verified_lists = DB::table('tbl_user_detail')
                        ->select(
                            'tbl_user_detail.ID AS RowID',
                            DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                            'tbl_user_detail.ID',
                            'tbl_user_detail.Created_On AS Application_date',
                            'master_user.Mobile_Number',
                            'tbl_user_post_apply.status AS Application_Status',
                            'tbl_user_post_apply.apply_id',
                            'master_district.name AS pref_district_name',
                            'master_post.title',
                            DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                            DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                        )
                        ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                        ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                        ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                        ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                        ->where('tbl_user_post_apply.is_final_submit', '1')
                        ->where('master_post.project_code', $project_code)
                        ->where('tbl_user_post_apply.status', 'Verified')
                        ->where('tbl_user_post_apply.fk_district_id', $district_id)
                        ->get();
                } else {
                    // DB::enableQueryLog();
                    $verified_lists = DB::table('tbl_user_detail')
                        ->select(
                            'tbl_user_detail.ID AS RowID',
                            DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                            'tbl_user_detail.ID',
                            'tbl_user_detail.Created_On AS Application_date',
                            'master_user.Mobile_Number',
                            'tbl_user_post_apply.status AS Application_Status',
                            'tbl_user_post_apply.apply_id',
                            'master_district.name AS pref_district_name',
                            'master_post.title',
                            DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                            DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                        )
                        ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                        ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                        ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                        ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                        ->where('tbl_user_post_apply.status', 'Verified')
                        ->where('tbl_user_post_apply.is_final_submit', '1')
                        ->where('tbl_user_post_apply.fk_district_id', $pref_dist); // ✅ Condition on tbl_user_post_apply

                    // dd($cdpoIdParam);
                    // Apply drill-down filters if present
                    if ($cdpoIdParam) {
                        // dd($cdpoIdParam);
                        $verified_lists->where('master_post.project_code', $cdpoIdParam);
                    }
                    $verified_lists = $verified_lists->get();
                }
            } else {
                $verified_lists = DB::table('tbl_user_detail')
                    ->select(
                        'tbl_user_detail.ID AS RowID',
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'tbl_user_detail.ID',
                        'tbl_user_detail.Created_On AS Application_date',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status',
                        'tbl_user_post_apply.apply_id',
                        'master_post.title',
                        DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', '=', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->get();
            }
        } elseif ($role === 'Admin') {
            $verified_lists = DB::table('tbl_user_detail')
                ->select(
                    'tbl_user_detail.ID AS RowID',
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'tbl_user_detail.ID',
                    'tbl_user_detail.Created_On AS Application_date',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status',
                    'tbl_user_post_apply.apply_id',
                    'master_district.name AS pref_district_name',
                    'master_post.title',
                    DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                    DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.status', 'Verified')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id) // ✅ Condition on tbl_user_post_apply
                ->get();
        }
        return view('admin.verified_list', compact('verified_lists'));
    }


    public function rejected_list(Request $request, $pref_dist = null)
    {
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");

        $project_code = Session::get('project_code', 0);
        // Get drill-down filter parameters from query string
        $districtIdParam = $request->get('district_id');
        $cdpoIdParam = $request->get('cdpo_id'); // Project code
        $awcIdParam = $request->get('awc_id'); // Village code
        // dd($cdpoIdParam);

        if ($role === 'Super_admin') {
            if ($pref_dist || $district_id || $districtIdParam) {
                if ($district_id) {
                    $rejected_lists = DB::table('tbl_user_detail')
                        ->select(
                            'tbl_user_detail.ID AS RowID',
                            DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                            'tbl_user_detail.ID',
                            'tbl_user_detail.Created_On AS Application_date',
                            'master_user.Mobile_Number',
                            'tbl_user_post_apply.status AS Application_Status',
                            'tbl_user_post_apply.reason_rejection AS reason_rejection',
                            'tbl_user_post_apply.apply_id',
                            'master_district.name AS pref_district_name',
                            'master_post.title',
                            DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                            DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                        )
                        ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                        ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                        ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                        ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                        ->where('master_post.project_code', $project_code)
                        ->where('tbl_user_post_apply.status', 'Rejected')
                        ->where('tbl_user_post_apply.is_final_submit', '1')
                        ->where('tbl_user_post_apply.fk_district_id', $district_id)
                        ->get();
                } else {
                    $rejected_lists = DB::table('tbl_user_detail')
                        ->select(
                            'tbl_user_detail.ID AS RowID',
                            DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                            'tbl_user_detail.ID',
                            'tbl_user_detail.Created_On AS Application_date',
                            'master_user.Mobile_Number',
                            'tbl_user_post_apply.status AS Application_Status',
                            'tbl_user_post_apply.reason_rejection AS reason_rejection',
                            'tbl_user_post_apply.apply_id',
                            'master_district.name AS pref_district_name',
                            'master_post.title',
                            DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                            DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                        )
                        ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                        ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                        ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                        ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                        ->where('tbl_user_post_apply.status', 'Rejected')
                        ->where('tbl_user_post_apply.is_final_submit', '1')
                        ->where('tbl_user_post_apply.fk_district_id', $pref_dist);

                    if ($cdpoIdParam) {
                        // dd($cdpoIdParam);
                        $rejected_lists->where('master_post.project_code', $cdpoIdParam);
                    }
                    $rejected_lists = $rejected_lists->get();
                }
            } else {
                $rejected_lists = DB::table('tbl_user_detail')
                    ->select(
                        'tbl_user_detail.ID AS RowID',
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'tbl_user_detail.ID',
                        'tbl_user_detail.Created_On AS Application_date',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status',
                        'tbl_user_post_apply.reason_rejection AS reason_rejection',
                        'tbl_user_post_apply.apply_id',
                        'master_post.title',
                        DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', '=', 'Rejected')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->get();
            }
        } elseif ($role === 'Admin') {
            $rejected_lists = DB::table('tbl_user_detail')
                ->select(
                    'tbl_user_detail.ID AS RowID',
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'tbl_user_detail.ID',
                    'tbl_user_detail.Created_On AS Application_date',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status',
                    'tbl_user_post_apply.reason_rejection AS reason_rejection',
                    'tbl_user_post_apply.apply_id',
                    'master_district.name AS pref_district_name',
                    'master_post.title',
                    DB::raw("MD5(tbl_user_detail.ID) AS Encrypted_Applicant_ID"),
                    DB::raw("MD5(tbl_user_post_apply.apply_id) AS Encrypted_Apply_ID")
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.status', 'Rejected')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id) // ✅ Condition on tbl_user_post_apply
                ->get();
        }

        return view('admin/rejected_list', compact('rejected_lists'));
    }


    public function final_application_detail(Request $request, $applicant_id = 0, $application_id = 0)
    {


        if ($request->ajax()) {

            //  Ensure Row Number Works with Yajra DataTables
            DB::statement('SET @row := 0'); // Initialize Row Number


            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'master_post.title AS Title',
                    'master_user.Mobile_Number AS Mobile_Number',
                    'tbl_user_post_apply.apply_date AS App_Date',
                    'tbl_user_post_apply.status AS Application_Status',
                    DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id"),
                    // 'tbl_user_post_apply.self_attested_file'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                ->orderBy('tbl_user_detail.ID', 'desc')
                ->get();




            if (!$query) {
                return back()->with('error', 'Applicant details not found!');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = $row->EncryptedID;
                    $encryptedAppId = $row->Application_Id;

                    $viewBtn = '<a href="/admin/final-application-detail/' . $encryptedId . '/' . $encryptedAppId . '">
                        <button class="btn btn-info btn-sm text-white">
                            <i class="ri-eye-line"></i>
                        </button>
                    </a>';

                    return $viewBtn;
                })
                ->rawColumns(['action']) //  HTML render hone ke liye zaroori hai
                ->make(true);
        }

        $post_area = DB::select("SELECT post_area FROM tbl_user_post_apply WHERE MD5(apply_id) = ?", [$application_id]);
        $postArea_Check = $post_area[0]->post_area;
        $applicant_details = '';


        if ($post_area && in_array($postArea_Check, [1, 2])) {

            $applicant_details = DB::table('record_user_detail_map')
                ->select(
                    'record_user_detail_map.user_details_AI_ID AS RowID',
                    'record_user_detail_map.user_details_AI_ID AS user_row_id',
                    'record_user_detail_map.fk_apply_id AS fk_apply_id',
                    'master_post.title',
                    'master_district.name As Dist_name',
                    'record_user_detail_map.*',
                    'master_user.*',
                    'tbl_user_post_apply.*',
                    'tbl_user_post_apply.apply_id as apply_id',
                    'tbl_user_post_apply.fk_post_id AS fk_post_id_ind',
                    'tbl_user_post_apply.fk_applicant_id AS fk_applicant_id_ind'
                )
                ->join('master_user', 'master_user.ID', '=', 'record_user_detail_map.Applicant_ID')
                ->join('tbl_user_post_apply', 'record_user_detail_map.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                ->whereRaw("MD5(record_user_detail_map.user_details_AI_ID) = ?", [$applicant_id])
                ->whereRaw("MD5(tbl_user_post_apply.apply_id) = ?", [$application_id]);

            if ($postArea_Check == 1) {
                $applicant_details
                    ->addSelect(
                        'master_blocks.block_name_hin',
                        'master_panchayats.panchayat_name_hin',
                        'master_villages.village_name_hin'
                    )
                    ->join('master_blocks', 'tbl_user_post_apply.post_block', '=', 'master_blocks.block_lgd_code')
                    ->join('master_panchayats', 'tbl_user_post_apply.post_gp', '=', 'master_panchayats.panchayat_lgd_code')
                    ->join('master_villages', 'tbl_user_post_apply.post_village', '=', 'master_villages.village_code');
            } elseif ($postArea_Check == 2) {
                $applicant_details
                    ->addSelect(
                        'master_nnn.nnn_name',
                        'master_ward.ward_name'
                    )
                    ->join('master_nnn', 'tbl_user_post_apply.post_nagar', '=', 'master_nnn.std_nnn_code')
                    ->join('master_ward', function ($join) {
                        $join->on('tbl_user_post_apply.post_ward', '=', 'master_ward.ID')
                            ->on('master_nnn.std_nnn_code', '=', 'master_ward.std_nnn_code');
                    });
            }

            $applicant_details = $applicant_details->first();
        }


        // dd($applicant_details);
        // Fetch multiple Education separately
        $education_details = DB::table('record_applicant_edu_map')
            ->select('record_applicant_edu_map.*', 'master_qualification.Quali_Name')
            ->join('master_qualification', 'record_applicant_edu_map.fk_Quali_ID', '=', 'master_qualification.Quali_ID')
            ->where('record_applicant_edu_map.fk_apply_id', '=', $applicant_details->fk_apply_id)
            ->get();


        // Fetch multiple experiences separately
        $experience_details = DB::table('record_applicant_experience_map')
            ->where('fk_apply_id', $applicant_details->fk_apply_id ? $applicant_details->fk_apply_id : 0)
            ->get();



        // $Post_questionAnswer = DB::table('tbl_user_post_question_answer as answer')
        //     ->select('apply.fk_post_id', 'questions.ques_ID', 'questions.ques_name', 'answer.answer')
        //     ->join('tbl_user_post_apply as apply', function ($join) {
        //         $join->on('answer.post_id', '=', 'apply.fk_post_id')
        //             ->on('answer.applicant_id', '=', 'apply.fk_applicant_id');
        //     })
        //     ->leftJoin('post_question_map as question_map', 'answer.post_map_id', '=', 'question_map.post_map_id')
        //     ->leftJoin('master_post_questions as questions', 'question_map.fk_ques_id', '=', 'questions.ques_ID')
        //     ->where('apply.apply_id', $applicant_details->apply_id)
        //     ->whereNull('question_map.deleted_at')
        //     ->get();

        $Post_questionAnswer = DB::table('tbl_user_post_question_answer as answer')
            ->select(
                'apply.fk_post_id',
                'questions.ques_ID',
                'questions.ques_name',
                'questions.ans_type',
                'answer.date_From',
                'answer.date_To',
                DB::raw("CASE 
                    WHEN questions.ans_type = 'M' 
                    THEN GROUP_CONCAT(answer.answer ORDER BY answer.answer SEPARATOR ', ')
                    ELSE answer.answer
                 END as answer_list")
            )
            ->join('tbl_user_post_apply as apply', function ($join) {
                $join->on('answer.post_id', '=', 'apply.fk_post_id')
                    ->on('answer.applicant_id', '=', 'apply.fk_applicant_id');
            })
            ->leftJoin('post_question_map as question_map', 'answer.post_map_id', '=', 'question_map.post_map_id')
            ->leftJoin('master_post_questions as questions', 'question_map.fk_ques_id', '=', 'questions.ques_ID')
            ->where('apply.apply_id', $applicant_details->apply_id)
            ->whereNull('question_map.deleted_at')
            ->groupBy('apply.fk_post_id', 'questions.ques_ID', 'questions.ques_name', 'questions.ans_type')
            ->get();

        // dd($applicant_details->fk_applicant_id_ind, $applicant_details->fk_post_id_ind);


        $marks_details = DB::table('tbl_user_post_apply')
            ->select(
                'min_edu_qualification_mark',
                'edu_qualification_mark',
                'min_experiance_mark',
                'v_p_t_questionMarks',
                'secc_questionMarks',
                'kan_ash_questionMarks',
                'domicile_mark',
                'total_mark'
            )
            ->where('fk_applicant_id', '=', $applicant_details->fk_applicant_id_ind)
            ->where('fk_post_id', '=', $applicant_details->fk_post_id_ind) // Corrected the naming of fk_post_id
            ->first();
        // dd($Post_questionAnswer);





        $Skill_Ans = DB::table('tbl_post_skill_answer as ans')
            ->join('master_skills as ms', 'ans.fk_skill_id', '=', 'ms.skill_id')
            ->join('tbl_user_post_apply as apply', 'ans.fk_apply_id', '=', 'apply.apply_id')
            ->whereRaw('MD5(ans.fk_apply_id) = ?', [$application_id])
            ->select('ans.skill_ans_id', 'ms.skill_name as SkillName', 'ans.skill_answers as SkillAnswer')
            ->get();

        // dd($Skill_Ans);



        // return view('admin.view_app_detail', compact('applicant_details'));
        return view('admin.final_application_detail', compact(
            'marks_details',
            'applicant_details',
            'experience_details',
            'education_details',
            'Post_questionAnswer',
            'Skill_Ans',
            'postArea_Check'
        ));
    }


    public function dashboard_view_application_detail(Request $request, $applicant_id = 0)
    {
        if ($request->ajax()) {

            //  Ensure Row Number Works with Yajra DataTables
            DB::statement('SET @row := 0'); // Initialize Row Number

            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('@row := @row + 1 AS SerialNumber'),
                    'tbl_user_post_apply.is_final_submit',
                    'tbl_user_detail.ID as Applicant_ID',
                    DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'master_post.title AS Title',
                    'master_user.Mobile_Number AS Mobile_Number',
                    'tbl_user_detail.Created_On AS Application_date',
                    'tbl_user_post_apply.status AS Application_Status',
                    DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id")
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.status', '=', 'Submitted') //  Condition 1
                ->where('tbl_user_post_apply.is_final_submit', '=', '1') //  Condition 2 (Check is_final_submit = 1)
                ->orderBy('tbl_user_detail.ID', 'desc')
                ->limit(5)
                ->get();


            return DataTables::of($query)
                ->addIndexColumn()
                ->make(true); //  Yajra DataTables format
        }
        return view('admin/view_app_detail', compact('applicant_details'));
    }

    public function view_docs($application_id = 0)
    {
        // DB::enableQueryLog();
        $applicant_details = DB::table('record_user_detail_map')
            ->select(
                'record_user_detail_map.user_details_AI_ID AS RowID',
                'record_user_detail_map.Applicant_ID',
                'record_user_detail_map.Document_Photo',
                'record_user_detail_map.Document_Sign',
                'record_user_detail_map.Document_Aadhar',
                'record_user_detail_map.Document_Caste',
                'record_user_detail_map.Document_Domicile',
                'record_user_detail_map.Document_5th',
                'record_user_detail_map.Document_8th',
                'record_user_detail_map.Document_SSC',
                'record_user_detail_map.Document_Inter',
                'record_user_detail_map.Document_UG',
                'record_user_detail_map.Document_PG',
                'record_user_detail_map.Document_BPL',
                'record_user_detail_map.Document_Widow',
                'record_user_detail_map.Document_Exp',
                'record_user_detail_map.Document_Epic',
                'record_user_detail_map.Document_other',
                'tbl_user_post_apply.fk_post_id',
                'tbl_user_post_apply.apply_id',
                // 'tbl_user_post_apply.self_attested_file'
            )
            ->join('tbl_user_post_apply', 'record_user_detail_map.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
            ->whereRaw("MD5(record_user_detail_map.fk_apply_id) = ?", [$application_id])
            ->first();

        $post_id = $applicant_details->fk_post_id;
        $apply_id = $applicant_details->apply_id;
        $Applicant_id = $applicant_details->Applicant_ID;
        $Applicant_RowID = $applicant_details->RowID;

        // dd($Applicant_id);

        $questionAnswers = DB::select("SELECT ans.applicant_id, ans.post_id,  ques.ques_ID,  ques.ques_name, GROUP_CONCAT(ans.answer SEPARATOR ', ') AS all_answers, GROUP_CONCAT(ans.answer_file_upload SEPARATOR ', ') AS all_files
                                                FROM tbl_user_post_question_answer AS ans
                                                JOIN post_question_map AS qmap ON ans.post_map_id = qmap.post_map_id
                                                JOIN master_post_questions AS ques ON qmap.fk_ques_id = ques.ques_ID
                                                WHERE
                                                    ans.applicant_id = ? AND ans.post_id = ? AND qmap.deleted_at IS NULL AND ques.ans_type !='M'
                                                -- Group the results for each question
                                                GROUP BY ans.applicant_id, ans.post_id, ques.ques_ID, ques.ques_name", [$Applicant_RowID, $post_id]);

        $questionMultipleAnswers = DB::select("SELECT ans.applicant_id, ans.post_id,  ques.ques_ID,  ques.ques_name, GROUP_CONCAT(ans.answer SEPARATOR ', ') AS all_answers, GROUP_CONCAT(ans.answer_file_upload SEPARATOR ', ') AS all_files
                                                FROM tbl_user_post_question_answer AS ans
                                                JOIN post_question_map AS qmap ON ans.post_map_id = qmap.post_map_id
                                                JOIN master_post_questions AS ques ON qmap.fk_ques_id = ques.ques_ID
                                                WHERE
                                                    ans.applicant_id = ? AND ans.post_id = ? AND qmap.deleted_at IS NULL AND ques.ans_type ='M'
                                                -- Group the results for each question
                                                GROUP BY ans.applicant_id, ans.post_id, ques.ques_ID, ques.ques_name", [$Applicant_RowID, $post_id]);

        $role = session()->get("sess_role");
        $documentNames = DB::table('tbl_viewed')
            ->selectRaw('GROUP_CONCAT(DISTINCT document_name) AS document_array')
            ->where('fk_role_name', $role)
            ->where('fk_post_id', $applicant_details->fk_post_id)
            ->where('fk_applicant_id', $Applicant_RowID)
            ->value('document_array');

        // Null check before explode
        $documentArray = $documentNames ? explode(',', $documentNames) : [];
        //   dd($documentNames, $role, $applicant_details->fk_post_id, $applicant_id);

        // dd(DB::getQueryLog());

        $experience_docs = DB::table('record_applicant_experience_map')
            ->select('exp_document')
            ->where('Applicant_ID', $Applicant_RowID)
            ->whereRaw("MD5(fk_apply_id) = ?", [$application_id])
            ->get();

        $other_docs = DB::table('tbl_user_other_documents')
            ->select('other_documents')
            ->where('fk_applicant_id', $Applicant_RowID)
            ->get();


        // Fetch marks for current applicant/post
        $marksEntry = DB::table('tbl_user_post_apply')
            ->select(
                'domicile_mark',
                'v_p_t_questionMarks',
                'secc_questionMarks',
                'kan_ash_questionMarks',
                'min_experiance_mark',
                'min_edu_qualification_mark',
                'total_mark',
                'is_marks_confirmed'
            )
            ->where('apply_id', $apply_id)
            ->where('fk_applicant_id', $Applicant_RowID)
            ->where('fk_post_id', $post_id)
            ->first();

        // Fetch prior verification decisions (avoid relying on tbl_viewed only)
        $verificationStates = DocumentVerification::where('applicant_id', $Applicant_RowID)
            ->where('fk_post_id', $post_id)
            ->get()
            ->keyBy('document_key');

        //    dd('Appliant details: ', $applicant_details,
        //         'Question Answer: ', $questionAnswers,
        //         'Question Answer Multiple: ', $questionMultipleAnswers,
        //         'experience_docs: ', $experience_docs);
        //    dd($questionAnswers,$questionMultipleAnswers,$experience_docs );
        return view('/admin/view_docs', compact('applicant_details', 'documentArray', 'questionAnswers', 'questionMultipleAnswers', 'experience_docs', 'verificationStates', 'marksEntry', 'other_docs'));
    }

    public function seened_docs(Request $request)
    {
        $fk_post_id = $request->input('fk_post_id');
        $Applicant_ID = $request->input('Applicant_ID');
        $role = session()->get("sess_role");
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        $documentNames = DB::table('tbl_viewed')
            ->selectRaw('GROUP_CONCAT(DISTINCT document_name) AS document_array')
            ->where('fk_role_name', $role)
            ->where('fk_post_id', $fk_post_id)
            ->where('fk_applicant_id', $Applicant_ID)
            ->where('district_lgd_code', $district_id)
            ->where('project_code', $project_code)
            ->value('document_array');

        // Null check before explode
        $documentArray = $documentNames ? explode(',', $documentNames) : [];

        // Return JSON Response
        return response()->json([
            'status' => 'success',
            'documentArray' => $documentArray,
        ]);
    }


    public function merit_list(Request $request)
    {
        // dd(Session::all());
        $project_code = Session::get('project_code', 0);
        if ($request->ajax()) {
            $district_id = session()->get("district_id");
            $role = session()->get("sess_role");
            $filterAdvertisementId = $request->get('advertisement_id');
            $filterPostId = $request->get('post_id');
            $filterGp = $request->get('gp_name');
            $filterVillage = $request->get('village_name');
            $filterNagar = $request->get('nagar_name');
            $filterWard = $request->get('ward_name');

            if ($role == 'Admin') {

                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number

                $subQuery = DB::table('tbl_user_detail')
                    ->select(
                        'tbl_user_detail.ID AS RowID',
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'tbl_user_detail.FatherName AS FatherName',
                        'tbl_user_detail.DOB AS DOB',
                        'master_post.title AS Post_Name',
                        'master_user.Mobile_Number',
                        DB::raw("CASE WHEN r_pending.id IS NULL THEN 0 ELSE 1 END as has_request"),
                        'gp.panchayat_name_hin as gp_name',
                        'vill.village_name_hin as village_name',
                        'nnn.nnn_name as nagar_name',
                        'ward.ward_name as ward_name',
                        'ward.ward_no as ward_no',
                        'rudm.Document_Caste as caste_doc',
                        DB::raw('COALESCE(rudm.Document_Inter, rudm.Document_SSC, rudm.Document_8th, rudm.Document_5th) as edu_doc'),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id IN (16,17,18) AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as vpt_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 3 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as secc_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 15 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as kan_ash_doc"),
                        DB::raw("(SELECT exp_document FROM record_applicant_experience_map exp WHERE exp.Applicant_ID = tbl_user_detail.ID AND exp.exp_document IS NOT NULL AND exp.exp_document != '' LIMIT 1) as exp_doc"),
                        'tbl_user_post_apply.edu_qualification_mark',
                        'tbl_user_post_apply.min_edu_qualification_mark',
                        'tbl_user_post_apply.min_experiance_mark',
                        'tbl_user_post_apply.domicile_mark',
                        'tbl_user_post_apply.v_p_t_questionMarks',
                        'tbl_user_post_apply.secc_questionMarks',
                        'tbl_user_post_apply.kan_ash_questionMarks',
                        // DB::raw('(
                        //             tbl_user_post_apply.edu_qualification_mark +
                        //             tbl_user_post_apply.min_edu_qualification_mark +
                        //             tbl_user_post_apply.min_experiance_mark +
                        //             tbl_user_post_apply.domicile_mark +
                        //             tbl_user_post_apply.ques_mark
                        //         ) AS Total_Marks'),
                        'tbl_user_post_apply.total_mark AS Total_Marks',
                        'tbl_user_post_apply.apply_id as apply_id',
                        'tbl_user_post_apply.fk_post_id as post_id',
                        'tbl_user_post_apply.fk_applicant_id as applicant_id',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS fk_apply_id_ind"),
                        DB::raw("MD5(tbl_user_post_apply.fk_applicant_id) AS fk_applicant_id_ind"),
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                    ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                    ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                    ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                    ->leftJoin('merit_edit_requests as r_pending', function ($join) {
                        $join->on('r_pending.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
                            ->on('r_pending.fk_post_id', '=', 'tbl_user_post_apply.fk_post_id')
                            ->on('r_pending.fk_applicant_id', '=', 'tbl_user_post_apply.fk_applicant_id')
                            ->where('r_pending.status', '=', 'Pending');
                    })
                    ->leftJoin('record_user_detail_map as rudm', function ($join) {
                        $join->on('rudm.user_details_AI_ID', '=', 'tbl_user_detail.ID')
                            ->on('rudm.fk_apply_id', '=', 'tbl_user_post_apply.apply_id');
                    })
                    ->where('tbl_user_post_apply.is_final_submit', '1') // Ensures only final submitted applications
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_marks_confirmed', '1') // Ensure marks are confirmed
                    ->where('tbl_user_post_apply.fk_district_id', $district_id) // Filter by district
                    ->whereNotNull('tbl_user_post_apply.edu_qualification_mark') // Ensure qualification marks are present
                    ->groupBy('tbl_user_post_apply.fk_applicant_id', 'tbl_user_post_apply.fk_post_id') // Group by both applicant and post
                    ->orderBy('tbl_user_post_apply.edu_qualification_mark'); // Sort by qualification marks in descending order

                if (!empty($filterAdvertisementId)) {
                    $subQuery->where('master_post.Advertisement_ID', $filterAdvertisementId);
                }

                if (!empty($filterPostId)) {
                    $subQuery->where('master_post.post_id', $filterPostId);
                }
                if (!empty($filterGp)) {
                    $subQuery->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
                }
                if (!empty($filterVillage)) {
                    $subQuery->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
                }
                if (!empty($filterNagar)) {
                    $subQuery->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
                }
                if (!empty($filterWard)) {
                    $subQuery->where(function ($q) use ($filterWard) {
                        $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                            ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                    });
                }


                $query = DB::table(DB::raw("({$subQuery->toSql()}) AS temp"))
                    ->mergeBindings($subQuery)
                    ->select(
                        DB::raw('@row := @row + 1 AS SerialNumber'),
                        'temp.*'
                    )
                    ->get();
            } elseif (($role == 'Super_admin') && $district_id) {

                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number
                DB::statement('SET @row := 0'); // Reset counter before running query

                $subQuery = DB::table('tbl_user_detail')
                    ->select(
                        'tbl_user_detail.ID AS RowID',
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'tbl_user_detail.FatherName AS FatherName',
                        'tbl_user_detail.DOB AS DOB',
                        'master_post.title AS Post_Name',
                        'master_user.Mobile_Number',
                        DB::raw("CASE WHEN r_pending.id IS NULL THEN 0 ELSE 1 END as has_request"),
                        'gp.panchayat_name_hin as gp_name',
                        'vill.village_name_hin as village_name',
                        'nnn.nnn_name as nagar_name',
                        'ward.ward_name as ward_name',
                        'ward.ward_no as ward_no',
                        'rudm.Document_Caste as caste_doc',
                        DB::raw('COALESCE(rudm.Document_Inter, rudm.Document_SSC, rudm.Document_8th, rudm.Document_5th) as edu_doc'),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id IN (16,17,18) AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as vpt_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 3 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as secc_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 15 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as kan_ash_doc"),
                        DB::raw("(SELECT exp_document FROM record_applicant_experience_map exp WHERE exp.Applicant_ID = tbl_user_detail.ID AND exp.exp_document IS NOT NULL AND exp.exp_document != '' LIMIT 1) as exp_doc"),
                        'tbl_user_post_apply.edu_qualification_mark',
                        'tbl_user_post_apply.min_edu_qualification_mark',
                        'tbl_user_post_apply.min_experiance_mark',
                        'tbl_user_post_apply.domicile_mark',
                        'tbl_user_post_apply.v_p_t_questionMarks',
                        'tbl_user_post_apply.secc_questionMarks',
                        'tbl_user_post_apply.kan_ash_questionMarks',
                        // DB::raw('(
                        //             tbl_user_post_apply.edu_qualification_mark +
                        //             tbl_user_post_apply.min_edu_qualification_mark +
                        //             tbl_user_post_apply.min_experiance_mark +
                        //             tbl_user_post_apply.domicile_mark +
                        //             tbl_user_post_apply.ques_mark
                        //         ) AS Total_Marks'),
                        'tbl_user_post_apply.total_mark AS Total_Marks',
                        'tbl_user_post_apply.apply_id as apply_id',
                        'tbl_user_post_apply.fk_post_id as post_id',
                        'tbl_user_post_apply.fk_applicant_id as applicant_id',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS fk_apply_id_ind"),
                        DB::raw("MD5(tbl_user_post_apply.fk_applicant_id) AS fk_applicant_id_ind")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                    ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                    ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                    ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                    ->leftJoin('merit_edit_requests as r_pending', function ($join) {
                        $join->on('r_pending.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
                            ->on('r_pending.fk_post_id', '=', 'tbl_user_post_apply.fk_post_id')
                            ->on('r_pending.fk_applicant_id', '=', 'tbl_user_post_apply.fk_applicant_id')
                            ->where('r_pending.status', '=', 'Pending');
                    })
                    ->leftJoin('record_user_detail_map as rudm', function ($join) {
                        $join->on('rudm.user_details_AI_ID', '=', 'tbl_user_detail.ID')
                            ->on('rudm.fk_apply_id', '=', 'tbl_user_post_apply.apply_id');
                    })
                    ->where('master_post.project_code', $project_code)
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.is_marks_confirmed', '1') // Ensure marks are confirmed
                    ->where('tbl_user_post_apply.fk_district_id', $district_id)
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->whereNotNull('tbl_user_post_apply.edu_qualification_mark')
                    ->groupBy('tbl_user_post_apply.fk_applicant_id', 'tbl_user_post_apply.fk_post_id')
                    ->orderBy('tbl_user_post_apply.edu_qualification_mark');

                if (!empty($filterAdvertisementId)) {
                    $subQuery->where('master_post.Advertisement_ID', $filterAdvertisementId);
                }

                if (!empty($filterPostId)) {
                    $subQuery->where('master_post.post_id', $filterPostId);
                }
                if (!empty($filterGp)) {
                    $subQuery->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
                }
                if (!empty($filterVillage)) {
                    $subQuery->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
                }
                if (!empty($filterNagar)) {
                    $subQuery->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
                }
                if (!empty($filterWard)) {
                    $subQuery->where(function ($q) use ($filterWard) {
                        $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                            ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                    });
                }

                $query = DB::table(DB::raw("({$subQuery->toSql()}) AS temp"))
                    ->mergeBindings($subQuery)
                    ->select(
                        DB::raw('@row := @row + 1 AS SerialNumber'),
                        'temp.*'
                    )
                    ->get();
            } elseif ($role == 'Super_admin') {
                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number
                DB::statement('SET @row := 0'); // Reset counter before running query

                $subQuery = DB::table('tbl_user_detail')
                    ->select(
                        'tbl_user_detail.ID AS RowID',
                        DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                        'tbl_user_detail.FatherName AS FatherName',
                        'tbl_user_detail.DOB AS DOB',
                        'master_post.title AS Post_Name',
                        'master_user.Mobile_Number',
                        DB::raw("CASE WHEN r_pending.id IS NULL THEN 0 ELSE 1 END as has_request"),
                        'gp.panchayat_name_hin as gp_name',
                        'vill.village_name_hin as village_name',
                        'nnn.nnn_name as nagar_name',
                        'ward.ward_name as ward_name',
                        'ward.ward_no as ward_no',
                        'rudm.Document_Caste as caste_doc',
                        DB::raw('COALESCE(rudm.Document_Inter, rudm.Document_SSC, rudm.Document_8th, rudm.Document_5th) as edu_doc'),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id IN (16,17,18) AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as vpt_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 3 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as secc_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 15 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as kan_ash_doc"),
                        DB::raw("(SELECT exp_document FROM record_applicant_experience_map exp WHERE exp.Applicant_ID = tbl_user_detail.ID AND exp.exp_document IS NOT NULL AND exp.exp_document != '' LIMIT 1) as exp_doc"),
                        'tbl_user_post_apply.edu_qualification_mark',
                        'tbl_user_post_apply.min_edu_qualification_mark',
                        'tbl_user_post_apply.min_experiance_mark',
                        'tbl_user_post_apply.domicile_mark',
                        'tbl_user_post_apply.v_p_t_questionMarks',
                        'tbl_user_post_apply.secc_questionMarks',
                        'tbl_user_post_apply.kan_ash_questionMarks',
                        // DB::raw('(
                        //             tbl_user_post_apply.edu_qualification_mark +
                        //             tbl_user_post_apply.min_edu_qualification_mark +
                        //             tbl_user_post_apply.min_experiance_mark +
                        //             tbl_user_post_apply.domicile_mark +
                        //             tbl_user_post_apply.ques_mark
                        //         ) AS Total_Marks'),
                        'tbl_user_post_apply.total_mark AS Total_Marks',
                        'tbl_user_post_apply.apply_id as apply_id',
                        'tbl_user_post_apply.fk_post_id as post_id',
                        'tbl_user_post_apply.fk_applicant_id as applicant_id',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS fk_apply_id_ind"),
                        DB::raw("MD5(tbl_user_post_apply.fk_applicant_id) AS fk_applicant_id_ind")
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                    ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                    ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                    ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                    ->leftJoin('merit_edit_requests as r_pending', function ($join) {
                        $join->on('r_pending.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
                            ->on('r_pending.fk_post_id', '=', 'tbl_user_post_apply.fk_post_id')
                            ->on('r_pending.fk_applicant_id', '=', 'tbl_user_post_apply.fk_applicant_id')
                            ->where('r_pending.status', '=', 'Pending');
                    })
                    ->leftJoin('record_user_detail_map as rudm', function ($join) {
                        $join->on('rudm.user_details_AI_ID', '=', 'tbl_user_detail.ID')
                            ->on('rudm.fk_apply_id', '=', 'tbl_user_post_apply.apply_id');
                    })
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.is_marks_confirmed', '1') // Ensure marks are confirmed
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->whereNotNull('tbl_user_post_apply.edu_qualification_mark')
                    ->groupBy('tbl_user_post_apply.fk_applicant_id', 'tbl_user_post_apply.fk_post_id')
                    ->orderBy('tbl_user_post_apply.edu_qualification_mark');

                if (!empty($filterAdvertisementId)) {
                    $subQuery->where('master_post.Advertisement_ID', $filterAdvertisementId);
                }

                if (!empty($filterPostId)) {
                    $subQuery->where('master_post.post_id', $filterPostId);
                }
                if (!empty($filterGp)) {
                    $subQuery->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
                }
                if (!empty($filterVillage)) {
                    $subQuery->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
                }
                if (!empty($filterNagar)) {
                    $subQuery->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
                }
                if (!empty($filterWard)) {
                    $subQuery->where(function ($q) use ($filterWard) {
                        $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                            ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                    });
                }

                $query = DB::table(DB::raw("({$subQuery->toSql()}) AS temp"))
                    ->mergeBindings($subQuery)
                    ->select(
                        DB::raw('@row := @row + 1 AS SerialNumber'),
                        'temp.*'
                    )
                    ->get();
            }
            // dd($query->fk_apply_id_ind , $query->fk_apply_id_ind);
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = $row->fk_apply_id_ind;
                    $encryptedAppId = $row->fk_applicant_id_ind;
                    $url = url('/admin/final-application-detail/' . $encryptedAppId . '/' . $encryptedId . '?type=fulfilled');
                    return '<td>
                    <a href="' . $url . '" class="btn btn-info btn-sm text-white" id="printBtn"> <i class="bi bi-eye"></i></a>
                </td>';
                })
                ->rawColumns(['action']) // Required when returning HTML in a column
                ->make(true);
        }

        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");

        if ($role === 'Super_admin' && $district_id) {
            $advertisment_lists = DB::select("select * from master_advertisement where district_lgd_code = ? and project_code = ?", [$district_id, $project_code]);
            $post_lists = DB::table('master_post')
                ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                ->select('master_post.post_id', 'master_post.title', 'master_projects.project as project_name')
                ->where('master_post.project_code', $project_code)
                ->orderBy('master_post.title', 'asc')
                ->get();
        } elseif ($role === 'Super_admin') {
            $advertisment_lists = DB::select("select * from master_advertisement");
            $post_lists = DB::table('master_post')
                ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                ->select('master_post.post_id', 'master_post.title', 'master_projects.project as project_name')
                ->orderBy('master_post.title', 'asc')
                ->get();
        } else {
            $advertisment_lists = DB::select("select * from master_advertisement where district_lgd_code = ? and project_code = ?", [$district_id, $project_code]);
            $post_lists = DB::select("select * from master_post where project_code = ?", [$project_code]);
        }

        return view('admin.merit_list', [
            'query' => [],
            'advertisment_lists' => $advertisment_lists,
            'post_lists' => $post_lists
        ]);
    }

    public function submitMeritEditRequest(Request $request)
    {
        $role = session()->get('sess_role');
        $district_id = session()->get('district_id');

        if (!($role === 'Super_admin' && $district_id)) {
            return response()->json(['message' => 'अनुमति नहीं है।'], 403);
        }

        $items = $request->input('items', []);
        if (empty($items) || !is_array($items)) {
            return response()->json(['message' => 'कम से कम एक रिकॉर्ड चुनें।'], 422);
        }

        $project_code = session()->get('project_code', 0);
        $userId = session('uid');
        $ip = $request->ip();

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $applyId = (int) ($item['apply_id'] ?? 0);
                $postId = (int) ($item['post_id'] ?? 0);
                $applicantId = (int) ($item['applicant_id'] ?? 0);

                if (!$applyId || !$postId || !$applicantId) {
                    continue;
                }

                $existing = DB::table('merit_edit_requests')
                    ->where('fk_apply_id', $applyId)
                    ->where('fk_post_id', $postId)
                    ->where('fk_applicant_id', $applicantId)
                    ->first();

                if ($existing) {
                    DB::table('merit_edit_requests')
                        ->where('id', $existing->id)
                        ->update([
                            'request_count' => DB::raw('request_count + 1'),
                            'requested_by' => $userId,
                            'requested_role' => $role,
                            'requested_at' => now(),
                            'status' => 'Pending',
                            'approved_by' => null,
                            'approved_at' => null,
                            'ip_address' => $ip,
                        ]);
                } else {
                    DB::table('merit_edit_requests')->insert([
                        'fk_apply_id' => $applyId,
                        'fk_post_id' => $postId,
                        'fk_applicant_id' => $applicantId,
                        'district_id' => $district_id,
                        'project_code' => $project_code,
                        'requested_by' => $userId,
                        'requested_role' => $role,
                        'requested_at' => now(),
                        'status' => 'Pending',
                        'request_count' => 1,
                        'approve_count' => 0,
                        'ip_address' => $ip,
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'रिक्वेस्ट नहीं भेजी जा सकी।'], 500);
        }

        return response()->json(['message' => 'रिक्वेस्ट भेज दी गई।']);
    }

    public function meritEditRequests(Request $request)
    {
        $role = session()->get('sess_role');
        if ($role !== 'Admin') {
            return redirect('/admin/admin-dashboard');
        }

        $district_id = session()->get('district_id');
        $query = DB::table('merit_edit_requests as r')
            ->join('tbl_user_post_apply as upa', 'r.fk_apply_id', '=', 'upa.apply_id')
            ->join('tbl_user_detail as ud', 'r.fk_applicant_id', '=', 'ud.ID')
            ->join('master_post as mp', 'r.fk_post_id', '=', 'mp.post_id')
            ->leftJoin('master_panchayats as gp', 'upa.post_gp', '=', 'gp.panchayat_lgd_code')
            ->leftJoin('master_villages as vill', 'upa.post_village', '=', 'vill.village_code')
            ->leftJoin('master_nnn as nnn', 'upa.post_nagar', '=', 'nnn.std_nnn_code')
            ->leftJoin('master_ward as ward', 'upa.post_ward', '=', 'ward.ID')
            ->leftJoin('master_user as approver', 'r.approved_by', '=', 'approver.ID')
            ->leftJoin('master_user as requester', 'r.requested_by', '=', 'requester.ID')
            ->leftJoin('master_projects as proj', 'r.project_code', '=', 'proj.project_code')
            ->select(
                'r.*',
                DB::raw("CONCAT_WS(' ', ud.First_Name, ud.Middle_Name, ud.Last_Name) as applicant_name"),
                'ud.FatherName',
                'mp.title as post_name',
                'proj.project as project_name',
                'requester.Full_Name as requested_by_name',
                'gp.panchayat_name_hin as gp_name',
                'vill.village_name_hin as village_name',
                'nnn.nnn_name as nagar_name',
                'ward.ward_name as ward_name',
                'ward.ward_no as ward_no',
                'r.request_count',
                'r.approve_count',
                DB::raw("(CASE WHEN r.approve_count > 0 THEN ROUND(TIMESTAMPDIFF(MINUTE, r.requested_at, r.approved_at)) ELSE NULL END) as avg_approval_minutes"),
                DB::raw("TIMESTAMPDIFF(MINUTE, r.requested_at, r.approved_at) as approval_minutes"),
                'approver.Full_Name as approved_by_name'
            )
            ->where('r.status', 'Pending')
            ->where('upa.fk_district_id', $district_id)
            ->orderBy('r.requested_at', 'desc')
            ->get();

        return view('admin.merit_edit_requests', compact('query'));
    }

    public function approveMeritEditRequest(Request $request)
    {
        $role = session()->get('sess_role');
        if ($role !== 'Admin') {
            return response()->json(['message' => 'अनुमति नहीं है।'], 403);
        }

        $ids = $request->input('ids', []);
        if (!$ids || !is_array($ids)) {
            return response()->json(['message' => 'कम से कम एक रिक्वेस्ट चुनें।'], 422);
        }

        $userId = session('uid');
        $updated = DB::table('merit_edit_requests')
            ->whereIn('id', $ids)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Approved',
                'approve_count' => DB::raw('approve_count + 1'),
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

        if ($updated) {
            $affected = DB::table('merit_edit_requests')
                ->whereIn('id', $ids)
                ->select('fk_apply_id', 'fk_post_id')
                ->get();

            foreach ($affected as $row) {
                DB::table('tbl_user_post_apply')
                    ->where('apply_id', $row->fk_apply_id)
                    ->where('fk_post_id', $row->fk_post_id)
                    ->update(['is_marks_confirmed' => '0']);
            }
        }

        return response()->json(['message' => $updated ? 'रिक्वेस्ट अप्रूव कर दी गई।' : 'कोई रिक्वेस्ट अपडेट नहीं हुई।']);
    }

    public function marks_entry(Request $request, $apply_id = 0)
    {

        $filterGp = $request->get('gp_name');
        $filterVillage = $request->get('village_name');
        $filterNagar = $request->get('nagar_name');
        $filterWard = $request->get('ward_name');

        $project_code = Session::get('project_code', 0);
        if ($request->isMethod('post')) {
            $rules = [
                'domicile_mark' => 'array',
                'domicile_mark.*' => 'numeric|min:0|max:10',

                'v_p_t_questionMarks' => 'array',
                'v_p_t_questionMarks.*' => 'numeric|min:0|max:15',

                'secc_questionMarks' => 'array',
                'secc_questionMarks.*' => 'numeric|min:0|max:6',

                'kan_ash_questionMarks' => 'array',
                'kan_ash_questionMarks.*' => 'numeric|min:0|max:3',

                'min_experiance_mark' => 'array',
                'min_experiance_mark.*' => 'numeric|min:0|max:6',

                'min_edu_qualification_mark' => 'array',
                'min_edu_qualification_mark.*' => 'numeric|min:0|max:60',
            ];

            $valid = $request->validate($rules);

            // dd($request->marks);

            DB::beginTransaction();
            try {

                foreach ($request->id as $index => $idVal) {
                    $dom = (float) ($request->domicile_mark[$index] ?? 0);
                    $vpt = (float) ($request->v_p_t_questionMarks[$index] ?? 0);
                    $secc = (float) ($request->secc_questionMarks[$index] ?? 0);
                    $kan = (float) ($request->kan_ash_questionMarks[$index] ?? 0);
                    $minExp = (float) ($request->min_experiance_mark[$index] ?? 0);
                    $minEdu = (float) ($request->min_edu_qualification_mark[$index] ?? 0);

                    $total = $dom + $vpt + $secc + $kan + $minExp + $minEdu;

                    $marks_data_status = DB::table('tbl_user_post_apply')
                        ->where('fk_applicant_id', $idVal)
                        ->where('fk_post_id', $request->post_id[$index])
                        ->update([
                            'domicile_mark' => $dom,
                            'v_p_t_questionMarks' => $vpt,
                            'secc_questionMarks' => $secc,
                            'kan_ash_questionMarks' => $kan,
                            'min_experiance_mark' => $minExp,
                            'min_edu_qualification_mark' => $minEdu,
                            'total_mark' => $total,
                            'is_marks_confirmed' => 1
                        ]);
                }
                if ($marks_data_status) {

                    DB::commit();
                    return response()->json(['message' => "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं ।", 'status' => 'success']);
                } else {

                    DB::rollBack();
                    return response()->json(['message' => "कोई भी अंक अपडेट नहीं किया गया।", 'status' => 'notChange']);
                }
            } catch (\Throwable $th) {

                // dd($th->getMessage());
                print ('An error occurred: ' . $th->getMessage());
                DB::rollBack();
                return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
            }
        } else {
            $district_id = session()->get("district_id");
            $role = session()->get("sess_role");
            $filterAdvertisementId = $request->get('advertisement_id');
            $filterPostId = $request->get('post_id');

            if ($role == 'Admin') {

                DB::statement('SET @row := 0'); // Initialize Row Number

                DB::statement("SET @row := 0");

                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('@row := @row + 1 AS SerialNumber'),
                        'tbl_user_detail.ID AS RowID',
                        'tbl_user_post_apply.*',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id"),
                        DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                        'master_post.title AS Post_Name',
                        'gp.panchayat_name_hin as gp_name',
                        'vill.village_name_hin as village_name',
                        'nnn.nnn_name as nagar_name',
                        'ward.ward_name as ward_name',
                        'ward.ward_no as ward_no',
                        'rudm.Document_Caste as caste_doc',
                        DB::raw('COALESCE(rudm.Document_Inter, rudm.Document_SSC, rudm.Document_8th, rudm.Document_5th) as edu_doc'),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id IN (16,17,18) AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as vpt_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 3 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as secc_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 15 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as kan_ash_doc"),
                        DB::raw("(SELECT exp_document FROM record_applicant_experience_map exp WHERE exp.Applicant_ID = tbl_user_detail.ID AND exp.exp_document IS NOT NULL AND exp.exp_document != '' LIMIT 1) as exp_doc"),
                        'tbl_user_detail.*',
                        'master_user.*',
                        'tbl_user_detail.Pref_Districts AS pref_district_name',
                        'tbl_applicant_education_qualification.*'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                    ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                    ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                    ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                    ->leftJoin('record_user_detail_map as rudm', function ($join) {
                        $join->on('rudm.user_details_AI_ID', '=', 'tbl_user_detail.ID')
                            ->on('rudm.fk_apply_id', '=', 'tbl_user_post_apply.apply_id');
                    })
                    ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.fk_district_id', $district_id);

                if (!empty($filterAdvertisementId)) {
                    $query->where('master_post.Advertisement_ID', $filterAdvertisementId);
                }

                if (!empty($filterPostId)) {
                    $query->where('master_post.post_id', $filterPostId);
                }
                if (!empty($filterGp)) {
                    $query->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
                }
                if (!empty($filterVillage)) {
                    $query->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
                }
                if (!empty($filterNagar)) {
                    $query->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
                }
                if (!empty($filterWard)) {
                    $query->where(function ($q) use ($filterWard) {
                        $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                            ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                    });
                }

                $application_lists = $query
                    ->groupBy('tbl_user_post_apply.apply_id') // Yeh line hi sirf change hui hai
                    ->get();
            } elseif (($role = 'Super_admin') && $district_id) {

                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number

                if (!empty($apply_id)) {
                    // dd($apply_id);
                    DB::table('tbl_user_post_apply')
                        ->where('apply_id', $apply_id)
                        ->update(values: [
                            'is_marks_confirmed' => '0'
                        ]);
                }

                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('@row := @row + 1 AS SerialNumber'),
                        'tbl_user_detail.ID AS RowID',
                        'tbl_user_post_apply.*',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id"),
                        DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                        'master_post.title AS Post_Name',
                        'gp.panchayat_name_hin as gp_name',
                        'vill.village_name_hin as village_name',
                        'nnn.nnn_name as nagar_name',
                        'ward.ward_name as ward_name',
                        'ward.ward_no as ward_no',
                        'rudm.Document_Caste as caste_doc',
                        DB::raw('COALESCE(rudm.Document_Inter, rudm.Document_SSC, rudm.Document_8th, rudm.Document_5th) as edu_doc'),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id IN (16,17,18) AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as vpt_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 3 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as secc_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 15 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as kan_ash_doc"),
                        DB::raw("(SELECT exp_document FROM record_applicant_experience_map exp WHERE exp.Applicant_ID = tbl_user_detail.ID AND exp.exp_document IS NOT NULL AND exp.exp_document != '' LIMIT 1) as exp_doc"),
                        'tbl_user_detail.*',
                        'master_user.*',
                        'tbl_user_detail.Pref_Districts AS pref_district_name',
                        'tbl_applicant_education_qualification.*'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                    ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                    ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                    ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                    ->leftJoin('record_user_detail_map as rudm', function ($join) {
                        $join->on('rudm.user_details_AI_ID', '=', 'tbl_user_detail.ID')
                            ->on('rudm.fk_apply_id', '=', 'tbl_user_post_apply.apply_id');
                    })
                    ->join(
                        'tbl_applicant_education_qualification',
                        'tbl_user_detail.ID',
                        '=',
                        'tbl_applicant_education_qualification.fk_applicant_id'
                    )
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.fk_district_id', $district_id);

                if (!empty($filterAdvertisementId)) {
                    $query->where('master_post.Advertisement_ID', $filterAdvertisementId);
                }

                if (!empty($filterPostId)) {
                    $query->where('master_post.post_id', $filterPostId);
                }
                if (!empty($filterGp)) {
                    $query->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
                }
                if (!empty($filterVillage)) {
                    $query->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
                }
                if (!empty($filterNagar)) {
                    $query->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
                }
                if (!empty($filterWard)) {
                    $query->where(function ($q) use ($filterWard) {
                        $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                            ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                    });
                }

                // Apply filter only if apply_id exists
                if (!empty($apply_id)) {
                    $query->where('tbl_user_post_apply.apply_id', $apply_id);
                }

                // Final execution
                $application_lists = $query
                    ->groupBy('tbl_user_post_apply.apply_id')
                    ->get();
            } elseif ($role = 'Super_admin') {
                //  Ensure Row Number Works with Yajra DataTables
                DB::statement('SET @row := 0'); // Initialize Row Number

                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('@row := @row + 1 AS SerialNumber'),
                        'tbl_user_detail.ID AS RowID',
                        'tbl_user_post_apply.*',
                        DB::raw("MD5(tbl_user_post_apply.apply_id) AS Application_Id"),
                        DB::raw("MD5(tbl_user_detail.ID) AS EncryptedID"),
                        'master_post.title AS Post_Name',
                        'gp.panchayat_name_hin as gp_name',
                        'vill.village_name_hin as village_name',
                        'nnn.nnn_name as nagar_name',
                        'ward.ward_name as ward_name',
                        'ward.ward_no as ward_no',
                        'rudm.Document_Caste as caste_doc',
                        DB::raw('COALESCE(rudm.Document_Inter, rudm.Document_SSC, rudm.Document_8th, rudm.Document_5th) as edu_doc'),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id IN (16,17,18) AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as vpt_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 3 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as secc_doc"),
                        DB::raw("(SELECT ans.answer_file_upload FROM tbl_user_post_question_answer ans WHERE ans.applicant_id = tbl_user_detail.ID AND ans.post_id = tbl_user_post_apply.fk_post_id AND ans.fk_question_id = 15 AND ans.answer_file_upload IS NOT NULL AND ans.answer_file_upload != '' LIMIT 1) as kan_ash_doc"),
                        DB::raw("(SELECT exp_document FROM record_applicant_experience_map exp WHERE exp.Applicant_ID = tbl_user_detail.ID AND exp.exp_document IS NOT NULL AND exp.exp_document != '' LIMIT 1) as exp_doc"),
                        'tbl_user_detail.*',
                        'master_user.*',
                        'tbl_user_detail.Pref_Districts AS pref_district_name',
                        'tbl_applicant_education_qualification.*'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                    ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                    ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                    ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                    ->leftJoin('record_user_detail_map as rudm', function ($join) {
                        $join->on('rudm.user_details_AI_ID', '=', 'tbl_user_detail.ID')
                            ->on('rudm.fk_apply_id', '=', 'tbl_user_post_apply.apply_id');
                    })
                    ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1');

                if (!empty($filterAdvertisementId)) {
                    $query->where('master_post.Advertisement_ID', $filterAdvertisementId);
                }

                if (!empty($filterPostId)) {
                    $query->where('master_post.post_id', $filterPostId);
                }
                if (!empty($filterGp)) {
                    $query->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
                }
                if (!empty($filterVillage)) {
                    $query->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
                }
                if (!empty($filterNagar)) {
                    $query->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
                }
                if (!empty($filterWard)) {
                    $query->where(function ($q) use ($filterWard) {
                        $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                            ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                    });
                }

                $application_lists = $query
                    ->groupBy('tbl_user_post_apply.apply_id') //  Change yaha
                    ->get();
            }

            if ($role === 'Super_admin' && $district_id) {
                $advertisment_lists = DB::select("select * from master_advertisement where district_lgd_code = ? and project_code = ?", [$district_id, $project_code]);
                $post_lists = DB::table('master_post')
                    ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                    ->select('master_post.post_id', 'master_post.title', 'master_projects.project as project_name')
                    ->where('master_post.project_code', $project_code)
                    ->orderBy('master_post.title', 'asc')
                    ->get();
            } elseif ($role === 'Super_admin') {
                $advertisment_lists = DB::select("select * from master_advertisement");
                $post_lists = DB::table('master_post')
                    ->leftJoin('master_projects', 'master_post.project_code', '=', 'master_projects.project_code')
                    ->select('master_post.post_id', 'master_post.title', 'master_projects.project as project_name')
                    ->orderBy('master_post.title', 'asc')
                    ->get();
            } else {
                $advertisment_lists = DB::select("select * from master_advertisement where district_lgd_code = ? and project_code = ?", [$district_id, $project_code]);
                $post_lists = DB::select("select * from master_post where project_code = ?", [$project_code]);
            }

            return view('admin/marks_entry', compact('application_lists', 'advertisment_lists', 'post_lists'));
        }
    }

    public function applicationApproveReject(Request $request)
    {


        // dd($request->all());
        // check that admin are viewed or not all documents.
        $post_id = $request['post_id'];
        $applicant_id = $request['user_id'];
        $role = session()->get("sess_role");

        // $user_applicant_id = DB::table('record_user_detail_map')
        //     ->whereRaw("user_details_AI_ID = ?", [$request['user_id']])
        //     ->pluck('RowID');
        // dd($role, $post_id, $user_applicant_id[0]);
        if ($post_id) {

            $data1 = DB::select("
                                    SELECT 
                                        fk_applicant_id,
                                        fk_post_id,
                                        COUNT(DISTINCT document_name) AS total_documents_viewed,
                                        GROUP_CONCAT(DISTINCT document_name) AS viewed_documents
                                    FROM 
                                        tbl_viewed
                                    WHERE 
                                        fk_post_id = :post_id 
                                        AND fk_applicant_id = :applicant_id 
                                        AND fk_role_name = :fk_role_name
                                        and project_code = :project_code
                                    GROUP BY 
                                        fk_applicant_id, fk_post_id, fk_role_name
                                ", [
                'post_id' => $post_id,
                'applicant_id' => $applicant_id,
                'fk_role_name' => $role,
                'project_code' => session()->get('project_code')
            ]);

            DB::enableQueryLog();
            $data2 = DB::select(" SELECT
                                        ud.Applicant_ID,
                                        (
                                        MAX(CASE WHEN Document_Aadhar IS NOT NULL AND Document_Aadhar != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_Caste IS NOT NULL AND Document_Caste != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_Domicile IS NOT NULL AND Document_Domicile != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_8th IS NOT NULL AND Document_8th != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_5th IS NOT NULL AND Document_5th != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_SSC IS NOT NULL AND Document_SSC != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_Inter IS NOT NULL AND Document_Inter != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_UG IS NOT NULL AND Document_UG != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_PG IS NOT NULL AND Document_PG != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_BPL IS NOT NULL AND Document_BPL != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_Widow IS NOT NULL AND Document_Widow != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_Exp IS NOT NULL AND Document_Exp != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_other IS NOT NULL AND Document_other != '' THEN 1 ELSE 0 END) +
                                        MAX(CASE WHEN Document_Epic IS NOT NULL AND Document_Epic != '' THEN 1 ELSE 0 END) +
                                        SUM(CASE WHEN upqa.answer_file_upload IS NOT NULL AND upqa.answer_file_upload != '' THEN 1 ELSE 0 END)
                                        ) AS submitted_documents_count
                                        FROM record_user_detail_map ud
                                        LEFT JOIN tbl_user_post_question_answer upqa
                                        ON ud.user_details_AI_ID = upqa.applicant_id AND upqa.post_id = :post_id and answer_file_upload IS NOT NULL AND answer_file_upload != ''
                                        WHERE ud.user_details_AI_ID = :applicant_id
                                        GROUP BY ud.user_details_AI_ID
                                ", ['applicant_id' => $applicant_id, 'post_id' => $post_id]);
            // dd(DB::getQueryLog());

            // Check if data1 and data2 have values, otherwise handle it properly
            $admin_doc_view_count = isset($data1[0]) ? $data1[0]->total_documents_viewed + 1 : 0;
            $candicate_submitted_doc_count = isset($data2[0]) ? intval($data2[0]->submitted_documents_count) : 0;

            // dd('data1:',$data1, 'data2:',$data2, 'admin_doc_view_count:',$admin_doc_view_count, 'candicate_submitted_doc_count:',$candicate_submitted_doc_count,$admin_doc_view_count >= $candicate_submitted_doc_count);

            if ($admin_doc_view_count <= $candicate_submitted_doc_count) {
                return response()->json(
                    [
                        "status" => 'doc_not_viwed'
                    ]
                );
            }
            ;
        }

        if ($request['button_status'] == 'Approve') {

            $data_update_status = DB::table('tbl_user_post_apply')
                ->where('fk_applicant_id', $applicant_id)
                ->where('apply_id', $request['encrypted_id']) // Identify the record to update based on the ID
                ->update([
                    'status' => 'Verified',
                    'eligiblity_date' => now()
                ]);
            // dd($applicant_id, $post_id);

            if ($data_update_status) {

                $userData = DB::select("SELECT applicant_id, post_id,fk_question_id, answer, answer_file_upload, date_From,date_To,total_experience_days 
                                                FROM tbl_user_post_question_answer
                                                WHERE applicant_id=$applicant_id AND post_id=$post_id;");

                $weightageData = DB::select("SELECT post_id, question_id, option_value, marks FROM master_weightage_marks WHERE post_id=$post_id;");
                $experienceData = DB::select("SELECT * FROM master_experience_weightage WHERE post_id=$post_id LIMIT 1;");

                $apprRejDocs = DB::table('document_verifications')
                    ->where('fk_apply_id', $applicant_id)
                    ->where('fk_post_id', $post_id)
                    ->where('is_verified', '=', 0)
                    ->whereNotNull('remark')
                    ->get();

                // dd($apprRejDocs->document_key);

                // dd($userData, $weightageData);

                $totalExperienceDays = 0;

                $totalQuesMarks = 0; // Variable to store the sum of all marks
                $questionMarks = []; // Array to store marks for each question ID

                $docVerify = DB::SELECT("SELECT * FROM document_verifications 
                                                WHERE applicant_id = ? AND fk_post_id = ? #AND is_verified = 0 AND remark IS NOT NULL", [$userData[0]->applicant_id, $userData[0]->post_id]);
                // dd($docVerify);
                // Loop through user data
                if (!empty($weightageData)) {
                    $v_p_t_questionMarks = 0;
                    $secc_questionMarks = 0;
                    $kan_ash_questionMarks = 0;

                    // Extract all valid question IDs from weightageData
                    $validQuestionIds = array_column($weightageData, 'question_id');
                    foreach ($userData as $user) {
                        if (!empty($user->date_From) || !empty($user->date_To) || !empty($user->total_experience_days)) {
                            $totalExperienceDays = $user->total_experience_days; // Add experience days if present
                            // dd($totalExperienceDays);
                            continue; // Skip this question
                        }
                        // dd($user->applicant_id, $user->post_id, $user->fk_question_id, $user->answer);


                        // $docVerify = DB::SELECT("SELECT * FROM document_verifications 
                        //                         WHERE applicant_id = ? AND fk_post_id = ? #AND is_verified = 0 AND remark IS NOT NULL", [$user->applicant_id, $user->post_id]);
                        // dd($docVerify[0]->document_key);

                        // Check if the question ID exists in weightageData
                        if (in_array($user->fk_question_id, array_column($weightageData, 'question_id'))) {
                            $questionTotalMarks = 0; // Initialize total marks for this question ID

                            // Find all matching questions in weightage data
                            foreach ($weightageData as $weightage) {
                                if ($user->fk_question_id == $weightage->question_id && $user->answer == $weightage->option_value) {
                                    // Add marks for the specific question
                                    $questionTotalMarks += $weightage->marks;
                                    if ($weightage->question_id == 1) {

                                        if (array_column($docVerify[0]->document_key, ['परित्यक्ता प्रमाण पत्र', 'तलाकशुदा प्रमाण पत्र', 'विधवा प्रमाण पत्र']) && $docVerify[0]->is_verified == 0 && !empty($docVerify[0]->remark)) {
                                            $v_p_t_questionMarks += 0;
                                        } else {
                                            if (in_array($user->answer, ['परित्यक्ता', 'तलाकशुदा'])) {
                                                // dd(1);
                                                $marriageData = DB::table('tbl_user_post_question_answer')
                                                    ->where('applicant_id', $applicant_id)
                                                    ->where('post_id', $post_id);

                                                $result = null;

                                                if ($user->answer == 'परित्यक्ता') {
                                                    $result = $marriageData->where('fk_question_id', 16)->first();
                                                    // dd($result->answer);
                                                } elseif ($user->answer == 'तलाकशुदा') {
                                                    $result = $marriageData->where('fk_question_id', 17)->first();
                                                }

                                                $from_date = !empty($result->answer) ? $result->answer : null;
                                                $current_date = date('Y-m-d');
                                                $date_diff = date_diff(date_create($from_date), date_create($current_date));
                                                $years_difference = $date_diff->y;

                                                if ($years_difference >= 2) {
                                                    $v_p_t_questionMarks += $weightage->marks;
                                                } else {
                                                    $v_p_t_questionMarks += 0;
                                                }
                                            } elseif ($user->answer == 'विधवा') {
                                                $v_p_t_questionMarks += $weightage->marks;
                                            } else {
                                                $v_p_t_questionMarks += 0;
                                            }
                                        }

                                    } elseif ($weightage->question_id == 3) {
                                        if ($docVerify[0]->document_key == 'गरीबी रेखा प्रमाण पत्र' && $docVerify[0]->is_verified == 0 && !empty($docVerify[0]->remark)) {
                                            $secc_questionMarks += 0;
                                        } else {
                                            $secc_questionMarks += $weightage->marks;
                                        }
                                        // }
                                    } elseif ($weightage->question_id == 15) {
                                        $kan_ash_questionMarks += $weightage->marks;
                                    }
                                }
                            }
                            // Store the total marks for the question ID
                            if (!isset($questionMarks[$user->fk_question_id])) {
                                $questionMarks[$user->fk_question_id] = 0;
                            }
                            $questionMarks[$user->fk_question_id] += $questionTotalMarks;

                            // Add to the overall total marks
                            $totalQuesMarks += $questionTotalMarks;
                        }
                    }
                    // dd($v_p_t_questionMarks, $secc_questionMarks, $kan_ash_questionMarks, $totalQuesMarks);
                }


                $extraExperienceMarks = 0;

                $filterDataforExp = collect($docVerify)
                    ->where('document_key', '=', 'आगनबाड़ी सहायक अनुभव प्रमाण पत्र')
                    ->where('is_verified', '=', 0)
                    ->whereNotNull('remark')
                    ->first();

                if ($filterDataforExp) {
                    $extraExperienceMarks = 0;
                } else {
                    // dd($experienceData);
                    if (!empty($experienceData)) {
                        $totalExperienceYears = intval($totalExperienceDays / 365);
                        // dd($totalExperienceDays);
                        if ($totalExperienceYears > $experienceData[0]->minimum_experience_years) {
                            // Calculate extra years
                            $extraYears = $totalExperienceYears - $experienceData[0]->minimum_experience_years;
                            // Add +1 mark for each extra year
                            $extraExperienceMarks = $extraYears * $experienceData[0]->increment_value_per_year;

                            // Cap the marks at the maximum allowed
                            if ($extraExperienceMarks > $experienceData[0]->maximum_experience_marks) {
                                $extraExperienceMarks = $experienceData[0]->maximum_experience_marks;
                            }
                        }
                    }
                }

                $qualificationData = DB::select("SELECT post_id, qualification_id, marks FROM master_qualification_marks WHERE post_id = $post_id;");
                $userQualificationData = DB::select("SELECT qualification_id, fk_applicant_id, fk_Quali_ID FROM record_applicant_edu_map WHERE fk_applicant_id = $applicant_id;");

                $totalQualificationMarks = 0;

                if (!empty($qualificationData)) {
                    // Loop through user qualification data
                    foreach ($userQualificationData as $userQualification) {
                        // Find matching qualification in master qualification data
                        foreach ($qualificationData as $qualification) {
                            if ($userQualification->fk_Quali_ID == $qualification->qualification_id) {
                                // Add the marks to the total
                                $totalQualificationMarks += $qualification->marks;
                                break; // Exit the loop once a match is found
                            }
                        }
                    }
                }


                $minQualificationData = DB::select("SELECT post_id, qualification_id, multiplier_value from master_qualification_multiplier WHERE post_id = $post_id;");
                $min_qualification = DB::select("SELECT fk_applicant_id, fk_Quali_ID, percentage, fk_grade_id FROM record_applicant_edu_map WHERE fk_applicant_id=$applicant_id;");

                $multipliedValue = 0;

                if (!empty($minQualificationData)) {
                    // Loop through user qualification data
                    foreach ($min_qualification as $userQualification) {
                        // Find the matching qualification in master qualification multiplier data
                        foreach ($minQualificationData as $qualificationMultiplier) {
                            if ($userQualification->fk_Quali_ID == $qualificationMultiplier->qualification_id) {

                                if ($userQualification->percentage) {
                                    $multipliedValue = $userQualification->percentage;
                                } else {
                                    $grade = DB::select("SELECT mg.grade_value_to FROM master_grades mg  WHERE mg.grade_id IN (
                                                                SELECT fk_grade_id from record_applicant_edu_map WHERE fk_applicant_id=$applicant_id);");
                                    if (!empty($grade)) {
                                        $multipliedValue = $grade[0]->grade_value_to;
                                    }
                                }
                                // Calculate the product of percentage and multiplier value
                                $multipliedValue = intval($multipliedValue * $qualificationMultiplier->multiplier_value);
                                break; // Exit the loop once a match is found
                            }
                        }
                    }
                }


                $casteData = DB::select("SELECT post_id,caste_id,marks FROM master_caste_marks WHERE master_caste_marks.post_id = $post_id;");
                $userCasteData = DB::select("SELECT * FROM master_tbl_caste mc 
                                                    WHERE mc.caste_title IN (SELECT ud.Caste FROM tbl_user_detail ud WHERE ud.ID = $applicant_id);");

                $filterData = collect($docVerify)
                    ->where('document_key', '=', 'Document_Caste')
                    ->where('is_verified', '=', 0)
                    ->whereNotNull('remark')
                    ->first();


                $casteMarks = 0;
                if ($filterData) {
                    $casteMarks = 0;
                } else {
                    if (!empty($casteData)) {
                        foreach ($casteData as $casteMark) {
                            foreach ($userCasteData as $userCaste) {
                                if ($userCaste->caste_id == $casteMark->caste_id) {
                                    $casteMarks = $casteMark->marks;
                                    break 2; // Exit both loops after first match
                                }
                            }
                        }
                    }
                }

                // dd( 'ques_mark' , $totalQuesMarks,
                //         'v_p_t_questionMarks' , $v_p_t_questionMarks,
                //         'secc_questionMarks' , $secc_questionMarks,
                //         'kan_ash_questionMarks' , $kan_ash_questionMarks,
                //         'edu_qualification_mark' , $totalQualificationMarks,
                //         'min_edu_qualification_mark' , $multipliedValue,
                //         'domicile_mark' , $casteMarks,
                //         'min_experiance_mark' , $extraExperienceMarks, 'post_id',$post_id );

                DB::table('tbl_user_post_apply')
                    ->where('fk_applicant_id', $applicant_id)
                    ->where('apply_id', $request['encrypted_id']) // Identify the record to update based on the ID
                    ->update([
                        'ques_mark' => $totalQuesMarks,
                        'v_p_t_questionMarks' => $v_p_t_questionMarks,
                        'secc_questionMarks' => $secc_questionMarks,
                        'kan_ash_questionMarks' => $kan_ash_questionMarks,
                        'edu_qualification_mark' => $totalQualificationMarks,
                        'min_edu_qualification_mark' => $multipliedValue,
                        'domicile_mark' => $casteMarks,
                        'min_experiance_mark' => $extraExperienceMarks,
                        'ques_marks_json_with_ques_id' => json_encode($questionMarks)
                    ]);
            }
        } else {


            $data_update_status = DB::table('tbl_user_post_apply')
                ->where('fk_applicant_id', $applicant_id) // use the same applicant id used in approve path
                ->where(function ($q) use ($request) {
                    $q->where('apply_id', $request['encrypted_id'])
                        ->orWhere(DB::raw('MD5(apply_id)'), $request['encrypted_id']);
                })
                ->update([
                    'status' => 'Rejected',
                    'reason_rejection' => $request->input('remark') // Add another column to update here
                ]);
        }

        // $data_update->Last_Updated_By = session()->get('sess_id');
        // $data_update->Last_Updated_On = now();
        if ($data_update_status) {
            return response()->json(
                [
                    "status" => 'success',
                ]
            );
        } else {
            return response()->json(
                [
                    "status" => 'failed',
                ]
            );
        }
    }

    public function district_wise_applications(Request $request)
    {
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");

        // Get drill-down parameters
        $districtId = $request->get('district_id');
        $cdpoId = $request->get('cdpo_id'); // This will be GP code
        $awcId = $request->get('awc_id'); // This will be village code

        // Determine current level
        $currentLevel = 'district';
        if ($awcId) {
            $currentLevel = 'awc';
        } elseif ($cdpoId) {
            $currentLevel = 'cdpo';
        } elseif ($districtId) {
            $currentLevel = 'project';
        }

        $districtName = null;
        $cdpoName = null;
        $awcName = null;

        if ($role == 'Super_admin') {

            if ($currentLevel == 'district') { {
                    $results = DB::select("SELECT
                                                master_district.District_Code_LGD AS Pref_Districts,
                                                master_district.name AS name,
                                                COUNT(
                                                    CASE 
                                                        WHEN tbl_user_post_apply.is_final_submit = 1 AND master_post.post_id IS NOT NULL
                                                        THEN 1 
                                                    END
                                                ) AS submitted_count,
                                                SUM(
                                                    CASE 
                                                        WHEN tbl_user_post_apply.status = 'Rejected'
                                                            AND tbl_user_post_apply.is_final_submit = 1 AND master_post.post_id IS NOT NULL
                                                        THEN 1 ELSE 0
                                                    END
                                                ) AS rejected_count,
                                                SUM(
                                                    CASE 
                                                        WHEN tbl_user_post_apply.status = 'Verified'
                                                            AND tbl_user_post_apply.is_final_submit = 1 AND master_post.post_id IS NOT NULL
                                                        THEN 1 ELSE 0
                                                    END
                                                ) AS approved_count
                                            FROM master_district
                                            LEFT JOIN tbl_user_post_apply
                                                ON tbl_user_post_apply.fk_district_id = master_district.District_Code_LGD 
                                            AND tbl_user_post_apply.is_final_submit = 1
                                            left JOIN master_post
                                                ON tbl_user_post_apply.fk_post_id = master_post.post_id 
                                            GROUP BY
                                                master_district.District_Code_LGD,
                                                master_district.name;
                                            ");
                }
            } elseif ($currentLevel == 'project') {
                $results = DB::select("SELECT
                                                md.District_Code_LGD AS Pref_Districts,
                                                md.name AS name,
                                                mp.project,
                                                mp.project_code,
                                                COUNT(tupa.apply_id) AS submitted_count,
                                                SUM(
                                                    CASE 
                                                        WHEN tupa.status = 'Rejected' THEN 1 ELSE 0
                                                    END
                                                ) AS rejected_count,
                                                SUM(
                                                    CASE 
                                                        WHEN tupa.status = 'Verified' THEN 1 ELSE 0
                                                    END
                                                ) AS approved_count

                                            FROM master_district md
                                            LEFT JOIN master_projects mp
                                                ON mp.district_lgd_code = md.District_Code_LGD
                                            LEFT JOIN master_post mpst
                                                ON mpst.project_code = mp.project_code 
                                            LEFT JOIN tbl_user_post_apply tupa
                                                ON tupa.fk_post_id = mpst.post_id            
                                            AND tupa.is_final_submit = 1
                                            WHERE md.District_Code_LGD = ?
                                            GROUP BY
                                                md.District_Code_LGD,
                                                md.name,
                                                mp.project,
                                                mp.project_code;", [$districtId]);
            }
        }

        return view('/admin/district_wise_report', compact(
            'results',
            'currentLevel',
            'districtName',
            'cdpoName',
            'awcName'
        ));
    }

    // advertisment 
    public function upload_advertisment()
    {
        return view('admin/upload_advertisment');
    }

    /**
     * OTP Request for create/update advertisement (stages data in pending_advertisements)
     */
    public function requestAdvertisementOtp(Request $request)
    {
        $mode = $request->input('mode', 'create');
        $advertisementId = $request->input('advertisement_id');
        $uid = Session::get('uid', 0);
        $mobile = Session::get('sess_mobile');
        $project_code = Session::get('project_code', 0);
        $district_id = Session::get('district_id', 0);

        if (!$mobile) {
            return response()->json(['success' => false, 'message' => 'मोबाइल नंबर उपलब्ध नहीं है।'], 422);
        }

        if (!in_array($mode, ['create', 'update'])) {
            return response()->json(['success' => false, 'message' => 'अमान्य मोड।'], 422);
        }

        $advertisement = null;
        if ($mode === 'update') {
            $advertisement = DB::table('master_advertisement')->where('Advertisement_ID', $advertisementId)->first();
            if (!$advertisement) {
                return response()->json(['success' => false, 'message' => 'विज्ञापन नहीं मिला।'], 404);
            }
        }

        // Validation rules
        $baseRules = [
            'advertisement_title' => 'required|string|max:255',
            'advertisement_date' => 'required|date',
            'date_for_age' => 'required|date',
            'advertisement_description' => 'required|string',
            'newspaper_publish_date' => 'nullable|date',
            'newspaper_cutting_doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ];
        if ($mode === 'create') {
            $baseRules['advertisement_document'] = 'required|file|mimes:pdf,doc,docx|max:2048';
        } else {
            $baseRules['advertisement_document'] = 'nullable|file|mimes:pdf,doc,docx|max:2048';
        }

        $validator = Validator::make($request->all(), $baseRules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Build payload & upload files
        $payload = [
            'Advertisement_Title' => $request->advertisement_title,
            'district_lgd_code' => $district_id,
            'project_code' => $project_code,
            'Advertisement_Date' => $request->advertisement_date,
            'Date_For_Age' => $request->date_for_age,
            'Advertisement_Description' => $request->advertisement_description,
            'newspaper_publish_date' => $request->newspaper_publish_date,
            'created_by' => $uid,
            'IP_Address' => $request->ip(),
        ];

        // Handle main document
        if ($request->hasFile('advertisement_document')) {
            $uploadedFile = $request->file('advertisement_document');
            $fileName = $uploadedFile->getClientOriginalName();
            $uploadFile = UtilController::upload_file(
                $uploadedFile,
                'advertisement_document',
                'uploads',
                ['jpeg', 'jpg', 'png', 'pdf', 'doc', 'docx'],
                ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            );

            if (!$uploadFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'फ़ाइल अपलोड करने में समस्या आई।'
                ], 422);
            }

            $payload['Advertisement_Doc_Name'] = $fileName;
            $payload['Advertisement_Document'] = $uploadFile;
        } else {
            // update mode fallback to existing
            $payload['Advertisement_Doc_Name'] = $advertisement->Advertisement_Doc_Name ?? null;
            $payload['Advertisement_Document'] = $advertisement->Advertisement_Document ?? null;
        }

        // Handle newspaper cutting
        if ($request->hasFile('newspaper_cutting_doc')) {
            $newspaperCuttingUploadedFile = $request->file('newspaper_cutting_doc');
            $newspaperCuttingFile = UtilController::upload_file(
                $newspaperCuttingUploadedFile,
                'newspaper_cutting_doc',
                'uploads',
                ['jpeg', 'jpg', 'png', 'pdf'],
                ['image/jpeg', 'image/png', 'application/pdf']
            );
            if (!$newspaperCuttingFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'न्यूज़पेपर फ़ाइल अपलोड करने में समस्या आई।'
                ], 422);
            }
            $payload['newspaper_cutting_doc'] = $newspaperCuttingFile;
        } else {
            $payload['newspaper_cutting_doc'] = $advertisement->newspaper_cutting_doc ?? null;
        }

        // Preserve old paths for cleanup if updated
        if ($mode === 'update' && $advertisement) {
            $payload['old_file_path'] = $advertisement->Advertisement_Document;
            $payload['old_cutting_path'] = $advertisement->newspaper_cutting_doc;
        }

        // Generate OTP (store also in tbl_otp_verification for SMS tracking)
        $otp = OtpHelper::generateAndStoreOtp('advertisement', $mobile ?? '');
        (new OtpHelper)->sendOtp($mobile, $otp);
        $expiresAt = Carbon::now()->addMinutes(2);

        $pendingId = DB::table('pending_advertisements')->insertGetId([
            'mode' => $mode,
            'advertisement_id' => $mode === 'update' ? $advertisementId : null,
            'user_id' => $uid,
            'mobile_number' => $mobile,
            'project_code' => $project_code,
            'district_lgd_code' => $district_id,
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
            'is_verified' => false,
            'payload' => json_encode($payload),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'pending_id' => $pendingId,
            'message' => 'OTP भेज दिया गया है। कृपया 2 मिनट में सत्यापित करें।',
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    }

    /**
     * Resend OTP for pending advertisement
     */
    public function resendAdvertisementOtp(Request $request)
    {
        $pendingId = $request->input('pending_id');
        $mobile = Session::get('sess_mobile');

        if (!$mobile) {
            return response()->json(['success' => false, 'message' => 'मोबाइल नंबर उपलब्ध नहीं है।'], 422);
        }

        $pending = DB::table('pending_advertisements')->where('id', $pendingId)->where('is_verified', false)->first();
        if (!$pending) {
            return response()->json(['success' => false, 'message' => 'रिकॉर्ड नहीं मिला या पहले ही सत्यापित हो चुका है।'], 404);
        }

        $otp = OtpHelper::generateAndStoreOtp('advertisement', $mobile ?? '');
        (new OtpHelper)->sendOtp($mobile, $otp);
        $expiresAt = Carbon::now()->addMinutes(2);

        DB::table('pending_advertisements')->where('id', $pendingId)->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP फिर से भेज दिया गया है।',
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    }

    /**
     * Verify OTP and persist advertisement data to main table
     */
    public function verifyAdvertisementOtp(Request $request)
    {
        $pendingId = $request->input('pending_id');
        $otp = $request->input('otp');

        $pending = DB::table('pending_advertisements')->where('id', $pendingId)->first();
        if (!$pending) {
            return response()->json(['success' => false, 'message' => 'रिकॉर्ड नहीं मिला।'], 404);
        }
        if ($pending->is_verified) {
            return response()->json(['success' => false, 'message' => 'पहले ही सत्यापित हो चुका है।'], 422);
        }
        if (!$pending->otp || !$otp || $pending->otp != $otp) {
            return response()->json(['success' => false, 'message' => 'गलत OTP।'], 422);
        }
        if ($pending->otp_expires_at && Carbon::now()->greaterThan(Carbon::parse($pending->otp_expires_at))) {
            return response()->json(['success' => false, 'message' => 'OTP की समय सीमा समाप्त हो गई है।'], 422);
        }

        $data = json_decode($pending->payload, true) ?? [];
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'डेटा उपलब्ध नहीं है।'], 422);
        }

        DB::beginTransaction();
        try {
            if ($pending->mode === 'create') {
                $insertId = DB::table('master_advertisement')->insertGetId([
                    'Advertisement_Title' => $data['Advertisement_Title'],
                    'district_lgd_code' => $data['district_lgd_code'],
                    'project_code' => $data['project_code'],
                    'Advertisement_Date' => $data['Advertisement_Date'],
                    'Advertisement_Doc_Name' => $data['Advertisement_Doc_Name'] ?? null,
                    'Advertisement_Document' => $data['Advertisement_Document'] ?? null,
                    'Date_For_Age' => $data['Date_For_Age'],
                    'Advertisement_Description' => $data['Advertisement_Description'],
                    'newspaper_publish_date' => $data['newspaper_publish_date'] ?? null,
                    'newspaper_cutting_doc' => $data['newspaper_cutting_doc'] ?? null,
                    'created_by' => $data['created_by'] ?? null,
                    'Created_On' => now(),
                    'Last_Updated_dttime' => now(),
                    'IP_Address' => $data['IP_Address'] ?? null,
                ]);
                $advertisementId = $insertId;
            } else {
                $advertisementId = $pending->advertisement_id;
                $advertisement = DB::table('master_advertisement')->where('Advertisement_ID', $advertisementId)->first();
                if (!$advertisement) {
                    return response()->json(['success' => false, 'message' => 'विज्ञापन नहीं मिला।'], 404);
                }

                DB::table('master_advertisement')->where('Advertisement_ID', $advertisementId)->update([
                    'Advertisement_Title' => $data['Advertisement_Title'],
                    'Advertisement_Date' => $data['Advertisement_Date'],
                    'Advertisement_Doc_Name' => $data['Advertisement_Doc_Name'] ?? $advertisement->Advertisement_Doc_Name,
                    'Advertisement_Document' => $data['Advertisement_Document'] ?? $advertisement->Advertisement_Document,
                    'Date_For_Age' => $data['Date_For_Age'],
                    'Advertisement_Description' => $data['Advertisement_Description'],
                    'newspaper_publish_date' => $data['newspaper_publish_date'] ?? null,
                    'newspaper_cutting_doc' => $data['newspaper_cutting_doc'] ?? $advertisement->newspaper_cutting_doc,
                    'Last_Updated_dttime' => now(),
                    'IP_Address' => $data['IP_Address'] ?? null,
                ]);

                // Optionally delete old files if new ones uploaded
                if (!empty($data['Advertisement_Document']) && !empty($data['old_file_path']) && $data['old_file_path'] !== $data['Advertisement_Document']) {
                    @unlink(public_path('uploads/' . $data['old_file_path']));
                }
                if (!empty($data['newspaper_cutting_doc']) && !empty($data['old_cutting_path']) && $data['old_cutting_path'] !== $data['newspaper_cutting_doc']) {
                    @unlink(public_path('uploads/newspaper_cuttings/' . basename($data['old_cutting_path'])));
                }
            }

            DB::table('pending_advertisements')->where('id', $pendingId)->update([
                'is_verified' => true,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'OTP सत्यापित, विज्ञापन सहेजा गया।',
                'advertisement_id' => $advertisementId,
                'redirect_url' => '/admin/show-advertisment'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Verify Advertisement OTP Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'सहेजने में त्रुटि: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store_advertisment(Request $request)
    {
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        $uid = Session::get('uid', 0);
        // Validate the request
        $validator = Validator::make($request->all(), [
            'advertisement_title' => 'required|string|max:255',
            'advertisement_date' => 'required|date',
            'advertisement_document' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'date_for_age' => 'required|date',
            'advertisement_description' => 'required|string',
            'newspaper_publish_date' => 'nullable|date',
            'newspaper_cutting_doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ], 422);
        }

        // uploading file in s3 storage
        $uploadedFile = $request->file('advertisement_document');
        $fileName = $uploadedFile->getClientOriginalName();
        $uploadFile = UtilController::upload_file(
            $uploadedFile,
            'advertisement_document',
            'uploads',
            ['jpeg', 'jpg', 'png', 'pdf'],
            ['image/jpeg', 'image/png', 'application/pdf']
        );

        // Handle newspaper cutting document upload
        $newspaperCuttingFile = null;
        if ($request->hasFile('newspaper_cutting_doc')) {
            $newspaperCuttingUploadedFile = $request->file('newspaper_cutting_doc');
            $newspaperCuttingFile = UtilController::upload_file(
                $newspaperCuttingUploadedFile,
                'newspaper_cutting_doc',
                'uploads',
                ['jpeg', 'jpg', 'png', 'pdf'],
                ['image/jpeg', 'image/png', 'application/pdf']
            );
        }

        // Start database transaction
        DB::beginTransaction();

        try {

            // Insert Data into Database
            $insertId = DB::table('master_advertisement')->insertGetId([
                'Advertisement_Title' => $request->advertisement_title,
                'district_lgd_code' => $district_id,
                'project_code' => $project_code,
                'Advertisement_Date' => $request->advertisement_date,
                'Advertisement_Doc_Name' => $fileName,
                'Advertisement_Document' => $uploadFile,
                'Date_For_Age' => $request->date_for_age,
                'Advertisement_Description' => $request->advertisement_description,
                'newspaper_publish_date' => $request->newspaper_publish_date,
                'newspaper_cutting_doc' => $newspaperCuttingFile,
                'created_by' => session()->get('uid'),
                'Created_On' => now(),
                'Last_Updated_dttime' => now(),
                'IP_Address' => $request->ip(),
            ]);

            // Commit the transaction if            // Commit the transaction if everything is successful everything is successful
            DB::commit();

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'विज्ञापन सफलतापूर्वक जोड़ा गया।',
                'insert_id' => $insertId
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Log the error for debugging
            Log::error('Advertisement Store Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'कुछ गलत हो गया।',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function showAdvertisment(Request $request)
    {
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        if ($request->ajax()) {

            $query = DB::table('master_advertisement')
                ->select(
                    'master_advertisement.Advertisement_Id',
                    'master_advertisement.Advertisement_Title',
                    'master_advertisement.Advertisement_Date',
                    'master_advertisement.Advertisement_Doc_Name',
                    'master_advertisement.Advertisement_Document',
                    'master_advertisement.Date_For_Age',
                    'master_advertisement.newspaper_publish_date',
                    'master_advertisement.newspaper_cutting_doc',
                    'master_advertisement.is_disable'
                );

            if ($role == 'Super_admin' && $district_id) {
                $query->where('master_advertisement.project_code', $project_code);
            } else if ($role == 'Admin') {
                $query->where('master_advertisement.district_lgd_code', $district_id);
            }
            //  Filter: Advertisement Title
            if (!empty($request->adv_title)) {
                $query->where('master_advertisement.Advertisement_Title', 'like', '%' . $request->adv_title . '%');
            }

            //  Filter: Advertisement Date
            if (!empty($request->adv_date)) {
                $query->whereDate('master_advertisement.Advertisement_Date', $request->adv_date);
            }

            //  Sorting (based on column clicked in DataTables)
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $orderColumnName = $request->input("columns.$orderColumnIndex.name");

            $allowedColumns = ['Advertisement_Title', 'Advertisement_Date', 'Advertisement_Doc_Name', 'Date_For_Age'];
            if (in_array($orderColumnName, $allowedColumns)) {
                $query->orderBy('master_advertisement.' . $orderColumnName, $orderDirection);
            } else {
                // Default order if nothing valid is clicked
                $query->orderBy('master_advertisement.Advertisement_Id', 'desc');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('select_checkbox', function ($row) {
                    return '<input type="checkbox" class="advertisement-select-checkbox" data-advertisement-id="' . (int) $row->Advertisement_Id . '" data-advertisement-title="' . e($row->Advertisement_Title) . '">';
                })
                ->addColumn('disable_status', function ($row) {
                    return ((int) ($row->is_disable ?? 1) === 0)
                        ? '<span class="badge bg-danger">Deleted</span>'
                        : '<span class="badge bg-success">Active</span>';
                })

                //  Action Button
                ->addColumn('action', function ($row) use ($role) {
                    $today = date('Y-m-d');
                    $editUrl = route('advertisement.edit', [MD5($row->Advertisement_Id), 'edit']);
                    $buttons = '';

                    if ((int) ($row->is_disable ?? 1) === 1 && $row->Advertisement_Date > $today && $role == 'Super_admin') {
                        $editUrl = route('advertisement.edit', [MD5($row->Advertisement_Id), 'edit']);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="संपादित करें">
                            <i class="bi bi-pencil-square"></i>
                        </a>';
                    } else {
                        $buttons = '-';
                    }

                    return $buttons;
                })

                ->addColumn('action2', function ($row) {
                    $editUrl = route('advertisement.related_docs', MD5($row->Advertisement_Id));
                    return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="संबंधित दस्तावेज" onclick="console.log(\'Clicking: \' + this.href); return true;">
                        <i class="bi bi-file-earmark-text"></i> दस्तावेज
                    </a>';
                })

                ->addColumn('newspaper_cutting', function ($row) {
                    if (!empty($row->newspaper_cutting_doc)) {
                        $post_file_path = asset('uploads/' . $row->newspaper_cutting_doc);
                        if (config('app.env') === 'production') {
                            $post_file_path = config('custom.file_point') . $row->newspaper_cutting_doc;
                        }
                        return '<span class="pdf-downloads">
                                    <a href="#" target="_blank" data-file="' . $post_file_path . '" class="existingFile  btn btn-sm btn-info text-white" 
                                       style="cursor: pointer;" title="देखने के लिए यहाँ क्लिक करें" 
                                       onclick="viewNewspaperCutting(\'' . $post_file_path . '\', \'' . $row->Advertisement_Id . '\'); return false;" 
                                       rel="noopener noreferrer">  <i class="bi bi-eye"></i>
                                    </a>
                                </span>';
                    } else {
                        return '<span class="text-muted">उपलब्ध नहीं</span>';
                    }
                })

                ->addColumn('view_document', function ($row) {
                    if (!empty($row->Advertisement_Document)) {
                        $post_file_path = asset('uploads/' . $row->Advertisement_Document);
                        if (config('app.env') === 'production') {
                            $post_file_path = config('custom.file_point') . $row->Advertisement_Document;
                        }
                        return '<span class="pdf-downloads">
                                    <a href="#" target="_blank" data-file="' . $post_file_path . '" class="existingFile  btn btn-sm btn-info text-white" 
                                       style="cursor: pointer;" title="देखने के लिए यहाँ क्लिक करें" 
                                       onclick="viewAdvertisementDocument(\'' . $post_file_path . '\', \'' . $row->Advertisement_Id . '\'); return false;" 
                                       rel="noopener noreferrer">  <i class="bi bi-eye"></i>
                                    </a>
                                </span>';
                    } else {
                        return '<span class="text-muted">उपलब्ध नहीं</span>';
                    }
                })

                ->addColumn('view_details', function ($row) {
                    $viewUrl = route('advertisement.edit', [MD5($row->Advertisement_Id), 'view']);
                    return '<a href="' . $viewUrl . '" class="btn btn-sm btn-info text-white" title="विवरण देखें">
                       <i class="bi bi-eye"></i> 
                    </a>';
                })

                ->rawColumns(['select_checkbox', 'disable_status', 'action', 'action2', 'newspaper_cutting', 'view_document', 'view_details'])
                ->make(true);
        }

        return view('admin.show_advertisment');
    }

    public function editAdvertisementPage($id, $mode = 'edit')
    {
        // Fetch advertisement data from the database
        $advertisement = DB::table('master_advertisement')
            ->whereRaw('MD5(master_advertisement.Advertisement_ID) = ?', [$id])
            ->first();



        // If no advertisement found, redirect back with error
        if (!$advertisement) {
            return redirect()->route('advertisement.list')->with('error', 'Advertisement not found!');
        }

        // Determine if it's readonly mode
        $readonly = ($mode === 'view');

        // Load the edit blade with data
        return view('admin.edit_advertisment', compact('advertisement', 'readonly'));
    }

    public function updateAdvertisement(Request $request, $id)
    {

        // Validate request
        $validator = Validator::make($request->all(), [
            'advertisement_title' => 'required|string|max:255',
            'advertisement_date' => 'required|date',
            'advertisement_document' => 'nullable|file|mimes:pdf|max:2048',
            'date_for_age' => 'required|date',
            'advertisement_description' => 'required|string',
            'newspaper_publish_date' => 'nullable|date',
            'newspaper_cutting_doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // If validation fails, return JSON response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $advertisement = DB::table('master_advertisement')->where('Advertisement_ID', $id)->first();

        if (!$advertisement) {
            return response()->json(['message' => 'विज्ञापन नहीं मिला।'], 404);
        }

        // Store old file path before potential overwrite
        $oldFilePath = $advertisement->Advertisement_Document;
        $oldFileName = $advertisement->Advertisement_Doc_Name;
        $oldNewspaperCutting = $advertisement->newspaper_cutting_doc;

        // Initialize variables
        $filePath = $oldFilePath;
        $fileName = $oldFileName;

        // Handle file upload
        if ($request->hasFile('advertisement_document')) {

            // Delete old file if exists
            if (!empty($oldFilePath) && file_exists(public_path('uploads/' . $advertisement->Advertisement_Document))) {
                unlink(public_path('uploads/' . $advertisement->Advertisement_Document));
            }

            $uploadedFile = $request->file('advertisement_document');
            $fileName = $uploadedFile->getClientOriginalName();
            $uploadFile = UtilController::upload_file(
                $uploadedFile,
                'advertisement_document',
                'uploads',
                ['jpeg', 'jpg', 'png', 'pdf'],
                ['image/jpeg', 'image/png', 'application/pdf']
            );
            if (!$uploadFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'फ़ाइल सहेजने में असमर्थ।',
                ], 422);
            }

            // Update file path with new location
            $filePath = $uploadFile;
        } else {
            // Keep old file if no new file is uploaded
            $filePath = $oldFilePath;
        }

        // Handle newspaper cutting document upload
        $newspaperCuttingFilePath = $oldNewspaperCutting;
        if ($request->hasFile('newspaper_cutting_doc')) {
            // Delete old newspaper cutting file if exists
            if (!empty($oldNewspaperCutting) && file_exists(public_path('uploads/newspaper_cuttings/' . basename($oldNewspaperCutting)))) {
                unlink(public_path('uploads/newspaper_cuttings/' . basename($oldNewspaperCutting)));
            }

            $newspaperCuttingUploadedFile = $request->file('newspaper_cutting_doc');
            $uploadedNewspaperCutting = UtilController::upload_file(
                $newspaperCuttingUploadedFile,
                'newspaper_cutting_doc',
                'uploads',
                ['jpeg', 'jpg', 'png', 'pdf'],
                ['image/jpeg', 'image/png', 'application/pdf']
            );

            if (!$uploadedNewspaperCutting) {
                return response()->json([
                    'success' => false,
                    'message' => 'प्रसारित पत्र फ़ाइल सहेजने में असमर्थ।',
                ], 422);
            }

            $newspaperCuttingFilePath = $uploadedNewspaperCutting;
        }

        // Update the advertisement
        $sts = DB::table('master_advertisement')->where('Advertisement_ID', $id)->update([
            'Advertisement_Title' => $request->advertisement_title,
            'Advertisement_Date' => $request->advertisement_date,
            'Advertisement_Doc_Name' => $fileName,
            'Advertisement_Document' => $filePath,
            'Date_For_Age' => $request->date_for_age,
            'Advertisement_Description' => $request->advertisement_description,
            'newspaper_publish_date' => $request->newspaper_publish_date,
            'newspaper_cutting_doc' => $newspaperCuttingFilePath,
            'Last_Updated_dttime' => now(),
            'IP_Address' => $request->ip(),
        ]);
        // dd($sts);
        if (!$sts) {
            return response()->json(['status' => 'info', 'message' => 'यहाँ कोई बदलाव लागू नहीं किए गए।', 'insert_id' => $id]);
        }

        return response()->json(['status' => 'success', 'message' => 'विज्ञापन सफलतापूर्वक अपडेट किया गया।', 'insert_id' => $id]);
    }

    public function updateTransition(Request $request)
    {
        // dd($request->tbl_user_detail);
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        if (session('sess_role') === 'Super_admin' || session('sess_role') === 'Admin') {

            $applicant_id = $request->tbl_user_detail['RowID'];
            $fk_post_id = $request->tbl_user_detail['fk_post_id'];

            $doc_name = $request->doc_name;

            switch ($doc_name) {
                case "आधार कार्ड":
                    $doc_name = "Document_Aadhar";
                    break;
                case "स्थानीय निवास प्रमाण पत्र":
                    $doc_name = "Document_Domicile";
                    break;
                case "10वीं कक्षा की अंकसूची":
                    $doc_name = "Document_SSC";
                    break;
                case "12वीं कक्षा की अंकसूची":
                    $doc_name = "Document_Inter";
                    break;
                case "स्नातक की अंकसूची":
                    $doc_name = "Document_UG";
                    break;
                case "स्नातकोत्तर की अंकसूची":
                    $doc_name = "Document_PG";
                    break;
                case "बीपीएल (गरीबी रेखा ) कार्ड":
                    $doc_name = "Document_BPL";
                    break;
                case "अनुभव प्रमाण पत्र":
                    $doc_name = "Document_Exp";
                    break;
                case "विधवा प्रमाण पत्र":
                    $doc_name = "Document_Widow";
                    break;
                case "जाति प्रमाण पत्र":
                    $doc_name = "Document_Caste";
                    break;
                case "5वीं कक्षा की अंकसूची":
                    $doc_name = "Document_5th";
                    break;
                case "8वीं कक्षा की अंकसूची":
                    $doc_name = "Document_8th";
                    break;
                case "अन्य प्रमाण पत्र":
                    $doc_name = "Document_other";
                    break;
                case "मतदाता पहचान पत्र":
                    $doc_name = "Document_Epic";
                    break;
            }

            try {

                DB::beginTransaction();
                $sts = DB::table('tbl_viewed')->insert([
                    'fk_applicant_id' => $applicant_id,
                    'document_name' => $doc_name,
                    'fk_post_id' => $fk_post_id,
                    'district_lgd_code' => $district_id,
                    'project_code' => $project_code,
                    'fk_role_name' => session('sess_role'),
                    'ip_address' => request()->ip(), // Capture IP Address
                    'created_at' => now(), // Current Timestamp
                ]);

                if ($sts) {
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'ऑपरेशन सफलतापूर्वक पूरा हुआ।',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'ऑपरेशन विफल रहा।',
                    ]);
                }
            } catch (Exception $e) {
            }
            // dd(session()->all());
            // dd($doc_name);
        } else {
            // dd("another user");
            return response()->json([
                'success' => false,
                'message' => 'Only admin view count is update',
            ]);
        }
    }

    public function advertisment_docs_open($id)
    {
        $advertisement_name = DB::table('master_advertisement')
            ->whereRaw('MD5(Advertisement_ID) = ?', [$id])
            ->value('Advertisement_Title');

        // dd($advertisement_name);
        return view('admin.advertisment_related_docs', compact('id', 'advertisement_name'));
    }

    public function advertisment_docs_store(Request $request)
    {
        // Backend validation
        $validator = Validator::make($request->all(), [
            'doc_title' => 'required|string|max:255',
            'related_file' => 'required|file|mimes:pdf,jpg,png|max:2048', // Allowed file types and max size
            'description' => 'nullable|string|max:1000',
        ]);

        // If validation fails, return the errors as a response
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Check if a file is uploaded
        if ($request->hasFile('related_file')) {
            // uploading file in s3 storage
            $uploadedFile = $request->file('related_file');
            $fileName = $uploadedFile->getClientOriginalName();
            $uploadFile = UtilController::upload_file(
                $uploadedFile,
                'advertisement_document',
                'uploads',
                ['jpeg', 'jpg', 'png', 'pdf'],
                ['image/jpeg', 'image/png', 'application/pdf']
            );

            // Decrypting the MD5 encrypted post_id (if you have mapping)
            $originalPostId = DB::table('master_advertisement') // Replace with your mapping table
                ->whereRaw('MD5(Advertisement_ID)=? ', $request->Advertisement_ID)
                ->pluck('Advertisement_ID'); // Assuming original post_id is stored in 'original_post_id'

            // dd($originalPostId);
            // Insert the data using raw query
            $result = DB::insert(
                'INSERT INTO tbl_advertisement_corrigendum_map (file_description, fk_adv_id, updated_file_tittle, updated_file_tittle_path,ip_address) 
            VALUES (?, ?, ?, ?,?)',
                [
                    $request->description,  // file_description
                    $originalPostId[0],      // fk_adv_id
                    $request->doc_title,    // updated_file_tittle
                    $uploadFile,               // updated_file_tittle_path
                    $request->ip()
                ]
            );

            // If insert is successful
            if ($result) {
                return response()->json(['message' => 'दस्तावेज सफलतापूर्वक सहेजा गया।'], 200);
            } else {
                return response()->json(['message' => 'कुछ त्रुटि हुई है। कृपया पुनः प्रयास करें।'], 500);
            }
        } else {
            // If no file is uploaded, show an error
            return response()->json(['message' => 'कृपया दस्तावेज अपलोड करें!'], 422);
        }
    }
    public function advertisment_docs_fetch($id)
    {
        $originalId = DB::table('master_advertisement')
            ->select('Advertisement_ID')
            ->whereRaw('MD5(Advertisement_ID)=?', [$id])
            ->pluck('Advertisement_ID');

        $data = DB::table('tbl_advertisement_corrigendum_map')
            ->whereRaw('fk_adv_id =?', [$originalId[0]])
            ->select('adv_file_id', 'updated_file_tittle', 'updated_file_tittle_path', 'file_description', 'updated_date')
            ->orderBy('fk_adv_id', 'DESC')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('updated_file_tittle_path', function ($row) {
                // File path generate karein based on environment
                $filePath = asset('uploads/' . $row->updated_file_tittle_path);
                if (config('app.env') === 'production') {
                    $filePath = config('custom.file_point') . $row->updated_file_tittle_path;
                }

                return '<a href="#" class="btn btn-sm btn-warning viewDocBtn" data-file="' . $filePath . '" data-bs-toggle="modal" data-bs-target="#docModal">दस्तावेज़ देखें</a>';
            })
            ->rawColumns(['updated_file_tittle_path'])
            ->make(true);
    }

    // Store a new skill
    public function storeSkill(Request $request)
    {
        $request->validate([
            'skill_name' => 'required|string|max:100'
        ]);

        try {
            DB::beginTransaction();

            $skillOptions = null;
            if ($request->has('options')) {
                // Ensure options is an array before encoding, even if it's empty
                $optionsData = is_array($request->options) ? $request->options : [];
                $skillOptions = json_encode($optionsData, JSON_UNESCAPED_UNICODE);
            }

            // Insert into master_skills table unconditionally
            $skillId = DB::table('master_skills')->insertGetId([
                'skill_name' => $request->skill_name,
                'skill_options' => $skillOptions, // Use the prepared variable
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Skill added successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error occurred while adding skill.']);
        }
    }

    public function showSkillsForm(Request $request)
    {
        if ($request->ajax()) {
            $skills = DB::table('master_skills')->get();
            $data = [];

            foreach ($skills as $index => $skill) {
                $options = json_decode($skill->skill_options, true);
                $optionList = '';
                if (!empty($options)) {
                    $optionList .= '<ol class="mb-0 ps-3">';
                    foreach ($options as $opt) {
                        $optionList .= "<li>{$opt}</li>";
                    }
                    $optionList .= '</ol>';
                } else {
                    $optionList = '<em>कोई विकल्प नहीं</em>';
                }

                $data[] = [
                    $index + 1,
                    $skill->skill_name,
                    $optionList,
                ];
            }

            // Return the response in the format expected by DataTables
            return response()->json([
                'draw' => intval($request->draw), // Ensure draw is an integer
                'recordsTotal' => $skills->count(), // Total records before filtering
                'recordsFiltered' => $skills->count(), // Total records after filtering (if applicable)
                'data' => $data,
            ]);
        }

        // This will be for the non-AJAX view.
        $skills = DB::table('master_skills')->get();
        return view('admin.add_skill', compact('skills'));
    }


    public function showSubjectsForm()
    {
        $subjects = DB::table('master_subjects')
            ->join('master_qualification', 'master_qualification.Quali_ID', '=', 'master_subjects.fk_Quali_ID')
            ->select('master_subjects.*', 'master_qualification.Quali_Name')
            ->orderBy('subject_id', 'asc')->get();
        $qualifications = DB::table('master_qualification')->orderBy('Quali_ID', 'asc')->get();

        return view('admin.add_subject', compact('subjects', 'qualifications'));
    }

    public function storeSubject(Request $request)
    {
        $fk_Quali_ID = $request->input('fk_Quali_ID');
        $subject_name = $request->input('subject_name');

        // Last subject_code get karo current fk_Quali_ID ke liye
        $lastCode = DB::table('master_subjects')
            ->where('fk_Quali_ID', $fk_Quali_ID)
            ->orderByRaw('CAST(subject_code AS UNSIGNED) DESC')
            ->value('subject_code');

        if ($lastCode) {
            $nextCode = (int) $lastCode + 1;
        } else {
            // Pehla code banega: Qualification ID + '01' jaise 401
            $nextCode = $fk_Quali_ID . '01';
        }

        // Insert new subject
        DB::table('master_subjects')->insert([
            'fk_Quali_ID' => $fk_Quali_ID,
            'subject_code' => $nextCode,
            'subject_name' => $subject_name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Subject added successfully',
            'subject_code' => $nextCode
        ]);
    }

    public function getCategoryDetail(Request $request)
    {
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $caste = $request->input('caste');

        $query = DB::table('tbl_user_post_apply')
            ->join('tbl_user_detail', 'tbl_user_post_apply.fk_applicant_id', '=', 'tbl_user_detail.Applicant_ID')
            ->where('tbl_user_detail.Caste', $caste)
            ->where('is_final_submit', '1');

        if ($role !== 'Super_admin' && $district_id > 0) {
            $query->where('tbl_user_post_apply.fk_district_id', $district_id);
        }

        $result = $query->select('tbl_user_detail.Caste', DB::raw('COUNT(*) as total'))
            ->groupBy('tbl_user_detail.Caste')
            ->get();

        $labels = $result->pluck('Caste');
        $values = $result->pluck('total');

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function exportAllApplications(Request $request)
    {
        $role = session()->get("sess_role");
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);
        DB::statement("SET @row := 0");

        if ($role === 'Super_admin' && $district_id) {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'master_post.title AS Title',
                    'master_user.Mobile_Number AS Mobile_Number',
                    DB::raw('DATE_FORMAT(tbl_user_post_apply.apply_date, "%d-%m-%Y") AS App_Date'),  // Date format applied here
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->orderBy('tbl_user_detail.ID', 'asc');
        } elseif ($role === 'Admin') {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'master_post.title AS Title',
                    'master_user.Mobile_Number AS Mobile_Number',
                    DB::raw('DATE_FORMAT(tbl_user_post_apply.apply_date, "%d-%m-%Y") AS App_Date'),  // Date format applied here
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                ->orderBy('tbl_user_detail.ID', 'asc');
        } elseif ($role === 'Super_admin') {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'master_post.title AS Title',
                    'master_user.Mobile_Number AS Mobile_Number',
                    DB::raw('DATE_FORMAT(tbl_user_post_apply.apply_date, "%d-%m-%Y") AS App_Date'),  // Date format applied here
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.is_final_submit', '1') //  Added condition for stepCount = 5
                ->orderBy('tbl_user_detail.ID', 'asc');
        }

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }

        $mainHeading = "महिला एवं बाल विकास विभाग";

        $headings = [
            'क्रम संख्या',
            'आवेदनकर्ता का नाम',
            'पद का शीर्षक',
            'मोबाइल नंबर',
            'आवेदन तिथि',
            'आवेदन की स्थिति'
        ];

        return DataUtility::exportToExcel($query, 'All_Application_List.xlsx', $headings, $mainHeading);
    }
    public function exportMeritList(Request $request)
    {
        DB::statement("SET @row := 0");

        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");
        $project_code = session()->get('project_code');
        $filterAdvertisementId = $request->get('advertisement_id');
        $filterPostId = $request->get('post_id');
        $filterGp = $request->get('gp_name');
        $filterVillage = $request->get('village_name');
        $filterNagar = $request->get('nagar_name');
        $filterWard = $request->get('ward_name');

        if ($role === 'Super_admin' && $district_id) {
            $subQuery = DB::table('tbl_user_detail')
                ->select(
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'tbl_user_detail.FatherName AS FatherName',
                    'tbl_user_detail.DOB AS DOB',
                    'master_post.title AS Post_Name',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.domicile_mark',
                    'tbl_user_post_apply.v_p_t_questionMarks',
                    'tbl_user_post_apply.secc_questionMarks',
                    'tbl_user_post_apply.kan_ash_questionMarks',
                    'tbl_user_post_apply.min_experiance_mark',
                    'tbl_user_post_apply.min_edu_qualification_mark',
                    DB::raw('tbl_user_post_apply.total_mark AS Total_Marks'),
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.is_marks_confirmed', '1')
                ->where('tbl_user_post_apply.status', 'Verified')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->whereNotNull('tbl_user_post_apply.edu_qualification_mark')
                ->groupBy('tbl_user_post_apply.fk_applicant_id', 'tbl_user_post_apply.fk_post_id')
                ->orderBy('tbl_user_post_apply.edu_qualification_mark');

            if (!empty($filterAdvertisementId)) {
                $subQuery->where('master_post.Advertisement_ID', $filterAdvertisementId);
            }

            if (!empty($filterPostId)) {
                $subQuery->where('master_post.post_id', $filterPostId);
            }
            if (!empty($filterGp)) {
                $subQuery->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
            }
            if (!empty($filterVillage)) {
                $subQuery->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
            }
            if (!empty($filterNagar)) {
                $subQuery->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
            }
            if (!empty($filterWard)) {
                $subQuery->where(function ($q) use ($filterWard) {
                    $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                        ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                });
            }

            $query = DB::table(DB::raw("({$subQuery->toSql()}) AS temp"))
                ->mergeBindings($subQuery)
                ->select(
                    DB::raw('@row := @row + 1 AS SerialNumber'),
                    'temp.*'
                );
        } elseif ($role === 'Admin') {
            $subQuery = DB::table('tbl_user_detail')
                ->select(
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'tbl_user_detail.FatherName AS FatherName',
                    'tbl_user_detail.DOB AS DOB',
                    'master_post.title AS Post_Name',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.domicile_mark',
                    'tbl_user_post_apply.v_p_t_questionMarks',
                    'tbl_user_post_apply.secc_questionMarks',
                    'tbl_user_post_apply.kan_ash_questionMarks',
                    'tbl_user_post_apply.min_experiance_mark',
                    'tbl_user_post_apply.min_edu_qualification_mark',
                    DB::raw('tbl_user_post_apply.total_mark AS Total_Marks'),
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                ->where('master_post.project_code', $project_code)
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.is_marks_confirmed', '1')
                ->where('tbl_user_post_apply.status', 'Verified')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->whereNotNull('tbl_user_post_apply.edu_qualification_mark')
                ->groupBy('tbl_user_post_apply.fk_applicant_id', 'tbl_user_post_apply.fk_post_id')
                ->orderBy('tbl_user_post_apply.edu_qualification_mark');

            if (!empty($filterAdvertisementId)) {
                $subQuery->where('master_post.Advertisement_ID', $filterAdvertisementId);
            }

            if (!empty($filterPostId)) {
                $subQuery->where('master_post.post_id', $filterPostId);
            }
            if (!empty($filterGp)) {
                $subQuery->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
            }
            if (!empty($filterVillage)) {
                $subQuery->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
            }
            if (!empty($filterNagar)) {
                $subQuery->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
            }
            if (!empty($filterWard)) {
                $subQuery->where(function ($q) use ($filterWard) {
                    $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                        ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                });
            }

            $query = DB::table(DB::raw("({$subQuery->toSql()}) AS temp"))
                ->mergeBindings($subQuery)
                ->select(
                    DB::raw('@row := @row + 1 AS SerialNumber'),
                    'temp.*'
                );
        } elseif ($role === 'Super_admin') {
            $subQuery = DB::table('tbl_user_detail')
                ->select(
                    DB::raw("CONCAT_WS(' ', tbl_user_detail.First_Name,tbl_user_detail.Middle_Name, tbl_user_detail.Last_Name) AS Full_Name"),
                    'tbl_user_detail.FatherName AS FatherName',
                    'tbl_user_detail.DOB AS DOB',
                    'master_post.title AS Post_Name',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.domicile_mark',
                    'tbl_user_post_apply.v_p_t_questionMarks',
                    'tbl_user_post_apply.secc_questionMarks',
                    'tbl_user_post_apply.kan_ash_questionMarks',
                    'tbl_user_post_apply.min_experiance_mark',
                    'tbl_user_post_apply.min_edu_qualification_mark',
                    DB::raw('tbl_user_post_apply.total_mark AS Total_Marks'),
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_applicant_education_qualification', 'tbl_user_detail.ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->leftJoin('master_panchayats as gp', 'tbl_user_post_apply.post_gp', '=', 'gp.panchayat_lgd_code')
                ->leftJoin('master_villages as vill', 'tbl_user_post_apply.post_village', '=', 'vill.village_code')
                ->leftJoin('master_nnn as nnn', 'tbl_user_post_apply.post_nagar', '=', 'nnn.std_nnn_code')
                ->leftJoin('master_ward as ward', 'tbl_user_post_apply.post_ward', '=', 'ward.ID')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.is_marks_confirmed', '1')
                ->where('tbl_user_post_apply.status', 'Verified')
                ->whereNotNull('tbl_user_post_apply.edu_qualification_mark')
                ->groupBy('tbl_user_post_apply.fk_applicant_id', 'tbl_user_post_apply.fk_post_id')
                ->orderBy('tbl_user_post_apply.edu_qualification_mark');

            if (!empty($filterAdvertisementId)) {
                $subQuery->where('master_post.Advertisement_ID', $filterAdvertisementId);
            }

            if (!empty($filterPostId)) {
                $subQuery->where('master_post.post_id', $filterPostId);
            }
            if (!empty($filterGp)) {
                $subQuery->where('gp.panchayat_name_hin', 'like', '%' . $filterGp . '%');
            }
            if (!empty($filterVillage)) {
                $subQuery->where('vill.village_name_hin', 'like', '%' . $filterVillage . '%');
            }
            if (!empty($filterNagar)) {
                $subQuery->where('nnn.nnn_name', 'like', '%' . $filterNagar . '%');
            }
            if (!empty($filterWard)) {
                $subQuery->where(function ($q) use ($filterWard) {
                    $q->where('ward.ward_name', 'like', '%' . $filterWard . '%')
                        ->orWhere('ward.ward_no', 'like', '%' . $filterWard . '%');
                });
            }

            $query = DB::table(DB::raw("({$subQuery->toSql()}) AS temp"))
                ->mergeBindings($subQuery)
                ->select(
                    DB::raw('@row := @row + 1 AS SerialNumber'),
                    'temp.*'
                );
        }

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }
        $mainHeading = "महिला एवं बाल विकास विभाग";

        $headings = [
            'क्रम संख्या',                      // SerialNumber (added via raw @row := @row + 1)
            'पूरा नाम',                        // Full_Name
            'पिता का नाम',                        // FatherName
            'जन्म तिथि',                        // DOB
            'पद का शीर्षक',                    // Post_Name
            'मोबाइल नंबर',                     // Mobile_Number
            'जाति प्रमाण अंक',                  // domicile_mark
            'विधवा/परित्यक्ता/तलाकशुदा प्रश्न अंक',       // v_p_t_questionMarks
            'गरीबी रेखा प्रश्न अंक',               // secc_questionMarks
            'कन्या आश्रम प्रश्न अंक',              // kan_ash_questionMarks
            'अनुभव अंक',                      // min_experiance_mark
            'न्यूनतम शैक्षिक योग्यता अंक',        // min_edu_qualification_mark
            'कुल अंक'                        // Total_Marks
        ];

        return DataUtility::exportToExcel($query, 'merit_list_data.xlsx', $headings, $mainHeading);
    }

    public function exportDistrict_wise_applications(Request $request, )
    {
        // Not required anymore if using ROW_NUMBER()
        // DB::statement("SET @row := 0");
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");
        $project_code = session()->get('project_code');


        $query = DB::table('master_district')
            ->leftJoin('tbl_user_post_apply', function ($join) {
                $join->on('tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD')
                    ->where('tbl_user_post_apply.is_final_submit', '=', 1); // Only final submitted
            })
            ->leftJoin('tbl_user_detail', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
            ->select(
                DB::raw('ROW_NUMBER() OVER() AS SerialNumber'),
                'master_district.name  as Dist_name',
                DB::raw('COALESCE(COUNT(CASE WHEN tbl_user_post_apply.is_final_submit = 1 THEN 1 ELSE NULL END), 0) AS submitted_count'),
                DB::raw('COALESCE(SUM(CASE WHEN tbl_user_post_apply.status = "Rejected" AND tbl_user_post_apply.is_final_submit = 1 THEN 1 ELSE 0 END), 0) AS rejected_count'),
                DB::raw('COALESCE(SUM(CASE WHEN tbl_user_post_apply.status = "Verified" AND tbl_user_post_apply.is_final_submit = 1 THEN 1 ELSE 0 END), 0) AS approved_count')
            )
            ->where('tbl_user_post_apply.fk_district_id', $district_id)
            ->groupBy('master_district.District_Code_LGD', 'master_district.name');

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }

        $mainHeading = "महिला एवं बाल विकास विभाग";

        $headings = [
            'क्रम संख्या',
            'जिला',
            'प्राप्त आवेदन',
            'अपात्र  आवेदन',
            'पात्र  आवेदन'
        ];

        return DataUtility::exportToExcel($query, 'district_wise_applications.xlsx', $headings, $mainHeading);
    }

    public function exportVerified_list(Request $request, $pref_dist = null)
    {
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");
        $project_code = session()->get('project_code');
        if ($role === 'Super_admin' && $district_id) {
            if ($district_id === 0) {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->whereRaw('MD5(tbl_user_post_apply.fk_district_id)=?', [$pref_dist]); //  Condition on tbl_user_post_apply

            } else {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.fk_district_id', $district_id); //  Condition on tbl_user_post_apply

            }
        } elseif ($role === 'Super_admin') {
            if ($district_id === 0) {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->whereRaw('MD5(tbl_user_post_apply.fk_district_id)=?', [$pref_dist]); //  Condition on tbl_user_post_apply

            } else {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('master_post.project_code', $project_code)
                    ->where('tbl_user_post_apply.status', 'Verified')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.fk_district_id', $district_id); //  Condition on tbl_user_post_apply

            }
        } elseif ($role === 'Admin') {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    'tbl_user_detail.First_Name',
                    'master_post.title',
                    DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                    'master_user.Mobile_Number',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('tbl_user_post_apply.status', 'Verified')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id);
        } else {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    'tbl_user_detail.First_Name',
                    'master_post.title',
                    DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                    'master_user.Mobile_Number',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.status', '=', 'Verified')
                ->where('tbl_user_post_apply.is_final_submit', '1');
        }

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }

        $headings = [
            'क्रम संख्या',
            'आवेदनकर्ता का नाम',
            'पद का नाम',
            'आवेदन की तिथि',
            'मोबाइल नंबर',
            'आवेदन स्थिति'
        ];

        $mainHeading = "महिला एवं बाल विकास विभाग";

        return DataUtility::exportToExcel($query, 'district_wise_approved_applications.xlsx', $headings, $mainHeading);
    }


    public function exportRejected_list(Request $request, $pref_dist = null)
    {
        $district_id = session()->get("district_id");
        $role = session()->get("sess_role");
        $project_code = session()->get('project_code');

        if ($role === 'Super_admin' && $district_id) {
            if ($district_id === 0) {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Rejected')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->whereRaw('MD5(tbl_user_post_apply.fk_district_id) = ?', [$pref_dist]);
            } else {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Rejected')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.fk_district_id', $district_id);
            }
        } elseif ($role === 'Super_admin') {
            if ($district_id === 0) {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Rejected')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->whereRaw('MD5(tbl_user_post_apply.fk_district_id) = ?', [$pref_dist]);
            } else {
                $query = DB::table('tbl_user_detail')
                    ->select(
                        DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                        'tbl_user_detail.First_Name',
                        'master_post.title',
                        DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                        'master_user.Mobile_Number',
                        'master_user.Mobile_Number',
                        'tbl_user_post_apply.status AS Application_Status'
                    )
                    ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                    ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                    ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') //  Corrected join
                    ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                    ->where('tbl_user_post_apply.status', 'Rejected')
                    ->where('tbl_user_post_apply.is_final_submit', '1')
                    ->where('tbl_user_post_apply.fk_district_id', $district_id);
            }
        } elseif ($role === 'Admin') {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    'tbl_user_detail.First_Name',
                    'master_post.title',
                    DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                    'master_user.Mobile_Number',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_district', 'tbl_user_post_apply.fk_district_id', '=', 'master_district.District_Code_LGD') // ✅ Corrected join
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('master_post.project_code', $project_code)
                ->where('tbl_user_post_apply.status', 'Rejected')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id);
        } else {
            $query = DB::table('tbl_user_detail')
                ->select(
                    DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                    'tbl_user_detail.First_Name',
                    'master_post.title',
                    DB::raw('DATE_FORMAT(tbl_user_detail.Created_On, "%d-%m-%Y") AS Application_date'),  // Date format applied here
                    'master_user.Mobile_Number',
                    'master_user.Mobile_Number',
                    'tbl_user_post_apply.status AS Application_Status'
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.status', '=', 'Rejected')
                ->where('tbl_user_post_apply.is_final_submit', '1');
        }

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }

        $headings = [
            'क्रम संख्या',
            'आवेदनकर्ता का नाम',
            'पद का नाम',
            'आवेदन की तिथि',
            'मोबाइल नंबर',
            'आवेदन स्थिति'
        ];

        $mainHeading = "महिला एवं बाल विकास विभाग";

        return DataUtility::exportToExcel($query, 'district_wise_rejected_applications.xlsx', $headings, $mainHeading);
    }

    public function exportAdvertisment(Request $request)
    {
        $user_id = Session::get('user_id', 0);
        DB::statement("SET @row := 0");
        $query = DB::table('master_advertisement')
            ->select(
                DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                'Advertisement_Title',
                'Advertisement_Doc_Name',
                DB::raw('DATE_FORMAT(Advertisement_Date, "%d-%m-%Y")'),
                DB::raw('DATE_FORMAT(Date_For_Age, "%d-%m-%Y")')
            )
            ->where('Created_by', $user_id);

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }

        $headings = [
            'क्रम संख्या',
            'विज्ञापन शीर्षक',
            'विज्ञापन दस्तावेज़ नाम',
            'विज्ञापन तिथि',
            'वैधता तिथि'
        ];
        $mainHeading = "महिला एवं बाल विकास विभाग";
        return DataUtility::exportToExcel($query, 'advertisement_list.xlsx', $headings, $mainHeading);
    }

    public function exportPosts(Request $request)
    {
        $district_id = session()->get("district_id");
        DB::statement("SET @row := 0");
        $query = DB::table('master_post')
            ->leftJoin('master_post_category', 'master_post.cat_id', '=', 'master_post_category.cat_id')
            ->leftJoin('post_map', 'master_post.post_id', '=', 'post_map.fk_post_id')
            ->leftJoin('master_district', 'post_map.fk_district_id', '=', 'master_district.District_Code_LGD')
            ->leftJoin('master_qualification', 'master_post.Quali_ID', '=', 'master_qualification.Quali_ID')
            ->leftJoin('post_vacancy_map', 'master_post.post_id', '=', 'post_vacancy_map.fk_post_id')
            ->leftJoin('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
            ->select(
                DB::raw('ROW_NUMBER() OVER() as SerialNumber'),
                'master_advertisement.Advertisement_Title AS advertisement_title',
                'master_post.title AS post_name',
                'master_post_category.cat_name AS category_name',
                DB::raw("GROUP_CONCAT(DISTINCT master_district.name ORDER BY master_district.name SEPARATOR ', ') AS district_names"),
                DB::raw("SUM(DISTINCT post_vacancy_map.no_of_vacancy) AS vacancy_count"),
                'master_post.min_age AS min_age',
                'master_post.max_age AS max_age',
                'master_qualification.Quali_Name AS qualification_name',
                DB::raw('DATE_FORMAT(master_advertisement.Advertisement_Date, "%d-%m-%Y")'),
                DB::raw('DATE_FORMAT(master_advertisement.Date_For_Age, "%d-%m-%Y")')

            )
            ->where('post_map.fk_district_id', $district_id)
            ->whereColumn('post_vacancy_map.fk_post_id', 'master_post.post_id')  // Ensuring fk_post_id matches
            ->groupBy(
                'master_post.post_id',
                'master_post.title',
                'master_post_category.cat_name',
                'master_advertisement.Advertisement_Date',
                'master_post.max_age',
                'master_qualification.Quali_Name'
            );

        // CheckOnly response for JS
        if ($request->has('checkOnly')) {
            $count = $query->count();
            return response()->json(['count' => $count]);
        }

        $headings = [
            'क्रम संख्या',
            'विज्ञापन शीर्षक',
            'पद का नाम',
            'श्रेणी',
            'जिले का नाम',
            'कुल रिक्तियाँ',
            'न्यूनतम आयु',
            'अधिकतम आयु',
            'न्यूनतम शैक्षणिक योग्यता',
            'विज्ञापन तिथि',
            'वैधता तिथि'
        ];
        $mainHeading = "महिला एवं बाल विकास विभाग";


        return DataUtility::exportToExcel($query, 'master_post_list.xlsx', $headings, $mainHeading);
    }

    public function getAdminNotifications()
    {
        try {
            $project_code = Session::get('project_code', 0);

            // Get pending approvals for notification based on project code
            $notifications = DB::select("
                SELECT 
                    ma.Advertisement_ID,
                    mp.project,
                    ma.Advertisement_Title as advertisement_title,
                    ma.Date_For_Age,
                    DATEDIFF(CURDATE(), ma.Date_For_Age) as days_after_expiry,
                    COUNT(DISTINCT post.post_id) as total_posts,
                    COUNT(upa.apply_id) as pending_applications
                FROM master_advertisement ma
                INNER JOIN master_post post ON ma.Advertisement_ID = post.Advertisement_ID 
                INNER JOIN master_projects mp ON ma.project_code = mp.project_code
                LEFT JOIN tbl_user_post_apply upa ON post.post_id = upa.fk_post_id 
                    AND upa.is_final_submit = 1 
                    AND upa.status = 'Submitted'
                WHERE post.project_code = ?
                    AND DATEDIFF(CURDATE(), ma.Date_For_Age) > 21
                GROUP BY ma.Advertisement_ID, mp.project, ma.Advertisement_Title, ma.Date_For_Age
                HAVING pending_applications > 0
                ORDER BY days_after_expiry DESC
            ", [$project_code]);

            // Calculate total notification count
            $totalNotifications = count($notifications);

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total_count' => $totalNotifications
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notifications: ' . $e->getMessage(),
                'notifications' => [],
                'total_count' => 0
            ]);
        }
    }

    function utf($string)
    {
        return htmlspecialchars($string);
    }

    /**
     * Show feedback form (works for both Admin and Candidate)
     */
    public function feedbackForm()
    {
        // Check user role from session
        $role = Session::get('sess_role');

        // Both admin and candidate will use same blade file (admin.feedback_form)
        // The layout already handles different roles properly
        return view('admin.feedback_form');
    }

    /**
     * Submit feedback
     */
    public function submitFeedback(Request $request)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'feedback_type' => 'required|in:suggestion,complaint,appreciation,bug_report,feature_request,other',
                // 'rating' => 'required|integer|min:1|max:5',
                'subject' => 'required|string|max:200',
                'feedback_message' => 'required|string|max:1000',
                'contact_email' => 'nullable|email|max:100',
                'feedback_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max per image
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'सत्यापन विफल / Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get admin details from session
            $adminId = Session::get('uid');
            $adminName = Session::get('sess_fname');
            $designation = Session::get('designation');
            $districtId = Session::get('district_id', 0);

            $recentFeedback = DB::table('admin_feedbacks')
                ->where('admin_id', $adminId)
                ->where('created_at', '>=', now()->subHour())
                ->first();

            // dd($recentFeedback);

            if ($recentFeedback) {
                $createdAt = Carbon::parse($recentFeedback->created_at);
                $nextAllowedAt = $createdAt->copy()->addHour();
                $now = now();
                $remainingSeconds = max(0, $nextAllowedAt->diffInSeconds($now));
                $remainingMinutes = ceil($remainingSeconds / 60);

                return response()->json([
                    'status' => 'error',
                    'message' => "आप $remainingMinutes मिनट बाद ही दोबारा फीडबैक सबमिट कर सकते हैं। / You can submit feedback again in $remainingMinutes minute(s)."
                ], 429);
            }

            // Handle image uploads using S3 storage (same as other documents)
            $imagePaths = [];
            if ($request->hasFile('feedback_images')) {
                foreach ($request->file('feedback_images') as $index => $image) {
                    if ($index >= 5)
                        break; // Max 5 images

                    try {
                        // Use UtilController's upload_file method (same as advertisement documents)
                        $uploadedFilePath = UtilController::upload_file(
                            $image,
                            'feedbacks', // Upload path in S3/local
                            'uploads', // Disk name
                            ['jpeg', 'jpg', 'png', 'gif'], // Allowed extensions
                            ['image/jpeg', 'image/png', 'image/gif'] // Allowed MIME types
                        );

                        if ($uploadedFilePath) {
                            $imagePaths[] = $uploadedFilePath;
                        }
                    } catch (\Exception $uploadError) {
                        Log::error('Feedback image upload error: ' . $uploadError->getMessage());
                        // Continue with other images even if one fails
                        continue;
                    }
                }
            }

            // Insert feedback into database
            $feedbackId = DB::table('admin_feedbacks')->insertGetId([
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'designation' => $designation,
                'district_id' => $districtId,
                'feedback_type' => $request->feedback_type,
                // 'rating' => $request->rating,
                'subject' => $request->subject,
                'feedback_message' => $request->feedback_message,
                'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
                'contact_email' => $request->contact_email,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'प्रतिक्रिया सफलतापूर्वक सबमिट की गई! / Feedback submitted successfully!',
                'feedback_id' => $feedbackId
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            Log::error('Feedback submission error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'प्रतिक्रिया सबमिट करने में विफल। कृपया पुनः प्रयास करें। / Failed to submit feedback. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get recent feedbacks for logged-in admin
     */
    public function getRecentFeedbacks(Request $request)
    {
        try {
            $adminId = Session::get('uid');

            $feedbacks = DB::table('admin_feedbacks')
                ->where('admin_id', $adminId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($feedback) {
                    $feedback->created_at = Carbon::parse($feedback->created_at)->diffForHumans();

                    // Decode images JSON and generate proper URLs for S3/local
                    if ($feedback->images) {
                        $imagePaths = json_decode($feedback->images, true);
                        $imageUrls = [];

                        foreach ($imagePaths as $path) {
                            if (config('app.env') === 'production') {
                                // Production: S3 URL
                                $imageUrls[] = config('custom.file_point') . $path;
                            } else {
                                // Local: Asset URL
                                $imageUrls[] = asset($path);
                            }
                        }

                        $feedback->image_urls = $imageUrls;
                        $feedback->image_count = count($imageUrls);
                    } else {
                        $feedback->image_urls = [];
                        $feedback->image_count = 0;
                    }

                    return $feedback;
                });

            return response()->json([
                'status' => 'success',
                'data' => $feedbacks
            ]);
        } catch (\Exception $e) {
            Log::error('Get recent feedbacks error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load feedbacks'
            ], 500);
        }
    }

    /**
     * Display feedback list page
     */
    public function feedbackList()
    {
        return view('admin.feedback_list');
    }

    /**
     * Get feedback list data for DataTables
     */
    public function getFeedbackListData(Request $request)
    {
        try {
            $role = Session::get('sess_role');
            $adminId = Session::get('uid');
            $districtId = Session::get('district_id', 0);

            // Base query
            $query = DB::table('admin_feedbacks')
                ->select(
                    'id',
                    'admin_name',
                    'feedback_type',
                    'subject',
                    'feedback_message',
                    'contact_email',
                    'status',
                    'images',
                    'admin_response',
                    'responded_at',
                    'created_at'
                )
                ->orderBy('created_at', 'desc');

            // Role-based filtering
            if ($role === 'Admin' && $districtId > 0) {
                // Admin sees only their district's feedbacks
                $query->where('district_id', $districtId);
            }
            // Super_admin sees all feedbacks (no filter)

            // Apply filters from request
            if ($request->filled('feedback_type')) {
                $query->where('feedback_type', $request->feedback_type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('admin_name')) {
                $query->where('admin_name', 'like', '%' . $request->admin_name . '%');
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // DataTables processing
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('admin_name', function ($row) {
                    return '<strong>' . htmlspecialchars($row->admin_name ?? '-', ENT_QUOTES, 'UTF-8') . '</strong>';
                })
                ->editColumn('feedback_type', function ($row) {
                    $types = [
                        'suggestion' => '<span class="badge bg-info">सुझाव</span>',
                        'complaint' => '<span class="badge bg-warning">शिकायत</span>',
                        'appreciation' => '<span class="badge bg-success">प्रशंसा</span>',
                        'bug_report' => '<span class="badge bg-danger">बग रिपोर्ट</span>',
                        'feature_request' => '<span class="badge bg-primary">फीचर अनुरोध</span>',
                        'other' => '<span class="badge bg-secondary">अन्य</span>',
                    ];
                    return $types[$row->feedback_type] ?? $row->feedback_type;
                })
                ->editColumn('subject', function ($row) {
                    $subject = htmlspecialchars($row->subject ?? '-', ENT_QUOTES, 'UTF-8');
                    if (strlen($subject) > 50) {
                        return '<span title="' . $subject . '">' . substr($subject, 0, 50) . '...</span>';
                    }
                    return $subject;
                })
                ->editColumn('feedback_message', function ($row) {
                    $message = htmlspecialchars($row->feedback_message ?? '-', ENT_QUOTES, 'UTF-8');
                    if (strlen($message) > 80) {
                        return '<span title="' . $message . '">' . substr($message, 0, 80) . '...</span>';
                    }
                    return $message;
                })
                ->editColumn('status', function ($row) {
                    $statuses = [
                        'pending' => '<span class="badge bg-warning">लंबित</span>',
                        'in_progress' => '<span class="badge bg-info">प्रगति में</span>',
                        'resolved' => '<span class="badge bg-success">हल हो गया</span>',
                        'closed' => '<span class="badge bg-secondary">बंद</span>',
                    ];
                    return $statuses[$row->status] ?? $row->status;
                })
                ->editColumn('images', function ($row) {
                    if ($row->images) {
                        $imagePaths = json_decode($row->images, true);
                        $count = count($imagePaths);
                        return '<span class="badge bg-info"><i class="bi bi-images"></i> ' . $count . '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d-m-Y H:i', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';

                    // View button
                    $buttons .= '<button class="btn btn-sm btn-primary view-feedback-btn" 
                                    data-id="' . $row->id . '" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewFeedbackModal"
                                    title="विवरण देखें">
                                    <i class="bi bi-eye"></i>
                                </button>';

                    // Reply button (only if not closed)
                    if ($row->status !== 'closed') {
                        $buttons .= '<button class="btn btn-sm btn-success reply-feedback-btn ms-1" 
                                        data-id="' . $row->id . '" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#replyFeedbackModal"
                                        title="जवाब दें">
                                        <i class="bi bi-reply-fill"></i>
                                    </button>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['admin_name', 'feedback_type', 'status', 'subject', 'feedback_message', 'images', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Feedback list data error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load feedback data'
            ], 500);
        }
    }

    /**
     * Get single feedback detail
     */
    public function getFeedbackDetail($id)
    {
        try {
            $role = Session::get('sess_role');
            $districtId = Session::get('district_id', 0);

            // Get feedback with role-based filtering
            $query = DB::table('admin_feedbacks')
                ->where('id', $id);

            // Role-based filtering
            if ($role === 'Admin' && $districtId > 0) {
                $query->where('district_id', $districtId);
            }

            $feedback = $query->first();

            if (!$feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feedback not found or access denied'
                ], 404);
            }

            // Format feedback type
            $feedbackTypes = [
                'suggestion' => 'सुझाव',
                'complaint' => 'शिकायत',
                'appreciation' => 'प्रशंसा',
                'bug_report' => 'बग रिपोर्ट',
                'feature_request' => 'फीचर अनुरोध',
                'other' => 'अन्य',
            ];

            // Format status
            $statuses = [
                'pending' => 'लंबित',
                'in_progress' => 'प्रगति में',
                'resolved' => 'हल हो गया',
                'closed' => 'बंद',
            ];

            // Parse images
            $images = [];
            if ($feedback->images) {
                $imagePaths = json_decode($feedback->images, true);
                if (is_array($imagePaths)) {
                    foreach ($imagePaths as $path) {
                        if (config('app.env') === 'production') {
                            $images[] = config('custom.file_point') . $path;
                        } else {
                            $images[] = asset($path);
                        }
                    }
                }
            }

            $response = [
                'success' => true,
                'data' => [
                    'id' => $feedback->id,
                    'admin_name' => htmlspecialchars($feedback->admin_name ?? '-', ENT_QUOTES, 'UTF-8'),
                    'designation' => htmlspecialchars($feedback->designation ?? '-', ENT_QUOTES, 'UTF-8'),
                    'feedback_type' => $feedbackTypes[$feedback->feedback_type] ?? $feedback->feedback_type,
                    'feedback_type_raw' => $feedback->feedback_type,
                    'status' => $statuses[$feedback->status] ?? $feedback->status,
                    'status_raw' => $feedback->status,
                    'subject' => htmlspecialchars($feedback->subject ?? '-', ENT_QUOTES, 'UTF-8'),
                    'feedback_message' => htmlspecialchars($feedback->feedback_message ?? '-', ENT_QUOTES, 'UTF-8'),
                    'contact_email' => htmlspecialchars($feedback->contact_email ?? '-', ENT_QUOTES, 'UTF-8'),
                    'images' => $images,
                    'admin_response' => htmlspecialchars($feedback->admin_response ?? '', ENT_QUOTES, 'UTF-8'),
                    'responded_at' => $feedback->responded_at ? date('d-m-Y H:i', strtotime($feedback->responded_at)) : null,
                    'created_at' => date('d-m-Y H:i', strtotime($feedback->created_at)),
                ]
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Feedback detail error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load feedback details'
            ], 500);
        }
    }

    /**
     * Submit admin reply to feedback
     */
    public function submitFeedbackReply(Request $request, $id)
    {
        try {
            // Validate input
            $request->validate([
                'admin_response' => 'required|string|max:1000',
                'status' => 'required|in:pending,in_progress,resolved,closed'
            ]);

            $role = Session::get('sess_role');
            $adminId = Session::get('uid');
            $districtId = Session::get('district_id', 0);

            // Get feedback with role-based filtering
            $query = DB::table('admin_feedbacks')->where('id', $id);

            // Role-based filtering
            if ($role === 'Admin' && $districtId > 0) {
                $query->where('district_id', $districtId);
            }

            $feedback = $query->first();

            if (!$feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feedback not found or access denied'
                ], 404);
            }

            // Update feedback with admin response
            $updated = DB::table('admin_feedbacks')
                ->where('id', $id)
                ->update([
                    'admin_response' => $request->admin_response,
                    'status' => $request->status,
                    'responded_at' => now(),
                    'updated_at' => now()
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'प्रतिक्रिया सफलतापूर्वक सबमिट की गई'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'प्रतिक्रिया सबमिट करने में त्रुटि'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'कृपया सभी आवश्यक फ़ील्ड भरें',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Feedback reply error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'प्रतिक्रिया सबमिट करने में त्रुटि हुई'
            ], 500);
        }
    }

    public function dava_apatti_list()
    {


        /* GET REQUEST (FORM LOAD)*/
        $claim_list = DB::select(
            "SELECT 
                        ob.claim_id,
                        app.apply_id,
                        TRIM(CONCAT_WS(' ', ud.First_Name, ud.Middle_Name, ud.Last_Name)) AS full_name,
                        p.title,
                        app.application_num,
                        ob.claim_status,
                        ob.request_type,
                        ob.request_category,
                        ob.created_at

                    FROM tbl_claim_objection ob
                    LEFT JOIN tbl_user_post_apply app 
                        ON ob.fk_apply_id = app.apply_id
                    LEFT JOIN record_user_detail_map ud 
                        ON ob.fk_applicant_id = ud.user_details_AI_ID
                    LEFT JOIN master_post p 
                        ON p.post_id = ob.fk_post_id 

                    WHERE ob.project_code = ?",
            [session('project_code')]
        );


        return view('admin.dava_apatti_list', compact('claim_list'));
    }
    public function view_dava_apatti($claim_id)
    {

        /* GET REQUEST (FORM LOAD)*/
        $claim_details = DB::selectOne(
            "SELECT 
        ob.claim_id,
        app.apply_id,
        app.fk_applicant_id,
        ob.admin_remark,
        ob.meeting_held,
        ob.meeting_date,
        ob.meeting_file,
        ob.request_type,
        ob.request_category,
        ob.description,
        ob.claim_status,
        ob.created_at,
        app.application_num,
        p.title,
        TRIM(CONCAT_WS(' ', ud.First_Name, ud.Middle_Name, ud.Last_Name)) AS full_name,
        ud.FatherName,
        ud.Contact_Number
    FROM tbl_claim_objection ob
    LEFT JOIN tbl_user_post_apply app ON ob.fk_apply_id = app.apply_id
    LEFT JOIN record_user_detail_map ud ON ob.fk_applicant_id = ud.user_details_AI_ID
    LEFT JOIN master_post p ON p.post_id = ob.fk_post_id 
    WHERE ob.claim_id = ?",
            [$claim_id]
        );

        return view('admin.view_dava_apatti', compact('claim_details'));
    }

    public function updateClaimStatus(Request $request)
    {

        $request->validate([
            'claim_id' => 'required|integer',
            'status' => 'required|in:Approved,Rejected',
            'admin_remark' => 'required|string|max:500',
            'meeting_held' => 'required',

        ], [
            'admin_remark.required' => 'कारण / टिप्पणी अनिवार्य है।',
        ]);


        if ($request->meeting_file) {

            $uploadedFilePath = UtilController::upload_file(
                $request->meeting_file,
                'dava_apatti_meetings', // Upload path in S3/local
                'uploads', // Disk name
                ['pdf'], // Allowed extensions
                ['application/pdf']
            );

            if (!$uploadedFilePath) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'मीटिंग फ़ाइल अपलोड करने में त्रुटि।'
                ], 500);
            }
        }
        $updated = DB::table('tbl_claim_objection')
            ->where('claim_id', $request->claim_id)
            ->whereNotIn('claim_status', ['Approved', 'Rejected'])
            ->update([
                'claim_status' => $request->status,
                'admin_remark' => $request->admin_remark,
                'meeting_held' => $request->meeting_held ?? null,
                'meeting_date' => $request->meeting_date ?? null,
                'meeting_file' => $uploadedFilePath ?? $request->meeting_file ?? null,
                'updated_by' => session('uid'),
                'updated_at' => now(),
                'updated_ip' => $request->ip(),
            ]);

        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'रिकॉर्ड अपडेट नहीं हो सका।'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'दावा–आपत्ति की स्थिति सफलतापूर्वक अपडेट की गई।'
        ]);
    }


    public function filterDavaApatti(Request $request)
    {
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;

        $query = "SELECT 
            ob.claim_id,
            TRIM(CONCAT_WS(' ', ud.First_Name, ud.Middle_Name, ud.Last_Name)) AS full_name,
            p.title,
            app.application_num,
            ob.claim_status,
            ob.request_type,
            ob.request_category,
            DATE_FORMAT(ob.created_at, '%d-%m-%Y') AS created_date
        FROM tbl_claim_objection ob
        LEFT JOIN tbl_user_post_apply app 
            ON ob.fk_apply_id = app.apply_id
        LEFT JOIN record_user_detail_map ud 
            ON ob.fk_applicant_id = ud.user_details_AI_ID
        LEFT JOIN master_post p 
            ON p.post_id = ob.fk_post_id 
        WHERE ob.project_code = ?
    ";

        $bindings = [session('project_code')];

        /* ===============================
       DATE FILTER CONDITION HANDLING
       =============================== */

        if (!empty($fromDate) && !empty($toDate)) {

            // BOTH dates present → use BETWEEN
            $query .= " AND DATE(ob.created_at) BETWEEN ? AND ?";
            $bindings[] = $fromDate;
            $bindings[] = $toDate;
        } elseif (!empty($fromDate)) {

            // Only FROM date
            $query .= " AND DATE(ob.created_at) >= ?";
            $bindings[] = $fromDate;
        } elseif (!empty($toDate)) {

            // Only TO date
            $query .= " AND DATE(ob.created_at) <= ?";
            $bindings[] = $toDate;
        }

        $records = DB::select($query, $bindings);

        /* ===============================
       SAFE RESPONSE EVEN IF NO DATA
       =============================== */

        $data = collect($records)->map(function ($row) {

            $statusMap = [
                'Submitted' => ['bg-warning', 'प्रतीक्षारत'],
                'InReview' => ['bg-info', 'समीक्षा में'],
                'Resolved' => ['bg-primary', 'हल किया'],
                'Approved' => ['bg-success', 'पात्र '],
                'Rejected' => ['bg-danger', 'अपात्र '],
            ];

            $badge = $statusMap[$row->claim_status] ?? ['bg-secondary', 'अज्ञात स्थिति'];

            return [
                'claim_id' => $row->claim_id,
                'full_name' => $row->full_name ?? '-',
                'title' => $row->title ?? '-',
                'application_num' => $row->application_num ?? '-',
                'created_date' => $row->created_date,
                'request_type' => $row->request_type === 'claim'
                    ? 'दावा (Claim)'
                    : 'आपत्ति (Objection)',
                'request_category' => ucfirst($row->request_category),
                'badge_class' => $badge[0],
                'status_hindi' => $badge[1],
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    public function CDPO_list()
    {
        $cdpo_list = DB::select("SELECT m.ID row_id ,d.district_name_eng District, p.project Project, m.Full_Name, m.Mobile_Number 
        FROM master_user m 
        INNER JOIN master_district d ON m.admin_district_id = d.District_Code_LGD
        INNER JOIN master_projects p ON m.project_id = p.project_code 
        WHERE m.Role='Super_admin' AND m.admin_district_id=? ORDER BY d.district_name_eng", [session('district_id')]);

        return view('admin.cdpo_list', compact('cdpo_list'));
    }

    public function anantim_list()
    {
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        // Fetch advertisements for the district with date
        $advertisements = DB::table('master_advertisement')
            ->select('Advertisement_ID', 'Advertisement_Title', 'Advertisement_Date')
            ->where('district_lgd_code', $district_id)
            ->where('project_code', $project_code)
            ->orderBy('Advertisement_Date', 'desc')
            ->orderBy('Advertisement_Title', 'asc')
            ->get();

        $anantim_list = DB::table('tbl_anantim_list as l')
            ->leftJoin('master_advertisement as a', 'a.Advertisement_ID', '=', 'l.fk_advertiesment_id')
            ->leftJoin('master_post as post', 'l.fk_post_id', '=', 'post.post_id')
            ->leftJoin('master_projects as proj', 'l.project_code', '=', 'proj.project_code')
            ->leftJoin('master_nnn as nnn', 'post.std_nnn_code', '=', 'nnn.std_nnn_code')
            ->leftJoin('master_panchayats as gp', 'post.gp_nnn_code', '=', 'gp.panchayat_lgd_code')
            ->select('l.anantim_id', 'l.fk_post_id', 'l.project_code', 'a.Advertisement_Title', 'post.title as postname', 'proj.project', 'nnn.nnn_name', 'gp.panchayat_name_hin', 'l.claim_start_date', 'l.claim_end_date', 'l.anantim_list_file')
            ->where('l.district_lgd_code', $district_id)
            ->where('l.project_code', $project_code)
            ->distinct()
            ->orderBy('l.created_at', 'desc')
            ->get();
        // dd($anantim_list, $district_id, $project_code);
        return view('report.anantim_list', compact('advertisements', 'anantim_list'));
    }
    public function antim_list()
    {
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        // Fetch advertisements for the district with date
        $advertisements = DB::table('master_advertisement')
            ->select('Advertisement_ID', 'Advertisement_Title', 'Advertisement_Date')
            ->where('district_lgd_code', $district_id)
            ->where('project_code', $project_code)
            ->orderBy('Advertisement_Date', 'desc')
            ->orderBy('Advertisement_Title', 'asc')
            ->get();

        $antim_list = DB::table('tbl_antim_list as l')
            ->leftJoin('master_advertisement as a', 'a.Advertisement_ID', '=', 'l.fk_advertiesment_id')
            ->leftJoin('master_post as post', 'l.fk_post_id', '=', 'post.post_id')
            ->leftJoin('master_projects as proj', 'l.project_code', '=', 'proj.project_code')
            ->leftJoin('master_nnn as nnn', 'post.std_nnn_code', '=', 'nnn.std_nnn_code')
            ->leftJoin('master_panchayats as gp', 'post.gp_nnn_code', '=', 'gp.panchayat_lgd_code')
            ->select('l.antim_id', 'l.fk_post_id', 'l.project_code', 'a.Advertisement_Title', 'post.title as postname', 'proj.project', 'nnn.nnn_name', 'gp.panchayat_name_hin', 'l.antim_list_file')
            ->where('l.district_lgd_code', $district_id)
            ->where('l.project_code', $project_code)
            ->distinct()
            ->orderBy('l.created_at', 'desc')
            ->get();

        return view('report.antim_list', compact('advertisements', 'antim_list'));
    }

    public function uploadAnantimList(Request $request)
    {
        // ================= VALIDATION =================
        $validator = Validator::make($request->all(), [
            'advertisement_id' => 'required|integer',
            'post_id' => 'required|integer',
            'claim_start_date' => 'required|date',
            'claim_end_date' => 'required|date|after_or_equal:claim_start_date',
            'anantim_file' => 'required|file|mimes:pdf|max:2048', // 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();

        try {

            // ================= FILE UPLOAD =================


            $allowedExtensions = ['pdf'];
            $allowedMimeTypes = ['application/pdf'];

            // Handle self-attested file upload
            if ($request->hasFile('anantim_file')) {
                $uploadedFile = $request->file('anantim_file');
                $uploadPath = "uploads/anantim_list/"; // Path relative to uploads root

                // Use UtilController to upload the file
                $uploadResult = UtilController::upload_file(
                    $uploadedFile,
                    'anantim_file',
                    'uploads',
                    $allowedExtensions,
                    $allowedMimeTypes
                );

                if (!$uploadResult) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to Anantim file!',
                    ], 422);
                }

                $isAnantimUploaded = DB::table('tbl_anantim_list')->where('fk_post_id', $request->post_id)->first();
                if ($isAnantimUploaded) {
                    return response()->json([
                        'success' => false,
                        'message' => 'इस पद के लिए अनंतिम सूची पहले ही अपलोड की जा चुकी है।',
                    ], 422);
                }
                $masterPost = DB::table('master_post')->where('post_id', $request->post_id)->first();
                if (!$masterPost) {
                    return response()->json([
                        'success' => false,
                        'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                    ], 422);
                }

                // ================= INSERT DATA =================
                DB::table('tbl_anantim_list')->insert([
                    'fk_advertiesment_id' => $request->advertisement_id,
                    'fk_post_id' => $request->post_id,
                    'claim_start_date' => $request->claim_start_date,
                    'claim_end_date' => $request->claim_end_date,
                    'anantim_list_file' => $uploadResult,
                    'uploaded_by' => session('uid'), // logged-in user
                    'district_lgd_code' => session('district_id'), // if stored in session
                    'project_code' => $masterPost->project_code,
                    'std_nnn_code' => $masterPost->std_nnn_code,
                    'gp_nnn_code' => $masterPost->gp_nnn_code,
                    'village_code' => $masterPost->village_code,
                    'ward_no' => $masterPost->ward_no,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'create_ip' => $request->ip(),
                    'update_ip' => $request->ip(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'अनंतिम सूची सफलतापूर्वक अपलोड कर दी गई है।'
                ]);
            }
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'अपलोड करते समय त्रुटि आई। कृपया पुनः प्रयास करें।'
            ], 500);
        }
    }
    public function uploadAntimList(Request $request)
    {
        // ================= VALIDATION =================
        $validator = Validator::make($request->all(), [
            'advertisement_id' => 'required|integer',
            'post_id' => 'required|integer',
            'antim_file' => 'required|file|mimes:pdf|max:2048', // 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();

        try {

            // ================= FILE UPLOAD =================


            $allowedExtensions = ['pdf'];
            $allowedMimeTypes = ['application/pdf'];

            // Handle self-attested file upload
            if ($request->hasFile('antim_file')) {
                $uploadedFile = $request->file('antim_file');
                $uploadPath = "uploads/antim_list/"; // Path relative to uploads root

                // Use UtilController to upload the file
                $uploadResult = UtilController::upload_file(
                    $uploadedFile,
                    'antim_file',
                    'uploads',
                    $allowedExtensions,
                    $allowedMimeTypes
                );

                if (!$uploadResult) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to Anantim file!',
                    ], 422);
                }

                $isAnantimUploaded = DB::table('tbl_antim_list')->where('fk_post_id', $request->post_id)->first();
                if ($isAnantimUploaded) {
                    return response()->json([
                        'success' => false,
                        'message' => 'इस पद के लिए अंतिम सूची पहले ही अपलोड की जा चुकी है।',
                    ], 422);
                }
                $masterPost = DB::table('master_post')->where('post_id', $request->post_id)->first();
                if (!$masterPost) {
                    return response()->json([
                        'success' => false,
                        'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                    ], 422);
                }

                // ================= INSERT DATA =================
                DB::table('tbl_antim_list')->insert([
                    'fk_advertiesment_id' => $request->advertisement_id,
                    'fk_post_id' => $request->post_id,
                    'antim_list_file' => $uploadResult,
                    'uploaded_by' => session('uid'), // logged-in user
                    'district_lgd_code' => session('district_id'), // if stored in session
                    'project_code' => $masterPost->project_code,
                    'std_nnn_code' => $masterPost->std_nnn_code,
                    'gp_nnn_code' => $masterPost->gp_nnn_code,
                    'village_code' => $masterPost->village_code,
                    'ward_no' => $masterPost->ward_no,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'create_ip' => $request->ip(),
                    'update_ip' => $request->ip(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'अंतिम सूची सफलतापूर्वक अपलोड कर दी गई है।'
                ]);
            }
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'अपलोड करते समय त्रुटि आई। कृपया पुनः प्रयास करें।'
            ], 500);
        }
    }
    public function getPostsByAdvertisement(Request $request)
    {
        $advertisementId = $request->input('advertisement_id');
        $isUpload = $request->input('is_upload', 0);

        if (!$advertisementId) {
            return response()->json([
                'success' => false,
                'message' => 'Advertisement ID is required'
            ]);
        }

        // Build the base query
        $baseQuery = "SELECT 
                    mp.post_id, 
                    mp.title, 
                    proj.project,
                    
                    -- Handle multiple std_nnn_code (JSON array) for nnn names
                    (
                        SELECT GROUP_CONCAT(DISTINCT nnn2.nnn_name ORDER BY nnn2.nnn_name SEPARATOR ', ')
                        FROM JSON_TABLE(
                            mp.std_nnn_code,
                            '$[*]' COLUMNS (
                                std_code VARCHAR(20) PATH '$'
                            )
                        ) AS std_codes
                        LEFT JOIN master_nnn nnn2 ON std_codes.std_code = nnn2.std_nnn_code
                        WHERE std_codes.std_code IS NOT NULL
                    ) AS nnn_names,
                    
                    -- Handle multiple gp_nnn_code (JSON array) for panchayat names
                    (
                        SELECT GROUP_CONCAT(DISTINCT gp2.panchayat_name_hin ORDER BY gp2.panchayat_name_hin SEPARATOR ', ')
                        FROM JSON_TABLE(
                            mp.gp_nnn_code,
                            '$[*]' COLUMNS (
                                gp_code VARCHAR(20) PATH '$'
                            )
                        ) AS gp_codes
                        LEFT JOIN master_panchayats gp2 ON gp_codes.gp_code = gp2.panchayat_lgd_code
                        WHERE gp_codes.gp_code IS NOT NULL
                    ) AS panchayat_names

                FROM master_post mp
                LEFT JOIN master_projects proj ON mp.project_code = proj.project_code
                WHERE mp.Advertisement_ID = ? ";

        // Add conditional clause only when $isUpload = 1
        if ($isUpload == 2) {
            $query = $baseQuery . " AND mp.post_id NOT IN (
                                    SELECT fk_post_id 
                                    FROM tbl_anantim_list 
                                    WHERE fk_advertiesment_id = ?
                                )";
            $params = [$advertisementId, $advertisementId];
        } else {
            $query = $baseQuery;
            $params = [$advertisementId];
        }

        // Add ORDER BY clause
        $query .= " ORDER BY mp.title ASC";

        // Execute the query
        $posts = DB::select($query, $params);

        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }


    public function getPostsByAdvertisementTop10(Request $request)
    {
        $advertisementId = $request->input('advertisement_id');

        if (!$advertisementId) {
            return response()->json([
                'success' => false,
                'message' => 'Advertisement ID is required'
            ]);
        }

        // Fetch posts for the selected advertisement with Quali_ID
        $posts = DB::table('master_post')
            ->where('Advertisement_ID', $advertisementId)
            ->select('post_id', 'title', 'Quali_ID')
            ->orderBy('title', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }


    function ProcessData($query)
    {
        return collect($query)->map(function ($item) {
            // Split organization names and add numbers with proper line breaks
            if ($item->organisation_name) {
                $orgs = explode(', ', $item->organisation_name);
                $numberedOrgs = [];
                foreach ($orgs as $index => $org) {
                    $numberedOrgs[] = ($index + 1) . '. ' . trim($org);
                }
                $item->organisation_name = implode(PHP_EOL, $numberedOrgs); // Use PHP_EOL for proper line breaks
            }

            // Same for designations
            if ($item->designation) {
                $designations = explode(', ', $item->designation);
                $numberedDesignations = [];
                foreach ($designations as $index => $designation) {
                    $numberedDesignations[] = ($index + 1) . '. ' . trim($designation);
                }
                $item->designation = implode(PHP_EOL, $numberedDesignations); // Use PHP_EOL for proper line breaks
            }

            // Handle fromToDate for better formatting
            if ($item->fromToDate) {
                $dates = explode(', ', $item->fromToDate);
                $numberedDates = [];
                foreach ($dates as $index => $date) {
                    $numberedDates[] = ($index + 1) . '. ' . trim($date);
                }
                $item->fromToDate = implode(PHP_EOL, $numberedDates);
            }

            // Format skills array if it's JSON
            if (!empty($item->skill_answers)) {
                $decoded = json_decode($item->skill_answers, true);
                // dd($decoded);
                if (is_array($decoded)) {
                    // अगर ये JSON array है तो numbering करके string बना दो
                    $numberedSkills = [];
                    foreach ($decoded as $index => $skill) {
                        $skill = trim($skill);
                        if ($skill !== '') {
                            $numberedSkills[] = ($index + 1) . '. ' . $skill;
                        }
                    }
                    $item->skill_answers = implode(PHP_EOL, $numberedSkills);
                }
            }


            return $item;
        });
    }


    public function downloadPostWiseReport(Request $request)
    {
        $advertisementId = $request->input('advertisement_id');
        $postId = $request->input('post_id');

        if (!$advertisementId || !$postId) {
            return back()->with('error', 'कृपया विज्ञापन और पद दोनों चुनें।');
        }

        // Fetch post and advertisement details
        $postDetails = DB::table('master_post as pm')
            ->join('master_advertisement as am', 'pm.Advertisement_ID', '=', 'am.Advertisement_ID')
            ->where('pm.post_id', $postId)
            ->where('am.Advertisement_ID', $advertisementId)
            ->select('pm.title', 'am.Advertisement_Title', 'pm.post_id', 'pm.Quali_ID')
            ->first();

        if (!$postDetails) {
            return back()->with('error', 'पद या विज्ञापन नहीं मिला।');
        }

        // SQL Query for verified/rejected application
        $verifiedRejectedSql = "SELECT
                    COALESCE(gp.panchayat_lgd_code, nnn.std_nnn_code) AS panchayat_nagar_no,
                    upa.application_num AS panjiyan_no,
                    COALESCE(gp.panchayat_name_hin, nnn.nnn_name) AS `nagar_panchayat_name`,
                    COALESCE(
                        CONCAT(ward.ward_name, ' (वार्ड नं. ', ward.ward_no, ')'),
                        vill.village_name_hin
                    ) AS `kendra_name`,
                    pm.title AS postname,
                      pm.Quali_ID,
                    COALESCE(
                        (
                            SELECT COUNT(*)
                            FROM tbl_user_post_apply upa2
                            WHERE upa2.fk_applicant_id = rud.user_details_AI_ID
                            AND upa2.fk_post_id = upa.fk_post_id
                        ),
                        0
                    ) AS total_application,
                    CONCAT_WS(' ', rud.firstName_hindi, rud.middleName_hindi, rud.lastName_hindi) AS Full_Name_hindi,
                    rud.FatherName,
                    CONCAT_WS(' , ', rud.Corr_Address, dmC.name, rud.Corr_pincode) AS niwas,
                    rud.Caste,

                    /* Age Calculation */
                    CONCAT(
                        TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01'), ' वर्ष  ',
                        TIMESTAMPDIFF(MONTH, rud.DOB, '2025-01-01') - TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01') * 12, ' माह  ',
                        DATEDIFF(
                            '2025-01-01',
                            DATE_ADD(
                                DATE_ADD(rud.DOB, INTERVAL TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01') YEAR),
                                INTERVAL (TIMESTAMPDIFF(MONTH, rud.DOB, '2025-01-01') - TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01') * 12) MONTH
                            )
                        ),
                        ' दिन '
                    ) AS Age,

                    /* Qualification */
                    qm.Quali_Name AS qualification,
                    raep.total_marks,
                    raep.obtained_marks,
                    raep.percentage,
                    raep.percentage * 0.6 AS sixty_percent_marks,

                    /* Vivdhwa/Talakshuda - vivran */
                    COALESCE(
                        CONCAT(
                            (
                                SELECT upqa2.answer
                                FROM tbl_user_post_question_answer upqa2
                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                AND upqa2.post_id = upa.fk_post_id
                                AND upqa2.fk_question_id = 1
                                AND upqa2.answer IN ('विधवा', 'परित्यक्ता', 'तलाकशुदा')
                                LIMIT 1
                            )
                           
                        ),
                        ''
                    ) AS vivdhwa_talakshuda_detail,


                    /* Vivdhwa/Talakshuda marks */
                   CASE
                        /* If marks are confirmed, use the pre-calculated marks */
                         WHEN upa.is_marks_confirmed = 1 THEN upa.v_p_t_questionMarks
                        
                        /* Otherwise calculate based on conditions */
                        ELSE
                            CASE
                                /* विधवा – 15 marks */
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer q1
                                    WHERE q1.applicant_id = rud.user_details_AI_ID
                                    AND q1.post_id = upa.fk_post_id
                                    AND q1.fk_question_id = 1
                                    AND q1.answer = 'विधवा'
                                ) THEN 15

                                /* परित्यक्ता / तलाकशुदा – only if 2+ years completed */
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer q1
                                    JOIN tbl_user_post_question_answer q2 
                                        ON q1.applicant_id = q2.applicant_id
                                        AND q1.post_id = q2.post_id
                                    WHERE q1.applicant_id = rud.user_details_AI_ID
                                    AND q1.post_id = upa.fk_post_id
                                    AND q1.fk_question_id = 1
                                    AND q1.answer IN ('परित्यक्ता','तलाकशुदा')
                                    AND q2.fk_question_id IN (16,17)
                                    AND q2.answer IS NOT NULL
                                    AND STR_TO_DATE(q2.answer, '%Y-%m-%d') IS NOT NULL
                                    AND TIMESTAMPDIFF(
                                            YEAR,
                                            STR_TO_DATE(q2.answer, '%Y-%m-%d'),
                                            CURDATE()
                                        ) >= 2
                                ) THEN 15

                                ELSE 0
                            END
                    END AS vidhwa_talakshuda_marks,

                    /* Caste */
                    CASE
                        WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN rud.Caste
                        ELSE NULL
                    END AS caste_answer,

                    /* Caste marks -10 marks for SC/ST */
                    CASE
                        WHEN upa.is_marks_confirmed = 1 THEN upa.domicile_mark
                        ELSE
                            CASE
                                WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN 10
                                ELSE 0
                            END
                    END AS caste_marks,

                    /* BPL detail */
                        CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM tbl_user_post_question_answer upqa2
                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                AND upqa2.post_id = upa.fk_post_id
                                AND upqa2.fk_question_id = 3
                                AND upqa2.answer = 'हाँ'
                            ) THEN 'हाँ'
                            ELSE NULL
                    END AS bpl_answer, 

                    /* BPL marks - 6 marks for BPL */
                   CASE
                        WHEN upa.is_marks_confirmed = 1 THEN upa.secc_questionMarks  /* Adjust this field name based on your actual column */
                        ELSE
                            CASE
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer upqa2
                                    WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                    AND upqa2.post_id = upa.fk_post_id
                                    AND upqa2.fk_question_id = 3
                                    AND upqa2.answer = 'हाँ'
                                ) THEN 6
                                ELSE 0
                            END
                    END AS bpl_marks,

                    /* Karyakarta/Sahayika details */
                    COALESCE(
                        (
                            SELECT CONCAT(
                                DATE_FORMAT(MIN(upqa2.date_From), '%d-%m-%Y'),
                                ' से ',
                                DATE_FORMAT(MAX(upqa2.date_To), '%d-%m-%Y')
                            )
                            FROM tbl_user_post_question_answer upqa2
                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                            AND upqa2.post_id = upa.fk_post_id
                            AND upqa2.fk_question_id = 9
                            AND upqa2.answer = 'हाँ'
                            AND upqa2.date_From IS NOT NULL
                            AND upqa2.date_To IS NOT NULL
                        ),
                        ''
                    ) AS karyakarta_sahayika_detail,

                    /* Karyakarta/Sahayika marks - 6 marks above 1Y*/
                     CASE
                        WHEN upa.is_marks_confirmed = 1 THEN upa.min_experiance_mark  /* Adjust this field name based on your actual column */
                        ELSE
                            CASE
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer upqa2
                                    WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                    AND upqa2.post_id = upa.fk_post_id
                                    AND upqa2.fk_question_id = 9
                                    AND upqa2.answer = 'हाँ' 
                                    AND DATEDIFF(upqa2.date_To, upqa2.date_From) >= 365
                                ) THEN 6 
                                ELSE 0
                            END
                     END AS karyakarta_sahayika_marks,

                    /* Experience Details */
                    -- COALESCE(raem.organisation_name, '') AS experience_details,
                    COALESCE(
                        (
                            SELECT GROUP_CONCAT(
                                CONCAT(row_num, '. ', Organization_Name )
                                ORDER BY row_num
                                SEPARATOR '\n  '
                            )
                            FROM (
                                SELECT 
                                    Organization_Name,
                                    ROW_NUMBER() OVER (ORDER BY date_from) AS row_num
                                FROM record_applicant_experience_map raem2
                                WHERE raem2.fk_apply_id = rud.fk_apply_id
                            ) AS numbered_exp
                        ),
                        ''
                    ) AS experience_details,

                    /* Experience Marks - Based on years of experience  */
                    COALESCE(raem.total_experience_marks, 0) AS experience_marks,

                   /* Total Counted Marks Calculation */
                        CASE
                            /* If marks are confirmed, use pre-calculated total marks */
                            WHEN upa.is_marks_confirmed = 1 THEN 
                                COALESCE(raep.percentage * 0.6) + /* Adjust this field name based on your actual column */
                                COALESCE(upa.v_p_t_questionMarks, 0) + /* Adjust this field name based on your actual column */
                                COALESCE(upa.domicile_mark, 0) + 
                                COALESCE(upa.secc_questionMarks, 0) + 
                                COALESCE(upa.min_experiance_mark, 0) + 
                                COALESCE(raem.total_experience_marks, 0)
                            
                            /* Otherwise calculate dynamically */
                            ELSE
                                (
                                    (raep.percentage * 0.6) +  /* 60% of percentage */
                                    -- COALESCE(raem.total_experience_marks, 0) +  /* Experience marks (commented out) */
                                    
                                    /* Caste marks - 10 marks for SC/ST */
                                    CASE
                                        WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN 10
                                        ELSE 0
                                    END +
                                    
                                    /* BPL marks - 6 marks for BPL */
                                    CASE
                                        WHEN EXISTS (
                                            SELECT 1
                                            FROM tbl_user_post_question_answer upqa2
                                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                            AND upqa2.post_id = upa.fk_post_id
                                            AND upqa2.fk_question_id = 3
                                            AND upqa2.answer = 'हाँ'
                                        ) THEN 6
                                        ELSE 0
                                    END +
                                    
                                    /* Karyakarta/Sahayika marks - 6 marks for eligible */
                                    CASE
                                        WHEN EXISTS (
                                            SELECT 1
                                            FROM tbl_user_post_question_answer upqa2
                                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                            AND upqa2.post_id = upa.fk_post_id
                                            AND upqa2.fk_question_id = 9
                                            AND upqa2.answer = 'हाँ'
                                            AND DATEDIFF(upqa2.date_To, upqa2.date_From) >= 365
                                        ) THEN 6
                                        ELSE 0
                                    END +
                                    
                                    /* Vidhwa/Talakshuda marks - 15 marks for eligible */
                                    CASE
                                        /* विधवा – 15 marks */
                                        WHEN EXISTS (
                                            SELECT 1
                                            FROM tbl_user_post_question_answer q1
                                            WHERE q1.applicant_id = rud.user_details_AI_ID
                                            AND q1.post_id = upa.fk_post_id
                                            AND q1.fk_question_id = 1
                                            AND q1.answer = 'विधवा'
                                        ) THEN 15

                                        /* परित्यक्ता / तलाकशुदा – only if 2+ years completed */
                                        WHEN EXISTS (
                                            SELECT 1
                                            FROM tbl_user_post_question_answer q1
                                            JOIN tbl_user_post_question_answer q2 
                                                ON q1.applicant_id = q2.applicant_id
                                                AND q1.post_id = q2.post_id
                                            WHERE q1.applicant_id = rud.user_details_AI_ID
                                            AND q1.post_id = upa.fk_post_id
                                            AND q1.fk_question_id = 1
                                            AND q1.answer IN ('परित्यक्ता','तलाकशुदा')
                                            AND q2.fk_question_id IN (16,17)
                                            AND q2.answer IS NOT NULL
                                            AND STR_TO_DATE(q2.answer, '%Y-%m-%d') IS NOT NULL
                                            AND TIMESTAMPDIFF(
                                                    YEAR,
                                                    STR_TO_DATE(q2.answer, '%Y-%m-%d'),
                                                    CURDATE()
                                                ) >= 2
                                        ) THEN 15
                                        ELSE 0
                                    END
                                )
                        END AS total_counted_marks,

                    /* Ranking */
                    ROW_NUMBER() OVER (
                        PARTITION BY upa.fk_post_id 
                        ORDER BY 
                            CASE
                                /* If marks are confirmed, sum all pre-calculated marks */
                                WHEN upa.is_marks_confirmed = 1 THEN  
                                COALESCE(raep.percentage * 0.6) +
                                COALESCE(upa.v_p_t_questionMarks, 0) + /* Adjust this field name based on your actual column */
                                COALESCE(upa.domicile_mark, 0) + 
                                COALESCE(upa.secc_questionMarks, 0) + 
                                COALESCE(upa.min_experiance_mark, 0) + 
                                COALESCE(raem.total_experience_marks, 0)
                                
                                /* Otherwise calculate dynamically */
                                ELSE
                                    (
                                        (raep.percentage * 0.6) +
                                        -- COALESCE(raem.total_experience_marks, 0) +
                                        
                                        /* Caste marks */
                                        CASE
                                            WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN 10
                                            ELSE 0
                                        END +
                                        
                                        /* BPL marks */
                                        CASE
                                            WHEN EXISTS (
                                                SELECT 1
                                                FROM tbl_user_post_question_answer upqa2
                                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                                AND upqa2.post_id = upa.fk_post_id
                                                AND upqa2.fk_question_id = 3
                                                AND upqa2.answer = 'हाँ'
                                            ) THEN 6
                                            ELSE 0
                                        END +
                                        
                                        /* Karyakarta/Sahayika marks */
                                        CASE
                                            WHEN EXISTS (
                                                SELECT 1
                                                FROM tbl_user_post_question_answer upqa2
                                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                                AND upqa2.post_id = upa.fk_post_id
                                                AND upqa2.fk_question_id = 9
                                                AND upqa2.answer = 'हाँ'
                                                AND DATEDIFF(upqa2.date_To, upqa2.date_From) >= 365
                                            ) THEN 6 
                                            ELSE 0
                                        END +
                                        
                                        /* Vidhwa/Talakshuda marks */
                                        CASE
                                            /* विधवा – 15 marks */
                                            WHEN EXISTS (
                                                SELECT 1
                                                FROM tbl_user_post_question_answer q1
                                                WHERE q1.applicant_id = rud.user_details_AI_ID
                                                AND q1.post_id = upa.fk_post_id
                                                AND q1.fk_question_id = 1
                                                AND q1.answer = 'विधवा'
                                            ) THEN 15

                                            /* परित्यक्ता / तलाकशुदा – only if 2+ years completed */
                                            WHEN EXISTS (
                                                SELECT 1
                                                FROM tbl_user_post_question_answer q1
                                                JOIN tbl_user_post_question_answer q2 
                                                    ON q1.applicant_id = q2.applicant_id
                                                    AND q1.post_id = q2.post_id
                                                WHERE q1.applicant_id = rud.user_details_AI_ID
                                                AND q1.post_id = upa.fk_post_id
                                                AND q1.fk_question_id = 1
                                                AND q1.answer IN ('परित्यक्ता','तलाकशुदा')
                                                AND q2.fk_question_id IN (16,17)
                                                AND q2.answer IS NOT NULL
                                                AND STR_TO_DATE(q2.answer, '%Y-%m-%d') IS NOT NULL
                                                AND TIMESTAMPDIFF(
                                                        YEAR,
                                                        STR_TO_DATE(q2.answer, '%Y-%m-%d'),
                                                        CURDATE()
                                                    ) >= 2
                                            ) THEN 15

                                            ELSE 0
                                        END
                                    )
                            END DESC
                    ) AS yogyata_kram,

                    NULL AS qualified_reason

                    FROM record_user_detail_map rud
                    JOIN (
                        SELECT *
                        FROM tbl_user_post_apply up
                        WHERE up.fk_post_id = ? AND up.status IN ('verified')   
                        GROUP BY up.apply_id
                        ) upa ON rud.fk_apply_id = upa.apply_id
                    LEFT JOIN tbl_user_post_question_answer upqa ON rud.user_details_AI_ID = upqa.applicant_id
                    LEFT JOIN post_question_map pqm ON upqa.post_map_id = pqm.post_map_id AND pqm.fk_post_id = ?
                    LEFT JOIN master_tbl_gender g ON rud.Gender = g.gender_id
                    LEFT JOIN (
                        SELECT 
                            raep.fk_applicant_id,
                            raep.fk_Quali_ID,
                            raep.total_marks,
                            raep.obtained_marks,
                            raep.percentage,
                            raep.Created_at,
                            ROW_NUMBER() OVER (
                                PARTITION BY raep.fk_applicant_id 
                                ORDER BY 
                                    CASE WHEN raep.fk_Quali_ID = pm.Quali_ID THEN 0 ELSE 1 END,
                                    raep.Created_at DESC,
                                    raep.fk_Quali_ID DESC
                            ) as rn
                        FROM record_applicant_edu_map raep
                        CROSS JOIN master_post pm
                        WHERE pm.post_id = ? 
                    ) raep ON rud.user_details_AI_ID = raep.fk_applicant_id AND raep.rn = 1
                    LEFT JOIN master_qualification qm ON raep.fk_Quali_ID = qm.Quali_ID
                    LEFT JOIN master_district dmC ON rud.Corr_District_lgd = dmC.District_Code_LGD
                    LEFT JOIN master_panchayats gp ON gp.panchayat_lgd_code = upa.post_gp
                    LEFT JOIN master_villages vill ON vill.village_code = upa.post_village
                    LEFT JOIN master_nnn nnn ON nnn.std_nnn_code = upa.post_nagar
                    LEFT JOIN master_ward ward ON ward.ID = upa.post_ward
                    LEFT JOIN (
                            SELECT 
                                fk_apply_id,
                                GROUP_CONCAT(Organization_Name SEPARATOR ', ') AS organisation_name_concat,
                                GROUP_CONCAT(Designation SEPARATOR ', ') AS designation,
                                GROUP_CONCAT(
                                    CONCAT(
                                        DATE_FORMAT(Date_From, '%d-%m-%Y'), ' से ', DATE_FORMAT(Date_To, '%d-%m-%Y')
                                    )
                                    SEPARATOR ', '
                                ) AS fromToDate,
                                /* 1 year or more = 6 marks, else 0 */
                                CASE 
                                    WHEN SUM(DATEDIFF(Date_To, Date_From) + 1) >= 365 THEN 0
                                    ELSE 0
                                END AS total_experience_marks
                            FROM record_applicant_experience_map
                            GROUP BY fk_apply_id
                     ) raem ON rud.fk_apply_id = raem.fk_apply_id
                    LEFT JOIN post_organization_map pom ON upa.fk_post_id = pom.fk_post_id AND pom.fk_post_id = ?
                    LEFT JOIN master_post pm ON upa.fk_post_id = pm.post_id AND pm.post_id = ? 
                    GROUP BY rud.user_details_AI_ID
                    ORDER BY upa.apply_date ASC";

        // SQL Query for all application
        $sql = "SELECT
                    COALESCE(gp.panchayat_lgd_code, nnn.std_nnn_code) AS panchayat_nagar_no,
                    upa.application_num AS panjiyan_no,
                    COALESCE(gp.panchayat_name_hin, nnn.nnn_name) AS `nagar_panchayat_name`,
                    COALESCE(
                        CONCAT(ward.ward_name, ' (वार्ड नं. ', ward.ward_no, ')'),
                        vill.village_name_hin
                    ) AS `kendra_name`,
                    pm.title AS postname,
                      pm.Quali_ID,
                    COALESCE(
                        (
                            SELECT COUNT(*)
                            FROM tbl_user_post_apply upa2
                            WHERE upa2.fk_applicant_id = rud.user_details_AI_ID
                            AND upa2.fk_post_id = upa.fk_post_id
                        ),
                        0
                    ) AS total_application,
                    CONCAT_WS(' ', rud.firstName_hindi, rud.middleName_hindi, rud.lastName_hindi) AS Full_Name_hindi,
                    rud.FatherName,
                    CONCAT_WS(' , ', rud.Corr_Address, dmC.name, rud.Corr_pincode) AS niwas,
                    rud.Caste,

                    /* Age Calculation */
                    CONCAT(
                        TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01'), ' वर्ष  ',
                        TIMESTAMPDIFF(MONTH, rud.DOB, '2025-01-01') - TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01') * 12, ' माह  ',
                        DATEDIFF(
                            '2025-01-01',
                            DATE_ADD(
                                DATE_ADD(rud.DOB, INTERVAL TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01') YEAR),
                                INTERVAL (TIMESTAMPDIFF(MONTH, rud.DOB, '2025-01-01') - TIMESTAMPDIFF(YEAR, rud.DOB, '2025-01-01') * 12) MONTH
                            )
                        ),
                        ' दिन '
                    ) AS Age,

                    /* Qualification */
                    qm.Quali_Name AS qualification,
                    raep.total_marks,
                    raep.obtained_marks,
                    raep.percentage,
                    raep.percentage * 0.6 AS sixty_percent_marks,

                    /* Vivdhwa/Talakshuda - vivran */
                    COALESCE(
                        CONCAT(
                            (
                                SELECT upqa2.answer
                                FROM tbl_user_post_question_answer upqa2
                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                AND upqa2.post_id = upa.fk_post_id
                                AND upqa2.fk_question_id = 1
                                AND upqa2.answer IN ('विधवा', 'परित्यक्ता', 'तलाकशुदा')
                                LIMIT 1
                            )
                            -- ,
                            -- '-(',
                            -- (
                            --     SELECT DATE_FORMAT(upqa3.answer, '%d-%m-%Y')
                            --     FROM tbl_user_post_question_answer upqa3
                            --     WHERE upqa3.applicant_id = rud.user_details_AI_ID
                            --     AND upqa3.post_id = upa.fk_post_id
                            --     AND upqa3.fk_question_id IN (16,17)  /* तलाक / परित्यक्ता तिथि */
                            --     LIMIT 1
                            -- ),
                            -- ')'
                        ),
                        ''
                    ) AS vivdhwa_talakshuda_detail,


                    /* Vivdhwa/Talakshuda marks */
                    CASE
                        /* विधवा – 15 marks */
                        WHEN EXISTS (
                            SELECT 1
                            FROM tbl_user_post_question_answer q1
                            WHERE q1.applicant_id = rud.user_details_AI_ID
                            AND q1.post_id = upa.fk_post_id
                            AND q1.fk_question_id = 1
                            AND q1.answer = 'विधवा'
                        ) THEN 15

                        /* परित्यक्ता / तलाकशुदा – only if 2+ years completed */
                        WHEN EXISTS (
                            SELECT 1
                            FROM tbl_user_post_question_answer q1
                            JOIN tbl_user_post_question_answer q2 
                                ON q1.applicant_id = q2.applicant_id
                                AND q1.post_id = q2.post_id
                            WHERE q1.applicant_id = rud.user_details_AI_ID
                            AND q1.post_id = upa.fk_post_id
                            AND q1.fk_question_id = 1
                            AND q1.answer IN ('परित्यक्ता','तलाकशुदा')
                            AND q2.fk_question_id IN (16,17)
                            AND q2.answer IS NOT NULL
                            AND STR_TO_DATE(q2.answer, '%Y-%m-%d') IS NOT NULL
                            AND TIMESTAMPDIFF(
                                    YEAR,
                                    STR_TO_DATE(q2.answer, '%Y-%m-%d'),
                                    CURDATE()
                                ) >= 2
                        ) THEN 15
                        ELSE 0
                    END AS vidhwa_talakshuda_marks,

                    /* Caste */
                    CASE
                        WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN rud.Caste
                        ELSE NULL
                    END AS caste_answer,

                    /* Caste marks -10 marks for SC/ST */
                    CASE
                        WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN 10
                        ELSE 0
                    END AS caste_marks,

                    /* BPL detail */
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM tbl_user_post_question_answer upqa2
                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                            AND upqa2.post_id = upa.fk_post_id
                            AND upqa2.fk_question_id = 3
                            AND upqa2.answer = 'हाँ'
                        ) THEN 'हाँ'
                        ELSE NULL
                    END AS bpl_answer, 

                    /* BPL marks - 6 marks for BPL */
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM tbl_user_post_question_answer upqa2
                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                            AND upqa2.post_id = upa.fk_post_id
                            AND upqa2.fk_question_id = 3
                            AND upqa2.answer = 'हाँ'
                        ) THEN 6
                        ELSE 0
                    END AS bpl_marks,

                    /* Karyakarta/Sahayika details */
                    COALESCE(
                        (
                            SELECT CONCAT(
                                DATE_FORMAT(MIN(upqa2.date_From), '%d-%m-%Y'),
                                ' से ',
                                DATE_FORMAT(MAX(upqa2.date_To), '%d-%m-%Y')
                            )
                            FROM tbl_user_post_question_answer upqa2
                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                            AND upqa2.post_id = upa.fk_post_id
                            AND upqa2.fk_question_id = 9
                            AND upqa2.answer = 'हाँ'
                            AND upqa2.date_From IS NOT NULL
                            AND upqa2.date_To IS NOT NULL
                        ),
                        ''
                    ) AS karyakarta_sahayika_detail,

                    /* Karyakarta/Sahayika marks - 6 marks above 1Y*/
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM tbl_user_post_question_answer upqa2
                            WHERE upqa2.applicant_id = rud.user_details_AI_ID
                            AND upqa2.post_id = upa.fk_post_id
                            AND upqa2.fk_question_id = 9
                            AND upqa2.answer = 'हाँ' 
                            AND DATEDIFF(upqa2.date_To, upqa2.date_From) >= 365
                        ) THEN 6 
                        ELSE 0
                    END AS karyakarta_sahayika_marks,

                    /* Experience Details */
                    -- COALESCE(raem.organisation_name, '') AS experience_details,
                    COALESCE(
                        (
                            SELECT GROUP_CONCAT(
                                CONCAT(row_num, '. ', Organization_Name )
                                ORDER BY row_num
                                SEPARATOR '\n  '
                            )
                            FROM (
                                SELECT 
                                    Organization_Name,
                                    ROW_NUMBER() OVER (ORDER BY date_from) AS row_num
                                FROM record_applicant_experience_map raem2
                                WHERE raem2.fk_apply_id = rud.fk_apply_id
                            ) AS numbered_exp
                        ),
                        ''
                    ) AS experience_details,

                    /* Experience Marks - Based on years of experience  */
                    COALESCE(raem.total_experience_marks, 0) AS experience_marks,

                    /* Total Counted Marks Calculation */
                    (
                        (raep.percentage * 0.6) +  /* 60% of percentage */
                        -- COALESCE(raem.total_experience_marks, 0) +  /* Experience marks  */
                        
                        /* Caste marks - 10 marks for SC/ST */
                        CASE
                            WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN 10
                            ELSE 0
                        END +
                        
                        /* BPL marks - 6 marks for BPL */
                        CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM tbl_user_post_question_answer upqa2
                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                AND upqa2.post_id = upa.fk_post_id
                                AND upqa2.fk_question_id = 3
                                AND upqa2.answer = 'हाँ'
                            ) THEN 6
                            ELSE 0
                        END +
                        
                        /* Karyakarta/Sahayika marks - 6 marks for eligible */
                        CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM tbl_user_post_question_answer upqa2
                                WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                AND upqa2.post_id = upa.fk_post_id
                                AND upqa2.fk_question_id = 9
                                AND upqa2.answer = 'हाँ'
                                AND DATEDIFF(upqa2.date_To, upqa2.date_From) >= 365
                            ) THEN 6
                            ELSE 0
                        END +
                        
                        /* Vivdhwa/Talakshuda marks - 15 marks for eligible */
                       CASE
                            /* विधवा – 15 marks */
                            WHEN EXISTS (
                                SELECT 1
                                FROM tbl_user_post_question_answer q1
                                WHERE q1.applicant_id = rud.user_details_AI_ID
                                AND q1.post_id = upa.fk_post_id
                                AND q1.fk_question_id = 1
                                AND q1.answer = 'विधवा'
                            ) THEN 15

                            /* परित्यक्ता / तलाकशुदा – only if 2+ years completed */
                            WHEN EXISTS (
                                SELECT 1
                                FROM tbl_user_post_question_answer q1
                                JOIN tbl_user_post_question_answer q2 
                                    ON q1.applicant_id = q2.applicant_id
                                    AND q1.post_id = q2.post_id
                                WHERE q1.applicant_id = rud.user_details_AI_ID
                                AND q1.post_id = upa.fk_post_id
                                AND q1.fk_question_id = 1
                                AND q1.answer IN ('परित्यक्ता','तलाकशुदा')
                                AND q2.fk_question_id IN (16,17)
                                AND q2.answer IS NOT NULL
                                AND STR_TO_DATE(q2.answer, '%Y-%m-%d') IS NOT NULL
                                AND TIMESTAMPDIFF(
                                        YEAR,
                                        STR_TO_DATE(q2.answer, '%Y-%m-%d'),
                                        CURDATE()
                                    ) >= 2
                            ) THEN 15
                            ELSE 0
                        END 
                    ) AS total_counted_marks,

                    /* Ranking */
                    ROW_NUMBER() OVER (PARTITION BY upa.fk_post_id ORDER BY 
                        (
                            (raep.percentage * 0.6) +
                            -- COALESCE(raem.total_experience_marks, 0) +
                            
                            /* Caste marks */
                            CASE
                                WHEN rud.Caste IN ('अनुसूचित जनजाति', 'अनुसूचित जाति') THEN 10
                                ELSE 0
                            END +
                            
                            /* BPL marks */
                            CASE
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer upqa2
                                    WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                    AND upqa2.post_id = upa.fk_post_id
                                    AND upqa2.fk_question_id = 3
                                    AND upqa2.answer = 'हाँ'
                                ) THEN 6
                                ELSE 0
                            END +
                            
                            /* Karyakarta/Sahayika marks */
                            CASE
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer upqa2
                                    WHERE upqa2.applicant_id = rud.user_details_AI_ID
                                    AND upqa2.post_id = upa.fk_post_id
                                    AND upqa2.fk_question_id = 9
                                    AND upqa2.answer = 'हाँ'
                            AND DATEDIFF(upqa2.date_To, upqa2.date_From) >= 365
                        ) THEN 6 
                                ELSE 0
                            END +
                            
                            /* Vivdhwa/Talakshuda marks */
                            CASE
                                /* विधवा – 15 marks */
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer q1
                                    WHERE q1.applicant_id = rud.user_details_AI_ID
                                    AND q1.post_id = upa.fk_post_id
                                    AND q1.fk_question_id = 1
                                    AND q1.answer = 'विधवा'
                                ) THEN 15

                                /* परित्यक्ता / तलाकशुदा – only if 2+ years completed */
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM tbl_user_post_question_answer q1
                                    JOIN tbl_user_post_question_answer q2 
                                        ON q1.applicant_id = q2.applicant_id
                                        AND q1.post_id = q2.post_id
                                    WHERE q1.applicant_id = rud.user_details_AI_ID
                                    AND q1.post_id = upa.fk_post_id
                                    AND q1.fk_question_id = 1
                                    AND q1.answer IN ('परित्यक्ता','तलाकशुदा')
                                    AND q2.fk_question_id IN (16,17)
                                    AND q2.answer IS NOT NULL
                                    AND STR_TO_DATE(q2.answer, '%Y-%m-%d') IS NOT NULL
                                    AND TIMESTAMPDIFF(
                                            YEAR,
                                            STR_TO_DATE(q2.answer, '%Y-%m-%d'),
                                            CURDATE()
                                        ) >= 2
                                ) THEN 15

                                ELSE 0
                            END 
                        ) DESC
                    ) AS yogyata_kram,

                    NULL AS qualified_reason

                    FROM record_user_detail_map rud
                    JOIN (
                    SELECT *
                    FROM tbl_user_post_apply up
                    WHERE up.fk_post_id = ?
                    GROUP BY up.apply_id
                    ) upa ON rud.fk_apply_id = upa.apply_id
                    LEFT JOIN tbl_user_post_question_answer upqa ON rud.user_details_AI_ID = upqa.applicant_id
                    LEFT JOIN post_question_map pqm ON upqa.post_map_id = pqm.post_map_id AND pqm.fk_post_id = ?
                    LEFT JOIN master_tbl_gender g ON rud.Gender = g.gender_id
                  -- LEFT JOIN record_applicant_edu_map raep ON rud.user_details_AI_ID = raep.fk_applicant_id
                  --    LEFT JOIN (
                  --         SELECT 
                  --             raep.*,
                  --             ROW_NUMBER() OVER (PARTITION BY fk_applicant_id ORDER BY Created_at DESC, fk_Quali_ID DESC) as rn
                  --         FROM record_applicant_edu_map raep
                  --     ) raep ON rud.user_details_AI_ID = raep.fk_applicant_id AND raep.rn = 1
                    LEFT JOIN (
                        SELECT 
                            raep.fk_applicant_id,
                            raep.fk_Quali_ID,
                            raep.total_marks,
                            raep.obtained_marks,
                            raep.percentage,
                            raep.Created_at,
                            ROW_NUMBER() OVER (
                                PARTITION BY raep.fk_applicant_id 
                                ORDER BY 
                                    CASE WHEN raep.fk_Quali_ID = pm.Quali_ID THEN 0 ELSE 1 END,
                                    raep.Created_at DESC,
                                    raep.fk_Quali_ID DESC
                            ) as rn
                        FROM record_applicant_edu_map raep
                        CROSS JOIN master_post pm
                        WHERE pm.post_id = ? 
                    ) raep ON rud.user_details_AI_ID = raep.fk_applicant_id AND raep.rn = 1
                    LEFT JOIN master_qualification qm ON raep.fk_Quali_ID = qm.Quali_ID
                    LEFT JOIN master_district dmC ON rud.Corr_District_lgd = dmC.District_Code_LGD
                    LEFT JOIN master_panchayats gp ON gp.panchayat_lgd_code = upa.post_gp
                    LEFT JOIN master_villages vill ON vill.village_code = upa.post_village
                    LEFT JOIN master_nnn nnn ON nnn.std_nnn_code = upa.post_nagar
                    LEFT JOIN master_ward ward ON ward.ID = upa.post_ward
                    LEFT JOIN (
                            SELECT 
                                fk_apply_id,
                                GROUP_CONCAT(Organization_Name SEPARATOR ', ') AS organisation_name_concat,
                                GROUP_CONCAT(Designation SEPARATOR ', ') AS designation,
                                GROUP_CONCAT(
                                    CONCAT(
                                        DATE_FORMAT(Date_From, '%d-%m-%Y'), ' से ', DATE_FORMAT(Date_To, '%d-%m-%Y')
                                    )
                                    SEPARATOR ', '
                                ) AS fromToDate,
                                /* 1 year or more = 6 marks, else 0 */
                                CASE 
                                    WHEN SUM(DATEDIFF(Date_To, Date_From) + 1) >= 365 THEN 0
                                    ELSE 0
                                END AS total_experience_marks
                            FROM record_applicant_experience_map
                            GROUP BY fk_apply_id
                     ) raem ON rud.fk_apply_id = raem.fk_apply_id
                    LEFT JOIN post_organization_map pom ON upa.fk_post_id = pom.fk_post_id AND pom.fk_post_id = ?
                    LEFT JOIN master_post pm ON upa.fk_post_id = pm.post_id AND pm.post_id = ? 
                    GROUP BY rud.user_details_AI_ID
                    ORDER BY upa.apply_date ASC";
        try {
            $query = [];
            if ($request->reportdownloadOption == 'other') {

                $query = DB::select($verifiedRejectedSql, [$postId, $postId, $postId, $postId, $postId]);
            } elseif ($request->reportdownloadOption == 'all') {
                $query = DB::select($sql, [$postId, $postId, $postId, $postId, $postId]);
            }
            // dd($query);

            // Process data to match Excel structure
            $processedData = collect($query)->map(function ($item) {
                return [
                    'panchayat_nagar_no' => $item->panchayat_nagar_no,
                    'panjiyan_no' => $item->panjiyan_no,
                    'nagar_panchayat_name' => $item->nagar_panchayat_name,
                    'kendra_name' => $item->kendra_name,
                    'postname' => $item->postname,
                    'total_application' => $item->total_application,
                    'Full_Name_hindi' => $item->Full_Name_hindi,
                    'FatherName' => $item->FatherName,
                    'niwas' => $item->niwas,
                    'Caste' => $item->Caste,
                    'Age' => $item->Age,
                    'qualification' => $item->qualification,
                    'total_marks' => $item->total_marks,
                    'obtained_marks' => $item->obtained_marks,
                    'percentage' => $item->percentage,
                    'sixty_percent_marks' => $item->sixty_percent_marks,

                    // विधवा / परित्यक्ता section
                    'vivdhwa_talakshuda_detail' => $item->vivdhwa_talakshuda_detail, // विवरण
                    'vidhwa_talakshuda_marks' => $item->vidhwa_talakshuda_marks, // अंक

                    // अनु. जाति/जन जाति होने पर section
                    'caste_answer' => $item->caste_answer, // विवरण (जाति का नाम अगर SC/ST है तो, नहीं तो MULL)
                    'caste_marks' => $item->caste_marks, // अंक

                    // गरीबी रेखा परिवार से section
                    'bpl_answer' => $item->bpl_answer, // bpl_answer (yes or no)
                    'bpl_marks' => $item->bpl_marks, // अंक

                    // कार्यकर्ता /सहायिका होने पर section
                    'karyakarta_sahayika_detail' => $item->karyakarta_sahayika_detail, // विवरण
                    'karyakarta_sahayika_marks' => $item->karyakarta_sahayika_marks, // अंक

                    // अनुभव section
                    'experience_details' => $item->experience_details, // विवरण
                    'experience_marks' => $item->experience_marks, // अंक

                    // Other columns
                    'total_counted_marks' => $item->total_counted_marks,
                    'yogyata_kram' => $item->yogyata_kram,
                    'qualified_reason' => $item->qualified_reason,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Report Generation Error: ' . $e->getMessage());
            return back()->with('error', 'रिपोर्ट जनरेट करने की प्रक्रिया असफल रही। कृपया कुछ समय बाद पुनः प्रयास करें।');
        }
        $postname = $postDetails->title;
        $listName = $request->is_antim_list == 'true' ? 'अंतिम सूची' : 'अनंतिम सूची';
        $headingStructure = [
            'mainHeading' => 'कार्यालय परियोजना अधिकारी एकीकृत बाल विकास सेवा परियोजना, जिला – (छ.ग.)',
            'subHeading' => 'परिशिष्ट – चार',
            'thirdHeading' => $postname . ' पद हेतु प्राप्त आवेदन पत्रों की ' . $listName,
            'columnGroups' => [
                [
                    'title' => '',
                    'columns' => [
                        'सरल क्रमांक',
                        'पंचायत क्रमांक',
                        'पंजीयन क्रमांक'
                    ]
                ],
                [
                    'title' => 'आंगनवाड़ी केंद्र का',
                    'columns' => [
                        'ग्राम पंचायत का नाम',
                        'केंद्र का नाम',
                    ]
                ],
                [
                    'title' => '',
                    'columns' => [
                        'पद जिस हेतु आवेदन किया गया है',
                        'कुल प्राप्त आवेदन की संख्या',
                        'प्रत्येक आवेदिका का नाम',
                        'पिता / पति का नाम',
                        'निवास',
                        'जाति',
                        'आयु',
                        'शैक्षणिक योग्यता',
                        'पूर्णांक',
                        'प्राप्तांक',
                        'कुल प्राप्तांक का प्रतिशत',
                        'प्राप्तांक प्रतिशत का 60 प्रतिशत'
                    ]
                ],
                [
                    'title' => 'विधवा / परित्यक्ता',
                    'columns' => [
                        'विवरण',
                        'अंक'
                    ]
                ],
                [
                    'title' => 'अनु. जाति/जन जाति होने पर',
                    'columns' => [
                        'विवरण',  // This is for the checkbox (1 or 0)
                        'अंक'      // This is for the marks
                    ]
                ],
                [
                    'title' => 'गरीबी रेखा परिवार से',
                    'columns' => [
                        'विवरण',  // This is for the checkbox (1 or 0)
                        'अंक'      // This is for the marks
                    ]
                ],
                [
                    'title' => 'कार्यकर्ता /सहायिका होने पर',
                    'columns' => [
                        'विवरण',
                        'अंक'
                    ]
                ],
                [
                    'title' => 'अनुभव',
                    'columns' => [
                        'विवरण',
                        'अंक'
                    ]
                ],
                [
                    'title' => '',
                    'columns' => [
                        'कुल प्राप्तांक',
                    ]
                ],
                [
                    'title' => '',
                    'columns' => [
                        'योग्यता क्रम',
                    ]
                ],
                [
                    'title' => '',
                    'columns' => [
                        'यदि अपात्र किया गया है, कारण लिखें'
                    ]
                ]
            ]
        ];

        if ($request->is_antim_list == 'true') {

            $fileName = 'अंतिम_सूची_' . $postId . '_' . date('Y-m-d') . '.xlsx';
        } else {

            $fileName = 'अनंतिम_सूची_' . $postId . '_' . date('Y-m-d') . '.xlsx';
        }

        return DataUtility::exportToExcelComplex($processedData, $fileName, $headingStructure);
    }
}
