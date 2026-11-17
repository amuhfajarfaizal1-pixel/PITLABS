<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Seeder inventaris contoh dinonaktifkan; unit harus berasal dari inventaris mitra. */
class InventoryUnitSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('InventoryUnitSeeder dinonaktifkan: masukkan unit nyata dari inventaris mitra. Tidak ada unit contoh yang dibuat.');
    }
}
