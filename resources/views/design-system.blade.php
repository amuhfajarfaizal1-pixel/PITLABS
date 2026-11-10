@extends('layouts.app')

@section('title', 'Design System')
@section('description', 'Referensi komponen dan token desain PITLABS untuk tim pengembang.')

@section('content')

{{-- Pembuka --}}
<section class="pit-section--navy pit-on-navy py-5">
    <div class="container py-lg-4">
        <span class="pit-eyebrow">Fase 2, khusus tim pengembang</span>
        <h1 class="mb-3">Design system PITLABS</h1>
        <p class="pit-lead mb-0">Token warna, tipografi, dan komponen yang dipakai semua halaman. Halaman ini hanya aktif di environment <span class="pit-mono">local</span>.</p>
    </div>
</section>

{{-- 1. Warna --}}
<section class="pit-section" id="warna">
    <div class="container">
        <h2>Warna</h2>
        <p class="text-muted-pit pit-prose mb-4">Lima warna brand ditambah putih. Setiap warna punya peran tetap supaya tampilan tidak ramai.</p>

        @php
            $swatches = [
                ['Navy',          '#0A2540', '#fff', 'Judul, sidebar admin, hero, footer'],
                ['Sapphire Blue', '#1463FF', '#fff', 'CTA sewa, tautan, fokus. Teks putih di atasnya lolos kontras (4,9:1)'],
                ['Cyan',          '#00A3FF', '#fff', 'Aksen dan ikon. Teks hanya di atas navy; di latar putih kontrasnya 2,7:1'],
                ['Turquoise',     '#00F5D4', '#0A2540', 'Sorotan kecil di atas navy. Tidak untuk teks di latar putih'],
                ['Light Gray',    '#E9ECEF', '#0A2540', 'Border dan latar sekunder'],
                ['Orange',        '#F97316', '#0A2540', 'Hanya tombol Beli via Shopee. Teks navy (5,5:1); warna provisional'],
            ];
        @endphp
        <div class="row g-3">
            @foreach ($swatches as [$name, $hex, $fg, $use])
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="pit-swatch">
                        <div class="pit-swatch__color" style="background: {{ $hex }}"></div>
                        <div class="pit-swatch__meta">
                            <strong>{{ $name }}</strong>
                            <span class="pit-mono">{{ $hex }}</span>
                            <p class="text-muted-pit mt-2 mb-0">{{ $use }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 2. Tipografi --}}
<section class="pit-section pit-section--alt" id="tipografi">
    <div class="container">
        <h2>Tipografi</h2>
        <p class="text-muted-pit pit-prose mb-4">Sora untuk judul, Plus Jakarta Sans untuk teks isi, JetBrains Mono untuk kode unit dan nomor invoice.</p>

        <div class="pit-card">
            <div class="pit-spec"><span class="pit-spec__label">Display</span><span class="pit-display">Sewa teknologi, tanpa ribet</span></div>
            <div class="pit-spec"><span class="pit-spec__label">Heading 1</span><h1 class="mb-0">Katalog perangkat</h1></div>
            <div class="pit-spec"><span class="pit-spec__label">Heading 2</span><h2 class="mb-0">Spesifikasi produk</h2></div>
            <div class="pit-spec"><span class="pit-spec__label">Heading 3</span><h3 class="mb-0">Ringkasan pesanan</h3></div>
            <div class="pit-spec"><span class="pit-spec__label">Lead</span><p class="pit-lead mb-0">Ambil perangkat langsung di lokasi dan pantau masa sewa dari dashboard.</p></div>
            <div class="pit-spec"><span class="pit-spec__label">Isi (16 px)</span><p class="mb-0 pit-prose">Pengambilan dan pengembalian dilakukan langsung di lokasi admin. Denda kerusakan ditetapkan setelah pemeriksaan admin sesuai ketentuan mitra.</p></div>
            <div class="pit-spec"><span class="pit-spec__label">Teks sekunder</span><p class="mb-0 text-muted-pit">Keterangan tambahan, tanggal, dan petunjuk.</p></div>
            <div class="pit-spec"><span class="pit-spec__label">Mono</span><span class="pit-mono">JON-001, INV-2026-0001</span></div>
        </div>
    </div>
</section>

