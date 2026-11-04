<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StockAdjustment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'adjustment_date' => 'date:Y-m-d',
        'items_count' => 'integer',
        'net_value' => 'float',
        'surplus_qty' => 'float',
        'surplus_cost' => 'float',
        'surplus_sale' => 'float',
        'deficit_qty' => 'float',
        'deficit_cost' => 'float',
        'deficit_sale' => 'float',
        'rows' => 'array',
        'is_synced' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}