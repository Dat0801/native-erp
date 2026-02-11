<?php

namespace App\Modules\Supplier\Services;

use App\Modules\Supplier\Repositories\SupplierRepository;

class SupplierService
{
    public function __construct(private SupplierRepository $repository)
    {
    }
}
