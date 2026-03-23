@extends('layouts.dahboard_layout')

@section('styles')
    <style>
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
            max-height: 400px;
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
            cursor: pointer;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
        }

        #btnFilter {
            background: #28a745;
            color: white;
        }

        #btnReset {
            background: #6c757d;
            color: white;
        }

        .doc-cell {
            cursor: pointer;
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">आवेदन</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if (session('sess_role') === 'Super_admin')
                            <a href="{{ url('/admin/admin-dashboard') }}">होम</a>
                        @elseif (session('sess_role') === 'Admin')
                            <a href="{{ url('/examinor/examinor-dashboard') }}">होम</a>
                        @endif
                    <li class="breadcrumb-item active">अंकों की प्रविष्टि</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"> </i>अंकों की प्रविष्टि</h5>
                        <button type="button" class="filter-toggle-btn" id="toggleFilters">
                            <i class="bi bi-funnel-fill"></i>
                            <span>फ़िल्टर दिखाएं</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        {{-- <span class="badge border border-primary text-primary " id="detailsToggleDiv"
                            style="font-size: 0.8rem; text-align: left;">
                            <b>Note :</b>
                            <ol class="text-break ">
                                <li class="mt-2 lh-base text-wrap">According to this post, the weightage has been calculated
                                    based on the minimum qualification percentage multiplied by
                                    0.6 / इस पोस्ट के अनुसार, वेटेज की गणना न्यूनतम योग्यता प्रतिशत को 0.6 से गुणा करके की
                                    गई है।</li>
                                <li class="mt-2 lh-base text-wrap">According to the post, the minimum experience was
                                    considered, and for each additional year of experience beyond the
                                    minimum, the weightage was calculated by awarding 2 marks per extra year. subject to a
                                    maximum of 20 marks for extra experience.
                                    / पद के अनुसार, न्यूनतम अनुभव पर विचार किया गया तथा न्यूनतम अनुभव से अधिक प्रत्येक
                                    अतिरिक्त वर्ष के अनुभव के लिए, प्रति
                                    अतिरिक्त वर्ष 2 अंक प्रदान करके वेटेज की गणना की गई है। अतिरिक्त अनुभव के लिए अधिकतम 20
                                    अंक निर्धारित किए गए।</li>
                                <li class="mt-1 lh-base text-wrap"> You can only submit the points once / आप अंक केवल एक बार
                                    ही जमा कर सकते हैं।
                                </li>

                            </ol>
                        </span> --}}

                        <div class="filter-container" id="filterContainer">
                            <form id="marksFilterForm" method="get" action="{{ route('admin.marks-entry') }}">
                                <div class="custom-filter-group">
                                    <div class="form-group">
                                        <label>विज्ञापन का शीर्षक</label>
                                        <select id="filterAdvertisementTitle" name="advertisement_id" class="form-control">
                                            <option value="">विज्ञापन का शीर्षक</option>
                                            @foreach ($advertisment_lists as $advertisment)
                                                <option value="{{ $advertisment->Advertisement_ID }}"
                                                    {{ request('advertisement_id') == $advertisment->Advertisement_ID ? 'selected' : '' }}>
                                                    {{ $advertisment->Advertisement_Title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>पद का शीर्षक</label>
                                        <select id="filterPostTitle" name="post_id" class="form-control">
                                            <option value="">पद का शीर्षक</option>
                                            @foreach ($post_lists as $post)
                                                <option value="{{ $post->post_id }}"
                                                    {{ request('post_id') == $post->post_id ? 'selected' : '' }}>
                                                    {{ $post->title }}{{ !empty($post->project_name) ? ' (' . $post->project_name . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>ग्राम पंचायत</label>
                                        <input type="text" name="gp_name" class="form-control"
                                            value="{{ request('gp_name') }}" placeholder="ग्राम पंचायत">
                                    </div>
                                    <div class="form-group">
                                        <label>ग्राम</label>
                                        <input type="text" name="village_name" class="form-control"
                                            value="{{ request('village_name') }}" placeholder="ग्राम">
                                    </div>
                                    <div class="form-group">
                                        <label>नगर निकाय</label>
                                        <input type="text" name="nagar_name" class="form-control"
                                            value="{{ request('nagar_name') }}" placeholder="नगर निकाय">
                                    </div>
                                    <div class="form-group">
                                        <label>वार्ड</label>
                                        <input type="text" name="ward_name" class="form-control"
                                            value="{{ request('ward_name') }}" placeholder="वार्ड">
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button id="btnReset" class="btn filter-btn" type="button">
                                        <i class="bi bi-arrow-clockwise me-1"></i> रीसेट
                                    </button>
                                    <button id="btnFilter" class="btn filter-btn" type="submit">
                                        <i class="bi bi-search me-1"></i> फ़िल्टर लागू करें
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="alert alert-info mt-3" role="alert">
                            <small>नोट: संबंधित दस्तावेज़ देखने के लिए किसी भी अंक वाले कॉलम के सेल पर लिखे दस्तावेज़ देखें
                                को क्लिक करें, संबंधित दस्तावेज़ खुलेगा।</small>
                        </div>

                        <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">

                            <table id="datatable" class="table table-striped table-bordered cell-border">
                                {{-- {{dd($application_lists[0]);}} --}}
                                <thead>
                                    <tr>
                                        <th>क्रम संख्या</th>
                                        <th>पूरा नाम</th>
                                        <th>पिता का नाम</th>
                                        <th>जन्म तिथि</th>
                                        <th>पद का शीर्षक</th>
                                        <th>मोबाइल नंबर</th>
                                        <th>ग्राम पंचायत</th>
                                        <th>ग्राम</th>
                                        <th>नगर निकाय</th>
                                        <th>वार्ड</th>
                                        <th>जाति प्रमाण अंक (Max 10)</th>
                                        <th>विधवा/परित्यक्ता/तलाकशुदा अंक (Max 15)</th>
                                        <th>ग़रीबी रेखा अंक (Max 6)</th>
                                        <th>कन्या आश्रम अंक (Max 3)</th>
                                        <th>न्यूनतम अनुभव अंक (Max 6)</th>
                                        <th>न्यूनतम शैक्षिक योग्यता अंक (Max 60)</th>
                                        <th>कुल अंक</th>
                                        <th>एक्शन </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($application_lists as $index => $application_list)
                                        @if ($application_list->is_marks_confirmed == 0)
                                            @php
                                                $basePath =
                                                    config('app.env') === 'production'
                                                        ? config('custom.file_point')
                                                        : config('custom.file_point');
                                                $basePath = rtrim((string) $basePath, '/') . '/';

                                                $casteDoc = !empty($application_list->caste_doc)
                                                    ? $basePath . $application_list->caste_doc
                                                    : '';
                                                $vptDoc = !empty($application_list->vpt_doc)
                                                    ? $basePath . $application_list->vpt_doc
                                                    : '';
                                                $seccDoc = !empty($application_list->secc_doc)
                                                    ? $basePath . $application_list->secc_doc
                                                    : '';
                                                $kanAshDoc = !empty($application_list->kan_ash_doc)
                                                    ? $basePath . $application_list->kan_ash_doc
                                                    : '';
                                                $expDoc = !empty($application_list->exp_doc)
                                                    ? $basePath . $application_list->exp_doc
                                                    : '';
                                                $eduDoc = !empty($application_list->edu_doc)
                                                    ? $basePath . $application_list->edu_doc
                                                    : '';
                                            @endphp
                                            <tr>
                                                <form action="{{ route('admin.marks-entry') }}" method="post"
                                                    enctype="multipart/form-data" class="myForm">
                                                    @csrf
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $application_list->First_Name }}</td>
                                                    <td>{{ $application_list->FatherName }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($application_list->DOB)) }}</td>
                                                    <td>{{ $application_list->Post_Name }}</td>
                                                    <td>{{ $application_list->Contact_Number }}</td>
                                                    <td>{{ $application_list->gp_name ?? '-' }}</td>
                                                    <td>{{ $application_list->village_name ?? '-' }}</td>
                                                    <td>{{ $application_list->nagar_name ?? '-' }}</td>
                                                    <td>
                                                        @if (!empty($application_list->ward_name))
                                                            {{ $application_list->ward_name }}
                                                            @if (!empty($application_list->ward_no))
                                                                ({{ $application_list->ward_no }})
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    {{-- domicile_mark --}}
                                                    <td class="doc-cell" data-doc="{{ $casteDoc }}"
                                                        title="दस्तावेज़ देखें">
                                                        <input type="text" name="domicile_mark[]"
                                                            class="form-control decimal-only"
                                                            value="{{ $application_list->domicile_mark }}" max="10"
                                                            min="0">
                                                        <small class="text-muted d-block mt-1">दस्तावेज़ देखें</small>
                                                    </td>

                                                    {{-- v_p_t_questionMarks --}}
                                                    <td class="doc-cell" data-doc="{{ $vptDoc }}"
                                                        title="दस्तावेज़ देखें">
                                                        <input type="text" name="v_p_t_questionMarks[]"
                                                            class="form-control decimal-only"
                                                            value="{{ $application_list->v_p_t_questionMarks ?? 0 }}"
                                                            max="15" min="0">
                                                        <small class="text-muted d-block mt-1">दस्तावेज़ देखें</small>
                                                    </td>

                                                    {{-- secc_questionMarks --}}
                                                    <td class="doc-cell" data-doc="{{ $seccDoc }}"
                                                        title="दस्तावेज़ देखें">
                                                        <input type="text" name="secc_questionMarks[]"
                                                            class="form-control decimal-only"
                                                            value="{{ $application_list->secc_questionMarks ?? 0 }}"
                                                            max="6" min="0">
                                                        <small class="text-muted d-block mt-1">दस्तावेज़ देखें</small>
                                                    </td>

                                                    {{-- kan_ash_questionMarks --}}
                                                    <td class="doc-cell" data-doc="{{ $kanAshDoc }}"
                                                        title="दस्तावेज़ देखें">
                                                        <input type="text" name="kan_ash_questionMarks[]"
                                                            class="form-control decimal-only"
                                                            value="{{ $application_list->kan_ash_questionMarks ?? 0 }}"
                                                            max="3" min="0">
                                                        <small class="text-muted d-block mt-1">दस्तावेज़ देखें</small>
                                                    </td>

                                                    {{-- edu_qualification_mark --}}
                                                    {{-- <td>
                                                        <input type="text" name="edu_qualification_marks[]" class="form-control decimal-only"
                                                            value="{{ $application_list->edu_qualification_mark }}" max="10"
                                                            min="0">
                                                    </td> --}}

                                                    {{-- min_experiance_mark --}}
                                                    <td class="doc-cell" data-doc="{{ $expDoc }}"
                                                        title="दस्तावेज़ देखें">
                                                        <input type="text" name="min_experiance_mark[]"
                                                            class="form-control decimal-only"
                                                            value="{{ $application_list->min_experiance_mark }}"
                                                            max="6" min="0">
                                                        <small class="text-muted d-block mt-1">दस्तावेज़ देखें</small>
                                                    </td>

                                                    {{-- min_edu_qualification_mark --}}
                                                    <td class="doc-cell" data-doc="{{ $eduDoc }}"
                                                        title="दस्तावेज़ देखें">
                                                        <input type="text" name="min_edu_qualification_mark[]"
                                                            class="form-control decimal-only"
                                                            value="{{ $application_list->min_edu_qualification_mark }}"
                                                            max="60" min="0">
                                                        <small class="text-muted d-block mt-1">दस्तावेज़ देखें</small>
                                                    </td>

                                                    {{-- Total Marks --}}
                                                    <td>
                                                        <input type="text"
                                                            class="form-control total-marks decimal-only" value=""
                                                            readonly>
                                                    </td>

                                                    {{-- Submit --}}
                                                    <td>
                                                        <input type="hidden" name="id[]"
                                                            value="{{ $application_list->RowID }}">
                                                        <input type="hidden" name="post_id[]"
                                                            value="{{ $application_list->fk_post_id }}">
                                                        @if ($application_list->is_marks_confirmed == 0)
                                                            <button type="submit"
                                                                class="btn btn-success btn-sm">सबमिट</button>
                                                        @endif


                                                    </td>
                                                </form>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- <input type="Submit" value="Submit Marks" class="btn btn-info btn-lg"> -->
                            {{-- <button type="submit" class="btn btn-primary">Submit Marks</button> --}}
                        </div>

                        <!-- Document Preview Modal -->
                        <div class="modal fade" id="marksDocModal" tabindex="-1" aria-labelledby="marksDocModalLabel"
                            data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="marksDocModalLabel">दस्तावेज़ पूर्वावलोकन</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <iframe id="marksDocFrame" src="" width="100%" height="600"
                                            style="display: none; border: none;"></iframe>
                                        <img id="marksDocImg" src="" alt="दस्तावेज़"
                                            style="max-width: 100%; display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        function recalcTotals() {
            const rows = document.querySelectorAll('#datatable tbody tr');

            rows.forEach(row => {
                const domicileMarkInput = row.querySelector('input[name="domicile_mark[]"]');
                const vptMarkInput = row.querySelector('input[name="v_p_t_questionMarks[]"]');
                const seccMarkInput = row.querySelector('input[name="secc_questionMarks[]"]');
                const kanMarkInput = row.querySelector('input[name="kan_ash_questionMarks[]"]');
                const minExperienceMarkInput = row.querySelector('input[name="min_experiance_mark[]"]');
                const minEduQualificationMarkInput = row.querySelector(
                    'input[name="min_edu_qualification_mark[]"]');
                const totalMarksInput = row.querySelector('.total-marks');

                const calculateTotal = () => {
                    const domicileMark = parseFloat(domicileMarkInput?.value) || 0;
                    const vptMark = parseFloat(vptMarkInput?.value) || 0;
                    const seccMark = parseFloat(seccMarkInput?.value) || 0;
                    const kanMark = parseFloat(kanMarkInput?.value) || 0;
                    const minExperienceMark = parseFloat(minExperienceMarkInput?.value) || 0;
                    const minEduQualificationMark = parseFloat(minEduQualificationMarkInput?.value) || 0;

                    totalMarksInput.value = domicileMark + vptMark + seccMark + kanMark +
                        minExperienceMark + minEduQualificationMark;
                };

                calculateTotal();

                [domicileMarkInput, vptMarkInput, seccMarkInput, kanMarkInput, minExperienceMarkInput,
                    minEduQualificationMarkInput
                ]
                .forEach(input => input?.addEventListener('input', calculateTotal));
            });
        }
        $(document).ready(function() {
            recalcTotals();
            // Filter Toggle Functionality
            $('#toggleFilters').on('click', function() {
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

            const hasFilters = $('#filterAdvertisementTitle').val() ||
                $('#filterPostTitle').val() ||
                $('input[name="gp_name"]').val() ||
                $('input[name="village_name"]').val() ||
                $('input[name="nagar_name"]').val() ||
                $('input[name="ward_name"]').val();
            if (hasFilters) {
                $('#toggleFilters').trigger('click');
            }

            function initMarksTable() {
                return $('#datatable').DataTable({
                    "autoWidth": false,
                    "paging": true,
                    "lengthMenu": [10, 25, 50, 100],
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6'fB>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    buttons: [
                        'excel'
                    ]
                });
            }

            let marksTable = initMarksTable();

            function refreshTable(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.querySelector('#datatable tbody');

                if (!newTbody) {
                    return;
                }

                if ($.fn.DataTable.isDataTable('#datatable')) {
                    marksTable.destroy();
                }

                $('#datatable tbody').replaceWith(newTbody);
                marksTable = initMarksTable();

                // Re-bind total calc after DOM replace
                recalcTotals();
            }

            function loadFilteredData(queryString) {
                const url = "{{ route('admin.marks-entry') }}" + (queryString ? `?${queryString}` : '');
                $.get(url, function(response) {
                    refreshTable(response);
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, '', url);
                    }
                });
            }

            $('#marksFilterForm').on('submit', function(e) {
                e.preventDefault();
                const queryString = $(this).serialize();
                loadFilteredData(queryString);
            });

            $('#btnReset').on('click', function() {
                $('#filterAdvertisementTitle').prop('selectedIndex', 0);
                $('#filterPostTitle').prop('selectedIndex', 0);
                $('input[name="gp_name"]').val('');
                $('input[name="village_name"]').val('');
                $('input[name="nagar_name"]').val('');
                $('input[name="ward_name"]').val('');
                loadFilteredData('');
            });

            // Open document modal from marks cells
            $(document).on('click', '.doc-cell', function(e) {
                if ($(e.target).is('input,button,a,select,textarea')) {
                    return;
                }
                const docUrl = $(this).data('doc');
                if (!docUrl) {
                    Swal.fire({
                        icon: 'info',
                        title: 'दस्तावेज़ उपलब्ध नहीं है',
                        allowOutsideClick: false
                    });
                    return;
                }

                const isImage = docUrl.match(/\.(jpeg|jpg|png|gif)$/i);
                const $frame = $('#marksDocFrame');
                const $img = $('#marksDocImg');

                if (isImage) {
                    $img.attr('src', docUrl).show();
                    $frame.hide().attr('src', '');
                } else {
                    $frame.attr('src', docUrl).show();
                    $img.hide().attr('src', '');
                }

                const modal = new bootstrap.Modal(document.getElementById('marksDocModal'));
                modal.show();
            });

            $(document).on('click', '.doc-cell input', function(e) {
                e.stopPropagation();
            });

            //to add and remove class from input feild
            $('form input').keyup(function() {
                if (this.value) {
                    var element = $(this);

                    // Remove the is-invalid class from input elements
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    var element = $(this);
                    var elementName = element.attr('name');

                    if (elementName != "post" && elementName != "pehchan" && elementName != "samiti_name" &&
                        elementName != "panjiyan_no" && elementName != "total_member" && elementName !=
                        "house_no" && elementName != "demand" && elementName != "pond_name" &&
                        elementName != "khasra_no" && elementName != "rakba" && elementName != "amount")
                        if (element.length) {
                            // Remove the is-invalid class from input elements
                            element.removeClass('was-validated');
                            element.addClass('is-invalid');
                        }
                }
            });


            $(document).on('input', '.decimal-only', function() {
                this.value = this.value
                    .replace(/[^0-9.]/g, '') // Remove everything except digits and decimal
                    .replace(/(\..*)\./g, '$1'); // Allow only one decimal point
            });
        });



        $('.myForm').submit(function(e) {
            e.preventDefault(); // Prevent form from submitting normally

            var form = new FormData(this);
            var url = $(this).attr('action'); // Get the form action URL
            var csrf_token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token value
            form.append('_token', '{{ csrf_token() }}');

            // Submit the form using AJAX
            $.ajax({
                url: url,
                type: "POST",
                data: form,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                context: this,
                success: function(data) {
                    // Handle the response from the server
                    console.log(data);
                    if (data.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: data['message'],
                            // text: 'आवेदन की स्थिति को ट्रैक करने के लिए इस आईडी का उपयोग करें',
                            allowOutsideClick: false
                        }).then((data) => {
                            window.location.href = '/admin/marks-entry';
                        });
                    } else if (data.status == 'notChange') {
                        Swal.fire({
                            icon: 'info',
                            title: data['title'],
                            text: data['message'],
                            allowOutsideClick: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: data['title'],
                            text: data['message'],
                            allowOutsideClick: false
                        })
                    }
                },
                error: function(xhr, status, error) {
                    // Handle the error
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'warning',
                        title: 'अंकों की जाँच में त्रुटि हुई! <br> (Marks Verification Failed)',
                        html: error.message,
                        confirmButtonText: 'ठीक है',
                        allowOutsideClick: false
                    });

                    var response = JSON.parse(xhr.responseText);

                }
            });
        });
    </script>
@endsection