{{-- 3. Spasi, radius, bayangan --}}
<section class="pit-section" id="spasi">
    <div class="container">
        <h2>Spasi, radius, dan bayangan</h2>
        <p class="text-muted-pit pit-prose mb-4">Skala spasi kelipatan 4 px. Satu radius per peran, bukan satu radius untuk semuanya.</p>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="pit-card">
                    <h3 class="h4 mb-3">Skala spasi</h3>
                    @foreach ([4, 8, 16, 24, 48] as $i => $px)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="pit-mono" style="width:3.5rem">{{ $px }} px</span>
                            <span class="pit-space-demo" style="width: {{ $px * 2 }}px"></span>
                            <span class="text-muted-pit small">Bootstrap <span class="pit-mono">{{ $i + 1 }}</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-8">
                <div class="pit-card">
                    <h3 class="h4 mb-3">Radius</h3>
                    <div class="d-flex flex-wrap gap-4">
                        @foreach ([['8 px', 'sm', 'Badge, tag'], ['12 px', 'md', 'Tombol, input'], ['16 px', 'lg', 'Kartu'], ['24 px', 'xl', 'Modal, panel hero']] as [$px, $key, $use])
                            <div>
                                <div class="pit-radius-demo" style="border-radius: var(--pit-radius-{{ $key }})"></div>
                                <p class="mt-2 mb-0 small"><strong>{{ $px }}</strong><br><span class="text-muted-pit">{{ $use }}</span></p>
                            </div>
                        @endforeach
                    </div>
                    <h3 class="h4 mt-4 mb-3">Bayangan</h3>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="p-3 bg-white" style="border-radius: var(--pit-radius-lg); box-shadow: var(--pit-shadow-sm); width: 11rem"><strong class="small">Kecil</strong><br><span class="text-muted-pit small">Kartu diam</span></div>
                        <div class="p-3 bg-white" style="border-radius: var(--pit-radius-lg); box-shadow: var(--pit-shadow-md); width: 11rem"><strong class="small">Sedang</strong><br><span class="text-muted-pit small">Kartu disorot, dropdown, modal</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 4. Tombol --}}
<section class="pit-section pit-section--alt" id="tombol">
    <div class="container">
        <h2>Tombol</h2>
        <p class="text-muted-pit pit-prose mb-4">Tinggi minimal 44 px. Satu tombol utama per area. Nama tombol menyebut hasilnya.</p>

        <div class="pit-card mb-4">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <button class="btn btn-primary">Sewa sekarang</button>
                <button class="btn btn-outline-primary">Cek ketersediaan</button>
                <button class="btn btn-soft">Batal</button>
                <button class="btn btn-navy">Masuk ke dashboard</button>
                <button class="btn btn-ghost">Lihat detail</button>
                <button class="btn btn-shopee"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Beli via Shopee</button>
            </div>
            <hr class="my-4">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <button class="btn btn-primary btn-lg">Ukuran besar</button>
                <button class="btn btn-primary btn-sm">Ukuran kecil</button>
                <button class="btn btn-primary" disabled>Nonaktif</button>
                <button class="btn btn-primary" type="button" data-pit-loading>Simpan perubahan</button>
                <span class="text-muted-pit small">← klik untuk melihat state loading (simulasi 1,2 detik)</span>
            </div>
        </div>

        <div class="pit-section--navy pit-on-navy p-4" style="border-radius: var(--pit-radius-xl)">
            <p class="mb-3 small">Di atas latar navy</p>
            <div class="d-flex flex-wrap gap-3">
                <button class="btn btn-primary btn-lg">Jelajahi katalog</button>
                <button class="btn btn-on-dark btn-lg">Pelajari cara sewa</button>
            </div>
        </div>
    </div>
</section>

{{-- 5. Form --}}
<section class="pit-section" id="form">
    <div class="container">
        <h2>Form</h2>
        <p class="text-muted-pit pit-prose mb-4">Label selalu tampil, pesan error muncul di dekat kolom dan menjelaskan cara memperbaikinya. Coba kirim form tanpa mengisi.</p>

        <div class="row g-4">
            <div class="col-lg-6">
                <form class="pit-card" data-pit-validate data-pit-simulate novalidate>
                    <h3 class="h4 mb-3">Contoh registrasi</h3>
                    <x-field name="demo_name" label="Nama lengkap" required autocomplete="name" />
                    <x-field name="demo_email" label="Email" type="email" required autocomplete="email" hint="Dipakai untuk invoice dan pemberitahuan." />
                    <x-field name="demo_password" label="Kata sandi" type="password" required minlength="10" autocomplete="new-password" hint="Minimal 10 karakter." />

                    <x-terms-checkbox id="demo_terms" />

                    <button type="submit" class="btn btn-primary w-100">Buat akun</button>

                    <div data-pit-form-status hidden tabindex="-1" class="mt-3">
                        <x-alert type="success" title="Form valid">Ini simulasi frontend. Data tidak dikirim ke mana pun; penyimpanan dibuat di Fase 5.</x-alert>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="pit-card">
                    <h3 class="h4 mb-3">State kolom</h3>
                    <x-field name="state_default" label="Default" placeholder="Placeholder contoh" />
                    <x-field name="state_error" label="Error (dari server)" value="nama@" error="Format email tidak valid. Contoh: nama@email.com" />
                    <x-field name="state_disabled" label="Nonaktif" value="Tidak bisa diubah" disabled />
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 6. Kartu & data pending --}}
<section class="pit-section pit-section--alt" id="kartu">
    <div class="container">
        <h2>Kartu dan data yang belum tersedia</h2>
        <p class="text-muted-pit pit-prose mb-4">Harga, spesifikasi, dan kontak tidak dikarang. Selama mitra belum memutuskan, pakai <span class="pit-mono">&lt;x-pending&gt;</span>. Kartu katalog tidak memuat tombol Beli atau Sewa.</p>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <a href="#kartu" class="pit-card pit-card--flush pit-card--link">
                    <div class="d-grid" style="height: 180px; background: var(--pit-sapphire-soft); place-items: center;">
                        <i class="bi bi-cpu" style="font-size: 3rem; color: var(--pit-sapphire)" aria-hidden="true"></i>
                    </div>
                    <div class="p-3 p-lg-4">
                        <span class="pit-eyebrow mb-1">Perangkat fisik</span>
                        <h3 class="h5 mb-2">Jetson Orin Nano</h3>
                        <p class="mb-1 small"><span class="text-muted-pit">Harga sewa:</span> <x-pending field="product.jetson.rental_price" /></p>
                        <p class="mb-0 small"><span class="text-muted-pit">Harga jual:</span> <x-pending field="product.jetson.sale_price" /></p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="pit-card">
                    <h3 class="h5">Kartu informasi</h3>
                    <p class="text-muted-pit mb-3">Permukaan putih, border abu terang, bayangan kecil. Cukup untuk sebagian besar konten.</p>
                    <x-status-badge group="rental" status="RENTING" />
                </div>
            </div>
            <div class="col-md-12 col-lg-4">
                <div class="pit-card">
                    <h3 class="h5 mb-3">State loading</h3>
                    <span class="pit-skeleton pit-skeleton--block" aria-hidden="true"></span>
                    <span class="pit-skeleton pit-skeleton--line" style="width: 70%" aria-hidden="true"></span>
                    <span class="pit-skeleton pit-skeleton--line" style="width: 45%" aria-hidden="true"></span>
                    <span class="visually-hidden" role="status">Memuat data…</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 7. Badge status --}}
