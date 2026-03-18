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
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <label class="small fw-bold mb-1 text-success">NAMA VENDOR / PENGIRIM</label>
                                    <input type="text" name="nama_vendor_proses" class="form-control border-success mb-2" placeholder="Contoh: PT. Maju Jaya">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold mb-1 text-success">JUMLAH</label>
                                    <input type="number" id="input_qty_verif" name="jumlah_proses" class="form-control border-success" value="1" min="1" onchange="generateUrnVerif()" onkeyup="generateUrnVerif()">
                                </div>
                            </div>

                            <hr class="my-2 text-success opacity-25">

                            <div id="container_urn_verif">
                                <div class="mb-2">
                                    <label class="small fw-bold mb-1 text-success">NOMOR URN 1</label>
                                    <input type="text" name="nomor_urn[]" class="form-control border-success" placeholder="Masukkan Nomor URN">
                                </div>
                            </div>

                            <label class="small fw-bold mb-1 text-muted mt-2 text-uppercase">Catatan (Opsional)</label>
                            <textarea name="catatan_proses" class="form-control border-secondary" rows="2" placeholder="Tambahkan catatan jika perlu..."></textarea>
                        </div>
                    </div>

                    <div id="field_resolve" class="verif-extra-field d-none">
                        <div class="p-3 bg-light rounded-3 border-start border-info border-4">
                            <label class="small fw-bold mb-1 text-info text-uppercase">Cari Dokumen yang Dilengkapi:</label>
                            <select name="id_data_lama" class="form-select border-info mb-3" id="select_pending_data">
                                <option value="">-- Memuat Data Pending... --</option>
                            </select>

                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <label class="small fw-bold mb-1 text-info">JUMLAH DOKUMEN SUSULAN</label>
                                    <input type="number" id="input_qty_resolve" name="jumlah_resolve" class="form-control border-info mb-2" value="1" min="1" onchange="generateUrnResolve()" onkeyup="generateUrnResolve()">
                                </div>
                            </div>

                            <div id="container_urn_resolve">
                                <label class="small fw-bold mb-1 text-success text-uppercase">Input Nomor URN:</label>
                                <input type="text" name="nomor_urn_resolve[]" class="form-control border-success mb-2" placeholder="Masukkan Nomor URN" required>
                            </div>

                            <small class="text-muted d-block mt-2">Setiap URN yang diinput akan tercatat sebagai dokumen baru yang melengkapi resi pending di atas.</small>
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
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <label class="small fw-bold mb-1 text-danger">NAMA VENDOR (DI REJECT)</label>
                                    <input type="text" name="nama_vendor_reject" class="form-control border-danger mb-2" placeholder="Contoh: PT. Salah Kirim">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold mb-1 text-danger">QTY ITEM</label>
                                    <input type="number" name="jumlah_reject" class="form-control border-danger" value="1" min="1">
                                </div>
                            </div>
                            <label class="small fw-bold mb-1 text-danger">ALASAN REJECT</label>
                            <textarea name="alasan_reject" class="form-control border-danger" rows="2" placeholder="Sebutkan alasan penolakan..."></textarea>
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
function generateUrnVerif() {
    const qty = document.getElementById('input_qty_verif').value;
    const container = document.getElementById('container_urn_verif');

    // Simpan data yang sudah diketik agar tidak hilang
    const currentValues = Array.from(container.querySelectorAll('input[name="nomor_urn[]"]')).map(el => el.value);

    container.innerHTML = '';
    for (let i = 1; i <= qty; i++) {
        const val = currentValues[i-1] ? currentValues[i-1] : '';
        container.innerHTML += `
            <div class="mb-2">
                <label class="small fw-bold mb-1 text-success">NOMOR URN ${i}</label>
                <input type="text" name="nomor_urn[]" class="form-control border-success"
                       placeholder="Masukkan Nomor URN ${i}" value="${val}" required>
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