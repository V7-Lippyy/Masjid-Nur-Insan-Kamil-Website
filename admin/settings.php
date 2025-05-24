<?php
// session_start(); // Removed: Assuming session is started in config.php or header.php

// Include database connection
require_once '../config/database.php';
require_once '../config/config.php'; // Assuming config might have base URL or other settings
require_once '../includes/functions.php'; // Assuming functions might be needed

// Check if user is logged in and is a super admin
// Note: Based on login.php, session data is stored in $_SESSION['admin'] array
if (!isset($_SESSION['admin']['id']) || !isset($_SESSION['admin']['role']) || $_SESSION['admin']['role'] !== 'super_admin') {
    // Redirect to login page or show an error message
    // For now, let's just deny access
    // In a real app, redirect: header('Location: login.php'); exit;
    die('Akses ditolak. Anda harus menjadi Super Admin untuk mengakses halaman ini.');
}

// Get the database connection
$conn = getConnection();

// --- CRUD Logic will go here ---
$action = $_GET['action'] ?? '';
$user_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle Delete Action
if ($action === 'delete' && $user_id) {
    // Prevent deleting the currently logged-in super admin (or the first super admin)
    // Add more robust checks if needed
    if ($user_id == $_SESSION["admin"]["id"] || $user_id == 1) { // Assuming ID 1 is the main super admin
        $error = 'Tidak dapat menghapus akun super admin utama atau akun Anda sendiri.';
    } else {
        $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = 'Akun berhasil dihapus.';
        } else {
            $error = 'Gagal menghapus akun: ' . $stmt->error;
        }
        $stmt->close();
    }
    // Redirect to avoid re-deleting on refresh
    header("Location: settings.php?message=" . urlencode($message) . "&error=" . urlencode($error));
    exit;
}

// Handle Form Submissions (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_action = $_POST['form_action'] ?? '';
    $edit_id = $_POST['edit_id'] ?? null;
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Plain text as requested
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $no_telepon = trim($_POST['no_telepon']);
    $role = $_POST['role'];

    // Basic Validation
    if (empty($username) || empty($role) || empty($nama_lengkap) || empty($email)) {
        $error = 'Username, Nama Lengkap, Email, dan Role wajib diisi.';
    } elseif (!in_array($role, ['admin', 'super_admin'])) {
        $error = 'Role tidak valid.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    }

    if (!$error) {
        if ($form_action === 'add') {
            // Add New User
            if (empty($password)) {
                $error = 'Password wajib diisi untuk akun baru.';
            } else {
                // Check if username or email already exists
                $check_stmt = $conn->prepare("SELECT id FROM admin WHERE username = ? OR email = ?");
                $check_stmt->bind_param("ss", $username, $email);
                $check_stmt->execute();
                $check_stmt->store_result();
                if ($check_stmt->num_rows > 0) {
                    $error = 'Username atau Email sudah digunakan.';
                } else {
                    // Hash the password before storing
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO admin (username, password, nama_lengkap, email, no_telepon, role) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $username, $hashed_password, $nama_lengkap, $email, $no_telepon, $role);
                    if ($stmt->execute()) {
                        $message = 'Akun baru berhasil ditambahkan.';
                    } else {
                        $error = 'Gagal menambahkan akun: ' . $stmt->error;
                    }
                    $stmt->close();
                }
                $check_stmt->close();
            }
        } elseif ($form_action === 'edit' && $edit_id) {
            // Edit Existing User
            // Check if username or email already exists (excluding the current user being edited)
            $check_stmt = $conn->prepare("SELECT id FROM admin WHERE (username = ? OR email = ?) AND id != ?");
            $check_stmt->bind_param("ssi", $username, $email, $edit_id);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $error = 'Username atau Email sudah digunakan oleh akun lain.';
            } else {
                if (!empty($password)) {
                    // Update with new hashed password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE admin SET username = ?, password = ?, nama_lengkap = ?, email = ?, no_telepon = ?, role = ? WHERE id = ?");
                    $stmt->bind_param("ssssssi", $username, $hashed_password, $nama_lengkap, $email, $no_telepon, $role, $edit_id);
                } else {
                    // Update without changing password
                    $stmt = $conn->prepare("UPDATE admin SET username = ?, nama_lengkap = ?, email = ?, no_telepon = ?, role = ? WHERE id = ?");
                    $stmt->bind_param("sssssi", $username, $nama_lengkap, $email, $no_telepon, $role, $edit_id);
                }

                if ($stmt->execute()) {
                    $message = 'Akun berhasil diperbarui.';
                } else {
                    $error = 'Gagal memperbarui akun: ' . $stmt->error;
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
        // Redirect after successful add/edit to clear POST data
        if (!$error) {
             header("Location: settings.php?message=" . urlencode($message));
             exit;
        }
    }
    // If error occurred during POST, retain form data for display
    $form_data = $_POST;
}

// Fetch user data for editing if action is 'edit'
$edit_user = null;
if ($action === 'edit' && $user_id) {
    $stmt = $conn->prepare("SELECT id, username, nama_lengkap, email, no_telepon, role FROM admin WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_user = $result->fetch_assoc();
    } else {
        $error = 'User tidak ditemukan.';
    }
    $stmt->close();
}

// Fetch all admin users for display
$users = [];
$result = $conn->query("SELECT id, username, nama_lengkap, email, no_telepon, role FROM admin ORDER BY role, username");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
} else {
    $error = "Gagal mengambil data user: " . $conn->error;
}

