@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
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
            max-height: 400px;
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
            cursor: pointer;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
        }

        #btnFilter {
            background: #28a745;
            color: white;
        }

        #btnReset {
            background: #6c757d;
            color: white;
        }

        .doc-cell {
            cursor: pointer;
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
                    <li class="breadcrumb-item active">मेरिट लिस्ट</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">


                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-award"> </i>मेरिट लिस्ट
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="filter-toggle-btn" id="toggleFilters">
                                    <i class="bi bi-funnel-fill"></i>
                                    <span>फ़िल्टर दिखाएं</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                @if (session('sess_role') === 'Super_admin' && session('district_id'))
                                    <button id="request-edit" class="btn btn-warning">
                                        रिक्वेस्ट भेजें
                                    </button>
                                @endif
                                <button id="export-excel" class="btn btn-success float-end">
                                    <i class="bi bi-download"></i> Download Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="filter-container" id="filterContainer">
                            <div class="custom-filter-group">
                                <div class="form-group">
                                    <label>विज्ञापन का शीर्षक</label>
                                    <select id="filterAdvertisementTitle" class="form-control">
                                        <option value="">विज्ञापन का शीर्षक</option>
                                        @foreach ($advertisment_lists as $advertisment)
                                            <option value="{{ $advertisment->Advertisement_ID }}">
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
                                            <option value="{{ $post->post_id }}">
                                                {{ $post->title }}{{ !empty($post->project_name) ? ' (' . $post->project_name . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ग्राम पंचायत</label>
                                    <input type="text" id="filterGramPanchayat" class="form-control"
                                        placeholder="ग्राम पंचायत">
                                </div>
                                <div class="form-group">
                                    <label>ग्राम</label>
                                    <input type="text" id="filterVillage" class="form-control" placeholder="ग्राम">
                                </div>
                                <div class="form-group">
                                    <label>नगर निकाय</label>
                                    <input type="text" id="filterNagarNikay" class="form-control"
                                        placeholder="नगर निकाय">
                                </div>
                                <div class="form-group">
                                    <label>वार्ड</label>
                                    <input type="text" id="filterWard" class="form-control" placeholder="वार्ड">
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button id="btnReset" class="btn filter-btn" type="button">
                                    <i class="bi bi-arrow-clockwise me-1"></i> रीसेट
                                </button>
                                <button id="btnFilter" class="btn filter-btn" type="button">
                                    <i class="bi bi-search me-1"></i> फ़िल्टर लागू करें
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="datatable" class="table table-striped table-bordered cell-border">
                                <thead>
                                    <tr>
                                        @if (session('sess_role') === 'Super_admin' && session('district_id'))
                                            <th>
                                                <input type="checkbox" id="selectAllRequests">
                                            </th>
                                        @endif
                                        <th>क्रम संख्या</th>
                                        <th>पूरा नाम</th>
                                        <th>पिता का नाम</th>
                                        <th>जन्म तिथि</th>
                                        <th>पद का शीर्षक</th>
                                        <th>मोबाइल नंबर</th>
                                        <th>ग्राम पंचायत</th>
                                        <th>ग्राम</th>
                                        <th>नगर निकाय</th>
                                        <th>वार्ड</th>
                                        <th>जाति प्रमाण अंक</th>
                                        <th>विधवा/परित्यक्ता/तलाकशुदा अंक</th>
                                        <th>ग़रीबी रेखा अंक</th>
                                        <th>कन्या आश्रम अंक</th>
                                        <th>अनुभव अंक</th>
                                        <th>न्यूनतम शैक्षिक योग्यता अंक</th>
                                        <th>कुल अंक</th>
                                        <th>एक्शन </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <!-- Document Preview Modal -->
                        <div class="modal fade" id="meritDocModal" tabindex="-1" aria-labelledby="meritDocModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="meritDocModalLabel">दस्तावेज़ पूर्वावलोकन</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <iframe id="meritDocFrame" src="" width="100%" height="600"
                                            style="display: none; border: none;"></iframe>
                                        <img id="meritDocImg" src="" alt="दस्तावेज़"
                                            style="max-width: 100%; display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $(document).ready(function() {
            'use strict';
            const uploadBase = @json(config('app.env') === 'production'
                    ? rtrim((string) config('custom.file_point'), '/') . '/'
                    : rtrim((string) asset('uploads/'), '/') . '/');
            let table = $('#datatable').DataTable({

                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('merit_list') }}",
                    data: function(d) {
                        d.advertisement_id = $('#filterAdvertisementTitle').val();
                        d.post_id = $('#filterPostTitle').val();
                        d.gp_name = $('#filterGramPanchayat').val();
                        d.village_name = $('#filterVillage').val();
                        d.nagar_name = $('#filterNagarNikay').val();
                        d.ward_name = $('#filterWard').val();
                    }
                },
                columns: [
                    @if (session('sess_role') === 'Super_admin' && session('district_id'))
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const safeName = row.Full_Name ? String(row.Full_Name).replace(/"/g, '&quot;') : '';
                            if (row.has_request == 1) {
                                return `<span class="text-success">रिक्वेस्ट किया गया</span>`;
                            }
                            return `<input type="checkbox" class="request-checkbox" data-apply-id="${row.apply_id}" data-post-id="${row.post_id}" data-applicant-id="${row.applicant_id}" data-applicant-name="${safeName}">`;
                        }
                    },
                    @endif
                    {
                        data: 'SerialNumber',
                        name: 'SerialNumber'
                    },
                    {
                        data: 'Full_Name',
                        name: 'master_user.Full_Name'
                    },
                    {
                        data: 'FatherName',
                        name: 'tbl_user_detail.FatherName'
                    },
                    {
                        data: 'DOB',
                        name: 'tbl_user_detail.DOB',
                        render: function(data, type, row) {
                            if (!data) return '';

                            let date = new Date(data);
                            let day = ('0' + date.getDate()).slice(-2);
                            let month = ('0' + (date.getMonth() + 1)).slice(-2);
                            let year = date.getFullYear();

                            return day + '-' + month + '-' + year;
                        }
                    },
                    {
                        data: 'Post_Name',
                        name: 'Post_Name'
                    },
                    {
                        data: 'Mobile_Number',
                        name: 'master_user.Mobile_Number'
                    },
                    {
                        data: 'gp_name',
                        name: 'gp_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'village_name',
                        name: 'village_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'nagar_name',
                        name: 'nagar_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'ward_name',
                        render: function(data, type, row) {
                            if (!row.ward_name) return '-';
                            return row.ward_no ? `${row.ward_name} (${row.ward_no})` : row
                            .ward_name;
                        }
                    },
                    {
                        data: 'domicile_mark',
                        name: 'tbl_user_post_apply.domicile_mark',
                        render: function(data, type, row) {
                            const doc = row.caste_doc ? (uploadBase + row.caste_doc) : '';
                            return `<span class="doc-cell" data-doc="${doc}">${data ?? 0}</span><div class="text-muted small">दस्तावेज़ देखें</div>`;
                        }
                    },
                    {
                        data: 'v_p_t_questionMarks',
                        name: 'tbl_user_post_apply.v_p_t_questionMarks',
                        render: function(data, type, row) {
                            const doc = row.vpt_doc ? (uploadBase + row.vpt_doc) : '';
                            return `<span class="doc-cell" data-doc="${doc}">${data ?? 0}</span><div class="text-muted small">दस्तावेज़ देखें</div>`;
                        }
                    },
                    {
                        data: 'secc_questionMarks',
                        name: 'tbl_user_post_apply.secc_questionMarks',
                        render: function(data, type, row) {
                            const doc = row.secc_doc ? (uploadBase + row.secc_doc) : '';
                            return `<span class="doc-cell" data-doc="${doc}">${data ?? 0}</span><div class="text-muted small">दस्तावेज़ देखें</div>`;
                        }
                    },
                    {
                        data: 'kan_ash_questionMarks',
                        name: 'tbl_user_post_apply.kan_ash_questionMarks',
                        render: function(data, type, row) {
                            const doc = row.kan_ash_doc ? (uploadBase + row.kan_ash_doc) : '';
                            return `<span class="doc-cell" data-doc="${doc}">${data ?? 0}</span><div class="text-muted small">दस्तावेज़ देखें</div>`;
                        }
                    },
                    {
                        data: 'min_experiance_mark',
                        name: 'tbl_user_post_apply.min_experiance_mark',
                        render: function(data, type, row) {
                            const doc = row.exp_doc ? (uploadBase + row.exp_doc) : '';
                            return `<span class="doc-cell" data-doc="${doc}">${data ?? 0}</span><div class="text-muted small">दस्तावेज़ देखें</div>`;
                        }
                    },
                    {
                        data: 'min_edu_qualification_mark',
                        name: 'tbl_user_post_apply.min_edu_qualification_mark',
                        render: function(data, type, row) {
                            const doc = row.edu_doc ? (uploadBase + row.edu_doc) : '';
                            return `<span class="doc-cell" data-doc="${doc}">${data ?? 0}</span><div class="text-muted small">दस्तावेज़ देखें</div>`;
                        }
                    },
                    {
                        data: 'Total_Marks',
                        name: 'Total_Marks'
                    }, // Now defined correctly
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
                        customize: function(doc) {
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

            // Filter Toggle Functionality
            $('#toggleFilters').on('click', function() {
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

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            $('#btnReset').on('click', function() {
                $('#filterAdvertisementTitle').prop('selectedIndex', 0);
                $('#filterPostTitle').prop('selectedIndex', 0);
                $('#filterGramPanchayat').val('');
                $('#filterVillage').val('');
                $('#filterNagarNikay').val('');
                $('#filterWard').val('');
                table.ajax.reload();
            });

            $('#selectAllRequests').on('change', function() {
                $('.request-checkbox').prop('checked', this.checked);
            });

            $('#request-edit').on('click', function() {
                const selectedRows = $('.request-checkbox:checked');
                const selected = selectedRows.map(function() {
                    return {
                        apply_id: $(this).data('apply-id'),
                        post_id: $(this).data('post-id'),
                        applicant_id: $(this).data('applicant-id')
                    };
                }).get();

                if (!selected.length) {
                    Swal.fire({
                        icon: 'info',
                        title: 'कम से कम एक रिकॉर्ड चुनें',
                        allowOutsideClick: false
                    });
                    return;
                }

                const names = selectedRows.map(function() {
                    return $(this).data('applicant-name');
                }).get().filter(Boolean);

                const namePreview = names.slice(0, 5).join(', ');
                const moreCount = names.length > 5 ? (names.length - 5) : 0;
                const confirmText = names.length
                    ? `आवेदक: ${namePreview}${moreCount ? ' और ' + moreCount + ' अन्य' : ''}`
                    : 'चयनित आवेदकों के लिए रिक्वेस्ट भेजें?';

                Swal.fire({
                    icon: 'question',
                    title: 'रिक्वेस्ट कन्फर्म करें',
                    text: confirmText,
                    showCancelButton: true,
                    confirmButtonText: 'हाँ, भेजें',
                    cancelButtonText: 'नहीं',
                    allowOutsideClick: false
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: "{{ route('admin.merit-edit-requests.submit') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            items: selected
                        },
                        success: function(resp) {
                            Swal.fire({
                                icon: 'success',
                                title: resp.message || 'रिक्वेस्ट भेज दी गई',
                                allowOutsideClick: false
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'रिक्वेस्ट नहीं भेजी जा सकी',
                                text: xhr.responseJSON?.message || '',
                                allowOutsideClick: false
                            });
                        }
                    });
                });
            });

            // Open document modal from marks cells
            $(document).on('click', '.doc-cell', function() {
                const docUrl = $(this).data('doc');
                if (!docUrl) {
                    Swal.fire({
                        icon: 'info',
                        title: 'दस्तावेज़ उपलब्ध नहीं है',
                        allowOutsideClick: false
                    });
                    return;
                }

                const isImage = docUrl.match(/\.(jpeg|jpg|png|gif)$/i);
                const $frame = $('#meritDocFrame');
                const $img = $('#meritDocImg');

                if (isImage) {
                    $img.attr('src', docUrl).show();
                    $frame.hide().attr('src', '');
                } else {
                    $frame.attr('src', docUrl).show();
                    $img.hide().attr('src', '');
                }

                const modal = new bootstrap.Modal(document.getElementById('meritDocModal'));
                modal.show();
            });

            // $('select[name="datatable_length"]').addClass('form-control form-control-sm w-50');
        });

        // Export all data in Excel
        $('#export-excel').on('click', function() {
            $.ajax({
                url: "{{ route('meritList.export') }}",
                method: 'GET',
                data: {
                    checkOnly: true,
                    advertisement_id: $('#filterAdvertisementTitle').val(),
                    post_id: $('#filterPostTitle').val(),
                    gp_name: $('#filterGramPanchayat').val(),
                    village_name: $('#filterVillage').val(),
                    nagar_name: $('#filterNagarNikay').val(),
                    ward_name: $('#filterWard').val()
                },
                success: function(response) {
                    if (response.count > 0) {
                        const params = $.param({
                            advertisement_id: $('#filterAdvertisementTitle').val(),
                            post_id: $('#filterPostTitle').val(),
                            gp_name: $('#filterGramPanchayat').val(),
                            village_name: $('#filterVillage').val(),
                            nagar_name: $('#filterNagarNikay').val(),
                            ward_name: $('#filterWard').val()
                        });
                        const url = params ? "{{ route('meritList.export') }}" + '?' + params :
                            "{{ route('meritList.export') }}";
                        window.location.href = url;
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
                error: function() {
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
    </script>
@endsection
