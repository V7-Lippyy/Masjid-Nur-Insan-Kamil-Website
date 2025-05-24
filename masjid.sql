-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 01:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `masjid_nur_insan_kamil`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `role` enum('super_admin','admin') NOT NULL DEFAULT 'admin',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `email`, `no_telepon`, `role`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'admin', '123', 'Administrator', 'admin@masjidnurinsankamil.com', '08123456789', 'super_admin', NULL, '2025-05-22 10:30:49', '2025-05-23 23:34:11'),
(2, 'alif', '123', '', '', NULL, 'admin', NULL, '2025-05-22 10:50:01', '2025-05-22 10:50:01');

-- --------------------------------------------------------

--
-- Table structure for table `donasi`
--

CREATE TABLE `donasi` (
  `id` int(11) NOT NULL,
  `donatur_id` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donasi`
--

INSERT INTO `donasi` (`id`, `donatur_id`, `jumlah`, `tanggal`, `metode_pembayaran`, `keterangan`, `bukti_pembayaran`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 5000000.00, '2025-05-22', 'Transfer Bank', 'Donasi untuk pembangunan masjid', NULL, 'diterima', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 2, 1000000.00, '2025-05-17', 'Tunai', 'Donasi untuk kegiatan pengajian', NULL, 'diterima', '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `donatur`
--

CREATE TABLE `donatur` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jenis_donatur` enum('Tetap','Tidak Tetap') DEFAULT 'Tidak Tetap',
  `tanggal_bergabung` date DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donatur`
--

INSERT INTO `donatur` (`id`, `nama`, `alamat`, `no_telepon`, `email`, `jenis_donatur`, `tanggal_bergabung`, `status`, `created_at`, `updated_at`) VALUES
(1, 'H. Slamet', 'Jl. Anggrek No. 50', '081234567897', 'slamet@example.com', 'Tetap', '2025-05-22', 'aktif', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 'Ibu Mariam', 'Jl. Dahlia No. 45', '081234567898', 'mariam@example.com', 'Tidak Tetap', '2025-05-17', 'aktif', '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `judul`, `deskripsi`, `kategori`, `gambar`, `tanggal`, `created_at`, `updated_at`) VALUES
(1, 'tes', 'sds', 'Acara', '682f0189290b0.png', '2025-05-22', '2025-05-22 10:50:49', '2025-05-22 10:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

CREATE TABLE `inventaris` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `status` enum('baik','rusak','hilang') DEFAULT 'baik',
  `tanggal_perolehan` date DEFAULT NULL,
  `sumber_dana` varchar(100) DEFAULT NULL,
  `nilai_aset` decimal(15,2) DEFAULT NULL,
  `nilai_perolehan` decimal(15,2) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kondisi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id`, `nama_barang`, `kategori`, `jumlah`, `satuan`, `status`, `tanggal_perolehan`, `sumber_dana`, `nilai_aset`, `nilai_perolehan`, `lokasi`, `foto`, `keterangan`, `created_at`, `updated_at`, `kondisi`) VALUES
