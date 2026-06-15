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
-- Table structure for table `performance_review_professional_excellences`
--

CREATE TABLE `performance_review_professional_excellences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `key_result_area` varchar(255) DEFAULT NULL,
  `key_performance_indicator` varchar(255) DEFAULT NULL,
  `weightage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `percentage_self` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_self` decimal(5,2) NOT NULL DEFAULT 0.00,
  `percentage_ro` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_ro` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_percentage_self` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_points_self` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_percentage_ro` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_points_ro` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_professional_excellences`
--

INSERT INTO `performance_review_professional_excellences` (`id`, `review_id`, `key_result_area`, `key_performance_indicator`, `weightage`, `percentage_self`, `points_self`, `percentage_ro`, `points_ro`, `total_percentage_self`, `total_points_self`, `total_percentage_ro`, `total_points_ro`, `created_at`, `updated_at`) VALUES
(37, 10, 'Production', 'Quality', 30.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(38, 10, 'Production', 'TAT', 30.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(39, 10, 'Process Improvement', 'PMS, New Ideas', 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(40, 10, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(41, 10, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(42, 10, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(49, 12, 'Production', 'Quality', 30.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:38', '2024-11-04 01:49:38'),
(50, 12, 'Production', 'TAT', 30.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:38', '2024-11-04 01:49:38'),
(51, 12, 'Process Improvement', 'PMS, New Ideas', 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:38', '2024-11-04 01:49:38'),
(52, 12, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:38', '2024-11-04 01:49:38'),
(53, 12, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:38', '2024-11-04 01:49:38'),
(54, 12, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:38', '2024-11-04 01:49:38'),
(55, 13, 'Production', 'Quality', 30.00, 55.00, 16.50, 55.00, 16.50, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(56, 13, 'Production', 'TAT', 30.00, 55.00, 16.50, 55.00, 16.50, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(57, 13, 'Process Improvement', 'PMS, New Ideas', 10.00, 55.00, 5.50, 55.00, 5.50, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(58, 13, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 55.00, 2.75, 55.00, 2.75, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(59, 13, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 55.00, 2.75, 55.00, 2.75, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(60, 13, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 55.00, 2.75, 55.00, 2.75, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(61, 14, 'Production', 'Quality', 30.00, 33.00, 9.90, 33.00, 9.90, 198.00, 0.00, 209.00, 0.00, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(62, 14, 'Production', 'TAT', 30.00, 33.00, 9.90, 33.00, 9.90, 198.00, 0.00, 209.00, 0.00, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(63, 14, 'Process Improvement', 'PMS, New Ideas', 10.00, 33.00, 3.30, 33.00, 3.30, 198.00, 0.00, 209.00, 0.00, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(64, 14, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 33.00, 1.65, 33.00, 1.65, 198.00, 0.00, 209.00, 0.00, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(65, 14, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 33.00, 1.65, 33.00, 1.65, 198.00, 0.00, 209.00, 0.00, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(66, 14, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 33.00, 1.65, 44.00, 2.20, 198.00, 0.00, 209.00, 0.00, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(67, 15, 'Production', 'Quality', 30.00, 88.00, 26.40, 88.00, 26.40, 6.21, 74.80, 6.21, 74.80, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(68, 15, 'Production', 'TAT', 30.00, 88.00, 26.40, 88.00, 26.40, 6.21, 74.80, 6.21, 74.80, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(69, 15, 'Process Improvement', 'PMS, New Ideas', 10.00, 88.00, 8.80, 88.00, 8.80, 6.21, 74.80, 6.21, 74.80, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(70, 15, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 88.00, 4.40, 88.00, 4.40, 6.21, 74.80, 6.21, 74.80, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(71, 15, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 88.00, 4.40, 88.00, 4.40, 6.21, 74.80, 6.21, 74.80, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(72, 15, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 88.00, 4.40, 88.00, 4.40, 6.21, 74.80, 6.21, 74.80, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(73, 16, 'Production', 'Quality', 30.00, 99.00, 29.70, 99.00, 29.70, 99.00, 84.15, 99.00, 84.15, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(74, 16, 'Production', 'TAT', 30.00, 99.00, 29.70, 99.00, 29.70, 99.00, 84.15, 99.00, 84.15, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(75, 16, 'Process Improvement', 'PMS, New Ideas', 10.00, 99.00, 9.90, 99.00, 9.90, 99.00, 84.15, 99.00, 84.15, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(76, 16, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 99.00, 4.95, 99.00, 4.95, 99.00, 84.15, 99.00, 84.15, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(77, 16, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 99.00, 4.95, 99.00, 4.95, 99.00, 84.15, 99.00, 84.15, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(78, 16, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 99.00, 4.95, 99.00, 4.95, 99.00, 84.15, 99.00, 84.15, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(85, 19, 'Production', 'Quality', 30.00, 56.00, 16.80, 85.00, 25.50, 56.29, 47.85, 85.35, 72.55, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(86, 19, 'Production', 'TAT', 30.00, 52.00, 15.60, 85.00, 25.50, 56.29, 47.85, 85.35, 72.55, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(87, 19, 'Process Improvement', 'PMS, New Ideas', 10.00, 55.00, 5.50, 85.00, 8.50, 56.29, 47.85, 85.35, 72.55, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(88, 19, 'Team Management', 'Team Productivity, Dynamics, Attendance, Attrition', 5.00, 56.00, 2.80, 85.00, 4.25, 56.29, 47.85, 85.35, 72.55, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(89, 19, 'Knowledge Sharing', 'Sharing the Knowledge for Team Productivity', 5.00, 55.00, 2.75, 88.00, 4.40, 56.29, 47.85, 85.35, 72.55, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(90, 19, 'Reporting and Communication', 'Emails, Calls, Reports, and Other Communication', 5.00, 88.00, 4.40, 88.00, 4.40, 56.29, 47.85, 85.35, 72.55, '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_professional_excellences`
--
ALTER TABLE `performance_review_professional_excellences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_professional_excellences_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_professional_excellences`
--
ALTER TABLE `performance_review_professional_excellences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_professional_excellences`
--
ALTER TABLE `performance_review_professional_excellences`
  ADD CONSTRAINT `performance_review_professional_excellences_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
