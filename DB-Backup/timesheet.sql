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
-- Table structure for table `timesheet`
--

CREATE TABLE `timesheet` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `project_id` varchar(255) NOT NULL,
  `deadline` date NOT NULL,
  `total_hours` int(11) NOT NULL,
  `remaining_hours` int(11) NOT NULL,
  `assigned_date` date NOT NULL,
  `assigned_hours` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timesheet`
--

INSERT INTO `timesheet` (`id`, `employee_id`, `project_id`, `deadline`, `total_hours`, `remaining_hours`, `assigned_date`, `assigned_hours`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '1', 'PRO-0005', '2024-11-10', 360, 60, '2024-10-07', 300, 'smartphone management', 0, '2024-10-07 07:26:50', '2024-10-07 07:34:31'),
(2, '3', 'PRO-0002', '2024-10-30', 432, 57, '2024-10-07', 375, 'office management', 0, '2024-10-07 07:35:30', '2024-10-07 07:35:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `timesheet`
--
ALTER TABLE `timesheet`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `timesheet`
--
ALTER TABLE `timesheet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
