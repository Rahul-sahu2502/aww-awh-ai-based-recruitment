@extends('layouts.dahboard_layout')

@section('styles')
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">CDPO सूची </h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if (session('sess_role') === 'Super_admin')
                            <a href="{{ url('/admin/admin-dashboard') }}">होम</a>
                        @elseif (session('sess_role') === 'Admin' || session('sess_role') === 'Supervisor' || session('sess_role') === 'CDPO')
                            <a href="{{ url('/examinor/examinor-dashboard') }}">होम</a>
                        @endif
                        @if (session('sess_role') === 'Super_admin')
                    <li class="breadcrumb-item active">CDPO सूची </li>
                    @endif
                    <li class="breadcrumb-item active">CDPO सूची </li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list"> </i>CDPO सूची
                            </h5>
                            <div id="exportBtnContainer" class="d-flex align-items-center"></div>
                        </div>
                    </div>


                    <div class="card-body">
                        {{-- If any type filter apply
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
                                <button type="button" class="btn btn-primary mt-4" id="CDPO_LIST">
                                    फ़िल्टर करें
                                </button>
                                <button type="reset" class="btn btn-secondary mt-4" id="resetFilter">
                                    रीसेट
                                </button>
                            </div>
                        </div> --}}
                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="datatable" class="table table-striped table-bordered cell-border">
                                <thead>
                                    <tr>
                                        <th>क्रमांक</th> 
                                        <th>जिला </th>
                                        <th>परियोजना</th>
                                        <th>पूरा नाम </th>
                                        <th>मोबाइल नंबर </th>
                                        {{-- <th>एक्शन </th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cdpo_list as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->District ?? '-' }}</td>
                                            <td>{{ $row->Project ?? '-' }} ({{ $row->Project ?? '-' }})</td>
                                            <td>{{ $row->Full_Name ?? '-' }}</td>
                                            <td>{{ $row->Mobile_Number ?? '-' }}</td>
                                            {{-- <td>
                                                <a href="{{ url('admin/view-dava-apatti/' . $row->row_id) }}"
                                                    class="btn btn-sm btn-primary view-claim"
                                                    data-id="{{ $row->row_id }}"> <i class="bi bi-eye"></i> देखें </a>
                                            </td> --}}
                                        </tr>
                                    @empty -
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
                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-end d-none'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-download"></i> Excel Export',
                    title: 'CDPO_सूची',
                    exportOptions: {
                        columns: ':visible:not(:last-child)' // Action column exclude
                    },
                    className: 'btn btn-outline-primary'
                }]
            });

            // Move the existing export button into the header placeholder (red box area)
            table.buttons().container().appendTo('#exportBtnContainer');

        });
    </script>
@endsection
