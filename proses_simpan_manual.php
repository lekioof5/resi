<?php

include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pt = strtoupper(mysqli_real_escape_string($koneksi, $_POST['nama_pt']));
    $no_hp   = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    // CEK DOUBLE ENTRY
    $cek = mysqli_query($koneksi, "SELECT id FROM scans WHERE nomor_resi = '$nama_pt' AND is_validated = 0");

    if (mysqli_num_rows($cek) > 0) {
        header("Location: index.php?error=double_entry");
        exit;
    }

    $sql = "INSERT INTO scans (nomor_resi, ekspedisi, is_validated, waktu_masuk)
            VALUES ('$nama_pt', '$no_hp', 0, NOW())";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php?success=1");
    }
}
