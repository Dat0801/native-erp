<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\AuthRepository;

class AuthService
{
    public function __construct(private AuthRepository $repository)
    {
    }
}
