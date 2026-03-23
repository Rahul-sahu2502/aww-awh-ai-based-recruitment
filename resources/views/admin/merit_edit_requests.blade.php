@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">मेरिट एडिट रिक्वेस्ट</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">एडिट रिक्वेस्ट</li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pending Requests</h5>
                <button id="approveSelected" class="btn btn-success">चयनित अप्रूव करें</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllRequests"></th>
                                <th>आवेदक</th>
                                <th>पिता का नाम</th>
                                <th>पद</th>
                                <th>रिक्वेस्ट करने वाला</th>
                                <th>परियोजना</th>
                                <th>ग्राम पंचायत</th>
                                <th>ग्राम</th>
                                <th>नगर निकाय</th>
                                <th>वार्ड</th>
                                <th>कुल रिक्वेस्ट</th>
                                <th>कुल अप्रूवल</th>
                                <th>औसत अप्रूवल (मिन)</th>
                                <th>रिक्वेस्ट समय</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($query as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="request-checkbox" value="{{ $row->id }}"
                                            data-applicant-name="{{ $row->applicant_name }}">
                                    </td>
                                    <td>{{ $row->applicant_name }}</td>
                                    <td>{{ $row->FatherName }}</td>
                                    <td>{{ $row->post_name }}</td>
                                    <td>{{ $row->requested_by_name ?? '-' }}</td>
                                    <td>{{ $row->project_name ?? '-' }}</td>
                                    <td>{{ $row->gp_name ?? '-' }}</td>
                                    <td>{{ $row->village_name ?? '-' }}</td>
                                    <td>{{ $row->nagar_name ?? '-' }}</td>
                                    <td>
                                        @if (!empty($row->ward_name))
                                            {{ $row->ward_name }}
                                            @if (!empty($row->ward_no))
                                                ({{ $row->ward_no }})
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $row->request_count ?? 1 }}</td>
                                    <td>{{ $row->approve_count ?? 0 }}</td>
                                    <td>{{ $row->avg_approval_minutes !== null ? (int) $row->avg_approval_minutes : '-' }}</td>
                                    <td>{{ $row->requested_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">नोट: यहाँ marks नहीं दिखेंगे।</small>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();

            $('#selectAllRequests').on('change', function() {
                $('.request-checkbox').prop('checked', this.checked);
            });

            $('#approveSelected').on('click', function() {
                const selectedRows = $('.request-checkbox:checked');
                const ids = selectedRows.map(function() {
                    return $(this).val();
                }).get();

                if (!ids.length) {
                    Swal.fire({
                        icon: 'info',
                        title: 'कम से कम एक रिक्वेस्ट चुनें',
                        allowOutsideClick: false
                    });
                    return;
                }

                const names = selectedRows.map(function() {
                    return $(this).data('applicant-name');
                }).get().filter(Boolean);

                const namePreview = names.slice(0, 5).join(', ');
                const moreCount = names.length > 5 ? (names.length - 5) : 0;
                const confirmText = names.length
                    ? `आवेदक: ${namePreview}${moreCount ? ' और ' + moreCount + ' अन्य' : ''}`
                    : 'चयनित रिक्वेस्ट अप्रूव करें?';

                Swal.fire({
                    icon: 'question',
                    title: 'अप्रूवल कन्फर्म करें',
                    text: confirmText,
                    showCancelButton: true,
                    confirmButtonText: 'हाँ, अप्रूव करें',
                    cancelButtonText: 'नहीं',
                    allowOutsideClick: false
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: "{{ route('admin.merit-edit-requests.approve') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: ids
                        },
                        success: function(resp) {
                            Swal.fire({
                                icon: 'success',
                                title: resp.message || 'रिक्वेस्ट अप्रूव कर दी गई।',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'रिक्वेस्ट अप्रूव नहीं हो सकी',
                                allowOutsideClick: false
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
