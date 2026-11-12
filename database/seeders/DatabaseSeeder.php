<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            InventoryUnitSeeder::class,
            // Akun admin TIDAK dibuat otomatis: gunakan `php artisan pitlabs:create-admin`.
        ]);
    }
}
