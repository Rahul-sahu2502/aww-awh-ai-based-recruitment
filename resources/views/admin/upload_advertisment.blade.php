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
                    <li class="breadcrumb-item active">विज्ञापन जोड़ें</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-file-plus me-2"></i>नया विज्ञापन जोड़ें
                            </h5>
                            <a href="/admin/show-advertisment" class="btn btn-success ">
                                <i class="bi bi-list me-2"></i>विज्ञापन की सूची
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- OTP NOTES --}}

                       
                        <div class="row m-2">
                            <div class="col-12">

                                <div class="alert alert-danger border-start border-2 border-danger p-2">

                                    <!-- Header -->
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <strong>
                                            ⚠️ महत्वपूर्ण सूचना (OTP सत्यापन अनिवार्य)
                                        </strong>
                                    </div>

                                    <hr class="my-2">

                                    <!-- Steps -->
                                    <ol class="mb-1 ps-3 small">
                                        <li>
                                            विज्ञापन जोड़ने से पहले
                                            <strong>OTP सत्यापन</strong> करना अनिवार्य है
                                        </li>

                                        <li>
                                            OTP आपके <strong>पंजीकृत मोबाइल नंबर</strong> पर भेजा जाएगा
                                        </li>

                                        <li>
                                            प्राप्त OTP को
                                            <strong>OTP Verification</strong> सेक्शन में दर्ज करें
                                        </li>

                                        <li>
                                            <strong>OTP सत्यापन सफल होने के बाद ही</strong>
                                            विज्ञापन जोड़ने / सबमिट करने की अनुमति मिलेगी
                                        </li>

                                        <li>
                                            OTP सत्यापन असफल या लंबित होने की स्थिति में
                                            <strong>विज्ञापन सबमिट नहीं किया जा सकेगा</strong>
                                        </li>
                                    </ol>

                                    <!-- Flow -->
                                    <div class="bg-warning-subtle text-dark px-2 py-1 mt-1 small rounded">
                                        <strong>क्रम :</strong>
                                        विज्ञापन सबमिट करें →
                                        OTP प्राप्त करें →
                                        OTP सत्यापन →
                                        विज्ञापन सफलतापूर्वक जोड़ा जाएगा
                                    </div>

                                </div>

                            </div>
                        </div>



                        <div class="row "><br>
                            <form id="advertisementForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-12"><br>
                                    <div class="field_wrapper" id="inputFieldsContainer">
                                        <div class="row g-3">

                                            <div class="col-md-6">
                                                <label for="advertisement_title" class="form-label">विज्ञापन का शीर्षक<font
                                                        color="red">*</font></label>
                                                <input type="text" class="form-control" id="advertisement_title"
                                                    name="advertisement_title" required>
                                                <span class="text-danger error-advertisement_title"></span>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="advertisement_date" class="form-label">विज्ञापन जारी दिनांक<font
                                                        color="red">*</font></label>
                                                <input type="date" class="form-control" id="advertisement_date"
                                                    name="advertisement_date" required>
                                                <span class="text-danger error-advertisement_date"></span>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="advertisement_document" class="form-label">विज्ञापन का दस्तावेज़
                                                    <font color="red">*</font>
                                                </label>
                                                <input type="file" class="form-control" id="advertisement_document"
                                                    name="advertisement_document" accept=".pdf" required>
                                                <a id="viewButton" class=" btn-danger d-none" data-bs-toggle="modal"
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
                                                        <div class="modal-body text-center">
                                                            <img id="imagePreview" class="img-fluid d-none"
                                                                alt="Selected File">
                                                            <iframe id="docViewer" class="w-100 d-none"
                                                                style="height: 500px; border: none;"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Bootstrap Modal for Cutting Paper -->
                                            <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
                                                id="cuttingModal" tabindex="-1" aria-labelledby="cuttingModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="cuttingModalLabel">Selected
                                                                Cutting
                                                                File</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img id="cuttingImagePreview" class="img-fluid d-none"
                                                                alt="Selected Cutting File">
                                                            <iframe id="cuttingDocViewer" class="w-100 d-none"
                                                                style="height: 500px; border: none;"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="date_for_age" class="form-label">विज्ञापन की वैधता तिथि<font
                                                        color="red">*</font></label>
                                                <input type="date" class="form-control" id="date_for_age"
                                                    name="date_for_age" required>
                                                <span class="text-danger error-date_for_age"></span>
                                            </div>

                                            <div class="col-md-12">
                                                <label for="advertisement_description" class="form-label">विज्ञापन का
                                                    विवरण
                                                    <font color="red">*</font>
                                                </label>
                                                <textarea class="form-control" id="advertisement_description"
                                                    name="advertisement_description" rows="4" required></textarea>
                                                <span class="text-danger error-advertisement_description"></span>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="newspaper_publish_date" class="form-label">सूचना
                                                    प्रसारित का दिनांक</label>
                                                <input type="date" class="form-control" id="newspaper_publish_date"
                                                    name="newspaper_publish_date">
                                                <span class="text-danger error-newspaper_publish_date"></span>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="newspaper_cutting_doc" class="form-label">प्रसारित पत्र
                                                    अपलोड करें </label>
                                                <input type="file" class="form-control" id="newspaper_cutting_doc"
                                                    name="newspaper_cutting_doc" accept=".pdf,.jpg,.jpeg,.png">
                                                <a id="viewCuttingButton" class=" btn-danger d-none" data-bs-toggle="modal"
                                                    data-bs-target="#cuttingModal" style="cursor: pointer;">
                                                    View Document
                                                </a>
                                                <small class="text-muted">केवल PDF, JPG, JPEG, PNG फाइलें स्वीकार्य
                                                    हैं (अधिकतम फाइल साइज़ 2MB)</small>
                                                <span class="text-danger error-newspaper_cutting_doc"></span>
                                            </div>

                                            <div class="col-md-12 text-end">
                                                <button type="submit" id="submitBtn" class="btn btn-primary">ओटीपी
                                                    भेजे</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
    </main>
