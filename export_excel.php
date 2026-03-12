<?php
session_start();
include "koneksi.php";

// Proteksi Akses
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "login") {
    die("Akses ditolak.");
}

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

// Konfigurasi Header untuk Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Logistik_" . $bulan . "_" . $tahun . ".xls");

$query = "SELECT * FROM scans
          WHERE is_validated = 1
          AND MONTH(waktu_validasi) = '$bulan'
          AND YEAR(waktu_validasi) = '$tahun'
          ORDER BY waktu_validasi ASC";
$result = mysqli_query($koneksi, $query);
?>

<center>
    <h2>LAPORAN VALIDASI LOGISTIK</h2>
    <p>Periode: <?= $bulan ?> / <?= $tahun ?></p>
</center>

<table border="1">
    <thead>
        <tr style="background-color: #333; color: #fff; font-weight: bold;">
            <th>No</th>
            <th>No Resi</th>
            <th>Ekspedisi</th>
            <th>Tanggal Terima (Scan)</th>
            <th>Nama Vendor</th>
            <th>Entity (Jumlah)</th>
            <th>URN</th>
            <th>Waktu Validasi</th>
            <th>Petugas (PIC)</th>
            <th>Reason / Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td style="mso-number-format:'\@';">'<?= $row['nomor_resi'] ?></td>
            <td><?= $row['ekspedisi'] ?></td>
            <td><?= $row['waktu_masuk'] ?></td>
            <td><?= $row['nama_vendor'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= $row['urn'] ?></td>
            <td><?= $row['waktu_validasi'] ?></td>
            <td><?= $row['nama_pic'] ?></td>
            <td><?= $row['reason'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>