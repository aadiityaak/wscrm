<?php

use App\Http\Controllers\DomainPriceController;
use App\Http\Controllers\HostingPlanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('hosting')->name('hosting.')->group(function () {
    Route::get('/', [HostingPlanController::class, 'index'])->name('index');
    Route::get('/{hostingPlan}', [HostingPlanController::class, 'show'])->name('show');
});

Route::prefix('domains')->name('domains.')->group(function () {
    Route::get('/', [DomainPriceController::class, 'index'])->name('index');
    Route::get('/search', [DomainPriceController::class, 'search'])->name('search');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
    });
});
