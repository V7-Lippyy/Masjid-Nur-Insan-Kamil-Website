<?php
/**
 * Halaman jadwal shalat untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Ambil bulan dan tahun dari parameter URL
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Validasi bulan dan tahun
if ($bulan < 1 || $bulan > 12) {
    $bulan = date('n');
}
if ($tahun < 2020 || $tahun > 2030) {
    $tahun = date('Y');
}

// Ambil data jadwal shalat untuk bulan dan tahun yang dipilih
$dataJadwal = fetchAll("SELECT * FROM jadwal_shalat WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun ORDER BY tanggal ASC");

// Ambil jadwal shalat hari ini
$today = date('Y-m-d');
$jadwalHariIni = fetchOne("SELECT * FROM jadwal_shalat WHERE tanggal = '$today'");
if (!$jadwalHariIni) {
    // Jika tidak ada jadwal untuk hari ini, ambil jadwal terbaru
    $jadwalHariIni = fetchOne("SELECT * FROM jadwal_shalat ORDER BY tanggal DESC LIMIT 1");
}
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Jadwal Shalat</h1>
                <p class="lead">Jadwal waktu shalat di Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/jadwal-shalat-hero.jpg" alt="Jadwal Shalat" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Jadwal Hari Ini Section -->
<section class="jadwal-hari-ini py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Jadwal Shalat Hari Ini</h2>
        
        <?php if ($jadwalHariIni): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white text-center">
                            <h3 class="mb-0"><?= formatDate($jadwalHariIni['tanggal'], 'l, d F Y') ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Subuh</h5>
                                            <p class="card-text waktu display-6"><?= formatTime($jadwalHariIni['subuh']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Dzuhur</h5>
                                            <p class="card-text waktu display-6"><?= formatTime($jadwalHariIni['dzuhur']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Ashar</h5>
                                            <p class="card-text waktu display-6"><?= formatTime($jadwalHariIni['ashar']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Maghrib</h5>
                                            <p class="card-text waktu display-6"><?= formatTime($jadwalHariIni['maghrib']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Isya</h5>
                                            <p class="card-text waktu display-6"><?= formatTime($jadwalHariIni['isya']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Jadwal shalat hari ini belum tersedia.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Pilih Bulan dan Tahun</h4>
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-5">
                                <select name="bulan" class="form-select">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select name="tahun" class="form-select">
                                    <?php for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Lihat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jadwal Bulanan Section -->
<section class="jadwal-bulanan py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Jadwal Shalat Bulan <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?>
        </h2>
        
        <?php if (count($dataJadwal) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Tanggal</th>
                            <th>Subuh</th>
                            <th>Dzuhur</th>
                            <th>Ashar</th>
                            <th>Maghrib</th>
                            <th>Isya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataJadwal as $jadwal): ?>
                            <tr <?= $jadwal['tanggal'] == $today ? 'class="table-success"' : '' ?>>
                                <td><?= formatDate($jadwal['tanggal'], 'd F Y (l)') ?></td>
                                <td><?= formatTime($jadwal['subuh']) ?></td>
                                <td><?= formatTime($jadwal['dzuhur']) ?></td>
                                <td><?= formatTime($jadwal['ashar']) ?></td>
                                <td><?= formatTime($jadwal['maghrib']) ?></td>
                                <td><?= formatTime($jadwal['isya']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Jadwal shalat untuk bulan <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?> belum tersedia.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
