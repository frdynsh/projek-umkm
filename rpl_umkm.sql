-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 25, 2025 at 04:13 PM
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
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`) VALUES
(2, 'Plastik', 'barang', 25000.00, 34, '0'),
(3, 'Nugget', 'makanan', 68000.00, 3, '0'),
(5, 'sepeda', 'kendaraan', 2000000.00, 4, '0'),
(6, 'bakso', 'makanan', 25000.00, 5, '0'),
(7, 'Rendang', 'padang', 12000.00, 3, '0'),
(9, 'sosis', 'makanan', 20000.00, 5, '0'),
(10, 'saos', 'bumbu', 15000.00, 6, '0'),
(12, 'Cup', 'wadah', 12000.00, 45, '0');

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
(1, 'nova', '$2y$10$OT/030VAQknl8NZx9BlgSuTNeiq2mGQhSW2pjAr7RzlREHc6yvJ2u', 'admin'),
(2, 'ayang', '$2y$10$uJg.6h8hTxP2kvphTulGjOhplCLnDO8f8sHN8kjxgDnN9Z95sQVGi', 'admin'),
(3, 'captain', '$2y$10$PUFcZNbdhzZ.atlLM4oEuOOIvba1C4QqV.9T4//janI7duFR4/T3.', 'admin'),
(4, 'robinhood', '$2y$10$5mdjYrshr6XyxcjmWFT64uphwph3/HzADT7GD8lDM0mQJG5GSqF.2', 'admin'),
(5, 'batman', '$2y$10$bDu2zeWcXDAw/qg50BWze.xwnkUE2P/FWa9dgxWaH7zMUF60.LZs.', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
