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

        /* Smooth transitions */
        .short-desc,
        .full-desc {
            transition: opacity 0.2s ease;
        }

        /* Better readability */
        .full-desc {
            line-height: 1.6;
            word-break: break-word;
        }

        /* Focus styles for accessibility */
        .read-more-btn:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
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
                        <h3 class="hindi-heading">नवीनतम विज्ञापन</h3>
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
                        {{-- @foreach ($districts as $district)
                            <option value="{{ $district->name }}">{{ $district->name }}</option>
                        @endforeach --}}
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
                        <th>शीर्षक</th>
                        <th>विवरण</th>
                        <th>जिला </th>
                        <th>परियोजना</th>
                        <th>तारीख से</th>
                        <th>तारीख तक</th>
                        <th>स्थिति</th>
                        <th>फाइल</th>
                        <th>भर्ती पद</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $item)
                        @php
                            $startDate = \Carbon\Carbon::parse($item->Start_date)->format('d/m/Y');
                            $endDate = \Carbon\Carbon::parse($item->End_date)->format('d/m/Y');
                            $docFile = $item->Advertisement_Document;

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
                            {{-- <td class="description-cell">{{ $item->Description }}</td> --}}
                            <td class="description-cell px-4 py-3">
                                <div class="relative">
                                    <!-- Short Description (Visible by default) -->
                                    <span id="short-{{ $item->Advertisement_ID }}" class="short-desc text-gray-700">
                                        {{ Illuminate\Support\Str::limit($item->Description, 50, '...') }}
                                    </span>

                                    <!-- Full Description (Hidden by default) -->
                                    <span id="full-{{ $item->Advertisement_ID }}" class="full-desc text-gray-900"
                                        style="display: none;">
                                        {{ $item->Description }}
                                    </span>
<br>
                                    <!-- Toggle Button (Only shows if text is longer than limit) -->
                                    @if (strlen($item->Description) > 50)
                                        <a type="button" onclick="toggleReadMore('{{ $item->Advertisement_ID }}')" style="text-decoration: none"
                                            id="btn-{{ $item->Advertisement_ID }}" class="text-primary">
                                            <span>Read More</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="dist-cell">{{ $item->district_name }}</td>
                            <td class="dist-cell">{{ $item->project_name }}</td>
                            <td class="date-cell">{{ $startDate }}</td>
                            <td class="date-cell">{{ $endDate }}</td>
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
                                @if (isset($post_file_path))
                                    <a href="#" data-file="{{ $post_file_path }}" class="view-btn existingFile"
                                        title="देखने के लिए यहां क्लिक करें">देखें</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/bharti' . '/' . md5($item->Advertisement_ID)) }}" class="view-btn"
                                    title="भर्ती देखने के लिए यहां क्लिक करें">देखें </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">चयनित फ़ाइल</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="imagePreview" class="img-fluid d-none" alt="चयनित फ़ाइल">
                        <iframe id="docViewer" class="w-100 d-none" style="height: 500px; border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="advertisementDocumentModal" tabindex="-1"
            aria-labelledby="advertisementDocumentModalLabel" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
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

            // Convert base64 → Blob → Object URL (safe for large files)
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

        // Single function to handle all toggles
        function toggleReadMore(itemId) {
            const shortDesc = document.getElementById(`short-${itemId}`);
            const fullDesc = document.getElementById(`full-${itemId}`);
            const button = document.getElementById(`btn-${itemId}`);
            const icon = document.getElementById(`icon-${itemId}`);
            const textSpan = button.querySelector('span');

            if (fullDesc.style.display === 'none') {
                // Expand
                shortDesc.style.display = 'none';
                fullDesc.style.display = 'inline';
                textSpan.textContent = 'Show Less';
                icon.style.transform = 'rotate(180deg)';
                button.classList.add('bg-blue-50');
            } else {
                // Collapse
                shortDesc.style.display = 'inline';
                fullDesc.style.display = 'none';
                textSpan.textContent = 'Read More';
                icon.style.transform = 'rotate(0deg)';
                button.classList.remove('bg-blue-50');
            }
        }

        // Optional: Add keyboard accessibility
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const activeBtn = document.activeElement;
                if (activeBtn.classList.contains('read-more-btn')) {
                    e.preventDefault();
                    const itemId = activeBtn.id.replace('btn-', '');
                    toggleReadMore(itemId);
                }
            }
        });
    </script>

</body>

</html>
