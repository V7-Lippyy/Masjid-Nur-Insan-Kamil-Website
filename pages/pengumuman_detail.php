<?php
/**
 * Halaman detail pengumuman untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil ID pengumuman
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Validasi ID
if (empty($id)) {
    setAlert('ID pengumuman tidak valid', 'danger');
    redirect(BASE_URL . '/pages/pengumuman.php');
}

// Ambil data pengumuman
$pengumuman = fetchOne("SELECT * FROM pengumuman WHERE id = $id");

// Validasi pengumuman
if (!$pengumuman) {
    setAlert('Pengumuman tidak ditemukan', 'danger');
    redirect(BASE_URL . '/pages/pengumuman.php');
}

// Ambil pengumuman terkait
$pengumumanTerkait = fetchAll("SELECT * FROM pengumuman WHERE id != $id AND status = 'aktif' AND (tanggal_selesai IS NULL OR tanggal_selesai >= CURDATE()) ORDER BY tanggal_mulai DESC LIMIT 3");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 fw-bold"><?= $pengumuman['judul'] ?></h1>
                <p class="lead text-muted">
                    <i class="fas fa-calendar-alt me-1"></i> <?= formatDate($pengumuman['tanggal_mulai']) ?>
                    <?php if (!empty($pengumuman['tanggal_selesai'])): ?>
                        - <?= formatDate($pengumuman['tanggal_selesai']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Detail Pengumuman Section -->
<section class="detail-pengumuman py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <?php if (!empty($pengumuman['gambar'])): ?>
                        <img src="<?= BASE_URL ?>/assets/uploads/pengumuman/<?= $pengumuman['gambar'] ?>" class="card-img-top" alt="<?= $pengumuman['judul'] ?>" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-text mt-3">
                            <?= $pengumuman['isi'] ?>
                        </div>
                    </div>
                </div>
                
                <!-- Share Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">Bagikan</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-around">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(getCurrentUrl()) ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(getCurrentUrl()) ?>&text=<?= urlencode($pengumuman['judul']) ?>" target="_blank" class="btn btn-outline-info">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($pengumuman['judul'] . ' - ' . getCurrentUrl()) ?>" target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/pages/pengumuman.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pengumuman
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Pengumuman Terkait -->
                <?php if (count($pengumumanTerkait) > 0): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">Pengumuman Terkait</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($pengumumanTerkait as $pt): ?>
                                    <li class="list-group-item">
                                        <a href="<?= BASE_URL ?>/pages/pengumuman_detail.php?id=<?= $pt['id'] ?>" class="text-decoration-none">
                                            <h6 class="mb-1"><?= $pt['judul'] ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i> <?= formatDate($pt['tanggal_mulai']) ?>
                                            </small>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
