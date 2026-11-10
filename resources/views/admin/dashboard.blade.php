@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')
@section('page_label', 'Dashboard')

@section('content')
<x-dash.page-header title="Dashboard admin" description="Ringkasan operasional rental hari ini. New Order dan Ongoing Rental dipisahkan agar tindakan tidak tercampur.">
    <x-slot:actions>
        <a href="{{ route('admin.scanner') }}" class="btn btn-primary"><i class="bi bi-qr-code-scan" aria-hidden="true"></i> Scan QR invoice</a>
        <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary">Laporan keuangan</a>
    </x-slot:actions>
</x-dash.page-header>

<div class="dash-grid dash-grid--stats mb-4">
    <x-dash.stat label="New Order" :value="$k['new_orders']" icon="bi-bag-plus" hint="Menunggu diproses" :href="route('admin.orders.new')" />
    <x-dash.stat label="Ongoing Rental" :value="$k['ongoing']" icon="bi-arrow-repeat" hint="Sedang disewa" :href="route('admin.rentals.ongoing')" />
    <x-dash.stat label="Unit tersedia" :value="$k['units_available']" icon="bi-check2-circle" tone="success" hint="Hari ini" :href="route('admin.inventory')" />
    <x-dash.stat label="Unit disewa" :value="$k['units_rented']" icon="bi-person-check" hint="Hari ini" :href="route('admin.inventory')" />
    <x-dash.stat label="Reservasi mendatang" :value="$k['units_reserved']" icon="bi-calendar-check" hint="7 hari ke depan" :href="route('admin.inventory')" />
    <x-dash.stat label="Belum dibayar" :value="$k['unpaid']" icon="bi-clock" tone="warning" hint="Perlu verifikasi" :href="route('admin.payments')" />
    <x-dash.stat label="Lunas, belum diambil" :value="$k['paid_not_picked']" icon="bi-box-seam" hint="Siap diserahkan" :href="route('admin.pickups')" />
    <x-dash.stat label="Terlambat" :value="$k['overdue']" icon="bi-exclamation-octagon" tone="danger" :hint="$k['due_soon'].' hampir jatuh tempo'" :href="route('admin.returns')" />
</div>

<div class="dash-grid dash-grid--2 mb-4">
    <x-dash.panel title="Pendapatan rental" subtitle="6 bulan terakhir (data contoh)">
        <x-dash.bars :data="$revenue" title="Grafik batang pendapatan rental per bulan" />
    </x-dash.panel>

    <x-dash.panel title="Perlu tindakan" subtitle="Jatuh tempo dan keterlambatan" flush>
        <ul class="dash-list">
            @forelse ($alerts as $o)
                <li class="dash-list__item">
                    <div class="dash-list__main"><strong>{{ $o['product'] }} <span class="pit-mono">{{ $o['unit'] }}</span></strong><small>{{ $o['customer'] }} · {{ $o['period'] }}</small></div>
                    <x-status-badge group="rental" :status="$o['rental']" />
                </li>
            @empty
                <li class="dash-list__item"><x-empty-state icon="bi-check2-all" title="Tidak ada yang mendesak">Semua penyewaan berjalan sesuai jadwal.</x-empty-state></li>
            @endforelse
        </ul>
    </x-dash.panel>
</div>

<div class="dash-grid dash-grid--even">
    <x-dash.panel title="New Orders" subtitle="Pesanan yang belum diproses" flush>
        <x-slot:actions><a class="pit-link small" href="{{ route('admin.orders.new') }}">Lihat semua</a></x-slot:actions>
        <x-dash.table id="tblNewMini" :searchable="false" caption="New Orders terbaru" :rows="$new" :columns="[
            ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
            ['key'=>'stack','label'=>'Pelanggan','type'=>'stack'],
            ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
        ]" />
    </x-dash.panel>

    <x-dash.panel title="Ongoing Rentals" subtitle="Sedang disewa" flush>
        <x-slot:actions><a class="pit-link small" href="{{ route('admin.rentals.ongoing') }}">Lihat semua</a></x-slot:actions>
        <x-dash.table id="tblOngoingMini" :searchable="false" caption="Ongoing Rentals terbaru" :rows="$ongoing" :columns="[
            ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
            ['key'=>'stack','label'=>'Perangkat','type'=>'stack'],
            ['key'=>'rental','label'=>'Penyewaan','type'=>'status','group'=>'rental'],
        ]" />
    </x-dash.panel>
</div>
@endsection
