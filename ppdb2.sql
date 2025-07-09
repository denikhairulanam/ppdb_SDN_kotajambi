-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for ppdb2
CREATE DATABASE IF NOT EXISTS `ppdb2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ppdb2`;

-- Dumping structure for table ppdb2.pengaturan
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal_daftar_ulang` date DEFAULT NULL,
  `pengumuman` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ppdb2.pengaturan: ~1 rows (approximately)
INSERT INTO `pengaturan` (`id`, `tanggal_daftar_ulang`, `pengumuman`) VALUES
	(2, '2025-07-05', 'oke sudah siap');

-- Dumping structure for table ppdb2.upload
CREATE TABLE IF NOT EXISTS `upload` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `nama_ortu` varchar(100) DEFAULT NULL,
  `alamat` text,
  `kk` varchar(255) DEFAULT NULL,
  `akta` varchar(255) DEFAULT NULL,
  `pernyataan` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status_pendaftaran` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pengumuman` text,
  `status` enum('Diterima','Ditolak','Belum Diverifikasi') DEFAULT 'Belum Diverifikasi',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `upload_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ppdb2.upload: ~4 rows (approximately)
INSERT INTO `upload` (`id`, `user_id`, `nik`, `nama_ortu`, `alamat`, `kk`, `akta`, `pernyataan`, `foto`, `status_pendaftaran`, `created_at`, `pengumuman`, `status`) VALUES
	(2, 2, '1506072709050001', 'damiri', 'desa sri agung ,kec.batang asam,kab.tanjung jabung barat, jambi', '1749979470_6459884-20140320094718-5eb0545ed541df0dae293a42.jpg', '1749979470_download (4).jpg', '1749979470_rumah rahmat.jpg', '1749979470_Screenshot 2025-03-08 104632.png', 'Diterima', '2025-06-15 09:24:30', 'Selamat! Anda dinyatakan diterima. Silakan menunggu informasi selanjutnya dari panitia.', 'Belum Diverifikasi'),
	(3, 11, '1506072709050001', 'salbiah', 'desa sri agung ,kec.batang asam,kab.tanjung jabung barat, jambi', '1749998990_download (5).jpg', '1749998990_buku novella.jpg', '1749998990_download (6).jpg', '1749998990_download (7).jpg', 'Diterima', '2025-06-15 14:49:50', 'Selamat! Anda dinyatakan diterima. Silakan menunggu informasi selanjutnya dari panitia.', 'Belum Diverifikasi'),
	(7, 23, '1506072709050001', 'igk', 'gig', '1750110492_buku novella.jpg', '1750110492_images (2).jpg', '1750110492_download (2).jpg', '1750110492_download (7).jpg', 'Ditolak', '2025-06-16 21:39:23', 'Mohon maaf, Anda tidak diterima. Terima kasih telah mendaftar.', 'Belum Diverifikasi'),
	(8, 25, '1506072709050001', 'ni', 'naninu', '1750118096_images (1).jpg', '1750118096_download (4).jpg', '1750118096_download (7).jpg', '1750118096_download (2).jpg', 'Menunggu', '2025-06-16 23:52:52', NULL, 'Belum Diverifikasi');

-- Dumping structure for table ppdb2.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `role` enum('admin','siswa') DEFAULT 'siswa',
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ppdb2.users: ~9 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `email`, `nama`, `role`, `foto`) VALUES
	(1, 'rahmat', '$2y$10$16Z8fT94RCE73SDqUJqqxuK71M/UkFapLh/vH.jVK.d4oL7xVjE9i', 'rahmat@gmail.com', 'rahmat tanjung', 'admin', 'admin_1750116183.jpg'),
	(2, 'deni', '$2y$10$7Sp36G9wmm7iiZmY6yLSmevNF7uDxn1aqmLboeEulRS3zxGpI2acK', 'denikhairulanam@gmail.com', 'deni khairul anam', 'siswa', NULL),
	(9, 'nanda', '$2y$10$vaZaVcXwzUnexBED.OU.Le1VZpzbcZjqWSJ4zfFX6HrJgfZB7WRu6', 'nanda@gmail.com', 'nanda', 'admin', '1750117427_download (2).jpg'),
	(11, 'roni', '$2y$10$fDuSjTEAlbqwf6AWqDLTlunpJw5Vu/Ddp3Zqr2PaT4G1dfJDCI3mC', 'roni@gmail.com', 'roni', 'siswa', NULL),
	(14, 'defri', '$2y$10$5m7RQvJ711lBi1.uVGbVbeUih4pP.3dpIwUOA5uKMZxXvaRNU4mwS', 'defri@gmail.com', 'defri', 'admin', '1750117439_ABANGDA.jpeg'),
	(20, 'ibom', '$2y$10$lZOdFuBRF3TFrJfpYW5c0u0RNCdEhtYT2nbVKnacMMWZhBGo.DBOW', 'ibom@gmail.com', 'ibom', 'admin', '1750063952_download (7).jpg'),
	(23, 'ucok', '$2y$10$K0ubtKOs4VA.GymoKAF6NOAgxJMwFpcOqYdz4EoroYUByQwpYpcr6', 'ucokkk@gamil.com', 'ucok', 'siswa', NULL),
	(24, 'icibos', '$2y$10$9/k1gt6G.9twiUkWdXHFDed4PGl6P5j10AQcxjq/phlnp8VdU72Ru', 'icibos@gmail.com', 'icibos', 'admin', '1750116494_download (3).jpg'),
	(25, 'na', '$2y$10$zOuDGIsXKWsvP1hKTDIWZu.PKfg392PWq1tPwFAIireLSmIqmi0na', 'na@gmail.com', 'na', 'siswa', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
