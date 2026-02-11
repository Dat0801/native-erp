<?php

namespace App\Modules\Product\Controllers;

use App\Modules\Product\Services\ProductService;

class ProductController
{
    public function __construct(private ProductService $service)
    {
    }
}
