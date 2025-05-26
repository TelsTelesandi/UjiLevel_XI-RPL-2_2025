<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ujilevel_nyimas"; // Pastikan database ini sudah ada di MySQL kamu

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>
