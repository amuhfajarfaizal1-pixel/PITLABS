<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: reservations. Dasar pencegahan bentrok jadwal: new_start < existing_end AND new_end > existing_start. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('rental_order_items');
            $table->foreignId('unit_id')->nullable()->constrained('inventory_units');
            $table->string('resource_key', 120)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->enum('reservation_status', ['HOLD', 'CONFIRMED', 'CANCELLED', 'EXPIRED', 'COMPLETED'])->default('HOLD');
            $table->dateTime('hold_expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['unit_id', 'reservation_status', 'start_at', 'end_at'], 'idx_reservation_unit_dates');
            $table->index(['reservation_status', 'hold_expires_at'], 'idx_reservation_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
