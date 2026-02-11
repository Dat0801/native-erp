<?php

namespace App\Modules\Inventory\Controllers;

use App\Modules\Inventory\Services\InventoryService;

class InventoryController
{
    public function __construct(private InventoryService $service)
    {
    }
}
