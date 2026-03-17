<div class="modal fade" id="modalDetailHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-bold" style="font-size: 1rem;"><i class="bi bi-clock-history me-2"></i>TRACKING DOKUMEN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2 text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 800;">Nomor Resi</small>
                            <span id="h_resi" class="fw-bold text-dark small">-</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 800;">Jumlah (Qty)</small>
                            <span id="h_jumlah" class="fw-bold text-primary small">-</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 800;">Nomor URN</small>
                            <span id="h_urn" class="fw-bold text-success small">-</span>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="position-relative">
                        <div style="position: absolute; left: 17px; top: 10px; bottom: 10px; width: 1.5px; background: #dee2e6; z-index: 0;"></div>

                        <div class="d-flex mb-3 position-relative" style="z-index: 1;">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                <i class="bi bi-qr-code" style="font-size: 0.9rem;"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">Scan Masuk</h6>
                                <p id="h_tgl_masuk" class="text-muted mb-0" style="font-size: 0.75rem;">-</p>
                            </div>
                        </div>

                        <div id="row_receive" class="d-flex mb-3 position-relative" style="z-index: 1;">
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                <i class="bi bi-person-check-fill" style="font-size: 0.9rem;"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">Diterima oleh PIC</h6>
                                <p id="h_tgl_receive" class="text-muted mb-0" style="font-size: 0.75rem;">-</p>
                                <small id="h_pic_receive" class="text-muted" style="font-size: 0.7rem; font-style: italic;">-</small>
                            </div>
                        </div>

                        <div id="row_pending_history" class="d-flex mb-3 position-relative" style="z-index: 1;">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                <i class="bi bi-hourglass-split" style="font-size: 0.9rem;"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold text-secondary" style="font-size: 0.85rem;">Pernah Pending / Kurang</h6>
                                <p id="h_tgl_pending" class="text-muted mb-0" style="font-size: 0.75rem;">-</p>
                                <small class="text-muted" style="font-size: 0.7rem; font-style: italic;">Menunggu kelengkapan dokumen</small>
                            </div>
                        </div>

                        <div id="row_final" class="d-flex position-relative" style="z-index: 1;">
                            <div id="h_bg_final" class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                <i id="h_icon_final" class="bi bi-check-circle-fill" style="font-size: 0.9rem;"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h6 id="h_label_final" class="mb-0 fw-bold text-uppercase" style="font-size: 0.85rem;">Selesai</h6>
                                <p id="h_tgl_final" class="text-muted mb-0" style="font-size: 0.75rem;">-</p>
                                <div id="h_catatan_box" class="mt-2 p-2 rounded border-start border-3" style="display:none; font-size: 0.75rem; background: #f8f9fa;">
                                    <span id="h_catatan_text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-light fw-bold w-100 btn-sm" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

<script>
let modalHistoryInstance = null;

function lihatDetailHistory(data) {
    if (!modalHistoryInstance) {
        const modalElement = document.getElementById('modalDetailHistory');
        modalHistoryInstance = new bootstrap.Modal(modalElement);
    }

    // --- 1. RESET TOTAL (FORCE HIDE) ---
    // Kita sembunyikan semua secara paksa sebelum mengisi data baru
    const rowRec = document.getElementById('row_receive');
    const rowPen = document.getElementById('row_pending_history');
    const catBox = document.getElementById('h_catatan_box');

    rowRec.style.setProperty('display', 'none', 'important');
    rowPen.style.setProperty('display', 'none', 'important');
    catBox.style.setProperty('display', 'none', 'important');

    // Bersihkan teks lama agar tidak "berbayang"
    document.getElementById('h_tgl_pending').innerText = '';
    document.getElementById('h_catatan_text').innerHTML = '';

    // --- 2. ISI DATA HEADER ---
    document.getElementById('h_resi').innerText = data.nomor_resi || '-';
    document.getElementById('h_jumlah').innerText = (data.jumlah || '0') + " Lembar/Doc";
    document.getElementById('h_urn').innerText = data.nomor_urn || 'BELUM ADA';
    document.getElementById('h_tgl_masuk').innerText = data.waktu_masuk || '-';

    // --- 3. LOGIKA TAMPILAN RECEIVE ---
    if (data.waktu_receive && data.waktu_receive !== '0000-00-00 00:00:00' && data.waktu_receive !== 'null') {
        rowRec.style.setProperty('display', 'flex', 'important');
        document.getElementById('h_tgl_receive').innerText = data.waktu_receive;
        document.getElementById('h_pic_receive').innerText = "PIC: " + (data.nama_pic || '-');
    }

    // --- 4. LOGIKA PENDING (SUPER STRICT) ---
    // Kita cek apakah waktu_pending benar-benar berisi tanggal valid
    const wp = data.waktu_pending;
    const isPendingExist = wp && wp !== '0000-00-00 00:00:00' && wp !== 'null' && wp !== '';

    if (data.status != 5 && isPendingExist) {
        rowPen.style.setProperty('display', 'flex', 'important');
        document.getElementById('h_tgl_pending').innerText = wp;
    }

    // --- 5. LOGIKA FINAL STATUS ---
    const bgFinal = document.getElementById('h_bg_final');
    const iconFinal = document.getElementById('h_icon_final');
    const labelFinal = document.getElementById('h_label_final');
    const tglFinal = document.getElementById('h_tgl_final');
    const catText = document.getElementById('h_catatan_text');

    // Helper format link resi
    const formatCatatan = (teks) => {
        if (!teks) return '-';
        return teks.replace(/([A-Z0-9-]{7,})/g, function(match) {
            return `<span class="text-primary fw-bold" style="cursor:pointer; text-decoration:underline;" onclick="bukaResiViaAjax('${match}')">${match}</span>`;
        });
    };

    if (data.status == 2) { // PROCESSED
        bgFinal.className = "bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm";
        iconFinal.className = "bi bi-check-circle-fill";
        labelFinal.innerText = "Selesai Diproses";
        tglFinal.innerText = data.waktu_proses;
        if(data.keterangan_reject) {
            catBox.style.setProperty('display', 'block', 'important');
            catText.innerHTML = formatCatatan(data.keterangan_reject);
        }
    }
    else if (data.status == 4) { // REJECTED
        bgFinal.className = "bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm";
        iconFinal.className = "bi bi-x-circle-fill";
        labelFinal.innerText = "Dokumen Ditolak";
        tglFinal.innerText = data.waktu_reject;
        catBox.style.setProperty('display', 'block', 'important');
        catText.innerText = "Alasan: " + (data.keterangan_reject || '-');
    }
    else if (data.status == 5) { // LINKED
        bgFinal.className = "bg-info text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm";
        iconFinal.className = "bi bi-link-45deg";
        labelFinal.innerText = "Linked (Susulan)";
        tglFinal.innerText = data.waktu_proses;
        catBox.style.setProperty('display', 'block', 'important');
        catText.innerHTML = formatCatatan(data.keterangan_reject);
    }

    modalHistoryInstance.show();
}

// Fungsi AJAX tetap sama
function bukaResiViaAjax(nomorResi) {
    fetch('get_detail_resi_json.php?resi=' + nomorResi)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("Detail resi " + nomorResi + " tidak ditemukan.");
            } else {
                lihatDetailHistory(data);
            }
        })
        .catch(err => {
            console.error("Error fetching data:", err);
        });
}
</script>