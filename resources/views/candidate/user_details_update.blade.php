@extends('layouts.dahboard_layout')
@section('styles')
    <style>
        /* Basic Spinner Animation */
        .spinner {
            border: 4px solid #f3f3f3;
            /* Light grey */
            border-top: 4px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        #loader {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .AdharConfirm {
            width: 20px;
            height: 20px;
            border: 1px solid #007bff;
            border-radius: 4px;
            appearance: none;
            /* Reset default style */
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            background-color: white;
        }

        .AdharConfirm:checked {
            background-color: #007bff;
        }

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

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="file"],
        textarea,
        select {
            border: 1px solid gray !important;
        }
    </style>
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('body-page')
    <main id="main" class="main">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="row">

                    <div class="col-md-2 grid-margin stretch-card mt-2" style="padding: 0px !important;">
                        <button <?php echo isset($data['applicant_details']) ? '' : 'disabled'; ?> id="post-details-btn" style="width: 100%; border-radius : 15px;"
                            type="button" class="btn btn-primary btn-sm btn-icon-text add-app-btn">
                            पद चयन
                        </button>
                    </div>
                    <div class="col-md-2 grid-margin stretch-card mt-2" style="padding: 0px !important;">
                        <button <?php echo isset($data['applicant_details']) ? '' : 'disabled'; ?> id="personal-details-btn" style="width: 100%; border-radius : 15px;"
                            type="button" class="btn btn-primary btn-sm btn-icon-text add-app-btn">
                            व्यक्तिगत जानकारी
                        </button>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card mt-2" style="padding: 0px !important;">
                        <button <?php echo isset($data['applicant_details']) ? '' : 'disabled'; ?> id="edu-info-btn" style="width: 100%; border-radius :15px;"
                            type="button" class="btn btn-primary btn-sm btn-icon-text add-app-btn">
                            शैक्षणिक योग्यता
                        </button>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card mt-2" style="padding: 0px !important;">
                        <button <?php echo isset($data['applicant_details']) ? '' : 'disabled'; ?> id="exp-info-btn" style="width: 100%; border-radius :15px;"
                            type="button" class="btn btn-primary btn-sm btn-icon-text add-app-btn">
                            अनुभव एवं अन्य जानकारी
                        </button>
                    </div>
                    <div class="col-md-2 grid-margiaidn stretch-card mt-2" style="padding: 0px !important;">
                        <button <?php echo isset($data['applicant_details']) ? '' : 'disabled'; ?> id="attachmnet-btn" style="width: 100%; border-radius :15px;"
                            type="button" class="btn btn-primary btn-sm btn-icon-text add-app-btn">
                            दस्तावेज अपलोड
                        </button>
                    </div>
                </div><br>

                <div class="card" id="tab1">
                    <div class="row container"><br>
                        <form id="myForm1" action="{{ url('/candidate/post-details-update') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.appdetails">
                            @csrf
                            <input type="hidden" name="applicant_id" value="{{ session('uid') }}">
                            <input type="hidden" name="applicant_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->RowID : '' }}" />
                            <input type="hidden" name="apply_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->apply_id : '' }}" />

                            <div class="row mt-3">
                                <input type="hidden" value="{{ $data['applicant_details']->stepCount }}" id="stepCount">

                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="cities"> जिला चयन करें / Select District </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="selected_district" id="selected_district" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['cities']))
                                            @foreach ($data['cities'] as $district)
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->fk_district_id == $district->District_Code_LGD ? 'selected' : '' }}>
                                                    {{ $district->Dist_name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="cities-error" class="text-danger"></span>
                                </div>



                                {{-- <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="projects">परियोजना चयन करें / Select Project </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="projects" id="projects" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['projects']))
                                            @foreach ($data['projects'] as $projects)
                                                <option value="{{ $projects->project_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->project_code == $projects->project_code ? 'selected' : '' }}>
                                                    {{ $projects->project }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="projects-error" class="text-danger"></span>
                                </div> --}}


                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="area">क्षेत्र चयन करें / Select Area type </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="area" id="area" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['area']))
                                            @foreach ($data['area'] as $area)
                                                <option value="{{ $area->area_id }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_area == $area->area_id ? 'selected' : '' }}>
                                                    {{ $area->area_name_hi }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="area-error" class="text-danger"></span>
                                </div>
                            </div><br>

                            <div class="row" id="Rular-select">
                                {{-- Default hidden show based on choose Rular (Block) --}}
                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="block">विकासखंड चयन करें / Select Block </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="block" id="block">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['block']))
                                            @foreach ($data['block'] as $block)
                                                <option value="{{ $block->block_lgd_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_block == $block->block_lgd_code ? 'selected' : '' }}>
                                                    {{ $block->block_name_hin }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="block-error" class="text-danger"></span>
                                </div>

                                {{-- Default hidden show based on choose Rular (GP) --}}
                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="gp">ग्राम पंचायत चयन करें / Select Gram Panchayat </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="gp" id="gp">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['gp']))
                                            @foreach ($data['gp'] as $gp)
                                                <option value="{{ $gp->panchayat_lgd_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_gp == $gp->panchayat_lgd_code ? 'selected' : '' }}>
                                                    {{ $gp->panchayat_name_hin }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="gp-error" class="text-danger"></span>
                                </div>

                                <div class="col-md-6 col-xs-12 text-left mt-3">
                                    <label for="gp">ग्राम चयन करें / Select Gram Panchayat </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="post_village" id="village_id">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['village']))
                                            @foreach ($data['village'] as $village)
                                                <option value="{{ $village->village_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_village == $village->village_code
                                                        ? 'selected'
                                                        : '' }}>
                                                    {{ $village->village_name_hin }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="village-error" class="text-danger"></span>
                                </div>
                                <br>


                            </div>

                            <div class="row" id="Urban-select" style="display: none">
                                {{-- Default hidden show based on choose Rular (Nagar) --}}
                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="nagar">नगर निकाय चयन करें / Select Nagar </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="nagar" id="nagar">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['nagar']))
                                            @foreach ($data['nagar'] as $nagar)
                                                <option value="{{ $nagar->std_nnn_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_nagar == $nagar->std_nnn_code ? 'selected' : '' }}>
                                                    {{ $nagar->nnn_name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="nagar-error" class="text-danger"></span>
                                </div>


                                {{-- Default hidden show based on choose Rular (Ward) --}}
                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="ward">वार्ड चयन करें / Select Ward </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="ward" id="ward">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['ward']))
                                            @foreach ($data['ward'] as $ward)
                                                <option value="{{ $ward->ID }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_ward == $ward->ID ? 'selected' : '' }}>
                                                    {{ $ward->ward_name }} ({{ $ward->ward_no }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="ward-error" class="text-danger"></span>
                                </div>
                            </div>
                            <br>


                            <div class="row mt-2">

                                <div class="col-md-6 col-xs-12 text-left mt-2">
                                    <label for="master_post">पद चयन करें / Select Post </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="master_post" id="master_post" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['master_post']))
                                            @foreach ($data['master_post'] as $post)
                                                <option value="{{ $post->post_id }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->fk_post_id == $post->post_id ? 'selected' : '' }}>
                                                    {{ $post->title }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="master_post-error" class="text-danger"></span>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mt-3">
                                        <div id="questionsContainer"></div> <br>
                                        <span id="questions-error" class="text-danger"></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mt-3">
                                        <div id="skillsContainer"></div><br>
                                        <span id="skills-error" class="text-danger"></span> <br>
                                        <span id="skill_options-error" class="text-danger"></span>

                                    </div>
                                </div>

                            </div>


                            <br>
                            <div class='row'>
                                <div class='col-md-12 col-xw-12'>
                                    <hr>
                                </div>
                            </div>

                            <br>
                            <button style="float: right; margin-right: 10px; font-size:14px;" type="submit"
                                id="user-details-btn" class="btn btn-primary nextBtn btn-lg pull-right">सबमिट करें और आगे
                                जाएं</button>
                        </form>
                    </div>
                </div><br>


                <?php
                // $name = session()->get('sess_fname');
                // $parts = explode(" ", $name);
                
                // $firstName = $parts[0] ?? '';
                // $lastName = $parts[1] ?? '';
                
                $name = session()->get('sess_fname');
                $parts = explode(' ', trim($name));
                
                if (count($parts) === 1) {
                    // Agar sirf ek naam hai toh first name set hoga, last name blank hoga
                    $firstName = $parts[0];
                    $middleName = '';
                    $lastName = '';
                } elseif (count($parts) === 2) {
                    // Agar do naam hain toh first name aur last name set hoga, middle name blank hoga
                    $firstName = $parts[0];
                    $middleName = '';
                    $lastName = $parts[1];
                } elseif (count($parts) === 3) {
                    // Agar 3 naam hain toh first name aur last name set hoga, middle name set hoga
                    $firstName = $parts[0];
                    $middleName = $parts[1];
                    $lastName = $parts[2];
                } else {
                    // Agar 3 ya usse zyada words hain toh first, middle, aur last name assign hoga
                    $firstName = $parts[0]; // Pehla word first name hoga
                    $lastName = array_pop($parts); // Aakhri word last name hoga
                    $middleName = implode(' ', $parts); // Beech ka sab kuch middle name hoga
                }
                ?>

                <div class="card" id="tab2" style="display: none;">
                    <div class="row container"><br>
                        <form id="myForm2" action="{{ url('/candidate/applicant-details-update') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.appdetails">
                            @csrf
                            <input type="hidden" name="applicant_id" id="applicant_id" value="{{ session('uid') }}">
                            <input type="hidden" name="applicant_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->RowID : '' }}" />
                            <input type="hidden" name="apply_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->apply_id : '' }}" />

                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left mt-2"><br>
                                    <label for="fname">आवेदिका का प्रथम नाम/First Name</label><label
                                        style="color:red">*</label>
                                    <input type="text" id="fname" class="form-control alphabets-only"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->First_Name : $firstName }}"
                                        style="text-transform:uppercase" name="First_Name"
                                        placeholder="आवेदिका का प्रथम नाम दर्ज करें" readonly>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left mt-2"><br>
                                    <label for="mname">मध्य नाम/Middle Name </label>
                                    <input type="text" id="mname" class="form-control alphabets-only"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->Middle_Name : $middleName }}"
                                        style="text-transform:uppercase" name="Middle_Name"
                                        placeholder="आवेदिका का मध्य नाम दर्ज करें" readonly>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left mt-2"><br>
                                    <label for="lname">अंतिम नाम/Last Name</label>
                                    <input type="text" id="lname" class="form-control alphabets-only"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->Last_Name : $lastName }}"
                                        style="text-transform:uppercase" name="Last_Name"
                                        placeholder="आवेदिका का अंतिम नाम दर्ज करें" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 col-xs-12 text-left"><br>
                                    <label for="fname" style="font-weight:600">प्रथम नाम (हिंदी में)</label><label
                                        style="color:red">*</label>
                                    <input type="text" class="form-control hinditext-only"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->firstName_hindi : $firstName_hindi }}"
                                        name="firstName_hindi" placeholder="आवेदनकर्ता का प्रथम नाम हिंदी में दर्ज करें"
                                        title="आवेदनकर्ता का प्रथम नाम हिंदी में दर्ज करें" required>
                                    <div id="firstName_hindi" class="invalid-feedback">
                                        This field is required<br>
                                    </div>

                                </div>
                                <div class="col-md-4 col-xs-12 text-left"><br>
                                    <label for="mname" style="font-weight:600">मध्य नाम (हिंदी में)</label>
                                    <input type="text" class="form-control hinditext-only"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->middleName_hindi : $middleName_hindi }}"
                                        name="middleName_hindi" placeholder="आवेदनकर्ता का मध्य नाम हिंदी में दर्ज करें"
                                        title="आवेदनकर्ता का मध्य नाम हिंदी में दर्ज करें">
                                </div>
                                <div class="col-md-4 col-xs-12 text-left"><br>
                                    <label for="lname" style="font-weight:600">अंतिम नाम (हिंदी में)</label>
                                    <input type="text" class="form-control hinditext-only"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->lastName_hindi : $lastName_hindi }}"
                                        name="lastName_hindi" placeholder="आवेदनकर्ता का अंतिम नाम हिंदी में दर्ज करें"
                                        title="आवेदनकर्ता का अंतिम नाम हिंदी में दर्ज करें">
                                    <div id="lastName_hindi" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <a href="https://translate.google.co.in/?sl=en&tl=hi&op=translate" target="_blank"
                                    class="mb-3 ">
                                    Translate to Hindi
                                </a>
                            </div><br>
                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="mothername">माता का नाम/Mother's Name</label><label
                                        style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->MotherName : '' }}"
                                        id="mothername" class="form-control alphabets-only" name="mothername" required
                                        style="text-transform:uppercase;" placeholder="माता का नाम दर्ज करें">
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="fathername">पिता/पति का नाम/Father/Husband Name </label><label
                                        style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->FatherName : '' }}"
                                        id="fathername" class="form-control alphabets-only" name="fathername" required
                                        style="text-transform:uppercase;" placeholder="पिता/पति का नाम दर्ज करें">
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <div class="d-none">
                                        <label for="cities">मूल निवासी जिला/ Domicile District </label><label
                                            style="color:red">*</label>
                                        <select class="form-select" name="domicile_district" id="domicile_district"
                                            required>
                                            <option selected disabled value="undefined">-- चयन करें --</option>
                                            @if (!empty($data['cities']))
                                                @foreach ($data['cities'] as $district)
                                                    <option value="{{ $district->District_Code_LGD }}"
                                                        {{ !empty($data['applicant_details']) && $data['applicant_details']->Domicile_District_lgd == $district->District_Code_LGD ? 'selected' : '' }}>
                                                        {{ $district->Dist_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option> डेटा उपलब्ध नहीं है</option>
                                            @endif
                                        </select>
                                        <span id="domicile_district-error" class="text-danger"></span>
                                    </div>
                                </div>
                            </div><br>

                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="paddr">स्थायी पता/ Address </label><label style="color:red">*</label>
                                    <textarea rows="3" id="caddr" class="form-control" name="corr_addr" style="resize: none;" required
                                        placeholder="स्थायी पता दर्ज करें">{{ isset($data['applicant_details']) ? $data['applicant_details']->Corr_Address : '' }}</textarea>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="cities">स्थायी निवासी जिला/ District </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="cur_district" id="cur_district" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['cities']))
                                            @foreach ($data['cities'] as $district)
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->Corr_District_lgd == $district->District_Code_LGD ? 'selected' : '' }}>
                                                    {{ $district->Dist_name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="cur_district-error" class="text-danger"></span>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="pincode">स्थायी पिन कोड/ Pincode</label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="pincode" id="cpincode" required>
                                        <option value="">-- चयन करें --</option>
                                        @if (!empty($data['applicant_details']) && !empty($data['pincodes']))
                                            @foreach ($data['pincodes'] as $pin)
                                                <option value="{{ $pin->pincode }}"
                                                    {{ $data['applicant_details']->Corr_pincode == $pin->pincode ? 'selected' : '' }}>
                                                    {{ $pin->pincode }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span id="pincode-error" class="text-danger"></span>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="manualPincodeCheck"
                                            class="border border-primary">
                                        <label class="form-check-label" for="manualPincodeCheck">पिनकोड स्वयं दर्ज
                                            करें</label>
                                    </div>

                                    <input type="number" class="form-control" name="pincode_manual"
                                        id="cpincode_manual" placeholder="पिनकोड दर्ज करें" maxlength="6"
                                        minlength="6"
                                        value="{{ !empty($data['applicant_details']) ? $data['applicant_details']->Corr_pincode : '' }}"
                                        disabled>
                                    <span id="pincode-error" class="text-danger"></span>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const dropdown = document.getElementById('cpincode');
                                        const manualInput = document.getElementById('cpincode_manual');
                                        const checkbox = document.getElementById('manualPincodeCheck');

                                        checkbox.addEventListener('change', function() {
                                            if (this.checked) {
                                                dropdown.value = '';
                                                dropdown.disabled = true;

                                                manualInput.disabled = false;
                                                manualInput.required = true;
                                            } else {
                                                manualInput.value = '';
                                                manualInput.disabled = true;
                                                manualInput.required = false;

                                                dropdown.disabled = false;
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="area">स्थायी क्षेत्र चयन करें / Select Area type </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="current_area" id="area1" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['area']))
                                            @foreach ($data['area'] as $area)
                                                <option value="{{ $area->area_id }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_area == $area->area_id ? 'selected' : '' }}>
                                                    {{ $area->area_name_hi }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="current_area-error" class="text-danger"></span>
                                </div>



                                {{-- Default hidden show based on choose Rular (Block) --}}
                                <div class="col-md-4 col-xs-12 text-left mt-2" id="hidden-block">
                                    <label for="block">स्थायी विकासखंड चयन करें / Select Block </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="current_block" id="block1">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['block']))
                                            @foreach ($data['block'] as $block)
                                                <option value="{{ $block->block_lgd_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_block == $block->block_lgd_code ? 'selected' : '' }}>
                                                    {{ $block->block_name_hin }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="current_block-error" class="text-danger"></span>
                                </div>

                                {{-- Default hidden show based on choose Rular (GP) --}}
                                <div class="col-md-4 col-xs-12 text-left mt-2" id="hidden-gp">
                                    <label for="gp">ग्राम पंचायत चयन करें / Select Gram Panchayat </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="current_gp" id="gp1">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['gp']))
                                            @foreach ($data['gp'] as $gp)
                                                <option value="{{ $gp->panchayat_lgd_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_gp == $gp->panchayat_lgd_code ? 'selected' : '' }}>
                                                    {{ $gp->panchayat_name_hin }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="current_gp-error" class="text-danger"></span>
                                </div>

                                <div class="col-md-4 col-xs-12 text-left mt-2" id="hidden-village">
                                    <label for="gp">ग्राम चयन करें / Select Gram Panchayat </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="current_village" id="village_id1">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['village']))
                                            @foreach ($data['village'] as $village)
                                                <option value="{{ $village->village_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->current_village == $village->village_code
                                                        ? 'selected'
                                                        : '' }}>
                                                    {{ $village->village_name_hin }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="gp-error" class="text-danger"></span>
                                </div>

                                {{-- Default hidden show based on choose Rular (Nagar) --}}
                                <div class="col-md-4 col-xs-12 text-left mt-2" id="hidden-nagar" style="display: none">
                                    <label for="nagar">स्थायी नगर निकाय चयन करें / Select Nagar </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="current_nagar" id="nagar1">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['nagar']))
                                            @foreach ($data['nagar'] as $nagar)
                                                <option value="{{ $nagar->std_nnn_code }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_nagar == $nagar->std_nnn_code ? 'selected' : '' }}>
                                                    {{ $nagar->nnn_name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="current_nagar-error" class="text-danger"></span>
                                </div>


                                {{-- Default hidden show based on choose Rular (Ward) --}}
                                <div class="col-md-4 col-xs-12 text-left mt-2" id="hidden-ward" style="display: none">
                                    <label for="ward">स्थायी वार्ड चयन करें / Select Ward </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="current_ward" id="ward1">
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['ward']))
                                            @foreach ($data['ward'] as $ward)
                                                <option value="{{ $ward->ID }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->post_ward == $ward->ID ? 'selected' : '' }}>
                                                    {{ $ward->ward_name }} ({{ $ward->ward_no }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="current_ward-error" class="text-danger"></span>
                                </div>


                            </div>

                            {{-- <div class="row">
                                <div class="form-group formField">
                                    <div class="col-md-4 col-xs-12 text-left mt-2">
                                        <br>
                                        <input type="checkbox" id="same" name="sameAddress" value="1"
                                            {{ isset($data['applicant_details']) && $data['applicant_details']->sameAddress == '1' ? 'checked' : '' }}>
                                        <strong><label for="same" style="cursor: pointer;">स्थायी पता व वर्तमान पता एक
                                                है?</label></strong>

                                    </div>
                                </div>
                            </div> --}}
                            <br>
                            {{-- <div class="row">
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="paddr">स्थायी पता/Permanent Address </label><label
                                        style="color:red">*</label>
                                    <textarea rows="3" id="paddr" class="form-control" name="perm_addr" style="resize: none;" required
                                        placeholder="स्थायी पता दर्ज करें">{{ isset($data['applicant_details']) ? $data['applicant_details']->Perm_Address : '' }}</textarea>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="cities">स्थायी निवासी जिला/Permanent District </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="per_district" id="per_district" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @if (!empty($data['cities']))
                                            @foreach ($data['cities'] as $district)
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    {{ !empty($data['applicant_details']) && $data['applicant_details']->Perm_District_lgd == $district->District_Code_LGD ? 'selected' : '' }}>
                                                    {{ $district->Dist_name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option>डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <span id="per_district-error" class="text-danger"></span>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="ppincode">स्थायी पिन कोड/Pincode</label><label style="color:red">*</label>
                                    <select class="form-select" name="ppincode" id="ppincode" required>
                                        <option value="">-- चयन करें --</option>
                                        @if (!empty($data['applicant_details']) && !empty($data['per_pincodes']))
                                            @foreach ($data['per_pincodes'] as $pin)
                                                <option value="{{ $pin->pincode }}"
                                                    {{ $data['applicant_details']->Perm_pincode == $pin->pincode ? 'selected' : '' }}>
                                                    {{ $pin->pincode }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span id="ppincode-error" class="text-danger"></span>
                                </div>
                            </div> --}}


                            <div class='row'>
                                <div class='col-md-12 col-xw-12'>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                {{-- <div class="col-md-4 col-xs-12 text-left mt-2">
                                    <label for="nationality">राष्ट्रीयता/Nationality </label><label
                                        style="color:red">*</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality"
                                        value="Indian" required readonly>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div> --}}
                                <input type="hidden" value="Indian" name="nationality" id="nationality">

                                <div class="col-md-3 col-xs-12 text-left mt-2">
                                    <label for="dob">जन्मतिथि/Date of Birth </label><label
                                        style="color:red">*</label>
                                    <input type="date" value="{{ $data['user_dob'] }}" id="dob"
                                        class="form-control" name="dob" max="{{ $data['maxdate'] }}" readonly>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left mt-2">
                                    <label for="dob">आयु</label>
                                    <input type="text" value="{{ $user_age }}" class="form-control"
                                        name="" readonly>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left mt-2">
                                    <label for="mobile">मोबाइल नंबर/Mobile Number</label><label
                                        style="color:red">*</label>
                                    <input type="number" class="form-control number-only"
                                        value="{{ session()->get('sess_mobile') }}" id="mobile" name="mobile"
                                        maxlength="10" minlength="10" placeholder="मोबाइल नंबर दर्ज करें" readonly>
                                    <div id="mobile-error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <input type="hidden" value="महिला" name="gender" id="gender">
                                <div class="col-md-3  text-left mt-2">
                                    <label for="caste">वर्ग/Category</label><label style="color:red">*</label>&nbsp;
                                    <select id="caste" class="form-select" name="caste" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @foreach ($master_caste as $caste)
                                            @if ($caste->caste_id != 5)
                                                <option value="{{ $caste->caste_title }}"
                                                    {{ isset($data['applicant_details']) && $data['applicant_details']->Caste == $caste->caste_title ? 'selected' : '' }}>
                                                    {{ $caste->caste_title }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span id="caste-error" class="text-danger"></span>

                                </div>
                            </div>


                            <div class="row" style="margin-top: 17px">
                                {{-- <div class="col-md-4">
                                    <label for="gender">लिंग/Gender </label><label style="color:red">*</label>&nbsp;
                                    <select id="gender" class="form-select" name="gender" required>
                                        <option selected disabled value="undefined">-- चयन करें --</option>
                                        @foreach ($master_gender as $gender)
                                            @if ($gender->gender_id == 2)
                                                <option value="{{ $gender->gender_title }}" selected>
                                                    {{ $gender->gender_title }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span id="gender-error" class="text-danger"></span>
                                </div> --}}
                                {{-- @foreach ($master_gender as $gender)
                                            <option value="{{ $gender->gender_title }}" 
                                                 {{ isset($data['applicant_details']) && $data['applicant_details']->Gender == $gender->gender_title ? 'selected' : '' }}> 
                                                {{ $gender->gender_title }}
                                            </option>
                                        @endforeach --}}



                                {{-- <div class="col-md-3 col-xs-12 text-left mt-2">
                                    <label for="email">ई-मेल आई. डी./Email ID<label style="color:red">*</label></label>
                                    <input type="email"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->Email : '' }}"
                                        pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}" id="email"
                                        class="form-control" name="email" placeholder="ई-मेल आई.डी. दर्ज करें"
                                        required>
                                    <span id="email-error" class="text-danger"></span>

                                </div> --}}
                                <div class="col-md-4 col-xs-12 text-left mt-3">
                                    <label for="mobile">आधार नंबर/AADHAR Number<label
                                            style="color:red">*</label></label>
                                    <input type="text" title="आधार संख्या 12 अंकों की होनी चाहिए|"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->aadhar_no : '' }}"
                                        class="form-control number-only" id="adhaar" name="adhaar" maxlength="12"
                                        minlength="12" placeholder="आधार नंबर दर्ज करें">
                                    <span class="ms-1" style="font-size: 10px; color: #3498db;">नोट : आधार संख्या 12
                                        अंकों की होनी चाहिए|</span> <br>

                                    <span id="adhaar-error" class="text-danger"></span>
                                </div>


                                <div class="col-md-4 col-xs-12 text-left mt-3">
                                    <label for="identity-number" id="identity-label">मतदाता पहचान नंबर दर्ज करें </label>
                                    {{-- <label style="color:red">*</label> --}}
                                    <input type="text"
                                        value="{{ isset($data['applicant_details']) ? $data['applicant_details']->epicno : '' }}"
                                        class="form-control" id="identity-number" name="epicno"
                                        style="text-transform:uppercase;" placeholder="पहचान नंबर दर्ज करें"
                                        minlength="10" maxlength="10">
                                    <span id="identity-note" style="font-size: 10px; color: #3498db;">
                                        नोट: मतदाता पहचान संख्या ठीक 10 अक्षर (3 अक्षर + 7 अंक) की होनी चाहिए।
                                    </span><br>
                                    <span id="epic-error" class="text-danger"></span>

                                </div>
                            </div>

                            <div class="row" style="margin-top: 17px">
                                <div class="col-md-8 col-xs-12 text-left mt-2">
                                    <div class="form-check d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input AdharConfirm" name="AdharConfirm"
                                            value="1" id="confirmationCheckbox"
                                            {{ isset($data['applicant_details']) && $data['applicant_details']->AdharConfirm == '1' ? 'checked' : '' }}
                                            style="width: 30px; height: 20px; border: 1px solid #007bff; border-radius: 4px; outline: none; 
                                                                                    cursor: pointer;">
                                        <p class="form-check-label ms-3" style="font-size: 13px; color: #000;">
                                            <label for="confirmationCheckbox" style="cursor: pointer;">
                                                मैं इस आवेदन के साथ अपना सही आधार नंबर प्रस्तुत कर रही हूँ ताकि बाल विकास
                                                विभाग को
                                                सहमति
                                                देती हूँ कि मेरे आधार नंबर का उपयोग मौजूदा प्रमाणित करने एवं मेरा चयन हो
                                                जाने पर
                                                वेतन आदि का भुगतान आधार आधारित भुगतान हेतु किया जा सकता है !
                                            </label>
                                        </p>
                                    </div>
                                </div>
                                {{-- <div class="col-md-8 col-xs-12 text-left mt-2">
                                    <label class="form-label d-block mb-1" style="font-size: 13px; color: #000;">
                                        क्या आप जनमन क्षेत्र की निवासी हैं?
                                    </label>

                                    <div class="d-flex align-items-center gap-4">
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input AdharConfirm"
                                                name="isJanmanNiwasi" id="janmanYes" value="1" required
                                                {{ isset($data['applicant_details']) && $data['applicant_details']->isJanmanNiwasi == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="janmanYes" style="cursor: pointer;">
                                                हाँ
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input AdharConfirm"
                                                name="isJanmanNiwasi" id="janmanNo" value="0" required
                                                {{ isset($data['applicant_details']) && $data['applicant_details']->isJanmanNiwasi == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="janmanNo" style="cursor: pointer;">
                                                नहीं
                                            </label>
                                        </div>

                                    </div>
                                </div> --}}
                            </div><br>

                            <button style="float: right; margin-right: 10px; font-size:14px;" type="submit"
                                id="user-details-btn" class="btn btn-primary nextBtn btn-lg pull-right m-3">सबमिट करें और
                                आगे जाएं</button>
                        </form>
                    </div>
                </div><br>


                <div class="card" id="tab3" style="display: none;">
                    <div class="row"><br>
                        <form id="myForm3" action="{{ url('/candidate/education-details-update') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.education">
                            @csrf
                            <input type="hidden" name="fk_applicant_id" id="fk_applicant_id"
                                value="{{ session('uid') }}">
                            <input type="hidden" name="applicant_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->RowID : '' }}" />

                            <input type="hidden" name="apply_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->apply_id : '' }}" />

                            <div class="row">
                                @php
                                    $minyear = $data['minyear'];
                                    $maxyear = $data['maxyear'];
                                @endphp
                                <div class="col-md-12 feature-section">
                                    <input type="hidden" value="{{ $minyear }}" id="minyear">
                                    <input type="hidden" value="{{ $maxyear }}" id="maxyear">


                                    <table class="table table-responsive table-bordered mt-3 ms-0 ml-1 mb-2 "
                                        id="educationTable">
                                        <tr>
                                            <th>क्र.</th>
                                            <th style="width: 10%;">उत्तीर्ण परीक्षा</th>
                                            <th style="width: 20%;">विषय<label style="color:red">*</label></th>
                                            <th style="width: 12%;">वर्ष<label style="color:red">*</label></th>
                                            <th style="width: 12%;">प्राप्तांक<label style="color:red">*</label></th>
                                            <th style="width: 12%;">पूर्णांक<label style="color:red">*</label></th>
                                            <th style="width: 10%;">प्रतिशत (%)</th>
                                            {{-- <th style="width: 10%;">ग्रेड</th> --}}
                                            <th style="width: 20%;">बोर्ड/विश्वविद्यालय का नाम<label
                                                    style="color:red">*</label></th>
                                        </tr>

                                        @php
                                            // Map Quali_ID to row IDs for easier reference
                                            $qualiRows = [
                                                1 => 'class_5th',
                                                2 => 'class_8th',
                                                3 => 'ssc',
                                                4 => 'inter',
                                                // 5 => 'ug',
                                                // 6 => 'pg',
                                                // 7 => 'other',
                                            ];
                                            $serial = 1;
                                        @endphp

                                        @foreach ($qualiRows as $qualiId => $rowId)
                                            @php
                                                $eduDetail = isset($data['educationDetails'])
                                                    ? collect($data['educationDetails'])->firstWhere(
                                                        'fk_Quali_ID',
                                                        $qualiId,
                                                    )
                                                    : null;
                                                $isRequired = in_array($qualiId, [1]); // Only 5th required
                                                $isdisabled = in_array($qualiId, [1, 2, 3]); // Only 5th,8th,10th विषय disabled
                                            @endphp
                                            <tr id="{{ $rowId }}">
                                                <td>{{ $serial++ }}</td>


                                                <!-- उत्तीर्ण परीक्षा -->
                                                <td>
                                                    {{-- <select class="form-select" name="fk_Quali_ID[]"
                                                    {{ $isRequired ? 'required' : '' }}>
                                                    @foreach ($master_qualification as $qualification)
                                                        @if ($qualification->Quali_ID == $qualiId)
                                                            <option value="{{ $qualification->Quali_ID }}" selected>
                                                                {{ $qualification->Quali_Name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select> --}}
                                                    @foreach ($master_qualification as $qualification)
                                                        @if ($qualification->Quali_ID == $qualiId)
                                                            <input type="hidden" name="fk_Quali_ID[]"
                                                                class="form-control"
                                                                value="{{ $qualification->Quali_ID }}"
                                                                {{ $isRequired ? 'required' : '' }}>
                                                            <span>{{ $qualification->Quali_Name }}</span>
                                                        @endif
                                                    @endforeach
                                                </td>


                                                <!-- विषय -->
                                                <td>
                                                    <select class="form-select" name="fk_subject_id[]"
                                                        {{ $isRequired ? 'required' : '' }}>
                                                        <option selected disabled value="">-- चयन करें --</option>
                                                        @foreach ($subjects as $subject)
                                                            @if ($subject->fk_Quali_ID == $qualiId || ($qualiId == 7 && in_array($subject->fk_Quali_ID, [5, 6, 7])))
                                                                <option value="{{ $subject->subject_code }}"
                                                                    {{ (isset($eduDetail) && $eduDetail->fk_subject_id == $subject->subject_code) ||
                                                                    (isset($degree) && in_array($degree->fk_Quali_ID, [1, 2, 3]))
                                                                        ? 'selected'
                                                                        : '' }}>
                                                                    {{ $subject->subject_name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <!-- वर्ष -->
                                                <td>
                                                    <select name="year_passing[]" class="form-select"
                                                        {{ $isRequired ? 'required' : '' }}>
                                                        <option selected disabled value="">-- चयन करें --</option>
                                                        @for ($i = $minyear; $i <= $maxyear; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ isset($eduDetail) && $eduDetail->year_passing == $i ? 'selected' : '' }}>
                                                                {{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </td>

                                                <!-- प्राप्तांक -->
                                                <td>
                                                    <input type="text" name="obtained_marks[]" min="0"
                                                        value="{{ $eduDetail->obtained_marks ?? '' }}"
                                                        class="form-control decimal-only"
                                                        {{ $isRequired ? 'required' : '' }}
                                                        oninput="calc_percentage('{{ $rowId }}')">
                                                    <span class="calculation-error"
                                                        style="color:red; font-size: 12px;"></span>
                                                </td>

                                                <!-- पूर्णांक -->
                                                <td>
                                                    <input type="number" name="total_marks[]" min="1"
                                                        value="{{ $eduDetail->total_marks ?? '' }}"
                                                        class="form-control number-only"
                                                        {{ $isRequired ? 'required' : '' }}
                                                        oninput="calc_percentage('{{ $rowId }}')">
                                                    <span class="calculation-error"
                                                        style="color:red;font-size: 12px;"></span>
                                                </td>

                                                <!-- प्रतिशत -->
                                                <td>
                                                    <input type="text" name="percentage[]"
                                                        value="{{ $eduDetail->percentage ?? '' }}" class="form-control"
                                                        readonly>
                                                </td>

                                                <!-- Grade -->
                                                {{-- <td>
                                                    <select class="form-select" name="grade[]" ##{{ $isRequired ? 'required' : '' }} 
                                                        {{ $isdisabled ? '' : 'disabled' }}>
                                                        <option selected disabled value="">-- चयन करें --</option>
                                                        @foreach ($grades as $grade)
                                                            ##@if ($grade->fk_Quali_ID == $qualiId)
                                                            <option value="{{ $grade->grade_id }}"
                                                                {{ isset($eduDetail) && $eduDetail->fk_grade_id == $grade->grade_id ? 'selected' : '' }}>
                                                                {{ $grade->grade_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td> --}}

                                                <!-- बोर्ड/विश्वविद्यालय -->
                                                <td>
                                                    <input type="text" name="qualification_board[]"
                                                        value="{{ $eduDetail->qualification_board ?? '' }}"
                                                        class="form-control alphabets-only"
                                                        {{ $isRequired ? 'required' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </table>

                                </div>

                                <div class="col-md-12">
                                    <button style="float: right; margin-right: 10px; font-size:14px;" id="user-edu-btn"
                                        class="btn btn-primary nextBtn btn-lg pull-right mt-3" type="submit">सबमिट करें
                                        और आगे
                                        जाएं </button>
                                </div>

                                {{-- <div class="row col-md-12 mt-3">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-9">
                                        <div id="QualificationMessage"></div>
                                    </div>
                                </div> --}}


                            </div>
                        </form>

                        <script>
                            function calc_percentage(rowId) {
                                const row = document.getElementById(rowId);
                                const obtainedField = row.querySelector("input[name='obtained_marks[]']");
                                const totalField = row.querySelector("input[name='total_marks[]']");
                                const percentageField = row.querySelector("input[name='percentage[]']");
                                const errorSpan = row.querySelector(".calculation-error");

                                const obtained = parseFloat(obtainedField.value);
                                const total = parseFloat(totalField.value);

                                // Clear previous error
                                errorSpan.textContent = '';

                                if (isNaN(obtained) || isNaN(total)) {
                                    percentageField.value = '';
                                    return;
                                }

                                // Validation checks
                                if (obtained <= 0 || total <= 0) {
                                    errorSpan.textContent = "Marks 0 या negative नहीं हो सकते।";
                                    percentageField.value = '';
                                    $('#user-edu-btn').prop('disabled', true);

                                    return;
                                }

                                if (obtained > total) {
                                    errorSpan.textContent = "Obtained marks, total marks से ज्यादा नहीं हो सकते।";
                                    percentageField.value = '';
                                    $('#user-edu-btn').prop('disabled', true);

                                    return;
                                }

                                // Calculate and assign percentage
                                const percentage = ((obtained / total) * 100).toFixed(2);
                                percentageField.value = percentage;
                                $('#user-edu-btn').prop('disabled', false);

                            }
                        </script>
                    </div><br>
                </div>



                <div class="card" id="tab4" style="display: none;">
                    <div class="row container"><br>
                        <span class="text-start m-1 p-2 mb-3 text-danger border border-danger"
                            style="font-size: 14px;border-radius: 8px;">
                            <strong>नोट :</strong>
                            <span>अनुभव प्रमाण पत्र संबंधित बाल विकास परियोजना अधिकारी द्वारा हस्ताक्षरित अथवा उसके अभाव में
                                उसके सक्षम अधिकारी द्वारा हस्ताक्षरित ही मान्य होगा एवं सभी दस्तावेज स्वप्रमाणित होना
                                चाहिए।।
                            </span>
                        </span>
                        <form id="myForm4" action="{{ url('/candidate/experience-details-update') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.experience">
                            @csrf
                            <input type="hidden" name="applicant_id_tab4" id="applicant_id_tab4"
                                value="{{ session('uid') }}">
                            <input type="hidden" name="applicant_row_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->RowID : '' }}" />
                            <div class="col-md-12"><br>

                                <input type="hidden" name="apply_row_id"
                                    value="{{ isset($data['applicant_details']) ? $data['applicant_details']->apply_id : '' }}" />


                                <div class="field_wrapper" id="inputFieldsContainer">

                                    @if (isset($data['applicant_details'], $data['experience_details'], $data['experience_count']) &&
                                            $data['experience_count'] > 0)
                                        @foreach ($data['experience_details'] as $index => $experience_details)
                                            <div class="row experience-row mt-3 border-bottom  border-dark">
                                                <h5 class="text-primary">अनुभव जानकारी {{ $loop->iteration }} :</h5>
                                                <div class="row">
                                                    <div class="col-md-5 text-left mt-2">
                                                        <label for="orgname_0">संस्था का नाम </label>
                                                        <input type="text" id="orgname_0"
                                                            class="form-control alphabets-only"
                                                            value="{{ $experience_details->Organization_Name }}"
                                                            style="text-transform:uppercase;" name="org_name[]"
                                                            placeholder="संस्था का नाम/पता दर्ज करें">
                                                        <div class="invalid-feedback">This field is required</div>
                                                    </div>
                                                    <div class="col-md-3 text-left mt-2">
                                                        <label for="orgtype_0">संस्था शासकीय है अथवा अशासकीय</label>
                                                        <select id="orgtype_0" class="form-select org-type-select"
                                                            name="org_type[]">
                                                            <option selected disabled value="undefined">-- चयन करें --
                                                            </option>
                                                            @foreach ($organization_type as $org_type)
                                                                @if ($org_type->org_id != 4)
                                                                    <option value="{{ $org_type->org_id }}"
                                                                        {{ isset($experience_details) && $experience_details->Organization_Type == $org_type->org_id ? 'selected' : '' }}>
                                                                        {{ $org_type->org_type }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">This field is required</div>
                                                    </div>
                                                    <div class="col-md-4 text-left ngo-field" style="display: none;">
                                                        <label for="ngono_0">यदि संस्था अशासकीय है तो भारत शासन के NGO
                                                            पोर्टल में
                                                            पंजीयन क्र. <font color="red">* </font></label>
                                                        <input type="text" id="ngono_0" class="form-control"
                                                            value="{{ $experience_details->NGO_No }}" name="ngo_no[]"
                                                            placeholder="NGO पोर्टल में पंजीयन क्र दर्ज करें">
                                                    </div>
                                                    <div class="col-md-4 text-left mt-2">
                                                        <label for="nature"></label>संस्था का पूरा पता</label>
                                                        <textarea id="nature" type="text" class="form-control" name="org_address[]"
                                                            placeholder="संस्था का पूरा पता दर्ज करें">{{ $experience_details->org_address }}</textarea>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-left mt-2">
                                                        <label for="nature"></label>संस्था का दूरभाष</label>
                                                        <input type="text" id="org_contact"
                                                            class="form-control number-only" minlength="10"
                                                            maxlength="10"
                                                            value="{{ $experience_details->org_contact }}"
                                                            name="org_contact[]" placeholder="संस्था का दूरभाष दर्ज करें">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-left mt-2">
                                                        <label for="desgname">पदनाम</label>
                                                        <input type="text" id="desgname"
                                                            class="form-control alphabets-only"
                                                            value="{{ $experience_details->Designation }}"
                                                            style="text-transform:uppercase;" name="desg_name[]"
                                                            placeholder="पदनाम दर्ज करें">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 text-left mt-2">
                                                        <label for="nature">कार्य का विवरण</label>
                                                        <textarea id="nature" type="text" class="form-control alphabets-only" name="nature_work[]"
                                                            placeholder="कार्य का विवरण दर्ज करें">{{ $experience_details->Nature_Of_Work }}</textarea>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-left mt-2">
                                                        <label for="nature">मासिक वेतन / मानदेय</label>
                                                        <input id="salary" type="text" class="form-control"
                                                            value="{{ $experience_details->salary }}" name="salary[]"
                                                            placeholder="मासिक वेतन / मानदेय दर्ज करें">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-left mt-2">
                                                        <label for="dfrom">कब से</label>
                                                        <input type="date" id="dfrom" class="form-control"
                                                            value="{{ $experience_details->Date_From }}"
                                                            name="date_from[]" max="{{ $data['maxdate'] }}">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3  text-left mt-2">
                                                        <label for="dto">कब तक</label>
                                                        <input type="date" id="dto" class="form-control"
                                                            value="{{ $experience_details->Date_To }}" name="date_to[]"
                                                            max="{{ $data['maxdate'] }}">
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3  text-left mt-2">
                                                        <label for="dto">कुल अनुभव</label>
                                                        <input type="text" id="total_experience" class="form-control"
                                                            value="{{ $experience_details->Total_Experience }}"
                                                            name="total_experience[]" readonly>
                                                        <div id="error" class="invalid-feedback">
                                                            This field is required<br>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 col-xs-12 text-left mt-2">
                                                        <label for="sign">अनुभव प्रमाण पत्र / Upload Experience
                                                            Certificate </label><br>
                                                        @php
                                                            $file_path = '';
                                                            if (
                                                                isset($data['experience_details']) &&
                                                                $experience_details->exp_document
                                                            ) {
                                                                $file_path = asset(
                                                                    'uploads/' . $experience_details->exp_document,
                                                                );
                                                                if (config('app.env') === 'production') {
                                                                    $file_path =
                                                                        config('custom.file_point') .
                                                                        $experience_details->exp_document;
                                                                }
                                                            }
                                                        @endphp

                                                        @if (isset($data['experience_details']) && $experience_details->exp_document)
                                                            {{-- View existing file  --}}
                                                            <a class="myImg"
                                                                style="text-decoration:none; cursor: pointer;"
                                                                href="{{ $file_path }}" target="_blank"
                                                                id="view_file">📂<span
                                                                    class="btn btn-sm text-success">View
                                                                    Existing File <i class="bi bi-eye"></i></span></a> <br>

                                                            {{-- Edit file  --}}
                                                            <a href="#" class="editAllExperienceLink"
                                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                                    class="btn btn-sm text-primary"> Edit File <i
                                                                        class="bi bi-pencil"></i></span>
                                                            </a>
                                                            <input type="file"
                                                                class="form-control documentInput docExperience"
                                                                name="experience_document[]" data-key="अनुभव प्रमाण पत्र"
                                                                accept=".pdf" style="display: none">
                                                        @else
                                                            <input type="file"
                                                                class="form-control documentInput docExperience"
                                                                name="experience_document[]" data-key="अनुभव प्रमाण पत्र"
                                                                accept=".pdf">
                                                        @endif
                                                        <span class="file-preview-link"></span>
                                                        <br><span class="text-danger error-msg"
                                                            id="error-अनुभव_प्रमाण_पत्र"></span>

                                                    </div>

                                                    <div class="row col-md-12">
                                                        <div class="col-md-10 mb-2"></div>
                                                        <div class="col-md-2 mb-2">
                                                            <div class="">
                                                                <button type="button"
                                                                    class="btn btn-danger removeExp">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div> <br>
                                                {{-- <div class='row mt-1'>
                                                <div class='col-md-12 col-xw-12'>
                                                    <hr class="text-dark">
                                                </div>
                                            </div> --}}
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row experience-row mt-3 border-bottom  border-dark">
                                            <h5 class="text-primary">अनुभव जानकारी 1 :</h5>
                                            <div class="row">
                                                <div class="col-md-5 text-left mt-2">
                                                    <label for="orgname_0">संस्था का नाम </label>
                                                    <input type="text" id="orgname_0"
                                                        class="form-control alphabets-only exp_required" name="org_name[]"
                                                        style="text-transform:uppercase;"
                                                        placeholder="संस्था का नाम/पता दर्ज करें">
                                                    <div class="invalid-feedback">This field is required</div>
                                                </div>
                                                <div class="col-md-3 text-left mt-2">
                                                    <label for="orgtype_0">संस्था शासकीय है अथवा अशासकीय</label>
                                                    <select id="orgtype_0"
                                                        class="form-select org-type-select exp_required"
                                                        name="org_type[]">
                                                        <option selected disabled value="">-- चयन करें --</option>
                                                        @foreach ($organization_type as $org_type)
                                                            @if ($org_type->org_id != 4)
                                                                <option value="{{ $org_type->org_id }}">
                                                                    {{ $org_type->org_type }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback">This field is required</div>
                                                </div>
                                                <div class="col-md-4 text-left ngo-field" style="display: none;">
                                                    <label for="ngono_0">यदि संस्था अशासकीय है तो भारत शासन के NGO पोर्टल
                                                        में पंजीयन क्र.<label style="color:red">*</label></label>
                                                    <input type="text" id="ngono_0" class="form-control"
                                                        name="ngo_no[]" placeholder="NGO पोर्टल में पंजीयन क्र दर्ज करें">
                                                </div>
                                                <div class="col-md-4 text-left mt-2">
                                                    <label for="nature">संस्था का पूरा पता</label>
                                                    <textarea id="org_address" type="text" class="form-control exp_required" name="org_address[]"
                                                        placeholder="संस्था का पूरा पता दर्ज करें"></textarea>
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-left mt-2">
                                                    <label for="nature"></label>संस्था का दूरभाष</label>
                                                    <input type="text" id="org_contact"
                                                        class="form-control number-only exp_required" minlength="10"
                                                        maxlength="10" name="org_contact[]"
                                                        placeholder="संस्था का दूरभाष दर्ज करें">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 text-left mt-2">
                                                    <label for="desgname">पदनाम</label>
                                                    <input type="text" id="desgname"
                                                        class="form-control alphabets-only exp_required"
                                                        style="text-transform:uppercase;" name="desg_name[]"
                                                        placeholder="पदनाम दर्ज करें">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-5  text-left mt-2">
                                                    <label for="nature">कार्य का विवरण</label>
                                                    <textarea id="nature" type="text" class="form-control alphabets-only exp_required" name="nature_work[]"
                                                        placeholder="कार्य का विवरण दर्ज करें"></textarea>
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left mt-2">
                                                    <label for="nature">मासिक वेतन / मानदेय</label>
                                                    <input id="salary" type="text"
                                                        class="form-control exp_required" name="salary[]"
                                                        placeholder="मासिक वेतन / मानदेय दर्ज करें">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left mt-2">
                                                    <label for="dfrom">कब से</label>
                                                    <input type="date" id="dfrom"
                                                        class="form-control exp_required" name="date_from[]"
                                                        max="{{ $data['maxdate'] }}">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left mt-2">
                                                    <label for="dto">कब तक</label>
                                                    <input type="date" id="dto"
                                                        class="form-control exp_required" name="date_to[]"
                                                        max="{{ $data['maxdate'] }}">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left mt-2">
                                                    <label for="dto">कुल अनुभव</label>
                                                    <input type="text" id="total_experience"
                                                        class="form-control exp_required" name="total_experience[]">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                                    <label for="sign">अनुभव प्रमाण पत्र / Upload Experience
                                                        Certificate </label><br>

                                                    <input type="file" id="sign"
                                                        class="form-control documentInput exp_required"
                                                        name="exp_document[]" data-key="अनुभव प्रमाण पत्र"
                                                        accept=".pdf">
                                                    <span class="file-preview-link"></span>

                                                    <br><span class="text-danger error-msg"
                                                        id="error-अनुभव_प्रमाण_पत्र"></span>
                                                </div>
                                            </div>

                                        </div> <br>

                                </div>
                                @endif
                                <div id="experience-doc-error" class="invalid-feedback"> </div>
                            </div>


                            <!-- Add More and Remove buttons -->
                            <div class="col-md-4"><br>
                                <div class="d-flex">
                                    <button type="button" id="addmoreBtn" class="btn btn-info">Add
                                        More</button>&nbsp;&nbsp;
                                    <button style="display: none;" type="button"
                                        class="btn btn-danger removeBtn">Remove</button>
                                </div>
                            </div>


                            <button style="float: right; margin-right: 10px; font-size:14px;"
                                class="btn btn-primary btn-lg pull-right" id="user-exp-btn" type="submit">सबमिट करें
                                और आगे जाएं</button>
                    </div>
                    </form>
                </div><br>
            </div>

            <div class="card" id="tab5" style="display: none;">
                <div class="row container"><br>
                    <form id="myForm5" action="{{ url('/candidate/document-details-update') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="selectedCaste" id="selectedCasteInput"
                            value="{{ isset($data['applicant_details']) ? $data['applicant_details']->Caste : '' }}">

                        <input type="hidden" name="applicant_id_tab5" id="applicant_id_tab5"
                            value="{{ session('uid') }}">

                        <input type="hidden" name="applicant_row_id"
                            value="{{ isset($data['applicant_details']) ? $data['applicant_details']->RowID : '' }}" />
                        <input type="hidden" name="apply_row_id"
                            value="{{ isset($data['applicant_details']) ? $data['applicant_details']->apply_id : '' }}" />


                        <div class="col-md-12"><br>
                            <div class="field_wrapper" id="inputFieldsContainer">

                                <div class="row">
                                    <span class="text-start m-2 p-1 mb-3 text-danger border border-danger"
                                        style="font-size: 14px;border-radius: 8px;">
                                        <strong>नोट :</strong> <br>
                                        <ul>
                                            <li>**पासपोर्ट साइज़ फोटो:** इसका आकार 100KB से ज़्यादा नहीं होना चाहिए, और
                                                यह
                                                PNG, JPG, या JPEG फ़ॉर्मेट में होनी चाहिए।</li>
                                            <li>**हस्ताक्षर फ़ाइल:** इसका आकार 50KB से ज़्यादा नहीं होना चाहिए, और यह भी
                                                PNG, JPG, या JPEG फ़ॉर्मेट में होनी चाहिए।</li>
                                            <li>**अन्य सभी फ़ाइलें (जैसे PDF):** इनका आकार 2MB से ज़्यादा नहीं होना
                                                चाहिए।
                                            </li>
                                            <li>**स्थानीय निवास प्रमाण पत्र विकासखंडदार द्वारा हस्ताक्षरित अथवा उसके अभाव
                                                में
                                                उसके सक्षम अधिकारी द्वारा हस्ताक्षरित होना चाहिए।
                                            </li>
                                            <li>**सभी दस्तावेज उसके सक्षम अधिकारी द्वारा हस्ताक्षरित एवं स्वप्रमाणित
                                                होना चाहिए।
                                            </li>
                                            <li>**यदि आपकी मार्कशीट ग्रेड-आधारित है, तो प्राचार्य द्वारा जारी वह पत्र,
                                                जिसमें प्राप्तांक एवं पूर्णांक अंकित हों, उसे इस <a
                                                    href="https://www.ilovepdf.com/merge_pdf">click me</a> के माध्यम से
                                                मर्ज करके अपलोड करें।</li>
                                        </ul>
                                    </span>

                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="photo">पासपोर्ट साइज़ फोटो अपलोड करें / Upload Photo <font
                                                color="red">*</font></label>
                                        <br>
                                        {{-- View existing file  --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Photo
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Photo,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Photo;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Photo)
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a> <br>

                                            {{-- Edit file  --}}
                                            <a href="#" id="editPhotoLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>

                                            <input id="photo" type="file" class="form-control documentInput"
                                                accept="image/*" name="document_photo" data-key="पासपोर्ट फोटो"
                                                style="display: none;">
                                            {{-- View uploaded file  --}}
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input id="photo" type="file" class="form-control documentInput yes"
                                                accept="image/*" name="document_photo" data-key="पासपोर्ट फोटो" required>
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br>
                                        <span class="text-danger error-msg" id="error-पासपोर्ट_फोटो"></span>
                                        <div id="photo-error" class="invalid-feedback"> </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign">हस्ताक्षर अपलोड करें / Upload Signature <font
                                                color="red">*
                                            </font></label>
                                        <br>
                                        {{-- View existing file  --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Sign
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Sign,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Sign;
                                                }
                                            }
                                        @endphp
                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Sign)
                                            {{-- View existing file  --}}
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file"><span
                                                    class="btn btn-sm text-info">View Document <i
                                                        class="bi bi-eye"></i></span></a><br>
                                            {{-- Edit file  --}}
                                            <a href="#" id="editSignLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" id="document_sign" class="form-control documentInput"
                                                data-key="हस्ताक्षर" accept="image/*" name="document_sign"
                                                style="display: none;">

                                            {{-- View uploaded file  --}}
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" id="document_sign"
                                                class="form-control documentInput yes" accept="image/*"
                                                name="document_sign" data-key="हस्ताक्षर" required>
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br>
                                        <span class="text-danger error-msg" id="error-हस्ताक्षर"></span>
                                        <div id="sign-error" class="invalid-feedback"> </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign">आधार अपलोड करें / Upload AADHAAR </label><br>
                                        {{-- file Path --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Aadhar
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Aadhar,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Aadhar;
                                                }
                                            }
                                        @endphp
                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Aadhar)
                                            {{-- View existing file  --}}
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file"><span
                                                    class="btn btn-sm text-info">View Document <i
                                                        class="bi bi-eye"></i></span></a> <br>

                                            {{-- Edit file  --}}
                                            <a href="#" id="editAadharLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" id="document_adhaar"
                                                class="form-control documentInput" data-key="आधार"
                                                name="document_adhaar" accept="image/*,.pdf" style="display: none;">
                                            {{-- View uploaded file  --}}
                                            <span class="file-preview-link"></span>
                                        @else
                                            {{-- Upload new file  --}}
                                            <input type="file" id="document_adhaar"
                                                class="form-control documentInput yes" name="document_adhaar"
                                                accept="image/*,.pdf" data-key="आधार">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-आधार"></span>
                                        <div id="aadhaar-doc-error" class="invalid-feedback"> </div>
                                    </div>


                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign">स्थानीय निवास प्रमाण पत्र अपलोड करें / Upload Domicile <font
                                                color="red">*</font></label><br>
                                        {{-- file Path --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Domicile
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Domicile,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Domicile;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Domicile)
                                            {{-- View existing file  --}}

                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>

                                            {{-- Edit file  --}}
                                            <a href="#" id="editDomicialLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" class="form-control documentInput" name="domicile"
                                                accept=".pdf" data-key="स्थानीय निवास प्रमाण पत्र"
                                                id="document_domicile" style="display: none;">
                                            {{-- View uploaded file  --}}

                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" class="form-control documentInput yes"
                                                id="document_domicile" name="domicile" accept=".pdf"
                                                data-key="स्थानीय निवास प्रमाण पत्र" required>
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br>
                                        <span class="text-danger error-msg" id="error-मूल_निवासी_प्रमाण_पत्र"></span>
                                        <div id="domicile-doc-error" class="invalid-feedback"> </div>
                                    </div>


                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="photo" id="caste_astrick">जाति प्रमाण पत्र अपलोड करें /
                                            Upload
                                            Caste Certificate
                                        </label><br>
                                        {{-- file Path --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Caste
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Caste,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Caste;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Caste)
                                            {{-- View existing file  --}}

                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>
                                            {{-- Edit file  --}}
                                            <a href="#" id="editCasteLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input id="caste_certificate" type="file"
                                                class="form-control casteInput" name="caste_certificate"
                                                accept=".pdf" data-key="जाति प्रमाण पत्र" style="display: none;">
                                            {{-- View uploaded file  --}}

                                            <span class="file-preview-link"></span>
                                        @else
                                            <input id="caste_certificate" type="file"
                                                class="form-control casteInput" name="caste_certificate"
                                                accept=".pdf" data-key="जाति प्रमाण पत्र">
                                            <span class="file-preview-link"></span><br>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-जाति_प्रमाण_पत्र"></span>
                                        <div id="caste_certificate_error" class="invalid-feedback">
                                        </div>

                                    </div>


                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign" id="5th_astrick">5वीं अंक सूची / Upload 5th Marksheet
                                            <font color="red">*</font>
                                        </label><br>
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_5th
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_5th,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_5th;
                                                }
                                            }
                                        @endphp
                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_5th)
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a>
                                            <input type="file" id="5th_marksheet"
                                                class="form-control documentInput" name="5th_marksheet"
                                                data-key="5वीं प्रमाण पत्र" accept=".pdf">
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" id="5th_marksheet"
                                                class="form-control documentInput" name="5th_marksheet" accept=".pdf"
                                                data-key="5वीं प्रमाण पत्र">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-5वीं_प्रमाण_पत्र"></span>
                                        <div id="fifth-doc-error" class="invalid-feedback"> </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign" id="8th_astrick">8वीं अंक सूची / Upload 8th Marksheet
                                            <font color="red">*</font>
                                        </label><br>
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_8th
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_8th,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_8th;
                                                }
                                            }
                                        @endphp
                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_8th)
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a>
                                            <input type="file" id="8th_marksheet"
                                                class="form-control documentInput" name="8th_marksheet"
                                                data-key="8वीं प्रमाण पत्र" accept=".pdf">
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" id="8th_marksheet"
                                                class="form-control documentInput" name="8th_marksheet" accept=".pdf"
                                                data-key="8वीं प्रमाण पत्र">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br>
                                        <span class="text-danger error-msg" id="error-8वीं_प्रमाण_पत्र"></span>
                                        <div id="eight-doc-error" class="invalid-feedback"> </div>
                                    </div>


                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign"id="ssc_astrick">10वीं अंक सूची / Upload 10th
                                            Marksheet <font color="red">*</font>
                                        </label><br>
                                        {{-- file Path --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_SSC
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_SSC,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_SSC;
                                                }
                                            }
                                        @endphp
                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_SSC)
                                            {{-- View existing file  --}}
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a> <br>

                                            {{-- Edit file  --}}
                                            <a href="#" id="editTenthLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" id="ssc_marksheet"
                                                class="form-control documentInput" name="ssc_marksheet"
                                                data-key="10वीं प्रमाण पत्र" accept=".pdf" style="display: none;">
                                            {{-- View uploaded file  --}}

                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" id="ssc_marksheet"
                                                class="form-control documentInput" name="ssc_marksheet" accept=".pdf"
                                                data-key="10वीं प्रमाण पत्र">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br>
                                        <span class="text-danger error-msg" id="error-10वीं_प्रमाण_पत्र"></span>
                                        <div id="tenth-doc-error" class="invalid-feedback"> </div>

                                    </div>

                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="photo" id="inter_astrick">12वीं अंक सूची / Upload 12th
                                            Marksheet <font color="red">*</font>
                                        </label><br>
                                        {{-- file Path --}}
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Inter
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Inter,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Inter;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Inter)
                                            {{-- View existing file  --}}

                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a> <br>
                                            {{-- Edit file  --}}
                                            <a href="#" id="editTwellthLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" class="form-control documentInput"
                                                name="inter_marksheet" id="inter_marksheet"
                                                data-key="12वीं प्रमाण पत्र" accept=".pdf" style="display: none;">
                                            {{-- View uploaded file  --}}
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" class="form-control documentInput"
                                                name="inter_marksheet" id="inter_marksheet"
                                                data-key="12वीं प्रमाण पत्र" accept=".pdf">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-12वीं_प्रमाण_पत्र"></span>
                                        <div id="12th-doc-error" class="invalid-feedback"> </div>
                                    </div>


                                    {{-- <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign" id="ug_astrick">UG प्रमाण पत्र / Upload UG
                                            Marksheet</label><br>
                                        // file Path 
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_UG
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_UG,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_UG;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_UG)
                                            // View existing file 

                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>

                                            // Edit file  
                                            <a href="#" id="editUGLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" class="form-control documentInput" id="ug_marksheet"
                                                name="ug_marksheet" accept=".pdf" data-key="UG प्रमाण पत्र"
                                                style="display: none;">
                                            // View uploaded file  
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" class="form-control documentInput" id="ug_marksheet"
                                                name="ug_marksheet" data-key="UG प्रमाण पत्र" accept=".pdf">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-UG_प्रमाण_पत्र"></span>
                                        <div id="UG-doc-error" class="invalid-feedback"> </div>

                                    </div>

                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign" id="pg_astrick">PG प्रमाण पत्र / Upload PG
                                            Marksheet</label><br>
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_PG
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_PG,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_PG;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_PG)
                                           // View existing file 

                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>

                                            // Edit file  
                                            <a href="#" id="editPGLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" class="form-control documentInput" id="pg_marksheet"
                                                name="pg_marksheet" accept=".pdf" data-key="PG प्रमाण पत्र"
                                                style="display: none;">
                                            // View uploaded file  
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" class="form-control documentInput" id="pg_marksheet"
                                                name="pg_marksheet" accept=".pdf" data-key="PG प्रमाण पत्र">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-PG_प्रमाण_पत्र"></span>
                                        <div id="PG-doc-error" class="invalid-feedback"> </div>
                                    </div> --}}


                                    <div class="col-md-4 col-xs-12 text-left mt-4">
                                        <label for="sign">अद्यतन मतदाता सूची अपलोड करें / Upload Epic
                                            Certificate </label><br>
                                        {{-- file Path --}}

                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Epic
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Epic,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Epic;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Epic)
                                            {{-- View existing file  --}}
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>

                                            {{-- Edit file  --}}
                                            <a href="#" id="editEPICLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" id="Epic_document"
                                                class="form-control documentInput" name="epic_document"
                                                data-key="अद्यतन मतदाता सूची" accept=".pdf" style="display: none;">
                                            {{-- View uploaded file  --}}
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" id="Epic_document"
                                                class="form-control documentInput yes" name="epic_document"
                                                data-key="अद्यतन मतदाता सूची" accept=".pdf">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br>
                                        <span class="text-danger error-msg" id="error-मतदाता_प्रमाण_पत्र"></span>
                                        <div id="epic-doc-error" class="invalid-feedback"> </div>

                                    </div>

                                </div><br>


                                <h5>अन्य दस्तावेज़</h5>
                                <hr>

                                <div class="row">
                                    <div class="col-md-4 col-xs-12 text-left mt-2">
                                        <label for="sign" id="other_astrick">अन्य दस्तावेज अपलोड करें /
                                            Upload other Document
                                        </label><br>

                                        <!-- Display existing other documents -->
                                        @php
                                            $existingOtherDocs = [];
                                            if (
                                                isset($data['applicant_details']) &&
                                                isset($data['applicant_details']->RowID)
                                            ) {
                                                $existingOtherDocs = DB::table('tbl_user_other_documents')
                                                    ->where('fk_applicant_id', $data['applicant_details']->RowID)
                                                    ->orderBy('id', 'asc')
                                                    ->get();
                                            }
                                        @endphp

                                        <div id="existing-other-documents">

                                            @if ($existingOtherDocs && count($existingOtherDocs) > 0)
                                                <div class="mb-3">
                                                    @foreach ($existingOtherDocs as $index => $doc)
                                                        @php
                                                            $file_path = asset('uploads/' . $doc->other_documents);
                                                            if (config('app.env') === 'production') {
                                                                $file_path =
                                                                    config('custom.file_point') . $doc->other_documents;
                                                            }
                                                        @endphp
                                                        <div
                                                            class="existing-doc-row d-flex align-items-center mb-2 border p-2 rounded">
                                                            <div class="flex-grow-1">
                                                                <span class="doc-name">Document
                                                                    {{ $index + 1 }}</span>
                                                                <input type="hidden" name="existing_other_docs[]"
                                                                    value="{{ $doc->other_documents }}">
                                                            </div>
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-info view-doc-btn"
                                                                    title="View" data-bs-toggle="modal"
                                                                    data-bs-target="#fileViewModal"
                                                                    data-file-url="{{ $file_path }}">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger remove-existing-doc"
                                                                    title="Remove"
                                                                    data-file="{{ $doc->other_documents }}">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Wrapper for multiple new other document inputs -->
                                        <div id="other-documents-wrapper">
                                            <div class="other-doc-row input-group mb-2">
                                                <input type="file" name="other_marksheet[]"
                                                    class="form-control documentInput other-document" accept=".pdf"
                                                    data-key="अन्य दस्तावेज">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm ms-2 remove-other-doc"
                                                    style="display:none;">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>

                                        <button type="button" id="add-other-doc-btn"
                                            class="btn btn-info btn-sm mt-2">Add More</button>

                                        <span class="file-preview-link"></span>
                                        <br><span class="text-danger error-msg" id="error-अन्य_प्रमाण_पत्र"></span>
                                        <div id="other-doc-error" class="invalid-feedback"></div>

                                        <!-- Hidden field to track deleted files -->
                                        <input type="hidden" name="deleted_other_docs" id="deleted_other_docs"
                                            value="">
                                    </div>
                                </div>

                                <!-- File View Modal -->
                                <div class="modal fade" id="fileViewModal" tabindex="-1"
                                    aria-labelledby="fileViewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="fileViewModalLabel">Document View</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <iframe id="fileViewFrame" src=""
                                                        style="width: 100%; height: 600px; border: none;"></iframe>
                                                    <div id="fileViewError" class="text-danger mt-3"
                                                        style="display: none;">
                                                        Unable to load the document. Please try again or download the file.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <a id="downloadFileBtn" class="btn btn-primary" href=""
                                                    download>
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const wrapper = document.getElementById('other-documents-wrapper');
                                        const addBtn = document.getElementById('add-other-doc-btn');
                                        const deletedDocsInput = document.getElementById('deleted_other_docs');
                                        let deletedFiles = [];

                                        // Modal elements
                                        const fileViewFrame = document.getElementById('fileViewFrame');
                                        const downloadFileBtn = document.getElementById('downloadFileBtn');
                                        const fileViewError = document.getElementById('fileViewError');

                                        // Add new file input
                                        addBtn.addEventListener('click', function() {
                                            const row = document.createElement('div');
                                            row.className = 'other-doc-row input-group mb-2';
                                            row.innerHTML = `
                <input type="file" name="other_marksheet[]" class="form-control documentInput other-document" accept=".pdf" data-key="अन्य दस्तावेज">
                <button type="button" class="btn btn-danger btn-sm ms-2 remove-other-doc">Remove</button>`;
                                            wrapper.appendChild(row);
                                            updateRemoveButtons();
                                        });

                                        // Remove new file input
                                        wrapper.addEventListener('click', function(e) {
                                            if (e.target && e.target.classList.contains('remove-other-doc')) {
                                                const row = e.target.closest('.other-doc-row');
                                                if (row) {
                                                    row.remove();
                                                    updateRemoveButtons();
                                                }
                                            }
                                        });

                                        // Handle view document button click
                                        document.addEventListener('click', function(e) {
                                            // View document button click
                                            if (e.target && (e.target.classList.contains('view-doc-btn') ||
                                                    e.target.closest('.view-doc-btn'))) {

                                                const button = e.target.classList.contains('view-doc-btn') ?
                                                    e.target : e.target.closest('.view-doc-btn');

                                                const fileUrl = button.getAttribute('data-file-url');

                                                if (fileUrl) {
                                                    // Set the iframe source
                                                    fileViewFrame.src = fileUrl;
                                                    // Set download link
                                                    downloadFileBtn.href = fileUrl;
                                                    // Hide error message
                                                    fileViewError.style.display = 'none';

                                                    // Handle iframe load error
                                                    fileViewFrame.onerror = function() {
                                                        fileViewError.style.display = 'block';
                                                        fileViewFrame.style.display = 'none';
                                                    };

                                                    fileViewFrame.onload = function() {
                                                        fileViewError.style.display = 'none';
                                                        fileViewFrame.style.display = 'block';
                                                    };
                                                }
                                            }

                                            // Remove existing document button click
                                            if (e.target && (e.target.classList.contains('remove-existing-doc') ||
                                                    e.target.closest('.remove-existing-doc'))) {

                                                const button = e.target.classList.contains('remove-existing-doc') ?
                                                    e.target : e.target.closest('.remove-existing-doc');

                                                const docRow = button.closest('.existing-doc-row');
                                                const fileName = button.getAttribute('data-file');

                                                if (docRow && fileName) {
                                                    // Add to deleted files array
                                                    deletedFiles.push(fileName);
                                                    deletedDocsInput.value = JSON.stringify(deletedFiles);

                                                    // Mark as removed (hide with animation)
                                                    docRow.style.opacity = '0.5';
                                                    docRow.style.textDecoration = 'line-through';
                                                    docRow.style.backgroundColor = '#f8d7da';

                                                    // Hide action buttons
                                                    const btnGroup = docRow.querySelector('.btn-group');
                                                    if (btnGroup) btnGroup.style.display = 'none';

                                                    // Add removed indicator
                                                    const removedText = document.createElement('span');
                                                    removedText.className = 'text-danger ms-2';
                                                    removedText.innerHTML = '<i class="bi bi-x-circle"></i> Will be removed';
                                                    docRow.appendChild(removedText);

                                                    // Show undo button
                                                    const undoBtn = document.createElement('button');
                                                    undoBtn.type = 'button';
                                                    undoBtn.className = 'btn btn-sm btn-warning ms-2';
                                                    undoBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Undo';
                                                    undoBtn.onclick = function() {
                                                        // Remove from deleted files
                                                        const index = deletedFiles.indexOf(fileName);
                                                        if (index > -1) {
                                                            deletedFiles.splice(index, 1);
                                                            deletedDocsInput.value = JSON.stringify(deletedFiles);
                                                        }

                                                        // Restore row
                                                        docRow.style.opacity = '';
                                                        docRow.style.textDecoration = '';
                                                        docRow.style.backgroundColor = '';

                                                        // Show action buttons
                                                        btnGroup.style.display = '';

                                                        // Remove indicators
                                                        removedText.remove();
                                                        undoBtn.remove();
                                                    };
                                                    docRow.appendChild(undoBtn);
                                                }
                                            }
                                        });

                                        // Initialize remove button visibility
                                        function updateRemoveButtons() {
                                            const rows = wrapper.querySelectorAll('.other-doc-row');
                                            rows.forEach(row => {
                                                const removeBtn = row.querySelector('.remove-other-doc');
                                                if (removeBtn) {
                                                    // Show remove button only if there are more than 1 rows
                                                    removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
                                                }
                                            });
                                        }

                                        // Reset modal when closed
                                        const fileViewModal = document.getElementById('fileViewModal');
                                        fileViewModal.addEventListener('hidden.bs.modal', function() {
                                            fileViewFrame.src = '';
                                            fileViewError.style.display = 'none';
                                            fileViewFrame.style.display = 'block';
                                        });

                                        // Initialize on page load
                                        updateRemoveButtons();
                                    });
                                </script>

                                <style>
                                    .existing-doc-row {
                                        transition: all 0.3s ease;
                                    }

                                    .doc-name {
                                        font-weight: 500;
                                    }

                                    .view-doc-btn:hover {
                                        background-color: #0b5ed7 !important;
                                        color: white !important;
                                    }

                                    .remove-existing-doc:hover {
                                        background-color: #bb2d3b !important;
                                        color: white !important;
                                    }

                                    .other-doc-row .remove-other-doc {
                                        transition: all 0.2s ease;
                                    }
                                </style>

                                {{-- Divorce Certificate Upload Section --}}
                                {{-- <div class="col-md-4 col-xs-12 text-left mt-2">
                                        <label for="sign" id="other_astrick">अन्य दस्तावेज अपलोड करें /
                                            Upload other Document
                                        </label><br>
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_other
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_other,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_other;
                                                }
                                            }
                                        @endphp
                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_other)

                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>
                                            <a href="#" id="editOtherLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" id="other_marksheet"
                                                class="form-control documentInput" name="other_marksheet"
                                                data-key="अन्य दस्तावेज" accept=".pdf" style="display: none;">
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input type="file" id="other_marksheet"
                                                class="form-control documentInput" name="other_marksheet"
                                                accept=".pdf" data-key="अन्य दस्तावेज">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg" id="error-अन्य_प्रमाण_पत्र"></span>
                                        <div id="other-doc-error" class="invalid-feedback"> </div>
                                    </div> --}}
                                {{-- <div class="col-md-4 col-xs-12 text-left mt-2">
                                        <label for="sign" id="BPL_astrick">BPL (गरीबी रेखा) प्रमाण पत्र अपलोड करें/
                                            Upload BPL Certificate</label><br>
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_BPL
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_BPL,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_BPL;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_BPL)
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a><br>

                                            <a href="#" id="editBPLLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input type="file" class="form-control documentInput" id="BPL_document"
                                                name="bpl_marksheet" data-key="BPL प्रमाण पत्र" accept=".pdf"
                                                style="display: none;">
                                        @else
                                            <input type="file" class="form-control documentInput yes"
                                                id="BPL_document" name="bpl_marksheet" data-key="BPL प्रमाण पत्र"
                                                accept=".pdf">
                                        @endif
                                        <span class="file-preview-link"></span>
                                        <br><span class="text-danger error-msg" id="error-BPL_प्रमाण_पत्र"></span>
                                        <div id="BPL-doc-error" class="invalid-feedback"> </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12 text-left mt-2">
                                        <label for="photo" id="Widow_astrick">विधवा : मृत्यु प्रमाण पत्र अपलोड
                                            करें<br>
                                            परित्यक्ता/तलाकशुदा : तलाक प्रमाणपत्र अपलोड करें </label><br>
                                        @php
                                            $file_path = '';
                                            if (
                                                isset($data['applicant_details']) &&
                                                $data['applicant_details']->Document_Widow
                                            ) {
                                                $file_path = asset(
                                                    'uploads/' . $data['applicant_details']->Document_Widow,
                                                );
                                                if (config('app.env') === 'production') {
                                                    $file_path =
                                                        config('custom.file_point') .
                                                        $data['applicant_details']->Document_Widow;
                                                }
                                            }
                                        @endphp

                                        @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Widow)
                                            <a class="myImg" style="text-decoration:none; cursor: pointer;"
                                                href="{{ $file_path }}" target="_blank" id="view_file">📂<span
                                                    class="btn btn-sm text-info">View File <i
                                                        class="bi bi-eye"></i></span></a> <br>

                                            <a href="#" id="editWidowLink"
                                                style="text-decoration:none; cursor: pointer;"> 📂<span
                                                    class="btn btn-sm text-primary"> Edit File <i
                                                        class="bi bi-pencil"></i></span></a>
                                            <input id="widow_divorce_document" type="file"
                                                class="form-control documentInput" name="widow_certificate"
                                                accept=".pdf" data-key="विधवा/परित्यक्ता का प्रमाण पत्र"
                                                style="display: none;">
                                            <span class="file-preview-link"></span>
                                        @else
                                            <input id="widow_divorce_document" type="file"
                                                class="form-control documentInput yes" name="widow_certificate"
                                                accept=".pdf" data-key="विधवा/परित्यक्ता का प्रमाण पत्र">
                                            <span class="file-preview-link"></span>
                                        @endif
                                        <br><span class="text-danger error-msg"
                                            id="error-विधवा_परित्यक्ता_का_प्रमाण_पत्र"></span>
                                        <div id="widow-doc-error" class="invalid-feedback"> </div>
                                    </div> --}}

                            </div>
                            <button style="float: right; margin-right: 10px; font-size:14px;"
                                class="btn btn-primary btn-lg pull-right" id="user-doc-btn" type="submit"><i
                                    class="bi bi-file-check"></i> आवेदन
                                करें </button>
                        </div>
                    </form>
                </div><br>
            </div>
        </div>
        </div>
        <!-- Bootstrap Modal -->
        <div class="modal fade" id="documentModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" aria-labelledby="documentModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentModalLabel">दस्तावेज़
                            पूर्वावलोकन</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <!-- PDF Viewer -->
                        <iframe id="docPreview" src="" width="100%" height="500px"
                            style="border: none; display: none;"></iframe>

                        <!-- Image Viewer -->
                        <img id="imgPreview" src="" style="width: 100%; display: none; max-height: 90vh;" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple loader -->
        <div id="loader"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; ">
            <div class="spinner"
                style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;">
            </div>
            <p>कृपया प्रतीक्षा करें...</p>
        </div>



    </main>

