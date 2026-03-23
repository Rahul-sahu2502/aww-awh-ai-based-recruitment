@extends('layouts.dahboard_layout')

@section('styles')
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">दावा आपत्ति निराकरण</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if (session('sess_role') === 'Super_admin')
                            <a href="{{ url('/admin/admin-dashboard') }}">होम</a>
                        @elseif (session('sess_role') === 'Admin' || session('sess_role') === 'Supervisor' || session('sess_role') === 'CDPO')
                            <a href="{{ url('/examinor/examinor-dashboard') }}">होम</a>
                        @endif
                        @if (session('sess_role') === 'Super_admin')
                    <li class="breadcrumb-item active">दावा आपत्ति निराकरण</li>
                    @endif
                    <li class="breadcrumb-item active">दावा आपत्ति आवेदन सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list"> </i>दावा आपत्ति आवेदन सूची
                            </h5>
                            <div id="exportBtnContainer" class="d-flex align-items-center"></div>
                        </div>
                    </div>


                    <div class="card-body">
                        <div class="row col-lg-12 p-2 align-items-end">
                            <div class="col-lg-1"></div>

                            <div class="col-lg-4">
                                <label class="form-label fw-bold">कब से</label>
                                <input type="date" name="fromDate" id="fromDate" class="form-control">
                            </div>

                            <div class="col-lg-4">
                                <label class="form-label fw-bold">कब तक</label>
                                <input type="date" name="toDate" id="toDate" class="form-control">
                            </div>

                            <div class="col-lg-3">
                                <button type="button" class="btn btn-primary mt-4" id="filterDavaApatti">
                                    फ़िल्टर करें
                                </button>
                                <button type="reset" class="btn btn-secondary mt-4" id="resetFilter">
                                    रीसेट
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="datatable" class="table table-striped table-bordered cell-border">
                                <thead>
                                    <tr>
                                        <th>क्रमांक</th> <!--यह Row का ID है-->
                                        <th>पूरा नाम</th>
                                        <th>पद का नाम</th>
                                        <th>आवेदन तिथि</th>
                                        <th>दावा /आपत्ति</th>
                                        <th>विषय</th>
                                        <th>स्थिति</th>
                                        <th>एक्शन </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($claim_list as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->full_name ?? '-' }}</td>
                                            <td>{{ $row->title ?? '-' }} ({{ $row->application_num ?? '-' }})</td>
                                            <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                                            <td> {{ $row->request_type === 'claim' ? 'दावा (Claim)' : ($row->request_type === 'objection' ? 'आपत्ति (Objection)' : '-') }}
                                            </td>
                                            <td>{{ ucfirst($row->request_category) }}</td>
                                            <td> @php
                                                // Badge color mapping
                                                $badgeClass = match ($row->claim_status) {
                                                    'Submitted' => 'bg-warning',
                                                    'InReview' => 'bg-info',
                                                    'Resolved' => 'bg-primary',
                                                    'Approved' => 'bg-success',
                                                    'Rejected' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                }; // Status label in Hindi
                                                $statusHindi = match ($row->claim_status) {
                                                    'Submitted' => 'प्रतीक्षारत',
                                                    'InReview' => 'समीक्षा में',
                                                    'Resolved' => 'हल किया',
                                                    'Approved' => 'स्वीकृत',
                                                    'Rejected' => 'अस्वीकृत',
                                                    default => 'अज्ञात स्थिति',
                                                };
                                            @endphp <span
                                                    class="badge {{ $badgeClass }}">{{ $statusHindi }}</span> </td>
                                            <td> <a href="{{ url('admin/view-dava-apatti/' . $row->claim_id) }}"
                                                    class="btn btn-sm btn-primary view-claim"
                                                    data-id="{{ $row->claim_id }}"> <i class="bi bi-eye"></i> देखें </a>
                                            </td>
                                    </tr> @empty -
                                    @endforelse
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
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    
    <script>
        $(document).ready(function() {
            let table = $('#datatable').DataTable({
                autoWidth: false,
                paging: true,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    emptyTable: 'कोई रिकॉर्ड उपलब्ध नहीं है'
                },
                dom:
                    "<'row'<'col-sm-6'l><'col-sm-6 text-end d-none'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-download"></i> Excel Export',
                        title: 'दावा_आपत्ति_आवेदन_सूची',
                        exportOptions: {
                            columns: ':visible:not(:last-child)' // Action column exclude
                        },
                        className: 'btn btn-outline-primary'
                    }
                ]
            });

            // Move the existing export button into the header placeholder (red box area)
            table.buttons().container().appendTo('#exportBtnContainer');



            $('#filterDavaApatti').on('click', function() {

                let fromDate = $('#fromDate').val();
                let toDate = $('#toDate').val();

                $.ajax({
                    url: "{{ route('admin.filter.dava.apatti') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        fromDate: fromDate,
                        toDate: toDate
                    },
                    success: function(response) {

                        // Repopulate DataTable so export uses filtered rows only
                        table.clear();

                        if (response.status && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                const fullName = row.full_name ? row.full_name : '-';
                                const title = row.title ? row.title : '-';
                                const appNum = row.application_num ? row.application_num : '-';
                                const createdDate = row.created_date ? row.created_date : '-';
                                const requestType = row.request_type ? row.request_type : '-';
                                const requestCategory = row.request_category ? row.request_category : '-';
                                const badgeClass = row.badge_class ? row.badge_class : 'bg-secondary';
                                const statusHindi = row.status_hindi ? row.status_hindi : 'अज्ञात स्थिति';
                                const viewUrl = `{{ url('admin/view-dava-apatti') }}/${row.claim_id}`;

                                table.row.add([
                                    index + 1,
                                    fullName,
                                    `${title} (${appNum})`,
                                    createdDate,
                                    requestType,
                                    requestCategory,
                                    `<span class="badge ${badgeClass}">${statusHindi}</span>`,
                                    `<a href="${viewUrl}" class="btn btn-sm btn-primary">देखें</a>`
                                ]);
                            });
                        }

                        table.draw();
                    },
                    error: function() {
                        alert('डेटा लोड करते समय त्रुटि हुई।');
                    }
                });
            });

            $('#resetFilter').on('click', function() {
                location.reload();
            });
        });
    </script>
@endsection
