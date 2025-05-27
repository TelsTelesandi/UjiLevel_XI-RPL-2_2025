<?php
include 'config.php';

// Drop existing tables in correct order (child tables first)
$conn->query("DROP TABLE IF EXISTS verifikasi_event");
$conn->query("DROP TABLE IF EXISTS event_pengajuan");
$conn->query("DROP TABLE IF EXISTS users");

// Create users table
$sql_users = "CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Tabel users berhasil dibuat<br>";
} else {
    echo "Error membuat tabel users: " . $conn->error . "<br>";
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
)";

if ($conn->query($sql_event) === TRUE) {
    echo "Tabel event_pengajuan berhasil dibuat<br>";
} else {
    echo "Error membuat tabel event_pengajuan: " . $conn->error . "<br>";
}

// Create verifikasi_event table
$sql_verifikasi = "CREATE TABLE verifikasi_event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    tanggal_verifikasi DATE NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event_pengajuan(id) ON DELETE CASCADE
)";

if ($conn->query($sql_verifikasi) === TRUE) {
    echo "Tabel verifikasi_event berhasil dibuat<br>";
} else {
    echo "Error membuat tabel verifikasi_event: " . $conn->error . "<br>";
}

// Create default admin user
$admin_username = "admin";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_role = "admin";

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_username, $admin_password, $admin_role);

if ($stmt->execute()) {
    echo "User admin berhasil dibuat<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error membuat user admin: " . $stmt->error . "<br>";
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
    echo "Direktori uploads berhasil dibuat<br>";
} 