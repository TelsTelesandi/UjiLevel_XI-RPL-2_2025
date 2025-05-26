<?php
session_start();
include 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $role = isset($_POST['role']) ? clean_input($_POST['role']) : '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($role && $user['role'] !== $role) {
            $error = 'Role tidak sesuai. Silakan pilih role yang benar.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            header("Location: " . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit();
        }
    } else {
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Event Ekstrakurikuler</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-bg">
    <div class="login-container<?php if ($error) echo ' shake'; ?>">
        <div style="text-align:center; margin-bottom:18px;">
            <img src="assets/asset/images/telkom.png" alt="Logo Login" style="max-width:120px; width:100%; height:auto;">
        </div>
        <h1>Login Sistem Event Ekskul</h1>
        <div id="login-spinner" class="login-spinner" style="display:none;">
            <div class="spinner"></div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form id="loginForm" action="login.php" method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Login Sebagai</label>
                <select id="role" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
    </div>
    <script>
    // Spinner saat submit
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        document.getElementById('login-spinner').style.display = 'flex';
    });
    // Hilangkan animasi shake setelah animasi selesai
    window.addEventListener('DOMContentLoaded', function() {
        var container = document.querySelector('.login-container.shake');
        if (container) {
            container.addEventListener('animationend', function() {
                container.classList.remove('shake');
            });
        }
    });
    </script>
</body>
</html>