<?php

use Illuminate\Support\Facades\Route;

// M1: halaman tes desain. Diganti halaman Home (one-page) di M4.
Route::view('/', 'design-test')->name('home');
