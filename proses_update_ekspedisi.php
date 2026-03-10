<?php

session_start();
include "koneksi.php";

// Proteksi keamanan
if (!isset($_SESSION['status_login'])) {
    die("Unauthorized");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $ekspedisi = mysqli_real_escape_string($koneksi, $_POST['ekspedisi']);

    // HANYA update kolom ekspedisi, JANGAN ubah is_validated
    $sql = "UPDATE scans SET
            ekspedisi = '$ekspedisi'
            WHERE id = '$id'";

    if (mysqli_query($koneksi, $sql)) {
        echo "success";
    } else {
        echo "error";
    }
}
