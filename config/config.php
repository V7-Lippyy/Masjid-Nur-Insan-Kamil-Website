<?php
/**
 * Konfigurasi dasar untuk website Sistem Informasi dan Manajemen Nur Insan Kamil
 */

// Mengatasi masalah headers already sent
ob_start();

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'masjid_nur_insan_kamil');

// Konfigurasi URL
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/masjid_nur_insan_kamil');
define('ADMIN_URL', BASE_URL . '/admin');

// Konfigurasi direktori
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ASSETS_PATH . '/uploads');

// Konfigurasi upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Konfigurasi session
session_start();

// Zona waktu
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk debugging
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}
