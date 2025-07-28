<?php

use App\Http\Controllers\Management\AnnouncementController;
use App\Http\Controllers\Management\CarController;
use App\Http\Controllers\Management\CardController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\LeaveController;
use App\Http\Controllers\Management\SalaryController;
use App\Http\Controllers\Management\UnitController;
use App\Http\Controllers\Management\UserController;
use App\Http\Controllers\Staff\StaffAnnouncementController;
use App\Http\Controllers\Staff\StaffCardController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\StaffLeaveController;
use App\Http\Controllers\Staff\StaffSalaryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('home');
    Route::resource('leaves', StaffLeaveController::class)->parameters(['leaves' => 'leave']);
    Route::resource('announcements', StaffAnnouncementController::class)->only(['index', 'show']);
    Route::resource('cards', StaffCardController::class)->only(['edit', 'update']);
    Route::resource('salaries', StaffSalaryController::class)->only(['index']);

    Route::middleware('admin')->prefix('management')->as('management.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('units', UnitController::class);
        Route::resource('announcements', AnnouncementController::class);
        Route::resource('cards', CardController::class);
        Route::resource('cars', CarController::class);

        Route::get('/leaves/review', [LeaveController::class, 'review'])->name('leaves.review');
        Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
        Route::resource('leaves', LeaveController::class)->parameters(['leaves' => 'leave']);
        Route::resource('salaries', SalaryController::class)->except(['show']);
    });
});



require __DIR__.'/auth.php';
