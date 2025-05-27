<?php
include 'config.php';

// Drop existing tables
$conn->query("DROP TABLE IF EXISTS event_pengajuan");
$conn->query("DROP TABLE IF EXISTS users");

echo "Tables dropped successfully<br>";

// Create users table
$sql_users = "CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
) ENGINE=InnoDB";

if ($conn->query($sql_users) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create event_pengajuan table
$sql_event = "CREATE TABLE event_pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul_kegiatan VARCHAR(255) NOT NULL,
    event_ekskul VARCHAR(255) NOT NULL,
    tanggal_pengajuan DATE NOT NULL,
    total_biaya DECIMAL(15,2) NOT NULL,
    proposal VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Disetujui', 'Ditolak', 'Selesai') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB";

if ($conn->query($sql_event) === TRUE) {
    echo "Event pengajuan table created successfully<br>";
} else {
    echo "Error creating event pengajuan table: " . $conn->error . "<br>";
}

// Create admin user
$admin_username = "admin";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_role = "admin";

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_username, $admin_password, $admin_role);

if ($stmt->execute()) {
    echo "Admin user created successfully<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error creating admin user: " . $stmt->error . "<br>";
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
    echo "Uploads directory created successfully<br>";
}

$conn->close();
echo "Database reset completed!";
?> 