-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 10:34 AM
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
-- Table structure for table `performance_review_signatures`
--

CREATE TABLE `performance_review_signatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `employee_signature` varchar(255) NOT NULL,
  `employee_date` date NOT NULL,
  `ro_name` varchar(255) NOT NULL,
  `ro_signature` varchar(255) NOT NULL,
  `ro_date` date NOT NULL,
  `hod_name` varchar(255) NOT NULL,
  `hod_signature` varchar(255) NOT NULL,
  `hod_date` date NOT NULL,
  `hrd_name` varchar(255) NOT NULL,
  `hrd_signature` varchar(255) NOT NULL,
  `hrd_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_signatures`
--

INSERT INTO `performance_review_signatures` (`id`, `review_id`, `employee_name`, `employee_signature`, `employee_date`, `ro_name`, `ro_signature`, `ro_date`, `hod_name`, `hod_signature`, `hod_date`, `hrd_name`, `hrd_signature`, `hrd_date`, `created_at`, `updated_at`) VALUES
(1, 10, 'prem', 'fgf', '2024-11-04', 'dxd', 'fgfg', '2024-11-04', 'fvf', 'fgfg', '2024-11-04', 'fcf', 'fgf', '2024-11-04', '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(2, 12, 'hg', 'ggh', '2024-11-13', 'hg', 'ghhg', '2024-11-05', 'hghf', 'hggh', '2024-10-31', 'hg', 'hggh', '2024-11-20', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(3, 13, 'ttyty', 'trtyt', '2024-11-07', 'tyty', 'tyty', '2024-11-14', 'tyty', 'tyt', '2024-11-01', 'tyty', 'tthyt', '2024-11-05', '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(4, 14, 'yhtg', 'gg', '2024-11-11', 'gg', 'gg', '2024-11-04', 'g', 'gg', '2024-11-04', 'gg', 'gg', '2024-11-04', '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(5, 15, 'prem', 'ff', '2024-11-05', 'sdf', 'f', '2024-11-05', 'ddv', 'f', '2024-11-05', 'fdf', 'f', '2024-11-05', '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(6, 16, 'bgf', 'bgf', '2024-11-05', 'bgfv', 'bgfv', '2024-11-05', 'bgv', 'bgf', '2024-11-05', 'bgf', 'bgfv', '2024-11-05', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(7, 19, 'fgr', 'gf', '2024-11-05', 'gf', 'gfb', '2024-11-05', 'gfb', 'gf', '2024-11-05', 'gf', 'gf', '2024-11-05', '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_signatures`
--
ALTER TABLE `performance_review_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_signatures_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_signatures`
--
ALTER TABLE `performance_review_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_signatures`
--
ALTER TABLE `performance_review_signatures`
  ADD CONSTRAINT `performance_review_signatures_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
