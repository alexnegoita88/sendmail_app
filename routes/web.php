<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

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
    // Email Lists Management
    Route::get('/email-lists', App\Livewire\EmailListsIndex::class)->name('email-lists.index');
    Route::get('/email-lists/create', App\Livewire\CreateEmailList::class)->name('email-lists.create');
    Route::get('/email-lists/upload', [App\Http\Controllers\FileUploadController::class, 'index'])->name('email-lists.upload');
    Route::get('/email-lists/{id}/edit', App\Livewire\EditEmailList::class)->name('email-lists.edit');

    // Legacy redirect for old links
    Route::redirect('/file-upload', '/email-lists');

    Route::get('/email-templates', [App\Http\Controllers\EmailTemplateController::class, 'index'])
        ->name('email-templates.index');

    Route::get('/email-templates/create', [App\Http\Controllers\EmailTemplateController::class, 'create'])
        ->name('email-templates.create');

    Route::post('/email-templates', [App\Http\Controllers\EmailTemplateController::class, 'store'])
        ->name('email-templates.store');

    Route::get('/email-templates/{id}/edit', [App\Http\Controllers\EmailTemplateController::class, 'edit'])
        ->name('email-templates.edit');

    Route::put('/email-templates/{id}', [App\Http\Controllers\EmailTemplateController::class, 'update'])
        ->name('email-templates.update');

    Route::delete('/email-templates/{id}', [App\Http\Controllers\EmailTemplateController::class, 'destroy'])
        ->name('email-templates.destroy');

    Route::get('/campaigns', App\Livewire\Campaigns::class)->name('campaigns');
    Route::get('/campaigns/{id}/stats', App\Livewire\CampaignStats::class)->name('campaigns.stats');
    Route::get('/campaigns/create', App\Livewire\CreateCampaign::class)->name('campaigns.create');

    // Legacy redirect for campaigns
    Route::redirect('/campaigns-old', '/campaigns'); // Not really needed if I update nav, but let's keep name compatibility if possible


    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])
        ->name('analytics');
});

// Image upload route for TinyMCE
Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('image.upload');

require __DIR__ . '/auth.php';
