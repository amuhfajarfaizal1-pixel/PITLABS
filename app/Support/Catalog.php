<?php

namespace App\Support;

use App\Models\Product;

/**
 * Akses katalog publik dari DATABASE (Fase 5B). Hanya produk aktif.
 * Mengembalikan array dengan bentuk yang sama seperti data contoh sebelumnya,
 * sehingga view tidak berubah.
 */
class Catalog
{
    public static function all(): array
    {
        return Product::active()->orderBy('id')->get()->map->toViewArray()->all();
    }

    public static function find(string $slug): ?array
    {
        return Product::active()->where('slug', $slug)->first()?->toViewArray();
    }

    /** Pencarian nama + filter jenis + filter "ada unit tersedia hari ini" (perangkat fisik). */
    public static function search(?string $q, ?string $type, bool $onlyAvailable = false): array
    {
        $query = Product::active()->orderBy('id');

        $q = trim((string) $q);
        if ($q !== '') {
            $query->where('name', 'like', '%'.addcslashes($q, '%_\\').'%');
        }
        if ($type === 'physical') {
            $query->where('product_type', 'PHYSICAL');
        } elseif ($type === 'digital') {
            $query->where('product_type', 'DIGITAL_SERVICE');
        }

        $rows = $query->get()->map->toViewArray()->all();

        if ($onlyAvailable) {
            $rows = array_values(array_filter($rows, fn ($p) => $p['type'] === 'digital' || MockAvailability::availableNow($p['slug']) > 0));
        }

        return $rows;
    }
}
