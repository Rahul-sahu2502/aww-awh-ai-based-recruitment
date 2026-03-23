@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" />
    <style>
        #cke_notifications_area_editor {
            display: none !important;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
        }

        .position-relative {
            position: relative;
        }

        .text-danger {
            font-size: 0.875em;
        }

        .input-group-text.password-toggle {
            background-color: none;
            border-left: 0;
            padding-left: 10px;
            padding-right: 10px;
            cursor: pointer;
        }


        /* REMOVE this block from previous styles */
        .password-toggle {
            position: absolute;
            right: 0px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle mb-3">
            <h5 class="fw-bold">पासवर्ड बदलें</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">पासवर्ड बदलें</li>
                </ol>
            </nav>
        </div>

        <div class="card shadow-sm rounded">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">पासवर्ड बदलने का फॉर्म</h6>
            </div>
            <div class="card-body">
                <form id="changePasswordForm" method="POST" action="{{ url('/change-password') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">वर्तमान पासवर्ड</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                placeholder="वर्तमान पासवर्ड दर्ज करें">
                            <span class="input-group-text password-toggle" toggle="#current_password">&#128065;</span>
                        </div>
                        <span class="text-danger error" id="error_current_password"></span>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">नया पासवर्ड</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                placeholder="नया पासवर्ड दर्ज करें">
                            <span class="input-group-text password-toggle" toggle="#new_password">&#128065;</span>
                        </div>
                        <span class="text-danger error" id="error_new_password"></span>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">नया पासवर्ड पुष्टि करें</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                placeholder="नया पासवर्ड पुनः दर्ज करें">
                            <span class="input-group-text password-toggle" toggle="#confirm_password">&#128065;</span>
                        </div>
                        <span class="text-danger error" id="error_confirm_password"></span>
                    </div>


                    <div class="text-end">
                        <button type="submit" class="btn btn-success">पासवर्ड अपडेट करें</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/custom/sha512.js') }}"></script>
    <script>
        $(document).ready(function() {

            // Show/hide password
            $('.password-toggle').on('click', function() {
                const input = $($(this).attr('toggle'));
                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);
                $(this).html(type === 'password' ? '&#128065;' : '&#128064;');
            });

            // Loader div add karo
            $('body').append(`
            <div id="ajaxLoader" style="
                display:none;
                position:fixed;
                top:0; left:0; width:100%; height:100%;
                background: rgba(255,255,255,0.7);
                z-index: 9999;
                justify-content:center;
                align-items:center;
            ">
                <div class="spinner-border text-primary" role="status" style="width:4rem; height:4rem; margin:auto;">
                    <span class="visually-hidden">लोड हो रहा है...</span>
                </div>
            </div>
        `);

            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();

                let valid = true;
                const currentPassword = $('#current_password').val().trim();
                const newPassword = $('#new_password').val().trim();
                const confirmPassword = $('#confirm_password').val().trim();

                $('.error').text('');
                $('input').removeClass('is-invalid');

                if (currentPassword === '') {
                    $('#current_password').addClass('is-invalid');
                    $('#error_current_password').text('कृपया वर्तमान पासवर्ड दर्ज करें।');
                    valid = false;
                }

                if (newPassword === '') {
                    $('#new_password').addClass('is-invalid');
                    $('#error_new_password').text('कृपया नया पासवर्ड दर्ज करें।');
                    valid = false;
                } else if (newPassword.length < 6) {
                    $('#new_password').addClass('is-invalid');
                    $('#error_new_password').text('नया पासवर्ड कम से कम 6 अक्षरों का होना चाहिए।');
                    valid = false;
                }

                if (confirmPassword === '') {
                    $('#confirm_password').addClass('is-invalid');
                    $('#error_confirm_password').text('कृपया नया पासवर्ड पुष्टि करें।');
                    valid = false;
                } else if (newPassword !== confirmPassword) {
                    $('#confirm_password').addClass('is-invalid');
                    $('#error_confirm_password').text('नया पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।');
                    valid = false;
                }

                if (!valid) return;

                // LOGIN STYLE ENCRYPTION USING STATIC SALT
                const encryptionKey = '{{ Config::get('salt.STATIC_SALT') }}';
                if (currentPassword) {
                    let securePwd = encryptionKey + currentPassword + encryptionKey;
                    let shaObj = new jsSHA("SHA-512", "TEXT");
                    shaObj.update(securePwd);
                    $('#current_password').val(shaObj.getHash("HEX"));
                }

                if (newPassword) {
                    let secureNewPwd = encryptionKey + newPassword + encryptionKey;
                    let shaObjNew = new jsSHA("SHA-512", "TEXT");
                    shaObjNew.update(secureNewPwd);
                    let hashedNew = shaObjNew.getHash("HEX");
                    $('#new_password').val(hashedNew);
                    $('#confirm_password').val(hashedNew); // confirm में भी वही hash भेजें

                }



                const form = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: form,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#ajaxLoader').css('display', 'flex');
                    },
                    success: function(response) {
                        $('#ajaxLoader').hide();
                        if (response.error) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया ध्यान दें ',
                                text: response.error
                            });
                            submitBtn.prop('disabled', false);
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'सफलता',
                                text: response.message
                            }).then(() => {
                                window.location.href = '/logout';
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#ajaxLoader').hide();
                        let errMsg = 'कृपया सभी आवश्यक जानकारी भरें।';
                        
                        // Check for direct message (from middleware)
                        if (xhr.responseJSON?.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        // Check for validation errors
                        else if (xhr.responseJSON?.errors && typeof xhr.responseJSON.errors === 'object') {
                            const errors = xhr.responseJSON.errors;
                            const errorValues = Object.values(errors);
                            if (errorValues && errorValues.length > 0) {
                                errMsg = errorValues.flat().join('\n');
                            }
                        }
                        // Check for plain error property
                        else if (xhr.responseJSON?.error) {
                            errMsg = xhr.responseJSON.error;
                        }
                        
                        Swal.fire({
                            icon: 'warning',
                            title: 'ध्यान दें',
                            text: errMsg,
                            confirmButtonText: 'ठीक है'
                        });
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
