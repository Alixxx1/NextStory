<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. جدول شجرة حسابات وأقسام وبنود المصروفات (Expense Accounts Tree)
        // يحفظ (الحساب الرئيسي -> الأقسام الفرعية -> البنود)
        // =====================================================================
        Schema::create('expense_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('name', 120)->index(); // مثل: مصروفات تشغيل، مصروفات تأسيس
            $table->json('departments')->nullable(); // مصفوفة الأقسام وبداخل كل قسم مصفوفة البنود

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        // =====================================================================
        // 2. جدول سجل وتقارير المصروفات (Expenses)
        // يخدم: شاشة إدارة المصروفات (/expenses) + شاشة تقارير المصروفات (/expenses/reports)
        // =====================================================================
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->date('expense_date')->index(); // تاريخ الصرف YYYY-MM-DD
            $table->string('account_name', 120)->default('مصروفات تشغيل')->index();
            $table->string('department_name', 120)->default('عام')->index();
            $table->string('item_name', 150)->default('مصروف عام')->index();
            $table->string('description', 500)->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('source', 80)->default('الدرج')->index(); // الدرج | الخزينة الرئيسية | حساب بنكي / محفظة

            // بيانات الوردية والمستخدمين والتوقيتات لتقارير المصروفات
            $table->string('shift_name', 100)->default('مدير الفرع // 06:00AM')->index();
            $table->boolean('shift_open')->default(true)->index();
            $table->string('created_by', 100)->default('مدير الفرع')->index();
            $table->string('updated_by', 100)->default('مدير الفرع')->index();
            $table->string('created_time_label', 40)->nullable();
            $table->string('updated_time_label', 40)->nullable();

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes(); // يدعم فلتر "المحذوفات" في شاشة التقارير
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_accounts');
    }
};
