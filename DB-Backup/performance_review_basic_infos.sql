-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 10:32 AM
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
-- Table structure for table `performance_review_basic_infos`
--

CREATE TABLE `performance_review_basic_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_name` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `designation_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `date_of_join` date NOT NULL,
  `ro_name` varchar(255) NOT NULL,
  `ro_designation` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_basic_infos`
--

INSERT INTO `performance_review_basic_infos` (`id`, `employee_name`, `employee_id`, `designation_id`, `department_id`, `date_of_join`, `ro_name`, `ro_designation`, `created_at`, `updated_at`, `deleted_at`, `status`) VALUES
(10, 3, 'AP-0006', 3, 3, '2024-10-10', 'fee', 'rgferge', '2024-11-03 23:00:36', '2024-11-03 23:00:36', 0, 'active'),
(12, 2, 'IT-0002', 2, 4, '2024-10-18', 'gdhv', 'bdfdf', '2024-11-04 01:49:38', '2024-11-04 01:49:38', 0, 'active'),
(13, 5, 'IT-0007', 5, 4, '2024-10-24', 'hh', 'hh', '2024-11-04 02:56:43', '2024-11-04 02:56:43', 0, 'active'),
(14, 1, 'WE-0001', 1, 2, '2024-10-17', 'dd', 'ddf', '2024-11-04 06:42:53', '2024-11-04 06:42:53', 0, 'active'),
(15, 3, 'AP-0006', 3, 3, '2024-10-10', 'fsfdg', 'fdfg', '2024-11-04 21:32:22', '2024-11-04 21:32:22', 0, 'active'),
(16, 1, 'WE-0001', 1, 2, '2024-10-17', 'fh', 'vvv', '2024-11-05 04:41:39', '2024-11-05 04:41:39', 0, 'active'),
(19, 5, 'IT-0007', 5, 4, '2024-10-24', 'gfghb', 'ghg', '2024-11-05 05:11:01', '2024-11-05 05:11:01', 0, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_basic_infos`
--
ALTER TABLE `performance_review_basic_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_basic_infos_employee_name_foreign` (`employee_name`),
  ADD KEY `performance_review_basic_infos_designation_id_foreign` (`designation_id`),
  ADD KEY `performance_review_basic_infos_department_id_foreign` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_basic_infos`
--
ALTER TABLE `performance_review_basic_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_basic_infos`
--
ALTER TABLE `performance_review_basic_infos`
  ADD CONSTRAINT `performance_review_basic_infos_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
  ADD CONSTRAINT `performance_review_basic_infos_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designation` (`id`),
  ADD CONSTRAINT `performance_review_basic_infos_employee_name_foreign` FOREIGN KEY (`employee_name`) REFERENCES `allemployees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
