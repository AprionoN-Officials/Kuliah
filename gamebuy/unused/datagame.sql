-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 08:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datagame`
--

-- --------------------------------------------------------

--
-- Table structure for table `daftargame`
--

CREATE TABLE `daftargame` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `genre` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_sewa` int(11) NOT NULL,
  `gambar` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftargame`
--

INSERT INTO `daftargame` (`id`, `judul`, `genre`, `deskripsi`, `harga_sewa`, `gambar`) VALUES
(1, 'GTA V', 'Action', 'Open world adventure', 15000, 'gta.jpg'),
(2, 'Valorant', 'FPS', 'Competitive shooter game', 12000, 'valorant.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `harga_sewa` decimal(10,2) NOT NULL,
  `harga_beli` decimal(10,2) NOT NULL,
  `stok` int(11) DEFAULT 1,
  `gambar` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `judul`, `deskripsi`, `genre`, `harga_sewa`, `harga_beli`, `stok`, `gambar`, `created_at`) VALUES
(1, 'Elden Ring', NULL, 'RPG', 5000.00, 500000.00, 5, 'default.jpg', '2026-01-03 05:08:24'),
(2, 'FIFA 24', NULL, 'Sports', 7000.00, 750000.00, 3, 'default.jpg', '2026-01-03 05:08:24'),
(3, 'GTA V', NULL, 'Action', 3000.00, 300000.00, 10, 'default.jpg', '2026-01-03 05:08:24'),
(4, 'God Of War', 'Petualangan kratos ', 'Action', 5000.00, 799000.00, 5, '', '2026-01-04 04:40:39'),
(5, 'Cyberpunk 2077', 'RPG masa depan dengan grafis memukau di Night City.', 'RPG, Sci-Fi', 15000.00, 700000.00, 5, 'cyberpunk_2077.jpg', '2026-01-04 14:05:21'),
(6, 'Resident Evil Village', 'Kelanjutan kisah Ethan Winters di desa yang penuh misteri.', 'Horror, Action', 12000.00, 550000.00, 7, 'resident_evil_village.jpg', '2026-01-04 14:05:21'),
(7, 'Spider-Man Miles Morales', 'Menjadi Spider-Man baru dan selamatkan New York.', 'Action, Adventure', 10000.00, 450000.00, 8, 'spider-man_miles_morales.jpg', '2026-01-04 14:05:21'),
(8, 'Red Dead Redemption 2', 'Kisah epik di era koboi Amerika yang sangat detail.', 'Action, Open World', 15000.00, 650000.00, 6, 'red_dead_redemption_2.jpg', '2026-01-04 14:05:21'),
(9, 'Resident Evil Biohazard', 'Perjalanan Ethan Winters Mencari Istri Dan Anaknya.', 'FPS,Horror', 6000.00, 699000.00, 3, 'resident_evil_biohazard.png', '2026-01-04 14:28:46'),
(10, 'Assasins Creed Valhalla', 'Petualangan Assasins Di Dunia Viking', 'Action, RPG, Open World', 5900.00, 599000.00, 6, 'assasins_creed_valhalla.png', '2026-01-04 14:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `tipe_transaksi` enum('sewa','beli') NOT NULL,
  `tanggal_pinjam` datetime DEFAULT current_timestamp(),
  `durasi_hari` int(11) DEFAULT 0,
  `tanggal_kembali` datetime DEFAULT NULL,
  `status` enum('dipinjam','kembali','permanent') DEFAULT 'dipinjam',
  `total_bayar` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `saldo` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `saldo`, `created_at`) VALUES
(1, 'admin', 'admin', 'admin', 0.00, '2026-01-03 05:08:24'),
(2, 'user', '$2y$10$BehNLjwTaXbADiauovX87uCvx17Ja9TLkD7ZnO7LdsoemUz3AGC6S', 'user', 0.00, '2026-01-03 05:20:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daftargame`
--
ALTER TABLE `daftargame`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

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
-- AUTO_INCREMENT for table `daftargame`
--
ALTER TABLE `daftargame`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
