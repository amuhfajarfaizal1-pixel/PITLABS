@extends('layouts.app')
@section('title', 'Asisten AI — Segera Hadir')
@section('description', 'AI Technical Assistant PITLABS membantu memilih perangkat yang cocok untuk proyek Anda. Fitur ini segera hadir.')
@push('head')<meta name="robots" content="noindex, follow">@endpush

@section('content')
<section class="pit-section pt-4">
    <div class="container">
        <x-breadcrumb :items="[['Beranda', route('home')], ['Asisten AI', null]]" />

        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
            <span class="pit-eyebrow mb-0">PITLABS Smart Assistant</span>
            <span class="pit-tag-soon">Segera Hadir</span>
        </div>
        <h1 class="mb-2">Asisten pemilihan perangkat</h1>
        <p class="pit-lead mb-5">Ceritakan proyek atau masalah teknis Anda, lalu asisten akan mencocokkan kebutuhan dengan katalog. Fitur ini sedang disiapkan dan belum aktif.</p>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="pit-card" aria-label="Pratinjau asisten AI (belum aktif)">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="pit-tile" aria-hidden="true"><i class="bi bi-stars"></i></span>
                        <div><strong class="d-block">AI Technical Assistant</strong><small class="text-muted-pit">Rekomendasi perangkat AI dan IoT</small></div>
                    </div>
                    <x-alert type="info" class="mb-3">Pratinjau tampilan. Kolom di bawah belum dapat dipakai.</x-alert>
                    <label for="aiQuestion" class="form-label">Kebutuhan proyek Anda</label>
                    <textarea id="aiQuestion" class="form-control mb-3" rows="3" placeholder="Contoh: prototipe computer vision untuk tugas akhir" disabled></textarea>
                    <button type="button" class="btn btn-primary" disabled>Kirim</button>
                    <span class="small text-muted-pit ms-2">Tersedia setelah fitur AI diaktifkan.</span>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="pit-card">
                    <h2 class="h5">Sementara itu</h2>
                    <p class="text-muted-pit">Lihat katalog, bandingkan perangkat, dan cek ketersediaan untuk tanggal yang Anda butuhkan.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">Jelajahi katalog</a>
                        <a href="{{ route('availability') }}" class="btn btn-outline-primary">Cek ketersediaan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
