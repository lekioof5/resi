<?php
// Ambil daftar user untuk ditampilkan di tabel dalam modal
$users_query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC");
?>
<div class="modal fade" id="manageUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white pt-4 px-4 border-0">
                <h5 class="modal-title fw-bold">MANAJEMEN USER</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 small">TAMBAH PIC BARU</h6>
                        <form action="proses_user.php?aksi=tambah" method="POST" class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="nama_lengkap" class="form-control form-control-sm" placeholder="Nama Lengkap" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="username" class="form-control form-control-sm" placeholder="Username" required>
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select form-select-sm">
                                    <option value="pic">PIC</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">TAMBAH</button>
                            </div>
                        </form>
                        <small class="text-muted" style="font-size: 0.7rem;">*Password default otomatis: <strong>app123</strong></small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>NAMA LENGKAP</th>
                                <th>USERNAME</th>
                                <th>ROLE</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($u = mysqli_fetch_assoc($users_query)): ?>
                            <tr>
                                <td><?= $u['nama_lengkap'] ?></td>
                                <td><code><?= $u['username'] ?></code></td>
                                <td><span class="badge bg-secondary" style="font-size: 0.7rem;"><?= strtoupper($u['role']) ?></span></td>
                                <td class="text-center">
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="proses_user.php?aksi=hapus&id=<?= $u['id'] ?>"
                                           class="btn btn-outline-danger btn-sm border-0"
                                           onclick="return confirm('Hapus akun ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Anda</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>