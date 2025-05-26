<?php
$koneksi = new mysqli("localhost", "root", "", "uji_level2"); // Ganti nama_database sesuai database Anda
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>