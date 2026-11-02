<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Models\CustomerTransaction;
use App\Models\DeliveryZone;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShowController extends Controller
{
    /**
     * عرض شاشة حساب العميل (resources/views/customers/show.blade.php)
     */
    public function show()
    {
        return view('customers.show');
    }

    /**
     * جلب قائمة العملاء النشطين للبحث الفوري (Autocomplete F3)
     */
    public function getCustomersList()
    {
        $customers = Customer::orderBy('id', 'asc')->get()->map(function ($c) {
            return $this->formatCustomerBasic($c);
        });

        return response()->json([
            'ok'        => true,
            'customers' => $customers->values(),
        ]);
    }

    /**
     * جلب كافة تفاصيل حساب العميل والتبويبات الـ 9 مع تطبيق الفلترة بالتاريخ والمعادلة المحاسبية
     */
    public function getAccountDetails(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $dateMode = $request->query('date_mode', 'all'); // 'all' | 'range'
        $fromDate = $request->query('from_date');
        $toDate   = $request->query('to_date');

        // 1. جلب فواتير المبيعات الخاصة بالعميل (المغلقة والمعلقة للحجوزات)
        $invoicesQuery = SalesInvoice::with('items')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere('customer_name', $customer->name);
            });

        if ($dateMode === 'range' && $fromDate && $toDate) {
            $start = min($fromDate, $toDate);
            $end   = max($fromDate, $toDate);
            $invoicesQuery->whereBetween('invoice_date', [$start, $end]);
        }

        $allInvoices = $invoicesQuery->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        $closedInvoices    = $allInvoices->where('status', 'closed')->values();
        $suspendedInvoices = $allInvoices->where('status', 'suspended')->values();

        // 2. جلب سجل المرتجعات المستقل للعميل
        $returnsQuery = SalesReturn::with('items')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere('customer_name', $customer->name);
            });

        if ($dateMode === 'range' && $fromDate && $toDate) {
            $start = min($fromDate, $toDate);
            $end   = max($fromDate, $toDate);
            $returnsQuery->whereBetween('return_date', [$start, $end]);
        }

        $salesReturns = $returnsQuery->orderBy('return_date', 'desc')->orderBy('id', 'desc')->get();

        // 3. جلب حركات العميل المالية (دفعات payment، رد مبالغ refund، تسويات settlement، حجوزات reservation)
        $transQuery = CustomerTransaction::where('customer_id', $customer->id);
        if ($dateMode === 'range' && $fromDate && $toDate) {
            $start = min($fromDate, $toDate);
            $end   = max($fromDate, $toDate);
            $transQuery->whereBetween('transaction_date', [$start, $end]);
        }
        $transactions = $transQuery->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->get();

        // 4. جلب سجل ملحوظات العميل
        $notesQuery = CustomerNote::where('customer_id', $customer->id);
        if ($dateMode === 'range' && $fromDate && $toDate) {
            $start = min($fromDate, $toDate);
            $end   = max($fromDate, $toDate);
            $notesQuery->whereBetween('note_date', [$start, $end]);
        }
        $notes = $notesQuery->orderBy('note_date', 'desc')->orderBy('id', 'desc')->get();

        // 5. تطبيق المعادلة المحاسبية المعتمدة في المشروع:
        // الرصيد النهائي للعميل = opening_balance + (إجمالي المبيعات - المرتجعات) - (المدفوع بالفواتير + دفعات payment - المبالغ المردودة refund)
        $rawOpening = (float) ($customer->opening_balance ?? 0);
        $openingSigned = ($customer->balance_type === 'credit') ? -$rawOpening : $rawOpening;

        $totalSales = (float) $closedInvoices->sum(function ($inv) {
            return (float) ($inv->net_value ?: $inv->gross_total ?: 0);
        });

        $returnsFromInvoices = (float) $closedInvoices->sum('returns_value');
        $returnsFromTable    = (float) $salesReturns->sum('total');
        $totalReturns        = max($returnsFromInvoices, $returnsFromTable);

        $netSales  = $totalSales - $totalReturns;
        $orderPaid = (float) $closedInvoices->sum('paid_amount');

        $paymentsList    = $transactions->where('type', 'payment')->values();
        $refundsList     = $transactions->where('type', 'refund')->values();
        $settlementsList = $transactions->where('type', 'settlement')->values();
        $resTransList    = $transactions->where('type', 'reservation')->values();

        $extraPayments    = (float) $paymentsList->sum('amount');
        $totalRefunds     = (float) $refundsList->sum('amount');
        $totalSettlements = (float) $settlementsList->sum('amount');

        $netPayments  = $orderPaid + $extraPayments - $totalRefunds;
        $finalBalance = $openingSigned + $netSales - $netPayments - $totalSettlements;

        // حساب صافي الأرباح من العميل
        $totalCost = (float) $closedInvoices->sum('cost_total');
        $netProfit = $netSales - $totalCost;

        // تحديث حقل الرصيد في جدول العملاء إذا كنا نعرض كل الفترات
        if ($dateMode === 'all') {
            $this->syncCustomerBalanceField($customer, $finalBalance);
        }

        // تنسيق الطلبات والأصناف للعرض في التبويبات
        $formattedOrders = $closedInvoices->map(function ($inv) {
            return [
                'id'         => $inv->id,
                'orderNo'    => $inv->order_no ?: $inv->id,
                'date'       => $inv->invoice_date ?: optional($inv->created_at)->format('Y-m-d'),
                'type'       => $inv->order_type ?: 'استلام',
                'itemsCount' => (float) ($inv->total_qty ?: $inv->items->count()),
                'total'      => (float) ($inv->net_value ?: $inv->gross_total ?: 0),
                'paid'       => (float) ($inv->paid_amount ?? 0),
                'cost'       => (float) ($inv->cost_total ?? 0),
                'returns'    => (float) ($inv->returns_value ?? 0),
                'items'      => $inv->items->map(function ($item) {
                    return [
                        'id'    => $item->id,
                        'name'  => $item->name,
                        'qty'   => (float) $item->qty,
                        'unit'  => $item->unit ?: 'قطعة',
                        'price' => (float) $item->price,
                        'total' => (float) $item->total,
                    ];
                })->values(),
            ];
        })->values();

        $formattedReservations = $suspendedInvoices->map(function ($inv) {
            return [
                'orderNo'    => $inv->order_no ?: $inv->id,
                'date'       => $inv->invoice_date ?: optional($inv->created_at)->format('Y-m-d'),
                'type'       => $inv->order_type ?: 'حجز معلق',
                'itemsCount' => (float) ($inv->total_qty ?: $inv->items->count()),
                'total'      => (float) ($inv->net_value ?: $inv->gross_total ?: 0),
                'notes'      => $inv->notes ?: 'طلب معلق / محجوز',
            ];
        })->concat($resTransList->map(function ($t) {
            return [
                'orderNo'    => $t->ref_no ?: $t->id,
                'date'       => $t->transaction_date,
                'type'       => 'حجز',
                'itemsCount' => 1,
                'total'      => (float) $t->amount,
                'notes'      => $t->note ?: '-',
            ];
        }))->values();

        $formattedReturns = $salesReturns->map(function ($ret) {
            return [
                'returnNumber'  => $ret->return_number ?: $ret->id,
                'invoiceNumber' => $ret->invoice_number ?: '-',
                'date'          => $ret->return_date,
                'itemCount'     => (int) $ret->item_count,
                'summary'       => $ret->products_summary ?: '-',
                'total'         => (float) $ret->total,
                'returnType'    => $ret->return_type === 'full' ? 'مرتجع كلي' : 'مرتجع جزئي',
            ];
        })->values();

        $formattedPayments = $paymentsList->map(function ($p) {
            return [
                'id'     => $p->id,
                'date'   => $p->transaction_date,
                'amount' => (float) $p->amount,
                'method' => $p->payment_method ?: 'cash',
                'note'   => $p->note ?: 'تحصيل دفعة نقدية',
            ];
        })->values();

        $formattedRefunds = $refundsList->map(function ($r) {
            return [
                'id'     => $r->id,
                'date'   => $r->transaction_date,
                'amount' => (float) $r->amount,
                'note'   => $r->note ?: 'رد مبلغ للعميل',
            ];
        })->values();

        $formattedSettlements = $settlementsList->map(function ($s) {
            return [
                'id'     => $s->id,
                'date'   => $s->transaction_date,
                'amount' => (float) $s->amount,
                'note'   => $s->note ?: 'تسوية حساب',
            ];
        })->values();

        $formattedNotes = $notes->map(function ($n) {
            return [
                'id'   => $n->id,
                'date' => $n->note_date,
                'text' => $n->text,
                'user' => $n->user_name ?: 'مدير الفرع',
            ];
        })->values();

        return response()->json([
            'ok'       => true,
            'customer' => $this->formatCustomerBasic($customer->fresh()),
            'summary'  => [
                'opening'    => round($openingSigned, 2),
                'totalSales' => round($totalSales, 2),
                'returns'    => round($totalReturns, 2),
                'netSales'   => round($netSales, 2),
                'payments'   => round($netPayments, 2),
                'balance'    => round($finalBalance, 2),
                'netProfit'  => round($netProfit, 2),
            ],
            'orders'       => $formattedOrders,
            'reservations' => $formattedReservations,
            'returns'      => $formattedReturns,
            'payments'     => $formattedPayments,
            'refunds'      => $formattedRefunds,
            'settlements'  => $formattedSettlements,
            'notes'        => $formattedNotes,
        ]);
    }

    /**
     * التعديل السريع لبيانات العميل من نافذة القلم (F2) في شاشة حساب العميل
     */
    public function updateCustomerQuick(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'required|string|max:120',
            'area'   => 'nullable|string|max:120',
            'phone1' => 'required|string|max:30',
            'phone2' => 'nullable|string|max:30',
            'addr1'  => 'nullable|string|max:255',
            'addr2'  => 'nullable|string|max:255',
            'note'   => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($customer, $validated) {
            $areaName = trim((string) ($validated['area'] ?? ''));
            $zoneObj  = null;

            if ($areaName !== '' && $areaName !== '-') {
                $zoneObj = DeliveryZone::where('name', $areaName)->first();
                if (!$zoneObj) {
                    $zoneObj = DeliveryZone::create([
                        'uuid'      => (string) Str::uuid(),
                        'name'      => $areaName,
                        'fee'       => 0,
                        'is_synced' => false,
                    ]);
                }
            }

            $rawAddresses = is_array($customer->addresses)
                ? $customer->addresses
                : (json_decode($customer->addresses ?? '[]', true) ?: []);

            $defaultLabels = ['العنوان الرئيسي', 'عنوان بديل', 'عنوان بديل 2'];
            for ($i = 0; $i < 3; $i++) {
                if (!isset($rawAddresses[$i])) {
                    $rawAddresses[$i] = [
                        'label'        => $defaultLabels[$i],
                        'fullAddress'  => '',
                        'location'     => '',
                        'deliveryNote' => '',
                        'zoneId'       => '',
                        'zoneName'     => '',
                        'fee'          => 0,
                    ];
                }
            }

            $rawAddresses[0]['fullAddress'] = trim((string) ($validated['addr1'] ?? ''));
            if ($zoneObj) {
                $rawAddresses[0]['zoneId']   = (string) $zoneObj->id;
                $rawAddresses[0]['zoneName'] = $zoneObj->name;
                $rawAddresses[0]['fee']      = (float) $zoneObj->fee;
            } elseif ($areaName === '') {
                $rawAddresses[0]['zoneId']   = '';
                $rawAddresses[0]['zoneName'] = '';
            }

            $rawAddresses[1]['fullAddress'] = trim((string) ($validated['addr2'] ?? ''));

            $customer->update([
                'name'              => trim($validated['name']),
                'phone1'            => trim($validated['phone1']),
                'phone2'            => trim((string) ($validated['phone2'] ?? '')),
                'primary_zone_id'   => $zoneObj ? $zoneObj->id : $customer->primary_zone_id,
                'primary_zone_name' => $zoneObj ? $zoneObj->name : ($areaName ?: null),
                'addr1'             => trim((string) ($validated['addr1'] ?? '')) ?: null,
                'addr2'             => trim((string) ($validated['addr2'] ?? '')) ?: null,
                'contact_note'      => trim((string) ($validated['note'] ?? '')) ?: null,
                'invoice_note'      => trim((string) ($validated['note'] ?? '')) ?: null,
                'addresses'         => $rawAddresses,
                'is_synced'         => false,
            ]);

            return response()->json([
                'ok'       => true,
                'customer' => $this->formatCustomerBasic($customer->fresh()),
            ]);
        });
    }

    /**
     * تسجيل حركة مالية للعميل (تحصيل دفعة payment / رد مبلغ refund / تسوية settlement)
     */
    public function storeTransaction(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'type'           => 'required|in:payment,refund,settlement,reservation',
            'amount'         => 'required|numeric|min:0.01',
            'note'           => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|max:50',
        ]);

        return DB::transaction(function () use ($customer, $validated) {
            $defaultNotes = [
                'payment'     => 'تحصيل نقدي من العميل',
                'refund'      => 'رد مبلغ للعميل',
                'settlement'  => 'تسوية حساب',
                'reservation' => 'دفعة حجز',
            ];

            $transaction = CustomerTransaction::create([
                'uuid'             => (string) Str::uuid(),
                'customer_id'      => $customer->id,
                'type'             => $validated['type'],
                'transaction_date' => now()->format('Y-m-d'),
                'amount'           => (float) $validated['amount'],
                'payment_method'   => $validated['payment_method'] ?? 'cash',
                'safe_name'        => 'الخزينة الرئيسية',
                'ref_no'           => strtoupper(substr($validated['type'], 0, 3)) . '-' . time(),
                'note'             => trim((string) ($validated['note'] ?? '')) ?: $defaultNotes[$validated['type']],
                'user_name'        => 'مدير الفرع',
                'is_synced'        => false,
            ]);

            // إعادة حساب الرصيد النهائي للعميل وتحديثه في جدول customers
            $this->recalculateAndSaveCustomerBalance($customer);

            return response()->json([
                'ok'          => true,
                'transaction' => $transaction,
            ]);
        });
    }

    /**
     * إضافة ملحوظة جديدة في سجل ملحوظات العميل
     */
    public function storeNote(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $note = CustomerNote::create([
            'uuid'        => (string) Str::uuid(),
            'customer_id' => $customer->id,
            'note_date'   => now()->format('Y-m-d'),
            'text'        => trim($validated['text']),
            'user_name'   => 'مدير الفرع',
            'is_synced'   => false,
        ]);

        return response()->json([
            'ok'   => true,
            'note' => [
                'id'   => $note->id,
                'date' => $note->note_date,
                'text' => $note->text,
                'user' => $note->user_name,
            ],
        ]);
    }

    /**
     * إعادة حساب الرصيد النهائي للعميل حسب المعادلة المحاسبية وحفظه في جدول customers
     */
    private function recalculateAndSaveCustomerBalance(Customer $customer)
    {
        $rawOpening = (float) ($customer->opening_balance ?? 0);
        $openingSigned = ($customer->balance_type === 'credit') ? -$rawOpening : $rawOpening;

        $closedInvoices = SalesInvoice::where('status', 'closed')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere('customer_name', $customer->name);
            })->get();

        $totalSales = (float) $closedInvoices->sum(function ($inv) {
            return (float) ($inv->net_value ?: $inv->gross_total ?: 0);
        });

        $returnsFromInvoices = (float) $closedInvoices->sum('returns_value');
        $returnsFromTable    = (float) SalesReturn::where('customer_id', $customer->id)->sum('total');
        $totalReturns        = max($returnsFromInvoices, $returnsFromTable);

        $orderPaid = (float) $closedInvoices->sum('paid_amount');

        $extraPayments = (float) CustomerTransaction::where('customer_id', $customer->id)
            ->where('type', 'payment')->sum('amount');

        $totalRefunds = (float) CustomerTransaction::where('customer_id', $customer->id)
            ->where('type', 'refund')->sum('amount');

        $totalSettlements = (float) CustomerTransaction::where('customer_id', $customer->id)
            ->where('type', 'settlement')->sum('amount');

        $finalBalance = $openingSigned + ($totalSales - $totalReturns) - ($orderPaid + $extraPayments - $totalRefunds) - $totalSettlements;

        $this->syncCustomerBalanceField($customer, $finalBalance);
    }

    private function syncCustomerBalanceField(Customer $customer, float $finalBalance)
    {
        $newBalanceAbs  = round(abs($finalBalance), 2);
        $newBalanceType = $finalBalance < 0 ? 'credit' : 'debit';

        if ((float) $customer->balance !== $newBalanceAbs || $customer->balance_type !== $newBalanceType) {
            $customer->update([
                'balance'      => $newBalanceAbs,
                'balance_type' => $newBalanceType,
                'is_synced'    => false,
            ]);
        }
    }

    private function formatCustomerBasic(Customer $c)
    {
        $rawAddresses = is_array($c->addresses)
            ? $c->addresses
            : (json_decode($c->addresses ?? '[]', true) ?: []);

        $addr1 = $c->addr1 ?: ($rawAddresses[0]['fullAddress'] ?? '');
        $addr2 = $c->addr2 ?: ($rawAddresses[1]['fullAddress'] ?? '');
        $area  = $c->primary_zone_name ?: ($rawAddresses[0]['zoneName'] ?? '');

        return [
            'id'             => (string) $c->id,
            'uuid'           => $c->uuid,
            'code'           => (string) ($c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT)),
            'name'           => (string) $c->name,
            'phone1'         => (string) ($c->phone1 ?? ''),
            'phone2'         => (string) ($c->phone2 ?? ''),
            'area'           => (string) $area,
            'addr1'          => (string) $addr1,
            'addr2'          => (string) $addr2,
            'addr3'          => (string) ($c->addr3 ?? ''),
            'note'           => (string) ($c->invoice_note ?: $c->contact_note ?: ''),
            'openingBalance' => (float) ($c->opening_balance ?? 0),
            'balance'        => (float) ($c->balance ?? 0),
            'balanceType'    => $c->balance_type ?: 'debit',
        ];
    }
}
