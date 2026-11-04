<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseReturn extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'return_date' => 'date:Y-m-d',
        'item_count' => 'integer',
        'total' => 'float',
        'cash_refund' => 'float',
        'account_deduction' => 'float',
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

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }
}
