<?php

include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Bersihkan input resi (Kapital & Hapus Spasi)
    $resi = strtoupper(trim($_POST['nomor_resi']));

    // Cek double scan untuk resi yang belum divalidasi
    $cek = mysqli_query($koneksi, "SELECT id FROM scans WHERE nomor_resi = '$resi' AND is_validated = 0");

    if (mysqli_num_rows($cek) > 0) {
        header("Location: index.php?error=double_scan");
        exit;
    }

    // --- LOGIKA DETEKSI POLA EKSPEDISI ---
    $ekspedisi = "LAINNYA / MANUAL"; // Default sudah Kapital

    // 1. PREFIX HURUF
    if (preg_match('/^SPXID\d+$/i', $resi)) {
        $ekspedisi = "SHOPEE XPRESS";
    } elseif (preg_match('/^(SHP|NLID|NVID|NID)\w+$/i', $resi)) {
        $ekspedisi = "NINJA XPRESS";
    } elseif (preg_match('/^(JP|JX|JT)\d+$/i', $resi)) {
        $ekspedisi = "J&T EXPRESS";
    } elseif (preg_match('/^IDS\d+$/i', $resi)) {
        $ekspedisi = "ID EXPRESS";
    } elseif (preg_match('/^LP\d+$/i', $resi)) {
        $ekspedisi = "LION PARCEL";
    } elseif (preg_match('/^SAP\d+$/i', $resi)) {
        $ekspedisi = "SAP EXPRESS";
    } elseif (preg_match('/^TJR\d+$/i', $resi)) {
        $ekspedisi = "JNE (TJR)";
    } elseif (preg_match('/^1000\d{10,12}$/', $resi)) {
        $ekspedisi = "ANTERAJA";
    }

    // 2. POLA ANGKA KHUSUS
    elseif (preg_match('/^00\d{10}$/', $resi)) {
        $ekspedisi = "SICEPAT";
    }

    // 3. BERDASARKAN PANJANG DIGIT
    else {
        $panjang = strlen($resi);

        if (ctype_digit($resi)) {
            if ($panjang == 15 || $panjang == 16) {
                $ekspedisi = "JNE";
            } elseif ($panjang == 12) {
                $ekspedisi = "ID EXPRESS / TIKI";
            } elseif ($panjang == 11) {
                $ekspedisi = "POS INDONESIA";
            } elseif ($panjang == 13) {
                $ekspedisi = "J&T EXPRESS (CARGO)";
            }
        }
        // 4. ALFANUMERIK PENDEK
        elseif ($panjang == 8 && preg_match('/^[A-Z0-9]+$/', $resi)) {
            $ekspedisi = "WAHANA";
        }
    }

    // --- PENGAMAN TERAKHIR ---
    // Memastikan variabel ekspedisi benar-benar kapital sebelum masuk DB
    $ekspedisi_final = strtoupper($ekspedisi);
    $resi_clean = mysqli_real_escape_string($koneksi, $resi);

    // --- PROSES SIMPAN KE DATABASE ---
    $sql = "INSERT INTO scans (nomor_resi, ekspedisi, is_validated, waktu_masuk)
            VALUES ('$resi_clean', '$ekspedisi_final', 0, NOW())";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php?success=1");
    } else {
        header("Location: index.php?error=db_error");
    }
} else {
    header("Location: index.php");
}
