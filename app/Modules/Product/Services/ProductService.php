<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Repositories\ProductRepository;

class ProductService
{
    public function __construct(private ProductRepository $repository)
    {
    }
}
