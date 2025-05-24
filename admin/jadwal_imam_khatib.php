<?php
/**
 * Halaman kelola jadwal imam dan khatib
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$waktu_shalat = isset($_POST['waktu_shalat']) ? $_POST['waktu_shalat'] : '';
$nama_imam = isset($_POST['nama_imam']) ? $_POST['nama_imam'] : '';
$nama_khatib = isset($_POST['nama_khatib']) ? $_POST['nama_khatib'] : '';
$tema_khutbah = isset($_POST['tema_khutbah']) ? $_POST['tema_khutbah'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($tanggal) || empty($waktu_shalat) || empty($nama_imam)) {
        setAlert('Tanggal, waktu shalat, dan nama imam harus diisi', 'danger');
    } else {
        // Escape string
        $tanggal = escapeString($tanggal);
        $waktu_shalat = escapeString($waktu_shalat);
        $nama_imam = escapeString($nama_imam);
        $nama_khatib = escapeString($nama_khatib);
        $tema_khutbah = escapeString($tema_khutbah);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $query = "UPDATE jadwal_imam_khatib SET tanggal = '$tanggal', waktu_shalat = '$waktu_shalat', nama_imam = '$nama_imam', nama_khatib = '$nama_khatib', tema_khutbah = '$tema_khutbah', keterangan = '$keterangan' WHERE id = $id";
            
            if (execute($query)) {
                setAlert('Data jadwal imam dan khatib berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/jadwal_imam_khatib.php');
            } else {
                setAlert('Gagal update data jadwal imam dan khatib', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO jadwal_imam_khatib (tanggal, waktu_shalat, nama_imam, nama_khatib, tema_khutbah, keterangan) VALUES ('$tanggal', '$waktu_shalat', '$nama_imam', '$nama_khatib', '$tema_khutbah', '$keterangan')";
            
            if (execute($query)) {
                setAlert('Data jadwal imam dan khatib berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/jadwal_imam_khatib.php');
            } else {
                setAlert('Gagal menambahkan data jadwal imam dan khatib', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Hapus data
    if (execute("DELETE FROM jadwal_imam_khatib WHERE id = $id")) {
        setAlert('Data jadwal imam dan khatib berhasil dihapus', 'success');
    } else {
        setAlert('Gagal menghapus data jadwal imam dan khatib', 'danger');
    }
    
    redirect(ADMIN_URL . '/jadwal_imam_khatib.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM jadwal_imam_khatib WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data jadwal imam dan khatib tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/jadwal_imam_khatib.php');
    }
}

// Filter waktu shalat
$waktuFilter = isset($_GET['waktu']) ? $_GET['waktu'] : '';

// Ambil semua data jadwal imam dan khatib
$whereClause = $waktuFilter ? "WHERE waktu_shalat = '$waktuFilter'" : "";
$dataJadwal = fetchAll("SELECT * FROM jadwal_imam_khatib $whereClause ORDER BY tanggal DESC, FIELD(waktu_shalat, 'subuh', 'dzuhur', 'ashar', 'maghrib', 'isya', 'jumat')");

// Hitung jumlah jadwal per waktu shalat
$totalSubuh = fetchOne("SELECT COUNT(*) as total FROM jadwal_imam_khatib WHERE waktu_shalat = 'subuh'")['total'] ?? 0;
$totalDzuhur = fetchOne("SELECT COUNT(*) as total FROM jadwal_imam_khatib WHERE waktu_shalat = 'dzuhur'")['total'] ?? 0;
$totalAshar = fetchOne("SELECT COUNT(*) as total FROM jadwal_imam_khatib WHERE waktu_shalat = 'ashar'")['total'] ?? 0;
$totalMaghrib = fetchOne("SELECT COUNT(*) as total FROM jadwal_imam_khatib WHERE waktu_shalat = 'maghrib'")['total'] ?? 0;
$totalIsya = fetchOne("SELECT COUNT(*) as total FROM jadwal_imam_khatib WHERE waktu_shalat = 'isya'")['total'] ?? 0;
$totalJumat = fetchOne("SELECT COUNT(*) as total FROM jadwal_imam_khatib WHERE waktu_shalat = 'jumat'")['total'] ?? 0;
$totalJadwal = $totalSubuh + $totalDzuhur + $totalAshar + $totalMaghrib + $totalIsya + $totalJumat;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Jadwal Imam dan Khatib</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Waktu: <?= $waktuFilter ? ucfirst($waktuFilter) : 'Semua' ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?">Semua</a></li>
                    <li><a class="dropdown-item" href="?waktu=subuh">Subuh</a></li>
                    <li><a class="dropdown-item" href="?waktu=dzuhur">Dzuhur</a></li>
                    <li><a class="dropdown-item" href="?waktu=ashar">Ashar</a></li>
                    <li><a class="dropdown-item" href="?waktu=maghrib">Maghrib</a></li>
                    <li><a class="dropdown-item" href="?waktu=isya">Isya</a></li>
                    <li><a class="dropdown-item" href="?waktu=jumat">Jumat</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total</h5>
                    <h3 class="card-text"><?= $totalJadwal ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Subuh</h5>
                    <h3 class="card-text"><?= $totalSubuh ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Dzuhur</h5>
                    <h3 class="card-text"><?= $totalDzuhur ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Ashar</h5>
                    <h3 class="card-text"><?= $totalAshar ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Maghrib</h5>
                    <h3 class="card-text"><?= $totalMaghrib ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Isya</h5>
                    <h3 class="card-text"><?= $totalIsya ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Jadwal Imam dan Khatib <?= $waktuFilter ? "(" . ucfirst($waktuFilter) . ")" : "" ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Waktu Shalat</th>
                            <th>Imam</th>
                            <th>Khatib</th>
                            <th>Tema Khutbah</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataJadwal as $index => $jadwal): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= formatDate($jadwal['tanggal']) ?></td>
                                <td>
                                    <?php if ($jadwal['waktu_shalat'] == 'subuh'): ?>
                                        <span class="badge bg-info">Subuh</span>
                                    <?php elseif ($jadwal['waktu_shalat'] == 'dzuhur'): ?>
                                        <span class="badge bg-success">Dzuhur</span>
                                    <?php elseif ($jadwal['waktu_shalat'] == 'ashar'): ?>
                                        <span class="badge bg-warning">Ashar</span>
                                    <?php elseif ($jadwal['waktu_shalat'] == 'maghrib'): ?>
                                        <span class="badge bg-danger">Maghrib</span>
                                    <?php elseif ($jadwal['waktu_shalat'] == 'isya'): ?>
                                        <span class="badge bg-secondary">Isya</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Jumat</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $jadwal['nama_imam'] ?></td>
                                <td><?= $jadwal['nama_khatib'] ? $jadwal['nama_khatib'] : '-' ?></td>
                                <td><?= $jadwal['tema_khutbah'] ? $jadwal['tema_khutbah'] : '-' ?></td>
                                <td><?= $jadwal['keterangan'] ? $jadwal['keterangan'] : '-' ?></td>
                                <td>
                                    <a href="?action=edit&id=<?= $jadwal['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $jadwal['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Jadwal Imam dan Khatib</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $dataEdit ? $dataEdit['tanggal'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="waktu_shalat" class="form-label">Waktu Shalat</label>
                        <select class="form-select" id="waktu_shalat" name="waktu_shalat" required>
                            <option value="">Pilih Waktu Shalat</option>
                            <option value="subuh" <?= ($dataEdit && $dataEdit['waktu_shalat'] == 'subuh') ? 'selected' : '' ?>>Subuh</option>
                            <option value="dzuhur" <?= ($dataEdit && $dataEdit['waktu_shalat'] == 'dzuhur') ? 'selected' : '' ?>>Dzuhur</option>
                            <option value="ashar" <?= ($dataEdit && $dataEdit['waktu_shalat'] == 'ashar') ? 'selected' : '' ?>>Ashar</option>
                            <option value="maghrib" <?= ($dataEdit && $dataEdit['waktu_shalat'] == 'maghrib') ? 'selected' : '' ?>>Maghrib</option>
                            <option value="isya" <?= ($dataEdit && $dataEdit['waktu_shalat'] == 'isya') ? 'selected' : '' ?>>Isya</option>
                            <option value="jumat" <?= ($dataEdit && $dataEdit['waktu_shalat'] == 'jumat') ? 'selected' : '' ?>>Jumat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nama_imam" class="form-label">Nama Imam</label>
                        <input type="text" class="form-control" id="nama_imam" name="nama_imam" value="<?= $dataEdit ? $dataEdit['nama_imam'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_khatib" class="form-label">Nama Khatib</label>
                        <input type="text" class="form-control" id="nama_khatib" name="nama_khatib" value="<?= $dataEdit ? $dataEdit['nama_khatib'] : '' ?>">
                        <small class="text-muted">Kosongkan jika tidak ada khatib (khusus untuk shalat selain Jumat)</small>
                    </div>
                    <div class="mb-3">
                        <label for="tema_khutbah" class="form-label">Tema Khutbah</label>
                        <input type="text" class="form-control" id="tema_khutbah" name="tema_khutbah" value="<?= $dataEdit ? $dataEdit['tema_khutbah'] : '' ?>">
                        <small class="text-muted">Kosongkan jika tidak ada tema khutbah</small>
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
