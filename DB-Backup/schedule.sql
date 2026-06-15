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
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `repeat_every_week` int(11) NOT NULL,
  `accept_extra_hours` tinyint(1) NOT NULL DEFAULT 0,
  `publish` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `department_id`, `employee_id`, `shift_id`, `repeat_every_week`, `accept_extra_hours`, `publish`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 1, 2, 0, 0, 1, '2024-10-10 07:25:25', '2024-10-10 07:25:25'),
(2, 3, 5, 3, 5, 0, 0, 1, '2024-10-10 07:27:36', '2024-10-12 02:06:17'),
(3, 1, 1, 3, 3, 0, 0, 1, '2024-10-10 07:35:20', '2024-10-10 07:35:20'),
(5, 5, 2, 4, 3, 1, 1, 1, '2024-10-10 07:47:02', '2024-10-10 07:47:02'),
(6, 4, 2, 4, 1, 1, 0, 0, '2024-10-12 02:09:29', '2024-10-12 02:09:29'),
(7, 5, 5, 2, 2, 0, 0, 0, '2024-10-12 02:23:50', '2024-10-14 01:13:27'),
(8, 2, 1, 1, 3, 0, 0, 0, '2024-10-14 00:26:47', '2024-10-14 01:27:30'),
(9, 1, 3, 3, 4, 0, 0, 0, '2024-10-14 00:27:40', '2024-10-14 00:27:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
