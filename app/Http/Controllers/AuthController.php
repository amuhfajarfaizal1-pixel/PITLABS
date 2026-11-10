<?php

namespace App\Http\Controllers;

use App\Support\DemoAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Login/registrasi SIMULASI (Fase 4). Validasi input sungguhan; pengecekan akun
 * dan penyimpanan ke database baru ada di Fase 5 (Laravel Auth + Socialite).
 */
class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (DemoAuth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login', ['kembali' => DemoAuth::safePath($request->query('kembali'))]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email:rfc', 'max:190'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'email.required'    => 'Isi alamat email Anda.',
            'email.email'       => 'Format email tidak valid. Contoh: nama@email.com',
            'password.required' => 'Isi kata sandi Anda.',
        ]);

        DemoAuth::login($data['email']);

        return redirect(DemoAuth::safePath($request->input('kembali')))
            ->with('status', 'Anda masuk sebagai '.DemoAuth::user()['name'].' (mode demo).');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (DemoAuth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register', ['kembali' => DemoAuth::safePath($request->query('kembali'))]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:120'],
            'email'    => ['required', 'email:rfc', 'max:190'],
            'phone'    => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'password' => ['required', 'string', 'min:10', 'max:255', 'confirmed'],
        ], [
            'name.required'      => 'Isi nama lengkap Anda.',
            'name.min'           => 'Nama minimal 3 karakter.',
            'email.required'     => 'Isi alamat email Anda.',
            'email.email'        => 'Format email tidak valid. Contoh: nama@email.com',
            'phone.regex'        => 'Nomor WhatsApp tidak valid. Contoh: 081234567890',
            'password.required'  => 'Buat kata sandi.',
            'password.min'       => 'Kata sandi minimal 10 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        DemoAuth::login($data['email'], $data['name']);

        return redirect(DemoAuth::safePath($request->input('kembali')))
            ->with('status', 'Akun demo dibuat dan Anda sudah masuk. Data belum disimpan ke database.');
    }

    /** Simulasi "Lanjutkan dengan Google". Fase 5: redirect OAuth 2.0 lewat Laravel Socialite. */
    public function googleDemo(Request $request): RedirectResponse
    {
        DemoAuth::login('pengguna.google@contoh.test', 'Pengguna Google (demo)', 'google');

        return redirect(DemoAuth::safePath($request->input('kembali')))
            ->with('status', 'Masuk dengan akun Google demo. Login Google sungguhan dipasang di Fase 5.');
    }

    /** Akses cepat untuk presentasi (hanya saat demo_mode). Fase 5: dihapus. */
    public function demoLogin(Request $request): RedirectResponse
    {
        abort_unless(config('pitlabs.demo_mode'), 404);

        $admin = $request->input('role') === 'admin';
        DemoAuth::login(
            $admin ? 'admin@pitlabs.test' : 'pelanggan@pitlabs.test',
            $admin ? 'Admin Demo' : 'Pelanggan Demo',
            'demo',
            $admin ? 'admin' : 'customer'
        );

        return redirect()->route($admin ? 'admin.dashboard' : 'dashboard')
            ->with('status', 'Masuk sebagai '.($admin ? 'Admin' : 'Pelanggan').' demo.');
    }

    public function logout(): RedirectResponse
    {
        DemoAuth::logout();

        return redirect()->route('home')->with('status', 'Anda sudah keluar.');
    }
}
