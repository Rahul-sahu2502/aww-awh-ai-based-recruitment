@extends('layouts.login_layout')

@section('styles')
    <style>
        /* Modal Styling */
        .form-check-input[type="radio"] {
            border: 1px solid #000;
        }

        #confirmLoginModal .modal-content {
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            border: none;
            overflow: hidden;
        }

        .shadow-radio {
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.6);
            transition: box-shadow 0.2s ease-in-out;
        }

        .shadow-radio:checked {
            box-shadow: 0 2px 8px rgba(44, 6, 235, 0.6);
            /* bootstrap primary */
        }

        /* Header gradient color */
        #confirmLoginModal .modal-header {
            background: linear-gradient(135deg, #14375F, #0A6C7D);
            color: #ffffff;
            padding: 18px 20px;
        }

        /* Body text */
        #confirmLoginModal .modal-body {
            font-size: 20px;
            color: #333;
            padding: 22px;
            line-height: 1.6;
        }

        /* Footer buttons area */
        #confirmLoginModal .modal-footer {
            border-top: none;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
        }

        /* Cancel button */
        #confirmNo {
            background-color: #6c757d;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 18px;
            transition: 0.3s;
        }

        #confirmNo:hover {
            background-color: #555;
        }

        /* Yes button */
        #confirmYes {
            background: linear-gradient(135deg, #d9363e, #b72027);
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 18px;
            box-shadow: 0 4px 8px rgba(217, 54, 62, 0.35);
            transition: 0.3s;
        }

        #confirmYes:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(217, 54, 62, 0.5);
        }
    </style>
@endsection
<link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">


