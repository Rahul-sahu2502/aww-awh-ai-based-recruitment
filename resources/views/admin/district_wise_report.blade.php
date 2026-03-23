@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        .drill-down-link {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .drill-down-link:hover {
            transform: translateX(5px);
        }

        .level-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .level-district {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .level-cdpo {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .level-awc {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .breadcrumb-custom {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb-custom .breadcrumb-item+.breadcrumb-item::before {
            content: "→";
            color: #667eea;
            font-weight: bold;
        }

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

        .add-advertisement-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .add-advertisement-btn:hover {
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">आवेदन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/applications-list') }}">रिपोर्ट्स</a></li>
                    <li class="breadcrumb-item active">जिले के अनुसार आवेदन सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        {{-- Drill-down Breadcrumb --}}
        @php
            $districtId = request()->get('district_id');
            $cdpoId = request()->get('cdpo_id');
            $awcId = request()->get('awc_id');

            $currentLevel = 'district';
            if ($cdpoId) {
                $currentLevel = 'cdpo';
            } elseif ($districtId) {
                $currentLevel = 'project';
            }
        @endphp


        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list me-2"></i>
                                @if ($currentLevel == 'district')
                                    जिले के अनुसार आवेदन सूची <span class="level-badge level-district">स्तर 1: जिला</span>
                                @elseif($currentLevel == 'project')
                                    CDPO के अनुसार आवेदन सूची <span class="level-badge level-cdpo">स्तर 2: CDPO</span>
                                @endif
                            </h5>

                            <button id="export-excel" class="filter-toggle-btn add-advertisement-btn">
                                <i class="bi bi-download me-2"></i>Download Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive container d-flex flex-column gap-2"
                            style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="datatable" class="table table-striped table-bordered cell-border">
                                <thead>
                                    <tr>
                                        <th>क्रम संख्या</th>
                                        <th>
                                            @if ($currentLevel == 'district')
                                                जिला
                                            @elseif($currentLevel == 'project')
                                                परियोजना
                                            @endif
                                        </th>
                                        <th>कुल आवेदन</th>
                                        <th>पात्र आवेदन</th>
                                        <th>अपात्र आवेदन</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $index => $result)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if ($currentLevel == 'district')
                                                    <a href="{{ url('/admin/district-wise-report?district_id=' . $result->Pref_Districts) }}"
                                                        class="drill-down-link fw-bold text-primary">
                                                        <i class="bi bi-folder2-open me-2"></i>{{ $result->name }}
                                                        <i class="bi bi-chevron-right ms-2"></i>
                                                    </a>
                                                @elseif($currentLevel == 'project')
                                                    <i class="bi bi-person-badge me-2"></i>{{ $result->project }}
                                                    <i class="bi bi-chevron-right ms-2"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ url('/admin/application-list/' . ($currentLevel == 'district' ? $result->Pref_Districts : $districtId)) .
                                                    ($currentLevel == 'project' ? '?cdpo_id=' . $result->project_code : '') .
                                                    ($currentLevel == 'awc' ? '?&cdpo_id=' . $cdpoId . '&awc_id=' . $result->awc_id : '') }}"
                                                    class="btn btn-sm d-inline-flex align-items-center gap-2 px-3 rounded-pill shadow-sm"
                                                    style="background-color: {{ $result->submitted_count > 0 ? '#e6f4ea' : '#f8f9fa' }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        transition: 0.3s;">
                                                    <i
                                                        class="ri-eye-line {{ $result->submitted_count > 0 ? 'text-success' : 'text-danger' }} fs-5"></i>
                                                    {{ $result->submitted_count }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ url('/admin/verified-list/' . ($currentLevel == 'district' ? $result->Pref_Districts : $districtId)) .
                                                    ($currentLevel == 'project' ? '?cdpo_id=' . $result->project_code : '') .
                                                    ($currentLevel == 'awc' ? '?cdpo_id=' . $cdpoId . '&awc_id=' . $result->awc_id : '') }}"
                                                    class="btn btn-sm d-inline-flex align-items-center gap-2 px-3 rounded-pill shadow-sm"
                                                    style="background-color: {{ $result->approved_count > 0 ? '#e6f4ea' : '#f8f9fa' }};                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   transition: 0.3s;">
                                                    <i
                                                        class="ri-eye-line {{ $result->approved_count > 0 ? 'text-success' : 'text-danger' }} fs-5"></i>
                                                    {{ $result->approved_count }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ url('/admin/rejected-list/' . ($currentLevel == 'district' ? $result->Pref_Districts : $districtId)) .
                                                    ($currentLevel == 'project' ? '?cdpo_id=' . $result->project_code : '') .
                                                    ($currentLevel == 'awc' ? '?cdpo_id=' . $cdpoId . '&awc_id=' . $result->awc_id : '') }}"
                                                    class="btn btn-sm d-inline-flex align-items-center gap-2 px-3 rounded-pill shadow-sm"
                                                    style="background-color: {{ $result->rejected_count > 0 ? '#fce8e6' : '#f8f9fa' }};                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              transition: 0.3s;">
                                                    <i
                                                        class="ri-eye-line {{ $result->rejected_count > 0 ? 'text-danger' : 'text-muted' }} fs-5"></i>
                                                    {{ $result->rejected_count }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $('#datatable').DataTable({
                "autoWidth": false,
                "paging": true,
                "lengthMenu": [10, 25, 50, 100],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'fB>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>", // Customize the layout
                buttons: [
                    'excel'
                ]
            });
        });
        // // Export all data in Excel
        // $('#export-excel').on('click', function() {
        //     window.location.href = "{{ route('exportDistrict_wise_applications.export') }}";
        // }); 
        // Export all data in Excel
        $('#export-excel').on('click', function() {
                    $.ajax({
                        url: "{{ route('exportDistrict_wise_applications.export') }}",
                        method: 'GET',
                        data: {
                            checkOnly: true
                        },
                        success: function(response) {
                            if (response.count > 0) {
                                window.location.href =
                                    "{{ route('exportDistrict_wise_applications.export') }}";
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
    </script>
@endsection
