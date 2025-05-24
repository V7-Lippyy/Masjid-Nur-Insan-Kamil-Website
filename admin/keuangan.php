<?php
/**
 * Halaman kelola keuangan
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
$adminId = $_SESSION['admin']['id'];

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($jenis) || empty($kategori) || empty($jumlah) || empty($tanggal)) {
        setAlert('Semua field harus diisi kecuali keterangan', 'danger');
    } else {
        // Upload bukti jika ada
        $bukti = '';
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $bukti = uploadFile($_FILES['bukti'], UPLOADS_PATH . '/keuangan');
            
            if (!$bukti) {
                setAlert('Gagal upload bukti. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/keuangan.php');
            }
        }
        
        // Escape string
        $jenis = escapeString($jenis);
        $kategori = escapeString($kategori);
        $jumlah = escapeString($jumlah);
        $tanggal = escapeString($tanggal);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM keuangan WHERE id = $id");
            
            if ($bukti) {
                // Hapus bukti lama jika ada
                if (!empty($oldData['bukti'])) {
                    deleteFile(UPLOADS_PATH . '/keuangan/' . $oldData['bukti']);
                }
                
                $query = "UPDATE keuangan SET jenis = '$jenis', kategori = '$kategori', jumlah = '$jumlah', tanggal = '$tanggal', keterangan = '$keterangan', bukti = '$bukti', admin_id = $adminId WHERE id = $id";
            } else {
                $query = "UPDATE keuangan SET jenis = '$jenis', kategori = '$kategori', jumlah = '$jumlah', tanggal = '$tanggal', keterangan = '$keterangan', admin_id = $adminId WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data keuangan berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/keuangan.php');
            } else {
                setAlert('Gagal update data keuangan', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO keuangan (jenis, kategori, jumlah, tanggal, keterangan, bukti, admin_id) VALUES ('$jenis', '$kategori', '$jumlah', '$tanggal', '$keterangan', '$bukti', $adminId)";
            
            if (execute($query)) {
                setAlert('Data keuangan berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/keuangan.php');
            } else {
                setAlert('Gagal menambahkan data keuangan', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM keuangan WHERE id = $id");
    
    if ($data) {
        // Hapus bukti jika ada
        if (!empty($data['bukti'])) {
            deleteFile(UPLOADS_PATH . '/keuangan/' . $data['bukti']);
        }
        
        // Hapus data
        if (execute("DELETE FROM keuangan WHERE id = $id")) {
            setAlert('Data keuangan berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data keuangan', 'danger');
        }
    } else {
        setAlert('Data keuangan tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/keuangan.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM keuangan WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data keuangan tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/keuangan.php');
    }
}

// Ambil semua data keuangan
$dataKeuangan = fetchAll("SELECT k.*, a.nama_lengkap FROM keuangan k LEFT JOIN admin a ON k.admin_id = a.id ORDER BY k.tanggal DESC");

// Hitung total pemasukan, pengeluaran, dan saldo
$totalPemasukan = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pemasukan'")['total'] ?? 0;
$totalPengeluaran = fetchOne("SELECT SUM(jumlah) as total FROM keuangan WHERE jenis = 'pengeluaran'")['total'] ?? 0;
$saldo = $totalPemasukan - $totalPengeluaran;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Keuangan</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pemasukan</h5>
                    <h3 class="card-text"><?= formatRupiah($totalPemasukan) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pengeluaran</h5>
                    <h3 class="card-text"><?= formatRupiah($totalPengeluaran) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Saldo</h5>
                    <h3 class="card-text"><?= formatRupiah($saldo) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Keuangan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Bukti</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataKeuangan as $index => $keuangan): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= formatDate($keuangan['tanggal']) ?></td>
                                <td>
                                    <?php if ($keuangan['jenis'] == 'pemasukan'): ?>
                                        <span class="badge bg-success">Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $keuangan['kategori'] ?></td>
                                <td><?= formatRupiah($keuangan['jumlah']) ?></td>
                                <td><?= $keuangan['keterangan'] ?></td>
                                <td>
                                    <?php if (!empty($keuangan['bukti'])): ?>
                                        <a href="<?= BASE_URL ?>/assets/uploads/keuangan/<?= $keuangan['bukti'] ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $keuangan['nama_lengkap'] ?></td>
                                <td>
                                    <a href="?action=edit&id=<?= $keuangan['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $keuangan['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Data Keuangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select class="form-select" id="jenis" name="jenis" required>
                            <option value="">Pilih Jenis</option>
                            <option value="pemasukan" <?= ($dataEdit && $dataEdit['jenis'] == 'pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                            <option value="pengeluaran" <?= ($dataEdit && $dataEdit['jenis'] == 'pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" value="<?= $dataEdit ? $dataEdit['kategori'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $dataEdit ? $dataEdit['jumlah'] : '' ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $dataEdit ? $dataEdit['tanggal'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= $dataEdit ? $dataEdit['keterangan'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="bukti" class="form-label">Bukti</label>
                        <?php if ($dataEdit && !empty($dataEdit['bukti'])): ?>
                            <div class="mb-2">
                                <a href="<?= BASE_URL ?>/assets/uploads/keuangan/<?= $dataEdit['bukti'] ?>" target="_blank">Lihat Bukti</a>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="bukti" name="bukti">
                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if ($dataEdit): ?>
                        <input type="hidden" name="update" value="true">
                        <button type="submit" class="btn btn-primary">Update</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($dataEdit): ?>
<script>
    // Trigger modal on page load for edit
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('formModal'));
        myModal.show();
    });
</script>
<?php endif; ?>

<?php
// Memuat file footer
require_once 'includes/footer.php';
?>
