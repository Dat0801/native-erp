<?php

namespace App\Modules\Customer\Controllers;

use App\Modules\Customer\Services\CustomerService;

class CustomerController
{
    public function __construct(private CustomerService $service)
    {
    }
}
