<?php

namespace Tests\Feature;

use App\Models\InventoryUnit;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Katalog publik dari database (Fase 5B). Jalankan: php artisan test */
class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $name, array $extra = []): Product
    {
        return Product::create($extra + [
            'name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'product_type' => 'PHYSICAL',
            'rental_enabled' => true, 'sale_enabled' => false, 'is_active' => true,
        ]);
    }

    public function test_katalog_hanya_menampilkan_produk_aktif(): void
    {
        $this->product('Alat Aktif');
        $this->product('Alat Tersembunyi', ['is_active' => false]);

        $this->get('/katalog')->assertOk()->assertSee('Alat Aktif')->assertDontSee('Alat Tersembunyi');
    }

    public function test_pencarian_dan_filter_jenis(): void
    {
        $this->product('Kamera Uji');
        $this->product('Layanan Uji', ['product_type' => 'DIGITAL_SERVICE']);

        $this->get('/katalog?q=Kamera')->assertSee('Kamera Uji')->assertDontSee('Layanan Uji');
        $this->get('/katalog?jenis=digital')->assertSee('Layanan Uji')->assertDontSee('Kamera Uji');
        $this->get('/katalog?q=%25')->assertOk(); // karakter wildcard tidak merusak query
    }

    public function test_detail_produk_aktif_ok_dan_nonaktif_404(): void
    {
        $this->product('Alat Aktif');
        $this->product('Alat Mati', ['is_active' => false]);

        $this->get('/produk/alat-aktif')->assertOk()->assertSee('Alat Aktif');
        $this->get('/produk/alat-mati')->assertNotFound();
        $this->get('/produk/tidak-ada')->assertNotFound();
    }

    public function test_harga_tampil_jika_diisi_dan_pending_jika_kosong(): void
    {
        $this->product('Alat Berharga', ['rental_daily_rate' => 150000]);
        $this->product('Alat Tanpa Harga');

        $this->get('/produk/alat-berharga')->assertSee('Rp 150.000');
        $this->get('/produk/alat-tanpa-harga')->assertSee('Informasi belum tersedia');
    }

    public function test_beranda_dan_sitemap_memakai_produk_database(): void
    {
        $this->product('Alat Aktif');
        $this->product('Alat Mati', ['is_active' => false]);

        $this->get('/')->assertOk()->assertSee('Alat Aktif')->assertDontSee('Alat Mati');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/produk/alat-aktif', false)->assertDontSee('alat-mati');
    }

    public function test_cek_ketersediaan_memakai_unit_dari_database(): void
    {
        $p = $this->product('Alat Unit');
        foreach (['TST-001', 'TST-002', 'TST-003'] as $code) {
            InventoryUnit::create(['product_id' => $p->id, 'unit_code' => $code]);
        }
        InventoryUnit::create(['product_id' => $p->id, 'unit_code' => 'TST-009', 'operational_status' => 'MAINTENANCE']);

        $start = now('Asia/Makassar')->addDays(30)->toDateString();
        $this->get("/cek-ketersediaan?produk=alat-unit&mulai={$start}&durasi=1&satuan=hari")
            ->assertOk()->assertSee('TST-001')->assertSee('TST-003')->assertDontSee('TST-009');
    }
}
