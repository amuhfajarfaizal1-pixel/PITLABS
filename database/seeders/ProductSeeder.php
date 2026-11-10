<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/*
| Tujuh produk resmi PITLABS. Deskripsi, spesifikasi, harga, dan URL Shopee sengaja null:
| datanya belum diberikan mitra (docs/DATA_PRODUK_YANG_DIBUTUHKAN.md).
| sale_enabled/rental_enabled bernilai true sebagai nilai awal agar tombol Beli dan Sewa
| tampil seperti desain; admin mengubahnya per produk.
*/
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $physical = Category::where('slug', 'perangkat-fisik')->value('id');
        $digital = Category::where('slug', 'layanan-digital')->value('id');

        $products = [
            ['Laptop', 'laptop', 'PHYSICAL', $physical],
            ['Raspberry Pi 5', 'raspberry-pi-5', 'PHYSICAL', $physical],
            ['Jetson Orin Nano', 'jetson-orin-nano', 'PHYSICAL', $physical],
            ['Drone', 'drone', 'PHYSICAL', $physical],
            ['Kamera Mirrorless', 'kamera-mirrorless', 'PHYSICAL', $physical],
            ['Google Colab Pro', 'google-colab-pro', 'DIGITAL_SERVICE', $digital],
            ['Google Colab Pro Plus', 'google-colab-pro-plus', 'DIGITAL_SERVICE', $digital],
        ];

        foreach ($products as [$name, $slug, $type, $categoryId]) {
            Product::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'product_type' => $type,
                'category_id' => $categoryId,
                'sale_enabled' => true,
                'rental_enabled' => true,
                'is_active' => true,
            ]);
        }
    }
}
