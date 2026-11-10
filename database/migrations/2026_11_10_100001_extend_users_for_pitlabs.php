<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Menambah kolom PITLABS ke tabel users bawaan Laravel (docs/DATABASE.md: users).
| Catatan: nama kolom kata sandi tetap `password` (konvensi Laravel), bukan `password_hash`.
| `password` dibuat nullable agar akun Google (langkah 5B) tidak wajib punya kata sandi.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('role', ['CUSTOMER', 'ADMIN'])->default('CUSTOMER')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('google_id', 64)->nullable()->unique()->after('is_active');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['phone', 'role', 'is_active', 'google_id']);
        });
    }
};
