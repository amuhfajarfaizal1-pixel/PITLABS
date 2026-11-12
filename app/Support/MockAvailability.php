<?php

namespace App\Support;

use App\Models\Product;
use Carbon\CarbonImmutable;

/**
 * KETERSEDIAAN SIMULASI (Fase 4) — jadwal booking contoh, bukan data nyata.
 * Memakai aturan bentrok dari BUSINESS_RULES.md §2:
 *     bentrok jika  new_start < existing_end  DAN  new_end > existing_start
 * Fase 5: ganti dengan query ke tabel booking/reservasi di backend.
 */
class MockAvailability
{
    public const TZ = 'Asia/Makassar';

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(self::TZ)->startOfDay();
    }

    /** Kode unit AKTIF produk fisik dari database; null jika produk tidak ada atau digital. */
    private static function codes(string $slug): ?array
    {
        $product = Product::where('slug', $slug)->first();
        if (! $product || $product->isDigital()) {
            return null;
        }

        return $product->units()->where('operational_status', 'ACTIVE')->orderBy('unit_code')->pluck('unit_code')->all();
    }

    /** Jadwal booking CONTOH untuk unit ke-$i (0-based). Fase 5C: ganti dengan tabel booking. */
    private static function schedule(string $slug, int $i): array
    {
        $t = self::today();

        if ($slug === 'drone') { // sengaja penuh agar state "tidak tersedia" bisa dicoba
            return [[$t->subDays(2 - ($i % 2)), $t->addDays(8 + $i)]];
        }

        return match ($i) {
            0 => [[$t->addDays(2), $t->addDays(5)]],
            1 => [[$t->subDay(), $t->addDay()]],
            default => [],
        };
    }

    /** Daftar unit + status hari ini. null jika produk bukan perangkat fisik. */
    public static function unitsFor(string $slug): ?array
    {
        $codes = self::codes($slug);
        if ($codes === null) {
            return null;
        }

        $today = self::today();
        $units = [];
        foreach ($codes as $i => $code) {
            $status = 'AVAILABLE';
            foreach (self::schedule($slug, $i) as [$s, $e]) {
                if ($s <= $today && $e > $today) {
                    $status = 'RENTED';
                    break;
                }
                if ($s > $today && $s <= $today->addDays(7)) {
                    $status = 'RESERVED';
                }
            }
            $units[] = ['code' => $code, 'status' => $status];
        }

        return $units;
    }

    public static function availableNow(string $slug): int
    {
        $units = self::unitsFor($slug) ?? [];

        return count(array_filter($units, fn ($u) => $u['status'] !== 'RENTED'));
    }

    /** Cek bentrok untuk periode [$start, $end). */
    public static function check(string $slug, CarbonImmutable $start, CarbonImmutable $end): ?array
    {
        $codes = self::codes($slug);
        if ($codes === null) {
            return null;
        }

        $result = [];
        foreach ($codes as $i => $code) {
            $conflict = null;
            foreach (self::schedule($slug, $i) as [$s, $e]) {
                if ($start < $e && $end > $s) {
                    $conflict = [$s, $e];
                    break;
                }
            }
            $result[] = ['code' => $code, 'free' => $conflict === null, 'conflict' => $conflict];
        }

        return $result;
    }
}
