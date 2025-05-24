<?php
/**
 * Halaman masukan proses untuk pengguna umum
 */

// Memuat file konfigurasi dan fungsi
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Proses form masukan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
    $isi = isset($_POST['isi']) ? $_POST['isi'] : '';
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($jenis) || empty($isi)) {
        setAlert('Nama, email, jenis masukan, dan isi masukan harus diisi', 'danger');
        redirect(BASE_URL);
    }
    
    // Escape string
    $nama = escapeString($nama);
    $email = escapeString($email);
    $no_telepon = escapeString($no_telepon);
    $jenis = escapeString($jenis);
    $isi = escapeString($isi);
    
    // Insert data
    $tanggal = date('Y-m-d H:i:s');
    $query = "INSERT INTO masukan (nama, email, no_telepon, jenis, isi, tanggal, status) VALUES ('$nama', '$email', '$no_telepon', '$jenis', '$isi', '$tanggal', 'belum_dibaca')";
    
    if (execute($query)) {
        setAlert('Terima kasih! Masukan Anda telah berhasil dikirim.', 'success');
    } else {
        setAlert('Maaf, terjadi kesalahan saat mengirim masukan. Silakan coba lagi.', 'danger');
    }
    
    redirect(BASE_URL);
} else {
    // Jika bukan method POST, redirect ke halaman utama
    redirect(BASE_URL);
}
?>
