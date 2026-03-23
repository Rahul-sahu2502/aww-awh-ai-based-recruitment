@extends('layouts.dahboard_layout')
@section('content')

    <div class="pagetitle">
        <h1>लंबित अनुमोदन</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/examinor/examinor-dashboard') }}">डैशबोर्ड</a></li>
                <li class="breadcrumb-item active">लंबित अनुमोदन</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-clock-history me-2"></i>
                            अनुमोदन हेतु लंबित आवेदन (21 दिन के बाद)
                        </h5>

                        @if ($applications->count() > 0)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                कुल <strong>{{ $applications->count() }}</strong> आवेदन अनुमोदन हेतु लंबित हैं।
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="pendingApprovalsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>क्र.</th>
                                            <th>आवेदनकर्ता का नाम</th>
                                            <th>पिता/पति का नाम</th>
                                            <th>परियोजना</th>
                                            <th>विज्ञापन</th>
                                            <th>पद</th>
                                            <th>जन्म तिथि</th>
                                            <th>श्रेणी</th>
                                            <th>मोबाइल</th>
                                            <th>ईमेल</th>
                                            <th>आवेदन तिथि</th>
                                            <th>समाप्ति के बाद दिन</th>
                                            <th>कार्य</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($applications as $index => $app)
                                            <tr id="row_{{ $app->apply_id }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $app->applicant_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $app->username }}</small>
                                                </td>
                                                <td>{{ $app->Father_Name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $app->project_name }}</span>
                                                </td>
                                                <td>{{ $app->advertisement_title }}</td>
                                                <td>{{ $app->post_name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($app->DOB)->format('d-m-Y') ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $app->Category ?? 'N/A' }}</span>
                                                </td>
                                                <td>{{ $app->Mobile_Number ?? 'N/A' }}</td>
                                                <td>{{ $app->Email_ID ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($app->apply_date)->format('d-m-Y') }}</td>
                                                <td>
                                                    <span class="badge bg-warning">{{ $app->days_after_expiry }} दिन</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="updateStatus({{ $app->apply_id }}, 'Verified', '{{ $app->applicant_name }}')">
                                                            <i class="bi bi-check-circle"></i> अनुमोदित करें
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="updateStatus({{ $app->apply_id }}, 'Rejected', '{{ $app->applicant_name }}')">
                                                            <i class="bi bi-x-circle"></i> अस्वीकार करें
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success text-center">
                                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                <h4 class="mt-3">कोई लंबित अनुमोदन नहीं मिला!</h4>
                                <p class="text-muted">सभी आवेदन समय पर प्रसंस्कृत हो गए हैं या कोई आवेदन 21 दिन के बाद लंबित
                                    नहीं है।</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateStatus(applyId, status, applicantName) {
            const statusText = status === 'Verified' ? 'अनुमोदित' : 'अस्वीकार';
            const statusAction = status === 'Verified' ? 'approve' : 'reject';

            Swal.fire({
                title: `आवेदन ${statusText} करें?`,
                text: `क्या आप ${applicantName} के आवेदन को ${statusText} करना चाहते हैं?`,
                icon: status === 'Verified' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: status === 'Verified' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `हाँ, ${statusText} करें`,
                cancelButtonText: 'रद्द करें',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'कृपया प्रतीक्षा करें...',
                        text: 'आवेदन की स्थिति अपडेट की जा रही है',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX request
                    fetch('/examinor/update-application-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                    'content') || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                apply_id: applyId,
                                status: status,
                                remarks: ''
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'सफल!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Remove the row from table
                                    document.getElementById(`row_${applyId}`).remove();

                                    // Check if table is empty
                                    const tbody = document.querySelector(
                                    '#pendingApprovalsTable tbody');
                                    if (tbody.children.length === 0) {
                                        location
                                    .reload(); // Reload to show the "no pending approvals" message
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'कृपया ध्यान दें ',
                                    text: data.message,
                                    icon: 'warning',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'कृपया ध्यान दें ',
                                text: 'स्थिति अपडेट करने में त्रुटि हुई। कृपया पुनः प्रयास करें।',
                                icon: 'warning',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // Initialize DataTable if applications exist
        document.addEventListener('DOMContentLoaded', function() {
            @if ($applications->count() > 0)
                const table = document.getElementById('pendingApprovalsTable');
                if (table && typeof DataTable !== 'undefined') {
                    new DataTable(table, {
                        responsive: true,
                        pageLength: 25,
                        order: [
                            [11, 'desc']
                        ], // Sort by days after expiry
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/Hindi.json'
                        }
                    });
                }
            @endif
        });
    </script>

@endsection
