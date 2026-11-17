<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: payments. amount nullable selama tarif belum ditetapkan. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('rental_orders');
            $table->string('provider', 80)->nullable();
            $table->string('provider_reference', 190)->nullable();
            $table->enum('payment_method', ['QRIS', 'VIRTUAL_ACCOUNT', 'COD']);
            $table->decimal('amount', 14, 2)->nullable();
            $table->char('currency', 3)->default('IDR');
            $table->enum('payment_status', ['UNPAID', 'PAID', 'FAILED', 'REFUNDED'])->default('UNPAID');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->dateTime('verified_at')->nullable();
            $table->json('raw_event')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_reference'], 'uq_provider_reference');
            $table->index(['payment_status', 'created_at'], 'idx_payments_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
