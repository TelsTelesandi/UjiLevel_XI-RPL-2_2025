<?php
include_once 'init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only check for authentication if we're not on the login page
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php' && !isset($_SESSION['user_id'])) {
    header("Location: /Uji_level_Nabil/login.php");
    exit();
}

// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mengecek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Fungsi untuk redirect ke halaman login jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi untuk redirect ke halaman home jika bukan admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: home.php");
        exit();
    }
}

// Fungsi untuk mendapatkan username yang sedang login
function getCurrentUsername() {
    return $_SESSION['username'] ?? '';
}

// Fungsi untuk mendapatkan role yang sedang login
function getCurrentRole() {
    return $_SESSION['role'] ?? '';
}

// Fungsi untuk mendapatkan user_id yang sedang login
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}
?>
