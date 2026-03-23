<!DOCTYPE html>
<html lang="hi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>उपयोगकर्ता मैनुअल - User Manual | महिला एवं बाल विकास विभाग</title>
    <link rel="stylesheet" href="{{ asset('assets/css/landingPage_style.css') }}">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Subtle styles matching landing page design */
        .user-manual-section {
            padding: 40px 20px;
            background: #f9f9f9;
            min-height: calc(100vh - 200px);
        }

        .manual-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .manual-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .info-banner {
            background: #fff3cd;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid #ffc107;
        }

        .info-banner i {
            font-size: 1.5rem;
            color: #ff9800;
            flex-shrink: 0;
        }

        .info-banner .info-text {
            flex: 1;
            color: #856404;
        }

        .info-banner .info-text strong {
            display: block;
            font-size: 1rem;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .info-banner .info-text span {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .download-section {
            padding: 20px 25px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .download-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #495057;
        }

        .download-info i {
            font-size: 2rem;
            color: #1976d2;
        }

        .download-info strong {
            font-size: 1rem;
            color: rgb(26, 31, 54);
        }

        .download-info .file-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .download-btn {
            background: #1976d2;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .download-btn:hover {
            background: #1565c0;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.25);
        }

        .download-btn i {
            font-size: 1.1rem;
        }

        .pdf-viewer-wrapper {
            position: relative;
            width: 100%;
            height: 650px;
            background: #f5f5f5;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }

        .pdf-viewer-wrapper iframe {
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
            z-index: 10;
        }

        .pdf-loading .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e3e3e3;
            border-top: 4px solid #1976d2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 12px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .pdf-loading p {
            color: #555;
            font-weight: 500;
            font-size: 1rem;
        }

        .help-section {
            padding: 25px;
            background: white;
        }

        .help-section h3 {
            color: rgb(26, 31, 54);
            margin-bottom: 18px;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .help-section h3 i {
            color: #1976d2;
            font-size: 1.4rem;
        }

        .help-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .help-list li {
            padding: 12px 0;
            color: #555;
            display: flex;
            align-items: start;
            gap: 10px;
            line-height: 1.6;
            border-bottom: 1px solid #f0f0f0;
        }

        .help-list li:last-child {
            border-bottom: none;
        }

        .help-list li i {
            color: #4caf50;
            font-size: 1.1rem;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .help-list li a {
            color: #1976d2;
            font-weight: 500;
            text-decoration: none;
        }

        .help-list li a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-manual-section {
                padding: 20px 10px;
            }

            .pdf-viewer-wrapper {
                height: 450px;
            }

            .download-section {
                flex-direction: column;
                text-align: center;
            }

            .info-banner {
                flex-direction: column;
                text-align: center;
            }

            .download-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @include('partials.header')

        {{-- <section class="hindi-section head2">
            <div class="row">
                <div class="col" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 class="hindi-heading">उपयोगकर्ता मैनुअल</h3>
                        <p class="hindi-subtext">User Manual - भर्ती पोर्टल का उपयोग करने के लिए मार्गदर्शिका</p>
                    </div>
                </div>
                <div style="clear:both"></div>
            </div>
        </section> --}}

        <section class="user-manual-section">
            <div class="manual-container">

                <!-- Manual Card -->
                <div class="manual-card">
                    <!-- Info Banner -->
                    <div class="info-banner">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-text">
                            <strong>महत्वपूर्ण सूचना</strong>
                            <span>कृपया आवेदन करने से पहले इस मैनुअल को ध्यान से पढ़ें। इसमें भर्ती प्रक्रिया की सभी
                                जानकारी दी गई है।</span>
                        </div>
                    </div>

                    <!-- Download Section -->
                    <div class="download-section">
                        <div class="download-info">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            <div>
                                <strong>AWW-AWH आवेदनकर्ता मार्गदर्शिका</strong>
                                <div class="file-meta">पीडीएफ दस्तावेज़</div>
                            </div>
                        </div>
                        <a href="{{ asset('assets/user_manual/AWWCandidateManual.pdf') }}" download
                            class="download-btn">
                            <i class="bi bi-download"></i>
                            डाउनलोड करें
                        </a>
                    </div>

                    <!-- PDF Viewer -->
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-loading" id="pdfLoading">
                            <div class="spinner"></div>
                            <p>मैनुअल लोड हो रहा है...</p>
                        </div>
                        <iframe id="pdfFrame"
                            src="{{ asset('assets/user_manual/AWWCandidateManual.pdf') }}#toolbar=1&navpanes=1&scrollbar=1"
                            onload="document.getElementById('pdfLoading').style.display='none'"
                            title="User Manual PDF Viewer">
                        </iframe>
                    </div>

                    <!-- Help Section -->
                    <div class="help-section">
                        <h3>
                            <i class="bi bi-question-circle-fill"></i>
                            सहायता की आवश्यकता है?
                        </h3>
                        <ul class="help-list">
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>यदि PDF नहीं दिख रहा है, तो कृपया <strong>डाउनलोड करें</strong> बटन
                                    पर क्लिक करके फ़ाइल को अपने डिवाइस में सेव करें।</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>मैनुअल में दिए गए चरणों का पालन करके आप आसानी से ऑनलाइन आवेदन कर सकते
                                    हैं।</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>किसी भी तकनीकी समस्या के लिए <a href="{{ url('/contact') }}">संपर्क
                                        पेज</a> पर जाएं।</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>आवेदन करने के लिए <a href="{{ url('/login') }}">लॉगिन</a> करें या नया अकाउंट
                                    बनाएं।</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>

        @include('partials.footer')
    </div>

    <script src="{{ asset('assets/js/landingPage_script.js') }}"></script>
    <script>
        // Fallback if iframe doesn't load
        document.getElementById('pdfFrame').onerror = function () {
            const loadingDiv = document.getElementById('pdfLoading');
            loadingDiv.innerHTML = `
                <div style="padding: 30px; text-align: center;">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: #ff9800;"></i>
                    <h4 style="color: #555; margin-top: 15px; font-weight: 500;">PDF लोड नहीं हो सका</h4>
                    <p style="color: #777; margin-top: 8px; font-size: 0.95rem;">कृपया डाउनलोड बटन का उपयोग करें।</p>
                </div>
            `;
            loadingDiv.style.display = 'block';
        };

        // Hide loading after 3 seconds if iframe doesn't fire onload
        setTimeout(function () {
            const loadingDiv = document.getElementById('pdfLoading');
            if (loadingDiv.style.display !== 'none') {
                loadingDiv.style.display = 'none';
            }
        }, 3000);
    </script>
</body>

</html>