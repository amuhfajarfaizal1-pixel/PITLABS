<?php

namespace App\Http\Middleware;

use App\Support\DemoAuth;
use Closure;
use Illuminate\Http\Request;

/**
 * Penjaga rute SIMULASI (Fase 4). Pemakaian: ->middleware(DemoRole::class.':admin')
 * Fase 5: ganti dengan middleware auth + otorisasi role dari database.
 */
class DemoRole
{
    public function handle(Request $request, Closure $next, string $role = 'customer')
    {
        if (! DemoAuth::check()) {
            return redirect()->route('login', ['kembali' => $request->getRequestUri()]);
        }
        if ($role === 'admin' && ! DemoAuth::isAdmin()) {
            abort(403, 'Halaman ini khusus admin.');
        }

        return $next($request);
    }
}
