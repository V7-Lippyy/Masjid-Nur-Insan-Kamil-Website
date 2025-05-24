<?php
/**
 * Halaman kelola kegiatan
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama_kegiatan = isset($_POST['nama_kegiatan']) ? $_POST['nama_kegiatan'] : '';
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tanggal_selesai = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$lokasi = isset($_POST['lokasi']) ? $_POST['lokasi'] : '';
$deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'upcoming';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama_kegiatan) || empty($kategori) || empty($tanggal_mulai)) {
        setAlert('Nama kegiatan, kategori, dan tanggal mulai harus diisi', 'danger');
    } else {
        // Upload poster jika ada
        $poster = '';
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $poster = uploadFile($_FILES['poster'], UPLOADS_PATH . '/kegiatan');
            
            if (!$poster) {
                setAlert('Gagal upload poster. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/kegiatan.php');
            }
        }
        
        // Escape string
        $nama_kegiatan = escapeString($nama_kegiatan);
        $kategori = escapeString($kategori);
        $tanggal_mulai = escapeString($tanggal_mulai);
        $tanggal_selesai = escapeString($tanggal_selesai);
        $lokasi = escapeString($lokasi);
        $deskripsi = escapeString($deskripsi);
        $status = escapeString($status);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM kegiatan WHERE id = $id");
            
            if ($poster) {
                // Hapus poster lama jika ada
                if (!empty($oldData['poster'])) {
                    deleteFile(UPLOADS_PATH . '/kegiatan/' . $oldData['poster']);
                }
                
                $query = "UPDATE kegiatan SET nama_kegiatan = '$nama_kegiatan', kategori = '$kategori', tanggal_mulai = '$tanggal_mulai', tanggal_selesai = " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", lokasi = '$lokasi', deskripsi = '$deskripsi', poster = '$poster', status = '$status' WHERE id = $id";
            } else {
                $query = "UPDATE kegiatan SET nama_kegiatan = '$nama_kegiatan', kategori = '$kategori', tanggal_mulai = '$tanggal_mulai', tanggal_selesai = " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", lokasi = '$lokasi', deskripsi = '$deskripsi', status = '$status' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data kegiatan berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/kegiatan.php');
            } else {
                setAlert('Gagal update data kegiatan', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO kegiatan (nama_kegiatan, kategori, tanggal_mulai, tanggal_selesai, lokasi, deskripsi, poster, status) VALUES ('$nama_kegiatan', '$kategori', '$tanggal_mulai', " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", '$lokasi', '$deskripsi', '$poster', '$status')";
            
            if (execute($query)) {
                setAlert('Data kegiatan berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/kegiatan.php');
            } else {
                setAlert('Gagal menambahkan data kegiatan', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM kegiatan WHERE id = $id");
    
    if ($data) {
        // Hapus poster jika ada
        if (!empty($data['poster'])) {
            deleteFile(UPLOADS_PATH . '/kegiatan/' . $data['poster']);
        }
        
        // Hapus data
        if (execute("DELETE FROM kegiatan WHERE id = $id")) {
            setAlert('Data kegiatan berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data kegiatan', 'danger');
        }
    } else {
        setAlert('Data kegiatan tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/kegiatan.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM kegiatan WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data kegiatan tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/kegiatan.php');
    }
}

// Filter status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil semua data kegiatan
$whereClause = $statusFilter ? "WHERE status = '$statusFilter'" : "";
$dataKegiatan = fetchAll("SELECT * FROM kegiatan $whereClause ORDER BY tanggal_mulai DESC");

// Hitung jumlah kegiatan per status
$totalUpcoming = fetchOne("SELECT COUNT(*) as total FROM kegiatan WHERE status = 'upcoming'")['total'] ?? 0;
$totalOngoing = fetchOne("SELECT COUNT(*) as total FROM kegiatan WHERE status = 'ongoing'")['total'] ?? 0;
$totalCompleted = fetchOne("SELECT COUNT(*) as total FROM kegiatan WHERE status = 'completed'")['total'] ?? 0;
$totalCanceled = fetchOne("SELECT COUNT(*) as total FROM kegiatan WHERE status = 'canceled'")['total'] ?? 0;
$totalKegiatan = $totalUpcoming + $totalOngoing + $totalCompleted + $totalCanceled;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Kegiatan</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Status: <?= $statusFilter ? ucfirst($statusFilter) : 'Semua' ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?">Semua</a></li>
                    <li><a class="dropdown-item" href="?status=upcoming">Upcoming</a></li>
                    <li><a class="dropdown-item" href="?status=ongoing">Ongoing</a></li>
                    <li><a class="dropdown-item" href="?status=completed">Completed</a></li>
                    <li><a class="dropdown-item" href="?status=canceled">Canceled</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Kegiatan</h5>
                    <h3 class="card-text"><?= $totalKegiatan ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Upcoming</h5>
                    <h3 class="card-text"><?= $totalUpcoming ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ongoing</h5>
                    <h3 class="card-text"><?= $totalOngoing ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h3 class="card-text"><?= $totalCompleted ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Kegiatan <?= $statusFilter ? "(" . ucfirst($statusFilter) . ")" : "" ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Poster</th>
                            <th>Nama Kegiatan</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataKegiatan as $index => $kegiatan): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (!empty($kegiatan['poster'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $kegiatan['poster'] ?>" alt="Poster <?= $kegiatan['nama_kegiatan'] ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/images/default-event.png" alt="Default" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?= $kegiatan['nama_kegiatan'] ?></td>
                                <td><?= $kegiatan['kategori'] ?></td>
                                <td>
                                    <?= formatDate($kegiatan['tanggal_mulai'], 'd M Y H:i') ?>
                                    <?php if (!empty($kegiatan['tanggal_selesai'])): ?>
                                        <br>s/d<br>
                                        <?= formatDate($kegiatan['tanggal_selesai'], 'd M Y H:i') ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= $kegiatan['lokasi'] ?></td>
                                <td>
                                    <?php if ($kegiatan['status'] == 'upcoming'): ?>
                                        <span class="badge bg-info">Upcoming</span>
                                    <?php elseif ($kegiatan['status'] == 'ongoing'): ?>
                                        <span class="badge bg-success">Ongoing</span>
                                    <?php elseif ($kegiatan['status'] == 'completed'): ?>
                                        <span class="badge bg-secondary">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Canceled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $kegiatan['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $kegiatan['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                    <a href="<?= ADMIN_URL ?>/kas_acara.php?kegiatan_id=<?= $kegiatan['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-money-bill-wave"></i> Kas
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" value="<?= $dataEdit ? $dataEdit['nama_kegiatan'] : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori" value="<?= $dataEdit ? $dataEdit['kategori'] : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= $dataEdit ? date('Y-m-d\TH:i', strtotime($dataEdit['tanggal_mulai'])) : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= $dataEdit && $dataEdit['tanggal_selesai'] ? date('Y-m-d\TH:i', strtotime($dataEdit['tanggal_selesai'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?= $dataEdit ? $dataEdit['lokasi'] : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="upcoming" <?= ($dataEdit && $dataEdit['status'] == 'upcoming') ? 'selected' : '' ?>>Upcoming</option>
                                    <option value="ongoing" <?= ($dataEdit && $dataEdit['status'] == 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
                                    <option value="completed" <?= ($dataEdit && $dataEdit['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="canceled" <?= ($dataEdit && $dataEdit['status'] == 'canceled') ? 'selected' : '' ?>>Canceled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="poster" class="form-label">Poster</label>
                                <?php if ($dataEdit && !empty($dataEdit['poster'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= BASE_URL ?>/assets/uploads/kegiatan/<?= $dataEdit['poster'] ?>" alt="Poster <?= $dataEdit['nama_kegiatan'] ?>" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="poster" name="poster">
                                <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control summernote" id="deskripsi" name="deskripsi" rows="5"><?= $dataEdit ? $dataEdit['deskripsi'] : '' ?></textarea>
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
