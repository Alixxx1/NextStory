<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. تحديث أو إنشاء جدول المستخدمين والصلاحيات (pos_users لتجنب أي تعارض مع جدول users الافتراضي)
        if (!Schema::hasTable('pos_users')) {
            Schema::create('pos_users', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->string('username', 80)->unique();       // مثل: Cashier1, admin, BranchAdmin
                $table->string('full_name', 120);               // مثل: كاشير1, مدير النظام, مدير الفرع
                $table->string('password');                     // كلمة المرور المشفرة
                $table->string('plain_hint', 60)->nullable();   // تلميح اختياري أو للتسهيل المحلي
                $table->string('role', 40)->default('user');    // 'system_admin' (مدير النظام) | 'branch_admin' (مدير فرع) | 'user' (مستخدم/كاشير)
                $table->string('role_label', 60)->default('مستخدم'); // 'مدير' | 'مستخدم'
                $table->json('permissions')->nullable();        // مصفوفة الصلاحيات المسموحة لهذا المستخدم
                $table->boolean('is_active')->default(true);
                $table->boolean('is_synced')->default(false)->index();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 2. جدول سجل الورديات (pos_shifts)
        if (!Schema::hasTable('pos_shifts')) {
            Schema::create('pos_shifts', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->integer('shift_no')->index();           // رقم الوردية (مثل 136)
                $table->string('status', 20)->default('open');  // 'open' (مفتوحة) | 'closed' (مغلقة - تلون بالأحمر)
                $table->date('shift_date')->index();            // تاريخ فتح الوردية
                $table->dateTime('started_at');                 // توقيت البداية الفعلي
                $table->string('started_time_label', 40);       // مثل: 2026/11/04 - 01:35AM
                $table->integer('started_by_id')->nullable();
                $table->string('started_by_name', 100);         // بدأت بواسطة (مدير الفرع / كاشير1)
                $table->string('start_device', 100)->default('DESKTOP-0BV4S0U');
                $table->dateTime('ended_at')->nullable();       // توقيت النهاية
                $table->string('ended_time_label', 40)->nullable();
                $table->string('duration_label', 60)->nullable(); // المدة (مثال: 08 س و 15 د)
                $table->integer('ended_by_id')->nullable();
                $table->string('ended_by_name', 100)->nullable(); // أنهيت بواسطة
                $table->string('end_device', 100)->nullable();
                $table->decimal('opening_cash', 14, 3)->default(0);
                $table->decimal('expected_drawer_cash', 14, 3)->default(0);
                $table->decimal('counted_drawer_cash', 14, 3)->default(0); // المبلغ المدخل عند تقفيل الوردية
                $table->decimal('cash_difference', 14, 3)->default(0);
                $table->boolean('report_sent')->default(false);
                $table->text('notes')->nullable();
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamps();
            });
        }

        // 3. جدول سجل دخول المستخدمين والعمليات الأمنية (user_activity_logs)
        if (!Schema::hasTable('user_activity_logs')) {
            Schema::create('user_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->date('log_date')->index();              // للبحث السريع بالتاريخ
                $table->string('time_label', 45);               // مثل: 2026/11/04 - 01:09AM
                $table->integer('user_id')->nullable()->index();
                $table->string('user_name', 100)->index();      // مثل: مدير الفرع / كاشير1
                $table->string('operation_type', 80)->index();  // 'تسجيل دخول' | 'صلاحية متاحة' | 'تصريح عملية' | 'إغلاق وردية'
                $table->string('status', 30)->default('-');     // 'نجحت' | 'فشلت' | '-'
                $table->string('device_name', 100)->default('DESKTOP-0BV4S0U');
                $table->string('description', 255)->nullable(); // مثل: إضافة فاتورة مبيعات / مراجعة قائمة الموردين
                $table->string('note', 255)->nullable();        // مثل: صلاحية متاحة في الإعدادات. / تم التصريح بواسطة مدير النظام
                $table->boolean('is_synced')->default(false);
                $table->timestamps();
            });
        }

        // 4. جدول إدارة الخزائن (safes)
        if (!Schema::hasTable('safes')) {
            Schema::create('safes', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->integer('tenant_id')->default(1)->index();
                $table->integer('branch_id')->default(1)->index();
                $table->string('code', 20);                     // 001, 002, 003
                $table->string('name', 100);                    // الدرج, الخزنة, الحساب البنكي
                $table->string('type', 50)->default('خزينة');   // 'خزينة' | 'حساب بنكي'
                $table->string('description', 150)->nullable();
                $table->string('payment_method_ar', 80)->nullable();
                $table->string('payment_method_en', 80)->nullable(); // Cash, Credit Card
                $table->string('status', 30)->default('مفعل');
                $table->decimal('opening_balance', 14, 3)->default(0);
                $table->decimal('balance', 14, 3)->default(0);
                $table->boolean('is_basic')->default(true);     // حساب أساسي
                $table->boolean('is_synced')->default(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 5. جدول حركات الخزائن (إيداع / سحب / تحويل / تقفيل وردية)
        if (!Schema::hasTable('safe_transactions')) {
            Schema::create('safe_transactions', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('safe_id')->constrained('safes')->cascadeOnDelete();
                $table->integer('to_safe_id')->nullable();
                $table->date('tx_date')->index();
                $table->string('time_label', 45);
                $table->string('type', 40); // 'deposit' (إيداع) | 'withdraw' (سحب) | 'transfer' (تحويل) | 'shift_close' (تقفيل وردية)
                $table->decimal('amount', 14, 3)->default(0);
                $table->decimal('balance_before', 14, 3)->default(0);
                $table->decimal('balance_after', 14, 3)->default(0);
                $table->string('statement', 255)->nullable();
                $table->string('user_name', 100)->default('مدير الفرع');
                $table->boolean('is_synced')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('safe_transactions');
        Schema::dropIfExists('safes');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('pos_shifts');
        Schema::dropIfExists('pos_users');
    }
};