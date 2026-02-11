<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col gap-8 px-6 py-8">
        <header class="flex flex-col gap-4 border-b border-slate-800 pb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">NativePHP Desktop</p>
                    <h1 class="text-3xl font-semibold text-slate-50">{{ config('app.name') }}</h1>
                </div>
                <div class="rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-1 text-xs text-emerald-200">
                    Offline-first ready
                </div>
            </div>
            <nav class="flex flex-wrap gap-3 text-sm">
                <a class="rounded-full border border-slate-800 px-4 py-2 text-slate-200 hover:border-emerald-500/50 hover:text-emerald-200" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="rounded-full border border-slate-800 px-4 py-2 text-slate-200 hover:border-emerald-500/50 hover:text-emerald-200" href="{{ route('products.index') }}">Products</a>
                <a class="rounded-full border border-slate-800 px-4 py-2 text-slate-200 hover:border-emerald-500/50 hover:text-emerald-200" href="{{ route('sales.index') }}">Sales</a>
                <a class="rounded-full border border-slate-800 px-4 py-2 text-slate-200 hover:border-emerald-500/50 hover:text-emerald-200" href="{{ route('inventory.index') }}">Inventory</a>
                <a class="rounded-full border border-slate-800 px-4 py-2 text-slate-200 hover:border-emerald-500/50 hover:text-emerald-200" href="{{ route('reports.index') }}">Reports</a>
            </nav>
        </header>
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
