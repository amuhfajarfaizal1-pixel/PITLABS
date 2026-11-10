<?php

namespace Database\Seeders;

use App\Models\InventoryUnit;
use App\Models\Product;
use Illuminate\Database\Seeder;

/* Unit CONTOH (3 per perangkat fisik). Ganti dengan unit nyata dari halaman Inventaris admin. */
class InventoryUnitSeeder extends Seeder
{
    private const PREFIX = [
        'laptop' => 'LPT', 'raspberry-pi-5' => 'RPI', 'jetson-orin-nano' => 'JON',
        'drone' => 'DRN', 'kamera-mirrorless' => 'KMR',
    ];

    public function run(): void
    {
        foreach (self::PREFIX as $slug => $prefix) {
            $product = Product::where('slug', $slug)->first();
            if (! $product) {
                continue;
            }
            foreach (range(1, 3) as $n) {
                InventoryUnit::firstOrCreate(
                    ['unit_code' => sprintf('%s-%03d', $prefix, $n)],
                    ['product_id' => $product->id, 'notes' => 'Unit contoh, ganti dengan data nyata.']
                );
            }
        }
    }
}
