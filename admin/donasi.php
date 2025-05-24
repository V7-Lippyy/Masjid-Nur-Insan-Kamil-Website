<?php
/**
 * Halaman kelola donasi
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$donatur_id = isset($_GET['donatur_id']) ? $_GET['donatur_id'] : (isset($_POST['donatur_id']) ? $_POST['donatur_id'] : '');
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$metode_pembayaran = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Ambil data donatur jika ada donatur_id
$dataDonatur = null;
if (!empty($donatur_id)) {
    $dataDonatur = fetchOne("SELECT * FROM donatur WHERE id = $donatur_id");
    
    if (!$dataDonatur) {
        setAlert('Data donatur tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/donatur.php');
    }
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($donatur_id) || empty($jumlah) || empty($tanggal) || empty($metode_pembayaran)) {
        setAlert('Donatur, jumlah, tanggal, dan metode pembayaran harus diisi', 'danger');
    } else {
        // Upload bukti pembayaran jika ada
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/donatur');
            
            if (!$bukti_pembayaran) {
                setAlert('Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/donasi.php' . ($donatur_id ? "?donatur_id=$donatur_id" : ''));
            }
        }
        
        // Escape string
        $donatur_id = escapeString($donatur_id);
        $jumlah = escapeString($jumlah);
        $tanggal = escapeString($tanggal);
        $metode_pembayaran = escapeString($metode_pembayaran);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM donasi WHERE id = $id");
            
            if ($bukti_pembayaran) {
                // Hapus bukti lama jika ada
                if (!empty($oldData['bukti_pembayaran'])) {
                    deleteFile(UPLOADS_PATH . '/donatur/' . $oldData['bukti_pembayaran']);
                }
                
                $query = "UPDATE donasi SET donatur_id = '$donatur_id', jumlah = '$jumlah', tanggal = '$tanggal', metode_pembayaran = '$metode_pembayaran', keterangan = '$keterangan', bukti_pembayaran = '$bukti_pembayaran' WHERE id = $id";
            } else {
                $query = "UPDATE donasi SET donatur_id = '$donatur_id', jumlah = '$jumlah', tanggal = '$tanggal', metode_pembayaran = '$metode_pembayaran', keterangan = '$keterangan' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data donasi berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/donasi.php' . ($donatur_id ? "?donatur_id=$donatur_id" : ''));
            } else {
                setAlert('Gagal update data donasi', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO donasi (donatur_id, jumlah, tanggal, metode_pembayaran, keterangan, bukti_pembayaran) VALUES ('$donatur_id', '$jumlah', '$tanggal', '$metode_pembayaran', '$keterangan', '$bukti_pembayaran')";
            
            if (execute($query)) {
                setAlert('Data donasi berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/donasi.php' . ($donatur_id ? "?donatur_id=$donatur_id" : ''));
            } else {
                setAlert('Gagal menambahkan data donasi', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM donasi WHERE id = $id");
    
    if ($data) {
        // Hapus bukti pembayaran jika ada
        if (!empty($data['bukti_pembayaran'])) {
            deleteFile(UPLOADS_PATH . '/donatur/' . $data['bukti_pembayaran']);
        }
        
        // Hapus data
        if (execute("DELETE FROM donasi WHERE id = $id")) {
            setAlert('Data donasi berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data donasi', 'danger');
        }
    } else {
        setAlert('Data donasi tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/donasi.php' . ($donatur_id ? "?donatur_id=$donatur_id" : ''));
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM donasi WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data donasi tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/donasi.php' . ($donatur_id ? "?donatur_id=$donatur_id" : ''));
    }
    
    // Set donatur_id dari data edit
    $donatur_id = $dataEdit['donatur_id'];
    
    // Ambil data donatur
    $dataDonatur = fetchOne("SELECT * FROM donatur WHERE id = $donatur_id");
}

// Ambil semua data donatur untuk dropdown
$dataDonaturAll = fetchAll("SELECT * FROM donatur ORDER BY nama ASC");

// Ambil semua data donasi
$whereClause = $donatur_id ? "WHERE d.donatur_id = $donatur_id" : "";
$dataDonasi = fetchAll("SELECT d.*, dn.nama as nama_donatur FROM donasi d JOIN donatur dn ON d.donatur_id = dn.id $whereClause ORDER BY d.tanggal DESC");

// Hitung total donasi
$whereClauseSum = $donatur_id ? "WHERE donatur_id = $donatur_id" : "";
$totalDonasi = fetchOne("SELECT SUM(jumlah) as total FROM donasi $whereClauseSum")['total'] ?? 0;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            Kelola Donasi
            <?php if ($dataDonatur): ?>
                <span class="text-muted">: <?= $dataDonatur['nama'] ?></span>
            <?php endif; ?>
        </h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Donasi
            </button>
            <?php if ($donatur_id): ?>
                <a href="<?= ADMIN_URL ?>/donasi.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Lihat Semua
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Donasi</h5>
                    <h3 class="card-text"><?= formatRupiah($totalDonasi) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Donasi
                <?php if ($dataDonatur): ?>
                    : <?= $dataDonatur['nama'] ?>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Donatur</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Metode Pembayaran</th>
                            <th>Keterangan</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataDonasi as $index => $donasi): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $donasi['nama_donatur'] ?></td>
                                <td><?= formatDate($donasi['tanggal']) ?></td>
                                <td><?= formatRupiah($donasi['jumlah']) ?></td>
                                <td><?= $donasi['metode_pembayaran'] ?></td>
                                <td><?= $donasi['keterangan'] ?></td>
                                <td>
                                    <?php if (!empty($donasi['bukti_pembayaran'])): ?>
                                        <a href="<?= BASE_URL ?>/assets/uploads/donatur/<?= $donasi['bukti_pembayaran'] ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $donasi['id'] ?><?= $donatur_id ? "&donatur_id=$donatur_id" : '' ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $donasi['id'] ?><?= $donatur_id ? "&donatur_id=$donatur_id" : '' ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Donasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="donatur_id" class="form-label">Donatur</label>
                        <select class="form-select" id="donatur_id" name="donatur_id" required <?= $donatur_id ? 'disabled' : '' ?>>
                            <option value="">Pilih Donatur</option>
                            <?php foreach ($dataDonaturAll as $donatur): ?>
                                <option value="<?= $donatur['id'] ?>" <?= ($donatur_id == $donatur['id']) ? 'selected' : '' ?>>
                                    <?= $donatur['nama'] ?> (<?= $donatur['jenis_donatur'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($donatur_id): ?>
                            <input type="hidden" name="donatur_id" value="<?= $donatur_id ?>">
                        <?php endif; ?>
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
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="Tunai" <?= ($dataEdit && $dataEdit['metode_pembayaran'] == 'Tunai') ? 'selected' : '' ?>>Tunai</option>
                            <option value="Transfer Bank" <?= ($dataEdit && $dataEdit['metode_pembayaran'] == 'Transfer Bank') ? 'selected' : '' ?>>Transfer Bank</option>
                            <option value="QRIS" <?= ($dataEdit && $dataEdit['metode_pembayaran'] == 'QRIS') ? 'selected' : '' ?>>QRIS</option>
                            <option value="E-Wallet" <?= ($dataEdit && $dataEdit['metode_pembayaran'] == 'E-Wallet') ? 'selected' : '' ?>>E-Wallet</option>
                            <option value="Lainnya" <?= ($dataEdit && $dataEdit['metode_pembayaran'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                        <?php if ($dataEdit && !empty($dataEdit['bukti_pembayaran'])): ?>
                            <div class="mb-2">
                                <a href="<?= BASE_URL ?>/assets/uploads/donatur/<?= $dataEdit['bukti_pembayaran'] ?>" target="_blank">Lihat Bukti</a>
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
