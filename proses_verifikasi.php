<?php

session_start();
include "koneksi.php";

if (!isset($_SESSION['status_login'])) {
    die("Unauthorized");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_baru = mysqli_real_escape_string($koneksi, $_POST['id']);
    $tindakan = mysqli_real_escape_string($koneksi, $_POST['tindakan']);
    $nama_pic = $_SESSION['nama_user'];
    $waktu_sekarang = date('Y-m-d H:i:s');

    if ($tindakan == 'proses') {
        $urn = mysqli_real_escape_string($koneksi, $_POST['nomor_urn']);
        $sql = "UPDATE scans SET
                status = 2,
                nomor_urn = '$urn',
                is_validated = 1,
                nama_pic = '$nama_pic',
                waktu_proses = '$waktu_sekarang'
                WHERE id = '$id_baru'";

    } elseif ($tindakan == 'pending') {
        $vendor = mysqli_real_escape_string($koneksi, $_POST['nama_vendor']);
        $invoice = mysqli_real_escape_string($koneksi, $_POST['nomor_invoice']);
        $sql = "UPDATE scans SET
                status = 3,
                nama_vendor = '$vendor',
                nomor_invoice = '$invoice',
                is_validated = 1,
                nama_pic = '$nama_pic',
                waktu_pending = '$waktu_sekarang'
                WHERE id = '$id_baru'";

    } elseif ($tindakan == 'reject') {
        $alasan = mysqli_real_escape_string($koneksi, $_POST['alasan_reject']);
        $sql = "UPDATE scans SET
                status = 4,
                keterangan_reject = '$alasan',
                is_validated = 1,
                nama_pic = '$nama_pic',
                waktu_reject = '$waktu_sekarang'
                WHERE id = '$id_baru'";

    } elseif ($tindakan == 'resolve') {
        $id_data_lama = mysqli_real_escape_string($koneksi, $_POST['id_data_lama']);

        // 1. Ambil info resi baru untuk dicatat di histori data lama
        $res_baru = mysqli_query($koneksi, "SELECT nomor_resi FROM scans WHERE id = '$id_baru'");
        $data_baru = mysqli_fetch_assoc($res_baru);
        $resi_susulan = $data_baru['nomor_resi'];

        // 2. Update data LAMA: Ubah status dari Pending (3) kembali ke Received (1)
        // Agar PIC bisa memprosesnya kembali (input URN)
        $catatan = "Dokumen susulan diterima via resi: $resi_susulan";
        $sql_update_lama = "UPDATE scans SET
                            status = 1,
                            is_validated = 0,
                            keterangan_reject = '$catatan'
                            WHERE id = '$id_data_lama'";
        mysqli_query($koneksi, $sql_update_lama);

        // 3. Update data BARU: Tandai sebagai 'Linked' agar hilang dari antrian
        $sql = "UPDATE scans SET
                status = 5,
                is_validated = 1,
                nama_pic = '$nama_pic',
                keterangan_reject = 'Tersambung ke ID: $id_data_lama'
                WHERE id = '$id_baru'";
    }

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index2.php?status=success");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
