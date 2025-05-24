<?php
/**
 * Halaman kelola inventaris
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama_barang = isset($_POST['nama_barang']) ? $_POST['nama_barang'] : '';
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$tanggal_perolehan = isset($_POST['tanggal_perolehan']) ? $_POST['tanggal_perolehan'] : '';
$sumber_dana = isset($_POST['sumber_dana']) ? $_POST['sumber_dana'] : '';
$nilai_aset = isset($_POST['nilai_aset']) ? $_POST['nilai_aset'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama_barang) || empty($kategori) || empty($jumlah) || empty($status)) {
        setAlert('Nama barang, kategori, jumlah, dan status harus diisi', 'danger');
    } else {
        // Upload foto jika ada
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = uploadFile($_FILES['foto'], UPLOADS_PATH . '/inventaris');
            
            if (!$foto) {
                setAlert('Gagal upload foto. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/inventaris.php');
            }
        }
        
        // Escape string
        $nama_barang = escapeString($nama_barang);
        $kategori = escapeString($kategori);
        $jumlah = escapeString($jumlah);
        $status = escapeString($status);
        $tanggal_perolehan = escapeString($tanggal_perolehan);
        $sumber_dana = escapeString($sumber_dana);
        $nilai_aset = escapeString($nilai_aset);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM inventaris WHERE id = $id");
            
            if ($foto) {
                // Hapus foto lama jika ada
                if (!empty($oldData['foto'])) {
                    deleteFile(UPLOADS_PATH . '/inventaris/' . $oldData['foto']);
                }
                
                $query = "UPDATE inventaris SET nama_barang = '$nama_barang', kategori = '$kategori', jumlah = '$jumlah', status = '$status', tanggal_perolehan = " . ($tanggal_perolehan ? "'$tanggal_perolehan'" : "NULL") . ", sumber_dana = '$sumber_dana', nilai_aset = " . ($nilai_aset ? "'$nilai_aset'" : "NULL") . ", foto = '$foto', keterangan = '$keterangan' WHERE id = $id";
            } else {
                $query = "UPDATE inventaris SET nama_barang = '$nama_barang', kategori = '$kategori', jumlah = '$jumlah', status = '$status', tanggal_perolehan = " . ($tanggal_perolehan ? "'$tanggal_perolehan'" : "NULL") . ", sumber_dana = '$sumber_dana', nilai_aset = " . ($nilai_aset ? "'$nilai_aset'" : "NULL") . ", keterangan = '$keterangan' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data inventaris berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/inventaris.php');
            } else {
                setAlert('Gagal update data inventaris', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO inventaris (nama_barang, kategori, jumlah, status, tanggal_perolehan, sumber_dana, nilai_aset, foto, keterangan) VALUES ('$nama_barang', '$kategori', '$jumlah', '$status', " . ($tanggal_perolehan ? "'$tanggal_perolehan'" : "NULL") . ", '$sumber_dana', " . ($nilai_aset ? "'$nilai_aset'" : "NULL") . ", '$foto', '$keterangan')";
            
            if (execute($query)) {
                setAlert('Data inventaris berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/inventaris.php');
            } else {
                setAlert('Gagal menambahkan data inventaris', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM inventaris WHERE id = $id");
    
    if ($data) {
        // Hapus foto jika ada
        if (!empty($data['foto'])) {
            deleteFile(UPLOADS_PATH . '/inventaris/' . $data['foto']);
        }
        
        // Hapus data
        if (execute("DELETE FROM inventaris WHERE id = $id")) {
            setAlert('Data inventaris berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data inventaris', 'danger');
        }
    } else {
        setAlert('Data inventaris tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/inventaris.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM inventaris WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data inventaris tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/inventaris.php');
    }
}

// Ambil semua data inventaris
$dataInventaris = fetchAll("SELECT * FROM inventaris ORDER BY nama_barang ASC");

// Hitung total nilai aset
$totalNilaiAset = fetchOne("SELECT SUM(nilai_aset) as total FROM inventaris")['total'] ?? 0;

// Hitung jumlah per status
$jumlahBaik = fetchOne("SELECT SUM(jumlah) as total FROM inventaris WHERE status = 'baik'")['total'] ?? 0;
$jumlahRusak = fetchOne("SELECT SUM(jumlah) as total FROM inventaris WHERE status = 'rusak'")['total'] ?? 0;
$jumlahHilang = fetchOne("SELECT SUM(jumlah) as total FROM inventaris WHERE status = 'hilang'")['total'] ?? 0;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Inventaris</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
            <i class="fas fa-plus"></i> Tambah Inventaris
        </button>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Nilai Aset</h5>
                    <h3 class="card-text"><?= formatRupiah($totalNilaiAset) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Status Baik</h5>
                    <h3 class="card-text"><?= $jumlahBaik ?> Item</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Status Rusak</h5>
                    <h3 class="card-text"><?= $jumlahRusak ?> Item</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Status Hilang</h5>
                    <h3 class="card-text"><?= $jumlahHilang ?> Item</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Inventaris</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal Perolehan</th>
                            <th>Nilai Aset</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataInventaris as $index => $inventaris): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (!empty($inventaris['foto'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/uploads/inventaris/<?= $inventaris['foto'] ?>" alt="Foto <?= $inventaris['nama_barang'] ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/images/default-item.png" alt="Default" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?= $inventaris['nama_barang'] ?></td>
                                <td><?= $inventaris['kategori'] ?></td>
                                <td><?= $inventaris['jumlah'] ?></td>
                                <td>
                                    <?php if ($inventaris['status'] == 'baik'): ?>
                                        <span class="badge bg-success">Baik</span>
                                    <?php elseif ($inventaris['status'] == 'rusak'): ?>
                                        <span class="badge bg-warning">Rusak</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Hilang</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $inventaris['tanggal_perolehan'] ? formatDate($inventaris['tanggal_perolehan']) : '-' ?></td>
                                <td><?= $inventaris['nilai_aset'] ? formatRupiah($inventaris['nilai_aset']) : '-' ?></td>
                                <td>
                                    <a href="?action=edit&id=<?= $inventaris['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $inventaris['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= $dataEdit ? $dataEdit['nama_barang'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" value="<?= $dataEdit ? $dataEdit['kategori'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $dataEdit ? $dataEdit['jumlah'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="baik" <?= ($dataEdit && $dataEdit['status'] == 'baik') ? 'selected' : '' ?>>Baik</option>
                            <option value="rusak" <?= ($dataEdit && $dataEdit['status'] == 'rusak') ? 'selected' : '' ?>>Rusak</option>
                            <option value="hilang" <?= ($dataEdit && $dataEdit['status'] == 'hilang') ? 'selected' : '' ?>>Hilang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan</label>
                        <input type="date" class="form-control" id="tanggal_perolehan" name="tanggal_perolehan" value="<?= $dataEdit ? $dataEdit['tanggal_perolehan'] : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="sumber_dana" class="form-label">Sumber Dana</label>
                        <input type="text" class="form-control" id="sumber_dana" name="sumber_dana" value="<?= $dataEdit ? $dataEdit['sumber_dana'] : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="nilai_aset" class="form-label">Nilai Aset</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="nilai_aset" name="nilai_aset" value="<?= $dataEdit ? $dataEdit['nilai_aset'] : '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <?php if ($dataEdit && !empty($dataEdit['foto'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>/assets/uploads/inventaris/<?= $dataEdit['foto'] ?>" alt="Foto <?= $dataEdit['nama_barang'] ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="foto" name="foto">
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

<?php
// Memuat footer
require_once 'includes/footer.php';
?>
