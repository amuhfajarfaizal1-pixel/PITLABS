<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* docs/DATABASE.md: audit_logs. Jejak perubahan penting (pesanan, pembayaran, pengambilan, pengaturan). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users');
            $table->string('entity_type', 80);
            $table->unsignedBigInteger('entity_id');
            $table->string('action', 100);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id', 'created_at'], 'idx_audit_entity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
