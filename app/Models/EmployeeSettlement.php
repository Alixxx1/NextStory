<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EmployeeSettlement extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'settlement_date' => 'date:Y-m-d',
        'period_from' => 'date:Y-m-d',
        'period_to' => 'date:Y-m-d',
        'worked_days' => 'float',
        'accrued_salary' => 'float',
        'total_bonuses' => 'float',
        'total_deductions' => 'float',
        'total_advances' => 'float',
        'net_paid' => 'float',
        'recorded_in_expenses' => 'boolean',
        'details' => 'array',
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
}