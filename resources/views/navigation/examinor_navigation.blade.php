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
        transition: background-color 0.2s;
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

    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
        border-left: 3px solid #007bff;
    }

    .notification-item {
        transition: all 0.2s ease;
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
        <a href="/examinor/examinor-dashboard" class="logo d-flex align-items-center">
            {{-- <img src="{{url('assets/img/dpo.png')}}" alt="" style="width: 45px;"> --}}
            <img src="{{ url('assets/img/logonew.png') }}" alt="" style="width: 220px;">
            {{-- <span class="d-none d-lg-block">PWD</span> --}}
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <!-- Notification Bell - Only show for Admin role -->
            @if (Session::get('sess_role') === 'Admin')
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
            @endif

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ url('assets/img/user_profile.jfif') }}" alt="Profile" class="rounded-circle"
                        style="height: 25px;">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ session()->get('sess_fname') }}
                        ({{ session('designation') }})</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('/change-password') }}">
                            <i class="bi bi-lock"></i>
                            <span>Change Password</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar d-flex flex-column justify-content-between">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item ">
            <a class="nav-link collapsed " href="{{ url('/examinor/examinor-dashboard') }}" id="examinor-dashboard">
                <i class="bi bi-grid"></i>
                <span>डैशबोर्ड ({{ session('dist_name') }})</span>
            </a>
        </li><!-- End Dashboard Nav -->

        {{-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-list"></i><span>आवेदन</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{url('/admin/application-list')}}">
            <i class="bi bi-circle"></i><span>आवेदन की सूची</span>
          </a>
        </li>
      </ul>
    </li> --}}

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/admin/application-list') }}" id="admin-applications">
                <i class="bi bi-list"></i><span>आवेदन की सूची</span>
            </a>
        </li>

        @if (Session::get('sess_role') !== 'Admin')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav1" data-bs-toggle="collapse" href="#"
                    id="examinor-marks-entry">
                    <i class="bi bi-journal"></i><span>अंक प्रविष्टि</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    {{-- <li>
                        <a href="{{ url('/admin/marks-entry') }}">
                            <i class="bi bi-circle"></i><span>अंकों की प्रविष्टि</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{ url('/admin/merit-list') }}">
                            <i class="bi bi-circle"></i><span>मेरिट लिस्ट</span>
                        </a>
                    </li>
                </ul>

            </li>
        @else
            {{-- <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/examinor/pending-approvals') }}"
                    id="examinor-pending-approvals">
                    <i class="bi bi-clock-history"></i><span>लंबित अनुमोदन</span>
                </a>
            </li> --}}

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav1" data-bs-toggle="collapse" href="#"
                    id="examinor-marks-entry">
                    <i class="bi bi-journal"></i><span>मेरिट प्रविष्टि</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ url('/admin/merit-list') }}">
                            <i class="bi bi-circle"></i><span>मेरिट लिस्ट</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/merit-edit-requests') }}">
                            <i class="bi bi-circle"></i><span>मेरिट एडिट रिक्वेस्ट</span>
                        </a>
                    </li>
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

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav3" data-bs-toggle="collapse" href="#"
                id="examinor-reports">
                <i class="bi bi-file-earmark-text"></i><span>रिपोर्ट्स</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                {{-- <li>
          <a href="{{url('/admin/applications-list')}}">
            <i class="bi bi-circle"></i><span>ज़िला अनुसार आवेदन</span>
          </a>
        </li> --}}
                <li>
                    <a href="{{ url('/admin/verified-list') }}">
                        <i class="bi bi-circle"></i><span>पात्र अभियार्थी </span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/admin/rejected-list') }}">
                        <i class="bi bi-circle"></i><span>अपात्र अभियार्थी </span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/admin/application-list') }}">
                        <i class="bi bi-circle"></i><span>सभी आवेदन</span>
                    </a>
                </li>
            </ul>
            {{-- <ul id="forms-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{url('/admin/verified-list')}}">
            <i class="bi bi-circle"></i><span>Eligible Candidates</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/admin/rejected-list') }}">
            <i class="bi bi-circle"></i><span>Not Eligible Candidates</span>
          </a>
        </li>
        <li>
          <a href="{{url('/admin/application-list')}}">
            <i class="bi bi-circle"></i><span>All Applications</span>
          </a>
        </li>
      </ul> --}}
            <!-- <ul id="forms-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/admin/verified-list') }}">
            <i class="bi bi-circle"></i><span>Eligible Candidates</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/admin/rejected-list') }}">
            <i class="bi bi-circle"></i><span>Not Eligible Candidates</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/admin/application-list') }}">
            <i class="bi bi-circle"></i><span>All Applications</span>
          </a>
        </li>
      </ul> -->
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/admin/ssp-portal-data') }}" id="ssp-portal-data">
                <i class="bi bi-database"></i><span>SSP Portal Vacancy Data</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/admin/cdpo-list') }}" id="cdpo-list">
                <i class="bi bi-list"></i><span>CDPO सूची</span>
            </a>
        </li>
        <!--End Forms Nav -->
    </ul>
    <ul class="sidebar-nav" id="sidebar-nav" style="">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('examinor/examinor-dashboard') }}" class="logo d-flex align-items-center">
                <img style="max-height: 40px;" src="{{ url('assets/img/landingpage_img/cg-logo.svg') }}"
                    alt="">
                <div class="logo-text-container">
                    <h5 style="font-size: 15px;">महिला एवं बाल विकास विभाग</h5 style="font-size: 18px;">
                    <p>छत्तीसगढ़ शासन</p>
                </div>
            </a>
        </div><!-- End Logo -->
    </ul>

</aside><!-- End Sidebar-->

@if (Session::get('sess_role') === 'Admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationBadge = document.getElementById('notificationBadge');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationContent = document.getElementById('notificationContent');

            if (!notificationIcon) return; // Exit if notification icon doesn't exist

            // Toggle notification dropdown
            notificationIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                if (notificationDropdown.classList.contains('show')) {
                    loadNotifications();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationIcon.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                }
            });

            // Load notifications
            function loadNotifications() {
                fetch('/examinor/notifications', {
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
                        console.error('Error loading notifications:', error);
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

            // Render notifications
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
            window.closeNotificationAndNavigate = function(url) {
                notificationDropdown.classList.remove('show');
                window.location.href = url;
            };

            // Load notifications on page load
            loadNotifications();

            // Auto-refresh notifications every 5 minutes
            setInterval(loadNotifications, 300000); // 5 minutes
        });
    </script>
@endif
