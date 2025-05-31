-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2025 at 09:52 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `re_dilan`
--
CREATE DATABASE IF NOT EXISTS `re_dilan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `re_dilan`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `ekskul` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `role`, `nama_lengkap`, `ekskul`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', NULL),
('osis1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'OSIS Satu', 'OSIS'),
('pramuka1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Pramuka Satu', 'Pramuka'),
('pmr1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'PMR Satu', 'PMR'),
('rohis1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Rohis Satu', 'Rohis');

-- --------------------------------------------------------

--
-- Table structure for table `event_pengajuan`
--

DROP TABLE IF EXISTS `event_pengajuan`;
CREATE TABLE `event_pengajuan` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `judul_event` varchar(200) NOT NULL,
  `jenis_kegiatan` varchar(100) NOT NULL,
  `total_pembiayaan` decimal(15,2) NOT NULL,
  `proposal` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  PRIMARY KEY (`event_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `event_pengajuan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event_pengajuan`
--

INSERT INTO `event_pengajuan` (`user_id`, `judul_event`, `jenis_kegiatan`, `total_pembiayaan`, `proposal`, `deskripsi`, `tanggal_pengajuan`, `status`) VALUES
(2, 'LDKS 2025', 'Pelatihan', 5000000.00, 'proposal_ldks.pdf', 'Latihan Dasar Kepemimpinan Siswa', '2025-06-15', 'menunggu'),
(3, 'Kemah Bakti', 'Kemah', 7500000.00, 'proposal_kemah.pdf', 'Kemah bakti pramuka', '2025-06-20', 'disetujui'),
(4, 'Donor Darah', 'Sosial', 2000000.00, 'proposal_donor.pdf', 'Kegiatan donor darah PMR', '2025-07-01', 'menunggu'),
(5, 'Pesantren Ramadhan', 'Keagamaan', 3000000.00, 'proposal_ramadhan.pdf', 'Pesantren kilat Ramadhan', '2025-07-10', 'ditolak'),
(2, 'Class Meeting', 'Lomba', 4000000.00, 'proposal_class.pdf', 'Lomba antar kelas', '2025-07-15', 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_event`
--

DROP TABLE IF EXISTS `verifikasi_event`;
CREATE TABLE `verifikasi_event` (
  `verifikasi_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `tanggal_verifikasi` datetime NOT NULL,
  `catatan_admin` text DEFAULT NULL,
  `status` enum('disetujui','ditolak') NOT NULL,
  PRIMARY KEY (`verifikasi_id`),
  KEY `event_id` (`event_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `verifikasi_event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_pengajuan` (`event_id`) ON DELETE CASCADE,
  CONSTRAINT `verifikasi_event_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `verifikasi_event`
--

INSERT INTO `verifikasi_event` (`event_id`, `admin_id`, `tanggal_verifikasi`, `catatan_admin`, `status`) VALUES
(2, 1, '2025-06-21 10:00:00', 'Proposal lengkap dan kegiatan bermanfaat', 'disetujui'),
(3, 1, '2025-07-11 14:30:00', 'Anggaran sesuai dengan kegiatan', 'disetujui'),
(4, 1, '2025-06-16 09:15:00', 'Perlu revisi anggaran', 'ditolak'),
(5, 1, '2025-06-25 11:20:00', 'Jadwal bertabrakan', 'ditolak'),
(1, 1, '2025-07-16 13:45:00', 'Menunggu revisi proposal', 'ditolak');

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_pengajuan`
--
ALTER TABLE `event_pengajuan`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verifikasi_event`
--
ALTER TABLE `verifikasi_event`
  MODIFY `verifikasi_id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
