-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260503.e79a6a78f4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2026 at 02:05 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bookcash`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int NOT NULL,
  `id_kategori` int NOT NULL,
  `kode_barcode` varchar(100) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `penulis` varchar(150) NOT NULL,
  `penerbit` varchar(150) DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stok` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `id_kategori`, `kode_barcode`, `judul`, `penulis`, `penerbit`, `tahun`, `harga`, `stok`) VALUES
(1, 1, '899100000001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang', 2005, 85000.00, 6),
(2, 1, '899100000002', 'Bumi', 'Tere Liye', 'Gramedia', 2014, 95000.00, 14),
(3, 2, '899100000003', 'Doraemon', 'Fujiko F. Fujio', 'Elex Media', 2020, 45000.00, 14),
(4, 3, '899100000004', 'Matematika Kelas XI', 'Tim Edukasi', 'Erlangga', 2025, 70000.00, 12),
(5, 4, '899100000005', 'Belajar Java Dasar', 'Tim Pemrograman', 'Informatika', 2025, 90000.00, 5);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_buku` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_buku`, `jumlah`, `harga`, `subtotal`) VALUES
(6, 19, 2, 1, 95000.00, 95000.00),
(8, 21, 3, 2, 45000.00, 90000.00),
(9, 22, 1, 2, 85000.00, 170000.00);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Novel'),
(2, 'Komik'),
(3, 'Pelajaran'),
(4, 'Pemrograman'),
(5, 'Ensiklopedia');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `bayar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `kembalian` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `tanggal`, `total`, `bayar`, `kembalian`, `metode`) VALUES
(19, NULL, '2026-10-24 13:50:07', 95000.00, 95000.00, 0.00, 'Qris'),
(21, NULL, '2026-10-25 10:59:20', 90000.00, 100000.00, 10000.00, 'Tunai'),
(22, NULL, '2026-10-25 11:00:09', 170000.00, 170000.00, 0.00, 'Qris');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `level` enum('Admin','Kasir') NOT NULL DEFAULT 'Kasir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama`, `level`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'Admin'),
(2, 'kasir', 'kasir123', 'Kasir Toko', 'Kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD UNIQUE KEY `kode_barcode` (`kode_barcode`),
  ADD KEY `fk_buku_kategori` (`id_kategori`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_transaksi` (`id_transaksi`),
  ADD KEY `fk_detail_buku` (`id_buku`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `fk_buku_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `fk_detail_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
