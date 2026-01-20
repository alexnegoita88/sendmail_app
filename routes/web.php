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
Route::get('/track/{token}', function ($token) {
    // This will be handled by a controller for tracking email opens
    return response('')->header('Content-Type', 'image/gif');
})->name('email.track');

require __DIR__.'/auth.php';
