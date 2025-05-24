<?php
/**
 * Halaman kas acara untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Filter acara dan bulan/tahun
$acaraFilter = isset($_GET['acara_id']) ? $_GET['acara_id'] : '';
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Validasi bulan dan tahun
if ($bulan < 1 || $bulan > 12) {
    $bulan = date('n');
}
if ($tahun < 2020 || $tahun > 2030) {
    $tahun = date('Y');
}

// Ambil data acara untuk filter
$dataAcara = fetchAll("SELECT * FROM kegiatan ORDER BY nama_kegiatan ASC");

// Ambil data kas acara
$whereClause = "WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun";
if ($acaraFilter) {
    $whereClause .= " AND acara_id = $acaraFilter";
}
$dataKasAcara = fetchAll("SELECT ka.*, k.nama_kegiatan FROM kas_acara ka JOIN kegiatan k ON ka.acara_id = k.id $whereClause ORDER BY ka.tanggal DESC");

// Hitung total pemasukan dan pengeluaran
$whereSum = "MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun";
if ($acaraFilter) {
    $whereSum .= " AND acara_id = $acaraFilter";
}
$totalPemasukan = fetchOne("SELECT SUM(jumlah) as total FROM kas_acara WHERE jenis = 'pemasukan' AND $whereSum")['total'] ?? 0;
$totalPengeluaran = fetchOne("SELECT SUM(jumlah) as total FROM kas_acara WHERE jenis = 'pengeluaran' AND $whereSum")['total'] ?? 0;
$saldoAkhir = $totalPemasukan - $totalPengeluaran;

// Jika ada filter acara, ambil detail acara
$detailAcara = null;
if ($acaraFilter) {
    $detailAcara = fetchOne("SELECT * FROM kegiatan WHERE id = $acaraFilter");
}
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Laporan Kas Acara</h1>
                <p class="lead">Transparansi keuangan acara Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/kas-acara-hero.jpg" alt="Kas Acara Masjid" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Filter Laporan Kas Acara</h4>
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select name="acara_id" class="form-select">
                                    <option value="">Semua Acara</option>
                                    <?php foreach ($dataAcara as $acara): ?>
                                        <option value="<?= $acara['id'] ?>" <?= $acaraFilter == $acara['id'] ? 'selected' : '' ?>>
                                            <?= $acara['nama_kegiatan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="bulan" class="form-select">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="tahun" class="form-select">
                                    <?php for ($i = date('Y') - 2; $i <= date('Y'); $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detail Acara Section (jika ada filter acara) -->
<?php if ($detailAcara): ?>
<section class="detail-acara py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Detail Acara</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4><?= $detailAcara['nama_kegiatan'] ?></h4>
                                <p>
                                    <i class="fas fa-calendar-alt me-2"></i> <?= formatDate($detailAcara['tanggal_mulai']) ?>
                                    <?php if (!empty($detailAcara['tanggal_selesai'])): ?>
                                        - <?= formatDate($detailAcara['tanggal_selesai']) ?>
                                    <?php endif; ?>
                                </p>
                                <p><i class="fas fa-map-marker-alt me-2"></i> <?= $detailAcara['lokasi'] ?></p>
                                <p><i class="fas fa-tag me-2"></i> <?= $detailAcara['kategori'] ?></p>
                                <p><i class="fas fa-info-circle me-2"></i> Status: 
                                    <?php if ($detailAcara['status'] == 'upcoming'): ?>
                                        <span class="badge bg-info">Akan Datang</span>
                                    <?php elseif ($detailAcara['status'] == 'ongoing'): ?>
                                        <span class="badge bg-success">Sedang Berlangsung</span>
                                    <?php elseif ($detailAcara['status'] == 'completed'): ?>
                                        <span class="badge bg-secondary">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($detailAcara['poster'])): ?>
                                    <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $detailAcara['poster'] ?>" alt="<?= $detailAcara['nama_kegiatan'] ?>" class="img-fluid rounded">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Ringkasan Kas Acara Section -->
<section class="ringkasan-kas py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Ringkasan Kas Acara
            <?php if ($detailAcara): ?>
                : <?= $detailAcara['nama_kegiatan'] ?>
            <?php endif; ?>
            (<?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?>)
        </h2>
        
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Pemasukan</h5>
                        <h3 class="card-text"><?= formatRupiah($totalPemasukan) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <h3 class="card-text"><?= formatRupiah($totalPengeluaran) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Saldo Akhir</h5>
                        <h3 class="card-text"><?= formatRupiah($saldoAkhir) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Kas Acara -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Detail Transaksi Kas Acara</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($dataKasAcara) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Acara</th>
                                            <th>Keterangan</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataKasAcara as $index => $kas): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= formatDate($kas['tanggal']) ?></td>
                                                <td><?= $kas['nama_kegiatan'] ?></td>
                                                <td><?= $kas['keterangan'] ?></td>
                                                <td>
                                                    <?php if ($kas['jenis'] == 'pemasukan'): ?>
                                                        <span class="badge bg-success">Pemasukan</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Pengeluaran</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end"><?= formatRupiah($kas['jumlah']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                Belum ada data kas acara untuk periode yang dipilih.
                            </div>
                        <?php endif; ?>
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
