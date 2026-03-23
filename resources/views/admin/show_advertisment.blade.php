@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    {{--
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
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

        #filterBtn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        #resetBtn {
            background: #6c757d;
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

        .pdf-downloads a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .pdf-downloads a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .icon-pdf {
            margin-left: 5px;
        }

        .pdf-icon::before {
            content: "📄";
            font-size: 16px;
        }

        .add-advertisement-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            /* Green gradient */
        }

        .add-advertisement-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        @php
            $role = Session::get('sess_role');
            $district_id = Session::get('district_id', 0);
            $canBulkDisable = $role === 'Super_admin' && (int) $district_id > 0;
        @endphp
        <div class="pagetitle">
            <h5 class="fw-bold">विज्ञापन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">विज्ञापन की सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list me-2"></i> विज्ञापन की सूची
                            </h5>

                            <!-- ✅ दाएँ तरफ़: दोनों बटन्स एक साथ -->
                            <div class="d-flex gap-2">
                                @if ($canBulkDisable)
                                    <button type="button" class="btn btn-danger" id="disableSelectedAdvertisementsBtn" disabled>
                                        <i class="bi bi-slash-circle me-1"></i> चयनित विज्ञापन डिलीट करें
                                    </button>
                                @endif
                                <button type="button" class="filter-toggle-btn" id="toggleFilters">
                                    <i class="bi bi-funnel-fill"></i>
                                    <span>फ़िल्टर दिखाएं</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <a href="/admin/admin-advertisment" class="filter-toggle-btn add-advertisement-btn">
                                    <i class="bi bi-plus-circle me-2"></i>विज्ञापन जोड़ें
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body container" style="padding:0px !important;">

                        <div class="row">
                            <div class="col-md-12">

                                <!-- Filters Container (Initially Hidden) -->
                                <div class="filter-container" id="filterContainer">
                                    <div class="custom-filter-group">
                                        <div class="form-group">
                                            <label>शीर्षक का नाम</label>
                                            <input type="text" id="adv_title" class="form-control"
                                                placeholder="शीर्षक से खोजें">
                                        </div>
                                        <div class="form-group">
                                            <label>प्रारंभ तिथि</label>
                                            <input type="date" id="adv_date" class="form-control">
                                        </div>
                                    </div>

                                    <div class="filter-actions">
                                        <button id="resetBtn" class="btn filter-btn">
                                            <i class="bi bi-arrow-clockwise me-1"></i> रीसेट
                                        </button>
                                        <button id="filterBtn" class="btn filter-btn">
                                            <i class="bi bi-search me-1"></i> फ़िल्टर लागू करें
                                        </button>
                                    </div>
                                </div>

                                {{-- <button id="export-excel" class="btn btn-success float-end">Download Excel</button><br>
                                --}}
                                <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                                    <table id="advertisementTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                @if ($canBulkDisable)
                                                    <th style="width: 40px;">
                                                        <input type="checkbox" id="selectAllAdvertisements"
                                                            title="सभी चुनें">
                                                    </th>
                                                @endif
                                                <th>#</th>
                                                <th>शीर्षक</th>
                                                <th>जारी दिनांक</th>
                                                <th>वैधता तिथि</th>
                                                <th>स्थिति</th>
                                                {{-- <th>दस्तावेज़ नाम</th> --}}
                                                <th>दस्तावेज़ देखें</th>
                                                <th>प्रसारित पत्र देखें</th>
                                                <th>विवरण देखें</th>
                                                @if ($canBulkDisable)
                                                    <th>एक्शन </th>
                                                @endif
                                                <th>सहायक दस्तावेज</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Newspaper Cutting Modal -->
    <div class="modal fade" id="newspaperCuttingModal" tabindex="-1" aria-labelledby="newspaperCuttingModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newspaperCuttingModalLabel">प्रसारित पत्र दस्तावेज़</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="newspaperCuttingContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करें</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Advertisement Document Modal -->
    <div class="modal fade" id="advertisementDocumentModal" tabindex="-1" aria-labelledby="advertisementDocumentModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="advertisementDocumentModalLabel">विज्ञापन दस्तावेज़</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="advertisementDocumentContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करें</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{--
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

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
        let table;

        $(document).ready(function () {
            const selectedAdvertisements = {};
            const $disableSelectedAdvertisementsBtn = $('#disableSelectedAdvertisementsBtn');
            const $selectAllAdvertisements = $('#selectAllAdvertisements');
            const canBulkDisable = @json($canBulkDisable);

            function escapeHtml(text) {
                return $('<div>').text(text || '').html();
            }

            function selectedCount() {
                return Object.keys(selectedAdvertisements).length;
            }

            function updateDisableButtonState() {
                if (!canBulkDisable) return;
                const count = selectedCount();
                $disableSelectedAdvertisementsBtn.prop('disabled', count === 0);
                $disableSelectedAdvertisementsBtn.html(
                    '<i class="bi bi-slash-circle me-1"></i> चयनित विज्ञापन डिलीट करें' + (count > 0 ?
                        ` (${count})` : '')
                );
            }

            function syncSelectAllCheckbox() {
                if (!canBulkDisable) return;
                const totalRows = $('.advertisement-select-checkbox').length;
                const checkedRows = $('.advertisement-select-checkbox:checked').length;
                $selectAllAdvertisements.prop('checked', totalRows > 0 && totalRows === checkedRows);
            }

            // Filter Toggle Functionality
            $('#toggleFilters').on('click', function () {
                const container = $('#filterContainer');
                const icon = $(this).find('.bi-chevron-down');
                const text = $(this).find('span');

                container.toggleClass('show');

                if (container.hasClass('show')) {
                    text.text('फ़िल्टर छुपाएं');
                    icon.css('transform', 'rotate(180deg)');
                } else {
                    text.text('फ़िल्टर दिखाएं');
                    icon.css('transform', 'rotate(0deg)');
                }
            });

            const columns = [];
            if (canBulkDisable) {
                columns.push({
                    data: 'select_checkbox',
                    name: 'select_checkbox',
                    orderable: false,
                    searchable: false
                });
            }
            columns.push({
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'Advertisement_Title',
                    name: 'Advertisement_Title'
                },
                {
                    data: 'Advertisement_Date',
                    name: 'Advertisement_Date',
                    render: function (data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                    }
                },
                {
                    data: 'Date_For_Age',
                    name: 'Date_For_Age',
                    render: function (data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                    }
                },
                {
                    data: 'disable_status',
                    name: 'disable_status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'view_document',
                    name: 'view_document',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'newspaper_cutting',
                    name: 'newspaper_cutting',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'view_details',
                    name: 'view_details',
                    orderable: false,
                    searchable: false
                }
            );
            if (canBulkDisable) {
                columns.push({
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                });
            }
            columns.push({
                data: 'action2',
                name: 'action2',
                orderable: false,
                searchable: false
            });

            table = $('#advertisementTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('advertisement.list') }}",
                    data: function (d) {
                        d.adv_title = $('#adv_title').val();
                        d.adv_date = $('#adv_date').val();
                    }
                },
                columns: columns,
                order: [
                    [canBulkDisable ? 2 : 1, 'asc']
                ], // Default sort by Advertisement Title
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
                },
                drawCallback: function () {
                    if (!canBulkDisable) return;
                    $('.advertisement-select-checkbox').each(function () {
                        const advertisementId = String($(this).data('advertisement-id'));
                        if (selectedAdvertisements[advertisementId]) {
                            $(this).prop('checked', true);
                        }
                    });
                    syncSelectAllCheckbox();
                    updateDisableButtonState();
                }
            });

            if (canBulkDisable) {
                $(document).on('change', '.advertisement-select-checkbox', function () {
                    const advertisementId = String($(this).data('advertisement-id'));
                    const advertisementTitle = $(this).data('advertisement-title');

                    if ($(this).is(':checked')) {
                        selectedAdvertisements[advertisementId] = {
                            id: Number(advertisementId),
                            title: advertisementTitle
                        };
                    } else {
                        delete selectedAdvertisements[advertisementId];
                    }
                    syncSelectAllCheckbox();
                    updateDisableButtonState();
                });

                $selectAllAdvertisements.on('change', function () {
                    const isChecked = $(this).is(':checked');
                    $('.advertisement-select-checkbox').each(function () {
                        $(this).prop('checked', isChecked).trigger('change');
                    });
                });

                $disableSelectedAdvertisementsBtn.on('click', function () {
                    const items = Object.values(selectedAdvertisements);
                    if (!items.length) return;

                    const advertisementTitlesHtml = items
                        .map((item) => `<li>${escapeHtml(item.title)}</li>`)
                        .join('');

                    Swal.fire({
                        icon: 'warning',
                        title: 'विज्ञापन डिलीट करें',
                        html: `
                            <div class="text-start">
                                <p class="mb-2"><strong>चयनित विज्ञापन:</strong></p>
                                <ul style="max-height:180px;overflow:auto;padding-left:20px;">${advertisementTitlesHtml}</ul>
                                <p class="mb-0 text-danger"><strong>एक बार delete/disable करने के बाद आप इस विज्ञापन को दुबारा नहीं देख सकते।</strong></p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'हाँ, डिलीट करें',
                        cancelButtonText: 'रद्द करें',
                        focusCancel: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: "{{ route('advertisements.disable') }}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                advertisement_ids: items.map((item) => item.id)
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'सफल',
                                    text: response.message || 'विज्ञापन डिलीट कर दिए गए।'
                                });

                                Object.keys(selectedAdvertisements).forEach((key) =>
                                    delete selectedAdvertisements[key]);
                                $selectAllAdvertisements.prop('checked', false);
                                updateDisableButtonState();
                                table.ajax.reload(null, false);
                            },
                            error: function (xhr) {
                                const errorMessage = xhr.responseJSON?.message || 'विज्ञापन डिलीट करने में समस्या आई।';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'त्रुटि',
                                    text: errorMessage
                                });
                            }
                        });
                    });
                });
            }

            //  Filter button
            $('#filterBtn').on('click', function () {
                table.draw();
            });

            //  Reset button
            $('#resetBtn').on('click', function () {
                $('#adv_title').val('');
                $('#adv_date').val('');
                Object.keys(selectedAdvertisements).forEach((key) => delete selectedAdvertisements[key]);
                if (canBulkDisable) {
                    $selectAllAdvertisements.prop('checked', false);
                }
                updateDisableButtonState();
                table.draw();
            });
        });

        // Export all data in Excel
        $('#export-excel').on('click', function () {
            $.ajax({
                url: "{{ route('advertisment.export') }}",
                method: 'GET',
                data: {
                    checkOnly: true
                },
                success: function (response) {
                    if (response.count > 0) {
                        window.location.href = "{{ route('advertisment.export') }}";
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

        function viewNewspaperCutting(filePath, advertisementId) {
            if (!filePath || filePath === 'null' || filePath === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'कोई दस्तावेज़ नहीं',
                    text: 'इस विज्ञापन के लिए प्रसारित पत्र उपलब्ध नहीं है।',
                    confirmButtonText: 'ठीक है'
                });
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('newspaperCuttingModal'));
            const contentDiv = document.getElementById('newspaperCuttingContent');

            // Show loading spinner
            contentDiv.innerHTML =
                '<div class="spinner-border" role="status"><span class="visually-hidden">लोड हो रहा है...</span></div>';

            // Get file extension
            const fileExtension = filePath.split('.').pop().toLowerCase();

            let content = '';
            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                // Image file
                content = `<img src="${filePath}" class="img-fluid" alt="प्रसारित पत्र" style="max-height: 70vh;">`;
            } else if (fileExtension === 'pdf') {
                // PDF file
                content = `<embed src="${filePath}" type="application/pdf" width="100%" height="500px" />`;
            } else {
                // Other file types
                content = `<div class="alert alert-info">
                                                                                        <i class="bi bi-file-earmark"></i>
                                                                                        <p>दस्तावेज़ डाउनलोड करने के लिए नीचे दिए गए लिंक पर क्लिक करें:</p>
                                                                                        <a href="${filePath}" target="_blank" class="btn btn-primary">डाउनलोड करें</a>
                                                                                    </div>`;
            }

            contentDiv.innerHTML = content;
            modal.show();
        }

        function viewAdvertisementDocument(filePath, advertisementId) {
            if (!filePath || filePath === 'null' || filePath === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'कोई दस्तावेज़ नहीं',
                    text: 'इस विज्ञापन के लिए दस्तावेज़ उपलब्ध नहीं है।',
                    confirmButtonText: 'ठीक है'
                });
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('advertisementDocumentModal'));
            const contentDiv = document.getElementById('advertisementDocumentContent');

            // Show loading spinner
            contentDiv.innerHTML =
                '<div class="spinner-border" role="status"><span class="visually-hidden">लोड हो रहा है...</span></div>';

            // Get file extension
            const fileExtension = filePath.split('.').pop().toLowerCase();

            let content = '';
            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                // Image file
                content = `<img src="${filePath}" class="img-fluid" alt="विज्ञापन दस्तावेज़" style="max-height: 70vh;">`;
            } else if (fileExtension === 'pdf') {
                // PDF file
                content = `<embed src="${filePath}" type="application/pdf" width="100%" height="500px" />`;
            } else {
                // Other file types
                content = `<div class="alert alert-info">
                                                                                        <i class="bi bi-file-earmark"></i>
                                                                                        <p>दस्तावेज़ डाउनलोड करने के लिए नीचे दिए गए लिंक पर क्लिक करें:</p>
                                                                                        <a href="${filePath}" target="_blank" class="btn btn-primary">डाउनलोड करें</a>
                                                                                    </div>`;
            }

            contentDiv.innerHTML = content;
            modal.show();
        }

        function viewBase64Document(base64Data, advertisementId) {
            if (!base64Data || base64Data === 'null' || base64Data === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'कोई दस्तावेज़ नहीं',
                    text: 'इस विज्ञापन के लिए दस्तावेज़ उपलब्ध नहीं है।',
                    confirmButtonText: 'ठीक है'
                });
                return;
            }

            // Clean base64: remove whitespace, newlines, quotes
            base64Data = base64Data.replace(/[\r\n]+/g, '').replace(/"/g, '').trim();

            // If already prefixed (data:application/pdf;base64,), remove duplicate
            if (base64Data.startsWith('data:')) {
                base64Data = base64Data.split(',')[1];
            }

            // Convert base64 → Blob → Object URL (safe for large files)
            const byteCharacters = atob(base64Data);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], {
                type: 'application/pdf'
            });
            const blobUrl = URL.createObjectURL(blob);

            const modal = new bootstrap.Modal(document.getElementById('advertisementDocumentModal'));
            const contentDiv = document.getElementById('advertisementDocumentContent');

            contentDiv.innerHTML = `<iframe src="${blobUrl}" width="100%" height="500px" style="border:none;"></iframe>`;

            modal.show();

            // Optional: clean up URL when modal closes
            document.getElementById('advertisementDocumentModal').addEventListener('hidden.bs.modal', () => {
                URL.revokeObjectURL(blobUrl);
            });
        }
    </script>
@endsection
