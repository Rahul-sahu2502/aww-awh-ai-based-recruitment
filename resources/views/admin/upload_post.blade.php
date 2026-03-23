@extends('layouts.dahboard_layout')

@section('styles')
    {{--
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" /> --}}
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" />
    {{--
    <script src="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.umd.js"></script> --}}

    <style>
        #cke_notifications_area_editor {
            display: none !important;
        }

        .question-box {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            /*  Har question ke beech line */
        }

        .question-box:last-child {
            border-bottom: none;
            /*  Last question ke baad line nahi aayegi */
        }

        .question-box input {
            margin-right: 10px;
            /*  Checkbox aur text ke beech spacing */
        }
    </style>
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">नया पोस्ट जोड़ें</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">नया पोस्ट जोड़ें</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-file-plus me-2"></i>नया पोस्ट जोड़ें
                            </h5>
                            <a href="/admin/show-posts" class="btn btn-success">
                                <i class="bi bi-list me-2"></i>पोस्ट की सूची
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form id="myForm4" action="{{ url('/admin/upload-post') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="app_id"
                                    value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                                <div class="col-md-12"><br>
                                    <div class="field_wrapper" id="inputFieldsContainer">
                                        <div class="row">
                                            <div class="col-md-6 text-left mt-3">
                                                <label for="advertisementName">विज्ञापन का चयन करें <font color="red">*
                                                    </font>
                                                </label>
                                                <select id="advertisementName" class="form-select" name="Advertisement_ID"
                                                    onchange="toggleStartEndDateField()" required>
                                                    <!-- Default "Select" option -->
                                                    <option value="" disabled selected>-- चयन करें --</option>
                                                    <!-- Loop through advertisements and create options -->
                                                    @foreach ($advertisements as $advertisement)
                                                        <option value="{{ $advertisement->Advertisement_ID }}"
                                                            data-advertisement='@json($advertisement)'>
                                                            {{ $advertisement->Advertisement_Title }}
                                                            ({{ date('d-m-Y', strtotime($advertisement->Advertisement_Date)) }}
                                                            से
                                                            {{ date('d-m-Y', strtotime($advertisement->Date_For_Age)) }} तक)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('Advertisement_ID')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 text-left mt-3">
                                                <label for="postTittle">पोस्ट का चयन करें <font color="red">*</font>
                                                </label>
                                                <select id="postTittle" class="form-select" name="post_id"
                                                    onchange="toggleStartEndDateField()" required>
                                                    <!-- Default "Select" option -->
                                                    <option value="" disabled selected>-- चयन करें --</option>
                                                    <!-- Loop through advertisements and create options -->
                                                    @foreach ($master_post as $post)
                                                        <option value="{{ $post->id }}" data-advertisement='@json($post)'>
                                                            {{ $post->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('Advertisement_ID')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="master_area">क्षेत्र का चयन करें<font color="red">*</font>
                                                </label>
                                                <select name="master_area" id="master_area" class="form-select" required>
                                                    <option value="">-- चुनें --</option>
                                                    @foreach ($master_areas as $area)
                                                        <option value="{{ $area->area_name }}">{{ $area->area_name_hi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('master_area')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Urban repeatable groups -->
                                        <div class="row mt-2" id="urbanGroupsWrapper" style="display: none;">
                                            <div class="col-md-12 d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">शहर / वार्ड / रिक्तियाँ</h6>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="addUrbanGroup">
                                                        + एक और जोड़ें
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="urbanGroupsContainer"></div>
                                        </div>

                                        <template id="urbanGroupTemplate">
                                            <div class="urban-group border rounded p-3 mt-3" data-index="__INDEX__">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-2">शहरी स्थान <span class="urban-location-number"></span>
                                                    </h6>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger removeUrbanGroup">
                                                        हटाएं
                                                    </button>
                                                </div>

                                                <div class="row g-2 align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label">शहर का चयन करें <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select urban-city-select"
                                                            name="urban_groups[__INDEX__][city_nnn_code]" required>
                                                            <option value="">-- चुनें --</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">वार्ड का चयन करें <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select urban-ward-select"
                                                            name="urban_groups[__INDEX__][ward_codes][]" multiple
                                                            required></select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">कुल रिक्त पदों की संख्या <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control urban-total-vacancy"
                                                            name="urban_groups[__INDEX__][total_vacancy]" min="1"
                                                            placeholder="रिक्तियाँ" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Rural repeatable groups -->
                                        <div class="row mt-2" id="ruralGroupsWrapper" style="display: none;">
                                            <div class="col-md-12 d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">ग्राम पंचायत / ग्राम / रिक्तियाँ</h6>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="addLocationGroup">
                                                        + एक और जोड़ें
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="locationGroupsContainer"></div>
                                        </div>

                                        <template id="locationGroupTemplate">
                                            <div class="location-group border rounded p-3 mt-3" data-index="__INDEX__">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-2">ग्रामीण स्थान <span class="location-number"></span>
                                                    </h6>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger removeLocationGroup">
                                                        हटाएं
                                                    </button>
                                                </div>

                                                <div class="row g-2 align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label">ग्राम पंचायत का चयन करें <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select gp-select"
                                                            name="location_groups[__INDEX__][gp_nnn_code]" required>
                                                            <option value="">-- चुनें --</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">ग्राम का चयन करें <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select village-select"
                                                            name="location_groups[__INDEX__][village_codes][]" multiple
                                                            required></select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">कुल रिक्त पदों की संख्या <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control total-vacancy-input"
                                                            name="location_groups[__INDEX__][total_vacancy]" min="1"
                                                            placeholder="रिक्तियाँ" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-xs-12 text-left mt-2">
                                                    <label class="form-label d-block mb-1"
                                                        style="font-size: 15px; color: #000;">
                                                        क्या चयनित क्षेत्र जनमन क्षेत्र हैं? <span
                                                            class="text-danger">*</span>
                                                    </label>

                                                    <div class="d-flex align-items-center gap-4">
                                                        <div class="form-check">
                                                            <input type="radio" class="form-check-input AdharConfirm"
                                                                name="location_groups[__INDEX__][is_janman_area]" value="1"
                                                                required>
                                                            <label class="form-check-label" style="cursor: pointer;">
                                                                हाँ
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="radio" class="form-check-input AdharConfirm"
                                                                name="location_groups[__INDEX__][is_janman_area]" value="0"
                                                                required>
                                                            <label class="form-check-label" style="cursor: pointer;">
                                                                नहीं
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <!-- File Upload (optional) -->
                                        <div class="row align-items-end g-3 mt-3">
                                            <div class="col-md-9 col-sm-6">
                                                <label for="fileUpload" class="form-label">
                                                    पोस्ट संबंधित फ़ाइल अपलोड करें (वैकल्पिक)
                                                </label>
                                                <div class="position-relative">
                                                    <input type="file" name="file" id="fileUpload" class="form-control"
                                                        accept="image/*, .pdf">
                                                    <div id="viewButton" class="d-none mt-2">
                                                        <a type="button" class=" btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#docModal">
                                                            <i class="bi bi-eye me-1"></i>View Document
                                                        </a>
                                                    </div>
                                                </div>
                                                @error('file')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <hr class="mt-3">

                                        <!-- Readonly Information Message -->
                                        <div class="row mb-3" id="editToggleContainer" style="display: none;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info d-flex align-items-center">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    <div>
                                                        <strong>सूचना:</strong>
                                                        <span id="editModeText">इस पद का विवरण पहले से जोड़ दिया गया है।
                                                            निम्नलिखित
                                                            फ़ील्ड केवल देखने के लिए हैं और इन्हें संपादित या अपडेट नहीं
                                                            किया जा सकता।</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7 text-left mt-3">
                                                <label for="postName">पोस्ट का शीर्षक<font color="red">*</font></label>
                                                <input type="text" id="postName" class="form-control auto-populated-field"
                                                    name="post_name" placeholder="पद का नाम" required readonly>
                                                @error('post_name')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            {{-- <div class="col-md-4 text-left mt-3" style="display: none;">
                                                <label for="categoryType">श्रेणी <font color="red">*</font></label>
                                                <select id="categoryType" class="form-select" name="fk_category_id" required
                                                    onchange="toggleDistrictField()">
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->cat_id }}">{{ $category->cat_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('fk_category_id')
                                                <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 text-left mt-3" id="districtField" style="display: none;">
                                                <label for="distNames">जिला चुनें<font color="red">*</font></label>
                                                <select id="distNames" class="form-select select2" name="fk_district_id[]"
                                                    multiple="multiple">
                                                    @foreach ($districts as $district)
                                                    <option value="{{ $district->District_Code_LGD }}">
                                                        {{ $district->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" id="selectedDistricts" name="selectedDistricts">
                                            </div> --}}
                                        </div>


                                        <div class="row">

                                            {{-- Toggle to enable caste-wise vacancy --}}
                                            {{-- <div class="col-md-12 mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="toggleCasteVacancy">
                                                    <label class="form-check-label" for="toggleCasteVacancy">
                                                        क्या आप जाति-वार रिक्तियाँ बाँटना चाहते हैं?
                                                    </label>
                                                </div>
                                            </div> --}}

                                            {{-- Caste-wise inputs (from DB) --}}
                                            <div class="col-md-12 mt-3" id="casteVacancyDiv" style="display: none;">
                                                <label class="">जाति-वार रिक्तियाँ</label>

                                                @foreach ($caste_category as $item)
                                                    <div class="form-check mb-2 row align-items-center">
                                                        {{-- Checkbox & Label --}}
                                                        {{-- <div class="col-md-2">
                                                            <input class="form-check-input caste-toggle" type="checkbox"
                                                                name="cast_category[]" value="{{ $item->caste_id }}"
                                                                id="{{ strtolower($item->caste_id) }}">
                                                            <label class="form-check-label ms-1"
                                                                for="{{ strtolower($item->caste_id) }}">
                                                                {{ $item->caste_title }}<font color="red">*</font>
                                                            </label>
                                                        </div> --}}

                                                        {{-- Vacancy Input --}}
                                                        {{-- <div class="col-md-4">
                                                            <input type="number" name="vacancy[{{ $item->caste_id }}]"
                                                                class="form-control caste-vacancy"
                                                                placeholder="रिक्त पदों की संख्या" disabled>
                                                        </div> --}}
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>



                                        <div class="row">

                                            {{-- Total vacancy input --}}
                                            {{-- <div class="col-md-3 mt-3" id="totalVacancyDiv">
                                                <label for="total_vacancy">कुल रिक्त पदों की संख्या<font color="red">*
                                                    </font></label>
                                                <input type="number" name="total_vacancy" id="total_vacancy"
                                                    class="form-control" placeholder="कुल रिक्तियाँ दर्ज करें">
                                            </div> --}}



                                            <div class="col-md-3 text-left mt-3">
                                                <label for="minAge">न्यूनतम आयु दर्ज करें<font color="red">*</font>
                                                </label>
                                                <input type="number" id="minAge" class="form-control auto-populated-field"
                                                    name="min_age" min="18" max="47"
                                                    placeholder="18 से 47 के बीच आयु दर्ज करें" required readonly>

                                                @error('min_age')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- new changes end --}}

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="maxAge">अधिकतम आयु दर्ज करें<font color="red">*</font>
                                                </label>
                                                <input type="number" id="maxAge" class="form-control auto-populated-field"
                                                    name="max_age" min="18" max="47"
                                                    placeholder="18 से 47 के बीच आयु दर्ज करें" required readonly>

                                                @error('max_age')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="maxAge">आयु में छूट के बाद अधिकतम आयु </label>
                                                <input type="number" id="maxAgeRelax"
                                                    class="form-control auto-populated-field" name="max_age_relax" min="18"
                                                    max="47" placeholder="18 से 47 के बीच आयु दर्ज करें" readonly>

                                                @error('max_age_relax')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div class="col-md-3 text-left mt-3" id="minQualificationField">
                                                <label for="minQualification">न्यूनतम योग्यता का चयन करें<font color="red">*
                                                    </font>
                                                </label>
                                                <select id="minQualification" class="form-select auto-populated-field"
                                                    data-subject-url="{{ url('/admin/get-subjects-by-qualification') }}"
                                                    name="min_Qualification" required disabled>
                                                    <option value="" disabled selected>-- चयन करें --</option>
                                                    @foreach ($qualifications as $qualification)
                                                        @if ($qualification->Quali_ID != 1)
                                                            <option value="{{ $qualification->Quali_ID }}">
                                                                {{ $qualification->Quali_Name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="subject_select">विषय जोड़ें (न्यूनतम योग्यता से
                                                संबंधित)</label>
                                            <div class="input-group mb-2">
                                                <select id="subject_select" class="form-select">
                                                    <option value="">-- विषय चुनें --</option>
                                                    {{-- By default empty, will be filled by JS --}}
                                                </select>
                                                <button type="button" id="add_subject"
                                                    class="btn btn-success">जोड़ें</button>
                                            </div>
                                            <div id="subject_list" class="mt-2 d-flex flex-wrap gap-2">
                                                <!-- चुने गए विषय यहाँ दिखेंगे -->
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="skill_select">कौशल (Skills) जोड़ें</label>

                                            <div class="input-group mb-2">
                                                <select id="skill_select" class="form-select">
                                                    <option value="">-- कौशल चुनें --</option>
                                                    @foreach ($skills as $skill)
                                                        <option value="{{ $skill->skill_id }}">
                                                            {{ $skill->skill_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" id="add_skill" class="btn btn-success">जोड़ें</button>
                                            </div>

                                            <!-- Skills List -->
                                            <div id="skill_list" class="mt-2 d-flex flex-wrap gap-2">
                                                <!-- Selected skills will be added here -->
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="org_type_input">न्यूनतम अनुभव का चयन करें</label>

                                            <div id="org_type_container">
                                                <!-- Initially one field will be shown -->
                                                <div class="org-type-entry d-flex align-items-center mb-2" id="org_type_1">
                                                    <select class="form-control org-type-select me-2 form-select"
                                                        name="org_types[1]">
                                                        <option value="">-- संगठन प्रकार चुनें --</option>
                                                        @foreach ($org_types as $org_type)
                                                            <option value="{{ $org_type->org_id }}">
                                                                {{ $org_type->org_type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" class="form-control min-experience me-2"
                                                        name="experience[1]" placeholder="न्यूनतम अनुभव (वर्षों में)">
                                                </div>
                                            </div>

                                            {{-- <button type="button" id="add_org_type" class="btn btn-success mt-3">और
                                                जोड़ें</button> --}}
                                        </div>
                                    </div>

                                    <div class='row'>
                                        <div class='col-md-12 col-xw-12'>
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-xs-12 text-left">
                                            <label for="rules">नियम और विनियम दर्ज करें<font color="red">*</font>
                                            </label>
                                            <textarea name="rules" id="editor" required></textarea>
                                            @error('rules')
                                                <span class="text-danger is-invalid">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div><br>
                                    <!-- File Upload Column -->



                                    <div class="col-md-12 col-xs-12 text-left my-2">
                                        <label class="form-label" for="questions">कृपया प्रश्नों का चयन करें:<font
                                                color="red">*
                                            </font>
                                        </label>
                                        <div id="questionsContainer" class="border rounded p-2"></div>
                                        <!--  Rounded border aur padding -->
                                        @if ($errors->has('questions'))
                                            <span class="text-danger mt-2 d-block">{{ $errors->first('questions') }}</span>
                                        @endif

                                    </div>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="docModal"
                                        tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel">Selected File</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img id="imagePreview" class="img-fluid d-none" alt="Selected File">
                                                    <iframe id="docViewer" class="w-100 d-none"
                                                        style="height: 500px; border: none;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button id="submitBtn" style="float: right;" class="btn btn-success btn-lg pull-right"
                                        type="submit">सबमिट करें </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>

    <script>
        /**
         * =====================================================================
         * INITIALIZATION AND CONFIGURATION
         * =====================================================================
         */

        // Initialize CKEditor
        CKEDITOR.replace('editor', {
            height: 200,
            removePlugins: 'elementspath',
            toolbarCanCollapse: false,
            resize_enabled: false,
            toolbar: [{
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline']
            },
            {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList']
            },
            {
                name: 'links',
                items: ['Link', 'Unlink']
            },
            {
                name: 'insert',
                items: ['Image', 'Table']
            },
            {
                name: 'tools',
                items: ['Maximize']
            }
            ]
        });

        // Global variable to track edit mode and security token (no longer used - fields are always readonly)
        let isEditMode = false;
        let editModeToken = null;
        let locationGroupIndex = 0;
        let urbanGroupIndex = 0;
        let cachedGpOptions = [];
        let cachedCityOptions = [];
        let villageCache = {};
        let wardCache = {};

        // Initialize Select2 elements
        $(document).ready(function () {
            // Ensure optional file upload
            $('#fileUpload').prop('required', false);

            $('#distNames').select2({
                allowClear: true,
                width: '100%'
            });

            // Load initial subjects if qualification is pre-selected
            let preselectedQualification = $('#minQualification').val();
            if (preselectedQualification) {
                loadSubjects(preselectedQualification);
            }

            // Initialize area dependent UI
            toggleAreaSections();

            // Add first rural/urban group by default based on selected area
            if ($('#master_area').val() === 'Rural') {
                ensureAtLeastOneLocationGroup();
            } else if ($('#master_area').val() === 'Urban') {
                ensureAtLeastOneUrbanGroup();
            }

            // Handle post selection change
            $('#postTittle').on('change', function () {
                const postId = $(this).val();
                if (postId) {
                    // Clear form FIRST, immediately when selection changes
                    clearFormFields();
                    // Then load new data
                    loadPostConfigData(postId);
                }
            });

            // Add new rural location group
            $('#addLocationGroup').on('click', function () {
                addLocationGroup();
            });
            $('#addUrbanGroup').on('click', function () {
                addUrbanGroup();
            });

            // Remove rural location group
            $('#locationGroupsContainer').on('click', '.removeLocationGroup', function () {
                $(this).closest('.location-group').remove();
                refreshLocationGroupNumbers();
                toggleRemoveButtons();
            });
            $('#urbanGroupsContainer').on('click', '.removeUrbanGroup', function () {
                $(this).closest('.urban-group').remove();
                refreshUrbanGroupNumbers();
                toggleUrbanRemoveButtons();
            });

            // GP change -> load villages for that group
            $('#locationGroupsContainer').on('change', '.gp-select', function () {
                const gpCode = $(this).val();
                const $group = $(this).closest('.location-group');
                const $villageSelect = $group.find('.village-select');
                if (gpCode) {
                    loadVillagesForGp(gpCode, $villageSelect);
                } else {
                    $villageSelect.empty();
                }
            });

            // Urban: city change -> load wards
            $('#urbanGroupsContainer').on('change', '.urban-city-select', function () {
                const cityCode = $(this).val();
                const $group = $(this).closest('.urban-group');
                const $wardSelect = $group.find('.urban-ward-select');
                if (cityCode) {
                    loadWardsForCity(cityCode, $wardSelect);
                } else {
                    $wardSelect.empty();
                }
            });
        });

        function toggleRemoveButtons() {
            const groups = $('#locationGroupsContainer .location-group');
            if (groups.length <= 1) {
                groups.find('.removeLocationGroup').addClass('d-none');
            } else {
                groups.find('.removeLocationGroup').removeClass('d-none');
            }
        }

        function toggleUrbanRemoveButtons() {
            const groups = $('#urbanGroupsContainer .urban-group');
            if (groups.length <= 1) {
                groups.find('.removeUrbanGroup').addClass('d-none');
            } else {
                groups.find('.removeUrbanGroup').removeClass('d-none');
            }
        }

        function refreshLocationGroupNumbers() {
            $('#locationGroupsContainer .location-group').each(function (idx) {
                $(this).find('.location-number').text(idx + 1);
            });
        }

        function refreshUrbanGroupNumbers() {
            $('#urbanGroupsContainer .urban-group').each(function (idx) {
                $(this).find('.urban-location-number').text(idx + 1);
            });
        }

        function addLocationGroup(prefill = {}) {
            const tmpl = document.getElementById('locationGroupTemplate').innerHTML;
            const index = locationGroupIndex++;
            const html = tmpl.replace(/__INDEX__/g, index);
            $('#locationGroupsContainer').append(html);
            const $group = $('#locationGroupsContainer .location-group').last();
            initGroupSelect2($group);
            refreshLocationGroupNumbers();
            toggleRemoveButtons();

            populateGpSelect($group.find('.gp-select'), prefill.gp_nnn_code || '');

            if (prefill.total_vacancy) {
                $group.find('.total-vacancy-input').val(prefill.total_vacancy);
            }

            if (prefill.is_janman_area !== undefined) {
                $group.find(`input[type="radio"][value="${prefill.is_janman_area}"]`).prop('checked', true);
            }

            if (prefill.village_codes && prefill.village_codes.length) {
                loadVillagesForGp(prefill.gp_nnn_code, $group.find('.village-select'), prefill.village_codes);
            }

            return $group;
        }

        function addUrbanGroup(prefill = {}) {
            const tmpl = document.getElementById('urbanGroupTemplate').innerHTML;
            const index = urbanGroupIndex++;
            const html = tmpl.replace(/__INDEX__/g, index);
            $('#urbanGroupsContainer').append(html);
            const $group = $('#urbanGroupsContainer .urban-group').last();

            initUrbanSelect2($group);
            refreshUrbanGroupNumbers();
            toggleUrbanRemoveButtons();

            populateCitySelect($group.find('.urban-city-select'), prefill.city_nnn_code || '');

            if (prefill.total_vacancy) {
                $group.find('.urban-total-vacancy').val(prefill.total_vacancy);
            }

            if (prefill.is_janman_area !== undefined) {
                $group.find(`input[type="radio"][value="${prefill.is_janman_area}"]`).prop('checked', true);
            }

            if (prefill.ward_codes && prefill.ward_codes.length) {
                loadWardsForCity(prefill.city_nnn_code, $group.find('.urban-ward-select'), prefill.ward_codes);
            }

            return $group;
        }

        function initGroupSelect2($group) {
            $group.find('.gp-select').select2({
                placeholder: '-- चुनें --',
                width: '100%'
            });
            $group.find('.village-select').select2({
                placeholder: 'ग्राम चुनें',
                width: '100%',
                allowClear: true
            }).val(null).trigger('change');
        }

        function initUrbanSelect2($group) {
            $group.find('.urban-city-select').select2({
                placeholder: '-- चुनें --',
                width: '100%'
            });
            $group.find('.urban-ward-select').select2({
                placeholder: 'वार्ड चुनें',
                width: '100%',
                allowClear: true
            }).val(null).trigger('change');
        }

        function ensureAtLeastOneLocationGroup() {
            if ($('#locationGroupsContainer .location-group').length === 0) {
                addLocationGroup();
            }
        }

        function ensureAtLeastOneUrbanGroup() {
            if ($('#urbanGroupsContainer .urban-group').length === 0) {
                addUrbanGroup();
            }
        }

        function populateGpSelect($select, selectedValue = '') {
            $select.empty();
            $select.append('<option value="">-- चुनें --</option>');
            cachedGpOptions.forEach(function (item) {
                const option = `<option value="${item.gp_nnn_code}">${item.panchayat_name_hin}</option>`;
                $select.append(option);
            });
            if (selectedValue) {
                $select.val(String(selectedValue));
            }
        }

        function populateCitySelect($select, selectedValue = '') {
            $select.empty();
            $select.append('<option value="">-- चुनें --</option>');
            cachedCityOptions.forEach(function (item) {
                const option = `<option value="${item.std_nnn_code}">${item.nnn_name}</option>`;
                $select.append(option);
            });
            if (selectedValue) {
                $select.val(String(selectedValue));
            }
        }

        function fetchGpOptions(callback = null) {
            if (cachedGpOptions.length) {
                if (typeof callback === 'function') callback();
                return;
            }

            $.ajax({
                url: `/admin/get-ward-or-block/Rural`,
                type: 'GET',
                success: function (response) {
                    cachedGpOptions = response || [];
                    if (typeof callback === 'function') callback();
                },
                error: function () {
                    cachedGpOptions = [];
                }
            });
        }

        function fetchCityOptions(callback = null) {
            if (cachedCityOptions.length) {
                if (typeof callback === 'function') callback();
                return;
            }

            $.ajax({
                url: `/admin/get-ward-or-block/Urban`,
                type: 'GET',
                success: function (response) {
                    cachedCityOptions = response || [];
                    if (typeof callback === 'function') callback();
                },
                error: function () {
                    cachedCityOptions = [];
                }
            });
        }

        function loadVillagesForGp(gpCode, $villageSelect, preselected = []) {
            if (!gpCode) {
                $villageSelect.empty();
                return;
            }

            const cacheKey = String(gpCode);
            $villageSelect.html('<option value="">लोड हो रहा है...</option>');

            const handleVillageResponse = function (response) {
                $villageSelect.empty();
                $villageSelect.append('<option value="" disabled hidden>ग्राम चुनें</option>');
                response.forEach(function (item) {
                    $villageSelect.append(
                        `<option value="${item.village_code}">${item.village_name_hin}</option>`
                    );
                });
                const cleanedSelection = (preselected || []).filter(Boolean).map(String);
                if (cleanedSelection.length) {
                    $villageSelect.val(cleanedSelection).trigger('change');
                } else {
                    $villageSelect.val([]).trigger('change');

                }
            };

            if (villageCache[cacheKey]) {
                handleVillageResponse(villageCache[cacheKey]);
                return;
            }

            $.ajax({
                url: `/admin/get-ward-or-block/Rural?gp=${encodeURIComponent(gpCode)}`,
                type: 'GET',
                success: function (response) {
                    villageCache[cacheKey] = response || [];
                    handleVillageResponse(response || []);
                },
                error: function () {
                    $villageSelect.html('<option value="">लोड करने में समस्या</option>');
                }
            });
        }

        function loadWardsForCity(cityCode, $wardSelect, preselected = []) {
            if (!cityCode) {
                $wardSelect.empty();
                return;
            }

            const cacheKey = String(cityCode);
            $wardSelect.html('<option value="">लोड हो रहा है...</option>');

            const handleWardResponse = function (response) {
                $wardSelect.empty();
                $wardSelect.append('<option value="" disabled hidden>वार्ड चुनें</option>');
                response.forEach(function (item) {
                    $wardSelect.append(
                        `<option value="${item.id}">${item.ward_name} (${item.ward_no})</option>`
                    );
                });
                const cleanedSelection = (preselected || []).filter(Boolean).map(String);
                if (cleanedSelection.length) {
                    $wardSelect.val(cleanedSelection).trigger('change');
                } else {
                    $wardSelect.val([]).trigger('change');
                }
            };

            if (wardCache[cacheKey]) {
                handleWardResponse(wardCache[cacheKey]);
                return;
            }

            $.ajax({
                url: `/admin/get-ward-or-block/Urban?city=${encodeURIComponent(cityCode)}`,
                type: 'GET',
                success: function (response) {
                    wardCache[cacheKey] = response || [];
                    handleWardResponse(response || []);
                },
                error: function () {
                    $wardSelect.html('<option value="">लोड करने में समस्या</option>');
                }
            });
        }

        function toggleAreaSections() {
            const areaName = $('#master_area').val();

            if (areaName === 'Rural') {
                $('#ruralGroupsWrapper').show();
                $('#urbanGroupsWrapper').hide();
                $('#singleVacancyWrapper').hide();
                $('#urbanGroupsContainer').empty();
                urbanGroupIndex = 0;
                $('input[name="isJanmanArea"]').prop('required', false).prop('checked', false);

                fetchGpOptions(function () {
                    ensureAtLeastOneLocationGroup();
                    $('#locationGroupsContainer .gp-select').each(function () {
                        populateGpSelect($(this), $(this).val());
                    });
                });
            } else if (areaName === 'Urban') {
                $('#ruralGroupsWrapper').hide();
                $('#locationGroupsContainer').empty();
                locationGroupIndex = 0;
                $('#singleVacancyWrapper').hide();
                $('#urbanGroupsWrapper').show();
                $('input[name="isJanmanArea"]').prop('required', false).prop('checked', false);

                fetchCityOptions(function () {
                    ensureAtLeastOneUrbanGroup();
                    $('#urbanGroupsContainer .urban-city-select').each(function () {
                        populateCitySelect($(this), $(this).val());
                    });
                });
            } else {
                $('#ruralGroupsWrapper').hide();
                $('#urbanGroupsWrapper').hide();
                $('#singleVacancyWrapper').hide();
                $('#locationGroupsContainer, #urbanGroupsContainer').empty();
                locationGroupIndex = 0;
                urbanGroupIndex = 0;
                $('input[name="isJanmanArea"]').prop('required', false).prop('checked', false);
            }
        }

        /**
         * =====================================================================
         * POST CONFIG DATA LOADING
         * =====================================================================
         */

        function clearFormFields() {
            // Clear all text inputs
            $('#postName').val('');
            $('#minAge').val('');
            $('#maxAge').val('');
            $('#maxAgeRelax').val('');
            $('#total_vacancy').val('');

            // Reset dropdowns
            $('#master_area').val('').trigger('change');
            $('#categoryType').val('').trigger('change');
            $('#minQualification').val('').trigger('change');

            // Clear Select2 fields
            $('#urbanGroupsContainer').empty();
            urbanGroupIndex = 0;
            $('#gp_name').val(null).trigger('change');
            $('#village_name').val(null).trigger('change');
            $('#distNames').val(null).trigger('change');

            // Hide area-specific fields
            $('#nagarName, #gpName, #wardName, #villageName').hide();

            // Reset rural location groups
            $('#locationGroupsContainer').empty();
            locationGroupIndex = 0;
            if ($('#master_area').val() === 'Rural') {
                ensureAtLeastOneLocationGroup();
            }
            if ($('#master_area').val() === 'Urban') {
                ensureAtLeastOneUrbanGroup();
            }
            toggleRemoveButtons();
            toggleUrbanRemoveButtons();
            $('input[name="isJanmanArea"]').prop('checked', false);

            // Clear subject list
            $('#subject_list').empty();
            $('#subject_select').val('').html('<option value="">-- विषय चुनें --</option>');

            // Clear skill list
            $('#skill_list').empty();
            $('#skill_select').val('');

            // Clear organization container and reset to one field
            $('#org_type_container').html(`
                                                                                                                                <div class="org-type-entry d-flex align-items-center mb-2" id="org_type_1">
                                                                                                                                    <select class="form-control org-type-select me-2 form-select" name="org_types[1]">
                                                                                                                                        <option value="">-- संगठन प्रकार चुनें --</option>
                                                                                                                                        @foreach ($org_types as $org_type)
                                                                                                                                            <option value="{{ $org_type->org_id }}">{{ $org_type->org_type }}</option>
                                                                                                                                        @endforeach
                                                                                                                                    </select>
                                                                                                                                    <input type="number" class="form-control min-experience me-2" name="experience[1]" placeholder="न्यूनतम अनुभव (वर्षों में)">
                                                                                                                                </div>
                                                                                                                            `);

            // Clear CKEditor
            if (CKEDITOR.instances.editor) {
                CKEDITOR.instances.editor.setData('');
            }

            // Uncheck all questions
            $('input[name="questions[]"]').prop('checked', false);

            // Clear all child question containers
            $('.child-questions-container').empty();

            // Clear selected questions
            window.selectedQuestions = [];

            // Reset file upload
            $('#fileUpload').val('');
            $('#viewButton').addClass('d-none');
            $('#imagePreview').addClass('d-none').attr('src', '');
            $('#docViewer').addClass('d-none').attr('src', '');

            // Hide edit toggle container and reset to readonly mode
            $('#editToggleContainer').hide();
            isEditMode = false;
            editModeToken = null;

            // Reset auto-populated fields to readonly state
            $('.auto-populated-field').each(function () {
                if ($(this).is('select')) {
                    $(this).prop('disabled', true).removeAttr('data-edit-token');
                } else {
                    $(this).prop('readonly', true).removeAttr('data-edit-token');
                }
            });

            // Hide add/remove buttons
            $('#add_subject, #add_skill').hide();
            $('.remove-subject, .remove-skill, .remove-org-type').hide();
        }

        function loadPostConfigData(postId) {
            $.ajax({
                url: `/admin/get-post-config/${postId}`,
                type: 'GET',
                beforeSend: function () {
                    // Show loading animation
                    Swal.fire({
                        title: '<span style="font-size: 20px;">लोड हो रहा है...</span>',
                        html: '<p style="font-size: 16px; margin-top: 10px;">पोस्ट डेटा लोड किया जा रहा है</p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    if (response.success) {
                        populateFormFields(response.data);

                        // Show success message for 2 seconds before closing
                        Swal.fire({
                            icon: 'success',
                            title: 'लोड सफल!',
                            text: 'पोस्ट डेटा लोड हो गया।',
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false

                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'कृपया ध्यान दें ',
                            text: response.message,
                            confirmButtonText: 'ठीक है'
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'कृपया ध्यान दें ',
                        text: 'डेटा लोड करने में त्रुटि हुई',
                        confirmButtonText: 'ठीक है'
                    });
                }
            });
        }

        function populateFormFields(data) {
            console.log('Populating form with data:', data);

            // Show edit toggle container
            $('#editToggleContainer').show();
            isEditMode = false;
            editModeToken = null;

            // IMPORTANT: First enable all fields temporarily to set values properly
            $('.auto-populated-field').each(function () {
                if ($(this).is('select')) {
                    $(this).prop('disabled', false);
                } else {
                    $(this).prop('readonly', false);
                }
            });

            // Hide add/remove buttons initially
            $('#add_subject, #add_skill').hide();
            $('.remove-subject, .remove-skill, .remove-org-type').hide();

            // Set post name
            if (data.post_name) {
                $('#postName').val(data.post_name);
            }

            // Note: Area, City, Ward, GP, and Category fields are now user-selected
            // They are no longer auto-populated from master_post_config
            // These fields should be filled manually by the user

            // Set ages
            if (data.min_age) $('#minAge').val(data.min_age);
            if (data.max_age) $('#maxAge').val(data.max_age);
            if (data.max_age_relax) $('#maxAgeRelax').val(data.max_age_relax);

            // Set qualification
            if (data.quali_id) {
                $('#minQualification').val(data.quali_id).trigger('change');

                // Load subjects for this qualification
                setTimeout(function () {
                    loadSubjects(data.quali_id, function () {
                        // After subjects are loaded, select them
                        if (data.fk_subject_id && Array.isArray(data.fk_subject_id)) {
                            // Clear before adding to prevent duplicates
                            $('#subject_list').empty();

                            data.fk_subject_id.forEach(function (subjectId, idx) {
                                const subjectOption = $(
                                    `#subject_select option[value="${subjectId}"]`);
                                if (subjectOption.length) {
                                    const subjectText = subjectOption.text();
                                    const id = 'subject_' + subjectId + '_' + Date.now() + '_' +
                                        idx;

                                    // Check if this subject is not already added
                                    if (!$(`#subject_list input[value="${subjectId}"]`).length) {
                                        $('#subject_list').append(`
                                                                                                                                                            <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                                                                                                                                                                <input type="hidden" name="subjects[]" value="${subjectId}">
                                                                                                                                                                <span>${subjectText}</span>
                                                                                                                                                                <button type="button" class="btn-close btn-close-white ms-2 remove-subject" data-id="${id}" aria-label="Close" style="display: none;"></button>
                                                                                                                                                            </div>
                                                                                                                                                        `);
                                    }
                                }
                            });
                        }
                    });
                }, 300);
            }

            // Set skills
            if (data.fk_skill_id && Array.isArray(data.fk_skill_id)) {
                // Clear before adding to prevent duplicates
                $('#skill_list').empty();

                data.fk_skill_id.forEach(function (skillId, idx) {
                    const skillOption = $(`#skill_select option[value="${skillId}"]`);
                    if (skillOption.length) {
                        const skillText = skillOption.text();
                        const id = 'skill_' + skillId + '_' + Date.now() + '_' + idx;

                        // Check if this skill is not already added
                        if (!$(`#skill_list input[value="${skillId}"]`).length) {
                            $('#skill_list').append(`
                                                                                                                                                <div class="badge bg-info p-2 d-flex align-items-center" id="${id}">
                                                                                                                                                    <input type="hidden" name="skills[]" value="${skillId}">
                                                                                                                                                    <span>${skillText}</span>
                                                                                                                                                    <button type="button" class="btn-close btn-close-white ms-2 remove-skill" data-id="${id}" aria-label="Close" style="display: none;"></button>
                                                                                                                                                </div>
                                                                                                                                            `);
                        }
                    }
                });
            }

            // Set organization types and experience
            if (data.fk_organization_type_id && Array.isArray(data.fk_organization_type_id) && data.fk_organization_type_id
                .length > 0) {
                // Clear container completely before adding new entries
                $('#org_type_container').empty();

                data.fk_organization_type_id.forEach(function (orgTypeId, index) {
                    const experience = (data.minimum_experiance_year && data.minimum_experiance_year[index]) ? data
                        .minimum_experiance_year[index] : '';
                    const entryId = 'org_type_' + (index + 1);

                    $('#org_type_container').append(`
                                                                                                                                        <div class="org-type-entry d-flex align-items-center mb-2" id="${entryId}">
                                                                                                                                            <select class="form-control org-type-select me-2 form-select auto-populated-field" name="org_types[${index + 1}]" disabled>
                                                                                                                                                <option value="">-- संगठन प्रकार चुनें --</option>
                                                                                                                                                @foreach ($org_types as $org_type)
                                                                                                                                                    <option value="{{ $org_type->org_id }}" ${orgTypeId == '{{ $org_type->org_id }}' ? 'selected' : ''}>
                                                                                                                                                        {{ $org_type->org_type }}
                                                                                                                                                    </option>
                                                                                                                                                @endforeach
                                                                                                                                            </select>
                                                                                                                                            <input type="number" class="form-control min-experience me-2 auto-populated-field" name="experience[${index + 1}]" placeholder="न्यूनतम अनुभव (वर्षों में)" value="${experience}" readonly>
                                                                                                                                        </div>
                                                                                                                                    `);
                });
            }

            // Set guidelines in CKEditor
            if (data.guidelines) {
                setTimeout(function () {
                    if (CKEDITOR.instances.editor) {
                        CKEDITOR.instances.editor.setData(data.guidelines);
                    }
                }, 300);
            }

            // Handle questions - they will be checked when questions are loaded
            if (data.fk_ques_id && Array.isArray(data.fk_ques_id)) {
                // First, uncheck all questions to prevent duplicates
                $('input[name="questions[]"]').prop('checked', false);

                // Clear all child question containers
                $('.child-questions-container').empty();

                // Store selected questions temporarily
                window.selectedQuestions = data.fk_ques_id;

                // Check questions after they are loaded with increased delay
                setTimeout(function () {
                    data.fk_ques_id.forEach(function (quesId) {
                        const checkbox = $(`input[name="questions[]"][value="${quesId}"]`);
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                            checkbox.addClass('auto-populated-question'); // Mark as auto-populated

                            // If it's a parent question, trigger change event to load child questions
                            if (checkbox.hasClass('parent-question-checkbox')) {
                                checkbox.trigger('change');

                                // Wait for child questions to load, then check them if needed
                                setTimeout(function () {
                                    // Check if any child questions need to be selected
                                    data.fk_ques_id.forEach(function (childQuesId) {
                                        const childCheckbox = $(
                                            `input[name="questions[]"][value="${childQuesId}"]`
                                        );
                                        if (childCheckbox.length && childCheckbox.hasClass(
                                            'child-question-checkbox')) {
                                            childCheckbox.prop('checked', true);
                                            childCheckbox.addClass(
                                                'auto-populated-question'
                                            ); // Mark child as auto-populated
                                        }
                                    });
                                }, 800);
                            }
                        }
                    });

                    // IMPORTANT: Disable ALL questions checkboxes after checking (readonly mode)
                    setTimeout(function () {
                        $('input[name="questions[]"]').each(function () {
                            $(this).prop('disabled', true).removeAttr('data-edit-token');
                            // Mark all as auto-populated for toggle functionality
                            if ($(this).prop('checked')) {
                                $(this).addClass('auto-populated-question');
                            }
                        });
                    }, 1000);
                }, 1200);
            }

            // IMPORTANT: After all data is populated, make fields readonly
            // Use setTimeout to ensure all async operations complete first
            setTimeout(function () {
                $('.auto-populated-field').each(function () {
                    if ($(this).is('select')) {
                        $(this).prop('disabled', true).removeAttr('data-edit-token');
                    } else {
                        $(this).prop('readonly', true).removeAttr('data-edit-token');
                    }
                });

                // Also make CKEditor readonly
                if (CKEDITOR.instances.editor) {
                    CKEDITOR.instances.editor.setReadOnly(true);
                }
            }, 2000); // Wait for all data to populate including questions
        }

        /**
         * =====================================================================
         * VALIDATION FUNCTIONS
         * =====================================================================
         */

        // Form validation function
        function validateForm() {
            let isValid = true;
            document.querySelectorAll("#myForm4 input[required], #myForm4 select[required], #myForm4 textarea[required]")
                .forEach((el) => {
                    if (!el.value.trim()) {
                        isValid = false;
                        el.classList.add("is-invalid");
                    } else {
                        el.classList.remove("is-invalid");
                    }
                });
            return isValid;
        }

        // Age validation (min age)
        document.getElementById("minAge").addEventListener("input", function (e) {
            e.preventDefault();
            let age = parseInt(this.value);
            if (age < 18 || age > 47) {
                this.setCustomValidity("आयु 18 से 47 के बीच होनी चाहिए!");
                this.classList.add("is-invalid");
            } else {
                this.setCustomValidity("");
                this.classList.remove("is-invalid");
            }
            this.reportValidity();
        });

        // Age validation (max age)
        document.getElementById("maxAge").addEventListener("input", function (e) {
            e.preventDefault();
            let age = parseInt(this.value);
            if (age < 18 || age > 47) {
                this.setCustomValidity("आयु 18 से 47 के बीच होनी चाहिए!");
                this.classList.add("is-invalid");
            } else {
                this.setCustomValidity("");
                this.classList.remove("is-invalid");
            }
            this.reportValidity();
        });

        // Show/hide district field based on category selection
        function toggleDistrictField() {
            let categoryType = document.getElementById("categoryType").value;
            let districtField = document.getElementById("districtField");
            let districtSelect = document.getElementById("distNames");

            if (categoryType === "2") {
                districtField.style.display = "block";
                document.getElementById("distNames").setAttribute("required", "true");
            } else {
                districtField.style.display = "none";
                document.getElementById("distNames").removeAttribute("required");

                // Clear selected values
                if (districtSelect.multiple) {
                    for (let i = 0; i < districtSelect.options.length; i++) {
                        districtSelect.options[i].selected = false;
                    }
                }

                if ($.fn.select2) {
                    $('#distNames').val(null).trigger('change');
                }
            }
        }

        // Format date utility function
        function formatDate(isoDate) {
            let dateParts = isoDate.split("-"); // Split YYYY-MM-DD
            return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`; // Rearrange to DD-MM-YYYY
        }

        /**
         * =====================================================================
         * UI INTERACTION HANDLERS
         * =====================================================================
         */

        // Experience fields management
        let orgTypeCount = 1;

        // Add new organization type entry
        $('#add_org_type').on('click', function () {
            orgTypeCount++;
            $('#org_type_container').append(`
                                                                                                                                <div class="org-type-entry d-flex align-items-center mb-2" id="org_type_${orgTypeCount}">
                                                                                                                                    <select class="form-control org-type-select me-2" name="org_types[${orgTypeCount}]" >
                                                                                                                                        <option value="">-- संगठन प्रकार चुनें --</option>
                                                                                                                                        @foreach ($org_types as $org_type)
                                                                                                                                            <option value="{{ $org_type->org_id }}">{{ $org_type->org_type }}</option>
                                                                                                                                        @endforeach
                                                                                                                                    </select>
                                                                                                                                    <input type="number" class="form-control min-experience me-2" name="experience[${orgTypeCount}]" placeholder="न्यूनतम अनुभव (वर्षों में)" >
                                                                                                                                    <button type="button" class="btn btn-danger remove-org-type" data-id="${orgTypeCount}">हटाएं</button>
                                                                                                                                </div>
                                                                                                                            `);
        });

        // Remove organization type entry
        $(document).on('click', '.remove-org-type', function () {
            const id = $(this).data('id');
            $(`#org_type_${id}`).remove();
        });

        // Validate organization type fields
        $(document).on('change', '.org-type-select', function () {
            const id = $(this).closest('.org-type-entry').attr('id').split('_')[2];
            const selectedOrgType = $(this).val();

            if (selectedOrgType && !$(`#org_type_${id} .min-experience`).val()) {
                $(`#org_type_${id} .min-experience`).attr('required', true);
            }
        });

        // Subject management
        $('#add_subject').on('click', function () {
            let subject = $('#subject_select').val();
            let subjectText = $('#subject_select option:selected').text();
            let id = 'subject_' + Date.now();

            if (subject && !$('#subject_list input[value="' + subject + '"]').length) {
                $('#subject_list').append(`
                                                                                                                                    <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                                                                                                                                        <input type="hidden" name="subjects[]" value="${subject}">
                                                                                                                                        <span>${subjectText}</span>
                                                                                                                                        <button type="button" class="btn-close btn-close-white ms-2 remove-subject" data-id="${id}" aria-label="Close"></button>
                                                                                                                                    </div>
                                                                                                                                `);
                $('#subject_select').val('');
            }
        });

        // Remove subject
        $(document).on('click', '.remove-subject', function () {
            let targetId = $(this).data('id');
            $('#' + targetId).remove();
        });

        // Skills management
        $('#add_skill').on('click', function () {
            let skill = $('#skill_select').val();
            let skillText = $('#skill_select option:selected').text();
            let id = 'skill_' + Date.now();

            if (skill && !$('#skill_list input[value="' + skill + '"]').length) {
                $('#skill_list').append(`
                                                                                                                                    <div class="badge bg-info p-2 d-flex align-items-center" id="${id}">
                                                                                                                                        <input type="hidden" name="skills[]" value="${skill}">
                                                                                                                                        <span>${skillText}</span>
                                                                                                                                        <button type="button" class="btn-close btn-close-white ms-2 remove-skill" data-id="${id}" aria-label="Close"></button>
                                                                                                                                    </div>
                                                                                                                                `);
                $('#skill_select').val('');
            }
        });

        // Remove skill
        $(document).on('click', '.remove-skill', function () {
            let targetId = $(this).data('id');
            $('#' + targetId).remove();
        });

        // Toggle caste-wise / total vacancy
        $('#toggleCasteVacancy').on('change', function () {
            if ($(this).is(':checked')) {
                $('#casteVacancyDiv').show();
                $('#totalVacancyDiv').hide();
                $('#total_vacancy').val('');
            } else {
                $('#casteVacancyDiv').hide();
                $('#totalVacancyDiv').show();
                $('.caste-toggle').prop('checked', false);
                $('.caste-vacancy').prop('disabled', true).val('');
                $('.caste-toggle').prop('checked', false);
                $('.caste-vacancy').prop('disabled', true).val('');
            }
        });

        // Enable/disable vacancy input per caste
        $(document).on('change', '.caste-toggle', function () {
            const input = $(this).closest('.form-check').find('.caste-vacancy');
            input.prop('disabled', !$(this).is(':checked'));
            if (!$(this).is(':checked')) {
                input.val('');
            }
        });

        // File upload preview
        document.getElementById("fileUpload").addEventListener("change", function (event) {
            let file = event.target.files[0];
            let allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif", "application/pdf"];
            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: "error",
                        title: "⚠ गलत फ़ाइल प्रकार!",
                        text: "❌ केवल छवियाँ (JPG, PNG) और PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "ठीक है"
                    });
                    event.target.value = "";
                    return;
                }
                let fileURL = URL.createObjectURL(file);
                let viewButton = document.getElementById("viewButton");
                let docViewer = document.getElementById("docViewer");
                let imagePreview = document.getElementById("imagePreview");

                viewButton.classList.remove("d-none");

                if (file.type.startsWith("image/")) {
                    imagePreview.src = fileURL;
                    imagePreview.classList.remove("d-none");
                    docViewer.classList.add("d-none");
                } else {
                    docViewer.src = fileURL;
                    docViewer.classList.remove("d-none");
                    imagePreview.classList.add("d-none");
                }
            }
        });

        /**
         * =====================================================================
         * AJAX REQUESTS
         * =====================================================================
         */

        // Load subjects based on qualification
        function loadSubjects(qualificationId, callback = null) {
            let url = $('#minQualification').data('subject-url') + '/' + qualificationId;

            $.ajax({
                url: url,
                type: 'GET',
                success: function (subjects) {
                    let options = '<option value="">-- विषय चुनें --</option>';
                    subjects.forEach(subject => {
                        options +=
                            `<option value="${subject.subject_id}">${subject.subject_name}</option>`;
                    });
                    $('#subject_select').html(options);
                    if (typeof callback === "function") callback();
                },
                error: function () {
                    $('#subject_select').html('<option value="">कोई विषय नहीं मिला</option>');
                }
            });
        }

        // Qualification change handler
        $('#minQualification').change(function () {
            let qualificationId = $(this).val();
            if (qualificationId) {
                $('#subject_list').empty();
                loadSubjects(qualificationId);
            }
        });

        // Load parent questions on page load
        $(document).ready(function () {
            let loadedChildQuestions = {};

            $.ajax({
                url: "/admin/get-questions",
                type: "GET",
                dataType: "json",
                beforeSend: function () {
                    $("#questionsContainer").html('<p>प्रश्न लोड हो रहे हैं...</p>');
                },
                success: function (response) {
                    let html = "";
                    if (response.length > 0) {
                        response.forEach(question => {
                            html += `
                                                                                                                                                <div class="question-box parent-question-wrapper" id="parentQuestionWrapper_${question.ques_ID}" style="display:block;">
                                                                                                                                                    <input type="checkbox"
                                                                                                                                                        name="questions[]"
                                                                                                                                                        value="${question.ques_ID}"
                                                                                                                                                        class="parent-question-checkbox"
                                                                                                                                                        data-question-id="${question.ques_ID}">
                                                                                                                                                    <label class="fw-normal">${question.ques_name}</label>
                                                                                                                                                    <div class="child-questions-container" id="childQuestionsOf_${question.ques_ID}">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            `;
                        });
                        $("#questionsContainer").html(html);
                    } else {
                        $("#questionsContainer").html('<p>कोई प्रश्न उपलब्ध नहीं है।</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Initial AJAX Error:", status, error);
                    $("#questionsContainer").html(
                        '<p class="text-danger">प्रश्न लोड करने में त्रुटि। कृपया पुनः प्रयास करें।</p>'
                    );
                }
            });
        });

        // Load child questions on parent selection
        $('#questionsContainer').on('change', '.parent-question-checkbox', function () {
            const parentQuestionId = $(this).data('question-id');
            const isChecked = $(this).is(':checked');
            const childQuestionsContainer = $(`#childQuestionsOf_${parentQuestionId}`);

            if (isChecked) {
                $.ajax({
                    url: `/admin/get-questions?parentId=${parentQuestionId}`,
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let html = "";
                        if (response.length > 0) {
                            response.forEach(question => {
                                html += `
                                                                                                                                                    <div id="childQuestion_${question.ques_ID}" class="question-box child-question-item parent-${parentQuestionId}-child">
                                                                                                                                                        <input type="checkbox" name="questions[]" value="${question.ques_ID}" class="child-question-checkbox">
                                                                                                                                                        <label class="fw-normal">${question.ques_name}</label>
                                                                                                                                                    </div>
                                                                                                                                                `;
                            });
                            childQuestionsContainer.html(html);
                            console.log(`Questions loaded for parent ID: ${parentQuestionId}`);
                        } else {
                            console.log(`No child questions found for parent ID: ${parentQuestionId}`);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error for child questions:", status, error);
                        childQuestionsContainer.html(
                            '<p class="text-danger">संबंधित प्रश्न लोड करने में त्रुटि।</p>');
                    }
                });
            } else {
                childQuestionsContainer.empty();
                console.log(`Questions removed for parent ID: ${parentQuestionId}`);
            }
        });

        // Area change handler - load cities or gram panchayats
        $('#master_area').on('change', function () {
            toggleAreaSections();
        });

        // City change handler - load wards
        $('#city_name').on('change', function () {
            const cityName = $(this).val();
            const areaName = $('#master_area').val();

            // Reset ward field
            $('#wardName').hide();
            $('#ward_name').prop('required', false).val(null);

            if (areaName === 'Urban' && cityName) {
                $.ajax({
                    url: `/admin/get-ward-or-block/${areaName}?city=${encodeURIComponent(cityName)}`,
                    type: 'GET',
                    success: function (response) {
                        $('#wardName').show();
                        $('#ward_name').prop('required', true);

                        // Clear existing options first
                        $('#ward_name').html('');

                        response.forEach(function (item) {
                            $('#ward_name').append(
                                `<option value="${item.ward_no}">${item.ward_name} (${item.ward_no})</option>`
                            );
                        });
                    },
                    error: function () {
                        alert('वार्ड लोड करने में समस्या हुई।');
                    }
                });
            }
        });

        // GP change handler - load villages
        $('#gp_name').on('change', function () {
            const gpCode = $(this).val();
            const areaName = $('#master_area').val();

            // Reset village field
            $('#villageName').hide();
            $('#village_name').prop('required', false).val(null);

            if (areaName === 'Rural' && gpCode) {
                $.ajax({
                    url: `/admin/get-ward-or-block/${areaName}?gp=${encodeURIComponent(gpCode)}`,
                    type: 'GET',
                    success: function (response) {
                        $('#villageName').show();
                        $('#village_name').prop('required', true);

                        // Clear existing options first
                        $('#village_name').html('');

                        response.forEach(function (item) {
                            $('#village_name').append(
                                `<option value="${item.village_code}">${item.village_name_hin}</option>`
                            );
                        });
                    },
                    error: function () {
                        alert('ग्राम लोड करने में समस्या हुई।');
                    }
                });
            }
        });

        /**
         * =====================================================================
         * FORM SUBMISSION
         * =====================================================================
         */

        // Form submit handler
        $("#myForm4").submit(function (e) {
            e.preventDefault();

            // Validate form
            let isCasteMode = $('#toggleCasteVacancy').is(':checked');
            let isValid = true;
            let errorMsg = "";
            const areaName = $('#master_area').val();

            if (areaName === 'Rural') {
                const groups = $('#locationGroupsContainer .location-group');
                let totalVacancySum = 0;
                if (!groups.length) {
                    isValid = false;
                    errorMsg += "कम से कम एक ग्राम पंचायत/ग्राम प्रविष्टि जोड़ें।\n";
                }

                groups.each(function () {
                    const gp = $(this).find('.gp-select').val();
                    const villages = $(this).find('.village-select').val();
                    const vacancyVal = Number($(this).find('.total-vacancy-input').val());
                    const janmanVal = $(this).find('input[type="radio"]:checked').val();

                    if (!gp || !villages || !villages.length || !vacancyVal || Number(vacancyVal) <= 0 || janmanVal === undefined) {
                        isValid = false;
                    }

                    if (!isNaN(vacancyVal) && vacancyVal > 0) {
                        totalVacancySum += vacancyVal;
                    }
                });

                if (!isValid) {
                    errorMsg += "कृपया सभी ग्रामीण प्रविष्टियों के लिए ग्राम पंचायत, ग्राम, रिक्तियाँ और जनमन चयन करें।\n";
                } else {
                    // Keep an aggregate total for backend compatibility
                    $('#total_vacancy').val(totalVacancySum);
                }
            } else if (areaName === 'Urban') {
                const groups = $('#urbanGroupsContainer .urban-group');
                let totalVacancySum = 0;

                if (!groups.length) {
                    isValid = false;
                    errorMsg += "कम से कम एक शहर/वार्ड प्रविष्टि जोड़ें।\n";
                }

                groups.each(function () {
                    const city = $(this).find('.urban-city-select').val();
                    const wards = $(this).find('.urban-ward-select').val();
                    const vacancyVal = Number($(this).find('.urban-total-vacancy').val());

                    if (!city || !wards || !wards.length || !vacancyVal || Number(vacancyVal) <= 0) {
                        isValid = false;
                    }

                    if (!isNaN(vacancyVal) && vacancyVal > 0) {
                        totalVacancySum += vacancyVal;
                    }
                });

                if (!isValid) {
                    errorMsg += "कृपया सभी शहरी प्रविष्टियों के लिए शहर, वार्ड और रिक्तियाँ भरें।\n";
                } else {
                    $('#total_vacancy').val(totalVacancySum);
                }
            } else {
                if ($('#total_vacancy').val().trim() === '') {
                    isValid = false;
                    errorMsg += "कुल रिक्तियाँ भरना अनिवार्य है।\n";
                }

                if (!$('input[name="isJanmanArea"]:checked').length) {
                    isValid = false;
                    errorMsg += "जनमन क्षेत्र चुनना अनिवार्य है।\n";
                }
            }

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'कृपया ध्यान दें ',
                    text: errorMsg,
                    confirmButtonText: 'ठीक है'
                });
                return;
            }

            // Disable button and prepare form data
            $('#submitBtn').attr('disabled', true);
            validateForm();

            // Temporarily enable all readonly/disabled fields before form submission
            // This ensures their values are included in the FormData
            const readonlyFields = [];
            const disabledFields = [];
            const disabledQuestions = [];

            // Store and enable readonly fields
            $('.auto-populated-field').each(function () {
                if ($(this).is('select') && $(this).prop('disabled')) {
                    disabledFields.push($(this));
                    $(this).prop('disabled', false);
                } else if ($(this).prop('readonly')) {
                    readonlyFields.push($(this));
                    $(this).prop('readonly', false);
                }
            });

            // Store and enable disabled questions (including both checked and unchecked)
            $('input[name="questions[]"]').each(function () {
                if ($(this).prop('disabled')) {
                    disabledQuestions.push($(this));
                    $(this).prop('disabled', false);
                }
            });

            let formData = new FormData(this);
            formData.append("rules", CKEDITOR.instances.editor.getData());

            // Restore readonly/disabled state immediately after FormData is created
            readonlyFields.forEach(function (field) {
                field.prop('readonly', true);
            });
            disabledFields.forEach(function (field) {
                field.prop('disabled', true);
            });
            disabledQuestions.forEach(function (field) {
                field.prop('disabled', true);
            });

            // Submit form via AJAX
            $.ajax({
                url: "{{ url('/admin/upload-post') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    let messages = [
                        " पोस्ट सफलतापूर्वक दर्ज की गई!",
                        " आपकी पोस्ट जोड़ दी गई है!",
                        " पोस्ट अपलोड हो चुकी है, धन्यवाद!",
                        " आपका डेटा सफलतापूर्वक सहेजा गया!",
                        " पोस्ट जोड़ दी गई, आगे बढ़ें!"
                    ];
                    let randomMessage = messages[Math.floor(Math.random() * messages.length)];

                    Swal.fire({
                        title: "सफलता! ",
                        text: randomMessage,
                        icon: "success",
                        confirmButtonText: "ठीक है"
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errorMessage = 'कृपया सभी आवश्यक जानकारी भरें।';

                        // Check for direct message (from middleware)
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        // Check for validation errors
                        else if (xhr.responseJSON && xhr.responseJSON.errors && typeof xhr.responseJSON
                            .errors === 'object') {
                            var errors = xhr.responseJSON.errors;
                            var errorValues = Object.values(errors);

                            if (errorValues && errorValues.length > 0) {
                                errorMessage = '';
                                for (var key in errors) {
                                    if (errors.hasOwnProperty(key)) {
                                        errorMessage += errors[key].join('<br>') + '<br>';
                                    }
                                }
                            }
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'ध्यान दें',
                            html: errorMessage,
                            confirmButtonText: 'ठीक है'
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ध्यान दें',
                            text: 'कुछ गलत हो गया। कृपया दोबारा प्रयास करें।',
                            confirmButtonText: 'ठीक है'
                        });
                    }
                    $('#submitBtn').attr('disabled', false);
                }
            });
        });
    </script>
@endsection