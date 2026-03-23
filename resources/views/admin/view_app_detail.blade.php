@extends('layouts.dahboard_layout')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

            #submit-file-btn {
                margin-top: 10px;

            }
        }
    </style>
@endsection
@section('body-page')

    <main id="main" class="main">
        <div class="row printable-div">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card container" id="printable-div">
                    <div class="wrapper wrapper-content animated fadeInRight">
                        <div class="ibox-content p-xl">
                            <div class="row">
                                <div class="col-md-4 mt-2">
                                    @if ($applicant_details->is_final_submit == 1)
                                        <a href="{{ url('/candidate/All-documents/' . md5($applicant_details->apply_id)) }}"
                                            class="btn btn-sm btn-outline-primary ">दस्तावेज
                                            देखें</a>
                                    @else
                                        <a href="{{ url('/candidate/view-documents/' . md5($applicant_details->apply_id)) }}"
                                            class="btn btn-sm btn-outline-primary m-1">दस्तावेज
                                            देखें</a>
                                    @endif

                                </div>
                            </div>
                            <hr>
                            @php
                                $file_path = asset('uploads') . '/';
                                if (config('app.env') == 'production') {
                                    $file_path = config('custom.file_point');
                                }
                            @endphp
                            <div class="row">
                                <input type="hidden" id="user_id" value="{{ $applicant_details->Applicant_ID }}">
                                <input type="hidden" id="post_id" value="{{ $applicant_details->fk_post_id }}">
                                {{-- {{dd($applicant_details->fk_post_id);}} --}}
                                <input type="hidden" id="Encrypted_Apllication_id"
                                    value="{{ $applicant_details->apply_id }}">
                                <div class="col-md-12">
                                    <table class="table table-responsive">
                                        <tr>
                                            <td>
                                                प्रति,<br>
                                                संचालक,<br>
                                                संचालनालय महिला एवं बाल विकास विभाग,
                                                <br>अटल नगर, नया रायपुर, (छ.ग.)
                                            </td>
                                            <td align="right">
                                                <img src="{{ $file_path . $applicant_details->Document_Photo }}"
                                                    width="160" height="130"><br>
                                                <img src="{{ $file_path . $applicant_details->Document_Sign }}"
                                                    width="160" height="80">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <h5 class="text-primary"> व्यक्तिगत जानकारी</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-responsive">
                                        <tr>
                                            <td>1.आवेदन संख्या</td>
                                            <td>{{ $applicant_details->application_num }}</td>
                                        </tr>
                                        <tr>
                                            <td>2. आवेदित पद का नाम</td>
                                            <td>{{ $applicant_details->title }}</td>
                                        </tr>
                                        <tr>
                                            <td>3. आवेदित स्थान का विवरण </td>
                                            <td>
                                                @if ($postArea_Check == 1)
                                                    {{ $applicant_details->village_name_hin }} ,
                                                    {{ $applicant_details->panchayat_name_hin }} ,
                                                    {{ $applicant_details->block_name_hin }} ,
                                                @elseif($postArea_Check == 2)
                                                    {{ $applicant_details->ward_name }} ,
                                                    {{ $applicant_details->nnn_name }} ,
                                                @endif

                                                {{ $applicant_details->Dist_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4. आवेदन की तिथि</td>
                                            <td>{{ date('d-m-Y', strtotime($applicant_details->apply_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>5. आवेदनकर्ता का पूरा नाम</td>
                                            <td>{{ $applicant_details->First_Name }}&nbsp;{{ $applicant_details->Middle_Name }}&nbsp;{{ $applicant_details->Last_Name }}
                                                ({{ $applicant_details->firstName_hindi }}&nbsp;{{ $applicant_details->middleName_hindi }}&nbsp;{{ $applicant_details->lastName_hindi }})
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>6. पिता/पति का नाम</td>
                                            <td>{{ $applicant_details->FatherName }}</td>
                                        </tr>
                                        <tr>
                                            <td>7. स्थायी पता</td>
                                            <td>{{ $applicant_details->Corr_Address }}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td>7. स्थायी पता</td>
                                            <td>{{ $applicant_details->Perm_Address }}</td>
                                        </tr> --}}
                                        <tr>
                                            <td>8. जन्मतिथि</td>
                                            <td>{{ date('d-m-Y', strtotime($applicant_details->DOB)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>9. मोबाइल नंबर</td>
                                            <td>{{ $applicant_details->Contact_Number }}</td>
                                        </tr>

                                        {{-- <tr>
                                            <td>11. कौशल</td>
                                            <td>
                                                @if (!empty($Skill_Ans) && count($Skill_Ans) > 0)
                                                    @foreach ($Skill_Ans as $key => $skill)
                                                        <span>
                                                            {{ $loop->iteration }}. {{ $skill->SkillName }}
                                                            @php
                                                                $decodedOptions = json_decode(
                                                                    $skill->SkillAnswer,
                                                                    true,
                                                                );
                                                            @endphp
                                                            @if (is_array($decodedOptions))
                                                                ({{ implode(', ', $decodedOptions) }})
                                                            @endif
                                                            @if (!$loop->last)
                                                                <br>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span> कौशल अनिवार्य नहीं है</span>
                                                @endif
                                            </td>

                                        </tr> --}}


                                        @if (!empty($Post_questionAnswer) && count($Post_questionAnswer) > 0)
                                            @php $iteration = 10; @endphp
                                            @foreach ($Post_questionAnswer as $questionAnswer)
                                                <tr>
                                                    <td>{{ $iteration }}.
                                                        {{ $questionAnswer->ques_name ?? 'प्रश्न नहीं मिले' }}</td>
                                                    <td>
                                                        @if (
                                                            ($questionAnswer->ques_ID == 7 || $questionAnswer->ans_type == 'FD' || $questionAnswer->ans_type == 'D') &&
                                                                !empty($questionAnswer->answer_list))
                                                            {{ date('d-m-Y', strtotime($questionAnswer->answer_list)) }}
                                                        @elseif ($questionAnswer->ans_type == 'M' && !empty($questionAnswer->answer_list))
                                                            {{-- Multiple answers with numbering + comma --}}
                                                            @php
                                                                $answers = array_map(
                                                                    'trim',
                                                                    explode(',', $questionAnswer->answer_list),
                                                                );
                                                                $numberedAnswers = [];
                                                                foreach ($answers as $i => $ans) {
                                                                    $numberedAnswers[] = $i + 1 . '. ' . $ans;
                                                                }
                                                                echo implode(', ', $numberedAnswers);
                                                            @endphp
                                                        @else
                                                            {{-- Normal single answer --}}
                                                            {{ $questionAnswer->answer_list ?? 'प्रश्न का उत्तर नहीं मिला' }}
                                                            @if ($questionAnswer->date_From && $questionAnswer->date_To)
                                                                ({{ date('d-m-Y', strtotime($questionAnswer->date_From)) }}
                                                                से
                                                                {{ date('d-m-Y', strtotime($questionAnswer->date_To)) }})
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php $iteration++; @endphp
                                            @endforeach
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <h5 class="text-primary"> शैक्षणिक योग्यता </h5>
                            <div class="row feature-section">
                                <div class="col-md-12 ">

                                    <table class="table table-responsive table-bordered">
                                        <tr>
                                            <th>क्र. सं.</th>
                                            <th>योग्यता</th>
                                            <th>बोर्ड/विश्वविद्यालय का नाम</th>
                                            <th>उत्तीर्ण वर्ष</th>
                                            <th>प्राप्त अंक</th>
                                            <th>कुल अंक</th>
                                            <th>प्रतिशत</th>
                                        </tr>
                                        @if (@$education_details)
                                            @foreach ($education_details as $education)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $education->Quali_Name }}</td>
                                                    <td>{{ $education->qualification_board }}</td>
                                                    <td>{{ $education->year_passing }}</td>
                                                    <td>{{ $education->obtained_marks }}</td>
                                                    <td>{{ $education->total_marks }}</td>
                                                    <td>{{ $education->percentage }} %</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <hr>

                            @if (!empty($experience_details) && $experience_details->isNotEmpty())
                                <h5 class="text-primary">अनुभव एवं अन्य जानकारी</h5>
                                <div class="row feature-section">
                                    <div class="col-md-12">
                                        <table class="table table-responsive table-bordered">
                                            <tr>
                                                <th>संस्था का नाम</th>
                                                <th>संस्था का प्रकार</th>
                                                <th>पदनाम</th>
                                                <th>अनुभव का कार्यक्षेत्र</th>
                                                <th>कुल अनुभव</th>
                                            </tr>
                                            @foreach ($experience_details as $experience)
                                                <tr>
                                                    <td>{{ $experience->Organization_Name }}</td>
                                                    {{-- <td>{{ $experience->Organization_Type }}</td> --}}
                                                    <td>
                                                        {{ $experience->Organization_Type == 1
                                                            ? 'शासकीय'
                                                            : ($experience->Organization_Type == 2
                                                                ? 'अशासकीय'
                                                                : ($experience->Organization_Type == 3
                                                                    ? 'अर्ध-शासकीय'
                                                                    : ($experience->Organization_Type == 4
                                                                        ? 'अन्य / किसी भी क्षेत्र में'
                                                                        : 'N/A'))) }}
                                                    </td>
                                                    <td>{{ $experience->Designation }}</td>
                                                    <td>{{ $experience->Nature_Of_Work }}</td>
                                                    <td>{{ $experience->Total_Experience }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif


                            @if (Session::get('sess_role') === 'Candidate')
                                @if ($applicant_details->is_final_submit == 1)
                                    <div class="row">
                                        {{-- <div class="col-md-12 mt-4 mb-4">
                                            <span class="mb-3 text-danger"> नोट: इस फॉर्म को प्रिंट करके, उस पर अपनी
                                                स्वप्रमाणित फोटो
                                                चिपकाएँ और सभी पेज में
                                                हस्ताक्षर करें, फिर उसे स्कैन करके अपलोड करें। इसके बाद ही आपका एप्लीकेशन
                                                मान्य होगा |</span>
                                        </div>
                                        <a href="https://www.ilovepdf.com/compress_pdf" target="_blank" class="mb-3 ">
                                            PDF फाइल का साइज कम करने के लिए दिए गए लिंक पर क्लिक करें।
                                        </a> --}}
                                        <div class="col-md-2 mt-4 mb-4">
                                            @php
                                                $hashed_rowid = md5($applicant_details->RowID);
                                                $hashed_apply_id = md5($applicant_details->apply_id);
                                            @endphp
                                            <button type="button"
                                                onclick="printApplication('{{ $hashed_rowid }}','{{ $hashed_apply_id }}')"
                                                class="btn btn-primary">
                                                <i class="bi bi-printer"></i> प्रिंट करें
                                            </button>
                                        </div>
                                        {{-- <div class="col-md-10 mt-4 mb-4">
                                            <!-- Form with File Input and Submit Button -->
                                            <form id="self_attested_form" enctype="multipart/form-data">
                                                <input type="hidden" value="{{ $applicant_details->RowID }}"
                                                    name="applicant_id">
                                                <input type="hidden" value="{{ $applicant_details->apply_id }}"
                                                    name="application_id">
                                                <input type="hidden" value="{{ md5($applicant_details->RowID) }}"
                                                    name="applicant_id_file">
                                                <input type="hidden" value="{{ md5($applicant_details->apply_id) }}"
                                                    name="application_id_file">
                                                <div class="row">
                                                    <div class="col-md-6"><input type="file" name="self_attested_file"
                                                            class="form-control documentInput" id="self_attested_file"
                                                            accept=".pdf" required>
                                                        <span class="file-preview-link"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="submit" id="submit-file-btn"
                                                            class="btn btn-primary btn-md"><i class="bi bi-file-check"></i>
                                                            सबमिट करें</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div> --}}
                                    </div>
                                @else
                                    <div class="col-md-12 col-sm-12 col-xs-12 m-3">
                                        <div class="d-flex gap-3 flex-wrap align-items-center">
                                            <form id="myForm" class="d-flex align-items-center gap-2 flex-wrap">
                                                @csrf
                                                <input type="hidden" name="RowID" id="RowID"
                                                    value="{{ $applicant_details->RowID }}">
                                                <input type="hidden" name="apply_id" id="apply_id"
                                                    value="{{ $applicant_details->apply_id }}">

                                                <input type="hidden" name="encrypted_rowid" id="encrypted_rowid"
                                                    value="{{ md5($applicant_details->RowID) }}">
                                                <input type="hidden" name="encrypted_Apply_id" id="encrypted_Apply_id"
                                                    value="{{ md5($applicant_details->apply_id) }}">

                                                <div class="form-check d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input border border-primary"
                                                        id="confirmationCheckbox" required>
                                                    <label class="form-check-label ms-2" for="confirmationCheckbox">
                                                        मै यह घोषणा करता/करती हूँ कि ऊपर दी गयी जानकारी सही है |
                                                    </label>
                                                </div> <br>

                                                <button type="submit" id="finalSubmitBtn"
                                                    class="btn btn-primary btn-sm">सबमिट करें और प्रिंट
                                                    करें</button>
                                            </form>

                                            <form id="EditForm">
                                                @csrf
                                                <input type="hidden" id="Candidate_id"
                                                    value="{{ md5($applicant_details->RowID) }}">
                                                <input type="hidden" id="Apply_id"
                                                    value="{{ md5($applicant_details->apply_id) }}">
                                                <button type="button" id="editButton" class="btn btn-sm btn-success">विवरण
                                                    संपादित/जाँच करें</button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="documentModal" tabindex="-1" data-bs-backdrop="static"
                            data-bs-keyboard="false" aria-labelledby="documentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="documentModalLabel">दस्तावेज़ पूर्वावलोकन
                                        </h5>
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
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        // Define the function that constructs the URL and calls printExternalPage
        function printApplication(applicant_id, application_id) {

            var pageUrl = '/candidate/print-application/' + applicant_id + '/' + application_id;
            console.log("Redirecting to:", pageUrl);
            printExternalPage(pageUrl);
        }


        // Function to print the external page
        function printExternalPage(pageUrl) {
            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Define the request
            xhr.open('GET', pageUrl, true);

            // Set up a callback function to handle the response
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Create a new temporary container element
                    var tempContainer = document.createElement('div');

                    // Insert the fetched content into the container
                    tempContainer.innerHTML = xhr.responseText;

                    // Get the content to print
                    var printContents = tempContainer.innerHTML;

                    // Save the original content of the page
                    var originalContents = document.body.innerHTML;

                    // Replace the current content of the page with the fetched content
                    document.body.innerHTML = printContents;

                    // Print the fetched content
                    window.print();

                    // Restore the original content of the page after printing
                    document.body.innerHTML = originalContents;

                    // Reload the page
                    location.reload();
                } else {
                    // Handle the error if the request fails
                    console.error('Request failed with status: ' + xhr.status);
                }
            };

            // Send the request
            xhr.send();
        }

        $(document).ready(function() {

            // Form submit event handler - must be outside of any function
            $('#myForm').on('submit', function(e) {
                $('#finalSubmitBtn').prop("disabled", true);
                $('#editButton').prop("disabled", true);
                e.preventDefault(); // Default form submission रोकें

                if ($('#confirmationCheckbox').is(':checked')) {
                    // Checkbox checked है, तो फ़ंक्शन कॉल करें
                    updateDataAndPrint();
                } else {
                    // Checkbox checked नहीं है, तो SweetAlert दिखाएं
                    Swal.fire({
                        title: "कृपया पुष्टि करें!",
                        text: "कृपया पुष्टि करें कि दी गई जानकारी सही है।",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Confirm",
                        cancelButtonText: "Cancel",
                        allowOutsideClick: false
                    }).then((result) => {
                        $('#finalSubmitBtn').prop("disabled", false);
                        $('#editButton').prop("disabled", false);
                    });
                }
            });

            //## show modal for view selected and previous documents
            const $fileInputs = $('.documentInput');
            $fileInputs.on('change', function(e) {
                const file = e.target.files[0];
                const previewLinkSpan = $(this).next('span')[0];

                if (file) {
                    const fileURL = URL.createObjectURL(file);

                    $(previewLinkSpan).html(
                        `<a href="#" class="previewDoc btn btn-sm text-primary me-3" data-file="${fileURL}" data-type="${file.type}" style="text-decoration:none;font-size:14px;">View Selected <i class="bi bi-check-circle-fill text-success"></i></a>`
                    );

                    $(previewLinkSpan).find('.previewDoc').on('click', function(e) {
                        e.preventDefault();
                        showFileInModal(fileURL, file.type);
                    });
                } else {
                    $(previewLinkSpan).empty();
                }
            });

            $('.myImg').on('click', function(e) {
                e.preventDefault();
                const fileURL = $(this).attr('href');
                const fileType = fileURL.endsWith('.pdf') ? 'application/pdf' : 'image/*';
                showFileInModal(fileURL, fileType);
            });


            function showFileInModal(fileURL, fileType) {
                const modal = new bootstrap.Modal(document.getElementById('documentModal'));
                const $docPreview = $('#docPreview');
                const $imgPreview = $('#imgPreview');

                $docPreview.hide();
                $imgPreview.hide();

                if (fileType === 'application/pdf') {
                    $docPreview.attr('src', fileURL);
                    $docPreview.show();
                } else if (fileType.startsWith('image/')) {
                    $imgPreview.attr('src', fileURL);
                    $imgPreview.show();
                }

                modal.show();
            }


            $('#editButton').click(function() {
                var Candidate_id = $('#Candidate_id').val();
                var Apply_id = $('#Apply_id').val();
                // alert("Candidate_id: " + Candidate_id + ", Apply_id: " + Apply_id);
                $.ajax({
                    url: `/candidate/user-details-update/${Candidate_id}/${Apply_id}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#submit').attr('disabled', true);
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {

                        window.location.href =
                            `/candidate/user-details-update/${Candidate_id}/${Apply_id}`;

                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Something went wrong! Please try again.'
                        });
                    },
                    complete: function() {
                        $('#submit').attr('disabled', false);
                        Swal.close();
                    }
                });
            });

            // Self attested form submit event handler
            $('#self_attested_form').on('submit', function(e) {
                $("#submit-file-btn").attr("disabled", true);
                e.preventDefault();

                let fileInput = $('#self_attested_file')[0];
                let file = fileInput.files[0];

                // Check if a file is selected
                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'फ़ाइल चुनें',
                        text: 'कृपया एक फ़ाइल अपलोड करें।'
                    }).then(() => {
                        $("#submit-file-btn").attr("disabled", false);
                    });
                    return;

                }

                // 2. Validate file size (max 2MB)
                if (file.size > 1024 * 1024 * 2) {
                    Swal.fire({
                        icon: 'warning',
                        // title: 'फ़ाइल का आकार 2MB से अधिक है',
                        text: 'फ़ाइल का आकार 2MB से अधिक है, कृपया 2MB से कम आकार की PDF फ़ाइल अपलोड करें।'
                    }).then(() => {
                        $("#submit-file-btn").attr("disabled", false);
                    });
                    return;
                }

                // 3. Validate file type (only PDF)
                const allowedTypes = ['application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'फ़ाइल प्रकार अमान्य है',
                        text: 'केवल PDF फ़ाइल स्वीकार की जाती है।'
                    }).then(() => {
                        $("#submit-file-btn").attr("disabled", false);
                    });
                    return;
                }
                let formData = new FormData(this);

                $.ajax({
                    url: '/candidate/save-self-attested',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#submit-file-btn").attr("disabled", true);
                        // Show a loading spinner or message if needed
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.status == 'success') {

                            var applicant_id = response.applicant_id;
                            var application_id = response.application_id;
                            Swal.fire({
                                icon: 'success',
                                title: 'सफलता!',
                                text: 'फ़ाइल सफलतापूर्वक अपलोड हो गई।',
                                confirmButtonText: 'ठीक है'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Log to verify
                                    window.location.href =
                                        '/candidate/final-application-detail/' +
                                        applicant_id + '/' + application_id;
                                }
                            });

                        } else {

                            Swal.fire({
                                icon: 'warning',
                                text: response['message'],
                                allowOutsideClick: false
                            });
                            $("#submit-file-btn").attr("disabled", false);

                        }


                    },
                    error: function(xhr, status, error) {
                        Swal.close();

                        let response;

                        // Safe JSON parsing
                        try {
                            response = xhr.responseJSON || JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = {
                                message: 'फ़ाइल अपलोड करने में समस्या आई।',
                                errors: {}
                            };
                        }

                        // Base error message
                        let errorMessages = response.message ||
                            'फ़ाइल अपलोड करने में समस्या आई।';

                        // If validation errors exist → show them in list
                        if (response.errors) {
                            errorMessages += '<br><ul>';

                            $.each(response.errors, function(key, value) {
                                if (Array.isArray(value) && value.length > 0) {
                                    errorMessages += `<li>${value[0]}</li>`;
                                }

                                // highlight field if exists
                                var element = $(`[name="${key}"]`);
                                if (element.length) {
                                    element.addClass('is-invalid');
                                    element
                                        .closest('.form-group')
                                        .find('.invalid-feedback')
                                        .html(value[0] || '');
                                }
                            });

                            errorMessages += '</ul>';
                        }

                        // Final Swal message (with list + message)
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            html: errorMessages,
                            confirmButtonText: 'ठीक है',
                            allowOutsideClick: false
                        });

                        // Enable button again
                        $("#submit-file-btn").attr("disabled", false);

                        console.error(xhr.responseText);
                    }

                });
            });

            // Function to update data and print
            function updateDataAndPrint() {
                var rowid = document.getElementById('RowID').value;
                var Apply_id = document.getElementById('apply_id').value;
                var encrypted_rowid = document.getElementById('encrypted_rowid').value;
                var encrypted_Apply_id = document.getElementById('encrypted_Apply_id').value;

                console.log("RowID:", rowid, "Apply_id:", Apply_id, "Encrypted RowID:", encrypted_rowid,
                    "Encrypted Apply_id:", encrypted_Apply_id);

                // AJAX request to update data in the controller
                $.ajax({
                    url: "{{ url('/candidate/final-submit') }}",
                    method: 'POST',
                    data: {
                        RowID: rowid,
                        apply_id: Apply_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'डेटा सफलतापूर्वक सबमिट किया गया।',
                                confirmButtonText: "OK",
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    printApplication(encrypted_rowid, encrypted_Apply_id);
                                }
                            });
                        } else if (response.status == 'error') {
                            Swal.fire({
                                icon: response.status,
                                text: response.message,
                                confirmButtonText: "OK",
                                allowOutsideClick: false
                            }).then((result) => {
                                $('#finalSubmitBtn').prop('disabled', false);
                                $('#editButton').prop('disabled', false);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Error handling
                        console.error('Error updating data:', error);
                        $('#finalSubmitBtn').prop('disabled', false);
                        $('#editButton').prop('disabled', false);
                    }
                });
            }

        });
    </script>
@endsection
