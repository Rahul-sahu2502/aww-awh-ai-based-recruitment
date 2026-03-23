@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        .question-card {
            margin-bottom: 1.5rem;
            border-left: 3px solid #4154f1;
        }

        .option-input {
            margin-bottom: 0.75rem;
        }

        .option-input label {
            margin-bottom: 0.25rem;
        }

        .validation-error {
            color: red;
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">वेटेज मार्क्स प्रबंधन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.static-weightage.index') }}">वेटेज मार्क्स की
                            सूची</a></li>
                    <li class="breadcrumb-item active">वेटेज मार्क्स संपादित करें</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-pencil-square"></i> वेटेज मार्क्स संपादित करें - {{ $post->post_name }}</h5>
                    </div>
                    <div class="card-header bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>वेटेज कॉन्फ़िगरेशन स्थिति</h6>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    @php
                                        $hasWeightage = isset($post->weightage_count) && $post->weightage_count > 0;
                                        $hasQualification =
                                            (isset($post->qualification_marks_count) &&
                                                $post->qualification_marks_count > 0) ||
                                            (isset($post->qualification_multiplier_count) &&
                                                $post->qualification_multiplier_count > 0);
                                        $hasExperience =
                                            isset($post->has_experience_weightage) &&
                                            $post->has_experience_weightage > 0;
                                        $hasCaste = isset($post->caste_marks_count) && $post->caste_marks_count > 0;
                                        $isConfigured =
                                            $hasWeightage || $hasQualification || $hasExperience || $hasCaste;
                                    @endphp

                                    @if ($isConfigured)
                                        <span class="badge bg-success">कॉन्फ़िगर किया गया है</span>
                                    @else
                                        <span class="badge bg-warning">कॉन्फ़िगर नहीं किया गया है</span>
                                    @endif

                                    <small class="d-flex flex-column mt-2">
                                        <span class="{{ $hasWeightage ? 'text-success' : 'text-muted' }}">
                                            <i class="bi bi-{{ $hasWeightage ? 'check' : 'dash' }}-circle"></i>
                                            प्रश्न वेटेज
                                        </span>

                                        <span class="{{ $hasQualification ? 'text-success' : 'text-muted' }}">
                                            <i class="bi bi-{{ $hasQualification ? 'check' : 'dash' }}-circle"></i>
                                            योग्यता वेटेज
                                        </span>

                                        <span class="{{ $hasExperience ? 'text-success' : 'text-muted' }}">
                                            <i class="bi bi-{{ $hasExperience ? 'check' : 'dash' }}-circle"></i>
                                            अनुभव वेटेज
                                        </span>

                                        <span class="{{ $hasCaste ? 'text-success' : 'text-muted' }}">
                                            <i class="bi bi-{{ $hasCaste ? 'check' : 'dash' }}-circle"></i>
                                            जाति अनुसार अंक
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">


                        <form action="{{ route('admin.static-weightage.update', $post->id) }}" method="post"
                            id="weightageForm">
                            @csrf

                            <div id="questionsContainer">
                                @if (count($questions) === 0)
                                    <div class="alert alert-warning">इस पद के लिए कोई वेटेज मार्क्स प्रश्न नहीं मिला।</div>
                                @else
                                    <div class="alert alert-success">नीचे दिए गए प्रश्नों के लिए वेटेज मार्क्स संपादित करें।
                                    </div>

                                    @foreach ($questions as $question)
                                        @php
                                            // Get options count for this question
                                            $optionsCount = $question->parsed_options
                                                ? count($question->parsed_options)
                                                : 0;
                                        @endphp
                                        <div class="card question-card" data-question-type="{{ $question->ans_type }}"
                                            data-options-count="{{ $optionsCount }}">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $question->ques_name }}</h5>

                                                @if ($question->ans_type === 'M' || ($question->ans_type === 'O' && $optionsCount > 2))
                                                    <p class="text-muted">नोट: इस प्रश्न के प्रत्येक विकल्प के लिए अलग-अलग
                                                        वेटेज मार्क्स सेट करें।</p>

                                                    @foreach (json_decode($question->answer_options) as $option)
                                                        @php
                                                            $optionKey = $question->ques_ID . '-' . $option;
                                                            $existingValue = isset($weightageMarks[$optionKey])
                                                                ? $weightageMarks[$optionKey]->marks
                                                                : '';
                                                        @endphp

                                                        <div class="option-input">
                                                            <label
                                                                for="weightage_{{ $optionKey }}">{{ $option }}</label>
                                                            <input type="number" class="form-control"
                                                                id="weightage_{{ $optionKey }}"
                                                                name="weightage[{{ $optionKey }}]"
                                                                value="{{ $existingValue }}" step="1" min="0"
                                                                oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                                        </div>
                                                    @endforeach
                                                @elseif ($question->ans_type === 'O')
                                                    <p class="text-muted">प्रश्न के प्रत्येक विकल्प के लिए अंक निर्धारित
                                                        करें:</p>

                                                    @foreach (json_decode($question->answer_options) as $option)
                                                        @php
                                                            $optionKey = $question->ques_ID . '-' . $option;
                                                            $existingValue = isset($weightageMarks[$optionKey])
                                                                ? $weightageMarks[$optionKey]->marks
                                                                : '';
                                                        @endphp

                                                        <div class="option-input row">
                                                            <div class="col-md-6">
                                                                <label
                                                                    for="weightage_{{ $optionKey }}">{{ $option }}</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control"
                                                                    id="weightage_{{ $optionKey }}"
                                                                    name="weightage[{{ $optionKey }}]"
                                                                    value="{{ $existingValue }}" step="1"
                                                                    min="0"
                                                                    oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @elseif ($question->ans_type === 'F')
                                                    <p class="text-muted">फाइल अपलोड प्रश्न के लिए मार्क्स निर्धारित करें:
                                                    </p>

                                                    @foreach (json_decode($question->answer_options) as $option)
                                                        @php
                                                            $optionKey = $question->ques_ID . '-' . $option;
                                                            $existingValue = isset($weightageMarks[$optionKey])
                                                                ? $weightageMarks[$optionKey]->marks
                                                                : '';
                                                        @endphp

                                                        <div class="option-input row">
                                                            <div class="col-md-6">
                                                                <label
                                                                    for="weightage_{{ $optionKey }}">{{ $option }}</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control"
                                                                    id="weightage_{{ $optionKey }}"
                                                                    name="weightage[{{ $optionKey }}]"
                                                                    value="{{ $existingValue }}" step="1"
                                                                    min="0"
                                                                    oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @php
                                                        $existingValue = isset($weightageMarks[$question->ques_ID])
                                                            ? $weightageMarks[$question->ques_ID]->marks
                                                            : '';
                                                    @endphp

                                                    <div class="option-input">
                                                        <label for="weightage_{{ $question->ques_ID }}">वेटेज
                                                            मार्क्स</label>
                                                        <input type="number" class="form-control"
                                                            id="weightage_{{ $question->ques_ID }}"
                                                            name="weightage[{{ $question->ques_ID }}]"
                                                            value="{{ $existingValue }}" step="1" min="0"
                                                            oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="bi bi-mortarboard"></i> योग्यता अनुसार अंक</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">विभिन्न शैक्षिक योग्यताओं के लिए अंक निर्धारित करें:</p>
                                    <div class="row">
                                        @foreach ($qualifications as $qualification)
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-2">
                                                    <label for="qualification_marks_{{ $qualification->Quali_ID }}"
                                                        class="form-label">{{ $qualification->Quali_Name }}
                                                    </label>
                                                    <input type="number" class="form-control"
                                                        id="qualification_marks_{{ $qualification->Quali_ID }}"
                                                        name="qualification_marks[{{ $qualification->Quali_ID }}]"
                                                        step="1" min="0"
                                                        oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;"
                                                        value="{{ isset($qualificationMarks[$qualification->Quali_ID]) ? $qualificationMarks[$qualification->Quali_ID]->marks : '' }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Experience Weightage Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="bi bi-briefcase"></i> अनुभव के अनुसार वेटेज</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">अनुभव आधारित वेटेज के लिए पैरामीटर निर्धारित करें:</p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="minimum_experience_years" class="form-label">न्यूनतम अनुभव
                                                    (वर्षों में)</label>
                                                <input type="number" class="form-control" id="minimum_experience_years"
                                                    name="minimum_experience_years" step="0.5" min="0"
                                                    value="{{ isset($experienceWeightage->minimum_experience_years) ? $experienceWeightage->minimum_experience_years : '0' }}">
                                                <small class="text-muted">न्यूनतम अनुभव की आवश्यकता सेट करें</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="increment_value_per_year" class="form-label">प्रति अतिरिक्त
                                                    वर्ष अंक</label>
                                                <input type="number" class="form-control" id="increment_value_per_year"
                                                    name="increment_value_per_year" step="0.5" min="0"
                                                    value="{{ isset($experienceWeightage->increment_value_per_year) ? $experienceWeightage->increment_value_per_year : '0' }}">
                                                <small class="text-muted">हर अतिरिक्त वर्ष के लिए कितने अंक जोड़े
                                                    जाएं</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="maximum_experience_marks" class="form-label">अधिकतम अनुभव
                                                    अंक</label>
                                                <input type="number" class="form-control" id="maximum_experience_marks"
                                                    name="maximum_experience_marks" step="0.5" min="0"
                                                    value="{{ isset($experienceWeightage->maximum_experience_marks) ? $experienceWeightage->maximum_experience_marks : '0' }}">
                                                <small class="text-muted">अधिकतम अंक जो अनुभव के लिए दिए जा सकते
                                                    हैं</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Qualification Multiplier Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="bi bi-calculator"></i> योग्यता मल्टीप्लायर</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">योग्यता प्रतिशत के लिए मल्टीप्लायर निर्धारित करें:</p>
                                    <div class="row">
                                        @if (count($minimumQualification) > 0)
                                            @foreach ($minimumQualification as $qualification)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="mb-2">
                                                        <label
                                                            for="qualification_multiplier_{{ $qualification->Quali_ID }}"
                                                            class="form-label">{{ $qualification->Quali_Name }}
                                                            मल्टीप्लायर (न्यूनतम योग्यता)</label>
                                                        <input type="number" class="form-control"
                                                            id="qualification_multiplier_{{ $qualification->Quali_ID }}"
                                                            name="qualification_multiplier[{{ $qualification->Quali_ID }}]"
                                                            step="0.01" min="0"
                                                            value="{{ isset($qualificationMultipliers[$qualification->Quali_ID]) ? $qualificationMultipliers[$qualification->Quali_ID]->multiplier_value : '' }}">
                                                        <small class="text-muted">योग्यता प्रतिशत को इस मान से गुणा किया
                                                            जाएगा</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-warning">इस पद के लिए न्यूनतम योग्यता नहीं मिली
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Caste-wise Marks Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="bi bi-people"></i> जाति अनुसार अंक</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">प्रत्येक श्रेणी/जाति के लिए अधिकतम अंक निर्धारित करें:</p>
                                    <div class="row">
                                        @if (isset($castes) && count($castes) > 0)
                                            @foreach ($castes as $caste)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="mb-2">
                                                        <label for="caste_marks_{{ $caste->caste_id }}"
                                                            class="form-label">{{ $caste->caste_title }}</label>
                                                        <input type="number" class="form-control"
                                                            id="caste_marks_{{ $caste->caste_id }}"
                                                            name="caste_marks[{{ $caste->caste_id }}]" step="1"
                                                            min="0"
                                                            value="{{ isset($casteMarks[$caste->caste_id]) ? (int) $casteMarks[$caste->caste_id]->marks : '' }}"
                                                            oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-info">कोई जाति डेटा उपलब्ध नहीं है।</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn"
                                    {{ count($questions) === 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-save"></i> वेटेज मार्क्स अपडेट करें
                                </button>
                                {{-- <button type="button" class="btn btn-danger" id="clearFormBtn">
                                    <i class="bi bi-trash"></i> सभी वेटेज डेटा हटाएं
                                </button> --}}
                                <a href="{{ route('admin.weightage.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> रद्द करें
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Form validation and AJAX submission
            $('#weightageForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                let hasValues = false;

                // Check if at least one input has a value
                $('input[name^="weightage"]').each(function() {
                    if ($(this).val() !== '') {
                        hasValues = true;
                        return false; // Break the loop
                    }
                });

                if (!hasValues) {
                    Swal.fire({
                        title: "चेतावनी!",
                        text: "कृपया कम से कम एक प्रश्न के लिए वेटेज मार्क्स दर्ज करें।",
                        icon: "warning",
                        confirmButtonText: "ठीक है",
                        confirmButtonColor: "#ffc107"
                    });
                    return false;
                }

                // Disable submit button to prevent double submission
                const $submitBtn = $('#submitBtn');
                const originalBtnText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                    '<i class="bi bi-hourglass-split"></i> सहेजा जा रहा है...');

                // Get form data
                const formData = $(this).serialize();
                const formAction = $(this).attr('action');

                // Show loading
                Swal.fire({
                    title: 'कृपया प्रतीक्षा करें...',
                    text: 'वेटेज मार्क्स अपडेट किए जा रहे हैं',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form via AJAX
                $.ajax({
                    url: formAction,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            title: "सफलता!",
                            text: "वेटेज मार्क्स सफलतापूर्वक अपडेट किए गए हैं।",
                            icon: "success",
                            confirmButtonText: "ठीक है",
                            confirmButtonColor: "#28a745"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to index page
                                window.location.href =
                                    "{{ route('admin.static-weightage.index') }}";
                            }
                        });
                    },
                    error: function(xhr) {
                        // Re-enable submit button
                        $submitBtn.prop('disabled', false).html(originalBtnText);

                        let errorMessage = 'वेटेज मार्क्स अपडेट करने में त्रुटि हुई।';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                console.error('Parse error:', e);
                            }
                        }

                        Swal.fire({
                            title: "त्रुटि!",
                            text: errorMessage,
                            icon: "error",
                            confirmButtonText: "ठीक है",
                            confirmButtonColor: "#dc3545"
                        });
                    }
                });

                return false;
            });
        });
    </script>

    <script>
        // Function to handle option selection for non-M type questions
        function setupQuestionOptions() {
            // For each question card
            $('.question-card').each(function() {
                const questionType = $(this).data('question-type');
                const optionsCount = $(this).data('options-count') || 0;

                // Skip M-type questions or O-type with more than 2 options - all options should be enabled
                if (questionType === 'M' || (questionType === 'O' && optionsCount > 2)) {
                    return;
                }

                // For other questions, only allow one option to be selected
                const inputs = $(this).find('input[type="number"]');

                // Remove any previous event handlers to prevent duplicates
                inputs.off('input');

                // When any input changes
                inputs.on('input', function() {
                    // Convert to integer
                    this.value = this.value ? Math.max(0, parseInt(this.value)) : '';

                    const currentValue = $(this).val() === '' ? 0 : parseInt($(this).val());

                    // If value is greater than 0, disable all other inputs
                    if (currentValue > 0) {
                        inputs.not(this).prop('disabled', true).val('');
                    } else {
                        // If value is 0, enable all inputs
                        inputs.prop('disabled', false);
                    }
                });

                // Initial setup - if any input has a value, disable others
                let hasValue = false;
                inputs.each(function() {
                    const value = $(this).val() === '' ? 0 : parseInt($(this).val());
                    if (value > 0) {
                        // Convert to integer if it's not
                        $(this).val(parseInt(value));
                        inputs.not(this).prop('disabled', true).val('');
                        hasValue = true;
                        return false; // Break the loop after finding the first non-zero value
                    }
                });

                // Make sure all values are stored as integers
                // If no value set, don't force zeros; keep empty for clarity
            });
        }

        // Call this function when the document is ready
        $(document).ready(function() {
            // Display SweetAlert for error message
            @if (session('error'))
                Swal.fire({
                    title: "त्रुटि!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    confirmButtonText: "ठीक है",
                    confirmButtonColor: "#dc3545"
                });
            @endif

            setupQuestionOptions();
        });
    </script>
@endsection
