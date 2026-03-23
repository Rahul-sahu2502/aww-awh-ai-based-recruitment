@extends('layouts.dahboard_layout')

@section('styles')
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">अपूर्ण आवेदनों की सूची</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/candidate/candidate-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">अपूर्ण आवेदनों की सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-card-list"> </i>अपूर्ण आवेदनों की सूची</h5>
                    </div>
                    <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                        <table id="datatable" class="table table-striped table-bordered cell-border">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap;">आवेदन संख्या</th>
                                    <!-- <th style="white-space: nowrap;">नाम</th> -->
                                    <th style="white-space: nowrap;">विज्ञप्ति</th>
                                    <th style="white-space: nowrap;">पद का नाम</th>
                                    <th style="white-space: nowrap;">रिमार्क</th>
                                    <th style="white-space: nowrap;">देखें</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pending_application_lists as $index => $application_list)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <!-- <td>{{ $application_list->Full_Name }}</td> -->
                                        <td>{{ $application_list->Advertisement_Title }}</td>
                                        <td>{{ $application_list->post_name }}</td>
                                        <td>
                                            <span class="badge bg-warning">Pending</span>
                                            {{-- @if ($application_list->Application_Status == 'Submitted')
                                                <span class="badge bg-info">Submitted</strong>
                                                @elseif($application_list->Application_Status == 'Rejected')
                                                    <span class="badge bg-danger">Rejected</strong>
                                                    @elseif($application_list->Application_Status == 'Verified')
                                                        <span class="badge bg-success">Approved</strong>
                                            @endif --}}
                                        </td>
                                        <td>
                                            {{-- @if ($application_list->is_final_submit == 1 && $application_list->self_attested_file !== null)
                                                <a
                                                    href="{{ url('/candidate/user-details-update/' . md5($application_list->RowID) . '/' . md5($application_list->application_id)) }}">
                                                    <button class="btn btn-info btn-sm"><i class="bi bi-eye"></i></button>
                                                </a>
                                            @else --}}
                                            <a
                                                href="{{ url('/candidate/user-details-update/' . md5($application_list->RowID) . '/' . md5($application_list->application_id)) }}">
                                                <button class="btn btn-info btn-sm"><i class="bi bi-eye"></i></button>
                                            </a>
                                            {{-- @endif --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    </script>
@endsection
