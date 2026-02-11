<?php

namespace App\Modules\Sync\Controllers;

use App\Modules\Sync\Services\SyncService;
use Illuminate\Http\RedirectResponse;

class SyncController
{
    public function __construct(private SyncService $service)
    {
    }

    public function syncNow(): RedirectResponse
    {
        $this->service->syncNow();

        return redirect()->route('dashboard');
    }
}
