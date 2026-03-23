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

        .filter-toggle-btn.active i {
            transform: rotate(180deg);
        }

        /* Filter Container */
        /* ✅ Base container — केवल transition और hidden state */
        .filter-container {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0;
            /* हमेशा 0, क्योंकि जब छिपा होगा तो space नहीं लेगा */
        }

        /* ✅ सिर्फ तभी styling लगे जब दिख रहा हो */
        .filter-container.show {
            max-height: 500px;
            opacity: 1;
            margin-bottom: 20px;

            /* अब यहाँ सारी styling डालें */
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 10px;
            padding-bottom: 5px;
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
            margin-top: 5px;
            padding-top: 5px;
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
        @php
            $role = Session::get('sess_role');
            $district_id = Session::get('district_id', 0);
            $canBulkDisable = $role === 'Super_admin' && (int) $district_id > 0;
        @endphp
        <div class="pagetitle">
            <h5 class="fw-bold">पोस्ट</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">पोस्ट की सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list me-2"></i> पोस्ट की सूची
                            </h5>

                            <!-- ✅ दाएँ तरफ़: दोनों बटन्स एक साथ -->
                            <div class="d-flex gap-2">
                                @if ($canBulkDisable)
                                    <button type="button" class="btn btn-danger" id="disableSelectedBtn" disabled>
                                        <i class="bi bi-slash-circle me-1"></i> चयनित पोस्ट डिलीटकरें
                                    </button>
                                @endif
                                <button type="button" class="filter-toggle-btn" id="toggleFilters">
                                    <i class="bi bi-funnel-fill"></i>
                                    <span>फ़िल्टर दिखाएं</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <a href="/admin/admin-post" class="filter-toggle-btn add-advertisement-btn">
                                    <i class="bi bi-plus-circle me-2"></i>पोस्ट जोड़ें
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body container" style="padding:0px !important;">

                        <!-- Filters Container (Initially Hidden) -->
                        <div class="filter-container" id="filterContainer">
                            <div class="custom-filter-group">
                                <div class="form-group">
                                    <label>पोस्ट का नाम</label>
                                    <input type="text" id="filterPostTitle" class="form-control"
                                        placeholder="पोस्ट का नाम">
                                </div>
                                <div class="form-group">
                                    <label>परियोजना</label>
                                    <input type="text" id="filterProject" class="form-control" placeholder="परियोजना">
                                </div>
                                <div class="form-group">
                                    <label>अधिकतम आयु</label>
                                    <input type="number" id="filterMaxAge" class="form-control" placeholder="अधिकतम आयु">
                                </div>
                                {{-- <div class="form-group">
                                    <label>योग्यता</label>
                                    <select id="filterQualification" class="form-control form-select">
                                        <option value="">-- योग्यता चुनें --</option>
                                        @foreach ($qualifications as $quali)
                                        <option value="{{ $quali->Quali_ID }}">{{ $quali->Quali_Name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="form-group">
                                    <label>शहर</label>
                                    <input type="text" id="filterCity" class="form-control" placeholder="शहर">
                                </div>
                                <div class="form-group">
                                    <label>ग्राम पंचायत</label>
                                    <input type="text" id="filterGp" class="form-control" placeholder="ग्राम पंचायत">
                                </div>
                                <div class="form-group">
                                    <label>वार्ड</label>
                                    <input type="text" id="filterWard" class="form-control" placeholder="वार्ड">
                                </div>
                                <div class="form-group">
                                    <label>ग्राम</label>
                                    <input type="text" id="filterVillage" class="form-control" placeholder="ग्राम">
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

                        <!--  Table -->
                        {{-- <button id="export-excel" class="btn btn-success float-end">Download Excel</button><br> --}}
                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="postTable" class="table table-striped table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        @if ($canBulkDisable)
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="selectAllPosts" title="सभी चुनें">
                                            </th>
                                        @endif
                                        <th>क्र.स.</th>
                                        <th>#</th>
                                        <th>विज्ञापन</th>
                                        <th>पद</th>
                                        {{-- <th>जिला</th> --}}
                                        {{-- <th>परियोजना</th> --}}
                                        <th>क्षेत्र</th>
                                        <th>शहर</th>
                                        <th>ग्राम पंचायत</th>
                                        <th>वार्ड</th>
                                        <th>ग्राम</th>
                                        <th>रिक्तियां</th>
                                        {{-- <th>अधिकतम आयु</th> --}}
                                        <th>प्रारंभ तिथि</th>
                                        <th>अंतिम तिथि</th>
                                        <th>स्थिति</th>
                                        {{-- <th>योग्यता</th> --}}
                                        <th>विवरण देखें</th>
                                        @if ($canBulkDisable)
                                            <th>एक्शन </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    {{--
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> --}}

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
            const selectedPosts = {};
            const $disableSelectedBtn = $('#disableSelectedBtn');
            const $selectAllPosts = $('#selectAllPosts');
            const canBulkDisable = @json($canBulkDisable);

            const userRole = "{{ Session::get('sess_role') }}";
            const userDistrictId = parseInt("{{ Session::get('district_id', 0) }}");

            function escapeHtml(text) {
                return $('<div>').text(text || '').html();
            }

            function selectedCount() {
                return Object.keys(selectedPosts).length;
            }

            function updateDisableButtonState() {
                if (!canBulkDisable) {
                    return;
                }
                const count = selectedCount();
                $disableSelectedBtn.prop('disabled', count === 0);
                $disableSelectedBtn.html(
                    '<i class="bi bi-slash-circle me-1"></i> चयनित पोस्ट डिलीट करें' + (count > 0 ?
                        ` (${count})` : '')
                );
            }

            function syncSelectAllCheckbox() {
                if (!canBulkDisable) {
                    return;
                }
                const totalRows = $('.post-select-checkbox').length;
                const checkedRows = $('.post-select-checkbox:checked').length;
                $selectAllPosts.prop('checked', totalRows > 0 && totalRows === checkedRows);
            }

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
                }, {
                    data: 'post_id',
                    name: 'master_post.post_id',
                    visible: false
                }, {
                    data: 'advertisement_title',
                    name: 'advertisement_title',
                    searchable: false
                }, {
                    data: 'post_name',
                    name: 'master_post.title'
                },
                // {
                //     data: 'district_names',
                //     name: 'district_names',
                //     orderable: false,
                //     searchable: false
                // },
                // {
                //     data: 'project_name',
                //     name: 'project_name'
                // },
                {
                    data: 'area_name',
                    name: 'area_name'
                }, {
                    data: 'city_name',
                    name: 'city_name',
                    render: function(data) {
                        return data ? data : '-';
                    }
                }, {
                    data: 'panchayat_name',
                    name: 'panchayat_name',
                    render: function(data) {
                        return data ? data : '-';
                    }
                }, {
                    data: 'ward_names',
                    name: 'ward_names',
                    render: function(data) {
                        return data ? data : '-';
                    }
                }, {
                    data: 'village_names',
                    name: 'village_names',
                    render: function(data) {
                        return data ? data : '-';
                    }
                }, {
                    data: 'vacancy_count',
                    name: 'post_vacancy_map.no_of_vacancy'
                },
                // {
                //     data: 'max_age',
                //     name: 'master_post.max_age'
                // },
                {
                    data: 'date_from',
                    name: 'date_from',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                    }
                }, {
                    data: 'date_to',
                    name: 'date_to',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                    }
                }, {
                    data: 'disable_status',
                    name: 'disable_status',
                    orderable: false,
                    searchable: false
                },
                // {
                //     data: 'qualification_name',
                //     name: 'master_qualification.Quali_Name'
                // },
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

            let table = $('#postTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ route('posts.get') }}",
                    data: function(d) {
                        d.post_name = $('#filterPostTitle').val();
                        d.project = $('#filterProject').val();
                        d.max_age = $('#filterMaxAge').val();
                        d.qualification = $('#filterQualification').val();
                        d.filterCity = $('#filterCity').val();
                        d.filterGp = $('#filterGp').val();
                        d.filterWard = $('#filterWard').val();
                        d.filterVillage = $('#filterVillage').val();
                    }
                },
                columns: columns,
                dom: '<"d-flex justify-content-between align-items-center mb-1"l<"text-end mb-1"B>>frtip',
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
                order: [
                    [canBulkDisable ? 1 : 0, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                language: {
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                drawCallback: function() {
                    if (!canBulkDisable) {
                        return;
                    }
                    $('.post-select-checkbox').each(function() {
                        const postId = String($(this).data('post-id'));
                        if (selectedPosts[postId]) {
                            $(this).prop('checked', true);
                        }
                    });
                    syncSelectAllCheckbox();
                    updateDisableButtonState();
                }
            });

            if (canBulkDisable) {
                $(document).on('change', '.post-select-checkbox', function() {
                    const postId = String($(this).data('post-id'));
                    const postName = $(this).data('post-name');

                    if ($(this).is(':checked')) {
                        selectedPosts[postId] = {
                            id: Number(postId),
                            name: postName
                        };
                    } else {
                        delete selectedPosts[postId];
                    }
                    syncSelectAllCheckbox();
                    updateDisableButtonState();
                });

                $selectAllPosts.on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('.post-select-checkbox').each(function() {
                        $(this).prop('checked', isChecked).trigger('change');
                    });
                });

                $disableSelectedBtn.on('click', function() {
                    const items = Object.values(selectedPosts);

                    if (!items.length) {
                        return;
                    }

                    const postNamesHtml = items
                        .map((item) => `<li>${escapeHtml(item.name)}</li>`)
                        .join('');

                    Swal.fire({
                        icon: 'warning',
                        title: 'पोस्ट डिलीट करें',
                        html: `
                            <div class="text-start">
                                <p class="mb-2"><strong>चयनित पोस्ट:</strong></p>
                                <ul style="max-height:180px;overflow:auto;padding-left:20px;">${postNamesHtml}</ul>
                                <p class="mb-0 text-danger"><strong>एक बार delete/disable करने के बाद आप इस पोस्ट को दुबारा नहीं देख सकते।</strong></p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'हाँ, डिलीट करें',
                        cancelButtonText: 'रद्द करें',
                        focusCancel: true
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        $.ajax({
                            url: "{{ route('posts.disable') }}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                post_ids: items.map((item) => item.id)
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'सफल',
                                    text: response.message ||
                                        'पोस्ट डिलीट कर दी गई।'
                                });

                                Object.keys(selectedPosts).forEach((key) =>
                                    delete selectedPosts[key]);
                                $selectAllPosts.prop('checked', false);
                                updateDisableButtonState();
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.message ||
                                    'पोस्ट डिलीट करने में समस्या आई।';
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

            $('#btnFilter').on('click', function() {
                table.draw();
            });

            $('#btnReset').on('click', function() {
                $('#filterPostTitle, #filterProject, #filterDistrict, #filterMaxAge, #filterStartDate, #filterQualification, #filterCity, #filterGp, #filterWard, #filterVillage')
                    .val('');
                Object.keys(selectedPosts).forEach((key) => delete selectedPosts[key]);
                if (canBulkDisable) {
                    $selectAllPosts.prop('checked', false);
                }
                updateDisableButtonState();
                table.draw();
            });
        });


        // Export all data in Excel
        $('#export-excel').on('click', function() {
            $.ajax({
                url: "{{ route('post.export') }}",
                method: 'GET',
                data: {
                    checkOnly: true
                },
                success: function(response) {
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
