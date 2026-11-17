<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder pengguna lama dinonaktifkan agar tidak membuat akun demo atau admin otomatis.
 * Buat akun admin secara eksplisit memakai `php artisan pitlabs:create-admin`.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('UserSeeder dinonaktifkan: tidak ada akun contoh/admin yang dibuat otomatis. Gunakan perintah pitlabs:create-admin untuk admin.');
    }
}
