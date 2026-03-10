<?php
include "koneksi.php";
include "includes/header.php";

// Fetch Data
$query_baru = "SELECT * FROM scans WHERE is_validated = 0 ORDER BY waktu_masuk DESC LIMIT 10";
$result_baru = mysqli_query($koneksi, $query_baru);

$query_valid = "SELECT * FROM scans WHERE is_validated = 1 ORDER BY waktu_masuk DESC LIMIT 10";
$result_valid = mysqli_query($koneksi, $query_valid);
?>

<div class="container mt-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="scan-wrapper shadow-sm text-center">
                <div id="notification-area">
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success py-2 small alert-dismissible fade show">
                                Data berhasil tersimpan
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['error'])): ?>
                            <?php if ($_GET['error'] == 'double_scan' || $_GET['error'] == 'double_entry'): ?>
                                <div class="alert alert-danger border-0 shadow-sm mt-3 text-center small fw-bold alert-dismissible fade show">
                                    ⚠️ PERINGATAN: Nomor Resi / Nama PT sudah ada dalam antrian!
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                <h5 class="text-muted mb-3 fw-normal">Scan Resi Disini</h5>
                <form action="proses_simpan.php" method="POST" id="scanForm">
                    <input type="text" name="nomor_resi" id="resiInput"
                           class="form-control form-control-lg input-scan-field mb-3"
                           placeholder="Scan Barcode..." required autocomplete="off">

                    <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                        Bypass Scan (Tanpa Resi)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">ANTRIAN TERBARU</div>
                <div class="table-container p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th><th>RESI/PENGIRIM</th><th>EKSPEDISI/HP</th><th class="text-end">WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
while ($row = mysqli_fetch_assoc($result_baru)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="fw-bold"><?= $row['nomor_resi']; ?></td>
                                <td><?= $row['ekspedisi']; ?></td>
                                <td class="text-end text-muted small">
                                    <?php
                                    $waktu_db = strtotime($row['waktu_masuk']);
    if (date('Y-m-d', $waktu_db) == date('Y-m-d')) {
        // Jika hari ini, tampilkan jam saja
        echo "Hari ini, " . date('H:i', $waktu_db);
    } else {
        // Jika kemarin atau sebelumnya, tampilkan tanggal
        echo date('d M, H:i', $waktu_db);
    }
    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold border-start border-3 border-dark">RIWAYAT VALIDASI</div>
                <div class="table-container p-0">
                    <table class="table table-hover mb-0 text-muted">
                        <thead>
                            <tr>
                                <th>#</th><th>RESI</th><th>PIC</th><th class="text-end">WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no_v = 1;
while ($row_v = mysqli_fetch_assoc($result_valid)): ?>
                            <tr>
                                <td><?= $no_v++; ?></td>
                                <td><?= $row_v['nomor_resi']; ?></td>
                                <td><?= $row_v['nama_pic'] ?? 'Admin'; ?></td>
                                <td class="text-end small"><?= date('H:i', strtotime($row_v['waktu_masuk'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Panggil modal di sini agar struktur HTML tetap valid
include "includes/modal_manual.php";
include "includes/modal_login.php";
include "includes/footer.php";
?>