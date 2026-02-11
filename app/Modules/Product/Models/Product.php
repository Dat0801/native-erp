<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'is_active',
    ];
}
