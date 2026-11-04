<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. جدول الموردين (Suppliers)
        // =====================================================================
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('code', 50)->nullable()->index();
            $table->string('company', 150)->index();       // اسم الشركة أو المورد
            $table->string('contact', 150)->nullable();    // اسم المسؤول / المندوب
            $table->string('phone', 30)->nullable()->index();
            $table->string('phone2', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('email', 120)->nullable();
            $table->text('notes')->nullable();

            // الأرصدة المالية للمورد (credit = دائن له فلوس علينا، debit = مدين لنا فلوس عنده)
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->string('balance_type', 20)->default('credit'); // credit | debit

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================================
        // 2. جدول حركات ودفعات الموردين (Supplier Transactions)
        // =====================================================================
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('type', 30)->index(); // payment (سداد لمورد) | refund (استرداد من مورد) | settlement (تسوية)
            $table->date('transaction_date')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_method', 30)->default('cash'); // cash | wallet | instapay | bank-transfer
            $table->string('source', 40)->default('drawer');       // drawer | main-treasury
            $table->string('ref_no', 60)->nullable();
            $table->text('note')->nullable();
            $table->string('user_name', 100)->default('مدير الفرع');

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================================
        // 3. جدول فواتير المشتريات (Purchase Invoices)
        // يخدم: شاشة إضافة فاتورة مشتريات (/purchases/create) + سجل المشتريات (/purchases/log)
        // =====================================================================
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->unsignedBigInteger('code')->index();             // كود الفاتورة الداخلي (مثال: 781)
            $table->string('paper_number', 80)->nullable()->index(); // رقم فاتورة المورد الورقية (مثال: INV-0781)
            $table->date('invoice_date')->index();

            // بيانات المورد
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name', 150)->default('مورد نقدي عام')->index();

            $table->unsignedInteger('product_count')->default(0);
            $table->decimal('sub_total', 14, 2)->default(0);
            $table->decimal('invoice_discount', 14, 2)->default(0);
            $table->decimal('invoice_extra', 14, 2)->default(0);     // مصاريف أخرى / شحن
            $table->decimal('total', 14, 2)->default(0);             // إجمالي الفاتورة الصافي
            $table->decimal('paid', 14, 2)->default(0);              // المبلغ المدفوع
            $table->decimal('remaining', 14, 2)->default(0);         // المتبقي للمورد (ذمة/آجل)
            $table->decimal('returns_value', 14, 2)->default(0);     // قيمة مرتجعات المشتريات على هذه الفاتورة

            // الدفع ومصدر الأموال والوردية
            $table->string('payment_type', 20)->default('cash')->index();   // cash | credit
            $table->string('payment_method', 30)->default('cash')->index(); // cash | wallet | instapay | bank-transfer | credit
            $table->string('source', 40)->default('drawer')->index();       // drawer (الدرج) | main-treasury (الخزنة الرئيسية)
            $table->text('notes')->nullable();
            $table->string('user_name', 100)->default('مدير الفرع');
            $table->string('shift_id', 60)->default('current')->index();
            $table->boolean('shift_open')->default(true)->index();

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================================
        // 4. جدول أصناف فاتورة المشتريات (Purchase Invoice Items)
        // =====================================================================
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->cascadeOnDelete();
            $table->unsignedBigInteger('item_id')->nullable()->index();

            $table->string('name');
            $table->string('category_name', 120)->nullable()->index();
            $table->string('barcode', 100)->nullable()->index();
            $table->string('unit', 30)->default('قطعة');
            $table->string('quantity_type', 20)->default('piece'); // piece | weight
            $table->text('description')->nullable();

            $table->decimal('quantity', 14, 3)->default(1);
            $table->decimal('returned_qty', 14, 3)->default(0);     // لدعم مرتجعات المشتريات
            $table->decimal('cost', 14, 4)->default(0);             // سعر شراء الوحدة
            $table->decimal('discount', 14, 2)->default(0);         // الخصم على الصنف
            $table->decimal('total', 14, 2)->default(0);            // الإجمالي بعد الخصم
            $table->decimal('sell_price', 14, 2)->default(0);       // سعر البيع المعتمد
            $table->decimal('original_sell_price', 14, 2)->default(0); // سعر البيع الأصلي قبل التعديل
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // =====================================================================
        // 5. جدول مرتجعات المشتريات للموردين (Purchase Returns)
        // يخدم: شاشات مرتجعات المشتريات (/returns/suppliers & /returns/suppliers/create)
        // =====================================================================
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('return_number', 60)->unique()->index();
            $table->foreignId('purchase_invoice_id')->nullable()->constrained('purchase_invoices')->nullOnDelete();
            $table->string('invoice_code', 50)->nullable()->index();
            $table->date('return_date')->index();

            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name', 150)->default('مورد نقدي عام')->index();

            $table->unsignedInteger('item_count')->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('cash_refund', 14, 2)->default(0);      // المسترد نقداً للدرج/الخزنة
            $table->decimal('account_deduction', 14, 2)->default(0); // المخصوم من حساب المورد الآجل
            $table->string('source', 40)->default('drawer');
            $table->text('notes')->nullable();
            $table->string('user_name', 100)->default('مدير الفرع');

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================================
        // 6. جدول أصناف مرتجع المشتريات (Purchase Return Items)
        // =====================================================================
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->cascadeOnDelete();
            $table->foreignId('purchase_invoice_item_id')->nullable()->constrained('purchase_invoice_items')->nullOnDelete();
            $table->unsignedBigInteger('item_id')->nullable()->index();

            $table->string('name');
            $table->string('barcode', 100)->nullable();
            $table->decimal('quantity', 14, 3)->default(1);
            $table->decimal('cost', 14, 4)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('supplier_transactions');
        Schema::dropIfExists('suppliers');
    }
};
