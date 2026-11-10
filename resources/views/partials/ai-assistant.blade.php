{{-- Tombol melayang AI Technical Assistant + modal "Segera Hadir".
     Fitur AI belum diimplementasikan; tombol membuka penjelasan, bukan chatbot. --}}
<button type="button" class="pit-ai-fab" data-bs-toggle="modal" data-bs-target="#aiSoonModal">
    <i class="bi bi-stars" aria-hidden="true"></i>
    <span class="pit-ai-fab__label">AI Technical Assistant</span>
    <span class="pit-tag-soon pit-tag-soon--on-dark">Segera Hadir</span>
</button>

<x-modal id="aiSoonModal" title="AI Technical Assistant segera hadir">
    <p class="mb-3">Asisten ini akan membantu memilih perangkat yang cocok untuk proyek Anda, misalnya untuk computer vision, robotika, atau pelatihan model.</p>
    <p class="mb-0 text-muted-pit">Fitur ini belum aktif. Sementara itu, Anda bisa melihat spesifikasi dan ketersediaan langsung di katalog.</p>

    <x-slot:footer>
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Tutup</button>
        @if (Route::has('catalog.index'))
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Lihat katalog</a>
        @endif
    </x-slot:footer>
</x-modal>
