<?php
$host = 'localhost';
$dbname = 're_dilan';  // Pastikan nama ini sesuai
$username = 'root';     // Sesuaikan dengan username MySQL Anda
$password = '';         // Sesuaikan password MySQL Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>