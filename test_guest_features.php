<?php
/**
 * Halaman pengujian fitur guest
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/test_functions.php';

// Jalankan semua tes
$testResults = runAllTests();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengujian Fitur Guest - Masjid Nur Insan Kamil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Pengujian Fitur Guest</h1>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Hasil Pengujian</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Fitur</th>
                                <th>Status</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testResults as $feature => $result): ?>
                                <tr>
                                    <td><?= ucfirst(str_replace('_', ' ', $feature)) ?></td>
                                    <td>
                                        <?php if (strpos($result, 'gagal') !== false || strpos($result, 'tidak memiliki') !== false): ?>
                                            <span class="badge bg-danger">Gagal</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Berhasil</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $result ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title mb-0">Navigasi Fitur Guest</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">Fitur View</div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/keuangan.php" class="text-decoration-none">
                                            <i class="fas fa-money-bill-wave me-2"></i> View Keuangan
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/kas_acara.php" class="text-decoration-none">
                                            <i class="fas fa-calendar-alt me-2"></i> View Kas Acara
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/inventaris.php" class="text-decoration-none">
                                            <i class="fas fa-boxes me-2"></i> View Inventaris
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/qurban_list.php" class="text-decoration-none">
                                            <i class="fas fa-list me-2"></i> View Daftar Pengqurban
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">Fitur Pendaftaran</div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/zakat_daftar.php" class="text-decoration-none">
                                            <i class="fas fa-hand-holding-usd me-2"></i> Pendaftaran Zakat
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/qurban_daftar.php" class="text-decoration-none">
                                            <i class="fas fa-sheep me-2"></i> Pendaftaran Qurban
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/donasi.php" class="text-decoration-none">
                                            <i class="fas fa-donate me-2"></i> Donasi
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <a href="<?= BASE_URL ?>" class="btn btn-primary">
                <i class="fas fa-home me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
