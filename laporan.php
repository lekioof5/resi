<?php
session_start();
include "koneksi.php";

// Proteksi Akses: Hanya untuk yang sudah login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "login") {
    header("Location: index.php?error=unauthorized");
    exit;
}

// Ambil filter dari URL, default ke bulan & tahun berjalan
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query Data Utama
$query = "SELECT * FROM scans
          WHERE is_validated = 1
          AND MONTH(waktu_validasi) = '$bulan'
          AND YEAR(waktu_validasi) = '$tahun'
          ORDER BY waktu_validasi DESC";
$result = mysqli_query($koneksi, $query);

// Query Ringkasan (Total per Kurir)
$query_summary = "SELECT ekspedisi, COUNT(*) as total FROM scans
                  WHERE is_validated = 1
                  AND MONTH(waktu_validasi) = '$bulan'
                  AND YEAR(waktu_validasi) = '$tahun'
                  GROUP BY ekspedisi ORDER BY total DESC";
$summary_result = mysqli_query($koneksi, $query_summary);
?>

<?php include "includes/header.php"; ?>

<div class="container mt-4 mb-5">
    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold mb-0 text-dark">📊 Laporan Validasi</h4>
            <p class="text-muted small">Periode: <?= date('F', mktime(0, 0, 0, $bulan, 10)) ?> <?= $tahun ?></p>
        </div>
        <div class="col-auto">
            <a href="export_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success fw-bold shadow-sm">
                📗 Ekspor ke Excel
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php if (mysqli_num_rows($summary_result) > 0): ?>
            <?php while ($sum = mysqli_fetch_assoc($summary_result)): ?>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-3 text-center">
                        <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 0.6rem;"><?= $sum['ekspedisi'] ?></small>
                        <h3 class="fw-bold mb-0"><?= $sum['total'] ?></h3>
                        <small class="text-muted">Paket</small>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-3 bg-white rounded shadow-sm">
                <p class="text-muted mb-0">Tidak ada data untuk periode ini.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Pilih Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php
                        $bulan_list = ["01" => "Januari","02" => "Februari","03" => "Maret","04" => "April","05" => "Mei","06" => "Juni","07" => "Juli","08" => "Agustus","09" => "September","10" => "Oktober","11" => "November","12" => "Desember"];
foreach ($bulan_list as $key => $val) {
    $s = ($key == $bulan) ? "selected" : "";
    echo "<option value='$key' $s>$val</option>";
}
?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Pilih Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php
for ($y = date('Y'); $y >= 2024; $y--) {
    $s = ($y == $tahun) ? "selected" : "";
    echo "<option value='$y' $s>$y</option>";
}
?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark text-nowrap">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>No. Resi</th>
                        <th>Ekspedisi</th>
                        <th>Scan Masuk</th>
                        <th>Validasi</th>
                        <th>PIC Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $no++ ?></td>
                        <td class="fw-bold"><?= $row['nomor_resi'] ?></td>
                        <td><span class="badge bg-info text-dark"><?= $row['ekspedisi'] ?></span></td>
                        <td><small><?= $row['waktu_masuk'] ?></small></td>
                        <td><small><?= $row['waktu_validasi'] ?></small></td>
                        <td><i class="bi bi-person text-primary"></i> <?= $row['nama_pic'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include "includes/modal_manual.php";
include "includes/modal_profil.php";
if ($_SESSION['role'] == 'admin') {
    include "includes/modal_manage_user.php";
}
include "includes/footer.php";
?>