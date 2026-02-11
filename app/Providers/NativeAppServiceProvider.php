<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->title(config('app.name'))
            ->width(1280)
            ->height(800)
            ->minWidth(1024)
            ->minHeight(700)
            ->rememberState();

        Menu::create(
            Menu::app(),
            Menu::file(),
            Menu::edit(),
            Menu::label('Navigate')->submenu(
                Menu::route('dashboard', 'Dashboard'),
                Menu::route('products.index', 'Products'),
                Menu::route('sales.index', 'Sales'),
                Menu::route('inventory.index', 'Inventory'),
                Menu::route('reports.index', 'Reports'),
            ),
            Menu::window(),
            Menu::help(),
        );

        MenuBar::create()
            ->label('NativeERP')
            ->tooltip('NativeERP')
            ->onlyShowContextMenu()
            ->withContextMenu(
                Menu::make(
                    Menu::route('sync.now', 'Sync Now'),
                    Menu::separator(),
                    Menu::quit('Exit'),
                )
            );
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
