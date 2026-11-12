{{--
    Kartu produk katalog. Sesuai UI_UX_GUIDELINES: hanya gambar, nama, kategori, harga.
    TIDAK ada tombol Beli/Sewa di kartu (keduanya hanya di halaman detail).
    Foto produk belum tersedia → ubin ikon sebagai penanda posisi gambar.
    <x-product-card :product="$product" />   // ['slug','name','type','icon']
--}}
@props(['product'])

@php
    $url = \App\Support\Pit::url('catalog.show', null, ['slug' => $product['slug']]);
    $isDigital = $product['type'] === 'digital';
@endphp

<article {{ $attributes->class(['pit-product', 'pit-product--linked' => (bool) $url]) }}>
    <div class="pit-product__media pit-product__media--{{ $product['type'] }}" aria-hidden="true">
        <i class="bi {{ $product['icon'] }}"></i>
    </div>
    <div class="pit-product__body">
        <p class="pit-product__type">
            <i class="bi {{ $isDigital ? 'bi-cloud' : 'bi-box-seam' }}" aria-hidden="true"></i>
            {{ $isDigital ? 'Layanan digital' : 'Perangkat fisik' }}
        </p>
        <h3 class="pit-product__name">
            @if ($url)<a href="{{ $url }}" class="stretched-link">{{ $product['name'] }}</a>@else{{ $product['name'] }}@endif
        </h3>
        <p class="pit-product__price">@if ($product['rental_enabled'] && $product['rental_daily_rate'] !== null)Sewa mulai <strong>{{ \App\Support\Pit::rupiah($product['rental_daily_rate']) }}</strong>/hari @else Harga: <x-pending :field="'product.'.$product['slug'].'.price'" />@endif</p>
        @unless ($url)
            <p class="pit-product__soon">Halaman detail menyusul</p>
        @endunless
    </div>
</article>
