<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Pengaturan admin (Fase 5C-1). */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function pengguna(string $role): User
    {
        return User::unguarded(fn () => User::create([
            'name' => 'Uji', 'email' => strtolower($role).'@contoh.test', 'password' => 'kata-sandi-aman-123', 'role' => $role, 'is_active' => true,
        ]));
    }

    public function test_hanya_admin_dapat_membuka_dan_mengubah_pengaturan(): void
    {
        $this->get('/admin/pengaturan')->assertRedirect('/masuk');
        $this->actingAs($this->pengguna('CUSTOMER'))->get('/admin/pengaturan')->assertForbidden();
        $this->actingAs($this->pengguna('CUSTOMER'))->put('/admin/pengaturan', ['min_days' => 2])->assertForbidden();
        $this->assertNull(Setting::value('rental.min_days'));
    }

    public function test_admin_menyimpan_pengaturan_dan_nilainya_tampil_di_situs(): void
    {
        $admin = $this->pengguna('ADMIN');
        $this->actingAs($admin)->put('/admin/pengaturan', [
            'min_days' => 2, 'max_days' => 14, 'pickup_address' => 'Jl. Contoh No. 1, Makassar', 'pickup_hours' => 'Senin–Jumat 09.00–17.00 WITA',
        ])->assertRedirect();

        $this->assertSame('2', Setting::value('rental.min_days'));
        $this->assertSame('14', Setting::value('rental.max_days'));
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'settings', 'action' => 'updated']);

        $this->get('/')->assertSee('Jl. Contoh No. 1, Makassar')->assertSee('Minimum 2 hari, maksimum 14 hari');
    }

    public function test_maksimum_tidak_boleh_lebih_kecil_dari_minimum_tetapi_maksimum_saja_boleh(): void
    {
        $admin = $this->pengguna('ADMIN');

        $this->actingAs($admin)->put('/admin/pengaturan', ['min_days' => 5, 'max_days' => 2])->assertSessionHasErrors('max_days');
        $this->actingAs($admin)->put('/admin/pengaturan', ['max_days' => 7])->assertSessionHasNoErrors();
        $this->assertSame('7', Setting::value('rental.max_days'));
    }

    public function test_nilai_kosong_menghapus_pengaturan_dan_kembali_ke_belum_tersedia(): void
    {
        $admin = $this->pengguna('ADMIN');
        Setting::put('contact.pickup_address', 'Alamat lama');

        $this->actingAs($admin)->put('/admin/pengaturan', ['pickup_address' => ''])->assertRedirect();
        $this->assertNull(Setting::value('contact.pickup_address'));
    }
}
