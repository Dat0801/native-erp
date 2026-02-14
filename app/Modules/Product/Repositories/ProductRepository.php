<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
	public function query(): Builder
	{
		return Product::query();
	}

	public function paginate(int $perPage = 15): LengthAwarePaginator
	{
		return $this->query()->paginate($perPage);
	}

	public function create(array $data): Product
	{
		return Product::create($data);
	}

	public function update(Product $product, array $data): Product
	{
		$product->update($data);

		return $product;
	}

	public function delete(Product $product): void
	{
		$product->delete();
	}

	public function findBySku(string $sku): ?Product
	{
		return Product::query()->where('sku', $sku)->first();
	}
}
