@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}" />
    <style>
        .dataTables_filter,
        .dataTables_length {
            margin-bottom: 12px;
        }

        /* Filter Toggle Button */
        .filter-toggle-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            /* margin-bottom: 15px; */
        }

        .filter-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .filter-toggle-btn:active {
            transform: translateY(0);
        }

        .filter-toggle-btn i {
            transition: transform 0.3s ease;
        }

        .filter-toggle-btn.active i.bi-chevron-down {
            transform: rotate(180deg);
        }

        /* Filter Container */
        .filter-container {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0;
        }

        .filter-container.show {
            max-height: 500px;
            opacity: 1;
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 15px;
        }

        /* Filter Group Grid Layout */
        .custom-filter-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .custom-filter-group .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .custom-filter-group .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .custom-filter-group .form-control,
        .custom-filter-group .form-select {
            height: 38px;
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .custom-filter-group .form-control:focus,
        .custom-filter-group .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        /* Filter Action Buttons */
        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
            border: none;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary.filter-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary.filter-btn {
            background: #6c757d;
            color: white;
        }

        .add-advertisement-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            /* Green gradient */
        }

        .add-advertisement-btn:hover {
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .custom-filter-group {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
            }
        }

        .badge {
            font-size: 0.85rem;
        }

        .modal-body img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .feedback-detail-label {
            font-weight: 600;
            color: #495057;
            margin-top: 15px;
        }

        .feedback-detail-value {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .image-gallery img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .image-gallery img:hover {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">प्रतिक्रिया प्रबंधन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if (session('sess_role') === 'Super_admin')
                            <a href="{{ url('/admin/admin-dashboard') }}">होम</a>
                        @elseif (session('sess_role') === 'Admin')
                            <a href="{{ url('/examinor/examinor-dashboard') }}">होम</a>
                        @endif
                    </li>
                    <li class="breadcrumb-item active">प्रतिक्रिया सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-chat-left-text me-2"></i> सभी प्रतिक्रियाएं
                            </h5>

                            <!-- ✅ दाएँ तरफ़: फ़िल्टर बटन -->
                            <div class="d-flex gap-2">
                                <button type="button" class="filter-toggle-btn" id="toggleFilters">
                                    <i class="bi bi-funnel-fill"></i>
                                    <span>फ़िल्टर दिखाएं</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body container" style="padding:0px !important;">

                        <!-- Filters Container (Initially Hidden) -->
                        <div class="filter-container" id="filterContainer">
                            <div class="custom-filter-group">
                                <div class="form-group">
                                    <label>प्रतिक्रिया प्रकार</label>
                                    <select id="filterFeedbackType" class="form-control">
                                        <option value="">-- सभी --</option>
                                        <option value="suggestion">सुझाव</option>
                                        <option value="complaint">शिकायत</option>
                                        <option value="appreciation">प्रशंसा</option>
                                        <option value="bug_report">बग रिपोर्ट</option>
                                        <option value="feature_request">फीचर अनुरोध</option>
                                        <option value="other">अन्य</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>स्थिति</label>
                                    <select id="filterStatus" class="form-control">
                                        <option value="">-- सभी --</option>
                                        <option value="pending">लंबित</option>
                                        <option value="in_progress">प्रगति में</option>
                                        <option value="resolved">हल हो गया</option>
                                        <option value="closed">बंद</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>प्रशासक का नाम</label>
                                    <input type="text" id="filterAdminName" class="form-control"
                                        placeholder="प्रशासक का नाम">
                                </div>

                                <div class="form-group">
                                    <label>तिथि से</label>
                                    <input type="date" id="filterDateFrom" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>तिथि तक</label>
                                    <input type="date" id="filterDateTo" class="form-control">
                                </div>
                            </div>

                            <div class="filter-actions">
                                <button type="button" id="resetFilters" class="btn btn-secondary filter-btn">
                                    <i class="bi bi-arrow-clockwise me-1"></i> रीसेट करें
                                </button>
                                <button type="button" id="applyFilters" class="btn btn-primary filter-btn">
                                    <i class="bi bi-search me-1"></i> फ़िल्टर लागू करें
                                </button>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                            <table id="feedbackTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>क्रम संख्या</th>
                                        <th>प्रशासक का नाम</th>
                                        <th>प्रकार</th>
                                        <th>विषय</th>
                                        <th>संदेश</th>
                                        <th>छवियाँ</th>
                                        <th>स्थिति</th>
                                        <th>तिथि</th>
                                        <th>कार्रवाई</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Feedback Modal -->
        <div class="modal fade" id="viewFeedbackModal" tabindex="-1" aria-labelledby="viewFeedbackModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewFeedbackModalLabel">
                            <i class="bi bi-chat-left-text"></i> प्रतिक्रिया विवरण
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="feedbackDetailsContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">लोड हो रहा है...</span>
                            </div>
                            <p class="mt-2">प्रतिक्रिया विवरण लोड हो रहा है...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> बंद करें
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewModalLabel">छवि पूर्वावलोकन</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="previewImage" src="" alt="Preview" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Reply Feedback Modal -->
        <div class="modal fade" id="replyFeedbackModal" tabindex="-1" aria-labelledby="replyFeedbackModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="replyFeedbackModalLabel">
                            <i class="bi bi-reply-fill"></i> प्रतिक्रिया का जवाब दें
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="replyFeedbackForm">
                        <div class="modal-body">
                            <input type="hidden" id="replyFeedbackId" name="feedback_id">

                            <!-- Feedback Summary (Read-only) -->
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-info-circle"></i> प्रतिक्रिया सारांश
                                    </h6>
                                    <div id="feedbackSummary">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">लोड हो रहा है...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Response -->
                            <div class="mb-3">
                                <label for="adminResponse" class="form-label">
                                    <i class="bi bi-chat-dots"></i> आपका जवाब <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="adminResponse" name="admin_response" rows="5"
                                    placeholder="यहाँ अपना जवाब लिखें..." required maxlength="1000"></textarea>
                                <div class="form-text">अधिकतम 1000 अक्षर</div>
                            </div>

                            <!-- Status Update -->
                            <div class="mb-3">
                                <label for="feedbackStatus" class="form-label">
                                    <i class="bi bi-flag"></i> स्थिति अपडेट करें <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="feedbackStatus" name="status" required>
                                    <option value="">स्थिति चुनें</option>
                                    <option value="pending">लंबित</option>
                                    <option value="in_progress">प्रगति में</option>
                                    <option value="resolved">हल हो गया</option>
                                    <option value="closed">बंद</option>
                                </select>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill"></i>
                                <small>
                                    <strong>नोट:</strong> आपका जवाब प्रतिक्रिया देने वाले व्यवस्थापक को भेजा जाएगा।
                                    कृपया स्पष्ट और विस्तृत जवाब दें।
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> रद्द करें
                            </button>
                            <button type="submit" class="btn btn-success" id="submitReplyBtn">
                                <i class="bi bi-send-fill"></i> जवाब भेजें
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Filter Toggle Functionality
            $('#toggleFilters').on('click', function () {
                const filterContainer = $('#filterContainer');
                const toggleBtn = $(this);
                const btnText = toggleBtn.find('span');
                const chevronIcon = toggleBtn.find('.bi-chevron-down, .bi-chevron-up');

                filterContainer.toggleClass('show');
                toggleBtn.toggleClass('active');

                if (filterContainer.hasClass('show')) {
                    btnText.text('फ़िल्टर छुपाएं');
                    chevronIcon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                } else {
                    btnText.text('फ़िल्टर दिखाएं');
                    chevronIcon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                }
            });

            // Initialize DataTable
            var table = $('#feedbackTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.feedback.list.data') }}",
                    data: function (d) {
                        d.feedback_type = $('#filterFeedbackType').val();
                        d.status = $('#filterStatus').val();
                        d.admin_name = $('#filterAdminName').val();
                        d.date_from = $('#filterDateFrom').val();
                        d.date_to = $('#filterDateTo').val();
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'admin_name',
                    name: 'admin_name'
                },
                {
                    data: 'feedback_type',
                    name: 'feedback_type'
                },
                {
                    data: 'subject',
                    name: 'subject'
                },
                {
                    data: 'feedback_message',
                    name: 'feedback_message'
                },
                {
                    data: 'images',
                    name: 'images',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
                order: [
                    [7, 'desc']
                ], // Sort by created_at descending
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/hi.json',
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">लोड हो रहा है...</span></div>'
                },
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
            });

            // Apply filters
            $('#applyFilters').on('click', function () {
                table.draw();
            });

            // Reset filters
            $('#resetFilters').on('click', function () {
                $('#filterFeedbackType').val('');
                $('#filterStatus').val('');
                $('#filterAdminName').val('');
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                table.draw();
            });

            // View feedback details
            $(document).on('click', '.view-feedback-btn', function () {
                var feedbackId = $(this).data('id');

                // Show loading state
                $('#feedbackDetailsContent').html(`
                                                                        <div class="text-center">
                                                                            <div class="spinner-border text-primary" role="status">
                                                                                <span class="visually-hidden">लोड हो रहा है...</span>
                                                                            </div>
                                                                            <p class="mt-2">प्रतिक्रिया विवरण लोड हो रहा है...</p>
                                                                        </div>
                                                                    `);

                // Fetch feedback details
                $.ajax({
                    url: "{{ url('/admin/feedback-list') }}/" + feedbackId,
                    type: 'GET',
                    success: function (response) {
                        if (response.success && response.data) {
                            var feedback = response.data;
                            var html = `
                                                                                    <div class="row">
                                                                                        <div class="col-md-6 mb-3">
                                                                                            <div class="feedback-detail-label">प्रशासक का नाम:</div>
                                                                                            <div class="feedback-detail-value">${feedback.admin_name}</div>
                                                                                        </div>
                                                                                        <div class="col-md-6 mb-3">
                                                                                            <div class="feedback-detail-label">प्रकार:</div>
                                                                                            <div class="feedback-detail-value">${feedback.feedback_type}</div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                        <div class="col-md-6 mb-3">
                                                                                            <div class="feedback-detail-label">स्थिति:</div>
                                                                                            <div class="feedback-detail-value">${feedback.status}</div>
                                                                                        </div>
                                                                                        <div class="col-md-6 mb-3">
                                                                                            <div class="feedback-detail-label">तिथि:</div>
                                                                                            <div class="feedback-detail-value">${feedback.created_at}</div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="mb-3">
                                                                                        <div class="feedback-detail-label">विषय:</div>
                                                                                        <div class="feedback-detail-value">${feedback.subject}</div>
                                                                                    </div>

                                                                                    <div class="mb-3">
                                                                                        <div class="feedback-detail-label">संदेश:</div>
                                                                                        <div class="feedback-detail-value">${feedback.feedback_message}</div>
                                                                                    </div>
                                                                                `;

                            // Add images if available
                            if (feedback.images && feedback.images.length > 0) {
                                html += `
                                                                                        <div class="mb-3">
                                                                                            <div class="feedback-detail-label">संलग्न छवियाँ:</div>
                                                                                            <div class="image-gallery">
                                                                                    `;
                                feedback.images.forEach(function (imageUrl) {
                                    html += `<img src="${imageUrl}" alt="Feedback Image" class="feedback-image" data-image="${imageUrl}">`;
                                });
                                html += `</div></div>`;
                            }

                            // Add admin response if available
                            if (feedback.admin_response && feedback.admin_response.trim() !== '') {
                                html += `
                                                                                        <div class="mb-3">
                                                                                            <div class="feedback-detail-label">प्रशासक की प्रतिक्रिया:</div>
                                                                                            <div class="feedback-detail-value">${feedback.admin_response}</div>
                                                                                        </div>
                                                                                    `;
                                if (feedback.responded_at) {
                                    html += `
                                                                                            <div class="mb-3">
                                                                                                <div class="feedback-detail-label">प्रतिक्रिया तिथि:</div>
                                                                                                <div class="feedback-detail-value">${feedback.responded_at}</div>
                                                                                            </div>
                                                                                        `;
                                }
                            }

                            $('#feedbackDetailsContent').html(html);
                        } else {
                            $('#feedbackDetailsContent').html(`
                                                                                    <div class="alert alert-warning">
                                                                                        <i class="bi bi-exclamation-triangle"></i> प्रतिक्रिया विवरण नहीं मिला।
                                                                                    </div>
                                                                                `);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading feedback details:', error);
                        $('#feedbackDetailsContent').html(`
                                                                                <div class="alert alert-danger">
                                                                                    <i class="bi bi-x-circle"></i> प्रतिक्रिया विवरण लोड करने में त्रुटि हुई।
                                                                                </div>
                                                                            `);
                    }
                });
            });

            // Image preview
            $(document).on('click', '.feedback-image', function () {
                var imageUrl = $(this).data('image');
                $('#previewImage').attr('src', imageUrl);
                $('#imagePreviewModal').modal('show');
            });

            // Reply to feedback - Load feedback summary
            $(document).on('click', '.reply-feedback-btn', function () {
                var feedbackId = $(this).data('id');
                $('#replyFeedbackId').val(feedbackId);

                // Reset form
                $('#adminResponse').val('');
                $('#feedbackStatus').val('');

                // Show loading in summary
                $('#feedbackSummary').html(`
                                                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                                            <span class="visually-hidden">लोड हो रहा है...</span>
                                                                        </div> लोड हो रहा है...
                                                                    `);

                // Fetch feedback details for summary
                $.ajax({
                    url: "{{ url('/admin/feedback-list') }}/" + feedbackId,
                    type: 'GET',
                    success: function (response) {
                        if (response.success && response.data) {
                            var feedback = response.data;
                            var summaryHtml = `
                                                                                    <div class="row small">
                                                                                        <div class="col-md-6">
                                                                                            <strong>नाम:</strong> ${feedback.admin_name}
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <strong>प्रकार:</strong> ${feedback.feedback_type}
                                                                                        </div>
                                                                                        <div class="col-12 mt-2">
                                                                                            <strong>विषय:</strong> ${feedback.subject}
                                                                                        </div>
                                                                                        <div class="col-12 mt-2">
                                                                                            <strong>संदेश:</strong><br>
                                                                                            <div class="text-muted">${feedback.feedback_message}</div>
                                                                                        </div>
                                                                                        <div class="col-md-12 mt-2">
                                                                                            <strong>वर्तमान स्थिति:</strong> ${feedback.status}
                                                                                        </div>
                                                                                    </div>
                                                                                `;

                            $('#feedbackSummary').html(summaryHtml);

                            // Set current status as default in dropdown
                            $('#feedbackStatus').val(feedback.status_raw);
                        } else {
                            $('#feedbackSummary').html(`
                                                                                    <div class="alert alert-warning alert-sm mb-0">
                                                                                        प्रतिक्रिया विवरण लोड नहीं हो सका।
                                                                                    </div>
                                                                                `);
                        }
                    },
                    error: function () {
                        $('#feedbackSummary').html(`
                                                                                <div class="alert alert-danger alert-sm mb-0">
                                                                                    विवरण लोड करने में त्रुटि हुई।
                                                                                </div>
                                                                            `);
                    }
                });
            });

            // Submit reply form
            $('#replyFeedbackForm').on('submit', function (e) {
                e.preventDefault();

                var feedbackId = $('#replyFeedbackId').val();
                var adminResponse = $('#adminResponse').val().trim();
                var status = $('#feedbackStatus').val();

                // Validation
                if (!adminResponse) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'चेतावनी',
                        text: 'कृपया अपना जवाब लिखें।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                if (!status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'चेतावनी',
                        text: 'कृपया स्थिति चुनें।',
                        confirmButtonText: 'ठीक है'
                    });
                    return;
                }

                // Disable submit button
                var $submitBtn = $('#submitReplyBtn');
                $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> भेजा जा रहा है...');

                // Submit via AJAX
                $.ajax({
                    url: "{{ url('/admin/feedback-list') }}/" + feedbackId + "/reply",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        admin_response: adminResponse,
                        status: status
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'सफलता',
                                text: response.message || 'जवाब सफलतापूर्वक सबमिट किया गया।',
                                confirmButtonText: 'ठीक है'
                            }).then(function () {
                                // Close modal
                                $('#replyFeedbackModal').modal('hide');

                                // Reload DataTable
                                table.ajax.reload(null, false);

                                // Reset form
                                $('#replyFeedbackForm')[0].reset();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'त्रुटि',
                                text: response.message || 'जवाब सबमिट करने में त्रुटि हुई।',
                                confirmButtonText: 'ठीक है'
                            });
                        }
                    },
                    error: function (xhr) {
                        var errorMessage = 'जवाब सबमिट करने में त्रुटि हुई।';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: errorMessage,
                            confirmButtonText: 'ठीक है'
                        });
                    },
                    complete: function () {
                        // Re-enable submit button
                        $submitBtn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> जवाब भेजें');
                    }
                });
            });

            // Reset form when modal is closed
            $('#replyFeedbackModal').on('hidden.bs.modal', function () {
                $('#replyFeedbackForm')[0].reset();
                $('#feedbackSummary').html('');
            });
        });
    </script>
@endsection