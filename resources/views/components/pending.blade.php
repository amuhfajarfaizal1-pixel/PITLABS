{{--
    Penanda data yang belum tersedia (belum diputuskan mitra).
    Spesifikasi, harga, dan kontak tidak boleh dikarang — pakai komponen ini.
    Atribut data-needs-data memudahkan pencarian semua data yang perlu dilengkapi:
    grep -r "data-needs-data" resources/views
--}}
@props(['field' => null, 'dark' => false])

<span {{ $attributes->class(['pit-pending', 'pit-pending--dark' => $dark]) }}
      @if ($field) data-needs-data="{{ $field }}" @endif
      title="Data ini perlu dilengkapi oleh mitra">Informasi belum tersedia</span>
