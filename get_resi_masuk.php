<?php

include "koneksi.php";

// Ambil semua data yang masih di antrean depan (Status 0)
$sql = "SELECT id, nomor_resi, ekspedisi FROM scans WHERE status = 0 ORDER BY waktu_masuk DESC";
$query = mysqli_query($koneksi, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
