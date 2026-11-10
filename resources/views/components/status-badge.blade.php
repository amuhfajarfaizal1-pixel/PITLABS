{{--
    Badge status. Selalu teks + ikon, tidak mengandalkan warna saja.
    Pemakaian: <x-status-badge group="payment" status="UNPAID" />
    Grup: order | payment | pickup | rental (lihat config/pitlabs.php).
--}}
@props(['group', 'status'])

@php
    $def = config("pitlabs.status.$group.$status");
    $groupLabel = config("pitlabs.status_groups.$group");
@endphp

<span {{ $attributes->class(['pit-badge', 'pit-badge--'.($def['variant'] ?? 'neutral')]) }}>
    <i class="bi {{ $def['icon'] ?? 'bi-question-circle' }}" aria-hidden="true"></i>
    @if ($groupLabel)<span class="visually-hidden">{{ $groupLabel }}: </span>@endif
    <span>{{ $def['label'] ?? 'Status tidak dikenal' }}</span>
</span>
