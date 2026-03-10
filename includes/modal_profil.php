<div class="modal fade" id="profilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">PENGATURAN AKUN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_update_profil.php" method="POST">
                <div class="modal-body py-3 px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NAMA LENGKAP</label>
                        <input type="text" name="new_nama" class="form-control bg-light border-0"
                            value="<?= $_SESSION['nama_user']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">USERNAME BARU</label>
                        <input type="text" name="new_username" class="form-control bg-light border-0"
                            value="<?= $_SESSION['username_aktif']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">PASSWORD BARU</label>
                        <input type="password" name="new_password" class="form-control bg-light border-0"
                            placeholder="Isi jika ingin ganti">
                        <small class="text-muted" style="font-size: 0.65rem;">Kosongkan jika tidak ingin mengubah password.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-dark w-100 fw-bold">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>