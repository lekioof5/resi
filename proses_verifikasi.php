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

    // Flag untuk menentukan keberhasilan transaksi
    $success_flag = false;

    // Mulai Transaksi Database
    mysqli_begin_transaction($koneksi);

    try {
        if ($tindakan == 'proses') {
            // Ambil array URN dan data tambahan dari field_proses
            $urn_list = $_POST['nomor_urn']; // Array dari input dinamis
            $vendor = mysqli_real_escape_string($koneksi, $_POST['nama_vendor_proses']);
            $jumlah_total = mysqli_real_escape_string($koneksi, $_POST['jumlah_proses']);
            $reason = mysqli_real_escape_string($koneksi, $_POST['catatan_proses']);

            // Ambil data resi asli (ekspedisi & waktu_masuk) untuk diduplikasi ke baris baru
            $res_asal = mysqli_query($koneksi, "SELECT * FROM scans WHERE id = '$id_baru'");
            $data_asal = mysqli_fetch_assoc($res_asal);

            if (!$data_asal) {
                throw new Exception("Data resi asal tidak ditemukan.");
            }

            $nomor_resi = $data_asal['nomor_resi'];
            $ekspedisi = $data_asal['ekspedisi'];
            $waktu_masuk = $data_asal['waktu_masuk'];

            foreach ($urn_list as $index => $urn_val) {
                $urn_clean = mysqli_real_escape_string($koneksi, $urn_val);

                if ($index === 0) {
                    // 1. Baris pertama: UPDATE data yang sudah ada di tabel antrian (id yang di-klik)
                    $sql_utama = "UPDATE scans SET
                                    status = 2,
                                    nomor_urn = '$urn_clean',
                                    nama_vendor = '$vendor',
                                    jumlah = '$jumlah_total',
                                    keterangan_reject = '$reason',
                                    is_validated = 1,
                                    nama_pic = '$nama_pic',
                                    waktu_proses = '$waktu_sekarang'
                                  WHERE id = '$id_baru'";
                    mysqli_query($koneksi, $sql_utama);
                } else {
                    // 2. Baris berikutnya: INSERT baris baru (copy data resi, ganti URN)
                    $sql_insert = "INSERT INTO scans
                                    (nomor_resi, ekspedisi, nomor_urn, nama_vendor, jumlah, status, is_validated, nama_pic, waktu_masuk, waktu_proses, keterangan_reject)
                                   VALUES
                                    ('$nomor_resi', '$ekspedisi', '$urn_clean', '$vendor', '$jumlah_total', 2, 1, '$nama_pic', '$waktu_masuk', '$waktu_sekarang', '$reason')";
                    mysqli_query($koneksi, $sql_insert);
                }
            }
            $success_flag = true;

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
            $success_flag = mysqli_query($koneksi, $sql);

        } elseif ($tindakan == 'reject') {
            $alasan = mysqli_real_escape_string($koneksi, $_POST['alasan_reject']);
            $vendor_reject = mysqli_real_escape_string($koneksi, $_POST['nama_vendor_reject']);
            $qty_reject = mysqli_real_escape_string($koneksi, $_POST['jumlah_reject']);

            $sql = "UPDATE scans SET
                        status = 4,
                        nama_vendor = '$vendor_reject',
                        jumlah = '$qty_reject',
                        keterangan_reject = '$alasan',
                        is_validated = 1,
                        nama_pic = '$nama_pic',
                        waktu_reject = '$waktu_sekarang'
                    WHERE id = '$id_baru'";
            $success_flag = mysqli_query($koneksi, $sql);

        } elseif ($tindakan == 'resolve') {
            $id_data_lama = mysqli_real_escape_string($koneksi, $_POST['id_data_lama']); // ID Resi Utama (Pending)
            $urn_list_resolve = $_POST['nomor_urn_resolve']; // Array URN baru

            // 1. Ambil info dari Resi Utama (untuk copy data vendor, dsb)
            $res_lama = mysqli_query($koneksi, "SELECT * FROM scans WHERE id = '$id_data_lama'");
            $data_lama = mysqli_fetch_assoc($res_lama);
            $resi_utama = $data_lama['nomor_resi'] ?? '-';
            $vendor = $data_lama['nama_vendor'];
            $ekspedisi = $data_lama['ekspedisi'];
            $waktu_masuk_utama = $data_lama['waktu_masuk'];

            // 2. Ambil info dari Resi Susulan (untuk keterangan)
            $res_baru = mysqli_query($koneksi, "SELECT nomor_resi FROM scans WHERE id = '$id_baru'");
            $data_baru = mysqli_fetch_assoc($res_baru);
            $resi_susulan = $data_baru['nomor_resi'] ?? '-';

            // 3. Loop setiap URN yang dibawa oleh resi susulan
            foreach ($urn_list_resolve as $urn_val) {
                $urn_clean = mysqli_real_escape_string($koneksi, $urn_val);

                // Masukkan sebagai baris baru yang SUDAH VALID (status 2)
                // Kita hubungkan dengan resi UTAMA agar history-nya benar
                $sql_insert_doc = "INSERT INTO scans
                (nomor_resi, ekspedisi, nomor_urn, nama_vendor, status, is_validated, nama_pic, waktu_masuk, waktu_proses, keterangan_reject)
                VALUES
                ('$resi_utama', '$ekspedisi', '$urn_clean', '$vendor', 2, 1, '$nama_pic', '$waktu_masuk_utama', '$waktu_sekarang', 'Dokumen susulan dari resi: $resi_susulan')";
                mysqli_query($koneksi, $sql_insert_doc);
            }

            // 4. Update status Resi Susulan (id_baru) menjadi "Sudah Ditautkan" (status 5)
            $sql = "UPDATE scans SET
                    status = 5,
                    is_validated = 1,
                    nama_pic = '$nama_pic',
                    keterangan_reject = 'Menyusul ke resi utama: $resi_utama',
                    waktu_proses = '$waktu_sekarang'
                WHERE id = '$id_baru'";
            $success_flag = mysqli_query($koneksi, $sql);
        }

        // Commit jika semua query berhasil
        if ($success_flag) {
            mysqli_commit($koneksi);
            header("Location: index2.php?status=success");
            exit();
        } else {
            throw new Exception("Eksekusi query gagal.");
        }

    } catch (Exception $e) {
        // Batalkan semua jika ada error
        mysqli_rollback($koneksi);
        echo "Gagal memproses data: " . $e->getMessage();
    }
}
