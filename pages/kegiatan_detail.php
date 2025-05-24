<?php
/**
 * Halaman detail kegiatan untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil ID kegiatan
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Validasi ID
if (empty($id)) {
    setAlert('ID kegiatan tidak valid', 'danger');
    redirect(BASE_URL . '/pages/kegiatan.php');
}

// Ambil data kegiatan
$kegiatan = fetchOne("SELECT * FROM kegiatan WHERE id = $id");

// Validasi kegiatan
if (!$kegiatan) {
    setAlert('Kegiatan tidak ditemukan', 'danger');
    redirect(BASE_URL . '/pages/kegiatan.php');
}

// Ambil kegiatan terkait (kategori yang sama)
$kegiatanTerkait = fetchAll("SELECT * FROM kegiatan WHERE kategori = '{$kegiatan['kategori']}' AND id != $id AND status IN ('upcoming', 'ongoing') ORDER BY tanggal_mulai ASC LIMIT 3");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 fw-bold"><?= $kegiatan['nama_kegiatan'] ?></h1>
                <p class="lead">
                    <span class="badge bg-primary"><?= $kegiatan['kategori'] ?></span>
                    <?php if ($kegiatan['status'] == 'upcoming'): ?>
                        <span class="badge bg-info">Akan Datang</span>
                    <?php elseif ($kegiatan['status'] == 'ongoing'): ?>
                        <span class="badge bg-success">Sedang Berlangsung</span>
                    <?php elseif ($kegiatan['status'] == 'completed'): ?>
                        <span class="badge bg-secondary">Selesai</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Dibatalkan</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Detail Kegiatan Section -->
<section class="detail-kegiatan py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <?php if (!empty($kegiatan['poster'])): ?>
                        <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $kegiatan['poster'] ?>" class="card-img-top" alt="<?= $kegiatan['nama_kegiatan'] ?>" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h2 class="card-title">Deskripsi Kegiatan</h2>
                        <div class="card-text mt-3">
                            <?= $kegiatan['deskripsi'] ?>
                        </div>
                    </div>
                </div>
                
                <!-- Kegiatan Terkait -->
                <?php if (count($kegiatanTerkait) > 0): ?>
                    <div class="card shadow">
                        <div class="card-body">
                            <h3 class="card-title">Kegiatan Terkait</h3>
                            <div class="row mt-3">
                                <?php foreach ($kegiatanTerkait as $kt): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <?php if (!empty($kt['poster'])): ?>
                                                <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $kt['poster'] ?>" class="card-img-top" alt="<?= $kt['nama_kegiatan'] ?>" style="height: 120px; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="<?= BASE_URL ?>/assets/images/default-event.png" class="card-img-top" alt="Default" style="height: 120px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h6 class="card-title"><?= $kt['nama_kegiatan'] ?></h6>
                                                <p class="card-text small"><?= formatDate($kt['tanggal_mulai'], 'd M Y') ?></p>
                                                <a href="<?= BASE_URL ?>/pages/kegiatan_detail.php?id=<?= $kt['id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Informasi Kegiatan</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-calendar-alt me-2"></i> <strong>Tanggal Mulai:</strong><br>
                                <?= formatDate($kegiatan['tanggal_mulai'], 'l, d F Y H:i') ?>
                            </li>
                            <?php if (!empty($kegiatan['tanggal_selesai'])): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-calendar-check me-2"></i> <strong>Tanggal Selesai:</strong><br>
                                    <?= formatDate($kegiatan['tanggal_selesai'], 'l, d F Y H:i') ?>
                                </li>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <i class="fas fa-map-marker-alt me-2"></i> <strong>Lokasi:</strong><br>
                                <?= $kegiatan['lokasi'] ?>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-tag me-2"></i> <strong>Kategori:</strong><br>
                                <?= $kegiatan['kategori'] ?>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-info-circle me-2"></i> <strong>Status:</strong><br>
                                <?php if ($kegiatan['status'] == 'upcoming'): ?>
                                    <span class="badge bg-info">Akan Datang</span>
                                <?php elseif ($kegiatan['status'] == 'ongoing'): ?>
                                    <span class="badge bg-success">Sedang Berlangsung</span>
                                <?php elseif ($kegiatan['status'] == 'completed'): ?>
                                    <span class="badge bg-secondary">Selesai</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </li>
                        </ul>
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
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(getCurrentUrl()) ?>&text=<?= urlencode($kegiatan['nama_kegiatan']) ?>" target="_blank" class="btn btn-outline-info">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($kegiatan['nama_kegiatan'] . ' - ' . getCurrentUrl()) ?>" target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/pages/kegiatan.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Kegiatan
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
