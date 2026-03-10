<?php

session_start();
include "koneksi.php";

// Pastikan hanya PIC yang login bisa memproses
if (!isset($_SESSION['status_login'])) {
    header("Location: index.php?error=unauthorized");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $nama_pic = $_SESSION['nama_user'];

    // Query ini sekarang mengisi status, nama PIC, dan waktu secara manual
    $sql = "UPDATE scans SET
            is_validated = 1,
            nama_pic = '$nama_pic',
            waktu_validasi = NOW()
            WHERE id = '$id'";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index2.php?status=validated");
    } else {
        echo "Gagal validasi: " . mysqli_error($koneksi);
    }
}
