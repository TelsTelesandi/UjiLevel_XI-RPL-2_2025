-- Sample data for users table
INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES
('admin', '$2y$10$Wd8/uvDhG6VJaZvHndMrUOAZQyxvxlq2hVS.CxEGf3OGzDHCn8.Hy', 'admin', 'Admin', 'Admin'),
('rehan', '$2y$10$2Yw0PgGwNqvQNLGz9ZGBr.pwh0CUDw3I5VJKxFO9FLlJBAYYxGZOO', 'user', 'Raihan Kusuma', 'Basket'),
('biyu', '$2y$10$Yw0PgGwNqvQNLGz9ZGBr.pwh0CUDw3I5VJKxFO9FLlJBAYYxGZOO/', 'user', 'Abiyu Roblox', 'Futsal'),
('tegar', '$2y$10$GwNqvQNLGz9ZGBr.pwh0CUDw3I5VJKxFO9FLlJBAYYxGZOO/Yw0P', 'user', 'Tegar Arya', 'Pramuka'),
('juno', '$2y$10$NqvQNLGz9ZGBr.pwh0CUDw3I5VJKxFO9FLlJBAYYxGZOO/Yw0PgGw', 'user', 'Rifqhi Harjuno', 'PMR');

-- Sample data for event_pengajuan table
INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiayaan, Proposal, deskripsi, tanggal_pengajuan, status) VALUES
(2, 'Turnamen Basket Antar Kelas', 'Kompetisi', '2500000', 'proposal_basket.pdf', 'Turnamen basket antar kelas untuk meningkatkan sportivitas', '2024-05-20', 'menunggu'),
(3, 'Futsal Championship 2024', 'Kompetisi', '3000000', 'proposal_futsal.pdf', 'Kejuaraan futsal tingkat sekolah', '2024-05-21', 'disetujui'),
(4, 'Kemah Pramuka Tahunan', 'Kegiatan Outdoor', '5000000', 'proposal_pramuka.pdf', 'Kegiatan kemah tahunan untuk anggota pramuka', '2024-05-22', 'ditolak'),
(5, 'Pelatihan Pertolongan Pertama', 'Pelatihan', '1500000', 'proposal_pmr.pdf', 'Pelatihan dasar pertolongan pertama untuk anggota PMR', '2024-05-23', 'menunggu'),
(2, 'Workshop Teknik Basket', 'Workshop', '2000000', 'proposal_workshop.pdf', 'Workshop teknik dasar bermain basket', '2024-05-24', 'disetujui');

-- Sample data for verifikasi_event table
INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES
(1, 1, '2024-05-21', 'Proposal sudah bagus, mohon dilengkapi rincian anggaran', 'unclosed'),
(2, 1, '2024-05-22', 'Disetujui dengan revisi minor pada jadwal', 'closed'),
(3, 1, '2024-05-23', 'Anggaran terlalu besar, mohon direvisi', 'closed'),
(4, 1, '2024-05-24', 'Mohon dilengkapi dengan surat izin dari orangtua', 'unclosed'),
(5, 1, '2024-05-25', 'Disetujui, silahkan dilaksanakan sesuai proposal', 'closed'); 