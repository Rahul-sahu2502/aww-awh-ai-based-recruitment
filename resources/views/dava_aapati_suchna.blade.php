<!DOCTYPE html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}"> --}}
    <title>दावा आपत्ति सूचना - Women And Child Development Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">
    <style>
        .pdf-viewer-container {
            width: 100%;
            height: calc(100vh - 200px);
            min-height: 600px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pdf-viewer-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .additional-docs-section {
            margin: 40px 0;
            padding: 30px;
            /* background: #f8f9fa; */
            border-radius: 10px;
        }

        .doc-card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .doc-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .doc-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .doc-card-title i {
            margin-right: 10px;
            color: #3498db;
        }

        .doc-card-description {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .doc-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-view,
        .btn-download {
            padding: 10px 25px;
            border-radius: 5px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-view {
            background: #3498db;
            color: white;
        }

        .btn-view:hover {
            background: #2980b9;
            color: white;
            transform: scale(1.05);
        }

        .btn-download {
            background: #27ae60;
            color: white;
        }

        .btn-download:hover {
            background: #229954;
            color: white;
            transform: scale(1.05);
        }

        .filter-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #3498db;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-title i {
            color: #3498db;
            font-size: 1.3rem;
        }

        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .filter-label .required {
            color: #e74c3c;
            margin-left: 3px;
        }

        .filter-select {
            padding: 9px 11px;
            border: 2px solid #bdc3c7;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #2c3e50;
            background-color: white;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
        }

        .filter-select:hover {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
        }

        .filter-select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 12px rgba(52, 152, 219, 0.3);
            background-color: #f8fbff;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-filter-reset {
            padding: 12px 20px;
            background: #95a5a6;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .btn-filter-reset:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .filter-status {
            padding: 10px 15px;
            background: #ecf0f1;
            border-radius: 6px;
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            margin-top: 10px;
            display: none;
        }

        .filter-status.active {
            display: block;
            background: #d5f4e6;
            color: #27ae60;
            border-left: 4px solid #27ae60;
        }

        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        @media (max-width: 1024px) {
            .filter-row {
                grid-template-columns: 1fr 1fr;
            }

            .filter-buttons {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            .pdf-viewer-container {
                height: calc(100vh - 250px);
                min-height: 400px;
            }

            .doc-actions {
                flex-direction: column;
            }

            .btn-view,
            .btn-download {
                width: 100%;
                justify-content: center;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .filter-buttons {
                flex-direction: column;
            }

            .btn-filter-reset {
                width: 50%;
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
                        <h3 class="hindi-heading">दावा आपत्ति सूचना</h3>
                        {{-- <p class="hindi-subtext">अंतिम अपडेट : @php echo date('d-m-Y') @endphp</p> --}}
                    </div>
                </div>
                <div style=clear:both></div>
            </div>
        </section>

        <section class="mainContent full-width clearfix">
            <div class="container mt-3" style="background: #f8f9fa;">

                {{-- <div class="pdf-viewer-container">
                    <iframe src="{{ asset('assets/landing_page_files/Suchana_Sign.pdf') }}" type="application/pdf"
                        title="दावा आपत्ति सूचना">
                    </iframe>
                </div> --}}
                {{-- <div class="text-center mt-3 mb-4">
                    <a href="{{ asset('assets/landing_page_files/Suchana_Sign.pdf') }}" class="btn btn-primary"
                        download="Dwapati_Suchna.pdf" style="padding: 10px 30px; border-radius: 5px;">
                        <i class="bi bi-download"></i> PDF डाउनलोड करें
                    </a>
                </div> --}}


                <!-- Additional Documents Section -->
                <div class="additional-docs-section">
                    <h3 style="text-align: center; margin-bottom: 30px; color: #2c3e50; font-weight: 700;">
                        दावा आपत्ति सूचना दस्तावेज़
                    </h3>


                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="bi bi-funnel"></i>
                            दस्तावेज़ फ़िल्टर
                        </div>
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">
                                    जिला चुनें
                                </label>
                                <select class="filter-select" id="districtFilter">
                                    <option value="">-- सभी जिले --</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">
                                    परियोजना चुनें
                                </label>
                                <select class="filter-select" id="projectFilter">
                                    <option value="">-- सभी परियोजनाएं --</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="filter-group filter-buttons">
                                <button type="button" class=" btn btn-sm btn-filter-reset" id="resetFilterBtn">
                                    <i class="bi bi-arrow-clockwise"></i> रीसेट करें
                                </button>
                            </div>
                        </div>
                        <div class="filter-status" id="filterStatus"></div>
                    </div>



                    <div id="anatimListContainer">
                        @foreach ($anatim_list as $item)
                            @php
                                // Determine location name
                                $locationName = $item->nnn_names ?? ($item->panchayat_names ?? 'सामान्य क्षेत्र');

                                // Format dates
                                $startDate = $item->claim_start_date
                                    ? date('d-m-Y', strtotime($item->claim_start_date))
                                    : 'निर्धारित नहीं';
                                $endDate = $item->claim_end_date
                                    ? date('d-m-Y', strtotime($item->claim_end_date))
                                    : 'निर्धारित नहीं';

                                // Check if file exists
                                $fileExists = !empty($item->anantim_list_file);
                            @endphp

                            <div class="mb-4 bg-white shadow-sm p-2 rounded">
                                <div class="doc-card-title">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    {{ $loop->iteration }}. {{ $item->postname }} दस्तावेज़
                                    @if (!empty($item->project) || !empty($locationName))
                                        (परियोजना {{ $item->project ?? 'N/A' }} - {{ $locationName }})
                                    @endif
                                </div>

                                <div class="doc-card-description">
                                    {{ $item->Advertisement_Title ?? 'विज्ञापन' }}
                                    <br>
                                    <strong>
                                        दावा आपत्ति दिनांक
                                        {{ $startDate }}
                                        से
                                        {{ $endDate }}
                                        तक
                                    </strong>
                                </div>

                                <div class="doc-actions">
                                    @php
                                        $file_path = '';
                                        if ($fileExists) {
                                            $file_path = asset('uploads/' . $item->anantim_list_file);
                                            if (config('app.env') === 'production') {
                                                $file_path = config('custom.file_point') . $item->anantim_list_file;
                                            }
                                        }
                                    @endphp
                                    @if ($fileExists)
                                        <button class="btn-view view-pdf-btn" data-pdf="{{ $file_path }}">
                                            <i class="bi bi-eye"></i> अनंतिम सूची देखें
                                        </button>

                                        <a href="{{ $file_path }}" class="btn-download" download>
                                            <i class="bi bi-download"></i> अनंतिम सूची डाउनलोड करें
                                        </a>
                                    @else
                                        <span class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i> दस्तावेज़ उपलब्ध नहीं
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if (count($anatim_list) === 0)
                        <p class="text-center text-muted">कोई अनंतिम सूची उपलब्ध नहीं है</p>
                    @endif

                    @if (empty($anatim_list))
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">कोई अनंतिम सूची उपलब्ध नहीं है</h4>
                            <p class="text-muted">वर्तमान में कोई अनंतिम सूची प्रकाशित नहीं की गई है।</p>
                        </div>
                    @endif

                    @push('scripts')
                        <script>
                            // PDF viewer functionality
                            document.querySelectorAll('.view-pdf-btn').forEach(button => {
                                button.addEventListener('click', function() {
                                    const pdfUrl = this.getAttribute('data-pdf');

                                    // Open PDF in new tab
                                    window.open(pdfUrl, '_blank');

                                    // OR use a modal viewer if you have one
                                    /*
                                    const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
                                    const pdfFrame = document.getElementById('pdfFrame');
                                    pdfFrame.src = pdfUrl;
                                    modal.show();
                                    */
                                });
                            });
                        </script>
                    @endpush
                </div>
            </div>

        </section>

        <!-- PDF Viewer Modal -->
        <div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfViewerModalLabel">दस्तावेज़ पूर्वावलोकन</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="modalPdfViewer" src="" class="w-100"
                            style="height: 80vh; border: none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> बंद करें
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div style=clear:both></div>
        @include('partials.footer')
    </div>
    {{-- <script src="{{ asset('assets/js/script.js') }}"></script> --}}
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    <script>
        $(document).ready(function() {
            // PDF viewer functionality
            setupPdfViewer();

            // Initialize filter status
            updateFilterStatus();

            // 1. Get Projects Data based on selected District
            $('#districtFilter').on('change', function() {
                var districtID = $(this).val();
                $('#projectFilter').html('<option value="">-- परियोजना चुनें --</option>');

                if (!districtID) {
                    // Reset project filter to initial state
                    $('#projectFilter').html('<option value="">-- सभी परियोजनाएं --</option>');
                    @foreach ($projects as $project)
                        $('#projectFilter').append(
                            '<option value="{{ $project->id }}">{{ $project->name }}</option>');
                    @endforeach
                    updateFilterStatus();
                    return;
                }

                $.ajax({
                    url: '/get-project/' + districtID,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Projects Response:', response);

                        let projects = response.projects;
                        let html = '<option value="">-- परियोजना चुनें --</option>';

                        if (Array.isArray(projects) && projects.length > 0) {
                            $.each(projects, function(i, item) {
                                // Note: Using project_code as value
                                html += '<option value="' + item.project_code + '">' +
                                    item.project_name + '</option>';
                            });
                        } else {
                            html += '<option value="">कोई परियोजना नहीं मिली</option>';
                        }

                        $('#projectFilter').html(html);
                        updateFilterStatus();

                        // Clear the data container when district changes
                        $('#anatimListContainer').html(
                            '<p class="text-center text-muted p-3">कृपया परियोजना चुनें</p>'
                        );
                    },
                    error: function() {
                        $('#projectFilter').html(
                            '<option value="">परियोजना लोड नहीं हुआ</option>');
                        updateFilterStatus();
                    }
                });
            });

            // 2. Project Selection filter
            $('#projectFilter').on('change', function() {
                var projectID = $(this).val();
                var districtID = $('#districtFilter').val();

                // Clear previous data
                $('#anatimListContainer').html(
                    '<p class="text-center text-muted p-3">लोड हो रहा है...</p>'
                );

                // Ensure both district and project are selected
                if (!districtID || !projectID) {
                    $('#anatimListContainer').html(
                        '<p class="text-center text-danger p-3">कृपया जिला और परियोजना दोनों का चयन करें</p>'
                    );
                    updateFilterStatus();
                    return;
                }

                $.ajax({
                    url: '/dava-aapati-suchna/filters/' + districtID + '/' + projectID,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Filter Response:', response);

                        let container = $('#anatimListContainer');
                        container.empty();

                        // Check if response has error
                        if (response.status === 'error') {
                            container.html(
                                '<p class="text-center text-danger p-3">' + response
                                .message + '</p>'
                            );
                            return;
                        }

                        let data = response.data || [];

                        if (!data || data.length === 0) {
                            container.html(
                                '<p class="text-center text-danger p-3">कोई रिकॉर्ड उपलब्ध नहीं है</p>'
                            );
                            updateFilterStatus();
                            return;
                        }

                        // Render posts data
                        $.each(data, function(index, item) {
                            // Determine location name
                            let locationName = item.nnn_names || (item
                                .panchayat_names || 'सामान्य क्षेत्र');

                            // Format dates
                            let startDate = item.claim_start_date ?
                                formatDate(item.claim_start_date) :
                                'निर्धारित नहीं';
                            let endDate = item.claim_end_date ?
                                formatDate(item.claim_end_date) :
                                'निर्धारित नहीं';

                            // Check if file exists
                            let fileExists = item.anantim_list_file && item
                                .anantim_list_file.trim() !== '';

                            // Build the HTML for each post - EXACTLY matching your Blade template
                            let html = `
                            <div class="mb-4 bg-white shadow-sm p-2 rounded">
                                <div class="doc-card-title">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    ${index + 1}. ${item.postname || ''} दस्तावेज़
                                    ${(item.project || locationName !== 'सामान्य क्षेत्र') ? 
                                        `(परियोजना ${item.project || 'N/A'} - ${locationName})` : 
                                        ''}
                                </div>

                                <div class="doc-card-description">
                                    ${item.Advertisement_Title || 'विज्ञापन'}
                                    <br>
                                    <strong>
                                        दावा आपत्ति दिनांक
                                        ${startDate}
                                        से
                                        ${endDate}
                                        तक
                                    </strong>
                                </div>

                                <div class="doc-actions">
                                    ${fileExists ? 
                                        `<button class="btn-view view-pdf-btn"
                                                            data-pdf="/uploads/${item.anantim_list_file}"
                                                            data-title="${item.postname || ''} अनंतिम सूची">
                                                            <i class="bi bi-eye"></i> अनंतिम सूची देखें
                                                        </button>

                                                        <a href="/uploads/${item.anantim_list_file}"
                                                            class="btn-download" download>
                                                            <i class="bi bi-download"></i> अनंतिम सूची डाउनलोड करें
                                                        </a>` :
                                        `<span class="text-danger">
                                                            <i class="bi bi-exclamation-triangle"></i> दस्तावेज़ उपलब्ध नहीं
                                                        </span>`
                                    }
                                </div>
                            </div>
                        `;

                            container.append(html);
                        });

                        updateFilterStatus();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading posts:', error);
                        $('#anatimListContainer').html(
                            '<p class="text-center text-danger p-3">डेटा लोड करने में त्रुटि हुई। कृपया पुनः प्रयास करें।</p>'
                        );
                        updateFilterStatus();
                    }
                });
            });

            // Reset filter button functionality
            $('#resetFilterBtn').click(function() {
                // Reset all filter selects
                $('#districtFilter, #projectFilter').val('');

                // Reset selects to initial state
                $('#projectFilter').html('<option value="">-- सभी परियोजनाएं --</option>');
                @foreach ($projects as $project)
                    $('#projectFilter').append(
                        '<option value="{{ $project->id }}">{{ $project->name }}</option>');
                @endforeach

                // Clear the data container
                $('#anatimListContainer').html(
                    '<p class="text-center text-muted p-3">कृपया जिला और परियोजना चुनें</p>'
                );

                // Update filter status
                updateFilterStatus();
            });

            // Function to update filter status display
            function updateFilterStatus() {
                var statusText = '';
                var filters = [];

                // Check main filters
                var districtText = $('#districtFilter option:selected').text();
                var projectText = $('#projectFilter option:selected').text();

                if ($('#districtFilter').val() && districtText !== '-- जिला चुनें --') {
                    filters.push('जिला: ' + districtText);
                }
                if ($('#projectFilter').val() && projectText !== '-- परियोजना चुनें --' && projectText !==
                    '-- सभी परियोजनाएं --') {
                    filters.push('परियोजना: ' + projectText);
                }

                // Update status text
                if (filters.length > 0) {
                    statusText = 'सक्रिय फ़िल्टर: ' + filters.join(' | ');
                    $('#filterStatus').removeClass('inactive').addClass('active');
                } else {
                    statusText = 'कोई फ़िल्टर सक्रिय नहीं है';
                    $('#filterStatus').removeClass('active').addClass('inactive');
                }

                $('#filterStatus').text(statusText);
            }
        });

        function setupPdfViewer() {
            const $modalPdfViewer = $('#modalPdfViewer');
            const pdfViewerModal = new bootstrap.Modal($('#pdfViewerModal')[0]);
            const $modalTitle = $('#pdfViewerModalLabel');

            // Use event delegation for dynamically added buttons
            $(document).on('click', '.view-pdf-btn', function() {
                const pdfUrl = $(this).data('pdf');
                const title = $(this).data('title') || 'अनंतिम सूची';

                $modalPdfViewer.attr('src', pdfUrl);
                $modalTitle.text(title);
                pdfViewerModal.show();
            });

            // Clear iframe when modal is closed
            $('#pdfViewerModal').on('hidden.bs.modal', function() {
                $modalPdfViewer.attr('src', '');
            });
        }

        // Helper function to format dates
        function formatDate(dateString) {
            if (!dateString) return 'निर्धारित नहीं';
            try {
                var date = new Date(dateString);
                if (isNaN(date.getTime())) {
                    return dateString; // Return original if invalid
                }
                // Format as DD-MM-YYYY (matching your PHP format)
                var day = date.getDate().toString().padStart(2, '0');
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var year = date.getFullYear();
                return day + '-' + month + '-' + year;
            } catch (e) {
                return dateString;
            }
        }
    </script>


</body>

</html>
