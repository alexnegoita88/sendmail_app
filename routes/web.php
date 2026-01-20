<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::redirect('/', '/login');

// Dashboard route
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile route
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Email tracking route (for pixel tracking)
Route::get('/track/{token}', [App\Http\Controllers\EmailTrackingController::class, 'trackOpen'])
    ->name('email.track');

// Module pages routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/file-upload', [App\Http\Controllers\FileUploadController::class, 'index'])
        ->name('file-upload');
    
    Route::get('/email-templates', [App\Http\Controllers\EmailTemplateController::class, 'index'])
        ->name('email-templates');
    
    Route::get('/campaigns', [App\Http\Controllers\CampaignController::class, 'index'])
        ->name('campaigns');
    
    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])
        ->name('analytics');
});

require __DIR__.'/auth.php';
