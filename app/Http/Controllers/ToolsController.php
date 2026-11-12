<?php

namespace App\Http\Controllers;

use App\Support\SystemSettings;
use Illuminate\Support\Facades\DB;

/** أدوات الطباعة: استيكرات باركود الأصناف وكروت الموظفين، وباركود الموازين للأصناف الموزونة */
class ToolsController extends Controller
{
    public function barcodePrint()
    {
        return view('tools.barcode_print', [
            'items' => $this->items(),
            'employees' => DB::table('employees')->whereNull('deleted_at')->where('status', 'active')->orderBy('name')
                ->get(['id', 'code', 'name', 'job_title', 'phone'])->map(fn ($e) => ['id' => $e->id, 'code' => $e->code, 'name' => $e->name, 'job' => $e->job_title, 'phone' => $e->phone]),
            'shop' => SystemSettings::get('shop_name', 'NextStore'),
        ]);
    }

    public function scaleBarcodePrint()
    {
        return view('tools.scale_barcode_print', [
            'items' => $this->items()->filter(fn ($i) => in_array($i['unit'], ['كيلو', 'لتر'], true))->values(),
            'shop' => SystemSettings::get('shop_name', 'NextStore'),
        ]);
    }

    private function items()
    {
        return DB::table('items')->whereNull('deleted_at')->where('is_disabled', false)->orderByDesc('is_pinned')->orderBy('name')
            ->get(['id', 'name', 'barcode', 'retail_price', 'unit', 'qty', 'sub_units'])
            ->map(fn ($i) => [
                'id' => $i->id, 'name' => $i->name, 'barcode' => (string) $i->barcode, 'price' => (float) $i->retail_price, 'unit' => $i->unit ?: 'قطعة',
                'qty' => (float) $i->qty, 'packs' => array_values(array_filter(json_decode($i->sub_units ?: '[]', true) ?: [], fn ($u) => !empty($u['barcode']))),
            ]);
    }
}
