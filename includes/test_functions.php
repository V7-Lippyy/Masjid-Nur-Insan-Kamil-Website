<?php
/**
 * Fungsi untuk menambahkan link menu baru di header
 */
function addGuestMenuLinks() {
    // Tambahkan kode ini di includes/header.php setelah menu yang sudah ada
    ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/pages/keuangan.php">Keuangan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/pages/kas_acara.php">Kas Acara</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/pages/inventaris.php">Inventaris</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="layananDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Layanan
        </a>
        <ul class="dropdown-menu" aria-labelledby="layananDropdown">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/zakat_daftar.php">Pendaftaran Zakat</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/qurban_daftar.php">Pendaftaran Qurban</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/qurban_list.php">Daftar Pengqurban</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/donasi.php">Donasi</a></li>
        </ul>
    </li>
    <?php
}

/**
 * Fungsi untuk menguji koneksi database
 */
function testDatabaseConnection() {
    global $conn;
    
    if (!$conn) {
        return "Koneksi database gagal: " . mysqli_connect_error();
    }
    
    return "Koneksi database berhasil";
}

/**
 * Fungsi untuk menguji fitur view keuangan
 */
function testViewKeuangan() {
    global $conn;
    
    $query = "SELECT * FROM keuangan LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query keuangan gagal: " . mysqli_error($conn);
    }
    
    $count = mysqli_num_rows($result);
    return "Query keuangan berhasil, mendapatkan $count data";
}

/**
 * Fungsi untuk menguji fitur view kas acara
 */
function testViewKasAcara() {
    global $conn;
    
    $query = "SELECT ka.*, k.nama_kegiatan FROM kas_acara ka JOIN kegiatan k ON ka.kegiatan_id = k.id LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query kas acara gagal: " . mysqli_error($conn);
    }
    
    $count = mysqli_num_rows($result);
    return "Query kas acara berhasil, mendapatkan $count data";
}

/**
 * Fungsi untuk menguji fitur view inventaris
 */
function testViewInventaris() {
    global $conn;
    
    $query = "SELECT * FROM inventaris LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query inventaris gagal: " . mysqli_error($conn);
    }
    
    $count = mysqli_num_rows($result);
    return "Query inventaris berhasil, mendapatkan $count data";
}

/**
 * Fungsi untuk menguji fitur pendaftaran zakat
 */
function testZakatRegistration() {
    global $conn;
    
    // Cek struktur tabel
    $query = "DESCRIBE zakat";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query struktur tabel zakat gagal: " . mysqli_error($conn);
    }
    
    $fields = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fields[] = $row['Field'];
    }
    
    $requiredFields = ['nama_muzakki', 'jenis_zakat', 'jumlah', 'tanggal', 'metode_pembayaran', 'status'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!in_array($field, $fields)) {
            $missingFields[] = $field;
        }
    }
    
    if (count($missingFields) > 0) {
        return "Tabel zakat tidak memiliki field yang diperlukan: " . implode(', ', $missingFields);
    }
    
    return "Struktur tabel zakat sesuai dengan kebutuhan";
}

/**
 * Fungsi untuk menguji fitur pendaftaran qurban
 */
function testQurbanRegistration() {
    global $conn;
    
    // Cek struktur tabel
    $query = "DESCRIBE qurban";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query struktur tabel qurban gagal: " . mysqli_error($conn);
    }
    
    $fields = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fields[] = $row['Field'];
    }
    
    $requiredFields = ['nama_lengkap', 'jenis_hewan', 'jumlah_hewan', 'atas_nama', 'tahun', 'tanggal_daftar', 'metode_pembayaran', 'total_bayar', 'status'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!in_array($field, $fields)) {
            $missingFields[] = $field;
        }
    }
    
    if (count($missingFields) > 0) {
        return "Tabel qurban tidak memiliki field yang diperlukan: " . implode(', ', $missingFields);
    }
    
    return "Struktur tabel qurban sesuai dengan kebutuhan";
}

/**
 * Fungsi untuk menguji fitur donasi
 */
function testDonasiRegistration() {
    global $conn;
    
    // Cek struktur tabel donatur
    $query = "DESCRIBE donatur";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query struktur tabel donatur gagal: " . mysqli_error($conn);
    }
    
    $fields = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fields[] = $row['Field'];
    }
    
    $requiredFields = ['nama', 'alamat', 'no_telepon', 'email', 'jenis_donatur', 'tanggal_bergabung', 'status'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!in_array($field, $fields)) {
            $missingFields[] = $field;
        }
    }
    
    if (count($missingFields) > 0) {
        return "Tabel donatur tidak memiliki field yang diperlukan: " . implode(', ', $missingFields);
    }
    
    // Cek struktur tabel donasi
    $query = "DESCRIBE donasi";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query struktur tabel donasi gagal: " . mysqli_error($conn);
    }
    
    $fields = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fields[] = $row['Field'];
    }
    
    $requiredFields = ['donatur_id', 'jumlah', 'tanggal', 'metode_pembayaran', 'keterangan', 'bukti_pembayaran', 'status'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!in_array($field, $fields)) {
            $missingFields[] = $field;
        }
    }
    
    if (count($missingFields) > 0) {
        return "Tabel donasi tidak memiliki field yang diperlukan: " . implode(', ', $missingFields);
    }
    
    return "Struktur tabel donatur dan donasi sesuai dengan kebutuhan";
}

/**
 * Fungsi untuk menguji fitur view daftar pengqurban
 */
function testViewPengqurban() {
    global $conn;
    
    $query = "SELECT * FROM qurban WHERE status = 'approved' LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return "Query daftar pengqurban gagal: " . mysqli_error($conn);
    }
    
    $count = mysqli_num_rows($result);
    return "Query daftar pengqurban berhasil, mendapatkan $count data";
}

/**
 * Fungsi untuk menjalankan semua tes
 */
function runAllTests() {
    $results = [];
    
    $results['database'] = testDatabaseConnection();
    $results['keuangan'] = testViewKeuangan();
    $results['kas_acara'] = testViewKasAcara();
    $results['inventaris'] = testViewInventaris();
    $results['zakat'] = testZakatRegistration();
    $results['qurban'] = testQurbanRegistration();
    $results['donasi'] = testDonasiRegistration();
    $results['pengqurban'] = testViewPengqurban();
    
    return $results;
}
