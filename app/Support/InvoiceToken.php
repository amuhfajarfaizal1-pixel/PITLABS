<?php

namespace App\Support;

/**
 * Identitas QR invoice yang dapat diverifikasi. Token = HMAC-SHA256(nomor invoice, APP_KEY),
 * sehingga QR dapat dibuat ulang kapan saja dan tidak dapat dipalsukan tanpa APP_KEY.
 * Database hanya menyimpan SHA-256 dari token (invoices.qr_token_hash).
 * Isi QR:  PITLABS|1|<nomor invoice>|<token>
 * Catatan: mengganti APP_KEY membuat QR lama tidak valid (nomor invoice manual tetap bisa dipakai).
 */
class InvoiceToken
{
    private const PREFIX = 'PITLABS';
    private const VERSION = '1';

    public static function token(string $invoiceNumber): string
    {
        $mac = hash_hmac('sha256', 'pitlabs-invoice|'.$invoiceNumber, (string) config('app.key'), true);

        return rtrim(strtr(base64_encode($mac), '+/', '-_'), '=');
    }

    public static function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    public static function payload(string $invoiceNumber): string
    {
        return implode('|', [self::PREFIX, self::VERSION, $invoiceNumber, self::token($invoiceNumber)]);
    }

    /**
     * Membaca isi QR. Mengembalikan [nomorInvoice, tokenHash] bila bentuk dan tanda tangan sah, selain itu null.
     * (Pencocokan dengan database dilakukan pemanggil.)
     */
    public static function parse(string $raw): ?array
    {
        $parts = explode('|', trim($raw));
        if (count($parts) !== 4 || $parts[0] !== self::PREFIX || $parts[1] !== self::VERSION) {
            return null;
        }
        [, , $number, $token] = $parts;
        if (! preg_match('/^INV-[A-Z0-9-]{4,40}$/', $number) || ! hash_equals(self::token($number), $token)) {
            return null;
        }

        return [$number, self::hash($token)];
    }
}
