<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityTemplateController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;

// Auth routes
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-read');

    // Daily Activities
    Route::get('/activities', [DailyActivityController::class, 'index'])->name('activities.index');
    Route::patch('/activities/{dailyActivity}', [DailyActivityController::class, 'update'])->name('activities.update');

    // Incident Reports
    Route::resource('incidents', IncidentReportController::class)->except(['destroy']);

    // Handover
    Route::get('/handover', [HandoverController::class, 'index'])->name('handover.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::resource('activity-templates', ActivityTemplateController::class);
        Route::post('activity-templates/{activityTemplate}/restore', [ActivityTemplateController::class, 'restore'])
            ->name('activity-templates.restore');
        Route::resource('users', UserController::class);
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });
});
