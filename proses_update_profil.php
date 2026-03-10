<?php

session_start();
include "koneksi.php";

if (!isset($_SESSION['status_login'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$new_nama = mysqli_real_escape_string($koneksi, $_POST['new_nama']);
$new_user = mysqli_real_escape_string($koneksi, $_POST['new_username']);
$new_pass = $_POST['new_password'];

// Update Nama dan Username
$query = "UPDATE users SET nama_lengkap = '$new_nama', username = '$new_user' WHERE id = '$user_id'";
mysqli_query($koneksi, $query);

// Update Session agar perubahan langsung terlihat di header
$_SESSION['nama_user'] = $new_nama;
$_SESSION['username_aktif'] = $new_user;

// Update Password jika diisi (Gunakan Hashing)
if (!empty($new_pass)) {
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
    mysqli_query($koneksi, "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'");
}

header("Location: index2.php?status=profile_updated");
