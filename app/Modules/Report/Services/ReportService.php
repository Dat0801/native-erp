<?php

namespace App\Modules\Report\Services;

use App\Modules\Report\Repositories\ReportRepository;

class ReportService
{
    public function __construct(private ReportRepository $repository)
    {
    }
}
