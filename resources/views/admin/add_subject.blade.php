@extends('layouts.dahboard_layout')

@section('styles')
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">विषय</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">विषय जोड़ें</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-plus"></i> विषय जोड़ें</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="subjectForm">
                            @csrf
                            <div class="row mb-3 ">
                                <div class="col-md-6 mt-3">
                                    <label for="fk_Quali_ID" class="form-label">शैक्षणिक योग्यता<font color="red">*
                                        </font></label>
                                    <select name="fk_Quali_ID" id="fk_Quali_ID" class="form-control form-select" required>
                                        <option value="">-- शैक्षणिक योग्यता चुनें --</option>
                                        @foreach ($qualifications as $qualification)
                                            <option value="{{ $qualification->Quali_ID }}">{{ $qualification->Quali_Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="subject_name" class="form-label">विषय का नाम</label>
                                    <input type="text" name="subject_name" id="subject_name" class="form-control"
                                        placeholder="विषय का नाम दर्ज करें" required>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success" id="saveSubject">
                                    <i class="bi bi-check-circle"></i> सहेजें
                                </button>
                            </div>
                        </form>

                        <!-- Form ke baad table section -->
                        <hr>
                        <h4><i class="bi bi-folder2-open"></i> जोड़े गए विषय</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="subjectsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>क्रमांक</th>
                                        <th>शैक्षणिक योग्यता</th>
                                        <th>विषय कोड</th>
                                        <th>विषय का नाम</th>
                                    </tr>
                                </thead>
                               <tbody>
                                    @php $sr = 1; @endphp
                                    @foreach ($subjects as $subject)
                                        <tr>
                                            <td>{{ $sr++ }}</td>
                                            <td>{{ $subject->Quali_Name }}</td>
                                            <td>{{ $subject->subject_code }}</td>
                                            <td>{{ $subject->subject_name }}</td>
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
    <script>
        $(document).ready(function() {
            $('#subjectsTable').DataTable();
        });

        $('#saveSubject').on('click', function(e) {
            e.preventDefault();

            var isValid = true;
            var form = $('#subjectForm');

            if ($('#fk_Quali_ID').val() == '' || $('#subject_code').val() == '' || $('#subject_name').val() == '') {
                isValid = false;
                Swal.fire('त्रुटि!', 'सभी फ़ील्ड आवश्यक हैं!', 'error');
            }

            if (isValid) {
                var formData = new FormData(form[0]);

                $('#saveSubject').prop('disabled', true).html(
                    '<i class="bi bi-hourglass-split"></i> कृपया प्रतीक्षा करें...');

                $.ajax({
                    url: "{{ route('admin.subjects.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'सफलता!',
                            text: 'विषय सफलतापूर्वक जोड़ा गया।',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'ठीक है'
                        }).then(() => {
                            location.reload(); // Reload page to show new data
                        });

                        $('#saveSubject').prop('disabled', false).html(
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

                        $('#saveSubject').prop('disabled', false).html(
                            '<i class="bi bi-check-circle"></i> सहेजें');
                    }
                });
            }
        });
    </script>
@endsection
