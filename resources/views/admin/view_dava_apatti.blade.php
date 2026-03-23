@extends('layouts.dahboard_layout')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .claim-detail-table th {
            width: 300px;
            background-color: #f8f9fa;
            font-weight: 600;
            vertical-align: middle;
        }

        .claim-detail-table td {
            vertical-align: middle;
        }

        .claim-description {
            white-space: pre-line;
            line-height: 1.6;
        }

        .action-section .btn {
            min-width: 160px;
        }

        @media (max-width: 768px) {
            .feature-section {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                /* Smooth scrolling for iOS */
            }

            .feature-section table {
                width: 100%;
                min-width: 600px;
                /* Adjust based on table content width */
            }
        }
    </style>
@endsection
@section('body-page')
    <main id="main" class="main">
        <div class="row printable-div">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card shadow-sm border-0 mb-4" id="printable-div">
                    {{-- <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">दावा–आपत्ति आवेदन जानकारी</h5>

                        <span class="badge bg-light text-primary fs-6">
                            {{ $claim_details->claim_status }}
                        </span>
                    </div> --}}
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-card-list"> </i>दावा–आपत्ति आवेदन जानकारी
                            </h5>

                        </div>
                    </div>

                    <div class="card-body p-4">

                        <table class="table table-bordered align-middle claim-detail-table">

                            <tbody>
                                <tr>
                                    <th>आवेदन संख्या</th>
                                    <td>{{ $claim_details->application_num ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>आवेदित पद</th>
                                    <td>{{ $claim_details->title ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>आवेदन तिथि</th>
                                    <td>
                                        {{ $claim_details->created_at ? date('d-m-Y', strtotime($claim_details->created_at)) : '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>आवेदनकर्ता का नाम</th>
                                    <td>{{ $claim_details->full_name ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>पिता का नाम</th>
                                    <td>{{ $claim_details->FatherName ?? '-' }}</td>
                                </tr>

                                {{-- <tr>
                                    <th>मोबाइल नंबर</th>
                                    <td>{{ $claim_details->Contact_Number ?? '-' }}</td>
                                </tr> --}}

                                <tr>
                                    <th>दावा / आपत्ति प्रकार</th>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $claim_details->request_type === 'claim' ? 'दावा (Claim)' : 'आपत्ति (Objection)' }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th>विषय</th>
                                    <td>{{ ucfirst($claim_details->request_category) }}</td>
                                </tr>

                                <tr>
                                    <th>विवरण</th>
                                    <td class="claim-description">
                                        {{ $claim_details->description ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>स्थिति</th>
                                    <td class="claim-status">
                                        @php
                                            // Badge color mapping
                                            $badgeClass = match ($claim_details->claim_status ?? null) {
                                                'Submitted' => 'bg-warning',
                                                'InReview' => 'bg-info',
                                                'Resolved' => 'bg-primary',
                                                'Approved' => 'bg-success',
                                                'Rejected' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };

                                            // Hindi status text
                                            $statusHindi = match ($claim_details->claim_status ?? null) {
                                                'Submitted' => 'प्रतीक्षारत',
                                                'InReview' => 'समीक्षा में',
                                                'Resolved' => 'हल किया',
                                                'Approved' => 'स्वीकृत',
                                                'Rejected' => 'अस्वीकृत',
                                                default => '-',
                                            };
                                        @endphp

                                        <span class="badge {{ $badgeClass }} ">
                                            {{ $statusHindi }}
                                        </span>
                                    </td>

                                </tr>

                            </tbody>
                        </table>

                        <!-- Action Section -->


                        @if ($claim_details->claim_status === 'Submitted')
                            <div class="action-section border-top pt-3 mt-4 d-flex gap-2 flex-wrap">

                                <a
                                    href="/admin/final-application-detail/{{ MD5($claim_details->apply_id) }}/{{ MD5($claim_details->fk_applicant_id) }}">
                                    <button class="btn btn-info btn-sm text-white">
                                        विवरण देखें
                                    </button>
                                </a>
                                <button class="btn btn-success btn-sm approve-claim"
                                    data-id="{{ $claim_details->claim_id }}" data-status="Approved">
                                    स्वीकृत करें
                                </button>

                                <button class="btn btn-danger btn-sm reject-claim" data-id="{{ $claim_details->claim_id }}"
                                    data-status="Rejected">
                                    अस्वीकृत करें
                                </button>
                                <a href="{{ route('admin.dava_apatti_list') }}" class="btn btn-primary btn-sm">
                                    वापस जाएँ
                                </a>
                            </div>
                        @else
                            <!-- Reason Section -->
                            <div class="alert alert-info mt-4">
                                <h6 class="mb-1 fw-semibold">समिति बैठक का दिनांक :
                                    {{ date('d-m-Y', strtotime($claim_details->meeting_date)) ?? 'कोई टिप्पणी उपलब्ध नहीं है।' }}
                                </h6>
                                <br>
                                <h6 class="mb-1 fw-semibold">दावा–आपत्ति टिप्पणी :</h6>
                                <p class="mb-0">
                                    {{ $claim_details->admin_remark ?? 'कोई टिप्पणी उपलब्ध नहीं है।' }}
                                </p>
                            </div>


                            <div class="action-section border-top pt-3 mt-4 d-flex gap-2 flex-wrap">
                                {{-- @if ($claim_details->claim_status === 'Approved')
                                    @if ($claim_details->request_category === 'marks')
                                        <a class="btn btn-info btn-sm marks-claim  m-1 ps-2 pe-2"
                                            data-id="{{ $claim_details->apply_id }}" data-status="InReview">
                                            अंकों की प्रविष्टि अपडेट करें
                                        </a>
                                    @endif
                                @endif --}}
                                <a href="/admin/final-application-detail/{{ MD5($claim_details->apply_id) }}/{{ MD5($claim_details->fk_applicant_id) }}"
                                    class="btn btn-info btn-sm text-white  m-1 ps-2 pe-2">
                                    विवरण देखें
                                </a>
                                <a href="{{ route('admin.dava_apatti_list') }}" class="btn btn-primary m-1 ps-2 pe-2">
                                    वापस जाएँ
                                </a>

                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </main><!-- End #main -->
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).on('click', '.approve-claim, .reject-claim, .marks-claim', function() {

            let button = $(this);
            let claimId = button.data('id');
            let status = button.data('status');

            $('.approve-claim, .reject-claim, .marks-claim').prop('disabled', true);

            if (status === 'InReview') {

                Swal.fire({
                    title: 'अंक अपडेट',
                    text: 'क्या आप आवेदनकर्ता का अंक (marks) अपडेट करना चाहते हैं?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'हाँ',
                    cancelButtonText: 'रद्द करें'
                }).then((result) => {

                    if (result.isConfirmed) {
                        window.location.href = "{{ url('admin/marks-entry') }}/" + claimId;
                    } else {
                        $('.approve-claim, .reject-claim, .marks-claim').prop('disabled', false);
                    }
                });

                return;
            }

            /* APPROVED / REJECTED */

            let titleText = status === 'Approved' ?
                'दावा–आपत्ति स्वीकृत करें' :
                'दावा–आपत्ति अस्वीकृत करें';

            let confirmText = status === 'Approved' ?
                'स्वीकृत करें' :
                'अस्वीकृत करें';

            // Swal.fire({
            //     title: titleText,
            //     input: 'textarea',
            //     inputLabel: 'कारण / टिप्पणी',
            //     inputPlaceholder: 'कृपया कारण लिखें...',
            //     inputAttributes: {
            //         maxlength: 500
            //     },
            //     showCancelButton: true,
            //     confirmButtonText: confirmText,
            //     cancelButtonText: 'रद्द करें',
            //     preConfirm: (remark) => {
            //         if (!remark || remark.trim() === '') {
            //             Swal.showValidationMessage('कारण / टिप्पणी अनिवार्य है');
            //             return false;
            //         }
            //         return remark;
            //     }
            // }).then((result) => {

            //     if (!result.isConfirmed) {
            //         $('.approve-claim, .reject-claim, .marks-claim').prop('disabled', false);
            //         return;
            //     }

            //     $.ajax({
            //         url: "{{ route('admin.updateClaimStatus') }}",
            //         type: "POST",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             claim_id: claimId,
            //             status: status,
            //             admin_remark: result.value
            //         },
            //         success: function(response) {

            //             Swal.fire({
            //                 icon: 'success',
            //                 title: 'सफल',
            //                 text: response.message,
            //                 confirmButtonText: 'ठीक है'
            //             }).then(() => {
            //                 location.reload();
            //             });
            //         },
            //         error: function() {

            //             Swal.fire({
            //                 icon: 'error',
            //                 title: 'त्रुटि',
            //                 text: 'स्थिति अपडेट नहीं हो सकी।',
            //                 confirmButtonText: 'ठीक है'
            //             });

            //             $('.approve-claim, .reject-claim, .marks-claim').prop('disabled',
            //                 false);
            //         }
            //     });
            // });
            // #####################

            Swal.fire({
                title: titleText,
                html: `
                        <div class="container-fluid p-0">
                            <div class=" border-0">
                                <div class="p-4">
                                    
                                    <!-- Remark Section -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold mb-2 d-block text-start">कारण / टिप्पणी <span class="text-danger">*</span></label>
                                        <textarea 
                                            id="remark" 
                                            class="form-control" 
                                            placeholder="कृपया कारण लिखें..." 
                                            maxlength="500" 
                                            rows="3" 
                                            required
                                            style="resize: none;"
                                        ></textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">अधिकतम 500 अक्षर</small>
                                            <small class="text-muted"><span id="charCount" class="fw-medium">0</span>/500</small>
                                        </div>
                                    </div>

                                    <!-- Meeting Held Radio -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold mb-2 d-block text-start">क्या दावा–आपत्ति समिति की बैठक हुई थी  ?<span class="text-danger">*</span></label>
                                        <div class="d-flex gap-4 mt-2">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="meetingHeld" 
                                                    id="meetingHeldYes" 
                                                    value="1" 
                                                    required
                                                >
                                                <label class="form-check-label" for="meetingHeldYes">हाँ</label>
                                            </div>
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="meetingHeld" 
                                                    id="meetingHeldNo" 
                                                    value="0" 
                                                    required
                                                >
                                                <label class="form-check-label" for="meetingHeldNo">नहीं</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Meeting Date -->
                                    <div id="meetingDateContainer" class="mb-4" style="display: none;">
                                        <label class="form-label fw-semibold mb-2 d-block text-start">बैठक का दिनांक <span class="text-danger">*</span></label>
                                        <input 
                                            type="date" 
                                            id="meetingDate" 
                                            class="form-control" 
                                            max="${new Date().toISOString().split('T')[0]}" 
                                            required
                                        >
                                    {{-- <small class="text-muted">केवल पिछला दिनांक चुनें</small>--}}
                                    </div>

                                    <!-- File Upload -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold mb-2 d-block text-start">फाइल अपलोड <small class="text-muted">(वैकल्पिक)</small></label>
                                        <input 
                                            type="file" 
                                            id="meetingFile" 
                                            class="form-control" 
                                            accept=".pdf"
                                        >
                                        <small class="d-block text-start mt-2" style="font-size:14px">केवल PDF, अधिकतम 2MB</small>
                                        <div id="fileError" class="text-danger small mt-1" style="display: none;"></div>
                                        <div id="fileInfo" class="text-success small mt-1" style="display: none;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    `,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'रद्द करें',
                width: '600px',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    // Character counter for remark
                    const remarkTextarea = document.getElementById('remark');
                    const charCount = document.getElementById('charCount');

                    if (remarkTextarea && charCount) {
                        remarkTextarea.addEventListener('input', function() {
                            charCount.textContent = this.value.length;
                        });
                    }

                    // Toggle meeting date based on radio buttons
                    const meetingDateContainer = document.getElementById('meetingDateContainer');
                    const meetingDateInput = document.getElementById('meetingDate');

                    document.querySelectorAll('input[name="meetingHeld"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (this.value === '1') {
                                if (meetingDateContainer) meetingDateContainer.style.display = 'block';
                                if (meetingDateInput) meetingDateInput.required = true;
                            } else {
                                if (meetingDateContainer) meetingDateContainer.style.display = 'none';
                                if (meetingDateInput) {
                                    meetingDateInput.required = false;
                                    meetingDateInput.value = '';
                                }
                            }
                        });
                    });

                    // File validation
                    const fileInput = document.getElementById('meetingFile');
                    const fileError = document.getElementById('fileError');
                    const fileInfo = document.getElementById('fileInfo');

                    if (fileInput) {
                        fileInput.addEventListener('change', function() {
                            if (this.files && this.files[0]) {
                                const file = this.files[0];
                                const maxSize = 2 * 1024 * 1024; // 2MB

                                // Hide previous messages
                                if (fileError) fileError.style.display = 'none';
                                if (fileInfo) fileInfo.style.display = 'none';

                                // Check file type
                                if (file.type !== 'application/pdf') {
                                    if (fileError) {
                                        fileError.textContent = 'केवल PDF फाइल अपलोड करें';
                                        fileError.style.display = 'block';
                                    }
                                    this.value = ''; // Clear the file input
                                    return;
                                }

                                // Check file size
                                if (file.size > maxSize) {
                                    if (fileError) {
                                        fileError.textContent = 'फाइल साइज 2MB से कम होनी चाहिए';
                                        fileError.style.display = 'block';
                                    }
                                    this.value = ''; // Clear the file input
                                    return;
                                }

                                // Show file info
                                if (fileInfo) {
                                    fileInfo.textContent = `फाइल: ${file.name} (${formatBytes(file.size)})`;
                                    fileInfo.style.display = 'block';
                                }
                            } else {
                                if (fileInfo) fileInfo.style.display = 'none';
                            }
                        });
                    }

                    // Helper function to format file size
                    function formatBytes(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                    }
                },
                preConfirm: () => {
                    // Get all form values safely
                    const remarkEl = document.getElementById('remark');
                    const remark = remarkEl ? remarkEl.value.trim() : '';

                    // Check radio selection for meeting held
                    const selectedMeetingRadio = document.querySelector('input[name="meetingHeld"]:checked');

                    // Validation
                    if (!remark) {
                        Swal.showValidationMessage('कारण / टिप्पणी अनिवार्य है');
                        return false;
                    }

                    if (!selectedMeetingRadio) {
                        Swal.showValidationMessage('कृपया बताएं कि समिति की बैठक हुई थी या नहीं');
                        return false;
                    }

                    const meetingHeld = selectedMeetingRadio.value === '1' ? 1 : 0;
                    const meetingDate = meetingHeld ? (document.getElementById('meetingDate') ? document.getElementById('meetingDate').value : '') : null;

                    // If meeting was held, date is required
                    if (meetingHeld && !meetingDate) {
                        Swal.showValidationMessage('समिति की बैठक का दिनांक अनिवार्य है');
                        return false;
                    }

                    if (meetingDate) {
                        const selectedDate = new Date(meetingDate);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        if (selectedDate > today) {
                            Swal.showValidationMessage('भविष्य का दिनांक नहीं चुन सकते');
                            return false;
                        }
                    }

                    // File validation if uploaded
                    const fileInput = document.getElementById('meetingFile');
                    const file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

                    if (file) {
                        if (file.type !== 'application/pdf') {
                            Swal.showValidationMessage('केवल PDF फाइल अपलोड करें');
                            return false;
                        }

                        const maxSize = 2 * 1024 * 1024;
                        if (file.size > maxSize) {
                            Swal.showValidationMessage('फाइल साइज 2MB से कम होनी चाहिए');
                            return false;
                        }
                    }

                    // Prepare form data for AJAX
                    const formData = new FormData();
                    formData.append('remark', remark);
                    formData.append('meeting_held', meetingHeld);
                    if (meetingDate) {
                        formData.append('meeting_date', meetingDate);
                    }
                    if (file) {
                        formData.append('meeting_file', file);
                    }

                    return formData;
                }
            }).then((result) => {
                if (!result.isConfirmed) {
                    $('.approve-claim, .reject-claim, .marks-claim').prop('disabled', false);
                    return;
                }

                // Create a new FormData object for AJAX request
                const formData = new FormData();
                const data = result.value; // This is already a FormData object from preConfirm

                // Add other required data
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('claim_id', claimId);
                formData.append('status', status);

                // Append data from preConfirm FormData
                formData.append('admin_remark', data.get('remark'));
                formData.append('meeting_held', data.get('meeting_held'));
                if (data.get('meeting_date')) {
                    formData.append('meeting_date', data.get('meeting_date'));
                }
                if (data.get('meeting_file')) {
                    formData.append('meeting_file', data.get('meeting_file'));
                }

                $.ajax({
                    url: "{{ route('admin.updateClaimStatus') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'सफल',
                            text: response.message,
                            confirmButtonText: 'ठीक है'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'स्थिति अपडेट नहीं हो सकी।';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join(', ');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'त्रुटि',
                            text: errorMessage,
                            confirmButtonText: 'ठीक है'
                        });

                        $('.approve-claim, .reject-claim, .marks-claim').prop('disabled',
                            false);
                    }
                });
            });
            // #####################


        });
    </script>
@endsection
