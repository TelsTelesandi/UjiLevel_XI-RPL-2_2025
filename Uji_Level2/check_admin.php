<?php
require_once 'config/database.php';

// Check admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    echo "Admin ditemukan:\n";
    echo "Username: " . $admin['username'] . "\n";
    echo "Role: " . $admin['role'] . "\n";
    echo "Nama: " . $admin['nama_lengkap'] . "\n";
    
    // Test password verification
    $test_password = "123456";
    if (password_verify($test_password, $admin['password'])) {
        echo "\nPassword '123456' cocok dengan hash di database";
    } else {
        echo "\nPassword '123456' TIDAK cocok dengan hash di database";
        echo "\nHash di database: " . $admin['password'];
    }
} else {
    echo "Admin tidak ditemukan di database!";
}
?> 