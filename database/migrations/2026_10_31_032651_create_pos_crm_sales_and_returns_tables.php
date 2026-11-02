<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. جدول مناطق التوصيل وأسعارها (Delivery Zones)
        // يخدم: شاشة مناطق التوصيل + شاشة العملاء + شاشة البيع
        // =====================================================================
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('name', 100)->index();
            $table->decimal('fee', 12, 2)->default(0);

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        // =====================================================================
        // 2. جدول سجل وبيانات العملاء الموحد (Customers)
        // يخدم: قائمة العملاء + حساب عميل (9 تابات) + شاشة البيع (F2)
        // =====================================================================
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('code', 50)->nullable()->index();
            $table->string('name', 150)->index();
            $table->string('phone1', 30)->index();
            $table->string('phone2', 30)->nullable()->index();

            $table->text('contact_note')->nullable();
            $table->text('invoice_note')->nullable();

            // المنطقة الرئيسية لسرعة الفلترة وعد العملاء في شاشة المناطق
            $table->foreignId('primary_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->string('primary_zone_name', 100)->nullable()->index();

            // العناوين الـ 3 (نصية للبحث السريع + JSON للتفاصيل واللوكيشن والرسوم)
            $table->text('addr1')->nullable();
            $table->text('addr2')->nullable();
            $table->text('addr3')->nullable();
            $table->json('addresses')->nullable();

            // الأرصدة المالية (رصيد أول المدة + الرصيد الحالي النهائي)
            $table->decimal('opening_balance', 14, 2)->default(0); // رصيد أول المدة في شاشة حساب عميل
            $table->decimal('balance', 14, 2)->default(0);         // القيمة المطلقة للرصيد الحالي
            $table->string('balance_type', 20)->default('debit');  // debit (مدين - عليه) | credit (دائن - له)

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes(); // يدعم فلتر "المحذوفات"
            $table->timestamps();
        });

        // =====================================================================
        // 3. جدول فواتير المبيعات وأرشيف الطلبات (Sales Invoices)
        // يخدم: شاشة البيع (F12) + أرشيف الطلبات + حساب عميل + المرتجعات
        // =====================================================================
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('order_no', 50)->index(); // رقم الطلب / الفاتورة
            $table->string('order_type', 30)->default('استلام')->index(); // استلام | توصيل | أونلاين
            $table->string('price_tier', 30)->default('retail'); // retail | semi_wholesale | wholesale
            $table->string('status', 30)->default('closed')->index(); // closed | suspended
            $table->string('return_status', 20)->nullable()->index(); // null | partial | full

            $table->date('invoice_date')->index();
            $table->string('created_time_label', 40)->nullable();
            $table->string('closed_time_label', 40)->nullable();

            // بيانات العميل والعنوان وقت الفاتورة
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name', 150)->default('عميل نقدي')->index();
            $table->string('customer_phone', 30)->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_zone', 100)->nullable();

            // ملخص الأصناف للفلاتر والجدول في الأرشيف
            $table->text('snippet')->nullable();
            $table->decimal('total_qty', 14, 3)->default(0);
            $table->string('main_category_name', 120)->nullable()->index();

            // الحسابات المالية الدقيقة وفصل مبيعات المحل عن الخوارج (F4)
            $table->decimal('store_sub_total', 14, 2)->default(0);    // إجمالي أصناف المحل فقط قبل الخصم
            $table->decimal('external_sub_total', 14, 2)->default(0); // إجمالي الخوارج فقط (لا تخصم من المخزن ولا تدخل في الربح)
            $table->decimal('gross_total', 14, 2)->default(0);        // الإجمالي قبل الخصم والخدمة
            $table->decimal('service_fee', 14, 2)->default(0);        // خدمة التوصيل
            $table->decimal('invoice_discount', 14, 2)->default(0);   // خصم الفاتورة
            $table->decimal('item_discount', 14, 2)->default(0);      // خصم الأصناف
            $table->decimal('net_value', 14, 2)->default(0);          // قيمة الفاتورة النهائية (total)
            $table->decimal('store_net_total', 14, 2)->default(0);    // صافي مبيعات المحل بعد الخصم

            // التكلفة والربح والمرتجعات
            $table->decimal('cost_total', 14, 2)->default(0);         // التكلفة (قابلة للتعديل في الأرشيف)
            $table->decimal('profit_total', 14, 2)->default(0);       // صافي الربح
            $table->decimal('returns_value', 14, 2)->default(0);      // إجمالي قيمة المرتجعات النشطة على هذه الفاتورة

            // بيانات الدفع والآجل والخزينة
            $table->string('payment_type', 20)->default('cash')->index();   // cash | credit (آجل)
            $table->string('payment_method', 30)->default('cash')->index(); // cash | card | vodafone_cash | instapay
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('due_amount', 14, 2)->default(0);               // المتبقي الآجل المرحل على العميل
            $table->string('safe_name', 80)->default('الدرج')->index();

            // بيانات الوردية والمندوب والمستخدمين والأجهزة
            $table->string('shift_name', 80)->default('مدير الفرع')->index();
            $table->string('shift_period', 20)->default('morning')->index(); // morning | evening
            $table->string('courier_name', 100)->nullable()->index();
            $table->string('created_by', 100)->default('مدير الفرع')->index();
            $table->string('created_device', 100)->default('DESKTOP-A1');
            $table->string('confirmed_by', 100)->nullable()->index();
            $table->string('confirmed_device', 100)->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================================
        // 4. جدول أصناف فاتورة المبيعات (Sales Invoice Items)
        // يخدم: تفاصيل الفاتورة + شاشة المرتجع الجزئي + تاب سجل الأصناف للعميل
        // =====================================================================
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->unsignedBigInteger('item_id')->nullable()->index();

            $table->string('name');
            $table->string('barcode', 100)->nullable()->index();
            $table->string('category_name', 120)->nullable();
            $table->decimal('qty', 14, 3)->default(1);
            $table->decimal('returned_qty', 14, 3)->default(0); // مجموع ما تم إرجاعه من هذا السطر
            $table->string('quantity_type', 20)->default('piece'); // piece | weight
            $table->string('unit', 30)->default('قطعة');

            $table->decimal('cost', 14, 2)->default(0);  // سعر الشراء وقت البيع
            $table->decimal('price', 14, 2)->default(0); // سعر البيع للوحدة
            $table->decimal('total', 14, 2)->default(0); // إجمالي السطر
            $table->boolean('is_external')->default(false)->index(); // هل هو صنف خوارج؟
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // =====================================================================
        // 5. جدول سجل مرتجعات المبيعات (Sales Returns)
        // يخدم: شاشة سجل المرتجعات (/returns/log) + المرتجع الجزئي + حساب عميل
        // =====================================================================
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->string('return_number', 60)->unique()->index(); // مثل: RET-622
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->string('invoice_number', 50)->index();          // رقم الطلب الأصلي
            $table->date('original_date')->nullable()->index();     // تاريخ الفاتورة الأصلية
            $table->date('return_date')->index();                   // تاريخ تسجيل المرتجع

            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name', 150)->default('عميل نقدي')->index();
            $table->string('customer_phone', 30)->nullable();

            $table->unsignedInteger('item_count')->default(0);
            $table->text('products_summary')->nullable(); // أسماء وباركودات الأصناف المرتجعة للبحث الفوري في سجل المرتجعات

            $table->decimal('total', 14, 2)->default(0);          // القيمة الكلية للمرتجع
            $table->decimal('cash_refund', 14, 2)->default(0);    // المبلغ المسترد (نقداً)
            $table->decimal('account_credit', 14, 2)->default(0); // المضاف لحساب العميل (خصماً من مديونيته)

            $table->string('return_type', 20)->default('partial'); // partial | full
            $table->string('shift', 30)->default('morning')->index(); // morning | evening
            $table->string('user_name', 100)->default('مدير الفرع');
            $table->text('notes')->nullable();

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes(); // يدعم خيار "المحذوفات" في سجل المرتجعات
            $table->timestamps();
        });

        // =====================================================================
        // 6. جدول أصناف حركة المرتجع (Sales Return Items)
        // يخدم: تعديل المرتجع الجزئي واسترجاع الكميات للمخزن بدقة
        // =====================================================================
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignId('sales_invoice_item_id')->nullable()->constrained('sales_invoice_items')->nullOnDelete();
            $table->unsignedBigInteger('item_id')->nullable()->index();

            $table->string('name');
            $table->string('barcode', 100)->nullable();
            $table->decimal('quantity', 14, 3)->default(1);
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });

        // =====================================================================
        // 7. جدول حركات حساب العميل المالية (Customer Transactions)
        // يخدم: شاشة حساب عميل (تحصيل دفعة F4، رد مبلغ، سجل الدفعات، التسويات، الحجوزات)
        // =====================================================================
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('type', 30)->index(); // payment (تحصيل دفعة) | refund (رد مبلغ) | settlement (تسوية) | reservation (حجز)
            $table->date('transaction_date')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_method', 30)->default('cash'); // cash | card | vodafone_cash | instapay
            $table->string('safe_name', 80)->default('الدرج');
            $table->string('ref_no', 60)->nullable();
            $table->text('note')->nullable();
            $table->string('user_name', 100)->default('مدير الفرع');

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================================
        // 8. جدول سجل ملحوظات العميل (Customer Notes)
        // يخدم: تاب "سجل الملحوظات" في شاشة حساب عميل
        // =====================================================================
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('note_date')->index();
            $table->text('text');
            $table->string('user_name', 100)->default('مدير الفرع');
            $table->boolean('is_synced')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_notes');
        Schema::dropIfExists('customer_transactions');
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('delivery_zones');
    }
};
