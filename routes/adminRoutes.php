<?php

use App\Http\Controllers\AdminMasterController;
use App\Http\Controllers\insightsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportContoller;
use App\Http\Controllers\DscController;
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

Route::post('/validate-user', [LoginController::class, 'validate_user'])
    ->middleware('throttle:user-ip-login');

Route::get('/reload-captcha', [LoginController::class, 'reloadCaptcha']);

Route::middleware([CheckLogin::class . ':Super_admin-Admin'])->group(function () {
    // Route::group(['middleware' => 'check.adminAuth'], function () {admin-advertisment

    // Route::get('dsc-check', [DscController::class, 'dcs_register_ajax']);

    /*
    |--------------------------------------------------------------------------
    | NON–THROTTLED (mostly GET / listing / export)
    |--------------------------------------------------------------------------
    */

    Route::get('/admin-dashboard', [AdminController::class, 'dashboard']);
    Route::get('/notifications', [AdminController::class, 'getAdminNotifications']);


    // Change project (CDPO only)
    Route::get('/change-project', [LoginController::class, 'changeProject']);
    Route::post('/process-change-project', [LoginController::class, 'processChangeProject']);

    // dava-apatti-list Routes
    Route::get('/dava-apatti-list', [AdminController::class, 'dava_apatti_list'])->name('admin.dava_apatti_list');
    Route::get('/view-dava-apatti/{claim_id}', [AdminController::class, 'view_dava_apatti'])->name('admin.view_dava_apatti');
    Route::post('/update-claim-status', [AdminController::class, 'updateClaimStatus'])->name('admin.updateClaimStatus');
    Route::post('filter-dava-apatti', [AdminController::class, 'filterDavaApatti'])->name('admin.filter.dava.apatti');
    // Feedback Routes
    Route::get('/feedback', [AdminController::class, 'feedbackForm'])->name('admin.feedback');
    Route::get('/feedback/recent', [AdminController::class, 'getRecentFeedbacks'])->name('admin.feedback.recent');
    Route::get('/feedback-list', [AdminController::class, 'feedbackList'])->name('admin.feedback.list');
    Route::get('/feedback-list/data', [AdminController::class, 'getFeedbackListData'])->name('admin.feedback.list.data');
    Route::get('/feedback-list/{id}', [AdminController::class, 'getFeedbackDetail'])->name('admin.feedback.detail');
    Route::post('/feedback-list/{id}/reply', [AdminController::class, 'submitFeedbackReply'])->name('admin.feedback.reply');

    // Enhanced Dashboard AJAX Routes
    Route::get('/advertisement-analytics', [AdminController::class, 'getAdvertisementAnalytics'])->name('advertisement.analytics');
    Route::get('/post-analytics/{advertisementId}', [AdminController::class, 'getPostAnalytics'])->name('post.analytics');
    Route::get('/admin-insights', [insightsController::class, 'getCasteWiseChartData'])->name('caste.chart');
    Route::get('/admin-percentage-wise-chart/{qualiId}', [insightsController::class, 'getPercentageWiseApplicationCount'])->name('percent.chart');

    Route::get('/admin-post', [AdminController::class, 'upload_post']);
    Route::get('/get-subjects-by-qualification/{id}', [AdminController::class, 'getSubjectsByQualification'])->name('getSubjectsByQualification');
    Route::get('/get-post-config/{id}', [AdminController::class, 'getPostConfigById'])->name('getPostConfig');

    // Static Post List Routes
    Route::get('/static-posts', [AdminController::class, 'static_post_list'])->name('static-posts.list');
    Route::get('/static-posts/{id}/details', [AdminController::class, 'static_post_details'])->name('static-posts.details');
    Route::get('/static-posts/{id}/view', [AdminController::class, 'view_static_post'])->name('static-posts.view');
    Route::get('/static-posts/{id}/edit', [AdminController::class, 'edit_static_post'])->name('static-posts.edit');

    Route::get('/get-post-type-questions/{post_type_id}', [AdminController::class, 'getPostTypeQuestions']);
    Route::get('/get-ward-or-block/{area_name}', [AdminController::class, 'getWardOrBlock']);

    Route::get('/application-list/{pref_dist?}', [AdminController::class, 'application_list'])->name('application-list');
    Route::get('/distWise-application-list/{pref_dist?}', [AdminController::class, 'dist_application_list'])->name('dist-application-list');
    Route::get('/verified-list/{pref_dist?}', [AdminController::class, 'verified_list']);
    Route::get('/all-application-list/{pref_dist?}', [AdminController::class, 'all_application_list'])->name('all_application_list');

    Route::get('/rejected-list/{pref_dist?}', [AdminController::class, 'rejected_list']);
    Route::get('/view-application-detail/{applicant_id?}/{application_id?}/{pref_dist?}', [AdminController::class, 'view_application_detail'])->name('view_application_detail');
    Route::get('/final-application-detail/{applicant_id?}/{application_id?}', [AdminController::class, 'final_application_detail'])->name('final_application_detail');
    Route::get('/dashboard/view-application-detail/{applicant_id?}', [AdminController::class, 'dashboard_view_application_detail'])->name('dashboard_view_application_detail');
    Route::get('/view-docs/{applicant_id}', [AdminController::class, 'view_docs']);
    Route::get('/merit-list', [AdminController::class, 'merit_list'])->name('merit_list');

    // District-wise drill-down report
    Route::get('/applications-list', [AdminController::class, 'district_wise_applications']);
    Route::get('/district-wise-report', [AdminController::class, 'district_wise_applications'])->name('district.wise.report');
    // Route::get('/posts/data', [AdminController::class, 'getPosts'])->name('posts.data');
    Route::get('/show-posts', [AdminController::class, 'showPosts'])->name('posts.show');
    Route::get('/get-questions', [AdminController::class, 'getQuestions']);
    Route::get('/get-posts', [AdminController::class, 'getPosts'])->name('posts.get'); // AJAX ke liye

    // post Update page (GET)
    Route::get('/posts/{id}/{mode?}', [AdminController::class, 'edit'])->name('posts.edit');

    // Document verification
    Route::post('/store-verification', [AdminController::class, 'storeVerification'])->name('admin.store-verification');

    Route::get('/admin-advertisment', [AdminController::class, 'upload_advertisment']);
    Route::get('/show-advertisment', [AdminController::class, 'showAdvertisment'])->name('advertisement.list');

    // Specific routes MUST come BEFORE generic routes with wildcards
    Route::get('/advertisements/related_docs/{id}', [AdminController::class, 'advertisment_docs_open'])->name('advertisement.related_docs');
    Route::get('/advertisements/related_docs_list/{id}', [AdminController::class, 'advertisment_docs_fetch'])->name('advertisement.related_docs.fetch');

    // Generic route with wildcards should come AFTER specific routes
    Route::get('/advertisements/{id}/{mode?}', [AdminController::class, 'editAdvertisementPage'])->name('advertisement.edit');

    Route::get('/transition-show', [AdminController::class, 'seened_docs'])->name('transition.show');

    // master entry (GET forms)
    Route::get('/add-skills', [AdminController::class, 'showSkillsForm'])->name('admin.skills.show');
    Route::get('/add-subjects', [AdminController::class, 'showSubjectsForm'])->name('subjects.index');

    // Master Entry Routes POST/data fetch
    Route::post('/get-district', [AdminMasterController::class, 'get_district']);
    Route::post('/get-project', [AdminMasterController::class, 'get_project']);
    Route::post('/get-sector', [AdminMasterController::class, 'get_sector']);
    Route::post('/get-awc', [AdminMasterController::class, 'get_awc']);

    // excel export
    Route::get('/applications-export', [AdminController::class, 'exportAllApplications'])->name('allApplicationData.export');
    Route::get('/merit-list-export', [AdminController::class, 'exportMeritList'])->name('meritList.export');
    Route::get('/district-wise-applications-list-export', [AdminController::class, 'exportDistrict_wise_applications'])->name('exportDistrict_wise_applications.export');
    Route::get('/verified-list-export/{pref_dist?}', [AdminController::class, 'exportVerified_list'])->name('verifiedList.export');
    Route::get('/rejected-list-export/{pref_dist?}', [AdminController::class, 'exportRejected_list'])->name('rejectedList.export');
    Route::get('/advertisment-list-export', [AdminController::class, 'exportAdvertisment'])->name('advertisment.export');
    Route::get('/post-list-export', [AdminController::class, 'exportPosts'])->name('post.export');
    // Post location/status Excel
    Route::get('/posts-report-export', [ReportContoller::class, 'exportPostsExcel'])->name('posts.report.export');

    // Master Entry GET+POST (definitions kept, POST part will be throttled below)
    Route::match(['get', 'post'], '/add-district', [AdminMasterController::class, 'add_district'])->name('admin.add_district');
    Route::match(['get', 'post'], '/add-project', [AdminMasterController::class, 'add_project'])->name('admin.add_project');
    Route::match(['get', 'post'], '/add-sector', [AdminMasterController::class, 'add_sector'])->name('admin.add_sector');
    Route::match(['get', 'post'], '/add-awc', [AdminMasterController::class, 'add_awc'])->name('admin.add_awc');
    Route::match(['get', 'post'], '/add-gram-panchayat', [AdminMasterController::class, 'add_gram_panchayat'])->name('admin.add_gram_panchayat');
    Route::match(['get', 'post'], '/add-village', [AdminMasterController::class, 'add_village'])->name('admin.add_village');
    Route::match(['get', 'post'], '/add-nagar-nikay', [AdminMasterController::class, 'add_nagar_nikay'])->name('admin.add_nagar_nikay');
    Route::match(['get', 'post'], '/add-ward', [AdminMasterController::class, 'add_ward'])->name('admin.add_ward');

    // this url is for taking fresh data from external api and load into local db
    Route::get('/load-api-data', [App\Http\Controllers\ApiDataController::class, 'loadApiData'])->name('load.api.data');

    // SSP Portal Vacancy Data Routes
    Route::get('/ssp-portal-data', [AdminController::class, 'ssp_portal_vacancy_data'])->name('admin.ssp-portal-data');
    Route::get('/get-ssp-portal-data', [AdminController::class, 'getSspPortalData'])->name('admin.get-ssp-portal-data');
    Route::get('/export-ssp-portal-data', [AdminController::class, 'exportSspPortalData'])->name('admin.export-ssp-portal-data');

    // District-wise report (summary + drill-down + export)
    Route::get('/district-report', [ReportContoller::class, 'districtReportView'])->name('admin.district-report.view');
    Route::get('/district-report/data', [ReportContoller::class, 'districtReportData'])->name('admin.district-report.data');
    Route::get('/district-report/detail', [ReportContoller::class, 'districtReportDetail'])->name('admin.district-report.detail');
    Route::get('/district-report/export', [ReportContoller::class, 'districtReportExport'])->name('admin.district-report.export');
    Route::get('/district-report/summary-export', [ReportContoller::class, 'districtReportSummaryExport'])->name('admin.district-report.summary-export');

    //CDPO List
    Route::get('/cdpo-list', [AdminController::class, 'CDPO_list'])->name('admin.cdpo-list');


    Route::get('/anantim-list', [AdminController::class, 'anantim_list'])->name('admin.anantim-list');
    Route::get('/antim-list', [AdminController::class, 'antim_list'])->name('admin.antim-list');
    Route::get('/post-wise-reports/get-posts', [AdminController::class, 'getPostsByAdvertisement'])->name('report.getPostsByAdvertisement');
    Route::get('/post-wise-reports/download', [AdminController::class, 'downloadPostWiseReport'])->name('report.downloadPostWiseReport');

    Route::post('/anantim-list/upload', [AdminController::class, 'uploadAnantimList'])->name('anantim.list.upload');
    Route::post('/antim-list/upload', [AdminController::class, 'uploadAntimList'])->name('antim.list.upload');
    /*
    |--------------------------------------------------------------------------
    | RATE-LIMITED ADMIN OPERATIONS (POST / write-heavy)
    | throttle:admin-operations
    |--------------------------------------------------------------------------
    */

    Route::middleware('throttle:admin-operations')->group(function () {

        // DSC operations
        Route::post('dsc-register', [DscController::class, 'dcs_register_ajax']);
        Route::post('dsc-add-sign', [DscController::class, 'dsc_add_sign']);
        Route::post('dsc-sign-save', [DscController::class, 'dsc_save_sign']);

        // Category detail (POST)
        Route::post('/category-detail-data', [AdminController::class, 'getCategoryDetail'])->name('category.detail.data');

        // Posts create/update + static posts
        Route::match(['get', 'post'], '/upload-post', [AdminController::class, 'store'])->middleware('secure.file');
        Route::post('/upload-static-post', [AdminController::class, 'store_static_post'])->name('static-post.store')->middleware('secure.file');

        Route::post('/static-posts/{id}/update', [AdminController::class, 'update_static_post'])->name('static-posts.update');
        Route::delete('/static-posts/{id}', [AdminController::class, 'delete_static_post'])->name('static-posts.delete');

        // post Update (POST)
        Route::post('/posts/{id}/update', [AdminController::class, 'update'])->name('posts.update')->middleware('secure.file');
        Route::post('/posts/disable', [AdminController::class, 'disablePosts'])->name('posts.disable');
        Route::post('/advertisements/disable', [AdminController::class, 'disableAdvertisements'])->name('advertisements.disable');

        // Advertisements create/update/docs
        Route::post('/advertisement/otp/request', [AdminController::class, 'requestAdvertisementOtp'])->name('advertisement.otp.request')->middleware('secure.file');
        Route::post('/advertisement/otp/resend', [AdminController::class, 'resendAdvertisementOtp'])->name('advertisement.otp.resend');
        Route::post('/advertisement/otp/verify', [AdminController::class, 'verifyAdvertisementOtp'])->name('advertisement.otp.verify');
        Route::post('/upload-advertisment', [AdminController::class, 'store_advertisment'])->name('advertisement.store')->middleware('secure.file');
        Route::post('/advertisements/update/{id}', [AdminController::class, 'updateAdvertisement'])->name('advertisement.update')->middleware('secure.file');
        Route::post('/advertisements/related_docs', [AdminController::class, 'advertisment_docs_store'])->name('advertisement.related_docs.store')->middleware('secure.file');

        // Application status & marks
        Route::match(['get', 'post'], '/marks-entry/{apply_id?}', [AdminController::class, 'marks_entry'])->name('admin.marks-entry');
        Route::post('/approve-reject-application', [AdminController::class, 'applicationApproveReject']);
        Route::post('/merit-edit-requests', [AdminController::class, 'submitMeritEditRequest'])->name('admin.merit-edit-requests.submit');
        Route::get('/merit-edit-requests', [AdminController::class, 'meritEditRequests'])->name('admin.merit-edit-requests');
        Route::post('/merit-edit-requests/approve', [AdminController::class, 'approveMeritEditRequest'])->name('admin.merit-edit-requests.approve');

        // Transition (document view/status update)
        Route::post('/transition-entry', [AdminController::class, 'updateTransition'])->name('transition.entry');

        // master entry (POST actions)
        Route::post('/add-skills', [AdminController::class, 'storeSkill'])->name('admin.skills.store');
        Route::post('/add-subjects', [AdminController::class, 'storeSubject'])->name('admin.subjects.store');
        Route::post('/update-awc-gp-by-block', [AdminMasterController::class, 'update_awc_gp_by_block'])->name('admin.update_awc_gp_by_block');
        Route::post('/update-awc-village-gp-by-block', [AdminMasterController::class, 'update_awc_village_gp_by_block'])->name('admin.update_awc_village_gp_by_block');

        // Feedback submission
        Route::post('/feedback/submit', [AdminController::class, 'submitFeedback'])->name('admin.feedback.submit');
    });
});
