<?php

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Repositories\CustomerRepository;

class CustomerService
{
    public function __construct(private CustomerRepository $repository)
    {
    }
}
