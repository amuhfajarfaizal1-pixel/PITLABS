@extends('layouts.app')
@section('title', 'Panduan Menyewa Perangkat')
@section('description', 'Panduan lengkap menyewa perangkat PITLABS: cara memesan, mengambil, mengembalikan, serta ketentuan sewa dan denda kerusakan/kehilangan.')
@push('head')<link rel="stylesheet" href="{{ asset('assets/css/pages/home.css') }}">@endpush

@section('content')
<section class="pit-section pt-4">
    <div class="container">
        <x-breadcrumb :items="[['Beranda', route('home')], ['Panduan', null]]" />
        <span class="pit-eyebrow">Panduan</span>
        <h1 class="mb-2">Cara menyewa di PITLABS</h1>
        <p class="text-muted-pit pit-prose mb-5">Dari memilih perangkat sampai pengembalian. Pengambilan dan pengembalian dilakukan langsung di lokasi admin, tanpa kurir.</p>

        <ol class="pit-steps">
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">1</span><h2 class="pit-step__title">Pilih dan cek ketersediaan</h2><p>Buka <a class="pit-link" href="{{ route('catalog.index') }}">katalog</a>, pilih produk, lalu <a class="pit-link" href="{{ route('availability') }}">cek ketersediaan</a> untuk tanggal dan durasi sewa.</p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">2</span><h2 class="pit-step__title">Masuk dan buat pesanan</h2><p>Login atau daftar, pilih unit, metode pembayaran, dan setujui ketentuan sewa.</p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">3</span><h2 class="pit-step__title">Bayar dan terima invoice</h2><p>Invoice dengan QR terbit sejak pesanan dibuat. COD berstatus Belum Dibayar sampai admin menerima uang.</p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">4</span><h2 class="pit-step__title">Ambil di lokasi admin</h2><p>Tunjukkan QR invoice dan identitas. Pembayaran berhasil tidak berarti barang sudah diambil.</p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">5</span><h2 class="pit-step__title">Kembalikan dan diperiksa</h2><p>Admin memeriksa kondisi. Jika tidak ada masalah, sewa selesai.</p></li>
        </ol>
    </div>
</section>

@include('partials.ketentuan-sewa')
@endsection
