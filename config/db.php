<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "re_fachri";

try {
    error_log("Attempting to connect to MySQL server...");
    
    // Try to connect to MySQL server first without selecting database
    $conn = mysqli_connect($host, $user, $pass);
    
    if (!$conn) {
        throw new Exception("Koneksi ke MySQL gagal: " . mysqli_connect_error());
    }
    
    error_log("Successfully connected to MySQL server");
    
    // Check if database exists, if not create it
    error_log("Checking if database '$db' exists...");
    $check_db = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'");
    
    if (mysqli_num_rows($check_db) == 0) {
        error_log("Database '$db' does not exist, creating...");
        if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db")) {
            throw new Exception("Gagal membuat database: " . mysqli_error($conn));
        }
        error_log("Database '$db' created successfully");
    } else {
        error_log("Database '$db' already exists");
    }
    
    // Select the database
    error_log("Selecting database '$db'...");
    if (!mysqli_select_db($conn, $db)) {
        throw new Exception("Gagal memilih database: " . mysqli_error($conn));
    }
    error_log("Database '$db' selected successfully");
    
    // Set charset to utf8mb4
    error_log("Setting charset to utf8mb4...");
    if (!mysqli_set_charset($conn, "utf8mb4")) {
        throw new Exception("Error setting charset: " . mysqli_error($conn));
    }
    error_log("Charset set successfully");
    
    // Set timezone
    error_log("Setting timezone...");
    mysqli_query($conn, "SET time_zone = '+07:00'");
    error_log("Timezone set successfully");
    
    // Check if users table exists
    $check_users_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($check_users_table) == 0) {
        // Create users table if it doesn't exist
        error_log("Creating users table...");
        $create_users_table = "CREATE TABLE users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            nama_lengkap VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (!mysqli_query($conn, $create_users_table)) {
            throw new Exception("Gagal membuat tabel users: " . mysqli_error($conn));
        }
        error_log("Users table created successfully");
        
        // Insert default admin user
        error_log("Creating default admin user...");
        $admin_password = md5('admin123');
        $insert_admin = "INSERT INTO users (username, password, nama_lengkap, email, role) 
                        VALUES ('admin', '$admin_password', 'Administrator', 'admin@example.com', 'admin')";
        
        if (!mysqli_query($conn, $insert_admin)) {
            error_log("Error creating admin user: " . mysqli_error($conn));
        } else {
            error_log("Default admin user created successfully");
        }
    } else {
        error_log("Users table already exists");
    }
    
    // Check if event_pengajuan table exists
    $check_events_table = mysqli_query($conn, "SHOW TABLES LIKE 'event_pengajuan'");
    if (mysqli_num_rows($check_events_table) == 0) {
        // Create event_pengajuan table if it doesn't exist
        error_log("Creating event_pengajuan table...");
        $create_events_table = "CREATE TABLE event_pengajuan (
            event_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            nama_event VARCHAR(100) NOT NULL,
            deskripsi TEXT,
            tanggal DATE NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            file_path VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (!mysqli_query($conn, $create_events_table)) {
            throw new Exception("Gagal membuat tabel event_pengajuan: " . mysqli_error($conn));
        }
        error_log("Event_pengajuan table created successfully");
    } else {
        // Check if file_path column exists
        $check_column = mysqli_query($conn, "SHOW COLUMNS FROM event_pengajuan LIKE 'file_path'");
        if (mysqli_num_rows($check_column) == 0) {
            // Add file_path column if it doesn't exist
            error_log("Adding file_path column to event_pengajuan table...");
            $alter_table = "ALTER TABLE event_pengajuan ADD COLUMN file_path VARCHAR(255) DEFAULT NULL AFTER status";
            if (!mysqli_query($conn, $alter_table)) {
                throw new Exception("Gagal menambahkan kolom file_path: " . mysqli_error($conn));
            }
            error_log("File_path column added successfully");
        }
        error_log("Event_pengajuan table already exists");
    }
    
    error_log("Database connection and setup completed successfully");
    
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}
?>
