<?php

session_start();
include "koneksi.php";

// Proteksi: Hanya admin yang bisa akses file ini
if (!isset($_SESSION['status_login']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak.");
}

$aksi = $_GET['aksi'];

if ($aksi == 'tambah') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Password default di-hash
    $pass_default = password_hash("app123", PASSWORD_DEFAULT);

    // 1. Siapkan string query
    $sql = "INSERT INTO users (username, password, nama_lengkap, role)
            VALUES ('$user', '$pass_default', '$nama', '$role')";

    // 2. JALANKAN QUERY (Baris ini yang tadi hilang)
    if (mysqli_query($koneksi, $sql)) {
        header("Location: index2.php?status=user_added");
    } else {
        // Jika gagal (misal username sudah ada), arahkan ke error
        header("Location: index2.php?error=db_error");
    }
    exit(); // Selalu gunakan exit setelah header redirect
} elseif ($aksi == 'hapus') {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Cegah admin menghapus dirinya sendiri
    if ($id != $_SESSION['user_id']) {
        mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id'");
        header("Location: index2.php?status=user_deleted");
    } else {
        header("Location: index2.php?error=self_delete");
    }
    exit();
}
