<?php
/**
 * Halaman galeri untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Filter kategori
$kategoriFilter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Ambil semua kategori yang ada
$kategoriList = fetchAll("SELECT DISTINCT kategori FROM galeri WHERE kategori != '' ORDER BY kategori ASC");

// Ambil semua data galeri
$whereClause = $kategoriFilter ? "WHERE kategori = '$kategoriFilter'" : "";
$dataGaleri = fetchAll("SELECT * FROM galeri $whereClause ORDER BY tanggal DESC");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Galeri Foto</h1>
                <p class="lead">Dokumentasi kegiatan dan momen penting di Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/galeri-hero.jpg" alt="Galeri Masjid" class="img-fluid rounded shadow">
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
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="kategoriDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Kategori: <?= $kategoriFilter ? $kategoriFilter : 'Semua' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="kategoriDropdown">
                        <li><a class="dropdown-item" href="?">Semua Kategori</a></li>
                        <?php foreach ($kategoriList as $kategori): ?>
                            <li><a class="dropdown-item" href="?kategori=<?= $kategori['kategori'] ?>"><?= $kategori['kategori'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Galeri Section -->
<section class="galeri-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Galeri Foto
            <?php if ($kategoriFilter): ?>
                - <?= $kategoriFilter ?>
            <?php endif; ?>
        </h2>
        
        <?php if (count($dataGaleri) > 0): ?>
            <div class="row" id="lightgallery">
                <?php foreach ($dataGaleri as $galeri): ?>
                    <div class="col-md-4 col-6 mb-4" data-src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $galeri['gambar'] ?>">
                        <a href="<?= BASE_URL ?>/pages/galeri_detail.php?id=<?= $galeri['id'] ?>" class="galeri-item">
                            <img src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $galeri['gambar'] ?>" class="img-fluid rounded shadow" alt="<?= $galeri['judul'] ?>" style="height: 250px; width: 100%; object-fit: cover;">
                            <div class="galeri-overlay">
                                <div class="galeri-title"><?= $galeri['judul'] ?></div>
                                <div class="galeri-date"><?= formatDate($galeri['tanggal']) ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada foto dalam galeri<?= $kategoriFilter ? " untuk kategori $kategoriFilter" : "" ?>.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript for Lightbox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0/lightgallery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const galleryElements = document.querySelectorAll('.galeri-item');
        
        galleryElements.forEach(function(element) {
            element.addEventListener('click', function(e) {
                // Allow normal navigation when clicking on the link
                // The lightbox will be initialized on the detail page
            });
        });
    });
</script>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
