<?php

namespace App\Modules\Sale\Controllers;

use App\Modules\Sale\Services\SaleService;

class SaleController
{
    public function __construct(private SaleService $service)
    {
    }
}
