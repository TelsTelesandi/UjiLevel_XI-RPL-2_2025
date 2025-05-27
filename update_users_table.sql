-- First drop the verifikasi_event table
DROP TABLE IF EXISTS verifikasi_event;

-- Then drop the event_pengajuan table
DROP TABLE IF EXISTS event_pengajuan;

-- Then drop the users table
DROP TABLE IF EXISTS users;

-- Create users table with the updated structure
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO users (username, password, nama_lengkap, email, role) 
VALUES ('admin', MD5('admin123'), 'Administrator', 'admin@example.com', 'admin');

-- Recreate the simplified event_pengajuan table
CREATE TABLE event_pengajuan (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_event VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 