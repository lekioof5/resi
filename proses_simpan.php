<?php

include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kita cek apakah resi ini sudah ada dalam database (yang belum divalidasi)
    $resi = strtoupper(trim($_POST['nomor_resi'])); // trim() akan menghapus spasi liar
    $cek = mysqli_query($koneksi, "SELECT id FROM scans WHERE nomor_resi = '$resi' AND is_validated = 0");

    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, kembalikan dengan pesan error khusus
        header("Location: index.php?error=double_scan");
        exit;
    }

    // --- LOGIKA DETEKSI POLA EKSPEDISI INDONESIA (VERSI OPTIMASI) ---
    $ekspedisi = "Lainnya / Manual";

    // 1. PREFIX HURUF (Sangat Spesifik - Cek Ini Duluan)
    if (preg_match('/^SPXID\d+$/i', $resi)) {
        $ekspedisi = "Shopee Xpress";
    } elseif (preg_match('/^(SHP|NLID|NVID|NID)\w+$/i', $resi)) {
        $ekspedisi = "Ninja Xpress";
    } elseif (preg_match('/^(JP|JX|JT)\d+$/i', $resi)) {
        $ekspedisi = "J&T Express";
    } elseif (preg_match('/^IDS\d+$/i', $resi)) {
        $ekspedisi = "ID Express";
    } elseif (preg_match('/^LP\d+$/i', $resi)) {
        $ekspedisi = "Lion Parcel";
    } elseif (preg_match('/^SAP\d+$/i', $resi)) {
        $ekspedisi = "SAP Express";
    } elseif (preg_match('/^TJR\d+$/i', $resi)) {
        $ekspedisi = "JNE (TJR)";
    } elseif (preg_match('/^1000\d{10,12}$/', $resi)) {
        // Anteraja sangat spesifik diawali 1000 dan panjang total 14-15
        $ekspedisi = "Anteraja";
    }

    // 2. POLA ANGKA KHUSUS
    elseif (preg_match('/^00\d{10}$/', $resi)) {
        // SiCepat selalu 12 digit diawali 00
        $ekspedisi = "SiCepat";
    }

    // 3. BERDASARKAN PANJANG DIGIT (Hanya Angka)
    else {
        $panjang = strlen($resi);

        if (ctype_digit($resi)) {
            if ($panjang == 15 || $panjang == 16) {
                $ekspedisi = "JNE";
            } elseif ($panjang == 12) {
                // Jika 12 digit dan bukan SiCepat, kemungkinan TIKI atau ID Express
                // Di lapangan, 12 digit angka murni sekarang lebih sering ID Express
                $ekspedisi = "ID Express / TIKI";
            } elseif ($panjang == 11) {
                $ekspedisi = "POS Indonesia";
            } elseif ($panjang == 13) {
                $ekspedisi = "J&T Express (Cargo)";
            }
        }
        // 4. ALFANUMERIK PENDEK
        elseif ($panjang == 8 && preg_match('/^[A-Z0-9]+$/', $resi)) {
            $ekspedisi = "Wahana";
        }
    }

    // --- PROSES SIMPAN KE DATABASE ---
    $sql = "INSERT INTO scans (nomor_resi, ekspedisi, is_validated, waktu_masuk)
            VALUES ('$resi', '$ekspedisi', 0, NOW())";

    if (mysqli_query($koneksi, $sql)) {
        // Kembali ke index dengan notifikasi sukses
        header("Location: index.php?success=1");
    } else {
        // Kembali ke index dengan notifikasi error database
        header("Location: index.php?error=db_error");
    }
} else {
    header("Location: index.php");
}
