<?php

namespace App\Modules\Sale\Services;

use App\Modules\Sale\Repositories\SaleRepository;

class SaleService
{
    public function __construct(private SaleRepository $repository)
    {
    }
}
