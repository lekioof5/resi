<?php

include "koneksi.php";

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);

    // Cari berdasarkan Resi atau URN
    $sql = "SELECT * FROM scans
            WHERE nomor_resi = '$keyword'
            OR nomor_urn = '$keyword'
            LIMIT 1";

    $query = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
}
