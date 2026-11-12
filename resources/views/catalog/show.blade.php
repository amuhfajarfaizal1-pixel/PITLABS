@extends('layouts.app')

@section('title', $product['name'].' — Sewa dan Beli')
@section('description', 'Sewa atau beli '.$product['name'].' di PITLABS. Cek ketersediaan, ketentuan sewa, dan lokasi pengambilan sebelum memesan.')

@push('head')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/catalog.css') }}">
@endpush

@section('content')

@php
    $isDigital = $product['type'] === 'digital';
    $rentUrl   = route('availability', ['produk' => $product['slug']]);
@endphp

<section class="pit-section pt-4">
    <div class="container">

        <x-breadcrumb :items="[['Beranda', route('home')], ['Katalog', route('catalog.index')], [$product['name'], null]]" />

        <div class="row g-4 g-lg-5">

            {{-- Media --}}
            <div class="col-lg-6">
                <div class="pit-detail__media pit-product__media--{{ $product['type'] }}" role="img" aria-label="Foto {{ $product['name'] }} belum tersedia">
                    <i class="bi {{ $product['icon'] }}" aria-hidden="true"></i>
                </div>
                <p class="small text-muted-pit mt-2 mb-0">Galeri foto: <x-pending :field="'product.'.$product['slug'].'.photos'" /></p>
            </div>

            {{-- Ringkasan & aksi --}}
            <div class="col-lg-6">
                <p class="pit-product__type mb-2">
                    <i class="bi {{ $isDigital ? 'bi-cloud' : 'bi-box-seam' }}" aria-hidden="true"></i>
                    {{ $isDigital ? 'Layanan digital' : 'Perangkat fisik' }}
                </p>
                <h1 class="mb-3">{{ $product['name'] }}</h1>

                <div class="pit-price">
                    @if ($product['rental_enabled'])
                        <div class="pit-price__row">
                            <span>Harga sewa</span>
                            @if ($product['rental_daily_rate'] !== null)<strong>{{ \App\Support\Pit::rupiah($product['rental_daily_rate']) }}</strong> / hari @else<x-pending :field="'product.'.$product['slug'].'.rental_daily_rate'" />@endif
                        </div>
                    @endif
                    @if ($product['sale_enabled'])
                        <div class="pit-price__row">
                            <span>Harga jual</span>
                            @if ($product['sale_price'] !== null)<strong>{{ \App\Support\Pit::rupiah($product['sale_price']) }}</strong> @else<x-pending :field="'product.'.$product['slug'].'.sale_price'" />@endif
                        </div>
                    @endif
                </div>

                {{-- CTA sejajar. Produk jual-saja atau sewa-saja hanya menampilkan CTA yang berlaku. --}}
                <div class="d-grid d-sm-flex gap-3 mb-3">
                    @if ($product['sale_enabled'])
                        <button type="button" class="btn btn-shopee btn-lg flex-sm-fill" data-bs-toggle="modal" data-bs-target="#shopeeModal">
                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Beli via Shopee
                        </button>
                    @endif
                    @if ($product['rental_enabled'])
                        <a href="{{ $rentUrl }}" class="btn btn-primary btn-lg flex-sm-fill">Sewa sekarang</a>
                    @endif
                </div>

                <p class="small text-muted-pit mb-4">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    Pengambilan dan pengembalian dilakukan langsung di lokasi admin. Lihat
                    <a class="pit-link" href="{{ route('home') }}#ketentuan-sewa">ketentuan sewa dan denda</a>.
                </p>

                <nav class="pit-anchors" aria-label="Isi halaman">
                    <a href="#deskripsi">Deskripsi</a>
                    <a href="#spesifikasi">Spesifikasi</a>
                    <a href="#fungsi">Fungsi</a>
                    <a href="#panduan">Panduan</a>
                    <a href="#unit">{{ $isDigital ? 'Ketersediaan' : 'Unit rental' }}</a>
                </nav>
            </div>
        </div>

        {{-- Konten detail --}}
        <div class="row g-4 g-lg-5 mt-1">
            <div class="col-lg-8">
                <section id="deskripsi" class="pit-detail__section">
                    <h2>Deskripsi</h2>
                    @if ($product['description'])
                        <p class="pit-prose">{{ $product['description'] }}</p>
                    @else
                        <p><x-pending :field="'product.'.$product['slug'].'.description'" /></p>
                    @endif
                </section>

                <section id="spesifikasi" class="pit-detail__section">
                    <h2>Spesifikasi</h2>
                    @if (count($product['specifications']))
                        <table class="table pit-spec-table">
                            <tbody>
                                @foreach ($product['specifications'] as $label => $value)
                                    <tr><th scope="row">{{ $label }}</th><td>{{ $value }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p><x-pending :field="'product.'.$product['slug'].'.specifications'" /></p>
                    @endif
                </section>

                <section id="fungsi" class="pit-detail__section">
                    <h2>Fungsi</h2>
                    <p><x-pending :field="'product.'.$product['slug'].'.functions'" /></p>
                </section>

                <section id="panduan" class="pit-detail__section">
                    <h2>Panduan penggunaan</h2>
                    @if ($product['usage_guide'])
                        <p class="pit-prose">{{ $product['usage_guide'] }}</p>
                    @else
                        <p><x-pending :field="'product.'.$product['slug'].'.usage_guide'" /></p>
                    @endif
                </section>

                <section id="unit" class="pit-detail__section">
                    @if ($isDigital)
                        <h2>Ketersediaan</h2>
                        <p class="mb-2">Layanan digital tidak memakai unit fisik.</p>
                        <p class="mb-0">Ketersediaan slot dan cara aktivasi: <x-pending :field="'product.'.$product['slug'].'.digital_slots'" /></p>
                    @else
                        <h2>Unit rental</h2>
                        <p class="text-muted-pit">Status hari ini. Ketersediaan untuk tanggal tertentu dihitung dari jadwal booking di <a class="pit-link" href="{{ $rentUrl }}">cek ketersediaan</a>.</p>
                        <div class="table-responsive">
                            <table class="table align-middle pit-unit-table">
                                <caption class="visually-hidden">Daftar unit rental {{ $product['name'] }}</caption>
                                <thead><tr><th scope="col">Kode unit</th><th scope="col">Status hari ini</th></tr></thead>
                                <tbody>
                                    @foreach ($units as $unit)
                                        <tr>
                                            <td><span class="pit-mono">{{ $unit['code'] }}</span></td>
                                            <td><x-status-badge group="unit" :status="$unit['status']" /></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-muted-pit mb-0">Data unit di atas adalah contoh untuk tampilan. Data asli diisi admin pada Fase 5.</p>
                    @endif
                </section>
            </div>

            {{-- Produk lain (tautan internal) --}}
            <aside class="col-lg-4" aria-labelledby="lainnya-title">
                <h2 id="lainnya-title" class="h4 mb-3">Produk lainnya</h2>
                <div class="d-grid gap-3">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary w-100 mt-3">Lihat seluruh katalog</a>
            </aside>
        </div>
    </div>
</section>

{{-- Konfirmasi sebelum meninggalkan PITLABS --}}
@if ($product['sale_enabled'])
    <x-modal id="shopeeModal" title="Lanjutkan ke Shopee?">
        <p class="mb-2">Anda akan membuka halaman {{ $product['name'] }} di Shopee. Pembelian diselesaikan di Shopee, bukan di PITLABS.</p>
        @unless ($product['shopee_url'])
            <p class="mb-0 small text-muted-pit">Tautan Shopee: <x-pending :field="'product.'.$product['slug'].'.shopee_url'" /></p>
        @endunless

        <x-slot:footer>
            <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button>
            @if ($product['shopee_url'])
                <a href="{{ $product['shopee_url'] }}" class="btn btn-shopee" target="_blank" rel="noopener noreferrer sponsored">Lanjutkan ke Shopee</a>
            @else
                <button type="button" class="btn btn-shopee" disabled>Lanjutkan ke Shopee</button>
            @endif
        </x-slot:footer>
    </x-modal>
@endif

@endsection
