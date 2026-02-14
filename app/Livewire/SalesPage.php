<?php

namespace App\Livewire;

use App\Modules\Customer\Models\Customer;
use App\Modules\Product\Models\Product;
use App\Modules\Sale\Models\Sale;
use App\Modules\Sale\Requests\SaleRequest;
use App\Modules\Sale\Services\SaleService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SalesPage extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public int $perPage = 10;
    public ?int $editingId = null;

    public ?int $customer_id = null;
    public string $sale_number = '';
    public string $sale_date = '';
    public string $status = 'draft';
    public array $items = [];

    public $importFile;

    protected $queryString = ['search'];

    public function mount(): void
    {
        $this->sale_date = now()->toDateString();
        $this->items = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0],
        ];
    }

    public function render()
    {
        $service = app(SaleService::class);

        return view('livewire.sales-page')
            ->with([
                'sales' => $service->list($this->perPage, $this->search),
                'products' => Product::query()->orderBy('name')->get(['id', 'sku', 'name', 'price']),
                'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            ])
            ->layout('layouts.native');
    }

    public function rules(): array
    {
        return SaleRequest::rulesFor($this->editingId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function addItemRow(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function edit(int $saleId): void
    {
        $sale = Sale::query()->with('items')->findOrFail($saleId);

        $this->editingId = $sale->id;
        $this->customer_id = $sale->customer_id;
        $this->sale_number = $sale->sale_number;
        $this->sale_date = $sale->sale_date;
        $this->status = $sale->status;
        $this->items = $sale->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ];
        })->toArray();
    }

    public function save(SaleService $service): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $sale = Sale::findOrFail($this->editingId);
            $service->update($sale, $data);
            session()->flash('status', 'Sale updated.');
        } else {
            $service->create($data);
            session()->flash('status', 'Sale created.');
        }

        $this->resetForm();
    }

    public function delete(int $saleId, SaleService $service): void
    {
        $sale = Sale::findOrFail($saleId);
        $service->delete($sale);

        session()->flash('status', 'Sale removed.');
    }

    public function import(SaleService $service): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $count = $service->importCsv($this->importFile);
        $this->reset('importFile');

        session()->flash('status', "Imported {$count} sales.");
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->customer_id = null;
        $this->sale_number = '';
        $this->sale_date = now()->toDateString();
        $this->status = 'draft';
        $this->items = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0],
        ];
    }

    public function getSubtotalProperty(): float
    {
        $subtotal = 0;

        foreach ($this->items as $item) {
            $subtotal += (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
        }

        return $subtotal;
    }
}
