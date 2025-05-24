<?php
/**
 * Halaman pengurus untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Filter jabatan
$jabatanFilter = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';

// Ambil semua jabatan yang ada
$jabatanList = fetchAll("SELECT DISTINCT jabatan FROM pengurus WHERE status = 'aktif' ORDER BY FIELD(jabatan, 'Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara')");

// Ambil semua data pengurus aktif
$whereClause = "WHERE status = 'aktif'";
if ($jabatanFilter) {
    $whereClause .= " AND jabatan = '$jabatanFilter'";
}
$dataPengurus = fetchAll("SELECT * FROM pengurus $whereClause ORDER BY FIELD(jabatan, 'Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara'), nama ASC");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Pengurus Masjid</h1>
                <p class="lead">Mengenal lebih dekat dengan para pengurus Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/pengurus-hero.jpg" alt="Pengurus Masjid" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="jabatanDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Jabatan: <?= $jabatanFilter ? $jabatanFilter : 'Semua' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="jabatanDropdown">
                        <li><a class="dropdown-item" href="?">Semua Jabatan</a></li>
                        <?php foreach ($jabatanList as $jabatan): ?>
                            <li><a class="dropdown-item" href="?jabatan=<?= $jabatan['jabatan'] ?>"><?= $jabatan['jabatan'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pengurus Section -->
<section class="pengurus-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Daftar Pengurus
            <?php if ($jabatanFilter): ?>
                - <?= $jabatanFilter ?>
            <?php endif; ?>
        </h2>
        
        <?php if (count($dataPengurus) > 0): ?>
            <div class="row">
                <?php foreach ($dataPengurus as $pengurus): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow text-center">
                            <?php if (!empty($pengurus['foto'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/pengurus/<?= $pengurus['foto'] ?>" class="card-img-top mx-auto mt-3" alt="<?= $pengurus['nama'] ?>" style="height: 200px; width: 200px; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/assets/images/default-user.png" class="card-img-top mx-auto mt-3" alt="Default" style="height: 200px; width: 200px; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $pengurus['nama'] ?></h5>
                                <p class="card-text"><span class="badge bg-primary"><?= $pengurus['jabatan'] ?></span></p>
                                <?php if (!empty($pengurus['no_telepon'])): ?>
                                    <p class="card-text"><i class="fas fa-phone-alt me-1"></i> <?= $pengurus['no_telepon'] ?></p>
                                <?php endif; ?>
                                <?php if (!empty($pengurus['email'])): ?>
                                    <p class="card-text"><i class="fas fa-envelope me-1"></i> <?= $pengurus['email'] ?></p>
                                <?php endif; ?>
                                <?php if (!empty($pengurus['alamat'])): ?>
                                    <p class="card-text"><i class="fas fa-map-marker-alt me-1"></i> <?= $pengurus['alamat'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada data pengurus<?= $jabatanFilter ? " untuk jabatan $jabatanFilter" : "" ?>.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
