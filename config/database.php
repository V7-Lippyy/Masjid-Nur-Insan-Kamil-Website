<?php
/**
 * Koneksi database untuk website Sistem Informasi dan Manajemen Nur Insan Kamil
 */

// Memuat file konfigurasi
require_once 'config.php';

/**
 * Fungsi untuk membuat koneksi ke database
 * @return mysqli Object koneksi database
 */
function getConnection() {
    // Membuat koneksi baru
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Memeriksa koneksi
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }
    
    // Set charset ke utf8
    $conn->set_charset("utf8");
    
    return $conn;
}

/**
 * Fungsi untuk menjalankan query dan mengembalikan hasil
 * @param string $sql Query SQL yang akan dijalankan
 * @return mixed Hasil query
 */
function query($sql) {
    $conn = getConnection();
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Query error: " . $conn->error);
    }
    
    $conn->close();
    return $result;
}

/**
 * Fungsi untuk menjalankan query dan mengembalikan array hasil
 * @param string $sql Query SQL yang akan dijalankan
 * @return array Hasil query dalam bentuk array asosiatif
 */
function fetchAll($sql) {
    $result = query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    $result->free();
    return $data;
}

/**
 * Fungsi untuk menjalankan query dan mengembalikan satu baris hasil
 * @param string $sql Query SQL yang akan dijalankan
 * @return array|null Hasil query dalam bentuk array asosiatif atau null jika tidak ada hasil
 */
function fetchOne($sql) {
    $result = query($sql);
    
    $row = $result->fetch_assoc();
    $result->free();
    
    return $row;
}

/**
 * Fungsi untuk menjalankan query insert, update, atau delete
 * @param string $sql Query SQL yang akan dijalankan
 * @return int Jumlah baris yang terpengaruh
 */
function execute($sql) {
    $conn = getConnection();
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Query error: " . $conn->error);
    }
    
    $affectedRows = $conn->affected_rows;
    $conn->close();
    
    return $affectedRows;
}

/**
 * Fungsi untuk mendapatkan ID terakhir yang diinsert
 * @return int ID terakhir
 */
function getLastInsertId() {
    $conn = getConnection();
    $lastId = $conn->insert_id;
    $conn->close();
    
    return $lastId;
}

/**
 * Fungsi untuk escape string untuk mencegah SQL injection
 * @param string $str String yang akan di-escape
 * @return string String yang sudah di-escape
 */
function escapeString($str) {
    $conn = getConnection();
    $escaped = $conn->real_escape_string($str);
    $conn->close();
    
    return $escaped;
}
