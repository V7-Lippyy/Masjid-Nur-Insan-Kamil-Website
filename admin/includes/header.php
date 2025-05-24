<?php
/**
 * Header untuk halaman admin
 */

// Memuat file konfigurasi dan fungsi
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';

// Memastikan user sudah login
requireLogin();

// Mengambil data admin yang sedang login
$admin = $_SESSION['admin'];

// Tambahkan logging untuk debugging session di header
error_log("HEADER SESSION CHECK - Admin session: " . (isset($_SESSION['admin']) ? json_encode($_SESSION['admin']) : "NOT SET"));
echo "<script>console.log('HEADER SESSION CHECK - Admin session: " . (isset($_SESSION['admin']) ? json_encode($_SESSION['admin']) : "NOT SET") . "');</script>";

// Mengambil pengaturan website
$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= $settings['nama_masjid'] ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-size: 0.9rem;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            background-size: cover;
        }
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            z-index: 1;
        }
        .sidebar-brand span {
            font-size: 0.65rem;
            display: block;
        }
        .sidebar-divider {
            margin: 0 1rem 1rem;
        }
        .sidebar .nav-item {
            position: relative;
        }
        .sidebar .nav-item .nav-link {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-item .nav-link:hover,
        .sidebar .nav-item .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-item .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        .sidebar-heading {
            padding: 0 1rem;
            font-weight: 800;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .content {
            flex: 1 0 auto;
        }
        .topbar {
            height: 4.375rem;
        }
        .topbar .navbar-search {
            width: 25rem;
        }
        .topbar .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: calc(4.375rem - 2rem);
            margin: auto 1rem;
        }
        .topbar .nav-item .nav-link {
            height: 4.375rem;
            display: flex;
            align-items: center;
            padding: 0 0.75rem;
        }
        .topbar .nav-item .nav-link .badge-counter {
            position: absolute;
            transform: scale(0.7);
            transform-origin: top right;
            right: 0.25rem;
            margin-top: -0.25rem;
        }
        .dropdown-list {
            padding: 0;
            border: none;
            overflow: hidden;
            width: 20rem;
        }
        .dropdown-list .dropdown-header {
            background-color: #4e73df;
            border: 1px solid #4e73df;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            color: #fff;
        }
        .dropdown-list .dropdown-item {
            white-space: normal;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border-left: 1px solid #e3e6f0;
            border-right: 1px solid #e3e6f0;
            border-bottom: 1px solid #e3e6f0;
        }
        .dropdown-list .dropdown-item .dropdown-list-image {
            position: relative;
            height: 2.5rem;
            width: 2.5rem;
        }
        .dropdown-list .dropdown-item .dropdown-list-image img {
            height: 2.5rem;
            width: 2.5rem;
            object-fit: cover;
        }
        .dropdown-list .dropdown-item .text-truncate {
            max-width: 13.375rem;
        }
        .dropdown-list .dropdown-item:active {
            background-color: #eaecf4;
            color: #3a3b45;
        }
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar col-lg-2 col-md-3 d-none d-md-block">
            <a class="sidebar-brand text-white" href="<?= ADMIN_URL ?>">
                <?= $settings['nama_masjid'] ?>
                <span>Admin Panel</span>
            </a>
            
            <hr class="sidebar-divider bg-white">
            
            <div class="sidebar-heading">Menu Utama</div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('index') ?>" href="<?= ADMIN_URL ?>">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('pengurus') ?>" href="<?= ADMIN_URL ?>/pengurus.php">
                        <i class="fas fa-fw fa-users"></i>
                        Pengurus
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('keuangan') ?>" href="<?= ADMIN_URL ?>/keuangan.php">
                        <i class="fas fa-fw fa-money-bill-wave"></i>
                        Keuangan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('kas_acara') ?>" href="<?= ADMIN_URL ?>/kas_acara.php">
                        <i class="fas fa-fw fa-wallet"></i>
                        Kas Acara
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('inventaris') ?>" href="<?= ADMIN_URL ?>/inventaris.php">
                        <i class="fas fa-fw fa-boxes"></i>
                        Inventaris
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('kegiatan') ?>" href="<?= ADMIN_URL ?>/kegiatan.php">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        Kegiatan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('pengumuman') ?>" href="<?= ADMIN_URL ?>/pengumuman.php">
                        <i class="fas fa-fw fa-bullhorn"></i>
                        Pengumuman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('galeri') ?>" href="<?= ADMIN_URL ?>/galeri.php">
                        <i class="fas fa-fw fa-images"></i>
                        Galeri
                    </a>
                </li>
            </ul>
            
            <hr class="sidebar-divider bg-white">
            
            <div class="sidebar-heading">Layanan</div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('zakat') ?>" href="<?= ADMIN_URL ?>/zakat.php">
                        <i class="fas fa-fw fa-hand-holding-usd"></i>
                        Zakat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('qurban') ?>" href="<?= ADMIN_URL ?>/qurban.php">
                        <i class="fas fa-fw fa-sheep"></i>
                        Qurban
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('donasi') ?>" href="<?= ADMIN_URL ?>/donasi.php">
                        <i class="fas fa-fw fa-donate"></i>
                        Donasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('donatur') ?>" href="<?= ADMIN_URL ?>/donatur.php">
                        <i class="fas fa-fw fa-user-friends"></i>
                        Donatur
                    </a>
                </li>
            </ul>
            
            <hr class="sidebar-divider bg-white">
            
            <div class="sidebar-heading">Jadwal</div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('jadwal_shalat') ?>" href="<?= ADMIN_URL ?>/jadwal_shalat.php">
                        <i class="fas fa-fw fa-clock"></i>
                        Jadwal Shalat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('jadwal_imam_khatib') ?>" href="<?= ADMIN_URL ?>/jadwal_imam_khatib.php">
                        <i class="fas fa-fw fa-user-tie"></i>
                        Jadwal Imam & Khatib
                    </a>
                </li>
            </ul>
            
            <hr class="sidebar-divider bg-white">
            
            <div class="sidebar-heading">Lainnya</div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= isActiveMenu('masukan') ?>" href="<?= ADMIN_URL ?>/masukan.php">
                        <i class="fas fa-fw fa-comment-alt"></i>
                        Masukan
                    </a>
                </li>
                <?php if (isSuperAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ADMIN_URL ?>/settings.php">
                        <i class="fas fa-fw fa-cog"></i>
                        Pengaturan
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ADMIN_URL ?>/logout.php">
                        <i class="fas fa-fw fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Content -->
        <div class="content col-lg-10 col-md-9 col-12">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow-sm">
                <!-- Sidebar Toggle (Mobile) -->
                <button class="btn btn-link d-md-none rounded-circle mr-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                
                <!-- Mobile Sidebar -->
                <div class="collapse d-md-none" id="sidebarMobile">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('index') ?>" href="<?= ADMIN_URL ?>">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('pengurus') ?>" href="<?= ADMIN_URL ?>/pengurus.php">
                                <i class="fas fa-fw fa-users"></i>
                                Pengurus
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('keuangan') ?>" href="<?= ADMIN_URL ?>/keuangan.php">
                                <i class="fas fa-fw fa-money-bill-wave"></i>
                                Keuangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('inventaris') ?>" href="<?= ADMIN_URL ?>/inventaris.php">
                                <i class="fas fa-fw fa-boxes"></i>
                                Inventaris
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('kegiatan') ?>" href="<?= ADMIN_URL ?>/kegiatan.php">
                                <i class="fas fa-fw fa-calendar-alt"></i>
                                Kegiatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('pengumuman') ?>" href="<?= ADMIN_URL ?>/pengumuman.php">
                                <i class="fas fa-fw fa-bullhorn"></i>
                                Pengumuman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('galeri') ?>" href="<?= ADMIN_URL ?>/galeri.php">
                                <i class="fas fa-fw fa-images"></i>
                                Galeri
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('zakat') ?>" href="<?= ADMIN_URL ?>/zakat.php">
                                <i class="fas fa-fw fa-hand-holding-usd"></i>
                                Zakat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('qurban') ?>" href="<?= ADMIN_URL ?>/qurban.php">
                                <i class="fas fa-fw fa-sheep"></i>
                                Qurban
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('jadwal_shalat') ?>" href="<?= ADMIN_URL ?>/jadwal_shalat.php">
                                <i class="fas fa-fw fa-clock"></i>
                                Jadwal Shalat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('jadwal_imam_khatib') ?>" href="<?= ADMIN_URL ?>/jadwal_imam_khatib.php">
                                <i class="fas fa-fw fa-user-tie"></i>
                                Jadwal Imam & Khatib
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActiveMenu('masukan') ?>" href="<?= ADMIN_URL ?>/masukan.php">
                                <i class="fas fa-fw fa-comment-alt"></i>
                                Masukan
                            </a>
                        </li>
                        <?php if (isSuperAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ADMIN_URL ?>/settings.php">
                                <i class="fas fa-fw fa-cog"></i>
                                Pengaturan
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ADMIN_URL ?>/logout.php">
                                <i class="fas fa-fw fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Topbar Navbar -->
                <ul class="navbar-nav ms-auto">
                    <div class="topbar-divider"></div>
                    
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-none d-lg-inline text-gray-600 small me-2"><?= $admin['nama_lengkap'] ?></span>
                            <i class="fas fa-user-circle fa-fw"></i>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="<?= ADMIN_URL ?>/profile.php">
                                <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                Profile
                            </a>
                            <?php if (isSuperAdmin()): ?>
                            <a class="dropdown-item" href="<?= ADMIN_URL ?>/settings.php">
                                <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                                Pengaturan
                            </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= ADMIN_URL ?>/logout.php">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <?= getAlert() ?>
