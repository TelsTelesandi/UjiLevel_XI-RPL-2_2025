-- Create verifikasi_event table if not exists
CREATE TABLE IF NOT EXISTS verifikasi_event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    tanggal_verifikasi DATE NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event_pengajuan(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_id (event_id)
) ENGINE=InnoDB;

-- Add indexes for better performance
ALTER TABLE verifikasi_event ADD INDEX idx_status (status);
ALTER TABLE verifikasi_event ADD INDEX idx_tanggal (tanggal_verifikasi); 