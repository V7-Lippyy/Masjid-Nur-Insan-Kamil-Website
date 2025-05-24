<?php
/**
 * Halaman keuangan untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Filter jenis dan bulan/tahun
$jenisFilter = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Validasi bulan dan tahun
if ($bulan < 1 || $bulan > 12) {
    $bulan = date('n');
}
if ($tahun < 2020 || $tahun > 2030) {
    $tahun = date('Y');
}

// Ambil data keuangan
$whereClause = "WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun";
if ($jenisFilter) {
    $whereClause .= " AND jenis = '$jenisFilter'";
}
$dataKeuangan = fetchAll("SELECT * FROM keuangan $whereClause ORDER BY tanggal DESC");

// Hitung total pemasukan dan pengeluaran
$totalPemasukan = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pemasukan' AND MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun")['total'] ?? 0;
$totalPengeluaran = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pengeluaran' AND MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun")['total'] ?? 0;
$saldoAkhir = $totalPemasukan - $totalPengeluaran;

// Ambil data untuk grafik
$dataPemasukan = [];
$dataPengeluaran = [];
for ($i = 1; $i <= 31; $i++) {
    $tanggal = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
    if (checkdate($bulan, $i, $tahun)) {
        $pemasukan = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pemasukan' AND tanggal = '$tanggal'")['total'] ?? 0;
        $pengeluaran = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pengeluaran' AND tanggal = '$tanggal'")['total'] ?? 0;
        $dataPemasukan[] = $pemasukan;
        $dataPengeluaran[] = $pengeluaran;
    }
}
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Laporan Keuangan</h1>
                <p class="lead">Transparansi keuangan Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/keuangan-hero.jpg" alt="Keuangan Masjid" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Filter Laporan Keuangan</h4>
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select name="jenis" class="form-select">
                                    <option value="" <?= $jenisFilter == '' ? 'selected' : '' ?>>Semua Jenis</option>
                                    <option value="pemasukan" <?= $jenisFilter == 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                                    <option value="pengeluaran" <?= $jenisFilter == 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
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

<!-- Ringkasan Keuangan Section -->
<section class="ringkasan-keuangan py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">
            Ringkasan Keuangan Bulan <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?>
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
        
        <!-- Grafik Keuangan -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title text-center">Grafik Keuangan Bulan <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?></h5>
                        <canvas id="keuanganChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Keuangan -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Detail Transaksi Keuangan</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($dataKeuangan) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                            <th>Kategori</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataKeuangan as $index => $keuangan): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= formatDate($keuangan['tanggal']) ?></td>
                                                <td><?= $keuangan['keterangan'] ?></td>
                                                <td><?= $keuangan['kategori'] ?></td>
                                                <td>
                                                    <?php if ($keuangan['jenis'] == 'pemasukan'): ?>
                                                        <span class="badge bg-success">Pemasukan</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Pengeluaran</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end"><?= formatRupiah($keuangan['jumlah']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                Belum ada data keuangan untuk periode yang dipilih.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript untuk Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk chart
    const dataPemasukan = <?= json_encode($dataPemasukan) ?>;
    const dataPengeluaran = <?= json_encode($dataPengeluaran) ?>;
    const labels = [];
    
    // Buat label tanggal
    for (let i = 1; i <= <?= count($dataPemasukan) ?>; i++) {
        labels.push(i);
    }
    
    // Buat chart
    const ctx = document.getElementById('keuanganChart').getContext('2d');
    const keuanganChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: dataPemasukan,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1
                },
                {
                    label: 'Pengeluaran',
                    data: dataPengeluaran,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
