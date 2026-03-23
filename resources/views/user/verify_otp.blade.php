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

    <body class="d-flex flex-column h-100" style="background-color: aliceblue;">
        <main role="main">

            <div>
                <div class="site-login">
                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4"><br><br>

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

                                    <h4 class="text-center login-title">
                                        <br>{{ ($is_super_admin ?? false) ? 'पासवर्ड सत्यापित करें' : 'OTP सत्यापित करें' }}
                                    </h4><br>
                                    <form method="POST" action="{{ url('/verify-user-otp') }}" id="otpForm">
                                        @csrf

                                        <input type="hidden" name="mobile"
                                            value="{{ session('sess_mobile') ?? $Registerd_Mob_Number }}">

                                        <input type="hidden" name="is_super_admin"
                                            value="{{ $is_super_admin ?? false ? 1 : 0 }}">

                                        <input type="hidden" name="redirection" id="redirection"
                                            value="{{ session('sess_mobile') ? '/login' : ($Registerd_Mob_Number ? '/role-redirection' : '') }}">

                                        <input type="hidden" name="role" id="role" value="{{ session('sess_role') }}">


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group has-feedback field-loginform-password required">
                                                    <label for="otp">Registered Mobile Number/पंजीकृत मोबाइल
                                                        नंबर</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control number-only"
                                                            value="{{ session('sess_mobile') ?? $Registerd_Mob_Number }}"
                                                            readonly disabled>
                                                        <div id="error" class="invalid-feedback">This field is required
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (!($is_super_admin ?? false))
                                                    <div class="form-group has-feedback field-loginform-password required">
                                                        <label for="otp">OTP दर्ज करें/Enter OTP</label><label
                                                            style="color:red">*</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" id="otp" class="form-control number-only"
                                                                name="otp" placeholder="OTP दर्ज करें" aria-required="true"
                                                                maxlength="6"
                                                                oninput="if (this.value.length > 6) this.value = this.value.slice(0, 6);">
                                                            <div id="error" class="invalid-feedback">This field is required
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group has-feedback field-loginform-password required">
                                                        <label for="password">पासवर्ड दर्ज करें / Enter Password</label><label
                                                            style="color:red">*</label>
                                                        <div class="input-group mb-3">
                                                            <input type="password" id="password" class="form-control"
                                                                name="password" placeholder="पासवर्ड दर्ज करें"
                                                                aria-required="true">
                                                            <div id="error" class="invalid-feedback">This field is required
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success" id="verifyBtn"
                                                style="width:100%;">{{ ($is_super_admin ?? false) ? 'लॉगिन करें' : 'सत्यापित करें' }}</button>
                                        </div>
                                    </form>
                                    @if (!($is_super_admin ?? false))
                                        <div class="row col-md-12 mt-2">
                                            <div class="col-md-6 mt-2 text-muted"> पुनः प्रयास करें <span id="timerText"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <form method="POST" id="ResendOTPForm">
                                                    @csrf
                                                    <input type="hidden" name="mobile"
                                                        value="{{ session('sess_mobile') ?? $Registerd_Mob_Number }}">

                                                    <div class="form-group mt-2 text-right">
                                                        <button type="submit" class="btn btn-sm btn-success" id="resendBtn"
                                                            style="width:100%;" disabled>
                                                            ओटीपी पुनः भेजें / Resend OTP
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif


                                    <p class="mb-1" style="text-align: left;">
                                        <a href="{{ url('/') }}" style="float: right;"><i class="bi bi-house-door"></i>
                                            होम पेज पर जाएँ</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4"></div><br><br>
                    </div>
                </div>
            </div>
            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmLoginModal" tabindex="-1" aria-labelledby="confirmLoginModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="confirmLoginModalLabel">Confirm
                                Login</h5>
                            <button type="button" class="btn-close btn-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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

        </main>
    </body>
