{{--
    Modal Bootstrap. Fokus dikunci di dalam modal, Escape menutup, fokus kembali ke pemicu.
    Pemakaian:
      <x-modal id="konfirmasi" title="Judul">Isi
          <x-slot:footer> tombol </x-slot:footer>
      </x-modal>
      <button data-bs-toggle="modal" data-bs-target="#konfirmasi">Buka</button>
--}}
@props(['id', 'title', 'size' => null])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered @if ($size) modal-{{ $size }} @endif">
        <div class="modal-content pit-modal">
            <div class="modal-header">
                <h2 class="modal-title" id="{{ $id }}-title">{{ $title }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">{{ $slot }}</div>
            @isset($footer)
                <div class="modal-footer">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
