<?php

session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pending_utama = mysqli_real_escape_string($koneksi, $_POST['id']);
    $metode = $_POST['metode_susulan']; // HARDCOPY atau SOFTCOPY
    $urn_list = $_POST['nomor_urn'];
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $nama_pic = $_SESSION['nama_user'];

    mysqli_begin_transaction($koneksi);

    try {
        $urn_clean = strtoupper(trim(mysqli_real_escape_string($koneksi, $urn_list[0])));

        if ($metode == 'HARDCOPY') {
            $id_resi_fisik = mysqli_real_escape_string($koneksi, $_POST['id_resi_susulan']);

            // Ambil info resi fisik untuk dicatat di log
            $res_fisik = mysqli_query($koneksi, "SELECT nomor_resi FROM scans WHERE id = '$id_resi_fisik'");
            $data_fisik = mysqli_fetch_assoc($res_fisik);
            $resi_fisik_val = $data_fisik['nomor_resi'];

            // 1. Update Resi Fisik yang baru masuk jadi Status 5 (LINKED)
            // Ini supaya resi tersebut hilang dari tab MASUK
            $sql_fisik = "UPDATE scans SET
                          status = 5,
                          is_validated = 1,
                          keterangan_umum = 'Melengkapi pending resi ID: $id_pending_utama',
                          waktu_proses = NOW()
                          WHERE id = '$id_resi_fisik'";
            mysqli_query($koneksi, $sql_fisik);

            $prefix_catatan = "[HARDCOPY via Resi: $resi_fisik_val] ";
        } else {
            $prefix_catatan = "[SOFTCOPY/EMAIL] ";
        }

        // 2. Update Resi Utama yang tadinya Pending (Status 3) menjadi Valid (Status 2)
        $catatan_final = $prefix_catatan . $catatan;
        $sql_utama = "UPDATE scans SET
                      status = 2,
                      nomor_urn = '$urn_clean',
                      keterangan_umum = '$catatan_final',
                      is_validated = 1,
                      nama_pic = '$nama_pic',
                      waktu_proses = NOW()
                      WHERE id = '$id_pending_utama'";

        if (!mysqli_query($koneksi, $sql_utama)) {
            throw new Exception("Gagal update data utama");
        }

        mysqli_commit($koneksi);
        header("Location: index2.php?status=success");

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "Error: " . $e->getMessage();
    }
}