(4, 'TES', 'Acara', 1, NULL, 'rusak', '2025-05-22', 'Donasi', 20000.00, NULL, NULL, '682f113e34894.png', 'sa', '2025-05-22 11:57:50', '2025-05-22 11:57:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_imam_khatib`
--

CREATE TABLE `jadwal_imam_khatib` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_shalat` enum('subuh','dzuhur','ashar','maghrib','isya','jumat') NOT NULL,
  `nama_imam` varchar(100) NOT NULL,
  `nama_khatib` varchar(100) DEFAULT NULL,
  `tema_khutbah` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_imam_khatib`
--

INSERT INTO `jadwal_imam_khatib` (`id`, `tanggal`, `waktu_shalat`, `nama_imam`, `nama_khatib`, `tema_khutbah`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, '2025-05-22', 'jumat', 'cds', 'sd', 'sd', 's', '2025-05-22 10:59:50', '2025-05-22 11:55:29'),
(2, '2025-05-29', 'jumat', 'cds', '23', '23', '23', '2025-05-22 11:55:58', '2025-05-22 11:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_shalat`
--

CREATE TABLE `jadwal_shalat` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `subuh` time NOT NULL,
  `dzuhur` time NOT NULL,
  `ashar` time NOT NULL,
  `maghrib` time NOT NULL,
  `isya` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_shalat`
--

INSERT INTO `jadwal_shalat` (`id`, `tanggal`, `subuh`, `dzuhur`, `ashar`, `maghrib`, `isya`, `created_at`, `updated_at`) VALUES
(1, '2025-05-22', '04:30:00', '12:00:00', '15:30:00', '18:00:00', '19:15:00', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, '2025-05-23', '04:31:00', '12:00:00', '15:30:00', '18:01:00', '19:16:00', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(3, '2025-05-24', '04:31:00', '12:00:00', '15:30:00', '18:01:00', '19:16:00', '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `kas_acara`
--

CREATE TABLE `kas_acara` (
  `id` int(11) NOT NULL,
  `kegiatan_id` int(11) NOT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kas_acara`
--

INSERT INTO `kas_acara` (`id`, `kegiatan_id`, `jenis`, `jumlah`, `tanggal`, `keterangan`, `bukti`, `created_at`, `updated_at`) VALUES
(1, 1, 'pemasukan', 1000000.00, '2025-05-22', 'Sumbangan untuk pengajian mingguan', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 1, 'pengeluaran', 300000.00, '2025-05-22', 'Konsumsi pengajian mingguan', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(3, 2, 'pemasukan', 3000000.00, '2025-05-27', 'Sumbangan untuk buka puasa bersama', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(4, 2, 'pengeluaran', 2500000.00, '2025-05-31', 'Belanja bahan makanan untuk buka puasa', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed','canceled') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `nama_kegiatan`, `kategori`, `tanggal_mulai`, `tanggal_selesai`, `lokasi`, `deskripsi`, `poster`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Pengajian Mingguan', 'Kajian', '2025-05-22 19:30:00', '2025-05-22 21:00:00', 'Ruang Utama Masjid', 'Pengajian rutin mingguan dengan tema \"Fiqih Kontemporer\"', NULL, 'upcoming', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 'Buka Puasa Bersama', 'Sosial', '2025-06-01 17:30:00', '2025-06-01 19:00:00', 'Halaman Masjid', 'Buka puasa bersama untuk jamaah dan masyarakat sekitar masjid', '682f10eb128c7.png', 'ongoing', '2025-05-22 10:30:49', '2025-05-23 02:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id` int(11) NOT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan`
--

INSERT INTO `keuangan` (`id`, `jenis`, `kategori`, `jumlah`, `tanggal`, `keterangan`, `bukti`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 'pengeluaran', 'Infaq Jumat', 40000.00, '2025-05-22', 'Beli Gorengan', NULL, 2, '2025-05-22 10:30:49', '2025-05-22 10:58:09'),
(2, 'pemasukan', 'Donasi', 5000000.00, '2025-05-21', 'Donasi dari Bapak H. Slamet', NULL, 1, '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(3, 'pengeluaran', 'Listrik', 750000.00, '2025-05-20', 'Pembayaran listrik bulan ini', NULL, 1, '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(4, 'pengeluaran', 'Kebersihan', 500000.00, '2025-05-19', 'Gaji petugas kebersihan', NULL, 1, '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `masukan`
--

CREATE TABLE `masukan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `jenis` enum('kritik','saran','pertanyaan','lainnya') NOT NULL,
  `isi` text NOT NULL,
  `status` enum('baru','dibaca','diproses','selesai') DEFAULT 'baru',
  `tanggapan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `masukan`
--

INSERT INTO `masukan` (`id`, `nama`, `email`, `no_telepon`, `jenis`, `isi`, `status`, `tanggapan`, `created_at`, `updated_at`) VALUES
(1, 'Rahmat', 'rahmat@example.com', '081234567899', 'saran', 'Mohon jadwal pengajian ditambah untuk hari Minggu', 'dibaca', 'Baik', '2025-05-22 10:30:49', '2025-05-22 11:05:24'),
(2, 'Fatimah', 'fatimah@example.com', '081234567890', 'pertanyaan', 'Apakah ada kegiatan TPA untuk anak-anak?', 'diproses', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `isi` text NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `tanggal_mulai`, `tanggal_selesai`, `status`, `gambar`, `created_at`, `updated_at`) VALUES
(1, 'Jadwal Shalat Idul Fitri', 'Diberitahukan kepada seluruh jamaah bahwa Shalat Idul Fitri akan dilaksanakan pada tanggal 1 Syawal 1446 H pukul 07.00 WIB', '2025-05-22', '2025-06-21', 'aktif', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 'Pengajian Rutin', 'Pengajian rutin akan dilaksanakan setiap hari Kamis ba\'da Maghrib', '2025-05-22', NULL, 'aktif', NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `pengurus`
--

CREATE TABLE `pengurus` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengurus`
--

INSERT INTO `pengurus` (`id`, `nama`, `jabatan`, `alamat`, `no_telepon`, `email`, `foto`, `deskripsi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'H. Ahmad Fauzi', 'Ketua DKM', 'Jl. Melati No. 10', '081234567890', 'ahmad@example.com', NULL, 'Ketua DKM Masjid Nur Insan Kamil periode 2023-2026', 'aktif', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 'Ustadz Budi Santoso', 'Imam Utama', 'Jl. Anggrek No. 15', '081234567891', 'budi@example.com', NULL, 'Imam tetap Masjid Nur Insan Kamil', 'aktif', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(3, 'H. Darmawan', 'Bendahara', 'Jl. Dahlia No. 20', '081234567892', 'darmawan@example.com', NULL, 'Bendahara Masjid Nur Insan Kamil periode 2023-2026', 'aktif', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(4, 'Muhammad Alif Qadri', 'Ketua BIBDs', 'SEPINGAN, BALIKPAPAN', '2323', 'alifqadry@gmail.com', '682fdf83d7009.jpg', 'SDSd', 'aktif', '2025-05-22 10:51:23', '2025-05-23 02:37:56');

-- --------------------------------------------------------

--
-- Table structure for table `qurban`
--

CREATE TABLE `qurban` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_hewan` varchar(50) NOT NULL,
  `jumlah_hewan` int(11) NOT NULL DEFAULT 1,
  `atas_nama` varchar(100) DEFAULT NULL,
  `tahun` varchar(10) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected','selesai') DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `total_bayar` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `qurban`
--

INSERT INTO `qurban` (`id`, `nama_lengkap`, `jenis_hewan`, `jumlah_hewan`, `atas_nama`, `tahun`, `tanggal_daftar`, `alamat`, `no_telepon`, `email`, `status`, `bukti_pembayaran`, `total_bayar`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', 'Kambing', 1, 'Keluarga Budi Santoso', '2025', '2025-05-22', 'Jl. Melati No. 25', '081234567895', 'budi@example.com', 'approved', NULL, 2500000.00, 'Qurban untuk Idul Adha 1446 H', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 'Ani Wulandari', 'Sapi (1/7 bagian)', 1, 'Alm. H. Wulandari', '2025', '2025-05-20', 'Jl. Cempaka No. 30', '081234567896', 'ani@example.com', 'approved', NULL, 3000000.00, 'Qurban untuk Idul Adha 1446 H', '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `nama_masjid` varchar(100) NOT NULL DEFAULT 'Masjid Nur Insan Kamil',
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `rekening_bank` text DEFAULT NULL,
  `qris_image` varchar(255) DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `sejarah` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `nama_masjid`, `alamat`, `no_telepon`, `email`, `logo`, `deskripsi`, `rekening_bank`, `qris_image`, `visi`, `misi`, `sejarah`, `created_at`, `updated_at`) VALUES
(1, 'Masjid Nur Insan Kamil', 'Jl. Masjid No. 123, Kota Indah', '021-12345678', 'info@masjidnurinsankamil.com', NULL, 'Masjid Nur Insan Kamil adalah tempat ibadah dan pusat kegiatan Islam yang melayani masyarakat sekitar.', NULL, NULL, NULL, NULL, NULL, '2025-05-22 10:30:49', '2025-05-22 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `zakat`
--

CREATE TABLE `zakat` (
  `id` int(11) NOT NULL,
  `nama_muzakki` varchar(100) NOT NULL,
  `jenis_zakat` varchar(50) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status` enum('pending','diterima','disalurkan') DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zakat`
--

INSERT INTO `zakat` (`id`, `nama_muzakki`, `jenis_zakat`, `jumlah`, `tanggal`, `alamat`, `no_telepon`, `email`, `metode_pembayaran`, `status`, `bukti_pembayaran`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Hidayat', 'Zakat Fitrah', 50000.00, '2025-05-22', 'Jl. Mawar No. 15', '081234567893', 'ahmad@example.com', 'Transfer Bank', 'diterima', NULL, 'Zakat fitrah untuk 1 orang', '2025-05-22 10:30:49', '2025-05-22 10:30:49'),
(2, 'Siti Aminah', 'Zakat Maal', 2500000.00, '2025-05-21', 'Jl. Kenanga No. 20', '081234567894', 'siti@example.com', 'Tunai', 'diterima', NULL, 'Zakat maal tahunan', '2025-05-22 10:30:49', '2025-05-22 10:30:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `donasi`
--
ALTER TABLE `donasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donatur_id` (`donatur_id`);

--
-- Indexes for table `donatur`
--
ALTER TABLE `donatur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal_imam_khatib`
--
ALTER TABLE `jadwal_imam_khatib`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal_shalat`
--
ALTER TABLE `jadwal_shalat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indexes for table `kas_acara`
--
ALTER TABLE `kas_acara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kegiatan_id` (`kegiatan_id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `masukan`
--
ALTER TABLE `masukan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qurban`
--
ALTER TABLE `qurban`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zakat`
--
ALTER TABLE `zakat`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donasi`
--
ALTER TABLE `donasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donatur`
--
ALTER TABLE `donatur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jadwal_imam_khatib`
--
ALTER TABLE `jadwal_imam_khatib`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal_shalat`
--
ALTER TABLE `jadwal_shalat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kas_acara`
--
ALTER TABLE `kas_acara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `masukan`
--
ALTER TABLE `masukan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `qurban`
--
ALTER TABLE `qurban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zakat`
--
ALTER TABLE `zakat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donasi`
--
ALTER TABLE `donasi`
  ADD CONSTRAINT `donasi_ibfk_1` FOREIGN KEY (`donatur_id`) REFERENCES `donatur` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kas_acara`
--
ALTER TABLE `kas_acara`
  ADD CONSTRAINT `kas_acara_ibfk_1` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD CONSTRAINT `keuangan_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
