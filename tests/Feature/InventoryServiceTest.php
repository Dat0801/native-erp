<?php

namespace Tests\Feature;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_updates_inventory(): void
    {
        $product = Product::create([
            'sku' => 'SKU-2001',
            'name' => 'Stocked Item',
            'description' => null,
            'price' => 12,
            'is_active' => true,
        ]);

        $service = app(InventoryService::class);

        $inventory = $service->create([
            'product_id' => $product->id,
            'location' => 'Main',
            'quantity_on_hand' => 10,
            'quantity_reserved' => 2,
        ]);

        $this->assertInstanceOf(Inventory::class, $inventory);
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'location' => 'Main',
        ]);

        $service->update($inventory, [
            'quantity_on_hand' => 15,
            'quantity_reserved' => 1,
        ]);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity_on_hand' => 15,
            'quantity_reserved' => 1,
        ]);
    }
}
