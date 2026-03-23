@extends('layouts.login_layout')

@section('styles')
    <style>
        .AdharConfirm {
            width: 20px;
            height: 20px;
            border: 1px solid #007bff;
            border-radius: 4px;
            appearance: none;
            /* Reset default style */
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            background-color: white;
        }

        .AdharConfirm:checked {
            background-color: #007bff;
        }
    </style>
@endsection
<link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">


@section('main-content')

    <body class="d-flex flex-column h-100">
        <main role="main">

            <body style="background-color: aliceblue;">
                <div class="site-login">
                    <div class="row">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-6"><br>

                            <div class="card login-box">
                                <div class="card-header logo" style="background-color: #14375F;height: 150px;">
                                    <div class=logo-icon><img src="{{ asset('assets/img/landingpage_img/cg-logo.svg') }}">
                                    </div>
                                    <div class=logo-title>
                                        <h1>ई-भर्ती</h1>
                                        <p>महिला एवं बाल विकास विभाग</p>
                                    </div>
                                </div>
                                <div class="card-body login-card-body">
                                    <h4 class="text-center login-title">नए आवेदनकर्ता पंजीकरण </h4>  
                                    <form id="myForm" action="{{ url('/add-new-user') }}" method="post">
                                        @csrf
                                        <p class="p-1 border border-mute border-circle rounded">
                                            <span class="text-danger">
                                                ** नोट: कृपया अपना स्वयं का मोबाइल नंबर ही दर्ज करें। आगामी प्रक्रिया में
                                                ओटीपी (OTP) सत्यापन तथा आवेदन से संबंधित सभी सूचनाएं इसी मोबाइल नंबर पर भेजी
                                                जाएंगी। **
                                            </span>
                                        </p>
                                        <div class="row mt-2">
                                            <div class="col-lg-6">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <label for="name">आवेदनकर्ता का नाम/Applicant's Full
                                                        Name</label><label style="color:red">*</label>
                                                    <div class="input-group mb-3"><input type="text" id="name"
                                                            class="form-control alphabets-only" name="name"
                                                            style="text-transform:uppercase"
                                                            placeholder="आवेदनकर्ता का नाम दर्ज करें" aria-required="true" required>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <label for="dob">जन्मतिथि/Date of Birth </label><label
                                                        style="color:red">*</label>
                                                    <div class="input-group mb-3"> <input type="date" id="dob"
                                                            class="form-control" max="{{ $maxdate }}" name="dob" required>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <label for="mobile">मोबाइल नंबर/Mobile Number</label><label
                                                        style="color:red">*</label>
                                                    <div class="input-group mb-3"><input type="text" id="mob"
                                                            class="form-control number-only" name="mob"
                                                            placeholder="मोबाइल नंबर दर्ज करें" aria-required="true"
                                                            maxlength="10" required
                                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <label for="dob">आधार नंबर /Aadhar Number </label><label
                                                        style="color:red">*</label>
                                                    <div class="input-group mb-3"> <input type="text" id="adhaar"
                                                            class="form-control" maxlength="12" name="adhaar"
                                                            placeholder="आधार नंबर दर्ज करें">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12 text-left mt-2">
                                                <div class="form-check d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input AdharConfirm"
                                                        name="AdharConfirm" value="1" id="confirmationCheckbox"
                                                        {{ isset($data['applicant_details']) && $data['applicant_details']->AdharConfirm == '1' ? 'checked' : '' }}
                                                        style="width: 30px; height: 20px; border: 1px solid #007bff; border-radius: 4px; outline: none; 
                                                                                                        cursor: pointer;" required>
                                                    <p class="form-check-label ms-3" style="font-size: 12px; color: #000;">
                                                        <label for="confirmationCheckbox" style="cursor: pointer;">
                                                            मैं, स्वेच्छा से अपना आधार संख्या प्रदान कर रहा/रही हूँ और यह घोषित करता/करती हूँ कि मेरे द्वारा दी गई जानकारी पूर्णतः सत्य एवं सही है। मैं यह भी समझता/समझती हूँ कि मेरी आधार संबंधी जानकारी का उपयोग केवल पहचान सत्यापन (Identity Verification) के उद्देश्य से किया जाएगा, जो कि आधार अधिनियम, 2016 तथा भारतीय विशिष्ट पहचान प्राधिकरण (UIDAI) द्वारा जारी दिशा-निर्देशों के अनुरूप होगा।</label>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-lg-6 form-group">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <div class="input-group mb-3"><input type="text" id="captcha"
                                                            minlength="4" maxlength="4" class="form-control"
                                                            pattern="\d{4}" name="captcha" placeholder="कैप्चा दर्ज करें *"
                                                            aria-required="true" required>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text"><i
                                                                    class="bi bi-puzzle"></i></span></div>
                                                        </div>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" style="display: flex; justify-content: space-evenly;">
                                                <p class="svg-img captcha"><span>{!! captcha_img() !!}</span></p>
                                                &nbsp;&nbsp;&nbsp;
                                                <button type="button" class="mb-2 ml-19 reload"
                                                    style="height:43px;font-size:large; border:white; background-color:white; padding: 5px;"
                                                    id="reload">
                                                    <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                                                </button>
                                            </div>


                                        </div>


                                        <div class="form-group mt-1">
                                            <button type="submit" class="btn btn-success" id="submit"
                                                name="login-button" style="width:100%;">Submit</button>
                                        </div>

                                        <div class="m-3">
                                            <p style="position:absolute;color:red" id="error_msg"></p>
                                            <span id="Error-message" class="text-danger"></span>
                                        </div> <br>
                                    </form>
                                    <p class="mb-1" style="text-align: left;">
                                        <a href="{{ url('/login') }}" class="btn btn-outline-primary">पहले से पंजीकृत हैं
                                            ?
                                            लॉगिन करें</a>
                                        <a href="{{ url('/') }}" style="float: right;"
                                            class="btn btn-outline-primary"><i class="bi bi-house-door"></i>
                                            होम पेज पर जाएँ</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4"></div><br>

                    </div>
                </div>
            </body>
        </main>
    </body>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            //Captcha Reload
            $('#reload').click(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('/reload-captcha') }}",
                    type: 'GET',
                    success: function(response) {
                        $(".captcha span").html(response.captcha);
                        $('#captcha').val(''); // कैप्चा इनपुट को क्लियर करें
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });


            // For alphabets-only fields
            $(document).on('input', '.alphabets-only', function() {
                $(this).val(function(_, val) {
                    return val.replace(/[^\u0900-\u097Fa-zA-Z.\-\s]/g, '');
                });
            });

            // For number-only fields
            $('.number-only').on('input', function() {
                // Allow only numbers
                $(this).val(function(_, val) {
                    return val.replace(/[^0-9]/g, '');
                });
            });


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

                    if (elementName != "post" && elementName != "pehchan" && elementName != "samiti_name" &&
                        elementName != "panjiyan_no" && elementName != "total_member" && elementName !=
                        "house_no" && elementName != "demand" && elementName != "pond_name" &&
                        elementName != "khasra_no" && elementName != "rakba" && elementName != "amount")
                        if (element.length) {
                            // Remove the is-invalid class from input elements
                            element.removeClass('was-validated');
                            element.addClass('is-invalid');
                            $('.error').html("This field is required");
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



            $('#myForm').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally

                var form = new FormData(this);
                var url = $(this).attr('action'); // Get the form action URL
                var csrf_token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token value
                form.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    beforeSend: function() {
                        $("#submit").attr("disabled", true);
                        Swal.fire({
                            title: 'Processing...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(data) {
                        Swal.close(); // Close loader
                        $("#submit").attr("disabled", false);

                        if (data.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: data.message,
                                allowOutsideClick: false
                            }).then((da) => {
                                window.location.href = '/reg-otp-verify-page/' + data
                                    .encrypted_mobile;
                            });
                        } else if (data.status == 'error') {
                            // Clear previous error message
                            $('#Error-message').html('');

                            Swal.fire({
                                icon: 'warning',
                                title: data.message,
                                allowOutsideClick: false
                            });
                        } else if (data.status === 'failed') {
                            let errorMessages = '';
                            if (data.errors) {
                                $.each(data.errors, function(key, value) {
                                    errorMessages += `<li>${value[0]}</li>`;
                                });
                            } else {
                                errorMessages = `<li>${data.msg}</li>`;
                            }

                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया नीचे दी गई त्रुटियाँ ठीक करें',
                                position: 'center',
                                html: `<ul style="text-align: center; color: red;">${errorMessages}</ul>`
                            });

                            //  कैप्चा रीसेट करें
                            if (data.captcha) {
                                $(".captcha span").html(data.captcha);
                                $('#captcha').val('');
                            }
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        $("#submit").attr("disabled", false);
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया फॉर्म पुनः जांचें कुछ गलत हो गया है',
                            text: 'कृपया नेटवर्क कनेक्शन जांचें या बाद में प्रयास करें।',
                            position: 'center',
                            allowOutsideClick: false
                        });
                    }
                });


            });



        });
    </script>
@endsection
