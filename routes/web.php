<?php

use App\Http\Auth\AuthController;
use App\Http\Complaint\ComplaintController;
use App\Http\Register\RegisterController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth.user'], function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/dashboard-pending-list', [AuthController::class, 'getPendingList'])->name('dashboard.pending.list');
    Route::post('/dashboard-pending-list-client', [AuthController::class, 'getPendingListClinet'])->name('dashboard.pending.list.client');

    Route::get('/complaint', [ComplaintController::class, 'showComplaints'])->name('complaint');
    Route::get('/complaint/create', [ComplaintController::class, 'showCreateComplaints'])->name('show.create.complaint');
    Route::post('/complaint/create', [ComplaintController::class, 'saveCreateComplaints'])->name('save.create.complaint');
    Route::get('/complaint/edit/{id}', [ComplaintController::class, 'showEditComplaints'])->name('show.edit.complaint');
    Route::post('/complaint/edit/{id}', [ComplaintController::class, 'saveEditComplaints'])->name('save.edit.complaint');
    Route::post('/complaint/get-table-data', [ComplaintController::class, 'getTableData'])->name('getTableData.support');
    Route::get('download/{filename}', [ComplaintController::class, 'downloadComplaintFile'])->name('download.complaint.file');


    // Report
    Route::get('/complaint-register', [RegisterController::class, 'showComplaintsRegister'])->name('register.complaint');
    Route::post('/complaint-register', [RegisterController::class, 'showComplaintsRegisterReport'])->name('register.complaint.report');
    Route::post('/complaint-register/export', [RegisterController::class, 'showComplaintsRegisterReportExport'])->name('register.complaint.export');
});
