<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use App\Helpers\OtpHelper;
use Illuminate\Support\Facades\Log;
use App\Models\user_post_apply;
use App\Models\experience_detail;
use App\Models\User;
use App\Models\user_detail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Storage;
use App\Models\ApplicantEducationQualification;
use Illuminate\Support\Str;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LoginController;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CandidateController extends Controller
{

    public function dashboard()
    {

        $username = Session::get('sess_fname');
        $admin_pic = Session::get('admin_pic');
        $role = Session::get('sess_role');
        $district_id = Session::get('district_id');
        $appid = Session::get('sess_id');


        $application_lists = DB::select(
            "SELECT 
                tbl_user_detail.Applicant_ID AS aid,
                tbl_user_detail.ID AS RowID,
                apply.apply_id AS application_id,
                apply.status AS Application_Status,
                apply.apply_date AS apply_date,
                apply.application_num AS application_num,
                master_district.name AS Dist_name,
                apply.is_final_submit AS is_final_submit,
                -- apply.self_attested_file AS self_attested_file,
                -- apply.self_attested_file_upload_date AS self_attested_file_upload_date,
                tbl_user_detail.*,
                master_user.Full_Name,
                master_post.title AS post_name,
                master_advertisement.Advertisement_Title AS Advertisement_Title
            FROM tbl_user_post_apply AS apply 
            INNER JOIN tbl_user_detail ON tbl_user_detail.ID = apply.fk_applicant_id
            INNER JOIN master_user ON master_user.ID = tbl_user_detail.Applicant_ID
            INNER JOIN master_post ON apply.fk_post_id = master_post.post_id 
            LEFT JOIN master_district ON apply.fk_district_id = master_district.District_Code_LGD
            INNER JOIN master_advertisement ON master_post.Advertisement_ID = master_advertisement.Advertisement_ID
            WHERE tbl_user_detail.Applicant_ID=? AND apply.stepCount=5
        ",
            [$appid]
        );

        $total_applications = DB::table('tbl_user_post_apply')
            ->join('tbl_user_detail', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
            ->where('tbl_user_detail.Applicant_ID', $appid)
            ->count();

        $verified_applications = DB::table('tbl_user_post_apply')
            ->join('tbl_user_detail', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
            ->where('tbl_user_detail.Applicant_ID', $appid)
            ->where('status', 'Verified')
            ->count();

        $rejected_applications = DB::table('tbl_user_post_apply')
            ->join('tbl_user_detail', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
            ->where('tbl_user_detail.Applicant_ID', $appid)
            ->where('status', 'Rejected')
            ->where('is_final_submit', 1)
            ->count();

        $pending_count = DB::selectOne(
            "SELECT COUNT(*) AS total
                FROM tbl_user_post_apply AS apply 
                INNER JOIN tbl_user_detail ON tbl_user_detail.ID = apply.fk_applicant_id
                INNER JOIN master_user ON master_user.ID = tbl_user_detail.Applicant_ID
                INNER JOIN master_post ON apply.fk_post_id = master_post.post_id 
                LEFT JOIN master_district ON apply.fk_district_id = master_district.District_Code_LGD
                INNER JOIN master_advertisement ON master_post.Advertisement_ID = master_advertisement.Advertisement_ID 
                WHERE 
                (apply.is_final_submit != 1 OR apply.is_final_submit IS NULL)
                AND apply.stepCount < 5 
                AND tbl_user_detail.Applicant_ID = ?",
            [$appid],
        );


        $post_list = DB::select(
            "SELECT
                                            pm.*,
                                            am.*,
                                            md.name AS Dist_name,
                                            
                                            -- Handle multiple project_code (comma-separated or JSON)
                                            GROUP_CONCAT(DISTINCT projects.project ORDER BY projects.project SEPARATOR ', ') AS project_names,
                                            
                                            -- Handle multiple std_nnn_code (JSON array) - get city names
                                            GROUP_CONCAT(DISTINCT nagar.nnn_name ORDER BY nagar.nnn_name SEPARATOR ', ') AS nnn_names,
                                            
                                            -- Get block names through panchayat join
                                            GROUP_CONCAT(DISTINCT blocks.block_name_hin ORDER BY blocks.block_name_hin SEPARATOR ', ') AS block_names,
                                            
                                            -- Handle ward IDs (now contains master_ward.ID values)
                                            GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ', ') AS ward_names,
                                            
                                            -- Panchayat names
                                            GROUP_CONCAT(DISTINCT panchayat.panchayat_name_hin ORDER BY panchayat.panchayat_name_hin SEPARATOR ', ') AS panchayat_names,
                                            
                                            -- Village names
                                            GROUP_CONCAT(DISTINCT master_villages.village_name_hin ORDER BY master_villages.village_name_hin SEPARATOR ', ') AS villages_names,
                                            
                                            CASE
                                                WHEN CURDATE() BETWEEN am.Advertisement_Date AND am.Date_For_Age THEN 'Active'
                                                WHEN am.Advertisement_Date > CURDATE() THEN 'Upcoming'
                                                WHEN am.Date_For_Age < CURDATE() THEN 'Expired'
                                                ELSE 'unknown'
                                            END AS apply_status
                                            
                                        FROM master_post pm
                                        LEFT JOIN master_advertisement am ON pm.Advertisement_ID = am.Advertisement_ID
                                        LEFT JOIN master_district md ON am.district_lgd_code = md.District_Code_LGD
                                        
                                        -- Handle multiple std_nnn_code (JSON array) for cities
                                        LEFT JOIN JSON_TABLE(
                                            pm.std_nnn_code,
                                            '$[*]' COLUMNS (
                                                std_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS std_codes ON 1=1
                                        LEFT JOIN master_nnn nagar ON std_codes.std_code = nagar.std_nnn_code
                                        
                                        -- Handle multiple project_code (comma-separated or JSON)
                                        LEFT JOIN JSON_TABLE(
                                            CONCAT('[\"', REPLACE(pm.project_code, ',', '\",\"'), '\"]'),
                                            '$[*]' COLUMNS (
                                                proj_code VARCHAR(50) PATH '$'
                                            )
                                        ) AS proj_codes ON 1=1
                                        LEFT JOIN master_projects projects ON TRIM(proj_codes.proj_code) = projects.project_code
                                        
                                        -- Handle multiple gp_nnn_code (JSON array) for panchayats
                                        LEFT JOIN JSON_TABLE(
                                            pm.gp_nnn_code,
                                            '$[*]' COLUMNS (
                                                gp_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS gp_codes ON 1=1
                                        LEFT JOIN master_panchayats panchayat ON gp_codes.gp_code = panchayat.panchayat_lgd_code
                                        LEFT JOIN master_blocks blocks ON panchayat.block_lgd_code = blocks.block_lgd_code
                                        
                                        -- Handle multiple village_code (JSON array)
                                        LEFT JOIN JSON_TABLE(
                                            pm.village_code,
                                            '$[*]' COLUMNS (
                                                village_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS village_codes ON 1=1
                                        LEFT JOIN master_villages ON village_codes.village_code = master_villages.village_code
                                        
                                        -- Handle ward IDs (now contains master_ward.ID values as JSON array)
                                        LEFT JOIN JSON_TABLE(
                                            pm.ward_no,
                                            '$[*]' COLUMNS (
                                                ward_id INT PATH '$'
                                            )
                                        ) AS ward_ids ON 1=1
                                        LEFT JOIN master_ward ON ward_ids.ward_id = master_ward.ID
                                        
                                        WHERE pm.post_id IS NOT NULL AND pm.is_disable = 1 
                                        GROUP BY pm.post_id
                                        ORDER BY pm.post_id DESC
                                        -- LIMIT 10
                                        "
        );

        $incompleteCount = $pending_count->total;

        $data['application_lists'] = $application_lists ?? [];
        $data['total_applications'] = $total_applications ?? 0;
        $data['verified_applications'] = $verified_applications ?? 0;
        // $data['rejected_applications'] = $rejected_applications ?? 0;
        $data['incompleteCount'] = $incompleteCount ?? 0;
        $data['post_list'] = $post_list ?? 0;
        // dd($application_lists);

        return view('candidate.dashboard', compact('data', 'application_lists'));
    }

    /**
     * Display User Manual PDF viewer
     */
    public function userManual()
    {
        $pdfPath = asset('assets/user_manual/AWWCandidateManual.pdf');
        return view('candidate.user_manual', compact('pdfPath'));
    }

    public function submitted_application_list($status = null)
    {

        $appid = Session::get('sess_id');

        if ($status) {

            $application_lists = DB::select("SELECT 
                                                    tbl_user_detail.Applicant_ID AS aid,
                                                    tbl_user_detail.ID AS RowID,
                                                    apply.apply_id AS application_id,
                                                    apply.status AS Application_Status,
                                                    apply.is_final_submit AS is_final_submit,
                                                   -- apply.self_attested_file AS self_attested_file,
                                                   -- apply.self_attested_file_upload_date AS self_attested_file_upload_date,
                                                    tbl_user_detail.*,
                                                    master_user.Full_Name,
                                                    master_post.title AS post_name,
                                                    master_advertisement.Advertisement_Title AS Advertisement_Title
                                                FROM tbl_user_post_apply AS apply 
                                                INNER JOIN tbl_user_detail ON tbl_user_detail.ID = apply.fk_applicant_id
                                                INNER JOIN master_user ON master_user.ID = tbl_user_detail.Applicant_ID
                                                INNER JOIN master_post ON apply.fk_post_id = master_post.post_id 
                                                LEFT JOIN master_district ON apply.fk_district_id = master_district.District_Code_LGD
                                                INNER JOIN master_advertisement ON master_post.Advertisement_ID = master_advertisement.Advertisement_ID
                                               WHERE tbl_user_detail.Applicant_ID=? AND apply.status=? AND apply.stepCount=5 
                                            ", [$appid, $status]);
        } else {

            $application_lists = DB::select("SELECT 
                                                    tbl_user_detail.Applicant_ID AS aid,
                                                    tbl_user_detail.ID AS RowID,
                                                    apply.apply_id AS application_id,
                                                    apply.status AS Application_Status,
                                                    apply.is_final_submit AS is_final_submit,
                                                   -- apply.self_attested_file AS self_attested_file,
                                                   -- apply.self_attested_file_upload_date AS self_attested_file_upload_date,
                                                    tbl_user_detail.*,
                                                    master_user.Full_Name,
                                                    master_post.title AS post_name,
                                                    master_advertisement.Advertisement_Title AS Advertisement_Title
                                                FROM tbl_user_post_apply AS apply 
                                                INNER JOIN tbl_user_detail ON tbl_user_detail.ID = apply.fk_applicant_id
                                                INNER JOIN master_user ON master_user.ID = tbl_user_detail.Applicant_ID
                                                INNER JOIN master_post ON apply.fk_post_id = master_post.post_id 
                                                LEFT JOIN master_district ON apply.fk_district_id = master_district.District_Code_LGD
                                                INNER JOIN master_advertisement ON master_post.Advertisement_ID = master_advertisement.Advertisement_ID
                                               WHERE tbl_user_detail.Applicant_ID=? AND apply.stepCount=5
                                            ", [$appid]);
        }

        return view('candidate/submitted_application_list', compact('application_lists'));
    }

    public function save_self_attested(Request $request)
    {
        // Validate the request
        $request->validate([
            'applicant_id' => 'required|integer',
            'application_id' => 'required|integer',
            'self_attested_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048', // Max 2MB
        ]);

        // Retrieve applicant_id and application_id
        $applicantID = $request->input('applicant_id');
        $applicationID = $request->input('application_id');

        // Define allowed file types and MIME types
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'pdf'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $baseUploadPath = 'uploads/applicant/doc'; // Subdirectory within uploads root

        // Handle self-attested file upload
        if ($request->hasFile('self_attested_file')) {
            $uploadedFile = $request->file('self_attested_file');
            $uploadPath = "{$baseUploadPath}/{$applicantID}"; // Path relative to uploads root

            // Use UtilController to upload the file
            $uploadResult = UtilController::upload_file(
                $uploadedFile,
                'self_attested_file',
                'uploads',
                $allowedExtensions,
                $allowedMimeTypes
            );

            if (!$uploadResult) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to save self-attested file!',
                ], 422);
            }

            // Update the tbl_user_post_apply table
            try {
                $updated = DB::table('tbl_user_post_apply')
                    ->where('fk_applicant_id', $applicantID)
                    ->where('apply_id', $applicationID)
                    ->update([
                        'self_attested_file' => $uploadResult,
                        'self_attested_file_upload_date' => date('Y-m-d'),
                    ]);

                if ($updated === 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No record found to update for the given Applicant ID and Application ID!',
                    ], 404);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Self-attested file uploaded and updated successfully!',
                    'applicant_id' => md5($applicantID),
                    'application_id' => md5($applicationID),

                ], 200);
            } catch (\Throwable $th) {
                DB::rollBack();

                // If validation error thrown by user code
                if ($th instanceof \Exception) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $th->getMessage(), // <-- Swal will show this
                    ], 400, [], JSON_UNESCAPED_UNICODE);
                }

                // Default server exception
                return response()->json([
                    'status' => 'error',
                    'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                    'errors' => ['server' => [$th->getMessage()]]
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No file uploaded!',
        ], 422);
    }



    public function pending_application_list()
    {

        $appid = Session::get('sess_id');


        $pending_application_lists = DB::select("SELECT 
                                                            tbl_user_detail.Applicant_ID AS aid,
                                                            tbl_user_detail.ID AS RowID,
                                                            apply.apply_id AS application_id,
                                                            apply.status AS Application_Status,
                                                            apply.is_final_submit AS is_final_submit,
                                                            master_user.Full_Name,
                                                            master_post.title AS post_name,
                                                            master_advertisement.Advertisement_Title AS Advertisement_Title,
                                                            tbl_user_detail.*
                                                            FROM tbl_user_post_apply AS apply 
                                                            INNER JOIN tbl_user_detail ON tbl_user_detail.ID = apply.fk_applicant_id
                                                            INNER JOIN master_user ON master_user.ID = tbl_user_detail.Applicant_ID
                                                            INNER JOIN master_post ON apply.fk_post_id = master_post.post_id 
                                                            LEFT JOIN master_district ON apply.fk_district_id = master_district.District_Code_LGD
                                                            INNER JOIN master_advertisement ON master_post.Advertisement_ID = master_advertisement.Advertisement_ID 
                                                            WHERE 
                                                            apply.is_final_submit!=1 OR apply.is_final_submit IS NULL AND
                                                            apply.stepCount < 5 AND tbl_user_detail.Applicant_ID= ?", [$appid]);

        return view('candidate/pending_application_list', compact('pending_application_lists'));
    }

    public function final_submit()
    {
        // Update query for final submit now not editable
        $sess = Session::get('sess_id');
        $RowID = $_POST['RowID'];
        $apply_id = $_POST['apply_id'];
        $data['RowID'] = $RowID;
        $apply_data = DB::select(
            "SELECT am.Date_For_Age 
                                            FROM tbl_user_post_apply upa
                                            LEFT JOIN master_post pm ON upa.fk_post_id = pm.post_id 
                                            LEFT JOIN master_advertisement am ON pm.Advertisement_ID = am.Advertisement_ID
                                            WHERE upa.apply_id = ?",
            [$apply_id]
        );


        if (!empty($apply_data)) {
            $advertisementDate = $apply_data[0]->Date_For_Age;

            if (strtotime($advertisementDate) < strtotime(date('Y-m-d'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इस पद की अंतिम तिथि समाप्त हो चुकी है आप आवेदन जमा नही कर सकते हैं|'
                ]);
            }
        }


        ### Fetch and Insert The Record For Final Apply
        $tbl_user_details_map_success = '';
        $user_edu_map_success = '';
        $user_exp_map_success = '';


        // Step 1: Fetch Applicant Detail (Single Row)
        $applicant_details = DB::table('tbl_user_detail')
            ->select('tbl_user_detail.ID AS RowID', 'tbl_user_detail.*')
            ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
            ->where('tbl_user_detail.ID', $RowID)
            ->first();

        // Step 2: Insert into record_user_detail_map
        if ($applicant_details) {
            $userDetailData = (array) $applicant_details;
            unset($userDetailData['RowID']); // Remove alias
            $userDetailData['user_details_AI_ID'] = $userDetailData['ID'];
            $userDetailData['fk_apply_id'] = $apply_id;
            unset($userDetailData['ID']);
            $tbl_user_details_map_success = DB::table('record_user_detail_map')->insert($userDetailData);
        }



        // Step 3: Fetch and Insert Multiple Education Records
        $education_details = DB::table('tbl_applicant_education_qualification')
            ->where('fk_applicant_id', $RowID)
            ->get();

        if (!$education_details->isEmpty()) {
            $educationRecords = $education_details->map(function ($edu) use ($apply_id) {
                $eduArray = (array) $edu;
                $eduArray['fk_apply_id'] = $apply_id;
                return $eduArray;
            })->toArray();

            $user_edu_map_success = DB::table('record_applicant_edu_map')->insert($educationRecords);
        }




        // Step 4: Fetch Experience Records
        $experience_details = DB::table('tbl_applicant_experience_details')
            ->where('Applicant_ID', $RowID)
            ->get();

        // Step 5: Prepare insert array
        $experienceRecords = $experience_details->map(function ($exp) use ($apply_id) {
            $expArray = (array) $exp;
            $expArray['fk_apply_id'] = $apply_id;
            return $expArray;
        })->toArray();

        // Step 6: Insert only if data exists
        if (!empty($experienceRecords)) {
            $user_exp_map_success = DB::table('record_applicant_experience_map')->insert($experienceRecords);
        } else {
            $user_exp_map_success = true;
        }


        if ($tbl_user_details_map_success && $user_edu_map_success && $user_exp_map_success) {

            // Update the status in the database
            DB::table('tbl_user_post_apply')
                ->where('apply_id', $apply_id)
                ->where('fk_applicant_id', $RowID)
                ->update([
                    'is_final_submit' => 1
                ]);


            return response()->json(['message' => 'आवेदन सफलतापूर्वक सबमिट किया गया।', 'status' => 'success']);
        } else {
            return response()->json(['message' => 'रिकॉर्ड डेटा सहेजने में त्रुटि!', 'status' => 'error']);
        }
    }

    public function print_application(Request $request, $applicant_id = 0, $application_id = 0)
    {

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
                        'master_panchayats.panchayat_name_hin'
                    )
                    ->join('master_blocks', 'tbl_user_post_apply.post_block', '=', 'master_blocks.block_lgd_code')
                    ->join('master_panchayats', 'tbl_user_post_apply.post_gp', '=', 'master_panchayats.panchayat_lgd_code');
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


        $Post_questionAnswer = DB::table('tbl_user_post_question_answer as answer')
            ->select(
                'apply.fk_post_id',
                'questions.ques_ID',
                'questions.ques_name',
                'questions.ans_type',
                'answer.date_From',
                'answer.date_To',
                'answer.fk_question_id as fk_ques_id',
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

        $marks_details = DB::table('tbl_user_post_apply')
            ->select('min_edu_qualification_mark', 'edu_qualification_mark', 'min_experiance_mark', 'ques_mark', 'domicile_mark', 'total_mark')
            ->where('fk_applicant_id', '=', $applicant_details->fk_applicant_id_ind)
            ->where('fk_post_id', '=', $applicant_details->fk_post_id_ind) // Corrected the naming of fk_post_id
            ->first();

        $Skill_Ans = DB::table('tbl_post_skill_answer as ans')
            ->join('master_skills as ms', 'ans.fk_skill_id', '=', 'ms.skill_id')
            ->join('tbl_user_post_apply as apply', 'ans.fk_apply_id', '=', 'apply.apply_id')
            ->whereRaw('MD5(ans.fk_apply_id) = ?', [$application_id])
            ->select('ans.skill_ans_id', 'ms.skill_name as SkillName', 'ans.skill_answers as SkillAnswer')
            ->get();



        return view('candidate/application_print', compact('marks_details', 'applicant_details', 'experience_details', 'education_details', 'Post_questionAnswer', 'Skill_Ans', 'postArea_Check'));
    }


    public function recruitment_list()
    {

        $userId = Session::get('sess_id');

        $user_dob = DB::selectOne("SELECT Date_Of_Birth FROM master_user WHERE ID = ?", [$userId]);

        // Extract Date_Of_Birth properly
        $user_dob = $user_dob->Date_Of_Birth ?? null;

        // Calculate user age
        $user_age = $user_dob ? date_diff(date_create($user_dob), date_create('today'))->y : null;

        // Debugging age
        if (is_null($user_age)) {
            return back()->with('error', 'Date of Birth is missing in your profile.');
        }

        // Fetch recruitment data and check eligibility
        $schemes = DB::select("SELECT 
            am.Advertisement_ID,
            am.Advertisement_Title,
            am.Advertisement_Date AS Start_date,
            am.Date_For_Age AS End_date,
            am.Advertisement_Document,

            -- Combined post titles and vacancy counts
            GROUP_CONCAT(pm.title ORDER BY pm.post_id SEPARATOR ', ') AS Post_Titles,
            -- GROUP_CONCAT(pvm.no_of_vacancy ORDER BY pvm.fk_post_id SEPARATOR ', ') AS Vacancy_Counts,

            -- Eligibility logic simplified
            GROUP_CONCAT(
                CASE 
                    WHEN pm.max_age IS NULL THEN 'Age Not Specified'
                    WHEN ? IS NULL THEN 'User Age Not Provided'
                    WHEN ? BETWEEN pm.min_age AND pm.max_age THEN 'Eligible'
                    ELSE 'Not Eligible'
                END
                ORDER BY pm.post_id
                SEPARATOR ', '
            ) AS Eligibility_Status,

            COUNT(DISTINCT pm.post_id) AS Active_Posts_Count,
            MAX(am.Date_For_Age) AS Last_Active_Date

        FROM master_advertisement am
        JOIN master_post pm ON pm.Advertisement_ID = am.Advertisement_ID 
        -- JOIN post_vacancy_map pvm ON pvm.fk_post_id = pm.post_id

        WHERE 
            am.Advertisement_Date <= CURDATE()
            AND (am.Date_For_Age IS NULL OR am.Date_For_Age >= CURDATE())

        GROUP BY 
            am.Advertisement_ID,
            am.Advertisement_Title,
            am.Advertisement_Date,
            am.Date_For_Age,
            am.Advertisement_Document

      ", [$user_age, $user_age]);

        return view('candidate/recruitment_list', compact('schemes'));
    }


    public function user_register(Request $request, $appID)
    {
        if ($request->isMethod('post')) {
        } else {
            $data['eligibility'] = DB::table('eligibility_criteria')
                ->whereRaw("md5(Advertisement_ID) = ?", [$appID])
                ->get();

            $data['valuation'] = DB::table('valuation_parameters')
                ->whereRaw("md5(Advertisement_ID) = ?", [$appID])
                ->get();

            $data['qualification'] = DB::table('master_qualification')
                ->select('master_qualification.Quali_ID', 'master_qualification.Quali_Name')
                ->join('eligibility_criteria', 'eligibility_criteria.Min_Qualification_ID', '=', 'master_qualification.Quali_ID')
                ->whereRaw("MD5(eligibility_criteria.Advertisement_ID) = ?", [$appID])
                ->get();

            $data['recruitment'] = DB::table('master_post')
                ->whereRaw("md5(Advertisement_ID) = ?", [$appID])
                ->get();

            $data['schemeid'] = DB::table('master_advertisement')
                ->select('Advertisement_ID')
                ->whereRaw("MD5(Advertisement_ID) = ?", [$appID])
                ->get();

            $data['cities'] = DB::select("SELECT District_Code_LGD, name FROM master_district");

            return view('candidate/user_application_form', compact('data'));
        }
    }






    ###============= Apply Form Submission ========================####

    public function savePost(Request $request)
    {
        $rules = [
            'master_post' => 'required|exists:master_post,post_id',
            'selected_district' => 'required|exists:master_district,District_Code_LGD',
            'area' => 'required|in:1,2',
            'projects' => 'nullable',
            'block' => 'required_if:area,1|exists:master_blocks,block_lgd_code',
            'gp' => 'required_if:area,1|exists:master_panchayats,panchayat_lgd_code',
            'post_village' => 'required_if:area,1|exists:master_villages,village_code',
            'nagar' => 'required_if:area,2|exists:master_nnn,std_nnn_code',
            'ward' => 'required_if:area,2|exists:master_ward,ID',
        ];

        $customMessages = [
            'master_post.required' => 'कृपया पोस्ट का चयन करें।',
            'master_post.exists' => 'चयनित पोस्ट अमान्य है।',
            'selected_district.required' => 'कृपया जिला चयन करें।',
            'selected_district.exists' => 'चयनित जिला अमान्य है।',
            'area.required' => 'कृपया क्षेत्र का चयन करें (ग्रामीण या शहरी)।',
            'area.in' => 'चयनित क्षेत्र अमान्य है।',
            'block.required_if' => 'जब क्षेत्र ग्रामीण हो, तो विकासखंड चयन अनिवार्य है।',
            'block.exists' => 'चयनित ब्लॉक अमान्य है।',
            'gp.required_if' => 'जब क्षेत्र ग्रामीण हो, तो ग्राम पंचायत चयन अनिवार्य है।',
            'gp.exists' => 'चयनित ग्राम पंचायत अमान्य है।',
            'post_village.required_if' => 'जब क्षेत्र ग्रामीण हो, तो ग्राम चयन अनिवार्य है।',
            'post_village.exists' => 'चयनित ग्राम अमान्य है।',
            'nagar.required_if' => 'जब क्षेत्र शहरी हो, तो नगर निकाय चयन अनिवार्य है।',
            'nagar.exists' => 'चयनित नगर निकाय अमान्य है।',
            'ward.required_if' => 'जब क्षेत्र शहरी हो, तो वार्ड संख्या चयन अनिवार्य है।',
            'ward.exists' => 'चयनित वार्ड संख्या अमान्य है।',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'कृपया सभी आवश्यक फ़ील्ड का चयन करें ।',
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $uid = Session::get('uid');
        $postId = $request->input('master_post');

        $applicantIDs = DB::table('tbl_user_detail')
            ->where('Applicant_ID', $uid)
            ->pluck('ID');

        if ($applicantIDs->isNotEmpty()) {
            $checkPost = DB::table('tbl_user_post_apply')
                ->whereIn('fk_applicant_id', $applicantIDs)
                ->where('fk_post_id', $postId)
                ->where('stepCount', '=', 5);

            if ($request->input('area') == 1) {
                $checkPost->where('post_block', $request->input('block'))
                    ->where('post_gp', $request->input('gp'))
                    ->where('post_village', $request->input('post_village'));
            } else {
                $checkPost->where('post_nagar', $request->input('nagar'))
                    ->where('post_ward', $request->input('ward'));
            }

            if ($checkPost->exists()) {
                return response()->json([
                    'message' => 'आप इस पद के लिए पहले ही आवेदन कर चुके हैं।',
                    'status' => 'warning'
                ]);
            }
        }

        //## Validation On All Selected Question Selected Question Multiple Select 
        $SelectedQuestions = DB::table('post_question_map')
            ->where('fk_post_id', $postId)
            ->get();
        $row = $SelectedQuestions->firstWhere('fk_ques_id', 10);

        if ($row) {
            $post_map_id = $row->post_map_id;
            $reqKey = "question_$post_map_id";

            if (empty($request->$reqKey)) {
                return response()->json([
                    'message' => 'यूजीसी द्वारा मान्यता प्राप्त संस्थान से डिग्री, डिप्लोमा या पीजी डिप्लोमा प्रमाणपत्र अनिवार्य है कृपया चयन करें।',
                    'status' => 'error',
                ]);
            }
        }

        $questions = DB::table('post_question_map AS map')
            ->join('master_post_questions AS qus', 'map.fk_ques_id', '=', 'qus.ques_ID')
            ->select('map.post_map_id', 'map.fk_ques_id', 'qus.ques_name', 'qus.ans_type')
            ->where('map.fk_post_id', $postId)
            ->whereNull('map.deleted_at')
            ->get();

        // Step 3: Validate and prepare file uploads (but don't save yet)
        $allowedExtensions = ['pdf'];
        $allowedMimeTypes = ['application/pdf'];
        $filePaths = [];
        $answers = [];

        foreach ($questions as $question) {
            $questionId = $question->fk_ques_id;
            $postMapId = $question->post_map_id;
            $ansType = $question->ans_type;
            $inputKey = 'question_' . $postMapId;
            $fileKeyPrefix = 'question_' . $postMapId . '_file_'; // multi file prefix
            $singleFileKey = 'question_' . $postMapId . '_file'; // single file key
            $fdDateKey = 'question_' . $postMapId . '_date'; // FD type date key

            // Check if it's FD type question
            $isFDType = ($ansType === 'FD');

            if ($isFDType) {
                // Handle FD type question: date + file
                $dateValue = $request->input($fdDateKey);
                $filePath = null;

                if ($request->hasFile($singleFileKey)) {
                    $uploadedFile = $request->file($singleFileKey);
                    if (is_array($uploadedFile)) {
                        $uploadedFile = $uploadedFile[0];
                    }

                    $uploadResult = UtilController::upload_file(
                        $uploadedFile,
                        $singleFileKey,
                        'uploads',
                        $allowedExtensions,
                        $allowedMimeTypes
                    );

                    if (!$uploadResult) {
                        foreach ($filePaths as $prevPath) {
                            if (file_exists(public_path($prevPath))) {
                                unlink(public_path($prevPath));
                            }
                        }
                        return response()->json([
                            'status' => 'error',
                            'message' => "प्रश्न '{$question->ques_name}' के लिए फ़ाइल सहेजने में असमर्थ! केवल PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                        ], 422, [], JSON_UNESCAPED_UNICODE);
                    }
                    $filePath = $uploadResult;
                }

                // For FD type: Store date in answer and date_From, file in 'file' key
                $answers[] = [
                    'post_map_id' => $postMapId,
                    'fk_question_id' => $questionId,
                    'ans_type' => $ansType, // Store ans_type for reference
                    'answer' => $dateValue, // Store the date value
                    'file' => $filePath, // Store file path in 'file' key for consistency
                    'date_From' => $dateValue, // Also store in date_From
                    'date_To' => null,
                    'total_experience_days' => null
                ];
            } else {
                // Handle other question types (existing logic)
                if ($request->filled($inputKey) || $request->hasFile($singleFileKey) || $request->hasFile($fileKeyPrefix . '0')) {
                    $answerData = (array) $request->input($inputKey); // always array

                    foreach ($answerData as $ansIndex => $singleAnswer) {
                        $filePath = null;

                        $date_From = $request->input('dateFrom_question_' . $postMapId, null);
                        $date_To = $request->input('dateTo_question_' . $postMapId, null);
                        $total_experience_days = $request->input('totalExpDays_question_' . $postMapId, null);

                        // Single answer => singleFileKey, Multiple => fileKeyPrefix + index
                        $currentFileKey = (count($answerData) > 1) ? $fileKeyPrefix . $ansIndex : $singleFileKey;

                        if ($request->hasFile($currentFileKey)) {
                            $uploadedFile = $request->file($currentFileKey);
                            if (is_array($uploadedFile)) {
                                $uploadedFile = $uploadedFile[0]; // prevent array-to-object error
                            }

                            $uploadResult = UtilController::upload_file(
                                $uploadedFile,
                                $currentFileKey,
                                'uploads',
                                $allowedExtensions,
                                $allowedMimeTypes
                            );

                            if (!$uploadResult) {
                                foreach ($filePaths as $prevPath) {
                                    if (file_exists(public_path($prevPath))) {
                                        unlink(public_path($prevPath));
                                    }
                                }
                                return response()->json([
                                    'status' => 'error',
                                    'message' => "प्रश्न '{$question->ques_name}' के लिए फ़ाइल सहेजने में असमर्थ! केवल PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                                ], 422, [], JSON_UNESCAPED_UNICODE);
                            }
                            $filePath = $uploadResult;
                        }

                        $answers[] = [
                            'post_map_id' => $postMapId,
                            'fk_question_id' => $questionId,
                            'ans_type' => $ansType, // Store ans_type for reference
                            'answer' => $singleAnswer,
                            'file' => $filePath,
                            'date_From' => $date_From,
                            'date_To' => $date_To,
                            'total_experience_days' => $total_experience_days
                        ];
                    }
                }
            }
        }

        $project_code = DB::table('master_post')
            ->where('post_id', $postId)
            ->value('project_code');

        // Prepare data array for session storage for PostData, QuestionData And Skill Data
        $PostSessionData = [
            'fk_applicant_id' => $uid,
            'fk_post_id' => $postId,
            'fk_district_id' => $request->input('selected_district'),
            'post_area' => $request->input('area'),
            'post_projects' => $project_code ?? null,
            'apply_date' => now(),
            'status' => 'Submitted',
            'status_date' => now(),
        ];

        if ($request->input('area') == 1) {
            $PostSessionData['post_block'] = $request->input('block');
            $PostSessionData['post_gp'] = $request->input('gp');
            $PostSessionData['post_village'] = $request->input('post_village');
        } else {
            $PostSessionData['post_nagar'] = $request->input('nagar');
            $PostSessionData['post_ward'] = $request->input('ward');
        }

        //अनियमितता पात्र Case me 
        $Discontinue_ques = collect($answers)->first(
            fn($item) =>
            isset($item['fk_question_id']) && $item['fk_question_id'] == 11 && $item['answer'] == 'हाँ'
        );

        if ($Discontinue_ques) {
            return response()->json([
                'message' => 'आपको पहले अनियमितता के कारण सेवा से अलग किया गया था, अतः आप आवेदन करने के पात्र नहीं है।',
                'status' => 'error',
            ]);
        }

        // Store in session
        Session::put('apply_data', $PostSessionData);
        Session::put('Questions_answers', $answers);
        Session::put('Skill_answers', $request->input('skill_options', []));

        return response()->json([
            'message' => 'डेटा सफलतापूर्वक सत्र में संग्रहीत किया गया है।',
            'status' => 'success',
            'apply_data' => $PostSessionData
        ]);
    }

    public function saveAppDetail(Request $request)
    {
        // Validation rules
        $rules = [];
        $customMessages = [];

        $rules = [
            'First_Name' => 'required',
            'firstName_hindi' => 'required',
            'mothername' => 'required',
            'fathername' => 'required',
            // 'domicile_district' => 'required',
            'corr_addr' => 'required',
            'cur_district' => 'required',
            'pincode' => [
                'nullable',
                'digits:6',
                'required_without:pincode_manual',
            ],
            'pincode_manual' => [
                'nullable',
                'digits:6',
                'required_without:pincode',
            ],
            // 'perm_addr' => 'required',
            // 'per_district' => 'required',
            // 'ppincode' => 'required|numeric|digits:6',
            'nationality' => 'required',
            'dob' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'gender' => 'required',
            'caste' => 'required',
            'identity_type' => 'nullable|in:0,1,2,3',
            'epicno' => 'nullable|size:10|regex:/^[A-Z]{3}[0-9]{7}$/',
            'current_area' => 'required|in:1,2',
            'current_block' => 'required_if:current_area,1|exists:master_blocks,block_lgd_code',
            'current_gp' => 'required_if:current_area,1|exists:master_panchayats,panchayat_lgd_code',
            'current_village' => 'required_if:current_area,1|exists:master_villages,village_code',
            'current_nagar' => 'required_if:current_area,2|exists:master_nnn,std_nnn_code',
            'current_ward' => 'required_if:current_area,2|exists:master_ward,ID',
            'adhaar' => 'required|min:12|max:12',
            'AdharConfirm' => 'required_with:adhaar|in:1'
        ];
        // $rules['adhaar'] = $request->filled('app_id') ? 'min:12' : 'digits:12';


        $customMessages = array_merge($customMessages, [
            'mobile.digits' => 'मोबाइल नंबर 10 अंक का होना चाहिए।',
            'mobile.regex' => 'मोबाइल नंबर 6-9 से शुरू होना चाहिए।',
            'mobile.required' => 'मोबाइल नंबर आवश्यक है।',
            // 'pincode.digits' => 'पिनकोड 6 अंक का होना चाहिए।',
            // 'ppincode.digits' => 'पिनकोड 6 अंक का होना चाहिए।',
            'pincode.required_without' => 'कृपया पिनकोड चुनें या स्वयं दर्ज करें।',
            'pincode_manual.required_without' => 'कृपया पिनकोड चुनें या स्वयं दर्ज करें।',
            'pincode.digits' => 'पिनकोड 6 अंकों का होना चाहिए।',
            'pincode_manual.digits' => 'पिनकोड 6 अंकों का होना चाहिए।',
            'adhaar.required' => 'आधार नंबर अनिवार्य है।',
            'adhaar.min' => 'आधार नंबर 12 अंक का होना चाहिए।',
            'adhaar.max' => 'आधार नंबर 12 अंक का होना चाहिए।',
            'AdharConfirm.required_with' => 'आधार नंबर प्रस्तुत सहमति अनिवार्य है, कृपया चेकबॉक्स चुनें।',
            'identity_type.in' => 'अमान्य पहचान प्रकार। कृपया एक वैध विकल्प चुनें।',
            'current_block.required_if' => 'विकासखंड चयन अनिवार्य है जब क्षेत्र ग्रामीण हो।',
            'current_gp.required_if' => 'ग्राम पंचायत चयन अनिवार्य है जब क्षेत्र ग्रामीण हो।',
            'current_village.required_if' => 'ग्राम चयन अनिवार्य है जब क्षेत्र ग्रामीण हो।',
            'current_nagar.required_if' => 'नगर निकाय चयन अनिवार्य है जब क्षेत्र शहरी हो।',
            'current_ward.required_if' => 'वार्ड चयन अनिवार्य है जब क्षेत्र शहरी हो।',
            // 'epicno.required' => 'इपिक नंबर आवश्यक है।',
            'epicno.regex' => 'इपिक नंबर का प्रारूप 3 बड़े अक्षर + 7 अंक होना चाहिए। (e.g. ABC1234567)',
            'epicno.size' => 'इपिक नंबर ठीक 10 अंक का होना चाहिए।',
        ]);

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'कृपया सभी आवश्यक फ़ील्ड सही से भरें।',
                'errors' => $validator->errors(),
                'status' => 'error',
            ]);
        }

        // Retrieve saved post apply data from session
        $applyData = Session::get('apply_data', []);
        if (empty($applyData)) {
            return response()->json([
                'message' => 'पहले पोस्ट विवरण सहेजे गए नहीं।',
                'status' => 'error'
            ]);
        }

        // ## Widow And DIdvorce Case me 
        $answers = Session::get('Questions_answers', []);
        if (empty($answers)) {
            return response()->json([
                'message' => 'पहले पोस्ट प्रश्न विवरण सहेजे गए नहीं।',
                'status' => 'error'
            ]);
        }

        // ## अगर विधवा है तो address मैच नही करना है 
        $marrige_ques = collect($answers)->first(
            fn($item) =>
            isset($item['fk_question_id']) && $item['fk_question_id'] == 1 && $item['answer'] == 'विधवा'
        );

        if (!$marrige_ques) {
            $isRural = $applyData['post_area'] == 1;

            $mismatch = $isRural
                ? ($applyData['post_block'] != $request->current_block || $applyData['post_gp'] != $request->current_gp || $applyData['post_village'] != $request->current_village)
                : ($applyData['post_nagar'] != $request->current_nagar || $applyData['post_ward'] != $request->current_ward);

            if ($mismatch) {
                return response()->json([
                    'message' => $isRural
                        ? 'आपका स्थायी ब्लॉक/पंचायत/ग्राम चयनित पोस्ट के पते से मेल नहीं खाता। अतः आप इस पद के लिए आवेदन नहीं कर सकते।'
                        : 'आपका स्थायी नगर/वार्ड चयनित पोस्ट के पते से मेल नहीं खाता। अतः आप इस पद के लिए आवेदन नहीं कर सकते।',
                    'status' => 'warning'
                ]);
            }
        }


        //## Verify/Validate Age For User Eligible or not 
        $Post_ID = $applyData['fk_post_id'];
        $Applicant_id = $applyData['fk_applicant_id'];


        $post_data = DB::select("SELECT * FROM master_post WHERE post_id=? AND is_disable = 1", [$Post_ID]);
        if (empty($post_data)) {
            return response()->json([
                'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                'status' => 'error'
            ]);
        }
        $post_max_age = $post_data[0]->max_age;
        $post_age_relax = $post_data[0]->max_age_relax ?? null;
        $post_final_age = $post_age_relax ? $post_age_relax : $post_max_age;
        $post_name = $post_data[0]->title;
        $ExpRequired = 0;
        $WidowRequired = 0;
        $BPLRequired = 0;

        $user = DB::select("SELECT ID,Date_Of_Birth,Mobile_Number FROM master_user WHERE ID = ?", [$Applicant_id]);

        $userId = $user[0]->ID;
        $user_Date_Of_Birth = $user[0]->Date_Of_Birth;
        $user_Mobile_Number = $user[0]->Mobile_Number;

        $user_age = $user_Date_Of_Birth ? date_diff(date_create($user_Date_Of_Birth), date_create('2025-01-01'))->y : null;


        // ## यूजर की आयु चेक करेंगे और  पूर्व कार्यरत थी तो Age Relax भी चेक करेंगे 
        // 1. If user age is within max age, eligible
        if ($user_age <= $post_max_age) {
            // Eligible, continue
        } else {
            // 2. Check if question_id 9 exists and answer is 'हाँ'
            $age_relax_ques = collect($answers)->first(function ($item) {
                return isset($item['fk_question_id']) && $item['fk_question_id'] == 9 && $item['answer'] == 'हाँ';
            });

            if ($age_relax_ques) {
                Session::put('Experience_required', 1);
                $ExpRequired = Session::get('Experience_required', 0);

                // 3. If user age > post_final_age, not eligible
                if ($user_age > $post_final_age) {
                    return response()->json([
                        'message' => $post_name . ' पद के लिए अधिकतम आयु ' . $post_max_age . ' होनी चाहिए। अतः आप आवेदन करने के पात्र नहीं है।',
                        'status' => 'warning'
                    ]);
                }
            } else {
                // Not eligible if question 9 not present or answer is not 'हाँ'
                return response()->json([
                    'message' => $post_name . ' पद के लिए अधिकतम आयु ' . $post_max_age . ' होनी चाहिए। अतः आप आवेदन करने के पात्र नहीं है।',
                    'status' => 'warning'
                ]);
            }
        }


        // ## विधवा और परित्यक्ता/तलाकशुदा के लिए ExperienceDocument Required करेंगे
        $marrige_ques = collect($answers)->first(function ($item) {
            return isset($item['fk_question_id']) && $item['fk_question_id'] == 1 &&
                ($item['answer'] == 'परित्यक्ता' || $item['answer'] == 'तलाकशुदा' || $item['answer'] == 'विधवा');
        });
        if ($marrige_ques) {
            Session::put('Widow_Divorce_required', 1);
            $WidowRequired = Session::get('Widow_Divorce_required', 0);
        }

        // ## BPL/ गरीबी रेखा  के लिए Document Required करेंगे
        $marrige_ques = collect($answers)->first(function ($item) {
            return isset($item['fk_question_id']) && $item['fk_question_id'] == 3 && ($item['answer'] == 'हाँ');
        });
        if ($marrige_ques) {
            Session::put('BPL_required', 1);
            $BPLRequired = Session::get('BPL_required', 0);
        }


        try {
            DB::beginTransaction();
            $mobileNumber = session('sess_mobile');
            $uid = session('uid');
            $user = DB::table('master_user')
                ->where('Mobile_Number', $mobileNumber)
                ->orderBy('ID', 'desc')
                ->first();
            if (!$user) {
                return response()->json(['message' => 'उपयोगकर्ता नहीं मिला।', 'status' => 'error']);
            }
            $userId = $user->ID;

            // $masked_adhaar = substr_replace($request->adhaar, str_repeat("X", 8), 0, 8);
            $aadhaar = trim($request->adhaar);

            // Case 1: Pure 12 digit Aadhaar
            if (preg_match('/^\d{12}$/', $aadhaar)) {
                $masked_adhaar = substr_replace($aadhaar, str_repeat("X", 8), 0, 8);
            }

            // Case 2: Already masked (XXXXXXXX1234)
            elseif (preg_match('/^X{8}\d{4}$/', $aadhaar)) {
                $masked_adhaar = $aadhaar;
            }

            // Case 3: Invalid format
            else {
                return back()->withErrors(['adhaar' => 'अमान्य आधार नंबर']);
            }

            $first_name_uppercase = strtoupper($request->First_Name);
            $middle_name_uppercase = strtoupper($request->Middle_Name);
            $last_name_uppercase = strtoupper($request->Last_Name);
            $pincode = $request->pincode_manual ?? $request->pincode;

            // Prepare data array
            $data = [
                'First_Name' => $first_name_uppercase,
                'Middle_Name' => $middle_name_uppercase,
                'Last_Name' => $last_name_uppercase,
                'firstName_hindi' => $request['firstName_hindi'],
                'middleName_hindi' => $request['middleName_hindi'],
                'lastName_hindi' => $request['lastName_hindi'],
                'FatherName' => $request->fathername,
                'MotherName' => $request->mothername,
                'DOB' => $user_Date_Of_Birth,
                'Gender' => $request->gender,
                'Contact_Number' => $user_Mobile_Number,
                'reference_no' => $masked_adhaar,
                'AdharConfirm' => $request->AdharConfirm ?? 0,
                // 'isJanmanNiwasi' => $request->isJanmanNiwasi,
                'epicno' => $request->epicno ?? '',
                'identity_type' => $request->identity_type,
                'Domicile_District_lgd' => $request->domicile_district ?? '',
                'Corr_Address' => $request->corr_addr,
                'Corr_District_lgd' => $request->cur_district,
                'Corr_pincode' => $pincode, // $request->pincode,
                'Perm_Address' => $request->perm_addr ?? '',
                'Perm_District_lgd' => $request->per_district ?? '',
                'Perm_pincode' => $pincode, // $request->ppincode ?? '',
                // 'sameAddress' => $request->sameAddress,
                'current_area' => $request->current_area,
                'current_block' => $request->current_block,
                'current_gp' => $request->current_gp,
                'current_village' => $request->current_village,
                'current_nagar' => $request->current_nagar,
                'current_ward' => $request->current_ward,
                'Caste' => $request->caste,
                'Nationality' => $request->nationality,
                'IP_Address' => $request->ip(),
                'Last_Updated_By' => $uid,
                'Last_Updated_On' => now(),
                'Applicant_ID' => $userId,
                'Created_By' => $uid,
                'Created_On' => now(),

            ];

            if (!empty($applyData) && is_array($applyData)) {

                $check_user = DB::select("SELECT apply.fk_applicant_id , tbl_user_detail.ID
                                                FROM tbl_user_post_apply apply 
                                                INNER JOIN tbl_user_detail ON apply.fk_applicant_id=tbl_user_detail.ID
                                                WHERE apply.fk_post_id=? AND tbl_user_detail.Applicant_ID= ?", [$Post_ID, $userId]);


                if (!empty($check_user)) {
                    $Applicant_id = $check_user[0]->ID;
                    //  Record exists → update tbl_user_detail
                    DB::table('tbl_user_detail')
                        ->where('ID', $check_user[0]->ID)
                        ->update($data);

                    // return response()->json([
                    //     'message' => 'आप इस पद के लिए पहले ही आवेदन कर चुके हैं, अपूर्ण आवेदन सूची में आवेदन जमा करे।',
                    //     'status' => 'warning'
                    // ]);
                } else {
                    //  Record does not exist → insert new
                    $Applicant_id = DB::table('tbl_user_detail')->insertGetId($data);
                }

                $updateuser = DB::table('master_user')
                    ->where('Mobile_Number', $mobileNumber)
                    ->where('ID', session('sess_id'))
                    ->update(['reference_no' => $masked_adhaar]);

                $check_post = DB::select("SELECT apply.apply_id FROM tbl_user_post_apply apply 
                                                WHERE apply.fk_post_id=? AND apply.fk_applicant_id=?", [$Post_ID, $Applicant_id]);

                $applyData['fk_applicant_id'] = $Applicant_id;
                $applyData['stepCount'] = 2;

                if (!empty($check_post)) {
                    //  Record exists → update tbl_user_detail
                    DB::table('tbl_user_post_apply')
                        ->where('fk_applicant_id', $check_user[0]->ID)
                        ->where('fk_post_id', $applyData['fk_post_id'])
                        ->update($applyData);

                    $apply_id = $check_post[0]->apply_id;
                } else {

                    $apply_id = DB::table('tbl_user_post_apply')->insertGetId($applyData);
                }



                //## Save each skill_id with its selected options in JSON
                $skills = Session::get('Skill_answers', []);
                foreach ($skills as $skill_id => $options) {
                    DB::table('tbl_post_skill_answer')->insert([
                        'fk_apply_id' => $apply_id,
                        'fk_skill_id' => $skill_id,
                        'skill_answers' => json_encode($options, JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // dd($answers);
                // ## Retrieve saved QuestionAnswer data from session
                $allInserted = true;
                if ($allInserted) {

                    if (!empty($answers) && is_array($answers)) {
                        $delete = DB::table('tbl_user_post_question_answer')
                            ->where('applicant_id', $Applicant_id)
                            ->where('post_id', $Post_ID)
                            ->delete();
                    }
                    foreach ($answers as $item) {
                        // Determine if it's an FD type question (ans_type stored in session answers)
                        $isFDType = isset($item['ans_type']) && $item['ans_type'] === 'FD';

                        $insertData = [
                            'applicant_id' => $Applicant_id,
                            'post_id' => $Post_ID,
                            'post_map_id' => $item['post_map_id'],
                            'fk_question_id' => $item['fk_question_id'],
                            'answer' => $item['answer'],
                            'date_From' => $item['date_From'],
                            'date_To' => $item['date_To'],
                            'total_experience_days' => $item['total_experience_days'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        // Add file and ensure date for FD type
                        if ($isFDType) {
                            // For FD: uploaded file is stored in 'file'
                            if (!empty($item['file'])) {
                                $insertData['answer_file_upload'] = $item['file'];
                            } elseif (!empty($item['answer_file_upload'])) {
                                // fallback if file stored under this key
                                $insertData['answer_file_upload'] = $item['answer_file_upload'];
                            }

                            // If date is only present in 'answer', use it for date_From
                            if (empty($insertData['date_From']) && !empty($item['answer'])) {
                                $insertData['date_From'] = $item['answer'];
                            }
                        } else {
                            if (isset($item['file'])) {
                                $insertData['answer_file_upload'] = $item['file'];
                            } elseif (isset($item['answer_file_upload'])) {
                                $insertData['answer_file_upload'] = $item['answer_file_upload'];
                            }
                        }

                        // Skip saving to DB if 'answer' is null/empty (we don't want blank answers saved)
                        if (!isset($insertData['answer']) || $insertData['answer'] === null || trim($insertData['answer']) === '') {
                            // nothing to save for this answer
                            continue;
                        }


                        $inserted = DB::table('tbl_user_post_question_answer')->insert($insertData);

                        if (!$inserted) {
                            $allInserted = false;
                            break;
                        }
                    }

                    DB::commit();
                    session()->put('user_row_id', value: $Applicant_id);
                    session()->put('apply_id', $apply_id);

                    return response()->json([
                        'message' => 'डेटा सफलतापूर्वक दर्ज किया गया है।',
                        'status' => 'success',
                        'applicant_id' => $Applicant_id,
                        'tbl_user_detail_data' => $data,
                        'ExpRequired' => $ExpRequired,
                        'WidowRequired' => $WidowRequired,
                        'BPLRequired' => $BPLRequired,
                    ]);
                } else {

                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'डेटा सहेजने में त्रुटि हुई। कृपया पुनः प्रयास करें।',
                        'errors' => ['database' => ['Failed to save applicant details.']]
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function saveEducationDetail(Request $request)
    {
        try {

            DB::beginTransaction();
            $applicantId = $request->input('fk_applicant_id');
            $qualiIds = $request->input('fk_Quali_ID', []);
            $subjectIds = $request->input('fk_subject_id', []);
            $yearsPassing = $request->input('year_passing', []);
            $obtainedMarks = $request->input('obtained_marks', []);
            $totalMarks = $request->input('total_marks', []);
            $percentages = $request->input('percentage', []);
            $grades = $request->input('grade', []);
            $qualificationBoards = $request->input('qualification_board', []);

            // Retrieve saved post apply data from session
            $applyData = Session::get('apply_data', []);
            if (empty($applyData)) {
                return response()->json([
                    'message' => 'पहले पोस्ट विवरण सहेजे गए नहीं।',
                    'status' => 'error'
                ]);
            }
            $Post_ID = $applyData['fk_post_id'];
            $post_data = DB::select("SELECT post.*,q.Quali_Name as Qualification_name FROM master_post AS post
                                            LEFT JOIN master_qualification AS q ON post.Quali_ID=q.Quali_ID
                                            WHERE post.post_id=? AND post.is_disable = 1", bindings: [$Post_ID]);
            if (empty($post_data)) {
                return response()->json([
                    'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                    'status' => 'error'
                ]);
            }
            $post_min_qualification_id = $post_data[0]->Quali_ID;
            $post_title = $post_data[0]->title;
            $Qualification_name = $post_data[0]->Qualification_name;


            $finalQualiIds = []; // For fetching qualification names
            $allEducationData = []; // For storing session data

            foreach ($qualiIds as $index => $qualiId) {
                $subjectId = $subjectIds[$index] ?? null;
                $yearPassing = $yearsPassing[$index] ?? null;
                $obtained = $obtainedMarks[$index] ?? null;
                $total = $totalMarks[$index] ?? null;
                $percent = $percentages[$index] ?? null;
                $grade = $grades[$index] ?? null;
                $board = $qualificationBoards[$index] ?? null;

                $isAllEmpty = empty($qualiId) &&
                    empty($subjectId) &&
                    empty($yearPassing) &&
                    empty($obtained) &&
                    empty($total) &&
                    empty($board);

                $isPartial = empty($subjectId) || empty($qualiId) ||
                    empty($yearPassing) ||
                    empty($obtained) ||
                    empty($total) ||
                    empty($board);

                if ($isAllEmpty || $isPartial) {
                    continue; // Skip incomplete or empty rows
                }

                if (!empty($qualiId)) {
                    $finalQualiIds[] = $qualiId;
                }

                $allEducationData[] = [
                    'fk_applicant_id' => $applicantId,
                    'fk_Quali_ID' => $qualiId,
                    'fk_subject_id' => $subjectId,
                    'year_passing' => $yearPassing,
                    'obtained_marks' => $obtained,
                    'total_marks' => $total,
                    'percentage' => $percent,
                    'fk_grade_id' => $grade,
                    'qualification_board' => $board,
                    'create_ip' => $request->ip(),
                    'Created_by' => session()->get('uid'),
                    'Created_at' => now(),
                    'Created_on' => now(),
                ];
            }
            // Check if min qualification id exists in user's qualification ids
            if (!in_array($post_min_qualification_id, $finalQualiIds)) {
                return response()->json([
                    'message' => "$post_title पद के लिए न्यूनतम योग्यता $Qualification_name है, कृपया आवश्यक जानकारी भरें।",
                    'status' => 'error'
                ]);
            }
            $Applicant_id = session()->get('user_row_id');
            $apply_id = session()->get('apply_id');
            $DB_Entry = false;

            // Save education details
            if (!empty($allEducationData) && is_array($allEducationData)) {

                $Check_Edu = DB::table('tbl_applicant_education_qualification')
                    ->where('fk_applicant_id', $Applicant_id)
                    ->select('qualification_id', 'fk_applicant_id')
                    ->get();

                if ($Check_Edu->isNotEmpty()) {
                    // Delete old experience entries
                    DB::table('tbl_applicant_education_qualification')
                        ->where('fk_applicant_id', $Applicant_id)
                        ->delete();
                }

                foreach ($allEducationData as &$edu) {
                    $edu['fk_applicant_id'] = $Applicant_id;
                }
                DB::table('tbl_applicant_education_qualification')->insert($allEducationData);

                DB::update('UPDATE tbl_user_post_apply SET stepCount = 3 WHERE apply_id = ?', [$apply_id]);

                DB::commit();
                $DB_Entry = true;
            }


            // ## Fetch qualification names (unchanged)
            if (!empty($finalQualiIds)) {
                $placeholders = implode(',', array_fill(0, count($finalQualiIds), '?'));

                $qualificationRows = DB::select("SELECT Quali_ID, Quali_Name 
                                                        FROM master_qualification 
                                                        WHERE Quali_ID IN ($placeholders)
                                                    ", $finalQualiIds);

                $qualificationData = [];
                foreach ($qualificationRows as $row) {
                    $qualificationData[$row->Quali_ID] = $row->Quali_ID;
                }

                // Keep this session logic as-is
                session(['user_qualifications' => $qualificationData]);
            }
            if ($DB_Entry) {

                return response()->json([
                    'message' => "डेटा सफलतापूर्वक दर्ज कर लिया गया है।",
                    'status' => 'success',
                    'applicant_id' => $Applicant_id,
                    // 'all_user_qualifications_data' => $allEducationData,
                    'user_qualifications' => $qualificationData
                ]);
            } else {
                return response()->json([
                    'message' => 'डेटा सहेजने में त्रुटि हुई है।',
                    'status' => 'error'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function saveExperienceDetail(Request $request)
    {
        $rules = [
            //     'org_name' => 'array',
            //     'org_name.*' => 'string',
            //     'org_type' => 'array',
            //     'org_type.*' => 'required',
            //     'desg_name' => 'array',
            //     'desg_name.*' => 'string',
            //     'nature_work' => 'array',
            //     'nature_work.*' => 'string',
            //     'salary' => 'array',
            //     'salary.*' => 'string',
            //     'org_address' => 'array',
            //     'org_address.*' => 'string',
            //     'org_contact' => 'array',
            //     'org_contact.*' => 'string|size:10',
            //     'date_from' => 'array',
            //     'date_from.*' => 'date',
            //     'date_to' => 'array',
            //     'date_to.*' => 'date',
            //     'total_experience' => 'array',
            //     'total_experience.*' => 'string',
            //     'ngo_no' => 'array',
            //     'ngo_no.*' => 'nullable|string',
            'exp_document' => 'nullable',
            'exp_document.*' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',


        ];

        $messages = [
            'exp_document.mimes' => 'केवल PDF फ़ाइल ही मान्य है।',
            'exp_document.max' => 'फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।',
            'exp_document.*.mimes' => 'सभी अनुभव प्रमाण पत्र PDF फॉर्मेट में होनी चाहिए।',
            'exp_document.*.max' => ' सभी अनुभव प्रमाण पत्र 2MB से कम आकार की होनी चाहिए।',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => "कृपया सभी आवश्यक फ़ील्ड भरें! सुनिश्चित करें कि सभी आवश्यक फ़ील्ड सही तरीके से भरे गए हैं।",
                'status' => 'error',
                'errors' => $validator->errors(),
            ]);
        }

        // Manually validate dates
        foreach ($request->date_from as $i => $fromDate) {
            if (isset($request->date_to[$i]) && $request->date_to[$i] < $fromDate) {
                return response()->json([
                    'message' => "अनुभव की तिथि गलत है: 'कब तक' की तिथि 'कब से' से पहले नहीं हो सकती।",
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }
        }

        try {
            $applicantId = $request['app_id'] ?? $request['exp_fk_applicant_id'];

            if (!$applicantId) {
                throw new \Exception("Applicant ID is missing in the request.");
            }

            $experienceData = [];
            $orgNames = $request['org_name'] ?? [];


            //  Fetch old experience documents before deleting
            $oldExperiences = experience_detail::where('Applicant_ID', $applicantId)->get()->keyBy(function ($item, $key) {
                return $key;
            });

            // Delete old records
            experience_detail::where('Applicant_ID', $applicantId)->delete();
            DB::commit();

            $orgNames = $request->input('org_name', []);

            $orgCount = count($orgNames);
            $skippedIndexes = [];

            foreach ($orgNames as $index => $orgName) {
                $experienceEntries = [];

                // Completely blank row – skip
                if (
                    empty($orgName) &&
                    empty($request['org_type'][$index] ?? null) &&
                    empty($request['desg_name'][$index] ?? null) &&
                    empty($request['date_from'][$index] ?? null) &&
                    empty($request['date_to'][$index] ?? null) &&
                    empty($request['total_experience'][$index] ?? null) &&
                    empty($request['nature_work'][$index] ?? null) &&
                    empty($request['salary'][$index] ?? null) &&
                    empty($request['org_address'][$index] ?? null) &&
                    empty($request['org_contact'][$index] ?? null) &&
                    empty($request['exp_document'][$index] ?? null)
                ) {
                    continue;
                }

                // If even one required field is missing – skip this record
                if (
                    empty($orgName) ||
                    empty($request['org_type'][$index] ?? null) ||
                    empty($request['desg_name'][$index] ?? null) ||
                    empty($request['date_from'][$index] ?? null) ||
                    empty($request['date_to'][$index] ?? null) ||
                    empty($request['total_experience'][$index] ?? null) ||
                    // empty($request['exp_document'][$index] ?? null) ||
                    empty($request['nature_work'][$index] ?? null) ||
                    empty($request['org_address'][$index] ?? null) ||
                    empty($request['org_contact'][$index] ?? null) ||
                    empty($request['salary'][$index] ?? null)
                ) {
                    $skippedIndexes[] = $index;
                    continue;
                }

                $filePath = null;

                // Check if a file is uploaded for this specific index
                $file = $request->file("exp_document.$index");

                if ($file && $file->isValid() && $file->getSize() > 0) {
                    $uploadedFile = $request->file("exp_document")[$index];

                    // Call your custom upload helper
                    $filePath = UtilController::upload_file(
                        $uploadedFile,
                        'file',
                        'uploads',
                        ['pdf'],
                        ['application/pdf']
                    );
                } else {
                    // Use old file path if new file not uploaded
                    $filePath = $oldExperiences[$index]->exp_document ?? null;
                }

                // prepared the array for insert DB
                $experienceEntries[] = [
                    'Applicant_ID' => $applicantId,
                    'Organization_Name' => $orgName,
                    'Organization_Type' => $request['org_type'][$index] ?? null,
                    'NGO_No' => $request['ngo_no'][$index] ?? null,
                    'Designation' => $request['desg_name'][$index] ?? null,
                    'Date_From' => $request['date_from'][$index] ?? null,
                    'Date_To' => $request['date_to'][$index] ?? null,
                    'Total_Experience' => $request['total_experience'][$index] ?? '',
                    'Nature_Of_Work' => $request['nature_work'][$index] ?? null,
                    'salary' => $request['salary'][$index] ?? null,
                    'org_address' => $request['org_address'][$index] ?? null,
                    'org_contact' => $request['org_contact'][$index] ?? null,
                    'exp_document' => $filePath,
                    'IP_Address' => $request->ip(),
                    'Created_By' => session()->get('uid'),
                    'Created_On' => now(),
                ];

                if (count($experienceEntries) > 0) {
                    experience_detail::insert($experienceEntries);
                }
            }


            $Applicant_id = session()->get('user_row_id');
            $apply_id = session()->get('apply_id');
            DB::update('UPDATE tbl_user_post_apply SET stepCount = 4 WHERE apply_id = ?', [$apply_id]);


            return response()->json([
                'message' => count($skippedIndexes) > 0
                    ? "आपने कुछ अनुभव की जानकारी पूरी नही दी है ,यह मान्य नही होगा।"
                    : "सभी अनुभव प्रविष्टियाँ सफलतापूर्वक सहेजी गईं।",
                'icon' => count($skippedIndexes) > 0 ? "warning" : "success",
                'skipped_indexes' => $skippedIndexes,
                'status' => 'success',
                'applicant_id' => $applicantId
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }



    public function saveDocuments(Request $request)
    {
        // Get the applicant ID from session
        $applicantID = session()->get('uid');

        if (!$applicantID) {
            return response()->json([
                'status' => 'error',
                'message' => 'आवेदनकर्ता विवरण नहीं मिला। कृपया फिर से लॉगिन करें।',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $rules = [
                'document_photo' => 'required|file|mimes:jpeg,jpg,png|mimetypes:image/jpeg,image/png|max:100',
                'document_sign' => 'required|file|mimes:jpeg,jpg,png|mimetypes:image/jpeg,image/png|max:50',
                'document_adhaar' => 'file|mimes:pdf,jpeg,jpg,png|mimetypes:application/pdf,image/jpeg,image/png|max:2048',
                'domicile' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'epic_document' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:2048',
                // 'skills' => 'required|array',
            ];

            // Add caste certificate rule based on caste
            $caste = $request->input('selectedCaste');
            if ($caste !== 'सामान्य') {
                // If caste is not 'सामान्य', caste certificate is required
                $rules['caste_certificate'] = 'required|file|mimes:pdf|mimetypes:application/pdf|max:2048';
            }

            // Retrieve qualifications from session
            $qualifications = session()->get('qualifications', []);

            // Map qualifications to input fields
            $qualificationFields = [
                '5th' => '5th_marksheet',
                '8th' => '8th_marksheet',
                '10th' => 'ssc_marksheet',
                '12th' => 'inter_marksheet',
                'other' => 'other_marksheet',
            ];

            // Add validation rules for qualifications
            foreach ($qualifications as $qualification) {
                if (isset($qualificationFields[$qualification])) {
                    $field = $qualificationFields[$qualification];

                    // Special handling for other_marksheet (multiple files)
                    if ($field === 'other_marksheet') {
                        $rules[$field] = 'array';
                        $rules[$field . '.*'] = 'file|mimes:pdf|mimetypes:application/pdf|max:2048';
                    } else {
                        $rules[$field] = 'required|file|mimes:pdf|mimetypes:application/pdf|max:2048';
                    }
                }
            }

            // Optional fields
            $optionalFields = [
                'bpl_marksheet' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'widow_certificate' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'other_marksheet' => 'nullable|array',
                'other_marksheet.*' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
            ];

            // Remove optional rules for fields that are required
            foreach ($qualifications as $qualification) {
                if (isset($qualificationFields[$qualification])) {
                    unset($optionalFields[$qualificationFields[$qualification]]);
                    if ($qualificationFields[$qualification] === 'other_marksheet') {
                        unset($optionalFields['other_marksheet.*']);
                    }
                }
            }

            // Merge optional rules
            $rules = array_merge($rules, $optionalFields);

            $messages = [
                'document_photo.required' => 'पासपोर्ट साइज़ फोटो अपलोड करना अनिवार्य है।',
                'document_sign.required' => 'हस्ताक्षर अपलोड करना अनिवार्य है।',
                'domicile.required' => 'स्थानीय निवास प्रमाण पत्र अपलोड करना अनिवार्य है।',
                // 'epic_document.required' => 'अद्यतन मतदाता सूची अपलोड करना अनिवार्य है।',
                'caste_certificate.required' => 'जाति प्रमाण पत्र अपलोड करना अनिवार्य है।',
                '5th_marksheet.required' => '5th अंक सूची अपलोड करना अनिवार्य है।',
                '8th_marksheet.required' => '8th अंक सूची अपलोड करना अनिवार्य है।',
                'ssc_marksheet.required' => '10th अंक सूची अपलोड करना अनिवार्य है।',
                'inter_marksheet.required' => '12th अंक सूची अपलोड करना अनिवार्य है।',
                'other_marksheet.required' => 'अन्य प्रमाण पत्र अपलोड करना अनिवार्य है।',
                'other_marksheet.array' => 'अन्य प्रमाण पत्र अपलोड करना अनिवार्य है।',
                'document_photo.mimes' => 'कृपया पासपोर्ट साइज़ फोटो के लिए PNG, JPEG, JPG फाइल अपलोड करें।',
                'document_sign.mimes' => 'कृपया हस्ताक्षर के लिए PNG, JPEG, JPG फाइल अपलोड करें।',
                'document_adhaar.mimes' => 'कृपया आधार कार्ड के लिए PNG, JPEG, JPG,PDF फाइल अपलोड करें।',
                'domicile.mimes' => 'कृपया स्थानीय निवास प्रमाण पत्र के लिए PDF फाइल अपलोड करें।',
                'epic_document.mimes' => 'कृपया अद्यतन मतदाता सूची के लिए PDF फाइल अपलोड करें।',
                'caste_certificate.mimes' => 'कृपया जाति प्रमाण पत्र के लिए PDF फाइल अपलोड करें।',
                '5th_marksheet.mimes' => 'कृपया 5th अंक सूची के लिए PDF फाइल अपलोड करें।',
                '8th_marksheet.mimes' => 'कृपया 8th अंक सूची के लिए PDF फाइल अपलोड करें।',
                'ssc_marksheet.mimes' => 'कृपया 10th अंक सूची के लिए PDF फाइल अपलोड करें।',
                'inter_marksheet.mimes' => 'कृपया 12th अंक सूची के लिए PDF फाइल अपलोड करें।',
                'other_marksheet.*.mimes' => 'कृपया अन्य प्रमाण पत्र के लिए PDF फाइल अपलोड करें।',
                '*.mimes' => 'अमान्य फ़ाइल प्रारूप। कृपया स्वीकार्य फ़ाइल प्रकार PDF अपलोड करें।',
                '*.mimetypes' => 'अमान्य फ़ाइल MIME प्रकार। कृपया स्वीकार्य फ़ाइल प्रकार PDF अपलोड करें।',
                '*.max' => 'फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।',
                'other_marksheet.*.max' => 'प्रत्येक फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।',
            ];

            // Apply validation
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'कृपया सभी आवश्यक दस्तावेज़ अपलोड करें ।',
                    'errors' => $validator->errors(),
                ], 200);
            }

            // Define allowed file types and MIME types
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'pdf'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $baseUploadPath = 'Uploads/applicant/doc';

            // Array of document types and their corresponding model fields (excluding other_marksheet)
            $documents = [
                'document_photo' => 'Document_Photo',
                'document_sign' => 'Document_Sign',
                'document_adhaar' => 'Document_Aadhar',
                'caste_certificate' => 'Document_Caste',
                'domicile' => 'Document_Domicile',
                '5th_marksheet' => 'Document_5th',
                '8th_marksheet' => 'Document_8th',
                'ssc_marksheet' => 'Document_SSC',
                'inter_marksheet' => 'Document_Inter',
                'bpl_marksheet' => 'Document_BPL',
                'widow_certificate' => 'Document_Widow',
                'exp_document' => 'Document_Exp',
                'epic_document' => 'Document_Epic'
            ];

            $documents_user = [];
            // Get applicant ID and apply ID from session
            $Applicant_id = session()->get('user_row_id');
            $apply_id = session()->get('apply_id');

            // Process each document type (excluding other_marksheet)
            foreach ($documents as $inputName => $fieldName) {
                if ($request->hasFile($inputName)) {
                    $uploadedFile = $request->file($inputName);
                    $uploadResult = UtilController::upload_file(
                        $uploadedFile,
                        $inputName,
                        'uploads',
                        $allowedExtensions,
                        $allowedMimeTypes
                    );

                    if (!$uploadResult) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => "Unable to save {$inputName} file!",
                            'errors' => [$inputName => ["Unable to save {$inputName} file."]]
                        ], 422, [], JSON_UNESCAPED_UNICODE);
                    }

                    // Save path to array
                    $documents_user[$fieldName] = $uploadResult;
                }
            }

            // Special handling for other_marksheet (multiple files)
            $otherDocumentsData = [];
            if ($request->hasFile('other_marksheet')) {
                $otherFiles = $request->file('other_marksheet');

                // Ensure it's always an array
                if (!is_array($otherFiles)) {
                    $otherFiles = [$otherFiles];
                }

                foreach ($otherFiles as $file) {
                    if ($file && $file->isValid()) {
                        $uploadResult = UtilController::upload_file(
                            $file,
                            'other_marksheet_' . uniqid(),
                            'uploads',
                            ['pdf'], // Only PDF allowed
                            ['application/pdf'] // Only PDF MIME type
                        );

                        if (!$uploadResult) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Unable to save other marksheet file!',
                                'errors' => ['other_marksheet' => ['Unable to save other marksheet file.']]
                            ], 422, [], JSON_UNESCAPED_UNICODE);
                        }

                        // Store for batch insert
                        $otherDocumentsData[] = [
                            'other_documents' => $uploadResult,
                            'fk_applicant_id' => $Applicant_id,
                            'file_name' => $file->getClientOriginalName(),
                            'created_at' => now(),
                            'create_ip' => $request->ip()
                        ];
                    }
                }
            }


            $allInserted = false;

            // Save Documents, post question and skill data
            if (!empty($Applicant_id) && !empty($apply_id)) {
                // Update with uploaded document paths in tbl_user_detail
                if (!empty($documents_user)) {
                    DB::table('tbl_user_detail')
                        ->where('ID', $Applicant_id)
                        ->update($documents_user);
                }

                // Save other documents to new table
                if (!empty($otherDocumentsData)) {
                    DB::table('tbl_user_other_documents')->insert($otherDocumentsData);
                }

                DB::update('UPDATE tbl_user_post_apply SET stepCount = 5 WHERE apply_id = ?', [$apply_id]);
                $allInserted = true;
            }

            if ($allInserted) {
                DB::commit();
                Session::forget('apply_data');
                Session::forget('Questions_answers');
                Session::forget('Questions_file_paths');
                Session::forget('fk_question_ids');
                Session::forget('user_documents');
                Session::forget('Experience_required');
                Session::forget('Widow_Divorce_required');
                Session::forget('BPL_required');

                return response()->json([
                    'status' => 'success',
                    'message' => 'आवेदन पूर्ण कर लिया गया हैं कृपया अंतिम बार विवरण जाँच करके आवेदन जमा करे।',
                    'applicant_id' => md5($Applicant_id),
                    'application_id' => md5($apply_id),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'डेटा सहेजने में त्रुटि हुई। कृपया पुनः प्रयास करें।',
                    'errors' => ['database' => ['Failed to save applicant details.']]
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(),
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function add_user(Request $request, $id = 0)
    {
        $maxdate = date('Y-m-d', strtotime('-18 years'));
        if ($request->isMethod('post')) {

            $status = 'failed';
            $errors = null;
            $redirct_url = "";

            // Validation rules
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'mob' => 'required|numeric|digits:10|regex:/^[6-9]\d{9}$/',
                    'dob' => 'required|date|before_or_equal:' . $maxdate,
                    'captcha' => 'required|captcha',
                    'adhaar' => 'required|digits:12',
                    'AdharConfirm' => 'required|in:1'
                ],
                [
                    'captcha.captcha' => 'कैप्चा मान्य नहीं है, इसे दोबारा भरें ।',
                    'name.regex' => 'नाम फ़ील्ड में केवल अंग्रेज़ी अक्षर, खाली स्थान, डैश (-), डॉट (.), कॉमा (,) की अनुमति है।',
                    'mob.digits' => 'मोबाइल नंबर 10 अंक का होना चाहिए।',
                    'mob.regex' => 'मोबाइल नंबर 6-9 से शुरू होना चाहिए।',
                    'mob' => 'मोबाइल नंबर आवश्यक है।',
                    'dob.before_or_equal' => 'केवल वे उम्मीदवार पंजीकरण के पात्र हैं जिनकी जन्मतिथि 30 दिसंबर 2005 या उससे पहले की है।',
                    'adhaar.required' => 'आधार नंबर अनिवार्य है।',
                    'adhaar.digits' => 'आधार नंबर 12 अंक का होना चाहिए।',
                    'AdharConfirm.required' => 'आधार नंबर प्रस्तुत सहमति अनिवार्य है, कृपया चेकबॉक्स चुनें।',

                ]
            );

            if ($validator->fails()) {
                $captcha = (new CaptchaServiceController)->reloadCaptcha();
                return response()->json([
                    'status' => 'failed',
                    'msg' => 'Validation error',
                    'errors' => $validator->errors(),
                    'captcha' => $captcha->original['captcha']
                ]);
            }
            $checkMobile = User::where('Mobile_Number', $request['mob'])->exists();
            if ($checkMobile) {
                $captcha = (new CaptchaServiceController)->reloadCaptcha();
                return response()->json([
                    'message' => 'दर्ज किया गया मोबाइल नंबर पहले से मौजूद है |',
                    'status' => 'error',
                    'captcha' => $captcha->original['captcha']
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }

            $requestAdhaar = $request['adhaar'];

            // Extract last 4 digits from the input
            $last4Digits = substr($requestAdhaar, -4);

            // Check if any user has an Aadhaar number ending with these 4 digits
            $checkAdhaar = User::whereRaw('reference_no = ?', [$requestAdhaar])->exists();
            // $checkAdhaarInUserdetails = DB::table('tbl_user_detail')->whereRaw('SUBSTRING(reference_no, -4) = ?', [$last4Digits])->exists();
            // if ($checkAdhaar || $checkAdhaarInUserdetails) {

            if ($checkAdhaar) {
                $captcha = (new CaptchaServiceController)->reloadCaptcha();
                return response()->json([
                    'message' => 'दर्ज किया गया आधार नंबर पहले से मौजूद है |',
                    'status' => 'error',
                    'captcha' => $captcha->original['captcha']
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
            try {
                // Process DOB for password
                list($year, $month, $day) = explode('-', $request['dob']);
                $password = $day . $month . $year;
                $hashedPassword = hash('sha256', $password);
                $Name_uppercase = strtoupper($request['name']);
                $masked_adhaar = substr_replace($request->adhaar, str_repeat("X", 8), 0, 8);

                $user_data = [
                    'Role' => 'Candidate',
                    'Applicant_ID' => '',
                    'Full_Name' => $Name_uppercase,
                    'Mobile_Number' => $request['mob'],
                    'Date_Of_Birth' => $request['dob'],
                    'Password' => $hashedPassword,
                    'adhaar' => $requestAdhaar,
                    'AdharConfirm' => $request['AdharConfirm'] ?? 0,
                    'IP_Address' => $request->ip(),
                    'Created_On' => now(),
                ];

                session(['pending_user_data' => $user_data]);

                session(['sess_mobile' => $request['mob']]);

                $mobile_number = $request->mob;
                $encryptedMobile = Crypt::encryptString($mobile_number);

                return response()->json([
                    'status' => 'success',
                    'message' => 'डेटा सफलतापूर्वक दर्ज कर लिया गया हैं |',
                    'encrypted_mobile' => $encryptedMobile
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } catch (\Throwable $th) {
                DB::rollBack();

                // If validation error thrown by user code
                if ($th instanceof \Exception) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $th->getMessage(), // <-- Swal will show this
                    ], 400, [], JSON_UNESCAPED_UNICODE);
                }

                // Default server exception
                return response()->json([
                    'status' => 'error',
                    'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                    'errors' => ['server' => [$th->getMessage()]]
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // $maxdate = "2005-12-30";
            // $maxdate = date('Y-m-d', strtotime('-18 years'));
            $dist = DB::table('master_district')->get();
            return view('user.add_user', compact('maxdate', 'dist'));
        }
    }

    public function Otp_Verify($encryptedMobile = null, Request $request)
    {
        // Send OTP
        if ($encryptedMobile) {

            try {
                $mobile_number = Crypt::decryptString($encryptedMobile);

                $query = User::where('Mobile_Number', $mobile_number);
                $userCheck = $query->get();
                $request->session()->put([
                    'sess_id' => $userCheck[0]->ID,
                    'uid' => $userCheck[0]->ID,
                    'sess_fname' => $userCheck[0]->Full_Name,
                    'sess_mobile' => $userCheck[0]->Mobile_Number,
                    'sess_role' => $userCheck[0]->Role,
                    'admin_pic' => $userCheck[0]->admin_pic,
                ]);
            } catch (\Exception $e) {
                return redirect()->route('login')->with('error', 'लिंक अमान्य है या छेड़छाड़ की गई है।');
            }

            session()->forget('sess_mobile');
            $otp = OtpHelper::generateAndStoreOtp('mobile', $mobile_number);
            (new OtpHelper)->sendOtp($mobile_number, $otp);
            return view('user.verify_otp', ['Registerd_Mob_Number' => "$mobile_number"]);
        }

        return view('user.verify_otp');
    }

    public function reg_Otp_Verify($encryptedMobile = null, Request $request)
    {
        if ($encryptedMobile) {
            try {
                $mobile_number = Crypt::decryptString($encryptedMobile);

                // Check if pending user data exists in session
                $pending_user_data = session('pending_user_data');
                if (!$pending_user_data || $pending_user_data['Mobile_Number'] !== $mobile_number) {
                    return redirect()->route('login')->with('error', 'अमान्य सत्र डेटा।');
                }

                // Generate and send OTP
                $otp = OtpHelper::generateAndStoreOtp('mobile', $mobile_number);
                (new OtpHelper)->sendOtp($mobile_number, $otp);

                // Clear sess_mobile if needed
                session()->forget('sess_mobile');

                return view('user.verify_otp', ['Registerd_Mob_Number' => $mobile_number]);
            } catch (\Exception $e) {
                return redirect()->route('login')->with('error', 'लिंक अमान्य है या छेड़छाड़ की गई है।');
            }
        }

        return view('user.verify_otp');
    }



    public function login_Otp_Verify(Request $request, $encryptedMobile = null)
    {
        $st_login = "Super_admin";
        $isSuperAdmin = session('is_super_admin', false);
        $mobile_number = '';
        $session_token = null;

        if ($encryptedMobile) {
            try {
                $mobile_number = Crypt::decryptString($encryptedMobile);

                if (!$isSuperAdmin) {
                    // Generate and send OTP for normal flow
                    $otp = OtpHelper::generateAndStoreOtp('mobile', $mobile_number);
                    (new OtpHelper)->sendOtp($mobile_number, $otp);
                    // $isSuperAdmin = false; // Ensure flag is false for normal users
                }

                // Clear sess_mobile if needed && Store Login Key
                session()->forget('sess_mobile');
            } catch (\Exception $e) {
                return redirect()->route('login')->with('error', 'लिंक अमान्य है या छेड़छाड़ की गई है।');
            }
        }
        if (!$mobile_number) {
            $superAdminUser = DB::table('master_user')
                ->where('Mobile_Number', $st_login)
                ->first();

            if (!$superAdminUser) {
                return redirect()->route('login')->with('error', 'सुपर एडमिन उपयोगकर्ता नहीं मिला।');
            }

            $role = $superAdminUser->Role;
            $isSuperAdmin = true; // Set flag for super admin login

            // Ensure direct /st-login sets required session flags to bypass pending-login checks
            session([
                'is_super_admin' => true,
                'AlreadyLogin' => 0,
                'UserCheck' => $superAdminUser->ID,
                'st_login_skip_token' => true, // do not invalidate other device sessions for this URL
            ]);
        } else {
            $role = DB::table('master_user')
                ->where('Mobile_Number', $mobile_number)
                ->value('Role');
        }

        if ($role) {
            session()->forget('loginRole');
            session([
                'login' => '1',
                'loginRole' => $role
            ]);
        }
        $registered_mobile = empty($mobile_number) ? $st_login : $mobile_number;
        // dd($isSuperAdmin);
        return view('user.verify_otp', [
            'Registerd_Mob_Number' => $registered_mobile,
            'is_super_admin' => $isSuperAdmin,
        ]);


        // return view('user.verify_otp', ['is_super_admin' => $isSuperAdmin]);
    }
    public function verify_user_otp(Request $request)
    {
        $isSuperAdmin = session('is_super_admin', false);

        if ($isSuperAdmin) {
            $request->validate([
                'mobile' => 'required',
                'password' => 'required'
            ]);

            $user = DB::table('master_user')->where('Mobile_Number', $request->mobile)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'यूज़र नहीं मिला।',
                    'status' => 'error'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $salt = Config::get('salt.STATIC_SALT');
            $hashedInput = hash('sha512', $salt . $request->password . $salt);

            if ($hashedInput !== $user->Password) {
                return response()->json([
                    'message' => 'पासवर्ड गलत है।',
                    'status' => 'error'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            // Successful password auth: finalize login similar to admin OTP success flow
            if (session('AlreadyLogin') === 0) {
                $loginController = new LoginController();
                $loginController->finalizeLogin($user);
                Session::save();

                // Clear flag after success
                session()->forget('is_super_admin');

                $redirect_url = $loginController->role_wise_redirection();
                return response()->json([
                    'message' => 'लॉगिन सफल।',
                    'status' => 'success',
                    'url' => $redirect_url
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                $confirmUrl = route('login.confirm');
                return response()->json([
                    'message' => '',
                    'status' => 'confirm',
                    'confirm_url' => $confirmUrl
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        $request->validate([
            'mobile' => 'required',
            'otp' => 'required'
        ]);

        // Call OTP helper and decode the JsonResponse to stdClass
        $otpVerificationResponse = OtpHelper::verifyOtp('mobile', $request->mobile, $request->otp);
        $otpVerification = $otpVerificationResponse->getData(); // decode to object

        $login_key = session('login');

        if ($otpVerification->status === 'success' && session('login') == 1 && session('loginRole')) {


            if (session('loginRole') === 'Candidate') {
                return response()->json([
                    'message' => 'OTP सत्यापित हो गया है।',
                    'status' => 'success',
                    'url' => '/candidate/candidate-dashboard'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                // Admin Login Process

                if (session('AlreadyLogin') === 0) {
                    $loginController = new LoginController();

                    $userRecord = User::findOrFail(session('UserCheck'));

                    $loginController->finalizeLogin($userRecord);

                    Session::save();

                    $redirect_url = $loginController->role_wise_redirection();
                    $status = 'success';
                    $msg = 'सफलतापूर्वक लॉग इन हो गया है।';
                } else {

                    // dd('already login confirm');
                    $confirmUrl = route('login.confirm');
                    return response()->json([
                        'message' => '',
                        'status' => 'confirm',
                        'confirm_url' => $confirmUrl
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            }
        } else if ($otpVerification->status === 'success') {
            DB::beginTransaction();
            try {
                // Retrieve user data from session
                $pending_user_data = session('pending_user_data');

                if (!$pending_user_data || $pending_user_data['Mobile_Number'] !== $request->mobile) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'अमान्य सत्र डेटा।',
                        'status' => 'error'
                    ], 400, [], JSON_UNESCAPED_UNICODE);
                }

                // Save user data to database
                $user = new User();
                $user->Role = $pending_user_data['Role'];
                $user->Applicant_ID = $pending_user_data['Applicant_ID'];
                $user->Full_Name = $pending_user_data['Full_Name'];
                $user->Mobile_Number = $pending_user_data['Mobile_Number'];
                $user->Date_Of_Birth = $pending_user_data['Date_Of_Birth'];
                $user->Password = $pending_user_data['Password'];
                $user->reference_no = $pending_user_data['adhaar'];
                $user->AdharConfirm = $pending_user_data['AdharConfirm'];
                $user->IP_Address = $pending_user_data['IP_Address'];
                $user->Created_On = $pending_user_data['Created_On'];
                $user_status = $user->save();

                if ($user_status) {
                    // Store user session data
                    $request->session()->put([
                        'sess_id' => $user->ID,
                        'uid' => $user->ID,
                        'sess_fname' => $user->Full_Name,
                        'sess_mobile' => $user->Mobile_Number,
                        'sess_role' => $user->Role,
                        'admin_pic' => $user->admin_pic,
                    ]);

                    // Clear pending user data from session
                    session()->forget('pending_user_data');

                    DB::commit();
                    return response()->json([
                        'message' => 'OTP सत्यापन सफल। उपयोगकर्ता पंजीकरण पूर्ण।',
                        'status' => 'success'
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'उपयोगकर्ता डेटा सहेजने में त्रुटि।',
                        'status' => 'error'
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'सत्यापन के दौरान त्रुटि: ' . $e->getMessage(),
                    'status' => 'error'
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } else if ($otpVerification->status === 'error') {

            return response()->json([
                'message' => $otpVerification->message,
                'status' => 'error'
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'message' => $otpVerification->message ?? 'अमान्य OTP।',
            'status' => 'error'
        ], 400, [], JSON_UNESCAPED_UNICODE);
    }




    public function resendOtp(Request $request)
    {

        $otp = OtpHelper::generateAndStoreOtp('mobile', $request['mobile']);
        (new OtpHelper)->sendOtp($request['mobile'], $otp);

        return response()->json([
            'message' => 'ओटीपी सफलतापूर्वक पुनः भेज दिया गया है।',
            'status' => 'success'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function index_home()
    {

        $data = DB::table('master_advertisement as am')
            ->select(
                'am.Advertisement_ID',
                'am.Advertisement_Title as Advertisement_Title',
                'am.Advertisement_Date as Start_date',
                'am.Date_For_Age as End_date',
                'am.Advertisement_Description as Description',
                'am.Advertisement_Document'
            )
            ->whereDate('am.Advertisement_Date', '<=', now())
            ->whereDate('am.Date_For_Age', '>=', now())
            ->groupBy(
                'am.Advertisement_ID',
                'am.Advertisement_Title',
                'am.Advertisement_Date',
                'am.Date_For_Age',
                'am.Advertisement_Document'
            )
            ->get();

        return view('index', compact('data'));
    }

    public function notice_advertiesment()
    {

        $data = DB::table('master_advertisement as am')
            ->select(
                'am.Advertisement_ID',
                'am.Advertisement_Title as Advertisement_Title',
                'am.Advertisement_Date as Start_date',
                'am.Date_For_Age as End_date',
                'am.Advertisement_Description as Description',
                'am.Advertisement_Document',
                'md.name as district_name',
                'mp.project as project_name',
                DB::raw("
                        CASE
                            WHEN CURDATE() BETWEEN am.Advertisement_Date AND am.Date_For_Age THEN 'Active'
                            WHEN am.Advertisement_Date > CURDATE() THEN 'Upcoming'
                            WHEN am.Date_For_Age < CURDATE() THEN 'Expired'
                            ELSE 'unknown'
                        END AS apply_status
                    ")
            )
            ->leftJoin('master_district as md', 'am.district_lgd_code', '=', 'md.District_Code_LGD')
            ->leftJoin('master_projects as mp', 'am.project_code', '=', 'mp.project_code')
            ->where('am.is_disable', 1)
            ->groupBy(
                'am.Advertisement_ID',
                'am.Advertisement_Title',
                'am.Advertisement_Date',
                'am.Date_For_Age',
                'am.Advertisement_Description',
                'am.Advertisement_Document',
                'md.name',
                'mp.project_code',
            )
            ->get();

        // $districts = DB::table('master_district')
        //     ->select('District_Code_LGD as id', 'name')
        //     ->orderBy('name')
        //     ->get();

        // // Fetch all projects
        // $projects = DB::table('master_projects')
        //     ->select('project_code as id', 'project as name')
        //     ->orderBy('project')
        //     ->get();

        // dd($projects);
        return view('new_advertiesments', compact('data'));
    }


    public function bharti($advertiesment_id = null)
    {
        if ($advertiesment_id) {

            $data = DB::select("SELECT 
                                                pm.post_id,
                                                am.Advertisement_Title,
                                                pm.title AS Post_Title,
                                                am.Advertisement_Date AS Start_date,
                                                am.Date_For_Age AS End_date,
                                                am.Advertisement_Description AS Description,
                                                md.name AS district_name,
                                                pm.File_Path,
                                                am.Advertisement_Document,

                                            -- Handle multiple project_code (comma-separated or JSON)
                                            GROUP_CONCAT(DISTINCT projects.project ORDER BY projects.project SEPARATOR ', ') AS project_names,
                                            
                                            -- Handle multiple std_nnn_code (JSON array) - get city names
                                            GROUP_CONCAT(DISTINCT nagar.nnn_name ORDER BY nagar.nnn_name SEPARATOR ', ') AS nnn_names,
                                            
                                            -- Get block names through panchayat join
                                            GROUP_CONCAT(DISTINCT blocks.block_name_hin ORDER BY blocks.block_name_hin SEPARATOR ', ') AS block_names,
                                            
                                            -- Handle ward IDs (now contains master_ward.ID values)
                                            GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ', ') AS ward_names,
                                            
                                            -- Panchayat names
                                            GROUP_CONCAT(DISTINCT panchayat.panchayat_name_hin ORDER BY panchayat.panchayat_name_hin SEPARATOR ', ') AS panchayat_names,
                                            
                                            -- Village names
                                            GROUP_CONCAT(DISTINCT master_villages.village_name_hin ORDER BY master_villages.village_name_hin SEPARATOR ', ') AS villages_names,
                                          
                                            CASE
                                                    WHEN CURDATE() BETWEEN am.Advertisement_Date AND am.Date_For_Age THEN 'Active'
                                                    WHEN am.Advertisement_Date > CURDATE() THEN 'Upcoming'
                                                    WHEN am.Date_For_Age < CURDATE() THEN 'Expired'
                                                    ELSE 'unknown'
                                            END AS apply_status

                                            FROM master_post pm 
                                            INNER JOIN master_advertisement am ON pm.Advertisement_ID = am.Advertisement_ID
                                            LEFT JOIN master_district md ON am.district_lgd_code = md.District_Code_LGD
                                            

                                        -- Handle multiple std_nnn_code (JSON array) for cities
                                        LEFT JOIN JSON_TABLE(
                                            pm.std_nnn_code,
                                            '$[*]' COLUMNS (
                                                std_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS std_codes ON 1=1
                                        LEFT JOIN master_nnn nagar ON std_codes.std_code = nagar.std_nnn_code
                                        
                                        -- Handle multiple project_code (comma-separated or JSON)
                                        LEFT JOIN JSON_TABLE(
                                            CONCAT('[\"', REPLACE(pm.project_code, ',', '\",\"'), '\"]'),
                                            '$[*]' COLUMNS (
                                                proj_code VARCHAR(50) PATH '$'
                                            )
                                        ) AS proj_codes ON 1=1
                                        LEFT JOIN master_projects projects ON TRIM(proj_codes.proj_code) = projects.project_code
                                        
                                        -- Handle multiple gp_nnn_code (JSON array) for panchayats
                                        LEFT JOIN JSON_TABLE(
                                            pm.gp_nnn_code,
                                            '$[*]' COLUMNS (
                                                gp_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS gp_codes ON 1=1
                                        LEFT JOIN master_panchayats panchayat ON gp_codes.gp_code = panchayat.panchayat_lgd_code
                                        LEFT JOIN master_blocks blocks ON panchayat.block_lgd_code = blocks.block_lgd_code
                                        
                                        -- Handle multiple village_code (JSON array)
                                        LEFT JOIN JSON_TABLE(
                                            pm.village_code,
                                            '$[*]' COLUMNS (
                                                village_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS village_codes ON 1=1
                                        LEFT JOIN master_villages ON village_codes.village_code = master_villages.village_code
                                        
                                        -- Handle ward IDs (now contains master_ward.ID values as JSON array)
                                        LEFT JOIN JSON_TABLE(
                                            pm.ward_no,
                                            '$[*]' COLUMNS (
                                                ward_id INT PATH '$'
                                            )
                                        ) AS ward_ids ON 1=1
                                        LEFT JOIN master_ward ON ward_ids.ward_id = master_ward.ID
                                        WHERE MD5(am.Advertisement_ID) = ? AND pm.is_disable = 1

                                            GROUP BY pm.post_id", [$advertiesment_id]);
        } else {

            $data = DB::select("SELECT 
                                            pm.post_id,
                                            am.Advertisement_Title,
                                            pm.title AS Post_Title,
                                            am.Advertisement_Date AS Start_date,
                                            am.Date_For_Age AS End_date,
                                            am.Advertisement_Description AS Description,
                                            md.name AS district_name,
                                            pm.File_Path,
                                            am.Advertisement_Document,

                                            -- Handle multiple project_code (comma-separated or JSON)
                                            GROUP_CONCAT(DISTINCT projects.project ORDER BY projects.project SEPARATOR ', ') AS project_names,
                                            
                                            -- Handle multiple std_nnn_code (JSON array) - get city names
                                            GROUP_CONCAT(DISTINCT nagar.nnn_name ORDER BY nagar.nnn_name SEPARATOR ', ') AS nnn_names,
                                            
                                            -- Get block names through panchayat join
                                            GROUP_CONCAT(DISTINCT blocks.block_name_hin ORDER BY blocks.block_name_hin SEPARATOR ', ') AS block_names,
                                            
                                            -- Handle ward IDs (now contains master_ward.ID values)
                                            GROUP_CONCAT(DISTINCT CONCAT(master_ward.ward_name, ' (', master_ward.ward_no, ')') ORDER BY master_ward.ward_name SEPARATOR ', ') AS ward_names,
                                            
                                            -- Panchayat names
                                            GROUP_CONCAT(DISTINCT panchayat.panchayat_name_hin ORDER BY panchayat.panchayat_name_hin SEPARATOR ', ') AS panchayat_names,
                                            
                                            -- Village names
                                            GROUP_CONCAT(DISTINCT master_villages.village_name_hin ORDER BY master_villages.village_name_hin SEPARATOR ', ') AS villages_names,
                                          
                                            CASE
                                                WHEN CURDATE() BETWEEN am.Advertisement_Date AND am.Date_For_Age THEN 'Active'
                                                WHEN am.Advertisement_Date > CURDATE() THEN 'Upcoming'
                                                WHEN am.Date_For_Age < CURDATE() THEN 'Expired'
                                                ELSE 'unknown'
                                            END AS apply_status

                                        FROM master_post pm 
                                        INNER JOIN master_advertisement am  ON pm.Advertisement_ID = am.Advertisement_ID
                                        LEFT JOIN master_district md  ON am.district_lgd_code = md.District_Code_LGD

                                        -- Handle multiple std_nnn_code (JSON array) for cities
                                        LEFT JOIN JSON_TABLE(
                                            pm.std_nnn_code,
                                            '$[*]' COLUMNS (
                                                std_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS std_codes ON 1=1
                                        LEFT JOIN master_nnn nagar ON std_codes.std_code = nagar.std_nnn_code
                                        
                                        -- Handle multiple project_code (comma-separated or JSON)
                                        LEFT JOIN JSON_TABLE(
                                            CONCAT('[\"', REPLACE(pm.project_code, ',', '\",\"'), '\"]'),
                                            '$[*]' COLUMNS (
                                                proj_code VARCHAR(50) PATH '$'
                                            )
                                        ) AS proj_codes ON 1=1
                                        LEFT JOIN master_projects projects ON TRIM(proj_codes.proj_code) = projects.project_code
                                        
                                        -- Handle multiple gp_nnn_code (JSON array) for panchayats
                                        LEFT JOIN JSON_TABLE(
                                            pm.gp_nnn_code,
                                            '$[*]' COLUMNS (
                                                gp_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS gp_codes ON 1=1
                                        LEFT JOIN master_panchayats panchayat ON gp_codes.gp_code = panchayat.panchayat_lgd_code
                                        LEFT JOIN master_blocks blocks ON panchayat.block_lgd_code = blocks.block_lgd_code
                                        
                                        -- Handle multiple village_code (JSON array)
                                        LEFT JOIN JSON_TABLE(
                                            pm.village_code,
                                            '$[*]' COLUMNS (
                                                village_code VARCHAR(20) PATH '$'
                                            )
                                        ) AS village_codes ON 1=1
                                        LEFT JOIN master_villages ON village_codes.village_code = master_villages.village_code
                                        
                                        -- Handle ward IDs (now contains master_ward.ID values as JSON array)
                                        LEFT JOIN JSON_TABLE(
                                            pm.ward_no,
                                            '$[*]' COLUMNS (
                                                ward_id INT PATH '$'
                                            )
                                        ) AS ward_ids ON 1=1
                                        LEFT JOIN master_ward ON ward_ids.ward_id = master_ward.ID WHERE pm.is_disable = 1 
                                        GROUP BY pm.post_id");
        }
        return view('new_bharti', compact('data'));
    }




    ###===============New Changes Branch ======================####

    public function user_details_form(Request $request, $appID, $is_update = null)
    {
        $tr = new GoogleTranslate();
        $tr->setSource('en');
        $tr->setTarget('hi');

        $userId = Session::get('sess_id');
        $user = DB::selectOne("SELECT Full_Name, Date_Of_Birth FROM master_user WHERE ID = ?", [$userId]);

        $Full_Name = $user->Full_Name ?? null;
        $user_dob = $user->Date_Of_Birth ?? null;

        // Calculate user age
        $user_age = $user_dob ? date_diff(date_create($user_dob), date_create('2025-01-01'))->y : null;

        // Debugging age
        if (is_null($user_age)) {
            return back()->with('error', 'Date of Birth is missing in your profile.');
        }


        $data['master_user'] = DB::table('master_user')
            ->select('reference_no', 'AdharConfirm')
            ->where('ID', $userId)
            ->first();

        $data['cities'] = DB::select("SELECT District_Code_LGD, name as Dist_name FROM master_district ORDER BY name ASC");

        $data['projects'] = DB::select("SELECT district_lgd_code, project, project_code FROM master_projects ORDER BY project ASC");

        $data['area'] = DB::select("SELECT * FROM master_area");

        $data['block'] = DB::select("SELECT block_lgd_code, block_name, block_name_hin, district_lgd_code FROM master_blocks ORDER BY block_name_hin ASC"); // rular in case
        $data['gp'] = DB::select("SELECT panchayat_lgd_code, panchayat_name, panchayat_name_hin, block_lgd_code FROM master_panchayats ORDER BY panchayat_name_hin ASC");


        $data['nagar'] = DB::select("SELECT district_lgd_code, std_nnn_code, nnn_name, nnn_name_en FROM master_nnn ORDER BY nnn_name ASC"); // Urban in case
        $data['ward'] = DB::select("SELECT std_nnn_code, ward_no, ID, ward_name FROM master_ward ORDER BY ward_no ASC");


        $data['master_post'] = DB::select("SELECT * FROM master_post WHERE 1=1  ORDER BY post_id ASC");


        $data['maxdate'] = date('Y-m-d');
        $data['user_dob'] = $user_dob;

        // $data['minyear'] = \Carbon\Carbon::parse($data['user_dob'])->format('Y');
        $data['minyear'] = \Carbon\Carbon::parse($data['user_dob'])->addYears(value: 7)->format('Y');
        $data['maxyear'] = \Carbon\Carbon::parse(date('d-m-Y'))->format('Y');


        $subjects = DB::select('SELECT * FROM master_subjects ORDER BY fk_Quali_ID ASC');
        $grades = DB::select('SELECT * FROM master_grades');
        $master_caste = DB::select('SELECT * FROM master_tbl_caste');
        $master_gender = DB::select('SELECT * FROM master_tbl_gender');
        $organization_type = DB::select('SELECT * FROM master_tbl_organization_type');
        $master_qualification = DB::select('SELECT * FROM master_qualification');
        //  $educationDetails = DB::select('SELECT * FROM tbl_applicant_education_qualification');

        $parts = explode(' ', trim($Full_Name));
        if (count($parts) === 1) {
            // Agar sirf ek naam hai toh first name set hoga, last name blank hoga
            $firstName = $parts[0];
            $middleName = '';
            $lastName = '';
        } elseif (count($parts) === 2) {
            // Agar do naam hain toh first name aur last name set hoga, middle name blank hoga
            $firstName = $parts[0];
            $middleName = '';
            $lastName = $parts[1];
        } elseif (count($parts) === 3) {
            // Agar 3 naam hain toh first name aur last name set hoga, middle name set hoga
            $firstName = $parts[0];
            $middleName = $parts[1];
            $lastName = $parts[2];
        } else {
            // Agar 3 ya usse zyada words hain toh first, middle, aur last name assign hoga
            $firstName = $parts[0]; // Pehla word first name hoga
            $lastName = array_pop($parts); // Aakhri word last name hoga
            $middleName = implode(' ', $parts); // Beech ka sab kuch middle name hoga
        }
        $firstName_hindi = '';
        $middleName_hindi = '';
        $lastName_hindi = '';
        if ($parts) {
            $firstName_hindi = $tr->translate($firstName ?? '');
            $middleName_hindi = $tr->translate($middleName ?? '');
            $lastName_hindi = $tr->translate($lastName ?? '');
        }

        //For Edit Awc Form 
        if ($is_update == "update") {

            $data['applicant_details'] = DB::table('tbl_user_detail')
                ->select(
                    'tbl_user_detail.Applicant_ID AS RowID',
                    'tbl_user_detail.*',
                    'master_user.*',
                    'tbl_user_post_apply.*',
                )
                ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
                ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
                ->whereRaw('MD5(tbl_user_detail.Applicant_ID) = ?', [$appID])
                ->where(' tbl_user_post_apply.apply_id = ?', [$appID])
                ->first();

            $data['educationDetails'] = ApplicantEducationQualification::where('fk_applicant_id', $data['applicant_details']->RowID)->get();

            // Fetch multiple experiences separately
            $data['experience_details'] = DB::table('tbl_applicant_experience_details')
                ->where('Applicant_ID', $data['applicant_details']->RowID)
                ->get();
            $experience_count = $data['experience_details']->count();
            $data['experience_count'] = $experience_count;
        }


        return view('candidate/user_details_form', compact(
            'data',
            'subjects',
            'master_caste',
            'master_gender',
            'organization_type',
            'master_qualification',
            'user_age',
            'grades',
            'firstName_hindi',
            'middleName_hindi',
            'lastName_hindi'
        ));
    }


    public function user_details_update(Request $request, $Candidate_id = 0, $Apply_id = 0)
    {
        $tr = new GoogleTranslate();
        $tr->setSource('en');
        $tr->setTarget('hi');

        // $appID = $Candidate_id;
        $userId = Session::get('sess_id');
        $user_dob = DB::selectOne("SELECT Full_Name, Date_Of_Birth FROM master_user WHERE ID = ?", [$userId]);

        // Extract Date_Of_Birth properly
        $user_dob = $user_dob->Date_Of_Birth ?? null;
        $Full_Name = $user_dob->Full_Name ?? null;

        // Calculate user age
        $user_age = $user_dob ? date_diff(date_create($user_dob), date_create('today'))->y : null;

        // Debugging age
        if (is_null($user_age)) {
            return back()->with('error', 'Date of Birth is missing in your profile.');
        }

        $parts = explode(' ', trim($Full_Name));

        if (count($parts) === 1) {
            // Agar sirf ek naam hai toh first name set hoga, last name blank hoga
            $firstName = $parts[0];
            $middleName = '';
            $lastName = '';
        } elseif (count($parts) === 2) {
            // Agar do naam hain toh first name aur last name set hoga, middle name blank hoga
            $firstName = $parts[0];
            $middleName = '';
            $lastName = $parts[1];
        } elseif (count($parts) === 3) {
            // Agar 3 naam hain toh first name aur last name set hoga, middle name set hoga
            $firstName = $parts[0];
            $middleName = $parts[1];
            $lastName = $parts[2];
        } else {
            // Agar 3 ya usse zyada words hain toh first, middle, aur last name assign hoga
            $firstName = $parts[0]; // Pehla word first name hoga
            $lastName = array_pop($parts); // Aakhri word last name hoga
            $middleName = implode(' ', $parts); // Beech ka sab kuch middle name hoga
        }
        $firstName_hindi = '';
        $middleName_hindi = '';
        $lastName_hindi = '';
        if ($parts) {
            $firstName_hindi = $tr->translate($firstName ?? '');
            $middleName_hindi = $tr->translate($middleName ?? '');
            $lastName_hindi = $tr->translate($lastName ?? '');
        }

        $data['cities'] = DB::select("SELECT District_Code_LGD, name as Dist_name FROM master_district ORDER BY name ASC");

        $data['projects'] = DB::select("SELECT district_lgd_code, project, project_code FROM master_projects ORDER BY project ASC");

        $data['area'] = DB::select("SELECT * FROM master_area");

        $data['block'] = DB::select("SELECT blocks.block_lgd_code, blocks.block_name, blocks.block_name_hin, blocks.district_lgd_code FROM master_blocks blocks
                                            LEFT JOIN tbl_user_post_apply apply ON blocks.district_lgd_code=apply.fk_district_id 
                                            WHERE MD5(apply.apply_id) =? ORDER BY block_name_hin ASC", [$Apply_id]); // rular in case

        $data['gp'] = DB::select("SELECT 
                                            gp.panchayat_lgd_code, 
                                            gp.panchayat_name, 
                                            gp.panchayat_name_hin, 
                                            gp.block_lgd_code 
                                         FROM  master_panchayats gp
                                         LEFT JOIN master_blocks blocks ON gp.block_lgd_code = blocks.block_lgd_code 
                                         LEFT JOIN tbl_user_post_apply apply ON blocks.block_lgd_code = apply.post_block
                                         WHERE MD5(apply.apply_id) =?  ORDER BY panchayat_name_hin ASC", [$Apply_id]);

        $data['village'] = DB::select("SELECT 
                                            v.village_code, 
                                            v.village_name_hin ,
                                            v.panchayat_lgd_code 
                                         FROM  master_villages v
                                         LEFT JOIN master_panchayats p ON v.panchayat_lgd_code = p.panchayat_lgd_code 
                                         LEFT JOIN tbl_user_post_apply apply ON v.village_code = apply.post_village
                                         WHERE MD5(apply.apply_id) =?  ORDER BY village_name_hin ASC", [$Apply_id]);

        $data['nagar'] = DB::select("SELECT nagar.district_lgd_code, nagar.std_nnn_code, nagar.nnn_name, nagar.nnn_name_en FROM master_nnn nagar
                                            LEFT JOIN tbl_user_post_apply apply ON nagar.district_lgd_code=apply.fk_district_id 
                                            WHERE MD5(apply.apply_id) =? ORDER BY nnn_name ASC", [$Apply_id]); // Urban in case

        $data['ward'] = DB::select("SELECT 
                                             ward.std_nnn_code, 
                                             ward.ID,
                                             ward.ward_no,
                                             ward.ward_name 
                                          FROM master_ward ward
                                          LEFT JOIN master_nnn nagar ON ward.std_nnn_code = nagar.std_nnn_code 
                                          LEFT JOIN tbl_user_post_apply apply ON nagar.std_nnn_code = apply.post_nagar
                                          WHERE MD5(apply.apply_id) =? ORDER BY ward_no ASC", [$Apply_id]);

        $data['master_post'] = DB::select("SELECT * FROM master_post WHERE 1=1  ORDER BY post_id ASC");


        $data['maxdate'] = date('Y-m-d');
        $data['user_dob'] = $user_dob;

        // $data['minyear'] = \Carbon\Carbon::parse($data['user_dob'])->format('Y');
        $data['minyear'] = \Carbon\Carbon::parse($data['user_dob'])->addYears(7)->format('Y');
        $data['maxyear'] = \Carbon\Carbon::parse(date('d-m-Y'))->format('Y');


        $subjects = DB::select('SELECT * FROM master_subjects ORDER BY fk_Quali_ID ASC');
        $grades = DB::select('SELECT * FROM master_grades');
        $master_caste = DB::select('SELECT * FROM master_tbl_caste');
        $master_gender = DB::select('SELECT * FROM master_tbl_gender');
        $organization_type = DB::select('SELECT * FROM master_tbl_organization_type');
        $master_qualification = DB::select('SELECT * FROM master_qualification');


        $data['applicant_details'] = DB::table('tbl_user_detail')
            ->select(
                'tbl_user_detail.ID AS RowID',
                'tbl_user_detail.*',
                'master_user.*',
                'tbl_user_detail.reference_no as aadhar_no',
                'tbl_user_post_apply.*'
            )
            ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
            ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
            ->whereRaw('MD5(tbl_user_detail.ID) = ?', [$Candidate_id])
            ->whereRaw('MD5(tbl_user_post_apply.apply_id) = ?', [$Apply_id])
            ->first();


        $data['educationDetails'] = ApplicantEducationQualification::where('fk_applicant_id', $data['applicant_details']->RowID)->get();

        // Fetch multiple experiences separately
        $data['experience_details'] = DB::table('tbl_applicant_experience_details')
            ->where('Applicant_ID', $data['applicant_details']->RowID)
            ->get();

        $experience_count = $data['experience_details']->count();
        $data['experience_count'] = $experience_count;

        // Return view return view('candidate/tbl_user_details_form', compact('data', 'subjects'));
        return view('candidate/user_details_update', compact(
            'data',
            'subjects',
            'master_caste',
            'master_gender',
            'organization_type',
            'master_qualification',
            'user_age',
            'grades',
            'firstName_hindi',
            'middleName_hindi',
            'lastName_hindi'
        ));
    }

    public function areaData($district)
    {
        // Projects for this district
        // $projects = DB::table('master_projects')
        //     ->select('project_code', 'project')
        //     ->where('District_Code_LGD', $district)
        //     ->get();

        // Blocks (Rural)
        $blocks = DB::table('master_blocks')
            ->select('block_lgd_code', 'block_name_hin')
            ->where('district_lgd_code', $district)
            ->orderBy('block_name_hin', 'ASC')
            ->get();

        // Nagar (Urban)
        $nagars = DB::table('master_nnn')
            ->select('std_nnn_code', 'nnn_name')
            ->where('district_lgd_code', $district)
            ->orderBy('nnn_name', 'ASC')
            ->get();

        // JSON response
        return response()->json([
            // 'projects' => $projects,
            'blocks' => $blocks,
            'nagars' => $nagars,
        ]);
    }


    public function getGp($blockCode)
    {
        $gp = DB::table('master_panchayats')
            ->select('panchayat_lgd_code', 'panchayat_name_hin')
            ->where('block_lgd_code', $blockCode)
            ->orderBy('panchayat_name_hin', 'ASC')
            ->get();

        return response()->json($gp);
    }

    public function getVillage($GPcode)
    {
        $village = DB::table('master_villages')
            ->select('village_code', 'village_name_hin')
            ->where('panchayat_lgd_code', operator: $GPcode)
            ->orderBy('village_name_hin', 'ASC')
            ->get();
        return response()->json($village);
    }
    public function getWard($nagarCode)
    {
        $ward = DB::table('master_ward')
            ->select('ID', 'ward_no', 'ward_name')
            ->where('std_nnn_code', $nagarCode)
            ->orderBy('ward_no', 'ASC')
            ->get();

        return response()->json($ward);
    }


    public function getPost($ward_village_code)
    {



        $get_post = DB::select(
            "SELECT mp.post_id, 
                                            mp.title,
                                            mp.gp_nnn_code,
                                            mp.village_code,
                                            mp.ward_no,
                                            mp.fk_area_id,
                                            mp.std_nnn_code,
                                            mp.project_code,
                                            ma.Advertisement_Title,
                                            ma.Advertisement_Date,
                                            ma.Date_For_Age
                                        FROM master_post mp
                                        JOIN master_advertisement ma
                                            ON mp.Advertisement_ID = ma.Advertisement_ID
                                        WHERE (
                                            -- Option 1: Check village_code (village codes are usually strings)
                                            JSON_CONTAINS(mp.village_code, CONCAT('\"', ?, '\"'), '$')
                                            OR 
                                            -- Option 2: Check ward_no (ward IDs are usually integers in JSON)
                                            JSON_CONTAINS(mp.ward_no, ?, '$')
                                            OR
                                            -- Option 3: For integers in JSON array
                                            JSON_CONTAINS(mp.ward_no, CAST(? AS JSON), '$')
                                            OR
                                            -- Option 4: Handle as string in JSON
                                            JSON_CONTAINS(mp.ward_no, CONCAT('\"', ?, '\"'), '$')
                                        ) 
                                        AND ma.Advertisement_Date <= CURDATE()
                                        AND ma.Date_For_Age >= CURDATE()
                                        AND mp.is_disable = 1
                                        AND mp.post_id NOT IN (
                                            SELECT p.fk_post_id
                                            FROM tbl_user_post_apply p
                                            LEFT JOIN tbl_user_detail u
                                                ON p.fk_applicant_id = u.ID
                                            WHERE u.Applicant_ID = ? 
                                            AND p.post_village = ?
                                        )
                                        GROUP BY mp.post_id
                                        ORDER BY mp.post_id DESC",
            [
                $ward_village_code,
                $ward_village_code,
                $ward_village_code,
                $ward_village_code,
                session('sess_id'),
                $ward_village_code
            ]
        );
        // dd($get_post);
        return response()->json($get_post);
    }



    public function user_apply_post(Request $request, $appID)
    {


        $userId = Session::get('sess_id');

        // Get user's date of birth
        $user_dob = DB::selectOne("SELECT Date_Of_Birth FROM master_user WHERE ID = ?", [$userId]);
        $user_dob = $user_dob->Date_Of_Birth ?? null;

        // Calculate user's age
        $user_age = $user_dob ? date_diff(date_create($user_dob), date_create('today'))->y : null;


        // $user_age = 40;
        if ($user_age !== null) {
            $data['recruitment'] = DB::select(" SELECT 
                                                                   am.Advertisement_ID,
                                                                   am.Advertisement_Title,
                                                                     pm.post_id AS Post_ID,
                                                                   pm.title AS Post_Title,
                                                                    pm.guidelines AS guidelines
                                                               FROM master_advertisement am
                                                               JOIN master_post pm ON am.Advertisement_ID = pm.Advertisement_ID 
                                                               WHERE am.Advertisement_Date <= CURDATE()
                                                                   AND (am.Date_For_Age >= CURDATE())
                                                                   AND (
                                                                       pm.max_age IS NOT NULL 
                                                                       AND ? <= pm.max_age
                                                                       AND ? >= pm.min_age
                                                                   )
                                                                   AND md5(pm.Advertisement_ID) = ?
                                                           ", [$user_age, $user_age, $appID]);
        }


        $data['cities'] = DB::select("SELECT District_Code_LGD, name FROM master_district");
        return view('candidate/user_apply_post', compact('data'));
    }




    public function save_post_question(Request $request)
    {
        $post_id = $request->input('post_id');
        $applicant_id = $request->input('applicant_id_tab2');
        $skill_ids = $request->input('skills', []); // Checkbox name="skills[]"

        DB::beginTransaction();

        try {
            // 🔹 STEP 1: Fetch post-specific questions
            $questions = DB::table('post_question_map AS map')
                ->select('map.post_map_id', 'map.fk_ques_id', 'map.fk_post_id', 'master.ques_name', 'master.answer_options')
                ->join('master_post_questions AS master', 'master.ques_ID', '=', 'map.fk_ques_id')
                ->where('map.fk_post_id', $post_id)
                ->whereNull('map.deleted_at')
                ->get();

            if ($questions->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'title' => 'त्रुटि!',
                    'message' => 'इस पोस्ट के लिए कोई प्रश्न नहीं मिले।'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            // 🔹 STEP 2: Retrieve session-stored apply data
            $applyData = Session::get('apply_data');
            if (!$applyData) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'कृपया पहले पद का चयन करें।',
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            // 🔹 STEP 3: Save to tbl_user_post_apply table
            $applyModel = new user_post_apply();
            $applyModel->fk_applicant_id = $applyData['fk_applicant_id'];
            $applyModel->fk_post_id = $applyData['fk_post_id'];
            $applyModel->fk_district_id = $applyData['fk_district_id'];
            $applyModel->apply_date = $applyData['apply_date'];
            $applyModel->status = $applyData['status'];
            $applyModel->status_date = $applyData['status_date'];
            $applyModel->save();

            $apply_id = $applyModel->apply_id;

            // 🔹 STEP 4: Save selected skills to tbl_post_skill_answer
            foreach ($skill_ids as $skill_id) {
                DB::table('tbl_post_skill_answer')->insert([
                    'fk_apply_id' => $apply_id,
                    'fk_skill_id' => $skill_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 🔹 STEP 5: Save question answers only if present in request
            $allowedExtensions = ['pdf'];
            $allowedMimeTypes = ['application/pdf'];
            $filePaths = [];

            foreach ($questions as $question) {
                $inputKey = 'question_' . $question->post_map_id;
                $singleFileKey = $inputKey . '_file';
                $fdDateKey = $inputKey . '_date';

                $answer = $request->input($inputKey, null);
                $isFD = isset($question->ans_type) && $question->ans_type === 'FD';
                $filePath = null;

                // Handle file for FD type if provided, including child indexed file inputs like question_XX_file_0
                if ($isFD) {
                    $uploadedFile = null;
                    // Search all uploaded file keys for matching patterns
                    $allFiles = $request->allFiles();
                    $pattern = '/^' . preg_quote($singleFileKey, '/') . '(?:_\d+)?$/';
                    foreach ($allFiles as $fkey => $fval) {
                        if (preg_match($pattern, $fkey)) {
                            $uploadedFile = $fval;
                            break;
                        }
                    }

                    if ($uploadedFile) {
                        if (is_array($uploadedFile))
                            $uploadedFile = $uploadedFile[0];

                        $uploadResult = UtilController::upload_file(
                            $uploadedFile,
                            $singleFileKey,
                            'uploads',
                            $allowedExtensions,
                            $allowedMimeTypes
                        );

                        if (!$uploadResult) {
                            foreach ($filePaths as $prevPath) {
                                if (file_exists(public_path($prevPath))) {
                                    unlink(public_path($prevPath));
                                }
                            }
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => "प्रश्न '{$question->ques_name}' के लिए फ़ाइल सहेजने में असमर्थ! केवल PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                            ], 422, [], JSON_UNESCAPED_UNICODE);
                        }
                        $filePath = $uploadResult;
                    }
                }

                // Determine date_From for FD if present (date input or single-date answer)
                $dateFrom = null;
                if ($isFD) {
                    $dateFrom = $request->input($fdDateKey, null);
                    if (empty($dateFrom) && !empty($answer)) {
                        $dateFrom = $answer;
                    }
                }

                // Skip saving if answer is empty/null
                if (!isset($answer) || trim($answer) === '') {
                    continue;
                }

                // Insert
                $insertRow = [
                    'applicant_id' => $applicant_id,
                    'post_id' => $post_id,
                    'post_map_id' => $question->post_map_id,
                    'fk_question_id' => $question->fk_ques_id,
                    'answer' => $answer,
                    'date_From' => $dateFrom,
                    'date_To' => null,
                    'total_experience_days' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (!empty($filePath)) {
                    $insertRow['answer_file_upload'] = $filePath;
                }

                DB::table('tbl_user_post_question_answer')->insert($insertRow);
            }

            DB::commit();

            // 🔹 STEP 6: Clear session
            Session::forget('apply_data');

            return response()->json([
                'status' => 'success',
                'title' => 'सफलता!',
                'message' => 'उत्तर और कौशल सफलतापूर्वक सहेजे गए हैं।',
                'post_id' => $post_id,
                'applicant_id' => $applicant_id,
                'application_id' => $apply_id,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }




    public function view_user_detail(Request $request, $applicant_id = 0)
    {

        $applicant_details = DB::table('tbl_user_detail')
            ->select(
                'tbl_user_detail.Applicant_ID AS RowID',
                'tbl_user_detail.*',
                'master_user.*',
                'master_user.Applicant_ID as Genearted_AppId',
            )
            ->join('master_user', 'master_user.ID', '=', 'tbl_user_detail.Applicant_ID')
            ->whereRaw("MD5(tbl_user_detail.Applicant_ID) = ?", [$applicant_id])
            ->first();


        // Fetch multiple Education separately
        $education_details = DB::table('tbl_applicant_education_qualification')
            ->select('tbl_applicant_education_qualification.*', 'master_qualification.Quali_Name')
            ->join('master_qualification', 'tbl_applicant_education_qualification.fk_Quali_ID', '=', 'master_qualification.Quali_ID')
            //  ->join('master_subjects', 'tbl_applicant_education_qualification.fk_subject_id', '=', 'master_subjects.subject_id')
            ->where('tbl_applicant_education_qualification.fk_applicant_id', '=', $applicant_details->RowID)
            ->get();

        // Fetch multiple experiences separately
        $experience_details = DB::table('tbl_applicant_experience_details')
            ->where('Applicant_ID', $applicant_details->RowID)
            ->get();


        return view('candidate.view_user_detail', compact('applicant_details', 'experience_details', 'education_details'));
    }


    public function view_documents($apply_id = 0)
    {


        $applicant_details = DB::table('tbl_user_detail')
            ->select(
                'tbl_user_detail.ID AS RowID',
                'tbl_user_detail.Applicant_ID',
                'tbl_user_detail.Document_Photo',
                'tbl_user_detail.Document_Sign',
                'tbl_user_detail.Document_Aadhar',
                'tbl_user_detail.Document_Caste',
                'tbl_user_detail.Document_Domicile',
                'tbl_user_detail.Document_other',
                'tbl_user_detail.Document_5th',
                'tbl_user_detail.Document_8th',
                'tbl_user_detail.Document_SSC',
                'tbl_user_detail.Document_Inter',
                'tbl_user_detail.Document_UG',
                'tbl_user_detail.Document_PG',
                'tbl_user_detail.Document_BPL',
                'tbl_user_detail.Document_Widow',
                'tbl_user_detail.Document_Exp',
                'tbl_user_detail.Document_Epic',
                'tbl_user_post_apply.fk_post_id',
                'tbl_user_post_apply.apply_id',
            )
            ->join('tbl_user_post_apply', 'tbl_user_detail.ID', '=', 'tbl_user_post_apply.fk_applicant_id')
            ->whereRaw("MD5(tbl_user_post_apply.apply_id) = ?", [$apply_id])
            ->first();

        $apply_id = $applicant_details->apply_id;
        $post_id = $applicant_details->fk_post_id;
        $Applicant_id = $applicant_details->Applicant_ID;
        $Applicant_RowID = $applicant_details->RowID;

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


        $experience_docs = DB::table('tbl_applicant_experience_details')
            ->select('exp_document')
            ->where('Applicant_ID', $Applicant_RowID)
            ->get();

        $other_docs = DB::table('tbl_user_other_documents')
            ->select('other_documents')
            ->where('fk_applicant_id', $Applicant_RowID)
            ->get();


        return view('/candidate/view_documents', compact('applicant_details', 'questionAnswers', 'questionMultipleAnswers', 'experience_docs', 'other_docs'));
    }


    public function view_all_docs($application_id = 0)
    {
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
                'record_user_detail_map.fk_apply_id',
                // 'tbl_user_post_apply.self_attested_file'
            )
            ->join('tbl_user_post_apply', 'record_user_detail_map.fk_apply_id', '=', 'tbl_user_post_apply.apply_id')
            ->whereRaw("MD5(record_user_detail_map.fk_apply_id) = ?", [$application_id])
            ->first();

        $post_id = $applicant_details->fk_post_id;
        $Applicant_id = $applicant_details->Applicant_ID;
        $Applicant_RowID = $applicant_details->RowID;

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


        $experience_docs = DB::table('record_applicant_experience_map')
            ->select('exp_document')
            ->where('Applicant_ID', $Applicant_RowID)
            ->whereRaw("MD5(fk_apply_id) = ?", [$application_id])
            ->get();

        $other_docs = DB::table('tbl_user_other_documents')
            ->select('other_documents')
            ->where('fk_applicant_id', $Applicant_RowID)
            ->get();

        // dd($applicant_details, $questionAnswers,  $experience_docs, $Applicant_RowID);
        return view('/candidate/view_all_documents', compact('applicant_details', 'questionAnswers', 'questionMultipleAnswers', 'experience_docs', 'other_docs'));
    }


    public function getPincodesByDistrict($district_code)
    {
        $pincodes = DB::table('master_pincodes')
            ->where('district_code', $district_code)
            ->pluck('pincode');

        return response()->json($pincodes);
    }


    public function post_details_update(Request $request, $id = 0)
    {
        $rules = [
            'master_post' => 'required|exists:master_post,post_id',
            'selected_district' => 'required|exists:master_district,District_Code_LGD',
            'area' => 'required|in:1,2',
            'block' => 'required_if:area,1|exists:master_blocks,block_lgd_code',
            'gp' => 'required_if:area,1|exists:master_panchayats,panchayat_lgd_code',
            'post_village' => 'required_if:area,1|exists:master_villages,village_code',
            'nagar' => 'required_if:area,2|exists:master_nnn,std_nnn_code',
            'ward' => 'required_if:area,2|exists:master_ward,ID',
        ];

        $customMessages = [
            'master_post.required' => 'कृपया पोस्ट का चयन करें।',
            'master_post.exists' => 'चयनित पोस्ट अमान्य है।',
            'selected_district.required' => 'कृपया जिला चयन करें।',
            'selected_district.exists' => 'चयनित जिला अमान्य है।',
            'area.required' => 'कृपया क्षेत्र का चयन करें (ग्रामीण या शहरी)।',
            'area.in' => 'चयनित क्षेत्र अमान्य है।',
            'block.required_if' => 'जब क्षेत्र ग्रामीण हो, तो विकासखंड चयन अनिवार्य है।',
            'block.exists' => 'चयनित ब्लॉक अमान्य है।',
            'gp.required_if' => 'जब क्षेत्र ग्रामीण हो, तो ग्राम पंचायत चयन अनिवार्य है।',
            'gp.exists' => 'चयनित ग्राम पंचायत अमान्य है।',
            'post_village.required_if' => 'जब क्षेत्र ग्रामीण हो, तो ग्राम चयन अनिवार्य है।',
            'post_village.exists' => 'चयनित ग्राम अमान्य है।',
            'nagar.required_if' => 'जब क्षेत्र शहरी हो, तो नगर निकाय चयन अनिवार्य है।',
            'nagar.exists' => 'चयनित नगर निकाय अमान्य है।',
            'ward.required_if' => 'जब क्षेत्र शहरी हो, तो वार्ड संख्या चयन अनिवार्य है।',
            'ward.exists' => 'चयनित वार्ड संख्या अमान्य है।',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'कृपया सभी आवश्यक फ़ील्ड का चयन करें ।',
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $applyId = $request->input('apply_row_id');
        $postId = $request->input('master_post');
        $area = $request->input('area');
        $district = $request->input('selected_district');
        $block = $request->input('block');
        $gp = $request->input('gp');
        $post_village = $request->input('post_village');
        $nagar = $request->input('nagar');
        $ward = $request->input('ward');

        $applicantId = DB::table('tbl_user_post_apply')
            ->where('apply_id', $applyId)
            ->value('fk_applicant_id');

        if (!$applicantId) {
            return response()->json([
                'message' => 'आवेदनकर्ता आईडी मान्य नहीं है।',
                'status' => 'error'
            ]);
        }

        $isPostActive = DB::table('master_post')
            ->where('post_id', $postId)
            ->where('is_disable', 1)
            ->exists();

        if (!$isPostActive) {
            return response()->json([
                'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                'status' => 'error'
            ]);
        }

        // Duplicate check
        $ExistingCheck = DB::table('tbl_user_post_apply')
            ->where('fk_applicant_id', $applicantId)
            ->where('fk_post_id', $postId)
            ->where('apply_id', '!=', $applyId);

        $area == 1
            ? $ExistingCheck->where('post_block', $block)->where('post_gp', $gp)
            : $ExistingCheck->where('post_nagar', $nagar)->where('post_ward', $ward);

        if ($ExistingCheck->exists()) {
            return response()->json([
                'message' => 'आप इस पद के लिए पहले ही आवेदन कर चुके हैं।',
                'status' => 'warning'
            ]);
        }


        //## Validation On All Selected Question Multiple Select 
        $SelectedQuestions = DB::table('post_question_map')
            ->where('fk_post_id', $postId)
            ->get();
        $row = $SelectedQuestions->firstWhere('fk_ques_id', 10);

        if ($row) {
            $post_map_id = $row->post_map_id;
            $reqKey = "question_$post_map_id";

            if (empty($request->$reqKey)) {
                return response()->json([
                    'message' => 'यूजीसी द्वारा मान्यता प्राप्त संस्थान से डिग्री, डिप्लोमा या पीजी डिप्लोमा प्रमाणपत्र अनिवार्य है कृपया चयन करें।',
                    'status' => 'error',
                ]);
            }
        }

        $project_code = DB::table('master_post')
            ->where('post_id', $postId)
            ->value('project_code');

        // Update data
        $updateData = [
            'fk_post_id' => $postId,
            'fk_district_id' => $district,
            'post_area' => $area,
            'updated_at' => now(),
            'post_block' => $area == 1 ? $block : null,
            'post_gp' => $area == 1 ? $gp : null,
            'post_village' => $area == 1 ? $post_village : null,
            'post_nagar' => $area == 2 ? $nagar : null,
            'post_ward' => $area == 2 ? $ward : null,
            'post_projects' => $project_code ?? null,
        ];

        DB::table('tbl_user_post_apply')->where('apply_id', $applyId)->update($updateData);

        // Fetch questions
        $questions = DB::table('post_question_map AS map')
            ->join('master_post_questions AS qus', 'map.fk_ques_id', '=', 'qus.ques_ID')
            ->select('map.post_map_id', 'map.fk_ques_id', 'qus.ques_name', 'qus.ans_type')
            ->where('map.fk_post_id', $postId)
            ->whereNull('map.deleted_at')
            ->get();

        $allowedExtensions = ['pdf'];
        $allowedMimeTypes = ['application/pdf'];
        $filePaths = [];
        $answers = [];

        foreach ($questions as $question) {
            $questionId = $question->fk_ques_id;
            $inputKey = 'question_' . $question->post_map_id;
            $fileKeyPrefix = 'question_' . $question->post_map_id . '_file_'; // multi file prefix
            $singleFileKey = 'question_' . $question->post_map_id . '_file'; // single file key

            // Data available check (single or multiple)
            if ($request->filled($inputKey) || $request->hasFile($singleFileKey) || $request->hasFile($fileKeyPrefix . '0')) {

                $answerData = (array) $request->input($inputKey); // always array
                $isFDType = isset($question->ans_type) && $question->ans_type === 'FD';

                // If FD type and no answer data but file exists or a date input exists, create a single entry
                if (empty($answerData) && $isFDType) {
                    // Try both possible date input names used in views
                    $datePossible = $request->input('question_' . $question->post_map_id . '_date', $request->input('dateFrom_question_' . $question->post_map_id));
                    if (!empty($datePossible)) {
                        $answerData = [$datePossible];
                    } else {
                        // if file exists (child indexed), still want to process once
                        // set placeholder empty answer and let later logic handle saving based on file
                        $answerData = [''];
                    }
                }

                foreach ($answerData as $ansIndex => $singleAnswer) {
                    $filePath = null;

                    // For FD, prefer explicit date input names
                    $date_From = $isFDType ? ($request->input('question_' . $question->post_map_id . '_date', $request->input('dateFrom_question_' . $question->post_map_id, null))) : $request->input('dateFrom_question_' . $question->post_map_id, null);
                    $date_To = $request->input('dateTo_question_' . $question->post_map_id, null);
                    $total_experience_days = $request->input('totalExpDays_question_' . $question->post_map_id, null);

                    // Single answer => singleFileKey, Multiple => fileKeyPrefix + index
                    $currentFileKey = (count($answerData) > 1) ? $fileKeyPrefix . $ansIndex : $singleFileKey;

                    // Try to find uploaded file: allow singleFileKey, indexed fileKeyPrefix + index, or any matching file key
                    $uploadedFile = null;
                    if ($request->hasFile($currentFileKey)) {
                        $uploadedFile = $request->file($currentFileKey);
                    } else {
                        // scan all files for matching patterns like question_123_file or question_123_file_0
                        $allFiles = $request->allFiles();
                        $pattern = '/^' . preg_quote('question_' . $question->post_map_id . '_file', '/') . '(?:_\d+)?$/';
                        foreach ($allFiles as $fkey => $fval) {
                            if (preg_match($pattern, $fkey)) {
                                $uploadedFile = $fval;
                                break;
                            }
                        }
                    }

                    if ($uploadedFile) {
                        if (is_array($uploadedFile))
                            $uploadedFile = $uploadedFile[0]; // normalize

                        $uploadResult = UtilController::upload_file(
                            $uploadedFile,
                            $currentFileKey,
                            'uploads',
                            $allowedExtensions,
                            $allowedMimeTypes
                        );

                        if (!$uploadResult) {
                            foreach ($filePaths as $prevPath) {
                                if (file_exists(public_path($prevPath))) {
                                    unlink(public_path($prevPath));
                                }
                            }
                            return response()->json([
                                'status' => 'error',
                                'message' => "प्रश्न '{$question->ques_name}' के लिए फ़ाइल सहेजने में असमर्थ! केवल PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                            ], 422, [], JSON_UNESCAPED_UNICODE);
                        }
                        $filePath = $uploadResult;
                    }

                    // हर answer अलग row insert
                    $answers[] = [
                        'post_map_id' => $question->post_map_id,
                        'fk_question_id' => $questionId,
                        'ans_type' => $question->ans_type ?? null,
                        'answer' => $singleAnswer,
                        'file' => $filePath,
                        'date_From' => $date_From,
                        'date_To' => $date_To,
                        'total_experience_days' => $total_experience_days
                    ];
                }
            }
        }

        // Discontinue check
        if (collect($answers)->contains(fn($a) => $a['fk_question_id'] == 11 && $a['answer'] == 'हाँ')) {
            return response()->json([
                'message' => 'आपको पहले अनियमितता के कारण सेवा से अलग किया गया था, अतः आप आवेदन करने के पात्र नहीं है।',
                'status' => 'error',
            ]);
        }

        // Delete old data
        DB::table('tbl_user_post_question_answer')
            ->where(['applicant_id' => $applicantId, 'post_id' => $postId])
            ->delete();

        DB::table('tbl_post_skill_answer')
            ->where('fk_apply_id', $applyId)
            ->delete();

        // Insert new answers (skip empty answers; special handling for FD type)
        $allInserted = collect($answers)->every(function ($item) use ($applicantId, $postId) {
            $answer = trim($item['answer'] ?? '');
            $isFDType = isset($item['ans_type']) && $item['ans_type'] === 'FD';

            // Skip saving if answer is null/empty, except when FD type has a provided file (we want to save file + date)
            if ($answer === '') {
                if (!($isFDType && !empty($item['file']))) {
                    return true; // not an error, just skip
                }
                // If FD with file but no answer, try to use date_From as answer fallback
                $answerFallback = trim($item['date_From'] ?? '');
                $item['answer'] = $answerFallback !== '' ? $answerFallback : $item['answer'];
            }

            $insertRow = [
                'applicant_id' => $applicantId,
                'post_id' => $postId,
                'post_map_id' => $item['post_map_id'],
                'fk_question_id' => $item['fk_question_id'],
                'answer' => $item['answer'],
                'date_From' => $item['date_From'] ?? null,
                'date_To' => $item['date_To'] ?? null,
                'total_experience_days' => $item['total_experience_days'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $isFDType = isset($item['ans_type']) && $item['ans_type'] === 'FD';

            if ($isFDType) {
                if (!empty($item['file'])) {
                    $insertRow['answer_file_upload'] = $item['file'];
                } elseif (!empty($item['answer_file_upload'])) {
                    $insertRow['answer_file_upload'] = $item['answer_file_upload'];
                }

                // If date_From missing and answer contains single date, use it
                if (empty($insertRow['date_From']) && !empty($item['answer'])) {
                    $insertRow['date_From'] = $item['answer'];
                }
            } else {
                if (!empty($item['file'])) {
                    $insertRow['answer_file_upload'] = $item['file'];
                } elseif (!empty($item['answer_file_upload'])) {
                    $insertRow['answer_file_upload'] = $item['answer_file_upload'];
                }
            }

            return DB::table('tbl_user_post_question_answer')->insert($insertRow);
        });

        // Insert skills
        foreach ($request->input('skill_options', []) as $skill_id => $options) {
            DB::table('tbl_post_skill_answer')->insert([
                'fk_apply_id' => $applicantId,
                'fk_skill_id' => $skill_id,
                'skill_answers' => json_encode($options, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($allInserted) {
            DB::commit();
            return response()->json([
                'message' => 'डेटा सफलतापूर्वक संग्रहीत किया गया है।',
                'status' => 'success',
                'updated_apply_data' => $updateData
            ]);
        }

        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'डेटा सहेजने में त्रुटि हुई। कृपया पुनः प्रयास करें।',
            'errors' => ['database' => ['Failed to save applicant details.']]
        ], 500, [], JSON_UNESCAPED_UNICODE);
    }


    public function applicant_details_update(Request $request, $id = 0)
    {
        // Validation rules
        $rules = [
            'First_Name' => 'required',
            'firstName_hindi' => 'required',
            'mothername' => 'required',
            'fathername' => 'required',
            // 'domicile_district' => 'required',
            'corr_addr' => 'required',
            'cur_district' => 'required',
            'pincode' => 'required|numeric|digits:6',
            // 'perm_addr' => 'required',
            // 'per_district' => 'required',
            // 'ppincode' => 'required|numeric|digits:6',
            'nationality' => 'required',
            'dob' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'gender' => 'required',
            'caste' => 'required',
            'epicno' => 'nullable|size:10|regex:/^[A-Z]{3}[0-9]{7}$/',
            'current_area' => 'required|in:1,2',
            'current_block' => 'required_if:current_area,1|exists:master_blocks,block_lgd_code',
            'current_gp' => 'required_if:current_area,1|exists:master_panchayats,panchayat_lgd_code',
            'current_village' => 'required_if:current_area,1|exists:master_villages,village_code',
            'current_nagar' => 'required_if:current_area,2|exists:master_nnn,std_nnn_code',
            'current_ward' => 'required_if:current_area,2|exists:master_ward,ID',
            'adhaar' => 'required|min:12|max:12',
            'AdharConfirm' => 'required_with:adhaar|in:1'
        ];
        // $rules['adhaar'] = $request->filled('app_id') ? 'min:12' : 'digits:12';

        // Custom error messages
        $customMessages = [
            'mobile.digits' => 'मोबाइल नंबर 10 अंक का होना चाहिए।',
            'mobile.regex' => 'मोबाइल नंबर 6-9 से शुरू होना चाहिए।',
            'mobile.required' => 'मोबाइल नंबर आवश्यक है।',
            // 'pincode.digits' => 'पिनकोड 6 अंक का होना चाहिए।',
            // 'ppincode.digits' => 'पिनकोड 6 अंक का होना चाहिए।',
            'adhaar.required' => 'आधार नंबर अनिवार्य है।',
            'adhaar.min' => 'आधार नंबर 12 अंक का होना चाहिए।',
            'adhaar.max' => 'आधार नंबर 12 अंक का होना चाहिए।',
            'AdharConfirm.required_with' => 'आधार नंबर प्रस्तुत सहमति अनिवार्य है, कृपया चेकबॉक्स चुनें।',
            'identity_type.in' => 'अमान्य पहचान प्रकार। कृपया एक वैध विकल्प चुनें।',
            'current_block.required_if' => 'विकासखंड चयन अनिवार्य है जब क्षेत्र ग्रामीण हो।',
            'current_gp.required_if' => 'ग्राम पंचायत चयन अनिवार्य है जब क्षेत्र ग्रामीण हो।',
            'current_village.required_if' => 'ग्राम चयन अनिवार्य है जब क्षेत्र ग्रामीण हो।',
            'current_nagar.required_if' => 'नगर निकाय चयन अनिवार्य है जब क्षेत्र शहरी हो।',
            'current_ward.required_if' => 'वार्ड चयन अनिवार्य है जब क्षेत्र शहरी हो।',
            // 'epicno.required' => 'इपिक नंबर आवश्यक है।',
            'epicno.regex' => 'इपिक नंबर का प्रारूप 3 बड़े अक्षर + 7 अंक होना चाहिए। (e.g. ABC1234567)',
            'epicno.size' => 'इपिक नंबर ठीक 10 अंक का होना चाहिए।',
        ];

        // Validate
        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'कृपया सभी आवश्यक फ़ील्ड सही से भरें।',
                'errors' => $validator->errors(),
                'status' => 'error',
            ]);
        }

        // Retrieve post apply data
        $applyData = DB::table('tbl_user_post_apply')
            ->where('apply_id', $request->input('apply_row_id'))
            ->first();

        $postId = $applyData->fk_post_id;
        $applicantId = $applyData->fk_applicant_id;
        $mobileNumber = session('sess_mobile');
        $uid = session('uid');

        $user = DB::select("SELECT ID,Date_Of_Birth,Mobile_Number FROM master_user WHERE Mobile_Number = ?", [$mobileNumber]);
        if (!$user) {
            return response()->json(['message' => 'उपयोगकर्ता नहीं मिला।', 'status' => 'error']);
        }

        $userId = $user[0]->ID;
        $user_Date_Of_Birth = $user[0]->Date_Of_Birth;
        $user_Mobile_Number = $user[0]->Mobile_Number;
        $user_age = $user_Date_Of_Birth ? date_diff(date_create($user_Date_Of_Birth), date_create('2025-01-01'))->y : null;

        // Retrieve answers
        $answers = DB::table('tbl_user_post_question_answer')
            ->where('applicant_id', $applicantId)
            ->where('post_id', $postId)
            ->get()
            ->toArray();

        // Widow check for address validation skip
        $marrige_ques = collect($answers)->first(function ($item) {
            return isset($item->fk_question_id) && $item->fk_question_id == 1 && ($item->answer == 'विधवा');
        });
        if (!$marrige_ques) {
            if ($applyData->post_area == 1) {
                if ($applyData->post_block != $request->current_block || $applyData->post_gp != $request->current_gp || $applyData->post_village != $request->current_village) {
                    return response()->json([
                        'message' => 'आपका स्थायी ब्लॉक/पंचायत/ग्राम चयनित पोस्ट के पते से मेल नहीं खाता। अतः आप इस पद के लिए आवेदन नहीं कर सकते।',
                        'status' => 'warning'
                    ]);
                }
            } else {
                if ($applyData->post_nagar != $request->current_nagar || $applyData->post_ward != $request->current_ward) {
                    return response()->json([
                        'message' => 'आपका स्थायी नगर/वार्ड चयनित पोस्ट के पते से मेल नहीं खाता। अतः आप इस पद के लिए आवेदन नहीं कर सकते।',
                        'status' => 'warning'
                    ]);
                }
            }
        }

        // Age validation
        $post_data = DB::select("SELECT * FROM master_post WHERE post_id=? AND is_disable = 1", [$postId]);
        if (empty($post_data)) {
            return response()->json([
                'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                'status' => 'error'
            ]);
        }
        $post_max_age = $post_data[0]->max_age;
        $post_age_relax = $post_data[0]->max_age_relax ?? null;
        $post_final_age = $post_age_relax ? $post_age_relax : $post_max_age;
        $post_name = $post_data[0]->title;
        $ExpRequired = 0;
        $WidowRequired = 0;
        $BPLRequired = 0;


        if ($user_age > $post_max_age) {
            $age_relax_ques = collect($answers)->first(function ($item) {
                return isset($item->fk_question_id) && $item->fk_question_id == 9 && $item->answer == 'हाँ';
            });
            if ($age_relax_ques) {
                Session::put('Experience_required', 1);
                $ExpRequired = Session::get('Experience_required', 0);
                if ($user_age > $post_final_age) {
                    return response()->json([
                        'message' => $post_name . ' पद के लिए अधिकतम आयु ' . $post_max_age . ' होनी चाहिए। अतः आप आवेदन करने के पात्र नहीं है।',
                        'status' => 'warning'
                    ]);
                }
            } else {
                return response()->json([
                    'message' => $post_name . ' पद के लिए अधिकतम आयु ' . $post_max_age . ' होनी चाहिए। अतः आप आवेदन करने के पात्र नहीं है।',
                    'status' => 'warning'
                ]);
            }
        }

        // Widow/Divorce document requirement
        $marrige_ques = collect($answers)->first(function ($item) {
            return isset($item->fk_question_id) && $item->fk_question_id == 1 &&
                ($item->answer == 'परित्यक्ता' || $item->answer == 'तलाकशुदा' || $item->answer == 'विधवा');
        });
        if ($marrige_ques) {
            Session::put('Widow_Divorce_required', 1);
            $WidowRequired = Session::get('Widow_Divorce_required', 0);
        }

        // BPL document requirement
        $marrige_ques = collect($answers)->first(function ($item) {
            return isset($item->fk_question_id) && $item->fk_question_id == 3 && ($item->answer == 'हाँ');
        });
        if ($marrige_ques) {
            Session::put('BPL_required', 1);
            $BPLRequired = Session::get('BPL_required', 0);
        }

        try {
            DB::beginTransaction();
            $masked_adhaar = substr_replace($request['adhaar'], str_repeat("X", 8), 0, 8);

            $first_name_uppercase = strtoupper($request->First_Name);
            $middle_name_uppercase = strtoupper($request->Middle_Name);
            $last_name_uppercase = strtoupper($request->Last_Name);
            $pincode = $request->pincode_manual ?? $request->pincode;
            $data = [
                'First_Name' => $first_name_uppercase,
                'Middle_Name' => $middle_name_uppercase,
                'Last_Name' => $last_name_uppercase,
                'firstName_hindi' => $request['firstName_hindi'],
                'middleName_hindi' => $request['middleName_hindi'],
                'lastName_hindi' => $request['lastName_hindi'],
                'FatherName' => $request->fathername,
                'MotherName' => $request->mothername,
                'DOB' => $user_Date_Of_Birth,
                'Gender' => $request->gender,
                'Contact_Number' => $user_Mobile_Number,
                'reference_no' => $masked_adhaar,
                'AdharConfirm' => $request->AdharConfirm ?? 0,
                // 'isJanmanNiwasi' => $request->isJanmanNiwasi,
                'epicno' => $request->epicno ?? '',
                'identity_type' => $request->identity_type,
                'Domicile_District_lgd' => $request->domicile_district ?? '',
                'Corr_Address' => $request->corr_addr,
                'Corr_District_lgd' => $request->cur_district,
                'Corr_pincode' => $request->pincode,
                'Perm_Address' => $request->perm_addr ?? '',
                'Perm_District_lgd' => $request->per_district ?? '',
                'Perm_pincode' => $request->ppincode ?? '',
                // 'sameAddress' => $request->sameAddress,
                'current_area' => $request->current_area,
                'current_block' => $request->current_block,
                'current_gp' => $request->current_gp,
                'current_village' => $request->current_village,
                'current_nagar' => $request->current_nagar,
                'current_ward' => $request->current_ward,
                'Caste' => $request->caste,
                'Nationality' => $request->nationality,
                'IP_Address' => $request->ip(),
                'Last_Updated_By' => $uid,
                'Last_Updated_On' => now(),
                'Applicant_ID' => $userId,
            ];

            if (DB::table('tbl_user_detail')->where('Applicant_ID', $userId)->exists()) {
                DB::table('tbl_user_detail')->where('Applicant_ID', $userId)->update($data);
                DB::update('UPDATE tbl_user_post_apply SET stepCount = 2 WHERE apply_id = ?', [$applyData->apply_id]);

                // If any question inputs are present in this request, process and save them (FD file + date handling)
                $hasQuestionInput = false;
                foreach ($request->all() as $k => $v) {
                    if (strpos($k, 'question_') === 0) {
                        $hasQuestionInput = true;
                        break;
                    }
                }
                if ($hasQuestionInput) {
                    $questions = DB::table('post_question_map AS map')
                        ->join('master_post_questions AS qus', 'qus.ques_ID', '=', 'map.fk_ques_id')
                        ->select('map.post_map_id', 'map.fk_ques_id', 'qus.ans_type', 'qus.ques_name')
                        ->where('map.fk_post_id', $postId)
                        ->whereNull('map.deleted_at')
                        ->get();

                    $allowedExtensions = ['pdf'];
                    $allowedMimeTypes = ['application/pdf'];
                    $filePaths = [];
                    $answersToInsert = [];

                    foreach ($questions as $question) {
                        $inputKey = 'question_' . $question->post_map_id;
                        $singleFileKey = $inputKey . '_file';
                        $fdDateKey = $inputKey . '_date';

                        $answer = $request->input($inputKey, null);
                        $isFD = isset($question->ans_type) && $question->ans_type === 'FD';
                        $filePath = null;

                        if ($isFD) {
                            $uploadedFile = null;
                            $allFiles = $request->allFiles();
                            $pattern = '/^' . preg_quote($singleFileKey, '/') . '(?:_\d+)?$/';
                            foreach ($allFiles as $fkey => $fval) {
                                if (preg_match($pattern, $fkey)) {
                                    $uploadedFile = $fval;
                                    break;
                                }
                            }

                            if ($uploadedFile) {
                                if (is_array($uploadedFile))
                                    $uploadedFile = $uploadedFile[0];

                                $uploadResult = UtilController::upload_file(
                                    $uploadedFile,
                                    $singleFileKey,
                                    'uploads',
                                    $allowedExtensions,
                                    $allowedMimeTypes
                                );

                                if (!$uploadResult) {
                                    foreach ($filePaths as $prevPath) {
                                        if (file_exists(public_path($prevPath)))
                                            unlink(public_path($prevPath));
                                    }
                                    DB::rollBack();
                                    return response()->json([
                                        'status' => 'error',
                                        'message' => "प्रश्न '{$question->ques_name}' के लिए फ़ाइल सहेजने में असमर्थ! केवल PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                                    ], 422, [], JSON_UNESCAPED_UNICODE);
                                }

                                $filePath = $uploadResult;
                            }
                        }

                        $dateFrom = null;
                        if ($isFD) {
                            $dateFrom = $request->input($fdDateKey, null);
                            if (empty($dateFrom) && !empty($answer))
                                $dateFrom = $answer;
                        }

                        // Skip saving if answer is empty/null
                        if (!isset($answer) || trim($answer) === '') {
                            continue;
                        }

                        $answersToInsert[] = [
                            'applicant_id' => $applicantId,
                            'post_id' => $postId,
                            'post_map_id' => $question->post_map_id,
                            'fk_question_id' => $question->fk_ques_id,
                            'answer' => $answer,
                            'answer_file_upload' => $filePath,
                            'date_From' => $dateFrom,
                            'date_To' => null,
                            'total_experience_days' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Delete old and insert new answers
                    DB::table('tbl_user_post_question_answer')->where(['applicant_id' => $applicantId, 'post_id' => $postId])->delete();
                    foreach ($answersToInsert as $row) {
                        DB::table('tbl_user_post_question_answer')->insert($row);
                    }
                }

                DB::commit();
                // return response()->json(['message' => 'डेटा सफलतापूर्वक अपडेट किया गया है।', 'status' => 'success', 'Applicant_ID' => $userId]);
            }

            // dd($ExpRequired, $WidowRequired, $BPLRequired);
            return response()->json([
                'message' => 'डेटा सफलतापूर्वक दर्ज किया गया है।',
                'status' => 'success',
                'Applicant_ID' => $userId,
                'tbl_user_detail_data' => $data,
                'ExpRequired' => $ExpRequired,
                'WidowRequired' => $WidowRequired,
                'BPLRequired' => $BPLRequired,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function education_details_update(Request $request, $id = 0)
    {
        try {
            $applicantId = $request->input('applicant_row_id');
            $apply_row_id = $request->input('apply_row_id');
            $qualiIds = $request->input('fk_Quali_ID', []);
            $subjectIds = $request->input('fk_subject_id', []);
            $yearsPassing = $request->input('year_passing', []);
            $obtainedMarks = $request->input('obtained_marks', []);
            $totalMarks = $request->input('total_marks', []);
            $percentages = $request->input('percentage', []);
            $grades = $request->input('grade', []);
            $qualificationBoards = $request->input('qualification_board', []);

            // Retrieve saved post apply data from session
            $applyData = DB::select("SELECT * FROM tbl_user_post_apply WHERE apply_id = ?", bindings: [$apply_row_id]);

            $Post_ID = $applyData[0]->fk_post_id;
            $post_data = DB::select("SELECT post.*,q.Quali_Name as Qualification_name FROM master_post AS post
                                            LEFT JOIN master_qualification AS q ON post.Quali_ID=q.Quali_ID
                                            WHERE post.post_id=? AND post.is_disable = 1", bindings: [$Post_ID]);
            if (empty($post_data)) {
                return response()->json([
                    'message' => 'चयनित पोस्ट उपलब्ध नहीं है या डिसेबल हो चुकी है।',
                    'status' => 'error'
                ]);
            }
            $post_min_qualification_id = $post_data[0]->Quali_ID;
            $post_title = $post_data[0]->title;
            $Qualification_name = $post_data[0]->Qualification_name;


            $finalQualiIds = []; // For fetching qualification names
            $allEducationData = []; // For DB insert

            foreach ($qualiIds as $index => $qualiId) {
                $subjectId = $subjectIds[$index] ?? null;
                $yearPassing = $yearsPassing[$index] ?? null;
                $obtained = $obtainedMarks[$index] ?? null;
                $total = $totalMarks[$index] ?? null;
                $percent = $percentages[$index] ?? null;
                $grade = $grades[$index] ?? null;
                $board = $qualificationBoards[$index] ?? null;

                $isAllEmpty = empty($qualiId) &&
                    empty($subjectId) &&
                    empty($yearPassing) &&
                    empty($obtained) &&
                    empty($total) &&
                    empty($board);

                $isPartial = empty($subjectId) || empty($qualiId) ||
                    empty($yearPassing) ||
                    empty($obtained) ||
                    empty($total) ||
                    empty($board);

                if ($isAllEmpty || $isPartial) {
                    continue; // Skip incomplete or empty rows
                }

                if (!empty($qualiId)) {
                    $finalQualiIds[] = $qualiId;
                }

                $allEducationData[] = [
                    'fk_applicant_id' => $applicantId,
                    'fk_Quali_ID' => $qualiId,
                    'fk_subject_id' => $subjectId,
                    'year_passing' => $yearPassing,
                    'obtained_marks' => $obtained,
                    'total_marks' => $total,
                    'percentage' => $percent,
                    'fk_grade_id' => $grade,
                    'qualification_board' => $board,
                    'create_ip' => $request->ip(),
                    'Created_by' => session()->get('uid'),
                    'Created_at' => now(),
                    'Created_on' => now(),
                ];
            }

            // Check if min qualification id exists in user's qualification ids
            if (!in_array($post_min_qualification_id, $finalQualiIds)) {
                return response()->json([
                    'message' => "$post_title पद के लिए न्यूनतम योग्यता $Qualification_name है, कृपया आवश्यक जानकारी भरें।",
                    'status' => 'error'
                ]);
            }


            // ## Remove existing qualifications for this applicant
            DB::table('tbl_applicant_education_qualification')
                ->where('fk_applicant_id', $applicantId)
                ->delete();

            // ## Insert new records
            if (!empty($allEducationData)) {
                DB::table('tbl_applicant_education_qualification')->insert($allEducationData);
            }

            DB::update('UPDATE tbl_user_post_apply SET stepCount = 3 WHERE apply_id = ?', [$apply_row_id]);


            // ## Fetch qualification names to return (if needed)
            $qualificationData = [];
            if (!empty($finalQualiIds)) {
                $placeholders = implode(',', array_fill(0, count($finalQualiIds), '?'));

                $qualificationRows = DB::select(
                    "SELECT Quali_ID, Quali_Name FROM master_qualification WHERE Quali_ID IN ($placeholders)",
                    $finalQualiIds
                );

                foreach ($qualificationRows as $row) {
                    $qualificationData[$row->Quali_ID] = $row->Quali_ID;
                }
            }

            return response()->json([
                'message' => "डेटा सफलतापूर्वक अपडेट कर लिया गया है।",
                'status' => 'success',
                'applicant_id' => $applicantId,
                'user_qualifications' => $qualificationData
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function experience_details_update(Request $request, $id = 0)
    {
        $rules = [
            //     'org_name' => 'array',
            //     'org_name.*' => 'string',
            //     'org_type' => 'array',
            //     'org_type.*' => 'required',
            //     'desg_name' => 'array',
            //     'desg_name.*' => 'string',
            //     'nature_work' => 'array',
            //     'nature_work.*' => 'string',
            //     'salary' => 'array',
            //     'salary.*' => 'string',
            //     'org_address' => 'array',
            //     'org_address.*' => 'string',
            //     'org_contact' => 'array',
            //     'org_contact.*' => 'string|size:10',
            //     'date_from' => 'array',
            //     'date_from.*' => 'date',
            //     'date_to' => 'array',
            //     'date_to.*' => 'date',
            //     'total_experience' => 'array',
            //     'total_experience.*' => 'string',
            //     'ngo_no' => 'array',
            //     'ngo_no.*' => 'nullable|string',
            'exp_document' => 'nullable',
            'exp_document.*' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',


        ];

        $messages = [
            'exp_document.mimes' => 'केवल PDF फ़ाइल ही मान्य है।',
            'exp_document.max' => 'फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।',
            'exp_document.*.mimes' => 'सभी अनुभव प्रमाण पत्र PDF फॉर्मेट में होनी चाहिए।',
            'exp_document.*.max' => 'सभी अनुभव प्रमाण पत्र 2MB से कम आकार की होनी चाहिए।',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => "कृपया सभी आवश्यक फ़ील्ड भरें! सुनिश्चित करें कि सभी आवश्यक फ़ील्ड सही तरीके से भरे गए हैं।",
                'status' => 'error',
                'errors' => $validator->errors(),
            ]);
        }

        // Manually validate dates
        foreach ($request->date_from as $i => $fromDate) {
            if (isset($request->date_to[$i]) && $request->date_to[$i] < $fromDate) {
                return response()->json([
                    'message' => "अनुभव की तिथि गलत है: 'कब तक' की तिथि 'कब से' से पहले नहीं हो सकती।",
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }
        }
        try {
            $applicantId = $request->input('applicant_row_id');
            $apply_row_id = $request->input('apply_row_id');

            if (!$applicantId) {
                throw new \Exception("Applicant ID is missing in the request.");
            }

            $experienceData = [];
            $orgNames = $request['org_name'] ?? [];

            //  Fetch old experience documents before deleting
            $oldExperiences = experience_detail::where('Applicant_ID', $applicantId)->get()->keyBy(function ($item, $key) {
                return $key;
            });

            // Delete old records
            experience_detail::where('Applicant_ID', $applicantId)->delete();
            DB::commit();

            $orgNames = $request->input('org_name', []);

            $orgCount = count($orgNames);
            $skippedIndexes = [];

            foreach ($orgNames as $index => $orgName) {
                $experienceEntries = [];

                // Completely blank row – skip
                if (
                    empty($orgName) &&
                    empty($request['org_type'][$index] ?? null) &&
                    empty($request['desg_name'][$index] ?? null) &&
                    empty($request['date_from'][$index] ?? null) &&
                    empty($request['date_to'][$index] ?? null) &&
                    empty($request['total_experience'][$index] ?? null) &&
                    empty($request['nature_work'][$index] ?? null) &&
                    empty($request['salary'][$index] ?? null) &&
                    empty($request['org_address'][$index] ?? null) &&
                    empty($request['org_contact'][$index] ?? null) &&
                    empty($request['exp_document'][$index] ?? null)
                ) {
                    continue;
                }

                // If even one required field is missing – skip this record
                if (
                    empty($orgName) ||
                    empty($request['org_type'][$index] ?? null) ||
                    empty($request['desg_name'][$index] ?? null) ||
                    empty($request['date_from'][$index] ?? null) ||
                    empty($request['date_to'][$index] ?? null) ||
                    empty($request['total_experience'][$index] ?? null) ||
                    // empty($request['exp_document'][$index] ?? null) ||
                    empty($request['nature_work'][$index] ?? null) ||
                    empty($request['org_address'][$index] ?? null) ||
                    empty($request['org_contact'][$index] ?? null) ||
                    empty($request['salary'][$index] ?? null)
                ) {
                    $skippedIndexes[] = $index;
                    continue;
                }

                $filePath = null;

                // Check if a file is uploaded for this specific index
                $file = $request->file("exp_document.$index");

                if ($file && $file->isValid() && $file->getSize() > 0) {
                    $uploadedFile = $request->file("exp_document")[$index];

                    // Call your custom upload helper
                    $filePath = UtilController::upload_file(
                        $uploadedFile,
                        'file',
                        'uploads',
                        ['pdf'],
                        ['application/pdf']
                    );
                } else {
                    // Use old file path if new file not uploaded
                    $filePath = $oldExperiences[$index]->exp_document ?? null;
                }

                // prepared the array for insert DB
                $experienceEntries[] = [
                    'Applicant_ID' => $applicantId,
                    'Organization_Name' => $orgName,
                    'Organization_Type' => $request['org_type'][$index] ?? null,
                    'NGO_No' => $request['ngo_no'][$index] ?? null,
                    'Designation' => $request['desg_name'][$index] ?? null,
                    'Date_From' => $request['date_from'][$index] ?? null,
                    'Date_To' => $request['date_to'][$index] ?? null,
                    'Total_Experience' => $request['total_experience'][$index] ?? '',
                    'Nature_Of_Work' => $request['nature_work'][$index] ?? null,
                    'salary' => $request['salary'][$index] ?? null,
                    'org_address' => $request['org_address'][$index] ?? null,
                    'org_contact' => $request['org_contact'][$index] ?? null,
                    'exp_document' => $filePath,
                    'IP_Address' => $request->ip(),
                    'Created_By' => session()->get('uid'),
                    'Created_On' => now(),
                ];

                if (count($experienceEntries) > 0) {
                    experience_detail::insert($experienceEntries);
                }
            }


            $Applicant_id = session()->get('user_row_id');
            $apply_id = session()->get('apply_id');
            DB::update('UPDATE tbl_user_post_apply SET stepCount = 4 WHERE apply_id = ?', [$apply_id]);


            return response()->json([
                'message' => count($skippedIndexes) > 0
                    ? "आपने कुछ अनुभव की जानकारी पूरी नही दी है ,यह मान्य नही होगा।"
                    : "सभी अनुभव प्रविष्टियाँ सफलतापूर्वक सहेजी गईं।",
                'icon' => count($skippedIndexes) > 0 ? "warning" : "success",
                'skipped_indexes' => $skippedIndexes,
                'status' => 'success',
                'applicant_id' => $applicantId
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(), // <-- Swal will show this
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function document_details_update(Request $request, $id = 0)
    {
        $applicantID = session()->get('uid');
        $applicant_row_id = $request->input('applicant_row_id');
        $apply_row_id = $request->input('apply_row_id');

        if (!$applicantID) {
            return response()->json([
                'status' => 'error',
                'message' => 'आवेदिका विवरण नहीं मिला। कृपया फिर से लॉगिन करें।',
                'errors' => ['session' => ['Session UID is missing.']]
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Apply filter: where Applicant_ID = session UID AND ID = applicant_row_id
            $applicantDetail = user_detail::where('Applicant_ID', $applicantID)
                ->where('ID', $applicant_row_id)
                ->first();

            if (!$applicantDetail) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'आवेदिका विवरण नहीं मिला। कृपया समर्थन से संपर्क करें।',
                    'errors' => ['applicant' => ['Applicant details not found.']]
                ], 404);
            }

            // Validation Rules
            $rules = [
                'document_photo' => 'file|mimes:jpeg,jpg,png|mimetypes:image/jpeg,image/png|max:100',
                'document_sign' => 'file|mimes:jpeg,jpg,png|mimetypes:image/jpeg,image/png|max:50',
                'document_adhaar' => 'file|mimes:jpeg,jpg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:2048',
                'domicile' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
                '5th_marksheet' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
                '8th_marksheet' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'ssc_marksheet' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'inter_marksheet' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'epic_document' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
            ];

            $caste = $request->input('selectedCaste');
            $caste_certificate = $request->file('caste_certificate');

            if ($caste !== 'सामान्य' && !$caste_certificate) {
                $rules['caste_certificate'] = 'file|mimes:pdf|max:2048';
            }

            // Qualification session
            $qualifications = session()->get('qualifications', []);
            $qualificationFields = [
                '5th' => '5th_marksheet',
                '8th' => '8th_marksheet',
                '10th' => 'ssc_marksheet',
                '12th' => 'inter_marksheet',
                'other' => 'other_marksheet',
            ];

            foreach ($qualifications as $qualification) {
                if (isset($qualificationFields[$qualification])) {
                    $field = $qualificationFields[$qualification];

                    // Special handling for other_marksheet (multiple files)
                    if ($field === 'other_marksheet') {
                        $rules[$field] = 'array';
                        $rules[$field . '.*'] = 'file|mimes:pdf|mimetypes:application/pdf|max:2048';
                    } else {
                        $rules[$field] = 'file|mimes:pdf|mimetypes:application/pdf|max:2048';
                    }
                }
            }

            $optionalFields = [
                'bpl_marksheet' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'widow_certificate' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:2048',
                'other_marksheet' => 'nullable|array',
                'other_marksheet.*' => 'file|mimes:pdf|mimetypes:application/pdf|max:2048',
            ];

            foreach ($qualifications as $qualification) {
                if (isset($qualificationFields[$qualification])) {
                    unset($optionalFields[$qualificationFields[$qualification]]);
                    if ($qualificationFields[$qualification] === 'other_marksheet') {
                        unset($optionalFields['other_marksheet.*']);
                    }
                }
            }

            $post_id = DB::table('tbl_user_post_apply')
                ->where('apply_id', $apply_row_id)
                ->value('fk_post_id');

            $rules = array_merge($rules, $optionalFields);
            $messages = [
                'document_photo.required' => 'पासपोर्ट साइज़ फोटो अपलोड करना अनिवार्य है।',
                'document_sign.required' => 'हस्ताक्षर अपलोड करना अनिवार्य है।',
                'domicile.required' => 'स्थानीय निवास प्रमाण पत्र अपलोड करना अनिवार्य है।',
                // 'epic_document.required' => 'अद्यतन मतदाता सूची अपलोड करना अनिवार्य है।',
                'caste_certificate.required' => 'जाति प्रमाण पत्र अपलोड करना अनिवार्य है।',
                '5th_marksheet.required' => '5th अंक सूची अपलोड करना अनिवार्य है।',
                '8th_marksheet.required' => '8th अंक सूची अपलोड करना अनिवार्य है।',
                'ssc_marksheet.required' => '10th अंक सूची अपलोड करना अनिवार्य है।',
                'inter_marksheet.required' => '12th अंक सूची अपलोड करना अनिवार्य है।',
                'other_marksheet.array' => 'अन्य प्रमाण पत्र अपलोड करना अनिवार्य है।',
                'document_photo.mimes' => 'कृपया पासपोर्ट साइज़ फोटो के लिए PNG, JPEG, JPG फाइल अपलोड करें।',
                'document_sign.mimes' => 'कृपया हस्ताक्षर के लिए PNG, JPEG, JPG फाइल अपलोड करें।',
                'document_adhaar.mimes' => 'कृपया आधार कार्ड के लिए PNG, JPEG, JPG,PDF फाइल अपलोड करें।',
                'domicile.mimes' => 'कृपया स्थानीय निवास प्रमाण पत्र के लिए PDF फाइल अपलोड करें।',
                'epic_document.mimes' => 'कृपया अद्यतन मतदाता सूची के लिए PDF फाइल अपलोड करें।',
                'caste_certificate.mimes' => 'कृपया जाति प्रमाण पत्र के लिए PDF फाइल अपलोड करें।',
                '5th_marksheet.mimes' => 'कृपया 5th अंक सूची के लिए PDF फाइल अपलोड करें।',
                '8th_marksheet.mimes' => 'कृपया 8th अंक सूची के लिए PDF फाइल अपलोड करें।',
                'ssc_marksheet.mimes' => 'कृपया 10th अंक सूची के लिए PDF फाइल अपलोड करें।',
                'inter_marksheet.mimes' => 'कृपया 12th अंक सूची के लिए PDF फाइल अपलोड करें।',
                'other_marksheet.*.mimes' => 'कृपया अन्य प्रमाण पत्र के लिए PDF फाइल अपलोड करें।',
                '*.mimes' => 'अमान्य फ़ाइल प्रारूप। कृपया स्वीकार्य फ़ाइल प्रकार PDF अपलोड करें।',
                '*.mimetypes' => 'अमान्य फ़ाइल MIME प्रकार। कृपया स्वीकार्य फ़ाइल प्रकार PDF अपलोड करें।',
                '*.max' => 'फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।',
                'other_marksheet.*.max' => 'प्रत्येक फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'कृपया सभी आवश्यक दस्तावेज़ अपलोड करें।',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $allowedExtensions = ['jpeg', 'jpg', 'png', 'pdf'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $baseUploadPath = 'Uploads/applicant/doc';

            // Array of document types (excluding other_marksheet)
            $documents = [
                'document_photo' => 'Document_Photo',
                'document_sign' => 'Document_Sign',
                'document_adhaar' => 'Document_Aadhar',
                'caste_certificate' => 'Document_Caste',
                'domicile' => 'Document_Domicile',
                '5th_marksheet' => 'Document_5th',
                '8th_marksheet' => 'Document_8th',
                'ssc_marksheet' => 'Document_SSC',
                'inter_marksheet' => 'Document_Inter',
                'bpl_marksheet' => 'Document_BPL',
                'widow_certificate' => 'Document_Widow',
                'exp_document' => 'Document_Exp',
                'epic_document' => 'Document_Epic'
            ];

            // Process single file documents
            foreach ($documents as $inputName => $fieldName) {
                if ($request->hasFile($inputName)) {
                    $oldFile = $applicantDetail->$fieldName;

                    if (!empty($oldFile) && file_exists(public_path("{$baseUploadPath}/{$oldFile}"))) {
                        unlink(public_path("{$baseUploadPath}/{$oldFile}"));
                    }

                    $uploadedFile = $request->file($inputName);

                    $uploadResult = UtilController::upload_file(
                        $uploadedFile,
                        $inputName,
                        'uploads',
                        $allowedExtensions,
                        $allowedMimeTypes
                    );

                    if (!$uploadResult) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => "{$inputName} फ़ाइल सहेजने में असमर्थ!",
                            'errors' => [$inputName => ["Unable to save {$inputName} file."]]
                        ], 422);
                    }

                    $applicantDetail->$fieldName = $uploadResult;
                }
            }

            // Special handling for other_marksheet (multiple files)
            $newOtherDocumentsData = [];

            // Check if new files are uploaded
            if ($request->hasFile('other_marksheet')) {
                $otherFiles = $request->file('other_marksheet');

                // Ensure it's always an array
                if (!is_array($otherFiles)) {
                    $otherFiles = [$otherFiles];
                }

                // Get existing other documents
                $existingOtherDocs = DB::table('tbl_user_other_documents')
                    ->where('fk_applicant_id', $applicant_row_id)
                    ->get();

                // Delete existing files and records (only if new files are being uploaded)
                foreach ($existingOtherDocs as $doc) {
                    if (!empty($doc->other_documents) && file_exists(public_path("{$baseUploadPath}/{$doc->other_documents}"))) {
                        unlink(public_path("{$baseUploadPath}/{$doc->other_documents}"));
                    }
                }
                // dd($request->other_marksheet);
                // Delete all existing records from database
                // if ($existingOtherDocs->count() > 0) {
                //     DB::table('tbl_user_other_documents')
                //         ->where('fk_applicant_id', $applicant_row_id)
                //         ->delete();
                // }

                // Process new files
                foreach ($otherFiles as $file) {
                    if ($file && $file->isValid()) {
                        $uploadResult = UtilController::upload_file(
                            $file,
                            'other_marksheet_' . uniqid() . '_' . time(),
                            'uploads',
                            ['pdf'], // Only PDF allowed
                            ['application/pdf'] // Only PDF MIME type
                        );

                        if (!$uploadResult) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Unable to save other marksheet file!',
                                'errors' => ['other_marksheet' => ['Unable to save other marksheet file.']]
                            ], 422);
                        }

                        // Store for batch insert
                        $newOtherDocumentsData[] = [
                            'other_documents' => $uploadResult,
                            'fk_applicant_id' => $applicant_row_id,
                            'file_name' => $file->getClientOriginalName(),
                            'created_at' => now(),
                            'updated_at' => now(),
                            'create_ip' => $request->ip(),
                            'update_ip' => $request->ip(),
                        ];
                    }
                }

                // Insert new other documents if any
                if (!empty($newOtherDocumentsData)) {
                    DB::table('tbl_user_other_documents')->insert($newOtherDocumentsData);
                }

                // Update Document_other field in tbl_user_detail
                if (!empty($newOtherDocumentsData) && count($newOtherDocumentsData) > 0) {
                    // Use the first new document
                    $applicantDetail->Document_other = $newOtherDocumentsData[0]['other_documents'];
                } else {
                    // If no new documents were uploaded (empty array case), set to null
                    $applicantDetail->Document_other = null;
                }
            } else {
                // No new files uploaded, check if any existing files were marked for deletion
                $deletedOtherDocs = [];
                if ($request->has('deleted_other_docs') && !empty($request->input('deleted_other_docs'))) {
                    $deletedOtherDocs = json_decode($request->input('deleted_other_docs'), true);
                    if (is_array($deletedOtherDocs) && !empty($deletedOtherDocs)) {
                        foreach ($deletedOtherDocs as $deletedFile) {
                            // Delete file from storage
                            if (!empty($deletedFile) && file_exists(public_path("{$baseUploadPath}/{$deletedFile}"))) {
                                unlink(public_path("{$baseUploadPath}/{$deletedFile}"));
                            }
                            // Delete record from database
                            DB::table('tbl_user_other_documents')
                                ->where('fk_applicant_id', $applicant_row_id)
                                ->where('other_documents', $deletedFile)
                                ->delete();
                        }
                    }
                }

                // Update Document_other field with first existing document if available
                $remainingDocs = DB::table('tbl_user_other_documents')
                    ->where('fk_applicant_id', $applicant_row_id)
                    ->orderBy('id', 'asc')
                    ->first();

                if ($remainingDocs) {
                    $applicantDetail->Document_other = $remainingDocs->other_documents;
                } else {
                    $applicantDetail->Document_other = null;
                }
            }

            $applicantDetail->Last_Updated_By = $applicantID;
            $applicantDetail->Last_Updated_On = now();

            $applicantDetail_status = $applicantDetail->save();

            if ($applicantDetail_status) {
                DB::update('UPDATE tbl_user_post_apply SET stepCount = ? WHERE apply_id = ?', [5, $apply_row_id]);
                DB::commit();
                Session::forget('session_qualification');

                return response()->json([
                    'status' => 'success',
                    'message' => 'डेटा सफलतापूर्वक दर्ज कर लिया गया हैं।',
                    'applicant_id' => md5($applicant_row_id),
                    'application_id' => md5($apply_row_id),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'डेटा सहेजने में त्रुटि हुई। कृपया पुनः प्रयास करें।',
                    'errors' => ['database' => ['Failed to save applicant details.']]
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            // If validation error thrown by user code
            if ($th instanceof \Exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $th->getMessage(),
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Default server exception
            return response()->json([
                'status' => 'error',
                'message' => 'सर्वर त्रुटि: कृपया समर्थन से संपर्क करें।',
                'errors' => ['server' => [$th->getMessage()]]
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function davaApatti(Request $request)
    {
        /* POST REQUEST */
        if ($request->isMethod('post')) {

            $request->validate([
                'fk_apply_id' => 'required|integer',
                'request_type' => 'required|in:claim,objection',
                'request_category' => 'required|string',
                'description' => 'required|string|max:1500',
            ], [
                'fk_apply_id.required' => 'पोस्ट / आवेदन संख्या अनिवार्य है।',
                'request_type.required' => 'दावा–आपत्ति का प्रकार चुनें।',
                'request_category.required' => 'विषय चुनना अनिवार्य है।',
                'description.required' => 'विस्तृत विवरण अनिवार्य है।',
            ]);

            $applyData = DB::selectOne(
                "SELECT fk_post_id, fk_applicant_id,post_projects
                FROM tbl_user_post_apply
                WHERE apply_id = ?",
                [$request->fk_apply_id]
            );

            if (!$applyData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'अमान्य आवेदन चयनित किया गया है।'
                ], 200);
            }

            $alreadySubmitted = DB::table('tbl_claim_objection')
                ->where('fk_apply_id', $request->fk_apply_id)
                ->where('fk_applicant_id', $applyData->fk_applicant_id)
                ->where('fk_post_id', $applyData->fk_post_id)
                ->where('claim_status', 'Submitted')
                ->exists();

            if ($alreadySubmitted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इस पोस्ट के लिए दावा–आपत्ति पहले से सबमिट की जा चुकी है।'
                ], 200);
            }

            DB::table('tbl_claim_objection')->insert([
                'fk_apply_id' => $request->fk_apply_id,
                'fk_post_id' => $applyData->fk_post_id,
                'fk_applicant_id' => $applyData->fk_applicant_id,
                'project_code' => $applyData->post_projects,
                'request_type' => $request->request_type,
                'request_category' => $request->request_category,
                'description' => $request->description,
                'claim_status' => 'Submitted',
                'created_by' => session('uid'),
                'updated_by' => session('uid'),
                'created_at' => now(),
                'updated_at' => now(),
                'create_ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'दावा–आपत्ति सफलतापूर्वक सबमिट हो गई है।'
            ]);
        }

        /* GET REQUEST (FORM LOAD)*/
        $applicant_apply_list = DB::select(
            "SELECT app.apply_id,
                app.application_num,
                p.title,
                app.status
            FROM tbl_user_post_apply app
            LEFT JOIN tbl_user_detail ud ON ud.ID = app.fk_applicant_id
            LEFT JOIN master_post p ON p.post_id = app.fk_post_id 
            WHERE ud.Applicant_ID = ?
            AND app.status IN ('Verified', 'Rejected')",
            [session('uid')]
        );

        return view('candidate.dava_aapatti', compact('applicant_apply_list'));
    }

    public function getClaimObjectionList()
    {
        $claimObjections = DB::table('tbl_claim_objection as co')
            ->leftJoin('tbl_user_post_apply as ua', 'ua.apply_id', '=', 'co.fk_apply_id')
            ->leftJoin('record_user_detail_map as rudm', 'rudm.fk_apply_id', '=', 'ua.apply_id')
            ->leftJoin('master_post as p', 'p.post_id', '=', 'co.fk_post_id')
            ->where('rudm.Applicant_ID', session('uid'))
            ->orderByDesc('co.created_at')
            ->select(
                'co.claim_id',
                'co.request_type',
                'co.request_category',
                'co.description',
                'co.claim_status',
                'co.admin_remark',
                'co.created_at',
                'co.updated_at',
                'p.title as post_title',
                'ua.application_num'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $claimObjections
        ]);
    }

    public function get_project($districtID)
    {
        try {
            $projects = DB::table('master_projects')
                ->where('district_lgd_code', $districtID)
                ->select('project_code', 'project as project_name')
                ->orderBy('project')
                ->get();

            return response()->json([
                'status' => 'success',
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'प्रोजेक्ट डेटा लोड करने में त्रुटि।'
            ], 500);
        }
    }
    public function get_nagar($districtID)
    {
        try {
            // Nagar (Urban)
            $nagars = DB::table('master_nnn')
                ->select('std_nnn_code', 'nnn_name')
                ->where('district_lgd_code', $districtID)
                ->orderBy('nnn_name', 'ASC')
                ->get();
            return response()->json([
                'status' => 'success',
                'nagars' => $nagars
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'नगर निकाय डेटा लोड करने में त्रुटि।'
            ], 500);
        }
    }
    public function get_panchayats($blockID)
    {
        try {
            // Nagar (Urban)
            $nagars = DB::table('master_nnn')
                ->select('std_nnn_code', 'nnn_name')
                ->where('district_lgd_code', $blockID)
                ->orderBy('nnn_name', 'ASC')
                ->get();
            return response()->json([
                'status' => 'success',
                'nagars' => $nagars
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'नगर निकाय डेटा लोड करने में त्रुटि।'
            ], 500);
        }
    }

    public function get_blocks($districtID)
    {
        try {
            $blocks = DB::table('master_blocks')
                ->where('district_lgd_code', $districtID)
                ->select('block_lgd_code', 'block_name_hin')
                ->orderBy('block_name_hin')
                ->get();
            return response()->json([
                'status' => 'success',
                'blocks' => $blocks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'विकासखंड डेटा लोड करने में त्रुटि।'
            ], 500);
        }
    }


    public function dava_aapati_suchna(Request $request)
    {

        $anatim_list = DB::select("SELECT 
                                                l.anantim_id, 
                                                a.Advertisement_Title, 
                                                post.title as postname, 
                                                proj.project,
                                                
                                                -- Handle multiple std_nnn_code (JSON array) for nnn names
                                                (
                                                    SELECT GROUP_CONCAT(DISTINCT nnn2.nnn_name ORDER BY nnn2.nnn_name SEPARATOR ', ')
                                                    FROM JSON_TABLE(
                                                        l.std_nnn_code,
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
                                                        l.gp_nnn_code,
                                                        '$[*]' COLUMNS (
                                                            gp_code VARCHAR(20) PATH '$'
                                                        )
                                                    ) AS gp_codes
                                                    LEFT JOIN master_panchayats gp2 ON gp_codes.gp_code = gp2.panchayat_lgd_code
                                                    WHERE gp_codes.gp_code IS NOT NULL
                                                ) AS panchayat_names,
                                                
                                                -- Also get village names if needed
                                                (
                                                    SELECT GROUP_CONCAT(DISTINCT v.village_name_hin ORDER BY v.village_name_hin SEPARATOR ', ')
                                                    FROM JSON_TABLE(
                                                        l.village_code,
                                                        '$[*]' COLUMNS (
                                                            village_val VARCHAR(20) PATH '$'
                                                        )
                                                    ) AS village_vals
                                                    LEFT JOIN master_villages v ON village_vals.village_val = v.village_code
                                                    WHERE village_vals.village_val IS NOT NULL
                                                ) AS village_names,
                                                
                                                l.claim_start_date, 
                                                l.claim_end_date, 
                                                l.anantim_list_file,
                                                l.district_lgd_code,
                                                l.project_code,
                                                l.std_nnn_code,
                                                l.gp_nnn_code,
                                                l.village_code,
                                                l.created_at
                                                
                                            FROM tbl_anantim_list l
                                            LEFT JOIN master_advertisement a ON a.Advertisement_ID = l.fk_advertiesment_id
                                            LEFT JOIN master_post post ON l.fk_post_id = post.post_id 
                                            LEFT JOIN master_projects proj ON l.project_code = proj.project_code
                                            
                                            WHERE l.anantim_id IS NOT NULL
                                            
                                            ORDER BY l.created_at DESC
                                        ");

        $districts = DB::table('master_district')
            ->select('District_Code_LGD as id', 'name')
            ->orderBy('name')
            ->get();

        // Fetch all projects
        $projects = DB::table('master_projects')
            ->select('project_code as id', 'project as name')
            ->orderBy('project')
            ->get();

        return view('dava_aapati_suchna', compact('anatim_list', 'districts', 'projects'));
    }
    public function getDwapatiFilters($districtID = null, $projectID = null)
    {
        try {
            // Validate inputs
            if (!$districtID || !$projectID) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'जिला और परियोजना दोनों का चयन करना आवश्यक है।'
                ], 400);
            }

            $query = "SELECT 
                l.anantim_id, 
                a.Advertisement_Title, 
                post.title as postname, 
                proj.project,
                
                -- Handle multiple std_nnn_code (JSON array) for nnn names
                (
                    SELECT GROUP_CONCAT(DISTINCT nnn2.nnn_name ORDER BY nnn2.nnn_name SEPARATOR ', ')
                    FROM JSON_TABLE(
                        l.std_nnn_code,
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
                        l.gp_nnn_code,
                        '$[*]' COLUMNS (
                            gp_code VARCHAR(20) PATH '$'
                        )
                    ) AS gp_codes
                    LEFT JOIN master_panchayats gp2 ON gp_codes.gp_code = gp2.panchayat_lgd_code
                    WHERE gp_codes.gp_code IS NOT NULL
                ) AS panchayat_names,
                
                -- Also get village names if needed
                (
                    SELECT GROUP_CONCAT(DISTINCT v.village_name_hin ORDER BY v.village_name_hin SEPARATOR ', ')
                    FROM JSON_TABLE(
                        l.village_code,
                        '$[*]' COLUMNS (
                            village_val VARCHAR(20) PATH '$'
                        )
                    ) AS village_vals
                    LEFT JOIN master_villages v ON village_vals.village_val = v.village_code
                    WHERE village_vals.village_val IS NOT NULL
                ) AS village_names,
                
                l.claim_start_date, 
                l.claim_end_date, 
                l.anantim_list_file,
                l.district_lgd_code,
                l.project_code,
                l.std_nnn_code,
                l.gp_nnn_code,
                l.village_code,
                l.created_at
                
            FROM tbl_anantim_list l
            LEFT JOIN master_advertisement a ON a.Advertisement_ID = l.fk_advertiesment_id
            LEFT JOIN master_post post ON l.fk_post_id = post.post_id 
            LEFT JOIN master_projects proj ON l.project_code = proj.project_code
            
            WHERE l.anantim_id IS NOT NULL
            AND l.district_lgd_code = ?
            AND l.project_code = ?
            
            ORDER BY l.created_at DESC";

            $anatim_list = DB::select($query, [$districtID, $projectID]);

            return response()->json([
                'status' => 'success',
                'data' => $anatim_list
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDwapatiFilters: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'फ़िल्टर डेटा लोड करने में त्रुटि।',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
