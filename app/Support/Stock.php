<?php

namespace App\Support;

use App\Models\Item;
use App\Models\ItemMovement;
use Illuminate\Support\Facades\DB;

/**
 * كل تغيير في رصيد صنف يمر من هنا: قفل الصف + تحديث ذري (qty = qty + Δ) + حركة صنف.
 * يجب استدعاؤه داخل DB::transaction.
 */
class Stock
{
    public static function move(?Item $item, float $delta, string $type, string $ref, string $party, float $price = 0, ?string $user = null): ?Item
    {
        if (!$item || abs($delta) < 0.0000001) {
            return $item;
        }

        $locked = Item::whereKey($item->id)->lockForUpdate()->first();
        $before = (float) $locked->qty;
        Item::whereKey($item->id)->update([
            'qty' => DB::raw('qty + (' . round($delta, 3) . ')'),
            'last_move_date' => now()->format('Y-m-d'),
            'is_synced' => false,
        ]);
        $after = round($before + $delta, 3);

        ItemMovement::create([
            'item_id' => $item->id,
            'move_date' => now()->format('Y/m/d h:i A'),
            'moved_at' => now(),
            'type' => $type,
            'ref_no' => $ref,
            'party' => $party,
            'user_name' => $user ?? PosAuth::name(),
            'before_qty' => $before,
            'in_qty' => $delta > 0 ? $delta : 0,
            'out_qty' => $delta < 0 ? -$delta : 0,
            'after_qty' => $after,
            'price' => $price,
        ]);

        return $locked->setAttribute('qty', $after);
    }

    /** يبحث عن الصنف بالـ id ثم الباركود */
    public static function find($itemId, ?string $barcode): ?Item
    {
        $item = $itemId ? Item::with('category')->find($itemId) : null;
        if (!$item && $barcode) {
            $item = Item::with('category')->where('barcode', $barcode)->first();
        }
        return $item;
    }

    /** تكلفة الوحدة المعتمدة: المتوسط المرجح، وإن لم يوجد فآخر سعر شراء */
    public static function unitCost(Item $item): float
    {
        return (float) $item->avg_cost > 0 ? (float) $item->avg_cost : (float) $item->last_buy_price;
    }

    /** تحديث المتوسط المرجح عند دخول كمية بتكلفة معينة (أو خروجها عند عكس شراء بقيمة سالبة) */
    public static function applyWeightedCost(Item $item, float $qtyIn, float $unitCost): float
    {
        $locked = Item::whereKey($item->id)->lockForUpdate()->first();
        $onHand = max(0, (float) $locked->qty);
        $oldAvg = self::unitCost($locked);
        $newQty = $onHand + $qtyIn;
        $avg = $newQty > 0.0005
            ? max(0, ($onHand * $oldAvg + $qtyIn * $unitCost) / $newQty)
            : $unitCost;
        $avg = round($avg, 2);
        Item::whereKey($item->id)->update(['avg_cost' => $avg]);
        return $avg;
    }
}
