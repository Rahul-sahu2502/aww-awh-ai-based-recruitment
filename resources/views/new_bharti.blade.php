<!DOCTYPE html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width, initial-scale=1.0">
    <title>Anganwadi Workers and Supervisors Recruitment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons for Screen Reader -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">
    <!-- Bootstrap 5 CSS -->
    <style>
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

        #filterToggleBtn {
            border: 2px solid #6c757d;
            padding: 8px 15px;
            transition: all 0.3s ease;
        }

        #filterToggleBtn:hover {
            background-color: #e9ecef !important;
            border-color: #495057;
        }

        #filterToggleBtn.active {
            background-color: #007bff !important;
            color: white;
            border-color: #0056b3;
        }

        .filter-section {
            animation: slideDown 0.3s ease-in-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-section .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .filter-section .form-select {
            border: 1px solid #dee2e6;
        }

        .filter-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e3e6ea;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .filter-card .form-select {
            border-radius: 8px;
            padding: 10px;
        }

        /* Optional styling for better appearance */
        .short-desc,
        .full-desc,
        .short-nnn,
        .full-nnn,
        .short-ward,
        .full-ward {
            word-break: break-word;
            line-height: 1.5;
        }

        .text-primary {
            color: #3b82f6;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .text-primary:hover {
            color: #2563eb;
        }

        /* Add some spacing */
        .br {
            margin-top: 4px;
        }

        /* For better mobile experience */
        @media (max-width: 768px) {

            .description-cell,
            .dist-cell {
                max-width: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @include('partials.header')
        <section class="hindi-section head2">
            <div class=row>
                <div class="col" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 class="hindi-heading">भर्ती आवेदन</h3>
                        <p class="hindi-subtext">अंतिम अपडेट : @php echo date('d-m-Y') @endphp</p>
                    </div>
                    <button id="filterToggleBtn" class="btn btn-light" title="फ़िल्टर">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
                <div style=clear:both></div>
            </div>
        </section>

        <!-- Filter Section -->
        <div id="filterSection" class="filter-card mt-3" style="display:none;">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">जिला चुनें</label>
                    <select id="districtFilter" class="form-select">
                        <option value="">सभी जिले</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">परियोजना चुनें</label>
                    <select id="projectFilter" class="form-select" disabled>
                        <option value="">पहले जिला चुनें</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button id="resetFilter" class="btn btn-lg btn-outline-secondary w-50">
                        <i class="bi bi-arrow-counterclockwise"></i> रीसेट
                    </button>
                </div>
            </div>
        </div>

        <div class="feature-section mt-10">
            <table>
                <thead>
                    <tr>
                        <th>क्र.</th>
                        <th>विज्ञापन</th>
                        <th>पद</th>
                        <th>विवरण</th>
                        <th>जिला </th>
                        <th>परियोजना</th>
                        <th>ग्राम पंचायत /नगर निकाय </th>
                        <th>ग्राम /वार्ड नं. </th>
                        <th>तारीख से</th>
                        <th>तारीख तक</th>
                        <th>स्थिति</th>
                        <th>फाइल</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $item)
                        @php
                            // Always use Carbon objects for logic
                            $startDateCarbon = \Carbon\Carbon::parse($item->Start_date);
                            $endDateCarbon = \Carbon\Carbon::parse($item->End_date);
                            $today = \Carbon\Carbon::today();

                            // For display only
                            $startDate = $startDateCarbon->format('d/m/Y');
                            $endDate = $endDateCarbon->format('d/m/Y');

                            // File path logic
                            $docFile = $item->File_Path ?? $item->Advertisement_Document;
                            $post_file_path = $docFile ? asset('uploads/' . $docFile) : null;

                            if (config('app.env') === 'production' && $docFile) {
                                $post_file_path = config('custom.file_point') . $docFile;
                            }
                        @endphp

                        <tr class="row-animate">
                            <td>{{ $index + 1 }}</td>
                            <td class="title-cell">
                                {{ $item->Advertisement_Title }}
                                <span class="priority-marker"></span>
                            </td>
                            <td class="title-cell">{{ $item->Post_Title }}</td>
                            <!-- Description Column -->
                            <td class="description-cell px-4 py-3">
                                <div class="relative">
                                    <!-- Short Description -->
                                    <span id="short-desc-{{ $item->post_id }}" class="short-desc text-gray-700">
                                        {{ Illuminate\Support\Str::limit($item->Description, 50, '...') }}
                                    </span>

                                    <!-- Full Description -->
                                    <span id="full-desc-{{ $item->post_id }}" class="full-desc text-gray-900"
                                        style="display: none;">
                                        {{ $item->Description }}
                                    </span>
                                    <br>
                                    <!-- Toggle Button -->
                                    @if (strlen($item->Description) > 50)
                                        <a type="button" onclick="toggleReadMore('desc', '{{ $item->post_id }}')"
                                            style="text-decoration: none" id="btn-desc-{{ $item->post_id }}"
                                            class="text-primary">
                                            <span>Read More</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="dist-cell">{{ $item->district_name }}</td>
                            <td class="dist-cell">{{ $item->project_names }}</td>
                            <!-- NNN/Panchayat Names Column -->
                            <td class="dist-cell px-4 py-3">
                                <div class="relative">
                                    @php
                                        $nnnNames = $item->nnn_names ?? $item->panchayat_names;
                                        $nnnTruncated = Illuminate\Support\Str::limit($nnnNames, 30, '...');
                                    @endphp

                                    <!-- Short NNN/Panchayat Names -->
                                    <span id="short-nnn-{{ $item->post_id }}" class="short-nnn text-gray-700">
                                        {{ $nnnTruncated }}
                                    </span>

                                    <!-- Full NNN/Panchayat Names -->
                                    <span id="full-nnn-{{ $item->post_id }}" class="full-nnn text-gray-900"
                                        style="display: none;">
                                        {{ $nnnNames }}
                                    </span>

                                    <!-- Toggle Button -->
                                    @if (strlen($nnnNames) > 30)
                                        <br>
                                        <a type="button" onclick="toggleReadMore('nnn', '{{ $item->post_id }}')"
                                            style="text-decoration: none" id="btn-nnn-{{ $item->post_id }}"
                                            class="text-primary">
                                            <span>Read More</span>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Ward/Village Names Column -->
                            <td class="dist-cell px-4 py-3">
                                <div class="relative">
                                    @php
                                        $wardNames = $item->ward_names ?? $item->villages_names;
                                        $wardTruncated = Illuminate\Support\Str::limit($wardNames, 30, '...');
                                    @endphp

                                    <!-- Short Ward/Village Names -->
                                    <span id="short-ward-{{ $item->post_id }}" class="short-ward text-gray-700">
                                        {{ $wardTruncated }}
                                    </span>

                                    <!-- Full Ward/Village Names -->
                                    <span id="full-ward-{{ $item->post_id }}" class="full-ward text-gray-900"
                                        style="display: none;">
                                        {{ $wardNames }}
                                    </span>

                                    <!-- Toggle Button -->
                                    @if (strlen($wardNames) > 30)
                                        <br>
                                        <a type="button" onclick="toggleReadMore('ward', '{{ $item->post_id }}')"
                                            style="text-decoration: none" id="btn-ward-{{ $item->post_id }}"
                                            class="text-primary">
                                            <span>Read More</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="date-cell">{{ $startDate }}</td>
                            <td class="date-cell">{{ $endDate }}</td>         
                             <!-- Status Column -->
                            <td class="date-cell">
                                @switch($item->apply_status)
                                    @case('Active')
                                        <span class="badge bg-success">Active</span>
                                    @break

                                    @case('Upcoming')
                                        <span class="badge bg-warning text-dark">Upcoming</span>
                                    @break

                                    @case('Expired')
                                        <span class="badge bg-danger">Expired</span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">Unknown</span>
                                @endswitch
                            </td>
                            <td>
                                @if ($docFile)
                                    <a href="#" data-file="{{ $post_file_path }}" class="view-btn existingFile"
                                        title="देखने के लिए यहां क्लिक करें">देखें</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Bootstrap Modal -->
            <div class="modal fade" id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">चयनित फ़ाइल</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="imagePreview" class="img-fluid d-none" alt="चयनित फ़ाइल">
                            <iframe id="docViewer" class="w-100 d-none"
                                style="height: 500px; border: none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style=clear:both></div>
        @include('partials.footer')
    </div>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/fontSizeController.js') }}"></script>
    <script src="{{ asset('assets/js/screenReader.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const allRows = document.querySelectorAll('tbody tr');
            const districtFilter = document.getElementById('districtFilter');
            const projectFilter = document.getElementById('projectFilter');
            const resetBtn = document.getElementById('resetFilter');
            const filterBtn = document.getElementById('filterToggleBtn');
            const filterSection = document.getElementById('filterSection');

            filterBtn.addEventListener('click', function() {
                const isHidden = filterSection.style.display === '' || filterSection.style.display ===
                    'none';

                if (isHidden) {
                    filterSection.style.display = 'block';
                    filterBtn.classList.add('active');
                } else {
                    filterSection.style.display = 'none';
                    filterBtn.classList.remove('active');
                }
            });

            const districtSet = new Set();
            allRows.forEach(row => {
                const district = row.querySelectorAll('.dist-cell')[0]?.innerText.trim();
                if (district) districtSet.add(district);
            });

            districtSet.forEach(d => {
                districtFilter.innerHTML += `<option value="${d}">${d}</option>`;
            });

            districtFilter.addEventListener('change', function() {
                const selectedDistrict = this.value;
                const projectSet = new Set();

                allRows.forEach(row => {
                    const district = row.querySelectorAll('.dist-cell')[0]?.innerText.trim();
                    const project = row.querySelectorAll('.dist-cell')[1]?.innerText.trim();

                    if (!selectedDistrict || district === selectedDistrict) {
                        if (project) projectSet.add(project);
                    }
                });

                projectFilter.innerHTML = '<option value="">सभी परियोजना</option>';
                projectSet.forEach(p => {
                    projectFilter.innerHTML += `<option value="${p}">${p}</option>`;
                });

                projectFilter.disabled = false;
                applyFilter();
            });

            projectFilter.addEventListener('change', applyFilter);

            function applyFilter() {
                let count = 0;

                allRows.forEach(row => {
                    const district = row.querySelectorAll('.dist-cell')[0]?.innerText.trim();
                    const project = row.querySelectorAll('.dist-cell')[1]?.innerText.trim();

                    let show = true;

                    if (districtFilter.value && district !== districtFilter.value) show = false;
                    if (projectFilter.value && project !== projectFilter.value) show = false;

                    row.style.display = show ? '' : 'none';

                    if (show) {
                        count++;
                        row.querySelector('td:first-child').innerText = count;
                    }
                });
            }

            resetBtn.addEventListener('click', function() {
                districtFilter.value = '';
                projectFilter.innerHTML = '<option value="">पहले जिला चुनें</option>';
                projectFilter.disabled = true;

                allRows.forEach((row, index) => {
                    row.style.display = '';
                    row.querySelector('td:first-child').innerText = index + 1;
                });

                // reset filter UI
                filterSection.style.display = 'none';
                filterBtn.classList.remove('active');
            });

        });
    </script>
    <script>
        $(document).ready(function() {
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


        function toggleReadMore(type, itemId) {
            const shortElement = document.getElementById(`short-${type}-${itemId}`);
            const fullElement = document.getElementById(`full-${type}-${itemId}`);
            const button = document.getElementById(`btn-${type}-${itemId}`);
            const textSpan = button.querySelector('span');

            if (fullElement.style.display === 'none') {
                // Expand
                shortElement.style.display = 'none';
                fullElement.style.display = 'inline';
                textSpan.textContent = 'Show Less';
            } else {
                // Collapse
                shortElement.style.display = 'inline';
                fullElement.style.display = 'none';
                textSpan.textContent = 'Read More';
            }
        }

        // Optional: Add click event listeners for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to all read more links
            const readMoreLinks = document.querySelectorAll('a.text-primary[onclick^="toggleReadMore"]');

            readMoreLinks.forEach(link => {
                // Add hover effect
                link.addEventListener('mouseenter', function() {
                    this.style.textDecoration = 'underline';
                    this.style.fontWeight = '600';
                });

                link.addEventListener('mouseleave', function() {
                    this.style.textDecoration = 'none';
                    this.style.fontWeight = 'normal';
                });

                // Make it keyboard accessible
                link.setAttribute('tabindex', '0');
                link.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        const onclickAttr = this.getAttribute('onclick');
                        const match = onclickAttr.match(/toggleReadMore\('([^']+)', '([^']+)'\)/);
                        if (match) {
                            toggleReadMore(match[1], match[2]);
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
