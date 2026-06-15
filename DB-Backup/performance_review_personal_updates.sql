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
-- Table structure for table `performance_review_personal_updates`
--

CREATE TABLE `performance_review_personal_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `married_last_year` varchar(255) DEFAULT NULL,
  `married_last_year_details` text DEFAULT NULL,
  `marriage_plans` varchar(255) DEFAULT NULL,
  `marriage_plans_details` text DEFAULT NULL,
  `studies_last_year` varchar(255) DEFAULT NULL,
  `studies_last_year_details` text DEFAULT NULL,
  `study_plans` varchar(255) DEFAULT NULL,
  `study_plans_details` text DEFAULT NULL,
  `health_issues_last_year` varchar(255) DEFAULT NULL,
  `health_issues_last_year_details` text DEFAULT NULL,
  `certification_plans` varchar(255) DEFAULT NULL,
  `certification_plans_details` text DEFAULT NULL,
  `others_last_year` varchar(255) DEFAULT NULL,
  `others_last_year_details` text DEFAULT NULL,
  `others_current_year` varchar(255) DEFAULT NULL,
  `others_current_year_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_personal_updates`
--

INSERT INTO `performance_review_personal_updates` (`id`, `review_id`, `married_last_year`, `married_last_year_details`, `marriage_plans`, `marriage_plans_details`, `studies_last_year`, `studies_last_year_details`, `study_plans`, `study_plans_details`, `health_issues_last_year`, `health_issues_last_year_details`, `certification_plans`, `certification_plans_details`, `others_last_year`, `others_last_year_details`, `others_current_year`, `others_current_year_details`, `created_at`, `updated_at`) VALUES
(6, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(8, 12, 'Yes', 'dfdf', 'Yes', 'dfdf', 'Yes', 'dfdf', 'Yes', 'dfdf', 'Yes', 'dffd', 'Yes', 'dfdf', 'Yes', 'fddf', 'Yes', 'dfdf', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(9, 13, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'No', NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(10, 14, 'Yes', 'hh', 'Yes', 'gh', 'No', 'hhg', 'Yes', 'ghgh', 'Yes', 'ghg', 'Yes', 'gh', 'Yes', 'ghg', 'Yes', 'gh', '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(11, 15, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(12, 16, 'Yes', 'bn nb', 'Yes', 'b', 'Yes', 'bn', 'Yes', 'nb', 'Yes', 'bn', 'No', 'nb', 'No', 'bn', 'Yes', 'bn', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(14, 19, 'Yes', 'dfs', 'No', 'g', 'Yes', 'dfs', 'No', 'bgv', 'Yes', 'dc', 'No', 'gbv', 'Yes', 'cx', 'No', 'bgv', '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_personal_updates`
--
ALTER TABLE `performance_review_personal_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_personal_updates_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_personal_updates`
--
ALTER TABLE `performance_review_personal_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_personal_updates`
--
ALTER TABLE `performance_review_personal_updates`
  ADD CONSTRAINT `performance_review_personal_updates_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
