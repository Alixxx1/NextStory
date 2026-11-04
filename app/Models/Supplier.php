<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'opening_balance' => 'float',
        'balance' => 'float',
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

    public function invoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'supplier_id');
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class, 'supplier_id')->orderByDesc('id');
    }
}
