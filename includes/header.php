<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-shrink-0">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-2">
<div class="container">
        <?php
        // Menentukan tujuan link logo
        $logo_url = "index.php";
        if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == "login") {
            $logo_url = "index2.php";
        }
        ?>

        <a class="navbar-brand d-flex align-items-center" href="<?= $logo_url; ?>">
            <img src="assets/logo.png" alt="Logo" height="45" class="me-2">
            <div class="d-flex flex-column border-start ps-2" style="border-color: #dee2e6 !important;">
                <span class="fw-bold lh-1 text-dark" style="font-size: 1.1rem; letter-spacing: -0.5px;">LOGISTICS</span>
                <span class="text-muted lh-1 small" style="font-size: 0.7rem; letter-spacing: 1px;">MONITORING SYSTEM</span>
            </div>
        </a>

        <div class="ms-auto">
            <?php if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == "login"): ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-dark btn-sm dropdown-toggle fw-bold px-3" data-bs-toggle="dropdown">
                        Hi, <?= $_SESSION['nama_user'] ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item small <?= (basename($_SERVER['PHP_SELF']) == 'index2.php') ? 'fw-bold text-primary' : ''; ?>" href="index2.php">
                                📋 Dashboard Antrian
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item small <?= (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'fw-bold text-primary' : ''; ?>" href="laporan.php">
                                📊 Laporan Bulanan
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item small" href="#" data-bs-toggle="modal" data-bs-target="#profilModal">
                                ⚙️ Pengaturan Akun
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item small text-danger fw-bold" href="logout.php">
                                🚪 Logout
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <button type="button" class="btn btn-outline-dark btn-sm fw-bold px-4" data-bs-toggle="modal" data-bs-target="#loginModal">
                    LOGIN PIC
                </button>
            <?php endif; ?>
        </div>
    </div>
</nav>