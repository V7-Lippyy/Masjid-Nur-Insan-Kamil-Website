<?php
/**
 * Halaman pengumuman untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil semua data pengumuman aktif
$dataPengumuman = fetchAll("SELECT * FROM pengumuman WHERE status = 'aktif' AND (tanggal_selesai IS NULL OR tanggal_selesai >= CURDATE()) ORDER BY tanggal_mulai DESC");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Pengumuman</h1>
                <p class="lead">Informasi terbaru dari Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/pengumuman-hero.jpg" alt="Pengumuman Masjid" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Pengumuman Section -->
<section class="pengumuman-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Daftar Pengumuman</h2>
        
        <?php if (count($dataPengumuman) > 0): ?>
            <div class="row">
                <?php foreach ($dataPengumuman as $pengumuman): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow">
                            <?php if (!empty($pengumuman['gambar'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/pengumuman/<?= $pengumuman['gambar'] ?>" class="card-img-top" alt="<?= $pengumuman['judul'] ?>" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $pengumuman['judul'] ?></h5>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> <?= formatDate($pengumuman['tanggal_mulai']) ?>
                                    <?php if (!empty($pengumuman['tanggal_selesai'])): ?>
                                        - <?= formatDate($pengumuman['tanggal_selesai']) ?>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text"><?= substr(strip_tags($pengumuman['isi']), 0, 100) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="<?= BASE_URL ?>/pages/pengumuman_detail.php?id=<?= $pengumuman['id'] ?>" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada pengumuman yang tersedia.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
