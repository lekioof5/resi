<?php
// Konfigurasi Database
$host = "localhost";    // Biasanya localhost
$user = "root";         // Username default XAMPP adalah root
$pass = "";             // Password default XAMPP adalah kosong
$db   = "db_scan_resi"; // Ganti dengan nama database Anda

// Membuat Koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set timezone agar waktu scan akurat (WIB)
date_default_timezone_set('Asia/Jakarta');
?>