<?php

use App\Http\Admin\AdminController;
use App\Http\Auth\AuthController;
use App\Http\Complaint\ComplaintController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth.user'], function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/dashboard-pending-list', [AuthController::class, 'getPendingList'])->name('dashboard.pending.list');
    Route::post('/dashboard-pending-list-client', [AuthController::class, 'getPendingListClinet'])->name('dashboard.pending.list.client');

    Route::get('/complaint', [ComplaintController::class, 'showComplaints'])->name('complaint');
    Route::post('/complaint/list', [ComplaintController::class, 'getTableData'])->name('complaint.list');
    Route::post('/complaint/export', [ComplaintController::class, 'exportComplaints'])->name('complaint.export');
    Route::get('/complaint/create', [ComplaintController::class, 'showCreateComplaints'])->name('show.create.complaint');
    Route::post('/complaint/create', [ComplaintController::class, 'saveCreateComplaints'])->name('save.create.complaint');
    Route::get('/complaint/edit/{id}', [ComplaintController::class, 'showEditComplaints'])->name('show.edit.complaint');
    Route::post('/complaint/edit/{id}', [ComplaintController::class, 'saveEditComplaints'])->name('save.edit.complaint');
    Route::post('/complaint/edit/{id}/messages', [ComplaintController::class, 'postComplaintMessage'])->name('complaint.messages.store');
    Route::post('/complaint/edit/{id}/close', [ComplaintController::class, 'closeComplaint'])->name('complaint.close');
    Route::get('download/{filename}', [ComplaintController::class, 'downloadComplaintFile'])->name('download.complaint.file');

    // Legacy URLs — redirect to unified complaints page
    Route::redirect('/complaint-register', '/complaint');
    Route::post('/complaint-register', [ComplaintController::class, 'getTableData']);
    Route::post('/complaint-register/export', [ComplaintController::class, 'exportComplaints']);
    Route::post('/complaint/get-table-data', [ComplaintController::class, 'getTableData']);

    Route::middleware(['test.mode', 'admin'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
        Route::post('/admin/clients', [AdminController::class, 'storeClient'])->name('admin.clients.store');
        Route::post('/admin/support-users', [AdminController::class, 'storeSupportUser'])->name('admin.support.store');
        Route::post('/admin/portal-users', [AdminController::class, 'storePortalUser'])->name('admin.portal.store');
    });
});
