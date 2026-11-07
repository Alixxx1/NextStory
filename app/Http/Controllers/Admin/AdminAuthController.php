<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * قائمة جميع الصلاحيات المتاحة في النظام
     */
    public static function allSystemPermissions(): array
    {
        return [
            'pos.sales'               => 'فتح شاشة البيع وإضافة فواتير المبيعات (POS)',
            'pos.discount'            => 'منح خصم على الفاتورة في الكاشير',
            'pos.returns'             => 'عمل مرتجع مبيعات (كلي أو جزئي)',
            'orders.archive'          => 'الدخول إلى أرشيف الطلبات ومراجعة الفواتير',
            'orders.delete'           => 'حذف أو تعديل تكلفة فاتورة في الأرشيف',
            'returns.log'             => 'مراجعة سجل المرتجعات',
            'inventory.items'         => 'الدخول إلى أصناف المخزن وتعديل الأسعار',
            'inventory.categories'    => 'إدارة التصنيفات ووحدات القياس',
            'inventory.movement'      => 'مراجعة حركة الأصناف الشاملة',
            'inventory.stocktaking'   => 'جرد الأصناف الفوري وتعديل الرصيد',
            'inventory.manufacturing' => 'التصنيع والخلطات وسجل التصنيع',
            'inventory.adjustments'   => 'تسوية وضبط الأرصدة وتصفير المخزن',
            'inventory.orders'        => 'الأوامر المخزنية والأذون الإدارية',
            'purchases.manage'        => 'إضافة فواتير المشتريات ومرتجعات الموردين وسجلها',
            'suppliers.manage'        => 'مراجعة قائمة الموردين وكشف الحساب والدفعات',
            'customers.manage'        => 'إدارة العملاء وكشف الحساب وسندات القبض ومناطق التوصيل',
            'expenses.manage'         => 'تسجيل المصروفات ومراجعة تقارير المصروفات',
            'reports.shift'           => 'مراجعة ملخص الوردية',
            'reports.comprehensive'   => 'مراجعة التقرير الشامل وتقرير الدخل والأرباح',
            'employees.manage'        => 'إدارة شئون العاملين والرواتب وتصفية الحسابات',
            'admin.users'             => 'إدارة المستخدمين وتعديل كلمات المرور والصلاحيات',
            'admin.shifts'            => 'مراجعة سجل الورديات وتغيير/إغلاق الوردية',
            'admin.login_log'         => 'مراجعة سجل دخول المستخدمين والعمليات',
            'admin.safes'             => 'إدارة الخزائن (الدرج / الخزنة / البنك) والإيداع والسحب',
            'settings.manage'         => 'الدخول إلى الإعدادات العامة للنظام وربط الواتساب',
        ];
    }

    /**
     * بناء الجداول الـ 5 تلقائياً وزرع المستخدمين والخزائن دون الحاجة لـ migrate
     */
    public function ensureSeeded(): void
    {
        if (!Schema::hasTable('pos_users')) {
            Schema::create('pos_users', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->string('username', 80)->unique();
                $table->string('full_name', 120);
                $table->string('password');
                $table->string('plain_hint', 60)->nullable();
                $table->string('role', 40)->default('user');
                $table->string('role_label', 60)->default('مستخدم');
                $table->json('permissions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_synced')->default(false)->index();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pos_shifts')) {
            Schema::create('pos_shifts', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->integer('shift_no')->index();
                $table->string('status', 20)->default('open');
                $table->date('shift_date')->index();
                $table->dateTime('started_at');
                $table->string('started_time_label', 40);
                $table->integer('started_by_id')->nullable();
                $table->string('started_by_name', 100);
                $table->string('start_device', 100)->default('DESKTOP-0BV4S0U');
                $table->dateTime('ended_at')->nullable();
                $table->string('ended_time_label', 40)->nullable();
                $table->string('duration_label', 60)->nullable();
                $table->integer('ended_by_id')->nullable();
                $table->string('ended_by_name', 100)->nullable();
                $table->string('end_device', 100)->nullable();
                $table->decimal('opening_cash', 14, 3)->default(0);
                $table->decimal('expected_drawer_cash', 14, 3)->default(0);
                $table->decimal('counted_drawer_cash', 14, 3)->default(0);
                $table->decimal('cash_difference', 14, 3)->default(0);
                $table->boolean('report_sent')->default(false);
                $table->text('notes')->nullable();
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_activity_logs')) {
            Schema::create('user_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->date('log_date')->index();
                $table->string('time_label', 45);
                $table->integer('user_id')->nullable()->index();
                $table->string('user_name', 100)->index();
                $table->string('operation_type', 80)->index();
                $table->string('status', 30)->default('-');
                $table->string('device_name', 100)->default('DESKTOP-0BV4S0U');
                $table->string('description', 255)->nullable();
                $table->string('note', 255)->nullable();
                $table->boolean('is_synced')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('safes')) {
            Schema::create('safes', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->string('code', 20);
                $table->string('name', 100);
                $table->string('type', 50)->default('خزينة');
                $table->string('description', 150)->nullable();
                $table->string('payment_method_ar', 80)->nullable();
                $table->string('payment_method_en', 80)->nullable();
                $table->string('status', 30)->default('مفعل');
                $table->decimal('opening_balance', 14, 3)->default(0);
                $table->decimal('balance', 14, 3)->default(0);
                $table->boolean('is_basic')->default(true);
                $table->boolean('is_synced')->default(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('safe_transactions')) {
            Schema::create('safe_transactions', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('safe_id')->constrained('safes')->cascadeOnDelete();
                $table->integer('to_safe_id')->nullable();
                $table->date('tx_date')->index();
                $table->string('time_label', 45);
                $table->string('type', 40);
                $table->decimal('amount', 14, 3)->default(0);
                $table->decimal('balance_before', 14, 3)->default(0);
                $table->decimal('balance_after', 14, 3)->default(0);
                $table->string('statement', 255)->nullable();
                $table->string('user_name', 100)->default('مدير الفرع');
                $table->boolean('is_synced')->default(false);
                $table->timestamps();
            });
        }

        if (DB::table('pos_users')->count() === 0) {
            $allPermsKeys = array_keys(self::allSystemPermissions());
            $cashierDefaultPerms = ['pos.sales'];

            DB::table('pos_users')->insert([
                [
                    'uuid'        => (string) Str::uuid(),
                    'username'    => 'Cashier1',
                    'full_name'   => 'كاشير1',
                    'password'    => Hash::make('123'),
                    'role'        => 'user',
                    'role_label'  => 'مستخدم',
                    'permissions' => json_encode($cashierDefaultPerms),
                    'is_active'   => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'uuid'        => (string) Str::uuid(),
                    'username'    => 'Cashier2',
                    'full_name'   => 'كاشير2',
                    'password'    => Hash::make('123'),
                    'role'        => 'user',
                    'role_label'  => 'مستخدم',
                    'permissions' => json_encode($cashierDefaultPerms),
                    'is_active'   => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'uuid'        => (string) Str::uuid(),
                    'username'    => 'admin',
                    'full_name'   => 'مدير النظام',
                    'password'    => Hash::make('123'),
                    'role'        => 'system_admin',
                    'role_label'  => 'مدير',
                    'permissions' => json_encode($allPermsKeys),
                    'is_active'   => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'uuid'        => (string) Str::uuid(),
                    'username'    => 'BranchAdmin',
                    'full_name'   => 'مدير الفرع',
                    'password'    => Hash::make('123'),
                    'role'        => 'branch_admin',
                    'role_label'  => 'مدير',
                    'permissions' => json_encode($allPermsKeys),
                    'is_active'   => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }

        if (DB::table('safes')->count() === 0) {
            DB::table('safes')->insert([
                [
                    'uuid'              => (string) Str::uuid(),
                    'code'              => '001',
                    'name'              => 'الدرج',
                    'type'              => 'خزينة',
                    'description'       => '',
                    'payment_method_ar' => '',
                    'payment_method_en' => 'Cash',
                    'status'            => 'مفعل',
                    'opening_balance'   => 0,
                    'balance'           => 0,
                    'is_basic'          => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ],
                [
                    'uuid'              => (string) Str::uuid(),
                    'code'              => '002',
                    'name'              => 'الخزنة',
                    'type'              => 'خزينة',
                    'description'       => 'الخزنة الرئيسية',
                    'payment_method_ar' => 'الخزنة',
                    'payment_method_en' => 'الخزنة',
                    'status'            => 'مفعل',
                    'opening_balance'   => 0,
                    'balance'           => 0,
                    'is_basic'          => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ],
                [
                    'uuid'              => (string) Str::uuid(),
                    'code'              => '003',
                    'name'              => 'الحساب البنكي',
                    'type'              => 'حساب بنكي',
                    'description'       => '',
                    'payment_method_ar' => '',
                    'payment_method_en' => 'Credit Card',
                    'status'            => 'مفعل',
                    'opening_balance'   => 0,
                    'balance'           => 0,
                    'is_basic'          => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ],
            ]);
        }

        DB::table('pos_users')->whereNotNull('plain_hint')->update(['plain_hint' => null]);
    }

    public static function logActivity(string $userName, string $operationType, string $status = '-', string $description = '', string $note = '', ?int $userId = null): void
    {
        if (!Schema::hasTable('user_activity_logs')) {
            return;
        }

        $deviceName = gethostname() ?: 'DESKTOP-0BV4S0U';
        $now = Carbon::now('Africa/Cairo');

        DB::table('user_activity_logs')->insert([
            'uuid'           => (string) Str::uuid(),
            'log_date'       => $now->format('Y-m-d'),
            'time_label'     => $now->format('Y/m/d - h:iA'),
            'user_id'        => $userId,
            'user_name'      => $userName,
            'operation_type' => $operationType,
            'status'         => $status,
            'device_name'    => strtoupper($deviceName),
            'description'    => $description,
            'note'           => $note,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
    }

    private function hasAdminAccess(Request $request, string $permission): bool
    {
        $posUser = $request->session()->get('pos_user');
        if (!is_array($posUser)) {
            return false;
        }

        $role = $posUser['role'] ?? '';
        if (!empty($posUser['is_admin'])
            || !empty($posUser['is_manager'])
            || in_array($role, ['admin', 'branch_manager', 'system_admin', 'branch_admin', 'مدير النظام', 'مدير الفرع'], true)) {
            return true;
        }

        $permissions = $posUser['permissions'] ?? [];
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: [];
        }
        if (is_array($permissions)
            && (in_array('*', $permissions, true) || in_array('all', $permissions, true) || in_array($permission, $permissions, true))) {
            return true;
        }

        $temporaryGrants = $request->session()->get('pos_temp_authorized_permissions', []);
        return is_array($temporaryGrants)
            && (int) ($temporaryGrants[$permission] ?? 0) >= now()->timestamp;
    }

    private function adminAccessError(Request $request, string $permission): ?JsonResponse
    {
        if (!$request->session()->has('pos_user')) {
            return response()->json(['ok' => false, 'message' => 'يجب تسجيل الدخول أولاً'], 401);
        }
        if (!$this->hasAdminAccess($request, $permission)) {
            return response()->json(['ok' => false, 'message' => 'ليس لديك صلاحية تنفيذ هذا الإجراء'], 403);
        }
        return null;
    }

    private function requireAdminPageAccess(Request $request, string $permission): void
    {
        if (!$request->session()->has('pos_user')) {
            abort(redirect('/login'));
        }
        abort_unless($this->hasAdminAccess($request, $permission), 403);
    }

    public function loginView(Request $request)
    {
        $this->ensureSeeded();

        if ($request->session()->has('pos_user') && !$request->boolean('switch')) {
            return redirect('/pos/sales');
        }

        $users = DB::table('pos_users')->whereNull('deleted_at')->where('is_active', 1)->orderBy('id', 'asc')->get();

        return view('auth.login', compact('users'));
    }

    public function loginSubmit(Request $request)
    {
        $this->ensureSeeded();

        $username = trim((string) $request->input('username'));
        $password = trim((string) $request->input('password'));

        $user = DB::table('pos_users')
            ->whereNull('deleted_at')
            ->where('username', $username)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            self::logActivity(
                $user ? $user->full_name : ($username ?: 'غير معروف'),
                'تسجيل دخول',
                'فشلت',
                'محاولة تسجيل دخول بكلمة مرور غير صحيحة',
                'كلمة المرور خاطئة',
                $user ? $user->id : null
            );

            return response()->json([
                'ok'      => false,
                'message' => 'كلمة المرور غير صحيحة! (كلمة المرور الافتراضية: 123)',
            ], 422);
        }

        $permissions = json_decode($user->permissions ?? '[]', true) ?: [];
        if (in_array($user->role, ['system_admin', 'branch_admin'])) {
            $permissions = array_keys(self::allSystemPermissions());
        }

        $openShift = DB::table('pos_shifts')->where('status', 'open')->orderBy('id', 'desc')->first();
        if (!$openShift) {
            $lastShiftNo = DB::table('pos_shifts')->max('shift_no') ?? 135;
            $now = Carbon::now('Africa/Cairo');
            $shiftId = DB::table('pos_shifts')->insertGetId([
                'uuid'               => (string) Str::uuid(),
                'shift_no'           => $lastShiftNo + 1,
                'status'             => 'open',
                'shift_date'         => $now->format('Y-m-d'),
                'started_at'         => $now,
                'started_time_label' => $now->format('Y/m/d - h:iA'),
                'started_by_id'      => $user->id,
                'started_by_name'    => $user->full_name,
                'start_device'       => strtoupper(gethostname() ?: 'DESKTOP-0BV4S0U'),
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
            $openShift = DB::table('pos_shifts')->find($shiftId);
        }

        $sessionData = [
            'id'          => $user->id,
            'username'    => $user->username,
            'full_name'   => $user->full_name,
            'role'        => $user->role,
            'role_label'  => $user->role_label,
            'permissions' => $permissions,
            'shift_id'    => $openShift ? $openShift->id : 1,
            'shift_no'    => $openShift ? $openShift->shift_no : 136,
        ];

        $request->session()->forget('pos_temp_authorized_permissions');
        $request->session()->put('pos_user', $sessionData);

        self::logActivity($user->full_name, 'تسجيل دخول', 'نجحت', 'تسجيل دخول للبرنامج وفتح شاشة المبيعات', '', $user->id);

        return response()->json([
            'ok'       => true,
            'redirect' => '/pos/sales',
            'user'     => $sessionData,
        ]);
    }

    public function logout(Request $request)
    {
        $posUser = $request->session()->get('pos_user');
        if ($posUser) {
            self::logActivity($posUser['full_name'], 'تسجيل خروج', 'نجحت', 'خروج من الجلسة الحالية', '', $posUser['id']);
        }
        $request->session()->forget(['pos_user', 'pos_temp_authorized_permissions']);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'redirect' => '/login']);
        }
        return redirect('/login');
    }

    public function authorizeManagerAction(Request $request)
    {
        $this->ensureSeeded();

        $currentUser = $request->session()->get('pos_user');
        if (!is_array($currentUser)) {
            return response()->json(['ok' => false, 'message' => 'يجب تسجيل الدخول قبل طلب اعتماد المدير'], 401);
        }

        $validated = $request->validate([
            'password' => 'required|string|max:255',
            'action_name' => 'nullable|string|max:255',
            'permission' => 'nullable|string|max:100',
        ]);
        $password   = trim($validated['password']);
        $actionName = trim((string) $request->input('action_name', 'صلاحية إدارية'));

        $managers = DB::table('pos_users')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->whereIn('role', ['system_admin', 'branch_admin'])
            ->get();

        $authorizedManager = null;
        foreach ($managers as $mgr) {
            if (Hash::check($password, $mgr->password)) {
                $authorizedManager = $mgr;
                break;
            }
        }

        if (!$authorizedManager) {
            self::logActivity($currentUser['full_name'], 'تصريح عملية', 'فشلت', $actionName, 'فشل إدخال كلمة مرور المدير', $currentUser['id']);
            return response()->json(['ok' => false, 'message' => 'كلمة مرور المدير غير صحيحة!'], 422);
        }

        $permission = trim((string) $request->input('permission', ''));
        if ($permission !== '' && array_key_exists($permission, self::allSystemPermissions())) {
            $grantedPermissions = $request->session()->get('pos_temp_authorized_permissions', []);
            if (!is_array($grantedPermissions)) {
                $grantedPermissions = [];
            }
            $grantedPermissions[$permission] = now()->addMinutes(5)->timestamp;
            $request->session()->put('pos_temp_authorized_permissions', $grantedPermissions);
        }

        self::logActivity($currentUser['full_name'], 'تصريح عملية', 'نجحت', $actionName, "تم التصريح بواسطة: {$authorizedManager->full_name}", $currentUser['id']);

        return response()->json([
            'ok'           => true,
            'manager_name' => $authorizedManager->full_name,
        ]);
    }

    public function closeShiftAndSwitch(Request $request)
    {
        $this->ensureSeeded();

        $currentUser = $request->session()->get('pos_user');
        if (!is_array($currentUser)) {
            return response()->json(['ok' => false, 'message' => 'يجب تسجيل الدخول قبل إغلاق الوردية'], 401);
        }

        $password    = trim((string) $request->input('password'));
        $drawerCash  = (float) $request->input('drawer_cash', 0);
        $notes       = trim((string) $request->input('notes', ''));

        $managers = DB::table('pos_users')
            ->whereNull('deleted_at')
            ->whereIn('role', ['system_admin', 'branch_admin'])
            ->get();

        $authorizedManager = null;
        foreach ($managers as $mgr) {
            if (Hash::check($password, $mgr->password)) {
                $authorizedManager = $mgr;
                break;
            }
        }

        if (!$authorizedManager) {
            self::logActivity($currentUser['full_name'], 'صلاحية تغيير الوردية', 'فشلت', 'محاولة تقفيل الوردية بكلمة مرور خاطئة', '', $currentUser['id']);
            return response()->json(['ok' => false, 'message' => 'كلمة مرور المدير غير صحيحة لتقفيل الوردية!'], 422);
        }

        $now = Carbon::now('Africa/Cairo');
        $openShift = DB::table('pos_shifts')->where('status', 'open')->orderBy('id', 'desc')->first();

        if ($openShift) {
            $startedAt = Carbon::parse($openShift->started_at);
            $diffMinutes = max(1, $startedAt->diffInMinutes($now));
            $hours = floor($diffMinutes / 60);
            $mins  = $diffMinutes % 60;
            $durationLabel = ($hours > 0 ? "{$hours} ساعة و " : '') . "{$mins} دقيقة";

            $expectedDrawer = $this->calculateRealDrawerBalance();

            DB::table('pos_shifts')->where('id', $openShift->id)->update([
                'status'               => 'closed',
                'ended_at'             => $now,
                'ended_time_label'     => $now->format('Y/m/d - h:iA'),
                'duration_label'       => $durationLabel,
                'ended_by_id'          => $authorizedManager->id,
                'ended_by_name'        => $currentUser['full_name'],
                'end_device'           => strtoupper(gethostname() ?: 'DESKTOP-0BV4S0U'),
                'expected_drawer_cash' => $expectedDrawer,
                'counted_drawer_cash'  => $drawerCash,
                'cash_difference'      => round($drawerCash - $expectedDrawer, 3),
                'report_sent'          => 1,
                'notes'                => $notes,
                'updated_at'           => $now,
            ]);
        }

        self::logActivity($currentUser['full_name'], 'إغلاق الوردية', 'نجحت', 'تقفيل الوردية وجرد النقدية بالدرج: ' . number_format($drawerCash, 2) . ' ج.م', "تصريح: {$authorizedManager->full_name}", $currentUser['id']);

        $request->session()->forget(['pos_user', 'pos_temp_authorized_permissions']);

        return response()->json([
            'ok'       => true,
            'redirect' => '/login',
            'message'  => 'تم إغلاق الوردية بنجاح وتسجيل المبلغ الموجود بالدرج.',
        ]);
    }

    public function usersView(Request $request)
    {
        $this->ensureSeeded();
        $this->requireAdminPageAccess($request, 'admin.users');
        return view('admin.users');
    }

    public function usersBootstrapApi(Request $request)
    {
        $this->ensureSeeded();
        if ($error = $this->adminAccessError($request, 'admin.users')) {
            return $error;
        }

        $users = DB::table('pos_users')->orderBy('id', 'asc')->get()->map(function ($u) {
            return [
                'id'          => $u->id,
                'username'    => $u->username,
                'fullName'    => $u->full_name,
                'role'        => $u->role,
                'roleLabel'   => $u->role_label,
                'permissions' => json_decode($u->permissions ?? '[]', true) ?: [],
                'isDeleted'   => !is_null($u->deleted_at),
            ];
        });

        return response()->json([
            'ok'             => true,
            'users'          => $users,
            'allPermissions' => self::allSystemPermissions(),
        ]);
    }

    public function saveUserApi(Request $request)
    {
        $this->ensureSeeded();
        if ($error = $this->adminAccessError($request, 'admin.users')) {
            return $error;
        }

        $id          = $request->input('id');
        $username    = trim((string) $request->input('username'));
        $fullName    = trim((string) $request->input('fullName'));
        $password    = trim((string) $request->input('password'));
        $role        = $request->input('role', 'user');
        $permissions = $request->input('permissions', []);

        if ($username === '' || $fullName === '') {
            return response()->json(['ok' => false, 'message' => 'يرجى إدخال اسم المستخدم والاسم بالكامل.'], 422);
        }

        $dupQuery = DB::table('pos_users')->where('username', $username);
        if ($id) {
            $dupQuery->where('id', '!=', $id);
        }
        if ($dupQuery->exists()) {
            return response()->json(['ok' => false, 'message' => 'اسم المستخدم مسجل مسبقاً!'], 422);
        }

        $roleLabel = in_array($role, ['system_admin', 'branch_admin']) ? 'مدير' : 'مستخدم';
        if (in_array($role, ['system_admin', 'branch_admin'])) {
            $permissions = array_keys(self::allSystemPermissions());
        }

        $data = [
            'username'    => $username,
            'full_name'   => $fullName,
            'role'        => $role,
            'role_label'  => $roleLabel,
            'permissions' => json_encode(array_values($permissions)),
            'updated_at'  => now(),
        ];

        if ($password !== '') {
            $data['password']   = Hash::make($password);
            $data['plain_hint'] = null;
        }

        if ($id) {
            $targetUser = DB::table('pos_users')->where('id', $id)->first();
            if (!$targetUser) {
                return response()->json(['ok' => false, 'message' => 'المستخدم غير موجود'], 404);
            }
            DB::table('pos_users')->where('id', $id)->update($data);
        } else {
            if ($password === '') {
                $data['password']   = Hash::make('123');
                $data['plain_hint'] = null;
            }
            $data['uuid']       = (string) Str::uuid();
            $data['created_at'] = now();
            $id = DB::table('pos_users')->insertGetId($data);
        }

        $posUser = $request->session()->get('pos_user');
        if ($posUser && (int) $posUser['id'] === (int) $id) {
            $posUser['full_name']   = $fullName;
            $posUser['role']        = $role;
            $posUser['role_label']  = $roleLabel;
            $posUser['permissions'] = $permissions;
            $request->session()->put('pos_user', $posUser);
        }

        self::logActivity(
            $posUser['full_name'] ?? 'مدير النظام',
            'إدارة المستخدمين',
            'نجحت',
            ($id && isset($targetUser) ? 'تعديل بيانات وصلاحيات المستخدم: ' : 'إضافة مستخدم جديد: ') . $fullName,
            'إدارة المستخدمين',
            isset($posUser['id']) ? (int) $posUser['id'] : null
        );

        return $this->usersBootstrapApi($request);
    }

    public function changeUserPasswordApi(Request $request, $id)
    {
        $this->ensureSeeded();
        if ($error = $this->adminAccessError($request, 'admin.users')) {
            return $error;
        }

        $password = trim((string) $request->input('password'));
        if ($password === '') {
            return response()->json(['ok' => false, 'message' => 'أدخل كلمة المرور الجديدة.'], 422);
        }

        $user = DB::table('pos_users')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'المستخدم غير موجود'], 404);
        }

        DB::table('pos_users')->where('id', $id)->update([
            'password'   => Hash::make($password),
            'plain_hint' => null,
            'updated_at' => now(),
        ]);

        $posUser = $request->session()->get('pos_user', []);
        self::logActivity(
            $posUser['full_name'] ?? 'مدير النظام',
            'إدارة المستخدمين',
            'نجحت',
            'تغيير كلمة مرور المستخدم: ' . $user->full_name,
            'تم حفظ كلمة المرور مشفرة',
            isset($posUser['id']) ? (int) $posUser['id'] : null
        );

        return response()->json(['ok' => true, 'message' => 'تم تغيير كلمة المرور بنجاح.']);
    }

    public function toggleDeleteUserApi(Request $request, $id)
    {
        $this->ensureSeeded();
        if ($error = $this->adminAccessError($request, 'admin.users')) {
            return $error;
        }

        $user = DB::table('pos_users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'المستخدم غير موجود'], 404);
        }

        if ($user->username === 'admin' && is_null($user->deleted_at)) {
            return response()->json(['ok' => false, 'message' => 'لا يمكن حذف حساب مدير النظام الرئيسي (admin)!'], 422);
        }

        DB::table('pos_users')->where('id', $id)->update([
            'deleted_at' => is_null($user->deleted_at) ? now() : null,
            'updated_at' => now(),
        ]);

        $posUser = $request->session()->get('pos_user', []);
        self::logActivity(
            $posUser['full_name'] ?? 'مدير النظام',
            'إدارة المستخدمين',
            'نجحت',
            (is_null($user->deleted_at) ? 'حذف المستخدم: ' : 'استعادة المستخدم: ') . $user->full_name,
            'إدارة المستخدمين',
            isset($posUser['id']) ? (int) $posUser['id'] : null
        );

        return $this->usersBootstrapApi($request);
    }

    public function shiftsLogView(Request $request)
    {
        $this->ensureSeeded();
        $this->requireAdminPageAccess($request, 'admin.shifts');
        return view('admin.shifts_log');
    }

    public function shiftsLogApi(Request $request)
    {
        $this->ensureSeeded();
        if ($error = $this->adminAccessError($request, 'admin.shifts')) {
            return $error;
        }

        $validated = $request->validate([
            'date_mode' => ['sometimes', 'in:all,exact,before,after,range'],
            'date1' => ['required_if:date_mode,exact,before,after,range', 'date_format:Y-m-d'],
            'date2' => ['required_if:date_mode,range', 'date_format:Y-m-d'],
            'user' => ['nullable', 'string', 'max:120'],
        ]);
        $dateMode = $validated['date_mode'] ?? 'all';
        $date1    = $validated['date1'] ?? null;
        $date2    = $validated['date2'] ?? null;
        $user     = $validated['user'] ?? 'all';

        $q = DB::table('pos_shifts')->orderByDesc('started_at')->orderByDesc('id');

        if ($dateMode === 'exact' && $date1) {
            $q->where('shift_date', $date1);
        } elseif ($dateMode === 'before' && $date1) {
            $q->where('shift_date', '<=', $date1);
        } elseif ($dateMode === 'after' && $date1) {
            $q->where('shift_date', '>=', $date1);
        } elseif ($dateMode === 'range' && $date1 && $date2) {
            $q->whereBetween('shift_date', [min($date1, $date2), max($date1, $date2)]);
        }

        if ($user !== 'all' && $user !== '') {
            $q->where(function ($sub) use ($user) {
                $sub->where('started_by_name', $user)->orWhere('ended_by_name', $user);
            });
        }

        $shifts = $q->limit(500)->get();
        $usersList = DB::table('pos_users')->pluck('full_name')->unique()->values();
        $latestDate = DB::table('pos_shifts')->max('shift_date');

        return response()->json([
            'ok'          => true,
            'shifts'      => $shifts,
            'users'       => $usersList,
            'latest_date' => $latestDate ? substr((string) $latestDate, 0, 10) : null,
        ]);
    }

    public function loginLogView(Request $request)
    {
        $this->ensureSeeded();
        $this->requireAdminPageAccess($request, 'admin.login_log');
        return view('admin.login_log');
    }

    public function loginLogApi(Request $request)
    {
        $this->ensureSeeded();
        if ($error = $this->adminAccessError($request, 'admin.login_log')) {
            return $error;
        }

        $dateMode = $request->query('date_mode', 'exact');
        $date1    = $request->query('date1', now()->format('Y-m-d'));
        $date2    = $request->query('date2', now()->format('Y-m-d'));
        $user     = $request->query('user', 'all');
        $opType   = $request->query('op_type', 'all');
        $device   = trim((string) $request->query('device', ''));
        $desc     = trim((string) $request->query('description', ''));

        $q = DB::table('user_activity_logs')->orderBy('id', 'asc');

        if ($dateMode === 'exact' && $date1) {
            $q->where('log_date', $date1);
        } elseif ($dateMode === 'before' && $date1) {
            $q->where('log_date', '<=', $date1);
        } elseif ($dateMode === 'after' && $date1) {
            $q->where('log_date', '>=', $date1);
        } elseif ($dateMode === 'range' && $date1 && $date2) {
            $q->whereBetween('log_date', [min($date1, $date2), max($date1, $date2)]);
        }

        if ($user !== 'all' && $user !== '') {
            $q->where('user_name', $user);
        }
        if ($opType !== 'all' && $opType !== '') {
            $q->where('operation_type', $opType);
        }
        if ($device !== '') {
            $q->where('device_name', 'like', "%{$device}%");
        }
        if ($desc !== '') {
            $q->where(function ($sub) use ($desc) {
                $sub->where('description', 'like', "%{$desc}%")
                    ->orWhere('note', 'like', "%{$desc}%");
            });
        }

        $logs = $q->limit(1000)->get();
        $usersList = DB::table('pos_users')->pluck('full_name')->unique()->values();

        return response()->json([
            'ok'    => true,
            'logs'  => $logs,
            'users' => $usersList,
        ]);
    }

    public function safesView()
    {
        $this->ensureSeeded();
        return view('admin.safes');
    }

    private function calculateRealDrawerBalance(): float
    {
        $salesCash = Schema::hasTable('sales_invoices')
            ? (float) DB::table('sales_invoices')->whereNull('deleted_at')->where('status', 'closed')->where('payment_method', 'cash')->sum('paid_amount')
            : 0;

        $returnsCash = Schema::hasTable('sales_returns')
            ? (float) DB::table('sales_returns')->whereNull('deleted_at')->sum('cash_refund')
            : 0;

        $custIn = Schema::hasTable('customer_transactions')
            ? (float) DB::table('customer_transactions')->whereNull('deleted_at')->where('type', 'payment')->sum('amount')
            : 0;

        $custOut = Schema::hasTable('customer_transactions')
            ? (float) DB::table('customer_transactions')->whereNull('deleted_at')->where('type', 'refund')->sum('amount')
            : 0;

        $purchasesCash = Schema::hasTable('purchase_invoices')
            ? (float) DB::table('purchase_invoices')->whereNull('deleted_at')->sum('paid_amount')
            : 0;

        $expensesCash = Schema::hasTable('expenses')
            ? (float) DB::table('expenses')->whereNull('deleted_at')->sum('amount')
            : 0;

        $drawerSafe = Schema::hasTable('safes') ? DB::table('safes')->where('code', '001')->first() : null;
        $manualAdjust = $drawerSafe ? (float) $drawerSafe->balance : 0;

        return round($salesCash - $returnsCash + $custIn - $custOut - $purchasesCash - $expensesCash + $manualAdjust, 3);
    }

    public function safesBootstrapApi()
    {
        $this->ensureSeeded();

        $realDrawer = $this->calculateRealDrawerBalance();

        $safes = DB::table('safes')->orderBy('code', 'asc')->get()->map(function ($s) use ($realDrawer) {
            $computedVal = $s->code === '001' ? $realDrawer : (float) $s->balance;
            return [
                'id'              => $s->id,
                'code'            => $s->code,
                'name'            => $s->name,
                'type'            => $s->type,
                'description'     => $s->description ?? '',
                'paymentMethodAr' => $s->payment_method_ar ?? '',
                'paymentMethodEn' => $s->payment_method_en ?? '',
                'status'          => $s->status,
                'value'           => round($computedVal, 3),
                'isBasic'         => (bool) $s->is_basic,
                'isDeleted'       => !is_null($s->deleted_at),
            ];
        });

        $transactions = DB::table('safe_transactions')
            ->join('safes', 'safes.id', '=', 'safe_transactions.safe_id')
            ->select('safe_transactions.*', 'safes.name as safe_name')
            ->orderBy('safe_transactions.id', 'desc')
            ->limit(200)
            ->get();

        return response()->json([
            'ok'           => true,
            'safes'        => $safes,
            'transactions' => $transactions,
        ]);
    }

    public function saveSafeApi(Request $request)
    {
        $id   = $request->input('id');
        $name = trim((string) $request->input('name'));
        $type = $request->input('type', 'خزينة');

        if ($name === '') {
            return response()->json(['ok' => false, 'message' => 'أدخل اسم الخزنة'], 422);
        }

        $payload = [
            'name'              => $name,
            'type'              => $type,
            'description'       => trim((string) $request->input('description', '')),
            'payment_method_ar' => trim((string) $request->input('paymentMethodAr', '')),
            'payment_method_en' => trim((string) $request->input('paymentMethodEn', 'Cash')),
            'status'            => $request->input('status', 'مفعل'),
            'updated_at'        => now(),
        ];

        if ($id) {
            DB::table('safes')->where('id', $id)->update($payload);
        } else {
            $count = DB::table('safes')->count() + 1;
            $payload['uuid']       = (string) Str::uuid();
            $payload['code']       = str_pad((string) $count, 3, '0', STR_PAD_LEFT);
            $payload['is_basic']   = 0;
            $payload['created_at'] = now();
            DB::table('safes')->insert($payload);
        }

        return $this->safesBootstrapApi();
    }

    public function safeTransactionApi(Request $request)
    {
        $safeId    = (int) $request->input('safe_id');
        $toSafeId  = $request->input('to_safe_id') ? (int) $request->input('to_safe_id') : null;
        $type      = $request->input('type');
        $amount    = (float) $request->input('amount', 0);
        $statement = trim((string) $request->input('statement', ''));

        if ($amount <= 0) {
            return response()->json(['ok' => false, 'message' => 'أدخل مبلغاً أكبر من صفر'], 422);
        }

        $safe = DB::table('safes')->where('id', $safeId)->first();
        if (!$safe) {
            return response()->json(['ok' => false, 'message' => 'الخزنة غير موجودة'], 404);
        }

        $now = Carbon::now('Africa/Cairo');
        $posUser = $request->session()->get('pos_user', ['full_name' => 'مدير الفرع']);

        DB::transaction(function () use ($safe, $toSafeId, $type, $amount, $statement, $now, $posUser) {
            $before = (float) $safe->balance;
            $after  = $type === 'deposit' ? ($before + $amount) : ($before - $amount);

            DB::table('safes')->where('id', $safe->id)->update([
                'balance'    => $after,
                'updated_at' => $now,
            ]);

            DB::table('safe_transactions')->insert([
                'uuid'           => (string) Str::uuid(),
                'safe_id'        => $safe->id,
                'to_safe_id'     => $toSafeId,
                'tx_date'        => $now->format('Y-m-d'),
                'time_label'     => $now->format('Y/m/d - h:iA'),
                'type'           => $type,
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after,
                'statement'      => $statement ?: ($type === 'deposit' ? 'إيداع نقدي' : ($type === 'withdraw' ? 'سحب نقدي' : 'تحويل بين الخزائن')),
                'user_name'      => $posUser['full_name'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            if ($type === 'transfer' && $toSafeId) {
                $targetSafe = DB::table('safes')->where('id', $toSafeId)->first();
                if ($targetSafe) {
                    $tBefore = (float) $targetSafe->balance;
                    $tAfter  = $tBefore + $amount;
                    DB::table('safes')->where('id', $targetSafe->id)->update([
                        'balance'    => $tAfter,
                        'updated_at' => $now,
                    ]);
                }
            }
        });

        self::logActivity($posUser['full_name'], 'صلاحية متاحة', '-', "حركة خزينة ({$safe->name}): {$amount} ج.م", $statement);

        return $this->safesBootstrapApi();
    }





// =========================================================================
    // 1. تحميل نسخة احتياطية مشفرة بالكامل (.nsbak) لا يمكن فتحها خارج النظام
    // =========================================================================
    public function downloadEncryptedBackup(Request $request)
    {
        if ($error = $this->adminAccessError($request, 'settings.manage')) {
            return $error;
        }

        try {
            $driver = DB::connection()->getDriverName();
            $payload = [
                'system'     => 'NextStore_POS_Protected_Backup',
                'version'    => '2025.1',
                'driver'     => $driver,
                'created_at' => now()->toDateTimeString(),
            ];

            if ($driver === 'sqlite') {
                $dbPath = $this->sqliteDatabasePath();
                if (!is_file($dbPath)) {
                    return response()->json(['ok' => false, 'message' => 'ملف قاعدة البيانات غير موجود.'], 404);
                }

                $pdo = DB::connection()->getPdo();
                $checkpoint = $pdo->query('PRAGMA wal_checkpoint(FULL)')->fetch(\PDO::FETCH_NUM);
                if (!$checkpoint || (int) $checkpoint[0] !== 0 || (int) $checkpoint[1] !== (int) $checkpoint[2]) {
                    throw new \RuntimeException('Unable to checkpoint the SQLite database before backup.');
                }
                $pdo->exec('BEGIN IMMEDIATE');
                try {
                    $sqliteBinary = file_get_contents($dbPath);
                    if ($sqliteBinary === false) {
                        throw new \RuntimeException('Unable to read the SQLite database file.');
                    }
                } finally {
                    $pdo->exec('ROLLBACK');
                }
                $payload['raw_sqlite_base64'] = base64_encode($sqliteBinary);
            } elseif ($driver === 'mysql') {
                $tablesData = [];
                foreach (DB::select('SHOW TABLES') as $tableObj) {
                    $tableName = array_values((array) $tableObj)[0];
                    if ($tableName === 'migrations') {
                        continue;
                    }
                    $tablesData[$tableName] = DB::table($tableName)->get()->map(fn ($row) => (array) $row)->all();
                }
                $payload['tables_data'] = $tablesData;
            } else {
                return response()->json(['ok' => false, 'message' => 'نوع قاعدة البيانات غير مدعوم للنسخ الاحتياطي.'], 422);
            }

            $jsonString = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $compressed = gzencode($jsonString, 9);
            if ($compressed === false) {
                throw new \RuntimeException('Unable to compress the database backup.');
            }
            $encryptedContent = \Illuminate\Support\Facades\Crypt::encryptString(base64_encode($compressed));
            $fileName = 'NextStore_Backup_' . now()->format('Y_m_d_His') . '.nsbak';

            return response($encryptedContent, 200, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control'       => 'no-store, no-cache',
            ]);
        } catch (\Throwable $e) {
            Log::error('Database backup creation failed.', ['exception' => $e]);
            return response()->json(['ok' => false, 'message' => 'تعذر إنشاء النسخة الاحتياطية. راجع سجل النظام.'], 500);
        }
    }

    // =========================================================================
    // 2. استيراد واستعادة النسخة الاحتياطية المشفرة (.nsbak)
    // =========================================================================
    public function restoreEncryptedBackup(Request $request)
    {
        if ($error = $this->adminAccessError($request, 'settings.manage')) {
            return $error;
        }

        $request->validate(['backup_file' => 'required|file|max:512000']);

        try {
            $encryptedContent = file_get_contents($request->file('backup_file')->getRealPath());
            if ($encryptedContent === false) {
                return response()->json(['ok' => false, 'message' => 'تعذر قراءة ملف النسخة الاحتياطية.'], 422);
            }
            $compressedBase64 = \Illuminate\Support\Facades\Crypt::decryptString($encryptedContent);
            $compressed = base64_decode($compressedBase64, true);
            $jsonString = $compressed === false ? false : gzdecode($compressed);
            if ($jsonString === false) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'بيانات ملف النسخة الاحتياطية تالفة.',
                ], 422);
            }
            $payload = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($payload) || ($payload['system'] ?? '') !== 'NextStore_POS_Protected_Backup') {
                return response()->json(['ok' => false, 'message' => 'ملف النسخة الاحتياطية غير صالح أو لا ينتمي لنظام NextStore.'], 422);
            }

            $currentDriver = DB::connection()->getDriverName();

            if ($currentDriver === 'sqlite' && isset($payload['raw_sqlite_base64'])) {
                if (($payload['driver'] ?? null) !== 'sqlite') {
                    return response()->json(['ok' => false, 'message' => 'نوع قاعدة البيانات في الملف غير متوافق.'], 422);
                }
                $sqliteBinary = base64_decode($payload['raw_sqlite_base64'], true);
                if ($sqliteBinary === false || substr($sqliteBinary, 0, 16) !== "SQLite format 3\0") {
                    return response()->json([
                        'ok'      => false,
                        'message' => 'البيانات الداخلية للملف ليست قاعدة SQLite صالحة.',
                    ], 422);
                }

                $this->replaceSqliteDatabase($sqliteBinary);

                return response()->json([
                    'ok'      => true,
                    'message' => 'تم استيراد واستعادة النسخة الاحتياطية بالكامل بنجاح! (تاريخ النسخة: ' . ($payload['created_at'] ?? '') . ')',
                ]);
            }

            if ($currentDriver === 'mysql'
                && ($payload['driver'] ?? null) === 'mysql'
                && isset($payload['tables_data'])
                && is_array($payload['tables_data'])) {
                $this->restoreTableBackup($payload['tables_data']);
                return response()->json([
                    'ok'      => true,
                    'message' => 'تم استبدال بيانات الجداول الحالية ببيانات النسخة الاحتياطية بنجاح.',
                ]);
            }

            return response()->json([
                'ok'      => false,
                'message' => 'نوع قاعدة البيانات في الملف لا يتطابق مع الإعداد الحالي.',
            ], 422);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'فشل فك تشفير الملف! هذا الملف غير صالح أو تم التلاعب به.',
            ], 422);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['ok' => false, 'message' => 'بيانات النسخة الاحتياطية غير صالحة أو لا تطابق بنية قاعدة البيانات.'], 422);
        } catch (\JsonException $e) {
            return response()->json(['ok' => false, 'message' => 'محتوى النسخة الاحتياطية غير صالح.'], 422);
        } catch (\Throwable $e) {
            Log::error('Database backup restore failed.', ['exception' => $e]);
            return response()->json([
                'ok'      => false,
                'message' => 'تعذرت استعادة النسخة الاحتياطية. لم يتم اعتماد بيانات ناقصة؛ راجع سجل النظام.',
            ], 500);
        }
    }

    private function sqliteDatabasePath(): string
    {
        $databaseName = DB::connection()->getDatabaseName();
        if ($databaseName === ':memory:' || str_starts_with($databaseName, 'file:')) {
            throw new \RuntimeException('SQLite backup restore requires a file-backed database.');
        }

        if (!preg_match('/^(?:[A-Za-z]:[\\\\\/]|\\\\\\\\|\/)/', $databaseName)) {
            $databaseName = base_path($databaseName);
        }

        return $databaseName;
    }

    private function isValidSqliteDatabase(string $path): bool
    {
        try {
            $pdo = new \PDO('sqlite:' . $path, null, null, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            $integrity = $pdo->query('PRAGMA integrity_check')->fetchColumn();
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table'")->fetchAll(\PDO::FETCH_COLUMN);

            return $integrity === 'ok'
                && in_array('sales_invoices', $tables, true)
                && in_array('items', $tables, true);
        } catch (\PDOException $e) {
            return false;
        }
    }

    private function replaceSqliteDatabase(string $sqliteBinary): void
    {
        $databasePath = $this->sqliteDatabasePath();
        $directory = dirname($databasePath);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new \RuntimeException('SQLite database directory is not writable.');
        }

        $temporaryPath = tempnam($directory, '.nextstore-restore-');
        if ($temporaryPath === false) {
            throw new \RuntimeException('Unable to stage the restored SQLite database.');
        }

        $rollbackPath = $databasePath . '.restore-' . Str::uuid() . '.bak';
        $originalExists = is_file($databasePath);
        $originalPermissions = $originalExists ? fileperms($databasePath) : false;
        $databaseReplaced = false;

        try {
            $written = file_put_contents($temporaryPath, $sqliteBinary, LOCK_EX);
            if ($written !== strlen($sqliteBinary) || !$this->isValidSqliteDatabase($temporaryPath)) {
                throw new \UnexpectedValueException('The restored SQLite file failed integrity validation.');
            }
            if ($originalPermissions !== false) {
                if (!chmod($temporaryPath, $originalPermissions & 0777)) {
                    throw new \RuntimeException('Unable to preserve SQLite database permissions.');
                }
            }

            if ($originalExists) {
                DB::connection()->getPdo()->query('PRAGMA wal_checkpoint(FULL)');
            }
            DB::purge();

            if ($originalExists && !rename($databasePath, $rollbackPath)) {
                throw new \RuntimeException('Unable to preserve the current SQLite database before restore.');
            }
            foreach (['-wal', '-shm', '-journal'] as $suffix) {
                $sidecarPath = $databasePath . $suffix;
                if (is_file($sidecarPath) && !unlink($sidecarPath)) {
                    throw new \RuntimeException('Unable to clear an old SQLite journal file.');
                }
            }
            if (!rename($temporaryPath, $databasePath)) {
                throw new \RuntimeException('Unable to replace the current SQLite database.');
            }
            $databaseReplaced = true;

            DB::reconnect();
            if (!$this->isValidSqliteDatabase($databasePath)) {
                throw new \RuntimeException('The restored SQLite database failed post-restore validation.');
            }
            if ($originalExists && is_file($rollbackPath) && !unlink($rollbackPath)) {
                Log::warning('The previous SQLite database backup could not be removed after restore.');
            }
        } catch (\Throwable $e) {
            DB::purge();
            foreach (['-wal', '-shm', '-journal'] as $suffix) {
                $sidecarPath = $databasePath . $suffix;
                if (is_file($sidecarPath) && !unlink($sidecarPath)) {
                    Log::warning('A SQLite sidecar could not be removed during restore rollback.');
                }
            }
            if ($originalExists && is_file($rollbackPath)) {
                if (!rename($rollbackPath, $databasePath)) {
                    Log::critical('Failed to restore the previous SQLite database after a failed backup restore.');
                }
            } elseif ($databaseReplaced && is_file($databasePath) && !unlink($databasePath)) {
                Log::critical('Failed to remove a partially restored SQLite database.');
            }
            DB::reconnect();
            throw $e;
        } finally {
            if (is_file($temporaryPath) && !unlink($temporaryPath)) {
                Log::warning('A temporary SQLite restore file could not be removed.');
            }
        }
    }

    private function restoreTableBackup(array $tablesData): void
    {
        if ($tablesData === []) {
            throw new \UnexpectedValueException('The backup contains no table data.');
        }

        $validatedTables = [];
        foreach ($tablesData as $tableName => $rows) {
            if (!is_string($tableName)
                || !preg_match('/^[A-Za-z0-9_]+$/', $tableName)
                || $tableName === 'migrations'
                || !Schema::hasTable($tableName)
                || !is_array($rows)) {
                throw new \UnexpectedValueException('The backup contains an invalid table definition.');
            }

            $columns = Schema::getColumnListing($tableName);
            foreach ($rows as $row) {
                if (!is_array($row) || array_diff(array_keys($row), $columns) !== []) {
                    throw new \UnexpectedValueException('The backup contains invalid row data.');
                }
            }
            $validatedTables[$tableName] = $rows;
        }

        $currentTables = array_map(
            fn ($table) => array_values((array) $table)[0],
            DB::select('SHOW TABLES')
        );
        $constraintsDisabled = false;
        try {
            Schema::disableForeignKeyConstraints();
            $constraintsDisabled = true;

            DB::transaction(function () use ($validatedTables, $currentTables) {
                foreach ($currentTables as $tableName) {
                    if ($tableName !== 'migrations') {
                        DB::table($tableName)->delete();
                    }
                }

                foreach ($validatedTables as $tableName => $rows) {
                    foreach (array_chunk($rows, 200) as $chunk) {
                        DB::table($tableName)->insert($chunk);
                    }
                }
            });
        } finally {
            if ($constraintsDisabled) {
                Schema::enableForeignKeyConstraints();
            }
        }
    }

    // =========================================================================
    // 3. تصفير النظام بالكامل (Zero Reset) — مسح الفواتير والحركات وتصفير الأرصدة مع بقاء الأصناف والعملاء والموردين
    // =========================================================================
    public function factoryZeroReset(Request $request)
    {
        if ($error = $this->adminAccessError($request, 'settings.manage')) {
            return $error;
        }

        $this->ensureSeeded();
        $posUser = $request->session()->get('pos_user', []);
        $now = Carbon::now('Africa/Cairo');
        $constraintsDisabled = false;

        try {
            Schema::disableForeignKeyConstraints();
            $constraintsDisabled = true;

            $newShiftId = DB::transaction(function () use ($posUser, $now) {
                $transactionalTables = [
                    'orders',
                    'order_items',
                    'pos_orders',
                    'pos_order_items',
                    'sales_returns',
                    'sales_return_items',
                    'sales_invoice_items',
                    'sales_invoices',
                    'purchases',
                    'purchase_items',
                    'purchase_returns',
                    'purchase_return_items',
                    'purchase_invoice_items',
                    'purchase_invoices',
                    'item_movements',
                    'stock_adjustments',
                    'stock_adjustment_items',
                    'warehouse_orders',
                    'inventory_orders',
                    'inventory_order_items',
                    'admin_permissions',
                    'inventory_permissions',
                    'inventory_permission_items',
                    'manufacturing_batches',
                    'manufacturing_batch_items',
                    'customer_transactions',
                    'customer_notes',
                    'supplier_transactions',
                    'expenses',
                    'employee_transactions',
                    'employee_settlements',
                    'safe_transactions',
                    'pos_safe_transactions',
                    'pos_shifts',
                ];

                foreach ($transactionalTables as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->delete();
                    }
                }

                if (Schema::hasTable('items')) {
                    $itemUpdate = ['qty' => 0];
                    if (Schema::hasColumn('items', 'last_move_date')) {
                        $itemUpdate['last_move_date'] = null;
                    }
                    if (Schema::hasColumn('items', 'is_synced')) {
                        $itemUpdate['is_synced'] = false;
                    }
                    DB::table('items')->update($itemUpdate);
                }

                if (Schema::hasTable('customers')) {
                    $custUpdate = [];
                    foreach (['opening_balance', 'balance', 'current_balance', 'debit', 'credit'] as $col) {
                        if (Schema::hasColumn('customers', $col)) {
                            $custUpdate[$col] = 0;
                        }
                    }
                    if (!empty($custUpdate)) {
                        DB::table('customers')->update($custUpdate);
                    }
                }

                if (Schema::hasTable('suppliers')) {
                    $suppUpdate = [];
                    foreach (['opening_balance', 'balance', 'current_balance', 'total_purchases', 'invoices_count'] as $col) {
                        if (Schema::hasColumn('suppliers', $col)) {
                            $suppUpdate[$col] = 0;
                        }
                    }
                    if (!empty($suppUpdate)) {
                        DB::table('suppliers')->update($suppUpdate);
                    }
                }

                if (Schema::hasTable('safes')) {
                    $safeUpdate = [];
                    foreach (['opening_balance', 'balance'] as $column) {
                        if (Schema::hasColumn('safes', $column)) {
                            $safeUpdate[$column] = 0;
                        }
                    }
                    if (Schema::hasColumn('safes', 'is_synced')) {
                        $safeUpdate['is_synced'] = false;
                    }
                    if ($safeUpdate !== []) {
                        DB::table('safes')->update($safeUpdate);
                    }
                }

                $newShiftId = null;
                if (Schema::hasTable('pos_shifts')) {
                    $startedByName = $posUser['full_name'] ?? $posUser['name'] ?? 'مدير النظام';
                    $newShiftId = DB::table('pos_shifts')->insertGetId([
                        'uuid'               => (string) Str::uuid(),
                        'shift_no'           => 1,
                        'status'             => 'open',
                        'shift_date'         => $now->toDateString(),
                        'started_at'         => $now,
                        'started_time_label' => $now->format('Y/m/d - h:iA'),
                        'started_by_id'      => $posUser['id'] ?? null,
                        'started_by_name'    => $startedByName,
                        'start_device'       => strtoupper(gethostname() ?: 'DESKTOP-0BV4S0U'),
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ]);
                }

                self::logActivity(
                    $posUser['full_name'] ?? $posUser['name'] ?? 'مدير النظام',
                    'تصفير النظام',
                    'نجحت',
                    'تم مسح حركات النظام وتصفير المخزون والأرصدة مع الاحتفاظ بالبيانات الأساسية',
                    '',
                    isset($posUser['id']) ? (int) $posUser['id'] : null
                );

                return $newShiftId;
            });

            if ($newShiftId !== null) {
                $posUser['shift_id'] = $newShiftId;
                $posUser['shift_no'] = 1;
                $request->session()->put('pos_user', $posUser);
            }
            $request->session()->put('pos_shift_number', 1);

            return response()->json([
                'ok'      => true,
                'message' => 'تم حذف الفواتير والحركات وتصفير أرصدة المخزون والعملاء والموردين والخزائن، مع الاحتفاظ بالبيانات الأساسية وسجل التدقيق.',
            ]);
        } catch (\Throwable $e) {
            Log::error('System zero reset failed.', ['exception' => $e]);
            return response()->json([
                'ok'      => false,
                'message' => 'تعذر إكمال تصفير النظام. راجع سجل النظام.',
            ], 500);
        } finally {
            if ($constraintsDisabled) {
                Schema::enableForeignKeyConstraints();
            }
        }
    }









}