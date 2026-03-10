<div class="modal fade" id="manualEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <span class="border-start border-4 border-danger ps-2">ENTRY NON-RESI</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_simpan_manual.php" method="POST">
                <div class="modal-body py-3 px-4">
                    <p class="text-muted small mb-4">Gunakan form ini untuk pengiriman yang tidak memiliki nomor resi fisik.</p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Nama PT / Pengirim</label>
                        <input type="text" name="nama_pt" class="form-control form-control-lg bg-light border-0"
                               placeholder="Masukkan Nama Perusahaan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Nomor WhatsApp / HP</label>
                        <input type="tel" name="no_hp" class="form-control form-control-lg bg-light border-0"
                               placeholder="0812xxxx" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>