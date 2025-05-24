<?php
/**
 * Halaman beranda untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Memuat header
require_once 'includes/header.php';

// Ambil data pengumuman terbaru
$pengumuman = fetchAll("SELECT * FROM pengumuman WHERE status = 'aktif' AND (tanggal_selesai IS NULL OR tanggal_selesai >= CURDATE()) ORDER BY tanggal_mulai DESC LIMIT 5");

// Ambil data kegiatan terbaru
$kegiatan = fetchAll("SELECT * FROM kegiatan WHERE status IN ('upcoming', 'ongoing') ORDER BY tanggal_mulai ASC LIMIT 3");

// Ambil data jadwal shalat hari ini
$today = date('Y-m-d');
$jadwalShalat = fetchOne("SELECT * FROM jadwal_shalat WHERE tanggal = '$today'");
if (!$jadwalShalat) {
    // Jika tidak ada jadwal untuk hari ini, ambil jadwal terbaru
    $jadwalShalat = fetchOne("SELECT * FROM jadwal_shalat ORDER BY tanggal DESC LIMIT 1");
}

// Ambil data jadwal imam dan khatib jumat terbaru
$jadwalJumat = fetchOne("SELECT * FROM jadwal_imam_khatib WHERE waktu_shalat = 'jumat' AND tanggal >= CURDATE() ORDER BY tanggal ASC LIMIT 1");

// Ambil data galeri terbaru
$galeri = fetchAll("SELECT * FROM galeri ORDER BY tanggal DESC LIMIT 6");

// Ambil data pengurus (ketua, sekretaris, bendahara)
$pengurus = fetchAll("SELECT * FROM pengurus WHERE jabatan IN ('Ketua', 'Sekretaris', 'Bendahara') AND status = 'aktif' ORDER BY FIELD(jabatan, 'Ketua', 'Sekretaris', 'Bendahara')");
?>

<!-- Hero Section -->
<section class="hero-section">
    <div id="carouselHero" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= BASE_URL ?>/assets/images/masjid-hero-1.jpg" class="d-block w-100" alt="Masjid Nur Insan Kamil">
                <div class="carousel-caption d-none d-md-block">
                    <h2>Selamat Datang di Masjid Nur Insan Kamil</h2>
                    <p>Pusat Kegiatan Islam dan Pembinaan Umat</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= BASE_URL ?>/assets/images/masjid-hero-2.jpg" class="d-block w-100" alt="Kegiatan Masjid">
                <div class="carousel-caption d-none d-md-block">
                    <h2>Jadwal Kegiatan Rutin</h2>
                    <p>Kajian, Tahsin, dan Berbagai Kegiatan Islami</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= BASE_URL ?>/assets/images/masjid-hero-3.jpg" class="d-block w-100" alt="Program Sosial">
                <div class="carousel-caption d-none d-md-block">
                    <h2>Program Sosial</h2>
                    <p>Zakat, Infaq, Sedekah, dan Wakaf untuk Kemaslahatan Umat</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselHero" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselHero" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- Jadwal Shalat Section -->
<section class="jadwal-shalat py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="section-title mb-4">Jadwal Shalat Hari Ini</h2>
                <?php if ($jadwalShalat): ?>
                    <div class="jadwal-hari-ini">
                        <h4><?= formatDate($jadwalShalat['tanggal'], 'l, d F Y') ?></h4>
                        <div class="row mt-4">
                            <div class="col-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Subuh</h5>
                                        <p class="card-text waktu"><?= formatTime($jadwalShalat['subuh']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Dzuhur</h5>
                                        <p class="card-text waktu"><?= formatTime($jadwalShalat['dzuhur']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Ashar</h5>
                                        <p class="card-text waktu"><?= formatTime($jadwalShalat['ashar']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Maghrib</h5>
                                        <p class="card-text waktu"><?= formatTime($jadwalShalat['maghrib']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Isya</h5>
                                        <p class="card-text waktu"><?= formatTime($jadwalShalat['isya']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?= BASE_URL ?>/pages/jadwal_shalat.php" class="btn btn-primary">Lihat Jadwal Lengkap</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Jadwal shalat belum tersedia.
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h2 class="section-title mb-4">Jadwal Shalat Jumat</h2>
                <?php if ($jadwalJumat): ?>
                    <div class="jadwal-jumat">
                        <h4><?= formatDate($jadwalJumat['tanggal'], 'l, d F Y') ?></h4>
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-user me-2"></i> Khatib</h5>
                                        <p class="fs-5"><?= $jadwalJumat['nama_khatib'] ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-user me-2"></i> Imam</h5>
                                        <p class="fs-5"><?= $jadwalJumat['nama_imam'] ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($jadwalJumat['tema_khutbah'])): ?>
                                    <div class="mt-3">
                                        <h5><i class="fas fa-book me-2"></i> Tema Khutbah</h5>
                                        <p class="fs-5"><?= $jadwalJumat['tema_khutbah'] ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?= BASE_URL ?>/pages/jadwal_imam_khatib.php" class="btn btn-primary">Lihat Jadwal Lengkap</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Jadwal shalat Jumat belum tersedia.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Pengumuman Section -->
<section class="pengumuman py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Pengumuman Terbaru</h2>
        <?php if (count($pengumuman) > 0): ?>
            <div class="row">
                <?php foreach ($pengumuman as $p): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($p['gambar'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/pengumuman/<?= $p['gambar'] ?>" class="card-img-top" alt="<?= $p['judul'] ?>" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $p['judul'] ?></h5>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> <?= formatDate($p['tanggal_mulai']) ?>
                                    <?php if (!empty($p['tanggal_selesai'])): ?>
                                        - <?= formatDate($p['tanggal_selesai']) ?>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text"><?= substr(strip_tags($p['isi']), 0, 100) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="<?= BASE_URL ?>/pages/pengumuman_detail.php?id=<?= $p['id'] ?>" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/pages/pengumuman.php" class="btn btn-outline-primary">Lihat Semua Pengumuman</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada pengumuman terbaru.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Kegiatan Section -->
<section class="kegiatan py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Kegiatan Mendatang</h2>
        <?php if (count($kegiatan) > 0): ?>
            <div class="row">
                <?php foreach ($kegiatan as $k): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($k['poster'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $k['poster'] ?>" class="card-img-top" alt="<?= $k['nama_kegiatan'] ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/assets/images/default-event.png" class="card-img-top" alt="Default" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $k['nama_kegiatan'] ?></h5>
                                <p class="card-text small">
                                    <span class="badge bg-primary"><?= $k['kategori'] ?></span>
                                    <span class="badge bg-<?= $k['status'] == 'upcoming' ? 'info' : 'success' ?>"><?= $k['status'] == 'upcoming' ? 'Akan Datang' : 'Sedang Berlangsung' ?></span>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-calendar-alt me-1"></i> <?= formatDate($k['tanggal_mulai'], 'd M Y H:i') ?>
                                    <?php if (!empty($k['tanggal_selesai'])): ?>
                                        <br><i class="fas fa-calendar-check me-1"></i> <?= formatDate($k['tanggal_selesai'], 'd M Y H:i') ?>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?= $k['lokasi'] ?>
                                </p>
                                <p class="card-text"><?= substr(strip_tags($k['deskripsi']), 0, 100) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="<?= BASE_URL ?>/pages/kegiatan_detail.php?id=<?= $k['id'] ?>" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/pages/kegiatan.php" class="btn btn-outline-primary">Lihat Semua Kegiatan</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada kegiatan mendatang.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Galeri Section -->
<section class="galeri py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Galeri Foto</h2>
        <?php if (count($galeri) > 0): ?>
            <div class="row">
                <?php foreach ($galeri as $g): ?>
                    <div class="col-md-4 col-6 mb-4">
                        <a href="<?= BASE_URL ?>/pages/galeri_detail.php?id=<?= $g['id'] ?>" class="galeri-item">
                            <img src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $g['gambar'] ?>" class="img-fluid rounded" alt="<?= $g['judul'] ?>" style="height: 200px; width: 100%; object-fit: cover;">
                            <div class="galeri-overlay">
                                <div class="galeri-title"><?= $g['judul'] ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/pages/galeri.php" class="btn btn-outline-primary">Lihat Semua Galeri</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada foto dalam galeri.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Pengurus Section -->
<section class="pengurus py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Pengurus Masjid</h2>
        <?php if (count($pengurus) > 0): ?>
            <div class="row justify-content-center">
                <?php foreach ($pengurus as $p): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center h-100">
                            <?php if (!empty($p['foto'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/pengurus/<?= $p['foto'] ?>" class="card-img-top mx-auto mt-3" alt="<?= $p['nama'] ?>" style="height: 200px; width: 200px; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/assets/images/default-user.png" class="card-img-top mx-auto mt-3" alt="Default" style="height: 200px; width: 200px; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $p['nama'] ?></h5>
                                <p class="card-text"><span class="badge bg-primary"><?= $p['jabatan'] ?></span></p>
                                <?php if (!empty($p['no_telepon'])): ?>
                                    <p class="card-text"><i class="fas fa-phone-alt me-1"></i> <?= $p['no_telepon'] ?></p>
                                <?php endif; ?>
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

<!-- Kontak & Masukan Section -->
<section class="kontak py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="section-title mb-4">Kontak Kami</h2>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Masjid Nur Insan Kamil</h5>
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
            <div class="col-md-6">
                <h2 class="section-title mb-4">Kirim Masukan</h2>
                <div class="card">
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>/pages/masukan_proses.php" method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="no_telepon" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon">
                            </div>
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis Masukan</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">Pilih Jenis Masukan</option>
                                    <option value="kritik">Kritik</option>
                                    <option value="saran">Saran</option>
                                    <option value="pertanyaan">Pertanyaan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="isi" class="form-label">Isi Masukan</label>
                                <textarea class="form-control" id="isi" name="isi" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Masukan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Memuat footer
require_once 'includes/footer.php';
?>
