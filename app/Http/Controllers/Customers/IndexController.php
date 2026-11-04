<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    /**
     * عرض شاشة قائمة العملاء الموحدة (resources/views/customers/index.blade.php)
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * عرض شاشة مناطق التوصيل وأسعارها
     */
    public function deliveryAreasView()
    {
        return view('customers.delivery_areas');
    }

    /**
     * عرض شاشة كشف حساب العميل
     */
    public function showView()
    {
        return view('customers.show');
    }

    /**
     * جلب بيانات العملاء (شاملة المحذوفات للفلترة) + مناطق التوصيل من قاعدة البيانات
     */
    public function getInitData()
    {
        if (DeliveryZone::count() === 0) {
            DeliveryZone::create([
                'uuid'      => (string) Str::uuid(),
                'name'      => 'المنطقة الأولى',
                'fee'       => 15,
                'is_synced' => false,
            ]);
            DeliveryZone::create([
                'uuid'      => (string) Str::uuid(),
                'name'      => 'المنطقة الثانية',
                'fee'       => 20,
                'is_synced' => false,
            ]);
        }

        $zones = DeliveryZone::orderBy('id', 'asc')->get()->map(function (DeliveryZone $z) {
            return [
                'id'        => (string) $z->id,
                'uuid'      => $z->uuid,
                'name'      => $z->name,
                'fee'       => (float) $z->fee,
                'is_synced' => (bool) $z->is_synced,
            ];
        });

        $customers = Customer::withTrashed()
            ->orderBy('id', 'asc')
            ->get()
            ->map(function (Customer $c) use ($zones) {
                return $this->formatCustomerResponse($c, $zones);
            });

        return response()->json([
            'ok'        => true,
            'status'    => 'success',
            'zones'     => $zones->values(),
            'customers' => $customers->values(),
        ]);
    }

    /**
     * إضافة أو تعديل بيانات عميل مع عناوينه الـ 3
     */
    public function saveCustomer(Request $request)
    {
        $validated = $request->validate([
            'id'          => 'nullable',
            'code'        => 'nullable|string|max:50',
            'name'        => 'required|string|max:150',
            'phone1'      => 'required|string|max:30',
            'phone2'      => 'nullable|string|max:30',
            'contactNote' => 'nullable|string|max:500',
            'invoiceNote' => 'nullable|string|max:500',
            'balance'     => 'nullable|numeric|min:0',
            'balanceType' => 'nullable|in:debit,credit',
            'addresses'   => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $customerId = $request->input('id');
            $customer = null;

            if (!empty($customerId) && is_numeric($customerId)) {
                $customer = Customer::withTrashed()->find($customerId);
            }

            $rawAddresses = $request->input('addresses', []);
            $defaultLabels = ['العنوان الرئيسي', 'عنوان بديل', 'عنوان بديل 2'];
            $normalizedAddresses = [];

            for ($i = 0; $i < 3; $i++) {
                $addrItem = $rawAddresses[$i] ?? [];
                $zoneId = !empty($addrItem['zoneId']) && is_numeric($addrItem['zoneId']) ? (int) $addrItem['zoneId'] : null;
                $zoneObj = $zoneId ? DeliveryZone::find($zoneId) : null;

                if (!$zoneObj && !empty($addrItem['zoneName'])) {
                    $zoneObj = DeliveryZone::where('name', trim((string) $addrItem['zoneName']))->first();
                }

                $normalizedAddresses[] = [
                    'label'        => $addrItem['label'] ?? $defaultLabels[$i],
                    'fullAddress'  => trim((string) ($addrItem['fullAddress'] ?? '')),
                    'location'     => trim((string) ($addrItem['location'] ?? '')),
                    'deliveryNote' => trim((string) ($addrItem['deliveryNote'] ?? '')),
                    'zoneId'       => $zoneObj ? (string) $zoneObj->id : '',
                    'zoneName'     => $zoneObj ? $zoneObj->name : trim((string) ($addrItem['zoneName'] ?? '')),
                    'fee'          => $zoneObj ? (float) $zoneObj->fee : (float) ($addrItem['fee'] ?? 0),
                ];
            }

            $code = trim((string) ($validated['code'] ?? ''));
            if ($code === '') {
                $maxCodeNumeric = Customer::withTrashed()
                    ->pluck('code')
                    ->map(fn ($val) => (int) preg_replace('/\D/', '', (string) $val))
                    ->max() ?? 0;
                $maxId = Customer::withTrashed()->max('id') ?? 0;
                $nextNum = max($maxCodeNumeric, $maxId) + 1;
                $code = str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);
            }

            $balanceVal = isset($validated['balance']) ? (float) $validated['balance'] : 0.0;
            $balanceType = $validated['balanceType'] ?? 'debit';

            $primaryAddr = $normalizedAddresses[0];
            $primaryZoneId = !empty($primaryAddr['zoneId']) && is_numeric($primaryAddr['zoneId'])
                ? (int) $primaryAddr['zoneId']
                : null;

            $payload = [
                'code'              => $code,
                'name'              => trim($validated['name']),
                'phone1'            => trim($validated['phone1']),
                'phone2'            => trim((string) ($validated['phone2'] ?? '')),
                'contact_note'      => trim((string) ($validated['contactNote'] ?? '')),
                'invoice_note'      => trim((string) ($validated['invoiceNote'] ?? '')),
                'primary_zone_id'   => $primaryZoneId,
                'primary_zone_name' => $primaryAddr['zoneName'] ?: null,
                'addr1'             => $normalizedAddresses[0]['fullAddress'] ?: null,
                'addr2'             => $normalizedAddresses[1]['fullAddress'] ?: null,
                'addr3'             => $normalizedAddresses[2]['fullAddress'] ?: null,
                'addresses'         => $normalizedAddresses,
                'balance'           => $balanceVal,
                'balance_type'      => $balanceType,
                'is_synced'         => false,
            ];

            if ($customer) {
                $customer->update($payload);
            } else {
                $payload['uuid'] = (string) Str::uuid();
                $payload['opening_balance'] = $balanceVal;
                $customer = Customer::create($payload);
            }

            $zones = DeliveryZone::orderBy('id', 'asc')->get()->map(function (DeliveryZone $z) {
                return [
                    'id'   => (string) $z->id,
                    'name' => $z->name,
                    'fee'  => (float) $z->fee,
                ];
            });

            return response()->json([
                'ok'       => true,
                'status'   => 'success',
                'message'  => 'تم حفظ بيانات العميل بنجاح',
                'customer' => $this->formatCustomerResponse($customer->fresh(), $zones),
            ]);
        });
    }

    /**
     * حذف عميل (Soft Delete) أو استرجاعه من المحذوفات
     */
    public function toggleDeleteCustomer($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);

        if ($customer->trashed()) {
            $customer->restore();
            $customer->update(['is_synced' => false]);
            $action = 'restored';
        } else {
            $customer->update(['is_synced' => false]);
            $customer->delete();
            $action = 'deleted';
        }

        $zones = DeliveryZone::orderBy('id', 'asc')->get()->map(function (DeliveryZone $z) {
            return [
                'id'   => (string) $z->id,
                'name' => $z->name,
                'fee'  => (float) $z->fee,
            ];
        });

        return response()->json([
            'ok'       => true,
            'status'   => 'success',
            'action'   => $action,
            'customer' => $this->formatCustomerResponse($customer->fresh(), $zones),
        ]);
    }

    /**
     * إضافة منطقة توصيل سريعة من داخل مودال العميل (+)
     */
    public function quickCreateZone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'fee'  => 'required|numeric|min:0',
        ]);

        $cleanName = trim($validated['name']);
        $existing = DeliveryZone::where('name', $cleanName)->first();

        if ($existing) {
            $existing->update([
                'fee'       => (float) $validated['fee'],
                'is_synced' => false,
            ]);
            $zone = $existing;
        } else {
            $zone = DeliveryZone::create([
                'uuid'      => (string) Str::uuid(),
                'name'      => $cleanName,
                'fee'       => (float) $validated['fee'],
                'is_synced' => false,
            ]);
        }

        return response()->json([
            'ok'     => true,
            'status' => 'success',
            'zone'   => [
                'id'        => (string) $zone->id,
                'uuid'      => $zone->uuid,
                'name'      => $zone->name,
                'fee'       => (float) $zone->fee,
                'is_synced' => false,
            ],
        ]);
    }

    /**
     * تنسيق كائن العميل ليتطابق 100% مع هيكل الواجهة الأمامية وشاشة الكاشير
     */
    private function formatCustomerResponse(Customer $c, $zones): array
    {
        $rawAddresses = is_array($c->addresses)
            ? $c->addresses
            : (json_decode((string) ($c->addresses ?? '[]'), true) ?: []);

        $defaultLabels = ['العنوان الرئيسي', 'عنوان بديل', 'عنوان بديل 2'];
        $fallbackColumns = [$c->addr1, $c->addr2, $c->addr3];

        $addresses = [];
        for ($i = 0; $i < 3; $i++) {
            $a = $rawAddresses[$i] ?? [];
            $zoneId = (string) ($a['zoneId'] ?? ($i === 0 && $c->primary_zone_id ? $c->primary_zone_id : ''));
            $zoneName = (string) ($a['zoneName'] ?? ($i === 0 && $c->primary_zone_name ? $c->primary_zone_name : ''));

            $matchedZone = $zones->first(function ($z) use ($zoneId, $zoneName) {
                return ($zoneId !== '' && (string) $z['id'] === $zoneId) || ($zoneName !== '' && $z['name'] === $zoneName);
            });

            $addresses[] = [
                'label'        => $a['label'] ?? $defaultLabels[$i],
                'fullAddress'  => (string) ($a['fullAddress'] ?? $fallbackColumns[$i] ?? ''),
                'location'     => (string) ($a['location'] ?? ''),
                'deliveryNote' => (string) ($a['deliveryNote'] ?? ''),
                'zoneId'       => $matchedZone ? (string) $matchedZone['id'] : $zoneId,
                'zoneName'     => $matchedZone ? $matchedZone['name'] : $zoneName,
                'fee'          => $matchedZone ? (float) $matchedZone['fee'] : (float) ($a['fee'] ?? 0),
            ];
        }

        return [
            'id'             => (string) $c->id,
            'uuid'           => $c->uuid,
            'code'           => (string) ($c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT)),
            'name'           => (string) $c->name,
            'phone1'         => (string) ($c->phone1 ?? ''),
            'phone2'         => (string) ($c->phone2 ?? ''),
            'contactNote'    => (string) ($c->contact_note ?? ''),
            'invoiceNote'    => (string) ($c->invoice_note ?? ''),
            'addresses'      => $addresses,
            'area'           => $addresses[0]['zoneName'] ?: '-',
            'deliveryZoneId' => $addresses[0]['zoneId'] ?: '',
            'deliveryZone'   => $addresses[0]['zoneName'] ?: '',
            'deliveryFee'    => (float) ($addresses[0]['fee'] ?? 0),
            'addr1'          => $addresses[0]['fullAddress'] ?: '',
            'addr2'          => $addresses[1]['fullAddress'] ?: '',
            'addr3'          => $addresses[2]['fullAddress'] ?: '',
            'note'           => (string) ($c->invoice_note ?: $c->contact_note ?: ''),
            'openingBalance' => (float) ($c->opening_balance ?? 0),
            'balance'        => (float) ($c->balance ?? 0),
            'balanceType'    => $c->balance_type ?: 'debit',
            'deleted'        => !is_null($c->deleted_at),
        ];
    }
}
