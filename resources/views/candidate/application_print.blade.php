<!DOCTYPE html>
<html lang="hi">

<head>
    <meta charset="UTF-8">
    <title>आंगनबाड़ी भर्ती आवेदन पत्र</title>
    <style>
        body {
            font-family: 'Noto Sans Devanagari', sans-serif;
            margin: 20px;
            /* line-height: 1.6; */
        }

        .content {
            padding: 10px;
            margin-bottom: 30px;
            /* space for footer */
            overflow: visible;
        }

        .page {
            break-inside: avoid;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* force cells to wrap */
            word-wrap: break-word;
        }

        td,
        th {
            padding: 6px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .no-border {
            border: none;
        }

        .signature-box {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box div {
            width: 48%;
        }

        .form-section {
            margin-bottom: 20px;
        }

        @page {
            margin-bottom: 40px;
            /* Adjust to make space for footer */
        }

        @page: last {
            margin-bottom: 0;
        }

        @media print {
            * {
                overflow: visible !important;
                /* force visibility */
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'Kruti Dev', 'Mangal', sans-serif;
                font-size: 14px;
            }

            .page {
                break-inside: avoid;
                page-break-inside: avoid;
                page-break-before: auto;
                page-break-after: auto;
                /* border: 2px solid green; */
            }

            /* .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 60px;
                text-align: right;
                font-size: 14px;
                padding-right: 40px;
                box-sizing: border-box;
                z-index: 9999;
            }

            .footer::after {
                content: "आवेदनकर्ता के हस्ताक्षर : ....................................";
                position: absolute;
                right: 20px;
                bottom: 10px;
            } */


            .footer {
                position: fixed;
                bottom: -20px;
                right: 0;
                width: auto;
                margin-top: 20px;
                height: 80px;
                display: flex;
                align-items: flex-end;
                justify-content: flex-end;
                padding-right: 40px;
                padding-bottom: 20px;
                box-sizing: border-box;
                z-index: 9999;
            }

            .footer .signature-text {
                margin-top: 20px;
                position: relative;
                display: inline-block;
                text-align: right;
            }

            .footer img {
                position: absolute;
                height: 30px;
                bottom: 20px;
                right: 60px;
                z-index: 10000;
            }

            .footer .signature-text span {
                display: block;
                position: relative;
                left: 0;
                z-index: 9999;
            }

            /* Add this to ensure content doesn't overlap footer */
            .content {
                margin-bottom: 100px;
                /* Adjust based on footer height */
            }

            /* Force images to show during printing */
            img {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .Mention {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .leftContent {
                width: 70%;
            }

            .withPhoto {
                border: 1px solid #000;
                width: 150px;
                height: 160px;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
                background-color: #f8f8f8;
            }

            .withPhoto img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }

            .form-section {
                width: 100%;
                padding: 10px 0;
            }

            .field-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                align-items: flex-start;
            }

            .field-label {
                width: 55%;
                text-align: left;
                padding-right: 10px;
            }

            .field-data {
                width: 44%;
                text-align: left;
                border-bottom: 1px solid #000;
                height: 20px;
                display: flex;
                align-items: center;
            }

            .field-label.text-end {
                text-align: left;
                padding-left: 20px;
            }

            .field-inline {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .multi-line {
                display: flex;
                flex-direction: column;
            }

            .multi-line span {
                display: inline-block;
                border-bottom: 1px solid #000;
                height: 20px;
                margin-top: 5px;
            }

            .topSpace {
                margin-top: 10px;
            }

            .frontspace {
                margin-left: 10px;
            }

            table {
                table-layout: fixed;
                word-wrap: break-word;
            }

            td,
            th {
                max-width: 150px;
                word-break: break-word;
            }
        }
    </style>

</head>

<body>

    @php
use Carbon\Carbon;

$dob = Carbon::parse($applicant_details->DOB);
$reference_date = Carbon::createFromFormat('d-m-Y', '01-01-2025');
$age = $dob->diff($reference_date);
    @endphp

    <div class="content">
        <div class="page">
            <h2 class="mt-1">आंगनबाड़ी भर्ती के विभिन्न पदों में चयन हेतु आवेदन</h2>

            <p style="text-align:center;">आवेदन का प्रारूप </p>

            <div class="Mention form-section">
                <div class="leftContent">
                    प्रति,<br>
                    संचालक,<br>
                    संचालनालय महिला एवं बाल विकास विभाग,
                    <br>अटल नगर, नया रायपुर, (छ.ग.)
                </div>

                {{-- <div class="withPhoto">
                    <span style="font-size: 13px;">स्वप्रमाणित फोटो चिपकाएँ।</span>
                </div> --}}
                @php
$file_path = asset('uploads') . '/';
if (config('app.env') == 'production') {
    $file_path = config('custom.file_point');
}
                @endphp
                <div class="withPhoto">
                    <img id="fetchedPhoto" src="{{ $file_path . $applicant_details->Document_Photo }}" alt="Photo">
                </div>
            </div>

            <div class="form-section">
                <div class="field-row">
                    <div class="field-label">1. पंजीकरण क्रमांक :</div>
                    <div class="field-data">
                        {{ $applicant_details->application_num }}
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-label">2. जिला / केंद्र, जिस हेतु आवेदन किया जा रहा है:</div>
                    <div class="field-data">
                        {{ $applicant_details->Dist_name == 0 ? ' आंगनबाड़ी भर्ती ' : $applicant_details->Dist_name }}
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-label">3. आवेदित पद का नाम:</div>
                    <div class="field-data">{{ $applicant_details->title }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">4. आवेदनकर्ता का पूरा नाम (हिन्दी में):</div>
                    <div class="field-data">{{ $applicant_details->firstName_hindi }}
                        {{ $applicant_details->middleName_hindi }}
                        {{ $applicant_details->lastName_hindi }}
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-label text-end">(अंग्रेजी में):</div>
                    <div class="field-data">{{ $applicant_details->First_Name }}
                        {{ $applicant_details->Middle_Name }}
                        {{ $applicant_details->Last_Name }}
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-label">5. पिता / पति का नाम:</div>
                    <div class="field-data">{{ $applicant_details->FatherName }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">6. आवेदनकर्ता का आधार नंबर:</div>
                    <div class="field-data">{{ $applicant_details->reference_no }}</div>
                </div>
                {{-- <div class="field-row">
                    <div class="field-label">7. स्थायी पता:</div>
                    <div class="field-data" style="height: auto;">{{ $applicant_details->Corr_Address }}</div>
                </div> --}}
                <div class="field-row">
                    <div class="field-label">7. स्थायी पता:</div>
                    <div class="field-data" style="height: auto;">{{ $applicant_details->Corr_Address }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">8. दूरभाष / मोबाइल:</div>
                    <div class="field-data">{{ $applicant_details->Contact_Number }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">9. जन्मतिथि (अंकों में):</div>
                    <div class="field-data">{{ date('d-m-Y', strtotime($applicant_details->DOB)) }}</div>
                </div>
                <?php
// Assuming $applicant_details->DOB is already set, e.g., '1990-01-15'
// $applicant_details = (object)['DOB' => '1990-01-15']; // For demonstration

$englishMonth = date('F', strtotime($applicant_details->DOB)); // Gets the full English month name, e.g., 'January'

$hindiMonths = [
    'January' => 'जनवरी',
    'February' => 'फरवरी',
    'March' => 'मार्च',
    'April' => 'अप्रैल',
    'May' => 'मई',
    'June' => 'जून',
    'July' => 'जुलाई',
    'August' => 'अगस्त',
    'September' => 'सितंबर',
    'October' => 'अक्टूबर',
    'November' => 'नवंबर',
    'December' => 'दिसंबर',
];

$hindiMonth = $hindiMonths[$englishMonth]; // Get the Hindi month name
$day = date('d', strtotime($applicant_details->DOB)); // Get the day
$year = date('Y', strtotime($applicant_details->DOB)); // Get the year

$hindiDate = $day . ' ' . $hindiMonth . ' ' . $year;
                    ?>

                <div class="field-row">
                    <div class="field-label text-end">(शब्दों में):</div>
                    <div class="field-data">{{ $hindiDate }}</div>
                </div>
                {{-- <div class="field-row">
                    <div class="field-label">11. दिनांक 01/01/2025 को आयु:</div>
                    <div class="field-data">{{ $age->y }} वर्ष {{ $age->m }} माह {{ $age->d }}
                        दिन
                    </div>
                </div> --}}
                <div class="field-row">
                    <div class="field-label">10. जाति / वर्ग (अ.जा./अ.ज.जा./अ.पि.व./सामान्य):</div>
                    <div class="field-data">{{ $applicant_details->Caste }}</div>
                </div>


                @if (!empty($Post_questionAnswer) && count($Post_questionAnswer) > 0)
                    @php
    $answer1 = $answer2 = $answer3 = '';
    foreach ($Post_questionAnswer as $qa) {
        if ($qa->fk_ques_id == 1) {
            $answer1 = $qa->answer_list ?? '';
            // echo $answer1;
        } elseif ($qa->fk_ques_id == 7) {
            $answer2 = $qa->answer_list ?? '';
        } elseif ($qa->fk_ques_id == 8) {
            $answer3 = $qa->answer_list ?? '';
        } elseif ($qa->fk_ques_id == 6) {
            $answer4 = $qa->answer_list ?? '';
        }
    }
                    @endphp
                @endif



                <div class="field-row">
                    <div class="field-label">11. वैवाहिक स्थिति (विवाहित / अविवाहित / विधवा / परित्यक्ता/तलाकशुदा):
                    </div>
                    <div class="field-data"> {{ !empty($answer1) ? $answer1 : '' }} </div>
                </div>
                {{-- <div class="field-row">
                    <div class="field-label">14. यदि विवाहित हैं तो विवाह की तिथि एवं जीवित बच्चों की संख्या:</div>
                    <div class="field-data">
                        {{ !empty($answer2) ? date('d-m-Y', strtotime($answer2)) : '' }}{{ !empty($answer3) ? ', ' .
                        $answer3 : '' }}
                    </div>
                </div> --}}
                <div class="field-row">
                    <div class="field-label">12. क्या आवेदनकर्ता शासकीय / अर्धशासकीय / गैर शासकीय संगठन जिसमें
                        सेवारत
                        हैं?
                    </div>
                    <div class="field-data"> {{ !empty($answer4) ? $answer4 : '' }} </div>

                </div>
                <div class="field-row">
                    <div class="field-label">13. गैर सरकारी संगठन होने पर पंजीयन क्रमांक :
                    </div>
                    <div class="field-data"> </div>

                </div>
                {{-- <p style="margin-top: 10px;">16. गैर सरकारी संगठन होने पर पंजीयन क्रमांक :
                    ..........................................</p> --}}
            </div>
        </div>

        <div class="page form-section">


            <p style="margin-top: 20px;">14. शैक्षणिक योग्यता (जन्मतिथि एवं समस्त शैक्षणिक प्रमाण पत्रों की सत्यापित
                प्रति संलग्न करें )

            <table>
                <thead>
                    <tr>
                        {{-- <th>क्र. सं.</th> --}}
                        <th>उत्तीर्ण परीक्षा</th>
                        <th>उत्तीर्ण वर्ष</th>
                        <th>बोर्ड/संस्था का नाम</th>
                        <th>प्राप्तांक/ पूर्णाक</th>
                        <th>प्रतिशत(%)</th>
                        <th>श्रेणी</th>
                    </tr>
                </thead>
                <tbody>
                    @if (@$education_details)
                        @foreach ($education_details as $education)
                            {{-- @continue($education->fk_Quali_ID == 7) --}}
                            <tr>
                                <td>{{ $education->Quali_Name }}</td>

                                <td>{{ $education->year_passing }}</td>
                                <td>{{ $education->qualification_board }}</td>
                                <td>{{ $education->obtained_marks }} / {{ $education->total_marks }}</td>
                                <td>{{ $education->percentage }}</td>
                                <td>
                                    @php
        $percent = (float) $education->percentage;
                                    @endphp

                                    @if ($percent >= 60)
                                        प्रथम
                                    @elseif($percent >= 45)
                                        द्वितीय
                                    @elseif($percent >= 33)
                                        तृतीय
                                    @else
                                        अनुतीर्ण
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    @endif

                </tbody>
            </table>

            {{-- <p style="margin-top: 20px;">17. अन्य योग्यता का विवरण : </p> --}}

            {{-- <p style="margin-top: 20px;">15. अनिवार्य कौशल का विवरण :
                @if (!empty($Skill_Ans) && count($Skill_Ans) > 0)
                <br>
                @foreach ($Skill_Ans as $key => $skill)
                <span>
                    {{ $loop->iteration }}. {{ $skill->SkillName }}
                    @php
                    $decodedOptions = json_decode($skill->SkillAnswer, true);
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
                कौशल अनिवार्य नहीं है|
                @endif
            </p> --}}
            <p style="margin-top: 20px;">15. अनुभव (प्रमाण पत्र संलग्न करें)
                ..........................................</p>


            <table class="page form-section">
                <thead>
                    <tr>
                        <th>संस्था का नाम, जहाँ कार्य किया गया है</th>
                        <th>संस्था का पंजीयन क्रमांक</th>
                        <th>पदनाम</th>
                        <th>प्रस्तुत किया गया मासिक वेतन / मानदेय</th>
                        <th>अनुभव / कार्य अवधि का विवरण कब से कब तक</th>
                        <th>संपादित कार्य का विवरण</th>
                        <th>अनुभव प्रमाण–पत्र</th>
                    </tr>
                </thead>


                <tbody>
                    @forelse ($experience_details as $experience)
                                    <tr>
                                        <td>{{ $experience->Organization_Name }}</td>
                                        <td>
                                            {{ $experience->Organization_Type == 1
        ? 'शासकीय'
        : ($experience->Organization_Type == 2
            ? $experience->NGO_No
            : ($experience->Organization_Type == 3
                ? 'अर्ध-शासकीय'
                : 'N/A')) }}
                                        </td>
                                        <td>{{ $experience->Designation }}</td>
                                        <td>₹ {{ $experience->salary }}</td>
                                        <td>{{ date('d-m-Y', strtotime($experience->Date_From)) }} से
                                            {{ date('d-m-Y', strtotime($experience->Date_To)) }} तक
                                        </td>
                                        <td>{{ $experience->Nature_Of_Work }}</td>
                                        <td>{{ $experience->exp_document ? 'हाँ' : 'नहीं' }}</td>
                                    </tr>
                    @empty
                        <tr style="height: 50px;">
                            {{-- <td colspan="7" class="text-center text-muted">कोई अनुभव विवरण उपलब्ध नहीं है</td> --}}
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

       
            @if (Session::get('sess_role') === 'Super_admin' || Session::get('sess_role') === 'Admin')
                @if (request()->get('type') === 'pending')
                    @if ($marks_details->total_mark == null)
                        <h3 align="center"> अंकों का विवरण </h3>
                        <div class="row">
                            <div class="col-md-12">
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
                    @endif
                @endif
                @if (request()->get('type') === 'fulfilled')
                    @if ($marks_details->total_mark != null)
                        <h3 align="center"> अंकों का विवरण </h3>
                        <div class="row">
                            <div class="col-md-12">
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
                    @endif
                @endif
            @endif
            {{--
        </div> --}}

        {{-- <div class="page form-section"> --}}
            </div>
            
            <div class="col-md-12 mt-3 avoid-break page form-section">

            <h6 class="mt-2"> टीप : सक्षम अधिकारी द्वारा जारी किया गया अनुभव प्रमाण पत्र ही मान्य किया जाएगा तथा
                स्वैच्छिक
                सेवा का अनुभव
                मान्य
                नही किया जायेगा |</h6>
            <p>16. अनिवार्य सलंग्नकों की सूची (सभी सलंग्न दस्तावेज स्वप्रमाणित रहें) –</p>
            <span> 1. निवास प्रमाण–पत्र </span><br>
            <span> 2. जाति प्रमाण–पत्र (आवेदित पद हेतु अनिवार्य) </span><br>
            {{-- <span> 3. विवाहित होने पर शपथ–पत्र </span><br>
            <span> 4. आधार </span><br> --}}
            <span> 3. जन्म तिथि हेतु 10वीं की अंकसूची / प्रमाण पत्र </span><br>
            <span> 4. 12वीं की अंकसूची </span><br>
            {{-- <span> 7. स्नातक की अंकसूची </span><br>
            <span> 8. स्नातकोत्तर की अंकसूची (पद अनुसार आवश्यक होने पर) </span><br> --}}
            <span> 5. व्यावसायिक पाठ्यक्रम का प्रमाण–पत्र (पद अनुसार आवश्यक होने पर) </span><br>
            <span> 6. विज्ञापन अनुसार निर्धारित अवधि का अनुभव प्रमाण–पत्र </span><br>
            {{-- <span> 10. अनिवार्य कौशल संबंधी प्रमाण–पत्र (जहाँ लागू है) </span><br> --}}

            <br>
            <p>17. इसके अतिरिक्त अन्य कोई विवरण देना चाहें तो पृथक पत्र में संलग्न कर सकते हैं।<br>
                अन्य संलग्नकों का विवरण:<br>
                1. ..........................................<br>
                2. ..........................................<br>
                3. ..........................................</p>

            <div class="signature-box">
                <div>

                </div>
                <div style="text-align:right;">
                    <img id="signPhoto" src="{{ $file_path . $applicant_details->Document_Sign }}" alt="Signature"
                        style="height:30px; margin-right:40px; margin-bottom:2px;">
                    <p>आवेदनकर्ता के हस्ताक्षर: __________________</p>
                </div>
            </div>
        </div>
        <div class="page form-section">


            <h2>घोषणा</h2>
            <ol>
                <li>मैं एतद्द्वारा घोषणा करता/करती हूँ कि इस प्रपत्र में दिए गए समस्त विवरण तथा संलग्न अभिलेख मेरी
                    अधिकतम
                    जानकारी
                    और
                    विश्वास के अनुसार सत्य हैं और यदि ये असत्य पाए जाते हैं, तो मेरी उम्मेदवारी/नियुक्ति निरस्त किए
                    जाने
                    योग्य होगी
                    और मेरे विरुद्ध वैधानिक कार्यवाही की जा सकेगी।</li>
                <li class="topSpace">मैं इस आवेदन के साथ अपना आधार नंबर प्रस्तुत कर रही हूँ तथा स्वेच्छा से महिला
                    एवं बाल
                    विकास
                    विभाग को सहमति देती
                    हूँ कि मेरे आधार नंबर का उपयोग मुझे प्रमाणित करने एवं मेरा चयन हो जाने पर वेतन आदि का भुगतान
                    आधार आधारित
                    भुगतान
                    हेतु किया जा सकता है।</li>
            </ol>

            <div class="signature-box">
                <div>
                    <p>स्थान: __________________</p>
                    <p>दिनांक: __________________</p>
                </div>
                {{-- <div style="text-align:right;">
                    <p>आवेदनकर्ता के हस्ताक्षर: __________________</p>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="signature-text">
            <img id="signPhoto" src="{{ $file_path . $applicant_details->Document_Sign }}" alt="Signature">
            <span>आवेदनकर्ता के हस्ताक्षर : ....................................</span>
        </div>
    </div>


</body>

</html>