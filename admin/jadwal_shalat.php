<?php
/**
 * Halaman kelola jadwal shalat
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$subuh = isset($_POST['subuh']) ? $_POST['subuh'] : '';
$dzuhur = isset($_POST['dzuhur']) ? $_POST['dzuhur'] : '';
$ashar = isset($_POST['ashar']) ? $_POST['ashar'] : '';
$maghrib = isset($_POST['maghrib']) ? $_POST['maghrib'] : '';
$isya = isset($_POST['isya']) ? $_POST['isya'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($tanggal) || empty($subuh) || empty($dzuhur) || empty($ashar) || empty($maghrib) || empty($isya)) {
        setAlert('Semua field harus diisi', 'danger');
    } else {
        // Escape string
        $tanggal = escapeString($tanggal);
        $subuh = escapeString($subuh);
        $dzuhur = escapeString($dzuhur);
        $ashar = escapeString($ashar);
        $maghrib = escapeString($maghrib);
        $isya = escapeString($isya);
        
        // Cek apakah tanggal sudah ada
        $existingData = fetchOne("SELECT * FROM jadwal_shalat WHERE tanggal = '$tanggal'" . ($id ? " AND id != $id" : ""));
        
        if ($existingData) {
            setAlert('Jadwal shalat untuk tanggal tersebut sudah ada', 'danger');
        } else {
            if (isset($_POST['update'])) {
                // Update data
                $query = "UPDATE jadwal_shalat SET tanggal = '$tanggal', subuh = '$subuh', dzuhur = '$dzuhur', ashar = '$ashar', maghrib = '$maghrib', isya = '$isya' WHERE id = $id";
                
                if (execute($query)) {
                    setAlert('Data jadwal shalat berhasil diupdate', 'success');
                    redirect(ADMIN_URL . '/jadwal_shalat.php');
                } else {
                    setAlert('Gagal update data jadwal shalat', 'danger');
                }
            } else {
                // Insert data
                $query = "INSERT INTO jadwal_shalat (tanggal, subuh, dzuhur, ashar, maghrib, isya) VALUES ('$tanggal', '$subuh', '$dzuhur', '$ashar', '$maghrib', '$isya')";
                
                if (execute($query)) {
                    setAlert('Data jadwal shalat berhasil ditambahkan', 'success');
                    redirect(ADMIN_URL . '/jadwal_shalat.php');
                } else {
                    setAlert('Gagal menambahkan data jadwal shalat', 'danger');
                }
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Hapus data
    if (execute("DELETE FROM jadwal_shalat WHERE id = $id")) {
        setAlert('Data jadwal shalat berhasil dihapus', 'success');
    } else {
        setAlert('Gagal menghapus data jadwal shalat', 'danger');
    }
    
    redirect(ADMIN_URL . '/jadwal_shalat.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM jadwal_shalat WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data jadwal shalat tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/jadwal_shalat.php');
    }
}

// Ambil semua data jadwal shalat
$dataJadwal = fetchAll("SELECT * FROM jadwal_shalat ORDER BY tanggal DESC");

// Fungsi untuk generate jadwal shalat otomatis
function generateJadwalShalat($bulan, $tahun) {
    // Hitung jumlah hari dalam bulan
    $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
    // Loop untuk setiap hari dalam bulan
    for ($hari = 1; $hari <= $jumlahHari; $hari++) {
        $tanggal = sprintf("%04d-%02d-%02d", $tahun, $bulan, $hari);
        
        // Cek apakah tanggal sudah ada
        $existingData = fetchOne("SELECT * FROM jadwal_shalat WHERE tanggal = '$tanggal'");
        
        if (!$existingData) {
            // Generate waktu shalat (contoh sederhana, dalam implementasi nyata bisa menggunakan API atau perhitungan astronomi)
            // Untuk contoh ini, kita gunakan waktu default dengan sedikit variasi
            $baseSubuh = strtotime("04:30:00");
            $baseDzuhur = strtotime("12:00:00");
            $baseAshar = strtotime("15:30:00");
            $baseMaghrib = strtotime("18:00:00");
            $baseIsya = strtotime("19:15:00");
            
            // Tambahkan variasi berdasarkan tanggal
            $subuh = date("H:i:s", $baseSubuh + (($hari - 1) * 60)); // Tambah 0-30 menit
            $dzuhur = date("H:i:s", $baseDzuhur);
            $ashar = date("H:i:s", $baseAshar);
            $maghrib = date("H:i:s", $baseMaghrib + (($hari - 1) * 60)); // Tambah 0-30 menit
            $isya = date("H:i:s", $baseIsya + (($hari - 1) * 60)); // Tambah 0-30 menit
            
            // Insert data
            $query = "INSERT INTO jadwal_shalat (tanggal, subuh, dzuhur, ashar, maghrib, isya) VALUES ('$tanggal', '$subuh', '$dzuhur', '$ashar', '$maghrib', '$isya')";
            execute($query);
        }
    }
    
    return $jumlahHari;
}

// Proses generate jadwal shalat
if (isset($_POST['generate'])) {
    $bulan = isset($_POST['bulan']) ? (int)$_POST['bulan'] : date('n');
    $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y');
    
    $jumlahHari = generateJadwalShalat($bulan, $tahun);
    
    setAlert("Berhasil generate jadwal shalat untuk $jumlahHari hari pada bulan " . date("F Y", mktime(0, 0, 0, $bulan, 1, $tahun)), 'success');
    redirect(ADMIN_URL . '/jadwal_shalat.php');
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Jadwal Shalat</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="fas fa-calendar-plus"></i> Generate Jadwal
            </button>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Jadwal Shalat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Subuh</th>
                            <th>Dzuhur</th>
                            <th>Ashar</th>
                            <th>Maghrib</th>
                            <th>Isya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataJadwal as $index => $jadwal): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= formatDate($jadwal['tanggal']) ?></td>
                                <td><?= formatTime($jadwal['subuh']) ?></td>
                                <td><?= formatTime($jadwal['dzuhur']) ?></td>
                                <td><?= formatTime($jadwal['ashar']) ?></td>
                                <td><?= formatTime($jadwal['maghrib']) ?></td>
                                <td><?= formatTime($jadwal['isya']) ?></td>
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Jadwal Shalat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $dataEdit ? $dataEdit['tanggal'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="subuh" class="form-label">Waktu Subuh</label>
                        <input type="time" class="form-control" id="subuh" name="subuh" value="<?= $dataEdit ? substr($dataEdit['subuh'], 0, 5) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="dzuhur" class="form-label">Waktu Dzuhur</label>
                        <input type="time" class="form-control" id="dzuhur" name="dzuhur" value="<?= $dataEdit ? substr($dataEdit['dzuhur'], 0, 5) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="ashar" class="form-label">Waktu Ashar</label>
                        <input type="time" class="form-control" id="ashar" name="ashar" value="<?= $dataEdit ? substr($dataEdit['ashar'], 0, 5) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="maghrib" class="form-label">Waktu Maghrib</label>
                        <input type="time" class="form-control" id="maghrib" name="maghrib" value="<?= $dataEdit ? substr($dataEdit['maghrib'], 0, 5) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="isya" class="form-label">Waktu Isya</label>
                        <input type="time" class="form-control" id="isya" name="isya" value="<?= $dataEdit ? substr($dataEdit['isya'], 0, 5) : '' ?>" required>
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

<!-- Modal Generate -->
<div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateModalLabel">Generate Jadwal Shalat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Fitur ini akan menghasilkan jadwal shalat untuk satu bulan penuh. Jadwal yang sudah ada tidak akan diubah.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select class="form-select" id="bulan" name="bulan" required>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select class="form-select" id="tahun" name="tahun" required>
                                    <?php for ($i = date('Y') - 1; $i <= date('Y') + 5; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="generate" value="true" class="btn btn-success">Generate</button>
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
