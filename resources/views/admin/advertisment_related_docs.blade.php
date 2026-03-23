@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    {{--
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
    <link rel="stylesheet" href="{{asset('assets/libs/datatable/dataTables.bootstrap5.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/libs/datatable/buttons.dataTables.min.css')}}" />
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">विज्ञापन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">विज्ञापन संबंधी दस्तावेज जोड़ें</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5><i class="bi bi-file-plus"></i> विज्ञापन संबंधी दस्तावेज जोड़ें </h5>
                        <h5>विज्ञापन का शीर्षक - {{$advertisement_name}}</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="relatedDocForm">
                            @csrf
                            <input type="hidden" name="Advertisement_ID" value="{{$id}}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="doc_title" class="form-label">दस्तावेज का शीर्षक</label>
                                    <input type="text" name="doc_title" id="doc_title" class="form-control"
                                        placeholder="शीर्षक दर्ज करें" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="related_file" class="form-label">दस्तावेज अपलोड करें</label>
                                    <input type="file" name="related_file" id="related_file" class="form-control" required>
                                    <a id="viewButton" class=" btn-danger d-none" data-bs-toggle="modal"
                                        data-bs-target="#docModal" style="cursor: pointer;">
                                        View Document
                                    </a>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="description" class="form-label">विवरण</label>
                                    <textarea name="description" id="description" rows="3" class="form-control"
                                        placeholder="दस्तावेज का विवरण लिखें"></textarea>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success" id="saveRelatedDoc">
                                    <i class="bi bi-check-circle"></i> सहेजें
                                </button>
                            </div>
                        </form>
                        <!-- Bootstrap Modal -->
                        <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="docModal"
                            tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel">Selected File</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img id="imagePreview" class="img-fluid d-none" alt="Selected File">
                                        <iframe id="docViewer" class="w-100 d-none"
                                            style="height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Form ke baad table section -->
                        <hr>
                        <h4><i class="bi bi-folder2-open"></i> जोड़े गए दस्तावेज़</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="relatedDocsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>क्रमांक</th>
                                        <th>शीर्षक</th>
                                        <th>फाइल</th>
                                        <th>विवरण</th>
                                        <th>अपलोड तिथि</th>
                                    </tr>
                                </thead>
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
    <script src="{{asset('assets/libs/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/jszip.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/libs/datatable/vfs_fonts.js')}}"></script>
    <script>

        $(document).ready(function () {

            let advId = $('input[name="Advertisement_ID"]').val(); // yahan variable me store kiya
            let ajaxUrl = "{{ route('advertisement.related_docs.fetch', ['id' => '__id__']) }}".replace('__id__', advId);

            $('#relatedDocsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl, // Route me $id pass karein
                    type: 'GET'
                },
                columns: [
                    { data: 'adv_file_id', name: 'adv_file_id', orderable: false, searchable: false },
                    { data: 'updated_file_tittle', name: 'updated_file_tittle' },
                    { data: 'updated_file_tittle_path', name: 'updated_file_tittle_path' }, // already HTML aa raha hai backend se
                    { data: 'file_description', name: 'file_description' },
                    {
                        data: 'updated_date', name: 'updated_date',
                        render: function (data, type, row) {
                            return data ? new Date(data).toLocaleDateString('en-GB') : '';
                        }
                    },
                ],
                 dom: '<" d-flex align-items-center mb-2"l>Bfrtip',
                buttons: [
                    { extend: 'copy', text: 'COPY', className: 'btn btn-sm btn-outline-primary' },
                    { extend: 'csv', text: 'CSV', className: 'btn btn-sm btn-outline-secondary', charset: 'utf-8', bom: true },
                    { extend: 'excel', text: 'EXCEL', className: 'btn btn-sm btn-outline-success', charset: 'utf-8', bom: true },
                    {
                        extend: 'pdf', className: 'btn btn-sm btn-outline-success'
                        , customize: function (doc) {
                            pdfMake.fonts = {
                                unicode: {
                                    normal: 'unicode.ttf'
                                    , bold: 'unicode.ttf'
                                    , italics: 'unicode.ttf'
                                    , bolditalics: 'unicode.ttf'
                                }
                            };
                            doc.defaultStyle.font = "unicode";
                        }
                    }
                ], order: [[0, 'desc']],
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                language: {
                    paginate: { previous: '&laquo;', next: '&raquo;' },
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });


            $(document).on('click', '.viewDocBtn', function () {
                var fileUrl = $(this).data('file'); // Button ke data-file se URL le lo

                // id mil rahi hai aapke modal ke andar: docViewer aur imagePreview
                var docViewer = document.getElementById('docViewer');
                var imagePreview = document.getElementById('imagePreview');

                // Set iframe src
                if (fileUrl.match(/\.(jpeg|jpg|png)$/i)) {
                    // Agar image hai
                    $('#imagePreview').attr('src', fileUrl).removeClass('d-none');
                    $('#docViewer').addClass('d-none');
                } else {
                    // Agar PDF hai
                    $('#docViewer').attr('src', fileUrl).removeClass('d-none');
                    $('#imagePreview').addClass('d-none');
                }
            });

            // Jab modal band ho to reset kar do
            $('#docModal').on('hidden.bs.modal', function () {
                $('#docViewer').attr('src', '');
                $('#imagePreview').attr('src', '');
            });

        });


        // this function is use for showing the selected doc and existing doc in modal before submiting by a view button
        document.getElementById("related_file").addEventListener("change", function (event) {
            let file = event.target.files[0]; // Get selected file
            // Allowed file types
            let allowedTypes = ["image/png", "image/jpeg", "image/jpg", "application/pdf"];
            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: "error",
                        title: "⚠ गलत फ़ाइल प्रकार!",
                        text: "❌ केवल छवियाँ (JPG, PNG) और PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "ठीक है"
                    });
                    event.target.value = ""; // Reset file input
                    return;
                }
                let fileURL = URL.createObjectURL(file); // Generate file URL
                let viewButton = document.getElementById("viewButton");
                let docViewer = document.getElementById("docViewer");
                let imagePreview = document.getElementById("imagePreview");

                viewButton.classList.remove("d-none"); // Show View Button

                // Check if file is an image
                if (file.type.startsWith("image/")) {
                    imagePreview.src = fileURL;
                    imagePreview.classList.remove("d-none");
                    docViewer.classList.add("d-none");
                } else {
                    docViewer.src = fileURL;
                    docViewer.classList.remove("d-none");
                    imagePreview.classList.add("d-none");
                }
            }
        });

        $('#saveRelatedDoc').on('click', function (e) {
            e.preventDefault();

            // Form validation using jQuery (additional to HTML5)
            var isValid = true;
            var form = $('#relatedDocForm');

            // Check if title is filled
            if ($('#doc_title').val() == '') {
                isValid = false;
                Swal.fire('Error!', 'दस्तावेज का शीर्षक आवश्यक है!', 'error');
            }

            // Check if file is selected
            if ($('#related_file').val() == '') {
                isValid = false;
                Swal.fire('Error!', 'कृपया दस्तावेज अपलोड करें!', 'error');
            }

            if (isValid) {
                var formData = new FormData(form[0]);

                // Disable button and show loading text
                $('#saveRelatedDoc').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> कृपया प्रतीक्षा करें...');

                $.ajax({
                    url: "{{ route('advertisement.related_docs.store') }}", // Replace with your route
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'सफलता!',
                            text: 'दस्तावेज सफलतापूर्वक सहेजा गया।',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'ठीक है'
                        });

                        // Reset form
                        form[0].reset();

                        // Reload DataTable
                        $('#relatedDocsTable').DataTable().ajax.reload();

                        // Enable button and restore text
                        $('#saveRelatedDoc').prop('disabled', false).html('<i class="bi bi-check-circle"></i> सहेजें');
                    },
                    error: function (xhr) {
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
                        $('#saveRelatedDoc').prop('disabled', false).html('<i class="bi bi-check-circle"></i> सहेजें');
                    }
                });
            }
        });
    </script>
@endsection