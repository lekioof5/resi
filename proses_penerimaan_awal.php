<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['status_login'])) {
    die("Unauthorized");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $opsi = $_POST['opsi_awal']; // terima | tolak
    $kategori = $_POST['kategori']; // baru, susulan | return, incomplete
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan_umum']);
    $nama_pic = $_SESSION['nama_user'];

    $entity = strtoupper(trim(mysqli_real_escape_string($koneksi, $_POST['entity'])));
    $vendor = strtoupper(trim(mysqli_real_escape_string($koneksi, $_POST['nama_vendor'])));
    $qty = mysqli_real_escape_string($koneksi, $_POST['qty']);

    // Mulai Transaksi
    mysqli_begin_transaction($koneksi);

    try {
        if ($opsi == 'terima') {
            if ($kategori == 'baru') {
                // STATUS 1: Masuk ke tab PROSES
                $sql = "UPDATE scans SET 
                        status = 1, entity = '$entity', nama_vendor = '$vendor', 
                        jumlah = '$qty', keterangan_umum = '$keterangan', 
                        nama_pic = '$nama_pic', waktu_receive = NOW() 
                        WHERE id = '$id'";
                mysqli_query($koneksi, $sql);

            } else if ($kategori == 'susulan') {
                // LOGIKA LINK KE PENDING
                $id_pending_lama = mysqli_real_escape_string($koneksi, $_POST['id_data_lama']);
                $urn_list = $_POST['nomor_urn_resolve'];
                $urn_clean = strtoupper(trim(mysqli_real_escape_string($koneksi, $urn_list[0])));

                // 1. Ambil nomor resi fisik (resi baru) untuk dicatat di resi lama
                $res_skrg = mysqli_query($koneksi, "SELECT nomor_resi FROM scans WHERE id = '$id'");
                $data_skrg = mysqli_fetch_assoc($res_skrg);
                $resi_fisik = $data_skrg['nomor_resi'];

                // 2. Update Resi Baru (id) menjadi STATUS 5 (LINKED)
                $sql_baru = "UPDATE scans SET 
                            status = 5, entity = '$entity', nama_vendor = '$vendor', 
                            jumlah = '$qty', is_validated = 1, nama_pic = '$nama_pic', 
                            keterangan_umum = 'Ditautkan ke resi pending ID: $id_pending_lama', 
                            waktu_proses = NOW() WHERE id = '$id'";
                mysqli_query($koneksi, $sql_baru);

                // 3. Update Resi Lama (pending) menjadi STATUS 2 (VALID)
                $sql_lama = "UPDATE scans SET 
                            status = 2, nomor_urn = '$urn_clean', is_validated = 1, 
                            nama_pic = '$nama_pic', 
                            keterangan_umum = CONCAT(IFNULL(keterangan_umum,''), ' | Susulan via resi: $resi_fisik'), 
                            waktu_proses = NOW() WHERE id = '$id_pending_lama'";
                mysqli_query($koneksi, $sql_lama);
            }
        } else {
            // OPSI TOLAK
            if ($kategori == 'return') {
                // STATUS 4: REJECTED
                $sql = "UPDATE scans SET 
                        status = 4, entity = '$entity', nama_vendor = '$vendor', 
                        jumlah = '$qty', keterangan_umum = 'REJECT: $keterangan', 
                        is_validated = 1, nama_pic = '$nama_pic', waktu_reject = NOW() 
                        WHERE id = '$id'";
            } else {
                // STATUS 3: PENDING / INCOMPLETE
                $sql = "UPDATE scans SET 
                        status = 3, entity = '$entity', nama_vendor = '$vendor', 
                        jumlah = '$qty', keterangan_umum = 'PENDING: $keterangan', 
                        nama_pic = '$nama_pic', waktu_pending = NOW() 
                        WHERE id = '$id'";
            }
            mysqli_query($koneksi, $sql);
        }

        // Jika semua sukses, simpan perubahan
        mysqli_commit($koneksi);
        header("Location: index2.php?status=success");

    } catch (Exception $e) {
        // Jika ada error, batalkan semua
        mysqli_rollback($koneksi);
        echo "Gagal memproses data: " . $e->getMessage();
    }
}