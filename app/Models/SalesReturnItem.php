<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'float',
        'total' => 'float',
    ];
}
