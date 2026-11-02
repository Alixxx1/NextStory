<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SalesInvoice extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'invoice_date' => 'date:Y-m-d',
        'total_qty' => 'float',
        'store_sub_total' => 'float',
        'external_sub_total' => 'float',
        'gross_total' => 'float',
        'service_fee' => 'float',
        'invoice_discount' => 'float',
        'item_discount' => 'float',
        'net_value' => 'float',
        'store_net_total' => 'float',
        'cost_total' => 'float',
        'profit_total' => 'float',
        'returns_value' => 'float',
        'paid_amount' => 'float',
        'due_amount' => 'float',
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
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }

    public function returnTransactions()
    {
        return $this->hasMany(SalesReturn::class, 'sales_invoice_id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }
}
