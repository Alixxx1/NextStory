<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hq_admins', function (Blueprint $t) {
            $t->id(); $t->string('email')->unique(); $t->string('username'); $t->string('password'); $t->timestamps();
        });
        Schema::create('hq_plans', function (Blueprint $t) {
            $t->string('key')->primary(); $t->string('name'); $t->unsignedInteger('days')->nullable();
            $t->decimal('price', 12, 2)->default(0); $t->string('tier')->default('local_pos'); $t->boolean('active')->default(true); $t->timestamps();
        });
        Schema::create('hq_requests', function (Blueprint $t) {
            $t->id(); $t->string('store_name'); $t->string('owner'); $t->string('phone', 40); $t->string('address', 500)->nullable();
            $t->string('plan_key', 40); $t->string('fingerprint', 64)->index(); $t->string('status', 20)->default('pending'); $t->timestamps();
        });
        Schema::create('hq_stores', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('owner')->nullable(); $t->string('phone', 40)->nullable(); $t->string('address', 500)->nullable();
            $t->string('fingerprint', 64)->unique(); $t->string('plan', 40); $t->string('tier', 20)->default('local_pos');
            $t->dateTime('expires_at')->nullable(); $t->string('status', 20)->default('active');
            $t->string('store_uuid', 40)->nullable()->unique(); $t->string('sync_api_token', 80)->nullable()->index();
            $t->string('api_token', 80)->nullable()->index(); $t->string('mobile_password')->nullable();
            $t->dateTime('last_sync_at')->nullable(); $t->dateTime('last_seen_at')->nullable(); $t->timestamps();
        });

        Schema::create('cloud_invoices', function (Blueprint $t) {
            $t->id(); $t->string('store_uuid', 40); $t->unsignedBigInteger('local_id'); $t->string('order_no', 40)->nullable();
            $t->string('customer_name')->nullable(); $t->string('order_type', 30)->nullable(); $t->string('status', 20)->nullable();
            $t->date('invoice_date')->nullable(); $t->decimal('net_value', 14, 2)->default(0); $t->decimal('paid_amount', 14, 2)->default(0);
            $t->decimal('due_amount', 14, 2)->default(0); $t->timestamps(); $t->unique(['store_uuid', 'local_id']);
        });
        Schema::create('cloud_items', function (Blueprint $t) {
            $t->id(); $t->string('store_uuid', 40); $t->unsignedBigInteger('local_id'); $t->string('name'); $t->string('barcode', 100)->nullable();
            $t->decimal('qty', 14, 3)->default(0); $t->decimal('min_stock', 14, 3)->default(0); $t->decimal('retail_price', 14, 2)->default(0);
            $t->timestamps(); $t->unique(['store_uuid', 'local_id']);
        });
        Schema::create('cloud_parties', function (Blueprint $t) {
            $t->id(); $t->string('store_uuid', 40); $t->string('kind', 12); $t->unsignedBigInteger('local_id'); $t->string('name');
            $t->string('phone', 40)->nullable(); $t->decimal('balance', 14, 2)->default(0); $t->timestamps(); $t->unique(['store_uuid', 'kind', 'local_id']);
        });
        Schema::create('cloud_cash', function (Blueprint $t) {
            $t->id(); $t->string('store_uuid', 40); $t->string('name'); $t->decimal('balance', 14, 2)->default(0); $t->timestamps(); $t->unique(['store_uuid', 'name']);
        });
        Schema::create('cloud_vouchers', function (Blueprint $t) {
            $t->id(); $t->string('store_uuid', 40)->index(); $t->string('type', 10); $t->string('party_kind', 12); $t->unsignedBigInteger('party_id');
            $t->string('party_name'); $t->decimal('amount', 14, 2); $t->string('note', 500)->nullable();
            $t->string('status', 12)->default('pending'); $t->timestamps();
        });

        Schema::create('remote_vouchers', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('remote_id')->unique(); $t->string('type', 10); $t->string('party_kind', 12); $t->unsignedBigInteger('party_id');
            $t->string('party_name'); $t->decimal('amount', 14, 2); $t->string('note', 500)->nullable();
            $t->string('status', 12)->default('pending'); $t->boolean('acked')->default(false); $t->string('decided_by')->nullable(); $t->timestamps();
        });
    }

    public function down()
    {
        foreach (['remote_vouchers', 'cloud_vouchers', 'cloud_cash', 'cloud_parties', 'cloud_items', 'cloud_invoices', 'hq_stores', 'hq_requests', 'hq_plans', 'hq_admins'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
