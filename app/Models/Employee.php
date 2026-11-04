<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
        'hire_date' => 'date:Y-m-d',
        'accrual_start_date' => 'date:Y-m-d',
        'termination_date' => 'date:Y-m-d',
        'base_salary' => 'float',
        'work_hours' => 'float',
        'daily_rate' => 'float',
        'hourly_rate' => 'float',
        'documents' => 'array',
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

    public function transactions()
    {
        return $this->hasMany(EmployeeTransaction::class, 'employee_id')->orderByDesc('transaction_date')->orderByDesc('id');
    }

    public function settlements()
    {
        return $this->hasMany(EmployeeSettlement::class, 'employee_id')->orderByDesc('settlement_date')->orderByDesc('id');
    }
}