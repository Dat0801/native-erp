<?php

namespace App\Modules\Purchase\Controllers;

use App\Modules\Purchase\Services\PurchaseService;

class PurchaseController
{
    public function __construct(private PurchaseService $service)
    {
    }
}
