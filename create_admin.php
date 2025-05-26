<?php
require_once 'db_connect.php';

// Hapus admin lama jika ada
$sql_delete = "DELETE FROM users WHERE username = 'admin'";
$conn->query($sql_delete);

// Buat password hash
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert admin baru
$sql_insert = "INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$username = 'admin';
$role = 'admin';
$nama_lengkap = 'Administrator';
$ekskul = 'Admin';

$stmt->bind_param("sssss", $username, $hashed_password, $role, $nama_lengkap, $ekskul);

if ($stmt->execute()) {
    echo "Admin berhasil dibuat!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?> 