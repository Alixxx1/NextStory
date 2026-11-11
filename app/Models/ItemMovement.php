<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ItemMovement extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'before_qty' => 'float',
        'in_qty' => 'float',
        'out_qty' => 'float',
        'after_qty' => 'float',
        'price' => 'float',
        'is_synced' => 'boolean',
        'moved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->moved_at)) {
                $model->moved_at = now();
            }
        });
    }
}
