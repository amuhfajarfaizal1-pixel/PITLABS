<?php

namespace Tests\Feature;

use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\RentalOrder;
use App\Models\Setting;
use App\Models\User;
use App\Support\Availability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Booking, checkout, invoice, dan ketersediaan dari database (Fase 5C-1). Jalankan: php artisan test */
class BookingTest extends TestCase
{
    use RefreshDatabase;

    private function buatPengguna(string $email = 'pelanggan@contoh.test', string $role = 'CUSTOMER'): User
    {
        return User::unguarded(fn () => User::create([
            'name' => 'Penyewa Uji', 'email' => $email, 'password' => 'kata-sandi-aman-123', 'role' => $role, 'is_active' => true,
        ]));
    }

    private function buatProduk(?int $tarif = null, int $jumlahUnit = 2, string $tipe = 'PHYSICAL'): Product
    {
        $p = Product::create([
            'name' => 'Alat Sewa', 'slug' => 'alat-sewa', 'product_type' => $tipe, 'rental_enabled' => true,
            'sale_enabled' => false, 'is_active' => true, 'rental_daily_rate' => $tarif,
        ]);
        if ($tipe === 'PHYSICAL') {
            foreach (range(1, $jumlahUnit) as $n) {
                InventoryUnit::create(['product_id' => $p->id, 'unit_code' => sprintf('ALT-%03d', $n)]);
            }
        }

        return $p;
    }

    private function dataCheckout(array $ubah = [], int $hariDariSekarang = 10): array
    {
        return $ubah + [
            'produk' => 'alat-sewa', 'mulai' => Availability::today()->addDays($hariDariSekarang)->format('Y-m-d'),
            'durasi' => 3, 'satuan' => 'hari', 'unit' => 'ALT-001', 'metode' => 'COD', 'terms_accepted' => '1',
        ];
    }