<section class="pit-section" id="status">
    <div class="container">
        <h2>Badge status</h2>
        <p class="text-muted-pit pit-prose mb-4">Status pesanan, pembayaran, pengambilan, dan penyewaan selalu ditampilkan terpisah. Pembayaran lunas tidak berarti barang sudah diambil.</p>

        <div class="row g-4">
            @foreach (config('pitlabs.status') as $group => $items)
                <div class="col-md-6">
                    <div class="pit-card">
                        <h3 class="h5 mb-3">{{ config("pitlabs.status_groups.$group") }}</h3>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($items as $code => $def)
                                <x-status-badge :group="$group" :status="$code" />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <h3 class="h5 mt-5 mb-3">Contoh gabungan</h3>
        <div class="pit-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <caption class="visually-hidden">Contoh baris pesanan dengan empat status terpisah</caption>
                    <thead>
                        <tr><th scope="col">Invoice</th><th scope="col">Pesanan</th><th scope="col">Pembayaran</th><th scope="col">Pengambilan</th><th scope="col">Penyewaan</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="pit-mono">INV-CONTOH-001</span></td>
                            <td><x-status-badge group="order" status="READY_FOR_PICKUP" /></td>
                            <td><x-status-badge group="payment" status="PAID" /></td>
                            <td><x-status-badge group="pickup" status="NOT_PICKED_UP" /></td>
                            <td><x-status-badge group="rental" status="NOT_STARTED" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- 8. Alert, empty state, modal --}}
<section class="pit-section pit-section--alt" id="umpan-balik">
    <div class="container">
        <h2>Alert, state kosong, dan modal</h2>
        <p class="text-muted-pit pit-prose mb-4">Error menjelaskan apa yang salah dan cara memperbaikinya. State kosong menawarkan langkah berikutnya.</p>

        <div class="row g-4">
            <div class="col-lg-6 d-grid gap-3 align-content-start">
                <x-alert type="info" title="Informasi">Pengambilan dan pengembalian dilakukan langsung di lokasi admin.</x-alert>
                <x-alert type="success" title="Pesanan dibuat">Invoice sudah tersedia di dashboard Anda.</x-alert>
                <x-alert type="warning" title="Menunggu pembayaran">Unit ditahan sementara dan dilepas otomatis jika pembayaran tidak diterima.</x-alert>
                <x-alert type="danger" title="Tanggal tidak tersedia">Unit sudah dipesan pada 20–22 November. Pilih tanggal lain.</x-alert>
            </div>
            <div class="col-lg-6">
                <x-empty-state icon="bi-box-seam" title="Belum ada penyewaan aktif">
                    Perangkat yang Anda sewa akan muncul di sini beserta sisa masa sewanya.
                    <x-slot:action>
                        <a href="#kartu" class="btn btn-primary">Lihat katalog</a>
                    </x-slot:action>
                </x-empty-state>

                <div class="mt-4">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#demoModal">Buka contoh modal</button>
                </div>
            </div>
        </div>
    </div>
</section>

<x-modal id="demoModal" title="Lanjutkan ke Shopee?">
    <p class="mb-0">Anda akan membuka halaman produk di Shopee. Pembelian diselesaikan di sana, bukan di PITLABS.</p>
    <x-slot:footer>
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-shopee" data-bs-dismiss="modal">Lanjutkan ke Shopee</button>
    </x-slot:footer>
</x-modal>

@endsection
