@extends('layouts.dahboard_layout')
@section('styles')
<style></style>
@endsection
@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">
                <i class="bi bi-exclamation-octagon-fill text-primary me-2"></i>
                दावा आपत्ति
            </h5>

            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('candidate/davaApatti') }}">होम</a></li>
                    <li class="breadcrumb-item active">दावा आपत्ति</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            दावा–आपत्ति दर्ज करें / Claim–Objection Submission
                        </h5>
                    </div>

                    <div class="card-body" style="padding: 25px;">
                        <form id="claimObjectionForm" enctype="multipart/form-data">
                            @csrf

                            <!-- Application Number -->
                            {{-- <div class="mb-3">
                                <label class="form-label">
                                    आवेदन क्रमांक
                                </label>
                                <input type="text" class="form-control" name="dava_aaptti_no"
                                    value="{{ $application_no ?? '' }}" readonly>
                            </div> --}}

                            <!-- Subject -->
                            <div class="mb-3">
                                <label class="form-label">
                                    पोस्ट /आवेदन संख्या का चयन करें
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="fk_apply_id" required>
                                    <option value="">-- चयन करें --</option>

                                    @foreach ($applicant_apply_list as $item)
                                        <option value="{{ $item->apply_id }}">
                                            {{ $item->title }} (आवेदन संख्या : {{ $item->application_num }})
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                            <!-- Row start -->
                            <div class="row">
                                <!-- Claim / Objection Type -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">
                                        दावा–आपत्ति का प्रकार
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="request_type" required>
                                        <option value="">-- चयन करें --</option>
                                        <option value="claim">दावा (Claim)</option>
                                        <option value="objection">आपत्ति (Objection)</option>
                                    </select>
                                </div>

                                <!-- Category -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">
                                        विषय
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="request_category" required>
                                        <option value="">-- चयन करें --</option>
                                        <option value="merit">मेरिट सूची</option>
                                        <option value="marks">अंक गणना</option>
                                        <option value="eligibility">पात्रता / योग्यता</option>
                                        <option value="experience">अनुभव अंक</option>
                                        <option value="documents">दस्तावेज़ सत्यापन</option>
                                        <option value="other">अन्य</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Row end -->



                            <!-- Detailed Description -->
                            <div class="mb-3">
                                <label class="form-label">
                                    विस्तृत विवरण
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="description" rows="5" maxlength="1500" required
                                    placeholder="अपने दावा/आपत्ति का स्पष्ट विवरण लिखें"></textarea>
                            </div>

                            <!-- Declaration -->
                            <div class="form-check mb-4">
                                <input class="form-check-input border border-primary" type="checkbox" required>
                                <label class="form-check-label">
                                    मैं घोषणा करता/करती हूँ कि उपरोक्त दी गई जानकारी सत्य है।
                                </label>
                            </div>

                            <!-- Submit -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-fill"></i>
                                    दावा–आपत्ति सबमिट करें
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
                        <h5><i class="bi bi-clock-history"></i> दावा आपत्ति जानकारी </h5>
                    </div>
                    <div class="card-body" id="recentFeedbacksContainer">
                        <div class="table-responsive">
                            <table class="table table-hover" id="claimObjectionTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>आवेदन संख्या</th>
                                        <th>पद का नाम</th>
                                        <th>प्रकार</th>
                                        <th>विषय</th>
                                        <th>स्थिति</th>
                                        <th>तिथि</th>
                                        {{-- <th>क्रिया</th> --}}
                                    </tr>
                                </thead>
                                <tbody id="claimObjectionTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0">लोड हो रहा है... / Loading...</p>
                                        </td>
                                    </tr>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.18.0/js/md5.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        $(document).ready(function() {
            // Load claim objection data
            loadClaimObjectionData();

            function loadClaimObjectionData() {
                $.ajax({
                    url: "{{ route('candidate.claimObjection.list') }}",
                    type: "GET",
                    success: function(response) {
                        if (response.status === 'success') {
                            displayClaimObjectionData(response.data);
                        }
                    },
                    error: function(xhr) {
                        $('#claimObjectionTableBody').html(`
                            <tr>
                                <td colspan="8" class="text-center text-danger">
                                    <i class="bi bi-exclamation-circle"></i> डेटा लोड करने में त्रुटि
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            function displayClaimObjectionData(data) {
                let html = '';

                if (data.length === 0) {
                    html = `
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> कोई दावा आपत्ति नहीं मिली
                            </td>
                        </tr>
                    `;
                } else {
                    data.forEach((item, index) => {
                        const requestType = item.request_type === 'claim' ? 'दावा' : 'आपत्ति';
                        const status = getStatusBadge(item.claim_status);
                        const date = new Date(item.created_at);
                        const createdDate = new Intl.DateTimeFormat('hi-IN').format(date).replace(/\//g, '-');

                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><strong>${item.application_num || 'N/A'}</strong></td>
                                <td>${item.post_title || 'N/A'}</td>
                                <td>${requestType}</td>
                                <td>${getCategoryLabel(item.request_category)}</td>
                                <td>${status}</td>
                                <td>${createdDate}</td>
                                </tr>
                        `;
                        // <td>
                        //     <button class="btn btn-sm btn-info" onclick="viewClaimDetails(${item.claim_id})">
                        //         <i class="bi bi-eye"></i> देखें
                        //     </button>
                        // </td>
                    });
                }

                $('#claimObjectionTableBody').html(html);
            }

            function getStatusBadge(status) {
                const statusMap = {
                    'Submitted': '<span class="badge bg-warning ">प्रतीक्षारत</span>',
                    'InReview': '<span class="badge bg-info">समीक्षा में</span>',
                    'Resolved': '<span class="badge bg-success">हल किया</span>',
                    'Approved': '<span class="badge bg-success">स्वीकृत</span>',
                    'Rejected': '<span class="badge bg-danger">अस्वीकृत</span>'
                };
                return statusMap[status] || status;
            }

            function getCategoryLabel(category) {
                const categoryMap = {
                    'merit': 'मेरिट सूची',
                    'marks': 'अंक गणना',
                    'eligibility': 'पात्रता / योग्यता',
                    'experience': 'अनुभव अंक',
                    'documents': 'दस्तावेज़ सत्यापन',
                    'other': 'अन्य'
                };
                return categoryMap[category] || category;
            }

            // window.viewClaimDetails = function(claimId) {
            //     Swal.fire({
            //         title: 'दावा आपत्ति विवरण',
            //         text: 'विस्तृत जानकारी जल्द दिखाई जाएगी',
            //         icon: 'info',
            //         confirmButtonText: 'ठीक है'
            //     });
            // };

            $('#claimObjectionForm').on('submit', function(e) {
                e.preventDefault();

                // Declaration checkbox mandatory
                if (!$('#claimObjectionForm input[type="checkbox"]').is(':checked')) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'घोषणा आवश्यक',
                        text: 'कृपया घोषणा को स्वीकार करें।',
                        confirmButtonText: 'ठीक है'
                    });

                    return;
                }


                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('candidate.DavaApatti.submit') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'सफलता',
                                text: response.message,
                                confirmButtonText: 'ठीक है'
                            }).then(() => {
                                $('#claimObjectionForm')[0].reset();
                                loadClaimObjectionData(); // Reload data after submission
                            });

                        } else {

                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया ध्यान दें!',
                                text: response.message,
                                confirmButtonText: 'ठीक है'
                            });
                        }

                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {

                            let errors = xhr.responseJSON.errors;
                            let firstError = Object.values(errors)[0][0];

                            Swal.fire({
                                icon: 'warning',
                                title: 'मान्यता त्रुटि',
                                text: firstError,
                                confirmButtonText: 'ठीक है'
                            });

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'सर्वर त्रुटि',
                                text: 'Server error, पुनः प्रयास करें।',
                                confirmButtonText: 'ठीक है'
                            });
                        }

                    }
                });
            });

        });
    </script>
@endsection
