<?php

use App\Livewire\ProductsPage;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/products', ProductsPage::class)->name('products.index');
});
