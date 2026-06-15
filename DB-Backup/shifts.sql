-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 05:29 PM
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
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `shift_name` varchar(255) NOT NULL,
  `min_start_time` time NOT NULL,
  `start_time` time NOT NULL,
  `max_start_time` time NOT NULL,
  `min_end_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_end_time` time NOT NULL,
  `break_time` int(11) DEFAULT NULL,
  `recurring_shift` tinyint(1) NOT NULL DEFAULT 0,
  `days_of_week` varchar(255) NOT NULL,
  `end_on` date DEFAULT NULL,
  `indefinite` tinyint(1) NOT NULL DEFAULT 0,
  `tag` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_name`, `min_start_time`, `start_time`, `max_start_time`, `min_end_time`, `end_time`, `max_end_time`, `break_time`, `recurring_shift`, `days_of_week`, `end_on`, `indefinite`, `tag`, `note`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Daily Shift', '05:30:00', '06:00:00', '06:30:00', '14:30:00', '15:00:00', '15:30:00', 30, 1, 'Mon,Tue,Wed', '2024-12-31', 0, NULL, NULL, 'active', 0, '2024-10-08 03:23:09', '2024-10-12 01:07:42'),
(2, 'Second Shift', '13:30:00', '14:00:00', '14:30:00', '22:30:00', '23:00:00', '23:30:00', 30, 1, 'Thu,Fri,Sat', '2024-12-31', 0, NULL, NULL, 'active', 0, '2024-10-08 04:46:05', '2024-10-12 01:08:02'),
(3, 'Night Shift', '21:30:00', '22:00:00', '22:30:00', '17:30:00', '18:00:00', '18:30:00', 60, 1, 'Mon,Tue,Wed,Thu,Fri,Sat', '2024-12-31', 0, NULL, NULL, 'active', 0, '2024-10-08 05:11:31', '2024-10-12 01:06:38'),
(4, 'General Shift', '08:30:00', '09:00:00', '09:30:00', '17:30:00', '18:00:00', '18:30:00', 60, 1, 'Mon,Tue,Wed,Thu,Fri,Sat', '2024-12-31', 0, NULL, NULL, 'active', 0, '2024-10-08 08:11:28', '2024-10-12 01:07:25'),
(5, 'new shift', '19:28:00', '19:28:00', '19:28:00', '19:28:00', '19:28:00', '19:28:00', NULL, 1, 'Mon,Tue,Wed,Fri', NULL, 0, NULL, NULL, 'active', 1, '2024-10-08 08:29:03', '2024-10-08 08:32:56'),
(6, 'shift 2', '17:44:00', '17:44:00', '17:44:00', '17:45:00', '17:45:00', '17:44:00', 45, 1, 'Tue,Wed,Thurs', '2024-10-21', 0, NULL, NULL, 'active', 1, '2024-10-09 04:57:00', '2024-10-09 06:45:09'),
(7, 'shift 3', '16:12:00', '16:12:00', '16:12:00', '16:12:00', '16:12:00', '16:12:00', 35, 1, 'Mon,Wed,Thurs,Fri,Sat', '2024-10-17', 0, NULL, NULL, 'active', 1, '2024-10-09 05:15:32', '2024-10-09 07:09:07'),
(8, 'shift 3', '18:08:00', '18:08:00', '18:08:00', '18:08:00', '18:08:00', '18:08:00', 25, 1, 'Mon,Thurs,Fri', '2024-10-25', 0, NULL, NULL, 'active', 1, '2024-10-09 07:08:50', '2024-10-09 07:08:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
