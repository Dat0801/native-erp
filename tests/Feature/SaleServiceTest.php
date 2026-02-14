<?php

namespace Tests\Feature;

use App\Modules\Customer\Models\Customer;
use App\Modules\Product\Models\Product;
use App\Modules\Sale\Models\Sale;
use App\Modules\Sale\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_sale_with_items(): void
    {
        $customer = Customer::create([
            'code' => 'C-1001',
            'name' => 'Sample Customer',
            'email' => 'customer@example.test',
        ]);

        $product = Product::create([
            'sku' => 'SKU-3001',
            'name' => 'Sale Item',
            'description' => null,
            'price' => 5,
            'is_active' => true,
        ]);

        $service = app(SaleService::class);

        $sale = $service->create([
            'customer_id' => $customer->id,
            'sale_number' => 'SALE-1001',
            'sale_date' => '2026-02-14',
            'status' => 'draft',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 5,
                ],
            ],
        ]);

        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertDatabaseHas('sales', [
            'sale_number' => 'SALE-1001',
            'total' => 10,
        ]);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
