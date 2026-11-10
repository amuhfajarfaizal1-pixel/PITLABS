@extends('layouts.app')

@section('title', 'Daftar Akun')
@section('description', 'Buat akun PITLABS untuk menyewa perangkat AI, IoT, dan teknologi.')

@push('head')
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
@endpush

@section('content')
<section class="pit-auth">
    <div class="container">
        <div class="pit-auth__card">
            <h1 class="h2 mb-1">Buat akun PITLABS</h1>
            <p class="text-muted-pit mb-4">Sudah punya akun? <a class="pit-link" href="{{ route('login', ['kembali' => $kembali]) }}">Masuk</a></p>


            @include('auth._social', ['label' => 'Daftar dengan Google', 'kembali' => $kembali])

            <form method="POST" action="{{ route('register.store') }}" data-pit-validate novalidate>
                @csrf
                <input type="hidden" name="kembali" value="{{ $kembali }}">

                <x-field name="name" label="Nama lengkap" required autocomplete="name" minlength="3" maxlength="120" />
                <x-field name="email" label="Email" type="email" required autocomplete="email" hint="Dipakai untuk invoice dan pemberitahuan." />
                <x-field name="phone" label="Nomor WhatsApp (opsional)" type="tel" autocomplete="tel" inputmode="numeric"
                         pattern="(\+62|62|0)8[0-9]{8,12}" data-pattern-msg="Nomor WhatsApp tidak valid. Contoh: 081234567890"
                         hint="Contoh: 081234567890" />
                <x-field name="password" label="Kata sandi" type="password" required minlength="10" autocomplete="new-password" hint="Minimal 10 karakter." />
                <x-field name="password_confirmation" label="Ulangi kata sandi" type="password" required autocomplete="new-password" data-match="#field-password" data-msg-match="Konfirmasi kata sandi tidak sama." />

                <button type="submit" class="btn btn-primary w-100 mt-2">Buat akun</button>
            </form>
        </div>
    </div>
</section>
@endsection
