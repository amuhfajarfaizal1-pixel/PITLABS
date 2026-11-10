<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Otorisasi per role. Dipasang SETELAH middleware `auth`.
 * Pemakaian: ->middleware(['auth', EnsureRole::class.':admin'])
 *   admin    → hanya ADMIN
 *   customer → pengguna aktif mana pun yang sudah login
 * Akun yang dinonaktifkan langsung dikeluarkan.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role = 'customer'): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', 'Sesi berakhir atau akun tidak aktif. Silakan masuk kembali.');
        }

        abort_if($role === 'admin' && ! $user->isAdmin(), 403, 'Halaman ini khusus admin.');

        return $next($request);
    }
}
