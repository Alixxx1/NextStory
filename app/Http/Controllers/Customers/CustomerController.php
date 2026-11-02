<?php
// مناطق التوصثل

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DeliveryZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * عرض شاشة مناطق التوصيل وأسعارها
     */
    public function deliveryAreas()
    {
        return view('customers.delivery_areas');
    }

    /**
     * جلب جميع مناطق التوصيل مع حساب عدد العملاء المرتبطين بكل منطقة
     * GET /api/customers/delivery-zones
     */
    public function getDeliveryZones(Request $request): JsonResponse
    {
        $zones = DeliveryZone::orderBy('id', 'asc')->get();

        // جلب العملاء غير المحذوفين لحساب الارتباط سواء عبر primary_zone_id أو داخل مصفوفة addresses الثلاثية
        $activeCustomers = Customer::whereNull('deleted_at')
            ->select(['id', 'primary_zone_id', 'primary_zone_name', 'addresses'])
            ->get();

        $formattedZones = $zones->map(function (DeliveryZone $zone) use ($activeCustomers) {
            $linkedCount = $this->countLinkedCustomersForZone($zone, $activeCustomers);

            return [
                'id'              => $zone->id,
                'uuid'            => $zone->uuid,
                'name'            => $zone->name,
                'fee'             => (float) $zone->fee,
                'customers_count' => $linkedCount,
                'is_synced'       => (bool) $zone->is_synced,
            ];
        });

        $totalZones = $formattedZones->count();
        $avgFee = $totalZones > 0 ? round($formattedZones->avg('fee'), 2) : 0;

        return response()->json([
            'ok'    => true,
            'zones' => $formattedZones,
            'stats' => [
                'total_zones' => $totalZones,
                'avg_fee'     => $avgFee,
            ],
        ]);
    }

    /**
     * إضافة منطقة توصيل جديدة
     * POST /api/customers/delivery-zones
     */
    public function storeDeliveryZone(Request $request): JsonResponse
    {
        $cleanName = trim((string) $request->input('name', ''));
        $request->merge(['name' => $cleanName]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:80',
                'not_regex:/^\d+$/',
                Rule::unique('delivery_zones', 'name'),
            ],
            'fee' => ['required', 'numeric', 'min:0', 'max:5000'],
        ], [
            'name.required'  => 'يرجى إدخال اسم المنطقة (هذا الحقل مطلوب).',
            'name.min'       => 'اسم المنطقة يجب أن يتكون من حرفين على الأقل.',
            'name.max'       => 'اسم المنطقة طويل جداً (الحد الأقصى 80 حرفاً).',
            'name.not_regex' => 'لا يمكن أن يتكون اسم المنطقة من أرقام فقط.',
            'name.unique'    => 'اسم المنطقة موجود بالفعل في القائمة.',
            'fee.required'   => 'يرجى إدخال سعر التوصيل (أو 0 للتوصيل المجاني).',
            'fee.numeric'    => 'أدخل مبلغاً رقمياً صحيحاً (مثال: 25 أو 25.50).',
            'fee.min'        => 'سعر التوصيل لا يمكن أن يكون سالباً.',
            'fee.max'        => 'سعر التوصيل لا يمكن أن يتجاوز 5000 ج.م.',
        ]);

        $zone = DeliveryZone::create([
            'uuid'      => (string) Str::uuid(),
            'name'      => $validated['name'],
            'fee'       => round((float) $validated['fee'], 2),
            'is_synced' => false,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'تمت إضافة المنطقة بنجاح',
            'zone'    => [
                'id'              => $zone->id,
                'uuid'            => $zone->uuid,
                'name'            => $zone->name,
                'fee'             => (float) $zone->fee,
                'customers_count' => 0,
                'is_synced'       => false,
            ],
        ], 201);
    }

    /**
     * تعديل منطقة توصيل وتحديث أسعار التوصيل تلقائياً في عناوين العملاء المرتبطين
     * PUT /api/customers/delivery-zones/{id}
     */
    public function updateDeliveryZone(Request $request, $id): JsonResponse
    {
        $zone = DeliveryZone::findOrFail($id);
        $oldName = $zone->name;

        $cleanName = trim((string) $request->input('name', ''));
        $request->merge(['name' => $cleanName]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:80',
                'not_regex:/^\d+$/',
                Rule::unique('delivery_zones', 'name')->ignore($zone->id),
            ],
            'fee' => ['required', 'numeric', 'min:0', 'max:5000'],
        ], [
            'name.required'  => 'يرجى إدخال اسم المنطقة (هذا الحقل مطلوب).',
            'name.min'       => 'اسم المنطقة يجب أن يتكون من حرفين على الأقل.',
            'name.max'       => 'اسم المنطقة طويل جداً (الحد الأقصى 80 حرفاً).',
            'name.not_regex' => 'لا يمكن أن يتكون اسم المنطقة من أرقام فقط.',
            'name.unique'    => 'اسم المنطقة موجود بالفعل في القائمة.',
            'fee.required'   => 'يرجى إدخال سعر التوصيل (أو 0 للتوصيل المجاني).',
            'fee.numeric'    => 'أدخل مبلغاً رقمياً صحيحاً (مثال: 25 أو 25.50).',
            'fee.min'        => 'سعر التوصيل لا يمكن أن يكون سالباً.',
            'fee.max'        => 'سعر التوصيل لا يمكن أن يتجاوز 5000 ج.م.',
        ]);

        $newFee = round((float) $validated['fee'], 2);

        DB::transaction(function () use ($zone, $oldName, $validated, $newFee) {
            $zone->update([
                'name'      => $validated['name'],
                'fee'       => $newFee,
                'is_synced' => false,
            ]);

            // مزامنة الاسم والسعر الجديد مع العملاء المرتبطين ومصفوفة العناوين الـ 3 (addresses JSON)
            $customers = Customer::withTrashed()->get();

            foreach ($customers as $customer) {
                $changed = false;

                if ((string) $customer->primary_zone_id === (string) $zone->id || $customer->primary_zone_name === $oldName) {
                    $customer->primary_zone_id = $zone->id;
                    $customer->primary_zone_name = $zone->name;
                    $changed = true;
                }

                $addresses = is_array($customer->addresses)
                    ? $customer->addresses
                    : json_decode((string) $customer->addresses, true);

                if (is_array($addresses)) {
                    foreach ($addresses as $idx => $addr) {
                        $addrZoneId = $addr['zoneId'] ?? null;
                        $addrZoneName = $addr['zoneName'] ?? null;

                        if ((string) $addrZoneId === (string) $zone->id || ($addrZoneName && $addrZoneName === $oldName)) {
                            $addresses[$idx]['zoneId'] = $zone->id;
                            $addresses[$idx]['zoneName'] = $zone->name;
                            $addresses[$idx]['fee'] = $newFee;
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        $customer->addresses = $addresses;
                    }
                }

                if ($changed) {
                    $customer->is_synced = false;
                    $customer->save();
                }
            }
        });

        $activeCustomers = Customer::whereNull('deleted_at')
            ->select(['id', 'primary_zone_id', 'primary_zone_name', 'addresses'])
            ->get();

        return response()->json([
            'ok'      => true,
            'message' => 'تم تعديل المنطقة بنجاح',
            'zone'    => [
                'id'              => $zone->id,
                'uuid'            => $zone->uuid,
                'name'            => $zone->name,
                'fee'             => (float) $zone->fee,
                'customers_count' => $this->countLinkedCustomersForZone($zone, $activeCustomers),
                'is_synced'       => false,
            ],
        ]);
    }

    /**
     * حذف منطقة توصيل (يمنع الحذف إذا كانت مرتبطة بأي عميل)
     * DELETE /api/customers/delivery-zones/{id}
     */
    public function destroyDeliveryZone($id): JsonResponse
    {
        $zone = DeliveryZone::findOrFail($id);

        $activeCustomers = Customer::whereNull('deleted_at')
            ->select(['id', 'primary_zone_id', 'primary_zone_name', 'addresses'])
            ->get();

        $linkedCount = $this->countLinkedCustomersForZone($zone, $activeCustomers);

        if ($linkedCount > 0) {
            return response()->json([
                'ok'      => false,
                'message' => "لا يمكن حذف «{$zone->name}» لأنها مرتبطة بـ ({$linkedCount}) عميل!",
            ], 422);
        }

        $zone->delete();

        return response()->json([
            'ok'      => true,
            'message' => 'تم حذف المنطقة',
        ]);
    }

    /**
     * حساب عدد العملاء المرتبطين بمنطقة معينة (عبر primary_zone_id أو داخل JSON العناوين الثلاثة)
     */
    private function countLinkedCustomersForZone(DeliveryZone $zone, $customers): int
    {
        $count = 0;

        foreach ($customers as $customer) {
            $isLinked = false;

            if ((string) $customer->primary_zone_id === (string) $zone->id || $customer->primary_zone_name === $zone->name) {
                $isLinked = true;
            } else {
                $addresses = is_array($customer->addresses)
                    ? $customer->addresses
                    : json_decode((string) $customer->addresses, true);

                if (is_array($addresses)) {
                    foreach ($addresses as $addr) {
                        if (
                            (isset($addr['zoneId']) && (string) $addr['zoneId'] === (string) $zone->id) ||
                            (isset($addr['zoneName']) && $addr['zoneName'] === $zone->name)
                        ) {
                            $isLinked = true;
                            break;
                        }
                    }
                }
            }

            if ($isLinked) {
                $count++;
            }
        }

        return $count;
    }
}
