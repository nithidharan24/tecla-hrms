-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 10:33 AM
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
-- Table structure for table `performance_review_professional_goals`
--

CREATE TABLE `performance_review_professional_goals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `goal_self` text NOT NULL,
  `goal_ro` text DEFAULT NULL,
  `goal_hod` text DEFAULT NULL,
  `is_last_year` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_professional_goals`
--

INSERT INTO `performance_review_professional_goals` (`id`, `review_id`, `goal_self`, `goal_ro`, `goal_hod`, `is_last_year`, `created_at`, `updated_at`) VALUES
(20, 12, 'hjhj', 'jhhj', 'jhhj', 1, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(21, 12, 'jhhj', 'hjhjj', 'hjhj', 1, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(22, 12, 'jhjh', 'hjhjh', 'jhhj', 1, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(23, 12, 'jhjhh', 'hjhj', 'jhhj', 1, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(24, 12, 'hjjhh', 'hjhj', 'jhhj', 1, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(25, 12, 'hjjh', 'hjjh', 'hjhj', 0, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(26, 12, 'jhjh', 'jhhj', 'jhhj', 0, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(27, 12, 'jhj', 'jhjh', 'hjhjh', 0, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(28, 12, 'hjhj', 'jhhj', 'hjhj', 0, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(29, 12, 'jhj', 'jhhj', 'jhhj', 0, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(30, 16, 'r', 'nb', 'r', 1, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(31, 16, 'r', 'bn', 'r', 1, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(32, 16, 'r', 'r', 'r', 1, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(33, 16, 'r', 'r', 'r', 1, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(34, 16, 'r', 'r', 'r', 1, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(35, 16, 'dsds', 'dfs', 'dfs', 0, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(36, 16, 'dsds', 'dfs', 'dfs', 0, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(37, 16, 'dsds', 'dfs', 'dfs', 0, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(38, 16, 'dfs', 'dfs', 'dfs', 0, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(39, 16, 'dfs', 'dfs', 'dfs', 0, '2024-11-05 04:41:39', '2024-11-05 04:41:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_professional_goals`
--
ALTER TABLE `performance_review_professional_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_professional_goals_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_professional_goals`
--
ALTER TABLE `performance_review_professional_goals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_professional_goals`
--
ALTER TABLE `performance_review_professional_goals`
  ADD CONSTRAINT `performance_review_professional_goals_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
