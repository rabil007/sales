<?php

use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [QuoteController::class, 'dashboard'])->name('dashboard');
    Route::resource('quotes', QuoteController::class);
});

require __DIR__.'/settings.php';