@endsection
@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    {{--
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.18.0/js/md5.min.js"></script>

    <script>
        $(document).ready(function() {

            // Change #1 applied for dynamic and static view selected documents:
            $(document).on('change', '.documentInput, .casteInput, .QFileInput, .file-input, .fd-file', function(
                e) {
                const file = e.target.files[0];
                const previewLinkSpan = $(this).next('span')[0];

                if (file) {
                    const fileURL = URL.createObjectURL(file);
                    const fileType = file.type;

                    $(previewLinkSpan).html(
                        ` <br><a href="#" class="previewDoc btn btn-sm text-info me-3" data-file="${fileURL}" data-type="${fileType}" style="text-decoration:none;font-size:15px;">View Document <i class="bi bi-eye"></i></a>`
                    );
                } else {
                    $(previewLinkSpan).empty();
                }
            });

            //  Change #3 applied here:
            $(document).on('click', '.previewDoc', function(e) {
                e.preventDefault();
                const fileURL = $(this).data('file');
                const fileType = $(this).data('type');
                showFileInModal(fileURL, fileType);
            });


            //## show modal for view uploaded documents
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



            // $('#epic').on('input', function() {
            //     let value = $(this).val().toUpperCase();
            //     value = value.replace(/[^A-Z0-9]/g, '');
            //     let letters = value.slice(0, 3).replace(/[^A-Z]/g, '');
            //     let numbers = value.slice(3).replace(/[^0-9]/g, '').slice(0, 7);
            //     value = letters + numbers;
            //     $(this).val(value.slice(0, 10));

            //     // Custom validation
            //     if (value.length < 10) {
            //         $(this).addClass('is-invalid'); // Bootstrap invalid class (if using Bootstrap)
            //         $(this).next('.invalid-feedback').text(
            //             'EPIC number must be exactly 10 characters (3 letters + 7 digits).');
            //     } else {
            //         $(this).removeClass('is-invalid');
            //         $(this).next('.invalid-feedback').text('');
            //     }
            // });

            $(document).on('input', '.alphabets-only', function() {
                $(this).val(function(_, val) {
                    return val.replace(/[^\u0900-\u097Fa-zA-Z.\-\s]/g, '');
                });
            });

            $(document).on('input', '.number-only', function() {
                $(this).val(function(_, val) {
                    return val.replace(/[^0-9]/g, '');
                });
            });
            $(document).on('input', '.decimal-only', function() {
                this.value = this.value
                    .replace(/[^0-9.]/g, '') // Remove everything except digits and decimal
                    .replace(/(\..*)\./g, '$1'); // Allow only one decimal point
            });
            $('form input[type="date"]').change(function() {
                var element = $(this);
                if (element.val()) {
                    // Remove the is-invalid class and add the was-validated class
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    // Remove the was-validated class and add the is-invalid class
                    element.removeClass('was-validated');
                    element.addClass('is-invalid');
                }
            });

            $('form input[type="file"]').change(function() {
                var element = $(this);
                if (element.val()) {
                    // Remove the is-invalid class and add the was-validated class
                    element.removeClass('is-invalid');
                    element.addClass('was-validated');
                } else {
                    // Remove the was-validated class and add the is-invalid class
                    element.removeClass('was-validated');
                    element.addClass('is-invalid');
                }
            });


            //to add and remove class from dropdown
            $('form select').change(function() {
                if (this.value) {
                    var element = $(this);
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('is-invalid');
                        element.addClass('was-validated');
                    }
                } else {
                    var element = $(this);
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('was-validated');
                        element.addClass('is-invalid');
                    }
                }
            });




            function checkAndLockPermanentAddress() {
                if ($('#same').prop('checked')) {
                    $('#paddr').attr('readonly', true);
                    $('#per_district').attr('disabled', true);
                    $('#ppincode').attr('disabled', true);
                }
            }
            // checkAndLockPermanentAddress();



            // Initial state setup

            // ============================
            // ## Area Toggle Handler for Post Form
            // ============================
            function handleAreaChange() {
                const val = $('#area').val();

                $('#Rular-select, #Urban-select').hide();
                $('#block, #gp, #village_id, #nagar, #ward').removeAttr('required');
                // $('#questionsContainer, #skillsContainer').hide();

                if (val === '1') {
                    $('#Rular-select').show();
                    $('#block, #gp,#village_id').attr('required', true);
                    if (!$('#nagar').val()) $('#nagar').val('undefined').change();
                    if (!$('#ward').val()) $('#ward').val('undefined').change();

                } else if (val === '2') {
                    $('#Urban-select').show();
                    $('#nagar, #ward').attr('required', true);
                    if (!$('#block').val()) $('#block').val('undefined').change();
                    if (!$('#gp').val()) $('#gp').val('undefined').change();
                    if (!$('#village_id').val()) $('#village_id').val('undefined').change();
                }
            }

            $('#area').on('change', handleAreaChange);
            handleAreaChange();

            // ============================
            // ## Area Toggle Handler for Personal Details Form
            // ============================
            function HandleAreaChangePer() {
                const val = $('#area1').val();

                $('#hidden-block, #hidden-gp, #hidden-village, #hidden-nagar, #hidden-ward').hide();
                $('#block1, #gp1,#village_id1, #nagar1, #ward1').removeAttr('required');

                if (val === '1') {
                    $('#hidden-block, #hidden-gp,#hidden-village').show();
                    $('#block1, #gp1, #village_id1').attr('required', true);
                    if (!$('#nagar1').val()) $('#nagar1').val('undefined').change();
                    if (!$('#ward1').val()) $('#ward1').val('undefined').change();
                } else if (val === '2') {
                    $('#hidden-nagar, #hidden-ward').show();
                    $('#nagar1, #ward1').attr('required', true);
                    if (!$('#block1').val()) $('#block1').val('undefined').change();
                    if (!$('#gp1').val()) $('#gp1').val('undefined').change();
                    if (!$('#village_id1').val()) $('#village_id1').val('undefined').change();
                }
            }
            $('#area1').on('change', HandleAreaChangePer);
            HandleAreaChangePer();


            // ============================
            // ## Caste Change Handler
            // ============================
            function handleCasteChange(selectedCaste, hasDocument) {
                sessionStorage.setItem('selectedCaste', selectedCaste);
                const casteLabel = $('#caste_astrick');

                // Remove old asterisk
                casteLabel.find('font[color="red"]').remove();
                $('#caste_certificate').prop('required', false);

                if (selectedCaste !== 'सामान्य' && !hasDocument) {
                    casteLabel.append(' <font color="red">*</font>');
                    $('#caste_certificate').prop('required', true);
                }
            }

            const existingCaste = $('#caste').val();
            const casteDocUploaded = Boolean(@json(isset($data['applicant_details']->Document_Caste) ? $data['applicant_details']->Document_Caste : null));

            if (existingCaste) {
                handleCasteChange(existingCaste, casteDocUploaded);
            }

            $('#caste').on('change', function() {
                const selectedCaste = $(this).val();
                sessionStorage.removeItem('selectedCaste');
                const casteDocUploaded = Boolean(@json(isset($data['applicant_details']->Document_Caste) ? $data['applicant_details']->Document_Caste : null));
                handleCasteChange(selectedCaste, casteDocUploaded);
            });


            // ============================
            // ## Load Area Data (Blocks + Nagars)
            // ============================
            function loadAreaData(districtCode) {
                if (!districtCode || districtCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-area-data/' + districtCode,
                    type: 'GET',
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        let optBlock =
                            '<option selected disabled value="undefined">-- चयन करें --</option>';
                        let optNagar =
                            '<option selected disabled value="undefined">-- चयन करें --</option>';

                        if (response.blocks?.length) {
                            response.blocks.forEach(blk => {
                                optBlock +=
                                    `<option value="${blk.block_lgd_code}">${blk.block_name_hin}</option>`;
                            });
                        } else {
                            optBlock += '<option value="">कोई विकासखंड नहीं मिली</option>';
                        }

                        if (response.nagars?.length) {
                            response.nagars.forEach(nag => {
                                optNagar +=
                                    `<option value="${nag.std_nnn_code}">${nag.nnn_name}</option>`;
                            });
                        } else {
                            optNagar += '<option value="">कोई नगर निकाय नहीं मिला</option>';
                        }

                        $('#block').html(optBlock);
                        $('#nagar').html(optNagar);

                        const selectedBlock = $('#block').data('selected');
                        const selectedNagar = $('#nagar').data('selected');
                        if (selectedBlock) $('#block').val(selectedBlock).trigger('change');
                        if (selectedNagar) $('#nagar').val(selectedNagar).trigger('change');
                    },
                    error: function() {
                        $('#block, #nagar').html(
                            '<option selected disabled value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }

            // Initial Load
            // const initialDistrict = $('#selected_district').val();
            // if (initialDistrict) loadAreaData(initialDistrict);

            $('#selected_district').on('change', function() {
                loadAreaData($(this).val());
            });

            // ============================
            // ## Load Gram Panchayat
            // ============================
            function loadGramPanchayat(blockCode) {
                // $('#gp').html('<option selected disabled value="undefined">-- चयन करें --</option>');
                if (!blockCode || blockCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-gp/' + blockCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html =
                            '<option selected disabled value="undefined">-- ग्राम पंचायत चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.panchayat_lgd_code}">${item.panchayat_name_hin}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई पंचायत नहीं मिली</option>';
                        }
                        $('#gp').html(html);

                        const selectedGp = $('#gp').data('selected');
                        if (selectedGp) $('#gp').val(selectedGp);
                    },
                    error: function() {
                        $('#gp').html(
                            '<option selected hidden value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }

            $('#block').on('change', function() {
                loadGramPanchayat($(this).val());
            });


            function loadVillage(GpCode) {
                // $('#gp').html('<option selected disabled value="undefined">-- चयन करें --</option>');
                if (!GpCode || GpCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-village/' + GpCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html =
                            '<option selected disabled value="undefined">-- ग्राम चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.village_code}">${item.village_name_hin}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई ग्राम नहीं मिली</option>';
                        }
                        $('#village_id').html(html);

                        const selectedVillage = $('#village_id').data('selected');
                        if (selectedVillage) $('#village_id').val(selectedVillage);
                    },
                    error: function() {
                        $('#village_id').html(
                            '<option selected hidden value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }

            $('#gp').on('change', function() {
                loadVillage($(this).val());
            });

            // const selectedBlockCode = $('#block').val();
            // if (selectedBlockCode) loadGramPanchayat(selectedBlockCode);



            // ============================
            // ## Load Ward Data
            // ============================
            function loadWardData() {
                const nagarCode = $('#nagar').val(); // parent select ka value
                if (!nagarCode) return;

                $.ajax({
                    url: '/candidate/get-ward/' + nagarCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html = '<option disabled value="">-- वार्ड चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.ID}">${item.ward_name} क्रमांक ${item.ward_no}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई वार्ड नहीं मिला</option>';
                        }

                        $('#ward').html(html);

                        // update case: agar ward pre-selected hai to set karo
                        const selectedWard = $('#ward').data('selected');
                        if (selectedWard) {
                            $('#ward').val(selectedWard);
                        }
                    },
                    error: function() {
                        $('#ward').html(
                            '<option disabled value="">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }

            // nagar change hone par wards load
            $('#nagar').on('change', loadWardData);

            // page load par bhi execute
            // $(document).ready(loadWardData);



            // ============================
            // ## Load Posts Based on Ward/GP
            // ============================
            $('#ward, #village_id').change(function() {
                let ward_village_code = '';

                if ($('#Rular-select').is(':visible')) {
                    ward_village_code = $('#village_id').val();
                } else if ($('#Urban-select').is(':visible')) {
                    ward_village_code = $('#ward').val();
                }

                $('#master_post').html(
                    '<option selected disabled value="undefined">-- चयन करें --</option>');

                if (!ward_village_code || ward_village_code === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-post/' + ward_village_code,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html =
                            '<option selected disabled value="undefined">-- पद चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.post_id}">${item.title}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई पोस्ट नहीं मिला</option>';
                        }
                        $('#master_post').html(html);
                    },
                    error: function() {
                        $('#master_post').html(
                            '<option selected hidden value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            });




            function loadAreaDataP(districtCode) {
                // $('#block1, #nagar1, #gp1, #ward1').html(
                //     '<option selected disabled value="undefined">-- चयन करें --</option>');
                if (!districtCode || districtCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-area-data/' + districtCode,
                    type: 'GET',
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        let optBlock =
                            '<option selected disabled value="undefined">-- विकासखंड चुनें --</option>';
                        let optNagar =
                            '<option selected disabled value="undefined">-- नगर निकाय चुनें --</option>';

                        if (response.blocks?.length) {
                            response.blocks.forEach(blk => {
                                optBlock +=
                                    `<option value="${blk.block_lgd_code}">${blk.block_name_hin}</option>`;
                            });
                        } else {
                            optBlock += '<option value="">कोई विकासखंड नहीं मिली</option>';
                        }

                        if (response.nagars?.length) {
                            response.nagars.forEach(nag => {
                                optNagar +=
                                    `<option value="${nag.std_nnn_code}">${nag.nnn_name}</option>`;
                            });
                        } else {
                            optNagar += '<option value="">कोई नगर निकाय नहीं मिला</option>';
                        }

                        $('#block1').html(optBlock);
                        $('#nagar1').html(optNagar);
                    },
                    error: function() {
                        $('#block1, #nagar1').html(
                            '<option selected disabled value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }


            function loadGramPanchayatP(blockCode) {
                // $('#gp1').html('<option selected disabled value="undefined">-- चयन करें --</option>');
                if (!blockCode || blockCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-gp/' + blockCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html =
                            '<option selected disabled value="undefined">-- ग्राम पंचायत चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.panchayat_lgd_code}">${item.panchayat_name_hin}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई पंचायत नहीं मिली</option>';
                        }
                        $('#gp1').html(html);
                    },
                    error: function() {
                        $('#gp1').html(
                            '<option selected hidden value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }

            function loadVillageP(GpCode) {
                // $('#gp').html('<option selected disabled value="undefined">-- चयन करें --</option>');
                if (!GpCode || GpCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-village/' + GpCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html =
                            '<option selected disabled value="undefined">-- ग्राम चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.village_code}">${item.village_name_hin}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई ग्राम नहीं मिली</option>';
                        }
                        $('#village_id1').html(html);

                        const selectedVillage = $('#village_id1').data('selected');
                        if (selectedVillage) $('#village_id1').val(selectedVillage);
                    },
                    error: function() {
                        $('#village_id1').html(
                            '<option selected hidden value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }

            $('#gp1').on('change', function() {
                loadVillageP($(this).val());
            });

            function loadWardP(nagarCode) {
                // $('#ward1').html('<option selected disabled value="undefined">-- चयन करें --</option>');
                if (!nagarCode || nagarCode === 'undefined') return;

                $.ajax({
                    url: '/candidate/get-ward/' + nagarCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html =
                            '<option selected disabled value="undefined">-- वार्ड चुनें --</option>';
                        if (data.length) {
                            data.forEach(item => {
                                html +=
                                    `<option value="${item.ID}">${item.ward_name} क्रमांक ${item.ward_no}</option>`;
                            });
                        } else {
                            html += '<option value="">कोई वार्ड नहीं मिला</option>';
                        }
                        $('#ward1').html(html);
                    },
                    error: function() {
                        $('#ward1').html(
                            '<option selected hidden value="undefined">-- चयन करें --</option><option value="">लोड नहीं हुआ</option>'
                        );
                    }
                });
            }


            $('#cur_district').on('change', function() {
                loadAreaDataP($(this).val());
            });

            $('#block1').on('change', function() {
                loadGramPanchayatP($(this).val());
            });

            $('#nagar1').on('change', function() {
                loadWardP($(this).val());
            });

            // Document load function call
            // loadAreaDataP($('#cur_district').val());
            // loadGramPanchayatP($('#block1').val());
            // loadWardP($('#nagar1').val());


            // ##===========  // Get Min Quali_ID Select Post ========================##
            $('select[name=master_posts]').change(function() {
                let post_id = $("#master_post").val();
                let applicant_id = $("#applicant_id")
                    .val(); // Add this hidden input in form with applicant ID

                $.ajax({
                    url: '/candidate/get-post-qualification',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        applicant_id: applicant_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'not_matched') {
                            $('#QualificationMessage').html(
                                `<div class="alert alert-warning" role="alert">
                                     इस पोस्ट के लिए न्यूनतम शैक्षणिक योग्यता <strong>"${response.qualification_name}"</strong> आवश्यक है। आपकी योग्यता इस पद के अनुसार मेल नहीं खाती।
                                          </div>`
                            );
                            // $('#user-edu-btn').prop('disabled', true);

                        } else {
                            $('#QualificationMessage').html(''); // Clear previous warning
                            $('#user-edu-btn').prop('disabled', false);

                        }
                    },
                    error: function(xhr) {
                        console.log("Error:", xhr);
                    }
                });
            });



            // ##===========  // All Questions Load of Select Post ========================##
            function loadPostQuestions() {
                let post_id = $("#master_post").val();
                $.ajax({
                    url: '/candidate/get-post-questions-with-answer',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    cache: false,
                    success: function(response) {
                        let html = '';
                        html += `<h5>चयनित पोस्ट से संबंधित प्रश्नों का उत्तर दें :</h5>`;
                        const parentChildMap = {};
                        const today = new Date().toISOString().split('T')[0];

                        if (response.questions && response.questions.length > 0) {
                            $.each(response.questions, function(index, value) {
                                const isChild = value.parent_id !== null && value.parent_ans;
                                const parentKey = `${value.parent_id}_${value.fk_post_id}`;
                                const blockId =
                                    `question_block_${value.post_map_id}_${parentKey}`;
                                const questionId = value.post_map_id;

                                if (isChild) {
                                    if (!parentChildMap[parentKey]) {
                                        parentChildMap[parentKey] = [];
                                    }
                                    parentChildMap[parentKey].push(value.post_map_id);
                                }

                                html +=
                                    `<div class="row ${isChild ? 'parent-dependent-question' : ''}" id="${blockId}" ${isChild ? 'style="display: none;"' : ''}>
                                                <div class="col-md-12 col-xs-12 text-left">
                                                    <label>${value.ques_name}</label><label style="color:red">*</label>
                                                    <div class="question-group mt-3" data-question-id="${value.parent_id ?? value.fk_ques_id}" data-post-id="${value.fk_post_id}" ${isChild ? `data-parent-id="${value.parent_id}" data-parent-ans="${value.parent_ans}"` : ''}>`;

                                if (value.ans_type === 'O' || value.ans_type === 'F' || value
                                    .ans_type === 'OFD') {
                                    const options = JSON.parse(value.answer_options);
                                    $.each(options, function(optIndex, optValue) {
                                        html += `<label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                                        <input type="radio" name="question_${questionId}" value="${optValue}" required> ${optValue}
                                                    </label>`;
                                    });

                                    if (value.ans_type === 'F' || value.ans_type === 'OFD' ||
                                        value.ans_type === 'FD') {
                                        html +=
                                            `<input type="file" name="question_${questionId}_file" class="form-control mt-2 QFileInput" accept=".pdf" style="display: none;" data-key="प्रश्न-फ़ाइल">
                                                    <span id="fileshow_${questionId}" style="display:none;"></span> <br>`;
                                    }

                                    if (value.ans_type === 'OFD') {
                                        html +=
                                            `<div class="ofd-wrapper mt-1" id="ofdWrapper_${questionId}" style="display:none;">`;
                                        html +=
                                            '<div class="row col-md-12"> <div class="col-md-4"> ';
                                        html += '<label>कब से </label>';
                                        html +=
                                            `<input type="date" id="dateFrom_${questionId}" name="dateFrom_question_${questionId}" class="form-control"><br>`;
                                        html += '</div><div class="col-md-4">';
                                        html += '<label>कब तक </label>';
                                        html +=
                                            `<input type="date" id="dateTo_${questionId}" name="dateTo_question_${questionId}" max="${today}" class="form-control"><br>`;
                                        html += '</div><div class="col-md-4">';
                                        html += '<label>कुल अनुभव (दिनों में) </label>';
                                        html +=
                                            `<input type="text" id="totalExpDays_${questionId}" name="totalExpDays_question_${questionId}" class="form-control" readonly>`;
                                        html += '</div></div></div>';

                                        setTimeout(function() {
                                            // Radio change event
                                            $(`input[type="radio"][name="question_${questionId}"]`)
                                                .on('change', function() {
                                                    const firstOptionVal = $(
                                                        `input[type="radio"][name="question_${questionId}"]`
                                                    ).first().val();
                                                    const wrapper = $(
                                                        `#ofdWrapper_${questionId}`
                                                    );
                                                    const inputs = wrapper.find(
                                                        'input[type="date"], input[type="text"]'
                                                    );

                                                    if ($(this).val() ===
                                                        firstOptionVal) {
                                                        wrapper.show();
                                                        inputs.prop('required',
                                                            true);
                                                    } else {
                                                        wrapper.hide();
                                                        inputs.prop('required',
                                                            false).val('');
                                                        $(`#totalExpDays_${questionId}`)
                                                            .val('');
                                                        $(`#dateFrom_${questionId}`)
                                                            .val('');
                                                        $(`#dateTo_${questionId}`)
                                                            .val('');
                                                    }
                                                });

                                            // Date calculation
                                            $(`#dateFrom_${questionId}, #dateTo_${questionId}`)
                                                .on('change', function() {
                                                    let from = $(
                                                        `#dateFrom_${questionId}`
                                                    ).val();
                                                    let to = $(
                                                            `#dateTo_${questionId}`)
                                                        .val();
                                                    if (from && to) {
                                                        let dateFromObj = new Date(
                                                            from);
                                                        let dateToObj = new Date(
                                                            to);
                                                        if (dateToObj >=
                                                            dateFromObj) {
                                                            let diffTime = Math.abs(
                                                                dateToObj -
                                                                dateFromObj);
                                                            let diffDays = Math
                                                                .ceil(diffTime / (
                                                                    1000 * 60 *
                                                                    60 * 24)) + 1;
                                                            $(`#totalExpDays_${questionId}`)
                                                                .val(diffDays);
                                                        } else {
                                                            $(`#totalExpDays_${questionId}`)
                                                                .val('');
                                                        }
                                                    }
                                                });
                                        }, 0);
                                    }

                                } else if (value.ans_type === 'D' || value.ans_type === 'FD') {
                                    const extraId = (value.fk_ques_id == 7) ?
                                        ' id="marry_date"' : '';
                                    const isFD = value.ans_type === 'FD';

                                    if (isFD) {
                                        // For FD type: Show BOTH date input AND file upload
                                        html += `<div class="fd-wrapper">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="date" name="question_${questionId}_date" 
                                                                    class="form-control fd-date" max="${today}" required ${extraId} data-key="प्रश्न">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="file" name="question_${questionId}_file" 
                                                                    class="form-control mt-0 QFileInput fd-file" accept=".pdf" data-key="प्रश्न-फ़ाइल">
                                                            </div>
                                                        </div>
                                                        <span id="fileshow_${questionId}" style="display:none;"></span>
                                                    </div>`;
                                    } else {
                                        // For D type: Show only date input
                                        html +=
                                            `<input type="date" name="question_${questionId}" class="form-control" max="${today}" required ${extraId} data-key="प्रश्न">`;
                                    }

                                } else if (value.ans_type === 'N') {
                                    const extraId = (value.fk_ques_id == 8) ?
                                        ' id="child_count"' : '';
                                    html +=
                                        `<input type="number" name="question_${questionId}" class="form-control" value="0" required ${extraId} data-key="प्रश्न">`;
                                } else if (value.ans_type === 'M') {
                                    const options = JSON.parse(value.answer_options);
                                    $.each(options, function(optIndex, optValue) {
                                        const checkboxId =
                                            `question_${questionId}_opt_${optIndex}`;
                                        html += `<div class="checkbox-wrapper" style="margin-left: 30px; margin-top: 10px;">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="question_${questionId}[]" value="${optValue}" id="${checkboxId}" class="multi-check"> ${optValue}
                                                        </label>
                                                        <input type="file" name="question_${questionId}_file_${optIndex}" class="form-control mt-2 documentInput file-input" accept=".pdf" style="display: none; margin-left: 20px;">
                                                        <div id="fileshow_${checkboxId}" style="display:none;"></div>
                                                    </div>`;
                                    });
                                }

                                html +=
                                    `<span class="text-danger error-msg" id="error-प्रश्न"></span> <br> <span class="text-danger error-msg" id="error-प्रश्न-फ़ाइल"></span>`;
                                html +=
                                    `</div></div><div class='row'><div class='col-md-12 col-xw-12'><hr></div></div></div>`;
                            });

                            $('#questionsContainer').html(html);
                            childAnswerData = [];

                            if (response.answers && response.answers.length > 0) {
                                response.answers.forEach(ans => {
                                    childAnswerData[ans.fk_ques_id] = ans.answer;
                                });

                                response.answers.forEach(ans => {
                                    const qid = ans.fk_ques_id;
                                    const post_map_id = ans.post_map_id;
                                    const answer = ans.answer;
                                    const ans_type = ans.ans_type;
                                    const answer_file_upload = ans.answer_file_upload || null;
                                    const date_From = ans.date_From || null;
                                    const date_To = ans.date_To || null;
                                    const total_experience_days = ans.total_experience_days ||
                                        null;
                                    const APP_ENV = "{{ config('app.env') }}";
                                    const FILE_POINT = "{{ config('custom.file_point') }}";
                                    const assetPath = path => "{{ asset('') }}" + path;

                                    const showFile = (targetId, fileName, relatedInput) => {
                                        if (!fileName) return;
                                        let file_path = assetPath('uploads/' + fileName);
                                        if (APP_ENV === 'production') file_path =
                                            FILE_POINT + fileName;
                                        const $target = $(`#${targetId}`);
                                        $target.html(`
                                            <a href="${file_path}" class="myImg" style="text-decoration: none; cursor: pointer;" target="_blank">
                                                <span class="btn btn-sm text-info">View Document <i class="bi bi-eye"></i></span>
                                            </a>
                                        `);

                                        // Always show the preview if we have a file
                                        $target.show();
                                    };

                                    if (qid == 1) {
                                        $(`.question-group[data-question-id="${qid}"] input[type=radio]`)
                                            .each(function() {
                                                const val = $(this).val();
                                                if (val === answer) {
                                                    $(this).prop('checked', true);
                                                    setTimeout(() => $(this).trigger(
                                                        'change'), 500);
                                                }
                                                if (["विवाहित", "परित्यक्ता", "तलाकशुदा",
                                                        "विधवा"
                                                    ].includes(answer) && val ===
                                                    "अविवाहित") {
                                                    $(this).closest('label').hide();
                                                }
                                            });
                                    }

                                    if ((ans_type === 'O' || ans_type === 'F' || ans_type ===
                                            'OFD') && answer) {
                                        $(`input[type=radio][name="question_${post_map_id}"]`)
                                            .each(function() {
                                                if ($(this).val() === answer.trim()) {
                                                    $(this).prop('checked', true);
                                                    setTimeout(() => $(this).trigger(
                                                        'change'), 500);
                                                }
                                            });
                                    }

                                    if ((ans_type === 'F' || ans_type === 'OFD' || ans_type ===
                                            'FD') && answer_file_upload) {
                                        showFile(`fileshow_${post_map_id}`, answer_file_upload,
                                            `input[name="question_${post_map_id}_file"]`);
                                    }

                                    if (ans_type === 'FD' && answer) {
                                        // Set date value for FD type
                                        $(`input[name="question_${post_map_id}_date"]`).val(
                                            answer);
                                    }

                                    if (ans_type === 'OFD' && answer) {
                                        $(`#dateFrom_${post_map_id}`).val(date_From);
                                        $(`#dateTo_${post_map_id}`).val(date_To);
                                        $(`#totalExpDays_${post_map_id}`).val(
                                            total_experience_days);
                                    }

                                    if (ans_type === 'M' && answer) {
                                        let selectedOptions = [];
                                        try {
                                            selectedOptions = answer.trim().startsWith('[') ?
                                                JSON.parse(answer) : [answer.trim()];
                                        } catch {
                                            selectedOptions = answer.split(',').map(a => a
                                                .trim());
                                        }
                                        let optionsArray = [];
                                        try {
                                            if (ans.answer_options) optionsArray = JSON.parse(
                                                ans.answer_options);
                                        } catch {
                                            optionsArray = [];
                                        }
                                        selectedOptions.forEach(selOpt => {
                                            const optIndex = optionsArray.indexOf(
                                                selOpt);
                                            if (optIndex !== -1) {
                                                const checkboxId =
                                                    `question_${post_map_id}_opt_${optIndex}`;
                                                $(`#${checkboxId}`).prop('checked',
                                                    true);
                                                const fileInput = $(`#${checkboxId}`)
                                                    .closest('.checkbox-wrapper').find(
                                                        '.file-input');
                                                fileInput.show().attr('name',
                                                    `question_${post_map_id}_file_${optIndex}`
                                                );
                                                if (answer_file_upload) {
                                                    showFile(`fileshow_${checkboxId}`,
                                                        answer_file_upload,
                                                        fileInput);
                                                }
                                            }
                                        });
                                    }

                                    if ((ans_type === 'D' || ans_type === 'FD') && answer &&
                                        qid !== 7) {
                                        // For FD type, we only set the date input (not the file input)
                                        if (ans_type === 'FD') {
                                            $(`input[name="question_${post_map_id}_date"]`).val(
                                                answer);
                                        } else {
                                            $(`input[type=date][name="question_${post_map_id}"]`)
                                                .val(answer);
                                        }
                                    }

                                    if (ans_type === 'N' && answer && qid !== 8) {
                                        $(`input[type=number][name="question_${post_map_id}"]`)
                                            .val(answer);
                                    }

                                    if (qid == 7 && answer) {
                                        setTimeout(() => $('#marry_date').val(answer).prop(
                                            'readonly', true), 500);
                                    }

                                    if (qid == 8 && ans_type === 'N' && answer) {
                                        setTimeout(() => $('#child_count').val(answer), 150);
                                    }
                                });
                            }

                            $(document).on('change', '.multi-check', function() {
                                const fileInput = $(this).closest('.checkbox-wrapper').find(
                                    '.file-input');
                                const fileShow = $(this).closest('.checkbox-wrapper').find(
                                    '[id^="fileshow_"]');
                                if ($(this).is(':checked')) {
                                    fileInput.show();
                                    if (fileShow.children().length > 0) fileShow.show();
                                } else {
                                    fileInput.hide().val('');
                                    fileShow.hide();
                                }
                            });

                            $('input[type="radio"]').on('change', function() {
                                const group = $(this).closest('.question-group');
                                const questionId = group.data('question-id');
                                const postId = group.data('post-id');
                                const selectedValue = $(this).val();

                                const fileInput = group.find('input[type="file"]');
                                const fileShow = group.find('[id^="fileshow_"]');
                                if (fileInput.length > 0) {
                                    const firstOption = group.find('input[type="radio"]')
                                        .first().val();
                                    if (selectedValue === firstOption) {
                                        fileInput.show();
                                        if (fileShow.children().length > 0) fileShow.show();
                                    } else {
                                        fileInput.hide().val('');
                                        fileShow.hide();
                                    }
                                }

                                if (group.data('parent-id')) {
                                    return;
                                }

                                const parentKey = `${questionId}_${postId}`;
                                if (parentChildMap[parentKey]) {
                                    parentChildMap[parentKey].forEach(function(childId) {
                                        const childBlock = $(
                                            `#question_block_${childId}_${parentKey}`
                                        );
                                        const childGroup = childBlock.find(
                                            '.question-group');
                                        const expectedAns = childGroup.data(
                                            'parent-ans');

                                        if (selectedValue === expectedAns) {
                                            childBlock.show();
                                            childGroup.find('input, select, textarea')
                                                .each(function() {
                                                    $(this).prop('required', true);
                                                    const inputName = $(this).attr(
                                                        'name');
                                                    const match = inputName &&
                                                        inputName.match(
                                                            /^question_(\d+)/);
                                                    if (match) {
                                                        const childQID = parseInt(
                                                            match[1]);
                                                        if (childAnswerData[
                                                                childQID]) {
                                                            // For FD type child questions
                                                            if ($(this).hasClass(
                                                                    'fd-date') &&
                                                                childAnswerData[
                                                                    childQID]) {
                                                                $(this).val(
                                                                    childAnswerData[
                                                                        childQID
                                                                    ]);
                                                            } else if (!$(this)
                                                                .hasClass('fd-file')
                                                            ) {
                                                                $(this).val(
                                                                    childAnswerData[
                                                                        childQID
                                                                    ]);
                                                            }
                                                        }
                                                    }
                                                });
                                        } else {
                                            childGroup.find('input, select, textarea')
                                                .each(function() {
                                                    $(this).prop('required', false);
                                                    const inputName = $(this).attr(
                                                        'name');
                                                    const match = inputName &&
                                                        inputName.match(
                                                            /^question_(\d+)/);
                                                    if (match) {
                                                        const childQID = parseInt(
                                                            match[1]);
                                                        if (!(childQID in
                                                                childAnswerData)) {
                                                            if ($(this).is(
                                                                    ':radio') || $(
                                                                    this)
                                                                .is(':checkbox')) {
                                                                if ($(this).prop(
                                                                        'checked'
                                                                    )) {
                                                                    childAnswerData[
                                                                            childQID
                                                                        ] = $(
                                                                            this)
                                                                        .val();
                                                                }
                                                            } else {
                                                                childAnswerData[
                                                                        childQID] =
                                                                    $(this).val();
                                                            }
                                                        }
                                                    }
                                                    if ($(this).is(':radio') || $(
                                                            this).is(':checkbox')) {
                                                        $(this).prop('checked',
                                                            false);
                                                    } else if ($(this).is(
                                                            ':file')) {
                                                        $(this).hide().val('');
                                                        $(this).closest(
                                                                '.checkbox-wrapper')
                                                            .find(
                                                                '[id^="fileshow_"]')
                                                            .hide();
                                                    } else {
                                                        $(this).val('');
                                                    }
                                                });
                                            childBlock.hide();
                                        }
                                    });
                                }
                            });
                        } else {
                            html = "<p> कोई प्रश्न नहीं मिला </p>";
                            $('#questionsContainer').html(html);
                        }
                    },
                    error: function(xhr) {
                        $('#questionsContainer').html('<p>Error loading questions.</p>');
                    }
                });
            }


            if ($('#master_post').val()) {
                loadPostQuestions();
                // loadPostSkills();
            }

            // Bind to select change event
            $('select[name=master_post]').change(function() {
                loadPostQuestions();
                // loadPostSkills();
            });


            // ##===========  // All Skills Load of Select Post ========================##
            function loadPostSkills() {
                let post_id = $("#master_post").val();
                $.ajax({
                    url: '/candidate/get-post-skills',
                    type: "POST",
                    data: {
                        post_id: post_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    cache: false,


                    success: function(response) {
                        const skills = response[0];
                        const skillsAnswer = response[1];

                        let html = '';
                        if (skills && skills.length > 0) {
                            html += '<h5>कौशल चुनें :</h5><div class="form-group"><div class="row">';

                            $.each(skills, function(index, skill) {
                                html +=
                                    `<div class="col-md-4 mb-3"><strong>${index + 1}. ${skill.SkillName}</strong> `; //<font color="red">*</font>

                                // Parse skill options
                                let options = [];
                                try {
                                    options = JSON.parse(skill.skill_options);
                                } catch (e) {
                                    options = skill.skill_options?.split(',') || [];
                                }

                                // Get selected answers for this skill
                                let selectedOptions = [];
                                if (skillsAnswer && skillsAnswer.length > 0) {
                                    skillsAnswer.forEach(ans => {
                                        if (ans.fk_skill_id === skill.fk_skill_id) {
                                            try {
                                                selectedOptions = JSON.parse(ans
                                                    .skill_answers);
                                            } catch (e) {
                                                selectedOptions = ans.skill_answers
                                                    ?.split(',') || [];
                                            }
                                        }
                                    });
                                }

                                // Render checkboxes with pre-selected answers
                                $.each(options, function(optIndex, option) {
                                    const checkboxId =
                                        `skill_${skill.fk_skill_id}_${optIndex}`;
                                    const checked = selectedOptions.includes(option
                                        .trim()) ? 'checked' : '';

                                    // console.log("Option:", option, "Checked:", checked);
                                    html += `
                                            <div class="form-check ms-3">
                                                <input type="checkbox" name="skill_options[${skill.fk_skill_id}][]" 
                                                    value="${option.trim()}" class="form-check-input" 
                                                    id="${checkboxId}" data-key="${skill.SkillName}" ${checked}>
                                                <label class="form-check-label" for="${checkboxId}">${option.trim()}</label>
                                            </div>
                                        `;
                                });

                                html += `</div>`;
                            });

                            html += '</div></div>'; // Close row and form-group
                        }

                        $('#skillsContainer').html(html);
                    },
                    error: function(xhr) {
                        $('#skillsContainer').html('<p>कौशल लोड करने में त्रुटि.</p>');
                    }
                });
            };





            //============ ## All Funtions of Personal Detials Form ##===========


            // ## For Pincode Dynamic and for both permanent and current
            pincode($('#cur_district').val(), "cpincode", {{ @$data['applicant_details']->Corr_pincode }});
            // pincode($('#per_district').val(), "ppincode", {{ @$data['applicant_details']->Perm_pincode }});
            // pincode for Current District
            $('#cur_district').on('change', function() {
                let districtCode = $(this).val();
                pincode(districtCode, "cpincode");
            });

            // pincode for  Permanent District
            $('#per_district').on('change', function() {
                let districtCode = $(this).val();
                pincode(districtCode, "ppincode");
            });

            function pincode(districtCode = 0, selectId = "", pin = 0) {
                // console.log('District Code:', districtCode, 'Select ID:', selectId, 'Pin:', pin);
                if (districtCode) {
                    $.ajax({
                        url: '/candidate/get-pincodes-by-district/' + districtCode,
                        type: 'GET',
                        success: function(data) {
                            let options = '<option value="">-- चयन करें --</option>';
                            data.forEach(function(pincode) {
                                let selected = "";
                                if (pin == pincode) {
                                    selected = 'selected';

                                }
                                options += '<option value="' + pincode + '" ' + selected +
                                    ' >' +
                                    pincode + '</option>';
                            });
                            $('#' + selectId).html(options);
                        },
                        error: function() {
                            alert('पिन कोड लोड करने में त्रुटि हुई।');
                        }
                    });
                } else {
                    $('#' + selectId).html('<option value="">-- चयन करें --</option>');
                }
            }


            // ### adhaar confirmation Checkbox 
            // Function to handle readonly state based on checkbox
            function toggleAadhaarReadonly() {
                if ($('#confirmationCheckbox').is(':checked')) {
                    $('#adhaar').prop('readonly', false);
                } else {
                    $('#adhaar').prop('readonly', true);
                }
            }
            $('#confirmationCheckbox').on('change', toggleAadhaarReadonly);



            // ## Populate Year for EducationYear Qualification ##===========
            const minYear = parseInt($('#minyear').val()) || 1990;
            const maxYear = parseInt($('#maxyear').val()) || new Date().getFullYear();

            // Qualification-wise year gaps
            const yearGap = {
                1: 0, // 5th
                2: 3, // 8th
                3: 2, // 10th
                4: 2, // 12th
                5: 3, // UG
                6: 2, // PG
                7: 0 // Other 
            };

            // map qualification IDs to for row ids
            const qualiRowIds = {
                1: 'class_5th',
                2: 'class_8th',
                3: 'ssc',
                4: 'inter',
                5: 'ug',
                6: 'pg',
                7: 'other'
            };

            // Prepare year select elements mapping
            const yearSelects = {};
            for (const [qualiId, rowId] of Object.entries(qualiRowIds)) {
                const $select = $(`#${rowId} select[name='year_passing[]']`);
                yearSelects[qualiId] = $select;
            }

            // Populate year options between given range
            function populateYearOptions($select, fromYear, selected = null) {
                $select.empty().append('<option selected disabled value="">-- चयन करें --</option>');
                for (let y = fromYear; y <= maxYear; y++) {
                    const isSelected = selected == y ? 'selected' : '';
                    $select.append(`<option value="${y}" ${isSelected}>${y}</option>`);
                }
            }

            // Handle year change logic: update all next qualifications
            for (const [qualiId, $select] of Object.entries(yearSelects)) {
                $select.on('change', function() {
                    let currentYear = parseInt($(this).val());
                    let currentQualiId = parseInt(qualiId);
                    let nextQualiId = currentQualiId + 1;

                    // Chain forward until 'other' or end
                    while (qualiRowIds[nextQualiId]) {
                        if (parseInt(nextQualiId) === 7) break;
                        const gap = yearGap[nextQualiId] || 0;
                        const nextMinYear = currentYear + gap;

                        const $nextSelect = yearSelects[nextQualiId];
                        const existingValue = $nextSelect.val();
                        populateYearOptions($nextSelect, nextMinYear, existingValue);

                        currentYear = parseInt(existingValue) || nextMinYear;
                        nextQualiId++;
                    }
                });
            }

            // On page load, check selected years values
            for (const [qualiId, $select] of Object.entries(yearSelects)) {
                const selectedVal = parseInt($select.val());
                if (selectedVal) {
                    $select.trigger('change');
                } else if (parseInt(qualiId) === 7) {
                    // For other qualification, show full year list
                    populateYearOptions($select, minYear);
                }
            }


            //## Edit Files Clicked Here editFileLink ##===========
            $('#editPhotoLink').on('click', function(e) {
                e.preventDefault();
                $('#photo').trigger('click');
            });

            $('#editSignLink').on('click', function(e) {
                e.preventDefault();
                $('#document_sign').trigger('click');
            });

            $('#editAadharLink').on('click', function(e) {
                e.preventDefault();
                $('#document_adhaar').trigger('click');
            });

            $('#editCasteLink').on('click', function(e) {
                e.preventDefault();
                $('#caste_certificate').trigger('click');
            });

            $('#editDomicialLink').on('click', function(e) {
                e.preventDefault();
                $('#document_domicile').trigger('click');
            });

            $('#editFifthLink').on('click', function(e) {
                e.preventDefault();
                $('#5th_marksheet').trigger('click');
            });

            $('#editEightLink').on('click', function(e) {
                e.preventDefault();
                $('#8th_marksheet').trigger('click');
            });

            $('#editTenthLink').on('click', function(e) {
                e.preventDefault();
                $('#ssc_marksheet').trigger('click');
            });

            $('#editTwellthLink').on('click', function(e) {
                e.preventDefault();
                $('#inter_marksheet').trigger('click');
            });

            $('#editUGLink').on('click', function(e) {
                e.preventDefault();
                $('#ug_marksheet').trigger('click');
            });

            $('#editPGLink').on('click', function(e) {
                e.preventDefault();
                $('#pg_marksheet').trigger('click');
            });
            $('#editEPICLink').on('click', function(e) {
                e.preventDefault();
                $('#Epic_document').trigger('click');
            });

            $(document).on('click', '.editAllExperienceLink', function(e) {
                e.preventDefault();

                // Find the corresponding hidden file input in the same experience block
                var container = $(this).closest('.experience-row');
                container.find('.docExperience').trigger('click');
            });

            $('#editOtherLink').on('click', function(e) {
                e.preventDefault();
                $('#other_marksheet').trigger('click');
            });

            $('#editBPLLink').on('click', function(e) {
                e.preventDefault();
                $('#BPL_document').trigger('click');
            });

            $('#editWidowLink').on('click', function(e) {
                e.preventDefault();
                $('#widow_divorce_document').trigger('click');
            });


            //============ ## Save Local Storage of myForms Data ##===========

            $('#myForm1').submit(function(e) {
                e.preventDefault();
                // $('#user-details-btn').prop('disabled', true);
                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');


                // Submit the form using AJAX
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    success: function(data) {

                        if (data.status == 'success') {

                            hideAllDivs();

                            $("#tab2").css("display", "block");
                            $("#personal-details-btn").focus();

                            document.getElementById("post-details-btn").removeAttribute(
                                "disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("personal-details-btn").style
                                .backgroundColor =
                                "#21bf06";

                            // console.log('apply data :' + data.apply_data);
                            // Clear all error messages
                            $('#cities-error, #projects-error, #area-error, #block-error, #gp-error, #nagar-error, #ward-error, #master_post-error')
                                .html('');


                        } else if (data.status == 'error') {
                            if (data.errors) {
                                if (data.errors) {
                                    $('#cities-error').html(data.errors.selected_district ? data
                                        .errors.selected_district[0] : '');
                                    $('#projects-error').html(data.errors.projects ? data.errors
                                        .projects[0] : '');
                                    $('#area-error').html(data.errors.area ? data.errors.area[
                                        0] : '');
                                    $('#block-error').html(data.errors.block ? data.errors
                                        .block[0] : '');
                                    $('#gp-error').html(data.errors.gp ? data.errors.gp[0] :
                                        '');
                                    $('#nagar-error').html(data.errors.nagar ? data.errors
                                        .nagar[0] : '');
                                    $('#ward-error').html(data.errors.ward ? data.errors.ward[
                                        0] : '');
                                    $('#master_post-error').html(data.errors.master_post ? data
                                        .errors.master_post[0] : '');
                                }
                            } else {
                                // Clear all error messages
                                $('#cities-error, #projects-error, #area-error, #block-error, #gp-error, #nagar-error, #ward-error, #master_post-error')
                                    .html('');
                            }
                            Swal.fire({
                                icon: 'warning',
                                text: data['message'],
                                // text: data.errors,
                                allowOutsideClick: false
                            })

                        } else if (data.status == 'warning') {
                            Swal.fire({
                                icon: 'warning',
                                text: data['message'],
                                // text: data.errors,
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#post-details-btn').prop('disabled', false);

                        console.log('ajax error:', xhr.responseText);

                        let response;

                        // Safe JSON parsing
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = {
                                message: 'कृपया सभी आवश्यक फ़ील्ड भरें!',
                                errors: {}
                            };
                        }

                        // 🆕 If server sent an alert_message (e.g. rate limit), show it directly and stop
                        if (response && response.alert_message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'ध्यान दें',
                                html: response.alert_message,
                                allowOutsideClick: false
                            });
                            return; // do not run the normal validation-message flow
                        }

                        // Base message
                        let errorMessages = response.message || 'कृपया सभी आवश्यक फ़ील्ड भरें!';

                        // Validation errors list
                        if (response.errors) {
                            errorMessages += '<br><ul>';

                            $.each(response.errors, function(key, value) {
                                if (Array.isArray(value) && value.length > 0) {
                                    errorMessages += `<li>${value[0]}</li>`;
                                }
                            });

                            errorMessages += '</ul>';
                        }

                        // Show Swal with real backend message + errors
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            html: errorMessages,
                            allowOutsideClick: false
                        });

                        // Highlight invalid fields + focus on first visible invalid field
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                var element = $('[name="' + key + '"]');

                                if (element.length && element.is(':visible')) {
                                    element.addClass('is-invalid');
                                    element.focus();
                                    return false; // stop at first invalid visible field
                                }
                            });
                        }
                    }
                });
            });


            $('#myForm2').submit(function(e) {
                e.preventDefault();
                $('#user-details-btn').prop('disabled', true);
                // document.getElementById('per_district').disabled = false;
                // document.getElementById('ppincode').disabled = false;

                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');


                // Submit the form using AJAX
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    success: function(data) {
                        console.log('data:', data);
                        if (data.status == 'success') {
                            $('#user-details-btn').prop('disabled', false);
                            var applicant_id = data.Applicant_ID;
                            $("#fk_applicant_id").value = applicant_id;
                            hideAllDivs();

                            $("#tab3").css("display", "block");
                            $("#edu-info-btn").focus();

                            document.getElementById("personal-details-btn").removeAttribute(
                                "disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("edu-info-btn").style.backgroundColor =
                                "#21bf06";

                            let casteVal = $('#caste').val();
                            if (casteVal) {
                                sessionStorage.setItem('selectedCaste', casteVal);
                                let caste = sessionStorage.getItem('selectedCaste');
                                if (caste) {
                                    $('#selectedCasteInput').val(caste);
                                }

                                let castefieldDocCheck = @json(isset($data['applicant_details']->Document_Caste) ? $data['applicant_details']->Document_Caste : null);

                                //  Asterisk lagane ka condition
                                if (caste !== 'सामान्य' && !castefieldDocCheck) {
                                    const casteLabel = $('#caste_astrick');
                                    if (casteLabel.length && casteLabel.find(
                                            'font[color="red"]').length === 0) {
                                        casteLabel.append(' <font color="red">*</font>');
                                    }
                                }
                            }

                            checkAndLockPermanentAddress();

                            // ## Required Documents based on selected question, Experience, BPL, Widow_Divorce
                            var WidowDivorceDocuments = @json($data['applicant_details']->Document_Widow ?? null);
                            var BPLDocuments = @json($data['applicant_details']->Document_BPL ?? null);

                            if (data.ExpRequired == 1) {
                                $('.exp_required').attr('required', true);
                            }

                            const setRequiredIfMissing = (selector, labelSelector,
                                existingDoc) => {
                                if (!existingDoc) {
                                    $(selector).prop('required', true);
                                    const label = $(labelSelector);
                                    if (label.length && !label.find('font[color="red"]')
                                        .length) {
                                        label.append(' <font color="red">*</font>');
                                    }
                                }
                            };

                            if (data.WidowRequired == 1) {
                                // setRequiredIfMissing('#widow_divorce_document','#Widow_astrick', WidowDivorceDocuments);
                            }

                            if (data.BPLRequired == 1) {
                                // setRequiredIfMissing('#BPL_document', '#BPL_astrick',BPLDocuments);
                            }


                            console.log('Widow : ' + data.WidowRequired + ' - ' +
                                WidowDivorceDocuments);
                            console.log('BPL : ' + data.BPLRequired + ' - ' + BPLDocuments);



                            // Clear all error messages
                            $('#adhaar-error, #pincode-error, #ppincode-error, #domicile_district-error, #caste-error, #cur_district-error, #gender-error, #per_district-error,#current_area-error,#current_block-error,#current_gp-error,#current_nagar-error,#current_ward-error')
                                .html('');

                        } else if (data.status == 'error') {
                            if (data.errors) {
                                $('#adhaar-error').html(data.errors.adhaar ? data.errors.adhaar[
                                    0] : '');
                                $('#email-error').html(data.errors.email ? data.errors.email[
                                    0] : '');
                                $('#pincode-error').html(data.errors.pincode ? data.errors
                                    .pincode[0] : '');
                                $('#ppincode-error').html(data.errors.ppincode ? data.errors
                                    .ppincode[0] : '');
                                $('#domicile_district-error').html(data.errors
                                    .domicile_district ? data.errors.domicile_district[0] :
                                    '');
                                $('#caste-error').html(data.errors.caste ? data.errors.caste[
                                    0] : '');
                                $('#epic-error').html(data.errors.epicno ? data.errors.epicno[
                                    0] : '');
                                $('#cur_district-error').html(data.errors.cur_district ? data
                                    .errors.cur_district[0] : '');
                                $('#gender-error').html(data.errors.gender ? data.errors.gender[
                                    0] : '');
                                $('#per_district-error').html(data.errors.per_district ? data
                                    .errors.per_district[0] : '');
                                $('#mob-error').html(data.errors.mobile ? data
                                    .errors.mobile[0] : '');
                                $('#current_area-error').html(data.errors.current_area ? data
                                    .errors.current_area[0] : '');
                                $('#current_block-error').html(data.errors.current_block ? data
                                    .errors.current_block[0] : '');
                                $('#current_gp-error').html(data.errors.current_gp ? data
                                    .errors.current_gp[0] : '');
                                $('#current_nagar-error').html(data.errors.current_nagar ? data
                                    .errors.current_nagar[0] : '');
                                $('#current_ward-error').html(data.errors.current_ward ? data
                                    .errors.current_ward[0] : '');
                            } else {
                                // Clear all error messages
                                $('#adhaar-error, #pincode-error, #ppincode-error, #domicile_district-error, #caste-error, #cur_district-error, #gender-error, #per_district-error,#current_area-error,#current_block-error,#current_gp-error,#current_nagar-error,#current_ward-error')
                                    .html('');
                            }

                            // Prepare complete error message for popup
                            let errorMessage = '<ul style="text-align:left;">';
                            Object.keys(data.errors).forEach(function(key) {
                                errorMessage +=
                                    `<li>${data.errors[key][0]}</li>`;
                            });
                            errorMessage += '</ul>';

                            Swal.fire({
                                icon: 'warning',
                                title: data.message,
                                html: errorMessage,
                                allowOutsideClick: false
                            });


                        } else if (data.status == 'warning') {
                            Swal.fire({
                                icon: 'warning',
                                text: data['message'],
                                // text: data.errors,
                                allowOutsideClick: false
                            });
                            $('#user-details-btn').prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#user-details-btn').prop('disabled', false);
                        console.log(xhr.responseText);

                        let response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            console.error("Invalid JSON received");
                        }

                        // ✅ If alert_message exists, show directly in Swal and STOP further execution
                        if (response.alert_message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया ध्यान दें !',
                                text: response.alert_message,
                                confirmButtonText: 'ठीक है'
                            });
                            return; // ⛔ stop here
                        }

                        // ⚠️ Default validation error
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! ',
                            text: 'Please see fields marked in red.',
                            allowOutsideClick: false
                        });

                        if (response.errors) {
                            var errors = response.errors;

                            $.each(errors, function(key, value) {
                                var element = $('[name="' + key + '"]');
                                if (element.length) {
                                    element.addClass('is-invalid');
                                }
                            });

                            $('#error').html("This field is required");
                            $('#email-error').html("Enter Valid Email ID");
                            $('#mobile-error').html("Enter Valid 10 Digit Mobile No.");
                        }
                    }
                });
            });


            $('#myForm3').submit(function(e) {
                e.preventDefault();
                $('#user-edu-btn').prop('disabled', true);

                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');


                // Submit the form using AJAX
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#user-edu-btn').prop('disabled', false);


                            var applicant_id = data.applicant_id;
                            document.getElementById("applicant_id_tab4").value = applicant_id;

                            hideAllDivs();
                            $("#tab4").css("display", "block");
                            $("#exp-info-btn").focus();


                            document.getElementById("edu-info-btn").removeAttribute("disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("exp-info-btn").style.backgroundColor =
                                "#21bf06";

                            //  ####### Qualifications Astrick Required ##############
                            let sessionData_Astrick = data.user_qualifications || {};
                            let sessionArray_astrick = Object.values(sessionData_Astrick);

                            const qualificationFields_astk = {
                                1: '5th_astrick',
                                2: '8th_astrick',
                                3: 'ssc_astrick',
                                4: 'inter_astrick',
                                5: 'ug_astrick',
                                6: 'pg_astrick',
                                7: 'other_astrick'
                            };

                            const qualifications_astk = Array.isArray(sessionArray_astrick) ?
                                sessionArray_astrick : [];

                            if (!qualifications_astk.length) {
                                console.warn(
                                    'No qualifications found in session. Skipping asterisk insertion.'
                                );
                            }

                            qualifications_astk.forEach(qualification => {
                                if (qualificationFields_astk.hasOwnProperty(
                                        qualification)) {
                                    const fieldIDName_astk = qualificationFields_astk[
                                        qualification];
                                    const fieldElement_astk = $(`#${fieldIDName_astk}`);

                                    // Append red asterisk if not already added
                                    if (fieldElement_astk.length && fieldElement_astk
                                        .find('font[color="red"]').length === 0) {
                                        fieldElement_astk.append(
                                            ' <font color="red">*</font>');
                                    }
                                }
                            });


                            //  ####### Qualifications Documents Required ##############

                            let sessionData = data.user_qualifications || {};
                            let sessionArray = Object.values(sessionData);

                            const qualificationFields = {
                                1: {
                                    inputName: '5th_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_5th) ? $data['applicant_details']->Document_5th : null)
                                },
                                2: {
                                    inputName: '8th_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_8th) ? $data['applicant_details']->Document_8th : null)
                                },
                                3: {
                                    inputName: 'ssc_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_SSC) ? $data['applicant_details']->Document_SSC : null)
                                },
                                4: {
                                    inputName: 'inter_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_Inter) ? $data['applicant_details']->Document_Inter : null)
                                },
                                5: {
                                    inputName: 'ug_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_UG) ? $data['applicant_details']->Document_UG : null)
                                },
                                6: {
                                    inputName: 'pg_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_PG) ? $data['applicant_details']->Document_PG : null)
                                },
                                7: {
                                    inputName: 'other_marksheet',
                                    documentCheck: @json(isset($data['applicant_details']->Document_other) ? $data['applicant_details']->Document_other : null)
                                }
                            };

                            // First, remove 'required' from all qualification-related fields to ensure a clean state
                            Object.keys(qualificationFields).forEach(key => {
                                $(`input[name="${qualificationFields[key].inputName}"]`)
                                    .removeAttr('required');
                            });

                            const qualifications = Array.isArray(sessionArray) ? sessionArray :
                                [];

                            qualifications.forEach(qualification => {
                                if (qualificationFields.hasOwnProperty(qualification)) {
                                    const fieldData = qualificationFields[
                                        qualification];
                                    const inputName = fieldData.inputName;
                                    const documentUploaded = fieldData.documentCheck;

                                    // If a qualification is selected and no document is uploaded for it, make it required
                                    if (!documentUploaded) {
                                        $(`input[name="${inputName}"]`).attr('required',
                                            'required');
                                    }
                                }
                            });


                            //  #####################
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            }).then(() => {
                                $('#user-edu-btn').prop('disabled', false);
                            });

                        }
                    },
                    error: function(xhr, status, error) {
                        $('#user-edu-btn').prop('disabled', false);

                        let response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            console.error("Invalid JSON received", e);
                        }

                        // ✅ If server sends { alert_message: "..." } then show it in Swal and stop
                        if (response.alert_message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया ध्यान दें !',
                                text: response.alert_message,
                                confirmButtonText: 'ठीक है'
                            });
                            return; // ⛔ no further processing
                        }

                        // ⚠️ Default validation message
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें !',
                            text: 'Please see fields marked in red.',
                            allowOutsideClick: false
                        });

                        if (response.errors) {
                            var errors = response.errors;
                            var errorMessages = '';

                            $.each(errors, function(key, value) {
                                errorMessages += value[0] + '<br>';

                                var element = $('[name="' + key + '"]');
                                if (element.length) {
                                    element.addClass('is-invalid');
                                }
                            });

                            $('#error').html("This field is required");
                        }
                    }
                });
            });


            $('#myForm4').submit(function(e) {
                e.preventDefault();
                $('#user-exp-btn').prop('disabled', true);
                // Rules For Experience Document if Files Uploaded
                let isValid = true;
                let maxFileSizeKB = 2048; // 2MB
                let allowedTypes = ['application/pdf'];

                // Clear previous errors
                $('#experience-doc-error').text('').hide();
                $('#error-अनुभव_प्रमाण_पत्र').text('').hide();

                // Select file input safely
                const fileInput = $('input[name="experience_document[]"]');

                if (fileInput.length > 0) {
                    const file = fileInput[0].files[0]; // first selected file

                    if (file) {
                        console.log("File selected:", file.name, "Type:", file.type, "Size:", file.size);

                        // 1. Validate type
                        if (!allowedTypes.includes(file.type)) {
                            isValid = false;
                            $('#experience-doc-error').text('केवल PDF फ़ाइल की अनुमति है।').show();
                            $('#user-exp-btn').prop('disabled', false);

                        }

                        // 2. Validate size
                        const fileSizeKB = file.size / 1024;
                        if (fileSizeKB > maxFileSizeKB) {
                            isValid = false;
                            $('#experience-doc-error').text('फ़ाइल का आकार 2MB से अधिक नहीं होना चाहिए।')
                                .show();
                            $('#user-exp-btn').prop('disabled', false);

                        }
                    }
                }

                // Stop form submission if invalid
                if (!isValid) {
                    return false;
                    $('#user-exp-btn').prop('disabled', false);

                }


                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token value
                form.append('_token', '{{ csrf_token() }}');

                // Submit the form using AJAX
                $.ajax({
                    url: url,
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#user-exp-btn').prop('disabled', false);

                            Swal.fire({
                                icon: data['icon'],
                                text: data['message'],
                                allowOutsideClick: false
                            }).then(() => {
                                var applicant_id = data.applicant_id;
                                document.getElementById("applicant_id_tab5").value =
                                    applicant_id;


                                hideAllDivs();
                                $("#tab5").css("display", "block");
                                $("#attachmnet-btn").focus();


                                document.getElementById("exp-info-btn").removeAttribute(
                                    "disabled");
                                removeGreenBackground
                                    (); // Remove green background from all buttons

                                document.getElementById("attachmnet-btn").style
                                    .backgroundColor =
                                    "#21bf06";


                            });

                        } else if (data.status == 'error') {
                            $('#user-exp-btn').prop('disabled', false);

                            let errorList = '';
                            if (data.errors) {
                                for (const key in data.errors) {
                                    if (data.errors.hasOwnProperty(key)) {
                                        data.errors[key].forEach(function(msg) {
                                            errorList += `\n• ${msg}`;
                                        });
                                    }
                                }
                            }

                            $('#experience-doc-error1').text(errorList).show();

                            Swal.fire({
                                icon: 'warning',
                                title: data['message'],
                                text: errorList.trim(),
                                allowOutsideClick: false
                            }).then(() => {
                                $('#user-exp-btn').prop('disabled', false);

                            });
                        } else if (data.status == 'warning') {
                            Swal.fire({
                                icon: 'warning',
                                text: data['message'],
                                allowOutsideClick: false
                            }).then(() => {
                                $('#user-exp-btn').prop('disabled', false);


                                var applicant_id = data.applicant_id;
                                document.getElementById("applicant_id_tab5").value =
                                    applicant_id;


                                hideAllDivs();
                                $("#tab5").css("display", "block");
                                $("#attachmnet-btn").focus();


                                document.getElementById("exp-info-btn").removeAttribute(
                                    "disabled");
                                removeGreenBackground
                                    (); // Remove green background from all buttons

                                document.getElementById("attachmnet-btn").style
                                    .backgroundColor =
                                    "#21bf06";
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#user-exp-btn').prop('disabled', false);

                        console.log('ajax error:', xhr.responseText);

                        let response;

                        // Safe JSON parse
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = {
                                message: 'कृपया सभी आवश्यक फ़ील्ड भरें !',
                                errors: {}
                            };
                        }

                        // ✅ If alert_message exists → show direct error and STOP further execution
                        if (response.alert_message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया ध्यान दें !',
                                text: response.alert_message,
                                confirmButtonText: 'ठीक है'
                            });
                            return; // ⛔ STOP here
                        }

                        // Base message
                        let errorMessages = response.message ||
                            'कृपया सभी आवश्यक फ़ील्ड भरें !';

                        // Agar validation errors aaye hain to unko list karo + fields highlight
                        if (response.errors) {
                            errorMessages += '<br><ul>';

                            $.each(response.errors, function(key, value) {
                                if (Array.isArray(value) && value.length > 0) {
                                    errorMessages += `<li>${value[0]}</li>`;
                                }

                                var element = $('[name="' + key + '"]');
                                if (element.length) {
                                    element.addClass('is-invalid');
                                    element
                                        .closest('.form-group')
                                        .find('.invalid-feedback')
                                        .html(value[0] || '');
                                }
                            });

                            errorMessages += '</ul>';
                            $('#error').html('This field is required');
                        }

                        // Swal me proper message दिखाओ
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            html: errorMessages,
                            allowOutsideClick: false
                        });
                    }
                });
            });


            $('#myForm5').submit(function(e) {
                e.preventDefault();
                $('#user-doc-btn').prop('disabled', true);

                // Handle caste certificate requirement
                let caste = $('#selectedCasteInput').val();
                if (caste === 'सामान्य') {
                    $('input[name="caste_certificate"]').removeAttr('required');
                } else {
                    $('input[name="caste_certificate"]').attr('required', 'required');
                }

                // Validate file inputs
                const inputs = $('.documentInput');
                let isValid = true;
                let errorMessage = '';

                inputs.each(function() {
                    const input = $(this);
                    const dataKey = input.attr('data-key');
                    const Files = dataKey ? dataKey.trim() : '';
                    const errorSpanId =
                        `#error-${Files.replace(/[^a-zA-Z0-9\u0900-\u097F]+/g, '_')}`;

                    // Reset any previous error
                    $(errorSpanId).text('');

                    const result = validateFileInput(input, Files);
                    if (!result.isValid) {
                        isValid = false;
                        // Show the error on specific <span> near the input
                        $(errorSpanId).text(result.errorMessage);
                        errorMessage = result.errorMessage;
                        $('#user-doc-btn').prop('disabled', false);

                        // Focus the field to guide user
                        input.focus();
                        return false; // break loop
                    }
                });

                // Common validation function
                function validateFileInput(input, Files) {
                    if (!input || input.length === 0) {
                        console.error("No input element provided to validateFileInput()");
                        return {
                            isValid: true,
                            errorMessage: ''
                        };
                    }

                    if (input.attr("type") !== "file") {
                        console.error("Provided input is not a file input:", input);
                        return {
                            isValid: true,
                            errorMessage: ''
                        };
                    }

                    const isRequired = input.prop('required');
                    const file = input[0].files && input[0].files[0];
                    let isValid = true;
                    let errorMessage = '';

                    // Required field validation
                    if (isRequired && (!file || input[0].files.length === 0)) {
                        isValid = false;
                        errorMessage = `कृपया ${Files} फ़ाइल अपलोड करें।`;
                        return {
                            isValid,
                            errorMessage
                        };
                    }

                    // Validate only if a file is selected
                    if (file) {
                        const fileType = file.type;
                        const fileSize = file.size;
                        let expectedMaxFileSize;
                        let allowedTypes;

                        // Define file type and size rules
                        switch (Files) {
                            case 'पासपोर्ट फोटो':
                                expectedMaxFileSize = 100 * 1024; // 100KB
                                allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                                errorMessage =
                                    'कृपया पासपोर्ट साइज़ फोटो के लिए JPG, JPEG या PNG फ़ाइल अपलोड करें।';
                                break;
                            case 'हस्ताक्षर':
                                expectedMaxFileSize = 50 * 1024; // 50KB
                                allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                                errorMessage = 'कृपया हस्ताक्षर के लिए JPG, JPEG या PNG फ़ाइल अपलोड करें।';
                                break;
                            case 'आधार':
                                expectedMaxFileSize = 2 * 1024 * 1024; // 2MB
                                allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                                errorMessage =
                                    'कृपया आधार कार्ड के लिए PDF या इमेज फ़ाइल (PNG, JPEG, JPG, PDF) अपलोड करें।';
                                break;
                            default:
                                expectedMaxFileSize = 2 * 1024 * 1024; // 2MB
                                allowedTypes = ['application/pdf'];
                                errorMessage = `कृपया ${Files} के लिए केवल PDF फ़ाइल अपलोड करें।`;
                        }

                        // Validate file type
                        if (!allowedTypes.includes(fileType)) {
                            isValid = false;
                            return {
                                isValid,
                                errorMessage
                            };
                        }

                        // Validate file size
                        if (fileSize > expectedMaxFileSize) {
                            isValid = false;
                            const sizeLimit = expectedMaxFileSize >= 1024 * 1024 ?
                                `${(expectedMaxFileSize / (1024 * 1024))}MB` :
                                `${(expectedMaxFileSize / 1024)}KB`;
                            errorMessage = `${Files} फ़ाइल का आकार ${sizeLimit} से अधिक नहीं होना चाहिए।`;
                        }
                    }

                    return {
                        isValid,
                        errorMessage
                    };
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'त्रुटि1',
                        text: errorMessage,
                        allowOutsideClick: false
                    });
                    $('#user-doc-btn').prop('disabled', false);
                    return;
                }

                if (isValid) {

                    // Proceed with form submission
                    $('#user-doc-btn').prop('disabled', true);
                    var form = new FormData(this);
                    var url = $(this).attr('action');
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');

                    // Ensure CSRF token is included
                    if (!csrf_token) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'त्रुटि2',
                            text: 'CSRF टोकन अनुपस्थित है। कृपया पेज को रिफ्रेश करें।',
                            allowOutsideClick: false
                        });
                        $('#user-doc-btn').prop('disabled', false);
                        return;
                    }


                    form.append('_token', csrf_token);

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: form,
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: 'json',
                        context: this,
                        headers: {
                            'X-CSRF-TOKEN': csrf_token // Add CSRF token to headers as fallback
                        },
                        beforeSend: function() {
                            $('#loader').show();
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.status === 'success') {
                                $('#user-doc-btn').prop('disabled', false);

                                var applicant_id = data.applicant_id;
                                var application_id = data.application_id;
                                Swal.fire({
                                    icon: 'success',
                                    text: data.message,
                                    allowOutsideClick: false
                                }).then((result) => {
                                    window.location.href =
                                        '/candidate/view-application-detail/' +
                                        applicant_id + '/' + application_id;

                                });

                                // Clear all error messages
                                $('#photo-error,#sign-error,#aadhaar-doc-error,#domicile-doc-error,#fifth-doc-error,#eight-doc-error,#tenth-doc-error,#12th-doc-error,#UG-doc-error,#PG-doc-error,#experience-doc-error,#epic-doc-error,#other-doc-error,#BPL-doc-error,#widow-doc-error,#caste_certificate_error,#skills-error,#questions-error,#skill_options-error')
                                    .html('');

                            } else if (data.status == 'error') {
                                $('#user-doc-btn').prop('disabled', false);



                                if (data.errors) {
                                    $('#photo-error').html(data.errors.document_photo ? data
                                        .errors.document_photo[0] : '');
                                    $('#sign-error').html(data.errors.document_sign ? data
                                        .errors.document_sign[0] : '');
                                    $('#aadhaar-doc-error').html(data.errors.document_adhaar ?
                                        data.errors.document_adhaar[0] : '');
                                    $('#domicile-doc-error').html(data.errors.domicile ? data
                                        .errors.domicile[0] : '');
                                    $('#fifth-doc-error').html(data.errors['5th_marksheet'] ?
                                        data.errors['5th_marksheet'][0] : '');
                                    $('#eight-doc-error').html(data.errors['8th_marksheet'] ?
                                        data.errors['8th_marksheet'][0] : '');
                                    $('#tenth-doc-error').html(data.errors.ssc_marksheet ? data
                                        .errors.ssc_marksheet[0] : '');
                                    $('#12th-doc-error').html(data.errors.inter_marksheet ? data
                                        .errors.inter_marksheet[0] : '');
                                    $('#UG-doc-error').html(data.errors.ug_marksheet ? data
                                        .errors.ug_marksheet[0] : '');
                                    $('#PG-doc-error').html(data.errors.pg_marksheet ? data
                                        .errors.pg_marksheet[0] : '');
                                    $('#experience-doc-error').html(data.errors.exp_document ?
                                        data.errors.exp_document[0] : '');
                                    $('#epic-doc-error').html(data.errors.epic_document ? data
                                        .errors.epic_document[0] : '');
                                    $('#BPL-doc-error').html(data.errors.bpl_marksheet ? data
                                        .errors.bpl_marksheet[0] : '');
                                    $('#widow-doc-error').html(data.errors.widow_certificate ?
                                        data.errors.widow_certificate[0] : '');
                                    $('#other-doc-error').html(data.errors.other_marksheet ?
                                        data.errors.other_marksheet[0] : '');
                                    $('#caste_certificate_error').html(data.errors
                                        .caste_certificate ? data.errors.caste_certificate[
                                            0] : '');
                                    $('#skills-error').html(data.errors.skills ? data.errors
                                        .skills[0] : '');
                                    $('#skill_options-error').html(data.errors.skill_options ?
                                        data.errors.skill_options[0] : '');
                                    $('#questions-error').html(data.errors.questions ? data
                                        .errors.questions[0] : '');

                                    let errorMessage = '';
                                    if (data.errors.skills) {
                                        errorMessage += data.errors.skills[0] + ' ';
                                    }
                                    if (data.errors.skill_options) {
                                        errorMessage += data.errors.skill_options[0] + ' ';
                                    }
                                    if (data.errors.questions) {
                                        errorMessage += data.errors.questions[0];
                                    }

                                    Swal.fire({
                                        icon: 'warning',
                                        text: errorMessage.trim(),
                                        allowOutsideClick: false
                                    });

                                } else {
                                    // Clear all error messages
                                    $('#photo-error,#sign-error,#aadhaar-doc-error,#domicile-doc-error,#fifth-doc-error,#eight-doc-error,#tenth-doc-error,#12th-doc-error,#UG-doc-error,#PG-doc-error,#experience-doc-error,#epic-doc-error,#other-doc-error,#BPL-doc-error,#widow-doc-error,#caste_certificate_error,#skills-error,#questions-error,#skill_options-error')
                                        .html('');
                                }

                            } else if (data.status == 'warning') {

                                Swal.fire({
                                    icon: 'warning',
                                    text: data.message,
                                    allowOutsideClick: false
                                });
                                $('#user-doc-btn').prop('disabled', false);


                            }
                        },
                        error: function(xhr, status, error) {
                            $('#user-doc-btn').prop('disabled', false);
                            Swal.close();

                            // console.log('error data of docs:', xhr.responseText);

                            let response;

                            // Safe JSON parsing
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (e) {
                                response = {
                                    status: 'error',
                                    message: 'सर्वर त्रुटि: कृपया पुनः प्रयास करें।',
                                    errors: {}
                                };
                            }

                            // 🔥 If server returns alert_message → Show directly and STOP
                            if (response.alert_message) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'कृपया ध्यान दें !',
                                    text: response.alert_message,
                                    confirmButtonText: 'ठीक है'
                                });
                                return; // ⛔ stop further handling
                            }

                            // 🔐 CSRF Token Expired (419)
                            if (xhr.status === 419) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'सत्र समाप्त',
                                    html: 'आपका सत्र समाप्त हो गया है। कृपया पेज को रिफ्रेश करें या फिर से लॉगिन करें।',
                                    allowOutsideClick: false
                                }).then(() => {
                                    window.location.reload();
                                });
                                $('#user-doc-btn').prop('disabled', false);
                                return;
                            }

                            // Base error message
                            let errorMessages = response.message ||
                                'कृपया सभी आवश्यक फ़ील्ड भरें!';

                            // Validation errors list + field highlight
                            if (response.errors) {
                                errorMessages += '<br><ul>';

                                $.each(response.errors, function(key, errors) {
                                    if (Array.isArray(errors) && errors.length > 0) {
                                        errors.forEach(function(msg) {
                                            errorMessages += `<li>${msg}</li>`;
                                        });
                                    }

                                    // highlight invalid input
                                    var element = $(`[name="${key}"]`);
                                    if (element.length) {
                                        element.addClass('is-invalid');
                                        element.siblings('.invalid-feedback').html(
                                            errors[0] || '');
                                    }
                                });

                                errorMessages += '</ul>';
                            }

                            // 🔥 Final Swal showing real errors
                            Swal.fire({
                                icon: 'warning',
                                title: 'कृपया ध्यान दें ',
                                html: errorMessages,
                                allowOutsideClick: false
                            });

                            $('#user-doc-btn').prop('disabled', false);
                        },
                        complete: function() {
                            $('#loader').hide();
                        }
                    });
                }
            });


            // ##=========== //Form Tabs Functionality ===============================##
            function hideAllDivs() {
                document.getElementById("tab1").style.display = "none";
                document.getElementById("tab2").style.display = "none";
                document.getElementById("tab3").style.display = "none";
                document.getElementById("tab4").style.display = "none";
                document.getElementById("tab5").style.display = "none";
            }

            document.getElementById("post-details-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab1").style.display = "block";
            });

            document.getElementById("personal-details-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab2").style.display = "block";
            });

            document.getElementById("edu-info-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab3").style.display = "block";
            });

            document.getElementById("exp-info-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab4").style.display = "block";
            });

            document.getElementById("attachmnet-btn").addEventListener("click", function() {
                hideAllDivs();
                document.getElementById("tab5").style.display = "block";
            });

            // Show the tab corresponding to #stepCount on page load
            function showTabForStep(step) {
                try {
                    var s = parseInt(step);
                    if (isNaN(s) || s < 1 || s > 5) return;
                    hideAllDivs();
                    document.getElementById('tab' + s).style.display = 'block';

                    // Map to button ids
                    var btnMap = {
                        1: 'post-details-btn',
                        2: 'personal-details-btn',
                        3: 'edu-info-btn',
                        4: 'exp-info-btn',
                        5: 'attachmnet-btn'
                    };

                    // Enable only buttons up to current step; disable the rest
                    for (var i = 1; i <= 5; i++) {
                        var id = btnMap[i];
                        var el = document.getElementById(id);
                        if (!el) continue;
                        if (i <= s) {
                            el.removeAttribute('disabled');
                        } else {
                            el.setAttribute('disabled', 'disabled');
                        }
                    }

                    removeGreenBackground();

                    var activeBtnId = btnMap[s];
                    if (activeBtnId && document.getElementById(activeBtnId)) {
                        var activeBtn = document.getElementById(activeBtnId);
                        activeBtn.style.backgroundColor = '#21bf06';
                        try {
                            activeBtn.focus();
                        } catch (e) {}
                    }
                } catch (e) {
                    console.error('showTabForStep error', e);
                }
            }

            // Call after DOM ready
            $(function() {
                var initialStep = $('#stepCount').val();
                if (initialStep) showTabForStep(initialStep);
            });

            // Function to remove green background from all buttons
            function removeGreenBackground() {
                var buttons = document.getElementsByClassName("add-app-btn");
                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].style.backgroundColor = "#0d6efd";
                }
            }

            // Add click event listeners to all buttons
            var buttons = document.getElementsByClassName("add-app-btn");
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].addEventListener("click", function() {
                    removeGreenBackground(); // Remove green background from all buttons
                    this.style.backgroundColor =
                        "#157347"; // Set green background only on the clicked button
                });
            }






            // ##=========== //Current And Permamenent Address Same ===============================##
            $(document).on('change', '#same', function() {
                if ($(this).prop('checked')) {
                    // console.log('Checkbox checked');
                    var caddr = $('#caddr').val();
                    var cpincode = $('#cpincode').val();
                    var ccities = $('#cur_district').val();

                    $('#paddr').val(caddr).attr('readonly', true);
                    $('#per_district').val(ccities).trigger('change').attr("disabled", true);
                    pincode(ccities, "ppincode", cpincode);
                    $('#ppincode').attr("disabled", true);
                } else {
                    // console.log('Checkbox unchecked');
                    $('#paddr').val('').attr('readonly', false);
                    $('#ppincode').val('').attr('disabled', false);
                    $('#per_district').val('').trigger('change').attr("disabled", false);
                }
            });

            $(document).on('input change', '#caddr, #cpincode, #cur_district', function() {
                if ($('#same').prop('checked')) {
                    $('#same').prop('checked', false).trigger('change');
                }
            });






            // ##=========== //Add More Btn Function For Exp Details ===============================##
            // Function to toggle NGO field visibility
            function toggleNgoField($selectElement) {
                var $row = $selectElement.closest('.experience-row');
                var $ngoField = $row.find('.ngo-field');

                if ($selectElement.val() === '2') {
                    $ngoField.show();
                    $ngoField.find('input').prop('required', true);
                } else {
                    $ngoField.hide();
                    $ngoField.find('input').prop('required', false).val('');
                }
            }

            // Function to handle date logic and experience calculation
            function setDateLogicAndExperience($row) {
                const $from = $row.find('input[name="date_from[]"]');
                const $to = $row.find('input[name="date_to[]"]');
                const $experience = $row.find('input[name="total_experience[]"]');

                $from.on('change', function() {
                    const fromDate = $(this).val();
                    $to.attr('min', fromDate);
                    calculateExperience($from, $to, $experience);
                });

                $to.on('change', function() {
                    calculateExperience($from, $to, $experience);
                });
            }

            // Function to calculate experience
            function calculateExperience($from, $to, $experience) {
                const fromDate = new Date($from.val());
                const toDate = new Date($to.val());

                if ($from.val() && $to.val() && toDate >= fromDate) {
                    let years = toDate.getFullYear() - fromDate.getFullYear();
                    let months = toDate.getMonth() - fromDate.getMonth();
                    let days = toDate.getDate() - fromDate.getDate();

                    if (days < 0) {
                        months--;
                        days += new Date(toDate.getFullYear(), toDate.getMonth(), 0).getDate();
                    }
                    if (months < 0) {
                        years--;
                        months += 12;
                    }

                    let formatted = '';
                    if (years > 0) formatted += years + ' वर्ष ';
                    if (months > 0) formatted += months + ' माह ';
                    if (days > 0) formatted += days + ' दिन';

                    $experience.val(formatted);
                } else {
                    $experience.val('');
                }
            }

            // Initial NGO change listener
            $('.org-type-select').on('change', function() {
                toggleNgoField($(this));
            });

            // Trigger NGO toggle on page load
            $('.org-type-select').each(function() {
                toggleNgoField($(this));
            });

            // Initialize date logic for initial row
            $('.experience-row').each(function() {
                setDateLogicAndExperience($(this));
            });


            // Add More Button Function
            $('#addmoreBtn').click(function() {



                var rowCount = $('.experience-row').length;
                // console.log('Row Count: ' + rowCount);
                var ExpHead = rowCount + 1;

                var newInputFields = `<div class="row experience-row mt-3 border-bottom border-dark">
                                                             <h5 class="text-primary">अनुभव जानकारी ${ExpHead} :</h5> 
                                                                <div class="row" >
                                                                                <div class="col-md-5 text-left"><br>
                                                                                    <label for="orgname_${rowCount}">संस्था का नाम </label>
                                                                                    <input type="text" id="orgname_${rowCount}" class="form-control alphabets-only" name="org_name[]"
                                                                                     style="text-transform:uppercase;"    placeholder="संस्था का नाम/पता दर्ज करें" >
                                                                                </div>
                                                                                <div class="col-md-3 text-left"><br>
                                                                                    <label for="orgtype_${rowCount}">संस्था शासकीय है अथवा अशासकीय</label>
                                                                                    <select id="orgtype_${rowCount}" class="form-select org-type-select" name="org_type[]" >
                                                                                   <option selected disabled value="">-- चयन करें --</option>
                                                                                       @foreach ($organization_type as $org_type)
                                                                                            @if ($org_type->org_id != 4)
                                                                                                <option value="{{ $org_type->org_id }}"
                                                                                                    {{ isset($experience_details) && $experience_details->Organization_Type == $org_type->org_id ? 'selected' : '' }}>
                                                                                                    {{ $org_type->org_type }}
                                                                                                </option>
                                                                                            @endif
                                                                                        @endforeach
                                                                                 </select>
                                                                                </div>
                                                                                <div class="col-md-4 text-left ngo-field" style="display: none;">
                                                                                    <label for="ngono_${rowCount}">यदि संस्था अशासकीय है तो भारत शासन के NGO पोर्टल में पंजीयन क्र.</label>
                                                                                    <input type="text" id="ngono_${rowCount}" class="form-control" name="ngo_no[]"
                                                                                        placeholder="NGO पोर्टल में पंजीयन क्र दर्ज करें">
                                                                                </div>
                                                                                 <div class="col-md-4 text-left">
                                                                                    <label for="nature"></label>संस्था का पूरा पता<label
                                                                                        style="color:red">*</label>
                                                                                    <textarea id="org_address_${rowCount}" type="text" class="form-control" name="org_address[]" 
                                                                                        placeholder="संस्था का पूरा पता दर्ज करें"></textarea>
                                                                                   
                                                                                </div>
                                                                                <div class="col-md-4 text-left">
                                                                                    <label for="nature"></label>संस्था का दूरभाष</label>
                                                                                    <input type="text" id="org_contact_${rowCount}"
                                                                                        class="form-control number-only" minlength="10" maxlength="10" name="org_contact[]"
                                                                                         placeholder="संस्था का दूरभाष दर्ज करें">
                                                                                </div>
                                                                                <div class="col-md-3 text-left"><br>
                                                                                    <label for="desgname_${rowCount}">पदनाम</label>
                                                                                    <input type="text" id="desgname_${rowCount}" class="form-control alphabets-only" name="desg_name[]"
                                                                                       style="text-transform:uppercase;"  placeholder="पदनाम दर्ज करें" >
                                                                                </div>
                                                                                <div class="col-md-5 text-left"><br>
                                                                                    <label for="nature_${rowCount}">कार्य का विवरण</label>
                                                                                    <input type="text" id="nature_${rowCount}" class="form-control alphabets-only" name="nature_work[]"
                                                                                        placeholder="कार्य का विवरण दर्ज करें" >
                                                                                </div>
                                                                                <div class="col-md-3 text-left"><br>
                                                                                    <label for="nature_${rowCount}">मासिक वेतन / मानदेय</label>
                                                                                    <input type="text" id="salary_${rowCount}" class="form-control " name="salary[]"
                                                                                        placeholder="मासिक वेतन / मानदेय दर्ज करें" >
                                                                                </div>
                                                                                <div class="col-md-3 text-left"><br>
                                                                                    <label for="dfrom_${rowCount}">कब से</label>
                                                                                    <input type="date" id="dfrom_${rowCount}" class="form-control" name="date_from[]"
                                                                                        max="{{ $data['maxdate'] }}" >
                                                                                </div>
                                                                                <div class="col-md-3 text-left"><br>
                                                                                    <label for="dto_${rowCount}">कब तक</label>
                                                                                    <input type="date" id="dto_${rowCount}" class="form-control" name="date_to[]"
                                                                                        max="{{ $data['maxdate'] }}" >
                                                                                </div>

                                                                                <div class="col-md-3 text-left"><br>
                                                                                    <label for="total_exp_${rowCount}">कुल अनुभव</label>
                                                                                    <input type="text" id="total_exp_${rowCount}" class="form-control" name="total_experience[]" readonly>
                                                                               </div>
                                                                                <div class="col-md-4 col-xs-12 text-left mt-2">
                                                                                    <label for="exp_doc_${rowCount}">अनुभव प्रमाण पत्र / Upload Experience
                                                                                        Certificate </label><br>

                                                                                    <input type="file" id="exp_doc_${rowCount}" class="form-control documentInput"
                                                                                        name="exp_document[]" data-key="अनुभव प्रमाण पत्र"
                                                                                        accept=".pdf">
                                                                                    <span class="file-preview-link"></span>

                                                                                    <br><span class="text-danger error-msg"
                                                                                        id="error-अनुभव_प्रमाण_पत्र"></span>
                                                                                </div>
                                                                             

                                                                            </div>  <br>
                                                                            </div>`;



                $('#inputFieldsContainer').append(newInputFields);

                // Setup listeners for new row
                $('#orgtype_' + rowCount).on('change', function() {
                    toggleNgoField($(this));
                });

                let $newRow = $('.experience-row').last();
                setDateLogicAndExperience($newRow);

                $('.removeBtn').show();


            });


            // Remove functionality
            function RemoveEXpBtn(context = null) {
                if (context) {
                    $(context).closest('.experience-row').remove();
                }

                if ($('.experience-row').length <= 1) {
                    $('.removeExp').hide();
                } else {
                    $('.removeExp').show();
                }
            }

            // Page load 
            RemoveEXpBtn();

            // Remove Event
            $(document).on('click', '.removeExp', function() {
                RemoveEXpBtn(this);
            });


            // Clear custom validation messages when correcting
            $('input[type="number"]').on('input', function() {
                const $this = $(this);
                const value = $this.val();

                if (value !== '') {
                    $this.removeClass('is-invalid');
                    $this.next('.invalid-feedback').text('This field is required');
                }
            });


        });
    </script>
@endsection
