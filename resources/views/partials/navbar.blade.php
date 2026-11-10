{{-- Navbar utama. Sumber tautan: config/pitlabs.php → 'nav'. --}}
@php
    // Route yang belum dibuat → tampil nonaktif + keterangan fase (bukan tombol mati tanpa penjelasan).
    $linkFor  = fn (array $item) => isset($item['route']) && Route::has($item['route']) ? route($item['route']) : null;
    $isActive = fn (array $item) => isset($item['route']) && request()->routeIs($item['route']);
@endphp

<header class="pit-navbar">
    <nav class="navbar navbar-expand-lg" aria-label="Navigasi utama">
        <div class="container">

            <a class="navbar-brand" href="{{ Route::has('home') ? route('home') : url('/') }}">
                <img src="{{ asset('assets/img/logo-pitlabs.png') }}" alt="PITLABS — Your Premium IoT & AI Technology Hub" width="156" height="52">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Buka menu">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-3 mb-lg-0">
                    @foreach (config('pitlabs.nav') as $item)

                        @if (! empty($item['children']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $item['label'] }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($item['children'] as $child)
                                        @php $href = $linkFor($child); @endphp
                                        <li>
                                            @if ($href)
                                                <a class="dropdown-item @if($isActive($child)) active @endif" href="{{ $href }}">{{ $child['label'] }}</a>
                                            @else
                                                <span class="dropdown-item pit-nav-pending" aria-disabled="true" title="Halaman ini dikerjakan di fase berikutnya">{{ $child['label'] }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                        @elseif (! empty($item['soon']))
                            @php $href = $linkFor($item); @endphp
                            <li class="nav-item">
                                <a class="nav-link @if($isActive($item)) active @endif" href="{{ $href ?? '#' }}" @if($isActive($item)) aria-current="page" @endif>
                                    {{ $item['label'] }}
                                    <span class="pit-tag-soon">Segera Hadir</span>
                                </a>
                            </li>

                        @else
                            @php $href = $linkFor($item); @endphp
                            <li class="nav-item">
                                @if ($href)
                                    <a class="nav-link @if($isActive($item)) active @endif" href="{{ $href }}" @if($isActive($item)) aria-current="page" @endif>{{ $item['label'] }}</a>
                                @else
                                    <span class="nav-link pit-nav-pending" aria-disabled="true" title="Halaman ini dikerjakan di fase berikutnya">{{ $item['label'] }}</span>
                                @endif
                            </li>
                        @endif

                    @endforeach
                </ul>

                @if (\App\Support\DemoAuth::check())
                    @php $demoUser = \App\Support\DemoAuth::user(); @endphp
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                            <span class="text-truncate" style="max-width: 11rem">{{ \Illuminate\Support\Str::limit($demoUser['name'], 22) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg-end">
                            <li>
                                <a class="dropdown-item" href="{{ \App\Support\DemoAuth::isAdmin() ? route('admin.dashboard') : route('dashboard') }}">
                                    {{ \App\Support\DemoAuth::isAdmin() ? 'Dashboard admin' : 'Dashboard saya' }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right" aria-hidden="true"></i>&nbsp; Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-grid d-lg-flex gap-2">
                        @if (Route::has('login'))
                            <a class="btn btn-outline-primary" href="{{ route('login') }}">Masuk</a>
                        @else
                            <span class="btn btn-outline-primary disabled" aria-disabled="true" title="Halaman login dikerjakan di fase berikutnya">Masuk</span>
                        @endif
                        @if (Route::has('register'))
                            <a class="btn btn-primary" href="{{ route('register') }}">Daftar</a>
                        @else
                            <span class="btn btn-primary disabled" aria-disabled="true" title="Halaman registrasi dikerjakan di fase berikutnya">Daftar</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </nav>
</header>
