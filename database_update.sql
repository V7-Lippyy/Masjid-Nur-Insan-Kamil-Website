-- Database updates for new guest features
-- Created: May 22, 2025

-- Update zakat table to match implementation
ALTER TABLE zakat 
MODIFY COLUMN jenis_zakat VARCHAR(50) NOT NULL,
ADD COLUMN metode_pembayaran VARCHAR(50) AFTER email;

-- Update qurban table to match implementation
ALTER TABLE qurban 
CHANGE COLUMN nama_pengqurban nama_lengkap VARCHAR(100) NOT NULL,
CHANGE COLUMN tahun_hijriah tahun VARCHAR(10) NOT NULL,
CHANGE COLUMN jumlah_bayar total_bayar DECIMAL(15, 2) NOT NULL,
MODIFY COLUMN jenis_hewan VARCHAR(50) NOT NULL,
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'selesai') DEFAULT 'pending';

-- Create donasi table (separate from donatur)
CREATE TABLE IF NOT EXISTS donasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donatur_id INT NOT NULL,
    jumlah DECIMAL(15, 2) NOT NULL,
    tanggal DATE NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    keterangan TEXT,
    bukti_pembayaran VARCHAR(255),
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donatur_id) REFERENCES donatur(id) ON DELETE CASCADE
);

-- Update donatur table to match implementation
ALTER TABLE donatur 
DROP COLUMN jenis_donasi,
DROP COLUMN jumlah,
DROP COLUMN tanggal,
DROP COLUMN bukti,
ADD COLUMN jenis_donatur ENUM('Tetap', 'Tidak Tetap') DEFAULT 'Tidak Tetap' AFTER email,
ADD COLUMN tanggal_bergabung DATE AFTER jenis_donatur;

-- Update inventaris table to match implementation
ALTER TABLE inventaris 
CHANGE COLUMN kondisi status ENUM('baik', 'rusak', 'hilang') DEFAULT 'baik',
CHANGE COLUMN tanggal_pengadaan tanggal_perolehan DATE,
CHANGE COLUMN nilai_aset nilai_perolehan DECIMAL(15, 2),
ADD COLUMN satuan VARCHAR(50) AFTER jumlah,
ADD COLUMN lokasi VARCHAR(100) AFTER nilai_perolehan;
