@extends('layouts.app')

@section('title', 'Cek Ketersediaan Sewa')
@section('description', 'Cek ketersediaan perangkat PITLABS untuk tanggal dan durasi sewa yang Anda pilih sebelum membuat pesanan.')

@push('head')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/catalog.css') }}">
@endpush

@section('content')

@php
    $selected = request('produk');
    $checkoutUrl = \App\Support\Pit::url('checkout');
@endphp

<section class="pit-section pt-4">
    <div class="container">

        <x-breadcrumb :items="[['Beranda', route('home')], ['Cek ketersediaan', null]]" />

        <h1 class="mb-2">Cek ketersediaan</h1>
        <p class="text-muted-pit pit-prose mb-4">Pilih produk, tanggal mulai, dan durasi. Periode dianggap bentrok jika tumpang tindih dengan booking unit lain.</p>

        @if (config('pitlabs.demo_mode'))
            <x-alert type="info" title="Data contoh" class="mb-4">Jadwal booking pada halaman ini adalah contoh untuk tampilan. Perhitungan bentroknya memakai aturan yang sama dengan sistem sebenarnya.</x-alert>
        @endif

        <div class="row g-4 g-lg-5">
            <div class="col-lg-5">
                <form method="get" action="{{ route('availability') }}" class="pit-card" data-pit-validate novalidate>
                    <div class="mb-3">
                        <label for="produk" class="form-label">Produk <span class="pit-required" aria-hidden="true">*</span></label>
                        <select id="produk" name="produk" class="form-select @error('produk') is-invalid @enderror" required
                                @error('produk') aria-invalid="true" aria-describedby="produk-error" @enderror>
                            <option value="">Pilih produk</option>
                            @foreach ($products as $p)
                                @if ($p['rental_enabled'])
                                    <option value="{{ $p['slug'] }}" @selected($selected === $p['slug'])>{{ $p['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div id="produk-error" class="invalid-feedback">@error('produk'){{ $message }}@enderror</div>
                    </div>

                    <x-field name="mulai" label="Tanggal mulai sewa" type="date" required :value="request('mulai')" :min="$today"
                             hint="Jam pengambilan mengikuti ketentuan lokasi admin." />

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="durasi" class="form-label">Durasi <span class="pit-required" aria-hidden="true">*</span></label>
                            <input type="number" id="durasi" name="durasi" min="1" max="90" step="1" required
                                   value="{{ request('durasi', 1) }}" inputmode="numeric"
                                   class="form-control @error('durasi') is-invalid @enderror"
                                   @error('durasi') aria-invalid="true" aria-describedby="durasi-error" @enderror>
                            <div id="durasi-error" class="invalid-feedback">@error('durasi'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-6">
                            <label for="satuan" class="form-label">Satuan</label>
                            <select id="satuan" name="satuan" class="form-select">
                                @foreach (['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan'] as $val => $label)
                                    <option value="{{ $val }}" @selected(request('satuan', 'hari') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Cek ketersediaan</button>
                </form>
            </div>

            <div class="col-lg-7" aria-live="polite">
                @if ($result)
                    @php $r = $result; $isDigital = $r['units'] === null; @endphp

                    <div class="pit-card">
                        <p class="pit-eyebrow mb-1">Hasil pengecekan</p>
                        <h2 class="h3 mb-1">{{ $r['product']['name'] }}</h2>
                        <p class="text-muted-pit mb-4">{{ $r['start'] }} sampai {{ $r['end'] }} ({{ $r['days'] }} hari)</p>

                        @if ($isDigital)
                            <x-alert type="info" title="Layanan digital">Tidak memakai unit fisik. Ketersediaan slot: <x-pending :field="'product.'.$r['product']['slug'].'.digital_slots'" /></x-alert>
                        @elseif ($r['free_count'] > 0)
                            <x-alert type="success" title="Ada unit yang tersedia">{{ $r['free_count'] }} dari {{ count($r['units']) }} unit bebas pada periode ini.</x-alert>
                        @else
                            <x-alert type="danger" title="Semua unit sudah dipesan">Tidak ada unit yang bebas pada periode ini. Coba tanggal atau durasi lain.</x-alert>
                        @endif

                        @unless ($isDigital)
                            <div class="table-responsive mt-3">
                                <table class="table align-middle pit-unit-table mb-0">
                                    <caption class="visually-hidden">Ketersediaan tiap unit pada periode yang dipilih</caption>
                                    <thead><tr><th scope="col">Kode unit</th><th scope="col">Status</th><th scope="col">Keterangan</th></tr></thead>
                                    <tbody>
                                        @foreach ($r['units'] as $u)
                                            <tr>
                                                <td><span class="pit-mono">{{ $u['code'] }}</span></td>
                                                <td><x-status-badge group="availability" :status="$u['free'] ? 'FREE' : 'CONFLICT'" /></td>
                                                <td class="small text-muted-pit">{{ $u['free'] ? 'Bebas pada periode ini' : 'Sudah dipesan '.$u['conflict_label'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endunless

                        <hr class="my-4">
                        <p class="mb-3"><span class="text-muted-pit">Estimasi biaya:</span> <x-pending field="availability.estimate" /></p>

                        @if (! $isDigital && $r['free_count'] === 0)
                            <x-alert type="info" title="Semua unit penuh pada periode ini" class="mb-3">Anda bisa masuk antrean. Kami memberi tahu saat ada unit yang bebas. Antrean bukan jaminan unit.</x-alert>
                            @if (\App\Support\DemoAuth::check())
                                <form method="POST" action="{{ route('waitlist.store') }}">
                                    @csrf
                                    @foreach (['produk', 'mulai', 'durasi', 'satuan'] as $f)<input type="hidden" name="{{ $f }}" value="{{ request($f) }}">@endforeach
                                    <button type="submit" class="btn btn-primary">Masuk antrean rental</button>
                                </form>
                            @else
                                <a href="{{ route('login', ['kembali' => request()->getRequestUri()]) }}" class="btn btn-primary">Masuk untuk bergabung antrean</a>
                            @endif
                        @endif

                        @if ($isDigital || $r['free_count'] > 0)
                            @if (\App\Support\DemoAuth::check())
                                @if ($checkoutUrl)
                                    <a href="{{ $checkoutUrl }}?{{ http_build_query(request()->only(['produk', 'mulai', 'durasi', 'satuan'])) }}" class="btn btn-primary">Lanjutkan ke pemesanan</a>
                                @else
                                    <button type="button" class="btn btn-primary" disabled title="Halaman pemesanan dikerjakan di tahap berikutnya">Lanjutkan ke pemesanan</button>
                                    <p class="small text-muted-pit mt-2 mb-0">Halaman pemesanan (checkout) dibuat di tahap berikutnya.</p>
                                @endif
                            @else
                                <a href="{{ route('login', ['kembali' => request()->getRequestUri()]) }}" class="btn btn-primary">Masuk untuk melanjutkan</a>
                                <p class="small text-muted-pit mt-2 mb-0">Pemesanan sewa memerlukan akun.</p>
                            @endif
                        @endif
                    </div>
                @else
                    <x-empty-state icon="bi-calendar-check" title="Belum ada pengecekan">
                        Pilih produk dan tanggal di formulir, lalu tekan "Cek ketersediaan". Hasilnya muncul di sini.
                    </x-empty-state>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
