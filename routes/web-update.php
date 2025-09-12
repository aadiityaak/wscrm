<?php

use App\Http\Controllers\UpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Update System Routes
|--------------------------------------------------------------------------
| Routes untuk sistem auto-update dari GitHub releases
*/

Route::middleware(['auth'])->prefix('admin/system')->group(function () {
    // System Update Page
    Route::get('/update', function () {
        return inertia('Admin/SystemUpdate');
    })->name('admin.system.update');
    
    // Update API endpoints
    Route::get('/check-updates', [UpdateController::class, 'checkUpdates'])
        ->name('admin.system.check-updates');
        
    Route::post('/perform-update', [UpdateController::class, 'performUpdate'])
        ->name('admin.system.perform-update');
        
    Route::post('/restore-backup', [UpdateController::class, 'restoreBackup'])
        ->name('admin.system.restore-backup');
});