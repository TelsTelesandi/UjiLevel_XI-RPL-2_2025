-- Hapus user admin yang mungkin duplikat
DELETE FROM users WHERE username = 'admin' AND user_id NOT IN (
    SELECT min_id FROM (
        SELECT MIN(user_id) as min_id FROM users WHERE username = 'admin'
    ) as temp
);

-- Tambah akun admin baru jika belum ada
INSERT INTO users (username, password, role, nama_lengkap, Ekskul) 
SELECT 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'Admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');

-- Tampilkan semua user
SELECT user_id, username, role, nama_lengkap, Ekskul FROM users; 