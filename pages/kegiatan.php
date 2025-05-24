<?php
/**
 * Halaman kegiatan untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Filter kategori
$kategoriFilter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil semua kategori yang ada
$kategoriList = fetchAll("SELECT DISTINCT kategori FROM kegiatan WHERE kategori != '' ORDER BY kategori ASC");

// Ambil semua data kegiatan
$whereClause = [];
if ($kategoriFilter) {
    $whereClause[] = "kategori = '$kategoriFilter'";
}
if ($statusFilter) {
    $whereClause[] = "status = '$statusFilter'";
} else {
    // Default tampilkan yang upcoming dan ongoing
    $whereClause[] = "status IN ('upcoming', 'ongoing')";
}

$whereString = count($whereClause) > 0 ? "WHERE " . implode(" AND ", $whereClause) : "";
$dataKegiatan = fetchAll("SELECT * FROM kegiatan $whereString ORDER BY tanggal_mulai ASC");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Kegiatan Masjid</h1>
                <p class="lead">Informasi tentang kegiatan dan acara yang diselenggarakan oleh Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/kegiatan-hero.jpg" alt="Kegiatan Masjid" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="kategoriDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Kategori: <?= $kategoriFilter ? $kategoriFilter : 'Semua' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="kategoriDropdown">
                        <li><a class="dropdown-item" href="?<?= $statusFilter ? "status=$statusFilter" : "" ?>">Semua Kategori</a></li>
                        <?php foreach ($kategoriList as $kategori): ?>
                            <li><a class="dropdown-item" href="?kategori=<?= $kategori['kategori'] ?><?= $statusFilter ? "&status=$statusFilter" : "" ?>"><?= $kategori['kategori'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Status: <?= $statusFilter ? ucfirst($statusFilter) : 'Upcoming & Ongoing' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                        <li><a class="dropdown-item" href="?<?= $kategoriFilter ? "kategori=$kategoriFilter" : "" ?>">Upcoming & Ongoing</a></li>
                        <li><a class="dropdown-item" href="?status=upcoming<?= $kategoriFilter ? "&kategori=$kategoriFilter" : "" ?>">Upcoming</a></li>
                        <li><a class="dropdown-item" href="?status=ongoing<?= $kategoriFilter ? "&kategori=$kategoriFilter" : "" ?>">Ongoing</a></li>
                        <li><a class="dropdown-item" href="?status=completed<?= $kategoriFilter ? "&kategori=$kategoriFilter" : "" ?>">Completed</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Kegiatan Section -->
<section class="kegiatan-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Daftar Kegiatan
            <?php if ($kategoriFilter): ?>
                - <?= $kategoriFilter ?>
            <?php endif; ?>
            <?php if ($statusFilter): ?>
                (<?= ucfirst($statusFilter) ?>)
            <?php endif; ?>
        </h2>
        
        <?php if (count($dataKegiatan) > 0): ?>
            <div class="row">
                <?php foreach ($dataKegiatan as $kegiatan): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow">
                            <?php if (!empty($kegiatan['poster'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $kegiatan['poster'] ?>" class="card-img-top" alt="<?= $kegiatan['nama_kegiatan'] ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/assets/images/default-event.png" class="card-img-top" alt="Default" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $kegiatan['nama_kegiatan'] ?></h5>
                                <p class="card-text small">
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
                                <p class="card-text">
                                    <i class="fas fa-calendar-alt me-1"></i> <?= formatDate($kegiatan['tanggal_mulai'], 'd M Y H:i') ?>
                                    <?php if (!empty($kegiatan['tanggal_selesai'])): ?>
                                        <br><i class="fas fa-calendar-check me-1"></i> <?= formatDate($kegiatan['tanggal_selesai'], 'd M Y H:i') ?>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?= $kegiatan['lokasi'] ?>
                                </p>
                                <p class="card-text"><?= substr(strip_tags($kegiatan['deskripsi']), 0, 100) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="<?= BASE_URL ?>/pages/kegiatan_detail.php?id=<?= $kegiatan['id'] ?>" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada kegiatan yang tersedia.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
