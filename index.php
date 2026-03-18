<?php
include "koneksi.php";
include "includes/header.php";

// 1. Ambil Total Row untuk penomoran (agar data terbaru angkanya besar)
$res_count = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM scans");
$total_data = mysqli_fetch_assoc($res_count)['total'];

// 2. Fetch Data Antrian Baru (Gunakan LIMIT yang lebih banyak agar carousel bisa jalan)
$query_baru = "SELECT * FROM scans WHERE is_validated = 0 ORDER BY waktu_masuk DESC LIMIT 15";
$result_baru = mysqli_query($koneksi, $query_baru);
$data_baru = mysqli_fetch_all($result_baru, MYSQLI_ASSOC);

// 3. Fetch Data Validasi
$query_valid = "SELECT * FROM scans WHERE is_validated = 1 ORDER BY waktu_masuk DESC LIMIT 15";
$result_valid = mysqli_query($koneksi, $query_valid);
$data_valid = mysqli_fetch_all($result_valid, MYSQLI_ASSOC);

// Fungsi pembantu untuk mencegah Error Deprecated strtotime
function formatWaktu($waktu)
{
    if (!$waktu || $waktu == '0000-00-00 00:00:00') {
        return '-';
    }
    return date('d M, H:i', strtotime($waktu));
}
?>

<style>
    /* Agar tinggi card seragam saat carousel bergeser */
    .carousel-item { min-height: 350px; }
    .table th { background-color: #f8f9fa; position: sticky; top: 0; }
</style>

<div class="container mt-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="scan-wrapper shadow-sm text-center bg-white p-4 rounded">
                <div id="notification-area">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success py-2 small alert-dismissible fade show">Data berhasil tersimpan</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger border-0 shadow-sm mt-3 text-center small fw-bold">
                            ⚠️ PERINGATAN: Nomor Resi / Nama PT sudah ada!
                        </div>
                    <?php endif; ?>
                </div>
                <h5 class="text-muted mb-3 fw-normal">Scan Resi Disini</h5>
                <form action="proses_simpan.php" method="POST" id="scanForm">
                    <input type="text" name="nomor_resi" id="resiInput" class="form-control form-control-lg mb-3 text-center" placeholder="Scan Barcode..." required autofocus autocomplete="off">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#manualEntryModal">TANPA RESI (Klik Disini)</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold bg-dark text-white">ANTRIAN TERBARU (Live)</div>
                <div id="carouselAntrian" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        <?php
                        $chunks = array_chunk($data_baru, 5); // Bagi data jadi 5 per slide
foreach ($chunks as $index => $batch):
    ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr><th>#</th><th>RESI/PENGIRIM</th><th class="text-end">WAKTU</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batch as $key => $row): ?>
                                    <tr>
                                        <td class="text-muted"><?= $total_data - (array_search($row, $data_baru)); ?></td>
                                        <td class="fw-bold"><?= $row['nomor_resi']; ?> <br><small class="fw-normal text-muted"><?= $row['ekspedisi']; ?></small></td>
                                        <td class="text-end text-muted small"><?= formatWaktu($row['waktu_masuk']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold border-start border-3 border-dark">RIWAYAT PROSES</div>
                <div id="carouselValidasi" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
                    <div class="carousel-inner">
                        <?php
    $chunks_v = array_chunk($data_valid, 5);
foreach ($chunks_v as $index_v => $batch_v):
    ?>
                        <div class="carousel-item <?= $index_v === 0 ? 'active' : '' ?>">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr><th>#</th><th>RESI</th><th>PIC</th><th class="text-end">WAKTU</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batch_v as $row_v): ?>
                                    <tr>
                                        <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                        <td><?= $row_v['nomor_resi']; ?></td>
                                        <td><?= $row_v['nama_pic'] ?? 'Admin'; ?></td>
                                        <td class="text-end text-muted small"><?= formatWaktu($row_v['waktu_proses']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const IS_INDEX_KURIR = true;
</script>

<script src="js/script.js"></script>

<?php
// Panggil modal di sini agar struktur HTML tetap valid
include "includes/modal_manual.php";
include "includes/modal_login.php";
include "includes/footer.php";
?>