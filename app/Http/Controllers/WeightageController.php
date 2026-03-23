<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WeightageController extends Controller
{
    /**
     * Persist question weightage marks. For non-M and O(>2) we ensure single selection by grouping.
     */
    private function persistWeightage(array $weightage, int $post_id, string $ip_address, int $user_id): void
    {
        // Clear existing and re-insert for simpler, predictable state
        DB::table('master_weightage_marks')->where('post_id', $post_id)->delete();

        // Fetch questions map
        $questions = DB::table('master_post_questions')
            ->where('is_active', '1')
            ->where('is_weightage_marks', '1')
            ->get()
            ->keyBy('ques_ID');

        $groupedWeightage = [];
        foreach ($weightage as $key => $value) {
            $parts = explode('-', $key);
            $question_id = $parts[0];
            $option_value = isset($parts[1]) ? $parts[1] : null;
            if (!isset($questions[$question_id]))
                continue;

            $isOTypeMultipleOptions = ($questions[$question_id]->ans_type === 'O') &&
                (count(json_decode($questions[$question_id]->answer_options)) > 2);

            if ($questions[$question_id]->ans_type !== 'M' && !$isOTypeMultipleOptions) {
                $intVal = empty($value) ? 0 : (int) $value;
                if (!isset($groupedWeightage[$question_id]) || $intVal > 0) {
                    $groupedWeightage[$question_id] = [
                        'option_value' => $option_value,
                        'value' => $intVal,
                    ];
                }
            } else {
                $marks = empty($value) ? 0 : (int) $value;
                if ($marks > 0) {
                    DB::table('master_weightage_marks')->insert([
                        'post_id' => $post_id,
                        'question_id' => $question_id,
                        'option_value' => $option_value,
                        'marks' => $marks,
                        'is_active' => '1',
                        'ip_address' => $ip_address,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        foreach ($groupedWeightage as $question_id => $data) {
            $marks = $data['value'];
            if ($marks <= 0)
                continue;
            DB::table('master_weightage_marks')->insert([
                'post_id' => $post_id,
                'question_id' => $question_id,
                'option_value' => $data['option_value'],
                'marks' => $marks,
                'is_active' => '1',
                'ip_address' => $ip_address,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** Save qualification marks via delete+insert semantics. */
    private function persistQualificationMarks(?array $qualificationMarks, int $post_id, int $user_id): void
    {
        DB::table('master_qualification_marks')->where('post_id', $post_id)->delete();
        if (!$qualificationMarks)
            return;
        foreach ($qualificationMarks as $qualification_id => $value) {
            $marks = empty($value) ? 0 : (int) $value;
            if ($marks <= 0)
                continue;
            DB::table('master_qualification_marks')->insert([
                'post_id' => $post_id,
                'qualification_id' => $qualification_id,
                'marks' => $marks,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** Upsert experience weightage or clear if all zeros. */
    private function persistExperience(?float $minYears, ?float $incPerYear, ?float $maxMarks, int $post_id, int $user_id): void
    {
        $min = $minYears ?? 0;
        $inc = $incPerYear ?? 0;
        $max = $maxMarks ?? 0;
        DB::table('master_experience_weightage')->where('post_id', $post_id)->delete();
        if ($min > 0 || $inc > 0 || $max > 0) {
            DB::table('master_experience_weightage')->insert([
                'post_id' => $post_id,
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

    /** Save qualification multipliers via delete+insert semantics. */
    private function persistQualificationMultipliers(?array $multipliers, int $post_id, int $user_id): void
    {
        DB::table('master_qualification_multiplier')->where('post_id', $post_id)->delete();
        if (!$multipliers)
            return;
        foreach ($multipliers as $qualification_id => $value) {
            $mv = empty($value) ? 0 : (float) $value;
            if ($mv <= 0)
                continue;
            DB::table('master_qualification_multiplier')->insert([
                'post_id' => $post_id,
                'qualification_id' => $qualification_id,
                'multiplier_value' => $mv,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** Save caste-wise marks via delete+insert. */
    private function persistCasteMarks(?array $casteMarks, int $post_id, int $user_id): void
    {
        DB::table('master_caste_marks')->where('post_id', $post_id)->delete();
        if (!$casteMarks)
            return;
        foreach ($casteMarks as $caste_id => $value) {
            $marks = empty($value) ? 0 : (int) $value;
            if ($marks <= 0)
                continue;
            DB::table('master_caste_marks')->insert([
                'post_id' => $post_id,
                'caste_id' => $caste_id,
                'marks' => $marks,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    /**
     * Display a listing of the weightage marks.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Get all active static posts with comprehensive weightage configuration status
        $posts = DB::table('master_post_config')
            ->select(
                'master_post_config.*',
                'master_post_config.id as post_id',
                // Check if question weightage marks are configured
                DB::raw('(SELECT COUNT(*) FROM master_weightage_marks WHERE master_weightage_marks.post_id = master_post_config.id) as weightage_count'),

                // Check if qualification marks are configured
                DB::raw('(SELECT COUNT(*) FROM master_qualification_marks WHERE master_qualification_marks.post_id = master_post_config.id) as qualification_marks_count'),

                // Check if experience weightage is configured
                DB::raw('(CASE WHEN EXISTS (SELECT 1 FROM master_experience_weightage WHERE master_experience_weightage.post_id = master_post_config.id) THEN 1 ELSE 0 END) as has_experience_weightage'),

                // Check if qualification multipliers are configured
                DB::raw('(SELECT COUNT(*) FROM master_qualification_multiplier WHERE master_qualification_multiplier.post_id = master_post_config.id) as qualification_multiplier_count'),
                // Check if caste-wise marks are configured
                DB::raw('(SELECT COUNT(*) FROM master_caste_marks WHERE master_caste_marks.post_id = master_post_config.id) as caste_marks_count')
            )
            ->where('master_post_config.is_active', '1')
            ->orderBy('title', 'asc')
            ->get();
        // Make sure we have posts as an array of objects, not a string
        if (!is_object($posts) && !is_array($posts)) {
            $posts = [];
        }

        // Process posts to determine overall weightage configuration status
        foreach ($posts as $post) {
            if (is_object($post)) {
                // A post is considered fully configured if at least one type of weightage is set up
                $post->is_fully_configured =
                    (isset($post->weightage_count) && $post->weightage_count > 0) ||
                    (isset($post->qualification_marks_count) && $post->qualification_marks_count > 0) ||
                    (isset($post->has_experience_weightage) && $post->has_experience_weightage > 0) ||
                    (isset($post->qualification_multiplier_count) && $post->qualification_multiplier_count > 0) ||
                    (isset($post->caste_marks_count) && $post->caste_marks_count > 0);
            }
        }

        return view('/admin/static_forms/static_post_list', compact('posts'));
    }

    /**
     * Show the form for creating a new weightage marks.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        // Get selected post_id from query parameter
        $selected_post_id = $request->query('post_id');

        // Get static posts from master_post_config
        $posts = DB::table('master_post_config')
            ->select('id as post_id', 'title')
            ->where('is_active', '1')
            ->orderBy('title', 'asc')
            ->get();

        // Get all qualifications from master_qualification table
        $qualifications = DB::table('master_qualification')
            ->orderBy('Quali_ID', 'asc')
            ->get();

        // Get all castes
        $castes = DB::table('master_tbl_caste')
            ->orderBy('caste_id', 'asc')
            ->get();

        return view('admin.weightage.create', compact('posts', 'qualifications', 'castes', 'selected_post_id'));
    }


    /**
     * Store a newly created weightage configuration in storage.
     * 
     * SAVES TO: master_weightage_config (consolidated JSON storage ONLY)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
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
            // Begin transaction
            DB::beginTransaction();

            $post_id = (int) $request->post_id;
            $ip_address = $request->ip();
            $user_id = (int) Session::get('sess_user_id', 0);

            // ========================================================================
            // Prepare JSON data for master_weightage_config
            // ========================================================================

            // Prepare Question Weightage JSON arrays
            $questionIds = [];
            $optionValues = [];
            $questionMarks = [];

            foreach ($request->weightage as $key => $marks) {
                if (!empty($marks) && $marks > 0) {
                    $parts = explode('-', $key);
                    $questionIds[] = $parts[0];
                    $optionValues[] = isset($parts[1]) ? $parts[1] : null;
                    $questionMarks[] = (int) $marks;
                }
            }

            // Prepare Qualification Marks JSON arrays
            $qualificationIds = [];
            $qualificationMarksArr = [];

            if ($request->qualification_marks) {
                foreach ($request->qualification_marks as $qualId => $marks) {
                    if (!empty($marks) && $marks > 0) {
                        $qualificationIds[] = $qualId;
                        $qualificationMarksArr[] = (int) $marks;
                    }
                }
            }

            // Prepare Caste Marks JSON arrays
            $casteIds = [];
            $casteMarksArr = [];

            if ($request->caste_marks) {
                foreach ($request->caste_marks as $casteId => $marks) {
                    if (!empty($marks) && $marks > 0) {
                        $casteIds[] = $casteId;
                        $casteMarksArr[] = (int) $marks;
                    }
                }
            }

            // Prepare Qualification Multiplier JSON arrays
            $multiplierQualIds = [];
            $multiplierValues = [];

            if ($request->qualification_multiplier) {
                foreach ($request->qualification_multiplier as $qualId => $multValue) {
                    if (!empty($multValue) && $multValue > 0) {
                        $multiplierQualIds[] = $qualId;
                        $multiplierValues[] = (float) $multValue;
                    }
                }
            }

            // ========================================================================
            // Insert/Update master_weightage_config (single consolidated row)
            // ========================================================================
            $configData = [
                'fk_post_id' => null, // Static posts don't have dynamic post_id
                'fk_post_config_id' => $post_id,
                'question_id' => !empty($questionIds) ? json_encode($questionIds) : null,
                'option_value' => !empty($optionValues) ? json_encode($optionValues) : null,
                'question_marks' => !empty($questionMarks) ? json_encode($questionMarks) : null,
                'qualification_id' => !empty($qualificationIds) ? json_encode($qualificationIds) : null,
                'qualification_marks' => !empty($qualificationMarksArr) ? json_encode($qualificationMarksArr) : null,
                'caste_id' => !empty($casteIds) ? json_encode($casteIds) : null,
                'caste_marks' => !empty($casteMarksArr) ? json_encode($casteMarksArr) : null,
                'multiplyer_qualification_id' => !empty($multiplierQualIds) ? json_encode($multiplierQualIds) : null,
                'multiplier_value' => !empty($multiplierValues) ? json_encode($multiplierValues) : null,
                'minimum_experience_years' => $request->minimum_experience_years ?? null,
                'increment_value_per_year' => $request->increment_value_per_year ?? null,
                'maximum_experience_marks' => $request->maximum_experience_marks ?? null,
                'is_active' => '1',
                'ip_address' => $ip_address,
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'updated_at' => now()
            ];

            // Check if config already exists
            $existingConfig = DB::table('master_weightage_config')
                ->where('fk_post_config_id', $post_id)
                ->first();

            if ($existingConfig) {
                // Update existing config
                DB::table('master_weightage_config')
                    ->where('fk_post_config_id', $post_id)
                    ->update($configData);
            } else {
                // Insert new config
                $configData['created_at'] = now();
                DB::table('master_weightage_config')->insert($configData);
            }

            // ========================================================================
            // Update is_weightage flag in master_post_config
            // ========================================================================
            DB::table('master_post_config')
                ->where('id', $post_id)
                ->update([
                    'is_weightage' => '1',
                    'updated_at' => now()
                ]);

            // Commit transaction
            DB::commit();

            return redirect()->route('static-posts.list')
                ->with('success', 'वेटेज मार्क्स सफलतापूर्वक सहेजे गए हैं।');
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'डेटा सहेजने में त्रुटि: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Get questions by post for AJAX request
     * 
     * READS FROM: master_weightage_config (consolidated JSON storage)
     * WRITES TO: Individual tables (master_weightage_marks, master_qualification_marks, etc.)
     *
     * @param int $post_id
     * @return \Illuminate\Http\JsonResponse*/
    public function getQuestionsByPost($post_id)
    {
        // Get post details from master_post_config
        $post = DB::table('master_post_config')
            ->select('master_post_config.*', 'master_post_config.id as post_id')
            ->where('id', $post_id)
            ->first();

        if (!$post) {
            return response()->json([
                'error' => 'पद नहीं मिला।',
            ], 404);
        }

        // ========================================================================
        // STEP 1: Check if weightage config exists in master_weightage_config
        // ========================================================================
        $weightageConfig = DB::table('master_weightage_config')
            ->where('fk_post_config_id', $post_id)
            ->where('is_active', '1')
            ->first();

        // Initialize variables to hold existing data
        $existingWeightage = collect();
        $existingQualificationMarks = collect();
        $experienceWeightage = null;
        $qualificationMultipliers = collect();
        $existingCasteMarks = collect();

        // Configuration status flags
        $hasWeightageConfig = false;
        $hasQualificationConfig = false;
        $hasExperienceConfig = false;
        $hasMultiplierConfig = false;
        $hasCasteConfig = false;

        // ========================================================================
        // STEP 2: If config exists, decode JSON data
        // ========================================================================
        if ($weightageConfig) {
            // Decode Question Weightage (JSON format)
            if (!empty($weightageConfig->question_id)) {
                $questionIds = json_decode($weightageConfig->question_id, true) ?? [];
                $optionValues = json_decode($weightageConfig->option_value, true) ?? [];
                $questionMarks = json_decode($weightageConfig->question_marks, true) ?? [];

                // Build existingWeightage collection matching old format
                $tempWeightage = [];
                foreach ($questionIds as $index => $qId) {
                    $optVal = $optionValues[$index] ?? null;
                    $marks = $questionMarks[$index] ?? 0;

                    $key = $qId . ($optVal ? '-' . $optVal : '');
                    $tempWeightage[$key] = (object) [
                        'question_id' => $qId,
                        'option_value' => $optVal,
                        'marks' => (int) $marks
                    ];
                }
                $existingWeightage = collect($tempWeightage);
                $hasWeightageConfig = !empty($tempWeightage);
            }

            // Decode Qualification Marks (JSON format)
            if (!empty($weightageConfig->qualification_id)) {
                $qualIds = json_decode($weightageConfig->qualification_id, true) ?? [];
                $qualMarks = json_decode($weightageConfig->qualification_marks, true) ?? [];

                $tempQualMarks = [];
                foreach ($qualIds as $index => $qualId) {
                    $marks = $qualMarks[$index] ?? 0;
                    $tempQualMarks[$qualId] = (object) [
                        'qualification_id' => $qualId,
                        'marks' => (int) $marks
                    ];
                }
                $existingQualificationMarks = collect($tempQualMarks);
                $hasQualificationConfig = !empty($tempQualMarks);
            }

            // Decode Experience Weightage (direct columns)
            if (
                $weightageConfig->minimum_experience_years !== null ||
                $weightageConfig->increment_value_per_year !== null ||
                $weightageConfig->maximum_experience_marks !== null
            ) {

                $experienceWeightage = (object) [
                    'minimum_experience_years' => $weightageConfig->minimum_experience_years,
                    'increment_value_per_year' => $weightageConfig->increment_value_per_year,
                    'maximum_experience_marks' => $weightageConfig->maximum_experience_marks
                ];
                $hasExperienceConfig = true;
            }

            // Decode Qualification Multipliers (JSON format)
            if (!empty($weightageConfig->multiplyer_qualification_id)) {
                $multQualIds = json_decode($weightageConfig->multiplyer_qualification_id, true) ?? [];
                $multValues = json_decode($weightageConfig->multiplier_value, true) ?? [];

                $tempMultipliers = [];
                foreach ($multQualIds as $index => $qualId) {
                    $multVal = $multValues[$index] ?? 0;
                    $tempMultipliers[$qualId] = (object) [
                        'qualification_id' => $qualId,
                        'multiplier_value' => (float) $multVal
                    ];
                }
                $qualificationMultipliers = collect($tempMultipliers);
                $hasMultiplierConfig = !empty($tempMultipliers);
            }

            // Decode Caste Marks (JSON format)
            if (!empty($weightageConfig->caste_id)) {
                $casteIds = json_decode($weightageConfig->caste_id, true) ?? [];
                $casteMarksArr = json_decode($weightageConfig->caste_marks, true) ?? [];

                $tempCasteMarks = [];
                foreach ($casteIds as $index => $casteId) {
                    $marks = $casteMarksArr[$index] ?? 0;
                    $tempCasteMarks[$casteId] = (object) [
                        'caste_id' => $casteId,
                        'marks' => (int) $marks
                    ];
                }
                $existingCasteMarks = collect($tempCasteMarks);
                $hasCasteConfig = !empty($tempCasteMarks);
            }
        }

        // ========================================================================
        // STEP 3: Set configuration status counts for UI display
        // ========================================================================
        $post->weightage_count = $hasWeightageConfig ? $existingWeightage->count() : 0;
        $post->qualification_marks_count = $hasQualificationConfig ? $existingQualificationMarks->count() : 0;
        $post->has_experience_weightage = $hasExperienceConfig ? 1 : 0;
        $post->qualification_multiplier_count = $hasMultiplierConfig ? $qualificationMultipliers->count() : 0;
        $post->caste_marks_count = $hasCasteConfig ? $existingCasteMarks->count() : 0;

        // Determine if the post is fully configured
        $post->is_fully_configured = (
            $hasWeightageConfig ||
            $hasQualificationConfig ||
            $hasExperienceConfig ||
            $hasMultiplierConfig ||
            $hasCasteConfig
        );

        // ========================================================================
        // STEP 4: Get questions specific to this post
        // ========================================================================
        $questions = collect();

        if ($post->fk_ques_id) {
            // Decode JSON question IDs
            $questionIds = json_decode($post->fk_ques_id, true);

            if (is_array($questionIds) && !empty($questionIds)) {
                // Fetch only those questions which are in the post's question list
                $questions = DB::table('master_post_questions')
                    ->whereIn('ques_ID', $questionIds)
                    ->where('is_active', '1')
                    ->where('is_weightage_marks', '1')
                    ->get();
            }
        }

        // Process questions to properly handle options
        foreach ($questions as $question) {
            $question->parsed_options = json_decode($question->answer_options);
        }

        // ========================================================================
        // STEP 5: Get all qualifications and castes for dropdown/selection
        // ========================================================================
        $qualifications = DB::table('master_qualification')
            ->orderBy('Quali_ID', 'asc')
            ->get();

        // Get minimum qualification for the selected post from master_post_config
        $minimumQualification = [];
        if ($post->quali_id) {
            $minQual = DB::table('master_qualification')
                ->where('Quali_ID', $post->quali_id)
                ->first();

            if ($minQual) {
                $minimumQualification = [
                    (object) [
                        'Quali_ID' => $minQual->Quali_ID,
                        'Quali_Name' => $minQual->Quali_Name
                    ]
                ];
            }
        }

        // Get all castes
        $castes = DB::table('master_tbl_caste')
            ->orderBy('caste_id', 'asc')
            ->get();

        // ========================================================================
        // STEP 6: Return consolidated response
        // ========================================================================
        return response()->json([
            'post' => $post,
            'questions' => $questions,
            'existingWeightage' => $existingWeightage,
            'existingQualificationMarks' => $existingQualificationMarks,
            'qualifications' => $qualifications,
            'minimumQualification' => $minimumQualification,
            'experienceWeightage' => $experienceWeightage,
            'qualificationMultipliers' => $qualificationMultipliers,
            'existingCasteMarks' => $existingCasteMarks,
            'castes' => $castes
        ]);
    }

    /**
     * Show the form for editing weightage marks.
     * 
     * READS FROM: master_weightage_config (consolidated JSON storage)
     *
     * @param  int  $post_id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($post_id)
    {
        // Get post details from master_post_config
        $post = DB::table('master_post_config')
            ->select('master_post_config.*', 'master_post_config.id as post_id')
            ->where('id', $post_id)
            ->where('is_active', '1')
            ->first();

        if (!$post) {
            return redirect()->route('static-posts.list')
                ->with('error', 'पद नहीं मिला।');
        }

        // ========================================================================
        // STEP 1: Check if weightage config exists in master_weightage_config
        // ========================================================================
        $weightageConfig = DB::table('master_weightage_config')
            ->where('fk_post_config_id', $post_id)
            ->where('is_active', '1')
            ->first();

        // Initialize variables to hold existing data
        $weightageMarks = collect();
        $qualificationMarks = collect();
        $experienceWeightage = null;
        $qualificationMultipliers = collect();
        $casteMarks = collect();

        // Configuration status flags
        $hasWeightageConfig = false;
        $hasQualificationConfig = false;
        $hasExperienceConfig = false;
        $hasMultiplierConfig = false;
        $hasCasteConfig = false;

        // ========================================================================
        // STEP 2: If config exists, decode JSON data
        // ========================================================================
        if ($weightageConfig) {
            // Decode Question Weightage (JSON format)
            if (!empty($weightageConfig->question_id)) {
                $questionIds = json_decode($weightageConfig->question_id, true) ?? [];
                $optionValues = json_decode($weightageConfig->option_value, true) ?? [];
                $questionMarksArr = json_decode($weightageConfig->question_marks, true) ?? [];

                // Build weightageMarks collection matching old format
                $tempWeightage = [];
                foreach ($questionIds as $index => $qId) {
                    $optVal = $optionValues[$index] ?? null;
                    $marks = $questionMarksArr[$index] ?? 0;

                    $key = $qId . ($optVal ? '-' . $optVal : '');
                    $tempWeightage[$key] = (object) [
                        'question_id' => $qId,
                        'option_value' => $optVal,
                        'marks' => (int) $marks
                    ];
                }
                $weightageMarks = collect($tempWeightage);
                $hasWeightageConfig = !empty($tempWeightage);
            }

            // Decode Qualification Marks (JSON format)
            if (!empty($weightageConfig->qualification_id)) {
                $qualIds = json_decode($weightageConfig->qualification_id, true) ?? [];
                $qualMarksArr = json_decode($weightageConfig->qualification_marks, true) ?? [];

                $tempQualMarks = [];
                foreach ($qualIds as $index => $qualId) {
                    $marks = $qualMarksArr[$index] ?? 0;
                    $tempQualMarks[$qualId] = (object) [
                        'qualification_id' => $qualId,
                        'marks' => (int) $marks
                    ];
                }
                $qualificationMarks = collect($tempQualMarks);
                $hasQualificationConfig = !empty($tempQualMarks);
            }

            // Decode Experience Weightage (direct columns)
            if (
                $weightageConfig->minimum_experience_years !== null ||
                $weightageConfig->increment_value_per_year !== null ||
                $weightageConfig->maximum_experience_marks !== null
            ) {

                $experienceWeightage = (object) [
                    'minimum_experience_years' => $weightageConfig->minimum_experience_years,
                    'increment_value_per_year' => $weightageConfig->increment_value_per_year,
                    'maximum_experience_marks' => $weightageConfig->maximum_experience_marks
                ];
                $hasExperienceConfig = true;
            }

            // Decode Qualification Multipliers (JSON format)
            if (!empty($weightageConfig->multiplyer_qualification_id)) {
                $multQualIds = json_decode($weightageConfig->multiplyer_qualification_id, true) ?? [];
                $multValues = json_decode($weightageConfig->multiplier_value, true) ?? [];

                $tempMultipliers = [];
                foreach ($multQualIds as $index => $qualId) {
                    $multVal = $multValues[$index] ?? 0;
                    $tempMultipliers[$qualId] = (object) [
                        'qualification_id' => $qualId,
                        'multiplier_value' => (float) $multVal
                    ];
                }
                $qualificationMultipliers = collect($tempMultipliers);
                $hasMultiplierConfig = !empty($tempMultipliers);
            }

            // Decode Caste Marks (JSON format)
            if (!empty($weightageConfig->caste_id)) {
                $casteIds = json_decode($weightageConfig->caste_id, true) ?? [];
                $casteMarksArr = json_decode($weightageConfig->caste_marks, true) ?? [];

                $tempCasteMarks = [];
                foreach ($casteIds as $index => $casteId) {
                    $marks = $casteMarksArr[$index] ?? 0;
                    $tempCasteMarks[$casteId] = (object) [
                        'caste_id' => $casteId,
                        'marks' => (int) $marks
                    ];
                }
                $casteMarks = collect($tempCasteMarks);
                $hasCasteConfig = !empty($tempCasteMarks);
            }
        }

        // ========================================================================
        // STEP 3: Set configuration status counts for UI display
        // ========================================================================
        $post->weightage_count = $hasWeightageConfig ? $weightageMarks->count() : 0;
        $post->qualification_marks_count = $hasQualificationConfig ? $qualificationMarks->count() : 0;
        $post->has_experience_weightage = $hasExperienceConfig ? 1 : 0;
        $post->qualification_multiplier_count = $hasMultiplierConfig ? $qualificationMultipliers->count() : 0;
        $post->caste_marks_count = $hasCasteConfig ? $casteMarks->count() : 0;

        // Determine if the post is fully configured
        $post->is_fully_configured = (
            $hasWeightageConfig ||
            $hasQualificationConfig ||
            $hasExperienceConfig ||
            $hasMultiplierConfig ||
            $hasCasteConfig
        );

        // ========================================================================
        // STEP 4: Get questions specific to this post
        // ========================================================================
        $questions = collect();

        if ($post->fk_ques_id) {
            // Decode JSON question IDs
            $questionIds = json_decode($post->fk_ques_id, true);

            if (is_array($questionIds) && !empty($questionIds)) {
                // Fetch only those questions which are in the post's question list
                $questions = DB::table('master_post_questions')
                    ->whereIn('ques_ID', $questionIds)
                    ->where('is_weightage_marks', '1')
                    ->get();
            }
        }

        // Process questions to properly handle options
        foreach ($questions as $question) {
            $question->parsed_options = json_decode($question->answer_options);
        }

        // ========================================================================
        // STEP 5: Get all qualifications and castes for dropdown/selection
        // ========================================================================
        // Get minimum qualification for the selected post from master_post_config
        $minimumQualification = [];
        if ($post->quali_id) {
            $minQual = DB::table('master_qualification')
                ->where('Quali_ID', $post->quali_id)
                ->first();

            if ($minQual) {
                $minimumQualification = [
                    (object) [
                        'Quali_ID' => $minQual->Quali_ID,
                        'Quali_Name' => $minQual->Quali_Name
                    ]
                ];
            }
        }

        // Get all qualifications from master_qualification table
        $qualifications = DB::table('master_qualification')
            ->orderBy('Quali_ID', 'asc')
            ->get();

        // Get all castes
        $castes = DB::table('master_tbl_caste')
            ->orderBy('caste_id', 'asc')
            ->get();

        return view('admin.weightage.edit', compact(
            'post',
            'questions',
            'weightageMarks',
            'qualificationMarks',
            'minimumQualification',
            'experienceWeightage',
            'qualificationMultipliers',
            'qualifications',
            'casteMarks',
            'castes'
        ));
    }

    /**
     * Update the specified weightage marks.
     * 
     * UPDATES: master_weightage_config (consolidated JSON storage ONLY)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $post_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $post_id)
    {
        // Validate request
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Begin transaction
            DB::beginTransaction();

            $ip_address = $request->ip();
            $user_id = (int) Session::get('sess_user_id', 0);

            // ========================================================================
            // Prepare JSON data for master_weightage_config
            // ========================================================================

            // Prepare Question Weightage JSON arrays
            $questionIds = [];
            $optionValues = [];
            $questionMarks = [];

            foreach ($request->weightage as $key => $marks) {
                if (!empty($marks) && $marks > 0) {
                    $parts = explode('-', $key);
                    $questionIds[] = $parts[0];
                    $optionValues[] = isset($parts[1]) ? $parts[1] : null;
                    $questionMarks[] = (int) $marks;
                }
            }

            // Prepare Qualification Marks JSON arrays
            $qualificationIds = [];
            $qualificationMarksArr = [];

            if ($request->qualification_marks) {
                foreach ($request->qualification_marks as $qualId => $marks) {
                    if (!empty($marks) && $marks > 0) {
                        $qualificationIds[] = $qualId;
                        $qualificationMarksArr[] = (int) $marks;
                    }
                }
            }

            // Prepare Caste Marks JSON arrays
            $casteIds = [];
            $casteMarksArr = [];

            if ($request->caste_marks) {
                foreach ($request->caste_marks as $casteId => $marks) {
                    if (!empty($marks) && $marks > 0) {
                        $casteIds[] = $casteId;
                        $casteMarksArr[] = (int) $marks;
                    }
                }
            }

            // Prepare Qualification Multiplier JSON arrays
            $multiplierQualIds = [];
            $multiplierValues = [];

            if ($request->qualification_multiplier) {
                foreach ($request->qualification_multiplier as $qualId => $multValue) {
                    if (!empty($multValue) && $multValue > 0) {
                        $multiplierQualIds[] = $qualId;
                        $multiplierValues[] = (float) $multValue;
                    }
                }
            }

            // ========================================================================
            // Update master_weightage_config (single consolidated row)
            // ========================================================================
            $configData = [
                'fk_post_id' => null, // Static posts don't have dynamic post_id
                'fk_post_config_id' => (int) $post_id,
                'question_id' => !empty($questionIds) ? json_encode($questionIds) : null,
                'option_value' => !empty($optionValues) ? json_encode($optionValues) : null,
                'question_marks' => !empty($questionMarks) ? json_encode($questionMarks) : null,
                'qualification_id' => !empty($qualificationIds) ? json_encode($qualificationIds) : null,
                'qualification_marks' => !empty($qualificationMarksArr) ? json_encode($qualificationMarksArr) : null,
                'caste_id' => !empty($casteIds) ? json_encode($casteIds) : null,
                'caste_marks' => !empty($casteMarksArr) ? json_encode($casteMarksArr) : null,
                'multiplyer_qualification_id' => !empty($multiplierQualIds) ? json_encode($multiplierQualIds) : null,
                'multiplier_value' => !empty($multiplierValues) ? json_encode($multiplierValues) : null,
                'minimum_experience_years' => $request->minimum_experience_years ?? null,
                'increment_value_per_year' => $request->increment_value_per_year ?? null,
                'maximum_experience_marks' => $request->maximum_experience_marks ?? null,
                'is_active' => '1',
                'ip_address' => $ip_address,
                'updated_by' => $user_id,
                'updated_at' => now()
            ];

            // Always update (should exist from store())
            DB::table('master_weightage_config')
                ->where('fk_post_config_id', (int) $post_id)
                ->update($configData);

            // Commit transaction
            DB::commit();

            return redirect()->route('static-posts.list')
                ->with('success', 'वेटेज मार्क्स सफलतापूर्वक अपडेट किए गए हैं।');
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Clear all weightage data for a post.
     *
     * @param  int  $post_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearWeightageData($post_id)
    {
        try {
            // Begin transaction
            DB::beginTransaction();

            // Get current user ID
            $user_id = Session::get('sess_user_id', 0);
            $ip_address = request()->ip();

            // Delete all weightage marks
            DB::table('master_weightage_marks')
                ->where('post_id', $post_id)
                ->delete();

            // Delete all qualification marks
            DB::table('master_qualification_marks')
                ->where('post_id', $post_id)
                ->delete();

            // Delete experience weightage configuration
            DB::table('master_experience_weightage')
                ->where('post_id', $post_id)
                ->delete();

            // Delete qualification multipliers
            DB::table('master_qualification_multiplier')
                ->where('post_id', $post_id)
                ->delete();

            // Delete caste marks
            DB::table('master_caste_marks')
                ->where('post_id', $post_id)
                ->delete();

            // Log the action
            DB::table('activity_log')->insert([
                'log_name' => 'weightage',
                'description' => 'Cleared all weightage data for post ID: ' . $post_id,
                'subject_type' => 'post',
                'subject_id' => $post_id,
                'causer_type' => 'user',
                'causer_id' => $user_id,
                'properties' => json_encode(['post_id' => $post_id, 'cleared_by' => $user_id, 'ip_address' => $ip_address]),
                'created_at' => now()
            ]);

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'सभी वेटेज डेटा सफलतापूर्वक हटा दिए गए हैं।'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'डेटा हटाने में त्रुटि: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate experience marks based on the rules defined for a post
     * 
     * @param int $post_id The post ID
     * @param float $candidateExperienceYears The candidate's total experience in years
     * @return float The calculated marks for experience
     */
    public function calculateExperienceMarks($post_id, $candidateExperienceYears)
    {
        // Get experience weightage configuration for this post
        $experienceConfig = DB::table('master_experience_weightage')
            ->where('post_id', $post_id)
            ->first();

        if (!$experienceConfig) {
            return 0; // No configuration, no marks
        }

        // If candidate doesn't meet minimum experience, no marks
        if ($candidateExperienceYears < $experienceConfig->minimum_experience_years) {
            return 0;
        }

        // Calculate additional years beyond minimum
        $additionalYears = $candidateExperienceYears - $experienceConfig->minimum_experience_years;

        // Calculate marks: (additional years * increment value)
        $marks = $additionalYears * $experienceConfig->increment_value_per_year;

        // Cap at maximum marks if needed
        if ($marks > $experienceConfig->maximum_experience_marks) {
            $marks = $experienceConfig->maximum_experience_marks;
        }

        return $marks;
    }

    /**
     * Calculate qualification marks based on percentage and multiplier
     * 
     * @param int $post_id The post ID
     * @param float $percentage The candidate's qualification percentage
     * @return float The calculated marks for qualification
     */
    public function calculateQualificationMarks($post_id, $percentage)
    {
        // Get the minimum qualification ID for this post from master_post_config
        $post = DB::table('master_post_config')
            ->select('quali_id')
            ->where('id', $post_id)
            ->first();

        if (!$post || !$post->quali_id) {
            return 0; // No minimum qualification found
        }

        $qualification_id = $post->quali_id;

        // Get qualification multiplier for this post and qualification
        $multiplier = DB::table('master_qualification_multiplier')
            ->where('post_id', $post_id)
            ->where('qualification_id', $qualification_id)
            ->first();

        if (!$multiplier) {
            // If no multiplier found, use the standard qualification marks (if any)
            $standardMarks = DB::table('master_qualification_marks')
                ->where('post_id', $post_id)
                ->where('qualification_id', $qualification_id)
                ->first();

            return $standardMarks ? $standardMarks->marks : 0;
        }

        // Calculate marks by multiplying percentage with multiplier value
        return $percentage * $multiplier->multiplier_value;
    }
}
