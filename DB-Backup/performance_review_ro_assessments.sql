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
-- Table structure for table `performance_review_ro_assessments`
--

CREATE TABLE `performance_review_ro_assessments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `work_issues` varchar(255) NOT NULL,
  `work_issues_details` text DEFAULT NULL,
  `leave_issues` varchar(255) NOT NULL,
  `leave_issues_details` text DEFAULT NULL,
  `stability_issues` varchar(255) NOT NULL,
  `stability_issues_details` text DEFAULT NULL,
  `attitude_issues` varchar(255) NOT NULL,
  `attitude_issues_details` text DEFAULT NULL,
  `other_issues` varchar(255) NOT NULL,
  `other_issues_details` text DEFAULT NULL,
  `overall_performance` varchar(255) NOT NULL,
  `overall_performance_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_ro_assessments`
--

INSERT INTO `performance_review_ro_assessments` (`id`, `review_id`, `work_issues`, `work_issues_details`, `leave_issues`, `leave_issues_details`, `stability_issues`, `stability_issues_details`, `attitude_issues`, `attitude_issues_details`, `other_issues`, `other_issues_details`, `overall_performance`, `overall_performance_details`, `created_at`, `updated_at`) VALUES
(4, 10, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'No', NULL, 'No', NULL, 'Good', NULL, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(6, 12, 'Yes', 'thty', 'No', 'hyhy', 'Yes', 'hyhy', 'Yes', 'hhtht', 'Yes', 'hy', 'Excellent', 'hyht', '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(7, 13, 'Yes', NULL, 'No', NULL, 'Yes', NULL, 'Yes', NULL, 'Yes', NULL, 'Excellent', NULL, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(8, 14, 'No', 'ghdgh', 'Yes', 'ghdghd', 'Yes', 'ghdgh', 'Yes', 'ghhg', 'Yes', 'ghgh', 'Excellent', 'ghgh', '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(9, 15, 'Yes', 'fddf', 'Yes', 'df', 'Yes', 'dfgf', 'Yes', 'gfgf', 'Yes', 'fgf', 'Excellent', 'gfgf', '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(10, 16, 'Yes', 'dfs', 'Yes', 'df', 'No', 'dfzs', 'Yes', 'dfs', 'Yes', 'dfs', 'Good', 'dfsz', '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(12, 19, 'Yes', 'gtf', 'No', 'gf', 'Yes', 'gfv', 'No', 'f', 'Yes', 'gf', 'Excellent', 'bgf', '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_ro_assessments`
--
ALTER TABLE `performance_review_ro_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_ro_assessments_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_ro_assessments`
--
ALTER TABLE `performance_review_ro_assessments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_ro_assessments`
--
ALTER TABLE `performance_review_ro_assessments`
  ADD CONSTRAINT `performance_review_ro_assessments_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
