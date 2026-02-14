<?php

namespace App\Livewire;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Requests\InventoryRequest;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class InventoryPage extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public int $perPage = 10;
    public ?int $editingId = null;

    public ?int $product_id = null;
    public ?string $location = null;
    public int $quantity_on_hand = 0;
    public int $quantity_reserved = 0;

    public $importFile;

    protected $queryString = ['search'];

    public function render()
    {
        $service = app(InventoryService::class);

        return view('livewire.inventory-page')
            ->with([
                'inventories' => $service->list($this->perPage, $this->search),
                'products' => Product::query()->orderBy('name')->get(['id', 'sku', 'name']),
            ])
            ->layout('layouts.native');
    }

    public function rules(): array
    {
        return InventoryRequest::rulesFor();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $inventoryId): void
    {
        $inventory = Inventory::findOrFail($inventoryId);

        $this->editingId = $inventory->id;
        $this->product_id = $inventory->product_id;
        $this->location = $inventory->location;
        $this->quantity_on_hand = (int) $inventory->quantity_on_hand;
        $this->quantity_reserved = (int) $inventory->quantity_reserved;
    }

    public function save(InventoryService $service): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $inventory = Inventory::findOrFail($this->editingId);
            $service->update($inventory, $data);
            session()->flash('status', 'Inventory updated.');
        } else {
            $service->create($data);
            session()->flash('status', 'Inventory created.');
        }

        $this->resetForm();
    }

    public function delete(int $inventoryId, InventoryService $service): void
    {
        $inventory = Inventory::findOrFail($inventoryId);
        $service->delete($inventory);

        session()->flash('status', 'Inventory removed.');
    }

    public function import(InventoryService $service): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $count = $service->importCsv($this->importFile);
        $this->reset('importFile');

        session()->flash('status', "Imported {$count} inventory rows.");
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->product_id = null;
        $this->location = null;
        $this->quantity_on_hand = 0;
        $this->quantity_reserved = 0;
    }
}
