<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Only save redirect URL if it's not the verification page
    if (strpos($_SERVER['PHP_SELF'], 'verifikasi_event.php') === false) {
        $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
    }
    
    // Set error message
    $_SESSION['error'] = "Anda harus login sebagai admin untuk mengakses halaman ini.";
    
    // Redirect to login page
    header("Location: ../login.php");
    exit();
}
?> 