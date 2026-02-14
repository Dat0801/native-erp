<?php

namespace App\Modules\Product\Controllers;

use App\Modules\Product\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController
{
    public function __construct(private ProductService $service)
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
            ->route('products.index')
            ->with('status', "Imported {$count} products.");
    }
}
