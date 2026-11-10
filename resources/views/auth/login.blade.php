@extends('layouts.app')

@section('title', 'Masuk')
@section('description', 'Masuk ke akun PITLABS untuk menyewa perangkat dan memantau masa sewa.')

@push('head')
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
@endpush

@section('content')
<section class="pit-auth">
    <div class="container">
        <div class="pit-auth__card">
            <h1 class="h2 mb-1">Masuk ke PITLABS</h1>
            <p class="text-muted-pit mb-4">Belum punya akun? <a class="pit-link" href="{{ route('register', ['kembali' => $kembali]) }}">Daftar</a></p>


            @include('auth._social', ['label' => 'Masuk dengan Google', 'kembali' => $kembali])

            <form method="POST" action="{{ route('login.store') }}" data-pit-validate novalidate>
                @csrf
                <input type="hidden" name="kembali" value="{{ $kembali }}">

                <x-field name="email" label="Email" type="email" required autocomplete="email" />
                <x-field name="password" label="Kata sandi" type="password" required autocomplete="current-password" />

                <button type="submit" class="btn btn-primary w-100 mt-2">Masuk</button>
            </form>

        </div>
    </div>
</section>
@endsection
