@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .report-card {
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .table thead th {
            background: #0d6efd;
            color: #fff;
        }

        #detailCard {
            transition: all 0.3s ease-in-out;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
        }

        #detailCard.show {
            opacity: 1;
            max-height: 2000px;
            overflow: visible;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">जिला-अनुसार रिपोर्ट (ग्रामीण/शहरी)</h5>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                <li class="breadcrumb-item active">जिला रिपोर्ट</li>
            </ol>
        </div>

        <div class="card report-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">सारांश</h6>
                <button id="exportSummaryTable" class="btn btn-sm btn-outline-primary">Export Summary</button>
            </div>
            <div class="table-responsive">
                <table id="summaryTable" class="table table-striped table-bordered nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>जिला</th>
                            <th>परियोजना</th>
                            <th>क्षेत्र (ग्रामीण/शहरी)</th>
                            <th>विज्ञापन</th>
                            <th>पद</th>
                            <th>कुल रिक्तियाँ</th>
                            <th>कुल आवेदन</th>
                            <th>पात्र </th>
                            <th>अपात्र </th>
                            <th>लंबित</th>
                            <th>कार्यवाही</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card report-card p-3 mt-3" id="detailCard">
            <div class="d-flex justify-content-between align-items-center mb-2">
                {{-- <h6 class="mb-0">ड्रिल-डाउन</h6> --}}
                <b> <span class="text-bold" id="drillLabel">जिला चुनें</span></b>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <button id="exportSummary" class="btn btn-sm btn-outline-primary" disabled>Export Selected
                        Drill-down</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="detailTable" class="table table-striped table-bordered nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>विज्ञापन</th>
                            <th>पद</th>
                            <th>क्षेत्र</th>
                            <th>ग्राम/नगर</th>
                            <th>गाँव/वार्ड</th>
                            <th>रिक्तियाँ</th>
                            <th>आवेदन</th>
                            <th>पात्र </th>
                            <th>अपात्र </th>
                            <th>लंबित</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(function () {
            let selectedDistrict = null;
            let selectedArea = null;

            const summaryTable = $('#summaryTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ url('/admin/district-report/data') }}',
                columns: [
                    { data: null, orderable: false, searchable: false, render: (data, type, row, meta) => meta.row + 1 },
                    { data: 'district' },
                    { data: 'project_name' },
                    { data: 'area_type' },
                    { data: 'advertisements_count' },
                    { data: 'posts_count' },
                    { data: 'total_vacancy' },
                    { data: 'applications_count' },
                    { data: 'approved_count' },
                    { data: 'rejected_count' },
                    { data: 'pending_count' },
                    {
                        data: null, orderable: false, searchable: false, render: function (data) {
                            const district = data.district_code;
                            const area = data.area_id;
                            return `<button class="btn btn-sm btn-primary" data-district="${district || ''}" data-area="${area ?? ''}" onclick="loadDetail('${district || ''}','${area ?? ''}','${data.area_type}','${data.district}','${data.project_name}')">View</button>`;
                        }
                    }
                ]
            });

            const detailTable = $('#detailTable').DataTable({
                paging: true,
                searching: false,
                info: true,
                responsive: true,
                data: [],
                columns: [
                    { data: null, orderable: false, searchable: false, render: (data, type, row, meta) => meta.row + 1 },
                    { data: 'advertisement_title' },
                    { data: 'post_name' },
                    { data: 'area_type' },
                    { data: 'location' },
                    { data: 'village_ward' },
                    { data: 'total_vacancy' },
                    { data: 'applications_count' },
                    { data: 'approved_count' },
                    { data: 'rejected_count' },
                    { data: 'pending_count' }
                ]
            });

            window.loadDetail = function (district, area, areaLabel, districtName, project_name) {
                if (!district) return;
                selectedDistrict = district;
                selectedArea = area;
                $('#drillLabel').text(`${districtName || district} - ${project_name || ''} - ${areaLabel || ''}`);
                $('#exportSummary').prop('disabled', false);

                $.getJSON('{{ url('/admin/district-report/detail') }}', { district_code: district, area_id: area }, function (resp) {
                    const rows = resp.data || [];
                    detailTable.clear().rows.add(rows).draw();
                    
                    if (rows.length > 0) {
                        // Show the card with animation
                        $('#detailCard').addClass('show');
                        
                        // Scroll to the detail table after animation completes
                        setTimeout(() => {
                            const detailTableOffset = $('#detailTable').offset().top - 100;
                            $('html, body').animate({ scrollTop: detailTableOffset }, 500);
                            $('#detailTable').focus();
                        }, 300);
                    } else {
                        // Hide the card with animation
                        $('#detailCard').removeClass('show');
                        $('#drillLabel').text('डेटा उपलब्ध नहीं');
                    }
                });
            }

            $('#exportSummary').on('click', function () {
                if (!selectedDistrict) return;
                const url = `{{ url('/admin/district-report/export') }}?district_code=${selectedDistrict}${selectedArea ? '&area_id=' + selectedArea : ''}`;
                window.location = url;
            });

            $('#exportSummaryTable').on('click', function () {
                window.location = `{{ url('/admin/district-report/summary-export') }}`;
            });
        });
    </script>
@endsection