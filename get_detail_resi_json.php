<?php

include "koneksi.php";

if (isset($_GET['resi'])) {
    $resi = mysqli_real_escape_string($koneksi, $_GET['resi']);

    // Query mengambil semua data dari tabel scans berdasarkan nomor resi
    $sql = "SELECT * FROM scans WHERE nomor_resi = '$resi' LIMIT 1";
    $query = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
}
