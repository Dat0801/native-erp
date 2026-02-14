<?php

namespace App\Modules\Inventory\Repositories;

use App\Modules\Inventory\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryRepository
{
	public function query(): Builder
	{
		return Inventory::query();
	}

	public function paginate(int $perPage = 15): LengthAwarePaginator
	{
		return $this->query()->paginate($perPage);
	}

	public function create(array $data): Inventory
	{
		return Inventory::create($data);
	}

	public function update(Inventory $inventory, array $data): Inventory
	{
		$inventory->update($data);

		return $inventory;
	}

	public function delete(Inventory $inventory): void
	{
		$inventory->delete();
	}
}
