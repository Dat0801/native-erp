<?php

namespace App\Livewire;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Requests\ProductRequest;
use App\Modules\Product\Services\ProductService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductsPage extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public int $perPage = 10;
    public ?int $editingId = null;

    public string $sku = '';
    public string $name = '';
    public ?string $description = null;
    public string $price = '0.00';
    public bool $is_active = true;

    public $importFile;

    protected $queryString = ['search'];

    public function render()
    {
        $service = app(ProductService::class);

        return view('livewire.products-page')
            ->with([
                'products' => $service->list($this->perPage, $this->search),
            ])
            ->layout('layouts.native');
    }

    public function rules(): array
    {
        return ProductRequest::rulesFor($this->editingId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $this->editingId = $product->id;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = (string) $product->price;
        $this->is_active = (bool) $product->is_active;
    }

    public function save(ProductService $service): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            $service->update($product, $data);
            session()->flash('status', 'Product updated.');
        } else {
            $service->create($data);
            session()->flash('status', 'Product created.');
        }

        $this->resetForm();
    }

    public function delete(int $productId, ProductService $service): void
    {
        $product = Product::findOrFail($productId);
        $service->delete($product);

        session()->flash('status', 'Product removed.');
    }

    public function import(ProductService $service): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $count = $service->importCsv($this->importFile);
        $this->reset('importFile');

        session()->flash('status', "Imported {$count} products.");
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->sku = '';
        $this->name = '';
        $this->description = null;
        $this->price = '0.00';
        $this->is_active = true;
    }
}
