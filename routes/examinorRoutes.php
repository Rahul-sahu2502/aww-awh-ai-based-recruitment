<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ExaminorController;
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

Route::middleware([CheckLogin::class])->group(function () {
    Route::get('/examinor-dashboard', [ExaminorController::class, 'dashboard']);
    Route::get('/notifications', [ExaminorController::class, 'getNotifications']);
    Route::get('/pending-approvals', [ExaminorController::class, 'pendingApprovals']);
    Route::post('/update-application-status', [ExaminorController::class, 'updateApplicationStatus']);
});
