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
-- Table structure for table `performance_review_training_requirements`
--

CREATE TABLE `performance_review_training_requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `training_self` text NOT NULL,
  `training_ro` text DEFAULT NULL,
  `training_hod` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_training_requirements`
--

INSERT INTO `performance_review_training_requirements` (`id`, `review_id`, `training_self`, `training_ro`, `training_hod`, `created_at`, `updated_at`) VALUES
(6, 12, 'gfgf', 'gfgf', 'ggfgf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(7, 12, 'ggf', 'gg', 'gfgf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(8, 12, 'gfg', 'gg', 'gfgf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(9, 12, 'ggf', 'ggx', 'ggf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(10, 12, 'gg', 'gfgf', 'gfgf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(11, 16, 'dfs', 'dfs', 'dfs', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(12, 16, 'dfs', 'dfs', 'dfs', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(13, 16, 'dfs', 'dfs', 'dfs', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(14, 16, 'df', 'dfs', 'dfs', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(15, 16, 'df', 'dfs', 'dfs', '2024-11-05 04:41:39', '2024-11-05 04:41:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_training_requirements`
--
ALTER TABLE `performance_review_training_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_training_requirements_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_training_requirements`
--
ALTER TABLE `performance_review_training_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_training_requirements`
--
ALTER TABLE `performance_review_training_requirements`
  ADD CONSTRAINT `performance_review_training_requirements_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
