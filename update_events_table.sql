-- Drop the existing event_pengajuan table
DROP TABLE IF EXISTS event_pengajuan;

-- Recreate the event_pengajuan table with the status column
CREATE TABLE event_pengajuan (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_event VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    tanggal DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 