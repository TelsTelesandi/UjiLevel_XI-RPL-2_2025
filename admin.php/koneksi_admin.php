<?php
// Konfigurasi Database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ujilevel_nyimas';

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set karakter encoding
$conn->set_charset("utf8");

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>
