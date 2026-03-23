@extends('layouts.dahboard_layout')

@section('styles')
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
                        @if (session('sess_role') === 'Super_admin')
                    <li class="breadcrumb-item active">रिपोर्ट्स</li>
                    @endif
                    <li class="breadcrumb-item active">अपात्र  आवेदन पत्र की सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list me-2"></i> अपात्र  आवेदन पत्र
                            </h5>
                            <button id="export-excel" class="btn btn-success float-end"><i class="bi bi-download"></i>
                                Download Excel</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="datatable" class="table table-striped table-bordered cell-border">
                                <thead>
                                    <tr>
                                        <!--<th>ID</th> yeh application ka id hai-->
                                        <th>क्रमांक</th> <!--यह Row का ID है-->
                                        <th>पूरा नाम</th>
                                        <th>पद का नाम</th>
                                        <th>मोबाइल नंबर</th>
                                        <th>आवेदन तिथि</th>
                                        <th>अपात्र का विवरण</th>
                                        <th>एक्शन </th>
                                        <th>आवेदन स्थिति</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rejected_lists as $index => $rejected_list)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $rejected_list->Full_Name }}</td>
                                            <td>{{ $rejected_list->title }}</td>
                                            <td>{{ $rejected_list->Mobile_Number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($rejected_list->Application_date)->format('d/m/Y') }}
                                            </td>
                                            <td>{{ $rejected_list->reason_rejection }}</td>
                                            <td>
                                                <a
                                                    href="{{ url('/admin/final-application-detail/' . $rejected_list->Encrypted_Applicant_ID . '/' . $rejected_list->Encrypted_Apply_ID) }}">
                                                    <button class="btn btn-info btn-sm text-white"> <i class="bi bi-eye"></i></button>
                                                </a>
                                            </td>
                                            <td>
                                                @if ($rejected_list->Application_Status == 'Submitted')
                                                    <span class="badge bg-secondary">Submitted</span> {{-- gray के लिए bg-secondary --}}
                                                @elseif($rejected_list->Application_Status == 'Rejected')
                                                    <span class="badge bg-danger">Rejected</span> {{-- red --}}
                                                @elseif($rejected_list->Application_Status == 'Verified')
                                                    <span class="badge bg-success">Verified</span> {{-- green --}}
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

        // Export all data in Excel
        $('#export-excel').on('click', function() {
            const urlSegments = window.location.pathname.split('/');
            const segment = urlSegments.filter(Boolean).pop(); //take the segment data
            const exportUrl = "{{ route('rejectedList.export') }}/" + segment;
            $.ajax({
                url: exportUrl,
                method: 'GET',
                data: {
                    checkOnly: true
                },
                success: function(response) {
                    if (response.count > 0) {
                        window.location.href = "{{ route('rejectedList.export') }}";
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
