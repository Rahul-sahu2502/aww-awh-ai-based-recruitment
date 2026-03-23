<?php

use App\Http\Controllers\WeightageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Weightage Management Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes for weightage management.
|
*/

Route::middleware(['check.adminAuth'])->group(function () {
    // Weightage Management
    // Route::get('/weightage-management', [WeightageController::class, 'index'])->name('static-posts.list');
    Route::get('/weightage-management/create', [WeightageController::class, 'create'])->name('admin.weightage.create');
    Route::post('/weightage-management/store', [WeightageController::class, 'store'])->name('admin.weightage.store');
    Route::get('/weightage-management/edit/{post_id}', [WeightageController::class, 'edit'])->name('admin.weightage.edit');
    Route::post('/weightage-management/update/{post_id}', [WeightageController::class, 'update'])->name('admin.weightage.update');
    Route::get('/weightage-management/get-questions/{post_id}', [WeightageController::class, 'getQuestionsByPost'])->name('admin.weightage.questions');
});
