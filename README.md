# NativeERP

NativeERP is a NativePHP desktop application built on Laravel with Livewire and TailwindCSS. It uses a modular architecture and a local SQLite database as the default data store to support offline-first workflows.

## Stack

- Laravel 11
- NativePHP
- SQLite
- Livewire + Blade
- TailwindCSS

## Architecture

Business logic lives in module services, controllers are thin, validation uses Form Requests, and persistence goes through repositories. Each module lives under app/Modules with its own routes, models, controllers, and services.

Modules:

- Auth
- Product
- Inventory
- Purchase
- Sale
- Customer
- Supplier
- Report
- Sync

## Database

Base migrations are included for:

- users
- products
- customers
- suppliers
- inventories
- sales
- sale_items

Transactional tables (inventories, sales, sale_items) use soft deletes.

## Offline-first

The app uses SQLite locally and the Sync module provides the foundation for synchronization. NativePHP enables WAL mode for the local database when running in desktop mode.

## Development

Install dependencies:

- composer install
- npm install

Run migrations:

- php artisan migrate

Start the web UI during development:

- npm run dev
- php artisan serve

When running inside the NativePHP desktop runtime, run native migrations:

- php artisan native:migrate
