-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `re_nabil_efriansyah`;

-- Use the database
USE `re_nabil_efriansyah`;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
);

-- Create event_pengajuan table
CREATE TABLE IF NOT EXISTS event_pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul_kegiatan VARCHAR(255) NOT NULL,
    event_ekskul VARCHAR(255) NOT NULL,
    tanggal_pengajuan DATE NOT NULL,
    total_biaya INT NOT NULL,
    proposal VARCHAR(255),
    status ENUM('Pending', 'Disetujui', 'Ditolak', 'Selesai') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create event table
CREATE TABLE IF NOT EXISTS `event`