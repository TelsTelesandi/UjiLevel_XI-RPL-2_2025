-- Database: re_jihan
CREATE DATABASE IF NOT EXISTS re_jihan;
USE re_jihan;

-- Table: users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    nama_lengkap VARCHAR(100) NOT NULL,
    ekskul VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: event_pengajuan
CREATE TABLE event_pengajuan (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul_event VARCHAR(200) NOT NULL,
    jenis_kegiatan VARCHAR(100) NOT NULL,
    total_pembiayaan DECIMAL(15,2) NOT NULL,
    proposal VARCHAR(255),
    deskripsi TEXT,
    tanggal_pengajuan DATE NOT NULL,
    status ENUM('menunggu', 'disetujui', 'ditolak', 'selesai') DEFAULT 'menunggu',
    foto_dokumentasi VARCHAR(255),
    tanggal_selesai DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table: verifikasi_event
CREATE TABLE verifikasi_event (
    verifikasi_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    admin_id INT NOT NULL,
    tanggal_verifikasi DATE NOT NULL,
    catatan_admin TEXT,
    status ENUM('Unclosed', 'Close', 'Closed') DEFAULT 'Unclosed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event_pengajuan(event_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);
