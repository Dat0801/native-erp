<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
