{{-- ===== KETENTUAN SEWA & DENDA (target tautan checkbox persetujuan) ===== --}}
<section class="pit-section" id="ketentuan-sewa" tabindex="-1" aria-labelledby="ketentuan-title">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="pit-eyebrow">Sebelum menyewa</span>
                <h2 id="ketentuan-title" class="mb-3">Ketentuan sewa dan denda kerusakan/kehilangan</h2>
                <p class="text-muted-pit mb-0">Denda kerusakan ditetapkan setelah pemeriksaan admin sesuai ketentuan mitra. Sistem tidak menetapkan denda secara otomatis.</p>
            </div>

            <div class="col-lg-8">
                <div class="pit-terms-panel">
                    <dl class="pit-terms">
                        <div class="pit-terms__row">
                            <dt>Pengambilan dan pengembalian</dt>
                            <dd>Langsung di lokasi admin. Tidak ada pengiriman kurir.</dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Lokasi pengambilan</dt>
                            <dd><x-pending field="terms.pickup_address" /></dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Pemeriksaan saat pengembalian</dt>
                            <dd>Admin mencatat kondisi dan kelengkapan perangkat. Jika ditemukan kerusakan atau kehilangan, admin mencatat bukti, alasan, dan keputusan denda.</dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Kategori kerusakan</dt>
                            <dd>Ringan, sedang, dan berat. Contoh dan nominal per kategori: <x-pending field="terms.damage_fine_table" /></dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Denda keterlambatan</dt>
                            <dd><x-pending field="terms.late_fee" /></dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Rincian dan keberatan</dt>
                            <dd>Rincian denda dapat dilihat di dashboard Anda. Prosedur keberatan mengikuti kebijakan mitra: <x-pending field="terms.objection_procedure" /></dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Deposit</dt>
                            <dd><x-pending field="terms.deposit" /></dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Durasi minimum dan maksimum</dt>
                            <dd><x-pending field="terms.duration_limits" /></dd>
                        </div>
                        <div class="pit-terms__row">
                            <dt>Pembatalan dan refund</dt>
                            <dd><x-pending field="terms.cancellation_refund" /></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
