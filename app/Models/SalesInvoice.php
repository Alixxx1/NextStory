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
        'rounding_adjustment' => 'float',
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

    /** صافي السطر بعد نصيبه من خصم الفاتورة والتقريب (للفواتير القديمة يُحسب نصيب الخصم بالنسبة) */
    public function lineNet(SalesInvoiceItem $line): float
    {
        if ($line->net_total !== null) {
            return (float) $line->net_total;
        }
        $storeSub = (float) $this->store_sub_total;
        $total = (float) $line->total;
        if ($line->is_external || $storeSub <= 0) {
            return $total;
        }
        return round($total - (float) $this->invoice_discount * $total / $storeSub, 2);
    }

    /** إيراد وتكلفة أصناف المحل المتبقية بعد المرتجعات */
    public function remainingStoreRevenueAndCost(): array
    {
        $revenue = 0.0;
        $cost = 0.0;
        foreach ($this->items as $line) {
            $qty = (float) $line->qty;
            if ($line->is_external || $qty <= 0) {
                continue;
            }
            $remain = max(0, $qty - (float) $line->returned_qty);
            $revenue += $this->lineNet($line) * $remain / $qty;
            $cost += (float) $line->cost * $remain;
        }
        return [round($revenue, 2), round($cost, 2)];
    }

    /** الربح = إيراد أصناف المحل المتبقية − تكلفتها */
    public function computeProfit(): float
    {
        [$revenue, $cost] = $this->remainingStoreRevenueAndCost();
        return round($revenue - $cost, 2);
    }
}