@endsection
@section('scripts')
    <script>
        const isSuperAdmin = {{ ($is_super_admin ?? false) ? 'true' : 'false' }};

        $(document).on('submit', '#otpForm', function (e) {
            e.preventDefault();

            let redirection = $('#redirection').val();
            var form = new FormData(this);
            var url = $(this).attr('action');

            $.ajax({
                url: url,
                type: "POST",
                data: form,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                beforeSend: function () {
                    $('#verifyBtn').prop('disabled', true);
                    Swal.fire({
                        title: 'Loading...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function (data) {
                    Swal.close(); // Close loader
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            allowOutsideClick: false
                        }).then(() => {
                            let red_url = data.url || '/candidate/candidate-dashboard';
                            let base_url = "{{ url('/') }}";
                            window.location.href = base_url + red_url;
                        });
                    } else if (data.status === 'confirm') {

                        // Server indicates there's an existing active session on another device
                        // Redirect user to confirmation page
                        // Show modal-based confirmation to match theme using Bootstrap 5 API
                        var confirmUrl = data.confirm_url || '/login/confirm';
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
                    } else if (data.status === 'error') {
                        Swal.close();
                        $('#verifyBtn').prop('disabled', false);
                        // ResendOTP()
                        Swal.fire({
                            icon: 'warning',
                            title: 'OTP त्रुटि!',
                            text: data.message,
                            allowOutsideClick: false
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close(); // Close loader
                    $('#verifyBtn').prop('disabled', false);

                    let errorMessage = 'आपने निर्धारित प्रयासों की सीमा पार कर ली है। कृपया कुछ समय बाद पुनः प्रयास करें।';

                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.message) {
                            errorMessage = res.message;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'अधिक प्रयास!',
                        text: errorMessage,
                        allowOutsideClick: false
                    });
                    // ResendOTP();
                    console.log(errorMessage);
                }

                // complete: function() {
                //     $('#verifyBtn').prop('disabled', false);
                //     Swal.close();
                // },
            });
        });



        $(document).on('submit', '#ResendOTPForm', function (e) {

            $('#resendBtn').prop('disabled', true);
            e.preventDefault();
            var form = new FormData(this);
            var url = '/resend-otp';

            $.ajax({
                url: url,
                type: "POST",
                data: form,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                beforeSend: function () {
                    $('#resendBtn').prop('disabled', true);
                    Swal.fire({
                        title: 'Loading...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },

                success: function (data) {
                    Swal.close(); // Close loader
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            allowOutsideClick: false
                        }).then(() => {
                            $('#resendBtn').prop('disabled', true);
                            ResendOTP()

                        });

                    } else if (data.status === 'error') {
                        Swal.close(); // Close loader
                        Swal.fire({
                            icon: 'warning',
                            title: 'OTP त्रुटि!',
                            text: data.message,
                            allowOutsideClick: false
                        }).then(() => {
                            $('#resendBtn').prop('disabled', false);
                            // ResendOTP()

                        });
                    }
                },
                error: function (xhr) {
                    Swal.close(); // Close loader
                    console.log(xhr.responseText);
                }
            });
        });



        function ResendOTP() {
            var timer = 60; // seconds
            var $resendBtn = $('#resendBtn');
            var $timerText = $('#timerText');

            $resendBtn.prop('disabled', true); // Disable on load

            var countdown = setInterval(function () {
                if (timer > 0) {
                    $timerText.text(' 00:00:' + timer + ' sec');
                    timer--;
                } else {
                    clearInterval(countdown);
                    $resendBtn.prop('disabled', false); // Enable after timer
                    $timerText.text('');
                }
            }, 1000);
        }

        $(document).ready(function () {
            if (!isSuperAdmin) {
                ResendOTP();
            } else {
                $('#resendBtn').prop('disabled', true);
            }


            // Confirmation modal handlers
            $(document).on('click', '#confirmYes', function (e) {
                e.preventDefault();
                var modalEl = document.getElementById('confirmLoginModal');
                var url = "{{ route('login.confirm.post') }}";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'yes',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (data) {

                        if (data.status === 'success') {
                            // Redirect to role-based url
                            window.location.href = '{{ url('/') }}' + data.url;
                        } else {
                            // fallback: reload page
                            // window.location.reload();
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
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