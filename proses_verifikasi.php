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

    // Inisialisasi variabel SQL agar tidak undefined
    $sql = "";

    // Mulai Transaksi Database
    mysqli_begin_transaction($koneksi);

    try {
        if ($tindakan == 'proses') {
            $urn = mysqli_real_escape_string($koneksi, $_POST['nomor_urn']);
            $sql = "UPDATE scans SET
                    status = 2, nomor_urn = '$urn', is_validated = 1,
                    nama_pic = '$nama_pic', waktu_proses = '$waktu_sekarang'
                    WHERE id = '$id_baru'";

        } elseif ($tindakan == 'pending') {
            $vendor = mysqli_real_escape_string($koneksi, $_POST['nama_vendor']);
            $invoice = mysqli_real_escape_string($koneksi, $_POST['nomor_invoice']);
            $sql = "UPDATE scans SET
                    status = 3, nama_vendor = '$vendor', nomor_invoice = '$invoice',
                    is_validated = 1, nama_pic = '$nama_pic', waktu_pending = '$waktu_sekarang'
                    WHERE id = '$id_baru'";

        } elseif ($tindakan == 'reject') {
            $alasan = mysqli_real_escape_string($koneksi, $_POST['alasan_reject']);
            $sql = "UPDATE scans SET
                    status = 4, keterangan_reject = '$alasan', is_validated = 1,
                    nama_pic = '$nama_pic', waktu_reject = '$waktu_sekarang'
                    WHERE id = '$id_baru'";

        } elseif ($tindakan == 'resolve') {
            $id_data_lama = mysqli_real_escape_string($koneksi, $_POST['id_data_lama']);
            $urn_final = mysqli_real_escape_string($koneksi, $_POST['nomor_urn_resolve']);

            // 1. Ambil info resi untuk catatan
            $res_baru = mysqli_query($koneksi, "SELECT nomor_resi FROM scans WHERE id = '$id_baru'");
            $data_baru = mysqli_fetch_assoc($res_baru);
            $resi_susulan = $data_baru['nomor_resi'] ?? '-';

            $res_lama = mysqli_query($koneksi, "SELECT nomor_resi FROM scans WHERE id = '$id_data_lama'");
            $data_lama = mysqli_fetch_assoc($res_lama);
            $resi_utama = $data_lama['nomor_resi'] ?? '-';

            // 2. Update data LAMA
            $sql_update_lama = "UPDATE scans SET
                                status = 2, nomor_urn = '$urn_final', is_validated = 1,
                                keterangan_reject = 'Dilengkapi oleh resi susulan: $resi_susulan',
                                waktu_proses = '$waktu_sekarang'
                                WHERE id = '$id_data_lama'";
            mysqli_query($koneksi, $sql_update_lama);

            // 3. Update data BARU (sebagai variabel $sql utama)
            $sql = "UPDATE scans SET
                    status = 5, nomor_urn = '$urn_final', is_validated = 1,
                    nama_pic = '$nama_pic', keterangan_reject = 'Menyusul ke resi utama: $resi_utama',
                    waktu_proses = '$waktu_sekarang'
                    WHERE id = '$id_baru'";
        }

        // Jalankan query utama dan commit jika sukses
        if ($sql != "" && mysqli_query($koneksi, $sql)) {
            mysqli_commit($koneksi);
            header("Location: index2.php?status=success");
        } else {
            throw new Exception("Query Gagal");
        }

    } catch (Exception $e) {
        // Jika ada error, batalkan semua perubahan
        mysqli_rollback($koneksi);
        echo "Error: " . $e->getMessage();
    }
}