// Close connection
$conn->close();

// --- HTML Structure Starts Here ---
$page_title = "Pengaturan Akun";
include 'includes/header.php'; // Include admin header
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $page_title; ?></h1>

    <?php 
    // Display messages/errors from redirects or form submissions
    $display_message = $_GET['message'] ?? $message;
    $display_error = $_GET['error'] ?? $error;
    if ($display_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($display_message); ?></div>
    <?php endif; ?>
    <?php if ($display_error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($display_error); ?></div>
    <?php endif; ?>

    <?php // --- Add/Edit Form --- ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo ($edit_user) ? 'Edit Akun' : 'Tambah Akun Baru'; ?></h6>
        </div>
        <div class="card-body">
            <form action="settings.php" method="POST">
                <input type="hidden" name="form_action" value="<?php echo ($edit_user) ? 'edit' : 'add'; ?>">
                <?php if ($edit_user): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? $edit_user['username'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Password <?php echo ($edit_user) ? '(Kosongkan jika tidak ingin ganti)' : '<span class="text-danger">*</span>'; ?></label>
                        <input type="password" class="form-control" id="password" name="password" <?php echo (!$edit_user) ? 'required' : ''; ?>>
                    </div>
                </div>
                <div class="form-row">
                     <div class="form-group col-md-6">
                        <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($form_data['nama_lengkap'] ?? $edit_user['nama_lengkap'] ?? ''); ?>" required>
                    </div>
                     <div class="form-group col-md-6">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? $edit_user['email'] ?? ''); ?>" required>
                    </div>
                </div>
                 <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="no_telepon">No Telepon</label>
                        <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($form_data['no_telepon'] ?? $edit_user['no_telepon'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="role">Role <span class="text-danger">*</span></label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin" <?php echo (($form_data['role'] ?? $edit_user['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="super_admin" <?php echo (($form_data['role'] ?? $edit_user['role'] ?? '') === 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo ($edit_user) ? 'Update Akun' : 'Tambah Akun'; ?></button>
                <?php if ($edit_user): ?>
                    <a href="settings.php" class="btn btn-secondary">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php // --- User List Table --- ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akun Admin & Super Admin</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No Telepon</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data akun.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['no_telepon'] ?? '-'); ?></td>
                                    <td><?php echo ($user['role'] === 'super_admin') ? 'Super Admin' : 'Admin'; ?></td>
                                    <td>
                                        <a href="settings.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <?php // Add condition to prevent deleting self or main super admin ?>
                                        <?php if ($user["id"] != $_SESSION["admin"]["id"] && $user["id"] != 1): ?>
                                            <a href="settings.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php
include 'includes/footer.php'; // Include admin footer
?>
