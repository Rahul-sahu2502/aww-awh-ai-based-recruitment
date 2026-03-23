@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        a {
            font-size: medium;
        }

        /* Dashboard Cards Styling */
        .card-hover-effect:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .dashboard-card {
            background: linear-gradient(135deg, #fff4eb, #ffffff);
            border-radius: 1.2rem;
            transition: all 0.3s ease;
        }

        .dashboard-card-blue {
            background: linear-gradient(135deg, #615cf2, #aabfed);
        }

        .dashboard-card-green {
            background: linear-gradient(135deg, #4ab071, #a5f1e2);
        }

        .dashboard-card-red {
            background: linear-gradient(135deg, #d04f35, #eca18e);
        }

        .dashboard-card-orange {
            background: linear-gradient(135deg, #e57d37, #f5be97);
        }

        .dashboard-card-purple {
            background: linear-gradient(135deg, #a869d2, #c39eed);
        }

        .card-icon-orange {
            background: linear-gradient(135deg, #ba4c03, #ff9447);
        }

        .card-icon-blue {
            background: linear-gradient(135deg, #040086, #2563eb);
        }

        .card-icon-green {
            background: linear-gradient(135deg, #026b2a, #16a085);
        }

        .card-icon-red {
            background: linear-gradient(135deg, #6b1402, #be3e1e);
        }

        .card-icon-purple {
            background: linear-gradient(135deg, #460273, #732fc1);
        }

        /* Registration Sidebar Styles */
        .registration-sidebar {
            top: 20px;
        }

        .registration-card {
            background: linear-gradient(135deg, #e9f5ff, #ffffff);
            border-radius: 1.2rem;
            height: calc(100vh - 200px);
            max-height: 350px;
            min-height: 300px;
        }

        .registration-header {
            background: linear-gradient(135deg, #1b4d91, #2563eb);
            border-radius: 1.2rem 1.2rem 0 0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .registration-header-icon {
            font-size: 1.2rem;
        }

        .registration-content {
            height: calc(100% - 120px);
        }

        .registration-scrollbar {
            flex-grow: 1;
        }

        .registration-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .registration-scrollbar::-webkit-scrollbar-track {
            background: rgba(241, 196, 15, 0.1);
            border-radius: 10px;
        }

        .registration-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #1b4d91, #2563eb);
        }

        .registration-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #2563eb, #1b4d91);
        }

        .registration-item {
            background: linear-gradient(135deg, #f8f9fc, #ffffff);
            border: 1px solid rgba(27, 77, 145, 0.1);
            transition: all 0.3s ease;
        }

        .registration-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(27, 77, 145, 0.15);
            border-color: rgba(27, 77, 145, 0.3);
        }

        .registration-content-container {
            min-width: 0;
            overflow: hidden;
        }

        .registration-icon {
            min-width: 40px;
            min-height: 40px;
            width: 40px;
            height: 40px;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .registration-icon-pending {
            background: linear-gradient(135deg, #f1c40f, #f39c12);
        }

        .registration-icon-verified {
            background: linear-gradient(135deg, #149e49, #16a085);
        }

        .registration-icon-review {
            background: linear-gradient(135deg, #1b4d91, #2563eb);
        }

        .registration-icon-rejected {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .registration-icon-process {
            background: linear-gradient(135deg, #520486, #6a14ce);
        }

        .registration-text-container {
            min-width: 0;
            overflow: hidden;
        }

        .registration-name {
            color: #1b4d91;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .registration-category {
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .registration-badge {
            font-size: 0.7rem;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .registration-badge-pending {
            background: linear-gradient(135deg, #f1c40f, #f39c12);
        }

        .registration-badge-verified {
            background: linear-gradient(135deg, #149e49, #16a085);
        }

        .registration-badge-review {
            background: linear-gradient(135deg, #1b4d91, #2563eb);
        }

        .registration-badge-rejected {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .registration-badge-process {
            background: linear-gradient(135deg, #520486, #6a14ce);
        }

        .registration-footer {
            background: linear-gradient(135deg, #f8f9fc, #ffffff);
            border-radius: 0 0 1.2rem 1.2rem;
            position: sticky;
            bottom: 0;
        }

        .registration-footer-btn {
            background: linear-gradient(135deg, #1b4d91, #2563eb);
            border-radius: 0.8rem;
            padding: 0.5rem 1.2rem;
            transition: all 0.3s ease;
        }

        .registration-footer-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(27, 77, 145, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .registration-card {
                max-height: 450px;
                min-height: 400px;
            }
        }

        @media (max-width: 992px) {
            .registration-card {
                height: auto !important;
                max-height: none !important;
                min-height: 350px !important;
            }

            .registration-sidebar {
                position: static !important;
                top: auto !important;
            }
        }

        @media (max-width: 768px) {
            .card-body h3 {
                font-size: 1.2rem !important;
            }

            .card-body .fw-semibold {
                font-size: 0.8rem !important;
            }

            .card-icon {
                width: 38px !important;
                height: 38px !important;
                font-size: 0.9rem !important;
            }

            .registration-name {
                font-size: 0.8rem;
            }

            .registration-category {
                font-size: 0.7rem;
            }

            .registration-badge {
                font-size: 0.65rem;
            }

            .registration-icon {
                width: 35px;
                height: 35px;
                min-width: 35px;
                min-height: 35px;
                font-size: 0.8rem;
            }

            .registration-item {
                margin-bottom: 0.5rem !important;
                padding: 0.7rem !important;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 0.75rem !important;
            }

            .registration-card {
                min-height: 300px !important;
            }
        }

        /* Sticky sidebar adjustment */
        @media (min-width: 992px) {
            .sticky-top {
                position: sticky !important;
                top: 20px !important;
            }
        }

        /* ===================================
                                           Advertisement Table Styles (Bootstrap)
                                        =================================== */
        .nested-row {
            display: none;
        }

        .nested-row.show {
            display: table-row;
        }

        .expand-btn i {
            transition: transform 0.3s ease;
        }

        .expand-btn.expanded i {
            transform: rotate(90deg);
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">

        <div class="pagetitle">
            <h5 class="fw-bold">डैशबोर्ड</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/examinor/examinor-dashboard">होम</a></li>
                    <li class="breadcrumb-item active">डैशबोर्ड</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row gy-3">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <!-- First row of cards -->
                    <div class="row">
                        <!-- Advertisement Count Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-blue">
                                <div class="card-body px-3 py-3">
                                    <a href="{{ url('/admin/show-advertisment') }}" class="text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                    कुल विज्ञापन
                                                </div>
                                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                    {{ $data['totalAdvertisment'] }}
                                                </h3>
                                            </div>
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-blue text-white"
                                                style="width: 42px; height: 42px; font-size: 1rem;">
                                                <i class="bi bi-megaphone"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Total Posts Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-blue">
                                <div class="card-body px-3 py-3">
                                    <a href="{{ url('/admin/show-posts') }}" class="text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                    कुल पद
                                                </div>
                                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                    {{ $data['totalPosts'] }}
                                                </h3>
                                            </div>
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-blue text-white"
                                                style="width: 42px; height: 42px; font-size: 1rem;">
                                                <i class="bi bi-journal-text"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Total Vacancy Card (Placeholder) -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-blue">
                                <div class="card-body px-3 py-3">
                                    <a href="{{ url('/admin/show-posts') }}" class="text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                    कुल रिक्तियां
                                                </div>
                                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                    {{ $data['news_num'] ?? 0 }}
                                                </h3>
                                            </div>
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-blue text-white"
                                                style="width: 42px; height: 42px; font-size: 1rem;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second row of cards -->
                    <div class="row">
                        <!-- Total Applications Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-purple">
                                <div class="card-body px-3 py-3">
                                    <a href="{{ url('/admin/application-list') }}" class="text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                    कुल आवेदन
                                                </div>
                                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                    {{ $data['application_count'] }}
                                                </h3>
                                            </div>
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-purple text-white"
                                                style="width: 42px; height: 42px; font-size: 1rem;">
                                                <i class="bi bi-file-earmark"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Total Verified Applications Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-green">
                                <div class="card-body px-3 py-3">
                                    <a href="{{ url('/admin/verified-list') }}" class="text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                    कुल पात्र  आवेदन
                                                </div>
                                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                    {{ $data['verified_count'] }}
                                                </h3>
                                            </div>
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-green text-white"
                                                style="width: 42px; height: 42px; font-size: 1rem;">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Total Rejected Applications Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-red">
                                <div class="card-body px-3 py-3">
                                    <a href="{{ url('/admin/rejected-list') }}" class="text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                    कुल अपात्र  आवेदन
                                                </div>
                                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                    {{ $data['rejected_count'] }}
                                                </h3>
                                            </div>
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-red text-white"
                                                style="width: 42px; height: 42px; font-size: 1rem;">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Third row of cards -->
                    <div class="row">
                        <!-- Total Pending Applications Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card info-card shadow-sm border-0 card-hover-effect dashboard-card-orange">
                                <div class="card-body px-3 py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold mb-1" style="font-size: 0.9rem; color: #ffffff;">
                                                कुल लंबित आवेदन
                                            </div>
                                            <h3 class="fw-bold text-light mb-0" style="font-size: 1.4rem;">
                                                {{ $data['pending_count'] }}
                                            </h3>
                                        </div>
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center shadow-sm card-icon-orange text-white"
                                            style="width: 42px; height: 42px; font-size: 1rem;">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End col-lg-12 -->
            </div>
            <!-- Charts Row -->
            <div class="row mt-2 ">
                <!-- Overall Application Count Chart (Pie) -->
                <div class="col-lg-6 d-flex">
                    <div class="card shadow-sm rounded-4 p-4 w-100"
                        style="background: linear-gradient(135deg, #eafaf1, #ffffff);">
                        <h6 class="text-dark fw-bold mb-3 text-start">श्रेणी के अनुसार आवेदन</h6>
                        <div id="application-overview"
                            data-vals='["{{ $data['pending_count'] }}","{{ $data['verified_count'] }}","{{ $data['rejected_count'] }}"]'
                            style="min-height: 250px;"></div>
                    </div>
                </div>

                <!-- Last 7 Days Application Trend (Line) -->
                <div class="col-lg-6 d-flex">
                    <div class="card shadow-sm rounded-4 p-4 w-100"
                        style="background: linear-gradient(135deg, #f0f7fd, #ffffff);">
                        <h6 class="text-dark fw-bold mb-3 text-start">पिछले 7 दिनों की तिथि अनुसार आवेदन और
                            समीक्षा किए गए
                            आवेदन</h6>
                        <div id="application-overview2"
                            data-applicaton-date='@json(collect($no_of_application)->pluck('date_only'))'
                            data-applicaton-no='@json(collect($no_of_application)->pluck('total_applied'))'
                            data-applicaton-eligible='@json(collect($no_of_application)->pluck('total_eligible'))'
                            style="min-height: 250px;">
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Charts Row -->

            <!-- Qualification overview charts -->
            <div class="row mt-2">
                <div class="col-lg-8 d-flex">
                    <div class="card shadow-sm rounded-4 p-4 w-100"
                        style="min-height: 240px; background: linear-gradient(135deg, #f0f7fd, #ffffff); border-radius: 1.2rem;">
                        <h6 class="mb-3 fw-semibold text-start">उच्चतम योग्यता के आधार पर आवेदनों की संख्या</h6>
                        <div id="qualification-overview"
                            data-quali-id='@json(collect($data['qualiChartData'])->pluck('fk_Quali_ID'))'
                            data-quali-name='@json(collect($data['qualiChartData'])->pluck('qualification_name'))'
                            data-quali-percentage='@json(collect($data['qualiChartData'])->pluck('application_count'))'>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 d-flex">
                    <div id="subChartContainer" class="card shadow-sm rounded-4 p-4 w-100"
                        style="min-height: 240px; background: linear-gradient(135deg, #f0f7fd, #ffffff); border-radius: 1.2rem;">
                        <h6 id="subChartTitle" class="mb-3 fw-semibold text-start">योग्यता विवरण</h6>
                        <div id="chartSubContainer" class="text-center"></div>
                    </div>
                </div>
            </div>

            <!-- Advertisement Table Section -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white d-flex justify-content-between align-items-center flex-wrap"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="mb-0">📋 विज्ञापन-वार विस्तृत विवरण</h5>
                            <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
                                <button class="btn btn-light btn-sm" onclick="exportToExcel()">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </button>
                                <button class="btn btn-light btn-sm" onclick="window.print()">
                                    <i class="bi bi-printer"></i> Print
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0" id="advertisementTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">क्रम</th>
                                            <th>विज्ञापन शीर्षक</th>
                                            <th style="width: 100px;" class="text-center">कुल पद</th>
                                            <th style="width: 120px;" class="text-center">कुल आवेदन</th>
                                            <th style="width: 100px;" class="text-center">पात्र </th>
                                            <th style="width: 100px;" class="text-center">लंबित</th>
                                            <th style="width: 100px;" class="text-center">अपात्र </th>
                                            <th style="width: 120px;" class="text-center">कार्यवाही</th>
                                        </tr>
                                    </thead>
                                    <tbody id="advertisementTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-3 text-muted">डेटा लोड हो रहा है...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.18.0/js/md5.min.js"></script>
    <script src="{{ asset('assets/lib/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        const applicationChartElement = document.querySelector("#application-overview");
        let rawVals = JSON.parse(applicationChartElement.getAttribute("data-vals") || '[0, 0, 0]');
        let dataVals = [0, 0, 0].map((_, i) => Number(rawVals[i]) || 0);
        const isAllZero = dataVals.every(val => val === 0);
        const displayVals = isAllZero ? [1, 1, 1] : dataVals;

        const countOptions = {
            series: displayVals,
            chart: {
                type: 'pie',
                height: 250,
                events: {
                    dataPointSelection: function (event, chartContext, config) {
                        const index = config.dataPointIndex;
                        const routes = [
                            "{{ url('/admin/application-list') }}",
                            "{{ url('/admin/verified-list') }}",
                            "{{ url('/admin/rejected-list') }}"
                        ];
                        if (!isAllZero && routes[index]) {
                            window.location.href = routes[index];
                        }
                    }
                }
            },
            labels: ['लंबित आवेदन', 'पात्र  आवेदन', 'अपात्र  आवेदन'],
            colors: ['#f1c40f', '#2ecc71', '#e74c3c'],
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 'bold'
            },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function (val) {
                        return isAllZero ? '0' : val;
                    }
                }
            },
            dataLabels: {
                formatter: function (val) {
                    return isAllZero ? '0%' : val.toFixed(1) + '%';
                }
            },
            plotOptions: {
                pie: {
                    expandOnClick: true
                }
            }
        };

        const applicationCountChart = new ApexCharts(applicationChartElement, countOptions);
        applicationCountChart.render();


        // Line Chart - Last 7 Days Application Overview
        const chartElement2 = document.querySelector("#application-overview2");
        const dateVals = JSON.parse(chartElement2.getAttribute("data-applicaton-date"));
        const applicationVals = JSON.parse(chartElement2.getAttribute("data-applicaton-no"));
        const eligibleVals = JSON.parse(chartElement2.getAttribute("data-applicaton-eligible"));

        // Calculate max Y-axis value for better spacing
        const maxY = Math.max(...applicationVals.concat(eligibleVals)) + 1;

        // Format date from "YYYY-MM-DD" to "DD-MM-YYYY"
        const formattedDateVals = dateVals.map(date => {
            const parts = date.split('-'); // ["2025", "04", "24"]
            return `${parts[2]}-${parts[1]}-${parts[0]}`; // "24-04-2025"
        });

        var options = {
            chart: {
                type: 'line',
                height: 250,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            series: [{
                name: 'आवेदन',
                data: applicationVals
            },
            {
                name: 'समीक्षित आवेदन',
                data: eligibleVals
            }
            ],
            colors: ['#1E88E5', '#43A047'], // Blue & Green shades
            xaxis: {
                categories: formattedDateVals,
                labels: {
                    rotate: -45
                }
            },
            yaxis: {
                min: 0,
                max: maxY,
                labels: {
                    formatter: function (val) {
                        return Math.round(val);
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 5,
                colors: ['#fff'],
                strokeColors: ['#1E88E5', '#43A047'],
                strokeWidth: 2
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'left',
                labels: {
                    colors: '#333'
                },
                fontSize: '12px',
                fontWeight: 'bold'
            }
        };

        var chart2 = new ApexCharts(chartElement2, options);
        chart2.render();



        var csrf_token = "{{ csrf_token() }}";

        $(document).ready(function () {
            'use strict';
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard_view_application_detail') }}"
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
                    data: 'Title',
                    name: 'Title'
                },
                {
                    data: 'Mobile_Number',
                    name: 'Mobile_Number'
                },
                {
                    data: 'Application_date',
                    name: 'Application_date',
                    render: function (data, type, row) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                        // Formats to "DD/MM/YYYY"
                    }
                },
                {
                    data: 'Applicant_ID',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        // let encryptedId = md5(data); // ✅ MD5 convert Applicant_ID
                        let encryptedApplicantId = row.EncryptedID;
                        let encryptedApplicationId = row.Application_Id;
                        return `
                                                        <td>
                                                            <a href="/admin/final-application-detail/${encryptedApplicantId}/${encryptedApplicationId}">
                                                                <button class="btn btn-info btn-sm text-white"><i class="ri-eye-line"></i></button>
                                                            </a>
                                                        </td>
                                                    `;
                    }
                },
                {
                    data: 'Application_Status',
                    name: 'Application_Status'
                }
                ],
                order: [
                    [0, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                dom: '<"top"lf>rt<"bottom"ip><"clear">', // UI ke liye Custom Layout
                language: {
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });
        });


        // Qualification Overview Chart
        const chartElement3 = document.querySelector("#qualification-overview");
        const qualiIds = JSON.parse(chartElement3.getAttribute("data-quali-id") || "[]");
        const qualiNames = JSON.parse(chartElement3.getAttribute("data-quali-name") || "[]");
        const qualiPercents = JSON.parse(chartElement3.getAttribute("data-quali-percentage") || "[]");

        if (qualiNames.length > 0 && qualiPercents.length > 0) {
            const options = {
                series: [{
                    name: 'आवेदन',
                    data: qualiPercents
                }],
                chart: {
                    type: 'bar',
                    height: 230,
                    events: {
                        dataPointSelection: function (event, chartContext, config) {
                            const qualiId = qualiIds[config.dataPointIndex];
                            if (qualiId) loadInlineChart(qualiId);
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        columnWidth: '45%',
                        distributed: true
                    }
                },
                colors: ['#2ecc71', '#3498db', '#f1c40f', '#e67e22', '#9b59b6', '#34495e'],
                xaxis: {
                    categories: qualiNames,
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    max: Math.max(...qualiPercents) + 2,
                    labels: {
                        style: {
                            fontSize: '12px'
                        },
                        formatter: val => Number.isInteger(val) ? val : ''
                    }
                },
                tooltip: {
                    custom: ({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) => {
                        const label = w.globals.labels[dataPointIndex];
                        const value = series[seriesIndex][dataPointIndex];
                        const color = w.config.colors[dataPointIndex % w.config.colors.length];

                        return `
                                    <div style="padding:10px;color:white;background:${color};border-radius:6px;">
                                        <strong>${label}</strong><br>
                                        आवेदन: ${value}
                                    </div>
                                `;
                    }
                },
                legend: {
                    show: false,
                    fontSize: '12px',
                    fontWeight: 'bold'
                }
            };

            new ApexCharts(chartElement3, options).render();

            for (let i = 0; i < qualiPercents.length; i++) {
                if (qualiPercents[i] > 0) {
                    loadInlineChart(qualiIds[i]);
                    break;
                }
            }
        }

        function loadInlineChart(qualiId) {
            const qualiIndex = qualiIds.indexOf(qualiId);
            const qualiName = qualiNames[qualiIndex] || 'चयनित योग्यता';
            const chartSubContainer = document.getElementById('chartSubContainer');
            const subChartTitle = document.getElementById('subChartTitle');
            const palette = ['#3498db', '#1abc9c', '#f39c12', '#e74c3c'];

            getSubChartData(qualiId).then(data => {
                chartSubContainer.innerHTML = '';
                subChartTitle.innerText = `${qualiName} – प्रतिशत अनुसार`;

                new ApexCharts(chartSubContainer, {
                    chart: {
                        type: 'donut',
                        height: 230
                    },
                    series: data.values,
                    labels: data.categories,
                    colors: palette,
                    legend: {
                        position: 'bottom',
                        fontSize: '12px',
                        fontWeight: 'bold'
                    },
                    tooltip: {
                        y: {
                            formatter: val => `${val} आवेदन`
                        }
                    }
                }).render();
            }).catch(err => {
                console.error('Error:', err);
                chartSubContainer.innerHTML = `<div class='text-danger'>डेटा लोड करने में समस्या</div>`;
            });
        }

        function getSubChartData(qualiId) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `/admin/admin-percentage-wise-chart/${qualiId}`,
                    method: 'GET',
                    success: resolve,
                    error: reject
                });
            });
        }

        // ===================================
        // Advertisement Table Functionality
        // ===================================
        let advertisementsData = [];
        let expandedRows = new Set();

        // Load advertisement data on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadAdvertisements();
        });

        async function loadAdvertisements() {
            try {
                const response = await fetch("{{ url('/admin/advertisement-analytics') }}");
                const result = await response.json();

                if (result.success) {
                    advertisementsData = result.data;
                    renderAdvertisementTable();
                } else {
                    showError('डेटा लोड करने में त्रुटि');
                }
            } catch (error) {
                console.error('Error loading advertisements:', error);
                showError('सर्वर से जुड़ने में समस्या');
            }
        }

        function renderAdvertisementTable() {
            const tbody = document.getElementById('advertisementTableBody');

            if (advertisementsData.length === 0) {
                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                            <h5 class="text-muted">कोई विज्ञापन उपलब्ध नहीं</h5>
                                        </td>
                                    </tr>
                                `;
                return;
            }

            tbody.innerHTML = advertisementsData.map((ad, index) => `
                                <tr onclick="togglePostDetails(${ad.Advertisement_ID})" style="cursor: pointer;">
                                    <td class="text-center">${index + 1}</td>
                                    <td><strong>${ad.Advertisement_Title}</strong></td>
                                    <td class="text-center"><span class="badge bg-primary">${ad.total_posts || 0}</span></td>
                                    <td class="text-center"><span class="badge bg-primary">${ad.total_applications || 0}</span></td>
                                    <td class="text-center"><span class="badge bg-success">${ad.approved_count || 0}</span></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">${ad.pending_count || 0}</span></td>
                                    <td class="text-center"><span class="badge bg-danger">${ad.rejected_count || 0}</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary expand-btn" id="btn-${ad.Advertisement_ID}" onclick="event.stopPropagation(); togglePostDetails(${ad.Advertisement_ID})">
                                            <i class="bi bi-chevron-right"></i> विस्तार
                                        </button>
                                    </td>
                                </tr>
                                <tr class="nested-row" id="nested-${ad.Advertisement_ID}">
                                    <td colspan="8" class="bg-light">
                                        <div class="p-3" id="container-${ad.Advertisement_ID}">
                                            <div class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-3 text-muted">पद विवरण लोड हो रहा है...</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `).join('');
        }

        async function togglePostDetails(advertisementId) {
            const nestedRow = document.getElementById(`nested-${advertisementId}`);
            const btn = document.getElementById(`btn-${advertisementId}`);

            if (expandedRows.has(advertisementId)) {
                // Collapse
                nestedRow.classList.remove('show');
                btn.classList.remove('expanded');
                btn.innerHTML = '<i class="bi bi-chevron-right"></i> विस्तार';
                expandedRows.delete(advertisementId);
            } else {
                // Expand
                nestedRow.classList.add('show');
                btn.classList.add('expanded');
                btn.innerHTML = '<i class="bi bi-chevron-down"></i> छुपाएं';
                expandedRows.add(advertisementId);

                // Load post details if not already loaded
                await loadPostDetails(advertisementId);
            }
        }

        async function loadPostDetails(advertisementId) {
            const container = document.getElementById(`container-${advertisementId}`);

            try {
                const response = await fetch(`{{ url('/admin/post-analytics') }}/${advertisementId}`);
                const result = await response.json();

                if (result.success && result.data.length > 0) {
                    renderPostTable(container, result.data);
                } else {
                    container.innerHTML = `
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox fs-2 text-muted opacity-50"></i>
                                            <p class="text-muted mt-2">इस विज्ञापन में कोई पद उपलब्ध नहीं</p>
                                        </div>
                                    `;
                }
            } catch (error) {
                console.error('Error loading posts:', error);
                container.innerHTML = `
                                    <div class="text-center py-4">
                                        <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                                        <p class="text-danger mt-2">पद विवरण लोड करने में त्रुटि</p>
                                        <button onclick="loadPostDetails(${advertisementId})" class="btn btn-sm btn-primary mt-2">
                                            पुनः प्रयास करें
                                        </button>
                                    </div>
                                `;
            }
        }

        function renderPostTable(container, posts) {
            container.innerHTML = `
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th class="text-center">क्रम</th>
                                                <th>पद का नाम</th>
                                                <th class="text-center">रिक्तियां</th>
                                                <th class="text-center">कुल आवेदन</th>
                                                <th class="text-center">पात्र </th>
                                                <th class="text-center">लंबित</th>
                                                <th class="text-center">अपात्र </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${posts.map((post, index) => `
                                                                <tr>
                                                                    <td class="text-center">${index + 1}</td>
                                                                    <td><strong>${post.post_name} (${post.project_name})</strong></td>
                                                                    <td class="text-center"><span class="badge bg-primary">${post.vacancies || 0}</span></td>
                                                                    <td class="text-center"><span class="badge bg-primary">${post.total_applications || 0}</span></td>
                                                                    <td class="text-center"><span class="badge bg-success">${post.approved_count || 0}</span></td>
                                                                    <td class="text-center"><span class="badge bg-warning text-dark">${post.pending_count || 0}</span></td>
                                                                    <td class="text-center"><span class="badge bg-danger">${post.rejected_count || 0}</span></td>
                                                                </tr>
                                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            `;
        }

        function showError(message) {
            const tbody = document.getElementById('advertisementTableBody');
            tbody.innerHTML = `
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-exclamation-triangle fs-1 text-danger d-block mb-3"></i>
                                        <h5 class="text-danger">${message}</h5>
                                        <button onclick="loadAdvertisements()" class="btn btn-primary mt-3">
                                            पुनः प्रयास करें
                                        </button>
                                    </td>
                                </tr>
                            `;
        }

        // Export to Excel
        function exportToExcel() {
            const table = document.getElementById('advertisementTable');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "विज्ञापन विवरण"
            });
            XLSX.writeFile(wb, `विज्ञापन_विवरण_${new Date().toISOString().split('T')[0]}.xlsx`);
        }
    </script>

    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endsection