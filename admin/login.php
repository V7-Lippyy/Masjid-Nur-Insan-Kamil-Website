<?php
/**
 * Halaman login admin
 */

// Memuat file konfigurasi dan fungsi
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Cek jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/index.php');
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Log input login untuk debugging
    error_log("LOGIN ATTEMPT - Username: " . $username);
    
    // Tambahkan script untuk logging di console
    echo "<script>console.log('LOGIN ATTEMPT - Username: " . $username . "');</script>";
    
    // Validasi input
    if (empty($username) || empty($password)) {
        error_log("LOGIN ERROR - Empty username or password");
        echo "<script>console.log('LOGIN ERROR - Empty username or password');</script>";
        setAlert('Username dan password harus diisi', 'danger');
    } else {
        // Cek koneksi database
        // Note: getConnection() is called within fetchOne, no need to explicitly check here unless for specific debug
        
        // Cek username
        // IMPORTANT: Use prepared statements to prevent SQL injection
        $conn = getConnection(); // Get connection for prepared statement
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        $conn->close(); // Close connection after use
        
        error_log("LOGIN DEBUG - Query executed for username: " . $username);
        echo "<script>console.log('LOGIN DEBUG - Query executed for username: " . $username . "');</script>";
        
        if ($admin) {
            error_log("LOGIN DEBUG - Admin found: " . json_encode($admin));
            echo "<script>console.log(\"LOGIN DEBUG - Admin found: " . json_encode($admin) . "\");</script>";
            
            // Verifikasi password menggunakan password_verify
            $password_verify_result = password_verify($password, $admin['password']);
            error_log("LOGIN DEBUG - Password verification result (using password_verify): " . ($password_verify_result ? "TRUE" : "FALSE"));
            echo "<script>console.log(\"LOGIN DEBUG - Password verification result (using password_verify): " . ($password_verify_result ? "TRUE" : "FALSE") . "\");</script>";
            
            if ($password_verify_result) {
                // Set session
                $_SESSION['admin'] = [
                    'id' => $admin['id'],
                    'username' => $admin['username'],
                    'nama_lengkap' => $admin['nama_lengkap'],
                    'role' => $admin['role']
                ];
                
                error_log("LOGIN SUCCESS - Session set: " . json_encode($_SESSION['admin']));
                echo "<script>console.log(\"LOGIN SUCCESS - Session set: " . json_encode($_SESSION['admin']) . "\");</script>";
                
                // Redirect ke dashboard
                redirect(ADMIN_URL . '/index.php');
            } else {
                error_log("LOGIN ERROR - Password verification failed for user: " . $username);
                echo "<script>console.log(\"LOGIN ERROR - Password verification failed for user: " . $username . "\");</script>";
                setAlert('Username atau password salah', 'danger');
            }
        } else {
            error_log("LOGIN ERROR - Username not found: " . $username);
            echo "<script>console.log(\"LOGIN ERROR - Username not found: " . $username . "\");</script>";
            setAlert('Username atau password salah', 'danger');
        }
    }
}

// Ambil pengaturan website
$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?= htmlspecialchars($settings['nama_masjid'] ?? 'Nama Masjid Default') ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-logo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="<?= !empty($settings['logo']) ? BASE_URL . '/assets/uploads/' . htmlspecialchars($settings['logo']) : BASE_URL . '/assets/images/logo.png' ?>" alt="Logo">
            <h4 class="mt-3"><?= htmlspecialchars($settings['nama_masjid'] ?? 'Nama Masjid Default') ?></h4>
            <p class="text-muted">Admin Panel</p>
        </div>
        
        <?= getAlert() ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success">Login</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>" class="text-decoration-none">Kembali ke Website</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

