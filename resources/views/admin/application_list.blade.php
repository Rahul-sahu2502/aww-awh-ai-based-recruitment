@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}" />
    <style>
        .dataTables_filter,
        .dataTables_length {
            margin-bottom: 12px;
        }

        /* Filter Toggle Button */
        .filter-toggle-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            /* margin-bottom: 15px; */
        }

        .filter-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .filter-toggle-btn:active {
            transform: translateY(0);
        }

        .filter-toggle-btn i {
            transition: transform 0.3s ease;
        }

        .filter-toggle-btn.active i.bi-chevron-down {
            transform: rotate(180deg);
        }

        /* Filter Container */
        .filter-container {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0;
        }

        .filter-container.show {
            max-height: 500px;
            opacity: 1;
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 15px;
        }

        /* Filter Group Grid Layout */
        .custom-filter-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .custom-filter-group .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .custom-filter-group .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .custom-filter-group .form-control,
        .custom-filter-group .form-select {
            height: 38px;
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .custom-filter-group .form-control:focus,
        .custom-filter-group .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        /* Filter Action Buttons */
        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
            border: none;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #btnFilter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        #btnReset {
            background: #6c757d;
            color: white;
        }

        .add-advertisement-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            /* Green gradient */
        }

        .add-advertisement-btn:hover {
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .custom-filter-group {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">आवेदन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if (session('sess_role') === 'Super_admin')
                            <a href="{{ url('/admin/admin-dashboard') }}">होम</a>
                        @elseif (session('sess_role') === 'Admin' || session('sess_role') === 'Supervisor' || session('sess_role') === 'CDPO')
                            <a href="{{ url('/examinor/examinor-dashboard') }}">होम</a>
                        @endif
                    <li class="breadcrumb-item active">आवेदन की सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list me-2"></i> आवेदन की सूची
                            </h5>

                            <!-- ✅ दाएँ तरफ़: दोनों बटन्स एक साथ -->
                            <div class="d-flex gap-2">
                                <button type="button" class="filter-toggle-btn" id="toggleFilters">
                                    <i class="bi bi-funnel-fill"></i>
                                    <span>फ़िल्टर दिखाएं</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <button id="export-excel" class="filter-toggle-btn add-advertisement-btn">
                                    <i class="bi bi-download me-2"></i>Download Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body container" style="padding:0px !important;">
                        <!-- Notification filter info -->
                        <div id="notification-filter-info" class="alert alert-info" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="filter-message"></span>
                            <button type="button" class="btn-close float-end"
                                onclick="$('#notification-filter-info').hide();"></button>
                        </div>

                        <!-- Filters Container (Initially Hidden) -->
                        <div class="filter-container" id="filterContainer">
                            <div class="custom-filter-group">
                                <div class="form-group">
                                    <label>आवेदनकर्ता का नाम</label>
                                    <input type="text" id="filterApplicantTitle" class="form-control"
                                        placeholder="आवेदनकर्ता का नाम">
                                </div>
                                <div class="form-group">
                                    <label>विज्ञापन का शीर्षक</label>
                                    <select id="filterAdvertisementTitle" class="form-control">
                                        <option value="">विज्ञापन का शीर्षक</option>
                                        @foreach ($advertisment_lists as $advertisment)
                                            <option value="{{ $advertisment->Advertisement_Title }}">
                                                {{ $advertisment->Advertisement_Title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>पद का शीर्षक</label>
                                    <select id="filterPostTitle" class="form-control">
                                        <option value="">पद का शीर्षक</option>
                                        @foreach ($post_lists as $post)
                                            <option value="{{ $post->title }}">
                                                {{ $post->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>आवेदन तिथि</label>
                                    <input type="date" id="filterApplyDate" class="form-control" placeholder="आवेदन तिथि">
                                </div>
                                <div class="form-group">
                                    <label>आवेदन की स्थिति</label>
                                    <input type="text" id="filterApplicationStatus" class="form-control"
                                        placeholder="आवेदन की स्थिति">
                                </div>
                                <div class="form-group">
                                    <label>ग्राम पंचायत</label>
                                    <input type="text" id="filterGramPanchayat" class="form-control"
                                        placeholder="ग्राम पंचायत">
                                </div>
                                <div class="form-group">
                                    <label>नगर निकाय</label>
                                    <input type="text" id="filterNagarNikay" class="form-control"
                                        placeholder="नगर निकाय">
                                </div>
                            </div>

                            <div class="filter-actions">
                                <button id="btnReset" class="btn filter-btn">
                                    <i class="bi bi-arrow-clockwise me-1"></i> रीसेट
                                </button>
                                <button id="btnFilter" class="btn filter-btn">
                                    <i class="bi bi-search me-1"></i> फ़िल्टर लागू करें
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                        <table id="datatable" class="table table-striped table-bordered cell-border">
                            <thead>
                                <tr>
                                    <!--<th>ID</th> yeh application ka id hai-->
                                    <th>क्रम संख्या</th>
                                    <th>आवेदनकर्ता का नाम</th>
                                    <th>विज्ञापन का शीर्षक</th>
                                    <th>पद का शीर्षक</th>
                                    <th>ग्राम पंचायत</th>
                                    <th>नगर निकाय</th>
                                    <th>मोबाइल नंबर</th>
                                    <th>आवेदन तिथि</th>
                                    {{-- <th>स्वप्रमाणित दस्तावेज</th> --}}
                                    <th>एक्शन </th>
                                    <th>आवेदन की स्थिति</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data comes from yajra datatable --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js') }}"></script>
    <script>
        // console.log(dist_id);

        $(document).ready(function () {
            'use strict';

            // Filter Toggle Functionality
            $('#toggleFilters').on('click', function () {
                const filterContainer = $('#filterContainer');
                const toggleBtn = $(this);
                const btnText = toggleBtn.find('span');
                const chevronIcon = toggleBtn.find('.bi-chevron-down, .bi-chevron-up');

                filterContainer.toggleClass('show');
                toggleBtn.toggleClass('active');

                if (filterContainer.hasClass('show')) {
                    btnText.text('फ़िल्टर छुपाएं');
                    chevronIcon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                } else {
                    btnText.text('फ़िल्टर दिखाएं');
                    chevronIcon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                }
            });

            let url = window.location.href;
            let pathname = window.location.pathname;
            let search = window.location.search;
            let dist_id = pathname.split('/').pop();

            // Determine AJAX URL based on URL structure
            let ajaxUrl;
            // console.log(search, dist_id, pathname, url);


            // If we have query parameters (drill-down from district report)
            if (search && (search.includes('cdpo_id'))) {
                // alert('1');
                ajaxUrl = "{{ route('view_application_detail') }}/" + dist_id + search;
            }
            else if (Number.isInteger(Number(dist_id))) {
                // alert(dist_id);
                ajaxUrl = "{{ route('view_application_detail') }}/" + dist_id;
            }
            else {
                // alert('3');
                ajaxUrl = "{{ route('view_application_detail') }}";
            }

            // Handle URL parameters for filtering (from notifications)
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Set filters from URL parameters if they exist
            let filtersFromNotification = false;
            if (getUrlParameter('advertisement_title')) {
                const advTitle = decodeURIComponent(getUrlParameter('advertisement_title'));
                $('#filterAdvertisementTitle').val(advTitle);
                filtersFromNotification = true;
            }
            if (getUrlParameter('status')) {
                $('#filterApplicationStatus').val(decodeURIComponent(getUrlParameter('status')));
                filtersFromNotification = true;
            }
            if (getUrlParameter('post_title')) {
                const postTitle = decodeURIComponent(getUrlParameter('post_title'));
                $('#filterPostTitle').val(postTitle);
                filtersFromNotification = true;
            }

            // Show info message if filters were applied from notification
            if (filtersFromNotification && getUrlParameter('from_notification') == '1') {
                const advTitle = getUrlParameter('advertisement_title') ? decodeURIComponent(getUrlParameter(
                    'advertisement_title')) : '';
                const pendingCount = getUrlParameter('pending_count') || 'N/A';
                $('#filter-message').text(`अधिसूचना से फ़िल्टर लागू किया गया: ${advTitle} (केवल लंबित आवेदन)`);
                $('#notification-filter-info').show();
            }


            // console.log(ajaxUrl);

            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    data: function (d) {
                        d.status = $('#filterApplicationStatus').val();
                        d.apply_date = $('#filterApplyDate').val();
                        d.post_tittle = $('#filterPostTitle').val();
                        d.advertisement_title = $('#filterAdvertisementTitle').val();
                        d.applicant_name = $('#filterApplicantTitle').val();
                        d.gram_panchayat = $('#filterGramPanchayat').val();
                        d.nagar_nikay = $('#filterNagarNikay').val();
                        // Add notification-specific parameters
                        d.advertisement_id = getUrlParameter('advertisement_id');
                        d.from_notification = getUrlParameter('from_notification');
                        d.days_after_expiry = getUrlParameter('days_after_expiry');
                        // Add drill-down filter parameters
                        d.pref_dist = dist_id; // ✅ correct
                        d.cdpo_id = getUrlParameter('cdpo_id');
                        // d.self_attested_file = $('#filterSelfAttestedFile').val();
                    }
                },
                columns: [{
                    data: 'SerialNumber',
                    name: 'SerialNumber'
                },
                {
                    data: 'Full_Name',
                    name: 'Full_Name'
                },
                {
                    data: 'Advertisement_Title',
                    name: 'Advertisement_Title'
                },
                {
                    data: 'Title',
                    name: 'Title'
                },
                {
                    data: 'Gram_Panchayat',
                    name: 'Gram_Panchayat',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'Nagar_Nikay',
                    name: 'Nagar_Nikay',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'Mobile_Number',
                    name: 'Mobile_Number'
                },
                {
                    data: 'App_Date',
                    name: 'App_Date',
                    render: function (data, type, row) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                        // Formats to "DD/MM/YYYY"
                    }
                },
                // {
                //     data: 'self_attested_file',
                //     name: 'self_attested_file',
                //     searchable: true
                // },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'Application_Status',
                    name: 'Application_Status',
                    render: function (data, type, row) {
                        let badgeClass = '';
                        let statusText = data;

                        // Example: status के हिसाब से रंग बदलना
                        if (data === 'Verified' || data === 'पात्र ') {
                            badgeClass = 'badge bg-success';
                        } else if (data === 'Pending' || data === 'प्रतीक्षित') {
                            badgeClass = 'badge bg-warning';
                        } else if (data === 'Rejected' || data === 'अपात्र ') {
                            badgeClass = 'badge bg-danger';
                        } else {
                            badgeClass = 'badge bg-info'; // कोई अन्य स्थिति
                        }

                        return '<span class="' + badgeClass + '">' + statusText + '</span>';
                    }
                }
                ],
                order: [
                    [0, 'asc']
                ],
                lengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                dom: '<" d-flex align-items-center mb-2"l>Bfrtip',
                buttons: [{
                    extend: 'copy',
                    text: 'कॉपी',
                    className: 'btn btn-sm btn-outline-primary'
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    className: 'btn btn-sm btn-outline-secondary',
                    charset: 'utf-8',
                    bom: true
                },
                {
                    extend: 'excel',
                    text: 'एक्सेल',
                    className: 'btn btn-sm btn-outline-success',
                    charset: 'utf-8',
                    bom: true
                },
                {
                    extend: 'pdf',
                    text: 'पीडीएफ',
                    className: 'btn btn-sm btn-outline-success',
                    customize: function (doc) {
                        pdfMake.fonts = {
                            unicode: {
                                normal: 'unicode.ttf',
                                bold: 'unicode.ttf',
                                italics: 'unicode.ttf',
                                bolditalics: 'unicode.ttf'
                            }
                        };
                        doc.defaultStyle.font = "unicode";
                    }
                }
                ],
                language: {
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });

            // If filters were set from URL parameters, redraw the table
            if (getUrlParameter('advertisement_title') || getUrlParameter('status') || getUrlParameter(
                'post_title') || getUrlParameter('from_notification')) {
                table.draw();
            }

            $('#btnFilter').on('click', function () {
                table.draw();
            });

            // Auto-filter when select elements change
            $('#filterAdvertisementTitle, #filterPostTitle').on('change', function () {
                table.draw();
            });

            $('#btnReset').on('click', function () {
                $('#filterApplicationStatus, #filterApplyDate, #filterPostTitle, #filterAdvertisementTitle, #filterApplicantTitle, #filterGramPanchayat, #filterNagarNikay')
                    .val('');
                // Reset select elements to first option
                $('#filterAdvertisementTitle, #filterPostTitle').prop('selectedIndex', 0);
                // Hide notification info
                $('#notification-filter-info').hide();
                // Clear URL parameters
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
                table.draw();
            });

            // Export all data in Excel
            $('#export-excel').on('click', function () {
                $.ajax({
                    url: "{{ route('allApplicationData.export') }}",
                    method: 'GET',
                    data: {
                        checkOnly: true
                    },
                    success: function (response) {
                        if (response.count > 0) {
                            window.location.href = "{{ route('allApplicationData.export') }}";
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'कोई रिकॉर्ड मौजूद नहीं है',
                                text: 'एक्सेल फाइल बनाने के लिए रिकॉर्ड आवश्यक है।',
                                confirmButtonText: 'ठीक है',
                                allowOutsideClick: false,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            text: 'डेटा प्राप्त करने में समस्या आई।',
                            confirmButtonText: 'बंद करें',
                            allowOutsideClick: false,
                        });
                    }
                });
            });
        });
    </script>
@endsection
