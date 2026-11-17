<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: rental_order_items. Waktu disimpan UTC; tampil dan dihitung dalam WITA (Asia/Makassar). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('rental_orders');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('unit_id')->nullable()->constrained('inventory_units');
            $table->string('product_name_snapshot', 180);
            $table->string('unit_code_snapshot', 80)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('daily_rate', 14, 2)->nullable();
            $table->decimal('line_total', 14, 2)->nullable();

            $table->index(['unit_id', 'start_at', 'end_at'], 'idx_order_items_unit_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_order_items');
    }
};
