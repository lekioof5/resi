<div class="modal fade" id="modalProsesAwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div id="modal_header_bg" class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-box-seam me-2"></i>PROSES DOKUMEN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_penerimaan_awal.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="modal_id_proses">

                    <div class="text-center mb-4">
                        <label class="text-muted small d-block">NOMOR RESI</label>
                        <h4 id="display_resi_proses" class="fw-bold text-dark m-0">-</h4>
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <input type="radio" class="btn-check" name="opsi_awal" id="opsi_terima" value="terima" onclick="toggleOpsiAwal()" checked>
                        <label class="btn btn-outline-success w-100 py-2 fw-bold" for="opsi_terima">TERIMA</label>

                        <input type="radio" class="btn-check" name="opsi_awal" id="opsi_tolak" value="tolak" onclick="toggleOpsiAwal()">
                        <label class="btn btn-outline-danger w-100 py-2 fw-bold" for="opsi_tolak">REJECT</label>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold" id="label_kategori">KATEGORI PENERIMAAN</label>
                        <select name="kategori" id="select_kategori" class="form-select border-primary" onchange="toggleKategori()">
                            </select>
                    </div>

                    <div id="div_data_paket">
                        <div class="mb-3">
                            <label class="small fw-bold">ENTITY</label>
                            <input type="text" name="entity" id="input_entity"
                                class="form-control fw-bold text-uppercase"
                                required
                                oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">NAMA VENDOR / PENGIRIM</label>
                            <input type="text" name="nama_vendor" id="input_vendor" class="form-control text-uppercase" placeholder="Masukkan nama vendor...">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">JUMLAH (QTY)</label>
                            <input type="number" name="qty" id="input_qty" class="form-control" value="1" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label id="label_keterangan" class="small fw-bold text-muted">CATATAN TAMBAHAN</label>
                        <textarea name="keterangan_umum" class="form-control" rows="2" placeholder="Catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleOpsiAwal() {
    const isTerima = document.getElementById('opsi_terima').checked;
    const selectKategori = document.getElementById('select_kategori');
    const header = document.getElementById('modal_header_bg');
    const labelKategori = document.getElementById('label_kategori');

    selectKategori.innerHTML = ""; // Reset

    if (isTerima) {
        header.className = "modal-header bg-success text-white";
        labelKategori.innerText = "KATEGORI PENERIMAAN";
        selectKategori.className = "form-select border-success";

        // Opsi untuk TERIMA
        const opt1 = new Option("DOKUMEN BARU", "baru");
        const opt2 = new Option("SUSULAN (LINK KE PENDING)", "susulan");
        selectKategori.add(opt1);
        selectKategori.add(opt2);
    } else {
        header.className = "modal-header bg-danger text-white";
        labelKategori.innerText = "ALASAN REJECT / PENDING";
        selectKategori.className = "form-select border-danger";

        // Opsi untuk REJECT
        const opt1 = new Option("RETURN KE PENGIRIM (REJECT)", "return");
        const opt2 = new Option("PENDING / INCOMPLETE", "incomplete");
        selectKategori.add(opt1);
        selectKategori.add(opt2);
    }
    toggleKategori();
}

function toggleKategori() {
    const kategori = document.getElementById('select_kategori').value;
    const divData = document.getElementById('div_data_paket');
    const inputVendor = document.getElementById('input_vendor');
    const inputEntity = document.getElementById('input_entity'); // Tambahkan ini

    // Selalu tampilkan div data paket
    divData.style.display = "block";

    // Validasi wajib isi
    inputVendor.required = true;
    inputEntity.required = true; // Entity wajib diisi manual
}

// Inisialisasi awal
toggleOpsiAwal();
</script>