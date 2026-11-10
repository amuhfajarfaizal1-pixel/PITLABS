<?php

namespace App\Support;

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

    /** Awalan kode unit contoh per produk fisik. */
    private const PREFIX = [
        'laptop'            => 'LPT',
        'raspberry-pi-5'    => 'RPI',
        'jetson-orin-nano'  => 'JON',
        'drone'             => 'DRN',
        'kamera-mirrorless' => 'KMR',
    ];

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(self::TZ)->startOfDay();
    }

    /** Jadwal booking contoh per unit: [mulai, akhir) relatif terhadap hari ini. */
    private static function bookings(string $slug): array
    {
        $t = self::today();

        // Drone sengaja penuh agar state "tidak tersedia" bisa dicoba.
        if ($slug === 'drone') {
            return [
                '001' => [[$t->subDay(), $t->addDays(10)]],
                '002' => [[$t->subDays(2), $t->addDays(8)]],
                '003' => [[$t->subDay(), $t->addDays(12)]],
            ];
        }

        return [
            '001' => [[$t->addDays(2), $t->addDays(5)]],
            '002' => [[$t->subDay(), $t->addDay()]],
            '003' => [],
        ];
    }

    /** Daftar unit + status hari ini. null jika produk bukan perangkat fisik. */
    public static function unitsFor(string $slug): ?array
    {
        if (! isset(self::PREFIX[$slug])) {
            return null;
        }

        $today = self::today();
        $units = [];

        foreach (self::bookings($slug) as $no => $periods) {
            $status = 'AVAILABLE';
            foreach ($periods as [$s, $e]) {
                if ($s <= $today && $e > $today) {
                    $status = 'RENTED';
                    break;
                }
                if ($s > $today && $s <= $today->addDays(7)) {
                    $status = 'RESERVED';
                }
            }
            $units[] = ['code' => self::PREFIX[$slug].'-'.$no, 'status' => $status];
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
        if (! isset(self::PREFIX[$slug])) {
            return null;
        }

        $result = [];
        foreach (self::bookings($slug) as $no => $periods) {
            $conflict = null;
            foreach ($periods as [$s, $e]) {
                if ($start < $e && $end > $s) {
                    $conflict = [$s, $e];
                    break;
                }
            }
            $result[] = [
                'code'     => self::PREFIX[$slug].'-'.$no,
                'free'     => $conflict === null,
                'conflict' => $conflict,
            ];
        }

        return $result;
    }
}
