<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Support\PosAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** الفواتير المعلقة (F9): حفظ واسترجاع وحذف من قاعدة البيانات */
class SuspendedInvoiceController extends Controller
{
    public function index()
    {
        return response()->json(['invoices' => DB::table('suspended_invoices')->orderByDesc('updated_at')->pluck('snapshot')
            ->map(fn ($s) => json_decode($s, true))->filter()->values()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'snapshot' => 'required|array',
            'snapshot.id' => 'required|string|max:60',
            'snapshot.total' => 'nullable|numeric',
        ]);
        $s = (array) $request->input('snapshot'); // اللقطة كاملة (validated() يحذف المفاتيح غير المذكورة في القواعد)
        if (strlen(json_encode($s)) > 2_000_000) {
            return response()->json(['ok' => false, 'message' => 'حجم الفاتورة المعلقة كبير جداً.'], 422);
        }
        $existing = DB::table('suspended_invoices')->where('client_id', $s['id'])->first();
        $row = [
            'invoice_number' => (string) ($s['invoiceNumber'] ?? ''),
            'customer_name'  => mb_substr((string) ($s['customerName'] ?? ''), 0, 150),
            'total'          => round((float) ($s['total'] ?? 0), 2),
            'snapshot'       => json_encode($s, JSON_UNESCAPED_UNICODE),
            'shift_id'       => PosAuth::openShift()->id ?? null,
            'updated_at'     => now(),
        ];
        if ($existing) {
            DB::table('suspended_invoices')->where('id', $existing->id)->update($row);
        } else {
            DB::table('suspended_invoices')->insert($row + [
                'client_id' => $s['id'], 'created_by_id' => PosAuth::id(), 'created_by' => PosAuth::name(), 'created_at' => now(),
            ]);
        }
        return response()->json(['ok' => true]);
    }

    /** الحذف: لصاحب الفاتورة المعلقة أو لمن لديه صلاحية حذف أصناف/فواتير معلقة */
    public function destroy(string $clientId)
    {
        $row = DB::table('suspended_invoices')->where('client_id', $clientId)->first();
        if (!$row) {
            return response()->json(['ok' => true]);
        }
        if ((int) $row->created_by_id !== (int) PosAuth::id() && !PosAuth::can('pos.delete_item')) {
            return response()->json(['ok' => false, 'message' => 'حذف فاتورة معلقة لمستخدم آخر يحتاج تصريح المدير.', 'permission' => 'pos.delete_item'], 403);
        }
        \App\Support\SecurityAlert::raise('suspended_deleted', "حذف فاتورة معلقة #{$row->invoice_number}", ['العميل' => $row->customer_name, 'أنشأها' => $row->created_by], (float) $row->total, (string) $row->invoice_number);
        DB::table('suspended_invoices')->where('id', $row->id)->delete();
        return response()->json(['ok' => true]);
    }
}
