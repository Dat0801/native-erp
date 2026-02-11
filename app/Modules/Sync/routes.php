<?php

use App\Modules\Sync\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/sync/now', [SyncController::class, 'syncNow'])->name('sync.now');
});
