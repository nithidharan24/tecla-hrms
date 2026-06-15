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
-- Table structure for table `performance_review_personal_excellences`
--

CREATE TABLE `performance_review_personal_excellences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `personal_attribute` varchar(255) DEFAULT NULL,
  `key_indicator` varchar(255) DEFAULT NULL,
  `weightage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `percentage_self` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_self` decimal(5,2) NOT NULL DEFAULT 0.00,
  `percentage_ro` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_ro` decimal(5,2) NOT NULL DEFAULT 0.00,
  `percentage_self_total` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_self_total` decimal(5,2) NOT NULL DEFAULT 0.00,
  `percentage_ro_total` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_ro_total` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_review_personal_excellences`
--

INSERT INTO `performance_review_personal_excellences` (`id`, `review_id`, `personal_attribute`, `key_indicator`, `weightage`, `percentage_self`, `points_self`, `percentage_ro`, `points_ro`, `percentage_self_total`, `points_self_total`, `percentage_ro_total`, `points_ro_total`, `total_percentage`, `created_at`, `updated_at`) VALUES
(43, 10, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(44, 10, 'Attendance', 'Time Consciousness', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(45, 10, 'Attitude & Behavior', 'Team Collaboration', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(46, 10, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(47, 10, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(48, 10, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(49, 10, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-03 23:00:36', '2024-11-03 23:00:36'),
(57, 12, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(58, 12, 'Attendance', 'Time Consciousness', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(59, 12, 'Attitude & Behavior', 'Team Collaboration', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(60, 12, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(61, 12, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(62, 12, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(63, 12, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 01:49:39', '2024-11-04 01:49:39'),
(64, 13, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(65, 13, 'Attendance', 'Time Consciousness', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(66, 13, 'Attitude & Behavior', 'Team Collaboration', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(67, 13, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(68, 13, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(69, 13, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(70, 13, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-04 02:56:43', '2024-11-04 02:56:43'),
(71, 14, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 66.00, 1.32, 66.00, 1.32, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(72, 14, 'Attendance', 'Time Consciousness', 2.00, 66.00, 1.32, 66.00, 1.32, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(73, 14, 'Attitude & Behavior', 'Team Collaboration', 2.00, 66.00, 1.32, 66.00, 1.32, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(74, 14, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 66.00, 1.32, 66.00, 1.32, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(75, 14, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 66.00, 1.32, 66.00, 1.32, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(76, 14, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 66.00, 1.32, 66.00, 1.32, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(77, 14, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 66.00, 1.98, 66.00, 1.98, 462.00, 9.90, 462.00, 9.90, 9.90, '2024-11-04 06:42:53', '2024-11-04 06:42:53'),
(78, 15, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 88.00, 1.76, 88.00, 1.76, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(79, 15, 'Attendance', 'Time Consciousness', 2.00, 88.00, 1.76, 88.00, 1.76, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(80, 15, 'Attitude & Behavior', 'Team Collaboration', 2.00, 88.00, 1.76, 88.00, 1.76, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(81, 15, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 88.00, 1.76, 100.00, 2.00, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(82, 15, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 88.00, 1.76, 88.00, 1.76, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(83, 15, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 88.00, 1.76, 88.00, 1.76, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(84, 15, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 88.00, 2.64, 88.00, 2.64, 616.00, 13.20, 628.00, 13.44, 88.00, '2024-11-04 21:32:22', '2024-11-04 21:32:22'),
(85, 16, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 63.00, 1.26, 63.00, 1.26, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(86, 16, 'Attendance', 'Time Consciousness', 2.00, 63.00, 1.26, 36.00, 0.72, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(87, 16, 'Attitude & Behavior', 'Team Collaboration', 2.00, 63.00, 1.26, 36.00, 0.72, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(88, 16, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 36.00, 0.72, 36.00, 0.72, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(89, 16, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 63.00, 1.26, 63.00, 1.26, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(90, 16, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 36.00, 0.72, 36.00, 0.72, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(91, 16, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 36.00, 1.08, 63.00, 1.89, 360.00, 7.56, 333.00, 7.29, 50.40, '2024-11-05 04:41:39', '2024-11-05 04:41:39'),
(99, 19, 'Attendance', 'Planned or Unplanned Leaves', 2.00, 74.00, 1.48, 74.00, 1.48, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(100, 19, 'Attendance', 'Time Consciousness', 2.00, 74.00, 1.48, 74.00, 1.48, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(101, 19, 'Attitude & Behavior', 'Team Collaboration', 2.00, 74.00, 1.48, 74.00, 1.48, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(102, 19, 'Attitude & Behavior', 'Professionalism & Responsiveness', 2.00, 74.00, 1.48, 74.00, 1.48, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(103, 19, 'Policy & Procedures', 'Adherence to policies and procedures', 2.00, 74.00, 1.48, 77.00, 1.54, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(104, 19, 'Initiatives', 'Special Efforts, Suggestions, Ideas, etc.', 2.00, 74.00, 1.48, 77.00, 1.54, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01'),
(105, 19, 'Continuous Skill Improvement', 'Preparedness to move to next level & Training utilization', 3.00, 74.00, 2.22, 77.00, 2.31, 518.00, 11.10, 527.00, 11.31, 74.00, '2024-11-05 05:11:01', '2024-11-05 05:11:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_review_personal_excellences`
--
ALTER TABLE `performance_review_personal_excellences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_personal_excellences_review_id_foreign` (`review_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_review_personal_excellences`
--
ALTER TABLE `performance_review_personal_excellences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `performance_review_personal_excellences`
--
ALTER TABLE `performance_review_personal_excellences`
  ADD CONSTRAINT `performance_review_personal_excellences_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `performance_review_basic_infos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
