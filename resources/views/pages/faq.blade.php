@extends('layouts.app')
@section('title', 'FAQ — Pertanyaan yang Sering Diajukan')
@section('description', 'Jawaban atas pertanyaan umum tentang menyewa dan membeli perangkat di PITLABS: pengambilan, pembayaran, COD, dan denda kerusakan.')
@php
    $faqs = [
        ['Bagaimana cara membeli produk?', 'Buka detail produk dan pilih Beli via Shopee. Anda akan diarahkan ke toko setelah konfirmasi. Pembelian diselesaikan di Shopee.'],
        ['Bagaimana cara menyewa perangkat?', 'Login, pilih produk, periksa ketersediaan, tentukan durasi, lalu buat pesanan.'],
        ['Apakah alat dikirim oleh kurir?', 'Tidak. Pengambilan dan pengembalian dilakukan langsung di alamat admin.'],
        ['Bagaimana status pembayaran COD?', 'Invoice dibuat dengan status Belum Dibayar. Admin mengubah status menjadi Lunas setelah menerima uang.'],
        ['Pembayaran sudah berhasil, apakah barang langsung bisa dipakai?', 'Belum. Pembayaran berhasil tidak berarti barang sudah diambil. Tunjukkan QR invoice kepada admin; status pembayaran dan pengambilan dicatat terpisah.'],
        ['Bagaimana jika perangkat rusak?', 'Admin memeriksa kondisi sebelum dan sesudah sewa, lalu menetapkan denda sesuai kebijakan mitra.'],
        ['Bagaimana dengan Google Colab Pro dan Pro Plus?', 'Keduanya layanan digital, bukan perangkat fisik. Alur aktivasi dan ketentuan khususnya menunggu informasi dari mitra.'],
    ];
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $faqs)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush



@section('content')
<section class="pit-section pt-4">
    <div class="container">
        <x-breadcrumb :items="[['Beranda', route('home')], ['FAQ', null]]" />
        <span class="pit-eyebrow">Pusat bantuan</span>
        <h1 class="mb-4">Pertanyaan yang sering diajukan</h1>

        <div class="row"><div class="col-lg-8">
            <div class="accordion accordion-flush pit-accordion" id="faqAll">
                @foreach ($faqs as $i => [$q, $a])
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button @unless($i === 0) collapsed @endunless" type="button" data-bs-toggle="collapse" data-bs-target="#f{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="f{{ $i }}">{{ $q }}</button>
                        </h2>
                        <div id="f{{ $i }}" class="accordion-collapse collapse @if($i === 0) show @endif" data-bs-parent="#faqAll"><div class="accordion-body">{{ $a }}</div></div>
                    </div>
                @endforeach
            </div>
            <p class="mt-4 text-muted-pit">Pertanyaan lain? Kontak admin: <x-pending field="contact.phone" />. Lihat juga <a class="pit-link" href="{{ route('guide') }}#ketentuan-sewa">ketentuan sewa dan denda</a>.</p>
        </div></div>
    </div>
</section>
@endsection
