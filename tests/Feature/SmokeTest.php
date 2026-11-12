<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji asap: setiap halaman penting dapat dirender tanpa error (Blade, route, data).
 * Menangkap kerusakan tampilan yang tidak terlihat dari tes logika.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $role): User
    {
        $user = User::unguarded(fn () => User::create([
            'name' => 'Uji Asap', 'email' => strtolower($role).'@contoh.test', 'password' => 'kata-sandi-aman-123',
            'role' => $role, 'is_active' => true,
        ]));
        $this->actingAs($user);

        return $user;
    }

    private function assertPagesOk(array $paths): void
    {
        foreach ($paths as $path) {
            $this->assertSame(200, $this->get($path)->getStatusCode(), "Halaman {$path} tidak dapat dibuka.");
        }
    }

    public function test_semua_halaman_admin_dapat_dibuka_dengan_tema_admin(): void
    {
        $this->login('ADMIN');

        $this->assertPagesOk([
            '/admin', '/admin/new-orders', '/admin/ongoing-rentals', '/admin/verifikasi-pembayaran', '/admin/qr-scanner',
            '/admin/verifikasi-pengambilan', '/admin/pengembalian', '/admin/pemeriksaan-kondisi', '/admin/denda',
            '/admin/produk', '/admin/produk/baru', '/admin/inventaris', '/admin/inventaris/baru',
            '/admin/pelanggan', '/admin/antrean-rental', '/admin/laporan-keuangan', '/invoice/INV-CONTOH-001',
        ]);

        $this->get('/admin')->assertSee('pit-theme-admin', false)->assertDontSee('pit-theme-user', false);
    }

    public function test_semua_halaman_pelanggan_dapat_dibuka_dengan_tema_pelanggan(): void
    {
        $this->login('CUSTOMER');

        $this->assertPagesOk([
            '/dashboard', '/dashboard/penyewaan-aktif', '/dashboard/riwayat', '/dashboard/antrean',
            '/dashboard/pengembalian', '/dashboard/profil', '/checkout', '/invoice/INV-CONTOH-001',
        ]);

        $this->get('/dashboard')->assertSee('pit-theme-user', false)->assertDontSee('pit-theme-admin', false);
    }

    public function test_halaman_publik_dapat_dibuka_dan_hero_tidak_memuat_kartu_ilustrasi(): void
    {
        $this->assertPagesOk(['/', '/katalog', '/cek-ketersediaan', '/faq', '/panduan', '/tentang-kami', '/artikel', '/asisten-ai', '/masuk', '/daftar', '/sitemap.xml']);

        $this->get('/')
            ->assertSee('pit-hero__inner', false)
            ->assertDontSee('pit-hero-card', false)
            ->assertDontSee('Contoh tampilan dashboard')
            ->assertDontSee('INV-CONTOH-001');
    }
}
