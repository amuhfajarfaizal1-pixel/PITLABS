@extends('layouts.app')
@section('title', 'Artikel dan Tutorial')
@section('description', 'Panduan dan tutorial dasar penggunaan perangkat AI dan IoT dari PITLABS.')

@section('content')
<section class="pit-section pt-4">
    <div class="container">
        <x-breadcrumb :items="[['Beranda', route('home')], ['Artikel', null]]" />
        <span class="pit-eyebrow">Knowledge center</span>
        <h1 class="mb-2">Panduan dan tutorial</h1>
        <p class="text-muted-pit pit-prose mb-4">Informasi dasar penggunaan perangkat AI dan IoT.</p>

        <div class="row g-4">
            @foreach ([['AI Computing', 'Persiapan perangkat komputasi AI', 'Kenali kebutuhan daya, pendinginan, sistem operasi, dan setup awal.'], ['IoT', 'Memulai proyek IoT', 'Dasar konektivitas Wi-Fi, mikrokontroler, dan pengiriman data.']] as [$tag, $title, $desc])
                <div class="col-md-6 col-lg-4">
                    <article class="pit-card">
                        <span class="pit-eyebrow mb-1">{{ $tag }}</span>
                        <h2 class="h5">{{ $title }}</h2>
                        <p class="text-muted-pit mb-3">{{ $desc }}</p>
                        <span class="small text-muted-pit">Artikel lengkap menyusul</span>
                    </article>
                </div>
            @endforeach
        </div>
        <p class="small text-muted-pit mt-4 mb-0">Tautan rujukan eksternal ditambahkan saat isi artikel ditulis.</p>
    </div>
</section>
@endsection
