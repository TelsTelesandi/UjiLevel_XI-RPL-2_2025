-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS re_dillan;
USE re_dillan;

-- Membuat tabel users
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    Ekskul VARCHAR(255) NOT NULL
);

-- Membuat tabel event_pengajuan
CREATE TABLE event_pengajuan (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    judul_event VARCHAR(255) NOT NULL,
    jenis_kegiatan VARCHAR(255) NOT NULL,
    total_pembiayaan DECIMAL(15,2) NOT NULL,
    file_proposal VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    tanggal_pengajuan DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_update DATETIME ON UPDATE CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'menunggu',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membuat tabel verifikasi_event
CREATE TABLE verifikasi_event (
    verifikasi_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT,
    admin_id INT,
    tanggal_verifikasi DATETIME DEFAULT CURRENT_TIMESTAMP,
    catatan_admin TEXT,
    status VARCHAR(50) DEFAULT 'unclosed',
    FOREIGN KEY (event_id) REFERENCES event_pengajuan(event_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Menambahkan index untuk optimasi
CREATE INDEX idx_user_role ON users(role);
CREATE INDEX idx_event_status ON event_pengajuan(status);
CREATE INDEX idx_verifikasi_status ON verifikasi_event(status);
CREATE INDEX idx_event_tanggal ON event_pengajuan(tanggal_pengajuan);

-- Insert data admin default (password: admin123)
INSERT INTO users (username, password, role, nama_lengkap, Ekskul) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'Semua');

-- Insert sample data untuk event_pengajuan
INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, file_proposal, deskripsi, status) VALUES
(1, 'Lomba Futsal Antar Sekolah', 'lomba', 5000000.00, 'proposal_futsal_2024.pdf', 'Kompetisi futsal tingkat SMA se-Jakarta', 'menunggu'),
(1, 'Workshop Programming', 'workshop', 3500000.00, 'proposal_workshop_coding.pdf', 'Workshop coding untuk pemula dengan bahasa Python', 'menunggu'),
(1, 'Seminar Digital Marketing', 'seminar', 2500000.00, 'proposal_seminar_marketing.pdf', 'Seminar tentang strategi digital marketing untuk siswa', 'menunggu'),
(1, 'Pelatihan Public Speaking', 'pelatihan', 1500000.00, 'proposal_public_speaking.pdf', 'Pelatihan public speaking untuk meningkatkan kemampuan berbicara di depan umum', 'menunggu'),
(1, 'Kompetisi Robotik', 'lomba', 7500000.00, 'proposal_robotik_2024.pdf', 'Kompetisi robotik tingkat nasional', 'menunggu');

-- Tambahkan index baru jika belum ada
CREATE INDEX IF NOT EXISTS idx_event_status ON event_pengajuan(status);
CREATE INDEX IF NOT EXISTS idx_event_tanggal ON event_pengajuan(tanggal_pengajuan); 