<?php

session_start();
include "koneksi.php";

// Pastikan hanya user login yang bisa akses
if (!isset($_SESSION['status_login'])) {
    echo json_encode([]);
    exit;
}

// Ambil data yang statusnya 3 (Pending)
$pic_now = $_SESSION['nama_user'];
$query = mysqli_query($koneksi, "SELECT id, nomor_resi, nama_vendor, nomor_invoice FROM scans WHERE status = 3 AND nama_pic = '$pic_now'");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Kirim hasil dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);
