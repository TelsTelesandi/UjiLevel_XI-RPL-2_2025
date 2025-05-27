-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 26, 2025 at 12:06 AM
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
-- Database: `re_zayn`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_pengajuan`
--

CREATE TABLE `event_pengajuan` (
  `event_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `judul_event` varchar(100) DEFAULT NULL,
  `jenis_kegiatan` varchar(100) DEFAULT NULL,
  `total_pembiayaan` varchar(50) DEFAULT NULL,
  `proposal` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `tanggal_pengajuan` date DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_pengajuan`
--

INSERT INTO `event_pengajuan` (`event_id`, `user_id`, `judul_event`, `jenis_kegiatan`, `total_pembiayaan`, `proposal`, `deskripsi`, `tanggal_pengajuan`, `status`) VALUES
(10, 1, 'tes1', 'tes1', '23131343', 'proposal_6831caec7fdc3.pdf', 'er', '2025-05-29', 'ditolak'),
(11, 1, 'tes2', 'tes2', '52364354', 'proposal_6831cb05b5b75.pdf', 'dfa', '2025-05-31', 'disetujui'),
(12, 4, 'tessas', 'tessas', '100000', 'proposal_683330e5522c7.pdf', 'fd', '2025-06-04', 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `ekskul` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `nama_lengkap`, `ekskul`) VALUES
(1, 'zayn', 'zayn123', 'user', 'Zayn', 'Futsal'),
(2, 'admin', 'admin123', 'admin', 'Admin', NULL),
(4, 'sastio', 'sastio123', 'user', 'sastio dwi', 'voli');

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_event`
--

CREATE TABLE `verifikasi_event` (
  `verifikasi_id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL,
  `tanggal_verifikasi` varchar(50) DEFAULT NULL,
  `catatan_admin` text,
  `status` enum('closed','unclosed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `verifikasi_event`
--

INSERT INTO `verifikasi_event` (`verifikasi_id`, `event_id`, `admin_id`, `tanggal_verifikasi`, `catatan_admin`, `status`) VALUES
(1, 10, 2, '2025-05-24 20:36:05', 'dfsa', 'closed'),
(2, 11, 2, '2025-05-24 20:36:00', '', 'closed'),
(3, 12, NULL, '2025-05-25 22:01:57', NULL, 'unclosed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_pengajuan`
--
ALTER TABLE `event_pengajuan`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `verifikasi_event`
--
ALTER TABLE `verifikasi_event`
  ADD PRIMARY KEY (`verifikasi_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_pengajuan`
--
ALTER TABLE `event_pengajuan`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `verifikasi_event`
--
ALTER TABLE `verifikasi_event`
  MODIFY `verifikasi_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_pengajuan`
--
ALTER TABLE `event_pengajuan`
  ADD CONSTRAINT `event_pengajuan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `verifikasi_event`
--
ALTER TABLE `verifikasi_event`
  ADD CONSTRAINT `verifikasi_event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_pengajuan` (`event_id`),
  ADD CONSTRAINT `verifikasi_event_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
