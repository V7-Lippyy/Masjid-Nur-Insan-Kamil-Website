<?php
/**
 * Halaman daftar pengqurban untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil tahun qurban (default tahun ini)
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Validasi tahun
if ($tahun < 2020 || $tahun > date('Y')) {
    $tahun = date('Y');
}

// Ambil data pengqurban berdasarkan tahun
$dataPengqurban = fetchAll("SELECT * FROM qurban WHERE tahun = '$tahun' AND status = 'approved' ORDER BY nama_lengkap ASC");

// Ambil tahun-tahun yang tersedia untuk filter
$tahunList = fetchAll("SELECT DISTINCT tahun FROM qurban WHERE status = 'approved' ORDER BY tahun DESC");
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Daftar Pengqurban <?= $tahun ?></h1>
                <p class="lead">Daftar peserta qurban di Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/qurban-list-hero.jpg" alt="Daftar Pengqurban" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Pilih Tahun Qurban</h4>
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <select name="tahun" class="form-select">
                                    <?php if (count($tahunList) > 0): ?>
                                        <?php foreach ($tahunList as $t): ?>
                                            <option value="<?= $t['tahun'] ?>" <?= $t['tahun'] == $tahun ? 'selected' : '' ?>>
                                                <?= $t['tahun'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="<?= date('Y') ?>" selected><?= date('Y') ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Lihat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Pengqurban Section -->
<section class="pengqurban-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Daftar Pengqurban Tahun <?= $tahun ?></h2>
        
        <?php if (count($dataPengqurban) > 0): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pengqurban</th>
                                            <th>Jenis Hewan</th>
                                            <th>Jumlah</th>
                                            <th>Atas Nama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataPengqurban as $index => $qurban): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= $qurban['nama_lengkap'] ?></td>
                                                <td><?= $qurban['jenis_hewan'] ?></td>
                                                <td><?= $qurban['jumlah_hewan'] ?></td>
                                                <td><?= $qurban['atas_nama'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i> Informasi Penting:</h5>
                        <ul>
                            <li>Daftar ini menampilkan peserta qurban yang telah dikonfirmasi oleh pengurus masjid.</li>
                            <li>Jika Anda telah mendaftar tetapi nama belum muncul, silakan hubungi pengurus masjid.</li>
                            <li>Penyembelihan hewan qurban akan dilaksanakan pada Hari Raya Idul Adha dan hari-hari Tasyriq.</li>
                            <li>Distribusi daging qurban akan dilakukan sesuai dengan ketentuan syariat.</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Daftar Qurban -->
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <a href="<?= BASE_URL ?>/pages/qurban_daftar.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Daftar Qurban
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <h5><i class="fas fa-info-circle me-2"></i> Belum ada data pengqurban untuk tahun <?= $tahun ?>.</h5>
                <p>Jadilah yang pertama mendaftar untuk qurban tahun ini!</p>
                <a href="<?= BASE_URL ?>/pages/qurban_daftar.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-2"></i> Daftar Qurban
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
