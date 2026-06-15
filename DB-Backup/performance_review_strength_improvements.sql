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
-- Table structure for table `performance_review_strength_improvements`
--

CREATE TABLE `performance_review_strength_improvements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `strength` varchar(255) DEFAULT NULL,
  `improvement` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_strength_improvements`
--

INSERT INTO `performance_review_strength_improvements` (`id`, `review_id`, `strength`, `improvement`, `created_at`, `updated_at`) VALUES
(26, 10, NULL, NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(27, 10, NULL, NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(28, 10, NULL, NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(29, 10, NULL, NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(30, 10, NULL, NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(36, 12, 'fgfg', 'fddf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(37, 12, 'gfgf', 'gffd', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(38, 12, 'gffd', 'fdf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(39, 12, 'gffg', 'dffd', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(40, 12, 'gfgf', 'dfdf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(41, 13, NULL, NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(42, 13, NULL, NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(43, 13, NULL, NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(44, 13, NULL, NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(45, 13, NULL, NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(46, 14, NULL, NULL, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(47, 14, NULL, NULL, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(48, 14, NULL, NULL, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(49, 14, NULL, NULL, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(50, 14, NULL, NULL, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(51, 15, NULL, NULL, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(52, 15, NULL, NULL, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(53, 15, NULL, NULL, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(54, 15, NULL, NULL, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(55, 15, NULL, NULL, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(56, 16, 'n bh', 'nh', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(57, 16, 'n bh', 'nh', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(58, 16, 'n bh', 'n', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(59, 16, 'n', 'n', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(60, 16, 'n b', 'n b', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(66, 19, NULL, NULL, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(67, 19, NULL, NULL, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(68, 19, NULL, NULL, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(69, 19, NULL, NULL, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(70, 19, NULL, NULL, '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_strength_improvements`
--
ALTER TABLE `performance_review_strength_improvements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_strength_improvements_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_strength_improvements`
--
ALTER TABLE `performance_review_strength_improvements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_strength_improvements`
--
ALTER TABLE `performance_review_strength_improvements`
  ADD CONSTRAINT `performance_review_strength_improvements_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
