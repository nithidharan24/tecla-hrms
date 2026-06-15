-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 05:28 PM
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
-- Database: `hrms-app`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets_company`
--

CREATE TABLE `assets_company` (
  `id` int(11) NOT NULL,
  `asset_user` varchar(255) NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `asset_id` varchar(25) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `purchase_from` varchar(25) NOT NULL,
  `manufacturer` varchar(25) NOT NULL,
  `model` varchar(25) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `supplier` varchar(25) NOT NULL,
  `condition` varchar(100) NOT NULL,
  `warranty` int(3) NOT NULL,
  `value` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` enum('pending','approved','returned') NOT NULL DEFAULT 'pending',
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets_company`
--

INSERT INTO `assets_company` (`id`, `asset_user`, `asset_name`, `asset_id`, `purchase_date`, `purchase_from`, `manufacturer`, `model`, `serial_number`, `supplier`, `condition`, `warranty`, `value`, `description`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '2', 'Dell laptop', NULL, '2024-10-16', 'bhjg', 'chennai', 'Dell Inspiron 14 Plus', '65154866', 'prem', 'good', 12, 50000, 'dchgdfxcfgvhhhhh', 'pending', 1, '2024-10-15 14:42:53', '2024-10-15 14:42:53'),
(2, '5', 'Dell laptop', '#AST-7591', '2024-10-17', 'nithish', 'chennai', 'hp Inspiron 14 Plus', '24454866', 'prem', 'good', 12, 45000, 'qwertyuioas', 'returned', 0, '2024-10-15 14:49:39', '2024-10-15 14:49:39'),
(3, '3', 'Hp laptop', '#AST-2583', '2024-10-24', 'offline', 'chennai', 'hp victus f15', '548796331', 'prem', 'good', 11, 54000, 'hp victus f15', 'pending', 0, '2024-10-15 14:56:01', '2024-10-15 14:56:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets_company`
--
ALTER TABLE `assets_company`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets_company`
--
ALTER TABLE `assets_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
