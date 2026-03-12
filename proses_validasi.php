<?php

session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_vendor = mysqli_real_escape_string($koneksi, $_POST['nama_vendor']);
    $jumlah = mysqli_real_escape_string($koneksi, $_POST['jumlah']);
    $urn = mysqli_real_escape_string($koneksi, $_POST['urn']);
    $reason = mysqli_real_escape_string($koneksi, $_POST['reason']);
    $nama_pic = $_SESSION['nama_user'];

    $sql = "UPDATE scans SET
            nama_vendor = '$nama_vendor',
            jumlah = '$jumlah',
            urn = '$urn',
            reason = '$reason',
            is_validated = 1,
            nama_pic = '$nama_pic',
            waktu_validasi = NOW()
            WHERE id = '$id'";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index2.php?status=success");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
