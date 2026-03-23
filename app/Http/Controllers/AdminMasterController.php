<?php

namespace App\Http\Controllers;

use App\Models\master_awc;
use App\Models\master_district;
use App\Models\master_project;
use App\Models\master_sector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class AdminMasterController extends Controller
{

    public function get_district(Request $request)
    {
        $post_id = (@$request->post_id && $request->post_id) ? $request->post_id : 0;

        if ($request->ajax() && $post_id) {
            // First get cat_id from master_post
            $post = DB::table('master_post')
                ->select('cat_id')
                ->where('post_id', $post_id)
                ->first();

            // If no post found, return empty array
            if (!$post) {
                return response()->json([]);
            }

            // Check cat_id and fetch districts accordingly
            if ($post->cat_id == 1) {
                // For cat_id = 1, get all districts from master_district
                $district_details = DB::table('master_district')
                    ->select('District_Code_LGD', 'name')
                    ->get();

                return response()->json($district_details);
            } elseif ($post->cat_id == 2) {
                // For cat_id = 2, get districts from post_map
                $districts = DB::select("SELECT fk_district_id FROM `post_map` WHERE fk_post_id = ?", [$post_id]);

                // If no districts found, return empty array
                if (empty($districts)) {
                    return response()->json([]);
                }

                // Extract all dist_ids into an array
                $dist_ids = array_column($districts, 'fk_district_id');

                // Get district details from master_district using the dist_ids
                $district_details = DB::table('master_district')
                    ->select('District_Code_LGD', 'name')
                    ->whereIn('District_Code_LGD', $dist_ids)
                    ->get();

                return response()->json($district_details);
            }
        }
        return response()->json([]);
    }


    public function get_post_questions(Request $request)
    {
        $post_id = $request->post_id ?? 0;
        $applicant_id = session()->get('sess_id') ?? 0;

        if ($request->ajax() && $post_id) {
            $questions = DB::table('post_question_map as pqm')
                ->join('master_post_questions as qus', 'pqm.fk_ques_id', '=', 'qus.ques_ID')
                ->select(
                    'pqm.post_map_id',
                    'pqm.fk_post_id',
                    'pqm.fk_ques_id',
                    'qus.ques_name',
                    'qus.answer_options',
                    'qus.parent_id',
                    'qus.parent_ans',
                    'qus.ans_type',
                )
                ->where('pqm.fk_post_id', $post_id)
                ->whereNull('pqm.deleted_at')
                ->orderBy('qus.ques_order_id', 'asc')->get();

            $fkQuesIds = $questions->pluck('fk_ques_id')->toArray();

            // Married, Marry Date And childCount question IDs to check
            $targetIds = [1, 7, 8];
            $answers = [];

            // foreach ($targetIds as $qid) {
            //     if (in_array($qid, $fkQuesIds)) {
            //         $result = DB::table('tbl_user_post_question_answer as answer')
            //             ->leftJoin('post_question_map as map', 'answer.post_map_id', '=', 'map.post_map_id')
            //             ->leftJoin('tbl_user_detail as detail', 'answer.applicant_id', '=', 'detail.ID')
            //             ->select('map.fk_ques_id', 'answer.answer')
            //             ->where('detail.Applicant_ID', $applicant_id)
            //             ->where('map.fk_ques_id', $qid)
            //             ->orderByDesc('answer.id')
            //             ->limit(1)
            //             ->first();

            //         if ($result) {
            //             $answers[] = $result;
            //         }
            //     }
            // }
            // dd($answers);
            return response()->json([
                'questions' => $questions,
                // 'answers' => $answers,
            ]);
        }

        return response()->json([]);
    }


    public function get_post_questions_with_Answer(Request $request)
    {
        $post_id = $request->post_id ?? 0;
        $applicant_id = session()->get('sess_id') ?? 0;

        if ($request->ajax() && $post_id) {
            // Fetch questions
            $questions = DB::table('post_question_map as pqm')
                ->join('master_post_questions as qus', 'pqm.fk_ques_id', '=', 'qus.ques_ID')
                ->select(
                    'pqm.post_map_id',
                    'pqm.fk_post_id',
                    'pqm.fk_ques_id',
                    'qus.ques_name',
                    'qus.answer_options',
                    'qus.parent_id',
                    'qus.parent_ans',
                    'qus.ans_type',
                )
                ->where('pqm.fk_post_id', $post_id)
                ->whereNull('pqm.deleted_at')
                ->orderBy('qus.ques_order_id', 'asc')->get();

            // Check if any record exists for the applicant in tbl_user_post_question_answer
            $hasAnswers = DB::table('tbl_user_post_question_answer as answer')
                ->leftJoin('tbl_user_detail as detail', 'answer.applicant_id', '=', 'detail.ID')
                ->where('detail.Applicant_ID', $applicant_id)
                ->exists();

            if ($hasAnswers) {
                // Run allAnswer query
                $allAnswer = DB::table('tbl_user_post_question_answer as answer')
                    ->leftJoin('post_question_map as map', 'answer.post_map_id', '=', 'map.post_map_id')
                    ->leftJoin('master_post_questions as ques', 'map.fk_ques_id', '=', 'ques.ques_ID')
                    ->leftJoin('tbl_user_detail as detail', 'answer.applicant_id', '=', 'detail.ID')
                    ->select(
                        'detail.ID',
                        'answer.post_id',
                        'answer.post_map_id as post_map_id',
                        'map.fk_ques_id',
                        'ques.ques_name',
                        'ques.answer_options',
                        'ques.parent_id',
                        'ques.parent_ans',
                        'ques.ans_type',
                        'answer.answer',
                        'answer.answer_file_upload',
                        'answer.date_From',
                        'answer.date_To',
                        'answer.total_experience_days'
                    )
                    ->where('detail.Applicant_ID', $applicant_id)
                    ->where('answer.post_id', $post_id)
                    ->get();

                if ($allAnswer->isNotEmpty()) {
                    // Return questions with allAnswer if records found
                    return response()->json([
                        'questions' => $questions,
                        'answers' => $allAnswer
                    ]);
                }
            }

            // If no records in allAnswer or no answers for applicant, run answers query
            $fkQuesIds = $questions->pluck('fk_ques_id')->toArray();
            $targetIds = [1, 7, 8];
            $answers = [];

            foreach ($targetIds as $qid) {
                if (in_array($qid, $fkQuesIds)) {
                    $result = DB::table('tbl_user_post_question_answer as answer')
                        ->leftJoin('post_question_map as map', 'answer.post_map_id', '=', 'map.post_map_id')
                        ->leftJoin('tbl_user_detail as detail', 'answer.applicant_id', '=', 'detail.ID')
                        ->select('map.fk_ques_id', 'answer.answer', 'answer.answer_file_upload')
                        ->where('detail.Applicant_ID', $applicant_id)
                        ->where('map.fk_ques_id', $qid)
                        ->orderByDesc('answer.id')
                        ->limit(1)
                        ->first();

                    if ($result) {
                        $answers[] = $result;
                    }
                }
            }

            // Return questions with answers (or empty answers if none found)
            return response()->json([
                'questions' => $questions,
                'answers' => $answers
            ]);
        }

        return response()->json([]);
    }


    public function get_post_skills(Request $request)
    {
        $post_id = $request->post_id ?? 0;
        $applicant_id = session()->get('sess_id') ?? 0;

        if ($request->ajax() && $post_id) {
            $skills = DB::table('post_skills_map as map')
                ->join('master_post as pm', 'map.fk_post_id', '=', 'pm.post_id')
                ->join('master_skills as ms', 'map.fk_skill_id', '=', 'ms.skill_id')
                ->select('map.post_skill_id', 'map.fk_skill_id', 'pm.title AS PostName', 'ms.skill_name AS SkillName', 'ms.skill_options')
                ->where('map.fk_post_id', $post_id)
                ->get();


            // $skills_answer = DB::table('tbl_post_skill_answer AS skills')
            //     ->join('tbl_user_post_apply AS apply', 'skills.fk_apply_id', '=', 'apply.apply_id')
            //     ->join('tbl_user_detail AS detail', 'apply.fk_applicant_id', '=', 'detail.ID')
            //     ->select('skills.*')
            //     ->where('detail.Applicant_ID', $applicant_id)
            //     ->where('apply.fk_post_id', $post_id)
            //     ->get();

            // dd($skills,$skills_answer);
            return response()->json($skills); //, $skills_answer
        }



        return response()->json([]);
    }

    public function get_post_qualification(Request $request)
    {
        $post_id = $request->post_id ?? 0;
        $applicant_id = $request->applicant_id ?? 0;

        if ($request->ajax() && $post_id && $applicant_id) {

            // Step 1: Get post qualification id & name
            $postQualification = DB::table('master_post as pm')
                ->leftJoin('master_qualification as mq', 'pm.Quali_ID', '=', 'mq.Quali_ID')
                ->select('pm.Quali_ID', 'mq.Quali_Name')
                ->where('pm.post_id', $post_id)
                ->first();

            if (!$postQualification) {
                return response()->json(['status' => 'error', 'message' => 'Post not found.']);
            }

            // Step 2: Check in same query if applicant has this qualification
            $userHasQualification = DB::table('tbl_applicant_education_qualification as ae')
                ->where('ae.fk_applicant_id', $applicant_id)
                ->where('ae.fk_Quali_ID', $postQualification->Quali_ID)
                ->exists();

            if (!$userHasQualification) {
                return response()->json([
                    'status' => 'not_matched',
                    'Quali_ID' => $postQualification->Quali_ID,
                    'qualification_name' => $postQualification->Quali_Name ?? 'N/A'
                ]);
            }

            return response()->json(['status' => 'matched']);
        }

        return response()->json(['status' => 'error']);
    }

    public function get_project(Request $request)
    {
        $dist_id = (@$request->dist_id && $request->dist_id) ? $request->dist_id : 0;
        $result = null;
        if ($request->ajax() && $dist_id) {
            $result = DB::select("SELECT * FROM `ila_pariyojna` WHERE D_Code= ?", [$dist_id]);
        }
        return response()->json($result);
    }

    public function get_sector(Request $request)
    {
        $pro_id = (@$request->pro_id && $request->pro_id) ? $request->pro_id : 0;
        $result = null;
        if ($request->ajax() && $pro_id) {
            $result = DB::select("SELECT * FROM `ila_sectors` WHERE Project_Code= ?", [$pro_id]);
        }
        return response()->json($result);
    }

    public function get_awc(Request $request)
    {
        $pro_id = (@$request->pro_id && $request->pro_id) ? $request->pro_id : 0;
        $dist_id = (@$request->dist_id && $request->dist_id) ? $request->dist_id : 0;

        $result = null;
        if ($request->ajax() && $pro_id) {
            $result = DB::select("SELECT * FROM `final_cg_awc_tbl` WHERE Project_Code_7_Digit= ? and District_LGD_Code =?", [$pro_id, $dist_id]);
        }
        return response()->json($result);
    }

    public function add_district(Request $request, $id = 0)
    {
        // if ($id) {
        // 	$data['editItem'] = $this->db->get_where('master_district', array('md5(District_Code_LGD)' => $id))->row_array();
        // } else {
        // 	$data['editItem'] = array();
        // }

        if ($request->isMethod('post')) {
            $rules = [
                'name' => 'required',
            ];

            $valid = $request->validate($rules);

            DB::beginTransaction();
            try {

                if ($request->input('button_clicked') == 'update') {
                } else {

                    $district_data = new master_district();

                    $district_data->name = $request['name'];
                    $district_data->Created_By = session()->get('sess_id');
                    $district_data->IP_Address = $request->ip();
                    $district_data->Created_On = now();
                    $district_data_status = $district_data->save();
                }

                if ($district_data_status) {

                    DB::commit();
                    return response()->json(['message' => "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं ।", 'status' => 'success']);
                } else {

                    DB::rollBack();
                    return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
                }
            } catch (\Throwable $th) {

                print ('An error occurred: ' . $th->getMessage());
                DB::rollBack();
                return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
            }
        } else {
            $district = DB::table('master_district')
                ->orderBy('District_Code_LGD', 'desc')
                ->get();

            return view('admin/add_district', compact('district'));
        }
    }

    public function add_project(Request $request, $id = 0)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'district' => 'required',
                'p_code' => 'required',
                'p_name' => 'required',

            ];

            $valid = $request->validate($rules);

            DB::beginTransaction();
            try {

                if ($request->input('button_clicked') == 'update') {
                } else {

                    $project_data = new master_project();

                    $project_data->Project_Name = $request['p_name'];
                    $project_data->Project_Code = $request['p_code'];
                    $project_data->D_Code = $request['district'];
                    $project_data_status = $project_data->save();
                }

                if ($project_data_status) {

                    DB::commit();
                    return response()->json(['message' => "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं ।", 'status' => 'success']);
                } else {

                    DB::rollBack();
                    return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
                }
            } catch (\Throwable $th) {

                print ('An error occurred: ' . $th->getMessage());
                DB::rollBack();
                return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
            }
        } else {
            $projects = DB::table('ila_pariyojna')
                ->leftJoin('master_district', 'ila_pariyojna.D_Code', '=', 'master_district.District_Code_LGD')
                ->get();

            return view('admin/add_project', compact('projects'));
        }
    }

    public function add_sector(Request $request)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'district' => 'required',
                's_code' => 'required',
                's_name' => 'required',
                'project' => 'required',

            ];

            $valid = $request->validate($rules);

            DB::beginTransaction();
            try {

                if ($request->input('button_clicked') == 'update') {
                } else {

                    $sector_data = new master_sector();

                    $sector_data->Sec_Name = $request['s_name'];
                    $sector_data->Sector_Code = $request['s_code'];
                    $sector_data->Project_Code = $request['project'];
                    $sector_data->D_Code = $request['district'];
                    $sector_data_status = $sector_data->save();
                }

                if ($sector_data_status) {

                    DB::commit();
                    return response()->json(['message' => "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं ।", 'status' => 'success']);
                } else {

                    DB::rollBack();
                    return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
                }
            } catch (\Throwable $th) {

                print ('An error occurred: ' . $th->getMessage());
                DB::rollBack();
                return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
            }
        } else {
            $sectors = DB::table('ila_sectors')
                ->leftJoin('master_district', 'ila_sectors.D_Code', '=', 'master_district.District_Code_LGD')
                ->leftJoin('ila_pariyojna', 'ila_sectors.Project_Code', '=', 'ila_pariyojna.Project_Code')
                ->get();

            return view('admin/add_sector', compact('sectors'));
        }
    }

    public function add_awc(Request $request)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'district' => 'required',
                'a_code' => 'required',
                'a_name' => 'required',
                's_code' => 'required',
                'project' => 'required',

            ];

            $valid = $request->validate($rules);

            DB::beginTransaction();
            try {

                if ($request->input('button_clicked') == 'update') {
                } else {

                    $awc_data = new master_awc();

                    $awc_data->District_LGD_Code = $request['district'];
                    $awc_data->Project_Code_7_Digit = $request['project'];
                    $awc_data->Sector_Code_9_Digit = $request['s_code'];
                    $awc_data->AWC_Code_11_Digit = $request['a_code'];
                    $awc_data->AWC_Name = $request['a_name'];
                    $awc_data_status = $awc_data->save();
                }

                if ($awc_data_status) {

                    DB::commit();
                    return response()->json(['message' => "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं ।", 'status' => 'success']);
                } else {

                    DB::rollBack();
                    return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
                }
            } catch (\Throwable $th) {

                print ('An error occurred: ' . $th->getMessage());
                DB::rollBack();
                return response()->json(['message' => "कुछ त्रुटि हुई है।", 'status' => 'error']);
            }
        } else {
            $aganbaadi_list = DB::table('final_cg_awc_tbl')
                ->leftJoin('master_district', 'final_cg_awc_tbl.District_LGD_Code', '=', 'master_district.District_Code_LGD')
                ->leftJoin('ila_pariyojna', 'final_cg_awc_tbl.Project_Code_7_Digit', '=', 'ila_pariyojna.Project_Code')
                ->leftJoin('ila_sectors', 'final_cg_awc_tbl.Sector_Code_9_Digit', '=', 'ila_sectors.Sector_Code')
                ->get();

            // return view('admin/add_awc', compact('aganbaadi_list'));
            return view('admin/add_awc', ['aganbaadi_list' => $aganbaadi_list]);
        }
    }

    public function add_gram_panchayat(Request $request)
    {
        $districtLgdCode = (int) Session::get('district_id', 0);
        $projectCode = (int) Session::get('project_code', 0);

        if ($request->isMethod('post')) {
            if (!$districtLgdCode || !$projectCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'सेशन की जानकारी नहीं मिली। कृपया फिर से लॉगिन करें।',
                ], 422);
            }

            $request->validate([
                'block_code' => 'required|integer|exists:master_blocks,block_code',
                'panchayat_code' => 'required|digits_between:1,19',
                'panchayat_name_hin' => 'required|string|max:100',
                'panchayat_name_en' => 'required|string|max:100',
            ]);

            $panchayatCode = trim((string) $request->input('panchayat_code'));

            $block = DB::table('master_blocks')
                ->where('block_code', $request->block_code)
                ->first();

            if (!$block) {
                return response()->json(['status' => 'error', 'message' => 'चयनित ब्लॉक उपलब्ध नहीं है।'], 422);
            }

            if ((int) ($block->district_lgd_code ?? 0) !== $districtLgdCode) {
                return response()->json(['status' => 'error', 'message' => 'चयनित ब्लॉक आपके जिले से मेल नहीं खाता।'], 422);
            }

            if (empty($block->block_lgd_code) || empty($block->district_code) || empty($block->district_lgd_code)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित ब्लॉक की जानकारी अधूरी है।',
                ], 422);
            }
            // DB::enableQueryLog();
            // $projectBlockExists = DB::table('projects_with_blocks')
            //     ->where('project_code', $projectCode)
            //     ->whereRaw('UPPER(TRIM(block_name)) = UPPER(TRIM(?))', [$block->block_name ?? ''])
            //     ->exists();
            // dd(DB::getQueryLog());
            // if (!$projectBlockExists) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'यह ब्लॉक वर्तमान प्रोजेक्ट के साथ मैप नहीं है।',
            //     ], 422);
            // }

            $awcGpQuery = DB::table('master_awcs')
                ->where('project_code', $projectCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->where('area', 'Rural')
                ->where('gp_nnn_code', $panchayatCode);

            $awcGpExists = $awcGpQuery->exists();

            if (!$awcGpExists) {
                $awcBlockExists = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->where('area', 'Rural')
                    ->whereNotNull('block')
                    ->whereRaw("TRIM(block) <> ''")
                    ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$block->block_name ?? ''])
                    ->exists();

                if (!$awcBlockExists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'इस प्रोजेक्ट में यह ब्लॉक उपलब्ध नहीं है।',
                    ], 422);
                }
            }

            $normalizedHin = $this->normalizeForCompare($request->panchayat_name_hin);
            $normalizedEn = $this->normalizeForCompare($request->panchayat_name_en);

            $nameDuplicateRows = DB::table('master_panchayats')
                ->where('block_code', $block->block_code)
                ->where(function ($query) use ($normalizedHin, $normalizedEn) {
                    $query->whereRaw('LOWER(TRIM(panchayat_name_hin)) = ?', [$normalizedHin])
                        ->orWhereRaw('LOWER(TRIM(panchayat_name)) = ?', [$normalizedEn]);
                })
                ->select('panchayat_lgd_code', 'district_lgd_code', 'district_name')
                ->get();

            $duplicateUpdateTarget = null;
            if ($nameDuplicateRows->isNotEmpty()) {
                $sameDistrict = $nameDuplicateRows->firstWhere('district_lgd_code', $districtLgdCode);
                if ($sameDistrict) {
                    $existingGpCode = $sameDistrict->panchayat_lgd_code ?? null;
                    if ($existingGpCode) {
                        $awcBlockMatch = DB::table('master_awcs')
                            ->where('project_code', $projectCode)
                            ->where('district_lgd_code', $districtLgdCode)
                            ->where('area', 'Rural')
                            ->where('gp_nnn_code', $existingGpCode)
                            ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$block->block_name ?? ''])
                            ->exists();

                        if (!$awcBlockMatch) {
                            // DB::enableQueryLog();
                            $awcBaseQuery = DB::table('master_awcs')
                                ->where('project_code', $projectCode)
                                ->where('district_lgd_code', $districtLgdCode)
                                ->where('area', 'Rural')
                                ->where('gp_nnn_code', $existingGpCode);
                            // dd(DB::getQueryLog(), $projectCode, $districtLgdCode, $existingGpCode);
                            if ($awcBaseQuery->exists()) {
                                $awcBaseQuery->update([
                                    'block' => trim((string) ($block->block_name ?? '')),
                                ]);
                            } else {
                                $sector = DB::table('master_sectors')
                                    ->where('project_code', $projectCode)
                                    ->where('district_lgd_code', $districtLgdCode)
                                    ->orderBy('sector_code')
                                    ->first();

                                if (!$sector) {
                                    return response()->json([
                                        'status' => 'error',
                                        'message' => 'प्रोजेक्ट की आवश्यक जानकारी नहीं मिली।',
                                    ], 422);
                                }

                                $maxAwcCode = DB::table('master_awcs')
                                    ->where('project_code', $projectCode)
                                    ->where('sector_code', $sector->sector_code)
                                    ->max('awc_code');

                                $newAwcCode = (int) ($maxAwcCode ?? 0) + 1;

                                $districtName = DB::table('master_district')
                                    ->where('District_Code_LGD', $block->district_lgd_code)
                                    ->value('name');

                                $villageCodeForAwc = DB::table('master_villages')
                                    ->where('panchayat_lgd_code', $existingGpCode)
                                    ->where('block_code', $block->block_code)
                                    ->orderBy('village_code')
                                    ->value('village_code');

                                DB::table('master_awcs')->insert([
                                    'district' => $districtName,
                                    'district_lgd_code' => (int) $block->district_lgd_code,
                                    'district_code' => (int) $block->district_code,
                                    'project_code' => (int) $projectCode,
                                    'project' => $sector->project,
                                    'sector_code' => (int) $sector->sector_code,
                                    'sector' => $sector->sector,
                                    'awc_name' => null,
                                    'awc_code' => $newAwcCode,
                                    'area' => 'Rural',
                                    'gp_nnn_code' => $existingGpCode,
                                    'gram_ward_code' => $villageCodeForAwc,
                                    'block' => trim((string) ($block->block_name ?? '')),
                                    'awc_belong' => 'Rural',
                                    'awc_type' => 'Regular',
                                ]);
                            }

                            return response()->json([
                                'status' => 'success',
                                'message' => 'AWC में ब्लॉक अपडेट कर दिया गया।',
                            ]);
                        }
                    }

                    return response()->json([
                        'status' => 'error',
                        'message' => 'इसी ब्लॉक में यह पंचायत नाम पहले से है।',
                    ], 422);
                }

                if (!$request->boolean('confirm_duplicate')) {
                    $otherDistrict = $nameDuplicateRows->first();
                    $otherDistrictName = trim((string) ($otherDistrict->district_name ?? ''));
                    $districtLabel = $otherDistrictName !== '' ? $otherDistrictName : 'अन्य जिला';
                    return response()->json([
                        'status' => 'confirm',
                        // 'message' => "यह पंचायत नाम {$districtLabel} में मौजूद है। क्या आप इसे अपने प्रोजेक्ट में जोड़ना चाहते हैं?",
                        'message' => "यह पंचायत नाम {$districtLabel} में वर्तमान में दर्ज है। यदि आप इसे जोड़ते हैं, तो यह {$districtLabel} से हटकर आपके चयनित जिले में स्थानांतरित हो जाएगा।",
                    ]);
                }

                $duplicateUpdateTarget = $nameDuplicateRows->first();
            }

            $alreadyExistsQuery = DB::table('master_panchayats')
                ->where(function ($query) use ($panchayatCode) {
                    $query->where('panchayat_code', $panchayatCode)
                        ->orWhere('panchayat_lgd_code', $panchayatCode);
                });

            if ($duplicateUpdateTarget) {
                $alreadyExistsQuery->where('panchayat_code', '!=', $duplicateUpdateTarget->panchayat_code);
            }

            if ($alreadyExistsQuery->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'यह पंचायत कोड पहले से मौजूद है।',
                ], 422);
            }

            if ($awcGpExists) {
                $awcBlockNames = (clone $awcGpQuery)
                    ->whereNotNull('block')
                    ->whereRaw("TRIM(block) <> ''")
                    ->distinct()
                    ->pluck('block')
                    ->map(fn($item) => $this->normalizeForCompare($item))
                    ->filter()
                    ->unique()
                    ->values();

                $selectedBlockNames = collect([
                    $block->block_name ?? '',
                    $block->block_name_hin ?? '',
                ])->map(fn($item) => $this->normalizeForCompare($item))
                    ->filter()
                    ->unique()
                    ->values();

                if ($awcBlockNames->isEmpty()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'इस पंचायत के लिए ब्लॉक जानकारी नहीं मिली।',
                    ], 422);
                }

                $hasBlockMatch = $awcBlockNames->intersect($selectedBlockNames)->isNotEmpty();

                if (!$hasBlockMatch) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'चयनित ब्लॉक उपलब्ध रिकॉर्ड से मेल नहीं खा रहा है।',
                    ], 422);
                }
            }

            $districtName = DB::table('master_district')
                ->where('District_Code_LGD', $block->district_lgd_code)
                ->value('name');

            DB::beginTransaction();

            try {
                if ($duplicateUpdateTarget) {
                    DB::table('master_panchayats')
                        ->where('panchayat_code', $duplicateUpdateTarget->panchayat_code)
                        ->update([
                            'panchayat_code' => $panchayatCode,
                            'panchayat_lgd_code' => $panchayatCode,
                            'panchayat_name' => trim((string) $request->panchayat_name_en),
                            'panchayat_name_hin' => trim((string) $request->panchayat_name_hin),
                            'block_code' => (int) $block->block_code,
                            'block_lgd_code' => (int) $block->block_lgd_code,
                            'block_name' => trim((string) ($block->block_name ?? '')),
                            'district_code' => (int) $block->district_code,
                            'district_lgd_code' => (int) $block->district_lgd_code,
                            'district_name' => trim((string) ($districtName ?? '')),
                        ]);
                } else {
                    DB::table('master_panchayats')->insert([
                        'panchayat_code' => $panchayatCode,
                        'panchayat_lgd_code' => $panchayatCode,
                        'panchayat_name' => trim((string) $request->panchayat_name_en),
                        'panchayat_name_hin' => trim((string) $request->panchayat_name_hin),
                        'block_code' => (int) $block->block_code,
                        'block_lgd_code' => (int) $block->block_lgd_code,
                        'block_name' => trim((string) ($block->block_name ?? '')),
                        'district_code' => (int) $block->district_code,
                        'district_lgd_code' => (int) $block->district_lgd_code,
                        'district_name' => trim((string) ($districtName ?? '')),
                    ]);
                }

                $sector = DB::table('master_sectors')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->orderBy('sector_code')
                    ->first();

                if (!$sector) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'प्रोजेक्ट की आवश्यक जानकारी नहीं मिली।',
                    ], 422);
                }

                $maxAwcCode = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('sector_code', $sector->sector_code)
                    ->max('awc_code');

                $newAwcCode = (int) ($maxAwcCode ?? 0) + 1;

                $awcQuery = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->where('area', 'Rural');

                $updatedAwc = 0;
                if ($duplicateUpdateTarget) {
                    $updatedAwc = (clone $awcQuery)
                        ->where('gp_nnn_code', $duplicateUpdateTarget->panchayat_code)
                        ->update([
                            'gp_nnn_code' => $panchayatCode,
                            'block' => trim((string) $block->block_name),
                        ]);
                }

                if ($updatedAwc === 0) {
                    $existingAwc = (clone $awcQuery)
                        ->where('gp_nnn_code', $panchayatCode)
                        ->exists();

                    if (!$existingAwc) {
                        DB::table('master_awcs')->insert([
                            'district' => $districtName,
                            'district_lgd_code' => (int) $block->district_lgd_code,
                            'district_code' => (int) $block->district_code,
                            'project_code' => (int) $projectCode,
                            'project' => $sector->project,
                            'sector_code' => (int) $sector->sector_code,
                            'sector' => $sector->sector,
                            'awc_name' => null,
                            'awc_code' => $newAwcCode,
                            'area' => 'Rural',
                            'gp_nnn_code' => $panchayatCode,
                            'gram_ward_code' => null,
                            'block' => trim((string) $block->block_name),
                            'awc_belong' => 'Rural',
                            'awc_type' => 'Regular',
                        ]);
                    } else {
                        (clone $awcQuery)
                            ->where('gp_nnn_code', $panchayatCode)
                            ->update([
                                'block' => trim((string) $block->block_name),
                            ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'ग्राम पंचायत सफलतापूर्वक दर्ज की गई।',
                    'awc_code' => $newAwcCode,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'सेव करते समय समस्या हुई। कृपया फिर से प्रयास करें।',
                ], 500);
            }
        }

        if (!$districtLgdCode || !$projectCode) {
            return view('admin/add_gram_panchayat', [
                'blocks' => collect(),
                'mappedPanchayats' => collect(),
                'missingPanchayats' => collect(),
                'projectCode' => $projectCode,
                'districtLgdCode' => $districtLgdCode,
            ]);
        }

        $awcBlockNames = DB::table('master_awcs')
            ->where('project_code', $projectCode)
            ->where('district_lgd_code', $districtLgdCode)
            ->where('area', 'Rural')
            ->whereNotNull('block')
            ->whereRaw("TRIM(block) <> ''")
            ->distinct()
            ->pluck('block')
            ->map(fn($item) => strtoupper(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $blocksQuery = DB::table('master_blocks')
            ->select('block_code', 'block_name', 'block_name_hin', 'district_lgd_code')
            ->where('district_lgd_code', $districtLgdCode);

        if (!empty($awcBlockNames)) {
            $placeholders = implode(',', array_fill(0, count($awcBlockNames), '?'));
            $blocksQuery->where(function ($q) use ($awcBlockNames, $placeholders) {
                $q->whereRaw("UPPER(TRIM(block_name)) IN ($placeholders)", $awcBlockNames)
                    ->orWhereRaw("UPPER(TRIM(block_name_hin)) IN ($placeholders)", $awcBlockNames);
            });
        } else {
            $blocksQuery->whereRaw('1 = 0');
        }

        $blocks = $blocksQuery
            ->orderBy('block_name_hin')
            ->get();

        $mappedPanchayats = collect(DB::select(
            "SELECT
                                                        t.gp_nnn_code,
                                                        t.block_name AS awc_block_name,
                                                        t.awc_count,
                                                        p.panchayat_name_hin,
                                                        p.panchayat_name,
                                                        p.block_code,
                                                        p.block_name AS mapped_block_name
                                                    FROM (
                                                        SELECT
                                                            ma.gp_nnn_code,
                                                            TRIM(ma.block) AS block_name,
                                                            COUNT(*) AS awc_count
                                                        FROM master_awcs ma
                                                        WHERE ma.project_code = ?
                                                        AND ma.district_lgd_code = ?
                                                        AND ma.area = 'Rural'
                                                        AND ma.gp_nnn_code IS NOT NULL
                                                        AND TRIM(ma.gp_nnn_code) <> ''
                                                        AND ma.gp_nnn_code <> 0
                                                        GROUP BY ma.gp_nnn_code, TRIM(ma.block)
                                                    ) t
                                                    INNER JOIN master_panchayats p
                                                        ON t.gp_nnn_code = p.panchayat_lgd_code
                                                    AND UPPER(TRIM(t.block_name)) = UPPER(TRIM(p.block_name))
                                                    ORDER BY p.panchayat_name_hin ASC",
            [$projectCode, $districtLgdCode]
        ));

        $missingPanchayats = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.block_name AS awc_block_name,
                UPPER(TRIM(t.block_name)) AS block_key,
                t.awc_count,
                p_wrong.block_name AS master_block_by_gp,
                p_wrong.panchayat_name_hin AS master_gp_name,
                CASE
                    WHEN p_wrong.panchayat_lgd_code IS NOT NULL THEN 1
                    ELSE 0
                END AS gp_exists_in_master
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    TRIM(ma.block) AS block_name,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Rural'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, TRIM(ma.block)
            ) t
            LEFT JOIN master_panchayats p
                ON t.gp_nnn_code = p.panchayat_lgd_code
               AND UPPER(TRIM(t.block_name)) = UPPER(TRIM(p.block_name))
            LEFT JOIN master_panchayats p_wrong
                ON t.gp_nnn_code = p_wrong.panchayat_lgd_code
            LEFT JOIN master_panchayats p_name
                ON UPPER(TRIM(p_name.block_name)) = UPPER(TRIM(t.block_name))
               AND UPPER(TRIM(p_name.panchayat_name_hin)) = UPPER(TRIM(p_wrong.panchayat_name_hin))
            WHERE p.panchayat_lgd_code IS NULL
              AND p_wrong.panchayat_lgd_code IS NOT NULL
              AND p_name.panchayat_lgd_code IS NOT NULL
            ORDER BY t.gp_nnn_code ASC, t.block_name ASC",
            [$projectCode, $districtLgdCode]
        ));

        $blockGpOptions = [];
        $gpByBlock = DB::table('master_panchayats')
            ->select('panchayat_lgd_code', 'panchayat_name_hin', 'block_name', 'district_lgd_code')
            ->where('district_lgd_code', $districtLgdCode)
            ->orderBy('panchayat_name_hin')
            ->get();

        foreach ($gpByBlock as $gpRow) {
            $key = strtoupper(trim((string) $gpRow->block_name));
            if ($key === '') {
                continue;
            }
            if (!isset($blockGpOptions[$key])) {
                $blockGpOptions[$key] = [];
            }
            $code = trim((string) $gpRow->panchayat_lgd_code);
            if ($code === '') {
                continue;
            }
            $blockGpOptions[$key][] = [
                'code' => $code,
                'name' => (string) $gpRow->panchayat_name_hin,
            ];
        }

        $missingPanchayats = $missingPanchayats->map(function ($item) use ($blockGpOptions) {
            $options = $blockGpOptions[$item->block_key ?? ''] ?? [];
            $name = trim((string) ($item->master_gp_name ?? ''));
            if ($name !== '' && !empty($options)) {
                $needle = $this->normalizeForCompare($name);
                $options = array_values(array_filter($options, function ($opt) use ($needle) {
                    $candidate = $this->normalizeForCompare($opt['name'] ?? '');
                    return $candidate === $needle;
                }));
            }
            $item->gp_options = $options;
            return $item;
        });

        return view('admin/add_gram_panchayat', [
            'blocks' => $blocks,
            'mappedPanchayats' => $mappedPanchayats,
            'missingPanchayats' => $missingPanchayats,
            'blockGpOptions' => $blockGpOptions,
            'projectCode' => $projectCode,
            'districtLgdCode' => $districtLgdCode,
        ]);
    }

    public function update_awc_gp_by_block(Request $request)
    {
        $districtLgdCode = (int) Session::get('district_id', 0);
        $projectCode = (int) Session::get('project_code', 0);

        if (!$districtLgdCode || !$projectCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'सेशन में जिला/प्रोजेक्ट जानकारी उपलब्ध नहीं है।',
            ], 422);
        }

        $request->validate([
            'old_gp_nnn_code' => 'required|digits_between:1,19',
            'new_gp_nnn_code' => 'required|digits_between:1,19',
            'awc_block_name' => 'required|string|max:100',
        ]);

        $oldGp = trim((string) $request->old_gp_nnn_code);
        $newGp = trim((string) $request->new_gp_nnn_code);
        $awcBlockName = trim((string) $request->awc_block_name);

        if ($oldGp === $newGp) {
            return response()->json([
                'status' => 'error',
                'message' => 'पुराना और नया GP code एक ही है।',
            ], 422);
        }

        $gpMaster = DB::table('master_panchayats')
            ->select('panchayat_lgd_code', 'block_name')
            ->where('panchayat_lgd_code', $newGp)
            ->first();

        if (!$gpMaster) {
            return response()->json([
                'status' => 'error',
                'message' => 'नया GP code master_panchayats में मौजूद नहीं है।',
            ], 422);
        }

        if ($this->normalizeForCompare($gpMaster->block_name) !== $this->normalizeForCompare($awcBlockName)) {
            return response()->json([
                'status' => 'error',
                'message' => 'नया GP code, AWC के block नाम से match नहीं करता।',
            ], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('master_awcs')
                ->where('project_code', $projectCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->where('area', 'Rural')
                ->where('gp_nnn_code', $oldGp)
                ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$awcBlockName])
                ->update([
                    'gp_nnn_code' => $newGp,
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'AWC GP mapping सफलतापूर्वक अपडेट की गई।',
                'old_gp_nnn_code' => $oldGp,
                'new_gp_nnn_code' => $newGp,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'डेटा अपडेट करते समय त्रुटि हुई।',
            ], 500);
        }
    }

    private function normalizeForCompare($value): string
    {
        $value = mb_strtolower(trim((string) $value), 'UTF-8');
        return preg_replace('/\s+/', ' ', $value);
    }

    private function upsertAwcForVillage(int $projectCode, int $districtLgdCode, object $block, object $panchayat, string $villageCode, string $villageNameHin): array
    {
        $updated = DB::table('master_awcs')
            ->where('project_code', $projectCode)
            ->where('district_lgd_code', $districtLgdCode)
            ->where('area', 'Rural')
            ->where('gp_nnn_code', $panchayat->panchayat_lgd_code)
            ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$block->block_name ?? ''])
            ->where(function ($query) {
                $query->whereNull('gram_ward_code')
                    ->orWhere('gram_ward_code', 0)
                    ->orWhereRaw("TRIM(gram_ward_code) = ''");
            })
            ->update([
                'gram_ward_code' => $villageCode,
                'awc_name' => trim((string) $villageNameHin),
            ]);

        if ($updated > 0) {
            return ['updated' => $updated, 'inserted' => false, 'awc_code' => null];
        }

        $sector = DB::table('master_sectors')
            ->where('project_code', $projectCode)
            ->orderBy('sector_code')
            ->first();

        if (!$sector) {
            throw new \RuntimeException('इस प्रोजेक्ट के लिए सेक्टर डेटा नहीं मिला।');
        }

        $maxAwcCode = DB::table('master_awcs')
            ->where('project_code', $projectCode)
            ->where('sector_code', $sector->sector_code)
            ->max('awc_code');

        $newAwcCode = (int) ($maxAwcCode ?? 0) + 1;

        $districtName = DB::table('master_district')
            ->where('District_Code_LGD', $block->district_lgd_code)
            ->value('name');

        DB::table('master_awcs')->insert([
            'district' => $districtName,
            'district_lgd_code' => (int) $block->district_lgd_code,
            'district_code' => (int) $block->district_code,
            'project_code' => (int) $projectCode,
            'project' => $sector->project,
            'sector_code' => (int) $sector->sector_code,
            'sector' => $sector->sector,
            'awc_name' => trim((string) $villageNameHin),
            'awc_code' => $newAwcCode,
            'area' => 'Rural',
            'gp_nnn_code' => (string) $panchayat->panchayat_lgd_code,
            'gram_ward_code' => $villageCode,
            'block' => trim((string) $block->block_name),
            'awc_belong' => 'Rural',
            'awc_type' => 'Regular',
        ]);

        return ['updated' => 0, 'inserted' => true, 'awc_code' => $newAwcCode];
    }

    public function add_nagar_nikay(Request $request)
    {
        $districtLgdCode = (int) Session::get('district_id', 0);
        $projectCode = (int) Session::get('project_code', 0);

        if ($request->isMethod('post')) {
            if (!$districtLgdCode || !$projectCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'सेशन की जानकारी नहीं मिली। कृपया फिर से लॉगिन करें।',
                ], 422);
            }

            $request->validate([
                'nagar_code' => 'required|digits_between:1,19',
                'nagar_name_hin' => 'required|string|max:50',
                'nagar_name_en' => 'required|string|max:50',
            ]);

            $nagarCode = trim((string) $request->input('nagar_code'));

            $alreadyExists = DB::table('master_nnn')
                ->where('std_nnn_code', $nagarCode)
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'यह नगर निकाय कोड पहले से मौजूद है।',
                ], 422);
            }

            $nameDuplicate = DB::table('master_nnn')
                ->where('district_lgd_code', $districtLgdCode)
                ->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(TRIM(nnn_name)) = ?', [$this->normalizeForCompare($request->nagar_name_hin)])
                        ->orWhereRaw('LOWER(TRIM(nnn_name_en)) = ?', [$this->normalizeForCompare($request->nagar_name_en)]);
                })
                ->exists();

            if ($nameDuplicate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इसी जिले में यह नगर निकाय नाम पहले से है।',
                ], 422);
            }

            $districtName = DB::table('master_district')
                ->where('District_Code_LGD', $districtLgdCode)
                ->value('name');

            $districtInfo = DB::table('master_district')
                ->where('District_Code_LGD', $districtLgdCode)
                ->first();

            DB::beginTransaction();
            try {
                DB::table('master_nnn')->insert([
                    'district_lgd_code' => $districtLgdCode,
                    'std_nnn_code' => $nagarCode,
                    'nnn_name' => trim((string) $request->nagar_name_hin),
                    'nnn_name_en' => trim((string) $request->nagar_name_en),
                ]);

                $sector = DB::table('master_sectors')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->orderBy('sector_code')
                    ->first();

                if (!$sector) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'प्रोजेक्ट की आवश्यक जानकारी नहीं मिली।',
                    ], 422);
                }

                $maxAwcCode = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('sector_code', $sector->sector_code)
                    ->max('awc_code');

                $newAwcCode = (int) ($maxAwcCode ?? 0) + 1;

                $updatedAwc = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->where('area', 'Urban')
                    ->where('gp_nnn_code', $nagarCode)
                    ->where(function ($query) {
                        $query->whereNull('gram_ward_code')
                            ->orWhere('gram_ward_code', 0)
                            ->orWhereRaw("TRIM(gram_ward_code) = ''");
                    })
                    ->update([
                        'awc_name' => null,
                    ]);

                if ($updatedAwc === 0) {
                    DB::table('master_awcs')->insert([
                        'district' => $districtName,
                        'district_lgd_code' => (int) $districtLgdCode,
                        'district_code' => (int) ($districtInfo->district_code ?? 0),
                        'project_code' => (int) $projectCode,
                        'project' => $sector->project,
                        'sector_code' => (int) $sector->sector_code,
                        'sector' => $sector->sector,
                        'awc_name' => null,
                        'awc_code' => $newAwcCode,
                        'area' => 'Urban',
                        'gp_nnn_code' => $nagarCode,
                        'gram_ward_code' => null,
                        'block' => null,
                        'awc_belong' => 'Urban',
                        'awc_type' => 'Regular',
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'नगर निकाय सफलतापूर्वक दर्ज किया गया।',
                    'awc_code' => $newAwcCode,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'सेव करते समय समस्या हुई। कृपया फिर से प्रयास करें।',
                ], 500);
            }
        }

        if (!$districtLgdCode || !$projectCode) {
            return view('admin/add_nagar_nikay', [
                'mappedNagars' => collect(),
                'missingNagars' => collect(),
                'projectCode' => $projectCode,
                'districtLgdCode' => $districtLgdCode,
            ]);
        }

        $mappedNagars = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.awc_count,
                n.nnn_name,
                n.nnn_name_en
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Urban'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code
            ) t
            INNER JOIN master_nnn n
                ON t.gp_nnn_code = n.std_nnn_code
               AND n.district_lgd_code = ?
            ORDER BY n.nnn_name ASC",
            [$projectCode, $districtLgdCode, $districtLgdCode]
        ));

        $missingNagars = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.awc_count
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Urban'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code
            ) t
            LEFT JOIN master_nnn n
                ON t.gp_nnn_code = n.std_nnn_code
               AND n.district_lgd_code = ?
            WHERE n.std_nnn_code IS NULL
            ORDER BY t.gp_nnn_code ASC",
            [$projectCode, $districtLgdCode, $districtLgdCode]
        ));

        return view('admin/add_nagar_nikay', [
            'mappedNagars' => $mappedNagars,
            'missingNagars' => $missingNagars,
            'projectCode' => $projectCode,
            'districtLgdCode' => $districtLgdCode,
        ]);
    }

    public function add_ward(Request $request)
    {
        $districtLgdCode = (int) Session::get('district_id', 0);
        $projectCode = (int) Session::get('project_code', 0);

        if ($request->isMethod('post')) {
            if (!$districtLgdCode || !$projectCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'सेशन की जानकारी नहीं मिली। कृपया फिर से लॉगिन करें।',
                ], 422);
            }

            $request->validate([
                'nagar_code' => 'required|digits_between:1,19',
                'ward_no' => 'required|integer|min:1|max:999999',
                'ward_name' => 'required|string|max:50',
            ]);

            $nagarCode = trim((string) $request->input('nagar_code'));
            $wardNo = (int) $request->input('ward_no');

            $nagar = DB::table('master_nnn')
                ->where('std_nnn_code', $nagarCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->first();

            if (!$nagar) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित नगर निकाय उपलब्ध नहीं है।',
                ], 422);
            }

            $duplicateWardNo = DB::table('master_ward')
                ->where('district_lgd_code', $districtLgdCode)
                ->where('std_nnn_code', $nagarCode)
                ->where('ward_no', $wardNo)
                ->exists();

            if ($duplicateWardNo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'यह वार्ड नंबर पहले से मौजूद है।',
                ], 422);
            }

            $duplicateWardName = DB::table('master_ward')
                ->where('district_lgd_code', $districtLgdCode)
                ->where('std_nnn_code', $nagarCode)
                ->whereRaw('LOWER(TRIM(ward_name)) = ?', [$this->normalizeForCompare($request->ward_name)])
                ->exists();

            if ($duplicateWardName) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इसी नगर निकाय में यह वार्ड नाम पहले से है।',
                ], 422);
            }

            $districtName = DB::table('master_district')
                ->where('District_Code_LGD', $districtLgdCode)
                ->value('name');

            $districtInfo = DB::table('master_district')
                ->where('District_Code_LGD', $districtLgdCode)
                ->first();

            DB::beginTransaction();
            try {
                DB::table('master_ward')->insert([
                    'district_lgd_code' => $districtLgdCode,
                    'std_nnn_code' => $nagarCode,
                    'nnn_name' => $nagar->nnn_name,
                    'nnn_name_en' => $nagar->nnn_name_en,
                    'ward_no' => $wardNo,
                    'ward_name' => trim((string) $request->ward_name),
                ]);

                $sector = DB::table('master_sectors')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->orderBy('sector_code')
                    ->first();

                if (!$sector) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'प्रोजेक्ट की आवश्यक जानकारी नहीं मिली।',
                    ], 422);
                }

                $maxAwcCode = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('sector_code', $sector->sector_code)
                    ->max('awc_code');

                $newAwcCode = (int) ($maxAwcCode ?? 0) + 1;

                $existingWardAwc = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->where('area', 'Urban')
                    ->where('gp_nnn_code', $nagarCode)
                    ->where('gram_ward_code', $wardNo)
                    ->exists();

                if ($existingWardAwc) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'यह वार्ड पहले से मौजूद है।',
                    ], 422);
                }

                $updatedWardAwc = DB::table('master_awcs')
                    ->where('project_code', $projectCode)
                    ->where('district_lgd_code', $districtLgdCode)
                    ->where('area', 'Urban')
                    ->where('gp_nnn_code', $nagarCode)
                    ->where(function ($query) {
                        $query->whereNull('gram_ward_code')
                            ->orWhere('gram_ward_code', 0)
                            ->orWhereRaw("TRIM(gram_ward_code) = ''");
                    })
                    ->update([
                        'gram_ward_code' => $wardNo,
                        'awc_name' => trim((string) $request->ward_name),
                    ]);

                if ($updatedWardAwc === 0) {
                    DB::table('master_awcs')->insert([
                        'district' => $districtName,
                        'district_lgd_code' => (int) $districtLgdCode,
                        'district_code' => (int) ($districtInfo->district_code ?? 0),
                        'project_code' => (int) $projectCode,
                        'project' => $sector->project,
                        'sector_code' => (int) $sector->sector_code,
                        'sector' => $sector->sector,
                        'awc_name' => trim((string) $request->ward_name),
                        'awc_code' => $newAwcCode,
                        'area' => 'Urban',
                        'gp_nnn_code' => $nagarCode,
                        'gram_ward_code' => $wardNo,
                        'block' => null,
                        'awc_belong' => 'Urban',
                        'awc_type' => 'Regular',
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'वार्ड सफलतापूर्वक दर्ज किया गया।',
                    'awc_code' => $newAwcCode,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'सेव करते समय समस्या हुई। कृपया फिर से प्रयास करें।',
                ], 500);
            }
        }

        if (!$districtLgdCode || !$projectCode) {
            return view('admin/add_ward', [
                'nagars' => collect(),
                'matchedWards' => collect(),
                'missingWards' => collect(),
                'projectCode' => $projectCode,
                'districtLgdCode' => $districtLgdCode,
            ]);
        }

        $nagars = DB::table('master_awcs as ma')
            ->join('master_nnn as nnn', function ($join) {
                $join->on('ma.gp_nnn_code', '=', 'nnn.std_nnn_code');
            })
            ->where('ma.project_code', $projectCode)
            ->where('ma.district_lgd_code', $districtLgdCode)
            ->where('ma.area', 'Urban')
            ->whereNotNull('ma.gp_nnn_code')
            ->whereRaw("TRIM(ma.gp_nnn_code) <> ''")
            ->where('ma.gp_nnn_code', '!=', 0)
            ->select('nnn.std_nnn_code', 'nnn.nnn_name', 'nnn.nnn_name_en')
            ->distinct()
            ->orderBy('nnn.nnn_name')
            ->get();

        $matchedWards = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.gram_ward_code,
                t.awc_count,
                w.ward_name,
                w.ward_no,
                w.nnn_name
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    ma.gram_ward_code,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Urban'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND ma.gram_ward_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, ma.gram_ward_code
            ) t
            INNER JOIN master_ward w
                ON t.gp_nnn_code = w.std_nnn_code
               AND t.gram_ward_code = w.ward_no
               AND w.district_lgd_code = ?
            ORDER BY w.ward_name ASC",
            [$projectCode, $districtLgdCode, $districtLgdCode]
        ));

        $missingWards = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.gram_ward_code,
                t.awc_count
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    ma.gram_ward_code,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Urban'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND ma.gram_ward_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, ma.gram_ward_code
            ) t
            LEFT JOIN master_ward w
                ON t.gp_nnn_code = w.std_nnn_code
               AND t.gram_ward_code = w.ward_no
               AND w.district_lgd_code = ?
            WHERE w.ID IS NULL
            ORDER BY t.gram_ward_code ASC",
            [$projectCode, $districtLgdCode, $districtLgdCode]
        ));

        return view('admin/add_ward', [
            'nagars' => $nagars,
            'matchedWards' => $matchedWards,
            'missingWards' => $missingWards,
            'projectCode' => $projectCode,
            'districtLgdCode' => $districtLgdCode,
        ]);
    }

    public function add_village(Request $request)
    {
        $districtLgdCode = (int) Session::get('district_id', 0);
        $projectCode = (int) Session::get('project_code', 0);

        if ($request->isMethod('post')) {
            if (!$districtLgdCode || !$projectCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'सेशन की जानकारी नहीं मिली। कृपया फिर से लॉगिन करें।',
                ], 422);
            }

            $request->validate([
                'block_code' => 'required|integer|exists:master_blocks,block_code',
                'panchayat_code' => 'required|digits_between:1,19',
                'village_code' => 'required|digits_between:1,19',
                'village_name_hin' => 'required|string|max:100',
                'village_name_en' => 'required|string|max:100',
            ]);

            $block = DB::table('master_blocks')
                ->where('block_code', $request->block_code)
                ->first();

            if (!$block) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित ब्लॉक उपलब्ध नहीं है।',
                ], 422);
            }

            if ((int) ($block->district_lgd_code ?? 0) !== $districtLgdCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित ब्लॉक आपके जिले से मेल नहीं खाता।',
                ], 422);
            }

            $panchayatCode = trim((string) $request->input('panchayat_code'));
            $villageCode = trim((string) $request->input('village_code'));

            $panchayat = DB::table('master_panchayats')
                ->where('panchayat_lgd_code', $panchayatCode)
                ->first();

            if (!$panchayat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित पंचायत उपलब्ध नहीं है।',
                ], 422);
            }

            if ((int) ($panchayat->district_lgd_code ?? 0) !== $districtLgdCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित पंचायत आपके जिले से मेल नहीं खाती।',
                ], 422);
            }

            if ((int) ($panchayat->block_code ?? 0) !== (int) $block->block_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित पंचायत इस ब्लॉक से मेल नहीं खाती।',
                ], 422);
            }

            $awcGpExists = DB::table('master_awcs')
                ->where('project_code', $projectCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->where('area', 'Rural')
                ->where('gp_nnn_code', $panchayatCode)
                ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$block->block_name ?? ''])
                ->exists();

            if (!$awcGpExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इस प्रोजेक्ट में यह पंचायत उपलब्ध नहीं है।',
                ], 422);
            }

            $awcVillageExists = DB::table('master_awcs')
                ->where('project_code', $projectCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->where('area', 'Rural')
                ->where('gp_nnn_code', $panchayatCode)
                ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$block->block_name ?? ''])
                ->exists();

            if (!$awcVillageExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इस प्रोजेक्ट में यह पंचायत/ब्लॉक उपलब्ध नहीं है।',
                ], 422);
            }

            $existingVillage = DB::table('master_villages')
                ->where(function ($query) use ($villageCode) {
                    $query->where('village_code', $villageCode)
                        ->orWhere('village_lgd_code', $villageCode);
                })
                ->where('panchayat_lgd_code', $panchayatCode)
                ->where('block_code', $block->block_code)
                ->where('district_lgd_code', $block->district_lgd_code)
                ->first();

            if ($existingVillage && !empty($existingVillage->village_name_hin)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'यह गाँव कोड पहले से मौजूद है।',
                ], 422);
            }

            $nameDuplicate = DB::table('master_villages')
                ->where('panchayat_lgd_code', $panchayatCode)
                ->where('block_code', $block->block_code)
                ->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(TRIM(village_name_hin)) = ?', [$this->normalizeForCompare($request->village_name_hin)])
                        ->orWhereRaw('LOWER(TRIM(village_name)) = ?', [$this->normalizeForCompare($request->village_name_en)]);
                })
                ->exists();

            if ($nameDuplicate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इसी पंचायत में यह गाँव नाम पहले से है।',
                ], 422);
            }
            DB::enableQueryLog();
            $awcBlockNames = DB::table('master_awcs')
                ->where('project_code', $projectCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->where('area', 'Rural')
                ->where('gp_nnn_code', $panchayatCode)
                // ->where('gram_ward_code', $villageCode)
                ->whereNotNull('block')
                ->whereRaw("TRIM(block) <> ''")
                ->distinct()
                ->pluck('block')
                ->map(fn($item) => $this->normalizeForCompare($item))
                ->filter()
                ->unique()
                ->values();

            // dd(DB::getQueryLog());

            $selectedBlockNames = collect([
                $block->block_name ?? '',
            ])->map(fn($item) => $this->normalizeForCompare($item))
                ->filter()
                ->unique()
                ->values();

            if ($awcBlockNames->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'इस गाँव के लिए ब्लॉक जानकारी नहीं मिली।',
                ], 422);
            }

            if ($awcBlockNames->intersect($selectedBlockNames)->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'चयनित पंचायत का ब्लॉक उपलब्ध रिकॉर्ड से मेल नहीं खा रहा है।',
                ], 422);
            }

            DB::beginTransaction();
            try {
                if ($existingVillage && empty($existingVillage->village_name_hin)) {
                    DB::table('master_villages')
                        ->where('village_code', $existingVillage->village_code)
                        ->where('panchayat_lgd_code', $existingVillage->panchayat_lgd_code)
                        ->where('block_code', $existingVillage->block_code)
                        ->where('district_lgd_code', $existingVillage->district_lgd_code)
                        ->update([
                            'village_lgd_code' => $villageCode,
                            'village_name' => trim((string) $request->village_name_en),
                            'village_name_hin' => trim((string) $request->village_name_hin),
                            'panchayat_name' => trim((string) $panchayat->panchayat_name),
                            'block_name' => trim((string) $block->block_name),
                            'district_name' => trim((string) ($districtName ?? '')),
                        ]);
                } else {
                    DB::table('master_villages')->insert([
                        'village_code' => $villageCode,
                        'village_lgd_code' => $villageCode,
                        'village_name' => trim((string) $request->village_name_en),
                        'village_name_hin' => trim((string) $request->village_name_hin),
                        'panchayat_code' => (string) $panchayat->panchayat_code,
                        'panchayat_lgd_code' => (string) $panchayat->panchayat_lgd_code,
                        'panchayat_name' => trim((string) $panchayat->panchayat_name),
                        'block_code' => (int) $block->block_code,
                        'block_lgd_code' => (int) $block->block_lgd_code,
                        'block_name' => trim((string) $block->block_name),
                        'district_code' => (int) $panchayat->district_code,
                        'district_lgd_code' => (int) $block->district_lgd_code,
                        'district_name' => trim((string) ($districtName ?? '')),
                    ]);
                }

                $awcResult = $this->upsertAwcForVillage(
                    $projectCode,
                    $districtLgdCode,
                    $block,
                    $panchayat,
                    $villageCode,
                    (string) $request->village_name_hin
                );

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'गाँव सफलतापूर्वक दर्ज किया गया।',
                    'awc_updated' => $awcResult['updated'],
                    'awc_inserted' => $awcResult['inserted'],
                    'awc_code' => $awcResult['awc_code'],
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'सेव करते समय समस्या हुई। कृपया फिर से प्रयास करें।',
                ], 500);
            }
        }

        if (!$districtLgdCode || !$projectCode) {
            return view('admin/add_village', [
                'panchayats' => collect(),
                'matchedVillages' => collect(),
                'missingVillages' => collect(),
                'projectCode' => $projectCode,
                'districtLgdCode' => $districtLgdCode,
            ]);
        }

        $awcBlockNames = DB::table('master_awcs')
            ->where('project_code', $projectCode)
            ->where('district_lgd_code', $districtLgdCode)
            ->where('area', 'Rural')
            ->whereNotNull('block')
            ->whereRaw("TRIM(block) <> ''")
            ->distinct()
            ->pluck('block')
            ->map(fn($item) => strtoupper(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $blocksQuery = DB::table('master_blocks')
            ->select('block_code', 'block_name', 'block_name_hin', 'district_lgd_code')
            ->where('district_lgd_code', $districtLgdCode);

        if (!empty($awcBlockNames)) {
            $placeholders = implode(',', array_fill(0, count($awcBlockNames), '?'));
            $blocksQuery->where(function ($q) use ($awcBlockNames, $placeholders) {
                $q->whereRaw("UPPER(TRIM(block_name)) IN ($placeholders)", $awcBlockNames)
                    ->orWhereRaw("UPPER(TRIM(block_name_hin)) IN ($placeholders)", $awcBlockNames);
            });
        } else {
            $blocksQuery->whereRaw('1 = 0');
        }

        $blocks = $blocksQuery
            ->orderBy('block_name_hin')
            ->get();

        $panchayats = DB::table('master_awcs as ma')
            ->join('master_panchayats as mp', function ($join) {
                $join->on('ma.gp_nnn_code', '=', 'mp.panchayat_lgd_code')
                    ->whereRaw('UPPER(TRIM(ma.block)) = UPPER(TRIM(mp.block_name))');
            })
            ->where('ma.project_code', $projectCode)
            ->where('ma.district_lgd_code', $districtLgdCode)
            ->where('ma.area', 'Rural')
            ->whereNotNull('ma.gp_nnn_code')
            ->whereRaw("TRIM(ma.gp_nnn_code) <> ''")
            ->where('ma.gp_nnn_code', '!=', 0)
            ->select('mp.panchayat_lgd_code', 'mp.panchayat_name_hin', 'mp.block_name')
            ->distinct()
            ->orderBy('mp.panchayat_name_hin')
            ->get();

        $matchedVillages = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.gram_ward_code,
                t.block_name AS awc_block_name,
                t.awc_count,
                mv.village_name_hin,
                mv.village_name,
                mv.panchayat_name,
                mv.block_name AS mapped_block_name
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    ma.gram_ward_code,
                    TRIM(ma.block) AS block_name,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                INNER JOIN master_panchayats mp
                    ON ma.gp_nnn_code = mp.panchayat_lgd_code
                   AND UPPER(TRIM(ma.block)) = UPPER(TRIM(mp.block_name))
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Rural'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND ma.gram_ward_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, ma.gram_ward_code, TRIM(ma.block)
            ) t
            INNER JOIN master_villages mv
                ON (t.gram_ward_code = mv.village_code OR t.gram_ward_code = mv.village_lgd_code)
               AND t.gp_nnn_code = mv.panchayat_lgd_code
               AND UPPER(TRIM(t.block_name)) = UPPER(TRIM(mv.block_name))
            ORDER BY mv.village_name_hin ASC",
            [$projectCode, $districtLgdCode]
        ));

        $blockMismatchVillages = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.gram_ward_code,
                t.block_name AS awc_block_name,
                t.awc_count,
                mv.village_name_hin,
                mv.block_name AS master_block_name
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    ma.gram_ward_code,
                    TRIM(ma.block) AS block_name,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                INNER JOIN master_panchayats mp
                    ON ma.gp_nnn_code = mp.panchayat_lgd_code
                   AND UPPER(TRIM(ma.block)) = UPPER(TRIM(mp.block_name))
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Rural'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND ma.gram_ward_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, ma.gram_ward_code, TRIM(ma.block)
            ) t
            INNER JOIN master_villages mv
                ON (t.gram_ward_code = mv.village_code OR t.gram_ward_code = mv.village_lgd_code)
               AND t.gp_nnn_code = mv.panchayat_lgd_code
            WHERE UPPER(TRIM(t.block_name)) <> UPPER(TRIM(mv.block_name))
            ORDER BY mv.village_name_hin ASC",
            [$projectCode, $districtLgdCode]
        ));

        $gpMismatchVillages = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.gram_ward_code,
                t.block_name AS awc_block_name,
                t.awc_count,
                mv.village_name_hin,
                mv.panchayat_lgd_code AS master_panchayat_lgd_code,
                mp.panchayat_name_hin
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    ma.gram_ward_code,
                    TRIM(ma.block) AS block_name,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                INNER JOIN master_panchayats mp
                    ON ma.gp_nnn_code = mp.panchayat_lgd_code
                   AND UPPER(TRIM(ma.block)) = UPPER(TRIM(mp.block_name))
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Rural'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND ma.gram_ward_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, ma.gram_ward_code, TRIM(ma.block)
            ) t
            INNER JOIN master_villages mv
                ON (t.gram_ward_code = mv.village_code OR t.gram_ward_code = mv.village_lgd_code)
               AND UPPER(TRIM(t.block_name)) = UPPER(TRIM(mv.block_name))
            INNER JOIN master_panchayats mp
                ON mv.panchayat_lgd_code = mp.panchayat_lgd_code
            WHERE t.gp_nnn_code <> mv.panchayat_lgd_code
            ORDER BY mv.village_name_hin ASC",
            [$projectCode, $districtLgdCode]
        ));

        $missingVillages = collect(DB::select(
            "SELECT
                t.gp_nnn_code,
                t.gram_ward_code,
                t.block_name AS awc_block_name,
                t.awc_count,
                mp.panchayat_name_hin
            FROM (
                SELECT
                    ma.gp_nnn_code,
                    ma.gram_ward_code,
                    TRIM(ma.block) AS block_name,
                    COUNT(*) AS awc_count
                FROM master_awcs ma
                INNER JOIN master_panchayats mp
                    ON ma.gp_nnn_code = mp.panchayat_lgd_code
                   AND UPPER(TRIM(ma.block)) = UPPER(TRIM(mp.block_name))
                WHERE ma.project_code = ?
                  AND ma.district_lgd_code = ?
                  AND ma.area = 'Rural'
                  AND ma.gp_nnn_code IS NOT NULL
                  AND ma.gram_ward_code IS NOT NULL
                  AND TRIM(ma.gp_nnn_code) <> ''
                  AND ma.gp_nnn_code <> 0
                GROUP BY ma.gp_nnn_code, ma.gram_ward_code, TRIM(ma.block)
            ) t
            INNER JOIN master_panchayats mp
                ON t.gp_nnn_code = mp.panchayat_lgd_code
            LEFT JOIN master_villages mv
                ON (t.gram_ward_code = mv.village_code)
               AND t.gp_nnn_code = mv.panchayat_lgd_code
               AND UPPER(TRIM(t.block_name)) = UPPER(TRIM(mv.block_name))
            WHERE mv.village_code IS NULL
            ORDER BY t.gram_ward_code ASC, t.block_name ASC",
            [$projectCode, $districtLgdCode]
        ));

        $gpMismatchKeys = $gpMismatchVillages->map(function ($row) {
            return $row->gp_nnn_code . '|' . $row->gram_ward_code . '|' . strtoupper(trim((string) $row->awc_block_name));
        })->flip();

        $missingVillages = $missingVillages->filter(function ($row) use ($gpMismatchKeys) {
            $key = $row->gp_nnn_code . '|' . $row->gram_ward_code . '|' . strtoupper(trim((string) $row->awc_block_name));
            return !$gpMismatchKeys->has($key);
        })->values();

        $missingVillages = $missingVillages->map(function ($row) {
            $row->row_type = 'missing';
            return $row;
        })->merge(
                $blockMismatchVillages->map(function ($row) {
                    $row->row_type = 'block_mismatch';
                    return $row;
                })
            )->merge(
                $gpMismatchVillages->map(function ($row) {
                    $row->row_type = 'gp_mismatch';
                    return $row;
                })
            );

        return view('admin/add_village', [
            'blocks' => $blocks,
            'panchayats' => $panchayats,
            'matchedVillages' => $matchedVillages,
            'missingVillages' => $missingVillages,
            'projectCode' => $projectCode,
            'districtLgdCode' => $districtLgdCode,
        ]);
    }

    public function update_awc_village_gp_by_block(Request $request)
    {
        $districtLgdCode = (int) Session::get('district_id', 0);
        $projectCode = (int) Session::get('project_code', 0);

        if (!$districtLgdCode || !$projectCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'सेशन में जिला/प्रोजेक्ट जानकारी उपलब्ध नहीं है।',
            ], 422);
        }

        $request->validate([
            'old_gp_nnn_code' => 'required|digits_between:1,19',
            'village_code' => 'required|digits_between:1,19',
            'awc_block_name' => 'required|string|max:100',
        ]);

        $oldGp = trim((string) $request->old_gp_nnn_code);
        $villageCode = trim((string) $request->village_code);
        $awcBlockName = trim((string) $request->awc_block_name);

        $targetVillage = DB::table('master_villages')
            ->where(function ($query) use ($villageCode) {
                $query->where('village_code', $villageCode)
                    ->orWhere('village_lgd_code', $villageCode);
            })
            ->whereRaw('UPPER(TRIM(block_name)) = UPPER(TRIM(?))', [$awcBlockName])
            ->get();

        if ($targetVillage->count() !== 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Target village unique नहीं मिला। कृपया block/village code जांचें।',
            ], 422);
        }

        $newGp = (string) $targetVillage->first()->panchayat_lgd_code;

        DB::beginTransaction();
        try {
            DB::table('master_awcs')
                ->where('project_code', $projectCode)
                ->where('district_lgd_code', $districtLgdCode)
                ->where('area', 'Rural')
                ->where('gp_nnn_code', $oldGp)
                ->where('gram_ward_code', $villageCode)
                ->whereRaw('UPPER(TRIM(block)) = UPPER(TRIM(?))', [$awcBlockName])
                ->update([
                    'gp_nnn_code' => $newGp,
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'AWC GP mapping अपडेट हो गई।',
                'old_gp_nnn_code' => $oldGp,
                'new_gp_nnn_code' => $newGp,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'डेटा अपडेट करते समय त्रुटि हुई।',
            ], 500);
        }
    }
}
