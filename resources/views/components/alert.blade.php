{{-- Pemakaian: <x-alert type="success" title="Berhasil">Pesan</x-alert>   (info|success|warning|danger) --}}
@props(['type' => 'info', 'title' => null])

@php
    $icons = [
        'info'    => 'bi-info-circle-fill',
        'success' => 'bi-check-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'danger'  => 'bi-x-octagon-fill',
    ];
@endphp

<div {{ $attributes->class(['pit-alert', 'pit-alert--'.$type]) }} role="{{ $type === 'danger' ? 'alert' : 'status' }}">
    <i class="bi {{ $icons[$type] ?? $icons['info'] }} pit-alert__icon" aria-hidden="true"></i>
    <div>
        @if ($title)<p class="pit-alert__title">{{ $title }}</p>@endif
        <div class="pit-alert__body">{{ $slot }}</div>
    </div>
</div>
