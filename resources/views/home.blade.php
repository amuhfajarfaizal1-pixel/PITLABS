@extends('layouts.app')

@section('title', 'Sewa Perangkat AI dan IoT')
@section('description', 'Sewa Raspberry Pi 5, Jetson Orin Nano, laptop, drone, kamera mirrorless, dan Google Colab. Pesan online, ambil di lokasi admin, pantau masa sewa dari dashboard.')

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => config('pitlabs.name'),
        'url'      => url('/'),
        'logo'     => asset('assets/img/logo-pitlabs.png'),
        'slogan'   => config('pitlabs.tagline'),
    ];
@endphp

@push('head')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home.css') }}">
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')

@php
    $catalogUrl = \App\Support\Pit::url('catalog.index', '#produk');
    $registerUrl = \App\Support\Pit::url('register');
@endphp

{{-- ===== HERO ===== --}}
<section class="pit-hero pit-on-navy" aria-labelledby="hero-title">
    <div class="container">
        <div class="pit-hero__inner">
            <span class="pit-eyebrow">Penyewaan perangkat AI, IoT, dan teknologi</span>
            <h1 id="hero-title" class="pit-display">Sewa perangkat AI dan IoT untuk proyek Anda berikutnya</h1>
            <p class="pit-lead">Raspberry Pi 5, Jetson Orin Nano, laptop, drone, kamera mirrorless, dan Google Colab. Pesan online, ambil di lokasi admin, pantau masa sewa dari dashboard.</p>

            <div class="pit-hero__cta">
                <a href="{{ $catalogUrl }}" class="btn btn-primary btn-lg">Jelajahi katalog</a>
                <a href="#cara-rental" class="btn btn-on-dark btn-lg">Lihat cara sewa</a>
            </div>
        </div>

        <ul class="pit-trust list-unstyled">
            <li><i class="bi bi-patch-check" aria-hidden="true"></i><span>Kondisi perangkat diperiksa admin saat pengembalian</span></li>
            <li><i class="bi bi-qr-code" aria-hidden="true"></i><span>Invoice dengan QR untuk verifikasi pengambilan</span></li>
            <li><i class="bi bi-layout-split" aria-hidden="true"></i><span>Status pembayaran dan pengambilan ditampilkan terpisah</span></li>
        </ul>
    </div>
</section>

{{-- ===== PRODUK ===== --}}
<section class="pit-section" id="produk" aria-labelledby="produk-title">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div>
                <span class="pit-eyebrow">Katalog</span>
                <h2 id="produk-title" class="mb-0">Tujuh perangkat dan layanan</h2>
            </div>

            <div class="pit-filter" role="group" aria-label="Filter jenis produk"
                 data-pit-filter="#productGrid" data-pit-filter-status="#productCount">
                <button type="button" class="pit-filter__btn" data-filter="all" aria-pressed="true">Semua</button>
                <button type="button" class="pit-filter__btn" data-filter="physical" aria-pressed="false">Perangkat fisik</button>
                <button type="button" class="pit-filter__btn" data-filter="digital" aria-pressed="false">Layanan digital</button>
            </div>
        </div>

        <p id="productCount" class="visually-hidden" role="status" aria-live="polite"></p>

        <div class="row g-3 g-lg-4" id="productGrid">
            @foreach ($products as $product)
                <div class="col-sm-6 col-lg-4 col-xl-3" data-type="{{ $product['type'] }}">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>

        <p class="text-muted-pit small mt-4 mb-0">Foto, spesifikasi, dan harga ditampilkan setelah data produk dilengkapi. Tombol Beli dan Sewa tersedia di halaman detail tiap produk.</p>
    </div>
</section>

