<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/admin', function () {
    return view('admin.dash');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('gallery', GalleryController::class)->except(['show']);
});
