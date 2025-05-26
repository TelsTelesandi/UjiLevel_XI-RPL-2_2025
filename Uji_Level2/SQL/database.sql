-- Create database
CREATE DATABASE IF NOT EXISTS event_management;
USE event_management;

-- Create users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    Ekskul VARCHAR(255) NOT NULL
);

-- Create event_pengajuan table
CREATE TABLE event_pengajuan (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    judul_event VARCHAR(255) NOT NULL,
    jenis_kegiatan VARCHAR(255) NOT NULL,
    Total_pembiayaan VARCHAR(255) NOT NULL,
    Proposal VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal_pengajuan DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'menunggu',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create verifikasi_event table
CREATE TABLE verifikasi_event (
    verifikasi_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT,
    admin_id INT,
    tanggal_verifikasi VARCHAR(255) NOT NULL,
    catatan_admin TEXT,
    Status VARCHAR(50) DEFAULT 'unclosed',
    FOREIGN KEY (event_id) REFERENCES event_pengajuan(event_id),
    FOREIGN KEY (admin_id) REFERENCES users(user_id)
); 