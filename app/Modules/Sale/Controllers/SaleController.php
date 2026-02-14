<?php

namespace App\Modules\Sale\Controllers;

use App\Modules\Sale\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController
{
    public function __construct(private SaleService $service)
    {
    }

    public function export(): StreamedResponse
    {
        return $this->service->exportCsv();
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $count = $this->service->importCsv($request->file('file'));

        return redirect()
            ->route('sales.index')
            ->with('status', "Imported {$count} sales.");
    }
}
