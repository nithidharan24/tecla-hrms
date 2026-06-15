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
-- Table structure for table `performance_review_hrd_assessments`
--

CREATE TABLE `performance_review_hrd_assessments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `kra_points_available` decimal(5,2) NOT NULL,
  `kra_points_scored` decimal(5,2) NOT NULL,
  `kra_comment` text DEFAULT NULL,
  `professional_points_available` decimal(5,2) NOT NULL,
  `professional_points_scored` decimal(5,2) NOT NULL,
  `professional_comment` text DEFAULT NULL,
  `personal_points_available` decimal(5,2) NOT NULL,
  `personal_points_scored` decimal(5,2) NOT NULL,
  `personal_comment` text DEFAULT NULL,
  `achievement_points_available` decimal(5,2) NOT NULL,
  `achievement_points_scored` decimal(5,2) NOT NULL,
  `achievement_comment` text DEFAULT NULL,
  `total_points_available` decimal(5,2) NOT NULL,
  `total_points_scored` decimal(5,2) NOT NULL,
  `total_comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_hrd_assessments`
--

INSERT INTO `performance_review_hrd_assessments` (`id`, `review_id`, `kra_points_available`, `kra_points_scored`, `kra_comment`, `professional_points_available`, `professional_points_scored`, `professional_comment`, `personal_points_available`, `personal_points_scored`, `personal_comment`, `achievement_points_available`, `achievement_points_scored`, `achievement_comment`, `total_points_available`, `total_points_scored`, `total_comment`, `created_at`, `updated_at`) VALUES
(1, 10, 22.00, 11.00, NULL, 22.00, 11.00, NULL, 22.00, 11.00, NULL, 22.00, 11.00, NULL, 10.00, 10.00, '10', '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(3, 12, 55.00, 22.00, 'gfg', 55.00, 22.00, 'ghgh', 55.00, 22.00, 'ghh', 55.00, 22.00, 'ghh', 100.00, 100.00, '200', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(4, 13, 1.00, 1.00, '1', 1.00, 1.00, '1', 1.00, 1.00, '1', 1.00, 1.00, '1', 222.00, 222.00, '222', '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(5, 14, 3.00, 3.00, NULL, 3.00, 3.00, NULL, 3.00, 3.00, NULL, 3.00, 3.00, NULL, 44.00, 44.00, '44', '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(6, 15, 10.00, 44.00, NULL, 23.00, 33.00, NULL, 32.00, 22.00, NULL, 24.00, 11.00, NULL, 150.00, 300.00, '400', '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(7, 16, 4.00, 4.00, NULL, 4.00, 44.00, NULL, 4.00, 4.00, NULL, 4.00, 4.00, NULL, 4.00, 4.00, '4', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(8, 19, 5.00, 5.00, NULL, 5.00, 5.00, NULL, 55.00, 5.00, NULL, 5.00, 5.00, NULL, 555.00, 554.00, '200', '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_hrd_assessments`
--
ALTER TABLE `performance_review_hrd_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_hrd_assessments_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_hrd_assessments`
--
ALTER TABLE `performance_review_hrd_assessments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_hrd_assessments`
--
ALTER TABLE `performance_review_hrd_assessments`
  ADD CONSTRAINT `performance_review_hrd_assessments_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
