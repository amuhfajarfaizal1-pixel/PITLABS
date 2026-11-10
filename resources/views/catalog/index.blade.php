@extends('layouts.app')

@section('title', 'Katalog Perangkat dan Layanan')
@section('description', 'Telusuri katalog PITLABS: Laptop, Raspberry Pi 5, Jetson Orin Nano, drone, kamera mirrorless, dan Google Colab. Cari berdasarkan nama, jenis, dan ketersediaan sewa.')

@push('head')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/catalog.css') }}">
@endpush

@section('content')
<section class="pit-section pt-4">
    <div class="container">

        <x-breadcrumb :items="[['Beranda', route('home')], ['Katalog', null]]" />

        <h1 class="mb-2">Katalog perangkat dan layanan</h1>
        <p class="text-muted-pit pit-prose mb-4">Cari berdasarkan nama atau jenis. Tombol Beli dan Sewa ada di halaman detail setiap produk.</p>

        {{-- Pencarian & filter (GET, sehingga hasil bisa dibagikan lewat tautan) --}}
        <form method="get" action="{{ route('catalog.index') }}" class="pit-filterbar" role="search" aria-label="Cari dan saring produk" data-pit-validate>
            <div class="pit-filterbar__field pit-filterbar__field--grow">
                <label for="q" class="form-label">Nama produk</label>
                <input type="search" id="q" name="q" value="{{ $q }}" maxlength="80" class="form-control" placeholder="Contoh: Raspberry">
            </div>

            <div class="pit-filterbar__field">
                <label for="jenis" class="form-label">Jenis</label>
                <select id="jenis" name="jenis" class="form-select">
                    <option value="">Semua jenis</option>
                    <option value="physical" @selected($jenis === 'physical')>Perangkat fisik</option>
                    <option value="digital" @selected($jenis === 'digital')>Layanan digital</option>
                </select>
            </div>

            <div class="pit-filterbar__field pit-filterbar__field--check">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tersedia" name="tersedia" value="1" @checked($tersedia)>
                    <label class="form-check-label" for="tersedia">Ada unit tersedia hari ini</label>
                </div>
            </div>

            <div class="pit-filterbar__actions">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($q !== '' || $jenis || $tersedia)
                    <a href="{{ route('catalog.index') }}" class="btn btn-soft">Reset</a>
                @endif
            </div>
        </form>

        <p class="small text-muted-pit mt-2 mb-4">
            Filter harga dan spesifikasi aktif setelah datanya tersedia: <x-pending field="catalog.price_spec_filters" />.
            Filter ketersediaan hanya berlaku untuk perangkat fisik.
        </p>

        <p class="fw-semibold mb-3" role="status">Menampilkan {{ count($products) }} dari {{ $total }} produk</p>

        @if (count($products))
            <div class="row g-3 g-lg-4">
                @foreach ($products as $product)
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        @else
            <x-empty-state icon="bi-search" title="Produk tidak ditemukan">
                Tidak ada produk yang cocok dengan pencarian atau filter Anda. Coba kata kunci lain atau hapus filter.
                <x-slot:action>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary">Tampilkan semua produk</a>
                </x-slot:action>
            </x-empty-state>
        @endif
    </div>
</section>
@endsection
