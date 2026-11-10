<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Helper frontend PITLABS.
 * Halaman dibangun bertahap, jadi tautan ke route yang belum ada harus ditangani
 * dengan jelas (fallback atau tampil nonaktif), bukan error.
 */
class Pit
{
    /** URL route jika route sudah ada, selain itu kembalikan $fallback (bisa null). */
    public static function url(string $route, ?string $fallback = null, array $params = []): ?string
    {
        return Route::has($route) ? route($route, $params) : $fallback;
    }

    /**
     * Tujuan tautan "ketentuan sewa dan denda kerusakan/kehilangan".
     * Bagian #ketentuan-sewa ada di halaman Panduan; selama Panduan belum dibuat,
     * bagian yang sama tampil di homepage.
     */
    public static function termsUrl(): string
    {
        $page = Route::has('guide') ? route('guide') : route('home');

        return $page.'#ketentuan-sewa';
    }

    /** Hanya path internal yang boleh jadi tujuan redirect (cegah open redirect). */
    public static function safePath(?string $path, string $default = '/'): string
    {
        if (! is_string($path) || $path === '' || $path[0] !== '/') {
            return $default;
        }
        if (str_starts_with($path, '//') || str_contains($path, '\\') || preg_match('/[\x00-\x1F]/', $path)) {
            return $default;
        }

        return $path;
    }

    public static function rupiah(?int $n): string
    {
        return 'Rp '.number_format((int) $n, 0, ',', '.');
    }

    /**
     * Pola mirip QR (BUKAN QR sungguhan, tidak bisa dipindai) untuk tampilan invoice contoh.
     * Fase 5: ganti dengan QR berisi token verifikasi bertanda tangan.
     * @return array<int, array<int, bool>> matriks 25 x 25
     */
    public static function qrPlaceholder(string $seed, int $size = 25): array
    {
        $bits = '';
        $i = 0;
        while (strlen($bits) < $size * $size) {
            $bits .= sprintf('%032b', crc32($seed.$i++));
        }
        $m = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $m[$y][$x] = $bits[$y * $size + $x] === '1';
            }
        }
        // tiga penanda sudut agar terbaca sebagai pola QR
        foreach ([[0, 0], [0, $size - 7], [$size - 7, 0]] as [$oy, $ox]) {
            for ($y = 0; $y < 7; $y++) {
                for ($x = 0; $x < 7; $x++) {
                    $edge = $y === 0 || $y === 6 || $x === 0 || $x === 6;
                    $core = $y >= 2 && $y <= 4 && $x >= 2 && $x <= 4;
                    $m[$oy + $y][$ox + $x] = $edge || $core;
                }
            }
        }

        return $m;
    }
}
