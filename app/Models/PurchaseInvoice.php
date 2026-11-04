<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseInvoice extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'code' => 'integer',
        'invoice_date' => 'date:Y-m-d',
        'product_count' => 'integer',
        'sub_total' => 'float',
        'invoice_discount' => 'float',
        'invoice_extra' => 'float',
        'total' => 'float',
        'paid' => 'float',
        'remaining' => 'float',
        'returns_value' => 'float',
        'shift_open' => 'boolean',
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
        return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }
}
