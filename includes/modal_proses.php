<div class="modal fade" id="modalProses" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Detail Verifikasi Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formValidasi" action="proses_validasi.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="modal_id">

                    <div class="mb-3">
                        <label class="small fw-bold">Nomor Resi</label>
                        <input type="text" id="modal_resi" class="form-control bg-light" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="small fw-bold">Nama Vendor / Pengirim</label>
                            <input type="text" name="nama_vendor" class="form-control" placeholder="Contoh: PT. Sumber Makmur" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small fw-bold">Jumlah (Qty)</label>
                            <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Nomor URN</label>
                        <input type="text" name="urn" class="form-control" placeholder="Masukkan nomor URN internal" required>
                    </div>

                    <div class="mb-0">
                        <label class="small fw-bold">KETERANGAN / REASON (OPSIONAL)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Tulis catatan jika ada kendala atau informasi tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Simpan & Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>