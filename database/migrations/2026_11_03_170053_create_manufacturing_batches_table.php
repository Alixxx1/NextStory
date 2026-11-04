<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacturing_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('branch_id')->default(1)->index();

            // ربط بالصنف المنتج في جدول الأصناف (items)
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('product_name', 150)->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('category_name', 120)->nullable();
            $table->string('final_unit', 40)->default('كيلو');
            $table->string('barcode', 100)->nullable()->index();

            // طريقة الخصم: immediate (خصم فوري) | on_sale (خصم عند البيع)
            $table->string('deduction_mode', 30)->default('immediate')->index();

            // الحسابات والتكاليف والربح
            $table->decimal('total_weight_kg', 14, 3)->default(0);
            $table->decimal('total_batch_cost', 14, 2)->default(0);
            $table->decimal('cost_per_kg', 14, 2)->default(0);
            $table->decimal('profit_percent', 10, 2)->default(0);
            $table->decimal('selling_price', 14, 2)->default(0);

            // مكونات الخلطة أو البوكس
            $table->json('components')->nullable();

            $table->boolean('is_synced')->default(false)->index();
            $table->timestamp('synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturing_batches');
    }
};