<?php
session_start();
include '../config/db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    // Cek password (plain, jika belum hash)
    if ($password == $row['password']) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['nama_lengkap'] = $row['nama_lengkap'];

        // Redirect otomatis sesuai role
        if ($row['role'] == 'admin') {
            header("Location: ../views/dashboard_admin.php");
        } else {
            header("Location: ../views/dashboard_user.php");
        }
        exit;
    } else {
        // Password salah
        echo "<script>alert('Password salah!');window.location='index.php';</script>";
    }
} else {
    // Username tidak ditemukan
    echo "<script>alert('Username tidak ditemukan!');window.location='index.php';</script>";
}
?>
