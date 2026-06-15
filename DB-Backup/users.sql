-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2024 at 03:24 PM
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
-- Database: `hrms_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('Admin','Client','Employee') NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `employee_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `phone`, `role`, `company`, `employee_id`, `created_at`, `updated_at`, `permissions`) VALUES
(1, 'Pooranachandiran', 'G', 'Chandru', 'gpooranachandiran@gmail.com', '$2y$10$bewTOLBu4/VQWvQSNTUGgOsJRwkCZCGWsCBmjvw9UgfZCgw.Am1oy', '6379257529', 'Client', 'Global Technologies', 'WE-0001', '2024-10-18 06:04:32', '2024-10-19 06:36:31', '{\"employee\":{\"write\":\"on\",\"create\":\"on\",\"delete\":\"on\"},\"holidays\":{\"delete\":\"on\",\"import\":\"on\"},\"leaves\":{\"write\":\"on\",\"create\":\"on\",\"delete\":\"on\"},\"events\":{\"read\":\"on\",\"write\":\"on\",\"delete\":\"on\",\"import\":\"on\",\"export\":\"on\"}}'),
(2, 'Prem', 'M', 'Premns', 'prem@gmail.com', '$2y$10$Km3Dow83tXlRT8WFK14vnO5STL3QjAPoAMjFSHBTtnNjd5i4x9nfW', '6379257529', 'Employee', 'Global Technologies', 'AP-0006', '2024-10-18 06:36:36', '2024-10-19 05:36:05', '{\"employee\":{\"read\":\"on\",\"write\":\"on\",\"export\":\"on\"}}'),
(5, 'Rajesh', 'K', 'Rajesh', 'rajesh22@gmail.com', '$2y$10$cQ2iFjjAiqlkm/qo7Ibwh.yRQaYN1sZb4boxX/cM0M3zi1ZlDW0Y.', '6379257529', 'Employee', 'Delta Infotech', 'AP-0006', '2024-10-18 06:56:02', '2024-10-18 08:55:14', NULL),
(6, 'Riyas', 'M', 'riyas', 'riyas@gmail.com', '$2y$10$ZI/19H9iYwxffDDUTxkwf./.hOARnL9PIfgrltGd/CCgvpPws6PX.', '6379257529', 'Admin', 'Global Technologies', 'IT-0002', '2024-10-18 07:51:50', '2024-10-18 08:55:34', NULL),
(8, 'Virat', 'Kohli', 'virat', 'virat@gmail.com', '$2y$10$dOqzr6OKTEUb8OK5r1BUMuCf.8nf7kvzQ8tYDWZ5Y95zaWzhWga3.', '6379257529', 'Admin', 'Global Technologies', 'AP-0006', '2024-10-18 10:31:31', '2024-10-19 06:00:31', '{\"employee\":{\"delete\":\"on\"},\"holidays\":{\"read\":\"on\"},\"leaves\":{\"write\":\"on\"}}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
