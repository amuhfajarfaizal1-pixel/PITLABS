<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * DATA CONTOH untuk dashboard admin dan pengguna (Fase 4B) — hanya visualisasi.
 * Semua nama, invoice, dan nominal di sini FIKTIF dan bukan tarif resmi.
 * Fase 5: ganti dengan query database; bentuk array dipertahankan semirip mungkin.
 */
class MockData
{
    public static function rupiah(?int $n): string
    {
        return $n === null ? '' : 'Rp '.number_format($n, 0, ',', '.');
    }

    private static function d(int $offset): CarbonImmutable
    {
        return MockAvailability::today()->addDays($offset);
    }

    private static function label(CarbonImmutable $d): string
    {
        return $d->locale('id')->translatedFormat('j M');
    }

    /** Pesanan rental contoh. Empat status SELALU terpisah. */
    public static function orders(): array
    {
        //  id, customer, product, unit, start, end, method, total, order, payment, pickup, rental
        $rows = [
            [1,  'Andi Pratama',    'Jetson Orin Nano', 'JON-002', -2,  3, 'QRIS', 450000, 'PROCESSING',       'PAID',   'PICKED_UP',     'RENTING'],
            [2,  'Siti Rahma',      'Raspberry Pi 5',   'RPI-002', -1,  1, 'VA',   180000, 'PROCESSING',       'PAID',   'PICKED_UP',     'DUE_SOON'],
            [3,  'Budi Santoso',    'Laptop',           'LPT-002', -6, -1, 'COD',  600000, 'PROCESSING',       'PAID',   'PICKED_UP',     'OVERDUE'],
            [4,  'Dewi Lestari',    'Kamera Mirrorless','KMR-001',  2,  5, 'QRIS', 525000, 'READY_FOR_PICKUP', 'PAID',   'NOT_PICKED_UP', 'NOT_STARTED'],
            [5,  'Rizky Maulana',   'Drone',            'DRN-001',  1,  4, 'COD',  750000, 'WAITING_PAYMENT',  'UNPAID', 'NOT_PICKED_UP', 'NOT_STARTED'],
            [6,  'Nadia Putri',     'Jetson Orin Nano', 'JON-001',  2,  5, 'QRIS', 450000, 'NEW_ORDER',        'UNPAID', 'NOT_PICKED_UP', 'NOT_STARTED'],
            [7,  'Maya Anggraini',  'Laptop',           'LPT-003',-10, -7, 'VA',   450000, 'PROCESSING',       'PAID',   'PICKED_UP',     'UNDER_INSPECTION'],
            [8,  'Yusuf Hakim',     'Kamera Mirrorless','KMR-002',-14,-11, 'QRIS', 375000, 'COMPLETED',        'PAID',   'PICKED_UP',     'COMPLETED'],
            [9,  'Lina Marlina',    'Raspberry Pi 5',   'RPI-001',-20,-17, 'COD',  180000, 'COMPLETED',        'PAID',   'PICKED_UP',     'COMPLETED'],
            [10, 'Ilham Syahputra', 'Jetson Orin Nano', 'JON-003',  3,  6, 'QRIS', 450000, 'CANCELLED',        'FAILED', 'NOT_PICKED_UP', 'NOT_STARTED'],
            [11, 'Rani Oktaviani',  'Drone',            'DRN-002', -3,  2, 'VA',   900000, 'PROCESSING',       'PAID',   'PICKED_UP',     'RENTING'],
            [12, 'Hendra Wijaya',   'Laptop',           'LPT-001',  4,  8, 'COD',  480000, 'NEW_ORDER',        'UNPAID', 'NOT_PICKED_UP', 'NOT_STARTED'],
        ];

        return array_map(function ($r) {
            [$id, $cust, $prod, $unit, $s, $e, $method, $total, $order, $pay, $pick, $rent] = $r;
            $start = self::d($s);
            $end = self::d($e);

            return [
                'id'        => $id,
                'invoice'   => sprintf('INV-CONTOH-%03d', $id),
                'customer'  => $cust,
                'product'   => $prod,
                'unit'      => $unit,
                'start'     => $start,
                'end'       => $end,
                'period'    => self::label($start).' – '.self::label($end),
                'method'    => $method === 'VA' ? 'Virtual account' : $method,
                'total'     => $total,
                'order'     => $order,
                'payment'   => $pay,
                'pickup'    => $pick,
                'rental'    => $rent,
                'due_in'    => (int) MockAvailability::today()->diffInDays($end, false),
            ];
        }, $rows);
    }

    /** Pesanan milik "Pelanggan Demo" (subset contoh). */
    public static function forCustomer(): array
    {
        return array_map(
            fn ($o) => ['customer' => 'Pelanggan Demo'] + $o,
            self::where(fn ($o) => in_array($o['id'], [1, 4, 8, 9], true))
        );
    }

    public static function order(string $invoice): ?array
    {
        foreach (self::orders() as $o) {
            if (strcasecmp($o['invoice'], $invoice) === 0) {
                return $o;
            }
        }

        return null;
    }

    public static function where(callable $fn): array
    {
        return array_values(array_filter(self::orders(), $fn));
    }

