@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(2px);
            /* Blurs background */
            background-color: rgba(255, 255, 255, 0.3);
            /* Optional light tint */
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loader {
            width: 40px;
            height: 40px;
            border: 4px solid #ccc;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">विज्ञापन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/show-advertisment') }}">विज्ञापन की सूची</a></li>
                    <li class="breadcrumb-item active">
                        {{ isset($readonly) && $readonly ? 'विज्ञापन देखें' : 'विज्ञापन संपादित करें' }}</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <form id="editAdvertisementForm" method="POST" enctype="multipart/form-data"
                    action="{{ route('advertisement.update', MD5($advertisement->Advertisement_ID)) }}">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                @if (isset($readonly) && $readonly)
                                    <i class="bi bi-eye"> </i>विज्ञापन देखें
                                @else
                                    <i class="bi bi-pencil"> </i>विज्ञापन संपादित करें
                                @endif
                            </h5>
                        </div>
                        <input type="hidden" id="advertisement_id" value="{{ $advertisement->Advertisement_ID }}">
                        <div class="card-body">
                            <div class="field_wrapper" id="inputFieldsContainer">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="advertisement_title" class="form-label">विज्ञापन शीर्षक
                                            @if (!(isset($readonly) && $readonly))
                                                <font color="red">*</font>
                                            @endif
                                        </label>
                                        <input type="text" class="form-control" id="advertisement_title"
                                            name="advertisement_title" value="{{ $advertisement->Advertisement_Title }}"
                                            {{ isset($readonly) && $readonly ? 'readonly' : 'required' }}>
                                        <span class="text-danger error-advertisement_title"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="advertisement_date" class="form-label">विज्ञापन दिनांक
                                            @if (!(isset($readonly) && $readonly))
                                                <font color="red">*</font>
                                            @endif
                                        </label>
                                        <input type="date" class="form-control" id="advertisement_date"
                                            name="advertisement_date" value="{{ $advertisement->Advertisement_Date }}"
                                            {{ isset($readonly) && $readonly ? 'readonly' : 'required' }}>
                                        <span class="text-danger error-advertisement_date"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="advertisement_document" class="form-label">विज्ञापन दस्तावेज़
                                            @if (!(isset($readonly) && $readonly))
                                                <font color="red">*</font>
                                            @endif
                                        </label>
                                        @if (isset($advertisement->is_dsc) && $advertisement->is_dsc == 1 && isset($advertisement->dsc_sign_data))
                                            <p>📂 <a data-file="{{ (string) $advertisement->dsc_sign_data }}"
                                                    target="_blank" id=""
                                                    onclick="viewBase64Document({{ json_encode((string) $advertisement->dsc_sign_data) }});"
                                                    style="cursor: pointer;" class="btn-danger">Existing File</a></p>
                                        @elseif (!empty($advertisement->Advertisement_Document))
                                            @php
                                                $file_path = asset('uploads/' . $advertisement->Advertisement_Document);
                                                // {{ dd($file_path) }}
                                                if (config('app.env') == 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $advertisement->Advertisement_Document;
                                                }
                                            @endphp
                                            <p>📂 <a data-file="{{ $file_path }}" target="_blank" id="existingFile"
                                                    style="cursor: pointer;" class="btn-danger">Existing File</a></p>
                                        @endif
                                        @if (!(isset($readonly) && $readonly))
                                            <input type="file" class="form-control" id="advertisement_document"
                                                name="advertisement_document" accept=".pdf">
                                        @endif
                                        <a id="viewButton" class="btn-danger d-none" data-bs-toggle="modal"
                                            data-bs-target="#docModal" style="cursor: pointer;">
                                            View Document
                                        </a>
                                        <span class="text-danger error-advertisement_document"></span>
                                    </div>
                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
                                        id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel">Selected File</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center" id="documentContent">
                                                    <img id="imagePreview" class="img-fluid d-none" alt="Selected File">
                                                    <iframe id="docViewer" class="w-100 d-none"
                                                        style="height: 500px; border: none;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_for_age" class="form-label">विज्ञापन वैधता तिथि
                                            @if (!(isset($readonly) && $readonly))
                                                <font color="red">*</font>
                                            @endif
                                        </label>
                                        <input type="date" class="form-control" id="date_for_age" name="date_for_age"
                                            value="{{ $advertisement->Date_For_Age }}"
                                            {{ isset($readonly) && $readonly ? 'readonly' : 'required' }}>
                                        <span class="text-danger error-date_for_age"></span>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="advertisement_description" class="form-label">विज्ञापन विवरण
                                            @if (!(isset($readonly) && $readonly))
                                                <font color="red">*</font>
                                            @endif
                                        </label>
                                        <textarea class="form-control" id="advertisement_description" name="advertisement_description" rows="4"
                                            {{ isset($readonly) && $readonly ? 'readonly' : 'required' }}>{{ $advertisement->Advertisement_Description }}</textarea>
                                        <span class="text-danger error-advertisement_description"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="newspaper_publish_date" class="form-label">सूचना
                                            प्रसारित का दिनांक</label>
                                        <input type="date" class="form-control" id="newspaper_publish_date"
                                            name="newspaper_publish_date"
                                            value="{{ $advertisement->newspaper_publish_date ?? '' }}"
                                            {{ isset($readonly) && $readonly ? 'readonly' : '' }}>
                                        <span class="text-danger error-newspaper_publish_date"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="newspaper_cutting_doc" class="form-label">प्रसारित पत्र अपलोड</label>
                                        @if (!empty($advertisement->newspaper_cutting_doc))
                                            @php
                                                $cutting_file_path = asset(
                                                    'uploads/' . $advertisement->newspaper_cutting_doc,
                                                );
                                                if (config('app.env') == 'production') {
                                                    $cutting_file_path =
                                                        config('custom.file_point') .
                                                        $advertisement->newspaper_cutting_doc;
                                                }
                                            @endphp
                                            <p>📂 <a data-file="{{ $cutting_file_path }}" target="_blank"
                                                    id="existingCuttingFile" style="cursor: pointer;"
                                                    class="btn-danger">Existing File</a></p>
                                        @endif
                                        @if (!(isset($readonly) && $readonly))
                                            <input type="file" class="form-control" id="newspaper_cutting_doc"
                                                name="newspaper_cutting_doc" accept=".pdf,.jpg,.jpeg,.png">
                                        @endif
                                        <small class="text-muted">केवल PDF, JPG, JPEG, PNG फाइलें स्वीकार्य हैं</small>
                                        @if ($advertisement->newspaper_cutting_doc)
                                            <div class="mt-2">
                                                <small class="text-success">मौजूदा फाइल:
                                                    {{ basename($advertisement->newspaper_cutting_doc) }}</small>
                                            </div>
                                        @endif
                                        <span class="text-danger error-newspaper_cutting_doc"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (!(isset($readonly) && $readonly))
                            <div class="card-footer">
                                <div class="col-md-12 text-end">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">ओटीपी भेजे</button>
                                </div>
                            </div>
                        @else
                            <div class="card-footer">
                                <div class="col-md-12 text-end">
                                    <a href="/admin/show-advertisment" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>वापस जाएं
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </main>

    <!-- Advertisement Document Modal -->
    <div class="modal fade" id="advertisementDocumentModal" tabindex="-1"
        aria-labelledby="advertisementDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="advertisementDocumentModalLabel">Selected File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="advertisementDocumentContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loaderOverlay" class="loader-overlay" style="display: none;">
        <div id="loader"></div>
    </div>

    <!-- Advertisement OTP Modal -->
    <div class="modal fade" id="adOtpModal" tabindex="-1" aria-labelledby="adOtpModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adOtpModalLabel">OTP सत्यापन</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adOtpInput" class="form-label">OTP दर्ज करें</label>
                        <input type="text" maxlength="6" class="form-control" id="adOtpInput" placeholder="######">
                        <small class="text-muted">OTP 2 मिनट के लिए मान्य है।</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <button type="button" class="btn btn-outline-primary" id="resendOtpBtn" disabled>
                            पुनः OTP भेजें
                        </button>
                        <div class="fw-bold" id="otpTimer">02:00</div>
                    </div>
                    <input type="hidden" id="pendingId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करें</button>
                    <button type="button" class="btn btn-success" id="verifyOtpBtn">OTP सत्यापित करें</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // this function is use for showing the selected doc and existing doc in modal before submiting by a view button
        document.addEventListener("DOMContentLoaded", function() {
            let fileInput = document.getElementById("advertisement_document");
            let viewButton = document.getElementById("viewButton");
            let docViewer = document.getElementById("docViewer");
            let imagePreview = document.getElementById("imagePreview");
            let existingFileLink = document.getElementById("existingFile");
            let modalElement = document.getElementById("docModal");

            // Allowed file types
            let allowedTypes = ["application/pdf"];

            // New File Upload Logic (only if not readonly)
            if (fileInput) {
                fileInput.addEventListener("change", function(event) {
                    let file = event.target.files[0]; // Get selected file
                    if (file) {
                        if (!allowedTypes.includes(file.type)) {
                            Swal.fire({
                                icon: "error",
                                title: "⚠ गलत फ़ाइल प्रकार!",
                                text: "❌ केवल PDF फ़ाइल ही स्वीकार की जाती हैं।",
                                confirmButtonColor: "#d33",
                                confirmButtonText: "ठीक है"
                            });
                            event.target.value = ""; // Reset file input
                            return;
                        }

                        let fileURL = URL.createObjectURL(file); // Generate temporary file URL
                        viewButton.classList.remove("d-none"); // Show View Button
                        showFileInModal(fileURL, file.type.startsWith("image/"));
                    }
                });
            }

            // Existing File View Logic
            if (existingFileLink) {
                existingFileLink.addEventListener("click", function() {
                    let fileURL = existingFileLink.getAttribute("data-file");
                    let isImage = fileURL.match(/\.(jpeg|jpg|png|gif)$/i); // Check if it's an image
                    showFileInModal(fileURL, isImage);
                    let modal = new bootstrap.Modal(document.getElementById("docModal"));
                    modal.show(); // Open modal
                });
            }

            // Handler for existing cutting file
            let existingCuttingFileLink = document.getElementById("existingCuttingFile");
            if (existingCuttingFileLink) {
                existingCuttingFileLink.addEventListener("click", function() {
                    let fileURL = existingCuttingFileLink.getAttribute("data-file");
                    let isImage = fileURL.match(/\.(jpeg|jpg|png|gif)$/i); // Check if it's an image
                    showFileInModal(fileURL, isImage);
                    let modal = new bootstrap.Modal(document.getElementById("docModal"));
                    modal.show(); // Open modal
                });
            }

            // Function to Show File in Modal
            function showFileInModal(fileURL, isImage) {
                if (isImage) {
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

        // this function ensure that in advertisment you cannot select the previous date from current date.
        @if (!(isset($readonly) && $readonly))
            document.addEventListener("DOMContentLoaded", function() {
                var advertisementDateField = document.getElementById("advertisement_date");
                var date_for_ageDateField = document.getElementById("date_for_age");
                // Set today's date as minimum for advertisement_date
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();

                // Format the date as YYYY-MM-DD
                today = yyyy + '-' + mm + '-' + dd;

                // Set the "min" attribute to today's date for advertisement_date
                // document.getElementById("advertisement_date").min = today;

                // Ensure that the date entered by the user is not earlier than today
                advertisementDateField.addEventListener("input", function() {
                    var enteredDate = this.value;
                    // if (enteredDate < today) {   
                    if (!enteredDate) {
                        // If user enters a date before today, set it back to today
                        this.setCustomValidity("The date must be today or in the future.");
                        this.value = today;
                    } else {
                        this.setCustomValidity(""); // Reset custom validity if the input is valid
                    }
                });
                // Ensure that the date entered by the user is not earlier than today
                date_for_ageDateField.addEventListener("input", function() {
                    var enteredDate = this.value;
                    if (enteredDate < today) {
                        // If user enters a date before today, set it back to today
                        this.setCustomValidity("The date must be today or in the future.");
                        this.value = {{ $advertisement->Advertisement_Date }};
                    } else {
                        this.setCustomValidity(""); // Reset custom validity if the input is valid
                    }
                });
            });

            // document.getElementById("advertisement_date").addEventListener("change", function() {
            //     let startDate = this.value;
            //     let endDateField = document.getElementById("date_for_age");

            //     if (startDate) {
            //         endDateField.min = startDate; // Set the min attribute for the end date
            //         endDateField.value = ""; // Reset end date field if a new start date is selected
            //     }
            // });

            document.getElementById("advertisement_date").addEventListener("change", function() {
                let startDateVal = this.value;
                let endDateField = document.getElementById("date_for_age");

                if (startDateVal) {
                    // 1. Start date ko Date object mein badlein
                    let startDate = new Date(startDateVal);

                    // 2. Usmein 15 din jodein
                    startDate.setDate(startDate.getDate() + 15);

                    // 3. Date ko YYYY-MM-DD format mein convert karein
                    let yyyy = startDate.getFullYear();
                    let mm = String(startDate.getMonth() + 1).padStart(2, '0');
                    let dd = String(startDate.getDate()).padStart(2, '0');
                    let formattedEndDate = yyyy + '-' + mm + '-' + dd;

                    // 4. End Date field mein value set karein aur minimum limit bhi set karein
                    endDateField.min = startDateVal;
                    endDateField.value = formattedEndDate;
                }
            });
        @endif

        // console.log(0);
        $(document).ready(function() {
            // Add newspaper cutting file validation (only if not readonly)
            @if (!(isset($readonly) && $readonly))
                let newspaperCuttingInput = document.getElementById("newspaper_cutting_doc");
                if (newspaperCuttingInput) {
                    newspaperCuttingInput.addEventListener("change", function(event) {
                        let file = event.target.files[0];
                        let allowedTypes = ["application/pdf", "image/jpeg", "image/jpg", "image/png"];
                        let maxSize = 2048 * 1024; // 2MB in bytes

                        if (file) {
                            // Check file type
                            if (!allowedTypes.includes(file.type)) {
                                Swal.fire({
                                    icon: "error",
                                    title: "⚠ गलत फ़ाइल प्रकार!",
                                    text: "❌ केवल PDF, JPG, JPEG, PNG फ़ाइलें ही स्वीकार की जाती हैं।",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "ठीक है"
                                });
                                event.target.value = "";
                                return;
                            }

                            // Check file size
                            if (file.size > maxSize) {
                                Swal.fire({
                                    icon: "error",
                                    title: "⚠ फ़ाइल बहुत बड़ी है!",
                                    text: "❌ फ़ाइल का आकार 2MB से कम होना चाहिए।",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "ठीक है"
                                });
                                event.target.value = "";
                                return;
                            }
                        }
                    });
                }
            @endif

            @if (!(isset($readonly) && $readonly))
                $(document).on('submit', '#editAdvertisementForm', function(e) {
                    e.preventDefault();
                    // Disable button and show loader
                    $('#submitBtn').attr('disabled', true);
                    // console.log("Form submission started");

                    let formData = new FormData(this);
                    let advertisementId = $("#advertisement_id").val();
                    // console.log("Advertisement ID:", advertisementId);
                    let dscPrintResponse = null;
                    let file = formData.get('advertisement_document');

                    checkService();

                    if (dscRegisterStatus !== 1 && dscRegisterStatus !== 3) {
                        Swal.fire({
                            icon: 'info',
                            title: 'जानकारी!',
                            text: 'EDS Service is not running! Please ensure that the EDS Tool is installed on the machine and service is started',
                            confirmButtonText: 'OK'
                        });
                        $('#submitBtn').attr('disabled', false);
                        return;
                    }

                    if (file && file.name) {
                        // console.log('File is present:', file.name);

                        $.ajax({
                            url: "{{ url('admin/dsc-add-sign') }}",
                            type: "POST",
                            data: formData,
                            processData: false, // prevent jQuery from converting data
                            contentType: false, // prevent jQuery from setting wrong header
                            async: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // console.log("Response:", response);

                                if (response.documentType == "PDF") {
                                    dscPrintResponse = response;
                                    // console.log('dsc-add-sign '+response);

                                    showLoader();
                                    $.ajax({
                                        url: "http://localhost:8800/postcertificatedata",
                                        data: JSON.stringify(dscPrintResponse),
                                        type: 'POST',
                                        async: false,
                                        // dataType: "json",
                                        contentType: "application/json",
                                        success: function(response) {
                                            // console.log('postcertificatedata: ', response);
                                            if (response.isDocumentSigned == true) {
                                                let postCertResponse = response;

                                                $.ajax({
                                                    url: "{{ url('admin/advertisements/update') }}/" +
                                                        advertisementId,
                                                    type: "POST",
                                                    data: formData,
                                                    contentType: false,
                                                    cache: false,
                                                    async: false,
                                                    processData: false,
                                                    success: function(
                                                        response) {
                                                        // console.log("Success response:", response);
                                                        if (response
                                                            .insert_id
                                                        ) {
                                                            dscSignAjax(
                                                                    postCertResponse,
                                                                    response
                                                                    .insert_id
                                                                )
                                                                .then(
                                                                    () => {
                                                                        Swal.fire({
                                                                                icon: response
                                                                                    .status ===
                                                                                    "success" ?
                                                                                    "success" :
                                                                                    "info",
                                                                                title: "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं।",
                                                                                text: response
                                                                                    .message,
                                                                                confirmButtonText: "OK"
                                                                            })
                                                                            .then(
                                                                                () => {
                                                                                    window
                                                                                        .location
                                                                                        .href =
                                                                                        "{{ route('advertisement.list') }}";
                                                                                }
                                                                            );
                                                                    })
                                                                .catch((
                                                                    error
                                                                    ) => {
                                                                    Swal.fire({
                                                                        icon: "error",
                                                                        title: "DSC Sign Failed",
                                                                        text: "Failed to sign document with DSC",
                                                                        confirmButtonText: "OK"
                                                                    });
                                                                    $('#submitBtn')
                                                                        .attr(
                                                                            'disabled',
                                                                            false
                                                                        );
                                                                });
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        // Re-enable button on error
                                                        $('#submitBtn')
                                                            .attr(
                                                                'disabled',
                                                                false);
                                                        // console.log("Error response:", xhr);

                                                        let errors = xhr
                                                            .responseJSON
                                                            .errors;
                                                        let errorMessages =
                                                            "";
                                                        $.each(errors,
                                                            function(
                                                                key,
                                                                value
                                                            ) {
                                                                errorMessages
                                                                    +=
                                                                    value[
                                                                        0
                                                                    ] +
                                                                    "<br>";
                                                            });

                                                        Swal.fire({
                                                            icon: "error",
                                                            title: "दर्ज की गई जानकारी मान्य नहीं है। कृपया दोबारा जांचें।",
                                                            html: errorMessages,
                                                            confirmButtonText: "OK"
                                                        });
                                                    }
                                                });

                                            } else {
                                                Swal.fire({
                                                    icon: 'info',
                                                    title: 'DSC Not Recognized',
                                                    text: 'Please connect registered DSC into the machine and try again.'
                                                });
                                                $('#submitBtn').attr('disabled',
                                                    false);
                                            }
                                        },
                                        error: function() {
                                            reject(xhr);
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Error',
                                                text: 'An error occurred while sign DSC.'
                                            });
                                            $('#submitBtn').attr('disabled', false);
                                        },
                                        complete: function() {
                                            hideLoader();
                                        }
                                    });

                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: response.msg || 'DSC Print Failed Ajax',
                                        // text: response.msg
                                    });
                                    $('#submitBtn').attr('disabled', false);
                                    return;
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Error',
                                    text: 'Something went wrong while uploading PDF.'
                                });
                                $('#submitBtn').attr('disabled', false);
                                return;
                            }
                        });
                    } else {
                        // console.log('No file selected');
                        $.ajax({
                            url: "{{ url('admin/advertisements/update') }}/" + advertisementId,
                            type: "POST",
                            data: formData,
                            contentType: false,
                            cache: false,
                            async: false,
                            processData: false,
                            success: function(response) {
                                // Re-enable button
                                $('#submitBtn').attr('disabled', false);
                                Swal.fire({
                                    icon: response.status === "success" ? "success" :
                                        "info",
                                    title: "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं।",
                                    text: response.message,
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('advertisement.list') }}";
                                });
                            },
                            error: function(xhr) {
                                // Re-enable button on error
                                $('#submitBtn').attr('disabled', false);
                                // console.log("Error response:", xhr);

                                let errorMessages = "कृपया सभी आवश्यक जानकारी भरें।";

                                // Check for direct message (from middleware)
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessages = xhr.responseJSON.message;
                                }
                                // Check for validation errors
                                else if (xhr.responseJSON && xhr.responseJSON.errors &&
                                    typeof xhr.responseJSON.errors === 'object') {
                                    let errors = xhr.responseJSON.errors;
                                    let errorValues = Object.values(errors);

                                    if (errorValues && errorValues.length > 0) {
                                        errorMessages = "";
                                        $.each(errors, function(key, value) {
                                            if (Array.isArray(value)) {
                                                errorMessages += value[0] + "<br>";
                                            } else {
                                                errorMessages += value + "<br>";
                                            }
                                        });
                                    }
                                }

                                Swal.fire({
                                    icon: "warning",
                                    title: "ध्यान दें",
                                    html: errorMessages,
                                    confirmButtonText: "ठीक है"
                                });
                            }
                        });
                    }
                });
            @endif
        });

        function dscSignAjax(req, advtId) {
            return new Promise((resolve, reject) => {
                if (!req || !advtId && advtId != 0) {
                    // console.error("Missing required parameters for dscSignAjax");
                    reject("Missing parameters");
                    return;
                }

                // Merge advtId into response object
                let payload = {
                    ...req,
                    advertisement_id: advtId // or use 'advtId' as key if preferred
                };

                $.ajax({
                    url: "{{ url('admin/dsc-sign-save') }}",
                    type: 'POST',
                    data: payload,
                    async: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        // console.log('dsc-sign-save '+res);
                        resolve(res);
                    },
                    error: function() {
                        reject(xhr);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Error',
                            text: 'An error occurred while save sign DSC.'
                        });
                    }
                });

            });
        };

        // Show loader
        function showLoader() {
            $('#loaderOverlay').fadeIn(200); // or .show()
        }

        // Hide loader
        function hideLoader() {
            $('#loaderOverlay').fadeOut(200); // or .hide()
        }

        function checkService() {
            $.ajax({
                url: "http://localhost:8800/checkservice",
                type: 'GET',
                async: false,
                success: function(response) {
                    // console.log(response);
                    if (response.isServiceRunning === true) {
                        dscRegisterStatus = 1;
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'DSC Service Not Running',
                            text: response.message || 'Unable to connect to DSC service.'
                        });
                        return;
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Please try again'
                    });
                    $('#submitBtn').attr('disabled', false);
                    return;
                }
            });
        }

        @if (isset($readonly) && $readonly)
            // Disable form submission in readonly mode
            document.getElementById('editAdvertisementForm').addEventListener('submit', function(e) {
                e.preventDefault();
            });

            // Disable file input events in readonly mode
            if (document.getElementById('advertisement_document')) {
                document.getElementById('advertisement_document').addEventListener('change', function(e) {
                    e.preventDefault();
                });
            }
        @endif

        function viewBase64Document(base64Data) {
            if (!base64Data || base64Data === 'null' || base64Data === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'कोई दस्तावेज़ नहीं',
                    text: 'इस विज्ञापन के लिए दस्तावेज़ उपलब्ध नहीं है।',
                    confirmButtonText: 'ठीक है'
                });
                return;
            }

            if (typeof base64Data !== 'string') {
                base64Data = String(base64Data);
            }
            // console.log('Base64 Data:', base64Data);
            // Clean base64: remove whitespace, newlines, quotes
            base64Data = base64Data.replace(/[\r\n]+/g, '').replace(/"/g, '').trim();

            // If already prefixed (data:application/pdf;base64,), remove duplicate
            if (base64Data.startsWith('data:')) {
                base64Data = base64Data.split(',')[1];
            }

            // Convert base64 → Blob → Object URL (safe for large files)
            const byteCharacters = atob(base64Data);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], {
                type: 'application/pdf'
            });
            const blobUrl = URL.createObjectURL(blob);

            // const modal = new bootstrap.Modal(document.getElementById('docModal'));
            // let contentDiv = document.getElementById('documentContent');

            const modal = new bootstrap.Modal(document.getElementById('advertisementDocumentModal'));
            const contentDiv = document.getElementById('advertisementDocumentContent');
            contentDiv.innerHTML = `<iframe src="${blobUrl}" width="100%" height="500px" style="border:none;"></iframe>`;
            modal.show();

            // Optional: clean up URL when modal closes
            document.getElementById('docModal').addEventListener('hidden.bs.modal', () => {
                URL.revokeObjectURL(blobUrl);
                // contentDiv.innerHTML = '';
                const iframe = document.querySelector('#documentContent iframe');
                if (iframe) {
                    iframe.removeAttribute('src'); // Just clears the src, keeps the iframe element
                }
                contentDiv.innerHTML = '<img id="imagePreview" class="img-fluid d-none" alt="Selected File">';
            });
        }

        /*
        |------------------------------------------------------------------
        | OTP Flow for Advertisement Update
        |------------------------------------------------------------------
        */
        $(document).ready(function() {
            @if (isset($readonly) && $readonly)
                return; // readonly mode, skip submit override
            @endif

            $('#editAdvertisementForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('mode', 'update');
                formData.append('advertisement_id', $('#advertisement_id').val());

                $.ajax({
                    url: "{{ url('admin/advertisement/otp/request') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        showLoader();
                        $('#submitBtn').prop('disabled', true);
                    },
                    complete: function() {
                        hideLoader();
                        $('#submitBtn').prop('disabled', false);
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#pendingId').val(res.pending_id);
                            $('#adOtpInput').val('');
                            $('#resendOtpBtn').prop('disabled', true);
                            startOtpTimer(res.expires_at);
                            const otpModal = new bootstrap.Modal(document.getElementById(
                                'adOtpModal'));
                            otpModal.show();
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP भेज दिया गया',
                                text: res.message || 'कृपया OTP दर्ज करें',
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'सबमिट विफल',
                                text: res.message || 'कृपया पुनः प्रयास करें'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                                .responseJSON.message : 'अनुरोध विफल रहा'
                        });
                    }
                });
            });

            $('#resendOtpBtn').on('click', function() {
                const pendingId = $('#pendingId').val();
                if (!pendingId) return;

                $.ajax({
                    url: "{{ url('admin/advertisement/otp/resend') }}",
                    type: 'POST',
                    data: {
                        pending_id: pendingId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#resendOtpBtn').prop('disabled', true);
                    },
                    success: function(res) {
                        if (res.success) {
                            startOtpTimer(res.expires_at);
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP भेजा गया',
                                text: res.message || 'नया OTP भेजा गया है'
                            });
                        } else {
                            $('#resendOtpBtn').prop('disabled', false);
                            Swal.fire({
                                icon: 'warning',
                                title: 'त्रुटि',
                                text: res.message || 'OTP नहीं भेजा जा सका'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#resendOtpBtn').prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                                .responseJSON.message : 'अनुरोध विफल'
                        });
                    }
                });
            });

            $('#verifyOtpBtn').on('click', function() {
                const pendingId = $('#pendingId').val();
                const otp = $('#adOtpInput').val();

                if (!otp || otp.length !== 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'OTP आवश्यक',
                        text: 'कृपया 6 अंकों का OTP दर्ज करें'
                    });
                    return;
                }

                $.ajax({
                    url: "{{ url('admin/advertisement/otp/verify') }}",
                    type: 'POST',
                    data: {
                        pending_id: pendingId,
                        otp: otp,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    complete: function() {
                        hideLoader();
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'सफल',
                                text: res.message || 'OTP सत्यापित',
                                timer: 1800
                            }).then(() => {
                                window.location.href = res.redirect_url ||
                                    "{{ url('/admin/show-advertisment') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'अमान्य',
                                text: res.message || 'OTP सत्यापन विफल'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                                .responseJSON.message : 'अनुरोध विफल'
                        });
                    }
                });
            });

            function startOtpTimer(expiresAt) {
                let remainingSeconds = 120;
                if (expiresAt) {
                    const expiry = new Date(expiresAt).getTime();
                    const now = new Date().getTime();
                    remainingSeconds = Math.max(0, Math.round((expiry - now) / 1000));
                }

                $('#resendOtpBtn').prop('disabled', true);
                updateTimerDisplay(remainingSeconds);

                if (window.adOtpTimerInterval) {
                    clearInterval(window.adOtpTimerInterval);
                }

                window.adOtpTimerInterval = setInterval(() => {
                    remainingSeconds--;
                    updateTimerDisplay(remainingSeconds);

                    if (remainingSeconds <= 0) {
                        clearInterval(window.adOtpTimerInterval);
                        $('#otpTimer').text('00:00');
                        $('#resendOtpBtn').prop('disabled', false);
                    }
                }, 1000);
            }

            function updateTimerDisplay(seconds) {
                const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
                const secs = String(seconds % 60).padStart(2, '0');
                $('#otpTimer').text(`${mins}:${secs}`);
            }
        });
    </script>

    <style>
        @if (isset($readonly) && $readonly)
            .form-control[readonly] {
                background-color: #f8f9fa;
                opacity: 1;
            }

            textarea[readonly] {
                background-color: #f8f9fa;
                opacity: 1;
            }

            .form-label {
                font-weight: 600;
            }

            .card-body {
                background-color: #fafafa;
            }
        @endif
    </style>
@endsection
