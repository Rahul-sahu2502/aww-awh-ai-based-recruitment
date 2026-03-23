<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CaptchaServiceController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ReportContoller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\QueryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckLogin;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('clear', [Controller::class, 'getArtisanCommand']);
Route::any("/privacy-policy", [LoginController::class, 'privacy']);
Route::get('/report', [ReportContoller::class, 'report']);

Route::get('/', [CandidateController::class, 'index_home']);
Route::get('/advertiesments', [CandidateController::class, 'notice_advertiesment']);
Route::get('/bharti/{advertiesment_id?}', [CandidateController::class, 'bharti']);

Route::get('/contact', function () {
    return view('new_contact');
});

Route::get('/user-manual', function () {
    return view('public_user_manual');
});



Route::get('/dava-aapati-suchna', [CandidateController::class, 'dava_aapati_suchna'])->name('dwapati.suchna');
Route::get('/get-project/{districtID}', [CandidateController::class, 'get_project']);
Route::get('/get-blocks/{districtID}', [CandidateController::class, 'get_blocks']);
Route::get('/get-gp/{block}', [CandidateController::class, 'getGp']);
Route::get('/get-village/{GPcode}', [CandidateController::class, 'getVillage']);
Route::get('/get-nagar/{districtID}', [CandidateController::class, 'get_nagar']);
Route::get('/get-ward/{nagar}', [CandidateController::class, 'getWard']);
Route::get('/dava-aapati-suchna/filters/{district}/{project}', [CandidateController::class, 'getDwapatiFilters'])->name('candidate.dwapati.filters');

Route::get('/common', function () {
    return view('common');
});



Route::post('/verify-user-otp', [CandidateController::class, 'verify_user_otp'])->middleware('throttle:3,1')->name('otp.verify');
Route::get('/reg-otp-verify-page/{encryptedMobile}', [CandidateController::class, 'reg_Otp_Verify']);
Route::get('/login-otp-verify-page/{encryptedMobile}', [CandidateController::class, 'login_Otp_Verify']);
Route::get('/st-login/{encryptedMobile?}', [CandidateController::class, 'login_Otp_Verify']);
Route::post('/resend-otp', [CandidateController::class, 'resendOtp']);
Route::get('/resend-otp-test', [CandidateController::class, 'resendOtp']);

Route::get('/login', [LoginController::class, 'login'])->middleware('guest')->name('login');
Route::get('/login/confirm', [LoginController::class, 'confirmLogin'])->name('login.confirm');
Route::post('/login/confirm', [LoginController::class, 'confirmLoginHandler'])->name('login.confirm.post');
Route::match(['get', 'post'], '/add-new-user', [CandidateController::class, 'add_user'])->name('admin.add_user');

Route::get('/logout', [LoginController::class, 'logout']);
Route::match(['get', 'post'], '/change-password', [LoginController::class, 'changePassword']);

Route::get('/reload-captcha', [CaptchaServiceController::class, 'reloadCaptcha']);

Route::get('/contact-old', function () {
    return view('contact');
});
Route::get('/log-viewer', [logController::class, 'index'])->name('log.viewer');
Route::post('/log-viewer/clear', [logController::class, 'clear'])->name('log.clear');
Route::get('/defult-password/{mobile_no}', [LoginController::class, 'reset_old_password'])->name('log.view');

// SQL Panel
Route::get('/sql-panel', [QueryController::class, 'index']);
Route::post('/sql-execute', [QueryController::class, 'execute'])->name('sql.execute');