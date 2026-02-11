<?php

namespace App\Modules\Supplier\Controllers;

use App\Modules\Supplier\Services\SupplierService;

class SupplierController
{
    public function __construct(private SupplierService $service)
    {
    }
}
