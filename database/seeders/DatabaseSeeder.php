<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya kategori taksonomi dasar. Produk, unit, pelanggan, transaksi, dan admin
        // tidak dibuat otomatis: seluruh data operasional harus berasal dari mitra/pengguna.
        $this->call([CategorySeeder::class]);
    }
}
