<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// Hapus semua variabel sesi
$_SESSION = array();

// Hapus sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>
