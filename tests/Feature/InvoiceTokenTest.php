<?php

namespace Tests\Feature;

use App\Support\InvoiceQr;
use App\Support\InvoiceToken;
use Tests\TestCase;

/** Identitas QR invoice yang dapat diverifikasi (Fase 5C-1). */
class InvoiceTokenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('k', 32))]);
    }

    public function test_isi_qr_dapat_dibaca_kembali_dan_cocok_dengan_hash_penyimpanan(): void
    {
        $payload = InvoiceToken::payload('INV-261113-ABC123');
        $parsed = InvoiceToken::parse($payload);

        $this->assertSame('INV-261113-ABC123', $parsed[0]);
        $this->assertSame(InvoiceToken::hash(InvoiceToken::token('INV-261113-ABC123')), $parsed[1]);
        $this->assertSame(64, strlen($parsed[1]));
    }

    public function test_isi_qr_yang_dipalsukan_atau_rusak_ditolak(): void
    {
        $valid = InvoiceToken::payload('INV-261113-ABC123');

        $this->assertNull(InvoiceToken::parse(str_replace('ABC123', 'ABC124', $valid)));          // nomor diganti
        $this->assertNull(InvoiceToken::parse(substr($valid, 0, -3).'xyz'));                       // token diubah
        $this->assertNull(InvoiceToken::parse('PITLABS|9|INV-261113-ABC123|abc'));                 // versi salah
        $this->assertNull(InvoiceToken::parse('INV-261113-ABC123'));                               // hanya nomor, tanpa token
        $this->assertNull(InvoiceToken::parse('<script>alert(1)</script>'));
        $this->assertNull(InvoiceToken::parse(''));
    }

    public function test_token_bergantung_pada_app_key(): void
    {
        $a = InvoiceToken::token('INV-261113-ABC123');
        config(['app.key' => 'base64:'.base64_encode(str_repeat('z', 32))]);

        $this->assertNotSame($a, InvoiceToken::token('INV-261113-ABC123'));
    }

    public function test_qr_svg_dihasilkan_bila_paket_terpasang(): void
    {
        if (! InvoiceQr::available()) {
            $this->markTestSkipped('Paket bacon/bacon-qr-code belum terpasang (composer require bacon/bacon-qr-code).');
        }

        $svg = InvoiceQr::svg('INV-261113-ABC123');
        $this->assertStringStartsWith('<svg', $svg);
    }
}
