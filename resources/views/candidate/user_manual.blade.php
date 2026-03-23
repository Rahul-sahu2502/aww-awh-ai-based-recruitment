@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        .pdf-viewer-container {
            background: #f8f9fa;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .pdf-viewer-header {
            background: linear-gradient(135deg, #1E88E5, #1565C0);
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pdf-viewer-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .pdf-viewer-header .btn-download {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pdf-viewer-header .btn-download:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .pdf-iframe-wrapper {
            position: relative;
            width: 100%;
            height: calc(100vh - 250px);
            min-height: 600px;
            background: white;
        }

        .pdf-iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .pdf-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #1E88E5;
        }

        .pdf-loading .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
        }

        .info-alert {
            background: linear-gradient(135deg, #fff3cd, #ffeeba);
            border-left: 4px solid #ffc107;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-alert i {
            font-size: 1.5rem;
            color: #ffc107;
        }

        @media (max-width: 768px) {
            .pdf-viewer-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .pdf-iframe-wrapper {
                height: calc(100vh - 300px);
                min-height: 400px;
            }
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">

        <div class="pagetitle">
            <h5 class="fw-bold">उपयोगकर्ता मैनुअल / User Manual</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/candidate/candidate-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">उपयोगकर्ता मैनुअल</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <!-- Info Alert -->
                    <div class="info-alert d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3"></i>
                        <div>
                            <strong>सूचना:</strong>
                            <span>यह उपयोगकर्ता मैनुअल आपको पोर्टल का उपयोग करने में मदद करेगा। कृपया ध्यान से पढ़ें।</span>
                        </div>
                    </div>

                    <!-- PDF Viewer Card -->
                    <div class="card pdf-viewer-container border-0">
                        <div class="pdf-viewer-header">
                            <h5>
                                <i class="bi bi-book"></i>
                                उम्मीदवार उपयोगकर्ता मैनुअल / Candidate User Manual
                            </h5>
                            <a href="{{ $pdfPath }}" download class="btn-download">
                                <i class="bi bi-download"></i>
                                डाउनलोड करें / Download
                            </a>
                        </div>

                        <div class="pdf-iframe-wrapper">
                            <div class="pdf-loading" id="pdfLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">लोड हो रहा है...</span>
                                </div>
                                <p class="mt-3 fw-semibold">PDF लोड हो रहा है, कृपया प्रतीक्षा करें...</p>
                            </div>
                            <iframe id="pdfFrame" src="{{ $pdfPath }}#toolbar=1&navpanes=1&scrollbar=1"
                                onload="document.getElementById('pdfLoading').style.display='none'" title="User Manual PDF">
                            </iframe>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="card border-0 shadow-sm mt-3" style="border-radius: 0.75rem;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                सहायता की आवश्यकता है?
                            </h6>
                            <p class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                यदि PDF नहीं दिख रहा है, तो कृपया <strong>डाउनलोड करें</strong> बटन पर क्लिक करें।
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                किसी भी समस्या के लिए, कृपया <a href="{{ url('/candidate/feedback') }}"
                                    class="text-decoration-none fw-semibold">सहायता एवं सुझाव</a> पेज पर जाएं।
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                मैनुअल में दिए गए चरणों का पालन करके आसानी से आवेदन करें।
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        // Fallback if iframe doesn't load
        document.getElementById('pdfFrame').onerror = function () {
            document.getElementById('pdfLoading').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>PDF लोड नहीं हो सका।</strong> कृपया डाउनलोड बटन का उपयोग करें।
                        </div>
                    `;
        };

        // Set active menu
        document.addEventListener('DOMContentLoaded', function () {
            const userManualLink = document.getElementById('user-manual-id');
            if (userManualLink) {
                userManualLink.classList.remove('collapsed');
            }
        });
    </script>
@endsection