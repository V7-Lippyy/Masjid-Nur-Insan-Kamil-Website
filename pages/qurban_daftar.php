<?php
/**
 * Halaman pendaftaran qurban untuk pengguna umum
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
$jenis_hewan = isset($_POST['jenis_hewan']) ? $_POST['jenis_hewan'] : '';
$jumlah_hewan = isset($_POST['jumlah_hewan']) ? $_POST['jumlah_hewan'] : 1;
$atas_nama = isset($_POST['atas_nama']) ? $_POST['atas_nama'] : '';
$metode_pembayaran = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Ambil data harga qurban
$hargaQurban = [
    'Kambing' => 2500000,
    'Sapi (1/7 bagian)' => 3000000,
    'Sapi (Utuh)' => 21000000
];

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama) || empty($jenis_hewan) || empty($atas_nama) || empty($metode_pembayaran)) {
        setAlert('Nama, jenis hewan, atas nama, dan metode pembayaran harus diisi', 'danger');
    } else {
        // Upload bukti pembayaran jika ada
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/qurban');
            
            if (!$bukti_pembayaran) {
                setAlert('Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(BASE_URL . '/pages/qurban_daftar.php');
            }
        }
        
        // Escape string
        $nama = escapeString($nama);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $jenis_hewan = escapeString($jenis_hewan);
        $jumlah_hewan = (int)$jumlah_hewan;
        $atas_nama = escapeString($atas_nama);
        $metode_pembayaran = escapeString($metode_pembayaran);
        $keterangan = escapeString($keterangan);
        
        // Hitung total harga
        $harga_satuan = $hargaQurban[$jenis_hewan] ?? 0;
        $total_bayar = $harga_satuan * $jumlah_hewan;
        
        // Insert data
        $tanggal = date('Y-m-d');
        $tahun = date('Y');
        $status = 'pending';
        
        $query = "INSERT INTO qurban (nama_lengkap, alamat, no_telepon, email, jenis_hewan, jumlah_hewan, atas_nama, tanggal_daftar, tahun, metode_pembayaran, total_bayar, bukti_pembayaran, keterangan, status) 
                  VALUES ('$nama', '$alamat', '$no_telepon', '$email', '$jenis_hewan', $jumlah_hewan, '$atas_nama', '$tanggal', '$tahun', '$metode_pembayaran', $total_bayar, '$bukti_pembayaran', '$keterangan', '$status')";
        
        if (execute($query)) {
            setAlert('Pendaftaran qurban berhasil. Terima kasih atas kontribusi Anda.', 'success');
            redirect(BASE_URL . '/pages/qurban_daftar.php');
        } else {
            setAlert('Gagal mendaftarkan qurban. Silakan coba lagi.', 'danger');
        }
    }
}

// Ambil data rekening masjid
$rekeningMasjid = [
    ['bank' => 'BCA', 'nomor' => '1234567890', 'atas_nama' => 'Masjid Nur Insan Kamil'],
    ['bank' => 'Mandiri', 'nomor' => '0987654321', 'atas_nama' => 'Masjid Nur Insan Kamil'],
    ['bank' => 'BNI', 'nomor' => '1122334455', 'atas_nama' => 'Masjid Nur Insan Kamil']
];

// Ambil tahun qurban saat ini
$tahunQurban = date('Y');
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Pendaftaran Qurban <?= $tahunQurban ?> H</h1>
                <p class="lead">Tunaikan ibadah qurban Anda melalui Masjid Nur Insan Kamil</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/qurban-hero.jpg" alt="Qurban" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Informasi Qurban Section -->
<section class="info-qurban py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Tentang Qurban</h3>
                    </div>
                    <div class="card-body">
                        <p>Qurban atau Udhhiyah adalah ibadah penyembelihan hewan ternak pada hari raya Idul Adha dan hari-hari Tasyriq (11, 12, dan 13 Dzulhijjah) dengan tujuan untuk mendekatkan diri kepada Allah SWT.</p>
                        
                        <h5 class="mt-4">Keutamaan Qurban:</h5>
                        <ul>
                            <li>Mendekatkan diri kepada Allah SWT</li>
                            <li>Menghidupkan sunnah Nabi Ibrahim AS</li>
                            <li>Berbagi kebahagiaan dengan sesama, terutama kaum dhuafa</li>
                            <li>Setiap helai bulu hewan qurban menjadi kebaikan bagi yang berqurban</li>
                        </ul>
                        
                        <h5 class="mt-4">Penyaluran Daging Qurban:</h5>
                        <p>Daging qurban akan disalurkan kepada:</p>
                        <ul>
                            <li>Fakir miskin di sekitar masjid</li>
                            <li>Anak yatim dan dhuafa</li>
                            <li>Pesantren dan panti asuhan</li>
                            <li>Jamaah masjid</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">Harga & Rekening Qurban</h3>
                    </div>
                    <div class="card-body">
                        <h5>Harga Hewan Qurban <?= $tahunQurban ?>:</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Hewan</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hargaQurban as $jenis => $harga): ?>
                                        <tr>
                                            <td><?= $jenis ?></td>
                                            <td><?= formatRupiah($harga) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <h5>Rekening Pembayaran:</h5>
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
                            <i class="fas fa-info-circle me-2"></i> Setelah melakukan pembayaran, silakan isi formulir pendaftaran qurban dan unggah bukti pembayaran.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Pendaftaran Qurban Section -->
<section class="form-qurban py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Formulir Pendaftaran Qurban</h2>
        
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
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jenis_hewan" class="form-label">Jenis Hewan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jenis_hewan" name="jenis_hewan" required>
                                        <option value="" <?= $jenis_hewan == '' ? 'selected' : '' ?>>Pilih Jenis Hewan</option>
                                        <?php foreach ($hargaQurban as $jenis => $harga): ?>
                                            <option value="<?= $jenis ?>" <?= $jenis_hewan == $jenis ? 'selected' : '' ?> data-harga="<?= $harga ?>"><?= $jenis ?> - <?= formatRupiah($harga) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_hewan" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="jumlah_hewan" name="jumlah_hewan" value="<?= $jumlah_hewan ?>" min="1" max="10">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="atas_nama" class="form-label">Atas Nama (Shohibul Qurban) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="atas_nama" name="atas_nama" value="<?= $atas_nama ?>" required>
                                <small class="text-muted">Nama yang akan dibacakan saat penyembelihan qurban</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="total_bayar" class="form-label">Total Pembayaran</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="total_bayar" readonly>
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
                                <button type="submit" class="btn btn-primary">Daftar Qurban</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript untuk menghitung total bayar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisHewanSelect = document.getElementById('jenis_hewan');
    const jumlahHewanInput = document.getElementById('jumlah_hewan');
    const totalBayarInput = document.getElementById('total_bayar');
    
    function hitungTotalBayar() {
        const selectedOption = jenisHewanSelect.options[jenisHewanSelect.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga') || 0;
        const jumlah = jumlahHewanInput.value || 1;
        
        const total = harga * jumlah;
        totalBayarInput.value = formatRupiah(total);
    }
    
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
    
    jenisHewanSelect.addEventListener('change', hitungTotalBayar);
    jumlahHewanInput.addEventListener('change', hitungTotalBayar);
    jumlahHewanInput.addEventListener('input', hitungTotalBayar);
    
    // Hitung total bayar saat halaman dimuat
    hitungTotalBayar();
});
</script>

<?php
// Memuat footer
require_once '../includes/footer.php';
?>
