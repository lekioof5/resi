<div class="modal fade" id="modalProsesAwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-box-seam me-2"></i>PROSES DOKUMEN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_penerimaan_awal.php" method="POST">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <label class="text-muted small d-block">NOMOR RESI</label>
                        <h4 id="display_resi_proses" class="fw-bold text-dark m-0">-</h4>
                        <input type="hidden" name="id" id="modal_id_proses">
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <input type="radio" class="btn-check" name="opsi_awal" id="opsi_terima" value="terima" onclick="toggleOpsiAwal()" checked>
                        <label class="btn btn-outline-success w-100 py-2 fw-bold" for="opsi_terima">
                            <i class="bi bi-check-circle me-1"></i> TERIMA
                        </label>

                        <input type="radio" class="btn-check" name="opsi_awal" id="opsi_tolak" value="tolak" onclick="toggleOpsiAwal()">
                        <label class="btn btn-outline-danger w-100 py-2 fw-bold" for="opsi_tolak">
                            <i class="bi bi-x-circle me-1"></i> REJECT
                        </label>
                    </div>

                    <div id="div_vendor" class="mb-3">
                        <label class="small fw-bold">NAMA VENDOR / PENGIRIM</label>
                        <input type="text" name="nama_vendor" class="form-control" id="input_vendor" placeholder="Masukkan nama vendor...">
                    </div>

                    <div id="div_qty" class="mb-3">
                        <label class="small fw-bold">JUMLAH (QTY)</label>
                        <input type="number" name="qty" class="form-control" value="1" min="1">
                    </div>

                    <div class="mb-3">
                        <label id="label_keterangan" class="small fw-bold">KETERANGAN PENERIMAAN</label>
                        <textarea name="keterangan_umum" class="form-control" rows="3" placeholder="Tambahkan catatan di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleOpsiAwal() {
    const isTerima = document.getElementById('opsi_terima').checked;
    const divVendor = document.getElementById('div_vendor');
    const divQty = document.getElementById('div_qty');
    const labelKet = document.getElementById('label_keterangan');
    const inputVendor = document.getElementById('input_vendor');

    if (isTerima) {
        divVendor.classList.remove('d-none');
        divQty.classList.remove('d-none');
        labelKet.innerText = "KETERANGAN PENERIMAAN";
        labelKet.classList.remove('text-danger');
        inputVendor.required = true;
    } else {
        divVendor.classList.add('d-none');
        divQty.classList.add('d-none');
        labelKet.innerText = "ALASAN REJECT";
        labelKet.classList.add('text-danger');
        inputVendor.required = false;
    }
}
// Jalankan sekali saat load untuk set required
toggleOpsiAwal();
</script>