<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DeliveryZone extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'fee' => 'float',
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

    public function customers()
    {
        return $this->hasMany(Customer::class, 'primary_zone_id');
    }
}