    public static function newOrders(): array
    {
        return self::where(fn ($o) => in_array($o['order'], ['NEW_ORDER', 'WAITING_PAYMENT'], true));
    }

    public static function ongoing(): array
    {
        return self::where(fn ($o) => in_array($o['rental'], ['RENTING', 'DUE_SOON', 'OVERDUE'], true));
    }

    public static function unpaid(): array
    {
        return self::where(fn ($o) => $o['payment'] === 'UNPAID' && $o['order'] !== 'CANCELLED');
    }

    public static function readyForPickup(): array
    {
        return self::where(fn ($o) => $o['payment'] === 'PAID' && $o['pickup'] === 'NOT_PICKED_UP' && $o['order'] !== 'CANCELLED');
    }

    public static function underInspection(): array
    {
        return self::where(fn ($o) => $o['rental'] === 'UNDER_INSPECTION');
    }

    /** Ringkasan KPI admin (FR-15). */
    public static function kpis(): array
    {
        $units = [];
        foreach (['laptop', 'raspberry-pi-5', 'jetson-orin-nano', 'drone', 'kamera-mirrorless'] as $slug) {
            $units = array_merge($units, MockAvailability::unitsFor($slug) ?? []);
        }
        $count = fn (string $st) => count(array_filter($units, fn ($u) => $u['status'] === $st));

        return [
            'new_orders'      => count(self::newOrders()),
            'ongoing'         => count(self::ongoing()),
            'units_available' => $count('AVAILABLE'),
            'units_rented'    => $count('RENTED'),
            'units_reserved'  => $count('RESERVED'),
            'unpaid'          => count(self::unpaid()),
            'paid_not_picked' => count(self::readyForPickup()),
            'due_soon'        => count(self::where(fn ($o) => $o['rental'] === 'DUE_SOON')),
            'overdue'         => count(self::where(fn ($o) => $o['rental'] === 'OVERDUE')),
        ];
    }

    /** Pendapatan rental 6 bulan terakhir (FIKTIF). */
    public static function revenueByMonth(): array
    {
        $now = MockAvailability::today();
        $vals = [3200000, 4100000, 3700000, 5200000, 4800000, 6100000];
        $out = [];
        foreach ($vals as $i => $v) {
            $m = $now->subMonthsNoOverflow(5 - $i);
            $out[$m->locale('id')->translatedFormat('M')] = $v;
        }

        return $out;
    }

    /** Daftar pelanggan contoh (tab Pelanggan admin). */
    public static function customers(): array
    {
        $rows = [
            ['Andi Pratama', 'andi.pratama@contoh.test', '081234560001', -40, true],
            ['Siti Rahma', 'siti.rahma@contoh.test', '081234560002', -32, true],
            ['Budi Santoso', 'budi.santoso@contoh.test', '081234560003', -25, true],
            ['Dewi Lestari', 'dewi.lestari@contoh.test', '081234560004', -18, true],
            ['Rizky Maulana', 'rizky.maulana@contoh.test', '081234560005', -9, true],
            ['Nadia Putri', 'nadia.putri@contoh.test', null, -3, true],
            ['Ilham Syahputra', 'ilham.syahputra@contoh.test', '081234560007', -2, false],
        ];

        return array_map(fn ($r, $i) => [
            'id'      => $i + 1,
            'stack'   => [$r[0], $r[1]],
            'phone'   => $r[2],
            'joined'  => self::label(self::d($r[3])),
            'account' => $r[4] ? 'ACTIVE' : 'INACTIVE',
        ], $rows, array_keys($rows));
    }

    /** Antrean rental contoh: muncul saat unit bentrok dengan jadwal lain. */
    public static function waitlist(): array
    {
        $rows = [
            ['Pelanggan Demo', 'Drone', 4, 6, 3, 'WAITING'],
            ['Nadia Putri', 'Jetson Orin Nano', 2, 5, 3, 'NOTIFIED'],
            ['Rizky Maulana', 'Laptop', -12, -9, 3, 'FULFILLED'],
            ['Hendra Wijaya', 'Kamera Mirrorless', 1, 3, 2, 'CANCELLED'],
        ];

        return array_map(fn ($r, $i) => [
            'id' => $i + 1, 'customer' => $r[0], 'product' => $r[1],
            'period' => self::label(self::d($r[2])).' – '.self::label(self::d($r[3])),
            'days' => $r[4].' hari', 'status' => $r[5],
        ], $rows, array_keys($rows));
    }

    /** Notifikasi / pengingat dashboard (BUSINESS_RULES §10). */
    public static function reminders(): array
    {
        return [
            ['icon' => 'bi-alarm', 'tone' => 'warning', 'title' => 'Mendekati batas pengembalian', 'text' => 'Raspberry Pi 5 (RPI-002) jatuh tempo besok.', 'time' => 'H-1'],
            ['icon' => 'bi-exclamation-octagon', 'tone' => 'danger', 'title' => 'Terlambat dikembalikan', 'text' => 'Laptop (LPT-002) melewati batas sewa.', 'time' => 'Overdue'],
            ['icon' => 'bi-box-seam', 'tone' => 'info', 'title' => 'Siap diambil', 'text' => 'Kamera Mirrorless (KMR-001) siap diambil di lokasi admin.', 'time' => 'Hari ini'],
        ];
    }
}
