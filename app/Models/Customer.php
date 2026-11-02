<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'addresses' => 'array',
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
        return $this->hasMany(SalesInvoice::class, 'customer_id');
    }

    public function returns()
    {
        return $this->hasMany(SalesReturn::class, 'customer_id');
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class, 'customer_id')->orderByDesc('id');
    }

    public function notes()
    {
        return $this->hasMany(CustomerNote::class, 'customer_id')->orderByDesc('id');
    }
}
