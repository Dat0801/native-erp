<section class="space-y-6">
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-50">Products</h2>
                <p class="mt-2 text-sm text-slate-300">Manage the catalog and pricing.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" href="{{ route('products.export') }}">Export CSV</a>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-50">Catalog</h3>
                <input
                    class="w-full max-w-xs rounded-full border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100"
                    type="search"
                    placeholder="Search products"
                    wire:model.debounce.400ms="search"
                />
            </div>
            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-800">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/70 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-3 text-slate-200">{{ $product->sku }}</td>
                                <td class="px-4 py-3 text-slate-100">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="rounded-full border border-slate-800 px-3 py-1 text-xs text-slate-200" wire:click="edit({{ $product->id }})">Edit</button>
                                        <button
                                            class="rounded-full border border-rose-500/40 px-3 py-1 text-xs text-rose-200"
                                            wire:click="delete({{ $product->id }})"
                                            onclick="return confirm('Delete this product?')"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-slate-400" colspan="5">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>

        <div class="space-y-6">
            <form class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6" wire:submit.prevent="save">
                <h3 class="text-lg font-semibold text-slate-50">{{ $editingId ? 'Edit product' : 'New product' }}</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">SKU</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="sku" />
                        @error('sku') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Name</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="name" />
                        @error('name') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Description</label>
                        <textarea class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" rows="3" wire:model.defer="description"></textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Price</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="number" step="0.01" wire:model.defer="price" />
                        @error('price') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-3 text-sm text-slate-200">
                        <input class="h-4 w-4 rounded border-slate-700 bg-slate-950" type="checkbox" wire:model.defer="is_active" />
                        Active
                    </label>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" type="submit">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                    <button class="rounded-full border border-slate-800 px-4 py-2 text-xs text-slate-200" type="button" wire:click="resetForm">Clear</button>
                </div>
            </form>

            <form class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6" wire:submit.prevent="import">
                <h3 class="text-lg font-semibold text-slate-50">Import products</h3>
                <p class="mt-2 text-sm text-slate-400">CSV columns: sku, name, description, price, is_active.</p>
                <input class="mt-4 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="file" wire:model="importFile" accept=".csv,.txt" />
                @error('importFile') <p class="mt-2 text-xs text-rose-300">{{ $message }}</p> @enderror
                <button class="mt-4 rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" type="submit">Import CSV</button>
            </form>
        </div>
    </div>
</section>
