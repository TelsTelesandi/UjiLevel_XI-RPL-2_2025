<?php

$servername = "localhost";
$username = "root";
$password = ""; // Ganti dengan password Anda jika ada
$dbname = "re_dimas";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Setel charset ke utf8mb4 (disarankan)
$conn->set_charset("utf8mb4");

// echo "Koneksi berhasil"; // Baris ini bisa dihapus setelah dipastikan koneksi berhasil

?> 