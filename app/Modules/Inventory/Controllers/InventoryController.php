<?php

namespace App\Modules\Inventory\Controllers;

use App\Modules\Inventory\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController
{
    public function __construct(private InventoryService $service)
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
            ->route('inventory.index')
            ->with('status', "Imported {$count} inventory rows.");
    }
}
