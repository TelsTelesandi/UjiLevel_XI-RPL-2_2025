<?php
session_start();
require_once '../../../config/database.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'doLogin':
        // Handle login logic here
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    
                    header("Location: ../../../../index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Username atau password salah!";
                    header("Location: login.php");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Terjadi kesalahan saat login.";
                header("Location: login.php");
                exit();
            }
        }
        break;

    case 'doRegister':
        // Handle registration logic
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
            $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
            $ekskul = filter_input(INPUT_POST, 'ekskul', FILTER_SANITIZE_STRING);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi input
            if (empty($username) || empty($nama_lengkap) || empty($ekskul) || empty($password) || empty($confirm_password)) {
                $_SESSION['error'] = "Semua field harus diisi!";
                header("Location: register.php");
                exit();
            }

            // Validasi email
            if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Format email tidak valid!";
                header("Location: register.php");
                exit();
            }

            // Validasi password match
            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Password dan konfirmasi password tidak cocok!";
                header("Location: register.php");
                exit();
            }

            try {
                // Cek apakah username sudah ada
                $check_stmt = $db->prepare("SELECT username FROM users WHERE username = ?");
                $check_stmt->execute([$username]);
                
                if ($check_stmt->rowCount() > 0) {
                    $_SESSION['error'] = "Username sudah terdaftar!";
                    header("Location: register.php");
                    exit();
                }

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user baru
                $insert_stmt = $db->prepare("INSERT INTO users (username, password, nama_lengkap, Ekskul, role) VALUES (?, ?, ?, ?, 'user')");
                $insert_stmt->execute([$username, $hashed_password, $nama_lengkap, $ekskul]);

                $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
                header("Location: login.php");
                exit();

            } catch (PDOException $e) {
                $_SESSION['error'] = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
                header("Location: register.php");
                exit();
            }
        }
        break;

    case 'logout':
        session_destroy();
        header("Location: login.php");
        exit();

    default:
        header("Location: login.php");
        exit();
}
?> 