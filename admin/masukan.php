<?php
/**
 * Halaman kelola masukan (kritik dan saran)
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$tanggapan = isset($_POST['tanggapan']) ? $_POST['tanggapan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($status)) {
        setAlert('Status harus diisi', 'danger');
    } else {
        // Escape string
        $status = escapeString($status);
        $tanggapan = escapeString($tanggapan);
        
        // Update data
        $query = "UPDATE masukan SET status = '$status', tanggapan = '$tanggapan' WHERE id = $id";
        
        if (execute($query)) {
            setAlert('Data masukan berhasil diupdate', 'success');
            redirect(ADMIN_URL . '/masukan.php');
        } else {
            setAlert('Gagal update data masukan', 'danger');
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Hapus data
    if (execute("DELETE FROM masukan WHERE id = $id")) {
        setAlert('Data masukan berhasil dihapus', 'success');
    } else {
        setAlert('Gagal menghapus data masukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/masukan.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM masukan WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data masukan tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/masukan.php');
    }
}

// Filter status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil semua data masukan
$whereClause = $statusFilter ? "WHERE status = '$statusFilter'" : "";
$dataMasukan = fetchAll("SELECT * FROM masukan $whereClause ORDER BY created_at DESC");

// Hitung jumlah masukan per status
$totalBaru = fetchOne("SELECT COUNT(*) as total FROM masukan WHERE status = 'baru'")['total'] ?? 0;
$totalDibaca = fetchOne("SELECT COUNT(*) as total FROM masukan WHERE status = 'dibaca'")['total'] ?? 0;
$totalDiproses = fetchOne("SELECT COUNT(*) as total FROM masukan WHERE status = 'diproses'")['total'] ?? 0;
$totalSelesai = fetchOne("SELECT COUNT(*) as total FROM masukan WHERE status = 'selesai'")['total'] ?? 0;
$totalMasukan = $totalBaru + $totalDibaca + $totalDiproses + $totalSelesai;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Masukan</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Filter Status: <?= $statusFilter ? ucfirst($statusFilter) : 'Semua' ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?">Semua</a></li>
                <li><a class="dropdown-item" href="?status=baru">Baru</a></li>
                <li><a class="dropdown-item" href="?status=dibaca">Dibaca</a></li>
                <li><a class="dropdown-item" href="?status=diproses">Diproses</a></li>
                <li><a class="dropdown-item" href="?status=selesai">Selesai</a></li>
            </ul>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Masukan</h5>
                    <h3 class="card-text"><?= $totalMasukan ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Baru</h5>
                    <h3 class="card-text"><?= $totalBaru ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Dibaca/Diproses</h5>
                    <h3 class="card-text"><?= $totalDibaca + $totalDiproses ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Selesai</h5>
                    <h3 class="card-text"><?= $totalSelesai ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Masukan <?= $statusFilter ? "(" . ucfirst($statusFilter) . ")" : "" ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Jenis</th>
                            <th>Isi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataMasukan as $index => $masukan): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $masukan['nama'] ?></td>
                                <td><?= $masukan['email'] ?></td>
                                <td><?= $masukan['no_telepon'] ?></td>
                                <td>
                                    <?php if ($masukan['jenis'] == 'kritik'): ?>
                                        <span class="badge bg-danger">Kritik</span>
                                    <?php elseif ($masukan['jenis'] == 'saran'): ?>
                                        <span class="badge bg-info">Saran</span>
                                    <?php elseif ($masukan['jenis'] == 'pertanyaan'): ?>
                                        <span class="badge bg-warning">Pertanyaan</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Lainnya</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= substr($masukan['isi'], 0, 50) . (strlen($masukan['isi']) > 50 ? '...' : '') ?></td>
                                <td><?= formatDate($masukan['created_at']) ?></td>
                                <td>
                                    <?php if ($masukan['status'] == 'baru'): ?>
                                        <span class="badge bg-danger">Baru</span>
                                    <?php elseif ($masukan['status'] == 'dibaca'): ?>
                                        <span class="badge bg-warning">Dibaca</span>
                                    <?php elseif ($masukan['status'] == 'diproses'): ?>
                                        <span class="badge bg-info">Diproses</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $masukan['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Tanggapi
                                    </a>
                                    <a href="?action=delete&id=<?= $masukan['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel">Tanggapi Masukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <p class="form-control-static"><?= $dataEdit ? $dataEdit['nama'] : '' ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <p class="form-control-static">
                                <?php if ($dataEdit): ?>
                                    <?php if ($dataEdit['jenis'] == 'kritik'): ?>
                                        <span class="badge bg-danger">Kritik</span>
                                    <?php elseif ($dataEdit['jenis'] == 'saran'): ?>
                                        <span class="badge bg-info">Saran</span>
                                    <?php elseif ($dataEdit['jenis'] == 'pertanyaan'): ?>
                                        <span class="badge bg-warning">Pertanyaan</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Lainnya</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <p class="form-control-static"><?= $dataEdit ? $dataEdit['email'] : '' ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <p class="form-control-static"><?= $dataEdit ? $dataEdit['no_telepon'] : '' ?></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Masukan</label>
                        <div class="p-3 bg-light rounded">
                            <?= $dataEdit ? nl2br($dataEdit['isi']) : '' ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="baru" <?= ($dataEdit && $dataEdit['status'] == 'baru') ? 'selected' : '' ?>>Baru</option>
                            <option value="dibaca" <?= ($dataEdit && $dataEdit['status'] == 'dibaca') ? 'selected' : '' ?>>Dibaca</option>
                            <option value="diproses" <?= ($dataEdit && $dataEdit['status'] == 'diproses') ? 'selected' : '' ?>>Diproses</option>
                            <option value="selesai" <?= ($dataEdit && $dataEdit['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggapan" class="form-label">Tanggapan</label>
                        <textarea class="form-control" id="tanggapan" name="tanggapan" rows="5"><?= $dataEdit ? $dataEdit['tanggapan'] : '' ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Tanggapan</button>
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
