<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'float',
        'returned_qty' => 'float',
        'cost' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'sell_price' => 'float',
        'original_sell_price' => 'float',
    ];
}
