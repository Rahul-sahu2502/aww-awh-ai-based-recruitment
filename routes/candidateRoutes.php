<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMasterController;
use App\Http\Middleware\CheckLogin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([CheckLogin::class . ':Candidate'])->group(function () {
    Route::get('/candidate-dashboard', [CandidateController::class, 'dashboard']);
    Route::get('/submitted-applications/{status?}', [CandidateController::class, 'submitted_application_list']);
    Route::get('/pending-applications', [CandidateController::class, 'pending_application_list'])->name('pendingApplicationList');
    Route::get('/advertisement-list', [CandidateController::class, 'recruitment_list'])->name('recruitment_list');
    Route::get('/post-list/{Advertisement_ID}/', [CandidateController::class, 'post_list']);
    Route::match(['get', 'post'], '/user-register/{appID}', [CandidateController::class, 'user_register'])->name('application_submit');
    Route::match(['get', 'post'], '/user-register-awc/{appID}/{is_update?}', [CandidateController::class, 'user_register_awc'])->name('application_submit_awc');

    // Feedback Routes (Using AdminController - same table & blade)
    Route::get('/feedback', [AdminController::class, 'feedbackForm'])->name('candidate.feedback');
    Route::get('/feedback/recent', [AdminController::class, 'getRecentFeedbacks'])->name('candidate.feedback.recent');

    // User Manual Route
    Route::get('/user-manual', [CandidateController::class, 'userManual'])->name('candidate.user-manual');

    //Dava Apatti Get Route
    Route::get('/dava-apatti', [CandidateController::class, 'davaApatti']);
    Route::get('/claim-objection-list', [CandidateController::class, 'getClaimObjectionList'])->name('candidate.claimObjection.list');

    // ## Candidate write-operations (form save + final submit)
    Route::middleware('throttle:candidate-forms')->group(function () {
        Route::post('/final-submit', [CandidateController::class, 'final_submit']);

        Route::post('/save-post', [CandidateController::class, 'savePost'])->name('savePost');
        Route::post('/save-applicant-detail', [CandidateController::class, 'saveAppDetail'])->name('saveAppDetail');
        Route::post('/save-education-detail', [CandidateController::class, 'saveEducationDetail'])->name('saveEducationDetail');
        Route::post('/save-experience-detail', [CandidateController::class, 'saveExperienceDetail'])->name('saveExperienceDetail');
        Route::post('/save-self-attested', [CandidateController::class, 'save_self_attested'])->name('save_self_attested');
        Route::post('/save-post-question', [CandidateController::class, 'save_post_question'])->name('save_post_question');

        // Feedback submission (Using AdminController - same table & blade)
        Route::post('/feedback/submit', [AdminController::class, 'submitFeedback'])->name('candidate.feedback.submit');

        #######  Candidate Update Details Routes
        Route::post('/post-details-update', [CandidateController::class, 'post_details_update'])->name('post_details.update');
        Route::post('/applicant-details-update', [CandidateController::class, 'applicant_details_update'])->name('applicant_details.update');
        Route::post('/education-details-update', [CandidateController::class, 'education_details_update'])->name('education_details.update');
        Route::post('/experience-details-update', [CandidateController::class, 'experience_details_update'])->name('experience_details.update');
        Route::post('/document-details-update', [CandidateController::class, 'document_details_update'])->name('document_details.update');

        //Dava Apatti Post Route
        Route::post('/dava-apatti', [CandidateController::class, 'davaApatti'])->name('candidate.DavaApatti.submit');
    });

    // ## Candidate document upload – separate stricter limiter
    Route::middleware('throttle:candidate-documents')->group(function () {
        Route::post('/save-documents', [CandidateController::class, 'saveDocuments'])->name('saveDocuments');
    });

    Route::match(['get', 'post'], '/view-pendingApplication-detail/{EncodeID}/{is_update?}', [CandidateController::class, 'viewPendingApplicationDetail'])->name('candidate.view-pending-application');


    #######  New Routs  For Branch (New Changes)
    Route::match(['get', 'post'], '/user-details-form/{appID?}/{is_update?}', [CandidateController::class, 'user_details_form'])->name('user_details_form');
    Route::match(['get', 'post'], '/user-details-update/{Candidate_id}/{Apply_id}', [CandidateController::class, 'user_details_update'])->name('user_details_update');
    Route::match(['get', 'post'], '/user-apply-post/{appID}', [CandidateController::class, 'user_apply_post'])->name('user_apply_post');

    Route::get('/view-user-detail/{applicant_id}', [CandidateController::class, 'view_user_detail'])->name('view_user_detail');
    Route::get('/view-documents/{apply_id}', [CandidateController::class, 'view_documents']);

    // Master Data Get 
    Route::get('/get-area-data/{district}', [CandidateController::class, 'areaData'])->name('getAreaData');
    Route::get('/get-gp/{block}', [CandidateController::class, 'getGp'])->name('getGp');
    Route::get('/get-village/{GPcode}', [CandidateController::class, 'getVillage'])->name('getVillage');
    Route::get('/get-ward/{nagar}', [CandidateController::class, 'getWard'])->name('getWard');
    Route::get('/get-post/{ward_village_code}', [CandidateController::class, 'getPost'])->name('getPost');
    Route::get('/All-documents/{application_id}', [CandidateController::class, 'view_all_docs']);
    Route::get('/get-pincodes-by-district/{district_code}', [CandidateController::class, 'getPincodesByDistrict']);

    Route::get('/view-application-detail/{applicant_id?}/{application_id?}', [AdminController::class, 'view_application_detail'])->name('view_application_detail_candidate');
    Route::get('/final-application-detail/{applicant_id?}/{application_id?}', [AdminController::class, 'final_application_detail'])->name('final_application_detail_candidate');
    Route::get('/view-docs/{application_id}', [AdminController::class, 'view_docs']);

    // These are just data fetch POSTs – you can leave them without throttle or later add a lighter limiter
    Route::post('/get-post-questions', [AdminMasterController::class, 'get_post_questions']);
    Route::post('/get-post-skills', [AdminMasterController::class, 'get_post_skills']);
    Route::post('/get-post-questions-with-answer', [AdminMasterController::class, 'get_post_questions_with_Answer']);
    Route::post('/get-post-qualification', [AdminMasterController::class, 'get_post_qualification']);
});

Route::middleware([CheckLogin::class . ':Super_admin-Candidate-Admin'])->group(function () {
    Route::match(['get', 'post'], '/print-application/{applicant_id}/{application_id}', [CandidateController::class, 'print_application']);
});
