<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Akun admin dibuat manual, tidak otomatis. Jalankan: php artisan test */
class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    private function options(array $over = []): array
    {
        return $over + ['--name' => 'Admin Uji', '--email' => 'Admin@Contoh.Test', '--password' => 'kata-sandi-aman-123'];
    }

    public function test_seed_bawaan_tidak_membuat_akun_pengguna(): void
    {
        $this->seed(); // DatabaseSeeder: kategori, produk, unit

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('products', 7);
    }

    public function test_perintah_membuat_admin_aktif_dengan_kata_sandi_ter_hash(): void
    {
        $this->artisan('pitlabs:create-admin', $this->options())->assertExitCode(0);

        $user = User::where('email', 'admin@contoh.test')->firstOrFail(); // email dinormalkan ke huruf kecil
        $this->assertSame('ADMIN', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('kata-sandi-aman-123', $user->password));
    }

    public function test_perintah_menolak_kata_sandi_pendek_dan_email_tidak_valid(): void
    {
        $this->artisan('pitlabs:create-admin', $this->options(['--password' => 'pendek']))->assertExitCode(1);
        $this->artisan('pitlabs:create-admin', $this->options(['--email' => 'bukan-email']))->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_email_ganda_ditolak_kecuali_reset_dan_reset_mengganti_kata_sandi(): void
    {
        $this->artisan('pitlabs:create-admin', $this->options())->assertExitCode(0);

        $this->artisan('pitlabs:create-admin', $this->options(['--password' => 'kata-sandi-baru-456']))->assertExitCode(1);
        $this->assertTrue(Hash::check('kata-sandi-aman-123', User::firstOrFail()->password));

        $this->artisan('pitlabs:create-admin', $this->options(['--password' => 'kata-sandi-baru-456', '--reset' => true]))->assertExitCode(0);
        $user = User::firstOrFail();
        $this->assertTrue(Hash::check('kata-sandi-baru-456', $user->password));
        $this->assertFalse(Hash::check('kata-sandi-aman-123', $user->password));
        $this->assertSame(1, User::count());
    }

    public function test_pelanggan_tidak_menjadi_admin_tanpa_opsi_promote(): void
    {
        User::unguarded(fn () => User::create(['name' => 'Pelanggan', 'email' => 'admin@contoh.test', 'password' => 'kata-sandi-aman-123', 'role' => 'CUSTOMER', 'is_active' => true]));

        $this->artisan('pitlabs:create-admin', $this->options(['--reset' => true]))->assertExitCode(1);
        $this->assertSame('CUSTOMER', User::firstOrFail()->role);

        $this->artisan('pitlabs:create-admin', $this->options(['--reset' => true, '--promote' => true]))->assertExitCode(0);
        $this->assertSame('ADMIN', User::firstOrFail()->role);
    }

    public function test_seeder_manual_membaca_konfigurasi_dan_menolak_konfigurasi_kosong(): void
    {
        config(['pitlabs.admin_seed' => ['name' => 'Admin Seeder', 'email' => null, 'password' => null]]);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseCount('users', 0);

        config(['pitlabs.admin_seed' => ['name' => 'Admin Seeder', 'email' => 'seeder@contoh.test', 'password' => 'kata-sandi-aman-123']]);
        $this->seed(AdminSeeder::class);

        $this->assertSame('ADMIN', User::where('email', 'seeder@contoh.test')->firstOrFail()->role);
    }
}
