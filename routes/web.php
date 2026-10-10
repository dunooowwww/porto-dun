<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', HomeController::class)->name('home');

Route::get('/projects/{slug}', [ProjectController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('projects.show');

// PRD F8: form Box, Box (rate limit & honeypot ditangani di controller).
Route::post('/kontak', [ContactMessageController::class, 'store'])->name('contact.store');

// PRD §5: /cv mengarah ke file CV, hanya jika CV sudah diunggah di Pengaturan Situs.
Route::get('/cv', function () {
    $path = setting('cv_path');

    abort_unless(filled($path) && Storage::disk('uploads')->exists($path), 404);

    return redirect()->away(upload_url($path));
})->name('cv');