@section('main-content')

    <body class="d-flex flex-column h-100">
        <main role="main">

            <body style="background-color: aliceblue;">
                <div class="site-login">


                    <div class="row ">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4"><br><br>
                            <div class="card login-box">
                                <div class="card-header logo" style="background-color: #14375F;height: auto;">
                                    <div class=logo-icon><img src="{{ asset('assets/img/landingpage_img/cg-logo.svg') }}">
                                    </div>
                                    <div class=logo-title>
                                        <h1>ई-भर्ती</h1>
                                        <p>महिला एवं बाल विकास विभाग</p>
                                    </div>
                                </div>
                                <div class="card-body login-card-body">
                                    <h4 class="text-center login-title"><br>लॉग इन / Log In</h4><br>
                                    <form id="loginForm" action="{{ url('/admin/validate-user') }}" method="post">
                                        @csrf
                                        <div class="row mt-2 mb-2">
                                            <div class="col-lg-12 form-group d-flex">
                                                <p>लॉगिन प्रकार चुनें :</p>

                                                <div class="form-check ms-4">
                                                    <input class="form-check-input shadow-radio" type="radio"
                                                        name="loginType" value="candidate_type" id="CandidateRadio" checked
                                                        required>
                                                    <label class="form-check-label mt-2" for="CandidateRadio">
                                                        Candidate
                                                    </label>
                                                </div>
                                                <div class="form-check ms-4">
                                                    <input class="form-check-input shadow-radio" type="radio"
                                                        name="loginType" value="admin_type" id="AdminRadio" required>
                                                    <label class="form-check-label mt-2" for="AdminRadio">
                                                        DPO/CDPO
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12 form-group">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="username" class="form-control"
                                                            name="username" placeholder="मोबाइल नंबर दर्ज करें *"
                                                            aria-required="true" required>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text"><i
                                                                    class="bi bi-person"></i></span></div>
                                                        </div>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 form-group" id="passwordDiv" style="display: none">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <div class="input-group mb-3"><input type="password" id="password-input"
                                                            class="form-control" name="password"
                                                            placeholder="पासवर्ड दर्ज करें *" aria-required="true">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text"><i class="bi bi-eye-slash"
                                                                    id="togglePassword"></i></span>
                                                            </div>
                                                        </div>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6 form-group">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <div class="input-group mb-3"><input type="text" id="captcha"
                                                            minlength="4" maxlength="4" class="form-control" pattern="\d{4}"
                                                            name="captcha" placeholder="कैप्चा दर्ज करें *"
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
                                        <div class="form-group">
                                            <button type="submit" id="submit" class="btn btn-success login-button"
                                                name="login-button" style="width:100%;">Login</button>
                                        </div>
                                        <p style="position:absolute;color:red" id="error_msg"></p>
                                        <br><br>
                                    </form>
                                    <!-- Confirmation Modal -->
                                    <div class="modal fade" id="confirmLoginModal" tabindex="-1"
                                        aria-labelledby="confirmLoginModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-white" id="confirmLoginModalLabel">Confirm
                                                        Login</h5>
                                                    <button type="button" class="btn-close btn-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>आप पहले से किसी अन्य डिवाइस/ब्राउज़र में लॉगिन है।
                                                        क्या आप पुराने लॉगिन सत्र को बंद करके नया लॉगिन जारी रखना चाहते हैं?
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" id="confirmNo">रद्द
                                                        करें</button>
                                                    <button type="button" class="btn btn-danger" id="confirmYes">हाँ,
                                                        जारी रखें</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    {{--
                                </div>
                                <div class="row"> --}}
                                    <p class="mb-0" style="text-align: left;">
                                        <a href="{{ url('/add-new-user') }}" class="btn btn-outline-primary">नए आवेदनकर्ता
                                            पंजीकरण करें</a>
                                        <a href="{{ url('/') }}" style="float: right;" class="btn btn-outline-primary"><i
                                                class="bi bi-house-door"></i>
                                            होम पेज पर जाएँ</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4"></div>

                    </div>
                </div>
            </body>

        </main>
    </body>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/custom/sha512.js') }}"></script>
    <script>
        $(document).on('click', '#togglePassword', function () {
            const passwordInput = $('#password-input');
            const icon = $(this);

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            }
        });
        $(document).ready(function () {

            $('#reload').click(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('/reload-captcha') }}",
                    type: 'GET',
                    success: function (response) {
                        $(".captcha span").html(response.captcha);
                        $('#captcha').val(''); // कैप्चा इनपुट को क्लियर करें
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });



            // Function to toggle password field visibility
            function togglePasswordField() {
                const loginType = $('input[name="loginType"]:checked').val();
                const passwordDiv = $('#passwordDiv');

                if (loginType === 'candidate_type') {
                    // passwordDiv.hide();
                    // $('#password').removeAttr('required');
                    $('#username').attr('placeholder', 'मोबाइल नंबर दर्ज करें *');
                    $('#username').attr('minlength', '10');
                    $('#username').attr('maxlength', '10');
                    $('#username').attr('pattern', '[6-9]{1}[0-9]{9}');
                } else if (loginType === 'admin_type') {
                    // passwordDiv.show();
                    $('#username').removeAttr('minlength');
                    $('#username').removeAttr('maxlength');
                    $('#username').removeAttr('pattern');
                    $('#password').attr('required', 'required');
                    $('#username').attr('placeholder', 'यूजर आईडी दर्ज करें *');
                }
            }

            // Initial check on page load
            togglePasswordField();

            // Event listener for radio button change
            $('input[name="loginType"]').on('change', function () {
                togglePasswordField();
            });

        });



        $(document).ready(function () {

            //to add and remove class from input feild
            $('form input').keyup(function () {
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


            $('#loginForm').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('span.error').text('');
                var hash = '';
                let password = $('#password-input').val();

                if (password) {
                    const encryptionKey = '{{ Config::get('salt.STATIC_SALT') }}';
                    let securePwd = encryptionKey + password + encryptionKey;
                    let shaObj = new jsSHA("SHA-512", "TEXT");
                    shaObj.update(securePwd);
                    var hash = shaObj.getHash("HEX");
                    $('#password-input').val(hash);
                }
                // console.log(hash);
                // Move FormData creation after encryption
                var url = $(this).attr('action');
                var form = new FormData(this);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        $("#submit").attr("disabled", true);
                        // Show a loading spinner or message if needed
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    // complete: function() {
                    //     $("#submit").attr("disabled", false);
                    //     // Hide the loading spinner or message if needed
                    //     Swal.close();
                    // },
                    success: function (response) {
                        $("#submit").attr("disabled", false);
                        // console.log("Error response:", response);
                        Swal.close();

                        if (response.status == 'success') {
                            let red_url = response.url;
                            location.href = '{{ url('/') }}' + red_url;
                        } else if (response.status == 'OtpVerify') {
                            window.location.href = '/login-otp-verify-page/' + response
                                .encrypted_mobile;
                        } else if (response.status == 'confirm') {
                            // Server indicates there's an existing active session on another device
                            // Redirect user to confirmation page
                            // Show modal-based confirmation to match theme using Bootstrap 5 API
                            var confirmUrl = response.confirm_url || '/login/confirm';
                            var modalEl = document.getElementById('confirmLoginModal');
                            if (modalEl) {
                                modalEl.dataset.confirmUrl = confirmUrl;
                                var bsModal = bootstrap.Modal.getInstance(modalEl) ||
                                    new bootstrap.Modal(modalEl, {
                                        backdrop: 'static',
                                        keyboard: false
                                    });
                                bsModal.show();
                            }
                        } else if (response.status == 'failed') {
                            if (response.errors)
                                $('#error_msg').html(response.errors.captcha[0]);
                            else
                                $('#error_msg').html(response.msg);
                            $(".captcha span").html(response.captcha);
                            $('#captcha').val('');
                            $('#password-input').val('');
                        }
                    },
                    error: function (xhr) {
                        Swal.close();
                        console.log(xhr);
                    }
                });
            });

            // Confirmation modal handlers
            $(document).on('click', '#confirmYes', function (e) {
                e.preventDefault();
                var modalEl = document.getElementById('confirmLoginModal');
                var url = (modalEl && modalEl.dataset.confirmUrl) ? modalEl.dataset.confirmUrl :
                    '/login/confirm';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'yes',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.status === 'success') {
                            // Redirect to role-based url
                            window.location.href = '{{ url('/') }}' + resp.url;
                        } else {
                            // fallback: reload page
                            window.location.reload();
                        }
                    },
                    error: function () {
                        window.location.reload();
                    }
                });
            });

            $(document).on('click', '#confirmNo', function (e) {
                e.preventDefault();
                var modalEl = document.getElementById('confirmLoginModal');
                var url = (modalEl && modalEl.dataset.confirmUrl) ? modalEl.dataset.confirmUrl :
                    '/login/confirm';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'no',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (resp) {
                        // keep user on login page and show message
                        window.location.href = '/login';
                    },
                    error: function () {
                        window.location.href = '/login';
                    }
                });
            });

        });
    </script>
@endsection