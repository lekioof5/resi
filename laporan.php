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

/**
 * QUERY UTAMA
 * Mengambil semua data yang is_validated = 1
 * Filter mencakup waktu_proses (Valid), waktu_pending (Pending), atau waktu_reject (Reject)
 */
$query = "SELECT * FROM scans
          WHERE is_validated = 1
          AND (
            (MONTH(waktu_proses) = '$bulan' AND YEAR(waktu_proses) = '$tahun') OR
            (MONTH(waktu_pending) = '$bulan' AND YEAR(waktu_pending) = '$tahun') OR
            (MONTH(waktu_reject) = '$bulan' AND YEAR(waktu_reject) = '$tahun')
          )
          ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

/**
 * QUERY RINGKASAN STATUS
 * Menghitung total masing-masing status untuk periode terpilih
 */
$query_summary = "SELECT
    SUM(CASE WHEN status = 2 OR status = 5 THEN 1 ELSE 0 END) as total_valid,
    SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as total_pending,
    SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as total_reject
    FROM scans
    WHERE is_validated = 1
    AND (
        (MONTH(waktu_proses) = '$bulan' AND YEAR(waktu_proses) = '$tahun') OR
        (MONTH(waktu_pending) = '$bulan' AND YEAR(waktu_pending) = '$tahun') OR
        (MONTH(waktu_reject) = '$bulan' AND YEAR(waktu_reject) = '$tahun')
    )";
$summary_res = mysqli_query($koneksi, $query_summary);
$sum = mysqli_fetch_assoc($summary_res);
?>

<?php include "includes/header.php"; ?>

<div class="container mt-4 mb-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Laporan Validasi Dokumen</h4>
            <p class="text-muted small mb-0">Periode: <?= date('F', mktime(0, 0, 0, $bulan, 10)) ?> <?= $tahun ?></p>
        </div>
        <div class="col-auto">
            <a href="export_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success fw-bold shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Ekspor ke Excel
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body p-3 text-center">
                    <small class="text-uppercase fw-bold opacity-75 d-block" style="font-size: 0.7rem;">Valid / Selesai</small>
                    <h2 class="fw-bold mb-0"><?= $sum['total_valid'] ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body p-3 text-center">
                    <small class="text-uppercase fw-bold opacity-75 d-block" style="font-size: 0.7rem;">Pending / Kurang</small>
                    <h2 class="fw-bold mb-0"><?= $sum['total_pending'] ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body p-3 text-center">
                    <small class="text-uppercase fw-bold opacity-75 d-block" style="font-size: 0.7rem;">Reject / Return</small>
                    <h2 class="fw-bold mb-0"><?= $sum['total_reject'] ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <label class="small fw-bold text-muted">Bulan</label>
                    <select name="bulan" class="form-select border-0 shadow-sm">
                        <?php
                        $bulan_list = ["01" => "Januari","02" => "Februari","03" => "Maret","04" => "April","05" => "Mei","06" => "Juni","07" => "Juli","08" => "Agustus","09" => "September","10" => "Oktober","11" => "November","12" => "Desember"];
foreach ($bulan_list as $key => $val) {
    $selected = ($key == $bulan) ? "selected" : "";
    echo "<option value='$key' $selected>$val</option>";
}
?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Tahun</label>
                    <select name="tahun" class="form-select border-0 shadow-sm">
                        <?php
for ($y = date('Y'); $y >= 2024; $y--) {
    $selected = ($y == $tahun) ? "selected" : "";
    echo "<option value='$y' $selected>$y</option>";
}
?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">Tampilkan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-dark">
                    <tr class="text-nowrap">
                        <th class="ps-3 py-3">No</th>
                        <th>No. Resi & Kurir</th>
                        <th>Vendor & URN</th>
                        <th class="text-center">Status</th>
                        <th>Waktu Keputusan</th>
                        <th>PIC</th>
                        <th class="pe-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
if (mysqli_num_rows($result) > 0):
    while ($row = mysqli_fetch_assoc($result)):
        // Logika Warna Badge dan Waktu Berdasarkan Status
        $status_badge = "";
        $waktu_tampil = "";
        if ($row['status'] == 2) {
            $status_badge = '<span class="badge rounded-pill bg-success px-3">VALID</span>';
            $waktu_tampil = $row['waktu_proses'];
        } elseif ($row['status'] == 3) {
            $status_badge = '<span class="badge rounded-pill bg-warning text-dark px-3">PENDING</span>';
            $waktu_tampil = $row['waktu_pending'];
        } elseif ($row['status'] == 4) {
            $status_badge = '<span class="badge rounded-pill bg-danger px-3">REJECT</span>';
            $waktu_tampil = $row['waktu_reject'];
        } elseif ($row['status'] == 5) {
            $status_badge = '<span class="badge rounded-pill bg-info text-dark px-3">LINKED</span>';
            $waktu_tampil = $row['waktu_proses'];
        }
        ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold"><?= $row['nomor_resi'] ?></div>
                            <small class="badge bg-light text-dark border"><?= $row['ekspedisi'] ?></small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary"><?= $row['nama_vendor'] ?: '-' ?></div>
                            <div class="small text-muted">URN: <?= $row['nomor_urn'] ?: '-' ?></div>
                        </td>
                        <td class="text-center"><?= $status_badge ?></td>
                        <td>
                            <div class="small fw-bold text-dark">
                                <?= $waktu_tampil != '0000-00-00 00:00:00' ? date('d/m/Y', strtotime($waktu_tampil)) : '-' ?>
                            </div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <?= $waktu_tampil != '0000-00-00 00:00:00' ? date('H:i', strtotime($waktu_tampil)) : '-' ?> WIB
                            </div>
                        </td>
                        <td>
                            <small><i class="bi bi-person-circle me-1"></i> <?= $row['nama_pic'] ?></small>
                        </td>
                        <td class="pe-3">
                            <small class="text-muted">
                                <?= $row['keterangan_reject'] ?: ($row['nomor_invoice'] ?: '-') ?>
                            </small>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            Tidak ada data validasi ditemukan untuk periode ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Include modals
include "includes/modal_manual.php";
include "includes/modal_profil.php";
if ($_SESSION['role'] == 'admin') {
    include "includes/modal_manage_user.php";
}
include "includes/footer.php";
?>