{{-- ===== CARA RENTAL ===== --}}
<section class="pit-section pit-section--alt" id="cara-rental" aria-labelledby="cara-title">
    <div class="container">
        <span class="pit-eyebrow">Cara sewa</span>
        <h2 id="cara-title" class="mb-2">Dari memilih sampai mengembalikan</h2>
        <p class="text-muted-pit pit-prose mb-5">Pengambilan dan pengembalian dilakukan langsung di lokasi admin. Tidak ada pengiriman kurir.</p>

        <ol class="pit-steps">
            <li class="pit-step">
                <span class="pit-step__num" aria-hidden="true">1</span>
                <h3 class="pit-step__title">Pilih dan cek ketersediaan</h3>
                <p>Pilih perangkat, lalu tentukan tanggal mulai dan durasi sewa.</p>
            </li>
            <li class="pit-step">
                <span class="pit-step__num" aria-hidden="true">2</span>
                <h3 class="pit-step__title">Masuk dan buat pesanan</h3>
                <p>Login, setujui ketentuan sewa, lalu pilih QRIS, virtual account, atau COD.</p>
            </li>
            <li class="pit-step">
                <span class="pit-step__num" aria-hidden="true">3</span>
                <h3 class="pit-step__title">Bayar dan terima invoice</h3>
                <p>Invoice dengan QR dibuat sejak pesanan terbentuk. Status pembayaran dan pengambilan tercatat terpisah.</p>
            </li>
            <li class="pit-step">
                <span class="pit-step__num" aria-hidden="true">4</span>
                <h3 class="pit-step__title">Ambil di lokasi admin</h3>
                <p>Tunjukkan QR invoice. Admin memeriksa identitas dan status pesanan, lalu mencatat pengambilan.</p>
            </li>
            <li class="pit-step">
                <span class="pit-step__num" aria-hidden="true">5</span>
                <h3 class="pit-step__title">Kembalikan dan diperiksa</h3>
                <p>Admin memeriksa kondisi dan kelengkapan. Jika tidak ada masalah, sewa selesai.</p>
            </li>
        </ol>
    </div>
</section>

@include('partials.ketentuan-sewa')

{{-- ===== FAQ RINGKAS ===== --}}
<section class="pit-section pit-section--alt" id="faq" aria-labelledby="faq-title">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="pit-eyebrow">Pertanyaan umum</span>
                <h2 id="faq-title" class="mb-3">Yang sering ditanyakan</h2>
                @if ($faqUrl = \App\Support\Pit::url('faq'))
                    <a href="{{ $faqUrl }}" class="btn btn-outline-primary">Lihat semua FAQ</a>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="accordion accordion-flush pit-accordion" id="faqList">

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">Apakah perangkat dikirim ke alamat saya?</button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqList">
                            <div class="accordion-body">Tidak. Pengambilan dan pengembalian dilakukan langsung di lokasi admin. PITLABS tidak menyediakan pengiriman kurir.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">Pembayaran sudah berhasil, apakah barang langsung bisa dipakai?</button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqList">
                            <div class="accordion-body">Belum. Pembayaran berhasil tidak berarti barang sudah diambil. Tunjukkan QR invoice kepada admin di lokasi pengambilan; status pembayaran dan pengambilan dicatat terpisah.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">Siapa yang menentukan denda kerusakan?</button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqList">
                            <div class="accordion-body">Admin, setelah memeriksa perangkat yang dikembalikan dan mencatat buktinya. Nominal per kategori kerusakan mengikuti ketentuan mitra. Lihat <a class="pit-link" href="#ketentuan-sewa">ketentuan sewa dan denda</a>.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">Apa bedanya sewa dengan beli via Shopee?</button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqList">
                            <div class="accordion-body">Sewa diproses di PITLABS: pesan, bayar, lalu ambil di lokasi admin. Pembelian dilakukan dan diselesaikan di Shopee. Tombol Beli dan Sewa ada di halaman detail produk, dan hanya muncul sesuai produk yang memang disewakan atau dijual.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">Bagaimana dengan Google Colab Pro dan Pro Plus?</button>
                        </h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqList">
                            <div class="accordion-body">Keduanya layanan digital, bukan perangkat fisik. Alur aktivasi dan ketentuan khususnya: <x-pending field="faq.colab_flow" />.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA PENUTUP ===== --}}
<section class="pit-section pit-section--navy pit-on-navy" aria-labelledby="cta-title">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h2 id="cta-title" class="mb-2">Mulai dari katalog</h2>
                <p class="mb-0 pit-lead">Lihat perangkat yang tersedia, cek tanggal sewa, lalu buat pesanan dari dashboard Anda.</p>
            </div>
            <div class="col-lg-5">
                <div class="d-grid d-sm-flex gap-3 justify-content-lg-end">
                    <a href="{{ $catalogUrl }}" class="btn btn-primary btn-lg">Jelajahi katalog</a>
                    @if ($registerUrl)
                        <a href="{{ $registerUrl }}" class="btn btn-on-dark btn-lg">Buat akun</a>
                    @else
                        <span class="btn btn-on-dark btn-lg disabled" aria-disabled="true" title="Halaman registrasi dikerjakan di fase berikutnya">Buat akun</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
