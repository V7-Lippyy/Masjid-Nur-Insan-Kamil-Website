<?php
/**
 * Halaman dashboard admin
 */

// Memuat file header
require_once 'includes/header.php';

// Mengambil data untuk dashboard
$totalPengurus = fetchOne("SELECT COUNT(*) as total FROM pengurus WHERE status = 'aktif'")['total'];
$totalInventaris = fetchOne("SELECT COUNT(*) as total FROM inventaris")['total'];
$totalKegiatan = fetchOne("SELECT COUNT(*) as total FROM kegiatan WHERE status != 'canceled'")['total'];
$totalPengumuman = fetchOne("SELECT COUNT(*) as total FROM pengumuman WHERE status = 'aktif'")['total'];

// Mengambil data keuangan
$totalPemasukan = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pemasukan'")['total'] ?? 0;
$totalPengeluaran = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pengeluaran'")['total'] ?? 0;
$saldoKas = $totalPemasukan - $totalPengeluaran;

// Mengambil data zakat dan qurban
$totalZakat = fetchOne("SELECT COUNT(*) as total FROM zakat WHERE status != 'pending'")['total'];
$totalQurban = fetchOne("SELECT COUNT(*) as total FROM qurban WHERE status != 'pending'")['total'];

// Mengambil data masukan terbaru
$masukanTerbaru = fetchAll("SELECT * FROM masukan ORDER BY created_at DESC LIMIT 5");

// Mengambil data kegiatan mendatang
$kegiatanMendatang = fetchAll("SELECT * FROM kegiatan WHERE tanggal_mulai >= NOW() AND status = 'upcoming' ORDER BY tanggal_mulai ASC LIMIT 5");
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Dashboard</h1>
    
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Kas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= formatRupiah($saldoKas) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pengurus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPengurus ?> Orang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Kegiatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalKegiatan ?> Kegiatan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Inventaris</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalInventaris ?> Item</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kegiatan Mendatang</h6>
                    <a href="<?= ADMIN_URL ?>/kegiatan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (count($kegiatanMendatang) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kegiatanMendatang as $kegiatan): ?>
                                        <tr>
                                            <td><?= $kegiatan['nama_kegiatan'] ?></td>
                                            <td><?= formatDate($kegiatan['tanggal_mulai'], 'd M Y H:i') ?></td>
                                            <td><?= $kegiatan['lokasi'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Tidak ada kegiatan mendatang</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Masukan Terbaru</h6>
                    <a href="<?= ADMIN_URL ?>/masukan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (count($masukanTerbaru) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jenis</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($masukanTerbaru as $masukan): ?>
                                        <tr>
                                            <td><?= $masukan['nama'] ?></td>
                                            <td><?= ucfirst($masukan['jenis']) ?></td>
                                            <td><?= formatDate($masukan['created_at'], 'd M Y') ?></td>
                                            <td>
                                                <?php if ($masukan['status'] == 'baru'): ?>
                                                    <span class="badge bg-danger">Baru</span>
                                                <?php elseif ($masukan['status'] == 'dibaca'): ?>
                                                    <span class="badge bg-warning">Dibaca</span>
                                                <?php elseif ($masukan['status'] == 'diproses'): ?>
                                                    <span class="badge bg-info">Diproses</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Tidak ada masukan terbaru</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Keuangan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="keuanganChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Zakat & Qurban</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="zakatQurbanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Keuangan Chart
    var keuanganCtx = document.getElementById('keuanganChart').getContext('2d');
    var keuanganChart = new Chart(keuanganCtx, {
        type: 'bar',
        data: {
            labels: ['Pemasukan', 'Pengeluaran', 'Saldo'],
            datasets: [{
                label: 'Jumlah (Rp)',
                data: [<?= $totalPemasukan ?>, <?= $totalPengeluaran ?>, <?= $saldoKas ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Zakat & Qurban Chart
    var zakatQurbanCtx = document.getElementById('zakatQurbanChart').getContext('2d');
    var zakatQurbanChart = new Chart(zakatQurbanCtx, {
        type: 'pie',
        data: {
            labels: ['Zakat', 'Qurban'],
            datasets: [{
                label: 'Jumlah',
                data: [<?= $totalZakat ?>, <?= $totalQurban ?>],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
</script>

<?php
// Memuat file footer
require_once 'includes/footer.php';
?>
