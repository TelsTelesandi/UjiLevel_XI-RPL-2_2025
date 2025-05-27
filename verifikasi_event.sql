-- Membuat tabel verifikasi_event jika belum ada
CREATE TABLE IF NOT EXISTS verifikasi_event (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    tanggal_verifikasi DATE NOT NULL,
    catatan TEXT,
    FOREIGN KEY (event_id) REFERENCES event_pengajuan(id) ON DELETE CASCADE
); 