<section class="space-y-6">
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-50">Inventory</h2>
                <p class="mt-2 text-sm text-slate-300">Monitor stock levels and movements.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" href="{{ route('inventory.export') }}">Export CSV</a>
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
                <h3 class="text-lg font-semibold text-slate-50">Stock</h3>
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
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">On Hand</th>
                            <th class="px-4 py-3">Reserved</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($inventories as $inventory)
                            <tr>
                                <td class="px-4 py-3 text-slate-100">
                                    {{ $inventory->product?->name }}
                                    <span class="text-xs text-slate-500">({{ $inventory->product?->sku }})</span>
                                </td>
                                <td class="px-4 py-3 text-slate-200">{{ $inventory->location ?? 'Main' }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ $inventory->quantity_on_hand }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ $inventory->quantity_reserved }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="rounded-full border border-slate-800 px-3 py-1 text-xs text-slate-200" wire:click="edit({{ $inventory->id }})">Edit</button>
                                        <button
                                            class="rounded-full border border-rose-500/40 px-3 py-1 text-xs text-rose-200"
                                            wire:click="delete({{ $inventory->id }})"
                                            onclick="return confirm('Delete this inventory row?')"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-slate-400" colspan="5">No inventory data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $inventories->links() }}</div>
        </div>

        <div class="space-y-6">
            <form class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6" wire:submit.prevent="save">
                <h3 class="text-lg font-semibold text-slate-50">{{ $editingId ? 'Edit inventory' : 'New inventory' }}</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Product</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="product_id">
                            <option value="">Select a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Location</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="location" />
                        @error('location') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Quantity On Hand</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="number" wire:model.defer="quantity_on_hand" />
                        @error('quantity_on_hand') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Quantity Reserved</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="number" wire:model.defer="quantity_reserved" />
                        @error('quantity_reserved') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" type="submit">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                    <button class="rounded-full border border-slate-800 px-4 py-2 text-xs text-slate-200" type="button" wire:click="resetForm">Clear</button>
                </div>
            </form>

            <form class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6" wire:submit.prevent="import">
                <h3 class="text-lg font-semibold text-slate-50">Import inventory</h3>
                <p class="mt-2 text-sm text-slate-400">CSV columns: product_sku, location, quantity_on_hand, quantity_reserved.</p>
                <input class="mt-4 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="file" wire:model="importFile" accept=".csv,.txt" />
                @error('importFile') <p class="mt-2 text-xs text-rose-300">{{ $message }}</p> @enderror
                <button class="mt-4 rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" type="submit">Import CSV</button>
            </form>
        </div>
    </div>
</section>
