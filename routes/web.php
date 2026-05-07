<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RankController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [QuoteController::class, 'dashboard'])->name('dashboard');
    Route::resource('quotes', QuoteController::class);
    Route::resource('clients', ClientController::class)->except('show');
    Route::resource('ranks', RankController::class)->except('show');
});

require __DIR__.'/settings.php';
