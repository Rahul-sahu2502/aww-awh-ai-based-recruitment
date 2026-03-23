<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Exception;

class ExaminorController extends Controller
{
    public function dashboard()
    {
        $username = Session::get('sess_fname');
        $admin_pic = Session::get('admin_pic');
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id');
        $project_code = Session::get('project_code', 0);

        if ($role === 'Admin' || $role === 'Supervisor' || $role === 'CDPO') {

            $records = DB::table('post_vacancy_map')
                ->join('post_map', 'post_vacancy_map.fk_post_id', '=', 'post_map.fk_post_id')
                ->join('master_post', 'post_map.fk_post_id', '=', 'master_post.post_id')
                ->where('post_map.fk_district_id', $district_id)
                ->pluck('post_vacancy_map.no_of_vacancy');

            $news_num = 0;

            // dd($records);
            foreach ($records as $json) {
                $news_num += $this->calculateVacancySum($json);
            }

            $application_list = DB::table('tbl_user_detail')
                ->select('tbl_user_detail.ID AS RowID', 'master_post.title AS post_title', 'tbl_user_detail.*', 'master_user.*', 'tbl_user_detail.Pref_Districts AS pref_district_name', 'tbl_applicant_experience_details.*', 'tbl_applicant_education_qualification.*')
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_applicant_experience_details', 'tbl_user_detail.ID', '=', 'tbl_applicant_experience_details.Applicant_ID')
                ->join('tbl_applicant_education_qualification', 'tbl_user_detail.Applicant_ID', '=', 'tbl_applicant_education_qualification.fk_applicant_id')
                ->join('tbl_user_post_apply', 'tbl_user_detail.Applicant_ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->where('tbl_user_post_apply.is_final_submit', '1')
                ->limit(5)
                ->get();

            $total_Applications = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $total_verified_Applications = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('status', 'Verified')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $total_rejected_Applications = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('status', 'Rejected')
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->count();

            $application_count = DB::table('tbl_user_post_apply')
                ->where('is_final_submit', '1')
                ->where('fk_district_id', $district_id)
                ->count();

            $post_count = DB::table('master_post')->count();

            $totalAdvertisment = DB::table('master_advertisement')
                ->where('district_lgd_code', $district_id)
                ->count();

            $totalPosts = DB::table('master_post')
                ->join('master_advertisement', 'master_post.Advertisement_ID', '=', 'master_advertisement.Advertisement_ID')
                ->where('master_advertisement.district_lgd_code', $district_id)
                ->count();

            $pending_count = DB::table('tbl_user_post_apply')
                ->where('fk_district_id', $district_id)
                ->where('is_final_submit', '1')
                ->where('status', 'Submitted')
                ->count();

            $verified_count = DB::table('tbl_user_post_apply')
                ->where('fk_district_id', $district_id)
                ->where('is_final_submit', '1')
                ->where('status', 'Verified')
                ->count();

            $rejected_count = DB::table('tbl_user_post_apply')
                ->where('fk_district_id', $district_id)
                ->where('is_final_submit', '1')
                ->where('status', 'Rejected')
                ->count();

            // $no_of_application = DB::table('tbl_user_post_apply')
            //     ->select(
            //         DB::raw('DATE(apply_date) as date_only'),
            //         DB::raw('COUNT(*) as total_applied'),
            //         DB::raw('(SELECT COUNT(*) FROM tbl_user_post_apply AS e 
            //       WHERE DATE(e.eligiblity_date) = DATE(tbl_user_post_apply.apply_date) 
            //       AND e.fk_district_id = tbl_user_post_apply.fk_district_id) as total_eligible')
            //     )
            //     ->where('fk_district_id', $district_id)
            //     ->groupBy(DB::raw('DATE(apply_date)'))
            //     ->orderBy('date_only', 'ASC')
            //     ->get();

            $no_of_application = DB::select("
    WITH RECURSIVE dates AS (
        SELECT CURDATE() - INTERVAL 6 DAY AS date_only
        UNION ALL
        SELECT date_only + INTERVAL 1 DAY
        FROM dates
        WHERE date_only + INTERVAL 1 DAY <= CURDATE()
    )

    SELECT 
        dates.date_only,
        COALESCE(apply_data.total_applied, 0) AS total_applied,
        COALESCE(eligibility_data.total_eligible, 0) AS total_eligible
    FROM 
        dates
    LEFT JOIN 
        (
            SELECT 
                DATE(apply_date) AS date_only,
                COUNT(*) AS total_applied
            FROM 
                tbl_user_post_apply
            WHERE 
                apply_date >= CURDATE() - INTERVAL 6 DAY
                AND fk_district_id = ?
                AND is_final_submit =1
            GROUP BY 
                DATE(apply_date)
        ) AS apply_data 
    ON dates.date_only = apply_data.date_only
    LEFT JOIN 
        (
            SELECT 
                DATE(eligiblity_date) AS date_only,
                COUNT(*) AS total_eligible
            FROM 
                tbl_user_post_apply
            WHERE 
                eligiblity_date >= CURDATE() - INTERVAL 6 DAY
                AND fk_district_id = ?
            GROUP BY 
                DATE(eligiblity_date)
        ) AS eligibility_data 
    ON dates.date_only = eligibility_data.date_only
    ORDER BY 
        dates.date_only ASC
", [$district_id, $district_id]);

            // Qualification chart data
            $qualiChartData = DB::select("
                SELECT 
                    qm.Quali_ID AS fk_Quali_ID,
                    qm.Quali_Name AS qualification_name,
                    COUNT(upa.apply_id) AS application_count
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
                    ON upa.apply_id = edu.fk_apply_id AND upa.fk_district_id = ? AND upa.is_final_submit = 1
                LEFT JOIN master_post pm 
                    ON pm.post_id = upa.fk_post_id 
                WHERE 
                    qm.Quali_ID NOT IN (1, 2)
                GROUP BY 
                    qm.Quali_ID, qm.Quali_Name
                ORDER BY 
                    fk_Quali_ID ASC
            ", [$district_id]);


        }
        $data['news_num'] = $news_num ?? 0;
        $data['application_list'] = $application_list ?? [];
        $data['pending_count'] = $pending_count ?? 0;
        $data['application_count'] = $application_count ?? 0;
        $data['verified_count'] = $verified_count ?? 0;
        $data['rejected_count'] = $rejected_count ?? 0;
        $data['total_Applications'] = $total_Applications ?? 0;
        $data['total_verified_Applications'] = $total_verified_Applications ?? 0;
        $data['total_rejected_Applications'] = $total_rejected_Applications ?? 0;
        $data['totalAdvertisment'] = $totalAdvertisment ?? 0;
        $data['totalPosts'] = $totalPosts ?? 0;
        $data['qualiChartData'] = $qualiChartData ?? [];

        return view('examinor/dashboard', compact('data', 'no_of_application'));
    }

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

    public function getNotifications()
    {
        try {
            $district_id = Session::get('district_id');

            // Get pending approvals for notification
            $notifications = DB::select("
                SELECT 
                    ma.Advertisement_ID,
                    mp.project,
                    ma.Advertisement_Title as advertisement_title,
                    ma.Date_For_Age,
                    DATEDIFF(CURDATE(), ma.Date_For_Age) as days_after_expiry,
                    COUNT(DISTINCT post.post_id) as total_posts,
                    COUNT(upa.apply_id) as pending_applications,
                    post.title as post_title
                FROM master_advertisement ma
                INNER JOIN master_post post ON ma.Advertisement_ID = post.Advertisement_ID 
                INNER JOIN master_projects mp ON ma.project_code = mp.project_code
                LEFT JOIN tbl_user_post_apply upa ON post.post_id = upa.fk_post_id 
                    AND upa.is_final_submit = 1 
                    AND upa.status = 'Submitted'
                WHERE ma.district_lgd_code = ?
                    AND DATEDIFF(CURDATE(), ma.Date_For_Age) > 21
                GROUP BY ma.Advertisement_ID, mp.project, ma.Advertisement_Title, ma.Date_For_Age
                HAVING pending_applications > 0
                ORDER BY days_after_expiry DESC
            ", [$district_id]);

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

    public function pendingApprovals(Request $request)
    {
        try {
            $district_id = Session::get('district_id');
            $role = Session::get('sess_role');
            $advertisement_id = $request->get('advertisement_id');

            // Ensure only Admin role can access
            if ($role !== 'Admin') {
                return redirect()->back()->with('error', 'आपके पास इस पृष्ठ को देखने का अधिकार नहीं है।');
            }

            // Build query for pending applications
            $query = DB::table('tbl_user_post_apply as upa')
                ->select([
                    'upa.apply_id',
                    'upa.fk_applicant_id',
                    'upa.fk_post_id',
                    'upa.apply_date',
                    'upa.status',
                    'upa.eligiblity_date',
                    'mp.title as title',
                    'mp.Advertisement_ID',
                    'ma.Advertisement_Title as advertisement_title',
                    'ma.Date_For_Age',
                    'proj.project',
                    'ud.applicant_name',
                    'ud.Father_Name',
                    'ud.DOB',
                    'ud.Gender',
                    'ud.Category',
                    'ud.Mobile_Number',
                    'ud.Email_ID',
                    'ud.Pref_Districts as district_name',
                    'mu.username',
                    'aeq.qualification_name',
                    DB::raw('DATEDIFF(CURDATE(), ma.Date_For_Age) as days_after_expiry')
                ])
                ->join('master_post as mp', 'upa.fk_post_id', '=', 'mp.post_id')
                ->join('master_advertisement as ma', 'mp.Advertisement_ID', '=', 'ma.Advertisement_ID')
                ->join('master_projects as proj', 'ma.project_code', '=', 'proj.project_code')
                ->join('tbl_user_detail as ud', 'upa.fk_applicant_id', '=', 'ud.Applicant_ID')
                ->join('master_user as mu', 'upa.fk_applicant_id', '=', 'mu.ID')
                ->leftJoin('tbl_applicant_education_qualification as aeq', 'upa.fk_applicant_id', '=', 'aeq.fk_applicant_id')
                ->where('upa.fk_district_id', $district_id)
                ->where('upa.is_final_submit', '1')
                ->where('upa.status', 'Submitted')
                ->whereRaw('DATEDIFF(CURDATE(), ma.Date_For_Age) > 21');

            // Filter by advertisement if specified
            if ($advertisement_id) {
                $query->where('ma.Advertisement_ID', $advertisement_id);
            }

            $applications = $query->orderBy('ma.Date_For_Age', 'desc')->get();

            return view('examinor.pending_approvals', compact('applications'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading pending approvals: ' . $e->getMessage());
        }
    }

    public function updateApplicationStatus(Request $request)
    {
        try {
            $district_id = Session::get('district_id');
            $role = Session::get('sess_role');

            // Ensure only Admin role can update
            if ($role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'आपके पास इस कार्य को करने का अधिकार नहीं है।'
                ]);
            }

            $apply_id = $request->input('apply_id');
            $status = $request->input('status');
            $remarks = $request->input('remarks', '');

            // Validate inputs
            if (!$apply_id || !$status || !in_array($status, ['Verified', 'Rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'अमान्य डेटा प्रदान किया गया।'
                ]);
            }

            // Verify application belongs to this district
            $application = DB::table('tbl_user_post_apply')
                ->where('apply_id', $apply_id)
                ->where('fk_district_id', $district_id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'आवेदन नहीं मिला या आपके पास इसे अपडेट करने का अधिकार नहीं है।'
                ]);
            }

            // Update application status
            $updated = DB::table('tbl_user_post_apply')
                ->where('apply_id', $apply_id)
                ->where('fk_district_id', $district_id)
                ->update([
                    'status' => $status,
                    'eligiblity_date' => now(),
                    'updated_at' => now(),
                    'remarks' => $remarks
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => $status === 'Verified' ? 'आवेदन सफलतापूर्वक अनुमोदित किया गया।' : 'आवेदन सफलतापूर्वक अस्वीकार किया गया।'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'आवेदन की स्थिति अपडेट करने में त्रुटि हुई।'
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating application status: ' . $e->getMessage()
            ]);
        }
    }
}
