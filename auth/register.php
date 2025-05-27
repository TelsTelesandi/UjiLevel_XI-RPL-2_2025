<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($nama_lengkap) || empty($email)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek apakah username sudah ada
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username atau email sudah digunakan!";
        } else {
            // Hash password
            $hashed_password = md5($password);

            // Insert user baru
            $insert_query = "INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, 'user')";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $hashed_password, $nama_lengkap, $email);

            if (mysqli_stmt_execute($insert_stmt)) {
                $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Aplikasi Ekskul</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            background: var(--accent-color);
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            color: white;
        }

        .alert-success {
            background: var(--success-color);
        }

        .alert-error {
            background: var(--danger-color);
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 2rem;">Register</h1>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <button type="submit" class="btn">Register</button>

            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </form>
    </div>
</body>
</html> 