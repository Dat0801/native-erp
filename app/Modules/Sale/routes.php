<?php

use App\Livewire\SalesPage;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/sales', SalesPage::class)->name('sales.index');
});
