<?php

namespace App\Support;

use App\Exceptions\BookingException;
use App\Models\AuditLog;
use App\Models\InventoryUnit;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RentalOrder;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Pembuatan pesanan sewa (Fase 5C-1). Semua langkah dalam SATU transaksi database;
 * baris unit dikunci (lockForUpdate) sebelum memeriksa bentrok, sehingga dua pelanggan
 * tidak dapat memesan unit dan periode yang sama secara bersamaan.
 * Saat ini hanya COD (pembayaran online menunggu payment gateway).
 */
class BookingService
{
    public static function create(User $user, Product $product, CarbonImmutable $start, CarbonImmutable $end, string $unitCode, string $method, CarbonImmutable $acceptedAt): RentalOrder
    {
        if (! $product->is_active || ! $product->rental_enabled) {
            throw new BookingException('Produk ini tidak disewakan.');
        }
        if ($product->isDigital()) {
            throw new BookingException('Layanan digital belum dapat dipesan lewat sistem ini. Alur aktivasinya menunggu informasi dari mitra.');
        }
        if ($method !== 'COD') {
            throw new BookingException('Pembayaran online belum tersedia. Gunakan COD.');
        }

        $today = Availability::today();
        if ($start < $today) {
            throw new BookingException('Tanggal mulai tidak boleh sebelum hari ini.');
        }
        if ($end <= $start) {
            throw new BookingException('Periode sewa tidak valid.');
        }

        $days = (int) $start->diffInDays($end);
        $min = Setting::intValue('rental.min_days') ?? 1;
        $max = Setting::intValue('rental.max_days');
        if ($days < $min) {
            throw new BookingException("Durasi sewa minimal {$min} hari.");
        }
        if ($max !== null && $days > $max) {
            throw new BookingException("Durasi sewa maksimal {$max} hari.");
        }

        return DB::transaction(function () use ($user, $product, $start, $end, $unitCode, $method, $acceptedAt, $days) {
            // Kunci unit lebih dulu, baru periksa bentrok: transaksi lain menunggu di sini.
            $unit = InventoryUnit::where('product_id', $product->id)
                ->where('unit_code', $unitCode)
                ->where('operational_status', 'ACTIVE')
                ->lockForUpdate()
                ->first();

            if (! $unit) {
                throw new BookingException('Unit tidak ditemukan atau sedang tidak tersedia.');
            }
            if (Availability::conflictFor($unit->id, $start, $end)) {
                throw new BookingException('Unit ini sudah dipesan pada periode tersebut. Pilih unit atau tanggal lain.');
            }

            // Tarif belum ditetapkan → total null ("belum ditetapkan"), bukan angka karangan.
            $rate = $product->rental_daily_rate !== null ? (float) $product->rental_daily_rate : null;
            $subtotal = $rate !== null ? round($rate * $days, 2) : null;

            [$orderNumber, $invoiceNumber] = self::numbers();

            $order = RentalOrder::create([
                'order_number'               => $orderNumber,
                'user_id'                    => $user->id,
                'order_status'               => 'WAITING_PAYMENT', // COD: menunggu uang diterima admin
                'payment_status'             => 'UNPAID',
                'pickup_status'              => 'NOT_PICKED_UP',
                'rental_status'              => 'NOT_STARTED',
                'payment_method'             => $method,
                'subtotal'                   => $subtotal,
                'additional_fees'            => 0,
                'total_amount'               => $subtotal,
                'customer_terms_accepted_at' => $acceptedAt->utc(),
            ]);

            $item = $order->items()->create([
                'product_id'            => $product->id,
                'unit_id'               => $unit->id,
                'product_name_snapshot' => $product->name,
                'unit_code_snapshot'    => $unit->unit_code,
                'start_at'              => $start->utc(),
                'end_at'                => $end->utc(),
                'quantity'              => 1,
                'daily_rate'            => $rate,
                'line_total'            => $subtotal,
            ]);

            Reservation::create([
                'order_item_id'      => $item->id,
                'unit_id'            => $unit->id,
                'start_at'           => $start->utc(),
                'end_at'             => $end->utc(),
                'reservation_status' => 'CONFIRMED', // COD: unit langsung dipesan, tidak ada batas waktu bayar online
            ]);

            $order->invoice()->create([
                'invoice_number'    => $invoiceNumber,
                'qr_token_hash'     => InvoiceToken::hash(InvoiceToken::token($invoiceNumber)),
                'issued_at'         => now(),
                'document_snapshot' => [
                    'customer'        => ['name' => $user->name, 'email' => $user->email],
                    'product'         => $product->name,
                    'unit_code'       => $unit->unit_code,
                    'start_date'      => $start->format('Y-m-d'),
                    'end_date'        => $end->format('Y-m-d'),
                    'days'            => $days,
                    'daily_rate'      => $rate,
                    'total_amount'    => $subtotal,
                    'payment_method'  => $method,
                    'pickup_address'  => Setting::value('contact.pickup_address'),
                    'pickup_hours'    => Setting::value('contact.pickup_hours'),
                    'timezone'        => Availability::TZ,
                ],
            ]);

            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $method,
                'amount'         => $subtotal,
                'payment_status' => 'UNPAID',
            ]);

            AuditLog::record($user->id, 'rental_order', $order->id, 'created', null, [
                'invoice' => $invoiceNumber, 'unit' => $unit->unit_code, 'start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d'),
            ]);

            return $order->load(['items', 'invoice', 'user']);
        });
    }

    /** [nomor pesanan, nomor invoice] unik: ORD-ymd-XXXXXX / INV-ymd-XXXXXX (tanggal WITA). */
    private static function numbers(): array
    {
        $ymd = CarbonImmutable::now(Availability::TZ)->format('ymd');
        do {
            $suffix = strtoupper(Str::random(6));
            $order = "ORD-{$ymd}-{$suffix}";
            $invoice = "INV-{$ymd}-{$suffix}";
        } while (RentalOrder::where('order_number', $order)->exists() || \App\Models\Invoice::where('invoice_number', $invoice)->exists());

        return [$order, $invoice];
    }
}
