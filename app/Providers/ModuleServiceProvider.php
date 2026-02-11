<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    private const MODULES = [
        'Auth',
        'Product',
        'Inventory',
        'Purchase',
        'Sale',
        'Customer',
        'Supplier',
        'Report',
        'Sync',
    ];

    public function boot(): void
    {
        foreach (self::MODULES as $module) {
            $routesPath = app_path("Modules/{$module}/routes.php");

            if (is_file($routesPath)) {
                $this->loadRoutesFrom($routesPath);
            }
        }
    }
}
