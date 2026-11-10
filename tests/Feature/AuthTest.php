<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian autentikasi dan otorisasi (Fase 5A). Jalankan: php artisan test
 * Memakai database pengujian (SQLite in-memory dari phpunit.xml); tidak menyentuh MySQL.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'CUSTOMER', bool $active = true, string $password = 'kata-sandi-aman-123'): User
    {
        return User::unguarded(fn () => User::create([
            'name' => 'Uji Coba', 'email' => strtolower($role).'@contoh.test', 'password' => $password,
            'role' => $role, 'is_active' => $active,
        ]));
    }

    public function test_tamu_diarahkan_ke_login_saat_membuka_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/masuk');
        $this->get('/admin')->assertRedirect('/masuk');
    }

    public function test_registrasi_membuat_pelanggan_dengan_kata_sandi_ter_hash(): void
    {
        $this->post('/daftar', [
            'name' => 'Pelanggan Baru', 'email' => 'baru@contoh.test', 'phone' => '081234567890',
            'password' => 'kata-sandi-aman-123', 'password_confirmation' => 'kata-sandi-aman-123',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', 'baru@contoh.test')->firstOrFail();
        $this->assertSame('CUSTOMER', $user->role);
        $this->assertNotSame('kata-sandi-aman-123', $user->password);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registrasi_tidak_bisa_menyisipkan_role_admin(): void
    {
        $this->post('/daftar', [
            'name' => 'Penyusup', 'email' => 'x@contoh.test', 'role' => 'ADMIN', 'is_active' => 0,
            'password' => 'kata-sandi-aman-123', 'password_confirmation' => 'kata-sandi-aman-123',
        ]);

        $user = User::where('email', 'x@contoh.test')->firstOrFail();
        $this->assertSame('CUSTOMER', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_registrasi_menolak_email_ganda_dan_kata_sandi_pendek(): void
    {
        $this->user();
        $this->from('/daftar')->post('/daftar', [
            'name' => 'Dobel', 'email' => 'customer@contoh.test', 'password' => 'pendek', 'password_confirmation' => 'pendek',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_berhasil_mengarah_sesuai_role(): void
    {
        $this->user('CUSTOMER');
        $this->post('/masuk', ['email' => 'customer@contoh.test', 'password' => 'kata-sandi-aman-123'])->assertRedirect('/dashboard');
        $this->post('/keluar');

        $this->user('ADMIN');
        $this->post('/masuk', ['email' => 'admin@contoh.test', 'password' => 'kata-sandi-aman-123'])->assertRedirect('/admin');
    }

    public function test_login_gagal_dengan_pesan_umum(): void
    {
        $this->user();
        $this->from('/masuk')->post('/masuk', ['email' => 'customer@contoh.test', 'password' => 'salah-besar-12'])
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi salah.']);
        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_bisa_masuk(): void
    {
        $this->user('CUSTOMER', false);
        $this->from('/masuk')->post('/masuk', ['email' => 'customer@contoh.test', 'password' => 'kata-sandi-aman-123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_pelanggan_tidak_boleh_membuka_area_admin(): void
    {
        $this->actingAs($this->user('CUSTOMER'))->get('/admin')->assertForbidden();
        $this->actingAs($this->user('ADMIN'))->get('/admin')->assertOk();
    }

    public function test_tautan_kembali_eksternal_diabaikan(): void
    {
        $this->user();
        $this->post('/masuk', ['email' => 'customer@contoh.test', 'password' => 'kata-sandi-aman-123', 'kembali' => 'https://jahat.example/'])
            ->assertRedirect('/dashboard');
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $this->actingAs($this->user())->post('/keluar')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_dapat_menonaktifkan_pelanggan_dan_pelanggan_tidak_bisa(): void
    {
        $customer = $this->user('CUSTOMER');

        $this->actingAs($this->user('ADMIN'))->post("/admin/pelanggan/{$customer->id}/status")->assertRedirect();
        $this->assertFalse($customer->fresh()->is_active);

        $this->actingAs($this->user('CUSTOMER', true))->post("/admin/pelanggan/{$customer->id}/status")->assertForbidden();
    }
}
