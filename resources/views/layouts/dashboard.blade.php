<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('pitlabs.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/css/pitlabs.css') }}"><link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}"><link rel="stylesheet" href="{{ asset('assets/css/theme-dark.css') }}">
    @stack('head')
</head>
@php $theme = (request()->is('admin*') || (request()->is('invoice*') && auth()->user()?->isAdmin())) ? 'admin' : 'user'; @endphp
<body class="pit-dash pit-theme-{{ $theme }}" data-theme="light">
<a class="pit-skip-link" href="#main">Lewati ke konten utama</a>
<div class="dash-shell">
    @include('partials.dash-sidebar', ['theme' => $theme])
    <div class="dash-main">
        <header class="dash-topbar">
            <button class="dash-topbar__menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashSidebar" aria-controls="dashSidebar" aria-label="Buka menu navigasi"><i class="bi bi-list" aria-hidden="true"></i></button>
            <div class="dash-topbar__title"><span class="dash-topbar__role">{{ $theme === 'admin' ? 'Admin' : 'Akun saya' }}</span><span class="visually-hidden">: </span><span class="dash-topbar__page">@yield('page_label', 'Dashboard')</span></div>
            <div class="dash-topbar__actions">
                <button type="button" class="dash-iconbtn" data-theme-toggle aria-label="Aktifkan tema gelap" title="Ganti tema"><i class="bi bi-moon-stars" aria-hidden="true"></i></button>
                @if ($theme === 'admin') <a href="{{ route('admin.scanner') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex"><i class="bi bi-qr-code-scan" aria-hidden="true"></i> Scan QR</a>
                @else <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex">Sewa perangkat</a> @endif
                <div class="dropdown">
                    <button class="dash-userbtn" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="dash-avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name ?? 'U',0,1)) }}</span><span class="d-none d-md-inline text-truncate">{{ \Illuminate\Support\Str::limit(auth()->user()->name ?? 'Pengguna',20) }}</span><i class="bi bi-chevron-down small d-none d-md-inline" aria-hidden="true"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-house" aria-hidden="true"></i>&nbsp; Ke situs PITLABS</a></li>@if($theme === 'user')<li><a class="dropdown-item" href="{{ route('dashboard.profile') }}"><i class="bi bi-person" aria-hidden="true"></i>&nbsp; Profil</a></li>@endif<li><hr class="dropdown-divider"></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right" aria-hidden="true"></i>&nbsp; Keluar</button></form></li></ul>
                </div>
            </div>
        </header>
        <main id="main" tabindex="-1" class="dash-content">
            @if (session('status')) <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert> @endif
            @if ($errors->any()) <x-alert type="danger" class="mb-4" title="Periksa kembali">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</x-alert> @endif
            @yield('content')
        </main>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100"><div id="dashToast" class="toast dash-toast" role="status" aria-live="polite" aria-atomic="true"><div class="d-flex"><div class="toast-body"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> <span id="dashToast-text"></span></div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
<script src="{{ asset('assets/js/pitlabs.js') }}" defer></script><script src="{{ asset('assets/js/dashboard.js') }}" defer></script><script src="{{ asset('assets/js/theme.js') }}" defer></script>
@stack('scripts')
</body></html>
