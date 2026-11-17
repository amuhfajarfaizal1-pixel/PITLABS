<?php

namespace App\Support;

use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\Reservation;
use Carbon\CarbonImmutable;

/**
 * Ketersediaan unit dari DATABASE (reservasi). Zona waktu bisnis: Asia/Makassar (WITA);
 * waktu disimpan UTC. Aturan bentrok (BUSINESS_RULES §2):
 *     bentrok jika  new_start < existing_end  DAN  new_end > existing_start
 * Reservasi yang dihitung: CONFIRMED, atau HOLD yang belum kedaluwarsa.
 */
class Availability
{
    public const TZ = 'Asia/Makassar';

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(self::TZ)->startOfDay();
    }

    /** "2026-11-20" → awal hari tersebut di WITA. Null jika format tidak valid. */
    public static function startOf(string $ymd): ?CarbonImmutable
    {
        $d = CarbonImmutable::createFromFormat('!Y-m-d', $ymd, self::TZ);

        return $d && $d->format('Y-m-d') === $ymd ? $d : null;
    }

    private static function wita(\DateTimeInterface $d): CarbonImmutable
    {
        return CarbonImmutable::createFromInterface($d)->setTimezone(self::TZ);
    }

    /** Unit AKTIF produk fisik (urut kode), atau null bila produk tidak ada/digital. */
    private static function activeUnits(string $slug)
    {
        $product = Product::where('slug', $slug)->first();
        if (! $product || $product->isDigital()) {
            return null;
        }

        return $product->units()->where('operational_status', 'ACTIVE')->orderBy('unit_code')->get();
    }

    /** Query reservasi yang masih memblokir unit. */
    private static function blocking()
    {
        return Reservation::query()
            ->whereIn('reservation_status', ['HOLD', 'CONFIRMED'])
            ->where(function ($q) {
                $q->where('reservation_status', 'CONFIRMED')
                    ->orWhereNull('hold_expires_at')
                    ->orWhere('hold_expires_at', '>', now());
            });
    }

    /** Reservasi pertama yang bentrok dengan periode, atau null. */
    public static function conflictFor(int $unitId, CarbonImmutable $start, CarbonImmutable $end): ?Reservation
    {
        return self::blocking()
            ->where('unit_id', $unitId)
            ->where('start_at', '<', $end->utc())
            ->where('end_at', '>', $start->utc())
            ->orderBy('start_at')
            ->first();
    }

    /** Hasil cek per unit untuk periode [start, end). null jika bukan perangkat fisik. */
    public static function check(string $slug, CarbonImmutable $start, CarbonImmutable $end): ?array
    {
        $units = self::activeUnits($slug);
        if ($units === null) {
            return null;
        }

        return $units->map(function (InventoryUnit $u) use ($start, $end) {
            $r = self::conflictFor($u->id, $start, $end);

            return [
                'id'       => $u->id,
                'code'     => $u->unit_code,
                'free'     => $r === null,
                'conflict' => $r ? [self::wita($r->start_at), self::wita($r->end_at)] : null,
            ];
        })->all();
    }

    /** Status unit hari ini: RENTED (sedang dalam masa sewa/pesanan), RESERVED (dipesan ≤ 7 hari ke depan), AVAILABLE. */
    public static function unitsFor(string $slug): ?array
    {
        $units = self::activeUnits($slug);
        if ($units === null) {
            return null;
        }

        $now = CarbonImmutable::now('UTC');
        $soon = $now->addDays(7);

        return $units->map(function (InventoryUnit $u) use ($now, $soon) {
            $status = 'AVAILABLE';
            if (self::blocking()->where('unit_id', $u->id)->where('start_at', '<=', $now)->where('end_at', '>', $now)->exists()) {
                $status = 'RENTED';
            } elseif (self::blocking()->where('unit_id', $u->id)->where('start_at', '>', $now)->where('start_at', '<=', $soon)->exists()) {
                $status = 'RESERVED';
            }

            return ['code' => $u->unit_code, 'status' => $status];
        })->all();
    }

    public static function availableNow(string $slug): int
    {
        return count(array_filter(self::unitsFor($slug) ?? [], fn ($u) => $u['status'] !== 'RENTED'));
    }
}
