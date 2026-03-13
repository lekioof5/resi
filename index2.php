<?php
session_start();
if ($_SESSION['status_login'] != "login") {
    header("Location: index.php?error=akses_ditolak");
    exit;
}

include "koneksi.php";
include "includes/header.php";

// Ambil data yang BELUM divalidasi
$query_antrian = mysqli_query($koneksi, "SELECT * FROM scans WHERE is_validated = 0 ORDER BY waktu_masuk ASC");
?>

<div class="container py-5">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">DASHBOARD VALIDASI</h4>
        <p class="text-muted small">Halo, <?= $_SESSION['nama_user'] ?></p>
    </div>
    <div class="text-end">
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <button class="btn btn-primary btn-sm fw-bold px-3 me-2" data-bs-toggle="modal" data-bs-target="#manageUserModal">
                <i class="bi bi-people-fill"></i> KELOLA USER
            </button>
        <?php endif; ?>
        <span class="badge bg-dark px-3 py-2 text-uppercase">Role: <?= $_SESSION['role'] ?></span>
    </div>
</div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <ul class="nav nav-pills gap-2" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold btn-sm" onclick="filterAntrian('waiting', this)">
                        🆕 WAITING <span class="badge bg-danger ms-1" id="count-waiting">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold btn-sm" onclick="filterAntrian('received', this)">
                        📥 RECEIVED <span class="badge bg-primary ms-1" id="count-received">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold btn-sm" onclick="filterAntrian('pending', this)">
                        ⏳ PENDING <span class="badge bg-warning text-dark ms-1" id="count-pending">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold btn-sm" onclick="filterAntrian('history', this)">
                        ✅ HISTORY
                    </button>
                </li>
            </ul>

            <div id="refresh-loader" class="spinner-border spinner-border-sm text-muted opacity-0" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark"> <tr>
                        <th class="ps-4 col-time-fixed">WAKTU</th>
                        <th>DETAIL DOKUMEN / RESI</th>
                        <th>EKSPEDISI</th>
                        <th class="text-center" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="tabel-antrian-live">
                    <tr><td colspan="4" class="text-center py-5 text-muted">Menghubungkan ke server...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/realtime-antrian.js"></script>

<?php
include "includes/modal_verifikasi.php";
include "includes/modal_manual.php";
include "includes/modal_profil.php";
if ($_SESSION['role'] == 'admin') {
    include "includes/modal_manage_user.php";
}
include "includes/modal_proses_awal.php";
include "includes/modal_proses.php";
include "includes/footer.php";
?>