<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', HomeController::class)->name('home');

// PRD §5: /cv mengarah ke file CV, hanya jika CV sudah diunggah di Pengaturan Situs.
Route::get('/cv', function () {
    $path = setting('cv_path');

    abort_unless(filled($path) && Storage::disk('uploads')->exists($path), 404);

    return redirect()->away(upload_url($path));
})->name('cv');
