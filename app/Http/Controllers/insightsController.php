<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class insightsController extends Controller
{
    public function getCasteWiseChartData(Request $request)
    {

        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        if ($role === 'Super_admin' && $district_id) {


            $application_count = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->select(
                    'tbl_user_post_apply.fk_post_id',       // Post ID
                    'master_post.title',                // Post Title (from master_post)
                    DB::raw('COUNT(*) as applications_filled')  // Total number of applications for each post
                )
                ->where('master_post.project_code', $project_code)
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->groupBy('tbl_user_post_apply.fk_post_id', 'master_post.title') // Group by Post ID and Title
                ->get();  // Get the result

            // dd($category_count);


            $org_type_count = DB::table('master_tbl_organization_type')
                ->leftJoin('record_applicant_experience_map', 'record_applicant_experience_map.Organization_Type', '=', 'master_tbl_organization_type.org_id')
                ->leftJoin('tbl_user_post_apply', 'record_applicant_experience_map.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
                ->select(
                    'master_tbl_organization_type.org_type',
                    DB::raw('COUNT(*) as applications_filled')
                )
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->groupBy('master_tbl_organization_type.org_type')
                ->get();


            $caste_post_data = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->join('tbl_user_detail', 'tbl_user_post_apply.fk_applicant_id', '=', 'tbl_user_detail.applicant_id')
                ->select(
                    'master_post.title as post_title',
                    'tbl_user_detail.Caste',
                    DB::raw('COUNT(*) as caste_applications')
                )
                ->where('master_post.project_code', $project_code)
                ->where('tbl_user_post_apply.fk_district_id', $district_id)
                ->groupBy('master_post.title', 'tbl_user_detail.Caste')
                ->get();

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
                            AND fk_district_id = $district_id
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
                            AND fk_district_id = $district_id
                        GROUP BY 
                            DATE(eligiblity_date)
                    ) AS eligibility_data 
                ON dates.date_only = eligibility_data.date_only
                ORDER BY 
                    dates.date_only ASC
            ");
        } else if ($role === 'Super_admin') {


            $application_count = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->select(
                    'tbl_user_post_apply.fk_post_id',       // Post ID
                    'master_post.title',                // Post Title (from master_post)
                    DB::raw('COUNT(*) as applications_filled')  // Total number of applications for each post
                )
                ->groupBy('tbl_user_post_apply.fk_post_id', 'master_post.title') // Group by Post ID and Title
                ->get();  // Get the result

            // dd($category_count);


            $org_type_count = DB::table('master_tbl_organization_type')
                ->leftJoin('record_applicant_experience_map', 'record_applicant_experience_map.Organization_Type', '=', 'master_tbl_organization_type.org_id')
                ->leftJoin('tbl_user_post_apply', 'record_applicant_experience_map.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
                ->select(
                    'master_tbl_organization_type.org_type',
                    DB::raw('COUNT(*) as applications_filled')
                )
                ->groupBy('master_tbl_organization_type.org_type')
                ->get();


            $caste_post_data = DB::table('tbl_user_post_apply')
                ->join('master_post', 'tbl_user_post_apply.fk_post_id', '=', 'master_post.post_id')
                ->join('tbl_user_detail', 'tbl_user_post_apply.fk_applicant_id', '=', 'tbl_user_detail.applicant_id')
                ->select(
                    'master_post.title as post_title',
                    'tbl_user_detail.Caste',
                    DB::raw('COUNT(*) as caste_applications')
                )
                ->groupBy('master_post.title', 'tbl_user_detail.Caste')
                ->get();
        }

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
                GROUP BY 
                    DATE(eligiblity_date)
            ) AS eligibility_data 
        ON dates.date_only = eligibility_data.date_only
        ORDER BY 
            dates.date_only ASC
    ");




        return view('admin.insightsCharts', compact('no_of_application', 'application_count', 'org_type_count', 'caste_post_data'));
    }


    public function getPercentageWiseApplicationCount($qualiId)
    {

        $role = Session::get('sess_role');
        $district_id = Session::get('district_id', 0);
        $project_code = Session::get('project_code', 0);

        if ($role === 'Super_admin' && $district_id) {
            $results = DB::select("
            SELECT 
                COALESCE(COUNT(t1.fk_apply_id), 0) AS application_count,
                ranges.percentage_range
            FROM 
                (SELECT '00-33%' AS percentage_range UNION
                 SELECT '33-50%' UNION
                 SELECT '50-70%' UNION
                 SELECT '70-100%') ranges
            LEFT JOIN (
                -- Step 1: Get each applicant's max qualification
                SELECT 
                    fk_apply_id, 
                    MAX(fk_Quali_ID) AS max_quali
                FROM 
                    record_applicant_edu_map
                GROUP BY 
                    fk_apply_id
            ) t2 ON 1=1
            LEFT JOIN record_applicant_edu_map t1 
                ON t1.fk_apply_id = t2.fk_apply_id
                AND t1.fk_Quali_ID = t2.max_quali
                AND t1.fk_Quali_ID = :quali_id
                AND (
                    (t1.percentage BETWEEN 0 AND 33 AND ranges.percentage_range = '00-33%') OR
                    (t1.percentage > 33 AND t1.percentage <= 50 AND ranges.percentage_range = '33-50%') OR
                    (t1.percentage > 50 AND t1.percentage <= 70 AND ranges.percentage_range = '50-70%') OR
                    (t1.percentage > 70 AND t1.percentage <= 100 AND ranges.percentage_range = '70-100%')
                )
            LEFT JOIN tbl_user_post_apply upa 
                ON upa.apply_id = t1.fk_apply_id AND upa.fk_district_id = :district_id
            LEFT JOIN master_post mp ON mp.post_id = upa.fk_post_id 
            WHERE mp.project_code = :project_code
            AND upa.apply_id IS NOT NULL
            GROUP BY 
                ranges.percentage_range
            ORDER BY 
                FIELD(ranges.percentage_range, '00-33%', '33-50%', '50-70%', '70-100%');
        ", [
                'quali_id' => $qualiId,
                'district_id' => $district_id, // yahan aap dynamic district bhi bhej sakte ho
                'project_code' => $project_code
            ]);

        } else if ($role === 'Super_admin') {
            $results = DB::select("
            SELECT 
                COALESCE(COUNT(t1.fk_apply_id), 0) AS application_count,
                ranges.percentage_range
            FROM 
                (SELECT '00-33%' AS percentage_range UNION
                 SELECT '33-50%' UNION
                 SELECT '50-70%' UNION
                 SELECT '70-100%') ranges
            LEFT JOIN (
                -- Step 1: Get each applicant's max qualification
                SELECT 
                    fk_apply_id, 
                    MAX(fk_Quali_ID) AS max_quali
                FROM 
                    record_applicant_edu_map
                GROUP BY 
                    fk_apply_id
            ) t2 ON 1=1
            LEFT JOIN record_applicant_edu_map t1 
                ON t1.fk_apply_id = t2.fk_apply_id
                AND t1.fk_Quali_ID = t2.max_quali
                AND t1.fk_Quali_ID = :quali_id
                AND (
                    (t1.percentage BETWEEN 0 AND 33 AND ranges.percentage_range = '00-33%') OR
                    (t1.percentage > 33 AND t1.percentage <= 50 AND ranges.percentage_range = '33-50%') OR
                    (t1.percentage > 50 AND t1.percentage <= 70 AND ranges.percentage_range = '50-70%') OR
                    (t1.percentage > 70 AND t1.percentage <= 100 AND ranges.percentage_range = '70-100%')
                )
            LEFT JOIN tbl_user_post_apply upa 
                ON upa.apply_id = t1.fk_apply_id
            WHERE upa.apply_id IS NOT NULL
            GROUP BY 
                ranges.percentage_range
            ORDER BY 
                FIELD(ranges.percentage_range, '00-33%', '33-50%', '50-70%', '70-100%');
        ", [
                'quali_id' => $qualiId
            ]);

        } else {
            $results = DB::select("
            SELECT 
                COALESCE(COUNT(t1.fk_apply_id), 0) AS application_count,
                ranges.percentage_range
            FROM 
                (SELECT '00-33%' AS percentage_range UNION
                 SELECT '33-50%' UNION
                 SELECT '50-70%' UNION
                 SELECT '70-100%') ranges
            LEFT JOIN (
                -- Step 1: Get each applicant's max qualification
                SELECT 
                    fk_apply_id, 
                    MAX(fk_Quali_ID) AS max_quali
                FROM 
                    record_applicant_edu_map
                GROUP BY 
                    fk_apply_id
            ) t2 ON 1=1
            LEFT JOIN record_applicant_edu_map t1 
                ON t1.fk_apply_id = t2.fk_apply_id
                AND t1.fk_Quali_ID = t2.max_quali
                AND t1.fk_Quali_ID = :quali_id
                AND (
                    (t1.percentage BETWEEN 0 AND 33 AND ranges.percentage_range = '00-33%') OR
                    (t1.percentage > 33 AND t1.percentage <= 50 AND ranges.percentage_range = '33-50%') OR
                    (t1.percentage > 50 AND t1.percentage <= 70 AND ranges.percentage_range = '50-70%') OR
                    (t1.percentage > 70 AND t1.percentage <= 100 AND ranges.percentage_range = '70-100%')
                )
            LEFT JOIN tbl_user_post_apply upa 
                ON upa.apply_id = t1.fk_apply_id AND upa.fk_district_id = :district_id
            LEFT JOIN master_post mp ON mp.post_id = upa.fk_post_id 
            AND upa.apply_id IS NOT NULL
            GROUP BY 
                ranges.percentage_range
            ORDER BY 
                FIELD(ranges.percentage_range, '00-33%', '33-50%', '50-70%', '70-100%');
        ", [
                'quali_id' => $qualiId,
                'district_id' => $district_id
            ]);
        }

        // Format data for chart
        $formatted = [
            'categories' => [],
            'values' => []
        ];

        foreach ($results as $row) {
            $formatted['categories'][] = $row->percentage_range;
            $formatted['values'][] = $row->application_count;
        }

        return response()->json($formatted);
    }




}
