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

                                            @php
    $role = Session::get('sess_role');
    $url = '#';

    if ($role === 'Super_admin' || $role === 'Admin') {
        $url = url('/admin/view-docs/' . md5($applicant_details->fk_apply_id));
    } elseif ($role === 'Candidate') {
        $url = url(
            '/candidate/All-documents/' . md5($applicant_details->fk_apply_id),
        );
    }
                                            @endphp

                                            @if ($role === 'Super_admin' || $role === 'Admin' || $role === 'Candidate')
                                                <a href="{{ $url }}" class="btn btn-sm btn-outline-primary m-1">दस्तावेज
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
                                        <input type="hidden" id="user_id" value="{{ $applicant_details->user_row_id }}">
                                        <input type="hidden" id="post_id" value="{{ $applicant_details->fk_post_id }}">
                                        {{-- {{dd($applicant_details->fk_post_id);}} --}}
                                        <input type="hidden" id="Encrypted_Apllication_id"
                                            value="{{ $applicant_details->apply_id }}">
                                        <input type="hidden" id="Encrypted_Apllication_id_redirrect"
                                            value="{{ MD5($applicant_details->apply_id) }}">
                                        @if (Session::get('sess_role') === 'Candidate')
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
                                                            <img src="{{ $file_path . $applicant_details->Document_Photo }}" width="160"
                                                                height="130"><br>
                                                            <img src="{{ $file_path . $applicant_details->Document_Sign }}" width="160"
                                                                height="80">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <h5 class="text-primary text-primary"> व्यक्तिगत जानकारी</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-responsive">
                                                <tr>
                                                    <td style="min-width: 400px">1.आवेदन संख्या</td>
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
                                    <div class="row  feature-section">
                                        <div class="col-md-12 table-responsive">

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
                                            <div class="col-md-12 table-responsive">
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

                                    @if (Session::get('sess_role') === 'Super_admin' || Session::get('sess_role') === 'Admin')
                                        {{-- @if (request()->get('type') === 'pending')
                                        @if ($marks_details->walk_in_interview_mark == null && $marks_details->skill_test_mark == null)
                                        <h5 class="text-primary">अंकों का विवरण</h5>
                                        <div class="row feature-section">
                                            <div class="col-md-12 table-responsive">
                                                <table class="table table-responsive table-bordered">
                                                    <tr>
                                                        <th>अनुभव अंक</th>
                                                        <th>शैक्षिक योग्यता अंक</th>
                                                        <th>वॉक-इन इंटरव्यू</th>
                                                        <th>कौशल परीक्षा अंक</th>
                                                        <th>कुल अंक</th>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $marks_details->min_edu_qualification_mark }}</td>
                                                        <td>{{ $marks_details->edu_qualification_mark }}</td>
                                                        <td>{{ $marks_details->walk_in_interview_mark }}</td>
                                                        <td>{{ $marks_details->skill_test_mark }}</td>
                                                        <td>{{ $marks_details->total_mark }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                        @endif --}}
                                        {{-- @if (request()->get('type') === 'fulfilled') --}}
                                        {{-- @if ($marks_details->walk_in_interview_mark != null && $marks_details->skill_test_mark !=
                                        null) --}}
                                        <h5 class="text-primary">अंकों का विवरण</h5>
                                        <div class="row feature-section">
                                            <div class="col-md-12 table-responsive">
                                                <table class="table table-responsive table-bordered">
                                                    {{-- {{dd($marks_details);}} --}}
                                                    <tr>
                                                        <th>जाति प्रमाण अंक</th>
                                                        <th>विधवा/परित्यक्ता/तलाकशुदा प्रश्न अंक</th>
                                                        <th>ग़रीबी रेखा प्रश्न अंक</th>
                                                        <th>कन्या आश्रम प्रश्न अंक</th>
                                                        {{-- <th>शैक्षिक योग्यता अंक</th> --}}
                                                        <th>न्यूनतम अनुभव अंक</th>
                                                        <th>न्यूनतम शैक्षिक योग्यता अंक</th>
                                                        <th>कुल अंक</th>
                                                    </tr>
                                                    {{-- @foreach ($marks_details as $marks) --}}
                                                    <tr>
                                                        <td>{{ $marks_details->domicile_mark }}</td>
                                                        <td>{{ $marks_details->v_p_t_questionMarks }}</td>
                                                        <td>{{ $marks_details->secc_questionMarks }}</td>
                                                        <td>{{ $marks_details->kan_ash_questionMarks }}</td>
                                                        {{-- <td>{{ $marks_details->edu_qualification_mark }}</td> --}}
                                                        <td>{{ $marks_details->min_experiance_mark }}</td>
                                                        <td>{{ $marks_details->min_edu_qualification_mark }}</td>
                                                        <td>{{ $marks_details->total_mark }}</td>
                                                    </tr>
                                                    {{-- @endforeach --}}
                                                </table>
                                            </div>
                                        </div>
                                        {{-- @endif --}}
                                        {{-- @endif --}}
                                    @endif
                                    {{-- {{dd($applicant_details);}} --}}
                                    <p class="d-none" id="row_id"><?= md5($applicant_details->RowID) ?></p>
                                    <p class="d-none" id="fk_apply_id"><?= md5($applicant_details->fk_apply_id) ?></p>
                                    @if (
        Session::get('sess_role') === 'Candidate' ||
        Session::get('sess_role') === 'Super_admin' ||
        Session::get('sess_role') === 'Admin'
    )

                                        {{-- Jab Candidate hai --}}
                                        @if (Session::get('sess_role') === 'Candidate')
                                            @if ($applicant_details->is_final_submit == 1)
                                                <button type="button"
                                                    onclick="printApplication('{{ md5($applicant_details->RowID) }}', '{{ md5($applicant_details->fk_apply_id) }}')"
                                                    class="btn btn-primary btn-md mt-3"> <i class="bi bi-printer"></i> प्रिंट करें
                                                </button>
                                                {{-- <button type="button" id="candidate_print" class="btn btn-primary btn-md mt-3"> <i
                                                        class="bi bi-printer"></i> प्रिंट करें
                                                </button> --}}
                                                <br><br>
                                            @else
                                                {{-- Candidate ne final submit nahi kiya hai to Submit aur Edit form dikhana --}}
                                                <div class="col-md-12 col-sm-12 col-xs-12 m-5">
                                                    <div class="d-flex gap-3 flex-wrap align-items-center">
                                                        <form id="myForm" class="d-flex align-items-center gap-2 flex-wrap">
                                                            @csrf
                                                            <input type="hidden" name="RowID" id="RowID"
                                                                value="{{ $applicant_details->RowID }}">
                                                            <input type="hidden" name="apply_id" id="apply_id"
                                                                value="{{ $applicant_details->fk_apply_id }}">
                                                            <input type="hidden" name="encrypted_rowid" id="encrypted_rowid"
                                                                value="{{ md5($applicant_details->RowID) }}">
                                                            <input type="hidden" name="encrypted_Apply_id" id="encrypted_Apply_id"
                                                                value="{{ md5($applicant_details->fk_apply_id) }}">

                                                            <div class="form-check d-flex align-items-center">
                                                                <input type="checkbox" class="form-check-input" id="confirmationCheckbox"
                                                                    required>
                                                                <label class="form-check-label ms-2" for="confirmationCheckbox">
                                                                    मै यह घोषणा करता/करती हूँ कि ऊपर दी गयी जानकारी सही है |
                                                                </label>
                                                            </div> <br>

                                                            <button type="submit" class="btn btn-primary btn-sm">सबमिट करें और
                                                                प्रिंट करें</button>
                                                        </form>

                                                        <form id="EditForm">
                                                            @csrf
                                                            <input type="hidden" id="Candidate_id" value="{{ md5($applicant_details->RowID) }}">
                                                            <button type="button" id="editButton" class="btn btn-success btn-sm mt-2">विवरण
                                                                संपादित करें</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Jab Super_admin ya Admin hai --}}
                                        @elseif (Session::get('sess_role') === 'Super_admin' || Session::get('sess_role') === 'Admin')
                                            @if ($applicant_details->status == 'Verified' || $applicant_details->status == 'Rejected')
                                                {{-- Agar status Verified hai tabhi Print button allow --}}

                                                {{-- @php
                                                $post_file_path = asset(
                                                'uploads/' . $applicant_details->self_attested_file,
                                                );
                                                if (config('app.env') === 'production') {
                                                $post_file_path =
                                                config('custom.file_point') .
                                                $applicant_details->self_attested_file;
                                                }
                                                @endphp --}}

                                                @if (
                    // !empty($applicant_details->self_attested_file) &&
                    request()->get('type') !== 'pending' && request()->get('type') !== 'fulfilled'
                )
                                                    {{-- <a href="{{ $post_file_path }}" target="_blank" class="btn btn-primary btn-sm">
                                                        स्वप्रमाणित फ़ाइल देखें
                                                    </a><br> --}}
                                                    @if ($applicant_details->status == 'Rejected')
                                                        <p class="badge bg-danger mt-2">{{ $applicant_details->status }}</p>
                                                        <span class="text-danger">-
                                                            {{ $applicant_details->reason_rejection }}</span>
                                                    @elseif($applicant_details->status == 'Verified')
                                                        <p class="badge bg-success mt-2">{{ $applicant_details->status }}</p>
                                                        <br>
                                                    @endif
                                                @else
                                                    <button type="button" id="admin_print" class="btn btn-primary btn-sm"><i
                                                            class="bi bi-printer"></i> प्रिंट
                                                        करें</button><br>
                                                    @if ($applicant_details->status == 'Rejected')
                                                        <p class="badge bg-danger mt-2">{{ $applicant_details->status }}</p>
                                                        <span class="text-danger">-
                                                            {{ $applicant_details->reason_rejection }}</span>
                                                    @elseif($applicant_details->status == 'Verified')
                                                        <p class="badge bg-success mt-2">{{ $applicant_details->status }}</p>
                                                        <br>
                                                    @endif
                                                @endif
                                            @endif
                                        @endif

                                    @endif

                                    {{-- @else --}}

                                    {{-- {{dd(Session::get('sess_role'));}} --}}
                                    @if ($applicant_details->status === 'Submitted' && Session::get('sess_role') == 'Super_admin' && Session::get('district_id'))
                                        <hr>
                                        <div class="row"
                                            style=" border-radius: 20px; margin-left: auto; margin-right: auto; margin-bottom: 50px;">
                                            <div class="col-md">
                                                <form name="myform" method="post">
                                                    <style>
                                                        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

                                                        .row.form-group.formField {
                                                            background: linear-gradient(135deg, #e0eafc, #cfdef3);

                                                            /* max-width: 800px; */
                                                            margin: 20px auto;
                                                            padding: 40px;
                                                            background: linear-gradient(145deg, #ffffff, #f0f4ff);
                                                            /* Subtle container gradient */
                                                            border-radius: 20px;
                                                            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                                                            /* Stronger, modern shadow */
                                                            display: flex;
                                                            flex-direction: column;
                                                            justify-content: center;
                                                            align-items: center;
                                                            transition: transform 0.3s ease;
                                                        }


                                                        .col-md-5.col-xs {
                                                            display: flex;
                                                            justify-content: center;
                                                            align-items: center;
                                                            width: 100%;
                                                            max-width: 600px;
                                                            margin-bottom: 30px;
                                                        }

                                                        .col-md-5.col-xs label {
                                                            font-size: 18px;
                                                            /* Slightly larger for impact */
                                                            color: #2c3e50;
                                                            /* Darker, modern color */
                                                            margin-right: 15px;
                                                            font-weight: 600;
                                                            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
                                                            /* Subtle text shadow */
                                                        }

                                                        .radio-inline {
                                                            display: inline-flex;
                                                            align-items: center;
                                                            margin-left: 30px;
                                                            font-size: 16px;
                                                            color: #34495e;
                                                            font-weight: 500;
                                                            transition: color 0.3s ease;
                                                        }

                                                        .radio-inline input[type="radio"] {
                                                            margin-right: 8px;
                                                            transform: scale(1.4);
                                                            /* Larger, bolder radio buttons */
                                                            accent-color: #007bff;
                                                            /* Vibrant blue for radio */
                                                            cursor: pointer;
                                                        }

                                                        .radio-inline:hover {
                                                            color: #007bff;
                                                            /* Blue tint on hover */
                                                        }

                                                        label[for="eligibility"] {
                                                            font-weight: 700;
                                                            /* Bolder for main label */
                                                        }

                                                        label[style*="color:red"] {
                                                            color: #e74c3c;
                                                            /* Vibrant red for asterisk */
                                                            margin-left: 8px;
                                                            font-size: 18px;
                                                            font-weight: 600;
                                                        }

                                                        .col-md-6 {
                                                            width: 100%;
                                                            max-width: 600px;
                                                        }

                                                        .col-md-6 label[for="reason"] {
                                                            font-size: 18px;
                                                            color: #2c3e50;
                                                            display: block;
                                                            margin-bottom: 8px;
                                                            font-weight: 600;
                                                        }

                                                        .form-control {
                                                            width: 100%;
                                                            padding: 15px;
                                                            border: 2px solid #dfe6e9;
                                                            /* Thicker, softer border */
                                                            border-radius: 10px;
                                                            font-size: 16px;
                                                            resize: vertical;
                                                            background: #f9fbfd;
                                                            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
                                                            /* display: none; */
                                                            transition: all 0.3s ease;
                                                            /* Smooth appearance transition */
                                                        }

                                                        .form-control:focus {
                                                            border-color: #007bff;
                                                            outline: none;
                                                            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
                                                            /* Glow on focus */
                                                        }

                                                        .approve-reject-btn {
                                                            background: linear-gradient(90deg, #007bff, #00d4ff);
                                                            color: #fff;
                                                            border: none;
                                                            border-radius: 12px;
                                                            font-size: 16px;
                                                            font-weight: 600;
                                                            cursor: pointer;
                                                            transition: all 0.3s ease;
                                                            margin-bottom: 15px;
                                                            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
                                                            display: block;
                                                        }


                                                        .approve-reject-btn:hover {
                                                            background: linear-gradient(90deg, #0056b3, #00aaff);
                                                            transform: translateY(-2px);
                                                            /* Lift on hover */
                                                            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.5);
                                                        }

                                                        .approve-reject-btn:active {
                                                            transform: translateY(0);
                                                            /* Press down effect */
                                                            box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
                                                        }

                                                        @media (max-width: 600px) {
                                                            .row.form-group.formField {
                                                                margin: 15px;
                                                                padding: 25px;
                                                                border-radius: 15px;
                                                            }

                                                            .col-md-5.col-xs {
                                                                flex-direction: column;
                                                                align-items: flex-start;
                                                            }

                                                            .radio-inline {
                                                                margin-left: 0;
                                                                margin-top: 15px;
                                                                font-size: 18px;
                                                            }

                                                            .radio-inline input[type="radio"] {
                                                                transform: scale(1.3);
                                                            }

                                                            .col-md-5.col-xs label,
                                                            .col-md-6 label[for="reason"],
                                                            label[style*="color:red"] {
                                                                font-size: 18px;
                                                            }

                                                            .form-control {
                                                                font-size: 14px;
                                                                padding: 12px;
                                                            }

                                                            .approve-reject-btn {
                                                                font-size: 16px;
                                                                padding: 15px;
                                                            }
                                                        }
                                                    </style>
                                                    <div class="Eligiblediv">
                                                        <div
                                                            class="row form-group formField d-flex flex-column justify-content-center align-items-center">
                                                            <!-- Checkbox section -->
                                                            <div
                                                                class="col-md-5 col-xs d-flex justify-content-center align-items-center w-50 float-left">
                                                                <label for="eligibility">पात्र/अपात्र </label><label
                                                                    style="color:red">*</label>
                                                                <label class="radio-inline" style="margin-left: 30px;">
                                                                    <input id="eligibility" type="radio" name="verify" value="Approve"
                                                                        checked> पात्र
                                                                </label>
                                                                <label class="radio-inline" style="margin-left: 20px;">
                                                                    <input type="radio" id="eligibilityReject" name="verify"
                                                                        value="Reject"> अपात्र
                                                                </label>
                                                            </div>

                                                            <!-- Remark section -->
                                                            <div class="col-md-6 reasonSection">
                                                                <label for="reason">अपात्र होने का कारण</label>
                                                                <textarea class="form-control" name="reason" id="reason"
                                                                    placeholder="कारण लिखें"></textarea>
                                                            </div>

                                                            <!-- Button section with full width -->
                                                            <div class="col-md-12 d-flex justify-content-right align-items-right mt-3 ">
                                                                <div class="col-md-6"></div>
                                                                <div class="col-md-4">
                                                                    <input type="button" value=" सबमिट करें "
                                                                        class="btn btn-lg btn-primary approve-reject-btn w-50">
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main><!-- End #main -->
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function printApplication(applicant_id, application_id, type = null) {
            var pageUrl = '/candidate/print-application/' + applicant_id + '/' + application_id;

            if (type) {
                pageUrl += '?type=' + type;
            }

            printExternalPage(pageUrl);
        }

        function printExternalPage(pageUrl) {
            Swal.fire({
                title: 'प्रतीक्षा करे...',
                allowOutsideClick: false,
                position: 'center',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            var xhr = new XMLHttpRequest();
            xhr.open('GET', pageUrl, true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    var tempContainer = document.createElement('div');
                    tempContainer.innerHTML = xhr.responseText;
                    var printContents = tempContainer.innerHTML;
                    var originalContents = document.body.innerHTML;
                    document.body.innerHTML = printContents;
                    setTimeout(function () {
                        // Close loader
                        Swal.close();
                        window.print();
                        document.body.innerHTML = originalContents;
                        location.reload();
                    }, 500);
                } else {
                    console.error('Request failed with status: ' + xhr.status);
                }
            };

            xhr.send();
        }



        $(document).ready(function () {

            // Function to handle form submission
            function submitForm(event) {
                event.preventDefault();
                if (document.getElementById("confirmationCheckbox").checked) {
                    updateDataAndPrint();
                } else {
                    Swal.fire({
                        title: "कृपया पुष्टि करें!",
                        text: "कृपया पुष्टि करें कि दी गई जानकारी सही है।",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Confirm",
                        cancelButtonText: "Cancel",
                        allowOutsideClick: false
                    });
                }
            }

            $('#admin_print').click(function () {
                var row_id = $('#row_id').html().trim();
                var fk_apply_id = $('#fk_apply_id').html().trim();
                var type = "{{ request()->get('type') }}";

                console.log(row_id, fk_apply_id, type);

                if (type === 'pending' || type === 'fulfilled') {
                    printApplication(row_id, fk_apply_id, type);
                } else {
                    printApplication(row_id, fk_apply_id);
                }
            });
            $('#candidate_print').click(function () {
                var row_id = $('#row_id').html().trim();
                var fk_apply_id = $('#fk_apply_id').html().trim();

                printApplication(row_id, fk_apply_id);
            });



            function updateDataAndPrint() {
                var rowid = document.getElementById('RowID').value;
                var Apply_id = document.getElementById('apply_id').value;
                var encrypted_rowid = document.getElementById('encrypted_rowid').value;
                var encrypted_Apply_id = document.getElementById('encrypted_Apply_id').value;

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
                    success: function (response) {
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
                    },
                    error: function (xhr, status, error) {
                        console.error('Error updating data:', error);
                    }
                });
            }




            $('.approve-reject-btn').click(function (e) {
                e.preventDefault();
                var user_id = document.getElementById('user_id').value;
                var post_id = document.getElementById('post_id').value;
                var selectedValue = $("input[name='verify']:checked").val();
                var remark = document.getElementById('reason').value;

                if (selectedValue != "Reject" && selectedValue != "Approve") {
                    Swal.fire({
                        title: 'चयन आवश्यक!',
                        text: 'कृपया आवेदन की स्थिति चुनें।',
                        icon: 'warning',
                        confirmButtonText: 'ठीक है',
                        allowOutsideClick: false
                    });
                } else if (remark == "" && selectedValue == "Reject") {
                    Swal.fire({
                        title: 'कृपया टिप्पणी दर्ज करें!',
                        text: 'कृपया अपात्र के लिए टिप्पणी दर्ज करें।',
                        icon: 'warning',
                        confirmButtonText: 'ठीक है',
                        allowOutsideClick: false
                    });
                } else {
                    var button_status = selectedValue;
                    var error, btntext, error_hi, title, btnColor;
                    if (button_status == 'Approve') {
                        error = 'success';
                        btntext = 'पात्र';
                        error_hi = 'पात्र';
                        title = "क्या इस आवेदन को आप पात्र करना चाहते हैं ?";
                        btnColor = 'green';
                    } else {
                        error = 'error';
                        btntext = 'अपात्र';
                        error_hi = 'अपात्र';
                        title = "क्या इस आवेदन को आप अपात्र करना चाहते हैं ?";
                        btnColor = 'red';
                    }

                    showSwal();
                }

                function showSwal() {
                    Swal.fire({
                        icon: 'warning',
                        title: title,
                        showCancelButton: true,
                        confirmButtonColor: btnColor,
                        confirmButtonText: btntext,
                        cancelButtonText: 'रद्द करें',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.value) {
                            var rejectionReason = result.value;
                            let encryptedAppId = $('#Encrypted_Apllication_id').val();
                            let encryptedAppId_red = $('#Encrypted_Apllication_id_redirrect').val();

                            $.ajax({
                                type: "POST",
                                url: "{{ url('/') }}/admin/approve-reject-application",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                data: {
                                    user_id: user_id,
                                    button_status: button_status,
                                    remark: remark,
                                    encrypted_id: encryptedAppId,
                                    post_id: post_id
                                },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status == 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            text: 'सफ़लतापूर्वक ' + error_hi +
                                                ' कर दिया गया है |',
                                            allowOutsideClick: false
                                        }).then(() => {
                                            window.location.href =
                                                '/admin/application-list';
                                        });
                                    } else if (response.status == 'failed') {
                                        Swal.fire({
                                            icon: 'warning',
                                            text: 'आवेदन  ' + error_hi +
                                                ' नहीं किया गया है, पुनः कोशिश करें |',
                                            allowOutsideClick: false
                                        });
                                    } else if (response.status == 'doc_not_viwed') {
                                        Swal.fire({
                                            icon: 'warning',
                                            text: 'आपने अभी तक सभी दस्तावेज़ नहीं देखे हैं।',
                                            allowOutsideClick: false
                                        }).then(() => {
                                            window.location.href =
                                                '/admin/view-docs/' +
                                                encryptedAppId_red;
                                        });
                                    }
                                }
                            });
                        } else {
                            var rejectionReason = result.value;
                            if (btntext === 'Reject' && rejectionReason.trim() === '') {
                                Swal.fire({
                                    icon: 'warning',
                                    text: 'रिमार्क भरना अनिवार्य है',
                                    allowOutsideClick: false
                                }).then(() => {
                                    showSwal();
                                });
                            }
                        }
                    });
                }
            });

            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById("myForm").addEventListener("submit", submitForm);
            });

        });
    </script>


    <script>
        const reasonTextarea = document.getElementById('reason');
        const radios = document.querySelectorAll('input[name="verify"]');

        // // Initialize textarea visibility on page load
        // reasonTextarea.style.display = document.querySelector('input[name="verify"]:checked').value === 'Reject' ? 'block' :
        //     'none';

        // // Toggle visibility based on radio selection
        // radios.forEach(radio => {
        //     radio.addEventListener('change', () => {
        //         reasonTextarea.style.display = radio.value === 'Reject' ? 'block' : 'none';
        //     });
        // });
    </script>
@endsection