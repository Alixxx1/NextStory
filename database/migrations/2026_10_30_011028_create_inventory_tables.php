<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول التصنيفات (Categories)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();
            $table->string('code', 50)->nullable();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_published_online')->default(true);
            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. جدول أصناف المخزن الشامل (Items) - يخدم الكاشير والموبايل والموقع الإلكتروني
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            // البيانات الأساسية
            $table->string('item_type', 50)->default('صنف مخزني'); // صنف مخزني | صنف خدمي | خامات | صنف مجمع
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->string('unit', 50)->default('قطعة');
            $table->string('default_purchase_unit', 20)->default('main'); // main | unit2 | unit3
            $table->string('barcode', 100)->nullable()->index();

            // أرصدة المخزون والتكلفة
            $table->decimal('qty', 14, 3)->default(0);
            $table->decimal('last_buy_price', 14, 2)->default(0);
            $table->decimal('avg_cost', 14, 2)->default(0);
            $table->string('last_buy_date', 30)->nullable();
            $table->decimal('min_stock', 14, 3)->default(0);
            $table->decimal('max_stock', 14, 3)->default(0);
            $table->string('supplier_name')->nullable()->index();
            $table->boolean('has_purchase_invoice')->default(false);
            $table->string('last_move_date', 30)->nullable();

            // الأسعار المتعددة للكاشير (القطاعي إجباري، الباقي اختياري)
            $table->decimal('retail_price', 14, 2)->default(0);
            $table->decimal('semi_wholesale_price', 14, 2)->nullable();
            $table->decimal('wholesale_price', 14, 2)->nullable();

            // تسعير وعروض الموقع الإلكتروني (الأوفر: 100 بدلاً من 150)
            $table->boolean('is_published_online')->default(false)->index();
            $table->decimal('online_price', 14, 2)->nullable(); // السعر المعروض على الموقع (إن تركة فارغاً يأخذ سعر القطاعي)
            $table->decimal('online_compare_at_price', 14, 2)->nullable(); // السعر قبل الخصم المشطوب عليه (مثال: 150)

            // الوحدات الفرعية (تُحفظ كـ JSON لمرونة تامة في SQLite و MySQL)
            $table->json('sub_units')->nullable();

            // الإعدادات الضريبية
            $table->boolean('tax_included')->default(true);
            $table->string('tax_mode', 30)->default('default');
            $table->decimal('tax_rate', 5, 2)->default(0);

            // خيارات الصنف والصورة
            $table->boolean('is_disabled')->default(false)->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->boolean('allow_price_edit')->default(false);
            $table->boolean('has_commission')->default(false);
            $table->decimal('commission_val', 12, 2)->default(0);
            $table->longText('image')->nullable();

            // حقول المزامنة السحابية وتوسعات المستقبل
            $table->json('extra_attributes')->nullable(); // لأي إضافات مستقبلية بدون تعديل الداتابيز
            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. جدول سجل حركات الصنف (Item Movements)
        Schema::create('item_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('move_date', 40);
            $table->string('type', 100);
            $table->string('ref_no', 100)->nullable();
            $table->string('party', 150)->nullable();
            $table->string('user_name', 100)->nullable();
            $table->decimal('before_qty', 14, 3)->default(0);
            $table->decimal('in_qty', 14, 3)->default(0);
            $table->decimal('out_qty', 14, 3)->default(0);
            $table->decimal('after_qty', 14, 3)->default(0);
            $table->decimal('price', 14, 2)->default(0);
            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_movements');
        Schema::dropIfExists('items');
        Schema::dropIfExists('categories');
    }
};
