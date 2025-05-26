<?php
require_once 'config/database.php';

// Get all users
$stmt = $pdo->query("SELECT username, nama_lengkap, role FROM users");
$users = $stmt->fetchAll();

echo "Daftar Users:\n";
echo "=============\n";
foreach ($users as $user) {
    echo "Username: " . $user['username'] . "\n";
    echo "Nama: " . $user['nama_lengkap'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "-------------\n";
}
?> 