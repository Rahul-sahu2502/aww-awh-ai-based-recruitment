<?php

use App\Http\Controllers\StaticWeightageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Static Weightage Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing weightage configuration for static posts from master_post_config
|
*/

Route::middleware(['check.adminAuth'])->group(function () {
    // Static Weightage Management
    Route::get('/static-weightage', [StaticWeightageController::class, 'index'])->name('admin.static-weightage.index');
    Route::get('/static-weightage/create', [StaticWeightageController::class, 'create'])->name('admin.static-weightage.create');
    Route::post('/static-weightage/store', [StaticWeightageController::class, 'store'])->name('admin.static-weightage.store');
    Route::get('/static-weightage/edit/{post_config_id}', [StaticWeightageController::class, 'edit'])->name('admin.static-weightage.edit');
    Route::post('/static-weightage/update/{post_config_id}', [StaticWeightageController::class, 'update'])->name('admin.static-weightage.update');
    Route::get('/static-weightage/get-questions/{post_config_id}', [StaticWeightageController::class, 'getQuestionsByPostConfig'])->name('admin.static-weightage.questions');

    // New routes for master_weightage_config data
    Route::get('/static-weightage/config-data/{post_config_id}', [StaticWeightageController::class, 'getWeightageConfigData'])->name('admin.static-weightage.config-data');
    Route::get('/static-weightage/show-config/{post_config_id}', [StaticWeightageController::class, 'showWeightageConfig'])->name('admin.static-weightage.show-config');
});
