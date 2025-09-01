<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\DomainPriceController;
use App\Http\Controllers\Admin\HostingPlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('customers', CustomerController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('services', ServiceController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('domain-prices', DomainPriceController::class);
    Route::resource('hosting-plans', HostingPlanController::class);
});