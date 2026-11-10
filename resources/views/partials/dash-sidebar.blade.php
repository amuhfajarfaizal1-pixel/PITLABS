{{-- Sidebar dashboard. Sumber menu: config/pitlabs.php → dash_nav. Di bawah 992 px menjadi offcanvas. --}}
@php
    $groups = config('pitlabs.dash_nav.'.$theme);
    $kpi = $theme === 'admin' ? \App\Support\MockData::kpis() : [];
@endphp

<aside class="dash-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="dashSidebar" aria-label="Menu {{ $theme === 'admin' ? 'admin' : 'akun' }}">
    <div class="offcanvas-header d-lg-none">
        <span class="fw-bold">Menu</span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#dashSidebar" aria-label="Tutup menu"></button>
    </div>

    <div class="dash-sidebar__body">
        <a href="{{ route('home') }}" class="dash-brand" aria-label="PITLABS, ke beranda">
            <span class="dash-brand__logo"><img src="{{ asset('assets/img/logo-pitlabs.png') }}" alt="PITLABS" width="117" height="39"></span>
            @if ($theme === 'admin')<span class="dash-brand__tag">Admin</span>@endif
        </a>

        <nav class="dash-nav" aria-label="Navigasi utama dashboard">
            @foreach ($groups as $heading => $items)
                <p class="dash-nav__heading">{{ $heading }}</p>
                <ul class="dash-nav__list">
                    @foreach ($items as $item)
                        @php
                            [$label, $routeName, $icon] = $item;
                            $badgeKey = $item[3] ?? null;
                            $count = $badgeKey ? ($kpi[$badgeKey] ?? 0) : 0;
                            $active = request()->routeIs($routeName);
                        @endphp
                        <li>
                            <a href="{{ route($routeName) }}" class="dash-nav__link @if($active) is-active @endif" @if($active) aria-current="page" @endif>
                                <i class="bi {{ $icon }}" aria-hidden="true"></i>
                                <span>{{ $label }}</span>
                                @if ($count > 0)<span class="dash-nav__count" aria-label="{{ $count }} item">{{ $count }}</span>@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </nav>

        <div class="dash-sidebar__foot">
            <a href="{{ route('home') }}" class="dash-nav__link"><i class="bi bi-box-arrow-up-left" aria-hidden="true"></i><span>Kembali ke situs</span></a>
        </div>
    </div>
</aside>
