{{-- Panel konten. flush = tanpa padding isi (untuk tabel). --}}
@props(['title' => null, 'subtitle' => null, 'flush' => false])
<section {{ $attributes->class(['dash-panel']) }}>
    @if ($title || isset($actions))
        <header class="dash-panel__head">
            <div>
                @if ($title)<h2 class="dash-panel__title">{{ $title }}</h2>@endif
                @if ($subtitle)<p class="dash-panel__sub">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div class="dash-panel__actions">{{ $actions }}</div>@endisset
        </header>
    @endif
    <div @class(['dash-panel__body', 'dash-panel__body--flush' => $flush])>{{ $slot }}</div>
</section>
