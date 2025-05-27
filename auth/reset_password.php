<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';

$error = '';
$success = '';

if (!isset($_GET['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_GET['token'];

// Verify token and check expiry
$query = "SELECT user_id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$user = mysqli_fetch_assoc($result)) {
    $error = "Link reset password tidak valid atau sudah kadaluarsa.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password harus minimal 6 karakter!";
    } else {
        // Update password and clear reset token
        $hashed_password = md5($password); // Using MD5 to match existing password hashing
        $update_query = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $token);
        
        if (mysqli_stmt_execute($update_stmt)) {
            // Get user details for session
            $user_query = "SELECT user_id, username, role, nama_lengkap FROM users WHERE reset_token = ?";
            $user_stmt = mysqli_prepare($conn, $user_query);
            mysqli_stmt_bind_param($user_stmt, "s", $token);
            mysqli_stmt_execute($user_stmt);
            $user_result = mysqli_stmt_get_result($user_stmt);
            
            if ($user_data = mysqli_fetch_assoc($user_result)) {
                // Set session variables
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['role'] = $user_data['role'];
                $_SESSION['nama_lengkap'] = $user_data['nama_lengkap'];
                
                // Redirect based on role
                if ($user_data['role'] === 'admin') {
                    header("Location: /aplikasi_ekskul/admin/dashboard_admin.php");
                } else {
                    header("Location: /aplikasi_ekskul/user/dashboard_user.php");
                }
                exit();
            } else {
                $success = "Password berhasil direset. Silakan login dengan password baru Anda.";
            }
            mysqli_stmt_close($user_stmt);
        } else {
            $error = "Gagal mereset password. Silakan coba lagi.";
        }
        mysqli_stmt_close($update_stmt);
    }
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Aplikasi Ekskul</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <div class="back-link">
                <a href="login.php">Kembali ke Login</a>
            </div>
        <?php elseif (!$error): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="password">Password Baru:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value.trim();
        const confirmPassword = document.getElementById('confirm_password').value.trim();
        
        if (!password || !confirmPassword) {
            e.preventDefault();
            alert('Semua field harus diisi!');
            return false;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password tidak cocok!');
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password harus minimal 6 karakter!');
            return false;
        }
    });
    </script>
</body>
</html> 