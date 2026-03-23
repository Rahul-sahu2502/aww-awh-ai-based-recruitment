@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        @media (max-width: 768px) {

            .modal-content {
                width: 100%;
            }

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
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card container container-fluid">
                    <div class="wrapper wrapper-content animated fadeInRight">
                        <div class="ibox-content p-xl">

                            <div class="row" style="margin-top: 15px;margin-bottom:15px;">
                                <a href="{{ url()->previous() . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}"
                                    style="font-size: 30px; color: #007bff; text-decoration: none; transition: transform 0.3s;">
                                    <i class="bi bi-arrow-left-circle"></i>
                                </a>

                            </div>
                            <div class="row" style="margin-top: 15px;margin-bottom:15px;">
                                <h2>सभी दस्तावेज़</h2>
                            </div>
                            <div class="row">


                                <div class="col-md-12 feature-section">


                                    @php
                                        $documents = [
                                            'Document_Photo' => 'फोटो',
                                            'Document_Sign' => 'हस्ताक्षर',
                                            'Document_5th' => '5वीं कक्षा की अंकसूची',
                                            'Document_8th' => '8वीं कक्षा की अंकसूची',
                                            // 'Document_SSC' => '10वीं कक्षा की अंकसूची',
                                            // 'Document_Inter' => '12वीं कक्षा की अंकसूची',
                                        ];

                                    @endphp

                                    <table class="table table-responsive">
                                        <tr>
                                            @foreach ($documents as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($documents as $key => $label)
                                                <td>
                                                    @if (!empty($applicant_details->$key))
                                                        @php
                                                            $post_file_path = asset(
                                                                'uploads/' . $applicant_details->$key,
                                                            );
                                                            if (config('app.env') === 'production') {
                                                                $post_file_path =
                                                                    config('custom.file_point') .
                                                                    $applicant_details->$key;
                                                            }
                                                        @endphp

                                                        @if (strpos($key, 'Photo') !== false || strpos($key, 'Sign') !== false)
                                                            <!-- Image Click to Open Modal -->
                                                            <a class="myImg" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal"
                                                                data-doc="{{ $post_file_path }}">
                                                                <img width="100" height="100"
                                                                    src="{{ $post_file_path }}" alt="दस्तावेज़ पूर्वावलोकन">
                                                            </a>
                                                        @else
                                                            <!-- View Link to Open Modal -->
                                                            <a class="myImg" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal"
                                                                data-name="{{ $label }}"
                                                                data-doc="{{ $post_file_path }}">
                                                                {{ $label }} देखें
                                                            </a>
                                                        @endif
                                                    @else
                                                        सबमिट नहीं किया गया
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>



                                    @php
                                        $educationDocs = [
                                            'Document_SSC' => '10वीं कक्षा की अंकसूची',
                                            'Document_Inter' => '12वीं कक्षा की अंकसूची',
                                            // 'Document_UG' => 'स्नातक की अंकसूची',
                                            // 'Document_PG' => 'स्नातकोत्तर की अंकसूची',
                                            'Document_Aadhar' => 'आधार कार्ड',
                                            // 'Document_Domicile' => 'स्थानीय निवास प्रमाण पत्र',
                                        ];

                                    @endphp

                                    <table class="table table-responsive">
                                        <tr>
                                            @foreach ($educationDocs as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($educationDocs as $key => $label)
                                                <td>
                                                    @if (!empty($applicant_details->$key))
                                                        @php
                                                            $post_file_path = asset(
                                                                'uploads/' . $applicant_details->$key,
                                                            );
                                                            if (config('app.env') === 'production') {
                                                                $post_file_path =
                                                                    config('custom.file_point') .
                                                                    $applicant_details->$key;
                                                            }
                                                        @endphp

                                                        <!-- View Link to Open Modal -->
                                                        <a class="myImg" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#documentModal" data-name="{{ $label }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें
                                                        </a>
                                                    @else
                                                        सबमिट नहीं किया गया
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>



                                    @php
                                        $extraDocs = [
                                            // 'Document_Aadhar' => 'आधार कार्ड',
                                            'Document_Domicile' => 'स्थानीय निवास प्रमाण पत्र',
                                            'Document_Caste' => 'जाति प्रमाण पत्र',
                                            'Document_Epic' => 'मतदाता पहचान पत्र',
                                            // 'Document_BPL' => 'बीपीएल (गरीबी रेखा ) कार्ड',
                                        ];
                                    @endphp

                                    <table class="table table-responsive">
                                        <tr>
                                            @foreach ($extraDocs as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($extraDocs as $key => $label)
                                                <td>
                                                    @if (!empty($applicant_details->$key))
                                                        @php
                                                            $post_file_path = asset(
                                                                'uploads/' . $applicant_details->$key,
                                                            );
                                                            if (config('app.env') === 'production') {
                                                                $post_file_path =
                                                                    config('custom.file_point') .
                                                                    $applicant_details->$key;
                                                            }
                                                        @endphp

                                                        <a class="myImg" target="_blank" data-bs-toggle="modal"
                                                            data-bs-target="#documentModal" href="#"
                                                            data-name="{{ $label }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें</a>
                                                    @else
                                                        सबमिट नहीं किया गया
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>


                                    @php
                                        $extraDocs1 = [
                                            // 'Document_Epic' => 'मतदाता पहचान पत्र',
                                            // 'Document_BPL' => 'बीपीएल (गरीबी रेखा ) कार्ड',
                                            // 'Document_Widow' => 'विधवा/तलाकशुदा/परित्यक्ता प्रमाण पत्र',
                                            // 'Document_other' => 'अन्य दस्तावेज',
                                        ];
                                    @endphp

                                    <table class="table table-responsive">
                                        <tr>
                                            @foreach ($extraDocs1 as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($extraDocs1 as $key => $label)
                                                <td>
                                                    @if (!empty($applicant_details->$key))
                                                        @php
                                                            $post_file_path = asset(
                                                                'uploads/' . $applicant_details->$key,
                                                            );
                                                            if (config('app.env') === 'production') {
                                                                $post_file_path =
                                                                    config('custom.file_point') .
                                                                    $applicant_details->$key;
                                                            }
                                                        @endphp

                                                        <a class="myImg" target="_blank" data-bs-toggle="modal"
                                                            data-bs-target="#documentModal" href="#"
                                                            data-name="{{ $label }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें</a>
                                                    @else
                                                        सबमिट नहीं किया गया
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>

                                    @php
                                        $questionLabels = [
                                            1 => 'विवाहित प्रमाण पत्र',
                                            3 => 'गरीबी रेखा प्रमाण पत्र',
                                            5 => 'NCC/NSS/Scout Guide प्रमाण पत्र',
                                            9 => 'आगनबाड़ी सहायक अनुभव प्रमाण पत्र',
                                            12 => 'राज्य/राष्ट्रीय खेल प्रमाण पत्र',
                                            13 => 'प्राथमिक/पूर्व माध्यमिक स्कूल में रसोइया या मितानिन',
                                            16 => 'परित्यक्ता प्रमाण पत्र',
                                            17 => 'तलाकशुदा प्रमाण पत्र',
                                            18 => 'विधवा प्रमाण पत्र',
                                        ];
                                        // dd($questionAnswers);
                                        $docChunks = collect($questionAnswers) // assuming your data is in $questionAnswers
                                            ->filter(function ($item) {
                                                return !empty($item->all_files);
                                            })
                                            ->filter(function ($item) use ($questionLabels) {
                                                return array_key_exists($item->ques_ID, $questionLabels);
                                            })
                                            ->chunk(4); // Break into groups of 12 for row-wise display
                                        // dd($docChunks);
                                    @endphp

                                    <table class="table table-responsive table-bordered">
                                        @foreach ($docChunks as $chunk)
                                            <tr>
                                                @foreach ($chunk as $doc)
                                                    <th>{{ $questionLabels[$doc->ques_ID] }}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach ($chunk as $doc)
                                                    @php
                                                        $filePath = asset('uploads/' . $doc->all_files);
                                                        if (config('app.env') === 'production') {
                                                            $filePath = config('custom.file_point') . $doc->all_files;
                                                        }
                                                    @endphp
                                                    <td>
                                                        <a class="myImg" target="_blank" data-bs-toggle="modal"
                                                            data-bs-target="#documentModal" href="#"
                                                            data-key="{{ $questionLabels[$doc->ques_ID] }}"
                                                            data-name="{{ $questionLabels[$doc->ques_ID] }}"
                                                            data-doc="{{ $filePath }}">
                                                            {{ $questionLabels[$doc->ques_ID] }} देखें
                                                        </a>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </table>

                                    @php
                                        // --- 1. DATA PREPARATION ---

                                        // Find the single data record for the documents (where ques_ID is 10)
                                        $documentData = collect($questionMultipleAnswers)->firstWhere('ques_ID', 10);

                                        $processedDocuments = [];
                                        if ($documentData) {
                                            // Split the comma-separated strings into arrays. Using trim to clean up any extra spaces.
                                            $labels = array_map('trim', explode(',', $documentData->all_answers));
                                            $files = array_map('trim', explode(',', $documentData->all_files));

                                            // Combine the labels and files into a single structured array,
                                            // but only if the counts match to avoid errors.
                                            if (count($labels) === count($files)) {
                                                foreach ($labels as $index => $label) {
                                                    if (!empty($label) && !empty($files[$index])) {
                                                        $processedDocuments[] = [
                                                            'label' => $label,
                                                            'file' => $files[$index],
                                                        ];
                                                    }
                                                }
                                            }
                                        }

                                        // --- 2. CHUNKING FOR DISPLAY ---

                                        // Now, chunk the final processed array for the 4-column grid layout.
                                        $docChunks = collect($processedDocuments)->chunk(4);

                                    @endphp

                                    {{-- Check if there are any documents to display --}}
                                    {{-- Check if there are any documents to display --}}
                                    @if (!$docChunks->isEmpty())
                                        <h5 class="card-title">संबंधित दस्तावेज़ (Related Documents)</h5>
                                        <hr>

                                        {{-- Loop through each chunk, which represents a row --}}
                                        @foreach ($docChunks as $chunk)
                                            <div class="row mb-3">

                                                {{-- Loop through each document in the chunk --}}
                                                @foreach ($chunk as $doc)
                                                    <div class="col-md-3">
                                                        @php
                                                            // --- DYNAMIC PATH LOGIC ---
                                                            // Determine the correct file path based on the environment.
                                                            // Default to local path.
                                                            $filePath = asset('uploads/' . $doc['file']);

                                                            // If in production, use the custom file endpoint from your config.
                                                            if (config('app.env') === 'production') {
                                                                // Assumes you have 'file_point' in config/custom.php
                                                                $filePath = config('custom.file_point') . $doc['file'];
                                                            }
                                                        @endphp

                                                        {{-- Use the label from our processed array --}}
                                                        <strong>{{ $doc['label'] }} प्रमाण पत्र</strong>

                                                        <div class="mt-2">
                                                            {{-- This link is now set up to trigger the modal --}}
                                                            <a href="#" class="myImg" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal"
                                                                data-name="{{ $doc['label'] }}"
                                                                data-doc="{{ $filePath }}"
                                                                data-key="{{ $doc['label'] }}">
                                                                {{ $doc['label'] }} प्रमाण पत्र देखें
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        @endforeach
                                    @endif



                                    {{-- //Exp Documents --}}
                                    @if (isset($experience_docs) && count($experience_docs) > 0)
                                        <table class="table table-responsive">
                                            <tr>
                                                <th>अनुभव प्रमाण पत्र</th>
                                            </tr>
                                            <tr>
                                                @php $docCount = 1; @endphp
                                                @foreach ($experience_docs as $doc)
                                                    <td class="border border-secondary">
                                                        @if (!empty($doc->exp_document))
                                                            @php
                                                                $post_file_path = asset(
                                                                    'uploads/' . $doc->exp_document,
                                                                );
                                                                if (config('app.env') === 'production') {
                                                                    $post_file_path =
                                                                        config('custom.file_point') .
                                                                        $doc->exp_document;
                                                                }

                                                                $docLabel = 'अनुभव प्रमाण पत्र' . $docCount;
                                                            @endphp



                                                            <a class="myImg" target="_blank" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal" href="#"
                                                                data-key="{{ $docLabel }}"
                                                                data-name="अनुभव प्रमाण पत्र{{ $docCount }}"
                                                                data-doc="{{ $post_file_path }}">
                                                                अनुभव प्रमाण पत्र {{ $docCount }} देखें
                                                            </a>
                                                        @else
                                                            अनुभव प्रमाण पत्र {{ $docCount }} सबमिट नहीं किया गया
                                                        @endif
                                                    </td>
                                                    @php $docCount++; @endphp
                                                @endforeach
                                            </tr>
                                        </table>
                                    @else
                                        <p>कोई भी अतिरिक्त अनुभव प्रमाण पत्र उपलब्ध नहीं है।</p>
                                    @endif

                                    {{-- //Other Documents --}}
                                    @if (isset($other_docs) && count($other_docs) > 0)
                                        <table class="table table-responsive">
                                            <tr>
                                                <th>अन्य दस्तावेज़</th>
                                            </tr>
                                            <tr>
                                                @php $docCount = 1; @endphp
                                                @foreach ($other_docs as $doc)
                                                    <td class="border border-secondary">
                                                        @if (!empty($doc->other_documents))
                                                            @php
                                                                $post_file_path = asset(
                                                                    'uploads/' . $doc->other_documents,
                                                                );
                                                                if (config('app.env') === 'production') {
                                                                    $post_file_path =
                                                                        config('custom.file_point') .
                                                                        $doc->other_documents;
                                                                }

                                                                $docLabel = 'अन्य दस्तावेज़' . $docCount;
                                                            @endphp



                                                            <a class="myImg" target="_blank" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal" href="#"
                                                                data-key="{{ $docLabel }}"
                                                                data-name="अन्य दस्तावेज़{{ $docCount }}"
                                                                data-doc="{{ $post_file_path }}">
                                                                अन्य दस्तावेज़ {{ $docCount }} देखें
                                                            </a>
                                                        @else
                                                            अन्य दस्तावेज़ {{ $docCount }} सबमिट नहीं किया गया
                                                        @endif
                                                    </td>
                                                    @php $docCount++; @endphp
                                                @endforeach
                                            </tr>
                                        </table>
                                    @else
                                        {{-- <p>कोई भी अतिरिक्त अन्य दस्तावेज़ उपलब्ध नहीं है।</p> --}}
                                    @endif


                                    {{-- @php
                                        $selfDocs = [
                                            'self_attested_file' => 'स्वप्रमाणित पत्र',
                                        ];
                                    @endphp --}}

                                    {{-- <table class="table table-responsive">
                                        <tr>
                                            @foreach ($selfDocs as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($selfDocs as $key => $label)
                                                <td>
                                                    @if (!empty($applicant_details->$key))
                                                        @php
                                                            $post_file_path = asset(
                                                                'uploads/' . $applicant_details->$key,
                                                            );
                                                            if (config('app.env') === 'production') {
                                                                $post_file_path =
                                                                    config('custom.file_point') .
                                                                    $applicant_details->$key;
                                                            }
                                                        @endphp

                                                        <a class="myImg1" target="_blank" data-bs-toggle="modal"
                                                            data-bs-target="#documentModalself" href="#"
                                                            data-name="{{ $label }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें</a>
                                                    @else
                                                        सबमिट नहीं किया गया
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table> --}}

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="documentModalself" tabindex="-1"
                                        aria-labelledby="documentModalself" aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="documentModalself">
                                                        दस्तावेज़ पूर्वावलोकन</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <!-- PDF Viewer -->
                                                    <iframe id="docPreview1" src="" width="100%"
                                                        height="500px" style="border: none; display: none;"></iframe>

                                                    <!-- Image Viewer -->
                                                    <img id="imgPreview1" src=""
                                                        style="width: 100%; display: none; max-height: 90vh;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="documentModal" tabindex="-1"
                                        aria-labelledby="documentModalLabel" aria-hidden="true"
                                        data-bs-backdrop="static">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="documentModalLabel">
                                                        दस्तावेज़ पूर्वावलोकन</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <!-- PDF Viewer -->
                                                    <iframe id="docPreview" src="" width="100%" height="500px"
                                                        style="border: none; display: none;"></iframe>

                                                    <!-- Image Viewer -->
                                                    <img id="imgPreview" src=""
                                                        style="width: 100%; display: none; max-height: 90vh;" />
                                                </div>
                                            </div>
                                        </div>
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
        document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById("documentModal");
            var docPreview = document.getElementById("docPreview");
            var imgPreview = document.getElementById("imgPreview");

            document.querySelectorAll(".myImg").forEach(function(link) {
                link.addEventListener("click", function() {
                    var doc_name = this.getAttribute("data-name");

                    var docUrl = this.getAttribute("data-doc"); // Get document URL

                    // Check if it's an image or PDF
                    if (docUrl.match(/\.(jpeg|jpg|png|gif)$/)) {
                        // If image, show in full width
                        imgPreview.src = docUrl;
                        imgPreview.style.display = "block";
                        docPreview.style.display = "none";
                    } else {
                        // If PDF, show in iframe
                        docPreview.src = docUrl;
                        docPreview.style.display = "block";
                        imgPreview.style.display = "none";
                    }


                });
            });
        });
        var modal = document.getElementById("documentModalself");
        var docPreview1 = document.getElementById("docPreview1");
        var imgPreview1 = document.getElementById("imgPreview1");

        document.querySelectorAll(".myImg1").forEach(function(link) {
            link.addEventListener("click", function() {
                var doc_name = this.getAttribute("data-name");

                var docUrl = this.getAttribute("data-doc"); // Get document URL

                // Check if it's an image or PDF
                if (docUrl.match(/\.(jpeg|jpg|png|gif)$/)) {
                    // If image, show in full width
                    imgPreview1.src = docUrl;
                    imgPreview1.style.display = "block";
                    docPreview1.style.display = "none";
                } else {
                    // If PDF, show in iframe
                    docPreview1.src = docUrl;
                    docPreview1.style.display = "block";
                    imgPreview1.style.display = "none";
                }


            });
        });
    </script>
@endsection
