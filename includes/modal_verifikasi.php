<div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-search me-2"></i>VERIFIKASI DOKUMEN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="proses_verifikasi.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="text-muted small d-block">NOMOR RESI</label>
                        <h4 id="display_resi_verif" class="fw-bold text-dark mb-0">-</h4>
                        <input type="hidden" name="id" id="modal_id_verif">
                    </div>

                    <label class="fw-bold small mb-2 text-secondary text-uppercase">Tentukan Tindakan:</label>
                    <div class="d-flex flex-column gap-2 mb-4">
                        <input type="radio" class="btn-check" name="tindakan" id="radio_proses" value="proses" onclick="toggleVerifFields()" required>
                        <label class="btn btn-outline-success text-start p-3 border-2" for="radio_proses">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>
                                    <div class="fw-bold">LANJUT PROSES</div>
                                    <small class="opacity-75">Dokumen lengkap dan siap diproses</small>
                                </div>
                            </div>
                        </label>

                        <input type="radio" class="btn-check" name="tindakan" id="radio_resolve" value="resolve" onclick="toggleVerifFields()">
                        <label class="btn btn-outline-info text-start p-3 border-2" for="radio_resolve">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-link-45deg fs-4 me-3"></i>
                                <div>
                                    <div class="fw-bold">TAUTKAN KE PENDING</div>
                                    <small class="opacity-75">Dokumen ini adalah susulan/kekurangan</small>
                                </div>
                            </div>
                        </label>

                        <input type="radio" class="btn-check" name="tindakan" id="radio_pending" value="pending" onclick="toggleVerifFields()">
                        <label class="btn btn-outline-warning text-start p-3 border-2" for="radio_pending">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-split fs-4 me-3"></i>
                                <div>
                                    <div class="fw-bold">PENDING / KURANG</div>
                                    <small class="opacity-75">Ada dokumen yang masih kurang</small>
                                </div>
                            </div>
                        </label>

                        <input type="radio" class="btn-check" name="tindakan" id="radio_reject" value="reject" onclick="toggleVerifFields()">
                        <label class="btn btn-outline-danger text-start p-3 border-2" for="radio_reject">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill fs-4 me-3"></i>
                                <div>
                                    <div class="fw-bold">REJECT / RETURN</div>
                                    <small class="opacity-75">Salah kirim atau tidak sesuai</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div id="field_proses" class="verif-extra-field d-none">
                        <div class="p-3 bg-light rounded-3 border-start border-success border-4">
                            <label class="small fw-bold mb-1 text-success">NOMOR URN</label>
                            <input type="text" name="nomor_urn" class="form-control border-success" placeholder="Masukkan Nomor URN">
                        </div>
                    </div>

                    <div id="field_resolve" class="verif-extra-field d-none">
                        <div class="p-3 bg-light rounded-3 border-start border-info border-4">
                            <label class="small fw-bold mb-1 text-info text-uppercase">Cari Dokumen yang Dilengkapi:</label>
                            <select name="id_data_lama" class="form-select border-info mb-3" id="select_pending_data">
                                <option value="">-- Memuat Data Pending... --</option>
                            </select>

                            <label class="small fw-bold mb-1 text-success text-uppercase">Input Nomor URN (Untuk Keduanya):</label>
                            <input type="text" name="nomor_urn_resolve" class="form-control border-success mb-2" id="input_urn_resolve" placeholder="Masukkan Nomor URN">

                            <small class="text-muted d-block mt-2">Pilih data pending. URN ini akan otomatis diterapkan pada resi utama dan resi susulan ini.</small>
                        </div>
                    </div>

                    <div id="field_pending" class="verif-extra-field d-none">
                        <div class="p-3 bg-light rounded-3 border-start border-warning border-4">
                            <label class="small fw-bold mb-1 text-warning">NAMA VENDOR</label>
                            <input type="text" name="nama_vendor" class="form-control mb-2" placeholder="Contoh: PT. Maju Jaya">
                            <label class="small fw-bold mb-1 text-warning">NOMOR INVOICE / DOKUMEN</label>
                            <input type="text" name="nomor_invoice" class="form-control" placeholder="Masukkan nomor invoice">
                        </div>
                    </div>

                    <div id="field_reject" class="verif-extra-field d-none">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4">
                            <label class="small fw-bold mb-1 text-danger">ALASAN REJECT</label>
                            <textarea name="alasan_reject" class="form-control" rows="2" placeholder="Sebutkan alasan penolakan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4">SIMPAN KEPUTUSAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleVerifFields() {
    // Sembunyikan semua field tambahan
    document.querySelectorAll('.verif-extra-field').forEach(el => el.classList.add('d-none'));

    // Reset required attributes
    document.querySelectorAll('.verif-extra-field input, .verif-extra-field textarea, .verif-extra-field select').forEach(el => el.required = false);

    if (document.getElementById('radio_proses').checked) {
        document.getElementById('field_proses').classList.remove('d-none');
        document.querySelector('input[name="nomor_urn"]').required = true;
    }
    else if (document.getElementById('radio_resolve').checked) {
        document.getElementById('field_resolve').classList.remove('d-none');
        document.getElementById('select_pending_data').required = true;
        document.getElementById('input_urn_resolve').required = true;
        loadPendingOptions(); // Panggil data pending dari database
    }
    else if (document.getElementById('radio_pending').checked) {
        document.getElementById('field_pending').classList.remove('d-none');
        document.querySelector('input[name="nama_vendor"]').required = true;
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