<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder katalog lama dinonaktifkan. Produk resmi harus dimasukkan admin dari data mitra.
 * Class dipertahankan agar pemanggilan lama tidak menyebabkan data contoh terbuat.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('ProductSeeder dinonaktifkan: masukkan produk berdasarkan data resmi mitra melalui panel admin. Tidak ada produk contoh yang dibuat.');
    }
}
