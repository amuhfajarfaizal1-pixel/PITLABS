<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * AUTH SIMULASI (Fase 4) — BUKAN autentikasi sungguhan.
 * Hanya menyimpan penanda di session agar alur UI (navbar, logout, "login dulu
 * sebelum sewa") bisa didemonstrasikan. Tidak memeriksa akun/kata sandi.
 * Fase 5: ganti seluruh kelas ini dengan Laravel Auth + Socialite (Google).
 */
class DemoAuth
{
    private const KEY = 'pit_demo_user';

    public static function login(string $email, ?string $name = null, string $provider = 'password', string $role = 'customer'): void
    {
        session()->regenerate(); // cegah session fixation

        session([self::KEY => [
            'name'     => $name ?: self::nameFromEmail($email),
            'email'    => $email,
            'provider' => $provider,
            'role'     => $role,
        ]]);
    }

    public static function check(): bool
    {
        return session()->has(self::KEY);
    }

    public static function isAdmin(): bool
    {
        return (session(self::KEY)['role'] ?? null) === 'admin';
    }

    public static function user(): ?array
    {
        return session(self::KEY);
    }

    public static function logout(): void
    {
        session()->forget(self::KEY);
        session()->invalidate();
        session()->regenerateToken();
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

    private static function nameFromEmail(string $email): string
    {
        return (string) Str::of(Str::before($email, '@'))->replace(['.', '_', '-'], ' ')->title();
    }
}
