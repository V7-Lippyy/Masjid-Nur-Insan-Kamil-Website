<?php
/**
 * Halaman qurban untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Filter tahun
$tahunFilter = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Ambil semua tahun yang ada
$tahunList = fetchAll("SELECT DISTINCT tahun FROM qurban ORDER BY tahun DESC");

// Ambil tahun terbaru jika tidak ada filter
if (empty($tahunFilter) && count($tahunList) > 0) {
    $tahunFilter = $tahunList[0]['tahun'];
}

// Ambil semua data qurban
$whereClause = $tahunFilter ? " WHERE tahun = '$tahunFilter'" : "";
$dataQurban = fetchAll("SELECT * FROM qurban$whereClause ORDER BY tanggal_daftar DESC");

// Hitung total qurban per jenis
$totalSapi = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'sapi'" . ($tahunFilter ? " AND tahun = '$tahunFilter'" : ""))['total'] ?? 0;
$totalKambing = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'kambing'" . ($tahunFilter ? " AND tahun = '$tahunFilter'" : ""))['total'] ?? 0;
$totalDomba = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'domba'" . ($tahunFilter ? " AND tahun = '$tahunFilter'" : ""))['total'] ?? 0;
$totalLainnya = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'lainnya'" . ($tahunFilter ? " AND tahun = '$tahunFilter'" : ""))['total'] ?? 0;
$totalHewan = $totalSapi + $totalKambing + $totalDomba + $totalLainnya;

// Hitung total peserta qurban
$totalPeserta = fetchOne("SELECT COUNT(*) as total FROM qurban$whereClause")['total'] ?? 0;
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Program Qurban</h1>
                <p class="lead">Informasi program qurban Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/qurban-hero.jpg" alt="Program Qurban" class="img-fluid rounded shadow">
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
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="tahunDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Tahun: <?= $tahunFilter ? $tahunFilter . ' H' : 'Semua' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="tahunDropdown">
                        <?php foreach ($tahunList as $tahun): ?>
                            <li><a class="dropdown-item" href="?tahun=<?= $tahun['tahun'] ?>"><?= $tahun['tahun'] ?> H</a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ringkasan Qurban Section -->
<section class="ringkasan-qurban py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Peserta</h5>
                        <h3 class="card-text"><?= $totalPeserta ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Hewan</h5>
                        <h3 class="card-text"><?= $totalHewan ?> Ekor</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Sapi</h5>
                        <h3 class="card-text"><?= $totalSapi ?> Ekor</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Kambing & Domba</h5>
                        <h3 class="card-text"><?= $totalKambing + $totalDomba ?> Ekor</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Informasi Qurban Section -->
<section class="informasi-qurban py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Informasi Program Qurban <?= $tahunFilter ? "Tahun $tahunFilter H" : "" ?></h2>
        
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Ketentuan Qurban</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Hewan qurban harus sehat dan tidak cacat</li>
                            <li class="list-group-item">Usia minimal sapi 2 tahun</li>
                            <li class="list-group-item">Usia minimal kambing/domba 1 tahun</li>
                            <li class="list-group-item">Penyembelihan dilakukan pada hari raya Idul Adha dan 3 hari tasyriq</li>
                            <li class="list-group-item">Pendaftaran paling lambat H-3 sebelum Idul Adha</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Cara Pendaftaran</h5>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-flush list-group-numbered">
                            <li class="list-group-item">Datang langsung ke sekretariat Masjid Nur Insan Kamil</li>
                            <li class="list-group-item">Mengisi formulir pendaftaran</li>
                            <li class="list-group-item">Melakukan pembayaran sesuai jenis hewan qurban</li>
                            <li class="list-group-item">Menerima bukti pendaftaran</li>
                            <li class="list-group-item">Konfirmasi pembayaran melalui WhatsApp ke nomor yang tertera</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Harga Hewan Qurban</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Hewan</th>
                                        <th>Harga</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Sapi</td>
                                        <td>Rp 15.000.000 - Rp 25.000.000</td>
                                        <td>1 ekor untuk 7 orang</td>
                                    </tr>
                                    <tr>
                                        <td>Kambing</td>
                                        <td>Rp 2.500.000 - Rp 3.500.000</td>
                                        <td>1 ekor untuk 1 orang</td>
                                    </tr>
                                    <tr>
                                        <td>Domba</td>
                                        <td>Rp 2.800.000 - Rp 4.000.000</td>
                                        <td>1 ekor untuk 1 orang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Harga dapat berubah sesuai dengan kondisi pasar. Silakan hubungi panitia untuk informasi terbaru.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">Kontak Panitia Qurban</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user me-2"></i> Ketua Panitia</h6>
                                <p>Bapak H. Ahmad Fauzi<br>Telp/WA: 081234567890</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-user me-2"></i> Sekretaris</h6>
                                <p>Bapak Muhammad Rizki<br>Telp/WA: 085678901234</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user me-2"></i> Bendahara</h6>
                                <p>Bapak H. Sulaiman<br>Telp/WA: 089876543210</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-mosque me-2"></i> Sekretariat</h6>
                                <p>Masjid Nur Insan Kamil<br>Jl. Contoh No. 123, Kota<br>Telp: (021) 1234567</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Peserta Qurban Section -->
<?php if (count($dataQurban) > 0): ?>
<section class="daftar-peserta py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Daftar Peserta Qurban <?= $tahunFilter ? "Tahun $tahunFilter H" : "" ?></h2>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Atas Nama</th>
                        <th>Jenis Hewan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataQurban as $index => $qurban): ?>
                        <?php if ($qurban['status'] != 'pending'): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $qurban['nama_lengkap'] ?></td>
                                <td><?= $qurban['atas_nama'] ? $qurban['atas_nama'] : $qurban['nama_lengkap'] ?></td>
                                <td>
                                    <?php if ($qurban['jenis_hewan'] == 'sapi'): ?>
                                        <span class="badge bg-success">Sapi</span>
                                    <?php elseif ($qurban['jenis_hewan'] == 'kambing'): ?>
                                        <span class="badge bg-info">Kambing</span>
                                    <?php elseif ($qurban['jenis_hewan'] == 'domba'): ?>
                                        <span class="badge bg-primary">Domba</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Lainnya</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $qurban['jumlah_hewan'] ?> ekor</td>
                                <td>
                                    <?php if ($qurban['status'] == 'approved'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php elseif ($qurban['status'] == 'selesai'): ?>
                                        <span class="badge bg-primary">Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-4">Daftar Qurban Sekarang</h2>
        <p class="lead mb-4">Segera daftarkan diri Anda untuk program qurban tahun ini. Dapatkan pahala dan berkah dari Allah SWT.</p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-light btn-lg">Hubungi Kami</a>
    </div>
</section>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
