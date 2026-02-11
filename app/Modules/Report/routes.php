<?php

use App\Livewire\DashboardPage;
use App\Livewire\ReportsPage;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/dashboard', DashboardPage::class)->name('dashboard');
    Route::get('/reports', ReportsPage::class)->name('reports.index');
});
