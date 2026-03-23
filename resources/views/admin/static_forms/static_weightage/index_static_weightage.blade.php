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
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">वेटेज मार्क्स प्रबंधन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">वेटेज मार्क्स की सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-calculator"> </i> वेटेज मार्क्स की सूची</h5>
                        <a href="{{ route('admin.static-weightage.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> नया वेटेज जोड़ें
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="weightageTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>क्र.</th>
                                        <th>पद नाम</th>
                                        <th>वेटेज कॉन्फ़िगरेशन स्थिति</th>
                                        <th>एक्शन </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts as $index => $post)
                                        @php
                                            // Check if configured in master_weightage_config
                                            $weightageConfigCount = (int) data_get($post, 'weightage_config_count', 0);
                                            $isConfigured = $weightageConfigCount > 0;
                                            $postName = data_get($post, 'title', 'N/A');
                                            $postConfigId = (int) data_get($post, 'id', 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $postName }}</td>
                                            <td>
                                                @if ($isConfigured)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> कॉन्फ़िगर किया गया है
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> कॉन्फ़िगर नहीं किया गया
                                                        है
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($isConfigured && $postConfigId > 0)
                                                    <a href="{{ route('admin.static-weightage.edit', $postConfigId) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil-square"></i> संपादित करें
                                                    </a>
                                                @elseif ($postConfigId > 0)
                                                    <a href="{{ route('admin.static-weightage.create') }}?post_config_id={{ $postConfigId }}"
                                                        class="btn btn-sm btn-success">
                                                        <i class="bi bi-plus-circle"></i> वेटेज जोड़ें
                                                    </a>
                                                @else
                                                    <span class="text-muted small">उपलब्ध नहीं</span>
                                                @endif
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
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Display SweetAlert for success message
            @if (session('success'))
                Swal.fire({
                    title: "सफल!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    confirmButtonText: "ठीक है",
                    confirmButtonColor: "#28a745"
                });
            @endif

            // Display SweetAlert for error message
            @if (session('error'))
                Swal.fire({
                    title: "त्रुटि!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    confirmButtonText: "ठीक है",
                    confirmButtonColor: "#dc3545"
                });
            @endif

            $('#weightageTable').DataTable({
                "pageLength": 10,
                "language": {
                    "zeroRecords": "कोई रिकॉर्ड नहीं मिला",
                    "infoEmpty": "कोई रिकॉर्ड उपलब्ध नहीं है",
                    "infoFiltered": "(_MAX_ कुल रिकॉर्ड्स में से फ़िल्टर किया गया)",
                    "search": "खोजें:",
                }
            });
        });
    </script>
@endsection
