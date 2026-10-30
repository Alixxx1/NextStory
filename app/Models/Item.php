<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'qty' => 'float',
        'last_buy_price' => 'float',
        'avg_cost' => 'float',
        'min_stock' => 'float',
        'max_stock' => 'float',
        'retail_price' => 'float',
        'semi_wholesale_price' => 'float',
        'wholesale_price' => 'float',
        'is_published_online' => 'boolean',
        'online_price' => 'float',
        'online_compare_at_price' => 'float',
        'sub_units' => 'array',
        'tax_included' => 'boolean',
        'tax_rate' => 'float',
        'is_disabled' => 'boolean',
        'is_pinned' => 'boolean',
        'allow_price_edit' => 'boolean',
        'has_commission' => 'boolean',
        'commission_val' => 'float',
        'has_purchase_invoice' => 'boolean',
        'extra_attributes' => 'array',
        'is_synced' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::updating(function ($model) {
            // أي تعديل محلي يجعل السجل غير متزامن لكي يرفع تلقائياً عند الاتصال بالسحابة
            $model->is_synced = false;
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withTrashed();
    }

    public function movements()
    {
        return $this->hasMany(ItemMovement::class, 'item_id')->orderBy('id', 'desc');
    }
}
