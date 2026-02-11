<?php

use App\Livewire\InventoryPage;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/inventory', InventoryPage::class)->name('inventory.index');
});
