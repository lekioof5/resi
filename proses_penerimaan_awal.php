<?php

session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $opsi = $_POST['opsi_awal'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan_umum']);
    $nama_pic = $_SESSION['nama_user'];

    if ($opsi == 'terima') {
        $vendor = mysqli_real_escape_string($koneksi, $_POST['nama_vendor']);
        $qty = mysqli_real_escape_string($koneksi, $_POST['qty']);

        // Status 1 = Received, Masuk ke antrian verifikasi PIC
        $sql = "UPDATE scans SET
                status = 1,
                nama_vendor = '$vendor',
                qty = '$qty',
                keterangan_reject = '$keterangan', -- Masuk ke kolom keterangan yang sama
                nama_pic = '$nama_pic',
                waktu_receive = NOW()
                WHERE id = '$id'";
    } else {
        // Status 4 = Rejected, Langsung selesai (is_validated = 1)
        $sql = "UPDATE scans SET
                status = 4,
                keterangan_reject = '$keterangan',
                nama_pic = '$nama_pic',
                is_validated = 1,
                waktu_reject = NOW()
                WHERE id = '$id'";
    }

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index2.php?status=success");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
