<?php
/**
 * Halaman logout admin
 */

// Memuat file konfigurasi dan fungsi
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Hapus session
session_destroy();

// Redirect ke halaman login
redirect(ADMIN_URL . '/login.php');
