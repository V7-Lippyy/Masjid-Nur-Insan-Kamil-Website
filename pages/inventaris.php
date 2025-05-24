<?php
/**
 * Halaman inventaris untuk pengguna umum
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
$kategoriList = fetchAll("SELECT DISTINCT kategori FROM inventaris WHERE kategori != '' ORDER BY kategori ASC");

// Ambil semua data inventaris
$whereClause = [];
if ($kategoriFilter) {
    $whereClause[] = "kategori = '$kategoriFilter'";
}
if ($statusFilter) {
    $whereClause[] = "status = '$statusFilter'";
}

$whereString = count($whereClause) > 0 ? "WHERE " . implode(" AND ", $whereClause) : "";
$dataInventaris = fetchAll("SELECT * FROM inventaris $whereString ORDER BY nama_barang ASC");

// Hitung jumlah inventaris per status
$totalBaik = fetchOne("SELECT COUNT(*) as total FROM inventaris WHERE status = 'baik'")['total'] ?? 0;
$totalRusak = fetchOne("SELECT COUNT(*) as total FROM inventaris WHERE status = 'rusak'")['total'] ?? 0;
$totalHilang = fetchOne("SELECT COUNT(*) as total FROM inventaris WHERE status = 'hilang'")['total'] ?? 0;
$totalInventaris = $totalBaik + $totalRusak + $totalHilang;
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Inventaris Masjid</h1>
                <p class="lead">Daftar aset dan inventaris Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/inventaris-hero.jpg" alt="Inventaris Masjid" class="img-fluid rounded shadow">
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
                        Status: <?= $statusFilter ? ucfirst($statusFilter) : 'Semua' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                        <li><a class="dropdown-item" href="?<?= $kategoriFilter ? "kategori=$kategoriFilter" : "" ?>">Semua Status</a></li>
                        <li><a class="dropdown-item" href="?status=baik<?= $kategoriFilter ? "&kategori=$kategoriFilter" : "" ?>">Baik</a></li>
                        <li><a class="dropdown-item" href="?status=rusak<?= $kategoriFilter ? "&kategori=$kategoriFilter" : "" ?>">Rusak</a></li>
                        <li><a class="dropdown-item" href="?status=hilang<?= $kategoriFilter ? "&kategori=$kategoriFilter" : "" ?>">Hilang</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ringkasan Inventaris Section -->
<section class="ringkasan-inventaris py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Inventaris</h5>
                        <h3 class="card-text"><?= $totalInventaris ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Kondisi Baik</h5>
                        <h3 class="card-text"><?= $totalBaik ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Kondisi Rusak</h5>
                        <h3 class="card-text"><?= $totalRusak ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Hilang</h5>
                        <h3 class="card-text"><?= $totalHilang ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Inventaris Section -->
<section class="inventaris-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Daftar Inventaris
            <?php if ($kategoriFilter): ?>
                - <?= $kategoriFilter ?>
            <?php endif; ?>
            <?php if ($statusFilter): ?>
                (<?= ucfirst($statusFilter) ?>)
            <?php endif; ?>
        </h2>
        
        <?php if (count($dataInventaris) > 0): ?>
            <div class="row">
                <?php foreach ($dataInventaris as $inventaris): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow">
                            <?php if (!empty($inventaris['foto'])): ?>
                                <img src="<?= BASE_URL ?>/assets/uploads/inventaris/<?= $inventaris['foto'] ?>" class="card-img-top" alt="<?= $inventaris['nama_barang'] ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/assets/images/default-inventory.png" class="card-img-top" alt="Default" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $inventaris['nama_barang'] ?></h5>
                                <p class="card-text">
                                    <span class="badge bg-primary"><?= $inventaris['kategori'] ?></span>
                                    <?php if ($inventaris['status'] == 'baik'): ?>
                                        <span class="badge bg-success">Baik</span>
                                    <?php elseif ($inventaris['status'] == 'rusak'): ?>
                                        <span class="badge bg-warning text-dark">Rusak</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Hilang</span>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text">
                                    <strong>Jumlah:</strong> <?= $inventaris['jumlah'] ?> <?= $inventaris['satuan'] ?><br>
                                    <strong>Tanggal Perolehan:</strong> <?= formatDate($inventaris['tanggal_perolehan']) ?><br>
                                    <strong>Sumber Dana:</strong> <?= $inventaris['sumber_dana'] ?><br>
                                    <?php if (!empty($inventaris['nilai_perolehan'])): ?>
                                        <strong>Nilai Perolehan:</strong> <?= formatRupiah($inventaris['nilai_perolehan']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($inventaris['lokasi'])): ?>
                                        <strong>Lokasi:</strong> <?= $inventaris['lokasi'] ?><br>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($inventaris['keterangan'])): ?>
                                    <p class="card-text">
                                        <strong>Keterangan:</strong><br>
                                        <?= $inventaris['keterangan'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada data inventaris<?= $kategoriFilter ? " untuk kategori $kategoriFilter" : "" ?><?= $statusFilter ? " dengan status $statusFilter" : "" ?>.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
