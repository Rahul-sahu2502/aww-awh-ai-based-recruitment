<style>
    a {
        text-decoration-line: none;
    }

    .logo {
        display: flex;
        flex-direction: row;
        /* Keep the image and text container side by side */
        align-items: center;
        /* Vertically center the image and text */
        text-decoration: none;
        /* Remove underline from the link */
    }

    .logo img {
        margin-right: 10px;
        /* Space between the image and text */
    }

    .logo h5,
    .logo p {
        margin: 0;
        /* Remove default margins */
    }

    .logo h5 {
        font-size: 1.5rem;
        /* Adjust size as needed */
        font-weight: bold;
        color: #000;
        /* Adjust color as needed */
    }

    .logo p {
        font-size: 0.8rem;
        /* Adjust size as needed */
        color: #555;
        /* Adjust color as needed */
    }

    /* Ensure the text container stacks h5 and p vertically */
    .logo-text-container {
        display: flex;
        flex-direction: column;
        /* Stack h5 and p vertically */
    }

    /* Notification styles */
    .notification-icon {
        position: relative;
        cursor: pointer;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        animation: blink 1s infinite;
    }

    @keyframes blink {

        0%,
        50% {
            opacity: 1;
        }

        51%,
        100% {
            opacity: 0.3;
        }
    }

    .notification-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 400px;
        max-width: 95vw;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
    }

    /* Responsive design for notification dropdown */
    @media (max-width: 768px) {
        .notification-dropdown {
            width: 320px;
            max-width: 90vw;
            right: -10px;
        }
    }

    @media (max-width: 480px) {
        .notification-dropdown {
            width: 280px;
            max-width: 85vw;
            right: -20px;
            max-height: 350px;
        }
    }

    @media (max-width: 360px) {
        .notification-dropdown {
            width: 250px;
            max-width: 80vw;
            right: -30px;
            max-height: 300px;
        }
    }

    .notification-dropdown.show {
        display: block;
    }

    .notification-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background-color: #f8f9fa;
        font-weight: bold;
        color: #495057;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
        border-left: 3px solid #007bff;
    }

    /* Responsive padding and text for mobile devices */
    @media (max-width: 768px) {
        .notification-header {
            padding: 12px 15px;
            font-size: 14px;
        }

        .notification-item {
            padding: 12px 15px;
        }

        .notification-title {
            font-size: 14px;
        }

        .notification-details {
            font-size: 12px;
        }

        .notification-meta {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .notification-header {
            padding: 10px 12px;
            font-size: 13px;
        }

        .notification-item {
            padding: 10px 12px;
        }

        .notification-title {
            font-size: 13px;
        }

        .notification-details {
            font-size: 11px;
        }

        .notification-meta {
            font-size: 10px;
        }
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-title {
        font-weight: 600;
        color: #212529;
        margin-bottom: 5px;
    }

    .notification-details {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .notification-meta {
        font-size: 12px;
        color: #868e96;
    }

    .notification-empty {
        padding: 30px 20px;
        text-align: center;
        color: #6c757d;
        font-style: italic;
    }

    /* Responsive empty notification message */
    @media (max-width: 768px) {
        .notification-empty {
            padding: 25px 15px;
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .notification-empty {
            padding: 20px 12px;
            font-size: 13px;
        }
    }

    .pending-count {
        background-color: #dc3545;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: bold;
        margin-left: 5px;
    }

    /* Responsive design for pending count */
    @media (max-width: 768px) {
        .pending-count {
            display: block;
            margin-left: 0;
            margin-top: 3px;
            width: fit-content;
        }
    }
</style>


<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ url('admin/admin-dashboard') }}" class="logo d-flex align-items-center">
            {{-- <img src="{{url('assets/img/dpo.png')}}" alt="" style="width: 45px;"> --}}
            <img src="{{ url('assets/img/logonew.png') }}" alt="" style="width: 220px;">
            {{-- <span class="d-none d-lg-block">PWD</span> --}}
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <!-- Notification Bell -->
            <li class="nav-item dropdown pe-3">
                <div class="notification-icon" id="notificationIcon">
                    <i class="bi bi-bell fs-4 text-dark"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>

                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <i class="bi bi-bell me-2"></i>अनुमोदन सूचनाएं
                        </div>
                        <div id="notificationContent">
                            <div class="notification-empty">
                                सूचनाएं लोड हो रही हैं...
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ url('assets/img/user_profile.jfif') }}" alt="Profile" class="rounded-circle"
                        style="height: 25px;">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ session()->get('sess_fname') }}
                        ({{ session('designation') }})</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    @if (Session::get('sess_role') === 'Super_admin')
                        <li>
                            <button class="dropdown-item d-flex align-items-center" type="button" id="openProjectSwitch">
                                <i class="bi bi-shuffle"></i>
                                <span>प्रोजेक्ट बदलें</span>
                            </button>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>
                    {{-- <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('/change-password') }}">
                            <i class="bi bi-lock"></i>
                            <span>Change Password</span>
                        </a>
                    </li> --}}
                    @if (session('district_id') != 0)
                        {{-- <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('admin/admin-advertisment') }}">
                                id="enableDscBtn">
                                <i class="bi bi-shield-check"></i>
                                <span>Register/Enable DSC</span>
                            </a>
                        </li> --}}
                    @endif
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar d-flex flex-column justify-content-between">

    @php
        $session_district_id = Session::get('district_id');
        $session_role = Session::get('role');
    @endphp


    {{-- for cdpo login --}}
    @if ($session_district_id)
        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/admin-dashboard') }}" id="admin-dashboard">
                    <i class="bi bi-grid"></i>
                    @if (session('project_name'))
                        <span>डैशबोर्ड ({{ session('project_name') }})</span>
                    @else
                        <span>डैशबोर्ड</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/application-list') }}" id="admin-applications">
                    <i class="bi bi-list"></i><span>आवेदन की सूची</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav1" data-bs-toggle="collapse" href="#"
                    id="admin-masrks-entry">
                    <i class="bi bi-journal"></i><span>अंक प्रविष्टि</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ url('/admin/marks-entry') }}">
                            <i class="bi bi-circle"></i><span>अंकों की प्रविष्टि</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/merit-list') }}">
                            <i class="bi bi-circle"></i><span>मेरिट लिस्ट</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav3" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-file-earmark-text"></i><span>रिपोर्ट्स</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">

                    <li>
                        <a href="{{ url('/admin/verified-list') }}">
                            <i class="bi bi-circle"></i><span>पात्र अभ्यार्थी </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/rejected-list') }}">
                            <i class="bi bi-circle"></i><span>अपात्र अभ्यार्थी </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/application-list') }}">
                            <i class="bi bi-circle"></i><span>सभी आवेदन</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- सेटिंग्स मेन्यू -->
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#settingsMenu" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-gear"></i>
                    <span>सेटिंग्स</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="settingsMenu" class="nav-content collapse" data-bs-parent="#sidebar-nav"
                    style="padding-left: 15px;">


                    <!-- विज्ञापन -->
                    <li>
                        <a class="nav-link collapsed" data-bs-target="#advertisementSubMenu" data-bs-toggle="collapse"
                            href="#" id="admin-adverties">
                            <i class="bi bi-journal-text"></i>
                            <span>विज्ञापन</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="advertisementSubMenu" class="nav-content collapse" data-bs-parent="#settingsMenu"
                            style="padding-left: 15px;">
                            <li>
                                <a href="{{ url('/admin/admin-advertisment') }}">
                                    <i class="bi bi-circle"></i>
                                    <span>नया विज्ञापन जोड़ें</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/show-advertisment') }} ">
                                    <i class="bi bi-circle"></i>
                                    <span>विज्ञापन की सूची</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- पोस्ट  -->
                    <li>
                        <a class="nav-link collapsed" data-bs-target="#postsSubMenu" data-bs-toggle="collapse" href="#"
                            id="admin-posts">
                            <i class="bi bi-journal-text"></i>
                            <span>पोस्ट</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="postsSubMenu" class="nav-content collapse" data-bs-parent="#settingsMenu"
                            style="padding-left: 15px;">
                            <li>
                                <a href="{{ url('/admin/admin-post') }}">
                                    <i class="bi bi-circle"></i>
                                    <span>नया पोस्ट जोड़ें</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/show-posts') }}">
                                    <i class="bi bi-circle"></i>
                                    <span>पोस्ट की सूची</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ url('/admin/add-gram-panchayat') }}">
                            <i class="bi bi-circle"></i>
                            <span>ग्राम पंचायत मास्टर</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/add-village') }}">
                            <i class="bi bi-circle"></i>
                            <span>ग्राम (Village) मास्टर</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/add-nagar-nikay') }}">
                            <i class="bi bi-circle"></i>
                            <span>नगर निकाय मास्टर</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/add-ward') }}">
                            <i class="bi bi-circle"></i>
                            <span>वार्ड मास्टर</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/ssp-portal-data') }}" id="ssp-portal-data">
                    <i class="bi bi-database"></i><span>SSP Portal Vacancy Data</span>
                </a>
            </li>

            {{-- <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/dava-apatti-list') }}" id="dava-apatti-list-id">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <span>दावा आपत्ति निराकरण</span>
                </a>
            </li> --}}

            <!--दावा आपत्ति Nav -->
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav4" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-exclamation-octagon-fill"></i><span>दावा आपत्ति </span><i
                        class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav4" class="nav-content collapse " data-bs-parent="#sidebar-nav">


                    <li>
                        <a href="{{ url('/admin/anantim-list') }}">
                            <i class="bi bi-circle"></i><span>अनंतिम सूची </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/dava-apatti-list') }}">
                            <i class="bi bi-circle"></i><span>दावा आपत्ति निराकरण</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/antim-list') }}">
                            <i class="bi bi-circle"></i><span>अंतिम सूची</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    @endif

    {{-- end cdpo login --}}

    {{-- for state login --}}
    @if (!$session_district_id || $session_district_id == 0)
        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/admin-dashboard') }}" id="admin-dashboard">
                    <i class="bi bi-grid"></i>
                    @if (session('project_name'))
                        <span>डैशबोर्ड ({{ session('project_name') }})</span>
                    @else
                        <span>डैशबोर्ड</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/application-list') }}" id="admin-applications">
                    <i class="bi bi-list"></i><span>आवेदन की सूची</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav3" data-bs-toggle="collapse" href="#"
                    id="admin-reports">
                    <i class="bi bi-file-earmark-text"></i><span>रिपोर्ट्स</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ url('/admin/applications-list') }}">
                            <i class="bi bi-circle"></i><span>ज़िला अनुसार आवेदन</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/merit-list') }}">
                            <i class="bi bi-circle"></i><span>मेरिट लिस्ट</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/district-report') }}">
                            <i class="bi bi-geo"></i>
                            <span>जिला रिपोर्ट</span>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/admin/ssp-portal-data') }}" id="ssp-portal-data">
                    <i class="bi bi-database"></i><span>SSP Portal Vacancy Data</span>
                </a>
            </li>



            <!-- Feedback Nav -->
            {{-- <li class="nav-item">
                <a class="nav-link collapsed feedback-menu-link" href="{{ url('/admin/feedback') }}" id="admin-feedback">
                    <i class="bi bi-chat-left-text"></i>
                    <span>प्रतिक्रिया / Feedback</span>
                </a>
            </li> --}}

            <!-- End Feedback Nav -->


            <!-- मुख्य सेटिंग्स मेन्यू -->
            @if (!$session_district_id)
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#mainSettingsMenu" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-sliders"></i>
                        <span>मुख्य सेटिंग्स</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>

                    <ul id="mainSettingsMenu" class="nav-content collapse" data-bs-parent="#sidebar-nav"
                        style="padding-left: 15px;">
                        <li>
                            <a href="{{ url('/admin/admin-post?static=1') }}">
                                <i class="bi bi-circle"></i>
                                <span>अपरिवर्तनीय पोस्ट बनाएं</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/admin/static-posts') }}">
                                <i class="bi bi-circle"></i>
                                <span>अपरिवर्तनीय पोस्ट सूची</span>
                            </a>
                        </li>
                        {{-- <li>
                            <a href="{{ url('/admin/posts-report-export') }}">
                                <i class="bi bi-file-earmark-excel"></i>
                                <span>पोस्ट रिपोर्ट (Excel)</span>
                            </a>
                        </li> --}}
                    </ul>
                </li>
            @endif

            <!-- <li class="nav-item">
                                                                                                      <a class="nav-link collapsed" data-bs-target="#forms-nav2" data-bs-toggle="collapse" href="#">
                                                                                                        <i class="bi bi-gear"></i><span>Master Entry</span><i class="bi bi-chevron-down ms-auto"></i>
                                                                                                      </a>
                                                                                                      <ul id="forms-nav2" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                                                                                                       <li>
                                                                                                          <a href="{{ url('/admin/add-district') }}">
                                                                                                            <i class="bi bi-circle"></i>District Master
                                                                                                          </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                          <a href="{{ url('/admin/add-project') }}">
                                                                                                            <i class="bi bi-circle"></i>Project Master
                                                                                                          </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                          <a href="{{ url('/admin/add-sector') }}">
                                                                                                            <i class="bi bi-circle"></i>Sector Master
                                                                                                          </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                          <a href="{{ url('/admin/add-awc') }}">
                                                                                                            <i class="bi bi-circle"></i>Awc Master
                                                                                                          </a>
                                                                                                        </li>
                                                                                                      </ul>
                                                                                                    </li> -->
            <!-- End Masters Nav -->
        </ul>
    @endif

    {{-- end state login --}}




    <ul class="sidebar-nav" id="sidebar-nav" style="">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('admin/admin-dashboard') }}" class="logo d-flex align-items-center">
                <img style="max-height: 40px;" src="{{ url('assets/img/landingpage_img/cg-logo.svg') }}" alt="">
                <div class="logo-text-container">
                    <h5 style="font-size: 15px;">महिला एवं बाल विकास विभाग</h5 style="font-size: 18px;">
                    <p>छत्तीसगढ़ शासन</p>
                </div>
            </a>
        </div><!-- End Logo -->
    </ul>

