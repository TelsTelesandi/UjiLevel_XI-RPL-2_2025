<?php
include 'config.php';
include 'session.php';
if ($_SESSION['role'] != 'user') die("Akses ditolak");

$stmt = $conn->prepare("INSERT INTO event (nama_event, tanggal_event, penyelenggara, deskripsi)
                        VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss",
    $_POST['nama_event'],
    $_POST['tanggal_event'],
    $_POST['penyelenggara'],
    $_POST['deskripsi']
);
$stmt->execute();

echo "Event berhasil diajukan. <a href='index.php'>Kembali</a>";
