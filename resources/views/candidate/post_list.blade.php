@extends('layouts.dahboard_layout')

@section('styles')
@endsection

@section('body-page')
    <main id="main" class="main">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h3> Post List</h3>
                        <p class="mt-2">Click the type of post you want to proceed</p>
                    </div>
                    <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                        <table id="datatable" class="table table-striped table-bordered cell-border">
                            <thead>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Post Name</th>
                                    <th>Post Level</th>
                                    <th>Start Date</th>
                                    <th>Last Date</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($post_data as $index => $post)
                                    <tr>
                                        <td>{{$index + 1}}</td>
                                        <td>{{$post->title}}</td>
                                        <td>{{$post->title}}</td>
                                        <td>{{ date('d-m-Y', strtotime($post->start_date))}}</td>
                                        <td>{{ date('d-m-Y', strtotime($post->end_date))}}</td>
                                    <td>
                                        <a target="_blank" href="{{ asset('assets/upload/doc/posts/' . $post->File_Path) }}">
                                            View
                                        </a>
                                    </td>

                                        <td>
                                            <a href="{{ url('/candidate/user-register-awc/' . md5($post->post_id)) }}">
                                                <i class="bi bi-arrow-right"></i>&nbsp;Proceed
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