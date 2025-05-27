<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug mode
define('DEBUG', true);

// Define base paths
$base_path = dirname(dirname(__FILE__)); // Gets parent directory path

// Get the current hostname from Laragon
$hostname = $_SERVER['HTTP_HOST'];
// Remove 'auth' subdomain if present and get the main domain
$main_domain = preg_replace('/^auth\./', '', $hostname);
define('BASE_URL', 'http://' . $main_domain);

// Include database connection
require_once '../config/db.php';

// Check database connection
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Debug database connection and table structure
$table_structure = mysqli_query($conn, "DESCRIBE users");
if ($table_structure) {
    error_log("=== Users Table Structure ===");
    while ($column = mysqli_fetch_assoc($table_structure)) {
        error_log(print_r($column, true));
    }
    error_log("===========================");
}

// Debug: Check existing users
$users_check = mysqli_query($conn, "SELECT user_id, username, role FROM users");
if ($users_check) {
    error_log("=== Existing Users ===");
    while ($user = mysqli_fetch_assoc($users_check)) {
        error_log(print_r($user, true));
    }
    error_log("====================");
}

// Debug database connection
echo "<!-- Database connected successfully -->";
echo "<!-- Base URL: " . BASE_URL . " -->";
echo "<!-- Base Path: " . $base_path . " -->";

// Check if users table exists and has data
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($check_table) == 0) {
    die("Tabel users tidak ditemukan!");
}

// Debug users table
$check_users = mysqli_query($conn, "SELECT * FROM users");
if ($check_users) {
    echo "<!-- Found " . mysqli_num_rows($check_users) . " users in database -->";
} else {
    die("Error checking users: " . mysqli_error($conn));
}

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard_admin.php");
        exit();
    } else {
        header("Location: ../user/dashboard_user.php");
        exit();
    }
}

$error = '';
$debug_info = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Using MD5 for password hashing
    
    // Debug information
    error_log("Login attempt - Username/Email: " . $username);
    
    // Cek apakah input adalah email atau username
    $query = mysqli_query($conn, "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND password = '$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        
        error_log("Login successful - User: " . $user['username'] . " - Role: " . $user['role']);
        
        // Redirect berdasarkan role
        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard_admin.php");
        } else {
            header("Location: ../user/dashboard_user.php");
        }
        exit();
    } else {
        error_log("User tidak ditemukan: " . $username);
        $_SESSION['error'] = "Username/Email atau password salah!";
    }
}

// Display any errors
if (isset($_SESSION['error'])) {
    echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Start output buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Ekskul</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin-top: 20px;
        }

        .login-container {
            width: 100%;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--secondary-color);
            opacity: 0.8;
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
            padding: 1rem;
            border: none;
            border-radius: 8px;
            background: var(--accent-color);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .alert {
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            padding: 1rem;
            border-radius: 8px;
            color: white;
            text-align: center;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideDown 0.5s ease-out;
        }

        .alert-error {
            background: var(--danger-color);
            border-left: 4px solid #c0392b;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-20px);
                opacity: 0;
            }
        }

        .alert.hide {
            animation: fadeOut 0.5s ease-out forwards;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-dark);
            opacity: 0.8;
        }

        .login-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Animated Background */
        .background-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            filter: blur(5px);
            opacity: 0.3;
            animation: floatShape 20s linear infinite;
        }

        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            background: var(--primary-color);
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            background: var(--secondary-color);
            top: 60%;
            right: 15%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            bottom: 20%;
            left: 20%;
            animation-delay: -10s;
        }

        @keyframes floatShape {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            100% {
                transform: translateY(-1000px) rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="background-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="login-wrapper">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error" id="errorAlert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
            <script>
                // Auto hide error message after 5 seconds
                setTimeout(function() {
                    var alert = document.getElementById('errorAlert');
                    alert.classList.add('hide');
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            </script>
        <?php endif; ?>

        <div class="login-container">
            <div class="login-header">
                <h1>Login</h1>
                <p>Silakan login untuk melanjutkan</p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username atau Email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            
            <div class="login-footer">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>

            <?php if (DEBUG): ?>
            <div style="margin-top: 20px; font-size: 12px; color: #fff; opacity: 0.7;">
                <p>Debug Info:</p>
                <p>Default Admin Account:<br>
                Username: admin<br>
                Password: admin123</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>
