<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('pitlabs.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0A2540">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/css/pitlabs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    @stack('head')
</head>
@php
    $theme = (request()->is('admin*') || (request()->is('invoice*') && auth()->user()->isAdmin())) ? 'admin' : 'user';
    $authUser = ['name' => auth()->user()->name];
@endphp
<body class="pit-dash pit-theme-{{ $theme }}">

<a class="pit-skip-link" href="#main">Lewati ke konten utama</a>

<div class="dash-shell">
    @include('partials.dash-sidebar', ['theme' => $theme])

    <div class="dash-main">
        <header class="dash-topbar">
            <button class="dash-topbar__menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashSidebar" aria-controls="dashSidebar" aria-label="Buka menu navigasi">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <div class="dash-topbar__title">
                <span class="dash-topbar__role">{{ $theme === 'admin' ? 'Admin' : 'Akun saya' }}</span>
                <span class="visually-hidden">: </span>
                <span class="dash-topbar__page">@yield('page_label', 'Dashboard')</span>
            </div>

            <div class="dash-topbar__actions">
                @if ($theme === 'admin')
                    <a href="{{ route('admin.scanner') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex"><i class="bi bi-qr-code-scan" aria-hidden="true"></i> Scan QR</a>
                @else
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex">Sewa perangkat</a>
                @endif

                <div class="dropdown">
                    <button class="dash-iconbtn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi, {{ count(\App\Support\MockData::reminders()) }} baru">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                        <span class="dash-iconbtn__dot" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dash-notif">
                        <p class="dash-notif__head">Notifikasi</p>
                        @foreach (\App\Support\MockData::reminders() as $n)
                            <div class="dash-notif__item">
                                <span class="dash-notif__icon dash-notif__icon--{{ $n['tone'] }}"><i class="bi {{ $n['icon'] }}" aria-hidden="true"></i></span>
                                <div>
                                    <p class="dash-notif__title">{{ $n['title'] }} <span class="dash-notif__time">{{ $n['time'] }}</span></p>
                                    <p class="dash-notif__text">{{ $n['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                        <p class="dash-notif__foot">Contoh notifikasi. Pengingat WhatsApp/email menunggu konfigurasi penyedia.</p>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="dash-userbtn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="dash-avatar" aria-hidden="true">{{ strtoupper(mb_substr($authUser['name'] ?? 'U', 0, 1)) }}</span>
                        <span class="d-none d-md-inline text-truncate">{{ \Illuminate\Support\Str::limit($authUser['name'] ?? 'Pengguna', 20) }}</span>
                        <i class="bi bi-chevron-down small d-none d-md-inline" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-house" aria-hidden="true"></i>&nbsp; Ke situs PITLABS</a></li>
                        @if ($theme === 'user')
                            <li><a class="dropdown-item" href="{{ route('dashboard.profile') }}"><i class="bi bi-person" aria-hidden="true"></i>&nbsp; Profil</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right" aria-hidden="true"></i>&nbsp; Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main id="main" tabindex="-1" class="dash-content">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif

            @if (config('pitlabs.demo_mode'))
                <div class="dash-demo" role="note"><i class="bi bi-info-circle" aria-hidden="true"></i> Data contoh untuk tampilan. Nama, invoice, dan nominal bukan data nyata; aksi bersifat simulasi dan tidak disimpan.</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Modal konfirmasi simulasi (dipakai semua tombol [data-pit-sim]) --}}
<div class="modal fade" id="simModal" tabindex="-1" aria-labelledby="simModal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pit-modal">
            <div class="modal-header">
                <h2 class="modal-title" id="simModal-title">Konfirmasi</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p id="simModal-text" class="mb-3"></p>
                <label for="simModal-note" class="form-label">Catatan (opsional)</label>
                <textarea id="simModal-note" class="form-control" rows="2" maxlength="300"></textarea>
                <p class="small text-muted-pit mt-3 mb-0">Simulasi: perubahan hanya tampil di halaman ini dan tidak disimpan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="simModal-confirm">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="dashToast" class="toast dash-toast" role="status" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> <span id="dashToast-text"></span></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
<script src="{{ asset('assets/js/pitlabs.js') }}" defer></script>
<script src="{{ asset('assets/js/dashboard.js') }}" defer></script>
@stack('scripts')
</body>
</html>
