<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: products. Harga dan spesifikasi nullable: data belum diberikan mitra. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 180);
            $table->string('slug', 200)->unique();
            $table->enum('product_type', ['PHYSICAL', 'DIGITAL_SERVICE'])->default('PHYSICAL');
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->text('usage_guide')->nullable();
            $table->boolean('sale_enabled')->default(false);
            $table->boolean('rental_enabled')->default(true);
            $table->decimal('sale_price', 14, 2)->nullable();
            $table->decimal('rental_daily_rate', 14, 2)->nullable();
            $table->string('shopee_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
