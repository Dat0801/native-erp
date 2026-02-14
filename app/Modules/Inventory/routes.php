<?php

use App\Livewire\InventoryPage;
use App\Modules\Inventory\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/inventory', InventoryPage::class)->name('inventory.index');
    Route::get('/inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
    Route::post('/inventory/import', [InventoryController::class, 'import'])->name('inventory.import');
});
