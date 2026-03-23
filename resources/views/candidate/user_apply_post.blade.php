@extends('layouts.dahboard_layout')
@section('styles')
 
    <style>
        /* Optional: Custom modal styling */
        .modal-backdrop {
            opacity: 0.5 !important;
        }

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .35);
        }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border: 1px solid #007bff;
            border-radius: 4px;
            appearance: none;
            /* Reset default style */
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            background-color: white;
        }

        input[type="checkbox"]:checked {
            background-color: #007bff;
        }
    </style>
@endsection
@section('body-page')
    <main id="main" class="main">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="row">

                    <div class="col-md-4 grid-margin stretch-card" style="padding: 0px !important;">
                        <button style="width: 100%; border-radius :15px;" id="post-btn" type="button"
                            class="btn btn-success btn-sm btn-icon-text add-app-btn">
                            पद चयन
                        </button>
                    </div>
                    <div class="col-md-4 grid-margin stretch-card" style="padding: 0px !important;">
                        <button style="width: 100%; border-radius :15px;" id="post-questions-btn" type="button" disabled
                            class="btn btn-primary btn-sm btn-icon-text add-app-btn">
                            आवश्यक प्रश्न
                        </button>
                    </div>
                </div><br>


                <div class="card" id="tab1">
                    <div class="row container">
                        <form id="myForm1" action="{{ url('/candidate/save-post') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" value="{{ session()->get('sess_id') }}" id="applicant_id">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-5"><br>
                                            <label for="postname">पद सेलेक्ट करें </label><label style="color:red">*</label>
                                            <select class="form-select" id="postname" name="postname">
                                                <option selected disabled value="">-- चयन करें --</option>
                                                @foreach ($data['recruitment'] as $value_rc)
                                                    <option value="{{ $value_rc->Post_ID }}">
                                                        {{ $value_rc->Post_Title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-5"><br>
                                            <label for="city" id="labelcity">जिले का चयन करें </label><label
                                                style="color:red">*</label>
                                            <select class="form-select" name="district" id="district">
                                                <option selected disabled value="undefined">-- चयन करें --</option>
                                            </select>
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button id='postApplyBtn' style="float: right;"
                                                class="btn btn-primary nextBtn pull-right mt-5" type="submit">Save &
                                                Next</button>
                                        </div>
                                    </div>

                                    <div class="row col-md-12 mt-5">

                                        <div class="col-md-1"></div>
                                        <div class="col-md-9">
                                            <div id="QualificationMessage"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="col-md-12"><br>
                                        <div id="post_details"></div>

                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="postModalLabel">पद संबंधी निर्देश</h5>
                                            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button> --}}
                                        </div>
                                        <div class="modal-body" id="modalInstructions">
                                            <!-- Guidelines will be injected here -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद
                                                करें</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>


                <div class="card" id="tab2" style="display: none">
                    <div class="row container">
                        <form id="myForm2" action="{{ url('/candidate/save-post-question') }}" method="post"
                            enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="applicant_id_tab2" id="applicant_id_tab2">
                            <input type="hidden" name="application_id" id="application_id">
                            <input type="hidden" name="post_id" id="post_id_set">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mt-4">
                                        <h5>चयनित पोस्ट से संबंधित प्रश्नों का उत्तर दें :</h5>
                                        <div id="questionsContainer"></div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mt-3">
                                        <h5>कौशल चुनें :</h5>

                                        <div id="skillsContainer"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button style="float: right;" class="btn btn-primary nextBtn pull-right mt-3"
                                    type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </main>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.18.0/js/md5.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const recruitmentData = @json($data['recruitment']);

        document.getElementById('postname').addEventListener('change', function() {
            const postId = this.value;

            if (postId !== "") {
                const selectedPost = recruitmentData.find(post => post.Post_ID == postId);

                const postInstructions = selectedPost && selectedPost.guidelines ?
                    selectedPost.guidelines :
                    'इस पद के लिए कोई निर्देश उपलब्ध नहीं हैं।';

                document.getElementById('modalInstructions').innerHTML = postInstructions;

                const modal = new bootstrap.Modal(document.getElementById('postModal'), {
                    backdrop: 'static',
                    keyboard: true
                });
                modal.show();
            }
        });





        $(document).ready(function() {


            //to add and remove class from input feild
            $('form input').keyup(function() {
                if (this.value) {
                    var element = $(this);

                    // Remove the is-invalid class from input elements
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    var element = $(this);
                    var elementName = element.attr('name');
                }
            });


            //to add and remove class from textarea
            $('form textarea').keyup(function() {
                if (this.value) {
                    var element = $(this);
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('is-invalid');
                        element.addClass('was-validated');
                    }
                } else {
                    var element = $(this);
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('was-validated');
                        element.addClass('is-invalid');
                    }
                }
            });

            $('form input[type="date"]').change(function() {
                var element = $(this);
                if (element.val()) {
                    // Remove the is-invalid class and add the was-validated class
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    // Remove the was-validated class and add the is-invalid class
                    element.removeClass('was-validated');
                    element.addClass('is-invalid');
                }
            });

            $('form input[type="file"]').change(function() {
                var element = $(this);
                if (element.val()) {
                    // Remove the is-invalid class and add the was-validated class
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    // Remove the was-validated class and add the is-invalid class
                    element.removeClass('was-validated');
                    element.addClass('is-invalid');
                }
            });

            //to add and remove class from dropdown
            $('form select').change(function() {
                if (this.value) {
                    var element = $(this);
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('is-invalid');
                        element.addClass('was-validated');
                    }
                } else {
                    var element = $(this);
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('was-validated');
                        element.addClass('is-invalid');
                    }
                }
            });



            // ##=========== //Get Post Selected District ========================##

            $('select[name=postname]').change(function() {
                let post_id = $("#postname").val();
                $.ajax({
                    url: '/candidate/get-district',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    cache: false,
                    success: function(response) {
                        let option =
                            '<option selected disabled value="undefined">--चुनें--</option>';

                        if (response && response.length > 0) {
                            $.each(response, function(index, value) {
                                option += '<option value="' + value.District_Code_LGD +
                                    '">' + value
                                    .name + '</option>';
                            });
                        } else {
                            option += '<option value="">No Districts Found</option>';
                        }

                        $('#district').html(option);
                    },
                    error: function(xhr) {
                        $('#district').empty();
                        $('#district').append(
                            '<option selected hidden value=""> --Select-- </option>');
                        $('#district').append('<option value="">No Data Found</option>');
                    }
                });
            });


            // ##===========  // Get Min Quali_ID Select Post ========================##
            $('select[name=postname]').change(function() {
                let post_id = $("#postname").val();
                let applicant_id = $("#applicant_id")
                    .val(); // Add this hidden input in form with applicant ID

                $.ajax({
                    url: '/candidate/get-post-qualification',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        applicant_id: applicant_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'not_matched') {
                            $('#QualificationMessage').html(
                                `<div class="alert alert-warning" role="alert">
                                 इस पोस्ट के लिए न्यूनतम शैक्षणिक योग्यता <strong>"${response.qualification_name}"</strong> आवश्यक है। आपकी योग्यता पोस्ट के अनुसार मेल नहीं खाती।
                                      </div>`
                            );
                            $('#postApplyBtn').prop('disabled', true);
                        } else {
                            $('#QualificationMessage').html(''); // Clear previous warning
                            $('#postApplyBtn').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.log("Error:", xhr);
                    }
                });
            });



            // ##===========  // All Questions Load of Select Post ========================##
            $('select[name=postname]').change(function() {
                let post_id = $("#postname").val();
                $.ajax({
                    url: '/candidate/get-post-questions',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    cache: false,
                    success: function(response) {
                        let html = '';
                        if (response && response.length > 0) {
                            $.each(response, function(index, value) {
                                html += `<div class="row">
                        <div class="col-md-12 col-xs-12 text-left">
                            <label>${value.ques_name}</label>
                            <label style="color:red">*</label>
                            <div class="question-group" data-question-id="${value.post_map_id}">`;

                                let options = JSON.parse(value.answer_options);
                                $.each(options, function(optIndex, optValue) {
                                    html += `<label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                            <input type="radio" 
                                   name="question_${value.post_map_id}" 
                                   value="${optValue}" 
                                   required> ${optValue}
                        </label>`;
                                });

                                html += `</div><span class="error-text" style="color: red; display: none;">यह फ़ील्ड आवश्यक है</span>
                        </div></div><div class='row'><div class='col-md-12 col-xw-12'><hr></div></div>`;
                            });
                        } else {
                            html = "<p> कोई प्रश्न नहीं मिला </p>";
                        }
                        $('#questionsContainer').html(html);
                    },
                    error: function(xhr) {
                        $('#questionsContainer').html('<p>Error loading questions.</p>');
                    }
                });
            });




            // ##===========  // All Skills Load of Select Post ========================##
            $('select[name=postname]').change(function() {
                let post_id = $("#postname").val();
                $.ajax({
                    url: '/candidate/get-post-skills',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    cache: false,
                    success: function(response) {
                        let html = '';
                        if (response && response.length > 0) {
                            html += '<div class="form-group"><div>';
                            $.each(response, function(index, value) {
                                html += `
                                            <div class="form-check">
                                                <input type="checkbox" name="skills[]" value="${value.fk_skill_id}" class="form-check-input" id="skill_${index}">
                                                <label class="form-check-label" for="skill_${index}">${value.SkillName}</label>
                                            </div>
                                        `;
                            });
                            html += '</div></div>';
                        } else {
                            html = "<p> इस पद के लिए कोई कौशल अनिवार्य नहीं है| </p>";
                        }
                        $('#skillsContainer').html(html);
                    },
                    error: function(xhr) {
                        $('#skillsContainer').html('<p>कौशल लोड करने में त्रुटि.</p>');
                    }
                });
            });










            // ##=========== //Form Tabs Functionality ===============================##
            // Add this function at the top with your other functions
            function resetButtonColors() {
                document.getElementById("post-btn").style.backgroundColor = "#157347"; // Default green for post-btn
                document.getElementById("post-questions-btn").style.backgroundColor =
                    "#0d6efd"; // Default blue for post-questions-btn
            }

            function hideAllDivs() {
                document.getElementById("tab1").style.display = "none";
                document.getElementById("tab2").style.display = "none";
            }

            // Set initial colors when page loads
            window.onload = function() {
                resetButtonColors();
            };

            // Modify the click event listeners
            document.getElementById("post-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab1").style.display = "block";
                document.getElementById("tab2").style.display = "none";
                // Set colors when clicking post-btn (tab1 active)
                this.style.backgroundColor = "#157347"; // Green for active tab1
                document.getElementById("post-questions-btn").style.backgroundColor =
                    "#0d6efd"; // Blue for tab2
            });

            document.getElementById("post-questions-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab2").style.display = "block";
                document.getElementById("tab1").style.display = "none";
                // Set colors when clicking post-questions-btn (tab2 active)
                this.style.backgroundColor = "#157347"; // Green for active tab2
                document.getElementById("post-btn").style.backgroundColor = "#0d6efd"; // Blue for tab1
            });








            //============ ## Save Local Storage of myForms Data ##===========

            $('#myForm1').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally
                $('#post-questions-btn').removeAttr('disabled');
                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');
                // var storageKey = $(this).data('storage-key') || 'application.post';

                // Submit the form using AJAX
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    success: function(data) {

                        // console.log('Server response:', data);
                        if (data.status == 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: data['title'],
                                text: data['message'],
                            });

                            var post_id = data.post_id;
                            var apply_id = data.apply_id;
                            var application_id = data.application_id;
                            // console.log(post_id);
                            // console.log(apply_id);

                            $("#applicant_id_tab2").val(apply_id);
                            $("#application_id").val(application_id);
                            $("#post_id_set").val(post_id);

                            // Show tab2 and hide tab1
                            $("#tab2").css("display", "block");
                            $("#tab1").css("display", "none");

                            // Set button colors: tab2 active (green), tab1 inactive (blue)
                            document.getElementById("post-btn").style.backgroundColor =
                                "#0d6efd"; // Blue for inactive tab1
                            document.getElementById("post-questions-btn").style
                                .backgroundColor = "#157347"; // Green for active tab2

                            document.getElementById("post-btn").removeAttribute("disabled");
                            document.getElementById("post-questions-btn").removeAttribute(
                                "disabled");



                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('ajax error:', xhr.responseText);

                        let response;

                        // Safe JSON parsing
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = {
                                message: 'कृपया सभी आवश्यक फ़ील्ड भरें !',
                                errors: {}
                            };
                        }

                        // Base Swal message
                        let errorMessages = response.message ||
                            'कृपया सभी आवश्यक फ़ील्ड भरें !';

                        // Add validation errors inside list
                        if (response.errors) {
                            errorMessages += '<br><ul>';

                            $.each(response.errors, function(key, value) {
                                if (Array.isArray(value) && value.length > 0) {
                                    errorMessages += `<li>${value[0]}</li>`;
                                }

                                // Highlight field
                                var element = $('[name="' + key + '"]');
                                if (element.length) {
                                    element.addClass('is-invalid');
                                }
                            });

                            errorMessages += '</ul>';

                            // If your page uses #error div
                            $('#error').html("This field is required");
                        }

                        // Swal final output (fully updated)
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            html: `
            कृपया सभी आवश्यक फ़ील्ड भरें ! <br> 
            (Please Fill All Required Field)<br><br>
            <small>Please see fields marked in red....</small>
            <br><br>
            ${errorMessages}
        `,
                            allowOutsideClick: false
                        });
                    }
                });

            });



            $('#myForm2').submit(function(e) {
                e.preventDefault();

                // Check if all required fields are filled
                let isValid = true;
                $('.question-group').each(function() {
                    if ($(this).find('input[type="radio"]:checked').length === 0) {
                        $(this).siblings('.error-text').show();
                        isValid = false;
                    } else {
                        $(this).siblings('.error-text').hide();
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'कृपया सभी आवश्यक फ़ील्ड भरें!',
                        text: 'सभी प्रश्नों के उत्तर देना अनिवार्य है।'
                    });
                    return;
                }

                var form = new FormData(this);
                form.append('post_id', $('#postname').val()); // Add post_id to form data

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 'success') {

                            var post_id = data.post_id;
                            var apply_id = data.applicant_id;
                            var application_id = data.application_id;
                            console.log(application_id);

                            Swal.fire({
                                icon: 'success',
                                title: data.title,
                                text: data.message,

                            }).then((result) => {
                                window.location.href =
                                    '/candidate/view-application-detail/' + md5(
                                        apply_id) +
                                    '/' + md5(application_id);
                            });



                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data.title,
                                text: data.message,
                                allowOutsideClick: false
                            });
                        }
                    },
                    error: function(xhr) {

                        let response;

                        // Safe JSON parsing
                        try {
                            response = xhr.responseJSON || JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = {
                                message: 'कृपया सभी आवश्यक फ़ील्ड भरें!',
                                errors: {}
                            };
                        }

                        // Base error message
                        let errorMessages = response.message || 'कृपया सभी आवश्यक फ़ील्ड भरें!';

                        // If validation errors exist → show them in list + highlight fields
                        if (response.errors) {
                            errorMessages += '<br><ul>';

                            $.each(response.errors, function(key, value) {
                                if (Array.isArray(value) && value.length > 0) {
                                    errorMessages += `<li>${value[0]}</li>`;
                                }

                                // highlight input field
                                var element = $(`[name="${key}"]`);
                                if (element.length) {
                                    element.addClass('is-invalid');
                                    element
                                        .closest('.form-group')
                                        .find('.invalid-feedback')
                                        .html(value[0]);
                                }
                            });

                            errorMessages += '</ul>';
                        }

                        // Swal Box - now consistent with all your other handlers
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            html: errorMessages, // using html to show list properly
                            allowOutsideClick: false
                        });
                    }

                });
            });




        });
    </script>
@endsection
