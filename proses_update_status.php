<?php

session_start();
include "koneksi.php";

// Proteksi akses
if (!isset($_SESSION['status_login'])) {
    die("unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $nama_pic = $_SESSION['nama_user']; // Pastikan session ini ada saat login

    // Update status, nama pic, dan waktu terima
    $sql = "UPDATE scans SET
            status = '$status',
            nama_pic = '$nama_pic',
            waktu_receive = NOW()
            WHERE id = '$id'";

    if (mysqli_query($koneksi, $sql)) {
        echo "success";
    } else {
        echo "error";
    }
}
