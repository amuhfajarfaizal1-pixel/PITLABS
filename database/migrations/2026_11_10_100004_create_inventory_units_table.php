<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: inventory_units. Setiap unit fisik punya kode unik. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('unit_code', 80)->unique();
            $table->string('serial_number', 120)->nullable();
            $table->enum('condition_status', ['GOOD', 'MINOR_ISSUE', 'MAJOR_ISSUE', 'DAMAGED', 'LOST'])->default('GOOD');
            $table->enum('operational_status', ['ACTIVE', 'MAINTENANCE', 'RETIRED'])->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'operational_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_units');
    }
};
