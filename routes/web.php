<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\GalleryPhoto;

Route::get('/', function () {
    $photos = GalleryPhoto::where('is_active', true)
        ->orderBy('display_order')
        ->orderByDesc('created_at')
        ->get();
    return view('users.index', compact('photos'));
});

Route::get('/admin', function () {
    return view('admin.dash');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('gallery', GalleryController::class)->except(['show']);
});
