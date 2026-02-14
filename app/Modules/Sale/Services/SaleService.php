<?php

namespace App\Modules\Sale\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Sale\Models\Sale;
use App\Modules\Sale\Models\SaleItem;
use App\Modules\Sale\Repositories\SaleRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleService
{
    public function __construct(private SaleRepository $repository)
    {
    }

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->repository->query()->with(['items.product', 'customer']);

        if ($search) {
            $query->where('sale_number', 'like', "%{$search}%");
        }

        return $query->orderBy('sale_date', 'desc')->paginate($perPage);
    }

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data): Sale {
            $items = Arr::get($data, 'items', []);
            $totals = $this->calculateTotals($items);

            $sale = $this->repository->create(array_merge(
                Arr::except($data, ['items']),
                $totals
            ));

            $this->syncItems($sale, $items);

            return $sale;
        });
    }

    public function update(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data): Sale {
            $items = Arr::get($data, 'items', []);
            $totals = $this->calculateTotals($items);

            $this->repository->update($sale, array_merge(
                Arr::except($data, ['items']),
                $totals
            ));

            $sale->items()->delete();
            $this->syncItems($sale, $items);

            return $sale;
        });
    }

    public function delete(Sale $sale): void
    {
        $this->repository->delete($sale);
    }

    public function exportCsv(): StreamedResponse
    {
        $sales = $this->repository->query()
            ->with('items.product')
            ->orderBy('id')
            ->get();

        return response()->streamDownload(
            function () use ($sales): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'sale_number',
                    'sale_date',
                    'status',
                    'customer_id',
                    'item_sku',
                    'quantity',
                    'unit_price',
                ]);

                foreach ($sales as $sale) {
                    foreach ($sale->items as $item) {
                        fputcsv($handle, [
                            $sale->sale_number,
                            $sale->sale_date,
                            $sale->status,
                            $sale->customer_id,
                            $item->product?->sku,
                            $item->quantity,
                            $item->unit_price,
                        ]);
                    }
                }

                fclose($handle);
            },
            'sales-' . now()->format('Ymd-His') . '.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function importCsv(UploadedFile $file): int
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return 0;
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);

            return 0;
        }

        $columns = array_flip($header);
        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        if (empty($rows)) {
            return 0;
        }

        $grouped = [];

        foreach ($rows as $row) {
            $saleNumber = $row[$columns['sale_number'] ?? -1] ?? null;
            $saleDate = $row[$columns['sale_date'] ?? -1] ?? null;
            $itemSku = $row[$columns['item_sku'] ?? -1] ?? null;

            if (!$saleNumber || !$saleDate || !$itemSku) {
                continue;
            }

            $grouped[$saleNumber]['sale_number'] = trim((string) $saleNumber);
            $grouped[$saleNumber]['sale_date'] = $saleDate;
            $grouped[$saleNumber]['status'] = $row[$columns['status'] ?? -1] ?? 'draft';
            $grouped[$saleNumber]['customer_id'] = $row[$columns['customer_id'] ?? -1] ?? null;

            $grouped[$saleNumber]['items'][] = [
                'sku' => trim((string) $itemSku),
                'quantity' => (int) ($row[$columns['quantity'] ?? -1] ?? 1),
                'unit_price' => (float) ($row[$columns['unit_price'] ?? -1] ?? 0),
            ];
        }

        $count = 0;

        foreach ($grouped as $payload) {
            $items = [];

            foreach ($payload['items'] as $item) {
                $product = Product::query()->where('sku', $item['sku'])->first();

                if (!$product) {
                    continue;
                }

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ];
            }

            if (empty($items)) {
                continue;
            }

            $data = [
                'customer_id' => $payload['customer_id'] ?: null,
                'sale_number' => $payload['sale_number'],
                'sale_date' => $payload['sale_date'],
                'status' => $payload['status'] ?: 'draft',
                'items' => $items,
            ];

            $sale = Sale::query()->where('sale_number', $payload['sale_number'])->first();

            if ($sale) {
                $this->update($sale, $data);
            } else {
                $this->create($data);
            }

            $count++;
        }

        return $count;
    }

    private function calculateTotals(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += (float) $item['quantity'] * (float) $item['unit_price'];
        }

        $tax = 0;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ];
    }

    private function syncItems(Sale $sale, array $items): void
    {
        foreach ($items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => (float) $item['quantity'] * (float) $item['unit_price'],
            ]);
        }
    }
}
