<?php
/**
 * File fungsi-fungsi umum untuk website Sistem Informasi dan Manajemen Nur Insan Kamil
 */

// Memuat file konfigurasi dan database
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

/**
 * Fungsi untuk redirect ke halaman lain
 * @param string $url URL tujuan
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Fungsi untuk menampilkan pesan alert
 * @param string $message Pesan yang akan ditampilkan
 * @param string $type Tipe pesan (success, danger, warning, info)
 */
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Fungsi untuk menampilkan pesan alert
 * @return string HTML alert
 */
function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $message = $alert['message'];
        $type = $alert['type'];
        
        unset($_SESSION['alert']);
        
        return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                    {$message}
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    }
    
    return '';
}

/**
 * Fungsi untuk mengecek apakah user sudah login
 * @return bool True jika sudah login, false jika belum
 */
function isLoggedIn() {
    // Tambahkan logging untuk debugging session
    error_log("SESSION CHECK - Admin session: " . (isset($_SESSION['admin']) ? json_encode($_SESSION['admin']) : "NOT SET"));
    return isset($_SESSION['admin']);
}

/**
 * Fungsi untuk mengecek apakah user adalah super admin
 * @return bool True jika super admin, false jika bukan
 */
function isSuperAdmin() {
    return isLoggedIn() && $_SESSION['admin']['role'] == 'super_admin';
}

/**
 * Fungsi untuk memastikan user sudah login
 * Jika belum login, redirect ke halaman login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setAlert('Anda harus login terlebih dahulu', 'danger');
        redirect(BASE_URL . '/admin/login.php');
    }
}

/**
 * Fungsi untuk memastikan user adalah super admin
 * Jika bukan super admin, redirect ke halaman dashboard
 */
function requireSuperAdmin() {
    requireLogin();
    
    if (!isSuperAdmin()) {
        setAlert('Anda tidak memiliki akses ke halaman ini', 'danger');
        redirect(ADMIN_URL . '/index.php');
    }
}

/**
 * Fungsi untuk upload file
 * @param array $file File yang akan diupload ($_FILES['nama_field'])
 * @param string $destination Direktori tujuan
 * @param array $allowedExtensions Ekstensi yang diperbolehkan
 * @return string|false Nama file jika berhasil, false jika gagal
 */
function uploadFile($file, $destination, $allowedExtensions = ALLOWED_EXTENSIONS) {
    // Cek apakah ada error
    if ($file['error'] != 0) {
        return false;
    }
    
    // Cek ukuran file
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    // Cek ekstensi file
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, $allowedExtensions)) {
        return false;
    }
    
    // Generate nama file baru
    $newFileName = uniqid() . '.' . $extension;
    $targetPath = $destination . '/' . $newFileName;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $newFileName;
    }
    
    return false;
}

/**
 * Fungsi untuk menghapus file
 * @param string $path Path file yang akan dihapus
 * @return bool True jika berhasil, false jika gagal
 */
function deleteFile($path) {
    if (file_exists($path)) {
        return unlink($path);
    }
    
    return false;
}

/**
 * Fungsi untuk format tanggal
 * @param string $date Tanggal dalam format Y-m-d
 * @param string $format Format tanggal
 * @return string Tanggal yang sudah diformat
 */
function formatDate($date, $format = 'd F Y') {
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Fungsi untuk format waktu
 * @param string $time Waktu dalam format H:i:s
 * @param string $format Format waktu
 * @return string Waktu yang sudah diformat
 */
function formatTime($time, $format = 'H:i') {
    $timestamp = strtotime($time);
    return date($format, $timestamp);
}

/**
 * Fungsi untuk format angka ke rupiah
 * @param float $number Angka yang akan diformat
 * @return string Angka dalam format rupiah
 */
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

/**
 * Fungsi untuk mendapatkan pengaturan website
 * @return array Pengaturan website
 */
function getSettings() {
    return fetchOne("SELECT * FROM settings WHERE id = 1");
}

/**
 * Fungsi untuk mendapatkan menu aktif
 * @param string $menu Nama menu
 * @param string $active Nama class untuk menu aktif
 * @return string Class untuk menu aktif
 */
function isActiveMenu($menu, $active = 'active') {
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    
    if ($currentPage == $menu) {
        return $active;
    }
    
    return '';
}

/**
 * Fungsi untuk pagination
 * @param int $totalData Total data
 * @param int $limit Limit data per halaman
 * @param int $currentPage Halaman saat ini
 * @param string $url URL halaman
 * @return string HTML pagination
 */
function pagination($totalData, $limit, $currentPage, $url) {
    $totalPage = ceil($totalData / $limit);
    
    if ($totalPage <= 1) {
        return '';
    }
    
    $pagination = '<nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $pagination .= '<li class="page-item">
                            <a class="page-link" href="' . $url . '?page=' . ($currentPage - 1) . '" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
    } else {
        $pagination .= '<li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPage, $currentPage + 2);
    
    if ($startPage > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=1">1</a></li>';
        
        if ($startPage > 2) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $pagination .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPage) {
        if ($endPage < $totalPage - 1) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $totalPage . '">' . $totalPage . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPage) {
        $pagination .= '<li class="page-item">
                            <a class="page-link" href="' . $url . '?page=' . ($currentPage + 1) . '" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
    } else {
        $pagination .= '<li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
    }
    
    $pagination .= '</ul>
                </nav>';
    
    return $pagination;
}
