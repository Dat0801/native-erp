<?php

namespace App\Modules\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'customer_id',
        'sale_number',
        'sale_date',
        'status',
        'subtotal',
        'tax',
        'total',
    ];
}
