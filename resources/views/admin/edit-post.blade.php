@extends('layouts.dahboard_layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" />

    <style>
        #cke_notifications_area_editor {
            display: none !important;
        }

        .question-box {
            display: block;
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
            <h5 class="fw-bold">पोस्ट</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/admin-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/show-posts') }}">पोस्ट की सूची</a></li>
                    <li class="breadcrumb-item active">
                        {{ isset($readonly) && $readonly ? 'पोस्ट देखें' : 'पोस्ट संपादित करें' }}</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            @if (isset($readonly) && $readonly)
                                <i class="bi bi-eye"> </i>पोस्ट देखें
                            @else
                                <i class="bi bi-pencil"> </i>पोस्ट संपादित करें
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row container">
                            <form id="myForm4" method="post" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="applicant_id_tab4" id="applicant_id_tab4">
                                <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                                <input type="hidden" name="stepCount" value="4" />
                                <input type="hidden" name="app_id"
                                    value="{{ isset($data['applicant_details']) ? $data['applicant_details']->ID : '' }}" />
                                <div class="col-md-12"><br>
                                    <div class="field_wrapper" id="inputFieldsContainer">
                                        <div class="row">

                                            <div class="col-md-6 text-left mt-3">
                                                <label for="advertisementName">विज्ञापन<font color="red">*</font>
                                                </label>
                                                <select id="advertisementName" class="form-select" name="Advertisement_ID"
                                                    onchange="toggleStartEndDateField()" required>
                                                    <!-- Default "Select" option -->
                                                    <option value="" disabled selected>-- चयन करें --</option>
                                                    <!-- Loop through advertisements and create options -->
                                                    @foreach ($advertisements as $advertisement)
                                                        <option data-advertisement='@json($advertisement)'
                                                            value="{{ $advertisement->Advertisement_ID }}"
                                                            {{ isset($post) && $post->Advertisement_ID == $advertisement->Advertisement_ID ? 'selected' : '' }}>
                                                            {{ $advertisement->Advertisement_Title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('Advertisement_ID')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>



                                            <div class="col-md-3 text-left mt-3">
                                                <label for="master_area">क्षेत्र <font color="red">*</font></label>
                                                <select name="master_area" id="master_area" class="form-select" required>
                                                    <option value="">-- चुनें --</option>
                                                    @foreach ($master_areas as $area)
                                                        <option value="{{ $area->area_name }}"
                                                            {{ isset($post) && $post->fk_area_id == $area->area_id ? 'selected' : '' }}>
                                                            {{ $area->area_name_hi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('master_area')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">

                                            <!-- Legacy single selects kept hidden/disabled for backward compatibility -->
                                            <div class="col-md-4 text-left mt-3" id="nagarName" style="display:none;">
                                                <label for="city_name">शहर का चयन करें</label>
                                                <select name="city_name" id="city_name" class="form-select" disabled>
                                                    <option value="">-- चुनें --</option>
                                                </select>
                                            </div>

                                            <div class="col-md-8 text-left mt-3" id="wardName" style="display: none;">
                                                <label for="ward_name">वार्ड का चयन करें</label>
                                                <select name="ward_name[]" id="ward_name" class="form-select" multiple
                                                    disabled></select>
                                            </div>

                                            <div class="col-md-4 text-left mt-3" id="gpName" style="display:none;">
                                                <label for="gp_name">ग्राम पंचायत का चयन करें</label>
                                                <select name="gp_name" id="gp_name" class="form-select" disabled>
                                                    <option value="">-- चुनें --</option>
                                                </select>
                                            </div>

                                            <div class="col-md-8 text-left mt-3" id="villageName" style="display:none;">
                                                <label for="village_name">गाँव का चयन करें</label>
                                                <select name="village_name[]" id="village_name" class="form-select" multiple
                                                    disabled></select>
                                            </div>

                                            <!-- Urban repeatable groups -->
                                            <div class="row mt-2" id="urbanGroupsWrapper"
                                                style="@if ($post->fk_area_id == 1) display:none; @else display:block; @endif">
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
                                                        <h6 class="mb-2">शहरी स्थान <span class="urban-location-number"></span></h6>
                                                        <button type="button" class="btn btn-sm btn-outline-danger removeUrbanGroup">
                                                            हटाएं
                                                        </button>
                                                    </div>
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-4">
                                                            <label class="form-label">शहर का चयन करें <span class="text-danger">*</span></label>
                                                            <select class="form-select urban-city-select"
                                                                name="urban_groups[__INDEX__][city_nnn_code]" required>
                                                                <option value="">-- चुनें --</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">वार्ड का चयन करें <span class="text-danger">*</span></label>
                                                            <select class="form-select urban-ward-select"
                                                                name="urban_groups[__INDEX__][ward_codes][]" multiple required></select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">कुल रिक्त पदों की संख्या <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control urban-total-vacancy"
                                                                name="urban_groups[__INDEX__][total_vacancy]" min="1"
                                                                placeholder="रिक्तियाँ" required>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <label class="form-label d-block mb-1">क्या चयनित क्षेत्र जनमन क्षेत्र हैं? <span class="text-danger">*</span></label>
                                                            <div class="d-flex align-items-center gap-4">
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input"
                                                                        name="urban_groups[__INDEX__][is_janman_area]" value="1" required>
                                                                    <label class="form-check-label">हाँ</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input"
                                                                        name="urban_groups[__INDEX__][is_janman_area]" value="0" required>
                                                                    <label class="form-check-label">नहीं</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Rural repeatable groups (matches upload page) -->
                                            <div class="row mt-2" id="ruralGroupsWrapper"
                                                style="@if ($post->fk_area_id == 1) display:block; @else display:none; @endif">
                                                <div class="col-md-12 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">ग्राम पंचायत / गाँव / रिक्तियाँ</h6>
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
                                                <div class="location-group border rounded p-3 mt-3"
                                                    data-index="__INDEX__">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-2">स्थान <span class="location-number"></span>
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
                                                            <label class="form-label">गाँव का चयन करें <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select village-select"
                                                                name="location_groups[__INDEX__][village_codes][]" multiple
                                                                required></select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">कुल रिक्त पदों की संख्या <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" class="form-control total-vacancy-input"
                                                                name="location_groups[__INDEX__][total_vacancy]"
                                                                min="1" placeholder="कुल रिक्तियाँ दर्ज करें"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">क्या चयनित क्षेत्र जनमन क्षेत्र हैं?
                                                                <span class="text-danger">*</span></label>
                                                            <div class="d-flex align-items-center gap-4">
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input"
                                                                        name="location_groups[__INDEX__][is_janman_area]"
                                                                        value="1" required>
                                                                    <label class="form-check-label">हाँ</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input"
                                                                        name="location_groups[__INDEX__][is_janman_area]"
                                                                        value="0" required>
                                                                    <label class="form-check-label">नहीं</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <hr class="mt-3">

                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="alert alert-info d-flex align-items-center">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    <div>
                                                        <strong>सूचना:</strong> यह जानकारी मास्टर पोस्ट से स्वतः भरी गई है
                                                        और संपादित नहीं की जा सकती।
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7 text-left">

                                                <label for="postName">पोस्ट शीर्षक<font color="red">*</font></label>
                                                @if ($post->title)
                                                    <input type="text" id="postName"
                                                        class="form-control auto-populated-field" name="post_name"
                                                        placeholder="पद का नाम" value="{{ $post->title }}" required
                                                        readonly>
                                                @endif
                                                @error('post_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 text-left" style="display: none;">
                                                <label for="categoryType">श्रेणी<font color="red">*</font></label>
                                                <select id="categoryType" class="form-select" name="fk_category_id"
                                                    required onchange="toggleDistrictField()">
                                                    <!-- Default "Select" option -->
                                                    <option value="" disabled selected>-- चयन करें --</option>
                                                    <!-- Loop through advertisements and create options -->
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->cat_id }}"
                                                            {{ $category->cat_id == $post->cat_id ? 'selected' : '' }}>
                                                            {{ $category->cat_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('fk_category_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div class="col-md-6 text-left" id="districtField"
                                                style="@if ($post->cat_id == 1) display: none; @endif">
                                                <label for="distNames">जिला चुनें<font color="red">*</font></label>
                                                <select id="distNames" class="form-select select2"
                                                    name="fk_district_id[]" multiple>
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->District_Code_LGD }}"
                                                            {{ in_array($district->District_Code_LGD, $selected_districts) ? 'selected' : '' }}>
                                                            {{ $district->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" id="selectedDistricts" name="selectedDistricts">
                                            </div>
                                        </div>


                                        {{-- new changes --}}
                                        <div class="row">
                                            {{-- Toggle to enable caste-wise vacancy --}}
                                            {{-- <div class="col-md-12 mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="toggleCasteVacancy" {{ $isCasteWise ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="toggleCasteVacancy">
                                                        क्या आप जाति-वार रिक्तियाँ बाँटना चाहते हैं?
                                                    </label>
                                                </div>
                                            </div> --}}

                                            {{-- Caste-wise inputs (from DB) --}}
                                            <div class="col-md-12 mt-3" id="casteVacancyDiv"
                                                style="{{ $isCasteWise ? '' : 'display:none;' }}">
                                                <label class="">जाति-वार रिक्तियाँ</label>

                                                @foreach ($caste_category as $item)
                                                    @php
                                                        $checked = isset($selected_castes[$item->caste_id]);
                                                        $value = $selected_castes[$item->caste_id] ?? '';
                                                    @endphp
                                                    <div class="form-check mb-2 row align-items-center">
                                                        <div class="col-md-2">
                                                            <input class="form-check-input caste-toggle" type="checkbox"
                                                                name="cast_category[]" value="{{ $item->caste_id }}"
                                                                id="{{ strtolower($item->caste_id) }}"
                                                                {{ $checked ? 'checked' : '' }}>
                                                            <label class="form-check-label ms-1"
                                                                for="{{ strtolower($item->caste_id) }}">
                                                                {{ $item->caste_title }}
                                                            </label>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <input type="number" name="vacancy[{{ $item->caste_id }}]"
                                                                class="form-control caste-vacancy"
                                                                placeholder="रिक्त पदों की संख्या"
                                                                value="{{ $value }}"
                                                                {{ $checked ? '' : 'disabled' }}>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="row">

                                            {{-- Total vacancy input --}}
                                            <div class="col-md-3 mt-3" id="totalVacancyDiv"
                                                {{-- style="{{ $isCasteWise ? 'display:none;' : '' }} --}}
                                                 style="display:none;">
                                                <label for="total_vacancy">कुल रिक्त पदों की संख्या</label>
                                                <input type="number" name="total_vacancy" id="total_vacancy"
                                                    class="form-control" placeholder="कुल रिक्तियाँ दर्ज करें"
                                                    value="{{ $totalVacancy }}">
                                            </div>

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="minAge">न्यूनतम आयु<font color="red">*</font></label>
                                                <input type="number" id="minAge"
                                                    class="form-control auto-populated-field" name="min_age"
                                                    min="18" max="47"
                                                    placeholder="18 से 47 के बीच आयु दर्ज करें"
                                                    value="{{ old('max_age', $post->min_age) }}" required readonly>

                                                @error('min_age')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- new changes end --}}

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="maxAge">अधिकतम आयु<font color="red">*</font></label>
                                                <input type="number" id="maxAge"
                                                    class="form-control auto-populated-field" name="max_age"
                                                    min="18" max="47" required
                                                    placeholder="18 से 47 के बीच आयु दर्ज करें"
                                                    value="{{ old('max_age', $post->max_age) }}" readonly>

                                                @error('max_age')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3 text-left mt-3">
                                                <label for="maxAge">आयु में छूट के बाद अधिकतम आयु </label>
                                                <input type="number" id="maxAgeRelax"
                                                    class="form-control auto-populated-field" name="max_age_relax"
                                                    min="18" max="47" 
                                                    placeholder="18 से 47 के बीच आयु दर्ज करें"
                                                    value="{{ old('max_age', $post->max_age_relax) }}" readonly>

                                                @error('max_age_relax')
                                                    <span class="text-danger is-invalid">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            {{-- <div class="col-md-3 text-left mt-3" id="minQualificationField">
                                                <label for="minQualification">न्यूनतम योग्यता<font color="red">*</font>
                                                </label>
                                                <select id="minQualification" class="form-select"
                                                    name="min_Qualification" required>
                                                    <!-- Default "Select" option -->
                                                    <option value="" disabled selected>--- चुनें ---</option>
                                                    <!-- Loop through advertisements and create options -->
                                                    @foreach ($qualifications as $qualification)
                                                        <option value="{{ $qualification->Quali_ID }}"
                                                            {{ $post->Quali_ID == $qualification->Quali_ID ? 'selected' : '' }}>
                                                            {{ $qualification->Quali_Name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> --}}

                                            <div class="col-md-6 text-left mt-3" id="minQualificationField">
                                                <label for="minQualification">न्यूनतम योग्यता<font color="red">*</font>
                                                </label>
                                                <select id="minQualification" class="form-select auto-populated-field"
                                                    name="min_Qualification"
                                                    data-subject-url="{{ url('/admin/get-subjects-by-qualification') }}"
                                                    required>
                                                    <option value="" disabled selected>--- चुनें ---</option>
                                                    @foreach ($qualifications as $qualification)
                                                        @if ($qualification->Quali_ID != 1 && $qualification->Quali_ID != 2)
                                                            <option value="{{ $qualification->Quali_ID }}"
                                                                {{ $post->Quali_ID == $qualification->Quali_ID ? 'selected' : '' }}>
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
                                                <select id="subject_select" class="form-select auto-populated-field">
                                                    <option value="">-- विषय चुनें --</option>
                                                    @foreach ($subjects as $subject)
                                                        <option value="{{ $subject->subject_id }}"
                                                            {{ in_array($subject->subject_id, $selected_subjects) ? 'selected' : '' }}>
                                                            {{ $subject->subject_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" id="add_subject"
                                                    class="btn btn-success">जोड़ें</button>
                                            </div>
                                            <!-- Subjects List -->
                                            <div id="subject_list" class="mt-2 d-flex flex-wrap gap-2">
                                                <!-- Selected subjects will appear here -->
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="skill_select">कौशल (Skills) जोड़ें</label>

                                            <div class="input-group mb-2">
                                                <select id="skill_select" class="form-select auto-populated-field">
                                                    <option value="">-- कौशल चुनें --</option>
                                                    @foreach ($skills as $skill)
                                                        <option value="{{ $skill->skill_id }}">
                                                            {{ $skill->skill_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" id="add_skill"
                                                    class="btn btn-success">जोड़ें</button>
                                            </div>

                                            <!-- Skills List -->
                                            <div id="skill_list" class="mt-2 d-flex flex-wrap gap-2">
                                                <!-- Selected skills will be added here -->
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="org_type_input">न्यूनतम अनुभव</label>

                                            <div id="org_type_container">
                                                {{-- Existing values --}}
                                                @php $orgIndex = 1; @endphp
                                                @foreach ($selected_organization_map as $map)
                                                    <div class="org-type-entry d-flex align-items-center mb-2"
                                                        id="org_type_{{ $orgIndex }}">
                                                        <select
                                                            class="form-control form-select org-type-select me-2 auto-populated-field"
                                                            name="org_types[{{ $orgIndex }}]" required>
                                                            <option value="">-- संगठन प्रकार चुनें --</option>
                                                            @foreach ($org_types as $org_type)
                                                                <option value="{{ $org_type->org_id }}"
                                                                    {{ $org_type->org_id == $map->fk_organization_type_id ? 'selected' : '' }}>
                                                                    {{ $org_type->org_type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number"
                                                            class="form-control min-experience me-2 auto-populated-field"
                                                            name="experience[{{ $orgIndex }}]"
                                                            value="{{ $map->minimum_experiance_year }}"
                                                            placeholder="न्यूनतम अनुभव (वर्षों में)" required readonly>
                                                        @if ($orgIndex > 1)
                                                            <button type="button" class="btn btn-danger remove-org-type"
                                                                data-id="{{ $orgIndex }}">हटाएं</button>
                                                        @endif
                                                    </div>
                                                    @php $orgIndex++; @endphp
                                                @endforeach

                                                {{-- If no existing values, show one default field without remove button --}}
                                                @if (count($selected_organization_map) == 0)
                                                    <div class="org-type-entry d-flex align-items-center mb-2"
                                                        id="org_type_1">
                                                        <select class="form-control org-type-select me-2"
                                                            name="org_types[1]" required>
                                                            <option value="">-- संगठन प्रकार चुनें --</option>
                                                            @foreach ($org_types as $org_type)
                                                                <option value="{{ $org_type->org_id }}">
                                                                    {{ $org_type->org_type }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" class="form-control min-experience me-2"
                                                            name="experience[1]" placeholder="न्यूनतम अनुभव (वर्षों में)"
                                                            required>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- <button type="button" id="add_org_type" class="btn btn-success mt-3">+ और जोड़ें</button> --}}
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
                                        <label for="rules">नियम और विनियम<font color="red">*</font></label>
                                        <textarea name="rules" id="editor" required>{{ $post->guidelines }}</textarea>
                                        @error('rules')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><br>
                                <div class="col-md-6 col-xs-12 text-left">
                                    <label for="fileUpload">पोस्ट संबंधित फ़ाइलें अपलोड करें (वैकल्पिक)
                                    </label>

                                    <!-- Show existing file if available -->
                                    @if (!empty($file_path))
                                        @php
                                            $post_file_path = asset('uploads/' . $file_path);
                                            // {{ dd($post_file_path) }}
                                            if (config('app.env') == 'production') {
                                                $post_file_path = config('custom.file_point') . $file_path;
                                            }
                                        @endphp
                                        <p>📂 <a data-file="{{ $post_file_path }}" target="_blank" id="existingFile"
                                                style="cursor: pointer;" class="btn-danger">Existing File</a></p>
                                    @endif

                                    <input type="file" name="file" id="fileUpload" class="form-control"
                                        accept=".pdf">
                                    @error('file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <a id="viewButton" class="btn-danger d-none" data-bs-toggle="modal"
                                        data-bs-target="#docModal" style="cursor: pointer;">
                                        View Document
                                    </a>
                                </div>

                                <div class="col-md-12 col-xs-12 text-left my-2">
                                    <label class="form-label">कृपया प्रश्नों का चयन करें:<font color="red">*</font>
                                    </label>
                                    <div id="questionsContainer" class="border rounded p-2">
                                        @foreach ($parentQuestions as $question)
                                            {{-- $allQuestions को $parentQuestions से बदला गया --}}
                                            <div class="question-box parent-question-wrapper"
                                                id="parentQuestionWrapper_{{ $question->ques_ID }}">
                                                <input type="checkbox" name="questions[]"
                                                    value="{{ $question->ques_ID }}" class="parent-question-checkbox"
                                                    data-question-id="{{ $question->ques_ID }}"
                                                    {{ in_array($question->ques_ID, $selectedQuestionIds) ? 'checked' : '' }}>
                                                {{-- Pre-checking --}}
                                                <label class="fw-normal">{{ $question->ques_name }}?</label>
                                                <div class="child-questions-container"
                                                    id="childQuestionsOf_{{ $question->ques_ID }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($errors->has('questions'))
                                        <span class="text-danger mt-2 d-block">{{ $errors->first('questions') }}</span>
                                    @endif
                                </div>

                                <!-- Bootstrap Modal -->
                                <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
                                    id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
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

                                @if (!(isset($readonly) && $readonly))
                                    <div>
                                        <button id="submitBtn" style="float: right;"
                                            class="btn btn-success btn-lg pull-right" type="submit">पोस्ट अपडेट
                                            करें</button>
                                    </div>
                                @else
                                    <div>
                                        <a href="/admin/show-posts" style="float: right;"
                                            class="btn btn-secondary btn-lg pull-right">
                                            <i class="bi bi-arrow-left me-1"></i>वापस जाएं
                                        </a>
                                    </div>
                                @endif
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
        $(document).ready(function() {

            function loadSubjects(qualificationId, callback = null) {
                let url = $('#minQualification').data('subject-url') + '/' + qualificationId;
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(subjects) {
                        let options = '<option value="">-- विषय चुनें --</option>';
                        subjects.forEach(subject => {
                            options +=
                                `<option value="${subject.subject_id}">${subject.subject_name}</option>`;
                        });
                        $('#subject_select').html(options);
                        if (typeof callback === "function") callback();
                    },
                    error: function() {
                        $('#subject_select').html('<option value="">कोई विषय नहीं मिला</option>');
                    }
                });
            }

            $('#minQualification').change(function() {
                let qualificationId = $(this).val();
                if (qualificationId) {
                    $('#subject_list').empty();
                    loadSubjects(qualificationId);
                }
            });

            let preselectedQualification = $('#minQualification').val();
            if (preselectedQualification) {
                loadSubjects(preselectedQualification);
            }
        });

        $(document).ready(function() {
            let loadedChildQuestions = {};

            // ==========================================================
            // जब पेज लोड होता है, तो पहले से चेक किए गए पैरेंट चेकबॉक्स को ढूंढें
            $('.parent-question-checkbox:checked').each(function() {
                const parentQuestionId = $(this).data('question-id');
                const childQuestionsContainer = $(`#childQuestionsOf_${parentQuestionId}`);

                // उस पैरेंट के लिए चाइल्ड प्रश्नों को लोड करने के लिए AJAX कॉल करें
                loadChildQuestions(parentQuestionId, childQuestionsContainer, $(this));
            });


            // ==========================================================
            $('#questionsContainer').on('change', '.parent-question-checkbox', function() {
                // Check if we're in readonly mode
                const isReadonly = @json(isset($readonly) && $readonly);
                if (isReadonly) {
                    // Prevent any changes in readonly mode
                    return false;
                }

                const parentQuestionId = $(this).data('question-id');
                const isChecked = $(this).is(':checked');
                const childQuestionsContainer = $(`#childQuestionsOf_${parentQuestionId}`);

                if (isChecked) {
                    // अगर चेकबॉक्स चेक किया गया है, तो चाइल्ड प्रश्नों को लोड करें
                    loadChildQuestions(parentQuestionId, childQuestionsContainer, $(this));
                } else {
                    // अगर चेकबॉक्स अनचेक किया गया है, तो चाइल्ड प्रश्नों को UI से हटा दें
                    childQuestionsContainer.empty();
                    delete loadedChildQuestions[parentQuestionId];
                    console.log(`Questions removed for parent ID: ${parentQuestionId}`);
                }
            });

            // ==========================================================
            function loadChildQuestions(parentId, containerElement, parentCheckboxElement) {
                $.ajax({
                    url: `/admin/get-questions?parentId=${parentId}`, // parentId को query parameter के रूप में भेजें
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        containerElement.html(
                            // '<p class="text-muted text-sm">संबंधित प्रश्न लोड हो रहे हैं...</p>'
                        );
                    },
                    success: function(response) {
                        let html = "";
                        if (response.length > 0) {
                            const selectedQuestionIds = @json($selectedQuestionIds);

                            response.forEach(question => {
                                const isChildChecked = selectedQuestionIds.includes(question
                                    .ques_ID);
                                const isReadonly = @json(isset($readonly) && $readonly);
                                const disabledAttr = isReadonly ? 'disabled' : '';

                                html += `
                <div id="childQuestion_${question.ques_ID}" class="question-box child-question-item">
                    <input type="checkbox"
                        name="questions[]"
                        value="${question.ques_ID}"
                        ${isChildChecked ? 'checked' : ''}
                        ${disabledAttr}>
                    <label class="fw-normal">${question.ques_name}?</label>
                </div>
            `;
                            });
                            containerElement.html(html);
                            loadedChildQuestions[parentId] = true;
                            console.log(`Questions loaded and pre-checked for parent ID: ${parentId}`);
                        } else {
                            containerElement.html(
                                // '<p class="text-muted text-sm">कोई संबंधित प्रश्न नहीं।</p>'
                            );
                            console.log(`No child questions found for parent ID: ${parentId}`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error for child questions:", status, error);
                        containerElement.html(
                            '<p class="text-danger">संबंधित प्रश्न लोड करने में त्रुटि।</p>');
                    }
                });
            }

            $('#ward_name, #village_name').select2({
                placeholder: '-- चुनें --',
                width: '100%'
            });

            // Rural location groups (match upload page)
            let locationGroupIndex = 0;
            let urbanGroupIndex = 0;
            let cachedGpOptions = [];
            let cachedCityOptions = [];
            let villageCache = {};
            let wardCache = {};

            const isReadonlyMode = @json(isset($readonly) && $readonly);

            const prefillLocationGroups = @json($location_groups ?? []);
            const prefillUrbanGroups = @json($urban_groups ?? []);
            const fallbackGpCodes = @json($gp_nnn_code_array ?? []);
            const fallbackVillages = @json($village_code_array ?? []);
            const fallbackCityCodes = @json($city_code_array ?? []);
            const fallbackWardMatrix = @json($ward_matrix ?? []);
            const fallbackJanman = @json($post->is_janman_area ?? 0);
            const fallbackTotalVacancy = @json($post->total_vacancy ?? null);
            const fallbackTotalVacancyArray = @json($total_vacancy_array ?? []);
            const fallbackJanmanArray = @json($is_janman_area_array ?? []);

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

            function populateGpSelect($select, selectedValue = '') {
                $select.empty();
                $select.append('<option value="">-- चुनें --</option>');
                cachedGpOptions.forEach(function(item) {
                    const option =
                        `<option value="${item.gp_nnn_code}">${item.panchayat_name_hin}</option>`;
                    $select.append(option);
                });
                if (selectedValue) {
                    $select.val(String(selectedValue)).trigger('change');
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
                    success: function(response) {
                        cachedGpOptions = response || [];
                        if (typeof callback === 'function') callback();
                    },
                    error: function() {
                        cachedGpOptions = [];
                        if (typeof callback === 'function') callback();
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

                const handleVillageResponse = function(response) {
                    $villageSelect.empty();
                    $villageSelect.append('<option value="" disabled hidden>ग्राम चुनें</option>');
                    response.forEach(function(item) {
                        $villageSelect.append(
                            `<option value="${item.village_code}">${item.village_name_hin}</option>`
                        );
                    });
                    const cleanedSelection = (preselected || []).filter(Boolean).map(String);
                    if (cleanedSelection.length) {
                        $villageSelect.val(cleanedSelection).trigger('change');
                    } else {
                        // $villageSelect.val(null).trigger('change');
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
                    success: function(response) {
                        villageCache[cacheKey] = response || [];
                        handleVillageResponse(response || []);
                    },
                    error: function() {
                        $villageSelect.html('<option value="">लोड करने में समस्या</option>');
                    }
                });
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

            function populateCitySelect($select, selectedValue = '') {
                $select.empty();
                $select.append('<option value="">-- चुनें --</option>');
                cachedCityOptions.forEach(function(item) {
                    const option = `<option value="${item.std_nnn_code}">${item.nnn_name}</option>`;
                    $select.append(option);
                });
                if (selectedValue) {
                    $select.val(String(selectedValue));
                }
            }

            function fetchCityOptions(callback = null) {
                if (cachedCityOptions.length) {
                    if (typeof callback === 'function') callback();
                    return;
                }

                $.ajax({
                    url: `/admin/get-ward-or-block/Urban`,
                    type: 'GET',
                    success: function(response) {
                        cachedCityOptions = response || [];
                        if (typeof callback === 'function') callback();
                    },
                    error: function() {
                        cachedCityOptions = [];
                        if (typeof callback === 'function') callback();
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

                const handleWardResponse = function(response) {
                    $wardSelect.empty();
                    $wardSelect.append('<option value="" disabled hidden>वार्ड चुनें</option>');
                    response.forEach(function(item) {
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
                    success: function(response) {
                        wardCache[cacheKey] = response || [];
                        handleWardResponse(response || []);
                    },
                    error: function() {
                        $wardSelect.html('<option value="">लोड करने में समस्या</option>');
                    }
                });
            }

            function refreshLocationGroupNumbers() {
                $('#locationGroupsContainer .location-group').each(function(idx) {
                    $(this).find('.location-number').text(idx + 1);
                });
            }

            function toggleRemoveButtons() {
                const groups = $('#locationGroupsContainer .location-group');
                if (groups.length <= 1) {
                    groups.find('.removeLocationGroup').addClass('d-none');
                } else {
                    groups.find('.removeLocationGroup').removeClass('d-none');
                }
            }

            function refreshUrbanGroupNumbers() {
                $('#urbanGroupsContainer .urban-group').each(function(idx) {
                    $(this).find('.urban-location-number').text(idx + 1);
                });
            }

            function toggleUrbanRemoveButtons() {
                const groups = $('#urbanGroupsContainer .urban-group');
                if (groups.length <= 1) {
                    groups.find('.removeUrbanGroup').addClass('d-none');
                } else {
                    groups.find('.removeUrbanGroup').removeClass('d-none');
                }
            }

            function addLocationGroup(prefill = {}) {
                const tmpl = document.getElementById('locationGroupTemplate');
                if (!tmpl) return null;
                const html = tmpl.innerHTML.replace(/__INDEX__/g, locationGroupIndex++);
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
                const tmpl = document.getElementById('urbanGroupTemplate');
                if (!tmpl) return null;
                const html = tmpl.innerHTML.replace(/__INDEX__/g, urbanGroupIndex++);
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

            function hydrateLocationGroups() {
                const groups = (Array.isArray(prefillLocationGroups) ? prefillLocationGroups : []).filter(Boolean);
                let initialGroups = groups;

                if (!initialGroups.length && Array.isArray(fallbackGpCodes) && fallbackGpCodes.length) {
                    // Build one group per GP when only codes were stored
                    initialGroups = fallbackGpCodes.map(function(code, idx) {
                        return {
                            gp_nnn_code: code,
                            village_codes: Array.isArray(fallbackVillages) ? fallbackVillages : [],
                            total_vacancy: (Array.isArray(fallbackTotalVacancyArray) &&
                                    fallbackTotalVacancyArray[idx] !== undefined) ?
                                fallbackTotalVacancyArray[idx] :
                                (fallbackTotalVacancy || ''),
                            is_janman_area: (Array.isArray(fallbackJanmanArray) && fallbackJanmanArray[
                                    idx] !== undefined) ?
                                fallbackJanmanArray[idx] :
                                fallbackJanman
                        };
                    });
                }

                if (!initialGroups.length) {
                    ensureAtLeastOneLocationGroup();
                    return;
                }

                initialGroups.forEach(function(group) {
                    addLocationGroup(group);
                });
                applyReadonly();
            }

            function hydrateUrbanGroups() {
                const groups = (Array.isArray(prefillUrbanGroups) ? prefillUrbanGroups : []).filter(Boolean);
                let initialGroups = groups;

                if (!initialGroups.length && Array.isArray(fallbackCityCodes) && fallbackCityCodes.length) {
                    initialGroups = fallbackCityCodes.map(function(code, idx) {
                        const wardEntry = Array.isArray(fallbackWardMatrix) ? fallbackWardMatrix[idx] ?? fallbackWardMatrix : [];
                        const wardCodes = Array.isArray(wardEntry) ? wardEntry : (wardEntry ? [wardEntry] : []);
                        return {
                            city_nnn_code: code,
                            ward_codes: wardCodes,
                            total_vacancy: (Array.isArray(fallbackTotalVacancyArray) && fallbackTotalVacancyArray[idx] !== undefined)
                                ? fallbackTotalVacancyArray[idx]
                                : (fallbackTotalVacancyArray[0] ?? fallbackTotalVacancy ?? ''),
                            is_janman_area: (Array.isArray(fallbackJanmanArray) && fallbackJanmanArray[idx] !== undefined)
                                ? fallbackJanmanArray[idx]
                                : (fallbackJanmanArray[0] ?? fallbackJanman)
                        };
                    });
                }

                if (!initialGroups.length) {
                    ensureAtLeastOneUrbanGroup();
                    return;
                }

                initialGroups.forEach(function(group) {
                    addUrbanGroup(group);
                });
                applyReadonly();
            }

            function applyReadonly() {
                if (!isReadonlyMode) return;
                $('#addLocationGroup, .removeLocationGroup, #addUrbanGroup, .removeUrbanGroup').hide();
                $('#locationGroupsContainer, #urbanGroupsContainer').find('input, select').prop('disabled', true);
            }

            // Add/remove handlers
            $('#addLocationGroup').on('click', function() {
                addLocationGroup();
                applyReadonly();
            });

            $('#addUrbanGroup').on('click', function() {
                addUrbanGroup();
                applyReadonly();
            });

            $('#locationGroupsContainer').on('click', '.removeLocationGroup', function() {
                $(this).closest('.location-group').remove();
                refreshLocationGroupNumbers();
                toggleRemoveButtons();
                applyReadonly();
            });

            $('#urbanGroupsContainer').on('click', '.removeUrbanGroup', function() {
                $(this).closest('.urban-group').remove();
                refreshUrbanGroupNumbers();
                toggleUrbanRemoveButtons();
                applyReadonly();
            });

            $('#locationGroupsContainer').on('change', '.gp-select', function() {
                const gpCode = $(this).val();
                const $group = $(this).closest('.location-group');
                const $villageSelect = $group.find('.village-select');
                if (gpCode) {
                    loadVillagesForGp(gpCode, $villageSelect);
                } else {
                    $villageSelect.empty();
                }
            });

            $('#urbanGroupsContainer').on('change', '.urban-city-select', function() {
                const cityCode = $(this).val();
                const $group = $(this).closest('.urban-group');
                const $wardSelect = $group.find('.urban-ward-select');
                if (cityCode) {
                    loadWardsForCity(cityCode, $wardSelect);
                } else {
                    $wardSelect.empty();
                }
            });

            // AREA Change: Load City or GP
            const fnAreaToggle = function() {
                const handleArea = function(areaName) {
                    $('#ruralGroupsWrapper').hide();
                    $('#urbanGroupsWrapper').hide();

                    if (areaName === 'Urban') {
                        $('#locationGroupsContainer').empty();
                        locationGroupIndex = 0;
                        fetchCityOptions(function() {
                            $('#urbanGroupsContainer').empty();
                            urbanGroupIndex = 0;
                            hydrateUrbanGroups();
                            refreshUrbanGroupNumbers();
                            toggleUrbanRemoveButtons();
                            applyReadonly();
                            $('#urbanGroupsWrapper').show();
                        });
                    } else if (areaName === 'Rural') {
                        $('#urbanGroupsContainer').empty();
                        urbanGroupIndex = 0;
                        fetchGpOptions(function() {
                            $('#locationGroupsContainer').empty();
                            locationGroupIndex = 0;
                            hydrateLocationGroups();
                            refreshLocationGroupNumbers();
                            toggleRemoveButtons();
                            applyReadonly();
                            $('#ruralGroupsWrapper').show();
                        });
                    } else {
                        $('#locationGroupsContainer, #urbanGroupsContainer').empty();
                        locationGroupIndex = 0;
                        urbanGroupIndex = 0;
                    }
                };

                $(document).on('change', '#master_area', function() {
                    handleArea($(this).val());
                });

                const areaVal = $('#master_area').val();
                handleArea(areaVal);
            };

            const fnCityChange = function() {
                $(document).on('change', '#city_name', function() {
                    const cityName = $(this).val();
                    const areaName = $('#master_area').val();

                    $('#wardName').hide();
                    $('#ward_name').prop('required', false).val('').html(
                        '<option value="-- चुनें --"></option>');

                    if (areaName === 'Urban' && cityName) {
                        $.ajax({
                            url: `/admin/get-ward-or-block/${areaName}?city=${encodeURIComponent(cityName)}`,
                            type: 'GET',
                            success: function(response) {
                                $('#wardName').show();
                                $('#ward_name').prop('required', true).html(
                                    '<option value="">-- चुनें --</option>');

                                const currentWardNoArray = {!! json_encode($ward_no_array ?? []) !!}.map(
                                    String);

                                response.forEach(function(item) {
                                    const itemCode = String(item.ward_no);
                                    const isSelected = currentWardNoArray.includes(
                                            itemCode) ?
                                        'selected' : '';
                                    $('#ward_name').append(
                                        `<option value="${itemCode}" ${isSelected}>${item.ward_name}</option>`
                                    );
                                });
                            }
                        });
                    }
                });

                const cityVal = $('#city_name').val();
                if (cityVal) {
                    $('#city_name').trigger('change');
                }
            };

            // Legacy single GP change handler (kept for backward compatibility)
            const fnGpChange = function() {
                $(document).on('change', '#gp_name', function() {});

                const gpVal = $('#gp_name').val();
                if (gpVal) {
                    $('#gp_name').trigger('change');
                }
            };

            // Initialize area/city/GP handlers after helpers are in scope
            fnAreaToggle();
            fnCityChange();
            fnGpChange();

            // Toggle caste-wise / total vacancy
            $('#toggleCasteVacancy').on('change', function() {
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
            $(document).on('change', '.caste-toggle', function() {
                const input = $(this).closest('.form-check').find('.caste-vacancy');
                input.prop('disabled', !$(this).is(':checked'));
                if (!$(this).is(':checked')) {
                    input.val('');
                }
            });

            // subject section start
            $('#add_subject').on('click', function() {
                let subject = $('#subject_select').val();
                let subjectText = $('#subject_select option:selected').text();
                let id = 'subject_' + Date.now();

                // Check for non-empty and not already added
                if (subject && !$('#subject_list input[value="' + subject + '"]').length) {
                    $('#subject_list').append(`
                        <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                            <input type="hidden" name="subjects[]" value="${subject}">
                            <span>${subjectText}</span>
                            <button type="button" class="btn-close btn-close-white ms-2 remove-subject" data-id="${id}" aria-label="Close"></button>
                        </div>
                    `);

                    $('#subject_select').val(''); // Reset dropdown
                }
            });

            // Remove subject
            $(document).on('click', '.remove-subject', function() {
                let targetId = $(this).data('id');
                $('#' + targetId).remove();
            });

            const selectedSubjects = @json($selected_subjects);

            selectedSubjects.forEach(function(subjectId) {
                let subjectOption = $('#subject_select option[value="' + subjectId + '"]');
                if (subjectOption.length) {
                    let subjectText = subjectOption.text();
                    let id = 'subject_' + Date.now() + '_' + subjectId;

                    $('#subject_list').append(`
                    <div class="badge bg-primary p-2 d-flex align-items-center" id="${id}">
                        <input type="hidden" name="subjects[]" value="${subjectId}">
                        <span>${subjectText}</span>
                        <button type="button" class="btn-close btn-close-white ms-2 remove-subject" data-id="${id}" aria-label="Close"></button>
                    </div>
                `);
                }
            });

            // subject section end


            // skill section start
            $('#add_skill').on('click', function() {
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

                    $('#skill_select').val(''); // Reset dropdown
                }
            });

            $(document).on('click', '.remove-skill', function() {
                let targetId = $(this).data('id');
                $('#' + targetId).remove();
            });

            const selectedSkills = @json($selected_skills);

            selectedSkills.forEach(function(skillId) {
                let skillOption = $('#skill_select option[value="' + skillId + '"]');
                if (skillOption.length) {
                    let skillText = skillOption.text();
                    let id = 'skill_' + Date.now() + '_' + skillId;

                    $('#skill_list').append(`
                        <div class="badge bg-info p-2 d-flex align-items-center" id="${id}">
                            <input type="hidden" name="skills[]" value="${skillId}">
                            <span>${skillText}</span>
                            <button type="button" class="btn-close btn-close-white ms-2 remove-skill" data-id="${id}" aria-label="Close"></button>
                        </div>
                    `);
                }
            });

            // skill section end

            //  org and exp section start
            let orgTypeCount = {{ max(count($selected_organization_map), 1) }};

            $('#add_org_type').on('click', function() {
                orgTypeCount++;

                $('#org_type_container').append(`
                    <div class="org-type-entry d-flex align-items-center mb-2" id="org_type_${orgTypeCount}">
                        <select class="form-control form-select org-type-select me-2" name="org_types[${orgTypeCount}]" required>
                            <option value="">-- संगठन प्रकार चुनें --</option>
                            @foreach ($org_types as $org_type)
                                <option value="{{ $org_type->org_id }}">{{ $org_type->org_type }}</option>
                            @endforeach
                        </select>
                        <input type="number" class="form-control min-experience me-2"
                            name="experience[${orgTypeCount}]"
                            placeholder="न्यूनतम अनुभव (वर्षों में)" required>
                        <button type="button" class="btn btn-danger remove-org-type" data-id="${orgTypeCount}">हटाएं</button>
                    </div>
                `);
            });

            // Remove field
            $(document).on('click', '.remove-org-type', function() {
                const id = $(this).data('id');
                $(`#org_type_${id}`).remove();
            });
            // org and exp section end



            $('#distNames').select2({
                placeholder: "जिला चुनें", // Placeholder text
                allowClear: true, // Allow clearing selected options
                width: "100%" // Full width
            });

            // Ensure optional file upload
            $('#fileUpload').prop('required', false);

            // ajax call
            $('#submitBtn').on('click', function(e) {
                e.preventDefault(); // Prevent default form submission
                // Disable button and show loader

                // caste wise validations start
                let isCasteWise = $('#toggleCasteVacancy').is(':checked');
                let totalVacancy = $('#total_vacancy').val().trim();
                let casteVacancyFilled = false;
                let errorMsg = '';
                let isValid = true;

                if (isCasteWise) {
                    $('.caste-toggle:checked').each(function() {
                        let input = $(this).closest('.form-check').find('.caste-vacancy');
                        if (input.val().trim() !== '') {
                            casteVacancyFilled = true;
                        }
                    });

                    if (!casteVacancyFilled) {
                        errorMsg = 'कृपया कम से कम एक जाति के लिए रिक्ति दर्ज करें।';
                        isValid = false;
                    }
                } else {
                    if (totalVacancy === '') {
                        errorMsg = 'कृपया कुल रिक्त पदों की संख्या दर्ज करें।';
                        isValid = false;
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
                // // caste wise validations end

                $('#submitBtn').attr('disabled', true);


                let postId = $("input[name='post_id']").val(); // Get post ID
                let formData = new FormData($('#myForm4')[0]); // Get form data

                $.ajax({
                    url: "/admin/posts/" + postId + "/update", // Pass post_id in URL
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#submitBtn').prop('disabled', true).text('पोस्ट अपडेट हो रहा...');
                    },
                    success: function(response) {
                        $('#submitBtn').prop('disabled', false).text('पोस्ट अपडेट करें');

                        if (response.status === "no_changes") {
                            Swal.fire({
                                icon: "info",
                                title: "कोई बदलाव नहीं किया गया",
                                text: "आपने कोई भी बदलाव नहीं किया।",
                                timer: 2000
                            });
                        } else if (response.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "सफलतापूर्वक अपडेट हुआ!",
                                text: "पोस्ट अपडेट कर दिया गया है।",
                                timer: 2000
                            }).then(() => {
                                window.location.href =
                                    "{{ route('posts.show') }}"; // Redirect after update
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "अपडेट विफल हुआ!",
                                text: "कुछ गलत हो गया। कृपया दोबारा प्रयास करें।"
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#submitBtn').prop('disabled', false).text('पोस्ट अपडेट करें');

                        if (xhr.status === 422) {
                            // Laravel validation error
                            let errorMessage = 'कृपया सभी आवश्यक जानकारी भरें।';
                            let displayErrors = '';

                            // Check for direct message (from middleware)
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                                Swal.fire({
                                    icon: "warning",
                                    title: "ध्यान दें",
                                    text: errorMessage,
                                    confirmButtonText: "ठीक है"
                                });
                            }
                            // Check for validation errors
                            else if (xhr.responseJSON && xhr.responseJSON.errors && typeof xhr
                                .responseJSON.errors === 'object') {
                                let messages = xhr.responseJSON.errors;
                                let errorValues = Object.values(messages);

                                if (errorValues && errorValues.length > 0) {
                                    errorValues.forEach(function(msgArray) {
                                        if (Array.isArray(msgArray)) {
                                            displayErrors += msgArray.join('<br>') +
                                                '<br>';
                                        } else {
                                            displayErrors += msgArray + '<br>';
                                        }
                                    });

                                    Swal.fire({
                                        icon: "warning",
                                        title: "ध्यान दें",
                                        html: displayErrors,
                                        confirmButtonText: "ठीक है"
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "warning",
                                        title: "ध्यान दें",
                                        text: errorMessage,
                                        confirmButtonText: "ठीक है"
                                    });
                                }
                            }
                            // Fallback for unknown error format
                            else {
                                Swal.fire({
                                    icon: "warning",
                                    title: "ध्यान दें",
                                    text: errorMessage,
                                    confirmButtonText: "ठीक है"
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "ध्यान दें",
                                text: "कुछ गलत हो गया। कृपया दोबारा प्रयास करें।",
                                confirmButtonText: "ठीक है"
                            });
                        }
                    }
                });

            });
        });


        // select age between 18 to 47
        document.getElementById("maxAge").addEventListener("input", function(e) {
            e.preventDefault();
            let age = parseInt(this.value);
            if (age < 18 || age > 47) {
                this.setCustomValidity("आयु 18 से 47 के बीच होनी चाहिए!");
                this.classList.add("is-invalid");
            } else {
                this.setCustomValidity("");
                this.classList.remove("is-invalid");
            }
            this.reportValidity(); //  Yeh turant error message dikhata hai
        });

        // select age between 18 to 47
        document.getElementById("minAge").addEventListener("input", function(e) {
            e.preventDefault();
            let age = parseInt(this.value);
            if (age < 18 || age > 47) {
                this.setCustomValidity("आयु 18 से 47 के बीच होनी चाहिए!");
                this.classList.add("is-invalid");
            } else {
                this.setCustomValidity("");
                this.classList.remove("is-invalid");
            }
            this.reportValidity(); //  Yeh turant error message dikhata hai
        });

        // this function is use for showing the selected doc and existing doc in modal before submiting by a view button
        document.addEventListener("DOMContentLoaded", function() {

            let fileInput = document.getElementById("fileUpload");
            let viewButton = document.getElementById("viewButton");
            let docViewer = document.getElementById("docViewer");
            let imagePreview = document.getElementById("imagePreview");
            let existingFileLink = document.getElementById("existingFile");
            let modalElement = document.getElementById("docModal");

            // Allowed file types
            let allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif", "application/pdf"];

            // New File Upload Logic
            fileInput.addEventListener("change", function(event) {
                let file = event.target.files[0]; // Get selected file
                if (file) {
                    if (!allowedTypes.includes(file.type)) {
                        Swal.fire({
                            icon: "error",
                            title: "⚠ गलत फ़ाइल प्रकार!",
                            text: "❌ केवल छवियाँ (JPG, PNG) और PDF फ़ाइलें ही स्वीकार की जाती हैं।",
                            confirmButtonColor: "#d33",
                            confirmButtonText: "ठीक है"
                        });
                        event.target.value = ""; // Reset file input
                        return;
                    }

                    let fileURL = URL.createObjectURL(file); // Generate temporary file URL
                    viewButton.classList.remove("d-none"); // Show View Button
                    showFileInModal(fileURL, file.type.startsWith("image/"));
                }
            });

            // Existing File View Logic
            if (existingFileLink) {
                existingFileLink.addEventListener("click", function() {
                    let fileURL = existingFileLink.getAttribute("data-file");
                    let isImage = fileURL.match(/\.(jpeg|jpg|png|gif)$/i); // Check if it's an image
                    showFileInModal(fileURL, isImage);
                    let modal = new bootstrap.Modal(document.getElementById("docModal"));
                    modal.show(); // Open modal
                });
            }

            // Function to Show File in Modal
            function showFileInModal(fileURL, isImage) {
                if (isImage) {
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

        // Lock auto-populated fields (master post data) during edit
        $(document).ready(function() {
            const lockMessage = 'यह फ़ील्ड मास्टर पोस्ट से स्वतः भरी गई है और संपादित नहीं की जा सकती।';

            function notifyLock() {
                if (window.toastr) {
                    toastr.info(lockMessage);
                } else if (window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'जानकारी',
                        text: lockMessage,
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            }

            $('.auto-populated-field').each(function() {
                const $el = $(this);
                const original = $el.val();
                $el.data('original', original);

                if ($el.is('input[type="text"], input[type="number"], textarea')) {
                    $el.prop('readonly', true);
                }
            });

            $('.auto-populated-field').on('change keyup', function() {
                const $el = $(this);
                const original = $el.data('original');
                if ($el.is('select')) {
                    $el.val(original).trigger('change.select2');
                } else {
                    $el.val(original);
                }
                notifyLock();
            });

            // Questions: keep original state
            const lockedQuestions = {};
            $('input[name="questions[]"]').each(function() {
                lockedQuestions[$(this).val()] = $(this).is(':checked');
            }).on('change', function() {
                const val = $(this).val();
                $(this).prop('checked', !!lockedQuestions[val]);
                notifyLock();
            });

            // Hide add/remove controls for auto lists
            $('#add_subject, #add_skill, .remove-subject, .remove-skill, .remove-org-type').hide();
        });

        // CK editor for rules
        CKEDITOR.replace('editor', {
            height: 200, // एडिटर की ऊंचाई
            removePlugins: 'elementspath', // Elements path हटाना
            toolbarCanCollapse: true // टूलबार को हाइड/शो करने का ऑप्शन
        });

        if (CKEDITOR.instances.editor) {
            CKEDITOR.instances.editor.on('instanceReady', function(evt) {
                evt.editor.setReadOnly(true);
            });
        }

        // district toggle for state or district
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

                // Clear selected values when state changes
                districtSelect.value = ""; // For single select
                if (districtSelect.multiple) {
                    for (let option of districtSelect.options) {
                        option.selected = false;
                    }
                }
            }
        }

        @if (isset($readonly) && $readonly)
            // Make form readonly
            $(document).ready(function() {
                // Disable all input fields
                $('input, select, textarea').prop('disabled', true);
                $('input[type="checkbox"]').prop('disabled', true);

                // Add readonly class styling
                $('input, select, textarea').addClass('form-control-readonly');

                // Disable form submission
                $('#myForm4').on('submit', function(e) {
                    e.preventDefault();
                });

                // Disable all buttons except back button and modal close buttons
                $('button').not('.btn-secondary, .btn-close, [data-bs-dismiss="modal"]').prop(
                    'disabled', true);
            });
        @endif
    </script>

    <style>
        @if (isset($readonly) && $readonly)
            .form-control-readonly {
                background-color: #f8f9fa !important;
                opacity: 1 !important;
            }

            .form-control[disabled] {
                background-color: #f8f9fa;
                opacity: 1;
            }

            .form-label {
                font-weight: 600;
            }

            .card-body {
                background-color: #fafafa;
            }
        @endif
    </style>
@endsection
