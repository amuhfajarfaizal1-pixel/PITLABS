@extends('layouts.dashboard')
@section('title', 'Invoice '.$o['invoice'])
@section('page_label', 'Invoice')

@section('content')
<div class="no-print">
    <x-dash.page-header title="Invoice" description="Dokumen resmi pesanan. Dapat dicetak atau disimpan sebagai PDF lewat dialog cetak.">
        <x-slot:actions><button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer" aria-hidden="true"></i> Cetak invoice</button></x-slot:actions>
    </x-dash.page-header>
</div>

<article class="pit-invoice" aria-label="Invoice {{ $o['invoice'] }}">
    <div class="pit-invoice__top">
        <div>
            <img src="{{ asset('assets/img/logo-pitlabs.png') }}" alt="PITLABS" width="156" height="52">
            <p class="mb-0 mt-2 fw-semibold">{{ config('pitlabs.company') }}</p>
            <p class="mb-0 small text-muted-pit">{{ url('/') }}</p>
        </div>
        <div class="text-end">
            <p class="mb-1 small text-muted-pit">Nomor invoice</p>
            <p class="pit-mono fs-5 fw-semibold mb-2">{{ $o['invoice'] }}</p>
            <p class="mb-0 small">Tanggal terbit: {{ $issued }}</p>
        </div>
    </div>

    <div class="pit-invoice__meta">
        <div><h3>Pelanggan</h3><p class="mb-0 fw-semibold">{{ $o['customer'] }}</p></div>
        <div><h3>Lokasi pengambilan</h3><p class="mb-0"><x-pending field="terms.pickup_address" /></p></div>
        <div><h3>Metode pembayaran</h3><p class="mb-0 fw-semibold">{{ $o['method'] }}</p></div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <caption class="visually-hidden">Rincian pesanan</caption>
            <thead><tr><th scope="col">Produk</th><th scope="col">Unit</th><th scope="col">Mulai – selesai</th><th scope="col" class="text-end">Qty</th><th scope="col" class="text-end">Durasi</th><th scope="col" class="text-end">Harga</th></tr></thead>
            <tbody>
                <tr>
                    <td class="fw-semibold">{{ $o['product'] }}</td>
                    <td><span class="pit-mono">{{ $o['unit'] }}</span></td>
                    <td>{{ $o['start_full'] }} – {{ $o['end_full'] }}</td>
                    <td class="text-end">1</td>
                    <td class="text-end">{{ $o['days'] }} hari</td>
                    <td class="text-end">@if ($o['total'] !== null){{ \App\Support\Pit::rupiah($o['total']) }}@else<x-pending field="invoice.price" />@endif</td>
                </tr>
            </tbody>
        </table>
    </div>

    <dl class="dash-kv" style="max-width: 24rem; margin-left: auto">
        <dt>Biaya tambahan</dt><dd class="text-end">@if ($o['total'] !== null)Rp 0@else<x-pending field="invoice.fees" />@endif</dd>
    </dl>
    <div class="pit-invoice__total"><span>Total</span><span>@if ($o['total'] !== null){{ \App\Support\Pit::rupiah($o['total']) }}@else<x-pending field="invoice.total" />@endif</span></div>

    <h2 class="h6 mt-4 mb-2">Status (dicatat terpisah)</h2>
    <div class="dash-statusgrid mb-4">
        <div><small>Pembayaran</small><x-status-badge group="payment" :status="$o['payment']" /></div>
        <div><small>Pengambilan</small><x-status-badge group="pickup" :status="$o['pickup']" /></div>
        <div><small>Penyewaan</small><x-status-badge group="rental" :status="$o['rental']" /></div>
    </div>

    <div class="d-flex flex-wrap gap-4 align-items-start">
        <figure class="mb-0">
            <svg class="pit-invoice__qr" viewBox="0 0 25 25" role="img" aria-label="Contoh tampilan QR verifikasi invoice (belum dapat dipindai)" shape-rendering="crispEdges">
                @foreach ($qr as $y => $row)@foreach ($row as $x => $on)@if ($on)<rect x="{{ $x }}" y="{{ $y }}" width="1" height="1"/>@endif @endforeach @endforeach
            </svg>
            <figcaption class="small text-muted-pit mt-1">QR verifikasi (contoh tampilan)</figcaption>
        </figure>
        <div class="flex-fill" style="min-width: 15rem">
            <h2 class="h6 mb-2">Petunjuk</h2>
            <ul class="mb-0 small">
                <li>Pengambilan dan pengembalian dilakukan langsung di lokasi admin. Tidak ada pengiriman kurir.</li>
                <li>Tunjukkan QR atau nomor invoice dan identitas kepada admin saat pengambilan.</li>
                <li>Pembayaran berhasil tidak berarti barang sudah diambil.</li>
                <li>Denda kerusakan ditetapkan setelah pemeriksaan admin sesuai ketentuan mitra.</li>
            </ul>
        </div>
    </div>
</article>

<div class="text-center mt-4 no-print"><a href="{{ auth()->user()->isAdmin() ? route('admin.orders.new') : route('dashboard.history') }}" class="btn btn-soft">Kembali</a></div>
@endsection