@endsection


@section('scripts')
    {{--
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script>
        // New File Upload Logic
        document.getElementById("advertisement_document").addEventListener("change", function (event) {
            let file = event.target.files[0]; // Get selected file
            // Allowed file types
            let allowedTypes = ["application/pdf"];
            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: "error",
                        title: "⚠ गलत फ़ाइल प्रकार!",
                        text: "केवल PDF फ़ाइल ही स्वीकार की जाती हैं।",
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

        // Event listener for newspaper cutting document
        document.getElementById("newspaper_cutting_doc").addEventListener("change", function (event) {
            let file = event.target.files[0]; // Get selected file
            // Allowed file types
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
                    event.target.value = ""; // Reset file input
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
                    event.target.value = ""; // Reset file input
                    return;
                }

                let fileURL = URL.createObjectURL(file); // Generate file URL
                let viewButton = document.getElementById("viewCuttingButton");
                let docViewer = document.getElementById("cuttingDocViewer");
                let imagePreview = document.getElementById("cuttingImagePreview");

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

        // this function ensure that in advertisment you cannot select the previous date from current date.
        document.addEventListener("DOMContentLoaded", function () {
            var advertisementDateField = document.getElementById("advertisement_date");
            // Set today's date as minimum for advertisement_date
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            // Format the date as YYYY-MM-DD
            today = yyyy + '-' + mm + '-' + dd;

            // Set the "min" attribute to today's date for advertisement_date
            document.getElementById("advertisement_date").min = today;

            // Ensure that the date entered by the user is not earlier than today
            advertisementDateField.addEventListener("input", function () {
                var enteredDate = this.value;
                if (enteredDate < today) {
                    // If user enters a date before today, set it back to today
                    this.setCustomValidity("The date must be today or in the future.");
                    this.value = today;
                } else {
                    this.setCustomValidity(""); // Reset custom validity if the input is valid
                }
            });
        });

        // document.getElementById("advertisement_date").addEventListener("change", function () {
        //     let startDate = this.value;
        //     let endDateField = document.getElementById("date_for_age");

        //     if (startDate) {
        //         endDateField.min = startDate; // Set the min attribute for the end date
        //         endDateField.value = ""; // Reset end date field if a new start date is selected
        //     }
        // });

        document.getElementById("advertisement_date").addEventListener("change", function () {
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

        $("#enableDscBtn2").click(function (e) {
            e.preventDefault();
            enableDscBtn();

            if (dscRegisterStatus == 1 || dscRegisterStatus == 3) {
                $('#regBtn').text('पंजीकृत'); // Change button label
                $('#enableDscBtn2').addClass('disabled').css('pointer-events', 'none').css('opacity', '0.6');
            }
        });

        $(document).ready(function () {
            $('#advertisementForm').on('submit', function (e) {
                e.preventDefault();
                // Disable button and show loader
                $('#submitBtn').attr('disabled', true);
                let formData = new FormData(this);
                let dscPrintResponse = null;
                let serialNumber = null;

                $('.text-danger').text(''); // Clear previous validation messages
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
                    success: function (response) {
                        // console.log("Response:", response);

                        if (response.documentType == "PDF") {
                            dscPrintResponse = response;
                            // console.log('dsc-add-sign: ', response);

                            showLoader();
                            $.ajax({
                                url: "http://localhost:8800/postcertificatedata",
                                data: JSON.stringify(dscPrintResponse),
                                type: 'POST',
                                async: false,
                                // dataType: "json",
                                contentType: "application/json",
                                success: function (response) {
                                    // console.log('postcertificatedata: ', response);
                                    if (response.isDocumentSigned == true) {
                                        let postCertResponse = response;

                                        $.ajax({
                                            url: "{{ route('advertisement.store') }}",
                                            type: "POST",
                                            data: formData,
                                            async: false,
                                            contentType: false,
                                            processData: false,
                                            success: function (response) {
                                                // console.log("Advertisement Store Response:", response.insert_id);
                                                if (response
                                                    .insert_id) {
                                                    // console.log("Insert ID:", response.insert_id);
                                                    dscSignAjax(
                                                        postCertResponse,
                                                        response
                                                            .insert_id)
                                                        .then(() => {
                                                            Swal.fire({
                                                                icon: "success",
                                                                title: "डेटा सफलतापूर्वक दर्ज कर लिया गया हैं।",
                                                                text: response
                                                                    .message,
                                                                confirmButtonText: "OK"
                                                            })
                                                                .then(
                                                                    () => {
                                                                        location
                                                                            .reload();
                                                                    }
                                                                );
                                                        })
                                                        .catch((
                                                            error
                                                        ) => {
                                                            // console.error("DSC Sign failed:", error);
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
                                            error: function (xhr) {
                                                $('#submitBtn').attr(
                                                    'disabled',
                                                    false);
                                                if (xhr.status ===
                                                    422) {
                                                    let errors = xhr
                                                        .responseJSON
                                                        .errors;
                                                    $.each(errors,
                                                        function (
                                                            key,
                                                            value) {
                                                            $('.error-' +
                                                                key
                                                            )
                                                                .text(
                                                                    value[
                                                                    0
                                                                    ]
                                                                );
                                                        });
                                                } else {
                                                    Swal.fire({
                                                        icon: "error",
                                                        title: "त्रुटि!",
                                                        text: xhr
                                                            .responseJSON
                                                            .message ||
                                                            "Something went wrong!",
                                                        confirmButtonText: "OK"
                                                    });
                                                }
                                            }
                                        });

                                    } else {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'DSC Not Recognized',
                                            text: 'Please connect registered DSC into the machine and try again.'
                                        });
                                        $('#submitBtn').attr('disabled', false);
                                    }
                                },
                                error: function () {
                                    reject(xhr);
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Error',
                                        text: 'An error occurred while sign DSC.'
                                    });
                                    $('#submitBtn').attr('disabled', false);
                                },
                                complete: function () {
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
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Error',
                            text: 'Something went wrong while uploading PDF.'
                        });
                        $('#submitBtn').attr('disabled', false);
                        return;
                    }
                });
            });
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
                    success: function (res) {
                        // console.log('dsc-sign-save '+res);
                        resolve(res);
                    },
                    error: function () {
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
                beforeSend: function () {
                    // Optionally, you can show a loader here
                    $("#submitBtn").attr('disabled', true);
                },
                success: function (response) {
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
                error: function () {
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

        /*
        |------------------------------------------------------------------
        | OTP Flow for Advertisement
        |------------------------------------------------------------------
        */
        $(document).ready(function () {
            // Override old submit handler with OTP flow
            $('#advertisementForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('mode', 'create');

                $.ajax({
                    url: "{{ url('admin/advertisement/otp/request') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        showLoader();
                        $('#submitBtn').prop('disabled', true);
                    },
                    complete: function () {
                        hideLoader();
                        $('#submitBtn').prop('disabled', false);
                    },
                    success: function (res) {
                        if (res.success) {
                            $('#pendingId').val(res.pending_id);
                            $('#adOtpInput').val('');
                            $('#resendOtpBtn').prop('disabled', true);
                            startOtpTimer(res.expires_at);
                            const otpModal = new bootstrap.Modal(document.getElementById('adOtpModal'));
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
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'अनुरोध विफल रहा'
                        });
                    }
                });
            });

            $('#resendOtpBtn').on('click', function () {
                const pendingId = $('#pendingId').val();
                if (!pendingId) return;

                $.ajax({
                    url: "{{ url('admin/advertisement/otp/resend') }}",
                    type: 'POST',
                    data: {
                        pending_id: pendingId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('#resendOtpBtn').prop('disabled', true);
                    },
                    success: function (res) {
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
                    error: function (xhr) {
                        $('#resendOtpBtn').prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'अनुरोध विफल'
                        });
                    }
                });
            });

            $('#verifyOtpBtn').on('click', function () {
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
                    beforeSend: function () {
                        showLoader();
                    },
                    complete: function () {
                        hideLoader();
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'सफल',
                                text: res.message || 'OTP सत्यापित',
                                timer: 1800
                            }).then(() => {
                                window.location.href = res.redirect_url || "{{ url('/admin/show-advertisment') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'अमान्य',
                                text: res.message || 'OTP सत्यापन विफल'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'अनुरोध विफल'
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
@endsection