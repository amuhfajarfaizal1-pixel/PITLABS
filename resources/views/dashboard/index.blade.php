@extends('layouts.dashboard')
@section('title', 'Dashboard Saya')
@section('page_label', 'Ringkasan')

@section('content')
<x-dash.page-header :title="'Halo, '.\Illuminate\Support\Str::of($user['name'])->before(' ')" description="Pantau penyewaan, status pembayaran, dan jadwal pengembalian Anda di sini.">
    <x-slot:actions><a href="{{ route('catalog.index') }}" class="btn btn-primary">Sewa perangkat</a></x-slot:actions>
</x-dash.page-header>

<div class="dash-grid dash-grid--stats mb-4">
    <x-dash.stat label="Sedang disewa" :value="count($active)" icon="bi-play-circle" :href="route('dashboard.rentals')" />
    <x-dash.stat label="Siap diambil" :value="count($upcoming)" icon="bi-box-seam" hint="Di lokasi admin" :href="route('dashboard.rentals')" />
    <x-dash.stat label="Total transaksi" :value="count($history)" icon="bi-receipt" :href="route('dashboard.history')" />
</div>

<div class="dash-grid dash-grid--2 mb-4">
    <div class="d-grid gap-3 align-content-start">
        @forelse (array_merge($active, $upcoming) as $o)
            @include('dashboard._rental-card', ['o' => $o])
        @empty
            <x-empty-state icon="bi-box-seam" title="Belum ada penyewaan aktif">Perangkat yang Anda sewa muncul di sini beserta sisa masa sewanya.
                <x-slot:action><a href="{{ route('catalog.index') }}" class="btn btn-primary">Lihat katalog</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <x-dash.panel title="Pengingat" subtitle="Contoh pengingat di dashboard" flush>
        <ul class="dash-list">
            @foreach ($reminders as $n)
                <li class="dash-list__item">
                    <span class="dash-notif__icon dash-notif__icon--{{ $n['tone'] }}"><i class="bi {{ $n['icon'] }}" aria-hidden="true"></i></span>
                    <div class="dash-list__main"><strong>{{ $n['title'] }}</strong><small>{{ $n['text'] }}</small></div>
                </li>
            @endforeach
        </ul>
    </x-dash.panel>
</div>

<x-dash.panel title="Transaksi terakhir" flush>
    <x-slot:actions><a href="{{ route('dashboard.history') }}" class="pit-link small">Lihat semua</a></x-slot:actions>
    <x-dash.table id="tblRecent" :searchable="false" caption="Transaksi terakhir" :rows="array_map(fn ($o) => $o + ['stack' => [$o['product'], $o['unit']], '_actions' => [['label' => 'Invoice', 'variant' => 'soft', 'href' => route('invoice.show', $o['invoice'])]]], array_slice($history, 0, 4))" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Perangkat','type'=>'stack'],
        ['key'=>'period','label'=>'Periode'],
        ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
        ['key'=>'rental','label'=>'Penyewaan','type'=>'status','group'=>'rental'],
        ['type'=>'actions'],
    ]" />
</x-dash.panel>
@endsection
