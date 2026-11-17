<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| docs/DATABASE.md: rental_orders. Empat status SELALU terpisah (BUSINESS_RULES).
| Penyimpangan dari schema.sql: subtotal/total_amount NULLABLE karena tarif belum ditetapkan mitra
| (keputusan pemilik: pengaturan dikosongkan dulu, total tampil "belum ditetapkan").
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('order_status', ['NEW_ORDER', 'WAITING_PAYMENT', 'PAID', 'PROCESSING', 'READY_FOR_PICKUP', 'CANCELLED', 'COMPLETED'])->default('NEW_ORDER');
            $table->enum('payment_status', ['UNPAID', 'PAID', 'FAILED', 'REFUNDED'])->default('UNPAID');
            $table->enum('pickup_status', ['NOT_PICKED_UP', 'PICKED_UP'])->default('NOT_PICKED_UP');
            $table->enum('rental_status', ['NOT_STARTED', 'RENTING', 'DUE_SOON', 'OVERDUE', 'RETURNED', 'UNDER_INSPECTION', 'COMPLETED'])->default('NOT_STARTED');
            $table->enum('payment_method', ['QRIS', 'VIRTUAL_ACCOUNT', 'COD']);
            $table->decimal('subtotal', 14, 2)->nullable();
            $table->decimal('additional_fees', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->nullable();
            $table->dateTime('customer_terms_accepted_at');
            $table->timestamps();

            $table->index(['order_status', 'payment_status', 'pickup_status', 'rental_status'], 'idx_orders_statuses');
            $table->index('created_at', 'idx_orders_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_orders');
    }
};
