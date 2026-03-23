@extends('layouts.dahboard_layout')

@section('styles')
    <style>
        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
        }

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
        @php $verificationStates = $verificationStates ?? collect(); @endphp
        @php $marksEntry = $marksEntry ?? null; @endphp
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

                                        {{-- {{dd($applicant_details,$documentArray);}} --}}
                                        <tr>
                                            @foreach ($documents as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            {{-- @php dd($applicant_details)@endphp --}}
                                            @foreach ($documents as $key => $label)
                                                {{-- {{dd($applicant_details);}} --}}
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
                                                            <a class="myImg" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal"
                                                                data-name="{{ $label }}"
                                                                data-key="{{ $key }}"
                                                                data-doc="{{ $post_file_path }}">
                                                                {{ $label }} देखें
                                                                @php
                                                                    $state = $verificationStates[$key] ?? null;
                                                                @endphp

                                                                @if ($state)
                                                                    @if ($state->is_verified)
                                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                                    @else
                                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                                    @endif
                                                                @elseif (in_array($key, $documentArray))
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @endif
                                                            </a>
                                                            @if ($state && !$state->is_verified && $state->remark)
                                                                <div class="text-danger small mt-1 doc-remark">टिप्पणी:
                                                                    {{ $state->remark }}</div>
                                                            @endif
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
                                                            data-key="{{ $key }}" data-key="{{ $key }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें
                                                            @php
                                                                $state = $verificationStates[$key] ?? null;
                                                            @endphp

                                                            @if ($state)
                                                                @if ($state->is_verified)
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @else
                                                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                                                @endif
                                                            @elseif (in_array($key, $documentArray))
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @endif
                                                        </a>
                                                        @if ($state && !$state->is_verified && $state->remark)
                                                            <div class="text-danger small mt-1 doc-remark">टिप्पणी:
                                                                {{ $state->remark }}</div>
                                                        @endif
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
                                            'Document_Domicile' => 'स्थानीय निवास प्रमाण पत्र (सरपंच सचिव द्वारा
                                                संयुक्त हस्ताक्षरित/नगरीय क्षेत्र में वार्ड पार्षद द्वारा हस्ताक्षरितअथवा
                                                पटवारी द्वारा जारी)',
                                            'Document_Caste' => 'जाति प्रमाण पत्र',
                                            'Document_Epic' => 'अद्यतन मतदाता सूची',
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
                                                            data-name="{{ $label }}" data-key="{{ $key }}"
                                                            data-key="{{ $key }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें
                                                            @php
                                                                $state = $verificationStates[$key] ?? null;
                                                            @endphp

                                                            @if ($state)
                                                                @if ($state->is_verified)
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @else
                                                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                                                @endif
                                                            @elseif (in_array($key, $documentArray))
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @endif
                                                        </a>
                                                        @if ($state && !$state->is_verified && $state->remark)
                                                            <div class="text-danger small mt-1 doc-remark">टिप्पणी:
                                                                {{ $state->remark }}</div>
                                                        @endif
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
                                                            data-name="{{ $label }}" data-key="{{ $key }}"
                                                            data-doc="{{ $post_file_path }}">
                                                            {{ $label }} देखें
                                                            @foreach ($documentArray as $documentName)
                                                                @if ($key == $documentName)
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @endif
                                                            @endforeach
                                                        </a>
                                                    @else
                                                        सबमिट नहीं किया गया
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>




                                    {{-- <pre>{{ json_encode($questionAnswers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre> --}}
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
                                                            @php
                                                                $state =
                                                                    $verificationStates[
                                                                        $questionLabels[$doc->ques_ID]
                                                                    ] ?? null;
                                                            @endphp
                                                            @if ($state)
                                                                @if ($state->is_verified)
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @else
                                                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                                                @endif
                                                            @elseif (in_array($questionLabels[$doc->ques_ID], $documentArray))
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @endif
                                                        </a>
                                                        @if ($state && !$state->is_verified && $state->remark)
                                                            <div class="text-danger small mt-1 doc-remark">टिप्पणी:
                                                                {{ $state->remark }}</div>
                                                        @endif
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
                                                                @php
                                                                    $state = $verificationStates[$doc['label']] ?? null;
                                                                @endphp
                                                                @if ($state)
                                                                    @if ($state->is_verified)
                                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                                    @else
                                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                                    @endif
                                                                @elseif (in_array($doc['label'], $documentArray))
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @endif
                                                            </a>
                                                            @if ($state && !$state->is_verified && $state->remark)
                                                                <div class="text-danger small mt-1 doc-remark">टिप्पणी:
                                                                    {{ $state->remark }}</div>
                                                            @endif
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
                                                                @foreach ($documentArray as $documentName)
                                                                    @if ($docLabel == $documentName)
                                                                        <i
                                                                            class="bi bi-check-circle-fill text-success"></i>
                                                                    @endif
                                                                @endforeach
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
                                                <th>अन्य प्रमाण पत्र</th>
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

                                                                $docLabel = 'अन्य प्रमाण पत्र' . $docCount;
                                                            @endphp



                                                            <a class="myImg" target="_blank" data-bs-toggle="modal"
                                                                data-bs-target="#documentModal" href="#"
                                                                data-key="{{ $docLabel }}"
                                                                data-name="अन्य प्रमाण पत्र{{ $docCount }}"
                                                                data-doc="{{ $post_file_path }}">
                                                                अन्य प्रमाण पत्र {{ $docCount }} देखें
                                                                @foreach ($documentArray as $documentName)
                                                                    @if ($docLabel == $documentName)
                                                                        <i
                                                                            class="bi bi-check-circle-fill text-success"></i>
                                                                    @endif
                                                                @endforeach
                                                            </a>
                                                        @else
                                                            अन्य प्रमाण पत्र {{ $docCount }} सबमिट नहीं किया गया
                                                        @endif
                                                    </td>
                                                    @php $docCount++; @endphp
                                                @endforeach
                                            </tr>
                                        </table>
                                    @else
                                        {{-- <p>कोई भी अतिरिक्त अन्य प्रमाण पत्र उपलब्ध नहीं है।</p> --}}
                                    @endif

                                    {{-- Marks summary for current applicant/post --}}
                                    {{-- @if ($marksEntry) --}}
                                    <h5 class="card-title mt-4">अंक (Marks Summary)</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>जाति प्रमाण अंक (Max 10)</th>
                                                <th>प्रश्न अंक (विधवा/परित्यक्ता/तलाकशुदा + ग़रीबी रेखा + कन्या आश्रम) (Max
                                                    24)</th>
                                                <th>न्यूनतम अनुभव अंक (Max 6)</th>
                                                <th>न्यूनतम शैक्षिक योग्यता अंक (Max 60)</th>
                                                <th>कुल अंक</th>
                                                <th>स्थिति</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($marksEntry)
                                                <tr>
                                                    <td>{{ $marksEntry->domicile_mark ?? '-' }}</td>
                                                    <td>{{ $marksEntry->ques_mark ?? '-' }}</td>
                                                    <td>{{ $marksEntry->min_experiance_mark ?? '-' }}</td>
                                                    <td>{{ $marksEntry->min_edu_qualification_mark ?? '-' }}</td>
                                                    <td>{{ $marksEntry->total_mark ?? '-' }}</td>
                                                    <td>
                                                        @if ($marksEntry->is_marks_confirmed)
                                                            <span class="badge bg-success">पुष्ट</span>
                                                        @else
                                                            <span class="badge bg-secondary">लंबित</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

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
                                    <div class="modal fade" id="documentModal" tabindex="-1" data-bs-backdrop="static"
                                        data-bs-keyboard="false" aria-labelledby="documentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="documentModalLabel">
                                                        दस्तावेज़ पूर्वावलोकन</h5>
                                                    {{-- {{dd($applicant_details);}} --}}
                                                    <input type="hidden" id="RowID"
                                                        value="{{ $applicant_details->RowID }}">
                                                    <input type="hidden" id="Applicant_ID"
                                                        value="{{ $applicant_details->Applicant_ID }}">
                                                    <input type="hidden" id="fk_post_id"
                                                        value="{{ $applicant_details->fk_post_id }}">
                                                    <input type="hidden" id="apply_id"
                                                        value="{{ $applicant_details->apply_id }}">
                                                    {{-- <input type="text"> --}}
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

                                    <!-- Verification Modal -->
                                    <div class="modal fade" id="verificationModal" tabindex="-1"
                                        data-bs-backdrop="static" data-bs-keyboard="false"
                                        aria-labelledby="verificationModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="verificationModalLabel">क्या यह दस्तावेज़
                                                        प्रमाणित है?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-3">कृपया पुष्टि करें:</p>
                                                    <div class="d-flex gap-2 mb-3">
                                                        <button type="button" class="btn btn-success"
                                                            id="btn-verify-yes">हाँ</button>
                                                        <button type="button" class="btn btn-danger"
                                                            id="btn-verify-no">नहीं</button>
                                                    </div>
                                                    <div id="remarkSection" class="mt-3" style="display:none;">
                                                        <label for="verificationRemark" class="form-label">टिप्पणी
                                                            लिखें</label>
                                                        <textarea class="form-control" id="verificationRemark" rows="3" placeholder="टिप्पणी दर्ज करें"></textarea>
                                                        <div class="mt-3 d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-secondary"
                                                                id="btn-cancel-remark">रद्द करें</button>
                                                            <button type="button" class="btn btn-primary"
                                                                id="btn-save-remark">टिप्पणी जमा करें</button>
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
            </div>
        </div>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        let currentDocKey = null;
        let currentDocName = null;

        const fk_post_id = document.getElementById('fk_post_id').value;
        const Applicant_ID = document.getElementById('Applicant_ID').value;
        const user_row_id = document.getElementById('RowID').value;
        const apply_id = document.getElementById('apply_id').value;

        // Allow verification modal except for Admin with district_id = 0
        const allowVerification = @json(!(session('sess_role') === 'Admin' || (!(session('sess_role') === 'Admin') && session('district_id') == 0)));

        const docPreview = document.getElementById("docPreview");
        const imgPreview = document.getElementById("imgPreview");

        const escapeHtml = (unsafe) => {
            return unsafe ?
                unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;") :
                '';
        };

        // When the document modal is closed, force the verification modal
        $('#documentModal').on('hidden.bs.modal', function(e) {
            if (!allowVerification) return;
            if (!currentDocKey) return;
            $('#verificationModal').modal('show');
        });

        // Handle document click preview and remember current document
        document.querySelectorAll(".myImg").forEach(function(link) {
            link.addEventListener("click", function() {
                currentDocName = this.getAttribute("data-name");
                currentDocKey = this.getAttribute("data-key") || currentDocName;

                const docUrl = this.getAttribute("data-doc");

                if (docUrl.match(/\.(jpeg|jpg|png|gif)$/)) {
                    imgPreview.src = docUrl;
                    imgPreview.style.display = "block";
                    docPreview.style.display = "none";
                } else {
                    docPreview.src = docUrl;
                    docPreview.style.display = "block";
                    imgPreview.style.display = "none";
                }
            });
        });

        // Verification modal interactions
        $('#btn-verify-yes').on('click', function() {
            sendVerification(true, null);
        });

        $('#btn-verify-no').on('click', function() {
            $('#remarkSection').show();
            $('#verificationRemark').focus();
        });

        $('#btn-cancel-remark').on('click', function() {
            $('#remarkSection').hide();
            $('#verificationRemark').val('');
        });

        $('#btn-save-remark').on('click', function() {
            const remark = $('#verificationRemark').val().trim();
            if (!remark) {
                alert('कृपया टिप्पणी दर्ज करें');
                return;
            }
            sendVerification(false, remark);
        });

        function sendVerification(isVerified, remark) {
            if (!allowVerification) return;
            if (!currentDocKey || !currentDocName) {
                alert('दस्तावेज़ चयन नहीं हुआ।');
                return;
            }

            $.ajax({
                url: "{{ route('admin.store-verification') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    applicant_id: user_row_id,
                    fk_post_id: fk_post_id,
                    fk_apply_id: apply_id,
                    document_key: currentDocKey,
                    document_name: currentDocName,
                    is_verified: isVerified ? 1 : 0,
                    remark: remark
                },
                success: function(response) {
                    // mark check or remark indicator
                    const $links = $('a[data-key="' + currentDocKey + '"]');
                    if (isVerified) {
                        $links.each(function() {
                            const $this = $(this);
                            $this.find('.bi-x-circle-fill').remove();
                            $this.closest('td').find('.doc-remark').remove();
                            if ($this.find('.bi-check-circle-fill').length === 0) {
                                $this.append('<i class="bi bi-check-circle-fill text-success"></i>');
                            }
                        });
                    } else {
                        $links.each(function() {
                            const $this = $(this);
                            $this.find('.bi-check-circle-fill').remove();
                            if ($this.find('.bi-x-circle-fill').length === 0) {
                                $this.append('<i class="bi bi-x-circle-fill text-danger"></i>');
                            }

                            const $td = $this.closest('td');
                            const safeRemark = escapeHtml(remark || '');
                            if ($td.find('.doc-remark').length === 0) {
                                $td.append('<div class="text-danger small mt-1 doc-remark">टिप्पणी: ' +
                                    safeRemark + '</div>');
                            } else {
                                $td.find('.doc-remark').html('टिप्पणी: ' + safeRemark);
                            }
                        });
                    }

                    // Log tel_viewed only after decision
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('transition.entry') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            doc_name: currentDocName,
                            tbl_user_detail: @json($applicant_details)
                        }
                    });

                    // reset state and close verification modal
                    $('#verificationRemark').val('');
                    $('#remarkSection').hide();
                    $('#verificationModal').modal('hide');
                    currentDocKey = null;
                    currentDocName = null;
                },
                error: function(xhr) {
                    alert('सत्यापन सहेजने में समस्या हुई।');
                    console.error(xhr.responseText);
                }
            });
        }

        // Secondary modal preview (self)
        const docPreview1 = document.getElementById("docPreview1");
        const imgPreview1 = document.getElementById("imgPreview1");

        document.querySelectorAll(".myImg1").forEach(function(link) {
            link.addEventListener("click", function() {
                const docUrl = this.getAttribute("data-doc");

                if (docUrl.match(/\.(jpeg|jpg|png|gif)$/)) {
                    imgPreview1.src = docUrl;
                    imgPreview1.style.display = "block";
                    docPreview1.style.display = "none";
                } else {
                    docPreview1.src = docUrl;
                    docPreview1.style.display = "block";
                    imgPreview1.style.display = "none";
                }
            });
        });
    </script>
@endsection
