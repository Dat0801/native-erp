<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductService
{
    public function __construct(private ProductRepository $repository)
    {
    }

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->repository->query();

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return $this->repository->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->repository->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->repository->delete($product);
    }

    public function exportCsv(): StreamedResponse
    {
        $products = $this->repository->query()
            ->orderBy('id')
            ->get(['sku', 'name', 'description', 'price', 'is_active']);

        return response()->streamDownload(
            function () use ($products): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['sku', 'name', 'description', 'price', 'is_active']);

                foreach ($products as $product) {
                    fputcsv($handle, [
                        $product->sku,
                        $product->name,
                        $product->description,
                        $product->price,
                        $product->is_active ? 1 : 0,
                    ]);
                }

                fclose($handle);
            },
            'products-' . now()->format('Ymd-His') . '.csv',
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
            $sku = $row[$columns['sku'] ?? -1] ?? null;
            $name = $row[$columns['name'] ?? -1] ?? null;

            if (!$sku || !$name) {
                continue;
            }

            $data = [
                'sku' => trim((string) $sku),
                'name' => trim((string) $name),
                'description' => $row[$columns['description'] ?? -1] ?? null,
                'price' => (float) ($row[$columns['price'] ?? -1] ?? 0),
                'is_active' => (bool) ($row[$columns['is_active'] ?? -1] ?? true),
            ];

            Product::updateOrCreate(['sku' => $data['sku']], $data);
            $count++;
        }

        fclose($handle);

        return $count;
    }
}
