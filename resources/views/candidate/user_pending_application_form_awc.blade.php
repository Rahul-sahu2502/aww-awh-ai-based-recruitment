@extends('layouts.dahboard_layout')
@section('styles')
    <style>
        .myImg {
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .myImg:hover {
            opacity: 0.7;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.9);
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* Caption of Modal Image */
        #caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
        }

        /* Add Animation */
        .modal-content,
        #caption {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(0)
            }

            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0)
            }

            to {
                transform: scale(1)
            }
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
        }

        label {
            font-size: 14px;
        }
    </style>
@endsection
@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">

                <div class="row act" data-active-tab="{{ $data['candidate_details'][0]->stepCount }}">
                    <div class="col-md-2 grid-margin stretch-card" style="padding: 0px !important;">
                        <button style="width: 100%; border-radius: 15px;" id="post-btn" type="button"
                            class="btn btn-success btn-sm btn-icon-text add-app-btn class1">
                            पद चयन
                        </button>
                    </div>
                    <div class="col-md-2 grid-margin stretch-card" style="padding: 0px !important;">
                        <button id="personal-details-btn" style="width: 100%; border-radius: 15px;" type="button"
                            class="btn btn-primary btn-sm btn-icon-text add-app-btn class2" <?php echo $data['candidate_details'][0]->stepCount >= 2 ? '' : 'disabled'; ?>>
                            व्यक्तिगत जानकारी
                        </button>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card" style="padding: 0px !important;">
                        <button id="edu-info-btn" style="width: 100%; border-radius: 15px;" type="button"
                            class="btn btn-primary btn-sm btn-icon-text add-app-btn class3" <?php echo $data['candidate_details'][0]->stepCount >= 3 ? '' : 'disabled'; ?>>
                            शैक्षणिक योग्यता
                        </button>
                    </div>
                    <div class="col-md-3 grid-margin stretch-card" style="padding: 0px !important;">
                        <button id="exp-info-btn" style="width: 100%; border-radius: 15px;" type="button"
                            class="btn btn-primary btn-sm btn-icon-text add-app-btn class4" <?php echo $data['candidate_details'][0]->stepCount >= 4 ? '' : 'disabled'; ?>>
                            अनुभव एवं अन्य जानकारी
                        </button>
                    </div>
                    <div class="col-md-2 grid-margin stretch-card" style="padding: 0px !important;">
                        <button id="attachment-btn" style="width: 100%; border-radius: 15px;" type="button"
                            class="btn btn-primary btn-sm btn-icon-text add-app-btn class5" <?php echo $data['candidate_details'][0]->stepCount >= 5 ? '' : 'disabled'; ?>>
                            दस्तावेज अपलोड
                        </button>
                    </div>
                </div>
                <br>
                <?php $storedApplicantID = $data['candidate_details'][0]->RowID; ?>
                <?php $post_min_quali_url = $data['candidate_details'][0]->Quali_ID; ?>

                <!-- // form1 -->
                <div class="card" id="tab1">
                    <div class="row container">
                        <form id="myForm1" enctype="multipart/form-data" data-storage-key="application.post ">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="stepCount" value="1" />
                            <input type="hidden" name="app_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-12"><br>
                                        <label style="color:red"><b>Important Note:</b></label><br>
                                        <label><i class="bi bi-arrow-right"></i> आवेदन करने से पूर्व विज्ञप्ति को ध्यान
                                            पूर्वक पढ़ें |</label>
                                        <label><i class="bi bi-arrow-right"></i> आवेदन फॉर्म को 5 भागो में भरा जाना है Post
                                            Selection, व्यक्तिगत जानकारी, शैक्षणिक योग्यता, अनुभव एवं अन्य जानकारी, दस्तावेज
                                            अपलोड | प्रत्येक भाग को भरने के पश्चात Next बटन क्लिक करना है |</label>
                                        <label><i class="bi bi-arrow-right"></i> <label style="color:red">*</label> मार्क
                                            किये हुए जानकारी को भरना अनिवार्य है |</label>
                                        <label><i class="bi bi-arrow-right"></i> आवेदन करते समय स्कैन किये हुए फोटो एवं
                                            हस्ताक्षर JPG/PNG फॉर्मेट में अपलोड करना होगा एवं उनका साइज़ 50 KB से अधिक नहीं
                                            होना चाहिए |</label>
                                        <label><i class="bi bi-arrow-right"></i> एक बार आवेदन करने के पश्चात् नियुक्ति की
                                            प्रक्रिया के किसी भी स्तर में पृथक से अन्य कोई दस्तावेज स्वीकृत नहीं किये
                                            जायेंगे एवं दस्तावेजों की हार्ड कॉपी आवेदन फॉर्म के प्रिंट के साथ विज्ञप्ति में
                                            उल्लेखित कार्यालय में जमा करना होगा |</label>
                                        <label><i class="bi bi-arrow-right"></i> आवेदन करते समय अपने सारे दस्तावेज
                                            (10th,12th,UG,PG Marksheets एवं Experience Certificates ) अपने पास रखें ताकि
                                            फॉर्म भरते समय कोई जानकारी के लिए आपको असुविधा ना हो |</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6"><br>
                                            <label for="postname">पद सेलेक्ट करें </label><label style="color:red">*</label>
                                            <select class="form-select" id="postname" name="postname"
                                                onchange="select_district()" required>
                                                <option selected disabled value="undefined">--चुनें--</option>
                                                <?php foreach ($data['recruitment'] as $value_rc) {
    // if (isset($data['candidate_details'][0]->Post_ID) && $data['candidate_details'][0]->Post_ID == $value_rc->post_id) { 
                                            ?>
                                                <option value="{{ $value_rc->post_id }}"
                                                    @if ($data['candidate_details'][0]->Post_ID == $value_rc->post_id) selected @endif>
                                                    {{ $value_rc->title }}
                                                </option>
                                                <?php
} ?>
                                            </select>
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>

                                        <div class="col-md-6"><br>
                                            <label for="city" id="labelcity">जिले का चयन करें </label><label
                                                style="color:red">*</label>
                                            <select class="form-select" name="district" id="district" required>
                                                <option selected disabled value="undefined">-- Select --</option>


                                                <?php foreach ($data['cities'] as $district) {
    // if (isset($data['candidate_details'][0]->Pref_Districts) && $data['candidate_details'][0]->Pref_Districts == $district->District_Code_LGD) { 
                                            ?>
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    @if ($data['candidate_details'][0]->Pref_Districts == $district->District_Code_LGD) selected @endIf>
                                                    {{ $district->name }}
                                                </option>
                                                <?php
} ?>
                                            </select>
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <button style="float: right;" id="save-next-btn1"
                                            class="btn btn-primary nextBtn mt-5 pull-right" type="submit">Save &
                                            Next</button>
                                    </div>
                                </div>
                            </div><br>
                        </form>
                    </div>
                </div>

                <!-- // form2 -->
                <div class="card" id="tab2" style="display: none;">
                    <div class="row container"><br>
                        <form id="myForm2" action="{{ url('/candidate/save-applicant-detail') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.appdetails">
                            @csrf
                            <input type="hidden" name="applicant_id" id="applicant_id"
                                value="{{ $storedApplicantID }}">
                            <input type="hidden" name="stepCount" value="2" />
                            <input type="hidden" name="Post_ID" value="{{ $data['candidate_details'][0]->Post_ID }}">
                            <input type="hidden" name="app_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left"><br>
                                    <label for="fname">आवेदनकर्ता का प्रथम नाम/First Name</label><label
                                        style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->First_Name) ? $data['candidate_details'][0]->First_Name : '' }}"
                                        id="fname" class="form-control" name="First_Name"
                                        placeholder="आवेदनकर्ता का प्रथम नाम दर्ज करें" required>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left"><br>
                                    <label for="mname">मध्य नाम/Middle Name </label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->Middle_Name) ? $data['candidate_details'][0]->Middle_Name : '' }}"
                                        id="mname" class="form-control" name="Middle_Name"
                                        placeholder="आवेदनकर्ता का मध्य नाम दर्ज करें">
                                </div>
                                <div class="col-md-4 col-xs-12 text-left"><br>
                                    <label for="lname">अंतिम नाम/Last Name</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->Last_Name) ? $data['candidate_details'][0]->Last_Name : '' }}"
                                        id="lname" class="form-control" name="Last_Name"
                                        placeholder="आवेदनकर्ता का अंतिम नाम दर्ज करें" required>

                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="mothername">माता का नाम/Mother's Name</label><label
                                        style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->MotherName) ? $data['candidate_details'][0]->MotherName : '' }}"
                                        id="mothername" class="form-control" name="mothername"
                                        placeholder="माता का नाम दर्ज करें" required>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="fathername">पिता/पति का नाम/Father/Husband Name </label><label
                                        style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->FatherName) ? $data['candidate_details'][0]->FatherName : '' }}"
                                        id="fathername" class="form-control" name="fathername"
                                        placeholder="पिता/पति का नाम दर्ज करें" required>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="cities">मूल निवासी जिला/ Domicile District </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="domicile_district" id="domicile_district" required>
                                        <option selected disabled value="undefined">-- Select --</option>
                                        @if (!empty($data['cities']))
                                            @foreach ($data['cities'] as $district)
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    {{ !empty($data['candidate_details'][0]->Domicile_District_lgd) && $data['candidate_details'][0]->Domicile_District_lgd == $district->District_Code_LGD ? 'selected' : '' }}>
                                                    {{ $district->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                            </div><br>

                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="paddr">वर्तमान पता/Current Address </label><label
                                        style="color:red">*</label>
                                    <textarea rows="5" id="caddr" class="form-control" name="corr_addr" style="resize: none;"
                                        placeholder="वर्तमान पता दर्ज करें">{{ isset($data['candidate_details'][0]->Corr_Address) ? $data['candidate_details'][0]->Corr_Address : '' }}</textarea>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="cities">वर्तमान निवासी जिला/Current District </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="cur_district" id="cur_district" required>
                                        <option selected disabled value="undefined">-- Select --</option>
                                        @if (!empty($data['cities']))
                                            @foreach ($data['cities'] as $district)
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    {{ !empty($data['candidate_details'][0]->Corr_District_lgd) && $data['candidate_details'][0]->Corr_District_lgd == $district->District_Code_LGD ? 'selected' : '' }}>
                                                    {{ $district->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="pincode">वर्तमान पिन कोड/Current Pincode</label><label
                                        style="color:red">*</label>
                                    <input type="number"
                                        value="{{ isset($data['candidate_details'][0]->Corr_pincode) ? $data['candidate_details'][0]->Corr_pincode : '' }}"
                                        class="form-control" id="cpincode" name="pincode" maxlength="6"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        placeholder="वर्तमान पिन कोड दर्ज करें" required>
                                    <div id="pincode-error" class="invalid-feedback">
                                        <br>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group formField">
                                    <div class="col-md-4 col-xs-12 text-left">
                                        <br>
                                        <!-- <input type="checkbox" id="same" onchange="address()" name="same"> <strong>स्थायी पता व वर्तमान पता एक है</strong> -->
                                        <input type="checkbox" id="same" name="same"> <strong>स्थायी पता व
                                            वर्तमान पता एक
                                            है?</strong>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="paddr">स्थायी पता/Permanent Address </label><label
                                        style="color:red">*</label>
                                    <textarea rows="5" id="paddr" class="form-control" name="perm_addr" style="resize: none;"
                                        placeholder="स्थायी पता दर्ज करें">{{ isset($data['candidate_details'][0]->Perm_Address) ? $data['candidate_details'][0]->Perm_Address : '' }}</textarea>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="cities">स्थायी निवासी जिला/Permanent District </label><label
                                        style="color:red">*</label>
                                    <select class="form-select" name="per_district" id="per_district" required>
                                        <option selected disabled value="undefined">-- Select --</option>
                                        @if (!empty($data['cities']))
                                            @foreach ($data['cities'] as $district)
                                                <option value="{{ $district->District_Code_LGD }}"
                                                    {{ !empty($data['candidate_details'][0]->Corr_District_lgd) && $data['candidate_details'][0]->Corr_District_lgd == $district->District_Code_LGD ? 'selected' : '' }}>
                                                    {{ $district->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option> डेटा उपलब्ध नहीं है</option>
                                        @endif
                                    </select>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12 text-left">
                                    <label for="pincode">स्थायी पिन कोड/Pincode</label><label style="color:red">*</label>
                                    <input type="number"
                                        value="{{ isset($data['candidate_details'][0]->Perm_pincode) ? $data['candidate_details'][0]->Perm_pincode : '' }}"
                                        class="form-control" id="ppincode" name="ppincode" maxlength="6"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        placeholder="स्थायी पिन कोड दर्ज करें" required>
                                    <div id="ppincode-error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                            </div>

                            <div class='row'>
                                <div class='col-md-12 col-xw-12'>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-12 text-left">
                                    <label for="nationality">राष्ट्रीयता/Nationality </label><label
                                        style="color:red">*</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality"
                                        value="Indian" required readonly>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left">
                                    <label for="dob">जन्मतिथि/Date of Birth </label><label
                                        style="color:red">*</label>
                                    {{-- <input type="date"
                                    value="{{ isset($data['candidate_details'][0]->DOB) ? $data['candidate_details'][0]->DOB : '' }}"
                                    {{ $data['user_dob'] }} id="dob" class="form-control" name="dob"
                                    max="{{ $data['maxdate'] }}" readonly required> --}}

                                    <input type="date" value="{{ $data['user_dob'] }}" id="dob"
                                        class="form-control" name="dob" max="{{ $data['maxdate'] }}" readonly>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left">
                                    <label for="mobile">मोबाइल नंबर/Mobile Number</label><label
                                        style="color:red">*</label>
                                    <input type="number" class="form-control"
                                        value="{{ session()->get('sess_mobile') }}" id="mobile" name="mobile"
                                        placeholder="मोबाइल नंबर दर्ज करें" readonly>
                                    <div id="mobile-error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left">
                                    <label for="email">ई-मेल आई. डी./Email ID</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->Email) ? $data['candidate_details'][0]->Email : '' }}"
                                        id="email" class="form-control" name="email"
                                        placeholder="ई-मेल आई.डी. दर्ज करें" required>
                                    <div id="email-error" class="invalid-feedback">
                                        Enter Valid Email ID <br>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 15px">
                                <div class="col-md-3">
                                    <label for="gender">लिंग/Gender </label><label style="color:red">*</label>&nbsp;
                                    <select id="gender" class="form-select" name="gender" required>
                                        <option selected disabled value="undefined">-- Select --</option>
                                        <option value="male"
                                            {{ isset($data['candidate_details'][0]->Gender) && $data['candidate_details'][0]->Gender == 'male' ? 'selected' : '' }}>
                                            पुरुष (Male)
                                        </option>
                                        <option value="female"
                                            {{ isset($data['candidate_details'][0]->Gender) && $data['candidate_details'][0]->Gender == 'female' ? 'selected' : '' }}>
                                            महिला
                                            (Female)</option>
                                        <option value="other"
                                            {{ isset($data['candidate_details'][0]->Gender) && $data['candidate_details'][0]->Gender == 'other' ? 'selected' : '' }}>
                                            अन्य (Other)
                                        </option>
                                    </select>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3  text-left">
                                    <label for="caste">वर्ग/Category</label><label style="color:red">*</label>&nbsp;
                                    <select id="caste" class="form-select" name="caste" required>
                                        <option selected disabled value="undefined">-- Select --</option>
                                        <option value="UR"
                                            {{ isset($data['candidate_details'][0]->Caste) && $data['candidate_details'][0]->Caste == 'UR' ? 'selected' : '' }}>
                                            UR</option>
                                        <option value="OBC"
                                            {{ isset($data['candidate_details'][0]->Caste) && $data['candidate_details'][0]->Caste == 'OBC' ? 'selected' : '' }}>
                                            OBC</option>
                                        <option value="SC"
                                            {{ isset($data['candidate_details'][0]->Caste) && $data['candidate_details'][0]->Caste == 'SC' ? 'selected' : '' }}>
                                            SC</option>
                                        <option value="ST"
                                            {{ isset($data['candidate_details'][0]->Caste) && $data['candidate_details'][0]->Caste == 'ST' ? 'selected' : '' }}>
                                            ST</option>
                                    </select>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left">
                                    <label for="mobile">आधार नंबर/AADHAR Number</label><label
                                        style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->aadharno) ? $data['candidate_details'][0]->aadharno : '' }}"
                                        class="form-control" id="adhaar" name="adhaar" maxlength="12"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        placeholder="आधार नंबर दर्ज करें" required>
                                    <div id="adhaar-error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 text-left">
                                    <label for="mobile">इपिक नंबर/EPIC Number</label><label style="color:red">*</label>
                                    <input type="text"
                                        value="{{ isset($data['candidate_details'][0]->epicno) ? $data['candidate_details'][0]->epicno : '' }}"
                                        class="form-control" id="epic" name="epic"
                                        placeholder="इपिक नंबर दर्ज करें" required>
                                    <div id="error" class="invalid-feedback">
                                        This field is required<br>
                                    </div>
                                </div>
                            </div><br>
                            <button id="save-next-btn2" style="float: right;" type="submit"
                                class="btn btn-primary nextBtn btn-lg pull-right">Save & Next</button>
                        </form>
                    </div><br>
                </div>

                <!-- // form3 -->
                <div class="card" id="tab3" style="display: none;">
                    <div class="row"><br>
                        <form id="myForm3" action="{{ url('/candidate/save-education-detail') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.education">
                            @csrf
                            <input type="hidden" name="applicant_id_tab3" id="applicant_id_tab3"
                                value="{{ $storedApplicantID }}">
                            <input type="hidden" name="stepCount" value="3" />
                            <input type="hidden" name="Post_min_Qualification" value="{{ $post_min_quali_url }}"
                                id="post_min_quali_url" />
                            <input type="hidden" name="app_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                            <div class="row">
                                <div class="col-md-12">



                                    <input type="hidden" value="{{ $data['minyear'] }}" id='minyear'>
                                    <input type="hidden" value="{{ $data['maxyear'] }}" id='maxyear'>
                                    <table class="table table-responsive table-bordered" id="educationTable">
                                        <tr>
                                            <th>क्र.</th>
                                            <th style="width: 15%;">उत्तीर्ण परीक्षा</th>
                                            <th style="width: 17%;">विषय<label style="color:red">*</label></th>
                                            <th style="width: 12%;">वर्ष<label style="color:red">*</label></th>
                                            <th>प्राप्तांक<label style="color:red">*</label></th>
                                            <th>पूर्णांक<label style="color:red">*</label></th>
                                            <th>प्रतिशत</th>
                                            <th>बोर्ड/विश्वविद्यालय का नाम<label style="color:red">*</label></th>
                                        </tr>
                                        <!-- 10th -->
                                        <tr id="ssc">
                                            <td>1</td>
                                            <td><input type="text" name="ssc" class="form-control" value="10th"
                                                    readonly>
                                            </td>
                                            <td>
                                                <select class="form-select ssc_subject" name="ssc_subject">
                                                    <option value="Common">सामान्य</option>
                                                </select>
                                            </td>
                                            <td><select name="year_passing_ssc" class="form-select year_passing_ssc">
                                                    <option selected disabled value="undefined">-- चयन करें --</option>
                                                    <?php for ($i =  $data['minyear'] ; $i <= $data['maxyear']; $i++) { ?>
                                                    <option value="<?= $i ?>"
                                                        {{ !empty($data['candidate_details'][0]->Year_Passing_SSC) && $data['candidate_details'][0]->Year_Passing_SSC == $i ? 'selected' : '' }}>
                                                        <?= $i ?>
                                                    </option>
                                                    <?php }  ?>

                                                </select></td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->Marks_Obtained_10th) ? $data['candidate_details'][0]->Marks_Obtained_10th : '' }}"
                                                    name="marks_obtained_ssc" id="marks_obtained_ssc"
                                                    class="form-control" oninput="calc_ssc_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->Marks_Total_10th) ? $data['candidate_details'][0]->Marks_Total_10th : '' }}"
                                                    name="marks_total_ssc" id="marks_total_ssc" class="form-control"
                                                    oninput="calc_ssc_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->Perc_SSC) ? $data['candidate_details'][0]->Perc_SSC : '' }}"
                                                    name="perc_ssc" id="ssc_percentage" class="form-control" readonly>
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->School_Board_Name_10th) ? $data['candidate_details'][0]->School_Board_Name_10th : '' }}"
                                                    name="school_ssc" id="school_ssc" class="form-control">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 12th -->
                                        <tr id="inter" class="educationRow">
                                            <td>2</td>
                                            <td><input type="text" name="inter" class="form-control" value="12th"
                                                    readonly>
                                            </td>
                                            <td>

                                                <select class="form-select" name="inter_subject" id="inter_subject">
                                                    <option selected disabled value="undefined">-- चयन करें --</option>
                                                    @foreach ($subjects as $subject)
                                                        @if ($subject->fk_Quali_ID == 2)
                                                            <option value="{{ $subject->subject_code }}"
                                                                {{ !empty($data['candidate_details'][0]->Inter_Subject) && $data['candidate_details'][0]->Inter_Subject == $subject->subject_code ? 'selected' : '' }}>
                                                                {{ $subject->subject_name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            <td>
                                                <select name="year_passing_inter" class="form-select"
                                                    id="year_passing_inter">
                                                    <option selected disabled value="undefined">-- चयन करें --</option>
                                                    <?php for ($i = $data['minyear']; $i <= $data['maxyear']; $i++) {
                                                ?>
                                                    <option value="<?= $i ?>"
                                                        {{ !empty($data['candidate_details'][0]->Year_Passing_Inter) && $data['candidate_details'][0]->Year_Passing_Inter == $i ? 'selected' : '' }}>
                                                        <?= $i ?>
                                                    </option>
                                                    <?php }
                                                ?>

                                                </select>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->Marks_Obtained_12th) ? $data['candidate_details'][0]->Marks_Obtained_12th : '' }}"
                                                    name="marks_obtained_inter" id="marks_obtained_inter"
                                                    class="form-control" oninput="calc_inter_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->Marks_Total_12th) ? $data['candidate_details'][0]->Marks_Total_12th : '' }}"
                                                    name="marks_total_inter" id="marks_total_inter" class="form-control"
                                                    oninput="calc_inter_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->Perc_Inter) ? $data['candidate_details'][0]->Perc_Inter : '' }}"
                                                    name="perc_inter" id="perc_inter" class="form-control" readonly>
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->School_Board_Name_12th) ? $data['candidate_details'][0]->School_Board_Name_12th : '' }}"
                                                    name="school_inter" id="school_inter" class="form-control">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- UG -->
                                        <tr id="ug" class="educationRow">
                                            <td>3</td>
                                            <td><input type="text" name="ug" class="form-control"
                                                    value="UG (स्नातक)" readonly></td>
                                            <td>

                                                <select class="form-select ug_subject" name="ug_subject" id="ug">
                                                    <option value="">-- चयन करें --</option>
                                                    @foreach ($subjects as $subject)
                                                        @if ($subject->fk_Quali_ID == 3)
                                                            <option value="{{ $subject->subject_code }}"
                                                                {{ !empty($data['candidate_details'][0]->UG_Subject) && $data['candidate_details'][0]->UG_Subject == $subject->subject_code ? 'selected' : '' }}>
                                                                {{ $subject->subject_name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="year_passing_ug" class="form-select year_passing_ug">
                                                    <option selected disabled value="undefined">-- चयन करें --</option>
                                                    <?php for ($i = $data['minyear']; $i <= $data['maxyear']; $i++) {
                                                ?>
                                                    <option value="<?= $i ?>"
                                                        {{ !empty($data['candidate_details'][0]->Year_Passing_UG) && $data['candidate_details'][0]->Year_Passing_UG == $i ? 'selected' : '' }}>
                                                        <?= $i ?>
                                                    </option>
                                                    <?php }  ?>

                                                </select>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->Graduation_Obtained_Marks) ? $data['candidate_details'][0]->Graduation_Obtained_Marks : '' }}"
                                                    name="marks_obtained_ug" id="marks_obtained_ug" class="form-control"
                                                    oninput="calc_ug_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->Graduation_Total_Marks) ? $data['candidate_details'][0]->Graduation_Total_Marks : '' }}"
                                                    name="marks_total_ug" id="marks_total_ug" class="form-control"
                                                    oninput="calc_ug_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->Perc_UG) ? $data['candidate_details'][0]->Perc_UG : '' }}"
                                                    name="perc_ug" id="perc_ug" class="form-control" readonly>
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->Graduation_University) ? $data['candidate_details'][0]->Graduation_University : '' }}"
                                                    name="univ_ug" id="univ_ug" class="form-control">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- PG -->
                                        <tr id="pg" class="educationRow">
                                            <td>4</td>
                                            <td><input type="text" name="pg" class="form-control"
                                                    value="PG (स्नातकोत्तर)" readonly></td>
                                            <td>


                                                <select class="form-select" name="pg_subject" id="pg_subject">
                                                    <option value="">-- चयन करें --</option>
                                                    @foreach ($subjects as $subject)
                                                        @if ($subject->fk_Quali_ID == 4)
                                                            <option value="{{ $subject->subject_code }}"
                                                                {{ !empty($data['candidate_details'][0]->PG_Subject) && $data['candidate_details'][0]->PG_Subject == $subject->subject_code ? 'selected' : '' }}>
                                                                {{ $subject->subject_name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="year_passing_pg" id="year_passing_pg" class="form-select">
                                                    <option selected disabled value="undefined">-- चयन करें --</option>
                                                    <?php for ($i = $data['minyear']; $i <= $data['maxyear']; $i++) { ?>
                                                    <option value="<?= $i ?>"
                                                        {{ !empty($data['candidate_details'][0]->Year_Passing_PG) && $data['candidate_details'][0]->Year_Passing_PG == $i ? 'selected' : '' }}>
                                                        <?= $i ?>
                                                    </option>
                                                    <?php }  ?>
                                                </select>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->PG_Obtained_Marks) ? $data['candidate_details'][0]->PG_Obtained_Marks : '' }}"
                                                    name="marks_obtained_pg" id="marks_obtained_pg" class="form-control"
                                                    oninput="calc_pg_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="number"
                                                    value="{{ isset($data['candidate_details'][0]->PG_Total_Marks) ? $data['candidate_details'][0]->PG_Total_Marks : '' }}"
                                                    name="marks_total_pg" id="marks_total_pg" class="form-control"
                                                    oninput="calc_pg_perc()">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->Perc_PG) ? $data['candidate_details'][0]->Perc_PG : '' }}"
                                                    name="perc_pg" id="perc_pg" class="form-control" readonly>
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                            <td><input type="text"
                                                    value="{{ isset($data['candidate_details'][0]->PG_University) ? $data['candidate_details'][0]->PG_University : '' }}"
                                                    name="univ_pg" id="univ_pg" class="form-control" id="pg4">
                                                <div id="error" class="invalid-feedback">
                                                    This field is required<br>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <button id="save-next-btn3" style="float: right;margin-right: 10px;"
                                        class="btn btn-primary nextBtn btn-lg pull-right" type="submit">Save &
                                        Next</button>
                                </div>
                            </div>
                        </form>
                    </div><br>
                </div>

                <!-- // form4  -->
                <div class="card" id="tab4" style="display: none;">
                    <div class="row container"><br>
                        <form id="myForm4" action="{{ url('/candidate/save-experience-detail') }}" method="post"
                            enctype="multipart/form-data" data-storage-key="application.experience">
                            @csrf
                            <input type="hidden" name="applicant_id_tab4" id="applicant_id_tab4"
                                value="{{ $storedApplicantID }}">
                            <input type="hidden" name="stepCount" value="4" />
                            <input type="hidden" name="app_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                            <div class="col-md-12"><br>
                                <div class="field_wrapper" id="inputFieldsContainer">
                                    @if (isset($data['candidate_details'][0]->experience_details))
                                        @if ($data['candidate_details'][0]->experience_details)
                                            @foreach ($data['candidate_details'][0]->experience_details as $experience_detail)
                                                <div class="row">
                                                    <div class="row">
                                                        <div class="col-md-6 text-left">
                                                            <label for="orgname">संस्था का नाम व पूरा पता<font
                                                                    color="red">*</font>
                                                            </label>
                                                            <input type="text"
                                                                value="{{ $experience_detail->Organization_Name }}"
                                                                id="orgname" class="form-control" name="org_name[]"
                                                                placeholder="संस्था का नाम/पता दर्ज करें" required>
                                                            <div id="error" class="invalid-feedback">
                                                                This field is required<br>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 text-left">
                                                            <label for="orgtype">संस्था शासकीय है अथवा अशासकीय<font
                                                                    color="red">*</font>
                                                            </label>
                                                            <select id="orgtype" class="form-select" name="org_type[]">
                                                                <option
                                                                    {{ $experience_detail->Organization_Type == 'Govt' ? 'selected' : '' }}
                                                                    value="Govt">शासकीय</option>
                                                                <option
                                                                    {{ $experience_detail->Organization_Type == 'NGO' ? 'selected' : '' }}
                                                                    value="NGO">अशासकीय(NGO)</option>
                                                                <option
                                                                    {{ $experience_detail->Organization_Type == 'SemiGovt' ? 'selected' : '' }}
                                                                    value="SemiGovt">अर्धशासकीय</option>
                                                            </select>
                                                            <div id="error" class="invalid-feedback">
                                                                This field is required<br>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 text-left">
                                                            <label for="ngono">यदि संस्था अशासकीय है तो भारत शासन के NGO
                                                                पोर्टल में पंजीयन क्र.</label>
                                                            <input value="{{ $experience_detail->NGO_No }}"
                                                                type="text" id="ngono" class="form-control"
                                                                name="ngo_no[]"
                                                                placeholder=" NGO पोर्टल में पंजीयन क्र दर्ज करें">
                                                        </div>
                                                        <div class="col-md-3 text-left">
                                                            <label for="desgname">पदनाम</label><label
                                                                style="color:red">*</label>
                                                            <input value="{{ $experience_detail->Designation }}"
                                                                type="text" id="desgname" class="form-control"
                                                                name="desg_name[]" placeholder="पदनाम दर्ज करें">
                                                            <div id="error" class="invalid-feedback">
                                                                This field is required<br>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 text-left">
                                                            <label for="nature">अनुभव का कार्यक्षेत्र</label><label
                                                                style="color:red">*</label>
                                                            <input value="{{ $experience_detail->Nature_Of_Work }}"
                                                                id="nature" type="text" class="form-control"
                                                                name="nature_work[]"
                                                                placeholder="अनुभव कार्यक्षेत्र दर्ज करें">
                                                            <div id="error" class="invalid-feedback">
                                                                This field is required<br>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 text-left">
                                                            <label for="dfrom">कब से</label><label
                                                                style="color:red">*</label>
                                                            <input value="{{ $experience_detail->Date_From }}"
                                                                type="date" id="dfrom"
                                                                max="{{ $data['maxdate'] }}" class="form-control"
                                                                name="date_from[]">
                                                            <div id="error" class="invalid-feedback">
                                                                This field is required<br>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 text-left">
                                                            <label for="dto">कब तक</label><label
                                                                style="color:red">*</label>
                                                            <input value="{{ $experience_detail->Date_To }}"
                                                                type="date" id="dto" class="form-control"
                                                                name="date_to[]" max="{{ $data['maxdate'] }}">
                                                            <div id="error" class="invalid-feedback">
                                                                This field is required<br>
                                                            </div>
                                                        </div>
                                                    </div>
                                            @endforeach
                                        @endif
                                    @else
                                        <div class="row">
                                            <div class="row">
                                                <div class="col-md-6 text-left">
                                                    <label for="orgname">संस्था का नाम व पूरा पता<font color="red">*
                                                        </font>
                                                    </label>
                                                    <input type="text" id="orgname" class="form-control"
                                                        name="org_name[]" placeholder="संस्था का नाम/पता दर्ज करें">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-3 text-left">
                                                    <label for="orgtype">संस्था शासकीय है अथवा अशासकीय<font
                                                            color="red">*
                                                        </font>
                                                    </label>
                                                    <select id="orgtype" class="form-select" name="org_type[]">
                                                        <option value="Govt">शासकीय</option>
                                                        <option value="NGO">अशासकीय(NGO)</option>
                                                        <option value="SemiGovt">अर्धशासकीय</option>
                                                    </select>
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-left">
                                                    <label for="ngono">यदि संस्था अशासकीय है तो भारत शासन के NGO पोर्टल
                                                        में पंजीयन क्र.</label>
                                                    <input type="text" id="ngono" class="form-control"
                                                        name="ngo_no[]"
                                                        placeholder=" NGO पोर्टल में पंजीयन क्र दर्ज करें">
                                                </div> --}}

                                                <div class="col-md-3 text-left">
                                                    <label for="orgtype">संस्था शासकीय है अथवा अशासकीय<font
                                                            color="red">*</font></label>
                                                    <select id="orgtype" class="form-select" name="org_type[]">
                                                        <option value="Govt">शासकीय</option>
                                                        <option value="NGO">अशासकीय(NGO)</option>
                                                        <option value="SemiGovt">अर्धशासकीय</option>
                                                    </select>
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>

                                                <!-- Initially Hidden NGO Registration Field -->
                                                <div class="col-md-3 text-left" id="ngoDiv" style="display: none;">
                                                    <label for="ngono">यदि संस्था अशासकीय है तो भारत शासन के NGO पोर्टल
                                                        में पंजीयन क्र.</label>
                                                    <input type="text" id="ngono" class="form-control"
                                                        name="ngo_no[]" placeholder="NGO पोर्टल में पंजीयन क्र दर्ज करें">
                                                </div>


                                                <div class="col-md-3 text-left">
                                                    <label for="desgname">पदनाम</label><label style="color:red">*</label>
                                                    <input type="text" id="desgname" class="form-control"
                                                        name="desg_name[]" placeholder="पदनाम दर्ज करें">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left">
                                                    <label for="nature">अनुभव का कार्यक्षेत्र</label><label
                                                        style="color:red">*</label>
                                                    <input id="nature" type="text" class="form-control"
                                                        name="nature_work[]" placeholder="अनुभव कार्यक्षेत्र दर्ज करें">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left">
                                                    <label for="dfrom">कब से</label><label style="color:red">*</label>
                                                    <input type="date" id="dfrom" class="form-control"
                                                        name="date_from[]" max="{{ $data['maxdate'] }}">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                                <div class="col-md-3  text-left">
                                                    <label for="dto">कब तक</label><label style="color:red">*</label>
                                                    <input type="date" id="dto" class="form-control"
                                                        name="date_to[]" max="{{ $data['maxdate'] }}">
                                                    <div id="error" class="invalid-feedback">
                                                        This field is required<br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4"><br>
                                    <div class="d-flex">
                                        <button type="button" id="addmoreBtn" class="btn btn-info">Add
                                            More</button>&nbsp;&nbsp;&nbsp;
                                        <button style="display: none;" type="button"
                                            class="btn btn-danger removeBtn">Remove</button>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12 text-left">
                                        <label for="marital">क्या आप विवाहित हैं ? / Marital Status </label><label
                                            style="color:red">*</label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->Marital_Status) && $data['candidate_details'][0]->Marital_Status == 'married' ? 'checked' : '' }}
                                                id="marital" name="marital" value="married"> Married
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->Marital_Status) && $data['candidate_details'][0]->Marital_Status == 'unmarried' ? 'checked' : '' }}
                                                id="marital" name="marital" value="unmarried"> UnMarried
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->Marital_Status) && $data['candidate_details'][0]->Marital_Status == 'divorced' ? 'checked' : '' }}
                                                id="marital" name="marital" value="divorced"> Divorced
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->Marital_Status) && $data['candidate_details'][0]->Marital_Status == 'widow' ? 'checked' : '' }}
                                                id="marital" name="marital" value="widow"> Widow
                                        </label>
                                        <div id="error" class="invalid-feedback">
                                            This field is required<br>
                                        </div>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12 text-left">
                                        <label for="domicile">क्या आप छत्तीसगढ़ राज्य के मूल निवासी है ? / CG State
                                            Domicile</label><label style="color:red">*</label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                            <input class="pntr"
                                                {{ !empty($data['candidate_details'][0]->domicile) && $data['candidate_details'][0]->domicile == 'Yes' ? 'checked' : '' }}
                                                type="radio" id="prof" name="domicile" value="Yes"
                                                class="pntr @error('domicile') is-invalid @enderror">&nbsp;Yes
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input class="pntr"
                                                {{ !empty($data['candidate_details'][0]->domicile) && $data['candidate_details'][0]->domicile == 'No' ? 'checked' : '' }}
                                                type="radio" id="prof" name="domicile" value="No"
                                                class="pntr @error('domicile') is-invalid @enderror">&nbsp;No
                                        </label>
                                        <div id="error1" style="display: none;color:red;">
                                            This field is required
                                        </div>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12 text-left">
                                        <label for="girlchild">क्या आप केवल एक या दो बच्चियों की माता है एवं आपने नसबंदी
                                            करवा लिया हैं ?/ Are you Mother of one or two girl child & have you done family
                                            planning?</label><label style="color:red">*</label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->girlchild) && $data['candidate_details'][0]->girlchild == 'Yes' ? 'checked' : '' }}
                                                id="girlchild" name="girlchild" value="Yes">&nbsp;Yes
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->girlchild) && $data['candidate_details'][0]->girlchild == 'No' ? 'checked' : '' }}
                                                id="girlchild" name="girlchild" value="No">&nbsp;No
                                        </label>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12 text-left">
                                        <label for="below_pl">क्या आप बी पी एल परिवार के अंतर्गत आते है ?/ Does your family
                                            belongs under Below poverty line?</label><label style="color:red">*</label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->belowpl) && $data['candidate_details'][0]->belowpl == 'Yes' ? 'checked' : '' }}
                                                id="below_pl" name="below_pl" value="Yes">&nbsp;Yes
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->belowpl) && $data['candidate_details'][0]->belowpl == 'No' ? 'checked' : '' }}
                                                id="below_pl" name="below_pl" value="No">&nbsp;No
                                        </label>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12 text-left">
                                        <label for="ecci">क्या आपके पास ईसीसीई/ न्युट्रिशन/ मनोविज्ञान में
                                            डिग्री/डिप्लोमा
                                            है?/ Do you have Degree/Diploma in ECCI/Nutrition/Psychology?</label><label
                                            style="color:red">*</label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->ecci) && $data['candidate_details'][0]->ecci == 'Yes' ? 'checked' : '' }}
                                                id="ecci" name="ecci" value="Yes">&nbsp;Yes
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->ecci) && $data['candidate_details'][0]->ecci == 'No' ? 'checked' : '' }}
                                                id="ecci" name="ecci" value="No">&nbsp;No
                                        </label>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12 text-left">
                                        <label for="ncc">क्या आपके पास NCC/NSS/Scout Guide की सर्टिफिकेट है ?/ Do you
                                            have
                                            certificate of NCC/NSS/Scout Guide?</label><label style="color:red">*</label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->ncc) && $data['candidate_details'][0]->ncc == 'Yes' ? 'checked' : '' }}
                                                id="ncc" name="ncc" value="Yes">&nbsp;Yes
                                        </label>
                                        <label class="radio-inline" style="margin-top: -10px; margin-left: 20px;">
                                            <input type="radio"
                                                {{ !empty($data['candidate_details'][0]->ncc) && $data['candidate_details'][0]->ncc == 'No' ? 'checked' : '' }}
                                                id="ncc" name="ncc" value="No">&nbsp;No
                                        </label>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-12 col-xw-12'>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 col-xs-12 text-left">
                                        <label for="special">विशेष योग्यता</label>
                                        <textarea name="special" class="form-control" placeholder="विशेष योग्यता दर्ज करें">{{ isset($data['candidate_details'][0]->speciality) ? $data['candidate_details'][0]->speciality : '' }}</textarea>
                                    </div>
                                </div><br>

                                <button id="save-next-btn4" style="float: right;"
                                    class="btn btn-primary btn-lg pull-right" type="submit">Save & Next</button>
                            </div>
                        </form>
                    </div><br>
                </div>

                <!-- // form5 -->
                <div class="card" id="tab5" style="display: none;">
                    <div class="row container"><br>
                        <form id="myForm5" action="{{ url('/candidate/save-documents') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="applicant_id_tab5" id="applicant_id_tab5"
                                value="{{ $storedApplicantID }}">
                            <input type="hidden" name="stepCount" value="5" />
                            <input type="hidden" name="Post_min_Quali" id="Post_min_Quali2" />
                            <input type="hidden" name="app_id"
                                value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                            <div class="col-md-12"><br>
                                <div class="field_wrapper" id="inputFieldsContainer">
                                    <div class="row">
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="photo">पासपोर्ट साइज़ फोटो अपलोड करें / Upload Photo <font
                                                    color="red">*
                                                </font></label>
                                            <input id="photo" type="file" class="form-control"
                                                name="document_photo"
                                                accept="image/png, image/jpeg, image/jpg, image/webp">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Photo)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Photo) }}"
                                                    id="view_file" target="_blank">View File</a>
                                                <input type="hidden" name="exist_document_photo"
                                                    value="{{ $data['applicant_details']->Document_Photo }}">
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">हस्ताक्षर अपलोड करें / Upload Signature <font
                                                    color="red">*
                                                </font></label>
                                            <input type="file" id="document_sign" class="form-control"
                                                name="document_sign"
                                                accept="image/png, image/jpeg, image/jpg, image/webp">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Sign)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Sign) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">आधार अपलोड करें / Upload AADHAAR <font color="red">*
                                                </font>
                                            </label>
                                            <input type="file" id="document_adhaar" class="form-control"
                                                name="document_adhaar" accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Aadhar)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Aadhar) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="photo">जाति प्रमाण पत्र अपलोड करें / Upload Caste Certificate
                                                <font color="red">*</font>
                                            </label>
                                            <input id="photo" type="file" class="form-control"
                                                name="caste_certificate" accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Caste)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Caste) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">स्थानीय निवास प्रमाण पत्र अपलोड करें / Upload Domicile
                                                <font color="red">*
                                                </font></label>
                                            <input type="file" class="form-control" name="domicile"
                                                accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Domicile)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Domicile) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">10th प्रमाण पत्र अपलोड करें / Upload 10th Marksheet <font
                                                    color="red">
                                                    *</font></label>
                                            <input type="file" id="ssc_marksheet" class="form-control"
                                                name="ssc_marksheet" accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_SSC)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_SSC) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="photo">12th प्रमाण पत्र अपलोड करें / Upload 12th Marksheet
                                                <font color="red">*</font>
                                            </label>
                                            <input id="photo" type="file" class="form-control"
                                                name="inter_marksheet" accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Inter)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Inter) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">UG प्रमाण पत्र अपलोड करें / Upload UG Marksheet</label>
                                            <input type="file" class="form-control" name="ug_marksheet"
                                                accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_UG)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_UG) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">PG प्रमाण पत्र अपलोड करें / Upload PG Marksheet </label>
                                            <input type="file" class="form-control" name="pg_marksheet">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_PG)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_PG) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>

                                    </div><br>
                                    <div class="row">

                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">अनुभव प्रमाण पत्र अपलोड करें / Upload Experience
                                                Certificate
                                                <font color="red">*</font>
                                            </label>
                                            <input type="file" id="sign" class="form-control"
                                                name="exp_document" accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Exp)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Exp) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="sign">BPL प्रमाण पत्र अपलोड करें / Upload BPL
                                                Certificate</label>
                                            <input type="file" class="form-control" name="bpl_marksheet"
                                                accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_BPL)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_BPL) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-12 text-left">
                                            <label for="photo">विधवा/परित्यक्ता/तलाकशुदा का प्रमाण पत्र अपलोड करें /
                                                Upload
                                                Widow/Divorced Certificate </label>
                                            <input id="photo" type="file" class="form-control"
                                                name="widow_certificate" accept="application/pdf">
                                            @if (isset($data['applicant_details']) && $data['applicant_details']->Document_Widow)
                                                <a class="myImg"
                                                    href="{{ url('assets/user/applicant/doc/' . $data['applicant_details']->Applicant_ID . '/' . $data['applicant_details']->Document_Widow) }}"
                                                    id="view_file" target="_blank">View File</a>
                                            @endif
                                            <div id="error" class="invalid-feedback">
                                                This field is required<br>
                                            </div>
                                        </div>

                                    </div><br>
                                </div>
                                <button style="float: right;" class="btn btn-primary btn-lg pull-right"
                                    type="submit">Submit</button>
                            </div>
                        </form>
                    </div><br>
                </div>
            </div>
        </div>
    </main>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.18.0/js/md5.min.js"></script>
    <script>
        $(document).ready(function() {
            select_districts();
        });

        document.getElementById("orgtype").addEventListener("change", function() {
            var selectedValue = this.value;
            var ngoDiv = document.getElementById("ngoDiv");

            if (selectedValue === "NGO") {
                ngoDiv.style.display = "block";
            } else {
                ngoDiv.style.display = "none";
            }
        });



        function select_districts() {
            let post_id = $("#postname").val();
            $.ajax({
                url: '/candidate/get-district',
                type: "POST",
                data: {
                    post_id: post_id,
                    '_token': '{{ csrf_token() }}'
                },
                cache: false,
                success: function(response) {
                    let option = '<option selected disabled value="undefined">--चुनें--</option>';

                    if (response && response.length > 0) {
                        $.each(response, function(index, value) {
                            let selected = "";
                            if ({{ $data['candidate_details'][0]->Pref_Districts }} == value
                                .District_Code_LGD) selected = "selected";
                            option +=
                                `<option value="${value.District_Code_LGD}"  ${selected}> ${value.name}</option>`;
                        });
                    } else {
                        option += '<option value="">No Districts Found</option>';
                    }

                    $('#district').html(option);
                },
                error: function(xhr) {
                    $('#district').empty();
                    $('#district').append('<option selected hidden value=""> --Select-- </option>');
                    $('#district').append('<option value="">No Data Found</option>');
                }
            });
        }



        var modal = document.getElementById("myModal");

        // Get all anchor elements with the class "myImg"
        var anchors = document.querySelectorAll(".myImg");
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");

        // Loop through each anchor element and add an event listener
        anchors.forEach(function(anchor) {
            anchor.addEventListener("click", function(event) {
                event.preventDefault(); // Prevent the default behavior of the anchor
                modal.style.display = "block";
                modalImg.src = this.href; // Use the href attribute as the source
                captionText.innerHTML = this.textContent; // Use the link text as the caption
            });
        });

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        document.getElementById('dfrom').addEventListener('change', function() {
            document.getElementById('dto').value = "";
            var issuedDate = new Date(this.value);
            issuedDate.setDate(issuedDate.getDate() + 1); // Increment the date by one day
            var formattedDate = issuedDate.toISOString().split('T')[0]; // Format the date to YYYY-MM-DD
            document.getElementById('dto').setAttribute('min', formattedDate);
        });

        // Initialize min value on page load if issued_date has a value
        window.addEventListener('load', function() {
            var issuedDateValue = document.getElementById('dfrom').value;
            if (issuedDateValue) {
                var issuedDate = new Date(issuedDateValue);
                issuedDate.setDate(issuedDate.getDate() + 1); // Increment the date by one day
                var formattedDate = issuedDate.toISOString().split('T')[0]; // Format the date to YYYY-MM-DD
                document.getElementById('dto').setAttribute('min', formattedDate);
            }
        });






        // ##=========== //Active Tabs Functionality ===============================##
        $(document).ready(function() {


            /// Only Required Education Field Show
            function showEducationBasedOnQualification(postMinQuali) {
                let qualificationMap = {
                    1: "10th",
                    2: "12th",
                    3: "Graduation",
                    4: "Post Graduation"
                };

                // console.log('After call fn postMinQuali= ', postMinQuali)

                let postMinQualiText = qualificationMap[postMinQuali] || "10th"; // Default to 10th

                $(".educationRow").hide(); // Hide all rows initially
                $("#ssc").show(); // Always show SSC (10th)

                let educationLevels = [];
                let maxClicks = 0;

                if (postMinQualiText === "10th") {
                    educationLevels = ["inter", "ug", "pg"];
                    maxClicks = 3;
                } else if (postMinQualiText === "12th") {
                    $("#inter").show();
                    educationLevels = ["ug", "pg"];
                    maxClicks = 2;
                } else if (postMinQualiText === "Graduation") {
                    $("#inter, #ug").show();
                    educationLevels = ["pg"];
                    maxClicks = 1;
                } else if (postMinQualiText === "Post Graduation") {
                    $("#inter, #ug, #pg").show();
                    maxClicks = 0;
                }

                // Add button logic
                setupAddMoreButton(maxClicks, educationLevels);
            }

            function setupAddMoreButton(maxClicks, educationLevels) {
                let clickCount = 0;

                $("#addMoreTd").remove(); // Remove any existing button row

                if (maxClicks > 0) {
                    $("#educationTable").append(`
                                        <tr id="addMoreTd">
                                            <td colspan="4" class="text-start">
                                                <button type="button" id="addEduBtn" class="text-start bg-secondary btn btn-sm text-white ms-3 ml-3">Add More Qualification</button>
                                            </td>
                                        </tr>
                                    `);
                }

                $("#addEduBtn").click(function() {
                    if (clickCount < educationLevels.length) {
                        $("#" + educationLevels[clickCount]).show();
                        clickCount++;

                        // Button should only disappear when all options are added
                        if (clickCount >= educationLevels.length) {
                            $("#addMoreTd").remove();
                        }
                    }
                });
            }



            var postMinQualify = $("#post_min_quali_url").val(); // Value le rahe hain

            if (postMinQualify.trim() !== "") {
                console.log("Page Load: Qualification Value Found =", postMinQualify);
                showEducationBasedOnQualification(postMinQualify);
            } else {
                console.log("Page Load: No Qualification Value Found, function will be called on form submission.");
            }



            // ##=========== //Active Tabs Functionality ===============================##

            function setActiveTab(activeTabNumber) {
                for (let i = 1; i <= 5; i++) {
                    const tab = document.querySelector(`.class${i}`);
                    if (tab) {
                        if (i <= activeTabNumber) {
                            tab.classList.add("enable");
                            tab.removeAttribute("disabled");
                        } else {
                            tab.classList.remove("enable");
                            tab.setAttribute("disabled", "true");
                        }
                    }
                }

                const activeTab = document.querySelector(`.class${activeTabNumber}`);
                if (activeTab) {
                    activeTab.classList.add("active");
                }
            }

            function removeGreenBackground() {
                const buttons = document.getElementsByClassName("add-app-btn");
                for (let i = 0; i < buttons.length; i++) {
                    buttons[i].style.backgroundColor = "#0d6efd"; // Default blue
                }
            }

            function hideAllDivs() {
                const tabs = ["tab1", "tab2", "tab3", "tab4", "tab5"];
                tabs.forEach(tab => {
                    const element = document.getElementById(tab);
                    if (element) element.style.display = "none";
                });
            }

            // Get active tab from dataset or localStorage, default to 1 if invalid
            let activeTabNumber = parseInt(document.querySelector(".act")?.dataset.activeTab) || parseInt(
                localStorage.getItem("activeTab")) || 1;
            console.log('activeTabNumber==', activeTabNumber);
            setActiveTab(activeTabNumber);

            // Enable tabs up to activeTabNumber
            document.querySelectorAll(".add-app-btn").forEach((btn, index) => {
                if (index + 1 <= activeTabNumber) {
                    btn.removeAttribute("disabled");
                }
            });

            // Tab click event
            document.querySelectorAll(".add-app-btn").forEach((btn, index) => {
                btn.addEventListener("click", function() {
                    const newActiveTab = index + 1;
                    removeGreenBackground();
                    this.style.backgroundColor = "#157347"; // Highlight active tab
                    hideAllDivs();
                    const tabElement = document.getElementById(`tab${newActiveTab}`);
                    if (tabElement) {
                        tabElement.style.display = "block";
                    }
                    // Update localStorage only if new tab is higher
                    if (newActiveTab > activeTabNumber) {
                        activeTabNumber = newActiveTab;
                        localStorage.setItem("activeTab", activeTabNumber);
                    }
                });
            });




            //########### Save & Next Button Logic (safely handled)############
            const saveNextBtn1 = document.getElementById("save-next-btn1");
            if (saveNextBtn1) {
                saveNextBtn1.addEventListener("click", function() {
                    hideAllDivs();
                    const tab2 = document.getElementById("tab2");
                    if (tab2) tab2.style.display = "block";
                    const personalDetailsBtn = document.getElementById("personal-details-btn");
                    if (personalDetailsBtn) {
                        personalDetailsBtn.removeAttribute("disabled");
                        removeGreenBackground();
                        personalDetailsBtn.style.backgroundColor = "#157347";
                    }
                    if (activeTabNumber < 2) {
                        activeTabNumber = 2;
                        localStorage.setItem("activeTab", activeTabNumber);
                    }
                });
            } else {
                console.warn("Element with ID 'save-next-btn1' not found.");
            }

            const saveNextBtn2 = document.getElementById("save-next-btn2");
            if (saveNextBtn2) {
                saveNextBtn2.addEventListener("click", function() {
                    hideAllDivs();
                    const tab3 = document.getElementById("tab3");
                    if (tab3) tab3.style.display = "block";
                    const eduInfoBtn = document.getElementById("edu-info-btn");
                    if (eduInfoBtn) {
                        eduInfoBtn.removeAttribute("disabled");
                        removeGreenBackground();
                        eduInfoBtn.style.backgroundColor = "#21bf06";
                    }
                    if (activeTabNumber < 3) {
                        activeTabNumber = 3;
                        localStorage.setItem("activeTab", activeTabNumber);
                    }
                });
            } else {
                console.warn("Element with ID 'save-next-btn2' not found.");
            }

            // Save & Next Button 3 Logic
            const saveNextBtn3 = document.getElementById("save-next-btn3");
            if (saveNextBtn3) {
                saveNextBtn3.addEventListener("click", function() {
                    hideAllDivs();
                    const tab4 = document.getElementById("tab4");
                    if (tab4) tab4.style.display = "block";
                    const expInfoBtn = document.getElementById("exp-info-btn");
                    if (expInfoBtn) {
                        expInfoBtn.removeAttribute("disabled");
                        removeGreenBackground();
                        expInfoBtn.style.backgroundColor =
                            "#157347"; // Consistent green color (adjust as needed)
                    }
                    if (activeTabNumber < 4) {
                        activeTabNumber = 4;
                        localStorage.setItem("activeTab", activeTabNumber);
                    }
                });
            } else {
                console.warn("Element with ID 'save-next-btn3' not found.");
            }

            // Save & Next Button 4 Logic
            const saveNextBtn4 = document.getElementById("save-next-btn4");
            if (saveNextBtn4) {
                saveNextBtn4.addEventListener("click", function() {
                    hideAllDivs();
                    const tab5 = document.getElementById("tab5");
                    if (tab5) tab5.style.display = "block";
                    const attachmentBtn = document.getElementById("attachment-btn");
                    if (attachmentBtn) {
                        attachmentBtn.removeAttribute("disabled");
                        removeGreenBackground();
                        attachmentBtn.style.backgroundColor =
                            "#157347"; // Consistent green color (adjust as needed)
                    }
                    if (activeTabNumber < 5) {
                        activeTabNumber = 5;
                        localStorage.setItem("activeTab", activeTabNumber);
                    }
                });
            } else {
                console.warn("Element with ID 'save-next-btn4' not found.");
            }




            //=== Display the correct tab on page load====
            hideAllDivs();
            const activeTabElement = document.getElementById(`tab${activeTabNumber}`);
            if (activeTabElement) {
                activeTabElement.style.display = "block";
                removeGreenBackground();
                const activeBtn = document.querySelector(`.class${activeTabNumber}`);
                if (activeBtn) activeBtn.style.backgroundColor = "#157347";
            } else {
                console.warn(`Tab with ID 'tab${activeTabNumber}' not found.`);
            }


            // ##==========================================##

            //============ ## Save Local Storage of myForms Data ##===========
            // ===============##### form1  #####==========================
            $('#myForm1').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally
                // $('#postname').prop('disabled', false); // Enable the select field
                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');
                // var storageKey = $(this).data('storage-key') || 'application.post';

                // Submit the form using AJAX
                $.ajax({
                    url: "{{ url('/candidate/save-post') }}",
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    context: this,
                    success: function(data) {

                        console.log('Server response:', data);
                        if (data.status == 'success' || data.status == 'complete') {

                            // var applicant_id = data.applicant_id;
                            var applicant_id = data.applicant_id;
                            document.getElementById("applicant_id").value = applicant_id;
                            document.getElementById("tab2").style.display = "block";
                            document.getElementById("personal-details-btn").focus();

                            document.getElementById("personal-details-btn").removeAttribute(
                                "disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("personal-details-btn").style
                                .backgroundColor = "#21bf06";
                            // });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! <br> (Please Fill All Required Field) ',
                            text: 'Please see fields marked in red....',
                            allowOutsideClick: false
                        });

                        var response = JSON.parse(xhr.responseText);
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


            // ===============##### form2  #####==========================
            $('#myForm2').submit(function(e) {
                e.preventDefault();
                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');
                // var storageKey = $(this).data('storage-key') || 'application.appdetails';

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

                        // console.log('Server response (Form 2):', data);
                        if (data.status == 'success') {


                            var applicant_id = data.applicant_id;
                            document.getElementById("applicant_id_tab3").value = applicant_id;
                            document.getElementById("post_min_quali_url").value = data
                                .Post_min_Quali;
                            document.getElementById("Post_min_Quali2").value = data
                                .Post_min_Quali;
                            $Post_min_Qualification = data.Post_min_Quali;

                            // Call the function to show qualifications
                            showEducationBasedOnQualification($Post_min_Qualification);



                            // hideAllDivs();
                            document.getElementById("tab3").style.display = "block";
                            document.getElementById("edu-info-btn").focus();

                            document.getElementById("edu-info-btn").removeAttribute("disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("edu-info-btn").style.backgroundColor =
                                "#21bf06";
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! <br> (Please Fill All Required Field) ',
                            text: 'Please see fields marked in red....',
                            allowOutsideClick: false
                        });

                        var response = JSON.parse(xhr.responseText);
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
                            $('#email-error').html("Enter Valid Email ID");
                            $('#ppincode-error').html("Enter Valid 6 Digit Pincode");
                            $('#pincode-error').html("Enter Valid 6 Digit Pincode");
                            $('#mobile-error').html("Enter Valid 10 Digit Mobile No.");
                            $('#adhaar-error').html("Enter Valid 12 Digit Adhaar No.");

                        }
                    }
                });
            });


            // ===============##### form3  #####==========================
            $('#myForm3').submit(function(e) {
                e.preventDefault();
                var form = new FormData(this);
                var url = $(this).attr('action');
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                form.append('_token', '{{ csrf_token() }}');
                // var storageKey = $(this).data('storage-key') || 'application.education'; // Fallback to default key


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
                        console.log('Server response:', data);
                        if (data.status == 'success') {



                            var applicant_id = data.applicant_id;
                            document.getElementById("applicant_id_tab4").value = applicant_id;

                            // hideAllDivs();
                            document.getElementById("tab4").style.display = "block";
                            document.getElementById("exp-info-btn").focus();

                            document.getElementById("exp-info-btn").removeAttribute("disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("exp-info-btn").style.backgroundColor =
                                "#21bf06";
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! <br> (Please Fill All Required Field) ',
                            text: 'Please see fields marked in red....',
                            allowOutsideClick: false
                        });

                        var response = JSON.parse(xhr.responseText);
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


            // ===============##### form4  #####==========================
            $('#myForm4').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally
                var form = new FormData(this);
                var url = $(this).attr('action'); // Get the form action URL
                var csrf_token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token value
                form.append('_token', '{{ csrf_token() }}');
                // var storageKey = $(this).data('storage-key') || 'application.experience'; // Fallback to default key


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
                            var applicant_id = data.applicant_id;
                            document.getElementById("applicant_id_tab5").value = applicant_id;


                            // hideAllDivs();
                            document.getElementById("tab5").style.display = "block";
                            document.getElementById("attachmnet-btn").focus();

                            document.getElementById("attachmnet-btn").removeAttribute(
                                "disabled");
                            removeGreenBackground(); // Remove green background from all buttons

                            document.getElementById("attachmnet-btn").style.backgroundColor =
                                "#21bf06";
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! <br> (Please Fill All Required Field) ',
                            html: 'Please see fields marked <span style="color:red">*</span> ....',
                            allowOutsideClick: false
                        });

                        var response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            var errors = response.errors;
                            var errorMessages = '';

                            $.each(errors, function(key, value) {
                                errorMessages += value[0] + '<br>';
                                var element = $('[name="' + key + '"]');
                                if (element.length) {
                                    element.addClass('is-invalid');
                                    element.closest('.form-group').find(
                                        '.invalid-feedback').html(value[0]);
                                }
                                //  if(key === 'domicile') {
                                //     document.getElementById("error1").style.display = "block";
                                //  }

                            });
                            $('#error').html("This field is required");

                            // $('input[name="domicile"]').on('change', function() {
                            //     $('#error').hide();
                            //     $('input[name="domicile"]').removeClass('is-invalid');
                            // });
                        }
                    }
                });
            });


            // ===============##### form5  #####==========================
            $('#myForm5').submit(function(e) {
                e.preventDefault(); // Prevent form from submitting normally
                var form = new FormData(this);
                var url = $(this).attr('action'); // Get the form action URL
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
                        // Handle the response from the server
                        // console.log(data);
                        if (data.status == 'success') {

                            var applicant_id = data.applicant_id;
                            document.getElementById("applicant_id").value = applicant_id;

                            Swal.fire({
                                icon: 'success',
                                title: data['message'],
                                // text: 'आवेदन की स्थिति को ट्रैक करने के लिए इस आईडी का उपयोग करें',
                                allowOutsideClick: false
                            }).then((result) => {
                                window.location.href =
                                    '/candidate/view-application-detail/' + md5(
                                        applicant_id);
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: data['title'],
                                text: data['message'],
                                allowOutsideClick: false
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया सभी आवश्यक फ़ील्ड भरें ! <br> (Please Fill All Required Field) ',
                            text: 'Please see fields marked in red....',
                            allowOutsideClick: false
                        });

                        var response = JSON.parse(xhr.responseText);
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
        });


        // ##=========== //Add More Inputs Functionality ===============================##

        //to add and remove class from input feild
        $('form input').keyup(function() {
            if (this.value) {
                var element = $(this);

                // Remove the is-invalid class from input elements
                element.removeClass('is-invalid');
                element.addClass('was-validated');
            } else {
                var element = $(this);
                var elementName = element.attr('name');

                if (elementName != "Middle_Name" && elementName != "Last_Name" && elementName != "email" &&
                    elementName != "perc_ssc" && elementName != "perc_inter" && elementName != "perc_pg" &&
                    elementName != "perc_ug" && elementName != "pond_name" && elementName != "khasra_no" &&
                    elementName != "rakba" && elementName != "amount")
                    if (element.length) {
                        // Remove the is-invalid class from input elements
                        element.removeClass('was-validated');
                        element.addClass('is-invalid');
                    }
            }
        });

        //to add and remove class from textarea
        $('form textarea').keyup(function() {
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

        $('select[name=postname]').change(function() {
            let post_id = $("#postname").val();
            $.ajax({
                url: '/candidate/get-district',
                type: "POST",
                data: {
                    post_id: post_id,
                    '_token': '{{ csrf_token() }}'
                },
                cache: false,
                success: function(response) {
                    let option = '<option selected disabled value="undefined">--चुनें--</option>';

                    if (response && response.length > 0) {
                        $.each(response, function(index, value) {
                            option += '<option value="' + value.District_Code_LGD + '">' + value
                                .name + '</option>';
                        });
                    } else {
                        option += '<option value="">No Districts Found</option>';
                    }

                    $('#district').html(option);
                },
                error: function(xhr) {
                    $('#district').empty();
                    $('#district').append('<option selected hidden value=""> --Select-- </option>');
                    $('#district').append('<option value="">No Data Found</option>');
                }
            });
        });

        //Get Post Answer and Questions 
        $('select[name=postname]').change(function() {
            let post_id = $("#postname").val();
            $.ajax({
                url: '/candidate/get-post-questions',
                type: "POST",
                data: {
                    post_id: post_id,
                    '_token': '{{ csrf_token() }}'
                },
                cache: false,
                success: function(response) {
                    let html = '';

                    if (response && response.length > 0) {
                        $.each(response, function(index, value) {
                            html += `<div class="row">
                                <div class="col-md-12 col-xs-12 text-left">
                                    <label>${value.ques_name}</label>
                                    <label style="color:red">*</label>`;

                            let options = JSON.parse(value
                                .answer_options); // Convert string to array
                            $.each(options, function(optIndex, optValue) {
                                html += `<label class="radio-inline" style="margin-top: -10px; margin-left: 30px;">
                                    <input type="radio" name="question_${value.post_map_id}" value="${optValue}">&nbsp;${optValue}
                                 </label>`;
                            });

                            html +=
                                `</div></div><div class='row'><div class='col-md-12 col-xw-12'><hr></div></div>`;
                        });
                    } else {
                        html = "<p>No Questions Found.</p>";
                    }

                    $('#questionsContainer').html(html);
                },
                error: function(xhr) {
                    $('#questionsContainer').html('<p>Error loading questions.</p>');
                }
            });
        });


        2

        function select_district() {
            var postname = document.getElementById("postname").value;
            var catid = postname.split("|");


            var rows = $('table.quali tr');
            var ug = rows.filter('#ug');
            var pg = rows.filter('#pg');


            if ((catid[1] == "1")) {
                // state level post(s)
                document.getElementById("city").style.display = "none";
                document.getElementById("labelcity").style.display = "none";
                //document.getElementById("pg").style.display="inline";
                pg.show();
            } else if ((catid[1] == "2")) {
                // district level post(s)
                document.getElementById("city").style.display = "block";
                document.getElementById("labelcity").style.display = "block";
                //document.getElementById("pg").style.display="none";

                pg.hide();
                $("#pg1").prop('required', false);
                $("#pg2").prop('required', false);
                $("#pg3").prop('required', false);
                $("#pg4").prop('required', false);
            }
        }


        $(document).on('change', '#same', function() {
            if ($(this).prop('checked')) {
                var caddr = $('#caddr').val();
                var cpincode = $('#cpincode').val();
                var ccities = $('#cur_district').val();

                $('#paddr').val(caddr).attr('readonly', true);
                $('#ppincode').val(cpincode).attr('readonly', true);
                $('#per_district').val(ccities).trigger('change').attr("readonly", true);
            } else {
                $('#paddr').val('').attr('readonly', false);
                $('#ppincode').val('').attr('readonly', false);
                $('#per_district').val('').trigger('change').attr("readonly", false);
            }
        });

        // if user change Current Address, Pincode, District, then it in uncheck the  Checkbox and Permanent Fields Editable 
        $(document).on('input', '#caddr, #cpincode, #cur_district', function() {
            if ($('#same').prop('checked')) {
                $('#same').prop('checked', false).trigger('change'); // Checkbox Uncheck कर दो

            }
        });



        // Event delegation to handle dynamically added elements
        $(document).on('change', '.orgtype', function() {
            var selectedValue = $(this).val();
            var parentRow = $(this).closest('.row'); // Find the parent row of the selected element
            var ngoDiv = parentRow.find('.ngoDiv');

            if (selectedValue === "NGO") {
                ngoDiv.show();
            } else {
                ngoDiv.hide();
            }
        });


        $('#addmoreBtn').click(function() {
            var newInputFields = '<div class="input-fields"><div class="row">' +
                '<div class="col-md-6 text-left"><br>' +
                '<label for="orgname">संस्था का नाम व पूरा पता<font color="red">*</font></label>' +
                '<input type="text" id="orgname" class="form-control" name="org_name[]" placeholder="" required>' +
                '</div>' +
                '<div class="col-md-3 text-left"><br>' +
                '<label for="orgtype">संस्था शासकीय है अथवा अशासकीय<font color="red">*</font></label>' +
                '<select id="orgtype" class="form-select" name="org_type[]" required>' +
                '<option value="Govt">शासकीय</option>' +
                '<option value="NGO">अशासकीय(NGO)</option>' +
                '<option value="SemiGovt">अर्धशासकीय</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-md-3 text-left"><br>' +
                '<label for="ngono">यदि संस्था अशासकीय है तो भारत शासन के NGO पोर्टल में पंजीयन क्र.</label>' +
                '<input type="text" id="ngono" class="form-control" name="ngo_no[]" placeholder="">' +
                '</div>' +
                '<div class="col-md-3 text-left">' +
                '<label for="desgname">पदनाम</label><label style="color:red">*</label>' +
                '<input type="text" class="form-control" name="desg_name[]" placeholder="" required>' +
                '</div>' +
                '<div class="col-md-3 text-left">' +
                '<label for="nature">अनुभव का कार्यक्षेत्र</label><label style="color:red">*</label>' +
                '<input type="text" class="form-control" name="nature_work[]" placeholder="" required>' +
                '</div>' +
                '<div class="col-md-3 text-left">' +
                '<label for="dfrom">कब से</label><label style="color:red">*</label>' +
                '<input type="date" class="form-control" name="date_from[]"  max="{{ $data['maxdate'] }}" required>' +
                '</div>' +
                '<div class="col-md-3 text-left">' +
                '<label for="dto">कब तक</label><label style="color:red">*</label>' +
                '<input type="date" class="form-control" name="date_to[]"   max="{{ $data['maxdate'] }}" required>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('.removeBtn').css('display', 'block');
            $('#inputFieldsContainer').append(newInputFields);
        });

        $(document).on('click', '.removeBtn', function() {
            // $(this).closest('.input-fields').remove();
            $('.input-fields:last').remove();
            if ($('.input-fields').length <= 0) {
                $('.removeBtn').hide();
            }
        });


        // Get Post List Based on Selected District
        $('select[name=district]').change(function() {
            let dist_id = $("#district").val();
            $.ajax({

                url: '/candidate/get-postname',
                type: "POST",
                data: {
                    dist_id: dist_id,
                    '_token': '{{ csrf_token() }}'
                },
                cache: false,
                success: function(response) {
                    let option = '<option selected disabled value="undefined">--चुनें--</option>';
                    $.each(response, function(index, value) {
                        option += '<option  value="' + value.post_id + '">' + value
                            .title + '</option>';
                    });
                    $('#postname').html(option);
                },
                error: function(xhr) {
                    $('#postname').empty();
                    $('#postname').append(
                        '<option [selected]="true" hidden value=""> --Select-- </option>');
                    $('#postname').append('<option value="">No Data Found</option>');
                }
            });
        });


        // Get Project List Based on Selected District
        function updateprojectOptions(district, selectedproject = null) {
            // let dist_id = $("#district").val();
            $.ajax({

                url: '/candidate/get-project',
                type: "POST",
                data: {
                    dist_id: district,
                    '_token': '{{ csrf_token() }}'
                },
                cache: false,
                success: function(response) {

                    let option = '<option selected disabled value="undefined">--चुनें--</option>';
                    $.each(response, function(index, value) {
                        if (selectedproject && value.Project_Code == selectedproject) {
                            option += '<option value="' + value.Project_Code + '" selected>' + value
                                .Project_Name + '</option>';
                        } else {
                            option += '<option value="' + value.Project_Code + '">' + value
                                .Project_Name + '</option>';
                        }
                    });
                    $('#project').html(option);
                },
                error: function(xhr) {
                    $('#project').empty();
                    $('#project').append('<option [selected]="true" hidden value=""> --Select-- </option>');
                    $('#project').append('<option value="">No Data Found</option>');
                }
            });
        }


        // Get AWC List Based on Selected District And Project
        function updateawcOptions(district, selectedproject = null, selectedawc = null) {
            let dist_id = $("#district").val();
            let pro_id = $("#project").val() ?? selectedproject;
            $.ajax({

                url: '/candidate/get-awc',
                type: "POST",
                data: {
                    dist_id: dist_id,
                    pro_id: pro_id,
                    '_token': '{{ csrf_token() }}'
                },
                cache: false,
                success: function(response) {
                    // console.log(response);
                    let option = '<option selected disabled value="undefined">--चुनें--</option>';
                    $.each(response, function(index, value) {
                        if (selectedawc && value.AWC_Code_11_Digit == selectedawc) {
                            option += '<option value="' + value.AWC_Code_11_Digit + '" selected>' +
                                value.AWC_Name + '</option>';
                        } else {
                            option += '<option value="' + value.AWC_Code_11_Digit + '">' + value
                                .AWC_Name + '</option>';
                        }
                    });
                    $('#awc').html(option);
                },
                error: function(xhr) {
                    $('#awc').empty();
                    $('#awc').append('<option [selected]="true" hidden value=""> --Select-- </option>');
                    $('#awc').append('<option value="">No Data Found</option>');
                }
            });
        }


        // On change event
        $("select[name='district']").change(function() {
            let district = $(this).val();
            updateprojectOptions(district);
        });


        // On change event
        $("select[name='project']").change(function() {

            let dist_id = $("#district").val();
            let project = $(this).val();

            updateawcOptions(district, project);
        });


        // On page load, if there's a district value
        let initialDistrict = $('#district').val();
        var selectedproject = "{{ $data['applicant_details']->project ?? '' }}";
        var selectedawc = "{{ $data['applicant_details']->awc ?? '' }}";


        if (initialDistrict) {
            updateprojectOptions(initialDistrict, selectedproject);
        }

        if (selectedproject) {
            updateawcOptions(initialDistrict, selectedproject, selectedawc);
        }





        ///==================++++++++ Passing Year Functionality ================= +++++++++++##


        // Get all select elements
        const $minyear = $('#minyear');
        const $maxyear = $('#maxyear');
        const $sscYear = $('.year_passing_ssc');
        const $interYear = $('#year_passing_inter');
        const $ugYear = $('.year_passing_ug');
        const $pgYear = $('#year_passing_pg');

        // Get the actual year values
        const minYear = parseInt($minyear.val());
        const maxYear = parseInt($maxyear.val());

        // Function to populate years in a select element
        function populateYears($selectElement, minYear, maxYear) {
            const currentValue = $selectElement.val();
            $selectElement.empty();

            $selectElement.append(
                '<option selected disabled value="undefined">-चयन करें -</option>'); // Add default option

            for (let i = minYear; i <= maxYear; i++) {
                const $option = $('<option></option>')
                    .val(i)
                    .text(i);
                if (i == currentValue) {
                    $option.prop('selected', true);
                }
                $selectElement.append($option); // Append years as usual
            }
        }

        // Initial population of all dropdowns
        populateYears($sscYear, minYear, maxYear);
        populateYears($interYear, minYear, maxYear);
        populateYears($ugYear, minYear, maxYear);
        populateYears($pgYear, minYear, maxYear);

        // Event listener for 10th year change
        $sscYear.on('change', function() {
            const sscSelectedYear = parseInt($(this).val());
            const minInterYear = Math.min(sscSelectedYear + 2, maxYear);
            populateYears($interYear, minInterYear, maxYear);
            $interYear.trigger('change');
        });

        // Event listener for 12th year change
        $interYear.on('change', function() {
            const interSelectedYear = parseInt($(this).val());
            const minUgYear = Math.min(interSelectedYear + 3, maxYear);
            populateYears($ugYear, minUgYear, maxYear);
            $ugYear.trigger('change');
        });

        // Event listener for UG year change
        $ugYear.on('change', function() {
            const ugSelectedYear = parseInt($(this).val());
            const minPgYear = Math.min(ugSelectedYear + 2, maxYear);
            populateYears($pgYear, minPgYear, maxYear);
        });


        //#### Percentage calculation with HTML validation support

        function calculatePercentage(marksObtainedId, marksTotalId, percentageId) {
            const $marksObtained = $(`#${marksObtainedId}`);
            const $marksTotal = $(`#${marksTotalId}`);
            const $percentage = $(`#${percentageId}`);

            const marksObtainedVal = $marksObtained.val().trim();
            const marksTotalVal = $marksTotal.val().trim();

            // Reset validation state
            $marksObtained.removeClass('is-invalid');
            $marksTotal.removeClass('is-invalid');
            $marksObtained.next('.invalid-feedback').text('This field is required');
            $marksTotal.next('.invalid-feedback').text('This field is required');

            // If either field is empty, let HTML validation handle it
            if (marksObtainedVal === '' || marksTotalVal === '') {
                $percentage.val('');
                return;
            }

            const marksObtained = parseFloat(marksObtainedVal);
            const marksTotal = parseFloat(marksTotalVal);

            // Custom validation
            if (isNaN(marksObtained) || isNaN(marksTotal)) {
                $percentage.val('');
                return;
            }

            if (marksTotal <= 0) {
                $marksTotal.addClass('is-invalid');
                $marksTotal.next('.invalid-feedback').text('Total marks must be greater than 0');
                $percentage.val('');
                return;
            }

            if (marksObtained < 0 || marksTotal < 0) {
                $marksObtained.addClass('is-invalid');
                $marksTotal.addClass('is-invalid');
                $marksObtained.next('.invalid-feedback').text('Marks cannot be negative');
                $marksTotal.next('.invalid-feedback').text('Marks cannot be negative');
                $percentage.val('');
                return;
            }

            if (marksObtained > marksTotal) {
                $marksObtained.addClass('is-invalid');
                $marksObtained.next('.invalid-feedback').text('Obtained marks cannot exceed total marks');
                $percentage.val('');
                return;
            }

            // Calculate and display percentage
            const percentage = (marksObtained / marksTotal) * 100;
            $percentage.val(percentage.toFixed(2) + '%');
        }

        // Wrapper functions
        function calc_ssc_perc() {
            calculatePercentage('marks_obtained_ssc', 'marks_total_ssc', 'ssc_percentage');
        }

        function calc_inter_perc() {
            calculatePercentage('marks_obtained_inter', 'marks_total_inter', 'perc_inter');
        }

        function calc_ug_perc() {
            calculatePercentage('marks_obtained_ug', 'marks_total_ug', 'perc_ug');
        }

        function calc_pg_perc() {
            calculatePercentage('marks_obtained_pg', 'marks_total_pg', 'perc_pg');
        }

        // Event listeners for real-time validation
        function attachInputListeners(obtainedId, totalId, calcFunction) {
            const $obtained = $(`#${obtainedId}`);
            const $total = $(`#${totalId}`);

            $obtained.on('input', function() {
                calcFunction();
            });

            $total.on('input', function() {
                calcFunction();
            });
        }

        // Attach listeners for each level
        attachInputListeners('marks_obtained_ssc', 'marks_total_ssc', calc_ssc_perc);
        attachInputListeners('marks_obtained_inter', 'marks_total_inter', calc_inter_perc);
        attachInputListeners('marks_obtained_ug', 'marks_total_ug', calc_ug_perc);
        attachInputListeners('marks_obtained_pg', 'marks_total_pg', calc_pg_perc);

        // Handle Bootstrap validation on form submission
        $('form').on('submit', function(e) {
            const inputs = $(this).find('input[type="number"]');
            let isValid = true;

            inputs.each(function() {
                if ($(this).val().trim() === '') {
                    $(this).addClass('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Clear custom validation messages when correcting
        $('input[type="number"]').on('input', function() {
            const $this = $(this);
            const value = $this.val().trim();

            if (value !== '') {
                $this.removeClass('is-invalid');
                $this.next('.invalid-feedback').text('This field is required');
            }
        });

        // Initial calculations
        calc_ssc_perc();
        calc_inter_perc();
        calc_ug_perc();
        calc_pg_perc();
    </script>
@endsection
