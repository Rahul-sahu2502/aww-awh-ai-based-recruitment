@extends('layouts.dahboard_layout')

@section('styles')
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5>नए जिले की प्रविष्टि</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.add_district') }}" method="post" enctype="multipart/form-data"
                            id="myForm">
                            <div class="col-md-12 mb-3">
                                <label for="" class="form-label">जिले का नाम<span class="textspan" style="color: red;">
                                        * </span></label>
                                <input type="text" class="form-control" placeholder="जिले का नाम लिखे" id="name"
                                    name="name" />
                                <div id="error" class="invalid-feedback">
                                    This field is required<br>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">दर्ज करें</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h4>जिलो की सूची</h4>
                    </div>
                    <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                        <table id="datatable" class="table table-striped table-bordered cell-border">
                            <thead>
                                <tr>
                                    <th>आईडी</th>
                                    <th>जिले का नाम</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($district as $index => $district)
                                    <tr>
                                        <td>{{ $district->District_Code_LGD }}</td>
                                        <td>{{ $district->name }}</td>
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
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.1.2/css/buttons.dataTables.min.css">

    <!-- DataTables Buttons JavaScript -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.8.0/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.2/js/buttons.html5.min.js"></script>

    <script>

        $(document).ready(function () {

            $('#datatable').DataTable({
                "autoWidth": false,
                "paging": true,
                "lengthMenu": [10, 25, 50, 100],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'fB>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>", // Customize the layout
                buttons: [{
                    extend: 'excel', // Excel button
                    text: 'Export to Excel', // Button text
                    className: 'btn btn-success', // Custom CSS class
                    filename: 'datatable_export', // File name
                    exportOptions: {
                        columns: [0, 1, 2, 3] // Columns to export
                    }
                },
                {
                    extend: 'pdf', // PDF button
                    text: 'Export to PDF', // Button text
                    className: 'btn btn-primary', // Custom CSS class
                    filename: 'datatable_export', // File name
                    exportOptions: {
                        columns: [0, 1, 2, 3] // Columns to export
                    }
                },
                    // Add more buttons here as needed
                ]
            });


            //to add and remove class from input feild
            $('form input').keyup(function () {
                if (this.value) {
                    var element = $(this);

                    // Remove the is-invalid class from input elements
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    var element = $(this);
                    var elementName = element.attr('name');

                    if (elementName != "post" && elementName != "pehchan" && elementName != "samiti_name" && elementName != "panjiyan_no" && elementName != "total_member" && elementName != "house_no" && elementName != "demand" && elementName != "pond_name" && elementName != "khasra_no" && elementName != "rakba" && elementName != "amount")
                        if (element.length) {
                            // Remove the is-invalid class from input elements
                            element.removeClass('was-validated');
                            element.addClass('is-invalid');
                        }
                }
            });
        });

        $('#myForm').submit(function (e) {
            e.preventDefault(); // Prevent form from submitting normally

            var form = new FormData(this);
            var url = $(this).attr('action'); // Get the form action URL
            var csrf_token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token value
            form.append('_token', '{{ csrf_token() }}');

            // Submit the form using AJAX
            $.ajax({
                url: url,
                type: "POST",
                data: form,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                context: this,
                success: function (data) {
                    // Handle the response from the server
                    console.log(data);
                    if (data.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: data['message'],
                            // text: 'आवेदन की स्थिति को ट्रैक करने के लिए इस आईडी का उपयोग करें',
                            allowOutsideClick: false
                        }).then((data) => {
                            window.location.href = '/admin/add-district';
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: data['title'],
                            text: data['message'],
                            allowOutsideClick: false
                        })
                    }
                },
                error: function (xhr, status, error) {
                    // Handle the error
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'warning',
                        title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! <br> (Please Fill All Required Field) ',
                        text: 'Please see fields marked in red....',
                        allowOutsideClick: false
                    });

                    var response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        var errors = response.errors;
                        var errorMessages = '';

                        $.each(errors, function (key, value) {
                            errorMessages += value[0] + '<br>';
                            var element = $('[name="' + key + '"]');
                            if (element.length) {
                                element.addClass('is-invalid');
                            }
                        });
                        $('#error').html("This feild is required");
                    }
                }
            });
        });
    </script>
@endsection