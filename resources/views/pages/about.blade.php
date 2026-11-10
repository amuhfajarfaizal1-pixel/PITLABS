@extends('layouts.app')
@section('title', 'Tentang Kami')
@section('description', 'PITLABS adalah platform penyewaan perangkat AI, IoT, dan teknologi dari PT. Pulau Inovasi Teknologi untuk proyek, riset, dan pembelajaran.')

@section('content')
<section class="pit-section pt-4">
    <div class="container">
        <x-breadcrumb :items="[['Beranda', route('home')], ['Tentang kami', null]]" />
        <span class="pit-eyebrow">Tentang PITLABS</span>
        <h1 class="mb-3">Teknologi tepat untuk ide yang lebih besar</h1>
        <p class="pit-lead mb-5">PITLABS memudahkan mahasiswa, peneliti, dan pembuat proyek mengakses perangkat AI, IoT, dan teknologi lewat penyewaan yang tertata: pesan online, ambil di lokasi admin, dan pantau masa sewa dari dashboard.</p>

        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-4"><div class="pit-card"><h2 class="h5">Masalah yang diselesaikan</h2><p class="mb-0 text-muted-pit">Pemesanan manual lewat chat memakan waktu dan rawan keliru. PITLABS merapikan ketersediaan, pembayaran, pengambilan, dan pengembalian dalam satu alur.</p></div></div>
            <div class="col-md-6 col-lg-4"><div class="pit-card"><h2 class="h5">Dua cara memperoleh</h2><p class="mb-0 text-muted-pit">Sewa diproses di PITLABS. Pembelian dilakukan di Shopee dari halaman detail produk.</p></div></div>
            <div class="col-md-12 col-lg-4"><div class="pit-card"><h2 class="h5">Mitra</h2><p class="mb-0">{{ config('pitlabs.company') }}. Lokasi: <x-pending field="contact.address" />.</p></div></div>
        </div>

        <h2 class="h4 mb-3">Tim pengembang</h2>
        <p class="text-muted-pit mb-3">Proyek PjBL kelompok FAR STAR, Web Design and Programming, D4 Teknik Komputer dan Jaringan, Politeknik Negeri Ujung Pandang.</p>
        <div class="row g-3">
            @foreach ([["Ahmad Amar Ma'ruf", '47125050'], ['Fajar', '47125017'], ['Rislam', '47125015']] as [$n, $nim])
                <div class="col-sm-6 col-lg-4"><div class="pit-card d-flex align-items-center gap-3"><span class="dash-avatar" style="width:44px;height:44px" aria-hidden="true">{{ mb_substr($n, 0, 1) }}</span><div><strong class="d-block">{{ $n }}</strong><span class="pit-mono small text-muted-pit">{{ $nim }}</span></div></div></div>
            @endforeach
        </div>
    </div>
</section>
@endsection
