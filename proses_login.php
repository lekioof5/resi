<?php

session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass = $_POST['password'];

    // Cari user berdasarkan username saja
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$user' LIMIT 1");

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        // Verifikasi password inputan dengan hash di database
        if (password_verify($pass, $data['password'])) {
            $_SESSION['status_login']  = "login";
            $_SESSION['user_id']       = $data['id'];
            $_SESSION['username_aktif'] = $data['username'];
            $_SESSION['nama_user']     = $data['nama_lengkap'];
            $_SESSION['role']          = $data['role'];

            header("Location: index2.php");
            exit;
        }
    }

    // Jika user tidak ketemu atau password salah
    header("Location: index.php?error=login_gagal");
    exit;
}
