<?php
/**
 * Halaman donasi untuk pengguna umum
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
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$metode_pembayaran = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama) || empty($jumlah) || empty($metode_pembayaran)) {
        setAlert('Nama, jumlah, dan metode pembayaran harus diisi', 'danger');
    } else {
        // Upload bukti pembayaran jika ada
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/donatur');
            
            if (!$bukti_pembayaran) {
                setAlert('Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(BASE_URL . '/pages/donasi.php');
            }
        }
        
        // Escape string
        $nama = escapeString($nama);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $jumlah = escapeString($jumlah);
        $metode_pembayaran = escapeString($metode_pembayaran);
        $keterangan = escapeString($keterangan);
        
        // Cek apakah donatur sudah ada
        $donatur_id = 0;
        $donatur = fetchOne("SELECT id FROM donatur WHERE nama = '$nama' AND (no_telepon = '$no_telepon' OR email = '$email')");
        
        if ($donatur) {
            $donatur_id = $donatur['id'];
        } else {
            // Insert data donatur baru
            $query = "INSERT INTO donatur (nama, alamat, no_telepon, email, jenis_donatur, tanggal_bergabung, status) 
                      VALUES ('$nama', '$alamat', '$no_telepon', '$email', 'Tidak Tetap', NOW(), 'aktif')";
            
            if (execute($query)) {
                $donatur_id = getLastInsertId();
            }
        }
        
        if ($donatur_id > 0) {
            // Insert data donasi
            $tanggal = date('Y-m-d');
            
            $query = "INSERT INTO donasi (donatur_id, jumlah, tanggal, metode_pembayaran, keterangan, bukti_pembayaran) 
                      VALUES ($donatur_id, '$jumlah', '$tanggal', '$metode_pembayaran', '$keterangan', '$bukti_pembayaran')";
            
            if (execute($query)) {
                setAlert('Donasi berhasil dikirim. Terima kasih atas kontribusi Anda.', 'success');
                redirect(BASE_URL . '/pages/donasi.php');
            } else {
                setAlert('Gagal mengirim donasi. Silakan coba lagi.', 'danger');
            }
        } else {
            setAlert('Gagal menyimpan data donatur. Silakan coba lagi.', 'danger');
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
                <h1 class="display-4 fw-bold">Donasi</h1>
                <p class="lead">Berkontribusi untuk kemakmuran Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/donasi-hero.jpg" alt="Donasi" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Informasi Donasi Section -->
<section class="info-donasi py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Tentang Donasi</h3>
                    </div>
                    <div class="card-body">
                        <p>Donasi adalah bentuk kepedulian dan kontribusi untuk kemakmuran masjid dan kegiatan-kegiatan yang bermanfaat bagi umat. Dengan berdonasi, Anda telah ikut berpartisipasi dalam:</p>
                        
                        <h5 class="mt-4">Program-program Masjid:</h5>
                        <ul>
                            <li>Pembangunan dan renovasi masjid</li>
                            <li>Kegiatan pendidikan Islam</li>
                            <li>Santunan anak yatim dan dhuafa</li>
                            <li>Bantuan sosial untuk masyarakat sekitar</li>
                            <li>Pengembangan fasilitas masjid</li>
                        </ul>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i> Semua donasi akan dikelola secara transparan dan dipertanggungjawabkan kepada jamaah melalui laporan keuangan masjid yang dapat diakses di website ini.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">Rekening Donasi</h3>
                    </div>
                    <div class="card-body">
                        <p>Anda dapat menyalurkan donasi melalui rekening berikut:</p>
                        
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
                            <i class="fas fa-info-circle me-2"></i> Setelah melakukan pembayaran, silakan isi formulir donasi dan unggah bukti pembayaran.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Donasi Section -->
<section class="form-donasi py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Formulir Donasi</h2>
        
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
                                <label for="jumlah" class="form-label">Jumlah Donasi <span class="text-danger">*</span></label>
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
                                    <option value="E-Wallet" <?= $metode_pembayaran == 'E-Wallet' ? 'selected' : '' ?>>E-Wallet</option>
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
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Untuk pembangunan masjid, untuk santunan anak yatim, dll."><?= $keterangan ?></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Kirim Donasi</button>
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
