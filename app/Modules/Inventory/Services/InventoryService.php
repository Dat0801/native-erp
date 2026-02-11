<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Repositories\InventoryRepository;

class InventoryService
{
    public function __construct(private InventoryRepository $repository)
    {
    }
}
