<?php
/**
 * Halaman pendaftaran zakat untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Inisialisasi variabel
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$jenis_zakat = isset($_POST['jenis_zakat']) ? $_POST['jenis_zakat'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$metode_pembayaran = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama) || empty($jenis_zakat) || empty($jumlah) || empty($metode_pembayaran)) {
        setAlert('Nama, jenis zakat, jumlah, dan metode pembayaran harus diisi', 'danger');
    } else {
        // Upload bukti pembayaran jika ada
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/zakat');
            
            if (!$bukti_pembayaran) {
                setAlert('Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(BASE_URL . '/pages/zakat_daftar.php');
            }
        }
        
        // Escape string
        $nama = escapeString($nama);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $jenis_zakat = escapeString($jenis_zakat);
        $jumlah = escapeString($jumlah);
        $metode_pembayaran = escapeString($metode_pembayaran);
        $keterangan = escapeString($keterangan);
        
        // Insert data
        $tanggal = date('Y-m-d');
        $status = 'pending';
        
        $query = "INSERT INTO zakat (nama_muzakki, alamat, no_telepon, email, jenis_zakat, jumlah, tanggal, metode_pembayaran, bukti_pembayaran, keterangan, status) 
                  VALUES ('$nama', '$alamat', '$no_telepon', '$email', '$jenis_zakat', '$jumlah', '$tanggal', '$metode_pembayaran', '$bukti_pembayaran', '$keterangan', '$status')";
        
        if (execute($query)) {
            setAlert('Pendaftaran zakat berhasil. Terima kasih atas kontribusi Anda.', 'success');
            redirect(BASE_URL . '/pages/zakat_daftar.php');
        } else {
            setAlert('Gagal mendaftarkan zakat. Silakan coba lagi.', 'danger');
        }
    }
}

// Ambil data rekening masjid
$rekeningMasjid = [
    ['bank' => 'BCA', 'nomor' => '1234567890', 'atas_nama' => 'Masjid Nur Insan Kamil'],
    ['bank' => 'Mandiri', 'nomor' => '0987654321', 'atas_nama' => 'Masjid Nur Insan Kamil'],
    ['bank' => 'BNI', 'nomor' => '1122334455', 'atas_nama' => 'Masjid Nur Insan Kamil']
];
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Pendaftaran Zakat</h1>
                <p class="lead">Tunaikan kewajiban zakat Anda melalui Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/zakat-hero.jpg" alt="Zakat" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Informasi Zakat Section -->
<section class="info-zakat py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Tentang Zakat</h3>
                    </div>
                    <div class="card-body">
                        <p>Zakat adalah salah satu rukun Islam yang wajib ditunaikan oleh setiap Muslim yang memenuhi syarat (muzakki). Zakat merupakan bentuk ibadah yang memiliki dimensi sosial, dengan tujuan untuk membersihkan harta dan jiwa, serta membantu mereka yang membutuhkan.</p>
                        
                        <h5 class="mt-4">Jenis-jenis Zakat:</h5>
                        <ul>
                            <li><strong>Zakat Fitrah:</strong> Zakat yang wajib dikeluarkan oleh setiap Muslim di akhir bulan Ramadhan.</li>
                            <li><strong>Zakat Maal (Harta):</strong> Zakat yang dikeluarkan dari harta kekayaan seperti emas, perak, uang, hasil pertanian, hasil peternakan, hasil perdagangan, dan lain-lain.</li>
                            <li><strong>Zakat Profesi:</strong> Zakat yang dikeluarkan dari penghasilan profesi.</li>
                        </ul>
                        
                        <h5 class="mt-4">Penerima Zakat (Mustahik):</h5>
                        <p>Zakat disalurkan kepada 8 golongan (asnaf) yang berhak menerimanya, yaitu: fakir, miskin, amil zakat, muallaf, riqab (budak), gharimin (orang yang berhutang), fisabilillah (orang yang berjuang di jalan Allah), dan ibnu sabil (musafir).</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">Rekening Zakat</h3>
                    </div>
                    <div class="card-body">
                        <p>Anda dapat menyalurkan zakat melalui rekening berikut:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bank</th>
                                        <th>Nomor Rekening</th>
                                        <th>Atas Nama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekeningMasjid as $rekening): ?>
                                        <tr>
                                            <td><?= $rekening['bank'] ?></td>
                                            <td><?= $rekening['nomor'] ?></td>
                                            <td><?= $rekening['atas_nama'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <h5>QRIS:</h5>
                            <div class="text-center">
                                <img src="<?= BASE_URL ?>/assets/images/qris-code.png" alt="QRIS Code" class="img-fluid" style="max-width: 200px;">
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i> Setelah melakukan pembayaran, silakan isi formulir pendaftaran zakat dan unggah bukti pembayaran.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Pendaftaran Zakat Section -->
<section class="form-zakat py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Formulir Pendaftaran Zakat</h2>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= $nama ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= $alamat ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?= $no_telepon ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="jenis_zakat" class="form-label">Jenis Zakat <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_zakat" name="jenis_zakat" required>
                                    <option value="" <?= $jenis_zakat == '' ? 'selected' : '' ?>>Pilih Jenis Zakat</option>
                                    <option value="Zakat Fitrah" <?= $jenis_zakat == 'Zakat Fitrah' ? 'selected' : '' ?>>Zakat Fitrah</option>
                                    <option value="Zakat Maal" <?= $jenis_zakat == 'Zakat Maal' ? 'selected' : '' ?>>Zakat Maal (Harta)</option>
                                    <option value="Zakat Profesi" <?= $jenis_zakat == 'Zakat Profesi' ? 'selected' : '' ?>>Zakat Profesi</option>
                                    <option value="Zakat Perdagangan" <?= $jenis_zakat == 'Zakat Perdagangan' ? 'selected' : '' ?>>Zakat Perdagangan</option>
                                    <option value="Zakat Pertanian" <?= $jenis_zakat == 'Zakat Pertanian' ? 'selected' : '' ?>>Zakat Pertanian</option>
                                    <option value="Zakat Emas/Perak" <?= $jenis_zakat == 'Zakat Emas/Perak' ? 'selected' : '' ?>>Zakat Emas/Perak</option>
                                    <option value="Lainnya" <?= $jenis_zakat == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $jumlah ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                    <option value="" <?= $metode_pembayaran == '' ? 'selected' : '' ?>>Pilih Metode Pembayaran</option>
                                    <option value="Transfer Bank" <?= $metode_pembayaran == 'Transfer Bank' ? 'selected' : '' ?>>Transfer Bank</option>
                                    <option value="QRIS" <?= $metode_pembayaran == 'QRIS' ? 'selected' : '' ?>>QRIS</option>
                                    <option value="Tunai" <?= $metode_pembayaran == 'Tunai' ? 'selected' : '' ?>>Tunai</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
                                <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= $keterangan ?></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Daftar Zakat</button>
                            </div>
                        </form>
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
