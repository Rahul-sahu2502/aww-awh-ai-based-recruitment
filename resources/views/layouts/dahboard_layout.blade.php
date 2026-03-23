<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Women And Child Development Department</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <!-- Favicons -->
    <link href="{{ asset('assets/img/cg-logo.png') }}" rel="icon">
    <link href="{{ asset('assets/img/cg-logo.png') }}" rel="apple-touch-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Government-friendly Devanagari Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&family=Noto+Sans+Devanagari:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="{{ url('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/quill/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/quill/quill.bubble.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/remixicon/remixicon.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/lib/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/lib/select2/select2.min.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="{{ url('assets/css/style16042025.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap5.min.css') }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">

    @yield('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    @if (Session::get('sess_role') === 'Candidate')
        @include('navigation.candidate_navigation')
    @elseif (Session::get('sess_role') === 'Super_admin')
        @include('navigation.admin_navigation')
    @elseif (Session::get('sess_role') === 'Admin' ||
            Session::get('sess_role') == 'Supervisor' ||
            Session::get('sess_role') == 'CDPO')
        @include('navigation.examinor_navigation')
    @endif

    @if (session('success') || session('error'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
            @if (session('success'))
                <div class="toast align-items-center text-bg-success border-0 shadow" role="alert"
                    aria-live="assertive" aria-atomic="true" data-bs-delay="4000" data-bs-autohide="true">
                    <div class="d-flex">
                        <div class="toast-body fw-semibold">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="toast align-items-center text-bg-danger border-0 shadow" role="alert"
                    aria-live="assertive" aria-atomic="true" data-bs-delay="5000" data-bs-autohide="true">
                    <div class="d-flex">
                        <div class="toast-body fw-semibold">
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @yield('body-page')
    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer mt-auto">

        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            <strong>Developed By:</strong> Department of Women and Child Development &copy; {{ date('Y') }}
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ url('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ url('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ url('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ url('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ url('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ url('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ url('assets/js/main.js') }}"></script>
    <script src="{{ url('assets/js/jquery-3.5.1.js') }}"></script>
    <script src="{{ url('assets/js/dataTables.min.js') }}"></script>
    <script src="{{ url('assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ url('assets/lib/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ url('assets/lib/select2/select2.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ensure Bootstrap JS is available (fallback to CDN if vendor script failed)
            function ensureBootstrap(callback) {
                if (window.bootstrap && window.bootstrap.Toast) {
                    callback();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
                script.async = true;
                script.onload = callback;
                script.onerror = function () {
                    console.warn('Bootstrap JS failed to load; toasts will be skipped.');
                };
                document.head.appendChild(script);
            }

            ensureBootstrap(function () {
                // Auto-show any Bootstrap toasts (success/error flashes) with autohide
                const toastElList = [].slice.call(document.querySelectorAll('.toast'));
                toastElList.forEach(function (toastEl) {
                    if (window.bootstrap && window.bootstrap.Toast) {
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    }
                });
            });
        });
    </script>

    <style>
        /* Enhanced menu highlighting */
        .sidebar-nav .nav-link:not(.collapsed) {
            color: #4154f1 !important;
            background: #f6f9ff !important;
        }

        /* Global Alert Banner for Throttle Messages */
        .global-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #dc3545;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            font-size: 16px;
            font-weight: 500;
            min-width: 300px;
            text-align: center;
            opacity: 1;
            transition: opacity 0.6s ease;
        }


        .sidebar-nav .nav-link:not(.collapsed) i {
            color: #4154f1 !important;
        }

        .sidebar-nav .nav-content a.active {
            color: #4154f1 !important;
            background: #f6f9ff !important;
        }

        .sidebar-nav .nav-content a.active i {
            background-color: #4154f1 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currentUrl = window.location.pathname;

            // Helper function to activate submenu item
            function activateSubmenuItem(selector) {
                const item = document.querySelector(selector);
                if (item) {
                    item.classList.add('active');
                    // Also remove collapsed from any parent menu
                    let parent = item.closest('.nav-content');
                    if (parent) {
                        const parentToggle = document.querySelector(`[data-bs-target="#${parent.id}"]`);
                        if (parentToggle) {
                            parentToggle.classList.remove('collapsed');
                            parent.classList.add('show');
                        }
                    }
                }
            }

            //### Candidate Active Module
            if (currentUrl.includes('/candidate/candidate-dashboard')) {
                const el = document.getElementById('dashboard-id');
                if (el) el.classList.remove('collapsed');
            }

            if (currentUrl.includes('/candidate/submitted-applications')) {
                const el = document.getElementById('application-list-id');
                if (el) el.classList.remove('collapsed');
            }

            if (currentUrl.includes('/candidate/pending-applications')) {
                const el = document.getElementById('pending-application-list-id');
                if (el) el.classList.remove('collapsed');
            }

            // Candidate application related pages
            if (currentUrl.includes('/candidate/All-documents') ||
                currentUrl.includes('/candidate/view-documents') ||
                currentUrl.includes('/candidate/view-application-detail') ||
                currentUrl.includes('/candidate/final-application-detail') ||
                currentUrl.includes('/candidate/view-docs') ||
                currentUrl.includes('/candidate/view-user-detail')) {
                const el = document.getElementById('application-list-id');
                if (el) el.classList.remove('collapsed');
            }

            if (currentUrl.includes('/candidate/user-details-form') ||
                currentUrl.includes('/candidate/advertisement-list') ||
                currentUrl.includes('/candidate/post-list')) {
                const el = document.getElementById('user-form-id');
                if (el) el.classList.remove('collapsed');
            }

            // Candidate Feedback
            if (currentUrl.includes('/candidate/feedback')) {
                const el = document.getElementById('candidate-feedback');
                if (el) {
                    el.classList.remove('collapsed');
                    el.classList.add('active');
                }
            }

            //Candidate Dava Apatti 
            if (
                currentUrl.includes('/candidate/dava-apatti')) {
                const el = document.getElementById('dava-apatti-id');
                if (el) {
                    el.classList.remove('collapsed');
                    el.classList.add('active');
                }
            }

            //### Admin Active Module

            // Dashboard
            if (currentUrl.includes('/admin/admin-dashboard')) {
                const el = document.getElementById('admin-dashboard');
                if (el) el.classList.remove('collapsed');
            }

            // Applications - Main menu and related pages
            if (currentUrl.includes('/admin/application-list') ||
                currentUrl.includes('/admin/view-application-detail') ||
                currentUrl.includes('/admin/final-application-detail') ||
                currentUrl.includes('/admin/dashboard/view-application-detail') ||
                currentUrl.includes('/admin/view-docs') ||
                currentUrl.includes('/admin/distWise-application-list')) {
                const el = document.getElementById('admin-applications');
                if (el) el.classList.remove('collapsed');
            }

            // Marks Entry - Main menu and submenu
            if (currentUrl.includes('/admin/marks-entry')) {
                const el = document.getElementById('admin-masrks-entry');
                const submenu = document.getElementById('forms-nav1');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/marks-entry"]');
            }

            if (currentUrl.includes('/admin/merit-list')) {
                const el = document.getElementById('admin-masrks-entry');
                const submenu = document.getElementById('forms-nav1');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/merit-list"]');
            }

            // Reports - Main menu and submenu  
            if (currentUrl.includes('/admin/applications-list') ||
                currentUrl.includes('/admin/district-wise-applications-list-export')) {
                const el = document.getElementById('admin-reports');
                const submenu = document.getElementById('forms-nav3');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/applications-list"]');
            }

            if (currentUrl.includes('/admin/verified-list') ||
                currentUrl.includes('/admin/verified-list-export')) {
                const el = document.getElementById('admin-reports');
                const submenu = document.getElementById('forms-nav3');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/verified-list"]');
            }

            if (currentUrl.includes('/admin/rejected-list') ||
                currentUrl.includes('/admin/rejected-list-export')) {
                const el = document.getElementById('admin-reports');
                const submenu = document.getElementById('forms-nav3');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/rejected-list"]');
            }

            // Settings Menu - Advertisement
            if (currentUrl.includes('/admin/admin-advertisment')) {
                const el = document.getElementById('admin-adverties');
                const submenu = document.getElementById('advertisementSubMenu');
                const parentMenu = document.getElementById('settingsMenu');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/admin-advertisment"]');
            }

            if (currentUrl.includes('/admin/show-advertisment')) {
                const el = document.getElementById('admin-adverties');
                const submenu = document.getElementById('advertisementSubMenu');
                const parentMenu = document.getElementById('settingsMenu');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/show-advertisment"]');
            }

            // Settings Menu - Posts
            if (currentUrl.includes('/admin/admin-post')) {
                const el = document.getElementById('admin-posts');
                const submenu = document.getElementById('postsSubMenu');
                const parentMenu = document.getElementById('settingsMenu');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/admin-post"]');
            }

            if (currentUrl.includes('/admin/show-posts')) {
                const el = document.getElementById('admin-posts');
                const submenu = document.getElementById('postsSubMenu');
                const parentMenu = document.getElementById('settingsMenu');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/show-posts"]');
            }

            if (currentUrl.includes('/admin/static-posts')) {
                const el = document.getElementById('admin-posts');
                const submenu = document.getElementById('postsSubMenu');
                const parentMenu = document.getElementById('settingsMenu');
                if (el) el.classList.remove('collapsed');
                if (submenu) submenu.classList.add('show');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/static-posts"]');
            }

            // ssp-portal-data
            if (currentUrl.includes('/admin/ssp-portal-data')) {
                const el = document.getElementById('ssp-portal-data');
                if (el) {
                    el.classList.remove('collapsed');
                    el.classList.add('active');
                }
            }

            // Dava Apatti - Main menu and related pages
            // if (
            //     currentUrl.includes('/admin/dava-apatti-list') ||currentUrl.includes('/admin/view-dava-apatti')
            // ) {
            //     const el = document.getElementById('dava-apatti-list-id');
            //     if (el) {
            //         el.classList.remove('collapsed');
            //         el.classList.add('active');
            //     }
            // }
            // दावा आपत्ति निराकरण - Parent & Child Menu
            if (
                currentUrl.includes('/admin/dava-apatti-list') ||
                currentUrl.includes('/admin/anantim-list') ||
                currentUrl.includes('/admin/antim-list') ||
                currentUrl.includes('/admin/view-dava-apatti')
            ) {
                // Parent <a> tag
                const parentLink = document.querySelector(
                    'a[data-bs-target="#forms-nav4"]'
                );

                // Parent <ul>
                const parentUl = document.getElementById('forms-nav4');

                if (parentLink && parentUl) {
                    parentLink.classList.remove('collapsed');
                    parentLink.classList.add('active');

                    parentUl.classList.add('show');
                }

                // Sub-menu active handling
                const subLinks = parentUl.querySelectorAll('a');
                subLinks.forEach(link => {
                    if (link.getAttribute('href') && currentUrl.includes(link.getAttribute('href'))) {
                        link.classList.add('active');
                    }
                });
            }
            // CDPO List
            if (
                currentUrl.includes('/admin/cdpo-list')) {
                const el = document.getElementById('cdpo-list');
                if (el) {
                    el.classList.remove('collapsed');
                    el.classList.add('active');
                }
            }
            // Settings Menu - Skills
            if (currentUrl.includes('/admin/add-skills')) {
                const parentMenu = document.getElementById('settingsMenu');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/add-skills"]');
            }

            // Settings Menu - Subjects
            if (currentUrl.includes('/admin/add-subjects')) {
                const parentMenu = document.getElementById('settingsMenu');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/add-subjects"]');
            }

            // Settings Menu - Weightage Management
            if (currentUrl.includes('/admin/weightage-management') ||
                currentUrl.includes('/weightage-management') ||
                currentUrl.includes('admin.weightage')) {
                const parentMenu = document.getElementById('settingsMenu');
                if (parentMenu) parentMenu.classList.add('show');
                activateSubmenuItem('a[href*="weightage"]');
            }

            //### Examinor Active Module

            // Examinor Dashboard
            if (currentUrl.includes('/examinor/examinor-dashboard')) {
                const el = document.getElementById('examinor-dashboard');
                if (el) el.classList.remove('collapsed');
            }

            // For examinor navigation - application list and related pages
            if (currentUrl.includes('/admin/application-list') ||
                currentUrl.includes('/admin/view-application-detail') ||
                currentUrl.includes('/admin/final-application-detail') ||
                currentUrl.includes('/admin/view-docs') ||
                currentUrl.includes('/admin/distWise-application-list')) {
                // Check if we're in examinor context
                const examinorApp = document.getElementById('admin-applications');
                if (examinorApp) examinorApp.classList.remove('collapsed');
            }

            // For examinor navigation - marks and merit list
            if (currentUrl.includes('/admin/marks-entry')) {
                const examinorMarks = document.getElementById('examinor-marks-entry');
                const marksSubmenu = document.getElementById('forms-nav1');

                if (examinorMarks) examinorMarks.classList.remove('collapsed');
                if (marksSubmenu) marksSubmenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/marks-entry"]');
            }

            if (currentUrl.includes('/admin/merit-list')) {
                const examinorMarks = document.getElementById('examinor-marks-entry');
                const examinorMerit = document.getElementById('examinor-merit-list');
                const marksSubmenu = document.getElementById('forms-nav1');

                if (examinorMarks) examinorMarks.classList.remove('collapsed');
                if (examinorMerit) examinorMerit.classList.remove('collapsed');
                if (marksSubmenu) marksSubmenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/merit-list"]');
            }

            // For examinor navigation - reports
            if (currentUrl.includes('/admin/verified-list') ||
                currentUrl.includes('/admin/verified-list-export')) {
                const examinorReports = document.getElementById('examinor-reports');
                const reportsSubmenu = document.getElementById('forms-nav3');

                if (examinorReports) examinorReports.classList.remove('collapsed');
                if (reportsSubmenu) reportsSubmenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/verified-list"]');
            }

            if (currentUrl.includes('/admin/rejected-list') ||
                currentUrl.includes('/admin/rejected-list-export')) {
                const examinorReports = document.getElementById('examinor-reports');
                const reportsSubmenu = document.getElementById('forms-nav3');

                if (examinorReports) examinorReports.classList.remove('collapsed');
                if (reportsSubmenu) reportsSubmenu.classList.add('show');
                activateSubmenuItem('a[href*="/admin/rejected-list"]');
            }

            // Fallback: If we haven't matched any specific pattern, try to match by href
            // This helps catch any routes we might have missed
            if (!document.querySelector('.nav-link:not(.collapsed)') && !document.querySelector(
                    '.nav-content a.active')) {
                const allLinks = document.querySelectorAll('.sidebar-nav a[href]');
                allLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && currentUrl.includes(href.split('/').pop())) {
                        // For main menu items
                        if (link.classList.contains('nav-link')) {
                            link.classList.remove('collapsed');
                        }
                        // For submenu items
                        else if (link.closest('.nav-content')) {
                            link.classList.add('active');
                            const parentSubmenu = link.closest('.nav-content');
                            if (parentSubmenu) {
                                const parentToggle = document.querySelector(
                                    `[data-bs-target="#${parentSubmenu.id}"]`);
                                if (parentToggle) {
                                    parentToggle.classList.remove('collapsed');
                                    parentSubmenu.classList.add('show');
                                }
                            }
                        }
                    }
                });
            }

        });

        // DSC Enable Ajax Call
        var dscRegisterStatus = 0;
        $('#enableDscBtn').click(function(e) {
            e.preventDefault();
            enableDscBtn();
            // return;
        });

        function enableDscBtn() {
            $.ajax({
                url: "http://localhost:8800/checkservice",
                type: 'GET',
                async: false,
                success: function(response) {
                    // console.log(response);

                    if (response.isServiceRunning === true) {
                        $.ajax({
                            url: "http://localhost:8800/registercertificate",
                            type: 'GET',
                            async: false,
                            success: function(res) {
                                // console.log(res.hasPrivateKey, res['hasPrivateKey']);
                                if (res.hasPrivateKey === true) {
                                    dscRegisterAjax(res);
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Error',
                                    text: 'Please try again'
                                });
                                return;
                            }
                        });

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'DSC Service Not Running',
                            text: response.message || 'Unable to connect to DSC service.'
                        });
                        return;
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Please try again'
                    });
                    return;
                }
            });
        }

        function dscRegisterAjax(req) {
            $.ajax({
                url: "{{ url('admin/dsc-register') }}",
                data: req,
                type: 'POST',
                dataType: "json",
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // console.log(response);
                    dscRegisterStatus = response.status;
                    if (response.status === 1 || response.status === 3) {
                        Swal.fire({
                            icon: 'success',
                            title: response.msg || 'DSC Registered Successfully',
                            // text: response.msg || 'Digital Signature Certificate (DSC) registered successfully.'
                        });
                    } else if (response.status === 2) {
                        Swal.fire({
                            icon: 'warning',
                            title: response.msg || 'DSC Registration Failed Ajax',
                            // text: response.msg
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Please try again'
                    });
                    return;
                }
            });
        };
    </script>

    <script>
        // Global AJAX Error Handler
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            // Handle 429 Throttle Errors (Rate Limiting)
            if (jqxhr.status === 429 && jqxhr.responseJSON && jqxhr.responseJSON.alert_message) {
                showGlobalAlert(jqxhr.responseJSON.alert_message);
                return;
            }

            // Handle 422 Validation Errors (File Upload Security, Form Validation, etc.)
            if (jqxhr.status === 422) {
                let errorMessage = 'कृपया सभी आवश्यक जानकारी भरें।';

                if (jqxhr.responseJSON) {
                    // Check for direct message property
                    if (jqxhr.responseJSON.message) {
                        errorMessage = jqxhr.responseJSON.message;
                    }
                    // Check for validation errors object
                    else if (jqxhr.responseJSON.errors && typeof jqxhr.responseJSON.errors === 'object') {
                        const errors = jqxhr.responseJSON.errors;
                        const errorValues = Object.values(errors);
                        if (errorValues && errorValues.length > 0) {
                            const firstError = errorValues[0];
                            errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                        }
                    }
                    // Check for plain error property
                    else if (jqxhr.responseJSON.error) {
                        errorMessage = jqxhr.responseJSON.error;
                    }
                }

                // Show SweetAlert with error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ध्यान दें',
                        text: errorMessage,
                        confirmButtonText: 'ठीक है',
                        confirmButtonColor: '#ffc107',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                } else {
                    alert(errorMessage);
                }

            } // Note: 500 errors are handled by custom error page (resources/views/errors/500.blade.php)
        });

        // Global Alert Function (For Custom Alerts)
        function showGlobalAlert(message) {
            let alertDiv = document.createElement('div');
            alertDiv.className = 'global-alert';
            alertDiv.innerText = message;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 600);
            }, 10000); // 10 seconds display time
        }
    </script>

    @yield('scripts')
</body>

</html>
