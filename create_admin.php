<?php
include 'config.php';

$admin_username = "admin";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_role = "admin";

// Hapus admin lama jika ada
$conn->query("DELETE FROM users WHERE username = 'admin'");

// Buat admin baru
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_username, $admin_password, $admin_role);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error creating admin: " . $stmt->error;
}

$conn->close(); 