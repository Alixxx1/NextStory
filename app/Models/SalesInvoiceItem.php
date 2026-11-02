<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'qty' => 'float',
        'returned_qty' => 'float',
        'cost' => 'float',
        'price' => 'float',
        'total' => 'float',
        'is_external' => 'boolean',
    ];
}
