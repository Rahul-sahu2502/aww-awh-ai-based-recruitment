@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    {{--
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}" />
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">कौशल</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">कौशल जोड़ें</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-plus"></i> कौशल जोड़ें</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="skillForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="skill_name" class="form-label">कौशल का नाम</label>
                                    <input type="text" name="skill_name" id="skill_name" class="form-control"
                                        placeholder="कौशल का नाम दर्ज करें" required>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="option_select">विकल्प जोड़ें (कौशल से संबंधित)</label>

                                <div class="input-group mb-2">
                                    <input type="text" id="option_select" class="form-control"
                                        placeholder="यहाँ विकल्प टाइप करें" name="option_select">
                                    <button type="button" id="add_subject" class="btn btn-success">जोड़ें</button>
                                </div>

                                <!-- Subjects List -->
                                <div id="subject_list" class="mt-2 d-flex flex-wrap gap-2">
                                    <!-- Selected subjects will appear here -->
                                </div>
                            </div>



                            <div class="text-end">
                                <button type="submit" class="btn btn-success" id="saveSkill">
                                    <i class="bi bi-check-circle"></i> सहेजें
                                </button>
                            </div>
                        </form>

                        <!-- Form ke baad table section -->
                        <hr>
                        <h4><i class="bi bi-folder2-open"></i> जोड़े गए कौशल</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="skillsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>क्रमांक</th>
                                        <th>कौशल का नाम</th>
                                        <th>कौशल संबंधी विकल्प</th>
                                    </tr>
                                </thead>
                                <tbody>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#skillsTable').DataTable({
                processing: true, // Show the processing indicator
                serverSide: true, // Enable server-side processing
                ajax: '{{ route('admin.skills.show') }}', // Update with the actual route name (make sure to define this route in routes/web.php)
                columns: [{
                        data: '0'
                    }, // Index number
                    {
                        data: '1'
                    }, // Skill Name
                    {
                        data: '2'
                    }, // Skill Options (formatted as a list)
                ]
            });
        });


        $('#saveSkill').on('click', function(e) {
            e.preventDefault();

            // Form validation
            var isValid = true;
            var form = $('#skillForm');

            // Check if skill name is filled
            if ($('#skill_name').val() == '') {
                isValid = false;
                Swal.fire('त्रुटि!', 'कौशल का नाम आवश्यक है!', 'error');
            }

            if (isValid) {
                var formData = new FormData(form[0]);

                // Disable button and show loading text
                $('#saveSkill').prop('disabled', true).html(
                    '<i class="bi bi-hourglass-split"></i> कृपया प्रतीक्षा करें...');

                $.ajax({
                    url: "{{ route('admin.skills.store') }}", // Replace with your route
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'सफलता!',
                            text: 'कौशल सफलतापूर्वक जोड़ा गया।',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'ठीक है'
                        });

                        // Reload the table with new data
                        $('#skillsTable').DataTable().ajax.reload();

                        // Enable button and restore text
                        $('#saveSkill').prop('disabled', false).html(
                            '<i class="bi bi-check-circle"></i> सहेजें');
                    },
                    error: function(xhr) {
                        let errorMessage = 'कुछ त्रुटि हुई है। कृपया पुनः प्रयास करें।';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            text: errorMessage,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'बंद करें'
                        });

                        // Enable button and restore text
                        $('#saveSkill').prop('disabled', false).html(
                            '<i class="bi bi-check-circle"></i> सहेजें');
                    }
                });
            }
        });

        $('#add_subject').on('click', function() {
            let option = $('#option_select').val().trim();
            let optionText = option;
            let id = 'subject_' + Date.now();

            // Check for non-empty and not already added
            if (option && !$('#subject_list input[value="' + option + '"]').length) {
                $('#subject_list').append(`
            <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                <input type="hidden" name="options[]" value="${option}">
                <span>${optionText}</span>
                <button type="button" class="btn-close btn-close-white ms-2 remove-option" data-id="${id}" aria-label="Close"></button>
            </div>
        `);

                $('#option_select').val(''); // Clear input field
            }
        });

        // Remove option
        $(document).on('click', '.remove-option', function() {
            let targetId = $(this).data('id');
            $('#' + targetId).remove();
        });
    </script>
@endsection
