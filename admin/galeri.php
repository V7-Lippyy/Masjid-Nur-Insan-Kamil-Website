<?php
/**
 * Halaman kelola galeri
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$judul = isset($_POST['judul']) ? $_POST['judul'] : '';
$deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($judul) || empty($tanggal)) {
        setAlert('Judul dan tanggal harus diisi', 'danger');
    } else {
        // Upload gambar jika ada
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $gambar = uploadFile($_FILES['gambar'], UPLOADS_PATH . '/galeri');
            
            if (!$gambar) {
                setAlert('Gagal upload gambar. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/galeri.php');
            }
        }
        
        // Escape string
        $judul = escapeString($judul);
        $deskripsi = escapeString($deskripsi);
        $kategori = escapeString($kategori);
        $tanggal = escapeString($tanggal);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM galeri WHERE id = $id");
            
            if ($gambar) {
                // Hapus gambar lama jika ada
                if (!empty($oldData['gambar'])) {
                    deleteFile(UPLOADS_PATH . '/galeri/' . $oldData['gambar']);
                }
                
                $query = "UPDATE galeri SET judul = '$judul', deskripsi = '$deskripsi', kategori = '$kategori', tanggal = '$tanggal', gambar = '$gambar' WHERE id = $id";
            } else {
                $query = "UPDATE galeri SET judul = '$judul', deskripsi = '$deskripsi', kategori = '$kategori', tanggal = '$tanggal' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data galeri berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/galeri.php');
            } else {
                setAlert('Gagal update data galeri', 'danger');
            }
        } else {
            // Validasi gambar untuk data baru
            if (empty($gambar)) {
                setAlert('Gambar harus diupload', 'danger');
                redirect(ADMIN_URL . '/galeri.php');
            }
            
            // Insert data
            $query = "INSERT INTO galeri (judul, deskripsi, kategori, tanggal, gambar) VALUES ('$judul', '$deskripsi', '$kategori', '$tanggal', '$gambar')";
            
            if (execute($query)) {
                setAlert('Data galeri berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/galeri.php');
            } else {
                setAlert('Gagal menambahkan data galeri', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM galeri WHERE id = $id");
    
    if ($data) {
        // Hapus gambar jika ada
        if (!empty($data['gambar'])) {
            deleteFile(UPLOADS_PATH . '/galeri/' . $data['gambar']);
        }
        
        // Hapus data
        if (execute("DELETE FROM galeri WHERE id = $id")) {
            setAlert('Data galeri berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data galeri', 'danger');
        }
    } else {
        setAlert('Data galeri tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/galeri.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM galeri WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data galeri tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/galeri.php');
    }
}

// Filter kategori
$kategoriFilter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Ambil semua kategori yang ada
$kategoriList = fetchAll("SELECT DISTINCT kategori FROM galeri WHERE kategori != '' ORDER BY kategori ASC");

// Ambil semua data galeri
$whereClause = $kategoriFilter ? "WHERE kategori = '$kategoriFilter'" : "";
$dataGaleri = fetchAll("SELECT * FROM galeri $whereClause ORDER BY tanggal DESC");

// Hitung jumlah galeri
$totalGaleri = count($dataGaleri);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Galeri</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Galeri
            </button>
            <?php if (count($kategoriList) > 0): ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Kategori: <?= $kategoriFilter ? $kategoriFilter : 'Semua' ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?">Semua</a></li>
                        <?php foreach ($kategoriList as $kat): ?>
                            <li><a class="dropdown-item" href="?kategori=<?= $kat['kategori'] ?>"><?= $kat['kategori'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Galeri <?= $kategoriFilter ? "($kategoriFilter)" : "" ?></h6>
        </div>
        <div class="card-body">
            <?php if (count($dataGaleri) > 0): ?>
                <div class="row">
                    <?php foreach ($dataGaleri as $galeri): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <img src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $galeri['gambar'] ?>" class="card-img-top" alt="<?= $galeri['judul'] ?>" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $galeri['judul'] ?></h5>
                                    <p class="card-text small text-muted">
                                        <?= formatDate($galeri['tanggal']) ?>
                                        <?php if (!empty($galeri['kategori'])): ?>
                                            <span class="badge bg-info"><?= $galeri['kategori'] ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text"><?= substr($galeri['deskripsi'], 0, 100) . (strlen($galeri['deskripsi']) > 100 ? '...' : '') ?></p>
                                </div>
                                <div class="card-footer">
                                    <a href="?action=edit&id=<?= $galeri['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $galeri['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Belum ada data galeri<?= $kategoriFilter ? " untuk kategori $kategoriFilter" : "" ?>.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="<?= $dataEdit ? $dataEdit['judul'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" value="<?= $dataEdit ? $dataEdit['kategori'] : '' ?>" list="kategoriList">
                        <datalist id="kategoriList">
                            <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?= $kat['kategori'] ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $dataEdit ? $dataEdit['tanggal'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar</label>
                        <?php if ($dataEdit && !empty($dataEdit['gambar'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>/assets/uploads/galeri/<?= $dataEdit['gambar'] ?>" alt="<?= $dataEdit['judul'] ?>" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="gambar" name="gambar" <?= $dataEdit ? '' : 'required' ?>>
                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 5MB</small>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= $dataEdit ? $dataEdit['deskripsi'] : '' ?></textarea>
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
