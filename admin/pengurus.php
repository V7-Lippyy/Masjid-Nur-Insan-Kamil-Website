<?php
/**
 * Halaman kelola pengurus
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$jabatan = isset($_POST['jabatan']) ? $_POST['jabatan'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'aktif';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama) || empty($jabatan)) {
        setAlert('Nama dan jabatan harus diisi', 'danger');
    } else {
        // Upload foto jika ada
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = uploadFile($_FILES['foto'], UPLOADS_PATH . '/pengurus');
            
            if (!$foto) {
                setAlert('Gagal upload foto. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/pengurus.php');
            }
        }
        
        // Escape string
        $nama = escapeString($nama);
        $jabatan = escapeString($jabatan);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $deskripsi = escapeString($deskripsi);
        $status = escapeString($status);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM pengurus WHERE id = $id");
            
            if ($foto) {
                // Hapus foto lama jika ada
                if (!empty($oldData['foto'])) {
                    deleteFile(UPLOADS_PATH . '/pengurus/' . $oldData['foto']);
                }
                
                $query = "UPDATE pengurus SET nama = '$nama', jabatan = '$jabatan', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', foto = '$foto', deskripsi = '$deskripsi', status = '$status' WHERE id = $id";
            } else {
                $query = "UPDATE pengurus SET nama = '$nama', jabatan = '$jabatan', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', deskripsi = '$deskripsi', status = '$status' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data pengurus berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/pengurus.php');
            } else {
                setAlert('Gagal update data pengurus', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO pengurus (nama, jabatan, alamat, no_telepon, email, foto, deskripsi, status) VALUES ('$nama', '$jabatan', '$alamat', '$no_telepon', '$email', '$foto', '$deskripsi', '$status')";
            
            if (execute($query)) {
                setAlert('Data pengurus berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/pengurus.php');
            } else {
                setAlert('Gagal menambahkan data pengurus', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM pengurus WHERE id = $id");
    
    if ($data) {
        // Hapus foto jika ada
        if (!empty($data['foto'])) {
            deleteFile(UPLOADS_PATH . '/pengurus/' . $data['foto']);
        }
        
        // Hapus data
        if (execute("DELETE FROM pengurus WHERE id = $id")) {
            setAlert('Data pengurus berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data pengurus', 'danger');
        }
    } else {
        setAlert('Data pengurus tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/pengurus.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM pengurus WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data pengurus tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/pengurus.php');
    }
}

// Ambil semua data pengurus
$dataPengurus = fetchAll("SELECT * FROM pengurus ORDER BY nama ASC");
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Pengurus</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
            <i class="fas fa-plus"></i> Tambah Pengurus
        </button>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengurus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>No. Telepon</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataPengurus as $index => $pengurus): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (!empty($pengurus['foto'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/uploads/pengurus/<?= $pengurus['foto'] ?>" alt="Foto <?= $pengurus['nama'] ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/images/default-user.png" alt="Default" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?= $pengurus['nama'] ?></td>
                                <td><?= $pengurus['jabatan'] ?></td>
                                <td><?= $pengurus['no_telepon'] ?></td>
                                <td><?= $pengurus['email'] ?></td>
                                <td>
                                    <?php if ($pengurus['status'] == 'aktif'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $pengurus['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $pengurus['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Pengurus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= $dataEdit ? $dataEdit['nama'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?= $dataEdit ? $dataEdit['jabatan'] : '' ?>" required>
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
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= $dataEdit ? $dataEdit['deskripsi'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <?php if ($dataEdit && !empty($dataEdit['foto'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>/assets/uploads/pengurus/<?= $dataEdit['foto'] ?>" alt="Foto <?= $dataEdit['nama'] ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="foto" name="foto">
                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="aktif" <?= ($dataEdit && $dataEdit['status'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                            <option value="tidak_aktif" <?= ($dataEdit && $dataEdit['status'] == 'tidak_aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
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
