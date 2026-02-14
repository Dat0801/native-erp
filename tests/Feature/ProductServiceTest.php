<?php

namespace Tests\Feature;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_updates_product(): void
    {
        $service = app(ProductService::class);

        $product = $service->create([
            'sku' => 'SKU-1001',
            'name' => 'Demo Product',
            'description' => 'Sample',
            'price' => 19.5,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', ['sku' => 'SKU-1001']);

        $service->update($product, [
            'name' => 'Updated Product',
            'price' => 29,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-1001',
            'name' => 'Updated Product',
            'is_active' => 0,
        ]);
    }
}
