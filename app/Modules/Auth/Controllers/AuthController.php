<?php

namespace App\Modules\Auth\Controllers;

use App\Modules\Auth\Services\AuthService;

class AuthController
{
    public function __construct(private AuthService $service)
    {
    }
}
