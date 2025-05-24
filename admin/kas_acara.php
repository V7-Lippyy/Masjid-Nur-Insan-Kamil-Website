<?php
/**
 * Halaman kelola kas acara
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$kegiatan_id = isset($_GET['kegiatan_id']) ? $_GET['kegiatan_id'] : (isset($_POST['kegiatan_id']) ? $_POST['kegiatan_id'] : '');
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Ambil data kegiatan jika ada kegiatan_id
$dataKegiatan = null;
if (!empty($kegiatan_id)) {
    $dataKegiatan = fetchOne("SELECT * FROM kegiatan WHERE id = $kegiatan_id");
    
    if (!$dataKegiatan) {
        setAlert('Data kegiatan tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/kegiatan.php');
    }
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($kegiatan_id) || empty($jenis) || empty($jumlah) || empty($tanggal)) {
        setAlert('Kegiatan, jenis, jumlah, dan tanggal harus diisi', 'danger');
    } else {
        // Upload bukti jika ada
        $bukti = '';
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $bukti = uploadFile($_FILES['bukti'], UPLOADS_PATH . '/kas_acara');
            
            if (!$bukti) {
                setAlert('Gagal upload bukti. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/kas_acara.php' . ($kegiatan_id ? "?kegiatan_id=$kegiatan_id" : ''));
            }
        }
        
        // Escape string
        $kegiatan_id = escapeString($kegiatan_id);
        $jenis = escapeString($jenis);
        $jumlah = escapeString($jumlah);
        $tanggal = escapeString($tanggal);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM kas_acara WHERE id = $id");
            
            if ($bukti) {
                // Hapus bukti lama jika ada
                if (!empty($oldData['bukti'])) {
                    deleteFile(UPLOADS_PATH . '/kas_acara/' . $oldData['bukti']);
                }
                
                $query = "UPDATE kas_acara SET kegiatan_id = '$kegiatan_id', jenis = '$jenis', jumlah = '$jumlah', tanggal = '$tanggal', keterangan = '$keterangan', bukti = '$bukti' WHERE id = $id";
            } else {
                $query = "UPDATE kas_acara SET kegiatan_id = '$kegiatan_id', jenis = '$jenis', jumlah = '$jumlah', tanggal = '$tanggal', keterangan = '$keterangan' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data kas acara berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/kas_acara.php' . ($kegiatan_id ? "?kegiatan_id=$kegiatan_id" : ''));
            } else {
                setAlert('Gagal update data kas acara', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO kas_acara (kegiatan_id, jenis, jumlah, tanggal, keterangan, bukti) VALUES ('$kegiatan_id', '$jenis', '$jumlah', '$tanggal', '$keterangan', '$bukti')";
            
            if (execute($query)) {
                setAlert('Data kas acara berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/kas_acara.php' . ($kegiatan_id ? "?kegiatan_id=$kegiatan_id" : ''));
            } else {
                setAlert('Gagal menambahkan data kas acara', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM kas_acara WHERE id = $id");
    
    if ($data) {
        // Hapus bukti jika ada
        if (!empty($data['bukti'])) {
            deleteFile(UPLOADS_PATH . '/kas_acara/' . $data['bukti']);
        }
        
        // Hapus data
        if (execute("DELETE FROM kas_acara WHERE id = $id")) {
            setAlert('Data kas acara berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data kas acara', 'danger');
        }
    } else {
        setAlert('Data kas acara tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/kas_acara.php' . ($kegiatan_id ? "?kegiatan_id=$kegiatan_id" : ''));
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM kas_acara WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data kas acara tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/kas_acara.php' . ($kegiatan_id ? "?kegiatan_id=$kegiatan_id" : ''));
    }
    
    // Set kegiatan_id dari data edit
    $kegiatan_id = $dataEdit['kegiatan_id'];
    
    // Ambil data kegiatan
    $dataKegiatan = fetchOne("SELECT * FROM kegiatan WHERE id = $kegiatan_id");
}

// Ambil semua data kegiatan untuk dropdown
$dataKegiatanAll = fetchAll("SELECT * FROM kegiatan ORDER BY tanggal_mulai DESC");

// Ambil semua data kas acara
$whereClause = $kegiatan_id ? "WHERE ka.kegiatan_id = $kegiatan_id" : "";
$dataKasAcara = fetchAll("SELECT ka.*, k.nama_kegiatan FROM kas_acara ka JOIN kegiatan k ON ka.kegiatan_id = k.id $whereClause ORDER BY ka.tanggal DESC");

// Hitung total pemasukan, pengeluaran, dan saldo
$whereClauseSum = $kegiatan_id ? "WHERE kegiatan_id = $kegiatan_id" : "";
$totalPemasukan = fetchOne("SELECT SUM(jumlah) as total FROM kas_acara WHERE jenis = 'pemasukan' $whereClauseSum")['total'] ?? 0;
$totalPengeluaran = fetchOne("SELECT SUM(jumlah) as total FROM kas_acara WHERE jenis = 'pengeluaran' $whereClauseSum")['total'] ?? 0;
$saldo = $totalPemasukan - $totalPengeluaran;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            Kelola Kas Acara
            <?php if ($dataKegiatan): ?>
                <span class="text-muted">: <?= $dataKegiatan['nama_kegiatan'] ?></span>
            <?php endif; ?>
        </h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Kas
            </button>
            <?php if ($kegiatan_id): ?>
                <a href="<?= ADMIN_URL ?>/kas_acara.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Lihat Semua
                </a>
            <?php endif; ?>
        </div>
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
            <h6 class="m-0 font-weight-bold text-primary">
                Data Kas Acara
                <?php if ($dataKegiatan): ?>
                    : <?= $dataKegiatan['nama_kegiatan'] ?>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataKasAcara as $index => $kas): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $kas['nama_kegiatan'] ?></td>
                                <td><?= formatDate($kas['tanggal']) ?></td>
                                <td>
                                    <?php if ($kas['jenis'] == 'pemasukan'): ?>
                                        <span class="badge bg-success">Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatRupiah($kas['jumlah']) ?></td>
                                <td><?= $kas['keterangan'] ?></td>
                                <td>
                                    <?php if (!empty($kas['bukti'])): ?>
                                        <a href="<?= BASE_URL ?>/assets/uploads/kas_acara/<?= $kas['bukti'] ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $kas['id'] ?><?= $kegiatan_id ? "&kegiatan_id=$kegiatan_id" : '' ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $kas['id'] ?><?= $kegiatan_id ? "&kegiatan_id=$kegiatan_id" : '' ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Kas Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kegiatan_id" class="form-label">Kegiatan</label>
                        <select class="form-select" id="kegiatan_id" name="kegiatan_id" required <?= $kegiatan_id ? 'disabled' : '' ?>>
                            <option value="">Pilih Kegiatan</option>
                            <?php foreach ($dataKegiatanAll as $kegiatan): ?>
                                <option value="<?= $kegiatan['id'] ?>" <?= ($kegiatan_id == $kegiatan['id']) ? 'selected' : '' ?>>
                                    <?= $kegiatan['nama_kegiatan'] ?> (<?= formatDate($kegiatan['tanggal_mulai'], 'd M Y') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($kegiatan_id): ?>
                            <input type="hidden" name="kegiatan_id" value="<?= $kegiatan_id ?>">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select class="form-select" id="jenis" name="jenis" required>
                            <option value="">Pilih Jenis</option>
                            <option value="pemasukan" <?= ($dataEdit && $dataEdit['jenis'] == 'pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                            <option value="pengeluaran" <?= ($dataEdit && $dataEdit['jenis'] == 'pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
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
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= $dataEdit ? $dataEdit['keterangan'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="bukti" class="form-label">Bukti</label>
                        <?php if ($dataEdit && !empty($dataEdit['bukti'])): ?>
                            <div class="mb-2">
                                <a href="<?= BASE_URL ?>/assets/uploads/kas_acara/<?= $dataEdit['bukti'] ?>" target="_blank">Lihat Bukti</a>
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
