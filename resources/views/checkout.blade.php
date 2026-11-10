@extends('layouts.dashboard')
@section('title', 'Pemesanan Sewa')
@section('page_label', 'Pemesanan sewa')

@section('content')
<x-dash.page-header title="Pemesanan sewa" description="Pilih periode dan unit, metode pembayaran, lalu setujui ketentuan. Invoice terbit saat pesanan dibuat, termasuk untuk COD." />

<div class="dash-grid dash-grid--2">
    <div class="d-grid gap-3 align-content-start">

        {{-- Langkah 1: periode & unit (GET agar unit dihitung ulang) --}}
        <x-dash.panel title="1. Produk dan periode">
            <form method="get" action="{{ route('checkout') }}" data-pit-validate novalidate>
                <div class="mb-3">
                    <label for="produk" class="form-label">Produk <span class="pit-required" aria-hidden="true">*</span></label>
                    <select id="produk" name="produk" class="form-select" required>
                        <option value="">Pilih produk</option>
                        @foreach ($products as $p)<option value="{{ $p['slug'] }}" @selected(request('produk') === $p['slug'])>{{ $p['name'] }}</option>@endforeach
                    </select>
                    <div id="produk-error" class="invalid-feedback"></div>
                </div>
                <x-field name="mulai" label="Tanggal mulai" type="date" required :value="request('mulai')" :min="\App\Support\MockAvailability::today()->toDateString()" />
                <div class="row g-3 mb-3">
                    <div class="col-6"><label for="durasi" class="form-label">Durasi</label><input id="durasi" name="durasi" type="number" min="1" max="90" required class="form-control" value="{{ request('durasi', 1) }}"><div id="durasi-error" class="invalid-feedback"></div></div>
                    <div class="col-6"><label for="satuan" class="form-label">Satuan</label>
                        <select id="satuan" name="satuan" class="form-select">@foreach (['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan'] as $v => $l)<option value="{{ $v }}" @selected(request('satuan', 'hari') === $v)>{{ $l }}</option>@endforeach</select></div>
                </div>
                <button type="submit" class="btn btn-outline-primary">Cek unit tersedia</button>
            </form>
        </x-dash.panel>

        {{-- Langkah 2: unit, pembayaran, ketentuan --}}
        <x-dash.panel title="2. Unit, pembayaran, dan ketentuan">
            @if ($units === null)
                <x-empty-state icon="bi-calendar-check" title="Pilih periode terlebih dahulu">Isi produk dan tanggal di atas, lalu tekan "Cek unit tersedia".</x-empty-state>
            @else
                <form method="POST" action="{{ route('checkout.store') }}" data-pit-validate novalidate>
                    @csrf
                    @foreach (['produk', 'mulai', 'durasi', 'satuan'] as $f)<input type="hidden" name="{{ $f }}" value="{{ request($f) }}">@endforeach

                    <fieldset class="mb-4">
                        <legend class="form-label">Unit <span class="pit-required" aria-hidden="true">*</span></legend>
                        @foreach ($units as $u)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="unit" id="u-{{ $u['code'] }}" value="{{ $u['code'] }}" @disabled(! $u['free']) required>
                                <label class="form-check-label" for="u-{{ $u['code'] }}"><span class="pit-mono">{{ $u['code'] }}</span> {!! $u['free'] ? '' : '<span class="text-muted-pit small">(bentrok jadwal)</span>' !!}</label>
                            </div>
                        @endforeach
                        @error('unit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </fieldset>

                    <fieldset class="mb-4">
                        <legend class="form-label">Metode pembayaran <span class="pit-required" aria-hidden="true">*</span></legend>
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode" id="m-qris" value="QRIS" required><label class="form-check-label" for="m-qris">QRIS</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode" id="m-va" value="VA"><label class="form-check-label" for="m-va">Virtual account</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode" id="m-cod" value="COD"><label class="form-check-label" for="m-cod">COD, bayar saat pengambilan</label></div>
                        @error('metode')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </fieldset>

                    <x-terms-checkbox id="terms_accepted" />
                    @error('terms_accepted')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

                    <button type="submit" class="btn btn-primary" data-pit-loading-submit>Buat pesanan</button>
                    <p class="small text-muted-pit mt-3 mb-0">Simulasi: pesanan tidak disimpan dan pembayaran belum diproses.</p>
                </form>
            @endif
        </x-dash.panel>
    </div>

    <x-dash.panel title="Ringkasan">
        @if ($summary)
            <dl class="dash-kv mb-3">
                <dt>Produk</dt><dd>{{ $summary['product']['name'] }}</dd>
                <dt>Mulai</dt><dd>{{ $summary['start'] }}</dd>
                <dt>Selesai</dt><dd>{{ $summary['end'] }}</dd>
                <dt>Durasi</dt><dd>{{ $summary['days'] }} hari</dd>
                <dt>Tarif</dt><dd><x-pending field="checkout.rate" /></dd>
                <dt>Total</dt><dd><x-pending field="checkout.total" /></dd>
            </dl>
        @else
            <p class="text-muted-pit mb-3">Ringkasan muncul setelah periode dipilih.</p>
        @endif
        <p class="small mb-0"><i class="bi bi-geo-alt" aria-hidden="true"></i> Pengambilan di lokasi admin. Tidak ada pengiriman kurir. Lokasi: <x-pending field="terms.pickup_address" /></p>
    </x-dash.panel>
</div>
@endsection
