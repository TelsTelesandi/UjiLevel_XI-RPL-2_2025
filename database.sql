-- Create database if not exists
CREATE DATABASE IF NOT EXISTS re_fachri;
USE re_fachri;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS event_pengajuan;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_token_expiry TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create event_pengajuan table
CREATE TABLE IF NOT EXISTS event_pengajuan (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_event VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    tanggal DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'closed') DEFAULT 'pending',
    keterangan TEXT,
    completion_notes TEXT,
    completion_date TIMESTAMP NULL,
    closed_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO users (username, password, nama_lengkap, email, role) 
VALUES ('admin', MD5('admin123'), 'Administrator', 'admin@example.com', 'admin')
ON DUPLICATE KEY UPDATE username=username; 