<?php
/**
 * Halaman profil masjid untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil data pengurus (untuk ditampilkan di halaman profil)
$pengurus = fetchAll("SELECT * FROM pengurus WHERE status = 'aktif' ORDER BY FIELD(jabatan, 'Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara') LIMIT 8");
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Profil Masjid Nur Insan Kamil</h1>
                <p class="lead">Pusat Kegiatan Islam dan Pembinaan Umat</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/masjid-profile.jpg" alt="Masjid Nur Insan Kamil" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Sejarah Section -->
<section class="sejarah py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Sejarah Masjid</h2>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-body">
                        <p class="card-text">
                            Masjid Nur Insan Kamil didirikan pada tahun 1985 oleh sekelompok tokoh masyarakat yang peduli dengan perkembangan dakwah Islam di wilayah ini. Awalnya, masjid ini hanya berupa bangunan sederhana dengan kapasitas jamaah yang terbatas.
                        </p>
                        <p class="card-text">
                            Seiring berjalannya waktu, dengan dukungan dan partisipasi masyarakat sekitar, Masjid Nur Insan Kamil terus berkembang dan mengalami beberapa kali renovasi. Renovasi besar terakhir dilakukan pada tahun 2015, yang menghasilkan bangunan masjid yang megah dan nyaman seperti yang kita lihat sekarang.
                        </p>
                        <p class="card-text">
                            Nama "Nur Insan Kamil" dipilih dengan harapan masjid ini dapat menjadi sumber cahaya (nur) yang menerangi dan membimbing manusia menuju kesempurnaan (insan kamil) dalam beribadah dan bermuamalah sesuai dengan ajaran Islam.
                        </p>
                        <p class="card-text">
                            Saat ini, Masjid Nur Insan Kamil tidak hanya berfungsi sebagai tempat ibadah, tetapi juga sebagai pusat kegiatan Islam dan pembinaan umat, dengan berbagai program dan kegiatan yang dilaksanakan secara rutin.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi Misi Section -->
<section class="visi-misi py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Visi & Misi</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title text-center mb-0">Visi</h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Menjadikan Masjid Nur Insan Kamil sebagai pusat ibadah, pembinaan, dan peradaban Islam yang rahmatan lil 'alamin.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title text-center mb-0">Misi</h3>
                    </div>
                    <div class="card-body">
                        <ol class="card-text">
                            <li>Menyelenggarakan kegiatan ibadah yang khusyu' dan istiqomah</li>
                            <li>Mengembangkan pendidikan Islam yang komprehensif</li>
                            <li>Membangun ukhuwah islamiyah dan silaturahmi antar jamaah</li>
                            <li>Memberdayakan ekonomi umat melalui program-program sosial</li>
                            <li>Menjadi teladan dalam pengelolaan masjid yang profesional dan transparan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fasilitas Section -->
<section class="fasilitas py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Fasilitas Masjid</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="<?= BASE_URL ?>/assets/images/fasilitas-1.jpg" class="card-img-top" alt="Ruang Utama" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">Ruang Utama</h5>
                        <p class="card-text">Ruang shalat utama yang nyaman dengan kapasitas hingga 500 jamaah, dilengkapi dengan AC dan sound system berkualitas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="<?= BASE_URL ?>/assets/images/fasilitas-2.jpg" class="card-img-top" alt="Tempat Wudhu" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">Tempat Wudhu</h5>
                        <p class="card-text">Tempat wudhu yang bersih dan terpisah untuk pria dan wanita, dengan air yang mengalir lancar.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="<?= BASE_URL ?>/assets/images/fasilitas-3.jpg" class="card-img-top" alt="Perpustakaan" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">Perpustakaan</h5>
                        <p class="card-text">Perpustakaan dengan koleksi buku-buku Islam yang lengkap, dari tafsir, hadits, fiqih, hingga buku-buku Islami kontemporer.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="<?= BASE_URL ?>/assets/images/fasilitas-4.jpg" class="card-img-top" alt="Ruang Serbaguna" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">Ruang Serbaguna</h5>
                        <p class="card-text">Ruang serbaguna yang dapat digunakan untuk berbagai kegiatan seperti kajian, pernikahan, dan acara lainnya.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="<?= BASE_URL ?>/assets/images/fasilitas-5.jpg" class="card-img-top" alt="Taman" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">Taman</h5>
                        <p class="card-text">Taman yang asri dan sejuk, cocok untuk tempat istirahat dan bersosialisasi antar jamaah.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="<?= BASE_URL ?>/assets/images/fasilitas-6.jpg" class="card-img-top" alt="Area Parkir" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">Area Parkir</h5>
                        <p class="card-text">Area parkir yang luas dan aman, dapat menampung banyak kendaraan jamaah.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pengurus Section -->
<section class="pengurus py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Pengurus Masjid</h2>
        <?php if (count($pengurus) > 0): ?>
            <div class="row">
                <?php foreach ($pengurus as $p): ?>
                    <div class="col-md-3 col-6 mb-4">
                        <div class="card text-center h-100 shadow">
                            <?php if (!empty($p['foto'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/pengurus/<?= $p['foto'] ?>" class="card-img-top mx-auto mt-3" alt="<?= $p['nama'] ?>" style="height: 150px; width: 150px; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/assets/images/default-user.png" class="card-img-top mx-auto mt-3" alt="Default" style="height: 150px; width: 150px; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $p['nama'] ?></h5>
                                <p class="card-text"><span class="badge bg-primary"><?= $p['jabatan'] ?></span></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/pages/pengurus.php" class="btn btn-outline-primary">Lihat Semua Pengurus</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Data pengurus belum tersedia.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Kontak Section -->
<section class="kontak py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Kontak Kami</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Kontak</h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt me-2"></i> Jl. Contoh No. 123, Kota Contoh<br>
                            <i class="fas fa-phone-alt me-2"></i> (021) 1234-5678<br>
                            <i class="fas fa-envelope me-2"></i> info@nurinsankamil.com<br>
                            <i class="fas fa-clock me-2"></i> Buka 24 Jam
                        </p>
                        <div class="mt-4">
                            <h5>Sosial Media</h5>
                            <div class="social-links">
                                <a href="#" class="me-2"><i class="fab fa-facebook-f fa-2x"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-instagram fa-2x"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-youtube fa-2x"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-whatsapp fa-2x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Lokasi</h5>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0537222754153!2d106.82768731476932!3d-6.2568351954733!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1653141247727!5m2!1sid!2sid" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
