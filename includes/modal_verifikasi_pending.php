<div class="modal fade" id="modalVerifikasiPending" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-link-45deg me-2"></i>PENYELESAIAN PENDING</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="proses_verifikasi.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="pending_id"> <div class="text-center mb-4 bg-light p-3 rounded-3 border">
                        <label class="text-muted small d-block">RESI UTAMA (PENDING)</label>
                        <h4 id="pending_display_resi" class="fw-bold text-dark mb-0">-</h4>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary text-uppercase">Bentuk Dokumen Susulan:</label>
                        <div class="d-flex gap-2 mt-2">
                            <input type="radio" class="btn-check" name="metode_susulan" id="metode_soft" value="SOFTCOPY" onclick="togglePendingMethod()" checked>
                            <label class="btn btn-outline-info w-100 fw-bold" for="metode_soft">SOFTCOPY (EMAIL)</label>

                            <input type="radio" class="btn-check" name="metode_susulan" id="metode_hard" value="HARDCOPY" onclick="togglePendingMethod()">
                            <label class="btn btn-outline-info w-100 fw-bold" for="metode_hard">HARDCOPY (FISIK)</label>
                        </div>
                    </div>

                    <div id="div_select_resi_susulan" class="mb-3 d-none">
                        <label class="small fw-bold text-primary text-uppercase">Pilih Dokumen yang Masuk:</label>
                        <select name="id_resi_susulan" id="select_resi_susulan" class="form-select border-primary shadow-sm">
                            <option value="">-- Memuat Data Antrean --</option>
                        </select>
                    </div>

                    <div class="p-3 border-info border rounded-3 bg-white">
                        <label class="small fw-bold text-info text-uppercase">URN</label>
                        <input type="text" name="nomor_urn[]" class="form-control border-info text-uppercase fw-bold" placeholder="Input URN..." required oninput="this.value = this.value.toUpperCase()">
                        <input type="hidden" name="jumlah" value="1">

                        <label class="small fw-bold text-muted text-uppercase mt-3">Catatan Tambahan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-info text-white w-100 fw-bold py-2 shadow-sm">PROSES SELESAI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

function openModalPending(id, resi) {
    document.getElementById('pending_id').value = id;
    document.getElementById('pending_display_resi').innerText = resi;

    // Tampilkan modal
    var myModal = new bootstrap.Modal(document.getElementById('modalVerifikasiPending'));
    myModal.show();
}

function togglePendingMethod() {
    const isHardcopy = document.getElementById('metode_hard').checked;
    const divSelect = document.getElementById('div_select_resi_susulan');
    const select = document.getElementById('select_resi_susulan');

    if (isHardcopy) {
        divSelect.classList.remove('d-none');
        select.required = true;
        loadResiMasuk(); // Ambil data resi yang ada di tab MASUK
    } else {
        divSelect.classList.add('d-none');
        select.required = false;
    }
}

function loadResiMasuk() {
    const select = document.getElementById('select_resi_susulan');
    select.innerHTML = '<option value="">-- Memuat... --</option>';

    // Kita buatkan file get_resi_masuk.php untuk ambil data status = 0
    fetch('get_resi_masuk.php')
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Pilih Dokumen --</option>';
            data.forEach(item => {
                select.innerHTML += `<option value="${item.id}">${item.nomor_resi} (${item.ekspedisi})</option>`;
            });
        });
}
</script>