<?php

namespace App\Modules\Purchase\Services;

use App\Modules\Purchase\Repositories\PurchaseRepository;

class PurchaseService
{
    public function __construct(private PurchaseRepository $repository)
    {
    }
}
