<?php
/**
 * Halaman kelola donatur
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$jenis_donatur = isset($_POST['jenis_donatur']) ? $_POST['jenis_donatur'] : '';
$tanggal_bergabung = isset($_POST['tanggal_bergabung']) ? $_POST['tanggal_bergabung'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'aktif';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama) || empty($jenis_donatur) || empty($tanggal_bergabung)) {
        setAlert('Nama, jenis donatur, dan tanggal bergabung harus diisi', 'danger');
    } else {
        // Upload foto jika ada
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = uploadFile($_FILES['foto'], UPLOADS_PATH . '/donatur');
            
            if (!$foto) {
                setAlert('Gagal upload foto. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/donatur.php');
            }
        }
        
        // Escape string
        $nama = escapeString($nama);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $jenis_donatur = escapeString($jenis_donatur);
        $tanggal_bergabung = escapeString($tanggal_bergabung);
        $status = escapeString($status);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM donatur WHERE id = $id");
            
            if ($foto) {
                // Hapus foto lama jika ada
                if (!empty($oldData['foto'])) {
                    deleteFile(UPLOADS_PATH . '/donatur/' . $oldData['foto']);
                }
                
                $query = "UPDATE donatur SET nama = '$nama', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', jenis_donatur = '$jenis_donatur', tanggal_bergabung = '$tanggal_bergabung', status = '$status', keterangan = '$keterangan', foto = '$foto' WHERE id = $id";
            } else {
                $query = "UPDATE donatur SET nama = '$nama', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', jenis_donatur = '$jenis_donatur', tanggal_bergabung = '$tanggal_bergabung', status = '$status', keterangan = '$keterangan' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data donatur berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/donatur.php');
            } else {
                setAlert('Gagal update data donatur', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO donatur (nama, alamat, no_telepon, email, jenis_donatur, tanggal_bergabung, status, keterangan, foto) VALUES ('$nama', '$alamat', '$no_telepon', '$email', '$jenis_donatur', '$tanggal_bergabung', '$status', '$keterangan', '$foto')";
            
            if (execute($query)) {
                setAlert('Data donatur berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/donatur.php');
            } else {
                setAlert('Gagal menambahkan data donatur', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM donatur WHERE id = $id");
    
    if ($data) {
        // Hapus foto jika ada
        if (!empty($data['foto'])) {
            deleteFile(UPLOADS_PATH . '/donatur/' . $data['foto']);
        }
        
        // Hapus data
        if (execute("DELETE FROM donatur WHERE id = $id")) {
            setAlert('Data donatur berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data donatur', 'danger');
        }
    } else {
        setAlert('Data donatur tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/donatur.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM donatur WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data donatur tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/donatur.php');
    }
}

// Filter jenis donatur dan status
$jenisFilter = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil semua jenis donatur yang ada
$jenisDonatur = fetchAll("SELECT DISTINCT jenis_donatur FROM donatur ORDER BY jenis_donatur ASC");

// Ambil semua data donatur
$whereClause = [];
if ($jenisFilter) {
    $whereClause[] = "jenis_donatur = '$jenisFilter'";
}
if ($statusFilter) {
    $whereClause[] = "status = '$statusFilter'";
}

$whereString = count($whereClause) > 0 ? "WHERE " . implode(" AND ", $whereClause) : "";
$dataDonatur = fetchAll("SELECT * FROM donatur $whereString ORDER BY nama ASC");

// Hitung jumlah donatur per status
$totalAktif = fetchOne("SELECT COUNT(*) as total FROM donatur WHERE status = 'aktif'")['total'] ?? 0;
$totalTidakAktif = fetchOne("SELECT COUNT(*) as total FROM donatur WHERE status = 'tidak_aktif'")['total'] ?? 0;
$totalDonatur = $totalAktif + $totalTidakAktif;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Donatur</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Donatur
            </button>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Jenis: <?= $jenisFilter ? $jenisFilter : 'Semua' ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?<?= $statusFilter ? "status=$statusFilter" : "" ?>">Semua</a></li>
                    <?php foreach ($jenisDonatur as $jenis): ?>
                        <li><a class="dropdown-item" href="?jenis=<?= $jenis['jenis_donatur'] ?><?= $statusFilter ? "&status=$statusFilter" : "" ?>"><?= $jenis['jenis_donatur'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Status: <?= $statusFilter ? ucfirst(str_replace('_', ' ', $statusFilter)) : 'Semua' ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?<?= $jenisFilter ? "jenis=$jenisFilter" : "" ?>">Semua</a></li>
                    <li><a class="dropdown-item" href="?status=aktif<?= $jenisFilter ? "&jenis=$jenisFilter" : "" ?>">Aktif</a></li>
                    <li><a class="dropdown-item" href="?status=tidak_aktif<?= $jenisFilter ? "&jenis=$jenisFilter" : "" ?>">Tidak Aktif</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Donatur</h5>
                    <h3 class="card-text"><?= $totalDonatur ?></h3>
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
            <h6 class="m-0 font-weight-bold text-primary">Data Donatur</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Jenis Donatur</th>
                            <th>Kontak</th>
                            <th>Tanggal Bergabung</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataDonatur as $index => $donatur): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (!empty($donatur['foto'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/uploads/donatur/<?= $donatur['foto'] ?>" alt="Foto <?= $donatur['nama'] ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/images/default-user.png" alt="Default" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?= $donatur['nama'] ?></td>
                                <td><?= $donatur['jenis_donatur'] ?></td>
                                <td>
                                    <?php if (!empty($donatur['no_telepon'])): ?>
                                        <i class="fas fa-phone-alt me-1"></i> <?= $donatur['no_telepon'] ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($donatur['email'])): ?>
                                        <i class="fas fa-envelope me-1"></i> <?= $donatur['email'] ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatDate($donatur['tanggal_bergabung']) ?></td>
                                <td>
                                    <?php if ($donatur['status'] == 'aktif'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $donatur['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $donatur['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                    <a href="<?= ADMIN_URL ?>/donasi.php?donatur_id=<?= $donatur['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-money-bill-wave"></i> Donasi
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Donatur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= $dataEdit ? $dataEdit['nama'] : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_donatur" class="form-label">Jenis Donatur</label>
                                <input type="text" class="form-control" id="jenis_donatur" name="jenis_donatur" value="<?= $dataEdit ? $dataEdit['jenis_donatur'] : '' ?>" list="jenisList" required>
                                <datalist id="jenisList">
                                    <?php foreach ($jenisDonatur as $jenis): ?>
                                        <option value="<?= $jenis['jenis_donatur'] ?>">
                                    <?php endforeach; ?>
                                    <option value="Tetap">
                                    <option value="Tidak Tetap">
                                    <option value="Perusahaan">
                                    <option value="Individu">
                                </datalist>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                                <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?= $dataEdit ? $dataEdit['tanggal_bergabung'] : date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="aktif" <?= ($dataEdit && $dataEdit['status'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                    <option value="tidak_aktif" <?= ($dataEdit && $dataEdit['status'] == 'tidak_aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_telepon" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?= $dataEdit ? $dataEdit['no_telepon'] : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $dataEdit ? $dataEdit['email'] : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <?php if ($dataEdit && !empty($dataEdit['foto'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= BASE_URL ?>/assets/uploads/donatur/<?= $dataEdit['foto'] ?>" alt="Foto <?= $dataEdit['nama'] ?>" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= $dataEdit ? $dataEdit['alamat'] : '' ?></textarea>
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
