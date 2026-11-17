<?php

namespace App\Support;

/**
 * QR invoice berbentuk SVG (tanpa GD/Imagick). Membutuhkan paket Composer:
 *     composer require bacon/bacon-qr-code
 * Jika paket belum terpasang, available() bernilai false dan halaman invoice menampilkan petunjuk.
 */
class InvoiceQr
{
    public static function available(): bool
    {
        return class_exists(\BaconQrCode\Writer::class);
    }

    public static function svg(string $invoiceNumber): ?string
    {
        if (! self::available()) {
            return null;
        }

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(240, 2),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $svg = (new \BaconQrCode\Writer($renderer))->writeString(InvoiceToken::payload($invoiceNumber));

        return trim((string) preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg));
    }
}
