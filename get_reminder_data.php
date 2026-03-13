<?php

session_start();
include "koneksi.php";

$nama_pic = $_SESSION['nama_user'];
$query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM scans WHERE status = 3 AND nama_pic = '$nama_pic'");
$data = mysqli_fetch_assoc($query);

header('Content-Type: application/json');
echo json_encode(['my_pending' => (int) $data['total']]);
