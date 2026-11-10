<?php

namespace App\Support;

/**
 * DATA SEMENTARA (Fase 3–4) — hanya untuk visualisasi frontend.
 * Fase 5: ganti dengan model Product (tabel `products`, lihat database/schema.sql).
 * Nama field mengikuti kolom tabel agar penggantian mudah.
 *
 * Yang SENGAJA kosong (null/[]) karena belum ada datanya dan tidak boleh dikarang:
 * description, specifications, usage_guide, sale_price, rental_daily_rate, shopee_url.
 * Daftar data yang dibutuhkan: docs/DATA_PRODUK_YANG_DIBUTUHKAN.md
 *
 * sale_enabled / rental_enabled: nilai demo (semua true) agar kedua tombol
 * terlihat di halaman detail. Nilai sebenarnya ditentukan mitra.
 */
class MockCatalog
{
    private const BASE = [
        'description'       => null,
        'specifications'    => [],
        'usage_guide'       => null,
        'sale_enabled'      => true,
        'rental_enabled'    => true,
        'sale_price'        => null,
        'rental_daily_rate' => null,
        'shopee_url'        => null,
    ];

    public static function all(): array
    {
        $rows = [
            ['slug' => 'laptop',                'name' => 'Laptop',                'type' => 'physical', 'icon' => 'bi-laptop'],
            ['slug' => 'raspberry-pi-5',        'name' => 'Raspberry Pi 5',        'type' => 'physical', 'icon' => 'bi-motherboard'],
            ['slug' => 'jetson-orin-nano',      'name' => 'Jetson Orin Nano',      'type' => 'physical', 'icon' => 'bi-cpu'],
            ['slug' => 'drone',                 'name' => 'Drone',                 'type' => 'physical', 'icon' => 'bi-airplane'],
            ['slug' => 'kamera-mirrorless',     'name' => 'Kamera Mirrorless',     'type' => 'physical', 'icon' => 'bi-camera'],
            ['slug' => 'google-colab-pro',      'name' => 'Google Colab Pro',      'type' => 'digital',  'icon' => 'bi-cloud'],
            ['slug' => 'google-colab-pro-plus', 'name' => 'Google Colab Pro Plus', 'type' => 'digital',  'icon' => 'bi-cloud-plus'],
        ];

        return array_map(fn (array $r) => $r + self::BASE, $rows);
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Pencarian nama + filter jenis + filter "ada unit tersedia hari ini".
     * Layanan digital tidak ikut disaring ketersediaan (slotnya belum ditentukan).
     */
    public static function search(?string $q, ?string $type, bool $onlyAvailable = false): array
    {
        $q = mb_strtolower(trim((string) $q));

        return array_values(array_filter(self::all(), function (array $p) use ($q, $type, $onlyAvailable) {
            if ($q !== '' && ! str_contains(mb_strtolower($p['name']), $q)) {
                return false;
            }
            if ($type && $p['type'] !== $type) {
                return false;
            }
            if ($onlyAvailable && $p['type'] === 'physical' && MockAvailability::availableNow($p['slug']) === 0) {
                return false;
            }

            return true;
        }));
    }
}
