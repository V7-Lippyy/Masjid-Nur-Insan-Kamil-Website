<?php
/**
 * Halaman kelola pengumuman
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$judul = isset($_POST['judul']) ? $_POST['judul'] : '';
$isi = isset($_POST['isi']) ? $_POST['isi'] : '';
$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tanggal_selesai = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'aktif';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($judul) || empty($isi) || empty($tanggal_mulai)) {
        setAlert('Judul, isi, dan tanggal mulai harus diisi', 'danger');
    } else {
        // Upload gambar jika ada
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $gambar = uploadFile($_FILES['gambar'], UPLOADS_PATH . '/pengumuman');
            
            if (!$gambar) {
                setAlert('Gagal upload gambar. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/pengumuman.php');
            }
        }
        
        // Escape string
        $judul = escapeString($judul);
        $isi = escapeString($isi);
        $tanggal_mulai = escapeString($tanggal_mulai);
        $tanggal_selesai = escapeString($tanggal_selesai);
        $status = escapeString($status);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM pengumuman WHERE id = $id");
            
            if ($gambar) {
                // Hapus gambar lama jika ada
                if (!empty($oldData['gambar'])) {
                    deleteFile(UPLOADS_PATH . '/pengumuman/' . $oldData['gambar']);
                }
                
                $query = "UPDATE pengumuman SET judul = '$judul', isi = '$isi', tanggal_mulai = '$tanggal_mulai', tanggal_selesai = " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", status = '$status', gambar = '$gambar' WHERE id = $id";
            } else {
                $query = "UPDATE pengumuman SET judul = '$judul', isi = '$isi', tanggal_mulai = '$tanggal_mulai', tanggal_selesai = " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", status = '$status' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data pengumuman berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/pengumuman.php');
            } else {
                setAlert('Gagal update data pengumuman', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO pengumuman (judul, isi, tanggal_mulai, tanggal_selesai, status, gambar) VALUES ('$judul', '$isi', '$tanggal_mulai', " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", '$status', '$gambar')";
            
            if (execute($query)) {
                setAlert('Data pengumuman berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/pengumuman.php');
            } else {
                setAlert('Gagal menambahkan data pengumuman', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM pengumuman WHERE id = $id");
    
    if ($data) {
        // Hapus gambar jika ada
        if (!empty($data['gambar'])) {
            deleteFile(UPLOADS_PATH . '/pengumuman/' . $data['gambar']);
        }
        
        // Hapus data
        if (execute("DELETE FROM pengumuman WHERE id = $id")) {
            setAlert('Data pengumuman berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data pengumuman', 'danger');
        }
    } else {
        setAlert('Data pengumuman tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/pengumuman.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM pengumuman WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data pengumuman tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/pengumuman.php');
    }
}

// Filter status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil semua data pengumuman
$whereClause = $statusFilter ? "WHERE status = '$statusFilter'" : "";
$dataPengumuman = fetchAll("SELECT * FROM pengumuman $whereClause ORDER BY tanggal_mulai DESC");

// Hitung jumlah pengumuman per status
$totalAktif = fetchOne("SELECT COUNT(*) as total FROM pengumuman WHERE status = 'aktif'")['total'] ?? 0;
$totalTidakAktif = fetchOne("SELECT COUNT(*) as total FROM pengumuman WHERE status = 'tidak_aktif'")['total'] ?? 0;
$totalPengumuman = $totalAktif + $totalTidakAktif;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Pengumuman</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Pengumuman
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Status: <?= $statusFilter ? ucfirst(str_replace('_', ' ', $statusFilter)) : 'Semua' ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?">Semua</a></li>
                    <li><a class="dropdown-item" href="?status=aktif">Aktif</a></li>
                    <li><a class="dropdown-item" href="?status=tidak_aktif">Tidak Aktif</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pengumuman</h5>
                    <h3 class="card-text"><?= $totalPengumuman ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Aktif</h5>
                    <h3 class="card-text"><?= $totalAktif ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Tidak Aktif</h5>
                    <h3 class="card-text"><?= $totalTidakAktif ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengumuman <?= $statusFilter ? "(" . ucfirst(str_replace('_', ' ', $statusFilter)) . ")" : "" ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataPengumuman as $index => $pengumuman): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (!empty($pengumuman['gambar'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/uploads/pengumuman/<?= $pengumuman['gambar'] ?>" alt="Gambar <?= $pengumuman['judul'] ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/images/default-announcement.png" alt="Default" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?= $pengumuman['judul'] ?></td>
                                <td>
                                    <?= formatDate($pengumuman['tanggal_mulai']) ?>
                                    <?php if (!empty($pengumuman['tanggal_selesai'])): ?>
                                        <br>s/d<br>
                                        <?= formatDate($pengumuman['tanggal_selesai']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pengumuman['status'] == 'aktif'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $pengumuman['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $pengumuman['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="<?= $dataEdit ? $dataEdit['judul'] : '' ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= $dataEdit ? $dataEdit['tanggal_mulai'] : date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= $dataEdit && $dataEdit['tanggal_selesai'] ? $dataEdit['tanggal_selesai'] : '' ?>">
                                <small class="text-muted">Kosongkan jika tidak ada batas waktu</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="aktif" <?= ($dataEdit && $dataEdit['status'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                            <option value="tidak_aktif" <?= ($dataEdit && $dataEdit['status'] == 'tidak_aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar</label>
                        <?php if ($dataEdit && !empty($dataEdit['gambar'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>/assets/uploads/pengumuman/<?= $dataEdit['gambar'] ?>" alt="Gambar <?= $dataEdit['judul'] ?>" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="gambar" name="gambar">
                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                    </div>
                    <div class="mb-3">
                        <label for="isi" class="form-label">Isi Pengumuman</label>
                        <textarea class="form-control summernote" id="isi" name="isi" rows="5"><?= $dataEdit ? $dataEdit['isi'] : '' ?></textarea>
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
