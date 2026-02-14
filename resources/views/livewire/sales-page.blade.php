<section class="space-y-6">
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-50">Sales</h2>
                <p class="mt-2 text-sm text-slate-300">Track orders and invoices.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" href="{{ route('sales.export') }}">Export CSV</a>
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
                <h3 class="text-lg font-semibold text-slate-50">Recent sales</h3>
                <input
                    class="w-full max-w-xs rounded-full border border-slate-800 bg-slate-950 px-4 py-2 text-sm text-slate-100"
                    type="search"
                    placeholder="Search by sale number"
                    wire:model.debounce.400ms="search"
                />
            </div>
            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-800">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/70 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Sale #</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="px-4 py-3 text-slate-100">{{ $sale->sale_number }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ $sale->sale_date }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ ucfirst($sale->status) }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ number_format($sale->total, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="rounded-full border border-slate-800 px-3 py-1 text-xs text-slate-200" wire:click="edit({{ $sale->id }})">Edit</button>
                                        <button
                                            class="rounded-full border border-rose-500/40 px-3 py-1 text-xs text-rose-200"
                                            wire:click="delete({{ $sale->id }})"
                                            onclick="return confirm('Delete this sale?')"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-slate-400" colspan="5">No sales yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $sales->links() }}</div>
        </div>

        <div class="space-y-6">
            <form class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6" wire:submit.prevent="save">
                <h3 class="text-lg font-semibold text-slate-50">{{ $editingId ? 'Edit sale' : 'New sale' }}</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Sale Number</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="sale_number" />
                        @error('sale_number') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Sale Date</label>
                        <input class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="date" wire:model.defer="sale_date" />
                        @error('sale_date') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Customer</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="customer_id">
                            <option value="">Walk-in</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Status</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" wire:model.defer="status">
                            <option value="draft">Draft</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="paid">Paid</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-200">Items</h4>
                        <button class="rounded-full border border-slate-800 px-3 py-1 text-xs text-slate-200" type="button" wire:click="addItemRow">Add item</button>
                    </div>
                    <div class="mt-3 space-y-3">
                        @foreach ($items as $index => $item)
                            <div class="grid gap-2 rounded-2xl border border-slate-800 bg-slate-950/70 p-3 md:grid-cols-[2fr_1fr_1fr_auto]">
                                <div>
                                    <select class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm" wire:model.defer="items.{{ $index }}.product_id">
                                        <option value="">Select product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                    @error("items.{$index}.product_id") <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <input class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm" type="number" min="1" wire:model.defer="items.{{ $index }}.quantity" />
                                    @error("items.{$index}.quantity") <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <input class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm" type="number" min="0" step="0.01" wire:model.defer="items.{{ $index }}.unit_price" />
                                    @error("items.{$index}.unit_price") <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex items-center justify-end">
                                    <button class="rounded-full border border-rose-500/40 px-3 py-1 text-xs text-rose-200" type="button" wire:click="removeItemRow({{ $index }})">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-right text-sm text-slate-300">Subtotal: {{ number_format($this->subtotal, 2) }}</div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" type="submit">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                    <button class="rounded-full border border-slate-800 px-4 py-2 text-xs text-slate-200" type="button" wire:click="resetForm">Clear</button>
                </div>
            </form>

            <form class="rounded-3xl border border-slate-800 bg-slate-950/50 p-6" wire:submit.prevent="import">
                <h3 class="text-lg font-semibold text-slate-50">Import sales</h3>
                <p class="mt-2 text-sm text-slate-400">CSV columns: sale_number, sale_date, status, customer_id, item_sku, quantity, unit_price.</p>
                <input class="mt-4 w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-2 text-sm" type="file" wire:model="importFile" accept=".csv,.txt" />
                @error('importFile') <p class="mt-2 text-xs text-rose-300">{{ $message }}</p> @enderror
                <button class="mt-4 rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-xs text-emerald-100" type="submit">Import CSV</button>
            </form>
        </div>
    </div>
</section>
