<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/* Dua kelompok katalog resmi: perangkat fisik dan layanan digital. */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['Perangkat fisik', 'perangkat-fisik'], ['Layanan digital', 'layanan-digital']] as [$name, $slug]) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
        }
    }
}
