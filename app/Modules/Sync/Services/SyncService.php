<?php

namespace App\Modules\Sync\Services;

use App\Modules\Sync\Repositories\SyncRepository;
use Native\Laravel\Notification;

class SyncService
{
    public function __construct(private SyncRepository $repository)
    {
    }

    public function syncNow(): void
    {
        Notification::new()
            ->title('NativeERP')
            ->message('Sync queued for processing.')
            ->show();
    }
}
