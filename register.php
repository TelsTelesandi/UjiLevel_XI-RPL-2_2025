<?php
session_start();
include 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);
    $role = isset($_POST['role']) ? clean_input($_POST['role']) : 'user';

    // Validasi
    if ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } else {
        // Cek username atau email sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Username atau email sudah terdaftar!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru dengan role
            $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $nama_lengkap, $email, $role])) {
                // Login otomatis setelah register
                $user_id = $conn->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['role'] = $role;
                if ($role === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: user/dashboard.php");
                }
                exit();
            } else {
                $error = 'Terjadi kesalahan saat mendaftar. Silahkan coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Event Ekstrakurikuler</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="register-bg">
    <div class="register-container">
        <div style="text-align:center; margin-bottom:18px;">
            <img src="assets/asset/images/telkom.png" alt="Logo Register" style="max-width:120px; width:100%; height:auto;">
        </div>
        <h1>Daftar Akun Baru</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post" autocomplete="off">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="role">Daftar Sebagai</label>
                <select id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
    </div>
</body>
</html>