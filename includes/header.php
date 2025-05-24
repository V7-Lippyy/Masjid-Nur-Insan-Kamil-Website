<?php
/**
 * Header untuk halaman user/guest
 */

// Memuat file konfigurasi dan fungsi
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Ambil pengaturan website
$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['nama_masjid'] ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
                    <img src="<?= !empty($settings['logo']) ? BASE_URL . '/assets/uploads/' . $settings['logo'] : BASE_URL . '/assets/images/logo.png' ?>" alt="Logo" class="me-2" style="width: 40px; height: 40px;">
                    <span><?= $settings['nama_masjid'] ?></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>">Beranda</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLayanan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Layanan
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownLayanan">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/keuangan.php">Laporan Keuangan</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/inventaris.php">Daftar Inventaris</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/zakat.php">Zakat</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/qurban.php">Qurban</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/jadwal_imam_khatib.php">Jadwal Imam & Khatib</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/jadwal_shalat.php">Jadwal Shalat</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kegiatan.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/kegiatan.php">Kegiatan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pengumuman.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/pengumuman.php">Pengumuman</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'galeri.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/galeri.php">Galeri</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pengurus.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/pengurus.php">Pengurus</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'donasi.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/donasi.php">Donasi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'masukan.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/masukan.php">Masukan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-sm ms-2 px-3" href="<?= BASE_URL ?>/admin/login.php"><i class="fas fa-user me-1"></i> Login Admin</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main>
        <?= getAlert() ?>
