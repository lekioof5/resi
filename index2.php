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
        <h4 class="fw-bold mb-0">DASHBOARD VALIDASI PIC</h4>
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
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold"><i class="bi bi-list-check"></i> DAFTAR ANTRIAN BARANG</h6>
        </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 col-time-fixed">WAKTU MASUK</th>
                            <th>RESI / PENGIRIM</th>
                            <th>EKSPEDISI / HP</th>
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

<script>
function updateEkspedisi(id, nilaiBaru) {
    // Tampilkan loading simpel atau ganti warna teks
    const selectElement = event.target;
    selectElement.classList.add('text-primary');

    fetch('proses_update_ekspedisi.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&ekspedisi=${encodeURIComponent(nilaiBaru)}`
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            // Beri efek hijau kilat sebagai tanda sukses
            selectElement.classList.remove('text-primary');
            selectElement.classList.add('text-success');
            setTimeout(() => selectElement.classList.remove('text-success'), 1000);
        } else {
            alert("Gagal mengupdate ekspedisi.");
        }
    });
}
</script>

<?php
include "includes/modal_manual.php";
include "includes/modal_profil.php";
if ($_SESSION['role'] == 'admin') {
    include "includes/modal_manage_user.php";
}
include "includes/footer.php";
?>