<?php
/**
 * Halaman kelola zakat
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama_muzakki = isset($_POST['nama_muzakki']) ? $_POST['nama_muzakki'] : '';
$jenis_zakat = isset($_POST['jenis_zakat']) ? $_POST['jenis_zakat'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'pending';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama_muzakki) || empty($jenis_zakat) || empty($jumlah) || empty($tanggal)) {
        setAlert('Nama muzakki, jenis zakat, jumlah, dan tanggal harus diisi', 'danger');
    } else {
        // Upload bukti pembayaran jika ada
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/zakat');
            
            if (!$bukti_pembayaran) {
                setAlert('Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/zakat.php');
            }
        }
        
        // Escape string
        $nama_muzakki = escapeString($nama_muzakki);
        $jenis_zakat = escapeString($jenis_zakat);
        $jumlah = escapeString($jumlah);
        $tanggal = escapeString($tanggal);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $status = escapeString($status);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM zakat WHERE id = $id");
            
            if ($bukti_pembayaran) {
                // Hapus bukti lama jika ada
                if (!empty($oldData['bukti_pembayaran'])) {
                    deleteFile(UPLOADS_PATH . '/zakat/' . $oldData['bukti_pembayaran']);
                }
                
                $query = "UPDATE zakat SET nama_muzakki = '$nama_muzakki', jenis_zakat = '$jenis_zakat', jumlah = '$jumlah', tanggal = '$tanggal', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', status = '$status', bukti_pembayaran = '$bukti_pembayaran', keterangan = '$keterangan' WHERE id = $id";
            } else {
                $query = "UPDATE zakat SET nama_muzakki = '$nama_muzakki', jenis_zakat = '$jenis_zakat', jumlah = '$jumlah', tanggal = '$tanggal', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', status = '$status', keterangan = '$keterangan' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data zakat berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/zakat.php');
            } else {
                setAlert('Gagal update data zakat', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO zakat (nama_muzakki, jenis_zakat, jumlah, tanggal, alamat, no_telepon, email, status, bukti_pembayaran, keterangan) VALUES ('$nama_muzakki', '$jenis_zakat', '$jumlah', '$tanggal', '$alamat', '$no_telepon', '$email', '$status', '$bukti_pembayaran', '$keterangan')";
            
            if (execute($query)) {
                setAlert('Data zakat berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/zakat.php');
            } else {
                setAlert('Gagal menambahkan data zakat', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM zakat WHERE id = $id");
    
    if ($data) {
        // Hapus bukti pembayaran jika ada
        if (!empty($data['bukti_pembayaran'])) {
            deleteFile(UPLOADS_PATH . '/zakat/' . $data['bukti_pembayaran']);
        }
        
        // Hapus data
        if (execute("DELETE FROM zakat WHERE id = $id")) {
            setAlert('Data zakat berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data zakat', 'danger');
        }
    } else {
        setAlert('Data zakat tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/zakat.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM zakat WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data zakat tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/zakat.php');
    }
}

// Ambil semua data zakat
$dataZakat = fetchAll("SELECT * FROM zakat ORDER BY tanggal DESC");

// Hitung total zakat per jenis
$totalFitrah = fetchOne("SELECT SUM(jumlah) as total FROM zakat WHERE jenis_zakat = 'fitrah'")['total'] ?? 0;
$totalMal = fetchOne("SELECT SUM(jumlah) as total FROM zakat WHERE jenis_zakat = 'mal'")['total'] ?? 0;
$totalFidyah = fetchOne("SELECT SUM(jumlah) as total FROM zakat WHERE jenis_zakat = 'fidyah'")['total'] ?? 0;
$totalLainnya = fetchOne("SELECT SUM(jumlah) as total FROM zakat WHERE jenis_zakat = 'lainnya'")['total'] ?? 0;
$totalZakat = $totalFitrah + $totalMal + $totalFidyah + $totalLainnya;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Zakat</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
            <i class="fas fa-plus"></i> Tambah Zakat
        </button>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Zakat</h5>
                    <h3 class="card-text"><?= formatRupiah($totalZakat) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Zakat Fitrah</h5>
                    <h3 class="card-text"><?= formatRupiah($totalFitrah) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Zakat Mal</h5>
                    <h3 class="card-text"><?= formatRupiah($totalMal) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Fidyah & Lainnya</h5>
                    <h3 class="card-text"><?= formatRupiah($totalFidyah + $totalLainnya) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Zakat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Muzakki</th>
                            <th>Jenis Zakat</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataZakat as $index => $zakat): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $zakat['nama_muzakki'] ?></td>
                                <td>
                                    <?php if ($zakat['jenis_zakat'] == 'fitrah'): ?>
                                        <span class="badge bg-success">Fitrah</span>
                                    <?php elseif ($zakat['jenis_zakat'] == 'mal'): ?>
                                        <span class="badge bg-info">Mal</span>
                                    <?php elseif ($zakat['jenis_zakat'] == 'fidyah'): ?>
                                        <span class="badge bg-warning">Fidyah</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Lainnya</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatRupiah($zakat['jumlah']) ?></td>
                                <td><?= formatDate($zakat['tanggal']) ?></td>
                                <td>
                                    <?php if ($zakat['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($zakat['status'] == 'diterima'): ?>
                                        <span class="badge bg-success">Diterima</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Disalurkan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($zakat['bukti_pembayaran'])): ?>
                                        <a href="<?= BASE_URL ?>/assets/uploads/zakat/<?= $zakat['bukti_pembayaran'] ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $zakat['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $zakat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Zakat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_muzakki" class="form-label">Nama Muzakki</label>
                        <input type="text" class="form-control" id="nama_muzakki" name="nama_muzakki" value="<?= $dataEdit ? $dataEdit['nama_muzakki'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_zakat" class="form-label">Jenis Zakat</label>
                        <select class="form-select" id="jenis_zakat" name="jenis_zakat" required>
                            <option value="">Pilih Jenis Zakat</option>
                            <option value="fitrah" <?= ($dataEdit && $dataEdit['jenis_zakat'] == 'fitrah') ? 'selected' : '' ?>>Zakat Fitrah</option>
                            <option value="mal" <?= ($dataEdit && $dataEdit['jenis_zakat'] == 'mal') ? 'selected' : '' ?>>Zakat Mal</option>
                            <option value="fidyah" <?= ($dataEdit && $dataEdit['jenis_zakat'] == 'fidyah') ? 'selected' : '' ?>>Fidyah</option>
                            <option value="lainnya" <?= ($dataEdit && $dataEdit['jenis_zakat'] == 'lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
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
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= $dataEdit ? $dataEdit['alamat'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?= $dataEdit ? $dataEdit['no_telepon'] : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $dataEdit ? $dataEdit['email'] : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?= ($dataEdit && $dataEdit['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="diterima" <?= ($dataEdit && $dataEdit['status'] == 'diterima') ? 'selected' : '' ?>>Diterima</option>
                            <option value="disalurkan" <?= ($dataEdit && $dataEdit['status'] == 'disalurkan') ? 'selected' : '' ?>>Disalurkan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                        <?php if ($dataEdit && !empty($dataEdit['bukti_pembayaran'])): ?>
                            <div class="mb-2">
                                <a href="<?= BASE_URL ?>/assets/uploads/zakat/<?= $dataEdit['bukti_pembayaran'] ?>" target="_blank">Lihat Bukti</a>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= $dataEdit ? $dataEdit['keterangan'] : '' ?></textarea>
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
