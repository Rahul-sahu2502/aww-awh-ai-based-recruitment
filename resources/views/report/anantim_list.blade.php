@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .report-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .btn-download {
            min-width: 200px;
            padding: 10px 20px;
            font-size: 16px;
        }

        #anatimListTable {
            font-size: 0.95rem;
        }

        #anatimListTable thead th {
            vertical-align: middle;
            font-weight: 600;
            color: #fff;
        }

        #anatimListTable tbody tr:hover {
            background-color: #f5f5f5;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
        }

        .badge {
            padding: 0.35rem 0.65rem;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">अनंतिम सूची </h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if (session('sess_role') === 'Super_admin')
                            <a href="{{ url('/admin/admin-dashboard') }}">होम</a>
                        @elseif (session('sess_role') === 'Admin')
                            <a href="{{ url('/examinor/examinor-dashboard') }}">होम</a>
                        @endif
                    </li>
                    <li class="breadcrumb-item active">अनंतिम सूची </li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card report-card">

                    <!-- Card Header with Tabs -->
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="download-tab" data-bs-toggle="tab"
                                    data-bs-target="#download" type="button" role="tab">
                                    <i class="bi bi-download"></i> अनंतिम सूची डाउनलोड करें
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload"
                                    type="button" role="tab">
                                    <i class="bi bi-upload"></i> अनंतिम सूची अपलोड करें
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body tab-content">

                        <!-- ================= TAB 1 : DOWNLOAD ================= -->
                        <div class="tab-pane fade show active" id="download" role="tabpanel">
                            <form id="downloadForm">

                                <div class="mb-3 mt-2">
                                    <label class="form-label">रिपोर्ट डाउनलोड विकल्प <span
                                            class="text-danger">*</span></label>
                                    <select name="reportdownloadOption" id="reportdownloadOption" class="form-control"
                                        required>
                                        <option value="">-- चयन करें --</option>
                                        <option value="all">सभी आवेदनों के लिए </option>
                                        <option value="other">पात्र आवेदनों के लिए </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">विज्ञापन चुनें <span class="text-danger">*</span></label>
                                    <select name="advertisement_id" id="download_advertisement" class="form-control"
                                        required>
                                        <option value="">-- विज्ञापन चुनें --</option>
                                        @foreach ($advertisements as $index => $ad)
                                            <option value="{{ $ad->Advertisement_ID }}">
                                                {{ $index + 1 }}. {{ $ad->Advertisement_Title }}
                                                @if ($ad->Advertisement_Date)
                                                    ({{ \Carbon\Carbon::parse($ad->Advertisement_Date)->format('d-m-Y') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">पद चुनें <span class="text-danger">*</span></label>
                                    <select name="post_id" id="download_post" class="form-control" disabled required>
                                        <option value="">-- पहले विज्ञापन चुनें --</option>
                                    </select>
                                </div>

                                <div class="text-center">
                                    <button type="button" class="btn btn-success" id="btnDownloadReport" disabled>
                                        <i class="bi bi-download"></i> रिपोर्ट डाउनलोड करें
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- ================= TAB 2 : UPLOAD ================= -->
                        <div class="tab-pane fade" id="upload" role="tabpanel">
                            <form id="uploadForm">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">विज्ञापन चुनें <span
                                            class="text-danger">*</span></label>
                                    <select name="advertisement_id" id="upload_advertisement" class="form-control" required>
                                        <option value="">-- विज्ञापन चुनें --</option>
                                        @foreach ($advertisements as $index => $ad)
                                            <option value="{{ $ad->Advertisement_ID }}">
                                                {{ $index + 1 }}. {{ $ad->Advertisement_Title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">पद चुनें <span class="text-danger">*</span></label>
                                    <select name="post_id" id="upload_post" class="form-control" disabled required>
                                        <option value="">-- पहले विज्ञापन चुनें --</option>
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">दावा आपत्ति जारी दिनांक <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="claim_start_date"
                                            id="claimStartDate" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">दावा आपत्ति की वैधता तिथि <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="claim_end_date"
                                            id="ClaimEndDate" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">फ़ाइल अपलोड करें <span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control" accept=".pdf" name="anantim_file"
                                        id="upload_anantim_file" required>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input border border-primary" type="checkbox"
                                        id="confirm_anantim_upload" name="confirm_anantim_upload" required>
                                    <label class="form-check-label" for="confirm_anantim_upload">क्या आप अनंतिम सूची अपलोड
                                        करना चाहते हैं ?</label>
                                </div>

                                <div class="text-center">
                                    <button type="submit" id="UploadButton" class="btn btn-primary" disabled>
                                        <i class="bi bi-upload"></i> अपलोड करें
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- INFO NOTE -->
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>नोट:</strong> पहले विज्ञापन चुनें, फिर पद चुनें।
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="card mt-3" id="anantimTableCard" style="display: none;">
            <div class="card-header">
                <h5 class="card-title mb-0">अनंतिम सूची विवरण</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-bordered w-100" id="anatimListTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">क्रम</th>
                            <th style="width: 20%;">पद का नाम</th>
                            <th style="width: 20%;">विज्ञापन</th>
                            <th style="width: 15%;">परियोजना</th>
                            <th style="width: 15%;">दावा आपत्ति अवधि</th>
                            <th style="width: 25%;">कार्य</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anantim_list as $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- <i class="bi bi-file-earmark-pdf text-danger me-2" style="font-size: 18px;"></i> --}}
                                        <span>{{ $item->postname }} </span>
                                    </div>
                                </td>
                                <td>{{ $item->Advertisement_Title }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $item->project }}</span>
                                    @if ($item->nnn_name || $item->panchayat_name_hin)
                                        <br>
                                        <small class="text-muted">
                                            {{ $item->nnn_name ? $item->nnn_name : $item->panchayat_name_hin }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small class="d-block">
                                        <strong>शुरुआत :</strong> {{ date('d-m-Y', strtotime($item->claim_start_date)) }}
                                    </small>
                                    <small class="d-block">
                                        <strong>अंत :</strong> {{ date('d-m-Y', strtotime($item->claim_end_date)) }}
                                    </small>
                                </td>
                                @php
                                    $fileExists = !empty($item->anantim_list_file);
                                    $file_path = '';
                                    if ($fileExists) {
                                        $file_path = asset('uploads/' . $item->anantim_list_file);
                                        if (config('app.env') === 'production') {
                                            $file_path = config('custom.file_point') . $item->anantim_list_file;
                                        }
                                    }
                                @endphp
                                <td>
                                    @if ($fileExists)
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary view-pdf-btn"
                                                data-pdf="{{ $file_path }}"
                                                data-title="{{ $item->postname }} - {{ $item->Advertisement_Title }}"
                                                title="देखें"><i class="bi bi-eye"></i> देखें
                                            </button>
                                            <a href="{{ $file_path }}" class="btn btn-outline-success" download
                                                title="डाउनलोड करें">
                                                <i class="bi bi-download"></i> डाउनलोड
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i> दस्तावेज़ उपलब्ध नहीं
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-4" colspan="6">
                                    <i class="bi bi-inbox" style="font-size: 32px; color: #ccc;"></i>
                                    <p class="text-muted mt-2">कोई अनंतिम सूची उपलब्ध नहीं है।</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PDF Viewer Modal -->
        <div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfViewerModalLabel">दस्तावेज़ पूर्वावलोकन</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="modalPdfViewer" src="" class="w-100"
                            style="height: 80vh; border: none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> बंद करें
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {

            // Setup PDF Viewer
            setupPdfViewer();

            // Initialize DataTable when table becomes visible
            let anatimTable = null;
            let anatimTableInitialized = false;

            window.initializeAnatimTable = function() {
                // Check if table has proper rows before initializing
                const table = $('#anatimListTable');
                const headerCols = table.find('thead th').length;
                const firstRowCols = table.find('tbody tr:first td').length;

                // Only initialize if we have proper data structure
                if (headerCols === 6 && firstRowCols === 6 &&
                    !anatimTableInitialized &&
                    $.fn.DataTable.isDataTable('#anatimListTable') === false) {

                    anatimTable = $('#anatimListTable').DataTable({
                        responsive: true,
                        language: {
                            "search": "खोजें:",
                            "lengthMenu": "प्रति पृष्ठ _MENU_ रिकॉर्ड दिखाएं",
                            "info": "_START_ से _END_ तक, कुल _TOTAL_ रिकॉर्ड",
                            "infoEmpty": "कोई रिकॉर्ड नहीं",
                            "paginate": {
                                "first": "पहला",
                                "last": "आखिरी",
                                "next": "अगला",
                                "previous": "पिछला"
                            }
                        },
                        pageLength: 10,
                        order: [
                            [0, 'asc']
                        ],
                        columnDefs: [{
                            orderable: false,
                            targets: [5]
                        }]
                    });
                    anatimTableInitialized = true;
                } else if (headerCols === 6 && firstRowCols === 1) {
                    // This is the empty state - show appropriate message
                    console.log('Table is empty - showing message instead of DataTable');
                }
            };

            // Date Validations
            const today = new Date().toISOString().split('T')[0];

            // 1️⃣ Start Date: Disable past dates
            $('#claimStartDate').attr('min', today);

            // 2️⃣ End Date: By default disable past dates
            $('#ClaimEndDate').attr('min', today);

            // 3️⃣ When Start Date changes
            $('#claimStartDate').on('change', function() {

                const startDate = $(this).val();

                if (startDate) {
                    // Disable end dates before selected start date
                    $('#ClaimEndDate')
                        .attr('min', startDate)
                        .val(''); // reset end date if already selected
                }
            });


            // Enable & Load Post When Advertisement select
            function loadPostsByAdvertisement(adSelectId, postSelectId, buttonId = null) {
                $(adSelectId).on('change', function() {
                    const advertisementId = $(this).val();
                    const postSelect = $(postSelectId);
                    const actionBtn = buttonId ? $(buttonId) : null;

                    // Determine isUpload value: if buttonId is null => isUpload = 1, else isUpload = 0
                    const isUpload = buttonId ? 0 : 1;

                    // Reset
                    postSelect
                        .html('<option value="">-- लोड हो रहा है... --</option>')
                        .prop('disabled', true);

                    if (actionBtn) {
                        actionBtn.prop('disabled', true);
                    }

                    if (advertisementId) {
                        $.ajax({
                            url: "{{ route('report.getPostsByAdvertisement') }}",
                            method: "GET",
                            data: {
                                advertisement_id: advertisementId,
                                is_upload: isUpload // Add is_upload parameter
                            },
                            success: function(response) {
                                if (response.success && response.posts.length > 0) {
                                    postSelect.html('<option value="">-- पद चुनें --</option>');

                                    response.posts.forEach(function(post) {
                                        // Get location name
                                        const locationName = post.nnn_names || post
                                            .panchayat_names || '';

                                        // Format: आंगनबाड़ी कार्यकर्ता (Tilda - आसौदा, एडसेना, बंगोली)
                                        let displayText = post.title;

                                        if (post.project) {
                                            if (locationName) {
                                                displayText +=
                                                    ` (${post.project} - ${locationName})`;
                                            } else {
                                                displayText += ` (${post.project})`;
                                            }
                                        } else if (locationName) {
                                            displayText += ` (${locationName})`;
                                        }

                                        postSelect.append(
                                            `<option value="${post.post_id}">${displayText}</option>`
                                        );
                                    });

                                    postSelect.prop('disabled', false);

                                } else {
                                    postSelect.html(
                                        '<option value="">-- कोई पद उपलब्ध नहीं है --</option>'
                                    );

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'कोई पद नहीं मिला',
                                        text: 'इस विज्ञापन के लिए कोई पद उपलब्ध नहीं है।',
                                        confirmButtonText: 'ठीक है'
                                    });
                                }
                            },
                            error: function() {
                                postSelect.html(
                                    '<option value="">-- त्रुटि: पद लोड नहीं हो सके --</option>'
                                );

                                Swal.fire({
                                    icon: 'error',
                                    title: 'त्रुटि',
                                    text: 'पद लोड करने में समस्या आई। कृपया पुनः प्रयास करें।',
                                    confirmButtonText: 'ठीक है'
                                });
                            }
                        });

                    } else {
                        postSelect.html('<option value="">-- पहले विज्ञापन चुनें --</option>');
                    }
                });
            }

            //Download Tab
            loadPostsByAdvertisement(
                '#download_advertisement',
                '#download_post',
                '#btnDownload'
            );

            // Upload Tab
            loadPostsByAdvertisement(
                '#upload_advertisement',
                '#upload_post'
            );

            // Enable download button when post is selected
            $('#download_post').on('change', function() {
                const postId = $(this).val();
                const downloadBtn = $('#btnDownloadReport');

                if (postId) {
                    downloadBtn.prop('disabled', false);
                } else {
                    downloadBtn.prop('disabled', true);
                }
            });

            // Manage upload button enabling for anantim: require both file and confirmation checkbox
            function updateAnantimUploadButtonState() {
                const fileSelected = $('#upload_anantim_file').get(0).files.length > 0;
                const confirmed = $('#confirm_anantim_upload').is(':checked');
                const UploadButton = $('#UploadButton');
                UploadButton.prop('disabled', !(fileSelected && confirmed));
            }

            $('#upload_anantim_file, #confirm_anantim_upload').on('change', updateAnantimUploadButtonState);


            // Download report button click
            $('#btnDownloadReport').on('click', function() {
                const advertisementId = $('#download_advertisement').val();
                const postId = $('#download_post').val();
                const reportdownloadOption = $('#reportdownloadOption').val();

                if (!advertisementId || !postId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'चयन आवश्यक',
                        text: 'कृपया विज्ञापन और पद दोनों चुनें।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'रिपोर्ट तैयार हो रही है...',
                    text: 'कृपया प्रतीक्षा करें',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Download report
                window.location.href =
                    `{{ route('report.downloadPostWiseReport') }}?advertisement_id=${advertisementId}&post_id=${postId}&is_antim_list=false&reportdownloadOption=${reportdownloadOption}`;

                // Close loading after 2 seconds
                setTimeout(function() {
                    Swal.close();
                }, 2000);
            });


            // Upload Anantim List 
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                const advertisementId = $('#upload_advertisement').val();
                const postId = $('#upload_post').val();
                const claimStartDate = $('#claimStartDate').val();
                const claimEndDate = $('#ClaimEndDate').val();
                const fileInput = $('#upload_anantim_file')[0];

                // ================= VALIDATIONS =================

                if (!advertisementId || !postId || !claimStartDate || !claimEndDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'अधूरी जानकारी',
                        text: 'कृपया सभी आवश्यक फ़ील्ड भरें।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                // Confirmation checkbox must be checked
                if (!$('#confirm_anantim_upload').is(':checked')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'कन्फर्मेशन आवश्यक है',
                        text: 'कृपया पुष्टि करें कि आप अनंतिम सूची अपलोड करना चाहते हैं।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                if (!fileInput.files.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'फ़ाइल आवश्यक है',
                        text: 'कृपया PDF फ़ाइल अपलोड करें।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                const file = fileInput.files[0];
                const allowedType = 'application/pdf';
                const maxSize = 2 * 1024 * 1024; // 2 MB

                if (file.type !== allowedType) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'अमान्य फ़ाइल',
                        text: 'केवल PDF फ़ाइल अपलोड करने की अनुमति है।',
                        confirmButtonText: 'ठीक है'
                    });
                    fileInput.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'फ़ाइल बहुत बड़ी है',
                        text: 'PDF फ़ाइल का आकार अधिकतम 2 MB होना चाहिए।',
                        confirmButtonText: 'ठीक है'
                    });
                    fileInput.value = '';
                    return;
                }

                // ================= AJAX =================

                const formData = new FormData(this);

                $('#UploadButton').prop('disabled', true).text('अपलोड हो रहा है...');

                $.ajax({
                    url: "{{ route('anantim.list.upload') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'सफल',
                            text: response.message ??
                                'फ़ाइल सफलतापूर्वक अपलोड हो गई है।',
                            confirmButtonText: 'ठीक है'
                        });

                        $('#uploadForm')[0].reset();
                        $('#upload_post').prop('disabled', true);

                        // Reload table
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {

                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: xhr.responseJSON?.message ?? 'अपलोड में समस्या आई।',
                            confirmButtonText: 'ठीक है'
                        });
                    },
                    complete: function() {
                        $('#UploadButton')
                            .prop('disabled', false)
                            .html('<i class="bi bi-upload"></i> अपलोड करें');
                    }
                });
            });


        });
    </script>
    <script>
        $(document).ready(function() {
            const tableCard = $('#anantimTableCard');

            // Upload tab show → table show
            $('#upload-tab').on('shown.bs.tab', function() {
                tableCard.show();
                if (typeof initializeAnatimTable === 'function') {
                    initializeAnatimTable();
                }
            });

            // Download tab show → table hide
            $('#download-tab').on('shown.bs.tab', function() {
                tableCard.hide();
            });

            // Initialize PDF viewer
            setupPdfViewer();
        });

        function setupPdfViewer() {
            const $modalPdfViewer = $('#modalPdfViewer');
            const pdfViewerModal = new bootstrap.Modal($('#pdfViewerModal')[0]);
            const $modalTitle = $('#pdfViewerModalLabel');
            const modalElement = $('#pdfViewerModal')[0];

            // Use event delegation for dynamically rendered buttons
            $(document).on('click', '.view-pdf-btn', function() {
                const pdfUrl = $(this).data('pdf');
                const title = $(this).data('title');

                $modalPdfViewer.attr('src', pdfUrl);
                $modalTitle.text(title);
                pdfViewerModal.show();
            });

            // Clear iframe and properly cleanup backdrop when modal is closed
            $('#pdfViewerModal').on('hidden.bs.modal', function() {
                $modalPdfViewer.attr('src', '');

                // Remove any lingering backdrops
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });

                // Ensure body scroll is re-enabled
                document.body.classList.remove('modal-open');
            });
        }
    </script>
@endsection
