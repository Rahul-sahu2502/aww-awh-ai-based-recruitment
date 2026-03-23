@extends('layouts.dahboard_layout')

@section('styles')
@endsection

@section('body-page')
<main id="main" class="main">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h5>नए प्रोजेक्ट की प्रविष्टि</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.add_project') }}" method="post" enctype="multipart/form-data" id="myForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="" class="form-label">जिले का नाम<span class="textspan" style="color: red;">
                                        * </span></label>
                                <select class="form-select" id="district" name="district">
                                    <option value="">--Select Any District--</option>
                                    <option value="384">Koriya</option>
                                    <option value="389">Sarguja</option>
                                    <option value="380">Jashpur</option>
                                    <option value="386">Raigarh</option>
                                    <option value="383">Korba</option>
                                    <option value="379">Janjgir-Champa</option>
                                    <option value="375">Bilaspur</option>
                                    <option value="382">Kabeerdhaam</option>
                                    <option value="388">Rajnandgaon</option>
                                    <option value="378">Durg</option>
                                    <option value="387">Raipur</option>
                                    <option value="385">Mahasamund</option>
                                    <option value="377">Dhamtari</option>
                                    <option value="381">Kanker</option>
                                    <option value="374">Bastar</option>
                                    <option value="637">Narayanpur</option>
                                    <option value="376">Dantewada</option>
                                    <option value="636">Bijapur</option>
                                    <option value="646">Balod</option>
                                    <option value="644">Balodabazar</option>
                                    <option value="649">Balrampur</option>
                                    <option value="650">Bemetara</option>
                                    <option value="645">Gariaband</option>
                                    <option value="643">Kondagaon</option>
                                    <option value="647">Mungeli</option>
                                    <option value="642">Sukma</option>
                                    <option value="648">Surajpur</option>
                                </select>
                                <div id="error" class="invalid-feedback">
                                    This field is required<br>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="" class="form-label">प्रोजेक्ट का कोड<span class="textspan" style="color: red;">
                                        * </span></label>
                                <input type="text" class="form-control" placeholder="प्रोजेक्ट का कोड लिखे" id="p_code" name="p_code" />
                                <div id="error" class="invalid-feedback">
                                    This field is required<br>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="" class="form-label">प्रोजेक्ट का नाम<span class="textspan" style="color: red;">
                                        * </span></label>
                                <input type="text" class="form-control" placeholder="प्रोजेक्ट का नाम लिखे" id="p_name" name="p_name" />
                                <div id="error" class="invalid-feedback">
                                    This field is required<br>
                                </div>
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
                    <h4>प्रोजेक्ट की सूची</h4>
                </div>
                <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                    <table id="datatable" class="table table-striped table-bordered cell-border">
                        <thead>
                            <tr>
                                <th>आईडी</th>
                                <th>जिले का नाम</th>
                                <th>प्रोजेक्ट का कोड</th>
                                <th>प्रोजेक्ट का नाम</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $index => $project)
                            <tr>
                                <td>{{ $project->Id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->Project_Code }}</td>
                                <td>{{ $project->Project_Name }}</td>
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

        //to add and remove class from input feild
        $('form input').keyup(function() {
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

        //to add and remove class from dropdown
        $('form select').change(function() {
            if (this.value) {
                var element = $(this);
                if (element.length) {
                    // Remove the is-invalid class from input elements
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                }
            } else {
                var element = $(this);
                if (element.length) {
                    // Remove the is-invalid class from input elements
                    element.removeClass('was-validated');
                    element.addClass('is-invalid');
                }
            }
        });
    });

    $('#myForm').submit(function(e) {
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
            success: function(data) {
                // Handle the response from the server
                console.log(data);
                if (data.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: data['message'],
                        // text: 'आवेदन की स्थिति को ट्रैक करने के लिए इस आईडी का उपयोग करें',
                        allowOutsideClick: false
                    }).then((data) => {
                        window.location.href = '/admin/add-project';
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
            error: function(xhr, status, error) {
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

                    $.each(errors, function(key, value) {
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