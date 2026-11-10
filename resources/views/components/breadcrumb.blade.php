{{--
    Breadcrumb + data terstruktur BreadcrumbList (SEO).
    <x-breadcrumb :items="[['Beranda', route('home')], ['Katalog', route('catalog.index')], ['Laptop', null]]" />
    Item terakhir (url null) = halaman saat ini.
--}}
@props(['items'])

@php
    $list = [];
    foreach ($items as $i => [$label, $url]) {
        $list[] = array_filter([
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $label,
            'item'     => $url ?? url()->current(),
        ]);
    }
    $jsonLd = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

<nav aria-label="Breadcrumb" class="pit-breadcrumb">
    <ol>
        @foreach ($items as [$label, $url])
            <li @if (! $url) aria-current="page" @endif>
                @if ($url)<a href="{{ $url }}">{{ $label }}</a>@else<span>{{ $label }}</span>@endif
            </li>
        @endforeach
    </ol>
</nav>
