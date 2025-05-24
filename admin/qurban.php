<?php
/**
 * Halaman kelola qurban
 */

// Memuat file header
require_once 'includes/header.php';

// Inisialisasi variabel
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama_pengqurban = isset($_POST['nama_pengqurban']) ? $_POST['nama_pengqurban'] : '';
$jenis_hewan = isset($_POST['jenis_hewan']) ? $_POST['jenis_hewan'] : '';
$jumlah_hewan = isset($_POST['jumlah_hewan']) ? $_POST['jumlah_hewan'] : 1;
$atas_nama = isset($_POST['atas_nama']) ? $_POST['atas_nama'] : '';
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : '';
$tanggal_daftar = isset($_POST['tanggal_daftar']) ? $_POST['tanggal_daftar'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
$no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'pending';
$jumlah_bayar = isset($_POST['jumlah_bayar']) ? $_POST['jumlah_bayar'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (empty($nama_pengqurban) || empty($jenis_hewan) || empty($tahun) || empty($tanggal_daftar) || empty($jumlah_bayar)) {
        setAlert('Nama pengqurban, jenis hewan, tahun, tanggal daftar, dan jumlah bayar harus diisi', 'danger');
    } else {
        // Upload bukti pembayaran jika ada
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $bukti_pembayaran = uploadFile($_FILES['bukti_pembayaran'], UPLOADS_PATH . '/qurban');
            
            if (!$bukti_pembayaran) {
                setAlert('Gagal upload bukti pembayaran. Pastikan file adalah gambar dan ukuran maksimal 5MB', 'danger');
                redirect(ADMIN_URL . '/qurban.php');
            }
        }
        
        // Escape string
        $nama_pengqurban = escapeString($nama_pengqurban);
        $jenis_hewan = escapeString($jenis_hewan);
        $jumlah_hewan = escapeString($jumlah_hewan);
        $atas_nama = escapeString($atas_nama);
        $tahun = escapeString($tahun);
        $tanggal_daftar = escapeString($tanggal_daftar);
        $alamat = escapeString($alamat);
        $no_telepon = escapeString($no_telepon);
        $email = escapeString($email);
        $status = escapeString($status);
        $jumlah_bayar = escapeString($jumlah_bayar);
        $keterangan = escapeString($keterangan);
        
        if (isset($_POST['update'])) {
            // Update data
            $oldData = fetchOne("SELECT * FROM qurban WHERE id = $id");
            
            if ($bukti_pembayaran) {
                // Hapus bukti lama jika ada
                if (!empty($oldData['bukti_pembayaran'])) {
                    deleteFile(UPLOADS_PATH . '/qurban/' . $oldData['bukti_pembayaran']);
                }
                
                $query = "UPDATE qurban SET nama_lengkap = '$nama_pengqurban', jenis_hewan = '$jenis_hewan', jumlah_hewan = '$jumlah_hewan', atas_nama = '$atas_nama', tahun = '$tahun', tanggal_daftar = '$tanggal_daftar', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', status = '$status', bukti_pembayaran = '$bukti_pembayaran', total_bayar = '$jumlah_bayar', keterangan = '$keterangan' WHERE id = $id";
            } else {
                $query = "UPDATE qurban SET nama_lengkap = '$nama_pengqurban', jenis_hewan = '$jenis_hewan', jumlah_hewan = '$jumlah_hewan', atas_nama = '$atas_nama', tahun = '$tahun', tanggal_daftar = '$tanggal_daftar', alamat = '$alamat', no_telepon = '$no_telepon', email = '$email', status = '$status', total_bayar = '$jumlah_bayar', keterangan = '$keterangan' WHERE id = $id";
            }
            
            if (execute($query)) {
                setAlert('Data qurban berhasil diupdate', 'success');
                redirect(ADMIN_URL . '/qurban.php');
            } else {
                setAlert('Gagal update data qurban', 'danger');
            }
        } else {
            // Insert data
            $query = "INSERT INTO qurban (nama_lengkap, jenis_hewan, jumlah_hewan, atas_nama, tahun, tanggal_daftar, alamat, no_telepon, email, status, bukti_pembayaran, total_bayar, keterangan) VALUES ('$nama_pengqurban', '$jenis_hewan', '$jumlah_hewan', '$atas_nama', '$tahun', '$tanggal_daftar', '$alamat', '$no_telepon', '$email', '$status', '$bukti_pembayaran', '$jumlah_bayar', '$keterangan')";
            
            if (execute($query)) {
                setAlert('Data qurban berhasil ditambahkan', 'success');
                redirect(ADMIN_URL . '/qurban.php');
            } else {
                setAlert('Gagal menambahkan data qurban', 'danger');
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($id)) {
    // Ambil data yang akan dihapus
    $data = fetchOne("SELECT * FROM qurban WHERE id = $id");
    
    if ($data) {
        // Hapus bukti pembayaran jika ada
        if (!empty($data['bukti_pembayaran'])) {
            deleteFile(UPLOADS_PATH . '/qurban/' . $data['bukti_pembayaran']);
        }
        
        // Hapus data
        if (execute("DELETE FROM qurban WHERE id = $id")) {
            setAlert('Data qurban berhasil dihapus', 'success');
        } else {
            setAlert('Gagal menghapus data qurban', 'danger');
        }
    } else {
        setAlert('Data qurban tidak ditemukan', 'danger');
    }
    
    redirect(ADMIN_URL . '/qurban.php');
}

// Ambil data untuk edit
$dataEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($id)) {
    $dataEdit = fetchOne("SELECT * FROM qurban WHERE id = $id");
    
    if (!$dataEdit) {
        setAlert('Data qurban tidak ditemukan', 'danger');
        redirect(ADMIN_URL . '/qurban.php');
    }
}

// Filter tahun
$tahunFilter = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Ambil semua tahun yang ada
$tahunList = fetchAll("SELECT DISTINCT tahun FROM qurban ORDER BY tahun DESC");

// Ambil semua data qurban
$whereClause = $tahunFilter ? "WHERE tahun = '$tahunFilter'" : "";
$dataQurban = fetchAll("SELECT * FROM qurban $whereClause ORDER BY tanggal_daftar DESC");

// Hitung total qurban per jenis
$totalSapi = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'sapi' $whereClause")['total'] ?? 0;
$totalKambing = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'kambing' $whereClause")['total'] ?? 0;
$totalDomba = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'domba' $whereClause")['total'] ?? 0;
$totalLainnya = fetchOne("SELECT SUM(jumlah_hewan) as total FROM qurban WHERE jenis_hewan = 'lainnya' $whereClause")['total'] ?? 0;
$totalHewan = $totalSapi + $totalKambing + $totalDomba + $totalLainnya;

// Hitung total pembayaran
$totalPembayaran = fetchOne("SELECT SUM(total_bayar) as total FROM qurban $whereClause")['total'] ?? 0;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kelola Qurban</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#formModal">
                <i class="fas fa-plus"></i> Tambah Qurban
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter Tahun: <?= $tahunFilter ? $tahunFilter : 'Semua' ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?">Semua</a></li>
                    <?php foreach ($tahunList as $tahunItem): ?>
                        <li><a class="dropdown-item" href="?tahun=<?= $tahunItem['tahun'] ?>"><?= $tahunItem['tahun'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Hewan</h5>
                    <h3 class="card-text"><?= $totalHewan ?> Ekor</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Sapi</h5>
                    <h3 class="card-text"><?= $totalSapi ?> Ekor</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Kambing & Domba</h5>
                    <h3 class="card-text"><?= $totalKambing + $totalDomba ?> Ekor</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pembayaran</h5>
                    <h3 class="card-text"><?= formatRupiah($totalPembayaran) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Qurban <?= $tahunFilter ? "Tahun $tahunFilter" : "" ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengqurban</th>
                            <th>Atas Nama</th>
                            <th>Jenis Hewan</th>
                            <th>Jumlah</th>
                            <th>Tahun</th>
                            <th>Jumlah Bayar</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataQurban as $index => $qurban): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $qurban['nama_lengkap'] ?></td>
                                <td><?= $qurban['atas_nama'] ? $qurban['atas_nama'] : $qurban['nama_lengkap'] ?></td>
                                <td>
                                    <?php if ($qurban['jenis_hewan'] == 'sapi'): ?>
                                        <span class="badge bg-success">Sapi</span>
                                    <?php elseif ($qurban['jenis_hewan'] == 'kambing'): ?>
                                        <span class="badge bg-info">Kambing</span>
                                    <?php elseif ($qurban['jenis_hewan'] == 'domba'): ?>
                                        <span class="badge bg-primary">Domba</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Lainnya</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $qurban['jumlah_hewan'] ?> ekor</td>
                                <td><?= $qurban['tahun'] ?> H</td>
                                <td><?= formatRupiah($qurban['total_bayar']) ?></td>
                                <td>
                                    <?php if ($qurban['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($qurban['status'] == 'approved'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($qurban['bukti_pembayaran'])): ?>
                                        <a href="<?= BASE_URL ?>/assets/uploads/qurban/<?= $qurban['bukti_pembayaran'] ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $qurban['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?= $qurban['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                <h5 class="modal-title" id="formModalLabel"><?= $dataEdit ? 'Edit' : 'Tambah' ?> Qurban</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_pengqurban" class="form-label">Nama Pengqurban</label>
                        <input type="text" class="form-control" id="nama_pengqurban" name="nama_pengqurban" value="<?= $dataEdit ? $dataEdit['nama_lengkap'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="atas_nama" class="form-label">Atas Nama (Opsional)</label>
                        <input type="text" class="form-control" id="atas_nama" name="atas_nama" value="<?= $dataEdit ? $dataEdit['atas_nama'] : '' ?>">
                        <small class="text-muted">Kosongkan jika sama dengan nama pengqurban</small>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_hewan" class="form-label">Jenis Hewan</label>
                        <select class="form-select" id="jenis_hewan" name="jenis_hewan" required>
                            <option value="">Pilih Jenis Hewan</option>
                            <option value="sapi" <?= ($dataEdit && $dataEdit['jenis_hewan'] == 'sapi') ? 'selected' : '' ?>>Sapi</option>
                            <option value="kambing" <?= ($dataEdit && $dataEdit['jenis_hewan'] == 'kambing') ? 'selected' : '' ?>>Kambing</option>
                            <option value="domba" <?= ($dataEdit && $dataEdit['jenis_hewan'] == 'domba') ? 'selected' : '' ?>>Domba</option>
                            <option value="lainnya" <?= ($dataEdit && $dataEdit['jenis_hewan'] == 'lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_hewan" class="form-label">Jumlah Hewan</label>
                        <input type="number" class="form-control" id="jumlah_hewan" name="jumlah_hewan" value="<?= $dataEdit ? $dataEdit['jumlah_hewan'] : '1' ?>" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun Hijriah</label>
                        <input type="text" class="form-control" id="tahun" name="tahun" value="<?= $dataEdit ? $dataEdit['tahun'] : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_daftar" class="form-label">Tanggal Daftar</label>
                        <input type="date" class="form-control" id="tanggal_daftar" name="tanggal_daftar" value="<?= $dataEdit ? $dataEdit['tanggal_daftar'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= $dataEdit ? $dataEdit['alamat'] : '' ?></textarea>
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
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?= ($dataEdit && $dataEdit['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= ($dataEdit && $dataEdit['status'] == 'approved') ? 'selected' : '' ?>>Lunas</option>
                            <option value="selesai" <?= ($dataEdit && $dataEdit['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="jumlah_bayar" name="jumlah_bayar" value="<?= $dataEdit ? $dataEdit['total_bayar'] : '' ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                        <?php if ($dataEdit && !empty($dataEdit['bukti_pembayaran'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>/assets/uploads/qurban/<?= $dataEdit['bukti_pembayaran'] ?>" alt="Bukti Pembayaran" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
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