    public function test_checkout_membuat_pesanan_reservasi_invoice_pembayaran_dan_jejak_audit(): void
    {
        $this->buatProduk();
        $user = $this->buatPengguna();

        $this->actingAs($user)->post('/checkout', $this->dataCheckout())->assertRedirectContains('/invoice/INV-');

        $order = RentalOrder::with(['items', 'invoice', 'payment'])->firstOrFail();
        $this->assertSame('WAITING_PAYMENT', $order->order_status);
        $this->assertSame('UNPAID', $order->payment_status);
        $this->assertSame('NOT_PICKED_UP', $order->pickup_status);
        $this->assertSame('NOT_STARTED', $order->rental_status);
        $this->assertSame('COD', $order->payment_method);
        $this->assertNotNull($order->customer_terms_accepted_at);
        $this->assertSame('ALT-001', $order->items->first()->unit_code_snapshot);
        $this->assertSame(64, strlen($order->invoice->qr_token_hash));
        $this->assertDatabaseHas('reservations', ['unit_id' => $order->items->first()->unit_id, 'reservation_status' => 'CONFIRMED']);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'payment_status' => 'UNPAID']);
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'rental_order', 'entity_id' => $order->id, 'action' => 'created']);
    }

    public function test_total_dihitung_dari_tarif_harian_dikali_jumlah_hari(): void
    {
        $user = $this->buatPengguna();
        $this->buatProduk(150000);
        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['durasi' => 3]));
        $this->assertSame(450000, (int) RentalOrder::firstOrFail()->total_amount);

    }

    public function test_total_kosong_dan_invoice_menampilkan_informasi_belum_tersedia(): void
    {
        $user = $this->buatPengguna();
        $this->buatProduk(null);

        $this->actingAs($user)->post('/checkout', $this->dataCheckout());
        $order = RentalOrder::firstOrFail();
        $this->assertNull($order->total_amount);

        $this->get('/invoice/'.$order->invoice->invoice_number)->assertOk()->assertSee('Informasi belum tersedia');
    }

    public function test_unit_bentrok_ditolak_dan_batas_akhir_bersifat_eksklusif(): void
    {
        $this->buatProduk();
        $a = $this->buatPengguna('a@contoh.test');
        $b = $this->buatPengguna('b@contoh.test');
        $c = $this->buatPengguna('c@contoh.test');

        $this->actingAs($a)->post('/checkout', $this->dataCheckout(['durasi' => 3], 10)); // hari 10–13 (13 eksklusif)
        $this->assertSame(1, RentalOrder::count());

        // tumpang tindih pada unit yang sama → ditolak
        $this->actingAs($b)->from('/checkout')->post('/checkout', $this->dataCheckout(['durasi' => 3], 11))->assertSessionHasErrors('booking');
        $this->assertSame(1, RentalOrder::count());

        // unit lain pada periode yang sama → diterima
        $this->actingAs($b)->post('/checkout', $this->dataCheckout(['durasi' => 3, 'unit' => 'ALT-002'], 11));
        $this->assertSame(2, RentalOrder::count());

        // mulai tepat pada hari pengembalian pesanan pertama → diterima (tidak bentrok)
        $this->actingAs($c)->post('/checkout', $this->dataCheckout(['durasi' => 2], 13));
        $this->assertSame(3, RentalOrder::count());
    }

    public function test_validasi_persetujuan_metode_tanggal_unit_dan_layanan_digital(): void
    {
        $this->buatProduk();
        $user = $this->buatPengguna();

        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['terms_accepted' => null]))->assertSessionHasErrors('terms_accepted');
        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['metode' => 'QRIS']))->assertSessionHasErrors('metode');
        $this->actingAs($user)->post('/checkout', $this->dataCheckout([], -1))->assertSessionHasErrors('booking');
        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['unit' => 'TIDAK-ADA']))->assertSessionHasErrors('booking');

        Product::create(['name' => 'Layanan X', 'slug' => 'layanan-x', 'product_type' => 'DIGITAL_SERVICE', 'rental_enabled' => true, 'is_active' => true]);
        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['produk' => 'layanan-x', 'unit' => 'APA-SAJA']))->assertSessionHasErrors('booking');

        $this->assertSame(0, RentalOrder::count());
    }

    public function test_batas_durasi_mengikuti_pengaturan_admin(): void
    {
        $this->buatProduk();
        $user = $this->buatPengguna();
        Setting::put('rental.min_days', '2');
        Setting::put('rental.max_days', '5');

        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['durasi' => 1]))->assertSessionHasErrors('booking');
        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['durasi' => 6]))->assertSessionHasErrors('booking');
        $this->assertSame(0, RentalOrder::count());

        $this->actingAs($user)->post('/checkout', $this->dataCheckout(['durasi' => 3]));
        $this->assertSame(1, RentalOrder::count());
    }

    public function test_invoice_hanya_dapat_dibuka_pemilik_dan_admin(): void
    {
        $this->buatProduk();
        $owner = $this->buatPengguna('pemilik@contoh.test');
        $other = $this->buatPengguna('lain@contoh.test');
        $admin = $this->buatPengguna('admin@contoh.test', 'ADMIN');

        $this->actingAs($owner)->post('/checkout', $this->dataCheckout());
        $number = RentalOrder::firstOrFail()->invoice->invoice_number;

        $this->actingAs($owner)->get("/invoice/{$number}")->assertOk()->assertSee($number);
        $this->actingAs($admin)->get("/invoice/{$number}")->assertOk();
        $this->actingAs($other)->get("/invoice/{$number}")->assertNotFound();
        $this->get("/invoice/{$number}")->assertRedirect('/masuk');
    }

    public function test_cek_ketersediaan_menampilkan_bentrok_dari_reservasi_database(): void
    {
        $this->buatProduk(null, 1);
        $user = $this->buatPengguna();
        $data = $this->dataCheckout(['durasi' => 3]);
        $this->actingAs($user)->post('/checkout', $data);

        $this->get('/cek-ketersediaan?produk=alat-sewa&mulai='.$data['mulai'].'&durasi=1&satuan=hari')
            ->assertOk()->assertSee('Bentrok jadwal')->assertSee('Semua unit sudah dipesan');
    }

    public function test_riwayat_hanya_memuat_pesanan_milik_sendiri(): void
    {
        $this->buatProduk();
        $a = $this->buatPengguna('a@contoh.test');
        $b = $this->buatPengguna('b@contoh.test');
        $this->actingAs($a)->post('/checkout', $this->dataCheckout());
        $number = RentalOrder::firstOrFail()->invoice->invoice_number;

        $this->actingAs($a)->get('/dashboard/riwayat')->assertOk()->assertSee($number);
        $this->actingAs($b)->get('/dashboard/riwayat')->assertOk()->assertDontSee($number);
    }

    public function test_pembayaran_online_dinonaktifkan_di_halaman_checkout(): void
    {
        $this->buatProduk();
        $user = $this->buatPengguna();
        $d = $this->dataCheckout();

        $this->actingAs($user)->get('/checkout?produk=alat-sewa&mulai='.$d['mulai'].'&durasi=3&satuan=hari')
            ->assertOk()->assertSee('Segera Hadir')->assertSee('COD, bayar saat pengambilan');
    }
}
