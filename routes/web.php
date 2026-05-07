<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RankController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [QuoteController::class, 'dashboard'])->name('dashboard');
    Route::resource('quotes', QuoteController::class);
    Route::post('quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('quotes/{quote}/approve', [QuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('quotes/{quote}/activate', [QuoteController::class, 'activate'])->name('quotes.activate');
    Route::post('quotes/{quote}/expire', [QuoteController::class, 'expire'])->name('quotes.expire');
    Route::post('quotes/{quote}/renew', [QuoteController::class, 'renew'])->name('quotes.renew');
    Route::resource('clients', ClientController::class)->except('show');
    Route::resource('ranks', RankController::class)->except('show');
});

require __DIR__.'/settings.php';
