{{-- Footer. Logo resmi memuat huruf navy, jadi selalu diletakkan di atas panel putih. --}}
<footer class="pit-footer">
    <div class="container">
        <div class="row g-4 g-lg-5">

            <div class="col-lg-5">
                <div class="pit-footer__logo">
                    <img src="{{ asset('assets/img/logo-pitlabs.png') }}" alt="PITLABS" width="144" height="48">
                </div>
                <p class="pit-footer__tagline">{{ config('pitlabs.tagline') }}</p>
                <p class="pit-footer__text">Penyewaan perangkat AI, IoT, dan teknologi. Pengambilan dan pengembalian dilakukan langsung di lokasi admin.</p>
            </div>

            <div class="col-6 col-lg-3">
                <h2 class="pit-footer__heading">Jelajahi</h2>
                <ul class="list-unstyled pit-footer__links">
                    <li><a href="{{ Route::has('catalog.index') ? route('catalog.index') : '#' }}">Katalog produk</a></li>
                    <li><a href="{{ Route::has('availability') ? route('availability') : '#' }}">Cek ketersediaan</a></li>
                    <li><a href="{{ Route::has('guide') ? route('guide') : '#' }}">Panduan</a></li>
                    <li><a href="{{ Route::has('articles.index') ? route('articles.index') : '#' }}">Artikel</a></li>
                    <li><a href="{{ Route::has('faq') ? route('faq') : '#' }}">FAQ</a></li>
                    <li><a href="{{ Route::has('about') ? route('about') : '#' }}">Tentang kami</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h2 class="pit-footer__heading">{{ config('pitlabs.company') }}</h2>
                <dl class="pit-footer__contact">
                    <dt>Lokasi pengambilan</dt>
                    <dd><x-pending field="contact.address" dark /></dd>
                    <dt>Email</dt>
                    <dd><x-pending field="contact.email" dark /></dd>
                    <dt>WhatsApp</dt>
                    <dd><x-pending field="contact.phone" dark /></dd>
                </dl>
            </div>
        </div>

        <div class="pit-footer__bottom">
            <span>© {{ date('Y') }} {{ config('pitlabs.company') }}</span>
            <span>Proyek PjBL kelompok FAR STAR, D4 Teknik Komputer dan Jaringan, Politeknik Negeri Ujung Pandang</span>
        </div>
    </div>
</footer>
