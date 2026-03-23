@extends('layouts.dahboard_layout')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Modern Clean UI */
        .feedback-form-wrapper {
            background: #f8f9fa;
            min-height: calc(100vh - 100px);
            padding: 20px 0;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label .text-danger {
            font-size: 16px;
            margin-left: 2px;
        }

        /* Input Fields - Modern Style */
        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            background-color: #fff;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: #adb5bd;
        }

        /* Rating Stars - Clear and Easy to Read */
        .rating-container {
            background: #f8f9fa;
            padding: 18px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .rating-container:hover {
            border-color: #adb5bd;
            background: #fff;
        }

        .rating-stars {
            display: flex;
            gap: 12px;
            font-size: 36px;
            flex-direction: row-reverse;
            justify-content: flex-end;
            padding: 5px 0;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            cursor: pointer;
            color: #ddd;
            transition: all 0.2s ease;
            transform: scale(1);
            filter: grayscale(100%);
        }

        .rating-stars label:hover {
            color: #ffc107;
            transform: scale(1.15);
            filter: grayscale(0%);
        }

        .rating-stars input[type="radio"]:checked~label {
            color: #ffc107;
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        .rating-stars label:hover~label {
            color: #ffc107;
            filter: grayscale(0%);
        }

        .rating-stars.error {
            animation: shake 0.5s;
        }

        .rating-container.error {
            border-color: #dc3545;
            background-color: #ffe5e5;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-8px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(8px);
            }
        }

        #ratingError {
            font-size: 13px;
            color: #dc3545;
            margin-top: 8px;
            display: none;
        }

        /* File Upload - Modern Drag & Drop Style */
        .file-upload-wrapper {
            position: relative;
            width: 100%;
        }

        .file-upload-wrapper input[type=file] {
            display: none;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            background-color: #fff;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 120px;
        }

        .file-upload-label:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }

        .file-upload-label i {
            font-size: 32px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .file-upload-label .upload-text {
            color: #495057;
            font-size: 14px;
            font-weight: 500;
        }

        .file-upload-label .upload-hint {
            color: #6c757d;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Image Preview Grid */
        #imagePreviewContainer {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        #imagePreviewContainer img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        /* Character Counter */
        .char-counter {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }

        /* Buttons - Modern Style */
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 10px 24px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Card Header - Project Style */
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #e9ecef;
            padding: 15px 20px;
            font-weight: 600;
            color: #212529;
        }

        .card-header i {
            color: #0d6efd;
            margin-right: 8px;
        }

        /* Recent Feedbacks */
        .feedback-item {
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .feedback-item:hover {
            border-color: #0d6efd;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
        }

        .feedback-type-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        /* Loading Spinner */
        .spinner-border-sm {
            width: 18px;
            height: 18px;
            border-width: 2px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .rating-stars {
                font-size: 24px;
            }

            #imagePreviewContainer {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">
                <i class="bi bi-question-circle-fill text-primary me-2"></i>
                सहायता एवं सुझाव
            </h5>

            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">सहायता एवं सुझाव</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-chat-square-quote-fill"></i> अपनी राय दें / Share Your Feedback</h5>
                    </div>

                    <div class="card-body" style="padding: 25px;">
                        <form id="feedbackForm" enctype="multipart/form-data">
                            @csrf

                            <!-- Feedback Type -->
                            <div class="mb-3">
                                <label for="feedback_type" class="form-label">
                                    प्रतिक्रिया का प्रकार / Feedback Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="feedback_type" name="feedback_type" required>
                                    <option value="">प्रकार चुनें / Select Type</option>
                                    <option value="suggestion">सुझाव / Suggestion</option>
                                    <option value="bug_report">बग रिपोर्ट / Bug Report</option>
                                    <option value="feature_request">नई सुविधा / Feature Request</option>
                                    <option value="complaint">शिकायत / Complaint</option>
                                    <option value="appreciation">प्रशंसा / Appreciation</option>
                                    <option value="other">अन्य / Other</option>
                                </select>
                            </div>

                            <!-- Rating -->
                            {{-- <div class="mb-3">
                                <label class="form-label">
                                    रेटिंग / Rating
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="rating-container" id="ratingContainer">
                                    <div class="rating-stars" id="ratingStars">
                                        <input type="radio" id="star5" name="rating" value="5">
                                        <label for="star5" title="Excellent - 5 stars">⭐</label>

                                        <input type="radio" id="star4" name="rating" value="4">
                                        <label for="star4" title="Good - 4 stars">⭐</label>

                                        <input type="radio" id="star3" name="rating" value="3">
                                        <label for="star3" title="Average - 3 stars">⭐</label>

                                        <input type="radio" id="star2" name="rating" value="2">
                                        <label for="star2" title="Below Average - 2 stars">⭐</label>

                                        <input type="radio" id="star1" name="rating" value="1">
                                        <label for="star1" title="Poor - 1 star">⭐</label>
                                    </div>
                                    <div id="ratingError" class="text-danger" style="display: none;">
                                        कृपया रेटिंग दें / Please select a rating
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">
                                    विषय / Subject
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="संक्षिप्त विषय लिखें / Enter brief subject" maxlength="200" required>
                                <div class="char-counter">
                                    <span id="subjectCounter">0</span>/200
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="mb-3">
                                <label for="feedback_message" class="form-label">
                                    विस्तृत संदेश / Detailed Message
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="feedback_message" name="feedback_message" rows="5"
                                    placeholder="अपनी प्रतिक्रिया विस्तार से लिखें / Write your feedback in detail"
                                    maxlength="1000" required></textarea>
                                <div class="char-counter">
                                    <span id="messageCounter">0</span>/1000
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-3">
                                <label class="form-label">
                                    छवि अपलोड करें / Upload Images
                                    <span class="text-muted">(वैकल्पिक / Optional, अधिकतम 5 / Max 5)</span>
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="feedback_images" name="feedback_images[]"
                                        accept="image/jpeg,image/png,image/gif" multiple>
                                    <label for="feedback_images" class="file-upload-label">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div class="upload-text">छवि अपलोड करने के लिए क्लिक करें</div>
                                        <div class="upload-hint">JPEG, PNG, GIF (अधिकतम 2MB प्रति छवि)</div>
                                    </label>
                                </div>
                                <div id="imagePreviewContainer"></div>
                            </div>

                            <!-- Contact Email -->
                            <div class="mb-4">
                                <label for="contact_email" class="form-label">
                                    संपर्क ईमेल / Contact Email
                                    <span class="text-muted">(वैकल्पिक / Optional)</span>
                                </label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email"
                                    placeholder="your.email@example.com">
                            </div>

                            <!-- Submit Button -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-fill"></i> फीडबैक सबमिट करें / Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Feedbacks -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-clock-history"></i> हाल की फीडबैक / Recent Feedbacks</h5>
                    </div>
                    <div class="card-body" id="recentFeedbacksContainer">
                        <div class="text-center text-muted py-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">लोड हो रहा है... / Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Hide rating error on star selection
            $('input[name="rating"]').on('change', function () {
                $('#ratingError').hide();
                $('#ratingContainer').removeClass('error');
                $('#ratingStars').removeClass('error');
            });

            // Character counter for subject
            $('#subject').on('input', function () {
                const length = $(this).val().length;
                $('#subjectCounter').text(length);
            });

            // Character counter for message
            $('#feedback_message').on('input', function () {
                const length = $(this).val().length;
                $('#messageCounter').text(length);
            });

            // Image preview with modern grid
            $('#feedback_images').on('change', function (e) {
                const files = e.target.files;
                const previewContainer = $('#imagePreviewContainer');
                previewContainer.empty();

                if (files.length > 5) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'बहुत सारी छवियाँ / Too Many Images',
                        text: 'अधिकतम 5 छवियों की अनुमति है / Maximum 5 images allowed',
                        confirmButtonText: 'ठीक है / OK'
                    });
                    this.value = '';
                    return;
                }

                Array.from(files).forEach((file, index) => {
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'फ़ाइल बहुत बड़ी है / File Too Large',
                            text: `छवि ${index + 1} 2MB से बड़ी है / Image ${index + 1} exceeds 2MB`,
                            confirmButtonText: 'ठीक है / OK'
                        });
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = $('<img>')
                            .attr('src', e.target.result);
                        previewContainer.append(img);
                    };
                    reader.readAsDataURL(file);
                });
            });

            // Form submission
            $('#feedbackForm').on('submit', function (e) {
                e.preventDefault();

                // Validate rating is selected
                const ratingSelected = $('input[name="rating"]:checked').length > 0;

                // if (!ratingSelected) {
                //     // Show error message
                //     $('#ratingError').show();

                //     // Add error class to container
                //     $('#ratingContainer').addClass('error');
                //     $('#ratingStars').addClass('error');

                //     // Scroll to rating section
                //     $('html, body').animate({
                //         scrollTop: $('#ratingContainer').offset().top - 100
                //     }, 500);

                //     // Show SweetAlert
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'रेटिंग आवश्यक है / Rating Required',
                //         text: 'कृपया रेटिंग दें / Please provide a rating',
                //         confirmButtonText: 'ठीक है / OK'
                //     });

                //     // Remove error class after 3 seconds
                //     setTimeout(function () {
                //         $('#ratingContainer').removeClass('error');
                //         $('#ratingStars').removeClass('error');
                //     }, 3000);

                //     return false;
                // }

                // // Hide error if rating is selected
                // $('#ratingError').hide();
                // $('#ratingContainer').removeClass('error');
                // $('#ratingStars').removeClass('error');

                const formData = new FormData(this);

                Swal.fire({
                    title: 'सबमिट हो रहा है... / Submitting...',
                    text: 'कृपया प्रतीक्षा करें / Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ url('/candidate/feedback/submit') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.close();

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'सफलता! / Success!',
                                text: response.message,
                                confirmButtonText: 'ठीक है / OK'
                            }).then(() => {
                                // Reset form
                                $('#feedbackForm')[0].reset();
                                $('#imagePreviewContainer').empty();
                                $('#subjectCounter').text('0');
                                $('#messageCounter').text('0');
                                $('.rating-stars label').css({
                                    'color': '#ddd',
                                    'filter': 'grayscale(100%)',
                                    'transform': 'scale(1)'
                                });

                                // Reload recent feedbacks
                                loadRecentFeedbacks();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'त्रुटि! / Error!',
                                text: response.message || 'कुछ गलत हो गया / Something went wrong',
                                confirmButtonText: 'ठीक है / OK'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.close();

                        // ✅ अगर 429 Too Many Requests है (हमारी टाइम लिमिट एरर)
                        if (xhr.status === 429 && xhr.responseJSON?.message) {
                            Swal.fire({
                                icon: 'info',
                                title: 'प्रतीक्षा आवश्यक / Wait Required',
                                html: xhr.responseJSON.message, // सीधे बैकएंड से आया मैसेज
                                confirmButtonText: 'ठीक है / OK'
                            });
                            return;
                        }

                        // ❌ अन्य एरर्स (वैलिडेशन, सर्वर एरर आदि)
                        let errorMessage = 'प्रतिक्रिया सबमिट करने में विफल / Failed to submit feedback';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed / सबमिशन विफल',
                            text: errorMessage,
                        });
                    }
                });
            });

            // Load recent feedbacks (without status badges)
            function loadRecentFeedbacks() {
                $.ajax({
                    url: "{{ url('/candidate/feedback/recent') }}",
                    type: 'GET',
                    success: function (response) {
                        if (response.status === 'success' && response.data.length > 0) {
                            let html = '';

                            response.data.forEach(feedback => {
                                const stars = '⭐'.repeat(feedback.rating);

                                // Image count badge
                                const imageBadge = feedback.image_count > 0 ?
                                    `<span class="badge bg-info text-white ms-2"><i class="bi bi-images"></i> ${feedback.image_count}</span>` :
                                    '';

                                html += `
                                                                                            <div class="feedback-item">
                                                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                                    <h6 class="mb-0 fw-bold">${feedback.subject}</h6>
                                                                                                    <small class="text-muted">${feedback.created_at}</small>
                                                                                                </div>
                                                                                                <p class="mb-2 text-muted" style="font-size: 14px;">${feedback.feedback_message.substring(0, 120)}...</p>
                                                                                                <div class="d-flex align-items-center gap-2">
                                                                                                    <span style="font-size: 16px;">${stars}</span>
                                                                                                    <span class="badge feedback-type-badge bg-secondary">${feedback.feedback_type}</span>
                                                                                                    ${imageBadge}
                                                                                                </div>
                                                                                            </div>
                                                                                        `;
                            });

                            $('#recentFeedbacksContainer').html(html);
                        } else {
                            $('#recentFeedbacksContainer').html(
                                '<div class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size: 48px;"></i><p class="mt-2">अभी तक कोई प्रतिक्रिया नहीं / No feedbacks yet</p></div>'
                            );
                        }
                    },
                    error: function () {
                        $('#recentFeedbacksContainer').html(
                            '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle" style="font-size: 48px;"></i><p class="mt-2">प्रतिक्रियाएं लोड करने में विफल / Failed to load feedbacks</p></div>'
                        );
                    }
                });
            }

            // Load recent feedbacks on page load
            loadRecentFeedbacks();
        });
    </script>
@endsection