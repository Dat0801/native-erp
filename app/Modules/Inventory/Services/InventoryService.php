<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Repositories\InventoryRepository;
use App\Modules\Product\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryService
{
    public function __construct(private InventoryRepository $repository)
    {
    }

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->repository->query()->with('product');

        if ($search) {
            $query->whereHas('product', function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function create(array $data): Inventory
    {
        return $this->repository->create($data);
    }

    public function update(Inventory $inventory, array $data): Inventory
    {
        return $this->repository->update($inventory, $data);
    }

    public function delete(Inventory $inventory): void
    {
        $this->repository->delete($inventory);
    }

    public function exportCsv(): StreamedResponse
    {
        $inventories = $this->repository->query()
            ->with('product')
            ->orderBy('id')
            ->get(['product_id', 'location', 'quantity_on_hand', 'quantity_reserved']);

        return response()->streamDownload(
            function () use ($inventories): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['product_sku', 'location', 'quantity_on_hand', 'quantity_reserved']);

                foreach ($inventories as $inventory) {
                    fputcsv($handle, [
                        optional($inventory->product)->sku,
                        $inventory->location,
                        $inventory->quantity_on_hand,
                        $inventory->quantity_reserved,
                    ]);
                }

                fclose($handle);
            },
            'inventory-' . now()->format('Ymd-His') . '.csv',
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
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $sku = $row[$columns['product_sku'] ?? -1] ?? null;

            if (!$sku) {
                continue;
            }

            $product = Product::query()->where('sku', trim((string) $sku))->first();

            if (!$product) {
                continue;
            }

            $data = [
                'product_id' => $product->id,
                'location' => $row[$columns['location'] ?? -1] ?? null,
                'quantity_on_hand' => (int) ($row[$columns['quantity_on_hand'] ?? -1] ?? 0),
                'quantity_reserved' => (int) ($row[$columns['quantity_reserved'] ?? -1] ?? 0),
            ];

            Inventory::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'location' => $data['location'],
                ],
                $data
            );

            $count++;
        }

        fclose($handle);

        return $count;
    }
}
