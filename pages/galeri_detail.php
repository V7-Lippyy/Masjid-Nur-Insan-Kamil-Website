<?php
/**
 * Halaman detail galeri untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil ID galeri
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Validasi ID
if (empty($id)) {
    setAlert('ID galeri tidak valid', 'danger');
    redirect(BASE_URL . '/pages/galeri.php');
}

// Ambil data galeri
$galeri = fetchOne("SELECT * FROM galeri WHERE id = $id");

// Validasi galeri
if (!$galeri) {
    setAlert('Foto galeri tidak ditemukan', 'danger');
    redirect(BASE_URL . '/pages/galeri.php');
}

// Ambil galeri terkait (kategori yang sama)
$galeriTerkait = fetchAll("SELECT * FROM galeri WHERE kategori = '{$galeri['kategori']}' AND id != $id ORDER BY tanggal DESC LIMIT 6");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 fw-bold"><?= $galeri['judul'] ?></h1>
                <p class="lead">
                    <span class="badge bg-primary"><?= $galeri['kategori'] ?></span>
                    <span class="text-muted"><?= formatDate($galeri['tanggal']) ?></span>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Detail Galeri Section -->
<section class="detail-galeri py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <img src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $galeri['gambar'] ?>" class="img-fluid rounded" alt="<?= $galeri['judul'] ?>" id="mainImage">
                        
                        <?php if (!empty($galeri['deskripsi'])): ?>
                            <div class="mt-4 text-start">
                                <h4>Deskripsi</h4>
                                <p><?= $galeri['deskripsi'] ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Galeri Terkait -->
                <?php if (count($galeriTerkait) > 0): ?>
                    <div class="card shadow">
                        <div class="card-body">
                            <h3 class="card-title">Foto Terkait</h3>
                            <div class="row mt-3">
                                <?php foreach ($galeriTerkait as $gt): ?>
                                    <div class="col-md-4 col-6 mb-3">
                                        <a href="<?= BASE_URL ?>/pages/galeri_detail.php?id=<?= $gt['id'] ?>">
                                            <img src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $gt['gambar'] ?>" class="img-fluid rounded" alt="<?= $gt['judul'] ?>" style="height: 120px; width: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>/pages/galeri.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Galeri
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Lightbox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0/lightgallery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.getElementById('mainImage');
        
        if (mainImage) {
            mainImage.addEventListener('click', function() {
                const items = [
                    {
                        src: this.src,
                        thumb: this.src,
                        subHtml: `<h4>${<?= json_encode($galeri['judul']) ?>}</h4><p>${<?= json_encode($galeri['deskripsi']) ?>}</p>`
                    }
                ];
                
                const lightGallery = window.lightGallery(document.createElement('div'), {
                    dynamic: true,
                    dynamicEl: items,
                    download: false,
                    plugins: [lgZoom, lgThumbnail],
                });
                
                lightGallery.openGallery(0);
            });
        }
    });
</script>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
