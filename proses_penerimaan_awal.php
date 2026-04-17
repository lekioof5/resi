<?php

session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $opsi = $_POST['opsi_awal']; // terima | tolak
    $kategori = $_POST['kategori']; // baru, susulan | return, incomplete
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan_umum']);
    $nama_pic = $_SESSION['nama_user'];

    // Tambahkan pengambilan data Entity dan gunakan trim agar bersih dari spasi liar
    $entity = strtoupper(trim(mysqli_real_escape_string($koneksi, $_POST['entity'])));
    $vendor = strtoupper(trim(mysqli_real_escape_string($koneksi, $_POST['nama_vendor'])));
    $qty = mysqli_real_escape_string($koneksi, $_POST['qty']);

    if ($opsi == 'terima') {
        if ($kategori == 'baru') {
            // Status 1: RECEIVED (Normal) - Masuk ke tab PROSES
            $sql = "UPDATE scans SET
                    status = 1,
                    entity = '$entity',
                    nama_vendor = '$vendor',
                    jumlah = '$qty',
                    keterangan_reject = '$keterangan',
                    nama_pic = '$nama_pic',
                    waktu_receive = NOW()
                    WHERE id = '$id'";
        } else {
            // Status 5: LINKED (Susulan) -> Langsung divalidasi (HISTORY)
            $sql = "UPDATE scans SET
                    status = 5,
                    entity = '$entity',
                    nama_vendor = '$vendor',
                    jumlah = '$qty',
                    keterangan_reject = 'SUSULAN: $keterangan',
                    nama_pic = '$nama_pic',
                    is_validated = 1,
                    waktu_proses = NOW()
                    WHERE id = '$id'";
        }
    } else {
        if ($kategori == 'return') {
            // Status 4: REJECTED (HISTORY)
            $sql = "UPDATE scans SET
                    status = 4,
                    entity = '$entity',
                    nama_vendor = '$vendor',
                    jumlah = '$qty',
                    keterangan_reject = 'REJECT: $keterangan',
                    nama_pic = '$nama_pic',
                    is_validated = 1,
                    waktu_reject = NOW()
                    WHERE id = '$id'";
        } else {
            // Status 3: PENDING / INCOMPLETE (Tab PENDING)
            $sql = "UPDATE scans SET
                    status = 3,
                    entity = '$entity',
                    nama_vendor = '$vendor',
                    jumlah = '$qty',
                    keterangan_reject = 'PENDING: $keterangan',
                    nama_pic = '$nama_pic',
                    waktu_pending = NOW()
                    WHERE id = '$id'";
        }
    }

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index2.php?status=success");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
