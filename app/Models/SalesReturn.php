<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SalesReturn extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'original_date' => 'date:Y-m-d',
        'return_date' => 'date:Y-m-d',
        'item_count' => 'integer',
        'total' => 'float',
        'cash_refund' => 'float',
        'account_credit' => 'float',
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
        return $this->hasMany(SalesReturnItem::class, 'sales_return_id');
    }

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id')->withTrashed();
    }
}
