{{-- Kartu statistik. tone: accent (default) | success | warning | danger --}}
@props(['label', 'value', 'icon' => 'bi-circle', 'hint' => null, 'href' => null, 'tone' => 'accent'])
@php $tag = $href ? 'a' : 'div'; @endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['dash-stat', 'dash-stat--'.$tone, 'dash-stat--link' => (bool) $href]) }}>
    <span class="dash-stat__icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
    <span class="dash-stat__body">
        <span class="dash-stat__label">{{ $label }}</span>
        <span class="dash-stat__value">{{ $value }}</span>
        @if ($hint)<span class="dash-stat__hint">{{ $hint }}</span>@endif
    </span>
</{{ $tag }}>
