@extends('layouts.dahboard_layout')

@section('styles')
    {{--
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" /> --}}
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" />
    {{--
    <script src="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.umd.js"></script> --}}

    <style>
        #cke_notifications_area_editor {
            display: none !important;
        }

        .question-box {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            /*  Har question ke beech line */
        }

        .question-box:last-child {
            border-bottom: none;
            /*  Last question ke baad line nahi aayegi */
        }

        .question-box input {
            margin-right: 10px;
            /*  Checkbox aur text ke beech spacing */
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">अपरिवर्तनीय पोस्ट</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">
                        @if (isset($mode) && $mode == 'view')
                            अपरिवर्तनीय पोस्ट विवरण
                        @elseif(isset($mode) && $mode == 'edit')
                            अपरिवर्तनीय पोस्ट संपादित करें
                        @else
                            अपरिवर्तनीय पोस्ट जोड़ें
                        @endif
                    </li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            @if (isset($mode) && $mode == 'view')
                                <i class="bi bi-eye"></i> अपरिवर्तनीय पोस्ट विवरण
                            @elseif(isset($mode) && $mode == 'edit')
                                <i class="bi bi-pencil"></i> अपरिवर्तनीय पोस्ट संपादित करें
                            @else
                                <i class="bi bi-file-plus"></i> अपरिवर्तनीय पोस्ट जोड़ें
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end my-2">
                            <a href="{{ url('/admin/static-posts') }}" class="btn btn-success">
                                <i class="bi bi-list me-2"></i>अपरिवर्तनीय पोस्ट की सूची
                            </a>
                        </div>
                        <div class="row container">
                            <form id="myForm4"
                                action="{{ isset($mode) && $mode == 'edit' ? url('/admin/static-posts/' . $post->id . '/update') : url('/admin/upload-static-post') }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                @if (isset($mode) && $mode == 'edit')
                                    <input type="hidden" name="_method" value="POST">
                                @endif
                                <input type="hidden" name="app_id"
                                    value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                                <div class="col-md-12"><br>
                                    <div class="field_wrapper" id="inputFieldsContainer">
                                        <div class="row">
                                            <div class="col-md-7 text-left mt-3">
                                                <label for="postName">पोस्ट शीर्षक<font color="red">*</font></label>
                                                <input type="text" id="postName" class="form-control" name="post_name"
                                                    placeholder="पद का नाम" value="{{ isset($post) ? $post->title : '' }}"
                                                    {{ isset($mode) && $mode == 'view' ? 'readonly' : '' }} required>
                                                @error('post_name')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3 text-left mt-3">
                                                <label for="minAge">न्यूनतम आयु<font color="red">*</font></label>
                                                <input type="number" id="minAge" class="form-control" name="min_age"
                                                    min="18" max="50"
                                                    value="{{ isset($post) ? $post->min_age : '' }}"
                                                    {{ isset($mode) && $mode == 'view' ? 'readonly' : '' }}
                                                    placeholder="18 से 50 के बीच आयु दर्ज करें" required>

                                                @error('min_age')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- new changes end --}}

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="maxAge">अधिकतम आयु<font color="red">*</font></label>
                                                <input type="number" id="maxAge" class="form-control" name="max_age"
                                                    min="18" max="50"
                                                    value="{{ isset($post) ? $post->max_age : '' }}"
                                                    {{ isset($mode) && $mode == 'view' ? 'readonly' : '' }}
                                                    placeholder="18 से 50 के बीच आयु दर्ज करें" required>

                                                @error('max_age')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="maxAge">आयु में छूट के बाद अधिकतम आयु </label>
                                                <input type="number" id="maxAgeRelax" class="form-control"
                                                    name="max_age_relax" min="18" max="50"
                                                    value="{{ isset($post) ? $post->max_age_relax : '' }}"
                                                    {{ isset($mode) && $mode == 'view' ? 'readonly' : '' }}
                                                    placeholder="18 से 50 के बीच आयु दर्ज करें">

                                                @error('max_age_relax')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div class="col-md-3 text-left mt-3" id="minQualificationField">
                                                <label for="minQualification">न्यूनतम योग्यता<font color="red">*</font>
                                                </label>
                                                <select id="minQualification" class="form-select"
                                                    data-subject-url="{{ url('/admin/get-subjects-by-qualification') }}"
                                                    name="min_Qualification"
                                                    {{ isset($mode) && $mode == 'view' ? 'disabled' : '' }} required>
                                                    <option value="" disabled selected>-- चयन करें --</option>
                                                    @foreach ($qualifications as $qualification)
                                                        @if ($qualification->Quali_ID != 1 )
                                                            <option value="{{ $qualification->Quali_ID }}"
                                                                {{ isset($post) && $post->quali_id == $qualification->Quali_ID ? 'selected' : '' }}>
                                                                {{ $qualification->Quali_Name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="subject_select">विषय जोड़ें (न्यूनतम योग्यता से
                                                संबंधित)</label>
                                            <div class="input-group mb-2">
                                                <select id="subject_select" class="form-select">
                                                    <option value="">-- विषय चुनें --</option>
                                                    {{-- By default empty, will be filled by JS --}}
                                                </select>
                                                <button type="button" id="add_subject"
                                                    class="btn btn-success">जोड़ें</button>
                                            </div>
                                            <div id="subject_list" class="mt-2 d-flex flex-wrap gap-2">
                                                <!-- चुने गए विषय यहाँ दिखेंगे -->
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="skill_select">कौशल (Skills) जोड़ें</label>

                                            <div class="input-group mb-2">
                                                <select id="skill_select" class="form-select">
                                                    <option value="">-- कौशल चुनें --</option>
                                                    @foreach ($skills as $skill)
                                                        <option value="{{ $skill->skill_id }}">
                                                            {{ $skill->skill_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" id="add_skill"
                                                    class="btn btn-success">जोड़ें</button>
                                            </div>

                                            <!-- Skills List -->
                                            <div id="skill_list" class="mt-2 d-flex flex-wrap gap-2">
                                                <!-- Selected skills will be added here -->
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="org_type_input">न्यूनतम अनुभव</label>

                                            <div id="org_type_container">
                                                <!-- Organization types and experience will be rendered by JS in edit/view mode -->
                                                @if (isset($post->fk_organization_type_id) && !empty($post->fk_organization_type_id))
                                                    {{-- Rendered by JS --}}
                                                @else
                                                    <div class="org-type-entry d-flex align-items-center mb-2"
                                                        id="org_type_1">
                                                        <select class="form-control org-type-select me-2 form-select"
                                                            name="org_types[1]">
                                                            <option value="">-- संगठन प्रकार चुनें --</option>
                                                            @foreach ($org_types as $org_type)
                                                                <option value="{{ $org_type->org_id }}">
                                                                    {{ $org_type->org_type }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" class="form-control min-experience me-2"
                                                            name="experience[1]" placeholder="न्यूनतम अनुभव (वर्षों में)">
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- <button type="button" id="add_org_type" class="btn btn-success mt-3">और
                                                    जोड़ें</button> --}}
                                        </div>
                                    </div>

                                    <div class='row'>
                                        <div class='col-md-12 col-xw-12'>
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-xs-12 text-left">
                                            <label for="rules">नियम और विनियम<font color="red">*</font></label>
                                            <textarea name="rules" id="editor" required></textarea>
                                            @error('rules')
                                                <span class="text-danger is-invalid">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div><br>
                                    <!-- File Upload Column -->

                                    <div class="col-md-12 col-xs-12 text-left d-none">
                                        <label for="fileUpload">पोस्ट संबंधित फ़ाइल अपलोड करें
                                        </label>
                                        <input type="file" name="file" id="fileUpload" class="form-control"
                                            accept="image/*, .pdf">
                                        @error('file')
                                            <span class="text-danger is-invalid">{{ $message }}</span>
                                        @enderror
                                        @if (isset($post) && $post->file_path)
                                            <div class="mt-2">
                                                <a href="{{ asset($post->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-earmark-pdf"></i> वर्तमान फ़ाइल देखें
                                                </a>
                                            </div>
                                        @endif
                                        <a id="viewButton" class=" btn-danger d-none" data-bs-toggle="modal"
                                            data-bs-target="#docModal" style="cursor: pointer;">
                                            View Document
                                        </a>
                                    </div>

                                    <div class="col-md-12 col-xs-12 text-left my-2">
                                        <label class="form-label" for="questions">कृपया प्रश्नों का चयन करें:<font
                                                color="red">*
                                            </font>
                                        </label>
                                        <div id="questionsContainer" class="border rounded p-2"></div>
                                        <!--  Rounded border aur padding -->
                                        @if ($errors->has('questions'))
                                            <span
                                                class="text-danger mt-2 d-block">{{ $errors->first('questions') }}</span>
                                        @endif

                                    </div>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
                                        id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel">Selected File</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img id="imagePreview" class="img-fluid d-none" alt="Selected File">
                                                    <iframe id="docViewer" class="w-100 d-none"
                                                        style="height: 500px; border: none;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (!isset($mode) || $mode != 'view')
                                        <button id="submitBtn" style="float: right;"
                                            class="btn btn-success btn-lg pull-right" type="submit">
                                            {{ isset($mode) && $mode == 'edit' ? 'पोस्ट अपडेट करें' : 'पोस्ट सहेजें' }}
                                        </button>
                                    @else
                                        <div class="text-end mt-3">
                                            <a href="{{ url('/admin/static-posts/' . $post->id . '/edit') }}"
                                                class="btn btn-warning btn-lg">
                                                <i class="bi bi-pencil"></i> संपादित करें
                                            </a>
                                            <a href="{{ url('/admin/static-posts') }}" class="btn btn-secondary btn-lg">
                                                <i class="bi bi-arrow-left"></i> वापस जाएं
                                            </a>
                                        </div>
                                    @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>

    <script>
        /**
         * =====================================================================
         * INITIALIZATION AND CONFIGURATION
         * =====================================================================
         */

        // Initialize CKEditor
        CKEDITOR.replace('editor', {
            height: 200,
            removePlugins: 'elementspath',
            toolbarCanCollapse: false,
            resize_enabled: false,
            toolbar: [{
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ]
        });

        // Initialize Select2 elements (if used)
        $(document).ready(function() {
            // Load initial subjects if qualification is pre-selected
            let preselectedQualification = $('#minQualification').val();
            if (preselectedQualification) {
                loadSubjects(preselectedQualification);
            }

            // Call populateFormFields in edit or view mode
            @if (isset($post) && isset($mode) && ($mode == 'edit' || $mode == 'view'))
                let postData = @json($post);
                console.log('Post data from server:', postData);


                // Wait for questions to load first
                setTimeout(function() {
                    populateFormFields(postData);
                    Swal.fire({
                        icon: 'success',
                        title: 'लोड सफल!',
                        text: 'पोस्ट विवरण सफलतापूर्वक लोड हो गया।',
                        timer: 1200,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                }, 100);
            @endif
        });

        /**
         * =====================================================================
         * FORM POPULATION FUNCTION
         * =====================================================================
         */

        function populateFormFields(data) {
            console.log('Populating form with data:', data);

            // Show edit toggle container (if applicable)
            $('#editToggleContainer').show();
            let isEditMode = @json(isset($mode) && $mode == 'edit');

            // In edit mode, enable all fields; in view mode, keep them disabled/readonly
            if (isEditMode) {
                $('.auto-populated-field').each(function() {
                    if ($(this).is('select')) {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('readonly', false);
                    }
                });

                // Show add/remove buttons in edit mode
                $('#add_subject, #add_skill').show();
                $('.remove-subject, .remove-skill, .remove-org-type').show();

                if (CKEDITOR.instances.editor) {
                    CKEDITOR.instances.editor.setReadOnly(false);
                }
            } else {
                // In view mode, hide add/remove buttons
                @if (isset($mode) && $mode == 'view')
                    $('#add_subject, #add_skill').hide();
                    $('.remove-subject, .remove-skill, .remove-org-type').hide();
                @endif
            }

            // Set post name
            if (data.title) {
                $('#postName').val(data.title).addClass('auto-populated-field');
            }

            // Set ages
            if (data.min_age) $('#minAge').val(data.min_age).addClass('auto-populated-field');
            if (data.max_age) $('#maxAge').val(data.max_age).addClass('auto-populated-field');
            if (data.max_age_relax) $('#maxAgeRelax').val(data.max_age_relax).addClass('auto-populated-field');

            // Set qualification
            if (data.quali_id) {
                $('#minQualification').val(data.quali_id).trigger('change').addClass('auto-populated-field');

                // Load subjects for this qualification
                setTimeout(function() {
                    loadSubjects(data.quali_id, function() {
                        // After subjects are loaded, select them
                        if (data.fk_subject_id && Array.isArray(data.fk_subject_id)) {
                            $('#subject_list').empty();
                            data.fk_subject_id.forEach(function(subjectId, idx) {
                                const subjectOption = $(
                                    `#subject_select option[value="${subjectId}"]`);
                                if (subjectOption.length) {
                                    const subjectText = subjectOption.text();
                                    const id = 'subject_' + subjectId + '_' + Date.now() + '_' +
                                        idx;

                                    if (!$(`#subject_list input[value="${subjectId}"]`).length) {
                                        $('#subject_list').append(`
                                    <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                                        <input type="hidden" name="subjects[]" value="${subjectId}">
                                        <span>${subjectText}</span>
                                        <button type="button" class="btn-close btn-close-white ms-2 remove-subject" data-id="${id}" aria-label="Close" style="${isEditMode ? '' : 'display: none;'}"></button>
                                    </div>
                                `);
                                    }
                                }
                            });
                        }
                    });
                }, 300);
            }

            // Set skills
            if (data.fk_skill_id && Array.isArray(data.fk_skill_id)) {
                $('#skill_list').empty();
                data.fk_skill_id.forEach(function(skillId, idx) {
                    const skillOption = $(`#skill_select option[value="${skillId}"]`);
                    if (skillOption.length) {
                        const skillText = skillOption.text();
                        const id = 'skill_' + skillId + '_' + Date.now() + '_' + idx;

                        if (!$(`#skill_list input[value="${skillId}"]`).length) {
                            $('#skill_list').append(`
                        <div class="badge bg-info p-2 d-flex align-items-center" id="${id}">
                            <input type="hidden" name="skills[]" value="${skillId}">
                            <span>${skillText}</span>
                            <button type="button" class="btn-close btn-close-white ms-2 remove-skill" data-id="${id}" aria-label="Close" style="${isEditMode ? '' : 'display: none;'}"></button>
                        </div>
                    `);
                        }
                    }
                });
            }

            // Set organization types and experience
            if (data.fk_organization_type_id && Array.isArray(data.fk_organization_type_id) && data.fk_organization_type_id
                .length > 0) {
                $('#org_type_container').empty();
                data.fk_organization_type_id.forEach(function(orgTypeId, index) {
                    const experience = (data.minimum_experiance_year && data.minimum_experiance_year[index]) ? data
                        .minimum_experiance_year[index] : '';
                    const entryId = 'org_type_' + (index + 1);

                    // Build options for organization types
                    let orgTypeOptions = '<option value="">-- संगठन प्रकार चुनें --</option>';
                    @foreach ($org_types as $org_type)
                        orgTypeOptions +=
                            `<option value="{{ $org_type->org_id }}" ${orgTypeId == '{{ $org_type->org_id }}' ? 'selected' : ''}>{{ $org_type->org_type }}</option>`;
                    @endforeach

                    $('#org_type_container').append(`
                <div class="org-type-entry d-flex align-items-center mb-2" id="${entryId}">
                    <select class="form-control org-type-select me-2 form-select auto-populated-field" name="org_types[${index + 1}]" ${isEditMode ? '' : 'disabled'}>
                        ${orgTypeOptions}
                    </select>
                    <input type="number" class="form-control min-experience me-2 auto-populated-field" name="experience[${index + 1}]" placeholder="न्यूनतम अनुभव (वर्षों में)" value="${experience}" ${isEditMode ? '' : 'readonly'}>
                    <button type="button" class="btn btn-danger remove-org-type" data-id="${entryId}" style="${isEditMode ? '' : 'display: none;'}">हटाएं</button>
                </div>
            `);
                });
            }

            // Set guidelines in CKEditor
            if (data.guidelines) {
                setTimeout(function() {
                    if (CKEDITOR.instances.editor) {
                        CKEDITOR.instances.editor.setData(data.guidelines);
                    }
                }, 300);
            }

            // Handle questions
            if (data.fk_ques_id && Array.isArray(data.fk_ques_id)) {
                console.log('Setting questions:', data.fk_ques_id);
                $('input[name="questions[]"]').prop('checked', false);
                $('.child-questions-container').empty();
                window.selectedQuestions = data.fk_ques_id;

                setTimeout(function() {
                    // First pass: check all parent questions
                    data.fk_ques_id.forEach(function(quesId) {
                        const checkbox = $(`input[name="questions[]"][value="${quesId}"]`);
                        if (checkbox.length && checkbox.hasClass('parent-question-checkbox')) {
                            checkbox.prop('checked', true).addClass('auto-populated-question');
                            checkbox.trigger('change'); // Load child questions
                        }
                    });

                    // Second pass: after child questions loaded, check them
                    setTimeout(function() {
                        data.fk_ques_id.forEach(function(quesId) {
                            const checkbox = $(`input[name="questions[]"][value="${quesId}"]`);
                            if (checkbox.length) {
                                checkbox.prop('checked', true).addClass('auto-populated-question');
                            }
                        });

                        // Third pass: disable if view mode
                        setTimeout(function() {
                            $('input[name="questions[]"]').each(function() {
                                if (!isEditMode) {
                                    $(this).prop('disabled', true);
                                }
                                if ($(this).prop('checked')) {
                                    $(this).addClass('auto-populated-question');
                                }
                            });
                        }, 500);
                    }, 1000);
                }, 500);
            }
        }

        /**
         * =====================================================================
         * VALIDATION FUNCTIONS
         * =====================================================================
         */

        function validateForm() {
            let isValid = true;
            document.querySelectorAll("#myForm4 input[required], #myForm4 select[required], #myForm4 textarea[required]")
                .forEach((el) => {
                    if (!el.value.trim()) {
                        isValid = false;
                        el.classList.add("is-invalid");
                    } else {
                        el.classList.remove("is-invalid");
                    }
                });
            return isValid;
        }

        // Age validation
        ['minAge', 'maxAge', 'maxAgeRelax'].forEach(function(id) {
            document.getElementById(id)?.addEventListener("input", function(e) {
                e.preventDefault();
                let age = parseInt(this.value);
                if (age < 18 || age > 50) {
                    this.setCustomValidity("आयु 18 से 50 के बीच होनी चाहिए!");
                    this.classList.add("is-invalid");
                } else {
                    this.setCustomValidity("");
                    this.classList.remove("is-invalid");
                }
                this.reportValidity();
            });
        });

        /**
         * =====================================================================
         * UI INTERACTION HANDLERS
         * =====================================================================
         */

        let orgTypeCount = 1;

        $('#add_org_type').on('click', function() {
            orgTypeCount++;
            $('#org_type_container').append(`
        <div class="org-type-entry d-flex align-items-center mb-2" id="org_type_${orgTypeCount}">
            <select class="form-control org-type-select me-2 form-select" name="org_types[${orgTypeCount}]">
                <option value="">-- संगठन प्रकार चुनें --</option>
                @foreach ($org_types as $org_type)
                    <option value="{{ $org_type->org_id }}">{{ $org_type->org_type }}</option>
                @endforeach
            </select>
            <input type="number" class="form-control min-experience me-2" name="experience[${orgTypeCount}]" placeholder="न्यूनतम अनुभव (वर्षों में)">
            <button type="button" class="btn btn-danger remove-org-type" data-id="${orgTypeCount}">हटाएं</button>
        </div>
    `);
        });

        $(document).on('click', '.remove-org-type', function() {
            const id = $(this).data('id');
            $(`#org_type_${id}`).remove();
        });

        $(document).on('change', '.org-type-select', function() {
            const id = $(this).closest('.org-type-entry').attr('id').split('_')[2];
            const selectedOrgType = $(this).val();
            if (selectedOrgType && !$(`#org_type_${id} .min-experience`).val()) {
                $(`#org_type_${id} .min-experience`).attr('required', true);
            }
        });

        $('#add_subject').on('click', function() {
            let subject = $('#subject_select').val();
            let subjectText = $('#subject_select option:selected').text();
            let id = 'subject_' + Date.now();

            if (subject && !$('#subject_list input[value="' + subject + '"]').length) {
                $('#subject_list').append(`
            <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                <input type="hidden" name="subjects[]" value="${subject}">
                <span>${subjectText}</span>
                <button type="button" class="btn-close btn-close-white ms-2 remove-subject" data-id="${id}" aria-label="Close"></button>
            </div>
        `);
                $('#subject_select').val('');
            }
        });

        $(document).on('click', '.remove-subject', function() {
            let targetId = $(this).data('id');
            $('#' + targetId).remove();
        });

        $('#add_skill').on('click', function() {
            let skill = $('#skill_select').val();
            let skillText = $('#skill_select option:selected').text();
            let id = 'skill_' + Date.now();

            if (skill && !$('#skill_list input[value="' + skill + '"]').length) {
                $('#skill_list').append(`
            <div class="badge bg-info p-2 d-flex align-items-center" id="${id}">
                <input type="hidden" name="skills[]" value="${skill}">
                <span>${skillText}</span>
                <button type="button" class="btn-close btn-close-white ms-2 remove-skill" data-id="${id}" aria-label="Close"></button>
            </div>
        `);
                $('#skill_select').val('');
            }
        });

        $(document).on('click', '.remove-skill', function() {
            let targetId = $(this).data('id');
            $('#' + targetId).remove();
        });

        document.getElementById("fileUpload").addEventListener("change", function(event) {
            let file = event.target.files[0];
            let allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif", "application/pdf"];
            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: "error",
                        title: "⚠ गलत फ़ाइल प्रकार!",
                        text: "❌ केवल छवियाँ (JPG, PNG) और PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "ठीक है"
                    });
                    event.target.value = "";
                    return;
                }
                let fileURL = URL.createObjectURL(file);
                let viewButton = document.getElementById("viewButton");
                let docViewer = document.getElementById("docViewer");
                let imagePreview = document.getElementById("imagePreview");

                viewButton.classList.remove("d-none");

                if (file.type.startsWith("image/")) {
                    imagePreview.src = fileURL;
                    imagePreview.classList.remove("d-none");
                    docViewer.classList.add("d-none");
                } else {
                    docViewer.src = fileURL;
                    docViewer.classList.remove("d-none");
                    imagePreview.classList.add("d-none");
                }
            }
        });

        /**
         * =====================================================================
         * AJAX REQUESTS
         * =====================================================================
         */

        function loadSubjects(qualificationId, callback = null) {
            let url = $('#minQualification').data('subject-url') + '/' + qualificationId;
            $.ajax({
                url: url,
                type: 'GET',
                success: function(subjects) {
                    let options = '<option value="">-- विषय चुनें --</option>';
                    subjects.forEach(subject => {
                        options +=
                            `<option value="${subject.subject_id}">${subject.subject_name}</option>`;
                    });
                    $('#subject_select').html(options);
                    if (typeof callback === "function") callback();
                },
                error: function() {
                    $('#subject_select').html('<option value="">कोई विषय नहीं मिला</option>');
                }
            });
        }

        $('#minQualification').change(function() {
            let qualificationId = $(this).val();
            if (qualificationId) {
                $('#subject_list').empty();
                loadSubjects(qualificationId);
            }
        });

        $(document).ready(function() {
            let loadedChildQuestions = {};
            $.ajax({
                url: "/admin/get-questions",
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $("#questionsContainer").html('<p>प्रश्न लोड हो रहे हैं...</p>');
                },
                success: function(response) {
                    let html = "";
                    if (response.length > 0) {
                        response.forEach(question => {
                            html += `
                        <div class="question-box parent-question-wrapper" id="parentQuestionWrapper_${question.ques_ID}" style="display:block;">
                            <input type="checkbox"
                                name="questions[]"
                                value="${question.ques_ID}"
                                class="parent-question-checkbox"
                                data-question-id="${question.ques_ID}">
                            <label class="fw-normal">${question.ques_name}</label>
                            <div class="child-questions-container" id="childQuestionsOf_${question.ques_ID}">
                            </div>
                        </div>
                    `;
                        });
                        $("#questionsContainer").html(html);
                    } else {
                        $("#questionsContainer").html('<p>कोई प्रश्न उपलब्ध नहीं है।</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Initial AJAX Error:", status, error);
                    $("#questionsContainer").html(
                        '<p class="text-danger">प्रश्न लोड करने में त्रुटि। कृपया पुनः प्रयास करें।</p>'
                    );
                }
            });
        });

        $('#questionsContainer').on('change', '.parent-question-checkbox', function() {
            const parentQuestionId = $(this).data('question-id');
            const isChecked = $(this).is(':checked');
            const childQuestionsContainer = $(`#childQuestionsOf_${parentQuestionId}`);

            if (isChecked) {
                $.ajax({
                    url: `/admin/get-questions?parentId=${parentQuestionId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        let html = "";
                        if (response.length > 0) {
                            response.forEach(question => {
                                html += `
                            <div id="childQuestion_${question.ques_ID}" class="question-box child-question-item parent-${parentQuestionId}-child">
                                <input type="checkbox" name="questions[]" value="${question.ques_ID}" class="child-question-checkbox">
                                <label class="fw-normal">${question.ques_name}</label>
                            </div>
                        `;
                            });
                            childQuestionsContainer.html(html);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error for child questions:", status, error);
                        childQuestionsContainer.html(
                            '<p class="text-danger">संबंधित प्रश्न लोड करने में त्रुटि।</p>'
                        );
                    }
                });
            } else {
                childQuestionsContainer.empty();
            }
        });

        /**
         * =====================================================================
         * FORM SUBMISSION
         * =====================================================================
         */

        $("#myForm4").submit(function(e) {
            e.preventDefault();
            $('#submitBtn').attr('disabled', true);
            validateForm();
            let formData = new FormData(this);
            formData.append("rules", CKEDITOR.instances.editor.getData());

            let formUrl = $(this).attr('action');

            $.ajax({
                url: formUrl,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    @if (isset($mode) && $mode == 'edit')
                        let messages = [
                            "अपरिवर्तनीय पोस्ट सफलतापूर्वक अपडेट की गई!",
                            "आपकी अपरिवर्तनीय पोस्ट अपडेट हो गई है!",
                            "अपरिवर्तनीय पोस्ट अपडेट हो चुकी है, धन्यवाद!",
                            "आपका डेटा सफलतापूर्वक अपडेट किया गया!",
                            "पोस्ट अपडेट हो गई, आगे बढ़ें!"
                        ];
                        let randomMessage = messages[Math.floor(Math.random() * messages.length)];

                        Swal.fire({
                            title: "सफलता!",
                            text: randomMessage,
                            icon: "success",
                            confirmButtonText: "ठीक है"
                        }).then(() => {
                            window.location.href = "{{ url('/admin/static-posts') }}";
                        });
                    @else
                        let messages = [
                            "अपरिवर्तनीय पोस्ट सफलतापूर्वक दर्ज की गई!",
                            "आपकी अपरिवर्तनीय पोस्ट जोड़ दी गई है!",
                            "अपरिवर्तनीय पोस्ट अपलोड हो चुकी है, धन्यवाद!",
                            "आपका डेटा सफलतापूर्वक सहेजा गया!",
                            "पोस्ट जोड़ दी गई, आगे बढ़ें!"
                        ];
                        let randomMessage = messages[Math.floor(Math.random() * messages.length)];

                        Swal.fire({
                            title: "सफलता!",
                            text: randomMessage,
                            icon: "success",
                            confirmButtonText: "ठीक है"
                        }).then(() => {
                            // Ask if user wants to set weightage for this post
                            Swal.fire({
                                title: "वेटेज सेट करें?",
                                text: "क्या आप इस पोस्ट के लिए वेटेज सेट करना चाहते हैं?",
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "हाँ, सेट करें",
                                cancelButtonText: "नहीं, बाद में"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirect to static weightage create page with post_config_id
                                    let postConfigId = response.post_config_id || '';
                                    window.location.href =
                                        "{{ url('/admin/weightage-management/create') }}?post_id=" +
                                        postConfigId;
                                } else {
                                    // Reload the page or redirect to list
                                    window.location.href =
                                        "{{ url('/admin/static-posts') }}";
                                }
                            });
                        });
                    @endif
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errorMessage = 'कृपया सभी आवश्यक जानकारी भरें।';

                        // Check for direct message (from middleware)
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        // Check for validation errors
                        else if (xhr.responseJSON && xhr.responseJSON.errors && typeof xhr.responseJSON.errors === 'object') {
                            var errors = xhr.responseJSON.errors;
                            var errorValues = Object.values(errors);

                            if (errorValues && errorValues.length > 0) {
                                errorMessage = '';
                                for (var key in errors) {
                                    if (errors.hasOwnProperty(key)) {
                                        if (Array.isArray(errors[key])) {
                                            errorMessage += errors[key].join('<br>') + '<br>';
                                        } else {
                                            errorMessage += errors[key] + '<br>';
                                        }
                                    }
                                }
                            }
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'ध्यान दें',
                            html: errorMessage,
                            confirmButtonText: 'ठीक है'
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ध्यान दें',
                            text: 'कुछ गलत हो गया। कृपया दोबारा प्रयास करें।',
                            confirmButtonText: 'ठीक है'
                        });
                    }
                    $('#submitBtn').attr('disabled', false);
                }
            });
        });
    </script>
@endsection
