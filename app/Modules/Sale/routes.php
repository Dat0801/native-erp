<?php

use App\Livewire\SalesPage;
use App\Modules\Sale\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/sales', SalesPage::class)->name('sales.index');
    Route::get('/sales/export', [SaleController::class, 'export'])->name('sales.export');
    Route::post('/sales/import', [SaleController::class, 'import'])->name('sales.import');
});
