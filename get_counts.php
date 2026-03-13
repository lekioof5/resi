<?php

session_start();
include "koneksi.php";

$my_name = $_SESSION['nama_user'];

$sql = "SELECT
    COUNT(CASE WHEN status = 0 THEN 1 END) as waiting,
    COUNT(CASE WHEN status = 1 AND nama_pic = '$my_name' THEN 1 END) as received,
    COUNT(CASE WHEN status = 3 AND nama_pic = '$my_name' THEN 1 END) as pending
    FROM scans";

$res = mysqli_query($koneksi, $sql);
echo json_encode(mysqli_fetch_assoc($res));
