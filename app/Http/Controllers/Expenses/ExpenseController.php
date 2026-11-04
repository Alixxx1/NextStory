<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    // 1. شاشة إدارة وتسجيل المصروفات (/expenses)
    public function index()
    {
        return view('expenses.index', $this->buildBootstrapPayload());
    }

    // 2. شاشة تقارير وسجل المصروفات (/expenses/reports)
    public function reports()
    {
        return view('expenses.reports', $this->buildBootstrapPayload());
    }

    // 3. API جلب الشجرة والمصروفات المحدثة
    public function bootstrapApi()
    {
        return response()->json($this->buildBootstrapPayload());
    }

    private function ensureDefaultTreeExists(): void
    {
        if (ExpenseAccount::count() > 0) {
            return;
        }

        $defaults = [
            [
                'name' => 'مصروفات تشغيل',
                'departments' => [
                    [
                        'id' => 'dep-general',
                        'name' => 'عام',
                        'items' => ['أكياس وشنط تعبئة', 'ضيافة وشاي وقهوة', 'منظفات ومطهرات', 'أدوات مكتبية وبكر كاشير', 'إكراميات ونثريات'],
                    ],
                    [
                        'id' => 'dep-wages',
                        'name' => 'أجور ومرتبات',
                        'items' => ['مرتب شهري', 'سلفة عامل', 'يوميات عمال', 'حوافز وإضافي'],
                    ],
                    [
                        'id' => 'dep-utilities',
                        'name' => 'مرافق وفواتير محل',
                        'items' => ['فاتورة كهرباء', 'فاتورة مياه', 'اشتراك إنترنت وتليفون', 'إيجار المحل', 'غاز'],
                    ],
                    [
                        'id' => 'dep-maint',
                        'name' => 'صيانة ونقل',
                        'items' => ['صيانة ثلاجات ومعدات', 'مشال وتعتيق بضاعة', 'مواصلات ونقل'],
                    ],
                ],
            ],
            [
                'name' => 'مصروفات تأسيس',
                'departments' => [
                    [
                        'id' => 'dep-equip',
                        'name' => 'تجهيزات ومعدات',
                        'items' => ['أرفف واستاندات عرض', 'أجهزة كاشير وباركود', 'ثلاجات وموازين', 'كاميرات مراقبة'],
                    ],
                    [
                        'id' => 'dep-decor',
                        'name' => 'ديكور وتراخيص',
                        'items' => ['تشطيبات ودهانات', 'يفط وإعلانات', 'رسوم وتراخيص حكومية'],
                    ],
                ],
            ],
        ];

        foreach ($defaults as $acc) {
            ExpenseAccount::create([
                'uuid' => (string) Str::uuid(),
                'name' => $acc['name'],
                'departments' => $acc['departments'],
                'is_synced' => false,
            ]);
        }
    }

    private function buildBootstrapPayload(): array
    {
        $this->ensureDefaultTreeExists();

        $tree = ExpenseAccount::orderBy('id')
            ->get()
            ->map(fn (ExpenseAccount $acc) => [
                'id' => 'acc-' . $acc->id,
                'dbId' => $acc->id,
                'name' => $acc->name,
                'departments' => is_array($acc->departments) ? $acc->departments : [],
            ])
            ->values()
            ->all();

        $expenses = Expense::withTrashed()
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Expense $exp) => $this->formatExpense($exp))
            ->values()
            ->all();

        $drawerTotal = (float) Expense::where('source', 'الدرج')
            ->whereDate('expense_date', now()->toDateString())
            ->sum('amount');

        return [
            'initialTree' => $tree,
            'initialExpenses' => $expenses,
            'drawerTotal' => round($drawerTotal, 2),
        ];
    }

    private function formatExpense(Expense $exp): array
    {
        $dateIso = $exp->expense_date ? $exp->expense_date->format('Y-m-d') : now()->format('Y-m-d');
        $fallbackTime = $exp->created_at ? $exp->created_at->format('Y/m/d h:iA') : ($dateIso . ' 12:00PM');

        return [
            'id' => (string) $exp->id,
            'date' => $dateIso,
            'accountName' => $exp->account_name ?: 'مصروفات تشغيل',
            'departmentName' => $exp->department_name ?: 'عام',
            'itemName' => $exp->item_name ?: 'مصروف عام',
            'description' => $exp->description ?? '',
            'amount' => (float) $exp->amount,
            'source' => $exp->source ?: 'الدرج',
            'createdAt' => $exp->created_time_label ?: $fallbackTime,
            'createdBy' => $exp->created_by ?: 'مدير الفرع',
            'updatedAt' => $exp->updated_time_label ?: ($exp->updated_at ? $exp->updated_at->format('Y/m/d h:iA') : $fallbackTime),
            'updatedBy' => $exp->updated_by ?: 'مدير الفرع',
            'shiftName' => $exp->shift_name ?: 'مدير الفرع // 06:00AM',
            'shiftOpen' => (bool) $exp->shift_open,
            'deleted' => $exp->trashed(),
        ];
    }

    // 4. حفظ وتحديث شجرة (الحسابات / الأقسام / البنود) في قاعدة البيانات
    public function saveTree(Request $request)
    {
        $data = $request->validate([
            'tree' => 'required|array|min:1',
            'tree.*.name' => 'required|string|max:120',
            'tree.*.departments' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($data) {
            $keptIds = [];

            foreach ($data['tree'] as $accData) {
                $name = trim($accData['name']);
                $depts = $accData['departments'] ?? [];

                $existing = ExpenseAccount::where('name', $name)->first();
                if ($existing) {
                    $existing->update([
                        'departments' => $depts,
                        'is_synced' => false,
                    ]);
                    $keptIds[] = $existing->id;
                } else {
                    $created = ExpenseAccount::create([
                        'uuid' => (string) Str::uuid(),
                        'name' => $name,
                        'departments' => $depts,
                        'is_synced' => false,
                    ]);
                    $keptIds[] = $created->id;
                }
            }

            if (!empty($keptIds)) {
                ExpenseAccount::whereNotIn('id', $keptIds)->delete();
            }

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }

    // 5. إضافة أو تعديل مصروف في قاعدة البيانات
    public function saveExpense(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable',
            'date' => 'required|date',
            'accountName' => 'required|string|max:120',
            'departmentName' => 'required|string|max:120',
            'itemName' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|gt:0',
            'source' => 'required|string|max:80',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['id']) && is_numeric($data['id']) ? (int) $data['id'] : null;
            $nowLabel = now()->format('Y/m/d h:iA');

            if ($editingId) {
                $expense = Expense::withTrashed()->lockForUpdate()->findOrFail($editingId);
                if ($expense->trashed() || !$expense->shift_open) {
                    return response()->json([
                        'message' => 'لا يمكن تعديل هذا المصروف لأن الوردية الخاصة به قد تم إغلاقها أو أنه محذوف.'
                    ], 422);
                }

                $expense->update([
                    'expense_date' => $data['date'],
                    'account_name' => trim($data['accountName']),
                    'department_name' => trim($data['departmentName']),
                    'item_name' => trim($data['itemName']),
                    'description' => trim($data['description'] ?? ''),
                    'amount' => round((float) $data['amount'], 2),
                    'source' => trim($data['source']),
                    'updated_by' => 'مدير الفرع',
                    'updated_time_label' => $nowLabel,
                    'is_synced' => false,
                ]);
            } else {
                $expense = Expense::create([
                    'uuid' => (string) Str::uuid(),
                    'expense_date' => $data['date'],
                    'account_name' => trim($data['accountName']),
                    'department_name' => trim($data['departmentName']),
                    'item_name' => trim($data['itemName']),
                    'description' => trim($data['description'] ?? ''),
                    'amount' => round((float) $data['amount'], 2),
                    'source' => trim($data['source']),
                    'shift_name' => 'مدير الفرع // 06:00AM',
                    'shift_open' => true,
                    'created_by' => 'مدير الفرع',
                    'updated_by' => 'مدير الفرع',
                    'created_time_label' => $nowLabel,
                    'updated_time_label' => $nowLabel,
                    'is_synced' => false,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'expense' => $this->formatExpense($expense->fresh()),
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }

    // 6. حذف مصروف (SoftDelete)
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $expense = Expense::findOrFail($id);
            if (!$expense->shift_open) {
                return response()->json([
                    'message' => 'لا يمكن حذف مصروف في وردية مغلقة!'
                ], 422);
            }

            $expense->update(['is_synced' => false]);
            $expense->delete();

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }
}
