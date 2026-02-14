<?php

namespace App\Modules\Sale\Repositories;

use App\Modules\Sale\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleRepository
{
	public function query(): Builder
	{
		return Sale::query();
	}

	public function paginate(int $perPage = 15): LengthAwarePaginator
	{
		return $this->query()->paginate($perPage);
	}

	public function create(array $data): Sale
	{
		return Sale::create($data);
	}

	public function update(Sale $sale, array $data): Sale
	{
		$sale->update($data);

		return $sale;
	}

	public function delete(Sale $sale): void
	{
		$sale->delete();
	}
}
