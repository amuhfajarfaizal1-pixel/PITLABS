<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Pit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Autentikasi sungguhan (Fase 5A): registrasi, login, logout dengan database.
 * Login Google (Socialite) ditambahkan di 5B.
 */
class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login', ['kembali' => Pit::safePath($request->query('kembali'), '')]);
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

        // Batasi percobaan per email + IP (anti brute force), selain throttle pada rute.
        $key = Str::lower($data['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        // Pesan sengaja sama untuk email tidak ada, kata sandi salah, dan akun nonaktif.
        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'is_active' => true])) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate(); // cegah session fixation

        return redirect()->to($this->destination($request))
            ->with('status', 'Selamat datang, '.Auth::user()->name.'.');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register', ['kembali' => Pit::safePath($request->query('kembali'), '')]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:120'],
            'email'    => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'phone'    => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'password' => ['required', 'string', 'min:10', 'max:255', 'confirmed'],
        ], [
            'name.required'      => 'Isi nama lengkap Anda.',
            'name.min'           => 'Nama minimal 3 karakter.',
            'email.required'     => 'Isi alamat email Anda.',
            'email.email'        => 'Format email tidak valid. Contoh: nama@email.com',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk.',
            'phone.regex'        => 'Nomor WhatsApp tidak valid. Contoh: 081234567890',
            'password.required'  => 'Buat kata sandi.',
            'password.min'       => 'Kata sandi minimal 10 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        // role/is_active tidak mass-assignable; pelanggan baru selalu CUSTOMER dan aktif (default DB).
        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to($this->destination($request))
            ->with('status', 'Akun berhasil dibuat. Selamat datang, '.$user->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Anda sudah keluar.');
    }

    /** Tujuan setelah login: tautan "kembali" internal, atau dashboard sesuai role. */
    private function destination(Request $request): string
    {
        $back = Pit::safePath($request->input('kembali'), '');
        if ($back !== '') {
            return $back;
        }

        return Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard');
    }
}
