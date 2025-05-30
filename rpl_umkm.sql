-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 30, 2025 at 07:20 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rpl_umkm`
--

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `produk_option_id` int DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text,
  `harga` int NOT NULL,
  `gambar` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `ukuran` varchar(10) DEFAULT NULL,
  `label` varchar(50) DEFAULT NULL,
  `label_class` varchar(50) DEFAULT NULL,
  `stok` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `harga`, `gambar`, `kategori`, `ukuran`, `label`, `label_class`, `stok`) VALUES
(1, 'Tulang Rangu', 'Original ...', 10000, '01.jpg', 'tulangrangu', 'M', 'NEW', 'new', 29),
(2, 'Dakbal', 'Original ...', 10000, '02.jpg', 'dakbal', 'M', 'NEW', 'new', 29),
(3, 'Ceker Mercon', 'Original ...', 10000, '03.jpg', 'cekermercon', 'M', 'HOT', 'hot', 29),
(4, 'Dimsum Tulang Rangu', 'Original ...', 10000, '04.jpg', 'dimsum', 'M', 'NEW', 'new', 29);

-- --------------------------------------------------------

--
-- Table structure for table `produk_options`
--

CREATE TABLE `produk_options` (
  `id` int NOT NULL,
  `produk_id` int NOT NULL,
  `type` enum('size','extra') NOT NULL,
  `label` varchar(100) NOT NULL,
  `harga` int NOT NULL DEFAULT '0',
  `is_default` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk_options`
--

INSERT INTO `produk_options` (`id`, `produk_id`, `type`, `label`, `harga`, `is_default`) VALUES
(1, 1, 'size', 'Small', 5000, 0),
(2, 1, 'size', 'Medium', 10000, 1),
(3, 1, 'size', 'Large', 15000, 0),
(4, 1, 'extra', 'Extra Cheese', 2000, 0),
(13, 2, 'size', 'Small', 5000, 0),
(14, 2, 'size', 'Medium', 10000, 1),
(15, 2, 'size', 'Large', 15000, 0),
(16, 2, 'extra', 'Extra Cheese', 2000, 0),
(17, 3, 'size', 'Small', 5000, 0),
(18, 3, 'size', 'Medium', 10000, 1),
(19, 3, 'size', 'Large', 15000, 0),
(20, 3, 'extra', 'Extra Cheese', 2000, 0),
(29, 4, 'size', 'Small', 5000, 0),
(30, 4, 'size', 'Medium', 10000, 1),
(31, 4, 'size', 'Large', 15000, 0),
(32, 4, 'extra', 'Extra Cheese', 2000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'nova', '123', 'admin'),
(2, 'ayang', '$2y$10$uJg.6h8hTxP2kvphTulGjOhplCLnDO8f8sHN8kjxgDnN9Z95sQVGi', 'admin'),
(3, 'captain', '$2y$10$PUFcZNbdhzZ.atlLM4oEuOOIvba1C4QqV.9T4//janI7duFR4/T3.', 'admin'),
(4, 'robinhood', '$2y$10$5mdjYrshr6XyxcjmWFT64uphwph3/HzADT7GD8lDM0mQJG5GSqF.2', 'admin'),
(5, 'batman', '$2y$10$bDu2zeWcXDAw/qg50BWze.xwnkUE2P/FWa9dgxWaH7zMUF60.LZs.', 'admin'),
(8, 'ferdi', '$2y$10$rK5l2mu1VnyWwW0yBLw0ee76OVBbfOglgigpLFiPU8X7UmVOgaSKC', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `produk_option_id` (`produk_option_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_options`
--
ALTER TABLE `produk_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk_options`
--
ALTER TABLE `produk_options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`produk_option_id`) REFERENCES `produk_options` (`id`);

--
-- Constraints for table `produk_options`
--
ALTER TABLE `produk_options`
  ADD CONSTRAINT `produk_options_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
