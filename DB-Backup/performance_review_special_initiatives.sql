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
-- Table structure for table `performance_review_special_initiatives`
--

CREATE TABLE `performance_review_special_initiatives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `achievement_self` text NOT NULL,
  `achievement_ro` text DEFAULT NULL,
  `achievement_hod` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_special_initiatives`
--

INSERT INTO `performance_review_special_initiatives` (`id`, `review_id`, `achievement_self`, `achievement_ro`, `achievement_hod`, `created_at`, `updated_at`) VALUES
(3, 12, 'cbcbcv', 'gggv', 'ggb', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(4, 16, 'mhvnhc', 'hncghnj', 'hnvj', '2024-11-05 04:41:39', '2024-11-05 04:41:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_special_initiatives`
--
ALTER TABLE `performance_review_special_initiatives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_special_initiatives_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_special_initiatives`
--
ALTER TABLE `performance_review_special_initiatives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_special_initiatives`
--
ALTER TABLE `performance_review_special_initiatives`
  ADD CONSTRAINT `performance_review_special_initiatives_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
