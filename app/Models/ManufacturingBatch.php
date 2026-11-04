<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ManufacturingBatch extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'total_weight_kg' => 'float',
        'total_batch_cost' => 'float',
        'cost_per_kg' => 'float',
        'profit_percent' => 'float',
        'selling_price' => 'float',
        'components' => 'array',
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

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withTrashed();
    }
}