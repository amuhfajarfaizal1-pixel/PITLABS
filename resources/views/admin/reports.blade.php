@extends('layouts.dashboard')
@section('title', 'Laporan Keuangan')
@section('page_label', 'Laporan keuangan')

@section('content')
<x-dash.page-header title="Laporan keuangan" description="Pendapatan rental internal dipisahkan dari penjualan eksternal. Pembayaran yang belum lunas bukan pendapatan kas diterima, dan denda dicatat terpisah.">
    <x-slot:actions><button type="button" class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer" aria-hidden="true"></i> Cetak</button></x-slot:actions>
</x-dash.page-header>

<form class="dash-panel p-3 mb-4 d-flex flex-wrap gap-3 align-items-end" data-pit-validate data-pit-simulate novalidate aria-label="Filter periode laporan">
    <div>
        <label for="rPeriod" class="form-label">Periode</label>
        <select id="rPeriod" class="form-select">
            <option>Harian</option><option>Mingguan</option><option selected>Bulanan</option><option>Tahunan</option><option>Rentang tanggal</option>
        </select>
    </div>
    <div><label for="rFrom" class="form-label">Dari</label><input id="rFrom" type="date" class="form-control"></div>
    <div><label for="rTo" class="form-label">Sampai</label><input id="rTo" type="date" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Terapkan</button>
    <div data-pit-form-status hidden tabindex="-1" class="small text-muted-pit">Filter hanya simulasi. Data contoh tidak berubah. Zona waktu laporan: Asia/Makassar.</div>
</form>

<div class="dash-grid dash-grid--stats mb-4">
    <x-dash.stat label="Pendapatan rental diterima" :value="\App\Support\Pit::rupiah($received)" icon="bi-cash-stack" tone="success" hint="Pembayaran berstatus lunas" />
    <x-dash.stat label="Belum lunas" :value="\App\Support\Pit::rupiah($unpaid)" icon="bi-clock-history" tone="warning" hint="Bukan pendapatan kas" />
    <x-dash.stat label="Denda" value="Belum tersedia" icon="bi-exclamation-diamond" hint="Menunggu ketentuan mitra" />
    <x-dash.stat label="Penjualan Shopee" value="Belum tersedia" icon="bi-bag" hint="Diinput atau diimpor terpisah" />
</div>

<div class="dash-grid dash-grid--2">
    <x-dash.panel title="Pendapatan rental per bulan" subtitle="Data contoh"><x-dash.bars :data="$revenue" title="Grafik batang pendapatan rental per bulan" /></x-dash.panel>
    <x-dash.panel title="Ringkasan" subtitle="Bagaimana angka dihitung">
        <dl class="dash-kv mb-0">
            <dt>Rental internal</dt><dd>Pesanan dengan pembayaran lunas.</dd>
            <dt>Penjualan eksternal</dt><dd>Dicatat terpisah (diinput/impor), tidak dicampur dengan rental.</dd>
            <dt>Refund</dt><dd>Dicatat sebagai arus keluar atau penyesuaian.</dd>
            <dt>Denda</dt><dd>Dicatat terpisah dari biaya sewa.</dd>
        </dl>
    </x-dash.panel>
</div>

<x-dash.panel title="Rincian transaksi" subtitle="Data contoh" class="mt-4" flush>
    <x-dash.table id="tblRep" caption="Rincian transaksi" placeholder="Cari invoice atau pelanggan…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],
        ['key'=>'method','label'=>'Metode'],
        ['key'=>'total','label'=>'Total','type'=>'money'],
        ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
    ]" />
</x-dash.panel>
@endsection
