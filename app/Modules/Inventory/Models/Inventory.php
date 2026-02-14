<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use SoftDeletes;

    protected $table = 'inventories';

    protected $fillable = [
        'product_id',
        'location',
        'quantity_on_hand',
        'quantity_reserved',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
