<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                    'plain_hint'  => '123',
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
                    'plain_hint'  => '123',
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
                    'plain_hint'  => '123',
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
                    'plain_hint'  => '123',
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
        $request->session()->forget('pos_user');

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'redirect' => '/login']);
        }
        return redirect('/login');
    }

    public function authorizeManagerAction(Request $request)
    {
        $this->ensureSeeded();

        $password   = trim((string) $request->input('password'));
        $actionName = trim((string) $request->input('action_name', 'صلاحية إدارية'));
        $targetUrl  = trim((string) $request->input('target_url', ''));

        $currentUser = $request->session()->get('pos_user', ['id' => null, 'full_name' => 'كاشير1']);

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
            self::logActivity($currentUser['full_name'], 'تصريح عملية', 'فشلت', $actionName, 'فشل إدخال كلمة مرور المدير', $currentUser['id']);
            return response()->json(['ok' => false, 'message' => 'كلمة مرور المدير غير صحيحة!'], 422);
        }

        $grantedUrls = $request->session()->get('pos_temp_authorized_urls', []);
        if ($targetUrl) {
            $grantedUrls[] = parse_url($targetUrl, PHP_URL_PATH) ?: $targetUrl;
            $request->session()->put('pos_temp_authorized_urls', array_unique($grantedUrls));
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

        $password    = trim((string) $request->input('password'));
        $drawerCash  = (float) $request->input('drawer_cash', 0);
        $notes       = trim((string) $request->input('notes', ''));
        $currentUser = $request->session()->get('pos_user', ['id' => null, 'full_name' => 'مدير الفرع']);

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

        $request->session()->forget(['pos_user', 'pos_temp_authorized_urls']);

        return response()->json([
            'ok'       => true,
            'redirect' => '/login',
            'message'  => 'تم إغلاق الوردية بنجاح وتسجيل المبلغ الموجود بالدرج.',
        ]);
    }

    public function usersView()
    {
        $this->ensureSeeded();
        return view('admin.users');
    }

    public function usersBootstrapApi()
    {
        $this->ensureSeeded();

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
            $data['plain_hint'] = $password;
        }

        if ($id) {
            DB::table('pos_users')->where('id', $id)->update($data);
        } else {
            if ($password === '') {
                $data['password']   = Hash::make('123');
                $data['plain_hint'] = '123';
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

        self::logActivity($posUser['full_name'] ?? 'مدير النظام', 'صلاحية متاحة', '-', "حفظ بيانات وصلاحيات المستخدم: {$fullName}", 'إدارة المستخدمين');

        return $this->usersBootstrapApi();
    }

    public function changeUserPasswordApi(Request $request, $id)
    {
        $password = trim((string) $request->input('password'));
        if ($password === '') {
            return response()->json(['ok' => false, 'message' => 'أدخل كلمة المرور الجديدة.'], 422);
        }

        DB::table('pos_users')->where('id', $id)->update([
            'password'   => Hash::make($password),
            'plain_hint' => $password,
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true, 'message' => 'تم تغيير كلمة المرور بنجاح.']);
    }

    public function toggleDeleteUserApi($id)
    {
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

        return $this->usersBootstrapApi();
    }

    public function shiftsLogView()
    {
        $this->ensureSeeded();
        return view('admin.shifts_log');
    }

    public function shiftsLogApi(Request $request)
    {
        $this->ensureSeeded();

        $dateMode = $request->query('date_mode', 'all');
        $date1    = $request->query('date1');
        $date2    = $request->query('date2');
        $user     = $request->query('user', 'all');

        $q = DB::table('pos_shifts')->orderBy('id', 'desc');

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

        return response()->json([
            'ok'     => true,
            'shifts' => $shifts,
            'users'  => $usersList,
        ]);
    }

    public function loginLogView()
    {
        $this->ensureSeeded();
        return view('admin.login_log');
    }

    public function loginLogApi(Request $request)
    {
        $this->ensureSeeded();

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
}