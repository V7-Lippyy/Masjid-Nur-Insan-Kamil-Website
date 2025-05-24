<?php
/**
 * Halaman masukan (kritik dan saran) untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Memuat header
require_once '../includes/header.php';

// Inisialisasi variabel
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
$isi = isset($_POST['isi']) ? $_POST['isi'] : '';
$success = false;

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = 'Nama harus diisi';
    }
    
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    
    if (empty($jenis)) {
        $errors[] = 'Jenis masukan harus dipilih';
    }
    
    if (empty($isi)) {
        $errors[] = 'Isi masukan harus diisi';
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        // Escape string
        $nama = escapeString($nama);
        $email = escapeString($email);
        $no_telepon = escapeString($no_telepon);
        $jenis = escapeString($jenis);
        $isi = escapeString($isi);
        
        // Insert data
        $query = "INSERT INTO masukan (nama, email, no_telepon, jenis, isi, status) VALUES ('$nama', '$email', '$no_telepon', '$jenis', '$isi', 'baru')";
        
        if (execute($query)) {
            $success = true;
            // Reset form
            $nama = $email = $no_telepon = $jenis = $isi = '';
        } else {
            $errors[] = 'Gagal mengirim masukan. Silakan coba lagi nanti.';
        }
    }
}

// Hitung jumlah masukan yang sudah ditanggapi
$totalMasukan = fetchOne("SELECT COUNT(*) as total FROM masukan WHERE status = 'selesai'")['total'] ?? 0;
?>

<!-- Hero Section -->
<section class="hero-section bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Kritik & Saran</h1>
                <p class="lead">Sampaikan kritik, saran, atau pertanyaan Anda kepada kami</p>
            </div>
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/assets/images/contact-hero.jpg" alt="Kritik dan Saran" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="form-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Form Masukan</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i> Terima Kasih!</h5>
                                <p>Masukan Anda telah berhasil dikirim. Kami akan segera menanggapinya.</p>
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
                        
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $nama ?>" required>
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
                                    <label for="jenis" class="form-label">Jenis Masukan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jenis" name="jenis" required>
                                        <option value="" <?= $jenis == '' ? 'selected' : '' ?>>Pilih Jenis Masukan</option>
                                        <option value="kritik" <?= $jenis == 'kritik' ? 'selected' : '' ?>>Kritik</option>
                                        <option value="saran" <?= $jenis == 'saran' ? 'selected' : '' ?>>Saran</option>
                                        <option value="pertanyaan" <?= $jenis == 'pertanyaan' ? 'selected' : '' ?>>Pertanyaan</option>
                                        <option value="lainnya" <?= $jenis == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="isi" class="form-label">Isi Masukan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="isi" name="isi" rows="5" required><?= $isi ?></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Kirim Masukan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Kontak Kami</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i> Jl. Contoh No. 123, Kota
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone me-2 text-primary"></i> (021) 1234567
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope me-2 text-primary"></i> info@masjidnurinsankamil.com
                            </li>
                            <li>
                                <i class="fas fa-clock me-2 text-primary"></i> Senin - Jumat: 08.00 - 16.00
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Statistik</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="display-4 fw-bold text-primary"><?= $totalMasukan ?></h2>
                            <p class="lead">Masukan telah ditanggapi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Pertanyaan Umum</h2>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                Berapa lama masukan saya akan ditanggapi?
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Kami berusaha menanggapi setiap masukan dalam waktu 1-3 hari kerja. Untuk masalah mendesak, silakan hubungi kami melalui nomor telepon yang tertera.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                Apakah masukan saya akan dipublikasikan?
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Tidak, masukan Anda bersifat privat dan hanya akan digunakan untuk keperluan internal masjid. Kami menjaga kerahasiaan identitas dan isi masukan Anda.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                Bagaimana cara mengetahui tanggapan atas masukan saya?
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Kami akan mengirimkan tanggapan melalui email yang Anda berikan. Pastikan alamat email yang Anda masukkan benar dan periksa folder spam jika tidak menerima balasan dalam waktu yang ditentukan.
                            </div>
                        </div>
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
