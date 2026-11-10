{{--
    State kosong: jelaskan apa yang terjadi dan tawarkan langkah berikutnya.
    <x-empty-state icon="bi-box-seam" title="Belum ada penyewaan aktif">Teks
        <x-slot:action> <a class="btn btn-primary">Lihat katalog</a> </x-slot:action>
    </x-empty-state>
--}}
@props(['icon' => 'bi-inbox', 'title'])

<div {{ $attributes->class(['pit-empty']) }}>
    <i class="bi {{ $icon }} pit-empty__icon" aria-hidden="true"></i>
    <h3 class="pit-empty__title">{{ $title }}</h3>
    <p class="pit-empty__text">{{ $slot }}</p>
    @isset($action)<div class="pit-empty__action">{{ $action }}</div>@endisset
</div>
