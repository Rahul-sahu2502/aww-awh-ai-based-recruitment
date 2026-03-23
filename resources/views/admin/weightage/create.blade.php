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
                    <li class="breadcrumb-item"><a href="{{ route('static-posts.list') }}">वेटेज मार्क्स की सूची</a>
                    </li>
                    <li class="breadcrumb-item active">वेटेज मार्क्स जोड़ें</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-plus-circle"></i> वेटेज मार्क्स जोड़ें</h5>
                    </div>
                    <div class="card-header bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>नई वेटेज कॉन्फ़िगरेशन</h6>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <span class="badge bg-info">नया कॉन्फ़िगरेशन बनाएं</span>
                                    <small class="d-flex flex-column mt-2">
                                        <span class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            पद चुनकर प्रश्न वेटेज, योग्यता वेटेज, और अनुभव वेटेज कॉन्फ़िगर करें
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">


                        <form action="{{ route('admin.weightage.store') }}" method="post" id="weightageForm">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="post_id" class="form-label">पद चुनें <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('post_id') is-invalid @enderror" name="post_id"
                                            id="post_id" required>
                                            <option value="">-- पद चुनें --</option>
                                            @foreach ($posts as $post)
                                                @if (is_object($post) && isset($post->post_id) && isset($post->title))
                                                    <option value="{{ $post->post_id }}"
                                                        {{ request()->get('post_id') == $post->post_id || old('post_id') == $post->post_id ? 'selected' : '' }}>
                                                        {{ $post->title }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('post_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="questionsContainer">
                                <div class="alert alert-info">
                                    कृपया पहले पद चुनें। वेटेज मार्क्स वाले प्रश्न यहां दिखाए जाएंगे।
                                </div>
                            </div>

                            <div id="qualificationsContainer" style="display:none;">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5><i class="bi bi-mortarboard"></i> योग्यता अनुसार अंक</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">विभिन्न शैक्षिक योग्यताओं के लिए अंक निर्धारित करें:</p>
                                        <div class="row" id="qualificationsInputs">
                                            {{-- {{ dd($qualifications) }} --}}
                                            @foreach ($qualifications as $qualification)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="mb-2">
                                                        <label for="qualification_marks_{{ $qualification->Quali_ID }}"
                                                            class="form-label">{{ $qualification->Quali_Name }}</label>
                                                        <input type="number" class="form-control"
                                                            id="qualification_marks_{{ $qualification->Quali_ID }}"
                                                            name="qualification_marks[{{ $qualification->Quali_ID }}]"
                                                            step="1" min="0"
                                                            oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Experience Weightage Section -->
                            <div id="experienceWeightageContainer" style="display:none;">
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
                                                        name="minimum_experience_years" step="0.5" min="0">
                                                    <small class="text-muted">न्यूनतम अनुभव की आवश्यकता सेट करें</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="increment_value_per_year" class="form-label">प्रति अतिरिक्त
                                                        वर्ष अंक</label>
                                                    <input type="number" class="form-control" id="increment_value_per_year"
                                                        name="increment_value_per_year" step="0.5" min="0">
                                                    <small class="text-muted">हर अतिरिक्त वर्ष के लिए कितने अंक जोड़े
                                                        जाएं</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="maximum_experience_marks" class="form-label">अधिकतम अनुभव
                                                        अंक</label>
                                                    <input type="number" class="form-control" id="maximum_experience_marks"
                                                        name="maximum_experience_marks" step="0.5" min="0">
                                                    <small class="text-muted">अधिकतम अंक जो अनुभव के लिए दिए जा सकते
                                                        हैं</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Qualification Multiplier Section -->
                            <div id="qualificationMultiplierContainer" style="display:none;">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5><i class="bi bi-calculator"></i> योग्यता मल्टीप्लायर</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">योग्यता प्रतिशत के लिए मल्टीप्लायर निर्धारित करें:</p>
                                        <div class="row" id="qualificationMultiplierInputs">
                                            <!-- Will be populated dynamically based on the selected post's minimum qualification -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Caste-wise Marks Section -->
                            <div id="casteMarksContainer" style="display:none;">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5><i class="bi bi-people"></i> जाति अनुसार अंक</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">प्रत्येक श्रेणी/जाति के लिए अधिकतम अंक निर्धारित करें:</p>
                                        <div class="row" id="casteMarksInputs">
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
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                    <i class="bi bi-save"></i> वेटेज मार्क्स सहेजें
                                </button>
                                <a href="{{ route('static-posts.list') }}" class="btn btn-secondary">
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
            // When post is selected
            $('#post_id').change(function() {
                const postId = $(this).val();
                if (postId) {
                    fetchQuestions(postId);
                } else {
                    $('#questionsContainer').html(
                        '<div class="alert alert-info">कृपया पहले पद चुनें। वेटेज मार्क्स वाले प्रश्न यहां दिखाए जाएंगे।</div>'
                    );
                    $('#qualificationsContainer').hide();
                    $('#submitBtn').prop('disabled', true);
                }
            });

            // If post_id is already selected on page load (from query param)
            if ($('#post_id').val()) {
                fetchQuestions($('#post_id').val());
            }

            function fetchQuestions(postId) {
                $('#questionsContainer').html('<div class="alert alert-info">प्रश्न लोड हो रहे हैं...</div>');

                $.ajax({
                    url: "{{ route('admin.weightage.questions', '') }}/" + postId,
                    type: 'GET',
                    success: function(response) {
                        // Update the status section with the selected post's configuration status
                        if (response.post) {
                            const post = response.post;
                            const hasWeightage = post.weightage_count > 0;
                            const hasQualification = post.qualification_marks_count > 0 || post
                                .qualification_multiplier_count > 0;
                            const hasExperience = post.has_experience_weightage > 0;
                            const hasCaste = (post.caste_marks_count || 0) > 0;
                            const isConfigured = hasWeightage || hasQualification || hasExperience ||
                                hasCaste;

                            let statusHtml = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>${post.title} - वेटेज कॉन्फ़िगरेशन स्थिति</h6>
                                    <div class="d-flex flex-column gap-1 mt-2">
                                        <span class="badge bg-${isConfigured ? 'success' : 'warning'}">
                                            ${isConfigured ? 'कॉन्फ़िगर किया गया है' : 'कॉन्फ़िगर नहीं किया गया है'}
                                        </span>
                                        
                                        <small class="d-flex flex-column mt-2">
                                            <span class="${hasWeightage ? 'text-success' : 'text-muted'}">
                                                <i class="bi bi-${hasWeightage ? 'check' : 'dash'}-circle"></i> 
                                                प्रश्न वेटेज
                                            </span>
                                            
                                            <span class="${hasQualification ? 'text-success' : 'text-muted'}">
                                                <i class="bi bi-${hasQualification ? 'check' : 'dash'}-circle"></i> 
                                                योग्यता वेटेज
                                            </span>
                                            
                                            <span class="${hasExperience ? 'text-success' : 'text-muted'}">
                                                <i class="bi bi-${hasExperience ? 'check' : 'dash'}-circle"></i> 
                                                अनुभव वेटेज
                                            </span>
                                            
                                            <span class="${hasCaste ? 'text-success' : 'text-muted'}">
                                                <i class="bi bi-${hasCaste ? 'check' : 'dash'}-circle"></i> 
                                                जाति अनुसार अंक
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>`;

                            $('.card-header.bg-light').html(statusHtml);
                        }

                        displayQuestions(response);

                        // Show the experience and qualification multiplier containers
                        $('#experienceWeightageContainer').show();
                        $('#qualificationMultiplierContainer').show();
                        $('#casteMarksContainer').show();

                        // Fill in existing experience weightage data if available
                        if (response.experienceWeightage) {
                            $('#minimum_experience_years').val(response.experienceWeightage
                                .minimum_experience_years);
                            $('#increment_value_per_year').val(response.experienceWeightage
                                .increment_value_per_year);
                            $('#maximum_experience_marks').val(response.experienceWeightage
                                .maximum_experience_marks);
                        }

                        // Add the minimum qualification multiplier field
                        if (response.minimumQualification && response.minimumQualification.length > 0) {
                            const qualification = response.minimumQualification[
                                0]; // Get the first (and only) qualification
                            let html = `
                                <div class="col-md-3 col-sm-6">
                                    <div class="mb-2">
                                        <label for="qualification_multiplier_${qualification.Quali_ID}" class="form-label">
                                            ${qualification.Quali_Name} मल्टीप्लायर (न्यूनतम योग्यता)
                                        </label>
                                        <input type="number" class="form-control"
                                            id="qualification_multiplier_${qualification.Quali_ID}"
                                            name="qualification_multiplier[${qualification.Quali_ID}]"
                                            step="0.01" min="0">
                                        <small class="text-muted">योग्यता प्रतिशत को इस मान से गुणा किया जाएगा</small>
                                    </div>
                                </div>`;
                            $('#qualificationMultiplierInputs').html(html);

                            // Fill in existing qualification multiplier data if available
                            if (response.qualificationMultipliers && response.qualificationMultipliers
                                .length > 0) {
                                response.qualificationMultipliers.forEach(function(multiplier) {
                                    $('#qualification_multiplier_' + multiplier
                                            .qualification_id)
                                        .val(multiplier.multiplier_value);
                                });
                            }
                        } else {
                            $('#qualificationMultiplierInputs').html(
                                '<div class="col-12"><div class="alert alert-warning">इस पद के लिए न्यूनतम योग्यता नहीं मिली</div></div>'
                            );
                        }

                        // Populate caste-wise marks
                        if (response.castes && response.castes.length > 0) {
                            let casteHtml = '';
                            response.castes.forEach(function(caste) {
                                const existing = response.existingCasteMarks && response
                                    .existingCasteMarks[caste.caste_id] ? parseInt(response
                                        .existingCasteMarks[caste.caste_id].marks) : '';
                                casteHtml += `
                                <div class="col-md-3 col-sm-6">
                                    <div class="mb-2">
                                        <label for="caste_marks_${caste.caste_id}" class="form-label">${caste.caste_title}</label>
                                        <input type="number" class="form-control" id="caste_marks_${caste.caste_id}" name="caste_marks[${caste.caste_id}]" step="1" min="0" ${existing !== '' ? `value="${existing}"` : ''} oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                    </div>
                                </div>`;
                            });
                            $('#casteMarksInputs').html(casteHtml);
                        }
                    },
                    error: function(xhr) {
                        $('#questionsContainer').html(
                            '<div class="alert alert-danger">प्रश्न लोड करने में त्रुटि हुई। कृपया पुनः प्रयास करें।</div>'
                        );
                        console.error(xhr);
                    }
                });
            }

            function displayQuestions(data) {
                const questions = data.questions;
                const existingWeightage = data.existingWeightage;
                const existingQualificationMarks = data.existingQualificationMarks;
                const qualifications = data.qualifications;
                const minimumQualification = data.minimumQualification;

                if (questions.length === 0) {
                    $('#questionsContainer').html(
                        '<div class="alert alert-warning">इस पद के लिए कोई वेटेज मार्क्स प्रश्न नहीं मिला।</div>'
                    );
                    $('#submitBtn').prop('disabled', true);
                    return;
                }

                let html =
                    '<div class="alert alert-success">नीचे दिए गए प्रश्नों के लिए वेटेज मार्क्स सेट करें।</div>';

                questions.forEach(question => {
                    const questionId = question.ques_ID;
                    // Get options count for this question
                    const optionsCount = question.parsed_options ? question.parsed_options.length : 0;

                    html += `
                    <div class="card question-card" data-question-type="${question.ans_type}" data-options-count="${optionsCount}">
                        <div class="card-body">
                            <h5 class="card-title">${question.ques_name}</h5>`;

                    // Different handling based on question type
                    if (question.ans_type === 'M') {
                        // For multi-select questions
                        html +=
                            `<p class="text-muted">नोट: इस प्रश्न के प्रत्येक विकल्प के लिए अलग-अलग वेटेज मार्क्स सेट करें।</p><div class="row g-2">`;

                        if (question.parsed_options && question.parsed_options.length > 0) {
                            question.parsed_options.forEach(option => {
                                const optionKey = `${questionId}-${option}`;
                                const existingValue = existingWeightage && existingWeightage[
                                    optionKey] ? existingWeightage[optionKey].marks : '';

                                html += `
                                <div class="col-md-3 col-sm-6">
                                    <div class="option-input mb-2">
                                        <label for="weightage_${optionKey}">${option}</label>
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="weightage_${optionKey}" 
                                            name="weightage[${optionKey}]" 
                                            value="${existingValue}"
                                            step="1" 
                                            min="0"
                                            oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;"
                                        >
                                    </div>
                                </div>`;
                            });
                            html += `</div>`;
                        }
                    } else if (question.ans_type === 'O') {
                        // For single select/option questions
                        html +=
                            `<p class="text-muted">प्रश्न के प्रत्येक विकल्प के लिए अंक निर्धारित करें:</p><div class="row g-2">`;

                        if (question.parsed_options && question.parsed_options.length > 0) {
                            question.parsed_options.forEach(option => {
                                const optionKey = `${questionId}-${option}`;
                                const existingValue = existingWeightage && existingWeightage[
                                    optionKey] ? existingWeightage[optionKey].marks : '';

                                html += `
                                <div class="col-md-3 col-sm-6">
                                    <div class="option-input mb-2">
                                        <label for="weightage_${optionKey}">${option}</label>
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="weightage_${optionKey}" 
                                            name="weightage[${optionKey}]" 
                                            value="${existingValue}"
                                            step="1" 
                                            min="0"
                                            oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;"
                                        >
                                    </div>
                                </div>`;
                            });
                            html += `</div>`;
                        }
                    } else if (question.ans_type === 'F') {
                        // For file upload questions - usually just "हाँ" "नहीं" options
                        html +=
                            `<p class="text-muted">फाइल अपलोड प्रश्न के लिए मार्क्स निर्धारित करें:</p><div class="row g-2">`;

                        if (question.parsed_options && question.parsed_options.length > 0) {
                            question.parsed_options.forEach(option => {
                                const optionKey = `${questionId}-${option}`;
                                const existingValue = existingWeightage && existingWeightage[
                                    optionKey] ? existingWeightage[optionKey].marks : '';

                                html += `
                                <div class="col-md-3 col-sm-6">
                                    <div class="option-input mb-2">
                                        <label for="weightage_${optionKey}">${option}</label>
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            id="weightage_${optionKey}" 
                                            name="weightage[${optionKey}]" 
                                            value="${existingValue}"
                                            step="1" 
                                            min="0"
                                            oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;"
                                        >
                                    </div>
                                </div>`;
                            });
                            html += `</div>`;
                        }
                    } else {
                        // For other types of questions - single weightage
                        const existingValue = existingWeightage && existingWeightage[questionId] ?
                            existingWeightage[questionId].marks : '';

                        html += `
                        <div class="row g-2">
                            <div class="col-md-3 col-sm-6">
                                <div class="option-input mb-2">
                                    <label for="weightage_${questionId}">वेटेज मार्क्स</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="weightage_${questionId}" 
                                        name="weightage[${questionId}]" 
                                        value="${existingValue}"
                                        step="1" 
                                        min="0"
                                        oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;"
                                    >
                                </div>
                            </div>
                        </div>`;
                    }

                    html += `
                        </div>
                    </div>`;
                });

                $('#questionsContainer').html(html);

                // Update the qualifications container
                let qualificationsHtml = '';

                if (qualifications && qualifications.length > 0) {
                    // Loop through all qualifications
                    qualifications.forEach(qualification => {
                        let isMinimumQual = false;
                        if (minimumQualification && minimumQualification.length > 0) {
                            isMinimumQual = (qualification.Quali_ID == minimumQualification[0].Quali_ID);
                        }

                        const existingMark = existingQualificationMarks && existingQualificationMarks[
                                qualification.Quali_ID] ?
                            existingQualificationMarks[qualification.Quali_ID].marks :
                            '';

                        qualificationsHtml += `
                            <div class="col-md-3 col-sm-6">
                                <div class="mb-2">
                                    <label for="qualification_marks_${qualification.Quali_ID}" class="form-label">
                                        ${qualification.Quali_Name}
                                    </label>
                                    <input type="number" class="form-control"
                                        id="qualification_marks_${qualification.Quali_ID}"
                                        name="qualification_marks[${qualification.Quali_ID}]"
                                        value="${existingMark}"
                                        step="1" min="0"
                                        oninput="this.value = Math.round(this.value); if(this.value < 0) this.value = 0;">
                                </div>
                            </div>
                        `;
                    });
                } else {
                    qualificationsHtml =
                        '<div class="col-12"><div class="alert alert-info">कोई योग्यता डेटा उपलब्ध नहीं है।</div></div>';
                }

                // Update the qualifications container
                $('#qualificationsInputs').html(qualificationsHtml);
                $('#qualificationsContainer').show();

                $('#submitBtn').prop('disabled', false);

                // Setup option selection behavior for questions
                setupQuestionOptions();
            }

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

                    // Don't force default zeros; leave empty if unset
                });
            }

            // Form validation
            $('#weightageForm').on('submit', function(e) {
                let isValid = true;
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
                        confirmButtonColor: "#f39c12"
                    });
                    e.preventDefault();
                    return false;
                }
                return isValid;
            });
        });
    </script>
@endsection
