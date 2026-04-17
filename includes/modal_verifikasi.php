<div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>INPUT DATA PENYELESAIAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="proses_verifikasi_cepat.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-4 text-center bg-light p-3 rounded-3 border">
                        <label class="text-muted small d-block">NOMOR RESI</label>
                        <h4 id="display_resi_verif" class="fw-bold text-dark mb-0">-</h4>
                        <input type="hidden" name="id" id="modal_id_verif">
                    </div>

                    <div class="p-3 bg-white rounded-3 border-start border-primary border-4 shadow-sm">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="small fw-bold mb-1 text-primary">JUMLAH DOKUMEN (QTY)</label>
                                <input type="number" id="input_qty_verif" name="jumlah" class="form-control border-primary fw-bold"
                                       value="1" min="1" onchange="generateUrnVerif()" onkeyup="generateUrnVerif()">
                                <small class="text-muted" style="font-size: 10px;">Sesuaikan jumlah kolom URN di bawah</small>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div id="container_urn_verif">
                            <div class="mb-2">
                                <label class="small fw-bold mb-1 text-dark">NOMOR URN 1</label>
                                <input type="text" name="nomor_urn[]" class="form-control border-primary text-uppercase"
                                       placeholder="Masukkan Nomor URN 1" required oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="small fw-bold mb-1 text-muted text-uppercase">Catatan Internal (Opsional)</label>
                            <textarea name="catatan" class="form-control border-secondary" rows="2" placeholder="Tambahkan keterangan jika perlu..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow">
                        <i class="bi bi-cloud-check-fill me-2"></i>SELESAIKAN DOKUMEN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generateUrnVerif() {
    const qty = document.getElementById('input_qty_verif').value;
    const container = document.getElementById('container_urn_verif');

    // Simpan nilai yang sudah diketik agar tidak hilang saat QTY diubah
    const currentValues = Array.from(container.querySelectorAll('input[name="nomor_urn[]"]')).map(el => el.value);

    container.innerHTML = '';

    // Batasi maksimal misal 20 agar tidak lag, tapi sesuaikan kebutuhan
    const maxQty = Math.min(qty, 20);

    for (let i = 1; i <= maxQty; i++) {
        const val = currentValues[i-1] ? currentValues[i-1] : '';
        container.innerHTML += `
            <div class="mb-2">
                <label class="small fw-bold mb-1 text-dark">NOMOR URN ${i}</label>
                <input type="text" name="nomor_urn[]" class="form-control border-primary text-uppercase"
                       placeholder="Masukkan Nomor URN ${i}" value="${val}" required
                       oninput="this.value = this.value.toUpperCase()">
            </div>
        `;
    }
}

function generateUrnResolve() {
    const qty = document.getElementById('input_qty_resolve').value;
    const container = document.getElementById('container_urn_resolve');
    const currentValues = Array.from(container.querySelectorAll('input[name="nomor_urn_resolve[]"]')).map(el => el.value);

    container.innerHTML = '<label class="small fw-bold mb-1 text-success text-uppercase">Input Nomor URN:</label>';
    for (let i = 1; i <= qty; i++) {
        const val = currentValues[i-1] ? currentValues[i-1] : '';
        container.innerHTML += `
            <input type="text" name="nomor_urn_resolve[]" class="form-control border-success mb-2"
                   placeholder="Masukkan Nomor URN ${i}" value="${val}" required>
        `;
    }
}

function toggleVerifFields() {
    document.querySelectorAll('.verif-extra-field').forEach(el => el.classList.add('d-none'));
    document.querySelectorAll('.verif-extra-field input, .verif-extra-field textarea, .verif-extra-field select').forEach(el => el.required = false);

    if (document.getElementById('radio_proses').checked) {
        document.getElementById('field_proses').classList.remove('d-none');
        // Set vendor dan semua URN menjadi required
        document.querySelector('input[name="nama_vendor_proses"]').required = true;
        document.querySelectorAll('input[name="nomor_urn[]"]').forEach(el => el.required = true);
    }
    // ... (sisanya tetap seperti kode lama Anda)
    else if (document.getElementById('radio_resolve').checked) {
        document.getElementById('field_resolve').classList.remove('d-none');
        document.getElementById('select_pending_data').required = true;
        document.getElementById('input_urn_resolve').required = true;
        loadPendingOptions();
    }
    else if (document.getElementById('radio_pending').checked) {
        document.getElementById('field_pending').classList.remove('d-none');
        document.querySelector('#field_pending input[name="nama_vendor"]').required = true;
        document.querySelector('input[name="nomor_invoice"]').required = true;
    }
    else if (document.getElementById('radio_reject').checked) {
        document.getElementById('field_reject').classList.remove('d-none');
        document.querySelector('textarea[name="alasan_reject"]').required = true;
    }
}

// Fungsi mengambil data status = 3 secara realtime
function loadPendingOptions() {
    const select = document.getElementById('select_pending_data');
    select.innerHTML = '<option value="">-- Memuat Data... --</option>';

    fetch('get_data_pending.php')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                select.innerHTML = '<option value="">Tidak ada dokumen berstatus Pending</option>';
            } else {
                select.innerHTML = '<option value="">-- Pilih Data Pending --</option>';
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.id}">${item.nama_vendor} | Inv: ${item.nomor_invoice} (Resi: ${item.nomor_resi})</option>`;
                });
            }
        })
        .catch(err => {
            select.innerHTML = '<option value="">Gagal memuat data</option>';
        });
}
</script>