</aside><!-- End Sidebar-->

<!-- Project Switch Modal -->
<div class="modal fade" id="projectSwitchModal" tabindex="-1" aria-labelledby="projectSwitchModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold" id="projectSwitchModalLabel">नया प्रोजेक्ट चुनें</h5>
                    <small class="text-muted">वर्तमान प्रोजेक्ट: <strong id="currentProjectLabel">-</strong></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form id="projectSwitchForm">
                    @csrf
                    <div class="mb-3">
                        <label for="project_code_modal" class="form-label fw-bold">प्रोजेक्ट</label>
                        <select class="form-select" id="project_code_modal" name="project_code" required>
                            <option value="">-- प्रोजेक्ट चुनें --</option>
                        </select>
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="captcha_modal" class="form-label fw-bold">कैप्चा</label>
                            <input type="text" class="form-control" id="captcha_modal" name="captcha"
                                placeholder="कैप्चा दर्ज करें" minlength="4" maxlength="6" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-center justify-content-between">
                            <span class="captcha-img" id="captcha-image-modal">...</span>
                            <button type="button" class="btn btn-outline-primary"
                                id="reload-captcha-modal">रिलोड</button>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">रद्द करें</button>
                        <button type="submit" class="btn btn-primary" id="submitProjectSwitch">प्रोजेक्ट बदलें</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notificationIcon = document.getElementById('notificationIcon');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationContent = document.getElementById('notificationContent');

        // Project switch modal
        const projectSwitchBtn = document.getElementById('openProjectSwitch');
        const projectSwitchModalEl = document.getElementById('projectSwitchModal');
        const projectSwitchForm = document.getElementById('projectSwitchForm');
        const projectSelect = document.getElementById('project_code_modal');
        const captchaImage = document.getElementById('captcha-image-modal');
        const reloadCaptchaBtn = document.getElementById('reload-captcha-modal');
        const submitProjectSwitchBtn = document.getElementById('submitProjectSwitch');
        const currentProjectLabel = document.getElementById('currentProjectLabel');
        const projectModal = (projectSwitchModalEl && window.bootstrap && window.bootstrap.Modal)
            ? new window.bootstrap.Modal(projectSwitchModalEl)
            : null;

        function setLoadingState(isLoading) {
            if (!submitProjectSwitchBtn) return;
            submitProjectSwitchBtn.disabled = isLoading;
            submitProjectSwitchBtn.textContent = isLoading ? 'कृपया प्रतीक्षा करें...' : 'प्रोजेक्ट बदलें';
        }

        function loadCaptcha() {
            if (!captchaImage) return;
            fetch('{{ url('/reload-captcha') }}')
                .then(resp => resp.json())
                .then(data => {
                    captchaImage.innerHTML = data.captcha;
                })
                .catch(() => {
                    captchaImage.innerHTML = '<span class="text-danger">कैप्चा लोड त्रुटि</span>';
                });
        }

        function loadProjectsAndOpenModal() {
            if (!projectModal) return;
            setLoadingState(true);
            fetch('{{ url('/admin/change-project') }}?modal=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(resp => resp.json())
                .then(data => {
                    if (!data.success) throw new Error('failed');

                    // Fill current project name
                    if (currentProjectLabel) {
                        currentProjectLabel.textContent = data.current_project || '-';
                    }

                    // Populate options
                    if (projectSelect) {
                        projectSelect.innerHTML = '<option value="">-- प्रोजेक्ट चुनें --</option>';
                        (data.projects || []).forEach(function (p) {
                            const opt = document.createElement('option');
                            opt.value = p.project_id || p.project_code;
                            opt.textContent = `${p.project} (${p.district})`;
                            projectSelect.appendChild(opt);
                        });
                    }

                    if (captchaImage) {
                        captchaImage.innerHTML = data.captcha || '';
                    }

                    if (projectModal) projectModal.show();
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'त्रुटि',
                        text: 'प्रोजेक्ट सूची लोड नहीं हो सकी।'
                    });
                })
                .finally(() => {
                    setLoadingState(false);
                });
        }

        if (projectSwitchBtn) {
            projectSwitchBtn.addEventListener('click', function (e) {
                e.preventDefault();
                loadProjectsAndOpenModal();
            });
        }

        if (reloadCaptchaBtn) {
            reloadCaptchaBtn.addEventListener('click', function () {
                loadCaptcha();
            });
        }

        if (projectSwitchForm) {
            projectSwitchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                setLoadingState(true);

                const formData = new FormData(projectSwitchForm);

                fetch('{{ url('/admin/process-change-project') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(async resp => {
                        if (resp.ok) return resp.json();
                        const data = await resp.json().catch(() => null);
                        throw data || { errors: null };
                    })
                    .then(data => {
                        if (data.success) {
                            if (projectModal) projectModal.hide();
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'प्रोजेक्ट सफलतापूर्वक बदल गया।',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                            });
                            setTimeout(() => {
                                window.location.href = data.redirect_url || '/admin/admin-dashboard';
                            }, 1200);
                        } else {
                            throw data;
                        }
                    })
                    .catch((err) => {
                        if (err?.errors) {
                            const first = Object.values(err.errors)[0];
                            Swal.fire({ icon: 'error', title: 'त्रुटि', text: Array.isArray(first) ? first[0] : first });
                        } else if (err?.message) {
                            Swal.fire({ icon: 'error', title: 'त्रुटि', text: err.message });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'त्रुटि',
                                text: 'प्रोजेक्ट बदलने में समस्या आई।'
                            });
                        }
                        loadCaptcha();
                    })
                    .finally(() => {
                        setLoadingState(false);
                    });
            });
        }


        if (!notificationIcon) return; // Exit if notification icon doesn't exist

        // Toggle notification dropdown
        notificationIcon.addEventListener('click', function (e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
            if (notificationDropdown.classList.contains('show')) {
                loadNotifications();
            }
        });


        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!notificationIcon.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });

        // Load notifications
        function loadNotifications() {
            fetch('/admin/notifications', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.total_count);
                        renderNotifications(data.notifications);
                    } else {
                        notificationContent.innerHTML =
                            '<div class="notification-empty">सूचनाएं लोड करने में त्रुटि हुई</div>';
                    }
                })
                .catch(error => {
                    // console.error('Error loading notifications:', error);
                    notificationContent.innerHTML =
                        '<div class="notification-empty">सूचनाएं लोड करने में त्रुटि हुई</div>';
                });
        }

        // Update notification badge
        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count > 99 ? '99+' : count;
                notificationBadge.style.display = 'flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }

        // Render notifications (clickable)
        function renderNotifications(notifications) {
            if (notifications.length === 0) {
                notificationContent.innerHTML =
                    '<div class="notification-empty">कोई अनुमोदन सूचना नहीं मिली</div>';
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                // Create URL with specific filters for notification data
                const applicationListUrl = '/admin/application-list?' +
                    'advertisement_id=' + encodeURIComponent(notification.Advertisement_ID) +
                    '&advertisement_title=' + encodeURIComponent(notification.advertisement_title) +
                    '&status=Submitted' +
                    '&from_notification=1' +
                    '&days_after_expiry=' + notification.days_after_expiry +
                    '&pending_count=' + notification.pending_applications;

                html += `
                    <div class="notification-item" style="cursor: pointer;" onclick="closeNotificationAndNavigate('${applicationListUrl}')">
                        <div class="notification-title">
                            ${notification.project}
                        </div>
                        <div class="notification-details">
                            विज्ञापन: ${notification.advertisement_title}
                            <span class="pending-count">${notification.pending_applications} लंबित आवेदन</span>
                        </div>
                        <div class="notification-meta">
                            <i class="bi bi-clock me-1"></i>
                            ${notification.days_after_expiry} दिन पहले समाप्त
                            <span class="ms-2">
                                <i class="bi bi-file-text me-1"></i>
                                ${notification.total_posts} पद
                            </span>
                        </div>
                    </div>
                `;
            });

            notificationContent.innerHTML = html;
        }

        // Function to close notification dropdown and navigate
        window.closeNotificationAndNavigate = function (url) {
            notificationDropdown.classList.remove('show');
            window.location.href = url;
        };

        // Load notifications on page load
        loadNotifications();

        // Auto-refresh notifications every 5 minutes
        setInterval(loadNotifications, 300000); // 5 minutes


        // Auto collapsed
        // Sidebar auto-collapse for specific pages/links
        const currentPath = window.location.pathname;
        const feedbackMenuLink = document.querySelector('.feedback-menu-link');

        function collapseSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isMobile = window.innerWidth <= 1199;

            if (isMobile) {
                sidebar.classList.remove('show');
            } else {
                document.body.classList.add('toggle-sidebar');
            }
        }

        // Paths that should auto-collapse the sidebar on load
        const autoCollapsePaths = [
            '/admin/application-list',
            '/admin/marks-entry',
            '/admin/merit-list',
            '/admin/admin-advertisment',
            '/admin/show-advertisment',
            '/admin/admin-post',
            '/admin/show-posts',
            '/admin/district-report',
            '/admin/applications-list',
            '/admin/static-posts',
        ];

        if (autoCollapsePaths.some(p => currentPath.includes(p))) {
            collapseSidebar();
        }

        // Collapse when any of these links are clicked (ensure links exist)
        const linksToCollapse = [
            feedbackMenuLink,
            document.querySelector('a[href$="/admin/application-list"]'),
            document.querySelector('a[href$="/admin/marks-entry"]'),
            document.querySelector('a[href$="/admin/merit-list"]'),
            document.querySelector('a[href$="/admin/admin-advertisment"]'),
            document.querySelector('a[href$="/admin/show-advertisment"]'),
            document.querySelector('a[href$="/admin/admin-post"]'),
            document.querySelector('a[href$="/admin/show-posts"]'),
        ].filter(Boolean);

        linksToCollapse.forEach(function (link) {
            link.addEventListener('click', function () {
                setTimeout(collapseSidebar, 100);
            });
        });
        // End sidebar auto-collapse







    });

    $(document).on('submit', '#dscEnableForm', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ url('admin/dsc-add-sign') }}",
            type: "POST",
            data: formData,
            processData: false, // prevent jQuery from converting data
            contentType: false, // prevent jQuery from setting wrong header
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log("Response:", response);

                if (response.documentType === "PDF") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'PDF processed successfully!'
                    });
                    dscSignAjax(response);

                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: response.msg || 'DSC Print Failed Ajax',
                        // text: response.msg
                    });
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Something went wrong while uploading PDF.'
                });
            }
        });
    });


    function dscSignAjax(req) {
        $.ajax({
            url: "http://localhost:8800/postcertificatedata",
            data: JSON.stringify(req),
            type: 'POST',
            // dataType: "json",
            contentType: "application/json",
            success: function (response) {
                console.log(response);
                if (response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.msg || 'DSC Sign Ajax Successfully',
                        // text: response.msg || 'Digital Signature Certificate (DSC) registered successfully.'
                    });

                    $.ajax({
                        url: "{{ url('dsc-sign-save') }}",
                        type: 'POST',
                        data: response,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            console.log(res);

                            //  else {
                            //     Swal.fire({
                            //         icon: 'warning',
                            //         title: 'DSC Registration Failed',
                            //         text: res.message || 'Failed to register DSC.'
                            //     });
                            // }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Error',
                                text: 'An error occurred while registering DSC.'
                            });
                        }
                    });

                } else if (response.status === 2) {
                    Swal.fire({
                        icon: 'warning',
                        title: response.msg || 'DSC Sign Failed Ajax',
                        // text: response.msg
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'An error occurred while sign DSC.'
                });
            }
        });
    };
</script>
