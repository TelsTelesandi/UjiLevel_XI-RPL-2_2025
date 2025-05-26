-- Hapus admin lama jika ada
DELETE FROM users WHERE username = 'admin';

-- Tambah akun admin baru
INSERT INTO users (username, password, role, nama_lengkap, Ekskul) 
VALUES ('admin', '$2y$10$YourNewHashHere', 'admin', 'Administrator', 'Admin'); 