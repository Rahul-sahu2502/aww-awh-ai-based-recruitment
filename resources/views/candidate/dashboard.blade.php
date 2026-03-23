<?php

use Illuminate\Support\Facades\DB;
?>
@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        a {
            font-size: medium;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">

        <div class="pagetitle">
            <h5 class="fw-bold">डैशबोर्ड</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/candidate/candidate-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">डैशबोर्ड</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">

                        <div class="col-xxl-4 col-md-4 mb-2">
                            <div class="card info-card shadow border-0"
                                style="background: linear-gradient(135deg, #e9f5ff, #ffffff); border-radius: 1rem; transition: all 0.3s;">
                                <div class="card-body px-3 py-3">
                                    <a href="/candidate/submitted-applications"
                                        style="text-decoration: none; color: inherit;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Text Left -->
                                            <div style="max-width: calc(100% - 65px);">
                                                <div class="fw-semibold" style="font-size: 1rem; color: #1E88E5;">
                                                    कुल आवेदन
                                                </div>
                                                <h3 class="fw-bold text-dark mt-1 mb-0" style="font-size: 1.5rem;">
                                                    {{ $data['total_applications'] }}
                                                </h3>
                                            </div>
                                            <!-- Icon Right -->
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; background-color: #1E88E5; color: #fff; font-size: 1.2rem;">
                                                <i class="bi bi-file-earmark"></i>
                                            </div>
                                        </div>
                                    </a>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-md-4 mb-2">
                            <div class="card info-card shadow border-0"
                                style="background: linear-gradient(135deg, #ffebee, #ffffff); border-radius: 1rem; transition: all 0.3s;">
                                <div class="card-body px-3 py-3">
                                    <a href="/candidate/pending-applications"
                                        style="text-decoration: none; color: inherit;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Text Left -->
                                            <div style="max-width: calc(100% - 65px);">
                                                <div class="fw-semibold" style="font-size: 1rem; color:#ff9900;">
                                                    कुल अपूर्ण आवेदन
                                                </div>
                                                <h3 class="fw-bold text-dark mt-1 mb-0" style="font-size: 1.5rem;">
                                                    {{ $data['incompleteCount'] }}
                                                </h3>
                                            </div>
                                            <!-- Icon Right -->
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning"
                                                style="width: 50px; height: 50px; color: #fff; font-size: 1.2rem;">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-md-4 mb-2">
                            <div class="card info-card shadow border-0"
                                style="background: linear-gradient(135deg, #f1f8e9, #ffffff); border-radius: 1rem; transition: all 0.3s;">
                                <div class="card-body px-3 py-3">
                                    <a href="/candidate/submitted-applications/Verified"
                                        style="text-decoration: none; color: inherit;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Text Left -->
                                            <div style="max-width: calc(100% - 65px);">
                                                <div class="fw-semibold" style="font-size: 1rem; color: #43A047;">
                                                    कुल पात्र आवेदन
                                                </div>
                                                <h3 class="fw-bold text-dark mt-1 mb-0" style="font-size: 1.5rem;">
                                                    {{ $data['verified_applications'] }}
                                                </h3>
                                            </div>
                                            <!-- Icon Right -->
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; background-color: #43A047; color: #fff; font-size: 1.2rem;">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </div>
                                        </div>
                                    </a>
                                    </a>
                                </div>
                            </div>
                        </div>


                    </div>
                </div><!-- End Left side columns -->
            </div>

            <!-- Feedback CTA – Subtly Highlighted (Matches Your Dashboard) -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3"
                        style="background: #eef7ff; border: 1px solid #d0e7ff;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-lightbulb-fill text-primary me-3" style="font-size: 1.3rem;"></i>
                            <div class="flex-grow-1">
                                <span class=" small" style="color: #475569;">
                                    <b>क्या आपको कोई सहायता चाहिए या कोई सुझाव देना चाहते हैं?</b>
                                </span>
                            </div>
                            <a href="{{ url('/candidate/feedback') }}"
                                class="btn btn-sm btn-primary d-flex align-items-center px-3 py-2"
                                style="font-size: 0.85rem; font-weight: 600; background: #1E88E5; border-color: #1E88E5;">
                                सहायता एवं सुझाव
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Application List --}}
            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-card-list"> </i>आवेदन की सूची</h5>
                            <div class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                                <div class="datatable-container">
                                    <table id="applicationsTable"
                                        class="table table-borderless table-hover datatable datatable-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">क्र.</th>
                                                <th scope="col">आवेदन संख्या</th>
                                                <th scope="col">विज्ञप्ति</th>
                                                <th scope="col">पद शीर्षक</th>
                                                <th scope="col">आवेदित जिले / केन्द्र का नाम</th>
                                                <th scope="col">आवेदन की तिथि</th>
                                                <th scope="col">आवेदन की स्थिति</th>
                                                <th scope="col">देखें</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (@$data['application_lists'] && count($data['application_lists']) > 0)
                                                @foreach ($data['application_lists'] as $index => $application_list)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $application_list->application_num }}</td>
                                                        <td>{{ $application_list->Advertisement_Title }}</td>
                                                        <td>{{ $application_list->post_name }}</td>
                                                        <td>
                                                            {{ $application_list->Dist_name == 0 ? 'महिला सशक्तिकरण केंद्र' : $application_list->Dist_name }}
                                                        </td>
                                                        <td>{{ date('d-m-Y', strtotime($application_list->apply_date)) }}
                                                        </td>

                                                        <td>
                                                            @if ($application_list->Application_Status == 'Submitted' && $application_list->is_final_submit == 1)
                                                                <span class="badge bg-info">Submitted</span>
                                                            @elseif ($application_list->Application_Status == 'Rejected' && $application_list->is_final_submit == 1)
                                                                <span class="badge bg-danger">Rejected</span>
                                                            @elseif ($application_list->Application_Status == 'Verified' && $application_list->is_final_submit == 1)
                                                                <span class="badge bg-success">Approved</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if ($application_list->is_final_submit == 1)
                                                                <a
                                                                    href="{{ url('/candidate/final-application-detail/' . md5($application_list->RowID) . '/' . md5($application_list->application_id)) }}">
                                                                    <button class="btn btn-info btn-sm"><i
                                                                            class="bi bi-eye"></i></button>
                                                                </a>
                                                            @else
                                                                <a
                                                                    href="{{ url('/candidate/view-application-detail/' . md5($application_list->RowID) . '/' . md5($application_list->application_id)) }}">
                                                                    <button class="btn btn-info btn-sm"><i
                                                                            class="bi bi-eye"></i></button>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                {{-- <tr>
                                                    <td colspan="8" class="text-center"> आवेदन नही किया गया है।</td>
                                                </tr> --}}
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Post List --}}
            <div class="row">
                <div class="col-12">
                    <div class="card top-selling overflow-auto">
                        <div class="card-body pb-0">
                            <h5 class="card-title"><i class="bi bi-card-list"> </i> सक्रिय पदों की सूची </h5>

                            <table id="postsTable" class="table table-borderless table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">क्र.</th>
                                        <th scope="col">विज्ञापन शीर्षक</th>
                                        <th scope="col">पद शीर्षक</th>
                                        <th scope="col">जिला</th>
                                        <th scope="col">विकासखंड/नगर निकाय</th>
                                        <th scope="col">ग्राम पंचायत/वार्ड</th>
                                        <th scope="col">ग्राम</th>
                                        <th scope="col">आरंभ तिथि</th>
                                        <th scope="col">अंतिम तिथि</th>
                                        <th scope="col">स्थिति</th>
                                        <th scope="col">फाइल</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($data['post_list']) && count($data['post_list']) > 0)
                                        @foreach ($data['post_list'] as $index => $post)
                                            @php
                                                $docFile = $post->Advertisement_Document ?? '';
                                                $post_file_path = '';
                                                if ($docFile) {
                                                    $post_file_path =
                                                        config('app.env') === 'production'
                                                            ? config('custom.file_point') . $docFile
                                                            : asset('uploads/' . $docFile);
                                                }

                                                // Determine area type based on fk_area_id
                                                $area_type = '';
                                                if (($post->fk_area_id ?? 0) == 1) {
                                                    $area_type = '(ग्रामीण)';
                                                } elseif (($post->fk_area_id ?? 0) == 2) {
                                                    $area_type = '(शहरी)';
                                                }

                                                // Get district name
                                                $district_name = $post->Dist_name ?? '';

                                                // Handle block/urban display logic
                                                $block_nagar_display = '';
                                                if (($post->fk_area_id ?? 0) == 1) {
                                                    // Rural area - show block names
                                                    $block_nagar_display = $post->block_names ?? '';
                                                } elseif (($post->fk_area_id ?? 0) == 2) {
                                                    // Urban area - show nnn names (नगर निकाय)
                                                    $block_nagar_display = $post->nnn_names ?? '';
                                                }

                                                // Handle panchayat/ward display logic
                                                $panchayat_ward_display = '';
                                                if (($post->fk_area_id ?? 0) == 1) {
                                                    // Rural area - show panchayat names (ग्राम पंचायत)
                                                    $panchayat_ward_display = $post->panchayat_names ?? '';
                                                } elseif (($post->fk_area_id ?? 0) == 2) {
                                                    // Urban area - show ward names (वार्ड)
                                                    // Query returns ward names with numbers in format: "वार्ड नाम (वार्ड नंबर)"
                                                    $panchayat_ward_display = $post->ward_names ?? '';
                                                }

                                                // Handle village names
                                                $village_display = $post->villages_names ?? '';

                                                // Format dates
                                                $start_date = '';
                                                $end_date = '';
                                                if (!empty($post->Advertisement_Date)) {
                                                    $start_date = date('d-m-Y', strtotime($post->Advertisement_Date));
                                                }
                                                if (!empty($post->Date_For_Age)) {
                                                    $end_date = date('d-m-Y', strtotime($post->Date_For_Age));
                                                }

                                                // Determine badge class based on status
                                                $badge_class = 'bg-secondary';
                                                $status_text = $post->apply_status ?? 'unknown';

                                                if ($status_text === 'Active') {
                                                    $badge_class = 'bg-success';
                                                } elseif ($status_text === 'Expired') {
                                                    $badge_class = 'bg-danger';
                                                } elseif ($status_text === 'Upcoming') {
                                                    $badge_class = 'bg-warning';
                                                }

                                                // Check for document
                                                $has_document = !empty($docFile);
                                            @endphp
                                            <tr>
                                                <th scope="row">{{ $index + 1 }}</th>
                                                <td>{{ $post->Advertisement_Title ?? '-' }}</td>
                                                <td>{{ $post->title ?? ($post->post_name ?? '-') }}</td>
                                                <td>
                                                    {{ $district_name }}
                                                    {{ $area_type }}
                                                </td>
                                                <td>
                                                    @if (!empty($block_nagar_display))
                                                        {{ $block_nagar_display }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($panchayat_ward_display))
                                                        {{ $panchayat_ward_display }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($village_display))
                                                        {{ $village_display }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $start_date }}</td>
                                                <td>{{ $end_date }}</td>
                                                <td>
                                                    <span class="badge {{ $badge_class }}">
                                                        {{ $status_text }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($has_document)
                                                        <a href="#" data-file="{{ $post_file_path }}"
                                                            class="view-btn existingFile"
                                                            title="देखने के लिए यहां क्लिक करें">देखें</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- <tr>
                                            <td colspan="11" class="text-center">कोई डेटा उपलब्ध नहीं है</td>
                                        </tr> --}}
                                    @endif
                                </tbody>
                            </table>

                            <a href="{{ url('candidate/user-details-form') }}" class="btn btn-sm btn-primary m-3">
                                पद के लिए आवेदन करें</a>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Bootstrap Modal -->
            <div class="modal fade" id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">चयनित फ़ाइल</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="imagePreview" class="img-fluid d-none" alt="चयनित फ़ाइल">
                            <iframe id="docViewer" class="w-100 d-none" style="height: 500px; border: none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="advertisementDocumentModal" tabindex="-1"
                aria-labelledby="advertisementDocumentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="advertisementDocumentModalLabel">चयनित फ़ाइल</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div id="advertisementDocumentContent">
                                <!-- Content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        var csrf_token = "{{ csrf_token() }}";

        $(document).ready(function() {

            function initDataTable(selector) {
                if ($(selector).length) {
                    $(selector).DataTable({
                        autoWidth: false,
                        paging: true,
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        dom: "<'row'<'col-sm-6'l><'col-sm-6'fB>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        buttons: [
                            'excel'
                        ],
                        language: {
                            emptyTable: "आवेदन नहीं किया गया है।"
                        }
                    });
                }
            }

            initDataTable('#applicationsTable');
            initDataTable('#postsTable');

            let $docViewer = $('#docViewer');
            let $imagePreview = $('#imagePreview');
            let $modalElement = $('#docModal');

            function showFileInModal(fileURL, isImage) {
                if (isImage) {
                    $imagePreview.attr('src', fileURL).removeClass('d-none');
                    $docViewer.addClass('d-none');
                } else {
                    $docViewer.attr('src', fileURL).removeClass('d-none');
                    $imagePreview.addClass('d-none');
                }
            }

            $('.existingFile').on('click', function(e) {
                e.preventDefault();
                let fileURL = $(this).data('file');
                let isImage = /\.(jpeg|jpg|png|gif)$/i.test(fileURL);

                showFileInModal(fileURL, isImage);

                // Bootstrap modal show
                $modalElement.modal('show');
            });

            // Clear sources on modal close
            $modalElement.on('hidden.bs.modal', function() {
                $docViewer.attr('src', '');
                $imagePreview.attr('src', '');
            });

        });

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
            const modal = new bootstrap.Modal(document.getElementById('advertisementDocumentModal'));
            const contentDiv = document.getElementById('advertisementDocumentContent');
            contentDiv.innerHTML = `<iframe src="${blobUrl}" width="100%" height="500px" style="border:none;"></iframe>`;
            modal.show();

            // Optional: clean up URL when modal closes
            document.getElementById('docModal').addEventListener('hidden.bs.modal', () => {
                URL.revokeObjectURL(blobUrl);
            });
        }
    </script>
@endsection
