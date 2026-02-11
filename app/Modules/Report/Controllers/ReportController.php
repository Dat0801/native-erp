<?php

namespace App\Modules\Report\Controllers;

use App\Modules\Report\Services\ReportService;

class ReportController
{
    public function __construct(private ReportService $service)
    {
    }
}
