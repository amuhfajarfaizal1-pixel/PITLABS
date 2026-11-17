<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pemulihan non-destruktif untuk instalasi yang mencatat migration 5C-1,
 * tetapi tabel transaksi hilang/terhapus. Tabel yang sudah ada tidak disentuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id(); $table->string('setting_key', 100)->unique(); $table->text('setting_value')->nullable(); $table->timestamps();
            });
        }
        if (! Schema::hasTable('rental_orders')) {
            Schema::create('rental_orders', function (Blueprint $table) {
                $table->id(); $table->string('order_number', 40)->unique(); $table->foreignId('user_id')->constrained('users');
                $table->enum('order_status', ['NEW_ORDER','WAITING_PAYMENT','PAID','PROCESSING','READY_FOR_PICKUP','CANCELLED','COMPLETED'])->default('NEW_ORDER');
                $table->enum('payment_status', ['UNPAID','PAID','FAILED','REFUNDED'])->default('UNPAID');
                $table->enum('pickup_status', ['NOT_PICKED_UP','PICKED_UP'])->default('NOT_PICKED_UP');
                $table->enum('rental_status', ['NOT_STARTED','RENTING','DUE_SOON','OVERDUE','RETURNED','UNDER_INSPECTION','COMPLETED'])->default('NOT_STARTED');
                $table->enum('payment_method', ['QRIS','VIRTUAL_ACCOUNT','COD']);
                $table->decimal('subtotal',14,2)->nullable(); $table->decimal('additional_fees',14,2)->default(0); $table->decimal('total_amount',14,2)->nullable();
                $table->dateTime('customer_terms_accepted_at'); $table->timestamps();
                $table->index(['order_status','payment_status','pickup_status','rental_status'],'idx_orders_statuses'); $table->index('created_at','idx_orders_created');
            });
        }
        if (! Schema::hasTable('rental_order_items')) {
            Schema::create('rental_order_items', function (Blueprint $table) {
                $table->id(); $table->foreignId('order_id')->constrained('rental_orders'); $table->foreignId('product_id')->constrained('products');
                $table->foreignId('unit_id')->nullable()->constrained('inventory_units'); $table->string('product_name_snapshot',180); $table->string('unit_code_snapshot',80)->nullable();
                $table->dateTime('start_at'); $table->dateTime('end_at'); $table->unsignedInteger('quantity')->default(1); $table->decimal('daily_rate',14,2)->nullable(); $table->decimal('line_total',14,2)->nullable();
                $table->index(['unit_id','start_at','end_at'],'idx_order_items_unit_dates');
            });
        }
        if (! Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $table) {
                $table->id(); $table->foreignId('order_item_id')->constrained('rental_order_items'); $table->foreignId('unit_id')->nullable()->constrained('inventory_units');
                $table->string('resource_key',120)->nullable(); $table->dateTime('start_at'); $table->dateTime('end_at');
                $table->enum('reservation_status',['HOLD','CONFIRMED','CANCELLED','EXPIRED','COMPLETED'])->default('HOLD'); $table->dateTime('hold_expires_at')->nullable(); $table->timestamp('created_at')->useCurrent();
                $table->index(['unit_id','reservation_status','start_at','end_at'],'idx_reservation_unit_dates'); $table->index(['reservation_status','hold_expires_at'],'idx_reservation_expiry');
            });
        }
        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id(); $table->foreignId('order_id')->unique()->constrained('rental_orders'); $table->string('invoice_number',50)->unique();
                $table->char('qr_token_hash',64)->unique(); $table->timestamp('issued_at')->useCurrent(); $table->json('document_snapshot');
            });
        }
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id(); $table->foreignId('order_id')->constrained('rental_orders'); $table->string('provider',80)->nullable(); $table->string('provider_reference',190)->nullable();
                $table->enum('payment_method',['QRIS','VIRTUAL_ACCOUNT','COD']); $table->decimal('amount',14,2)->nullable(); $table->char('currency',3)->default('IDR');
                $table->enum('payment_status',['UNPAID','PAID','FAILED','REFUNDED'])->default('UNPAID'); $table->foreignId('verified_by')->nullable()->constrained('users');
                $table->dateTime('verified_at')->nullable(); $table->json('raw_event')->nullable(); $table->timestamps(); $table->unique(['provider','provider_reference'],'uq_provider_reference'); $table->index(['payment_status','created_at'],'idx_payments_status');
            });
        }
        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id(); $table->foreignId('actor_user_id')->nullable()->constrained('users'); $table->string('entity_type',80); $table->unsignedBigInteger('entity_id');
                $table->string('action',100); $table->json('old_values')->nullable(); $table->json('new_values')->nullable(); $table->text('reason')->nullable(); $table->string('ip_address',45)->nullable();
                $table->timestamp('created_at')->useCurrent(); $table->index(['entity_type','entity_id','created_at'],'idx_audit_entity');
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus tabel pada rollback: migration ini adalah pemulihan data.
    }
};
