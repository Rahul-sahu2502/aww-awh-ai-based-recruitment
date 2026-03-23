<style>
    .blink-animation {
        animation: blink 1.5s infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

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
</style>


<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ url('candidate/candidate-dashboard') }}" class="logo d-flex align-items-center">
            {{-- <img src="{{url('assets/img/dpo.png')}}" alt="" style="width: 45px;"> --}}
            <img src="{{ url('assets/img/logonew.png') }}" alt="" style="width: 220px;">
            {{-- <span class="d-none d-lg-block">PWD</span> --}}
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ url('assets/img/user_profile.jfif') }}" alt="Profile" class="rounded-circle"
                        style="height: 25px;">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ session()->get('sess_fname') }}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<?php
use Illuminate\Support\Facades\DB;

$appid = Session::get('sess_id');

// Fetch records
$pending_count = DB::selectOne(
    "SELECT COUNT(*) AS total
                                FROM tbl_user_post_apply AS apply 
                                INNER JOIN tbl_user_detail ON tbl_user_detail.ID = apply.fk_applicant_id
                                INNER JOIN master_user ON master_user.ID = tbl_user_detail.Applicant_ID
                                INNER JOIN master_post ON apply.fk_post_id = master_post.post_id
                                LEFT JOIN master_district ON apply.fk_district_id = master_district.District_Code_LGD
                                INNER JOIN master_advertisement ON master_post.Advertisement_ID = master_advertisement.Advertisement_ID 
                                WHERE 
                                (apply.is_final_submit != 1 OR apply.is_final_submit IS NULL)
                                AND apply.stepCount < 5 
                                AND tbl_user_detail.Applicant_ID = ?",
    [$appid],
);

$incompleteCount = $pending_count->total;

?>


<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar d-flex flex-column justify-content-between">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/candidate/candidate-dashboard') }}" id="dashboard-id">
                <i class="bi bi-grid-fill"></i>
                <span>डैशबोर्ड</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/candidate/user-details-form/') }}" id="user-form-id">
                <i class="bi bi-arrow-right-circle-fill"></i>
                <span>पद के लिए आवेदन करें</span>
            </a>
        </li>

        <!--End Forms Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/candidate/pending-applications/') }}"
                id="pending-application-list-id">
                <i class="bi bi-file-earmark-text-fill"></i>
                <span>
                    अपूर्ण आवेदनों की सूची
                    @if (!empty($incompleteCount) && $incompleteCount > 0)
                        <span class="badge badge-sm bg-warning">
                            {{ $incompleteCount }}
                        </span>
                    @endif

                </span>

            </a>
        </li>
        <!--End Forms Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/candidate/submitted-applications/') }}"
                id="application-list-id">
                <i class="bi bi-file-earmark-text-fill"></i>
                <span>आवेदित पदों की सूची</span>
            </a>
        </li>
       

        <!-- User Manual Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/candidate/user-manual') }}" id="user-manual-id">
                <i class="bi bi-book-fill"></i>
                <span>उपयोगकर्ता मैनुअल / User Manual</span>
            </a>
        </li>

        <!--दावा आपत्ति Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/candidate/dava-apatti') }}" id="dava-apatti-id">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <span>दावा आपत्ति</span>
            </a>
        </li>


         <!-- Feedback Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed feedback-menu-link" href="{{ url('/candidate/feedback') }}"
                id="candidate-feedback">
                <i class="bi bi-question-circle-fill"></i>
                <span>सहायता एवं सुझाव / Help & Suggestions</span>
                <!-- NEW Badge with Blink Animation -->
                <span class="badge bg-danger ms-2 blink-animation" style="font-size: 0.7em; vertical-align: super;">
                    NEW
                </span>
            </a>
        </li>
        <!--End Forms Nav -->






    </ul>
    <ul class="sidebar-nav" id="sidebar-nav" style="">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('candidate/candidate-dashboard') }}" class="logo d-flex align-items-center">
                <img style="max-height: 40px;" src="{{ url('assets/img/landingpage_img/cg-logo.svg') }}" alt="">
                <div class="logo-text-container">
                    <h5 style="font-size: 15px;">महिला एवं बाल विकास विभाग</h5 style="font-size: 18px;">
                    <p>छत्तीसगढ़ शासन</p>
                </div>
            </a>
        </div><!-- End Logo -->
    </ul>

</aside><!-- End Sidebar-->
{{--
<script>
    // Feedback sidebar auto-collapse
    const currentPath = window.location.pathname;
    const feedbackMenuLink = document.querySelector('.feedback-menu-link');

    function collapseSidebar() {
        const sidebar = document.getElementById('sidebar');
        const isMobile = window.innerWidth <= 1199;

        if (isMobile) {
            // Mobile: remove 'show' class
            sidebar.classList.remove('show');
        } else {
            // Desktop: add 'toggle-sidebar' class to body
            document.body.classList.add('toggle-sidebar');
        }
    }

    // Auto-collapse on page load if on feedback page
    if (currentPath.includes('/candidate/feedback')) {
        collapseSidebar();
    }

    // Collapse on feedback link click
    if (feedbackMenuLink) {
        feedbackMenuLink.addEventListener('click', function (e) {
            // Small delay to ensure navigation starts
            setTimeout(collapseSidebar, 100);
        });
    }
    // End feedback sidebar auto-collapse


</script> --}}