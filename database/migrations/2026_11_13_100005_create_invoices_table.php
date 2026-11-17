<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: invoices. qr_token_hash = SHA-256 dari token QR; token asli tidak disimpan. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('rental_orders');
            $table->string('invoice_number', 50)->unique();
            $table->char('qr_token_hash', 64)->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->json('document_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
