<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StaticWeightageController extends Controller
{
    /**
     * Save consolidated weightage data to master_weightage_config table
     */
    private function saveMasterWeightageConfig(Request $request, int $post_config_id, string $ip_address, int $user_id): void
    {
        // Get post details first
        $post = DB::table('master_post_config')->where('id', $post_config_id)->first();
        if (!$post) {
            throw new \Exception('Post configuration not found');
        }

        // Clear existing record for this post_config_id
        DB::table('master_weightage_config')->where('fk_post_config_id', $post_config_id)->delete();

        // Prepare data arrays
        $questionIds = [];
        $optionValues = [];
        $questionMarks = [];
        $qualificationIds = [];
        $qualificationMarks = [];
        $casteIds = [];
        $casteMarks = [];
        $multiplierQualificationIds = [];
        $multiplierValues = [];

        // Process question weightage data
        if ($request->has('weightage') && is_array($request->weightage)) {
            foreach ($request->weightage as $key => $value) {
                if (!empty($value) && $value > 0) {
                    $parts = explode('-', $key);
                    $question_id = $parts[0];
                    $option_value = isset($parts[1]) ? $parts[1] : null;

                    $questionIds[] = $question_id;
                    $optionValues[] = $option_value;
                    $questionMarks[] = (int) $value;
                }
            }
        }

        // Process qualification marks
        if ($request->has('qualification_marks') && is_array($request->qualification_marks)) {
            foreach ($request->qualification_marks as $qualification_id => $marks) {
                if (!empty($marks) && $marks > 0) {
                    $qualificationIds[] = $qualification_id;
                    $qualificationMarks[] = (int) $marks;
                }
            }
        }

        // Process caste marks
        if ($request->has('caste_marks') && is_array($request->caste_marks)) {
            foreach ($request->caste_marks as $caste_id => $marks) {
                if (!empty($marks) && $marks > 0) {
                    $casteIds[] = $caste_id;
                    $casteMarks[] = (int) $marks;
                }
            }
        }

        // Process qualification multipliers
        if ($request->has('qualification_multiplier') && is_array($request->qualification_multiplier)) {
            foreach ($request->qualification_multiplier as $qualification_id => $multiplier) {
                if (!empty($multiplier) && $multiplier > 0) {
                    $multiplierQualificationIds[] = $qualification_id;
                    $multiplierValues[] = (float) $multiplier;
                }
            }
        }

        // Check if this post_config has a corresponding post in master_post table
        // For static posts, we use the post_config as the post reference
        // Since master_post table exists but might not have all static posts,
        // we'll use the post_config_id directly as it represents the post
        $insertData = [
            'fk_post_id' => $post_config_id, // For static posts, post_config_id serves as the post identifier
            'fk_post_config_id' => $post_config_id,
            'question_id' => !empty($questionIds) ? json_encode($questionIds, JSON_UNESCAPED_UNICODE) : null,
            'option_value' => !empty($optionValues) ? json_encode($optionValues, JSON_UNESCAPED_UNICODE) : null,
            'question_marks' => !empty($questionMarks) ? json_encode($questionMarks, JSON_UNESCAPED_UNICODE) : null,
            'qualification_id' => !empty($qualificationIds) ? json_encode($qualificationIds, JSON_UNESCAPED_UNICODE) : null,
            'qualification_marks' => !empty($qualificationMarks) ? json_encode($qualificationMarks, JSON_UNESCAPED_UNICODE) : null,
            'caste_id' => !empty($casteIds) ? json_encode($casteIds, JSON_UNESCAPED_UNICODE) : null,
            'caste_marks' => !empty($casteMarks) ? json_encode($casteMarks, JSON_UNESCAPED_UNICODE) : null,
            'multiplyer_qualification_id' => !empty($multiplierQualificationIds) ? json_encode($multiplierQualificationIds, JSON_UNESCAPED_UNICODE) : null,
            'multiplier_value' => !empty($multiplierValues) ? json_encode($multiplierValues, JSON_UNESCAPED_UNICODE) : null,
            'minimum_experience_years' => $request->minimum_experience_years ?? null,
            'increment_value_per_year' => $request->increment_value_per_year ?? null,
            'maximum_experience_marks' => $request->maximum_experience_marks ?? null,
            'is_active' => '1',
            'ip_address' => $ip_address,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert into master_weightage_config
        DB::table('master_weightage_config')->insert($insertData);
    }

    /**
     * Transfer static weightage data from master_weightage_config to dynamic weightage tables
     * This function is called after a post is successfully uploaded to transfer weightage configuration
     * 
     * @param int $post_config_id - The static post config ID (from master_post_config)
     * @param int $uploaded_post_id - The newly created post ID (from master_post)
     * @param string $ip_address - IP address for audit trail
     * @param int $user_id - User ID for audit trail
     * @throws \Exception if weightage config not found or transfer fails
     */
    public function transferStaticWeightageToUploadedPost(int $post_config_id, int $uploaded_post_id, string $ip_address, int $user_id): void
    {
        // Get weightage configuration from master_weightage_config
        $masterWeightageConfig = $this->getMasterWeightageConfig($post_config_id);

        // if (!$masterWeightageConfig) {
        //     throw new \Exception('इस पोस्ट के लिए कोई वेटेज कॉन्फ़िगरेशन नहीं मिली।');
        // }

        if ($masterWeightageConfig) {

            // Get questions data for processing weightage marks
            $questions = DB::table('master_post_questions')
                ->where('is_active', '1')
                ->where('is_weightage_marks', '1')
                ->get()
                ->keyBy('ques_ID');

            // Transfer Question Weightage Marks
            if ($masterWeightageConfig->question_id && $masterWeightageConfig->question_marks) {
                $questionIds = json_decode($masterWeightageConfig->question_id, true);
                $optionValues = json_decode($masterWeightageConfig->option_value, true);
                $questionMarks = json_decode($masterWeightageConfig->question_marks, true);

                // Ensure all variables are arrays
                $questionIds = is_array($questionIds) ? $questionIds : ($questionIds !== null ? [$questionIds] : []);
                $optionValues = is_array($optionValues) ? $optionValues : ($optionValues !== null ? [$optionValues] : []);
                $questionMarks = is_array($questionMarks) ? $questionMarks : ($questionMarks !== null ? [$questionMarks] : []);

                // Group weightage for single-selection questions
                $groupedWeightage = [];
                foreach ($questionIds as $index => $question_id) {
                    if (!isset($questions[$question_id]))
                        continue;

                    $option_value = $optionValues[$index] ?? null;
                    $marks = $questionMarks[$index] ?? 0;

                    if ($marks <= 0)
                        continue;

                    $isOTypeMultipleOptions = ($questions[$question_id]->ans_type === 'O') &&
                        (count(json_decode($questions[$question_id]->answer_options)) > 2);

                    // For multiple choice or O-type with multiple options, insert directly
                    if ($questions[$question_id]->ans_type === 'M' || $isOTypeMultipleOptions) {
                        DB::table('master_weightage_marks')->insert([
                            'post_id' => $uploaded_post_id,
                            'question_id' => $question_id,
                            'option_value' => $option_value,
                            'marks' => (int) $marks,
                            'is_active' => '1',
                            'ip_address' => $ip_address,
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // For single-selection questions, group by question_id
                        if (!isset($groupedWeightage[$question_id]) || $marks > 0) {
                            $groupedWeightage[$question_id] = [
                                'option_value' => $option_value,
                                'marks' => (int) $marks,
                            ];
                        }
                    }
                }

                // Insert grouped weightage for single-selection questions
                foreach ($groupedWeightage as $question_id => $data) {
                    if ($data['marks'] <= 0)
                        continue;

                    DB::table('master_weightage_marks')->insert([
                        'post_id' => $uploaded_post_id,
                        'question_id' => $question_id,
                        'option_value' => $data['option_value'],
                        'marks' => $data['marks'],
                        'is_active' => '1',
                        'ip_address' => $ip_address,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }


            // Transfer Qualification Marks
            if ($masterWeightageConfig->qualification_id && $masterWeightageConfig->qualification_marks) {
                $qualificationIds = json_decode($masterWeightageConfig->qualification_id, true);
                $qualificationMarksArray = json_decode($masterWeightageConfig->qualification_marks, true);

                // Ensure all variables are arrays
                $qualificationIds = is_array($qualificationIds) ? $qualificationIds : ($qualificationIds !== null ? [$qualificationIds] : []);
                $qualificationMarksArray = is_array($qualificationMarksArray) ? $qualificationMarksArray : ($qualificationMarksArray !== null ? [$qualificationMarksArray] : []);

                foreach ($qualificationIds as $index => $qualification_id) {
                    $marks = $qualificationMarksArray[$index] ?? 0;
                    if ($marks <= 0)
                        continue;

                    DB::table('master_qualification_marks')->insert([
                        'post_id' => $uploaded_post_id,
                        'qualification_id' => $qualification_id,
                        'marks' => (int) $marks,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Transfer Experience Weightage
            if (
                $masterWeightageConfig->minimum_experience_years !== null ||
                $masterWeightageConfig->increment_value_per_year !== null ||
                $masterWeightageConfig->maximum_experience_marks !== null
            ) {
                $min = $masterWeightageConfig->minimum_experience_years ?? 0;
                $inc = $masterWeightageConfig->increment_value_per_year ?? 0;
                $max = $masterWeightageConfig->maximum_experience_marks ?? 0;

                if ($min > 0 || $inc > 0 || $max > 0) {
                    DB::table('master_experience_weightage')->insert([
                        'post_id' => $uploaded_post_id,
                        'minimum_experience_years' => $min,
                        'increment_value_per_year' => $inc,
                        'maximum_experience_marks' => $max,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Transfer Qualification Multipliers
            if ($masterWeightageConfig->multiplyer_qualification_id && $masterWeightageConfig->multiplier_value) {
                $multiplierQualificationIds = json_decode($masterWeightageConfig->multiplyer_qualification_id, true);
                $multiplierValues = json_decode($masterWeightageConfig->multiplier_value, true);

                // Ensure all variables are arrays
                $multiplierQualificationIds = is_array($multiplierQualificationIds) ? $multiplierQualificationIds : ($multiplierQualificationIds !== null ? [$multiplierQualificationIds] : []);
                $multiplierValues = is_array($multiplierValues) ? $multiplierValues : ($multiplierValues !== null ? [$multiplierValues] : []);

                foreach ($multiplierQualificationIds as $index => $qualification_id) {
                    $multiplier_value = $multiplierValues[$index] ?? 0;
                    if ($multiplier_value <= 0)
                        continue;

                    DB::table('master_qualification_multiplier')->insert([
                        'post_id' => $uploaded_post_id,
                        'qualification_id' => $qualification_id,
                        'multiplier_value' => (float) $multiplier_value,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Transfer Caste Marks
            if ($masterWeightageConfig->caste_id && $masterWeightageConfig->caste_marks) {
                $casteIds = json_decode($masterWeightageConfig->caste_id, true);
                $casteMarksArray = json_decode($masterWeightageConfig->caste_marks, true);

                // Ensure all variables are arrays
                $casteIds = is_array($casteIds) ? $casteIds : ($casteIds !== null ? [$casteIds] : []);
                $casteMarksArray = is_array($casteMarksArray) ? $casteMarksArray : ($casteMarksArray !== null ? [$casteMarksArray] : []);

                foreach ($casteIds as $index => $caste_id) {
                    $marks = $casteMarksArray[$index] ?? 0;
                    if ($marks <= 0)
                        continue;

                    DB::table('master_caste_marks')->insert([
                        'post_id' => $uploaded_post_id,
                        'caste_id' => $caste_id,
                        'marks' => (int) $marks,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Display a listing of static post weightage configurations
     */
    public function index()
    {
        $posts = DB::table('master_post_config')
            ->select(
                'master_post_config.*',
                DB::raw('(SELECT COUNT(*) FROM master_weightage_config WHERE master_weightage_config.fk_post_config_id = master_post_config.id AND master_weightage_config.is_active = "1") as weightage_config_count')
            )
            ->orderBy('title', 'asc')
            ->get();
        // dd($posts);
        foreach ($posts as $post) {
            if (is_object($post)) {
                // Check if configured in master_weightage_config table
                $post->is_fully_configured = (isset($post->weightage_config_count) && $post->weightage_config_count > 0);
            }
        }

        return view('admin.static_forms.static_weightage.index_static_weightage', compact('posts'));
    }

    /**
     * Show the form for creating new static post weightage
     */
    public function create(Request $request)
    {
        $post_config_id = $request->query('post_config_id');

        // Get all static posts with weightage disabled (is_weightage = 0)
        $posts = DB::table('master_post_config')
            ->where('is_weightage', '0')
            ->orderBy('title', 'asc')
            ->get();

        // Get all qualifications
        $qualifications = DB::table('master_qualification')
            ->orderBy('Quali_ID', 'asc')
            ->get();

        // Get all castes
        $castes = DB::table('master_tbl_caste')
            ->orderBy('caste_id', 'asc')
            ->get();

        return view('admin.static_forms.static_weightage.create_static_weightage', compact('posts', 'qualifications', 'castes', 'post_config_id'));
    }

    /**
     * Store newly created static post weightage
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_config_id' => 'required|integer',
            'weightage' => 'required|array',
            'weightage.*' => 'nullable|integer|min:0',
            'qualification_marks' => 'nullable|array',
            'qualification_marks.*' => 'nullable|integer|min:0',
            'caste_marks' => 'nullable|array',
            'caste_marks.*' => 'nullable|integer|min:0',
            'minimum_experience_years' => 'nullable|numeric|min:0',
            'increment_value_per_year' => 'nullable|numeric|min:0',
            'maximum_experience_marks' => 'nullable|numeric|min:0',
            'qualification_multiplier' => 'nullable|array',
            'qualification_multiplier.*' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $post_config_id = (int) $request->post_config_id;
            $ip_address = $request->ip();
            $user_id = (int) Session::get('sess_user_id', 0);

            // Save to master_weightage_config
            $this->saveMasterWeightageConfig($request, $post_config_id, $ip_address, $user_id);

            DB::commit();

            return redirect()->route('admin.static-weightage.index')
                ->with('success', 'अपरिवर्तनीय पोस्ट के लिए वेटेज मार्क्स सफलतापूर्वक सहेजे गए हैं।');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'वेटेज मार्क्स सहेजने में त्रुटि हुई: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get existing weightage data from master_weightage_config table
     */
    private function getMasterWeightageConfig(int $post_config_id): ?object
    {
        return DB::table('master_weightage_config')
            ->where('fk_post_config_id', $post_config_id)
            ->where('is_active', '1')
            ->first();
    }

    /**
     * Show the form for editing static post weightage
     */
    public function edit($post_config_id)
    {
        $post = DB::table('master_post_config')->where('id', $post_config_id)->first();

        if (!$post) {
            return redirect()->route('admin.static-weightage.index')
                ->with('error', 'पोस्ट नहीं मिली');
        }

        // Decode the questions from fk_ques_id
        $question_ids = [];
        if ($post->fk_ques_id) {
            $decoded = json_decode($post->fk_ques_id, true);

            // Ensure $question_ids is always an array
            if ($decoded === null) {
                $question_ids = [];
            } elseif (!is_array($decoded)) {
                // If it's a single integer, convert to array
                $question_ids = [$decoded];
            } else {
                $question_ids = $decoded;
            }
        }

        // Get questions with weightage enabled
        $questions = [];
        if (!empty($question_ids)) {
            $questionsData = DB::table('master_post_questions')
                ->whereIn('ques_ID', $question_ids)
                ->where('is_active', '1')
                ->where('is_weightage_marks', '1')
                ->orderBy('ques_name', 'asc')
                ->get();

            foreach ($questionsData as $question) {
                $question->parsed_options = $question->answer_options ? json_decode($question->answer_options) : [];
                $questions[] = $question;
            }
        }

        // Get specific qualifications for this post
        $qualifications = [];
        if ($post->quali_id) {
            $qualificationIds = json_decode($post->quali_id, true);

            // Ensure $qualificationIds is always an array
            if ($qualificationIds === null) {
                $qualificationIds = [];
            } elseif (!is_array($qualificationIds)) {
                // If it's a single integer, convert to array
                $qualificationIds = [$qualificationIds];
            }

            if (!empty($qualificationIds)) {
                $qualifications = DB::table('master_qualification')
                    ->whereIn('Quali_ID', $qualificationIds)
                    ->orderBy('Quali_ID', 'asc')
                    ->get();
            }
        }

        // Get minimum qualification for this post
        $minimumQualification = [];
        if ($post->quali_id) {
            $qualificationIds = json_decode($post->quali_id, true);

            // Ensure $qualificationIds is always an array
            if ($qualificationIds === null) {
                $qualificationIds = [];
            } elseif (!is_array($qualificationIds)) {
                // If it's a single integer, convert to array
                $qualificationIds = [$qualificationIds];
            }

            if (!empty($qualificationIds)) {
                $minimumQualification = DB::table('master_qualification')
                    ->whereIn('Quali_ID', $qualificationIds)
                    ->get();
            }
        }

        // Get all castes from master table directly
        $castes = DB::table('master_tbl_caste')
            ->orderBy('caste_id', 'asc')
            ->get();

        // Get existing weightage data from master_weightage_config
        $masterWeightageConfig = $this->getMasterWeightageConfig($post_config_id);

        // Initialize arrays for existing data
        $existingWeightage = [];
        $existingQualificationMarks = [];
        $existingExperience = null;
        $existingMultipliers = [];
        $existingCasteMarks = [];

        if ($masterWeightageConfig) {
            // Process question weightage data
            if ($masterWeightageConfig->question_id && $masterWeightageConfig->question_marks) {
                $questionIds = json_decode($masterWeightageConfig->question_id, true);
                $optionValues = json_decode($masterWeightageConfig->option_value, true);
                $questionMarks = json_decode($masterWeightageConfig->question_marks, true);

                // Ensure all variables are arrays
                $questionIds = is_array($questionIds) ? $questionIds : ($questionIds !== null ? [$questionIds] : []);
                $optionValues = is_array($optionValues) ? $optionValues : ($optionValues !== null ? [$optionValues] : []);
                $questionMarks = is_array($questionMarks) ? $questionMarks : ($questionMarks !== null ? [$questionMarks] : []);

                foreach ($questionIds as $index => $questionId) {
                    $optionValue = $optionValues[$index] ?? null;
                    $key = $optionValue ? $questionId . '-' . $optionValue : $questionId;

                    $existingWeightage[$key] = (object) [
                        'question_id' => $questionId,
                        'option_value' => $optionValue,
                        'marks' => $questionMarks[$index] ?? 0,
                    ];
                }
            }

            // Process qualification marks
            if ($masterWeightageConfig->qualification_id && $masterWeightageConfig->qualification_marks) {
                $qualificationIds = json_decode($masterWeightageConfig->qualification_id, true);
                $qualificationMarksArray = json_decode($masterWeightageConfig->qualification_marks, true);

                // Ensure all variables are arrays
                $qualificationIds = is_array($qualificationIds) ? $qualificationIds : ($qualificationIds !== null ? [$qualificationIds] : []);
                $qualificationMarksArray = is_array($qualificationMarksArray) ? $qualificationMarksArray : ($qualificationMarksArray !== null ? [$qualificationMarksArray] : []);

                foreach ($qualificationIds as $index => $qualificationId) {
                    $existingQualificationMarks[$qualificationId] = (object) [
                        'qualification_id' => $qualificationId,
                        'marks' => $qualificationMarksArray[$index] ?? 0,
                    ];
                }
            }

            // Process experience data
            if (
                $masterWeightageConfig->minimum_experience_years !== null ||
                $masterWeightageConfig->increment_value_per_year !== null ||
                $masterWeightageConfig->maximum_experience_marks !== null
            ) {
                $existingExperience = (object) [
                    'minimum_experience_years' => $masterWeightageConfig->minimum_experience_years,
                    'increment_value_per_year' => $masterWeightageConfig->increment_value_per_year,
                    'maximum_experience_marks' => $masterWeightageConfig->maximum_experience_marks,
                ];
            }

            // Process qualification multipliers
            if ($masterWeightageConfig->multiplyer_qualification_id && $masterWeightageConfig->multiplier_value) {
                $multiplierQualificationIds = json_decode($masterWeightageConfig->multiplyer_qualification_id, true);
                $multiplierValues = json_decode($masterWeightageConfig->multiplier_value, true);

                // Ensure all variables are arrays
                $multiplierQualificationIds = is_array($multiplierQualificationIds) ? $multiplierQualificationIds : ($multiplierQualificationIds !== null ? [$multiplierQualificationIds] : []);
                $multiplierValues = is_array($multiplierValues) ? $multiplierValues : ($multiplierValues !== null ? [$multiplierValues] : []);

                foreach ($multiplierQualificationIds as $index => $qualificationId) {
                    $existingMultipliers[$qualificationId] = (object) [
                        'qualification_id' => $qualificationId,
                        'multiplier_value' => $multiplierValues[$index] ?? 0,
                    ];
                }
            }

            // Process caste marks
            if ($masterWeightageConfig->caste_id && $masterWeightageConfig->caste_marks) {
                $casteIds = json_decode($masterWeightageConfig->caste_id, true);
                $casteMarksArray = json_decode($masterWeightageConfig->caste_marks, true);

                // Ensure all variables are arrays
                $casteIds = is_array($casteIds) ? $casteIds : ($casteIds !== null ? [$casteIds] : []);
                $casteMarksArray = is_array($casteMarksArray) ? $casteMarksArray : ($casteMarksArray !== null ? [$casteMarksArray] : []);

                foreach ($casteIds as $index => $casteId) {
                    $existingCasteMarks[$casteId] = (object) [
                        'caste_id' => $casteId,
                        'marks' => $casteMarksArray[$index] ?? 0,
                    ];
                }
            }
        }

        // Rename variables to match view expectations
        $weightageMarks = $existingWeightage;
        $qualificationMarks = $existingQualificationMarks;
        $experienceWeightage = $existingExperience;
        $qualificationMultipliers = $existingMultipliers;
        $casteMarks = $existingCasteMarks;

        return view('admin.static_forms.static_weightage.edit_static_weightage', compact(
            'post',
            'questions',
            'qualifications',
            'minimumQualification',
            'castes',
            'weightageMarks',
            'qualificationMarks',
            'experienceWeightage',
            'qualificationMultipliers',
            'casteMarks'
        ));
    }

    /**
     * Update static post weightage
     */
    public function update(Request $request, $post_config_id)
    {
        $validator = Validator::make($request->all(), [
            'weightage' => 'required|array',
            'weightage.*' => 'nullable|integer|min:0',
            'qualification_marks' => 'nullable|array',
            'qualification_marks.*' => 'nullable|integer|min:0',
            'caste_marks' => 'nullable|array',
            'caste_marks.*' => 'nullable|integer|min:0',
            'minimum_experience_years' => 'nullable|numeric|min:0',
            'increment_value_per_year' => 'nullable|numeric|min:0',
            'maximum_experience_marks' => 'nullable|numeric|min:0',
            'qualification_multiplier' => 'nullable|array',
            'qualification_multiplier.*' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'सत्यापन त्रुटि',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $ip_address = $request->ip();
            $user_id = (int) Session::get('sess_user_id', 0);

            // Save to master_weightage_config
            $this->saveMasterWeightageConfig($request, $post_config_id, $ip_address, $user_id);

            DB::commit();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'अपरिवर्तनीय पोस्ट के लिए वेटेज मार्क्स सफलतापूर्वक अपडेट किए गए हैं।',
                    'redirect' => route('admin.static-weightage.index')
                ]);
            }

            return redirect()->route('admin.static-weightage.index')
                ->with('success', 'अपरिवर्तनीय पोस्ट के लिए वेटेज मार्क्स सफलतापूर्वक अपडेट किए गए हैं।');
        } catch (\Exception $e) {
            DB::rollBack();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'वेटेज मार्क्स अपडेट करने में त्रुटि हुई: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'वेटेज मार्क्स अपडेट करने में त्रुटि हुई: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get questions by post config ID for AJAX
     */
    public function getQuestionsByPostConfig($post_config_id)
    {
        try {
            $post = DB::table('master_post_config')->where('id', $post_config_id)->first();

            if (!$post) {
                return response()->json(['success' => false, 'message' => 'पोस्ट नहीं मिली'], 404);
            }

            // Decode the questions from fk_ques_id
            $question_ids = [];
            if ($post->fk_ques_id) {
                $decoded = json_decode($post->fk_ques_id, true);

                // Ensure $question_ids is always an array
                if ($decoded === null) {
                    $question_ids = [];
                } elseif (!is_array($decoded)) {
                    // If it's a single integer, convert to array
                    $question_ids = [$decoded];
                } else {
                    $question_ids = $decoded;
                }
            }

            // Get questions with weightage enabled
            $questions = [];
            if (!empty($question_ids)) {
                $questionsData = DB::table('master_post_questions')
                    ->whereIn('ques_ID', $question_ids)
                    ->where('is_active', '1')
                    ->where('is_weightage_marks', '1')
                    ->orderBy('ques_name', 'asc')
                    ->get();

                foreach ($questionsData as $question) {
                    $question->parsed_options = $question->answer_options ? json_decode($question->answer_options) : [];
                    $questions[] = $question;
                }
            }

            // Get specific qualifications for this post
            $postQualifications = [];
            if ($post->quali_id) {
                $qualificationIds = json_decode($post->quali_id, true);

                // Ensure $qualificationIds is always an array
                if ($qualificationIds === null) {
                    $qualificationIds = [];
                } elseif (!is_array($qualificationIds)) {
                    // If it's a single integer, convert to array
                    $qualificationIds = [$qualificationIds];
                }

                if (!empty($qualificationIds)) {
                    $postQualifications = DB::table('master_qualification')
                        ->whereIn('Quali_ID', $qualificationIds)
                        ->orderBy('Quali_ID', 'asc')
                        ->get();
                }
            }

            // Get minimum qualification for this post
            $minimumQualification = [];
            // dd($post->quali_id);
            if ($post->quali_id) {
                $qualificationIds = json_decode($post->quali_id, true);

                // Ensure $qualificationIds is always an array
                if ($qualificationIds === null) {
                    $qualificationIds = [];
                } elseif (!is_array($qualificationIds)) {
                    // If it's a single integer, convert to array
                    $qualificationIds = [$qualificationIds];
                }

                if (!empty($qualificationIds)) {
                    $minimumQualification = DB::table('master_qualification')
                        ->whereIn('Quali_ID', $qualificationIds)
                        ->get();
                }
            }

            // Get all castes from master table directly
            $postCastes = DB::table('master_tbl_caste')
                ->orderBy('caste_id', 'asc')
                ->get();

            // Get existing weightage data from master_weightage_config
            $existingConfig = $this->getMasterWeightageConfig($post_config_id);
            $existingWeightage = [];
            $existingQualificationMarks = [];
            $existingCasteMarks = [];
            $existingMultipliers = [];
            $experienceWeightage = null;

            if ($existingConfig) {
                // Process existing question weightage
                if ($existingConfig->question_id && $existingConfig->question_marks) {
                    $questionIds = json_decode($existingConfig->question_id, true);
                    $optionValues = json_decode($existingConfig->option_value, true);
                    $questionMarks = json_decode($existingConfig->question_marks, true);

                    // Ensure all variables are arrays
                    $questionIds = is_array($questionIds) ? $questionIds : ($questionIds !== null ? [$questionIds] : []);
                    $optionValues = is_array($optionValues) ? $optionValues : ($optionValues !== null ? [$optionValues] : []);
                    $questionMarks = is_array($questionMarks) ? $questionMarks : ($questionMarks !== null ? [$questionMarks] : []);

                    for ($i = 0; $i < count($questionIds); $i++) {
                        $key = isset($optionValues[$i]) ? $questionIds[$i] . '-' . $optionValues[$i] : $questionIds[$i];
                        $existingWeightage[$key] = (object) ['marks' => $questionMarks[$i] ?? 0];
                    }
                }

                // Process existing qualification marks
                if ($existingConfig->qualification_id && $existingConfig->qualification_marks) {
                    $qualIds = json_decode($existingConfig->qualification_id, true);
                    $qualMarks = json_decode($existingConfig->qualification_marks, true);

                    // Ensure all variables are arrays
                    $qualIds = is_array($qualIds) ? $qualIds : ($qualIds !== null ? [$qualIds] : []);
                    $qualMarks = is_array($qualMarks) ? $qualMarks : ($qualMarks !== null ? [$qualMarks] : []);

                    for ($i = 0; $i < count($qualIds); $i++) {
                        $existingQualificationMarks[$qualIds[$i]] = (object) ['marks' => $qualMarks[$i] ?? 0];
                    }
                }

                // Process existing caste marks
                if ($existingConfig->caste_id && $existingConfig->caste_marks) {
                    $casteIds = json_decode($existingConfig->caste_id, true);
                    $casteMarks = json_decode($existingConfig->caste_marks, true);

                    // Ensure all variables are arrays
                    $casteIds = is_array($casteIds) ? $casteIds : ($casteIds !== null ? [$casteIds] : []);
                    $casteMarks = is_array($casteMarks) ? $casteMarks : ($casteMarks !== null ? [$casteMarks] : []);

                    for ($i = 0; $i < count($casteIds); $i++) {
                        $existingCasteMarks[$casteIds[$i]] = (object) ['marks' => $casteMarks[$i] ?? 0];
                    }
                }

                // Process existing qualification multipliers
                if ($existingConfig->multiplyer_qualification_id && $existingConfig->multiplier_value) {
                    $multQualIds = json_decode($existingConfig->multiplyer_qualification_id, true);
                    $multValues = json_decode($existingConfig->multiplier_value, true);

                    // Ensure all variables are arrays
                    $multQualIds = is_array($multQualIds) ? $multQualIds : ($multQualIds !== null ? [$multQualIds] : []);
                    $multValues = is_array($multValues) ? $multValues : ($multValues !== null ? [$multValues] : []);

                    for ($i = 0; $i < count($multQualIds); $i++) {
                        $existingMultipliers[] = (object) [
                            'qualification_id' => $multQualIds[$i],
                            'multiplier_value' => $multValues[$i] ?? 0
                        ];
                    }
                }

                // Process existing experience weightage
                if ($existingConfig->minimum_experience_years || $existingConfig->increment_value_per_year || $existingConfig->maximum_experience_marks) {
                    $experienceWeightage = (object) [
                        'minimum_experience_years' => $existingConfig->minimum_experience_years,
                        'increment_value_per_year' => $existingConfig->increment_value_per_year,
                        'maximum_experience_marks' => $existingConfig->maximum_experience_marks
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'questions' => $questions,
                'qualifications' => $postQualifications,
                'castes' => $postCastes,
                'minimumQualification' => $minimumQualification,
                'post' => $post,
                'existingWeightage' => $existingWeightage,
                'existingQualificationMarks' => $existingQualificationMarks,
                'existingCasteMarks' => $existingCasteMarks,
                'qualificationMultipliers' => $existingMultipliers,
                'experienceWeightage' => $experienceWeightage
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get weightage configuration data for rendering UI
     */
    public function getWeightageConfigData($post_config_id)
    {
        try {
            $masterWeightageConfig = $this->getMasterWeightageConfig($post_config_id);

            if (!$masterWeightageConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'No weightage configuration found for this post'
                ], 404);
            }

            // Prepare response data
            $responseData = [
                'success' => true,
                'data' => [
                    'post_id' => $masterWeightageConfig->fk_post_id,
                    'post_config_id' => $masterWeightageConfig->fk_post_config_id,
                    'questions' => [],
                    'qualifications' => [],
                    'castes' => [],
                    'multipliers' => [],
                    'experience' => []
                ]
            ];

            // Process questions data
            if ($masterWeightageConfig->question_id && $masterWeightageConfig->question_marks) {
                $questionIds = json_decode($masterWeightageConfig->question_id, true) ?? [];
                $optionValues = json_decode($masterWeightageConfig->option_value, true) ?? [];
                $questionMarks = json_decode($masterWeightageConfig->question_marks, true) ?? [];

                foreach ($questionIds as $index => $questionId) {
                    $responseData['data']['questions'][] = [
                        'question_id' => $questionId,
                        'option_value' => $optionValues[$index] ?? null,
                        'marks' => $questionMarks[$index] ?? 0
                    ];
                }
            }

            // Process qualification marks
            if ($masterWeightageConfig->qualification_id && $masterWeightageConfig->qualification_marks) {
                $qualificationIds = json_decode($masterWeightageConfig->qualification_id, true) ?? [];
                $qualificationMarksArray = json_decode($masterWeightageConfig->qualification_marks, true) ?? [];

                foreach ($qualificationIds as $index => $qualificationId) {
                    $responseData['data']['qualifications'][] = [
                        'qualification_id' => $qualificationId,
                        'marks' => $qualificationMarksArray[$index] ?? 0
                    ];
                }
            }

            // Process caste marks
            if ($masterWeightageConfig->caste_id && $masterWeightageConfig->caste_marks) {
                $casteIds = json_decode($masterWeightageConfig->caste_id, true) ?? [];
                $casteMarksArray = json_decode($masterWeightageConfig->caste_marks, true) ?? [];

                foreach ($casteIds as $index => $casteId) {
                    $responseData['data']['castes'][] = [
                        'caste_id' => $casteId,
                        'marks' => $casteMarksArray[$index] ?? 0
                    ];
                }
            }

            // Process qualification multipliers
            if ($masterWeightageConfig->multiplyer_qualification_id && $masterWeightageConfig->multiplier_value) {
                $multiplierQualificationIds = json_decode($masterWeightageConfig->multiplyer_qualification_id, true) ?? [];
                $multiplierValues = json_decode($masterWeightageConfig->multiplier_value, true) ?? [];

                foreach ($multiplierQualificationIds as $index => $qualificationId) {
                    $responseData['data']['multipliers'][] = [
                        'qualification_id' => $qualificationId,
                        'multiplier_value' => $multiplierValues[$index] ?? 0
                    ];
                }
            }

            // Process experience data
            if (
                $masterWeightageConfig->minimum_experience_years !== null ||
                $masterWeightageConfig->increment_value_per_year !== null ||
                $masterWeightageConfig->maximum_experience_marks !== null
            ) {
                $responseData['data']['experience'] = [
                    'minimum_experience_years' => $masterWeightageConfig->minimum_experience_years,
                    'increment_value_per_year' => $masterWeightageConfig->increment_value_per_year,
                    'maximum_experience_marks' => $masterWeightageConfig->maximum_experience_marks
                ];
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display weightage configuration in a formatted view
     */
    public function showWeightageConfig($post_config_id)
    {
        try {
            $post = DB::table('master_post_config')->where('id', $post_config_id)->first();

            if (!$post) {
                return redirect()->route('admin.static-weightage.index')
                    ->with('error', 'पोस्ट नहीं मिली');
            }

            $masterWeightageConfig = $this->getMasterWeightageConfig($post_config_id);

            if (!$masterWeightageConfig) {
                return redirect()->route('admin.static-weightage.index')
                    ->with('error', 'इस पोस्ट के लिए कोई वेटेज कॉन्फ़िगरेशन नहीं मिली');
            }

            // Get related data for display
            $questions = [];
            $qualifications = [];
            $castes = [];

            // Fetch questions data if available
            if ($masterWeightageConfig->question_id) {
                $questionIds = json_decode($masterWeightageConfig->question_id, true) ?? [];
                if (!empty($questionIds)) {
                    $questions = DB::table('master_post_questions')
                        ->whereIn('ques_ID', $questionIds)
                        ->get()
                        ->keyBy('ques_ID');
                }
            }

            // Fetch qualifications data if available
            if ($masterWeightageConfig->qualification_id) {
                $qualificationIds = json_decode($masterWeightageConfig->qualification_id, true) ?? [];
                if (!empty($qualificationIds)) {
                    $qualifications = DB::table('master_qualification')
                        ->whereIn('Quali_ID', $qualificationIds)
                        ->get()
                        ->keyBy('Quali_ID');
                }
            }

            // Fetch castes data if available
            if ($masterWeightageConfig->caste_id) {
                $casteIds = json_decode($masterWeightageConfig->caste_id, true) ?? [];
                if (!empty($casteIds)) {
                    $castes = DB::table('master_tbl_caste')
                        ->whereIn('caste_id', $casteIds)
                        ->get()
                        ->keyBy('caste_id');
                }
            }

            return view('admin.static_forms.static_weightage.show_weightage_config', compact(
                'post',
                'masterWeightageConfig',
                'questions',
                'qualifications',
                'castes'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.static-weightage.index')
                ->with('error', 'वेटेज कॉन्फ़िगरेशन लोड करने में त्रुटि: ' . $e->getMessage());
        }
    }
}
