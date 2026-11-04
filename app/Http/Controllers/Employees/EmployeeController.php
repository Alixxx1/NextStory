<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSettlement;
use App\Models\EmployeeTransaction;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    private function ensureEmployeesTablesSchema(): void
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();

                $table->string('code', 50)->nullable()->index();
                $table->string('name', 150)->index();
                $table->string('national_id', 20)->nullable()->index();
                $table->date('birth_date')->nullable();
                $table->unsignedInteger('age')->default(0);

                $table->string('phone', 30)->index();
                $table->string('emergency_phone', 30)->nullable();
                $table->string('emergency_relation', 100)->nullable();
                $table->string('address', 255)->nullable();

                $table->string('job_title', 100)->default('كاشير')->index();
                $table->string('shift_type', 60)->default('الوردية الصباحية');
                $table->date('hire_date')->nullable()->index();
                $table->date('accrual_start_date')->nullable()->index();

                $table->decimal('base_salary', 14, 2)->default(0);
                $table->decimal('work_hours', 5, 2)->default(8);
                $table->decimal('daily_rate', 14, 2)->default(0);
                $table->decimal('hourly_rate', 14, 2)->default(0);
                $table->unsignedTinyInteger('weekly_off_days')->default(1);

                $table->json('documents')->nullable();
                $table->text('notes')->nullable();

                $table->string('status', 30)->default('active')->index();
                $table->date('termination_date')->nullable();
                $table->timestamp('terminated_at')->nullable();
                $table->decimal('paused_days', 8, 2)->default(0);
                $table->string('termination_reason', 500)->nullable();

                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        } else {
            // إضافة أعمدة تتبع توقيت الفصل الدقيق وأيام التوقف تلقائياً للجدول الحالي
            if (!Schema::hasColumn('employees', 'terminated_at')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->timestamp('terminated_at')->nullable()->after('termination_date');
                });
            }
            if (!Schema::hasColumn('employees', 'paused_days')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->decimal('paused_days', 8, 2)->default(0)->after('terminated_at');
                });
            }
        }

        if (!Schema::hasTable('employee_transactions')) {
            Schema::create('employee_transactions', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();

                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('transaction_date')->index();
                $table->string('type', 40)->index();
                $table->decimal('hours_count', 8, 2)->default(0);
                $table->decimal('days_count', 8, 2)->default(0);
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('source', 80)->default('الدرج');
                $table->string('reason', 500)->nullable();
                $table->string('user_name', 100)->default('مدير الفرع');

                $table->boolean('is_settled')->default(false)->index();
                $table->unsignedBigInteger('settlement_id')->nullable()->index();

                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_settlements')) {
            Schema::create('employee_settlements', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();

                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('ref_no', 60)->nullable()->index();
                $table->date('settlement_date')->index();
                $table->date('period_from')->nullable();
                $table->date('period_to')->nullable();

                $table->decimal('worked_days', 8, 2)->default(0);
                $table->decimal('accrued_salary', 14, 2)->default(0);
                $table->decimal('total_bonuses', 14, 2)->default(0);
                $table->decimal('total_deductions', 14, 2)->default(0);
                $table->decimal('total_advances', 14, 2)->default(0);
                $table->decimal('net_paid', 14, 2)->default(0);

                $table->string('source', 80)->default('الدرج');
                $table->boolean('recorded_in_expenses')->default(false);
                $table->unsignedBigInteger('expense_id')->nullable();
                $table->string('notes', 500)->nullable();
                $table->string('user_name', 100)->default('مدير الفرع');
                $table->json('details')->nullable();

                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function createView()
    {
        return view('employees.create', $this->buildBootstrapPayload());
    }

    public function logView()
    {
        return view('employees.log', $this->buildBootstrapPayload());
    }

    public function bootstrapApi()
    {
        return response()->json($this->buildBootstrapPayload());
    }

    private function buildBootstrapPayload(): array
    {
        $this->ensureEmployeesTablesSchema();

        $employees = Employee::with(['transactions', 'settlements'])
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Employee $emp) => $this->formatEmployee($emp))
            ->values()
            ->all();

        $nextId = (Employee::withTrashed()->max('id') ?? 0) + 1;
        $nextCode = 'EMP-' . str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);

        return [
            'initialEmployees' => $employees,
            'initialNextCode' => $nextCode,
        ];
    }

    private function formatEmployee(Employee $emp): array
    {
        $baseSalary = (float) $emp->base_salary;
        $workHours = max(1, min(12, (float) ($emp->work_hours ?: 8)));
        $dailyRate = round($baseSalary / 30, 2);
        $hourlyRate = round($dailyRate / $workHours, 2);

        $birthDateStr = $emp->birth_date ? $emp->birth_date->format('Y-m-d') : '';
        $age = $birthDateStr ? Carbon::parse($birthDateStr)->age : (int) $emp->age;

        $hireDateStr = $emp->hire_date ? $emp->hire_date->format('Y-m-d') : now()->format('Y-m-d');
        $accrualStartStr = $emp->accrual_start_date ? $emp->accrual_start_date->format('Y-m-d') : $hireDateStr;

        $startCarbon = Carbon::parse($accrualStartStr)->startOfDay();

        // إذا كان الموظف مفصولاً يتجمد عداد أيامه عند تاريخ الفصل ولا يصفر حقه!
        $endCarbon = ($emp->status === 'terminated' && $emp->termination_date)
            ? Carbon::parse($emp->termination_date)->startOfDay()
            : now()->startOfDay();

        $rawCalendarDays = $endCarbon->gte($startCarbon)
            ? ($startCarbon->diffInDays($endCarbon) + 1)
            : 0;

        $pausedDays = (float) ($emp->paused_days ?? 0);
        $autoWorkedDays = max(0, round($rawCalendarDays - $pausedDays, 2));

        // حساب عدد الساعات المنقضية منذ لحظة الفصل إذا كان مفصولاً حالياً
        $terminatedAtIso = $emp->terminated_at ? Carbon::parse($emp->terminated_at)->toIso8601String() : null;
        $terminatedTimeLabel = $emp->terminated_at ? Carbon::parse($emp->terminated_at)->format('Y/m/d h:i A') : '';
        $hoursSinceTermination = 0.0;
        $daysSinceTermination = 0;

        if ($emp->status === 'terminated' && $emp->terminated_at) {
            $termCarbon = Carbon::parse($emp->terminated_at);
            $minutesDiff = max(0, $termCarbon->diffInMinutes(now()));
            $hoursSinceTermination = round($minutesDiff / 60, 2);
            $daysSinceTermination = max(0, $termCarbon->copy()->startOfDay()->diffInDays(now()->startOfDay()));
        }

        $unsettledTxs = $emp->transactions->where('is_settled', false)->values();

        $bonusesSum = 0.0;
        $deductionsSum = 0.0;
        $advancesSum = 0.0;
        $deductedHoursTotal = 0.0;
        $deductedDaysTotal = 0.0;

        foreach ($unsettledTxs as $tx) {
            $amt = (float) $tx->amount;
            if (in_array($tx->type, ['bonus', 'overtime_hours'])) {
                $bonusesSum += $amt;
            } elseif (in_array($tx->type, ['deduction_hours', 'deduction_days', 'deduction_amount'])) {
                $deductionsSum += $amt;
                $deductedHoursTotal += (float) $tx->hours_count;
                $deductedDaysTotal += (float) $tx->days_count;
            } elseif (in_array($tx->type, ['advance', 'store_withdrawal'])) {
                $advancesSum += $amt;
            }
        }

        $accruedSalary = round($autoWorkedDays * $dailyRate, 2);
        $netPayable = round($accruedSalary + $bonusesSum - $deductionsSum - $advancesSum, 2);

        return [
            'id' => (string) $emp->id,
            'code' => $emp->code ?: ('EMP-' . str_pad((string) $emp->id, 3, '0', STR_PAD_LEFT)),
            'name' => $emp->name,
            'nationalId' => $emp->national_id ?? '',
            'birthDate' => $birthDateStr,
            'age' => $age,
            'phone' => $emp->phone ?? '',
            'emergencyPhone' => $emp->emergency_phone ?? '',
            'emergencyRelation' => $emp->emergency_relation ?? '',
            'address' => $emp->address ?? '',
            'jobTitle' => $emp->job_title ?: 'كاشير',
            'shiftType' => $emp->shift_type ?: 'الوردية الصباحية',
            'hireDate' => $hireDateStr,
            'accrualStartDate' => $accrualStartStr,
            'baseSalary' => $baseSalary,
            'workHours' => $workHours,
            'dailyRate' => $dailyRate,
            'hourlyRate' => $hourlyRate,
            'weeklyOffDays' => (int) ($emp->weekly_off_days ?? 1),
            'documents' => is_array($emp->documents) ? $emp->documents : [],
            'notes' => $emp->notes ?? '',
            'status' => $emp->status ?: 'active',
            'terminationDate' => $emp->termination_date ? $emp->termination_date->format('Y-m-d') : '',
            'terminatedAt' => $terminatedAtIso,
            'terminatedTimeLabel' => $terminatedTimeLabel,
            'hoursSinceTermination' => $hoursSinceTermination,
            'daysSinceTermination' => $daysSinceTermination,
            'pausedDays' => $pausedDays,
            'terminationReason' => $emp->termination_reason ?? '',

            'currentPeriod' => [
                'fromDate' => $accrualStartStr,
                'toDate' => $endCarbon->format('Y-m-d'),
                'autoWorkedDays' => $autoWorkedDays,
                'accruedSalary' => $accruedSalary,
                'bonusesSum' => round($bonusesSum, 2),
                'deductionsSum' => round($deductionsSum, 2),
                'advancesSum' => round($advancesSum, 2),
                'deductedHoursTotal' => round($deductedHoursTotal, 2),
                'deductedDaysTotal' => round($deductedDaysTotal, 2),
                'netPayable' => $netPayable,
            ],

            'unsettledTransactions' => $unsettledTxs->map(fn (EmployeeTransaction $t) => [
                'id' => (string) $t->id,
                'date' => $t->transaction_date ? $t->transaction_date->format('Y-m-d') : '',
                'type' => $t->type,
                'typeArabic' => $this->translateTxType($t->type),
                'hoursCount' => (float) $t->hours_count,
                'daysCount' => (float) $t->days_count,
                'amount' => (float) $t->amount,
                'source' => $t->source,
                'reason' => $t->reason ?? '',
                'user' => $t->user_name ?: 'مدير الفرع',
            ])->values()->all(),

            'settlementsHistory' => $emp->settlements->map(fn (EmployeeSettlement $s) => [
                'id' => (string) $s->id,
                'refNo' => $s->ref_no ?: ('SET-' . $s->id),
                'date' => $s->settlement_date ? $s->settlement_date->format('Y-m-d') : '',
                'periodFrom' => $s->period_from ? $s->period_from->format('Y-m-d') : '',
                'periodTo' => $s->period_to ? $s->period_to->format('Y-m-d') : '',
                'workedDays' => (float) $s->worked_days,
                'accruedSalary' => (float) $s->accrued_salary,
                'totalBonuses' => (float) $s->total_bonuses,
                'totalDeductions' => (float) $s->total_deductions,
                'totalAdvances' => (float) $s->total_advances,
                'netPaid' => (float) $s->net_paid,
                'source' => $s->source,
                'recordedInExpenses' => (bool) $s->recorded_in_expenses,
                'notes' => $s->notes ?? '',
                'user' => $s->user_name ?: 'مدير الفرع',
                'details' => is_array($s->details) ? $s->details : [],
            ])->values()->all(),
        ];
    }

    private function translateTxType(string $type): string
    {
        return match ($type) {
            'deduction_hours' => 'خصم ساعات / انصراف مبكر',
            'deduction_days' => 'خصم غياب / جزاء أيام',
            'deduction_amount' => 'خصم مالي إداري',
            'advance' => 'سلفة نقدية',
            'store_withdrawal' => 'مسحوبات بضاعة من المحل',
            'bonus' => 'مكافأة / علاوة',
            'overtime_hours' => 'ساعات عمل إضافية',
            default => 'حركة مالية',
        };
    }

    public function saveEmployee(Request $request)
    {
        $this->ensureEmployeesTablesSchema();

        $data = $request->validate([
            'id' => 'nullable',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:150',
            'nationalId' => 'nullable|string|max:20',
            'birthDate' => 'nullable|date',
            'phone' => 'required|string|max:30',
            'emergencyPhone' => 'nullable|string|max:30',
            'emergencyRelation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'jobTitle' => 'required|string|max:100',
            'shiftType' => 'nullable|string|max:60',
            'hireDate' => 'nullable|date',
            'baseSalary' => 'required|numeric|min:0',
            'workHours' => 'required|numeric|min:1|max:12',
            'weeklyOffDays' => 'nullable|numeric|min:0|max:7',
            'documents' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['id']) && is_numeric($data['id']) ? (int) $data['id'] : null;
            $employee = $editingId ? Employee::findOrFail($editingId) : null;

            $baseSalary = round((float) $data['baseSalary'], 2);
            $workHours = round(max(1, min(12, (float) $data['workHours'])), 2);
            $dailyRate = round($baseSalary / 30, 2);
            $hourlyRate = round($dailyRate / $workHours, 2);

            $birthDate = !empty($data['birthDate']) ? $data['birthDate'] : null;
            $age = $birthDate ? Carbon::parse($birthDate)->age : 0;
            $hireDate = !empty($data['hireDate']) ? $data['hireDate'] : now()->toDateString();

            $payload = [
                'name' => trim($data['name']),
                'national_id' => trim($data['nationalId'] ?? ''),
                'birth_date' => $birthDate,
                'age' => $age,
                'phone' => trim($data['phone']),
                'emergency_phone' => trim($data['emergencyPhone'] ?? ''),
                'emergency_relation' => trim($data['emergencyRelation'] ?? ''),
                'address' => trim($data['address'] ?? ''),
                'job_title' => trim($data['jobTitle']),
                'shift_type' => trim($data['shiftType'] ?? 'الوردية الصباحية'),
                'hire_date' => $hireDate,
                'base_salary' => $baseSalary,
                'work_hours' => $workHours,
                'daily_rate' => $dailyRate,
                'hourly_rate' => $hourlyRate,
                'weekly_off_days' => (int) ($data['weeklyOffDays'] ?? 1),
                'documents' => $data['documents'] ?? [],
                'notes' => trim($data['notes'] ?? ''),
                'is_synced' => false,
            ];

            if ($employee) {
                $employee->update($payload);
            } else {
                $nextId = (Employee::withTrashed()->max('id') ?? 0) + 1;
                $payload['code'] = !empty($data['code']) ? trim($data['code']) : ('EMP-' . str_pad((string) $nextId, 3, '0', STR_PAD_LEFT));
                $payload['accrual_start_date'] = $hireDate;
                $payload['status'] = 'active';
                $employee = Employee::create($payload);
            }

            return response()->json([
                'status' => 'success',
                'employeeId' => (string) $employee->id,
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }

    public function storeTransaction(Request $request, $id)
    {
        $this->ensureEmployeesTablesSchema();

        $emp = Employee::findOrFail($id);

        $data = $request->validate([
            'type' => 'required|in:deduction_hours,deduction_days,deduction_amount,advance,store_withdrawal,bonus,overtime_hours',
            'date' => 'nullable|date',
            'hoursCount' => 'nullable|numeric|min:0',
            'daysCount' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:80',
            'reason' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($emp, $data) {
            $dailyRate = (float) ($emp->daily_rate ?: round($emp->base_salary / 30, 2));
            $hourlyRate = (float) ($emp->hourly_rate ?: round($dailyRate / max(1, $emp->work_hours), 2));

            $hours = round((float) ($data['hoursCount'] ?? 0), 2);
            $days = round((float) ($data['daysCount'] ?? 0), 2);
            $amount = round((float) ($data['amount'] ?? 0), 2);

            if ($data['type'] === 'deduction_hours' || $data['type'] === 'overtime_hours') {
                $amount = round($hours * $hourlyRate, 2);
            } elseif ($data['type'] === 'deduction_days') {
                $amount = round($days * $dailyRate, 2);
            }

            EmployeeTransaction::create([
                'employee_id' => $emp->id,
                'transaction_date' => !empty($data['date']) ? $data['date'] : now()->toDateString(),
                'type' => $data['type'],
                'hours_count' => $hours,
                'days_count' => $days,
                'amount' => $amount,
                'source' => $data['source'] ?? 'الدرج',
                'reason' => trim($data['reason'] ?? '') ?: $this->translateTxType($data['type']),
                'user_name' => 'مدير الفرع',
                'is_settled' => false,
                'is_synced' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }

    public function deleteTransaction($txId)
    {
        $this->ensureEmployeesTablesSchema();

        $tx = EmployeeTransaction::findOrFail($txId);
        $tx->delete();

        return response()->json([
            'status' => 'success',
            'bootstrap' => $this->buildBootstrapPayload(),
        ]);
    }

    public function settleAccount(Request $request, $id)
    {
        $this->ensureEmployeesTablesSchema();

        $emp = Employee::with('transactions')->lockForUpdate()->findOrFail($id);

        $data = $request->validate([
            'workedDays' => 'required|numeric|min:0',
            'source' => 'required|string|max:80',
            'recordInExpenses' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($emp, $data) {
            $dailyRate = (float) ($emp->daily_rate ?: round($emp->base_salary / 30, 2));
            $workedDays = round((float) $data['workedDays'], 2);
            $accruedSalary = round($workedDays * $dailyRate, 2);

            $unsettledTxs = $emp->transactions()->where('is_settled', false)->get();

            $bonusesSum = 0.0;
            $deductionsSum = 0.0;
            $advancesSum = 0.0;
            $detailsSnapshot = [];

            foreach ($unsettledTxs as $tx) {
                $amt = (float) $tx->amount;
                if (in_array($tx->type, ['bonus', 'overtime_hours'])) {
                    $bonusesSum += $amt;
                } elseif (in_array($tx->type, ['deduction_hours', 'deduction_days', 'deduction_amount'])) {
                    $deductionsSum += $amt;
                } elseif (in_array($tx->type, ['advance', 'store_withdrawal'])) {
                    $advancesSum += $amt;
                }

                $detailsSnapshot[] = [
                    'date' => $tx->transaction_date ? $tx->transaction_date->format('Y-m-d') : '',
                    'typeArabic' => $this->translateTxType($tx->type),
                    'hoursCount' => (float) $tx->hours_count,
                    'daysCount' => (float) $tx->days_count,
                    'amount' => $amt,
                    'reason' => $tx->reason ?? '',
                ];
            }

            $netPaid = round($accruedSalary + $bonusesSum - $deductionsSum - $advancesSum, 2);
            $periodFrom = $emp->accrual_start_date ? $emp->accrual_start_date->format('Y-m-d') : ($emp->hire_date ? $emp->hire_date->format('Y-m-d') : now()->format('Y-m-d'));
            $periodTo = now()->format('Y-m-d');

            $nextNo = (EmployeeSettlement::withTrashed()->max('id') ?? 0) + 1;
            $refNo = 'SAL-' . (1000 + $nextNo);

            $expenseId = null;
            if ($data['recordInExpenses'] && $netPaid > 0 && Schema::hasTable('expenses')) {
                $nowLabel = now()->format('Y/m/d h:iA');
                $exp = Expense::create([
                    'uuid' => (string) Str::uuid(),
                    'expense_date' => now()->toDateString(),
                    'account_name' => 'مصروفات تشغيل',
                    'department_name' => 'أجور ومرتبات',
                    'item_name' => 'مرتب شهري',
                    'description' => "تصفية حساب الموظف: {$emp->name} (سند {$refNo} عن {$workedDays} يوم)",
                    'amount' => $netPaid,
                    'source' => $data['source'],
                    'shift_name' => 'مدير الفرع // 06:00AM',
                    'shift_open' => true,
                    'created_by' => 'مدير الفرع',
                    'updated_by' => 'مدير الفرع',
                    'created_time_label' => $nowLabel,
                    'updated_time_label' => $nowLabel,
                    'is_synced' => false,
                ]);
                $expenseId = $exp->id;
            }

            $settlement = EmployeeSettlement::create([
                'employee_id' => $emp->id,
                'ref_no' => $refNo,
                'settlement_date' => now()->toDateString(),
                'period_from' => $periodFrom,
                'period_to' => $periodTo,
                'worked_days' => $workedDays,
                'accrued_salary' => $accruedSalary,
                'total_bonuses' => round($bonusesSum, 2),
                'total_deductions' => round($deductionsSum, 2),
                'total_advances' => round($advancesSum, 2),
                'net_paid' => $netPaid,
                'source' => $data['source'],
                'recorded_in_expenses' => (bool) ($data['recordInExpenses'] && $netPaid > 0),
                'expense_id' => $expenseId,
                'notes' => trim($data['notes'] ?? ''),
                'user_name' => 'مدير الفرع',
                'details' => $detailsSnapshot,
                'is_synced' => false,
            ]);

            foreach ($unsettledTxs as $tx) {
                $tx->update([
                    'is_settled' => true,
                    'settlement_id' => $settlement->id,
                    'is_synced' => false,
                ]);
            }

            // بعد التصفية نبدأ فترة جديدة ونصفر أيام التوقف المؤقتة
            $emp->update([
                'accrual_start_date' => now()->addDay()->toDateString(),
                'paused_days' => 0,
                'is_synced' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'settlement' => [
                    'id' => (string) $settlement->id,
                    'refNo' => $settlement->ref_no,
                    'date' => $settlement->settlement_date->format('Y-m-d'),
                    'periodFrom' => $periodFrom,
                    'periodTo' => $periodTo,
                    'workedDays' => $workedDays,
                    'accruedSalary' => $accruedSalary,
                    'totalBonuses' => round($bonusesSum, 2),
                    'totalDeductions' => round($deductionsSum, 2),
                    'totalAdvances' => round($advancesSum, 2),
                    'netPaid' => $netPaid,
                    'source' => $settlement->source,
                    'recordedInExpenses' => (bool) $settlement->recorded_in_expenses,
                    'details' => $detailsSnapshot,
                ],
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }

    // 7. فصل الموظف أو إعادته للعمل (مع معالجة الساعات في نفس اليوم وأيام التوقف بدقة)
    public function toggleStatus(Request $request, $id)
    {
        $this->ensureEmployeesTablesSchema();

        return DB::transaction(function () use ($request, $id) {
            $emp = Employee::lockForUpdate()->findOrFail($id);
            $dailyRate = (float) ($emp->daily_rate ?: round($emp->base_salary / 30, 2));
            $hourlyRate = (float) ($emp->hourly_rate ?: round($dailyRate / max(1, $emp->work_hours), 2));

            if ($emp->status === 'active') {
                $termDate = !empty($request->input('terminationDate'))
                    ? $request->input('terminationDate')
                    : now()->toDateString();

                $reason = trim((string) $request->input('reason', 'إنهاء خدمة'));

                // إذا لم يُتم ساعات اليوم الأخير كاملة (مثلاً مشى بعد 4 ساعات من أصل 8 ساعات يوم الفصل)
                $unworkedHoursToday = round((float) $request->input('unworkedHoursToday', 0), 2);
                if ($unworkedHoursToday > 0) {
                    EmployeeTransaction::create([
                        'employee_id' => $emp->id,
                        'transaction_date' => $termDate,
                        'type' => 'deduction_hours',
                        'hours_count' => $unworkedHoursToday,
                        'days_count' => 0,
                        'amount' => round($unworkedHoursToday * $hourlyRate, 2),
                        'source' => 'الدرج',
                        'reason' => "عدم استكمال ساعات يوم الفصل ({$unworkedHoursToday} ساعة غير مكتملة)",
                        'user_name' => 'مدير الفرع',
                        'is_settled' => false,
                        'is_synced' => false,
                    ]);
                }

                $emp->update([
                    'status' => 'terminated',
                    'termination_date' => $termDate,
                    'terminated_at' => now(),
                    'termination_reason' => $reason,
                    'is_synced' => false,
                ]);
            } else {
                // حالة إعادة الموظف للعمل (سواء في نفس اليوم بعد ساعة أو بعد عدة أيام)
                $deductStoppedHours = round((float) $request->input('deductStoppedHours', 0), 2);
                $pauseDaysToAdd = 0;

                if ($emp->termination_date) {
                    $termDay = Carbon::parse($emp->termination_date)->startOfDay();
                    $today = now()->startOfDay();
                    // لو رجع بعد عدة أيام (مثلاً اتفصل الحد ورجع التلات) نستبعد الأيام اللي قعدها في البيت بدون مسح أيامه السابقة!
                    if ($today->gt($termDay)) {
                        $pauseDaysToAdd = $termDay->diffInDays($today);
                    }
                }

                // لو اتفصل الساعة 8 بالليل ورجع الساعة 9 بالليل والمدير اختار خصم ساعة التوقف
                if ($deductStoppedHours > 0) {
                    $fromTimeLabel = $emp->terminated_at ? Carbon::parse($emp->terminated_at)->format('h:i A') : '';
                    $toTimeLabel = now()->format('h:i A');
                    $timeRangeNote = $fromTimeLabel ? " (من {$fromTimeLabel} إلى {$toTimeLabel})" : '';

                    EmployeeTransaction::create([
                        'employee_id' => $emp->id,
                        'transaction_date' => now()->toDateString(),
                        'type' => 'deduction_hours',
                        'hours_count' => $deductStoppedHours,
                        'days_count' => 0,
                        'amount' => round($deductStoppedHours * $hourlyRate, 2),
                        'source' => 'الدرج',
                        'reason' => "خصم فترة توقف/فصل مؤقت لمدة {$deductStoppedHours} ساعة{$timeRangeNote}",
                        'user_name' => 'مدير الفرع',
                        'is_settled' => false,
                        'is_synced' => false,
                    ]);
                }

                // نحتفظ بـ accrual_start_date القديم كما هو حتى لا تضيع أيامه السابقة أبداً!
                $emp->update([
                    'status' => 'active',
                    'termination_date' => null,
                    'terminated_at' => null,
                    'termination_reason' => null,
                    'paused_days' => round(((float) $emp->paused_days) + $pauseDaysToAdd, 2),
                    'is_synced' => false,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildBootstrapPayload(),
            ]);
        });
    }
}