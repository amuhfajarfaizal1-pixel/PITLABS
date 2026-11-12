<?php

namespace Tests\Feature;

use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** CRUD produk dan unit oleh admin (Fase 5B). Jalankan: php artisan test */
class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    /** Membuat SATU admin per tes. Panggil sekali, simpan di variabel, lalu pakai ulang. */
    private function admin(): User
    {
        return User::unguarded(fn () => User::create(['name' => 'Admin Uji', 'email' => 'a@contoh.test', 'password' => 'kata-sandi-aman-123', 'role' => 'ADMIN', 'is_active' => true]));
    }

    private function valid(array $over = []): array
    {
        return $over + ['name' => 'Produk Baru', 'product_type' => 'PHYSICAL', 'rental_enabled' => 1, 'is_active' => 1];
    }

    public function test_pelanggan_dan_tamu_tidak_bisa_mengakses_crud(): void
    {
        $this->get('/admin/produk')->assertRedirect('/masuk');

        $customer = User::unguarded(fn () => User::create(['name' => 'P', 'email' => 'p@contoh.test', 'password' => 'kata-sandi-aman-123', 'role' => 'CUSTOMER', 'is_active' => true]));
        $this->actingAs($customer)->get('/admin/produk')->assertForbidden();
        $this->actingAs($customer)->post('/admin/produk', $this->valid())->assertForbidden();
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_menambah_produk_dengan_slug_dan_spesifikasi(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/produk', $this->valid([
            'name' => 'Produk Baru', 'rental_daily_rate' => 120000, 'shopee_url' => 'https://shopee.co.id/contoh',
            'specifications_text' => "Memori: 8 GB\nDaya: 15 W",
        ]))->assertRedirect('/admin/produk');

        $p = Product::where('slug', 'produk-baru')->firstOrFail();
        $this->assertSame(['Memori' => '8 GB', 'Daya' => '15 W'], $p->specifications);
        $this->assertSame(120000, (int) $p->rental_daily_rate);

        // nama sama ditolak; slug unik untuk nama mirip
        $this->actingAs($admin)->post('/admin/produk', $this->valid())->assertSessionHasErrors('name');
        $this->assertSame(1, Product::where('name', 'Produk Baru')->count());
    }

    public function test_validasi_penawaran_url_dan_spesifikasi(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/produk', $this->valid(['rental_enabled' => 0]))->assertSessionHasErrors('rental_enabled');
        $this->actingAs($admin)->post('/admin/produk', $this->valid(['shopee_url' => 'http://tidak-aman.test']))->assertSessionHasErrors('shopee_url');
        $this->actingAs($admin)->post('/admin/produk', $this->valid(['specifications_text' => 'tanpa titik dua']))->assertSessionHasErrors('specifications_text');
        $this->actingAs($admin)->post('/admin/produk', $this->valid(['rental_daily_rate' => -5]))->assertSessionHasErrors('rental_daily_rate');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_ubah_produk_mempertahankan_slug_dan_toggle_status(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/produk', $this->valid());
        $p = Product::firstOrFail();

        $this->actingAs($admin)->put("/admin/produk/{$p->id}", $this->valid(['name' => 'Nama Diganti']))->assertRedirect('/admin/produk');
        $this->assertSame('produk-baru', $p->fresh()->slug);
        $this->assertSame('Nama Diganti', $p->fresh()->name);

        $this->actingAs($admin)->post("/admin/produk/{$p->id}/status");
        $this->assertFalse($p->fresh()->is_active);
        $this->get('/produk/produk-baru')->assertNotFound();
    }

    public function test_unit_dibuat_untuk_produk_fisik_dengan_kode_unik(): void
    {
        $admin = $this->admin();
        $phys = Product::create(['name' => 'Fisik', 'slug' => 'fisik', 'product_type' => 'PHYSICAL']);
        $dig = Product::create(['name' => 'Digital', 'slug' => 'digital', 'product_type' => 'DIGITAL_SERVICE']);

        $this->actingAs($admin)->post('/admin/inventaris', ['product_id' => $phys->id, 'unit_code' => 'FSK-001', 'condition_status' => 'GOOD', 'operational_status' => 'ACTIVE'])
            ->assertRedirect('/admin/inventaris');
        $this->assertDatabaseHas('inventory_units', ['unit_code' => 'FSK-001', 'product_id' => $phys->id]);

        $this->actingAs($admin)->post('/admin/inventaris', ['product_id' => $phys->id, 'unit_code' => 'FSK-001', 'condition_status' => 'GOOD', 'operational_status' => 'ACTIVE'])->assertSessionHasErrors('unit_code');
        $this->actingAs($admin)->post('/admin/inventaris', ['product_id' => $dig->id, 'unit_code' => 'DGT-001', 'condition_status' => 'GOOD', 'operational_status' => 'ACTIVE'])->assertSessionHasErrors('product_id');
        $this->actingAs($admin)->post('/admin/inventaris', ['product_id' => $phys->id, 'unit_code' => 'huruf kecil', 'condition_status' => 'GOOD', 'operational_status' => 'ACTIVE'])->assertSessionHasErrors('unit_code');
    }

    public function test_unit_dipensiunkan_bukan_dihapus_dan_produk_berunit_tidak_jadi_digital(): void
    {
        $admin = $this->admin();
        $phys = Product::create(['name' => 'Fisik', 'slug' => 'fisik', 'product_type' => 'PHYSICAL']);
        $unit = InventoryUnit::create(['product_id' => $phys->id, 'unit_code' => 'FSK-001']);

        $this->actingAs($admin)->put("/admin/inventaris/{$unit->id}", ['product_id' => $phys->id, 'unit_code' => 'FSK-001', 'condition_status' => 'GOOD', 'operational_status' => 'RETIRED'])
            ->assertRedirect('/admin/inventaris');
        $this->assertSame('RETIRED', $unit->fresh()->operational_status);

        $this->actingAs($admin)->put("/admin/produk/{$phys->id}", $this->valid(['name' => 'Fisik', 'product_type' => 'DIGITAL_SERVICE']))->assertSessionHasErrors('product_type');
    }
}
