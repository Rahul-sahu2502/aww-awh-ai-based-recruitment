@extends('layouts.dahboard_layout')

@section('title', 'SSP Portal Vacancy Data')

@section('styles')
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 5px;
            padding: 5px 10px;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-warning {
            background-color: #ffc107;
        }

        #sspDataTable_wrapper {
            padding: 20px;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .refresh-btn {
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .refresh-btn i {
            transition: transform 0.3s ease;
        }

        .refresh-btn i.spinning {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .stats-card {
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>SSP Portal Vacancy Data</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">SSP Portal Data</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3 mb-xl-0">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">कुल AWC</h5>
                            <h3 class="text-primary" id="totalAwc">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3 mb-xl-0">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">AWW Filled</h5>
                            <h3 class="text-success" id="awwFilled">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3 mb-xl-0">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">AWW Vacant</h5>
                            <h3 class="text-danger" id="awwVacant">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3 mb-xl-0">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">AWH Filled</h5>
                            <h3 class="text-success" id="awhFilled">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3 mb-xl-0">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">AWH Vacant</h5>
                            <h3 class="text-danger" id="awhVacant">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-table"></i> AWC Wise Vacancy Data
                            </h5>
                            <div>
                                <button class="btn btn-success btn-sm me-2" id="exportExcelBtn">
                                    <i class="bi bi-file-earmark-excel"></i> Excel Export
                                </button>
                                <button class="btn btn-light btn-sm refresh-btn" id="refreshBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body container">
                            <!-- Filters Row -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filterProject" class="form-label">प्रोजेक्ट नाम</label>
                                    <select class="form-select form-select-sm" id="filterProject">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterSector" class="form-label">सेक्टर नाम</label>
                                    <select class="form-select form-select-sm" id="filterSector">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterAwc" class="form-label">AWC नाम</label>
                                    <select class="form-select form-select-sm" id="filterAwc">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filterAww" class="form-label">AWW Position</label>
                                    <select class="form-select form-select-sm" id="filterAww">
                                        <option value="">All</option>
                                        <option value="Filled">Filled</option>
                                        <option value="Vacant">Vacant</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label for="filterAwh" class="form-label">AWH</label>
                                    <select class="form-select form-select-sm" id="filterAwh">
                                        <option value="">All</option>
                                        <option value="Filled">Filled</option>
                                        <option value="Vacant">Vacant</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="sspDataTable">
                                    <thead>
                                        <tr>
                                            <th>क्रमांक</th>
                                            <th>जिला नाम</th>
                                            <th>प्रोजेक्ट नाम</th>
                                            <th>सेक्टर नाम</th>
                                            <th>AWC नाम</th>
                                            <th>AWW Position</th>
                                            <th>AWH Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <!-- SheetJS Library for Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {
            let sspTable;

            // Initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#sspDataTable')) {
                    $('#sspDataTable').DataTable().destroy();
                }

                sspTable = $('#sspDataTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('admin.get-ssp-portal-data') }}",
                        type: "GET",
                        beforeSend: function () {
                            Swal.fire({
                                title: 'कृपया प्रतीक्षा करें',
                                html: 'डेटा लोड हो रहा है...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        dataSrc: function (json) {
                            if (json.success) {
                                updateStatistics(json.data);
                                // Populate filters after data is loaded
                                setTimeout(function () {
                                    populateFilters();
                                }, 100);
                                return json.data;
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'चेतावनी',
                                    text: json.message || 'कोई डेटा नहीं मिला',
                                    confirmButtonText: 'ठीक है',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                                return [];
                            }
                        },
                        complete: function () {
                            Swal.close();
                        },
                        error: function (xhr, error, thrown) {
                            console.error('AJAX Error:', error);
                            Swal.fire({
                                icon: 'warning',
                                title: 'चेतावनी',
                                text: 'डेटा लोड करने में त्रुटि। कृपया पुनः प्रयास करें।',
                                confirmButtonText: 'ठीक है',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            });
                        }
                    },
                    columns: [{
                        data: null,
                        // render: function (data, type, row, meta) {
                        //     // return meta.row + 1;
                        //      return meta.row + meta.settings._iDisplayStart + 1;
                        // },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'district_name',
                        render: function (data) {
                            return toInitCap(data);
                        }
                    },
                    {
                        data: 'project_name',
                        render: function (data) {
                            return toInitCap(data);
                        }
                    },
                    {
                        data: 'sector_name',
                        render: function (data) {
                            return toInitCap(data);
                        }
                    },
                    {
                        data: 'awc_name',
                        render: function (data) {
                            return toInitCap(data);
                        }
                    },
                    {
                        data: 'aww_position',
                        render: function (data) {
                            if (data && data.toLowerCase() === 'filled') {
                                return '<span class="badge badge-success">Filled</span>';
                            } else if (data && data.toLowerCase() === 'vacant') {
                                return '<span class="badge badge-danger">Vacant</span>';
                            } else {
                                return '<span class="badge badge-warning">' + (data || 'N/A') +
                                    '</span>';
                            }
                        }
                    },
                    {
                        data: 'awh_position',
                        render: function (data) {
                            if (data && data.toLowerCase() === 'filled') {
                                return '<span class="badge badge-success">Filled</span>';
                            } else if (data && data.toLowerCase() === 'vacant') {
                                return '<span class="badge badge-danger">Vacant</span>';
                            } else {
                                return '<span class="badge badge-warning">' + (data || 'N/A') +
                                    '</span>';
                            }
                        }
                    }
                    ],
                    order: [
                        [5, 'desc'],   // पहले App_Date
                        [6, 'desc']    // फिर Application_Status
                    ], // Sort by district name
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    language: {
                        // processing: "लोड हो रहा है...",
                        search: "खोजें:",
                        lengthMenu: "प्रति पृष्ठ _MENU_ रिकॉर्ड दिखाएं",
                        info: "_START_ से _END_ तक, कुल _TOTAL_ रिकॉर्ड",
                        infoEmpty: "कोई रिकॉर्ड नहीं मिला",
                        infoFiltered: "(कुल _MAX_ रिकॉर्ड में से फ़िल्टर किया गया)",
                        zeroRecords: "कोई मिलान रिकॉर्ड नहीं मिला",
                        emptyTable: "टेबल में कोई डेटा उपलब्ध नहीं है",
                        paginate: {
                            first: "पहला",
                            previous: "पिछला",
                            next: "अगला",
                            last: "अंतिम"
                        }
                    },
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                        }
                    }
                    ]
                });

                sspTable.on('order.dt search.dt draw.dt', function () {
                    sspTable
                        .column(0, { search: 'applied', order: 'applied' })
                        .nodes()
                        .each(function (cell, i) {
                            cell.innerHTML = i + 1;
                        });
                });
            }




            // Populate filter dropdowns from table data
            function populateFilters() {
                if (!sspTable) return;

                let data = sspTable.rows().data();
                let projects = new Set();
                let sectors = new Set();
                let awcs = new Set();

                // Extract unique values
                data.each(function (row) {
                    if (row.project_name) projects.add(row.project_name);
                    if (row.sector_name) sectors.add(row.sector_name);
                    if (row.awc_name) awcs.add(row.awc_name);
                });

                // Populate Project dropdown
                $('#filterProject').empty().append('<option value="">All</option>');
                Array.from(projects).sort().forEach(function (project) {
                    $('#filterProject').append('<option value="' + project + '">' + toInitCap(project) + '</option>');
                });

                // Populate Sector dropdown
                $('#filterSector').empty().append('<option value="">All</option>');
                Array.from(sectors).sort().forEach(function (sector) {
                    $('#filterSector').append('<option value="' + sector + '">' + toInitCap(sector) + '</option>');
                });

                // Populate AWC dropdown
                $('#filterAwc').empty().append('<option value="">All</option>');
                Array.from(awcs).sort().forEach(function (awc) {
                    $('#filterAwc').append('<option value="' + awc + '">' + toInitCap(awc) + '</option>');
                });
            }

            // Apply filters to DataTable
            function applyFilters() {
                if (!sspTable) return;

                let projectFilter = $('#filterProject').val();
                let sectorFilter = $('#filterSector').val();
                let awcFilter = $('#filterAwc').val();
                let awwFilter = $('#filterAww').val();
                let awhFilter = $('#filterAwh').val();

                // Custom filter function - using original row data instead of rendered HTML
                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        // Get the actual row data object
                        let rowData = sspTable.row(dataIndex).data();

                        // Match against actual data fields
                        let projectMatch = !projectFilter ||
                            (rowData.project_name && rowData.project_name.toLowerCase() === projectFilter.toLowerCase());

                        let sectorMatch = !sectorFilter ||
                            (rowData.sector_name && rowData.sector_name.toLowerCase() === sectorFilter.toLowerCase());

                        let awcMatch = !awcFilter ||
                            (rowData.awc_name && rowData.awc_name.toLowerCase() === awcFilter.toLowerCase());

                        let awwMatch = !awwFilter ||
                            (rowData.aww_position && rowData.aww_position.toLowerCase() === awwFilter.toLowerCase());

                        let awhMatch = !awhFilter ||
                            (rowData.awh_position && rowData.awh_position.toLowerCase() === awhFilter.toLowerCase());

                        return projectMatch && sectorMatch && awcMatch && awwMatch && awhMatch;
                    }
                );

                sspTable.draw();
                $.fn.dataTable.ext.search.pop(); // Remove custom filter after use
            }

            // Filter change event handlers
            $('#filterProject, #filterSector, #filterAwc, #filterAww, #filterAwh').on('change', function () {
                applyFilters();
            });

            function toInitCap(str) {
                return str.toLowerCase().replace(/(?:^|\s)\S/g, function (a) {
                    return a.toUpperCase();
                });
            }
            // Update statistics cards
            function updateStatistics(data) {
                let totalAwc = data.length;
                let awwFilled = 0;
                let awwVacant = 0;
                let awhFilled = 0;
                let awhVacant = 0;

                data.forEach(function (row) {
                    if (row.aww_position && row.aww_position.toLowerCase() === 'filled') {
                        awwFilled++;
                    } else if (row.aww_position && row.aww_position.toLowerCase() === 'vacant') {
                        awwVacant++;
                    }

                    if (row.awh_position && row.awh_position.toLowerCase() === 'filled') {
                        awhFilled++;
                    } else if (row.awh_position && row.awh_position.toLowerCase() === 'vacant') {
                        awhVacant++;
                    }
                });

                $('#totalAwc').text(totalAwc);
                $('#awwFilled').text(awwFilled);
                $('#awwVacant').text(awwVacant);
                $('#awhFilled').text(awhFilled);
                $('#awhVacant').text(awhVacant);
            }

            // Refresh button click
            $('#refreshBtn').on('click', function () {
                const $btn = $(this);
                const $icon = $btn.find('i');

                // Disable button and add spinning animation to icon
                $btn.prop('disabled', true);
                $icon.addClass('spinning');

                // Show loading Swal
                Swal.fire({
                    title: 'कृपया प्रतीक्षा करें',
                    html: 'डेटा लोड हो रहा है...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Reload table data
                sspTable.ajax.reload(function (json) {
                    // Remove spinning animation and re-enable button
                    $icon.removeClass('spinning');
                    $btn.prop('disabled', false);

                    // Close loading Swal
                    Swal.close();

                    // Repopulate filters after refresh
                    populateFilters();

                    // Show success message
                    if (json && json.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'सफलता',
                            text: 'डेटा सफलतापूर्वक रिफ्रेश किया गया! कुल रिकॉर्ड: ' + (json
                                .total_records || json.data.length),
                            timer: 2000,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    }
                }, true); // true = reset paging
            });

            // Excel Export Button - Export filtered/visible data
            $('#exportExcelBtn').on('click', function () {
                const $btn = $(this);
                const originalHtml = $btn.html();

                if (!sspTable) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'चेतावनी',
                        text: 'टेबल लोड नहीं हुई है।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                // Disable button and show loading
                $btn.prop('disabled', true).html(
                    '<i class="bi bi-hourglass-split"></i> निर्यात हो रहा है...');

                // Show loading Swal
                Swal.fire({
                    title: 'कृपया प्रतीक्षा करें',
                    html: 'Excel फाइल तैयार की जा रही है...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // Get filtered/visible data from DataTable
                    const filteredData = [];

                    // Get data from currently visible rows (after filtering)
                    let serialNumber = 1;
                    sspTable.rows({ search: 'applied' }).every(function () {
                        const rowData = this.data();
                        filteredData.push({
                            serial_no: serialNumber++,
                            district_name: rowData.district_name || '',
                            project_name: rowData.project_name || '',
                            sector_name: rowData.sector_name || '',
                            awc_name: rowData.awc_name || '',
                            aww_position: rowData.aww_position || '',
                            awh_position: rowData.awh_position || ''
                        });
                    });

                    if (filteredData.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'चेतावनी',
                            text: 'निर्यात करने के लिए कोई डेटा नहीं मिला।',
                            confirmButtonText: 'ठीक है'
                        });
                        $btn.prop('disabled', false).html(originalHtml);
                        return;
                    }

                    // Create worksheet from filtered data
                    const ws = XLSX.utils.json_to_sheet(filteredData, {
                        header: ['serial_no', 'district_name', 'project_name', 'sector_name', 'awc_name',
                            'aww_position', 'awh_position']
                    });

                    // Set column headers in Hindi
                    XLSX.utils.sheet_add_aoa(ws, [[
                        'क्रमांक',
                        'जिला नाम',
                        'प्रोजेक्ट नाम',
                        'सेक्टर नाम',
                        'AWC नाम',
                        'AWW Position',
                        'AWH Position'
                    ]], { origin: 'A1' });

                    // Set column widths
                    const colWidths = [
                        { wch: 10 }, // Serial No
                        { wch: 20 }, // District
                        { wch: 25 }, // Project
                        { wch: 25 }, // Sector
                        { wch: 30 }, // AWC Name
                        { wch: 15 }, // AWW Position
                        { wch: 15 }  // AWH Position
                    ];
                    ws['!cols'] = colWidths;

                    // Create workbook and add worksheet
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'SSP Portal Data');

                    // Generate filename with timestamp
                    const now = new Date();
                    const dateStr = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0') + '_' +
                        String(now.getHours()).padStart(2, '0') + '-' +
                        String(now.getMinutes()).padStart(2, '0');
                    const filename = 'SSP_Portal_Data_' + dateStr + '.xlsx';

                    // Download file
                    XLSX.writeFile(wb, filename);

                    // Reset button
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.close();

                    Swal.fire({
                        icon: 'success',
                        title: 'सफलता',
                        text: 'Excel फाइल डाउनलोड हो गई! कुल ' + filteredData.length + ' रिकॉर्ड निर्यात किए गए।',
                        timer: 3000,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });

                } catch (error) {
                    console.error('Export error:', error);
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.close();

                    Swal.fire({
                        icon: 'warning',
                        title: 'कृपया ध्यान दें ',
                        text: 'Excel फाइल निर्यात करते समय त्रुटि हुई।',
                        confirmButtonText: 'ठीक है'
                    });
                }
            });

            // Initialize table on page load
            initDataTable();
        });
    </script>
@endsection