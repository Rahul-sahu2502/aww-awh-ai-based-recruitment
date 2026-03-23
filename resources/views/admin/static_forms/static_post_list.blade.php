@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .status-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">सामान्य पद</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">सामान्य पद सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-list-ul me-2"></i>सामान्य पद सूची</h5>
                        <a href="{{ url('/admin/admin-post?static=1') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>नई सामान्य पद जोड़ें
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table id="staticPostTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>क्र.सं.</th>
                                        <th>पोस्ट नाम</th>
                                        <th>न्यूनतम योग्यता</th>
                                        <th>आयु सीमा</th>
                                        {{-- <th>फ़ाइल</th> --}}
                                        <th>वेटेज</th>
                                        <th>वेटेज प्रबंधन</th>
                                        <th>स्थिति</th>
                                        <th>निर्माण दिनांक</th>
                                        <th>कार्यवाही</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($static_posts as $index => $post)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $post->title }}</strong></td>
                                            <td>{{ $post->Quali_Name ?? '-' }}</td>
                                            <td>{{ $post->min_age }} - {{ $post->max_age }} वर्ष</td>
                                            {{-- <td>
                                                @if ($post->file_path)
                                                <a href="{{ asset($post->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-earmark-pdf"></i> देखें
                                                </a>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td> --}}
                                            <td>
                                                @if ($post->is_weightage == '1')
                                                    <span class="badge bg-success">हाँ</span>
                                                @else
                                                    <span class="badge bg-secondary">नहीं</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($post->is_weightage == '1')
                                                    <a href="{{ url('/admin/weightage-management/edit/' . $post->id) }}"
                                                        class="btn btn-sm btn-info" title="वेटेज संपादित करें">
                                                        <i class="bi bi-pencil-square"></i> संपादित करें
                                                    </a>
                                                @else
                                                    <a href="{{ url('/admin/weightage-management/create?post_id=' . $post->id) }}"
                                                        class="btn btn-sm btn-success" title="वेटेज जोड़ें">
                                                        <i class="bi bi-plus-circle"></i> जोड़ें
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($post->is_active == '1')
                                                    <span class="badge bg-success status-badge">सक्रिय</span>
                                                @else
                                                    <span class="badge bg-danger status-badge">निष्क्रिय</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-Y') }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ url('/admin/static-posts/' . $post->id . '/view') }}"
                                                        class="btn btn-sm btn-primary" title="विवरण देखें">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ url('/admin/static-posts/' . $post->id . '/edit') }}"
                                                        class="btn btn-sm btn-warning" title="संपादित करें">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-post"
                                                        data-id="{{ $post->id }}" title="हटाएं">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">पोस्ट विवरण</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">लोड हो रहा है...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करें</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.8.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#staticPostTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="bi bi-file-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer me-1"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
                ],
                language: {
                    search: "खोजें:",
                    lengthMenu: "प्रति पृष्ठ _MENU_ प्रविष्टियाँ दिखाएं",
                    info: "कुल _TOTAL_ में से _START_ से _END_ तक दिखा रहे हैं",
                    infoEmpty: "0 में से 0 से 0 तक दिखा रहे हैं",
                    infoFiltered: "(कुल _MAX_ प्रविष्टियों में से फ़िल्टर किया गया)",
                    zeroRecords: "कोई मिलान रिकॉर्ड नहीं मिला",
                    emptyTable: "तालिका में कोई डेटा उपलब्ध नहीं है",
                    paginate: {
                        first: "प्रथम",
                        last: "अंतिम",
                        next: "अगला",
                        previous: "पिछला"
                    }
                },
                order: [
                    [0, 'asc']
                ],
                pageLength: 10
            });

            // View Details
            $('.view-details').on('click', function () {
                const postId = $(this).data('id');

                $.ajax({
                    url: `/admin/static-posts/${postId}/details`,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            const post = response.data;
                            let html = `
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h6 class="border-bottom pb-2 mb-3">मूल जानकारी</h6>
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th width="30%">पोस्ट नाम</th>
                                                                    <td>${post.name}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>न्यूनतम आयु</th>
                                                                    <td>${post.min_age} वर्ष</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>अधिकतम आयु</th>
                                                                    <td>${post.max_age} वर्ष</td>
                                                                </tr>
                                                                ${post.max_age_relax ? `<tr><th>आयु में छूट</th><td>${post.max_age_relax} वर्ष</td></tr>` : ''}
                                                                <tr>
                                                                    <th>न्यूनतम योग्यता</th>
                                                                    <td>${post.qualification ? post.qualification.Quali_Name : '-'}</td>
                                                                </tr>
                                                            </table>

                                                            ${post.subjects && post.subjects.length > 0 ? `
                                                                                                                <h6 class="border-bottom pb-2 mb-3 mt-4">विषय</h6>
                                                                                                                <ul class="list-group">
                                                                                                                    ${post.subjects.map(subject => `<li class="list-group-item">${subject.subject_name}</li>`).join('')}
                                                                                                                </ul>
                                                                                                                ` : ''}

                                                            ${post.skills && post.skills.length > 0 ? `
                                                                                                                <h6 class="border-bottom pb-2 mb-3 mt-4">कौशल</h6>
                                                                                                                <ul class="list-group">
                                                                                                                    ${post.skills.map(skill => `<li class="list-group-item">${skill.skill_name}</li>`).join('')}
                                                                                                                </ul>
                                                                                                                ` : ''}

                                                            ${post.org_types && post.org_types.length > 0 ? `
                                                                                                                <h6 class="border-bottom pb-2 mb-3 mt-4">संगठन प्रकार और अनुभव</h6>
                                                                                                                <table class="table table-bordered">
                                                                                                                    <thead>
                                                                                                                        <tr>
                                                                                                                            <th>संगठन प्रकार</th>
                                                                                                                            <th>न्यूनतम अनुभव (वर्ष)</th>
                                                                                                                        </tr>
                                                                                                                    </thead>
                                                                                                                    <tbody>
                                                                                                                        ${post.org_types.map((org, idx) => `
                                                                        <tr>
                                                                            <td>${org.org_type}</td>
                                                                            <td>${post.experiences[idx] || '-'} वर्ष</td>
                                                                        </tr>
                                                                    `).join('')}
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                                ` : ''}

                                                            ${post.questions && post.questions.length > 0 ? `
                                                                                                                <h6 class="border-bottom pb-2 mb-3 mt-4">प्रश्न</h6>
                                                                                                                <ul class="list-group">
                                                                                                                    ${post.questions.map(question => `<li class="list-group-item">${question.ques_title}</li>`).join('')}
                                                                                                                </ul>
                                                                                                                ` : ''}

                                                            ${post.guidelines ? `
                                                                                                                <h6 class="border-bottom pb-2 mb-3 mt-4">नियम और विनियम</h6>
                                                                                                                <div class="border p-3 rounded">
                                                                                                                    ${post.guidelines}
                                                                                                                </div>
                                                                                                                ` : ''}
                                                        </div>
                                                    </div>
                                                `;
                            $('#modalContent').html(html);
                        } else {
                            $('#modalContent').html(
                                '<div class="alert alert-danger">डेटा लोड करने में त्रुटि</div>'
                            );
                        }
                    },
                    error: function () {
                        $('#modalContent').html(
                            '<div class="alert alert-danger">सर्वर त्रुटि</div>');
                    }
                });
            });

            // Delete Post
            $('.delete-post').on('click', function () {
                const postId = $(this).data('id');

                Swal.fire({
                    title: 'क्या आप निश्चित हैं?',
                    text: "यह अपरिवर्तनीय पोस्ट स्थायी रूप से हटा दी जाएगी!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'हाँ, हटाएं!',
                    cancelButtonText: 'रद्द करें'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/static-posts/${postId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'हटा दिया गया!',
                                        text: 'अपरिवर्तनीय पोस्ट सफलतापूर्वक हटा दी गई है।',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('त्रुटि!', response.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('त्रुटि!', 'कुछ गलत हो गया', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection