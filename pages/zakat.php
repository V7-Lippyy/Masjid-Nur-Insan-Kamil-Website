<?php
/**
 * Halaman zakat untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Inisialisasi variabel untuk form (jika diperlukan)
$nama_muzakki = isset($_POST['nama_muzakki']) ? $_POST['nama_muzakki'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$jenis_zakat = isset($_POST['jenis_zakat']) ? $_POST['jenis_zakat'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$success = false;

// Proses form jika ada pengiriman data zakat dari frontend
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_zakat'])) {
    // Validasi input
    $errors = [];
    
    if (empty($nama_muzakki)) {
        $errors[] = 'Nama Muzakki harus diisi';
    }
    
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    
    if (empty($jenis_zakat)) {
        $errors[] = 'Jenis Zakat harus dipilih';
    }
    
    if (empty($jumlah) || !is_numeric($jumlah) || $jumlah <= 0) {
        $errors[] = 'Jumlah Zakat harus diisi dengan angka yang valid';
    }
    
    // Upload bukti pembayaran jika ada
    $bukti_pembayaran = '';
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/zakat');
        
        if (!$bukti_pembayaran) {
            $errors[] = 'Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB';
        }
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        // Escape string
        $nama_muzakki = escapeString($nama_muzakki);
        $email = escapeString($email);
        $no_telepon = escapeString($no_telepon);
        $jenis_zakat = escapeString($jenis_zakat);
        $jumlah = escapeString($jumlah);
        $tanggal = date('Y-m-d'); // Tanggal saat ini
        
        // Insert data
        $query = "INSERT INTO zakat (nama_muzakki, email, no_telepon, jenis_zakat, jumlah, tanggal, status, bukti_pembayaran) VALUES ('$nama_muzakki', '$email', '$no_telepon', '$jenis_zakat', '$jumlah', '$tanggal', 'pending', '$bukti_pembayaran')";
        
        if (execute($query)) {
            $success = true;
            // Reset form
            $nama_muzakki = $email = $no_telepon = $jenis_zakat = $jumlah = '';
        } else {
            $errors[] = 'Gagal menyimpan data zakat. Silakan coba lagi nanti.';
        }
    }
}

// Ambil data ringkasan zakat (contoh: total zakat diterima)
$totalZakatDiterima = fetchOne("SELECT SUM(jumlah) as total FROM zakat WHERE status = 'diterima' OR status = 'disalurkan'")['total'] ?? 0;
$totalMuzakki = fetchOne("SELECT COUNT(DISTINCT nama_muzakki) as total FROM zakat WHERE status = 'diterima' OR status = 'disalurkan'")['total'] ?? 0;

?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Program Zakat</h1>
                <p class="lead">Informasi dan layanan pembayaran zakat Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/zakat-hero.jpg" alt="Program Zakat" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Informasi Zakat Section -->
<section class="informasi-zakat py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Informasi Zakat</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-heart fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Zakat Fitrah</h5>
                        <p class="card-text">Zakat wajib yang dikeluarkan setahun sekali pada bulan Ramadhan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-coins fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Zakat Mal</h5>
                        <p class="card-text">Zakat atas harta yang dimiliki jika telah mencapai nishab dan haul.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-seedling fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Fidyah & Lainnya</h5>
                        <p class="card-text">Pembayaran fidyah bagi yang tidak mampu berpuasa dan zakat lainnya.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="fas fa-calculator me-2"></i> Kalkulator Zakat Mal</h5>
                        <p>Hitung kewajiban zakat mal Anda dengan mudah menggunakan kalkulator kami.</p>
                        <a href="#kalkulator-zakat" class="btn btn-primary">Hitung Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="fas fa-donate me-2"></i> Bayar Zakat Online</h5>
                        <p>Salurkan zakat Anda dengan mudah dan aman melalui formulir online kami.</p>
                        <a href="#bayar-zakat" class="btn btn-success">Bayar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Kalkulator Zakat Mal Section -->
<section id="kalkulator-zakat" class="kalkulator-zakat py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Kalkulator Zakat Mal</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted text-center mb-4">Kalkulator ini membantu menghitung Zakat Mal (harta) berdasarkan harga emas saat ini. Nishab zakat mal adalah setara dengan 85 gram emas murni.</p>
                        
                        <div class="mb-3">
                            <label for="harga_emas" class="form-label">Harga Emas per Gram (Rp)</label>
                            <input type="number" class="form-control" id="harga_emas" placeholder="Masukkan harga emas saat ini">
                        </div>
                        
                        <div class="mb-3">
                            <label for="harta_simpanan" class="form-label">Total Harta Simpanan (Tabungan, Deposito, dll) (Rp)</label>
                            <input type="number" class="form-control" id="harta_simpanan" placeholder="Masukkan total simpanan">
                        </div>
                        
                        <div class="mb-3">
                            <label for="harta_investasi" class="form-label">Nilai Investasi (Saham, Properti, dll) (Rp)</label>
                            <input type="number" class="form-control" id="harta_investasi" placeholder="Masukkan nilai investasi">
                        </div>
                        
                        <div class="mb-3">
                            <label for="hutang_jatuh_tempo" class="form-label">Hutang Jatuh Tempo (Rp)</label>
                            <input type="number" class="form-control" id="hutang_jatuh_tempo" placeholder="Masukkan jumlah hutang">
                        </div>
                        
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary" onclick="hitungZakatMal()">Hitung Zakat</button>
                        </div>
                        
                        <div id="hasilZakatMal" class="mt-4 alert alert-info" style="display: none;">
                            <h5 class="alert-heading">Hasil Perhitungan:</h5>
                            <p>Nishab (85 gram emas): <strong id="nishabValue">Rp 0</strong></p>
                            <p>Total Harta Kena Zakat: <strong id="hartaKenaZakat">Rp 0</strong></p>
                            <p>Jumlah Zakat (2.5%): <strong id="jumlahZakatMal">Rp 0</strong></p>
                            <p id="statusZakatMal"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bayar Zakat Online Section -->
<section id="bayar-zakat" class="bayar-zakat py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Bayar Zakat Online</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i> Pembayaran Zakat Berhasil!</h5>
                                <p>Terima kasih telah menyalurkan zakat Anda melalui Masjid Nur Insan Kamil. Semoga Allah SWT menerima amal ibadah Anda.</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-circle me-2"></i> Terjadi Kesalahan!</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="#bayar-zakat" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_muzakki" class="form-label">Nama Muzakki <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_muzakki" name="nama_muzakki" value="<?= $nama_muzakki ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?= $no_telepon ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="jenis_zakat" class="form-label">Jenis Zakat <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jenis_zakat" name="jenis_zakat" required>
                                        <option value="" <?= $jenis_zakat == '' ? 'selected' : '' ?>>Pilih Jenis Zakat</option>
                                        <option value="fitrah" <?= $jenis_zakat == 'fitrah' ? 'selected' : '' ?>>Zakat Fitrah</option>
                                        <option value="mal" <?= $jenis_zakat == 'mal' ? 'selected' : '' ?>>Zakat Mal</option>
                                        <option value="fidyah" <?= $jenis_zakat == 'fidyah' ? 'selected' : '' ?>>Fidyah</option>
                                        <option value="lainnya" <?= $jenis_zakat == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Zakat (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $jumlah ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bukti_pembayaran" class="form-label">Upload Bukti Pembayaran (Opsional)</label>
                                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
                                <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                            </div>
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Informasi Pembayaran</h6>
                                <p>Silakan transfer ke rekening berikut:</p>
                                <p><strong>Bank Syariah Indonesia (BSI)</strong><br>No. Rek: <strong>1234567890</strong><br>Atas Nama: <strong>Masjid Nur Insan Kamil</strong></p>
                                <p>Konfirmasi pembayaran dapat dilakukan melalui WhatsApp ke nomor <strong>0812-xxxx-xxxx</strong>.</p>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="submit_zakat" class="btn btn-success btn-lg">Kirim Pembayaran Zakat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik Zakat Section -->
<section class="statistik-zakat py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Statistik Penerimaan Zakat</h2>
        <div class="row text-center">
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="display-4 text-primary fw-bold"><?= formatRupiah($totalZakatDiterima) ?></h3>
                        <p class="lead text-muted">Total Zakat Diterima</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="display-4 text-success fw-bold"><?= $totalMuzakki ?></h3>
                        <p class="lead text-muted">Jumlah Muzakki</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function hitungZakatMal() {
    const hargaEmas = parseFloat(document.getElementById('harga_emas').value) || 0;
    const hartaSimpanan = parseFloat(document.getElementById('harta_simpanan').value) || 0;
    const hartaInvestasi = parseFloat(document.getElementById('harta_investasi').value) || 0;
    const hutangJatuhTempo = parseFloat(document.getElementById('hutang_jatuh_tempo').value) || 0;
    
    const nishab = 85 * hargaEmas;
    const totalHarta = hartaSimpanan + hartaInvestasi;
    const hartaKenaZakat = totalHarta - hutangJatuhTempo;
    const jumlahZakat = hartaKenaZakat >= nishab ? 0.025 * hartaKenaZakat : 0;
    
    document.getElementById('nishabValue').textContent = formatRupiah(nishab);
    document.getElementById('hartaKenaZakat').textContent = formatRupiah(hartaKenaZakat);
    document.getElementById('jumlahZakatMal').textContent = formatRupiah(jumlahZakat);
    
    if (hartaKenaZakat >= nishab) {
        document.getElementById('statusZakatMal').textContent = 'Anda wajib membayar zakat.';
        document.getElementById('statusZakatMal').className = 'text-success fw-bold';
    } else {
        document.getElementById('statusZakatMal').textContent = 'Anda belum wajib membayar zakat mal.';
        document.getElementById('statusZakatMal').className = 'text-danger fw-bold';
    }
    
    document.getElementById('hasilZakatMal').style.display = 'block';
}

function formatRupiah(angka) {
    var number_string = angka.toString(),
    sisa 	= number_string.length % 3,
    rupiah 	= number_string.substr(0, sisa),
    ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
        
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return 'Rp ' + rupiah;
}
</script>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
