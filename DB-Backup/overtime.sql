-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 06:17 PM
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
-- Database: `hrms_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `overtime`
--

CREATE TABLE `overtime` (
  `id` int(255) NOT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `overtime_date` date DEFAULT NULL,
  `overtime_hours` int(255) DEFAULT NULL,
  `overtime_type` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('Approved','Rejected','Pending') DEFAULT 'Approved',
  `approved_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `deleted_at` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `overtime`
--

INSERT INTO `overtime` (`id`, `employee_name`, `overtime_date`, `overtime_hours`, `overtime_type`, `description`, `status`, `approved_by`, `updated_at`, `created_at`, `deleted_at`) VALUES
(34, 'John Doe', '2024-10-03', 1, 'Rest day OT 2.0x', 'For Complete my Project', 'Pending', 'HR Team', '2024-10-06 08:20:25.000000', '2024-10-05 11:17:15.000000', 1),
(35, 'Jane Smith', '2024-10-13', 5, 'Rest day OT 2.0x', 'Starting New Project', 'Approved', 'HR Team', '2024-10-06 08:33:12.000000', '2024-10-05 11:18:25.000000', 0),
(37, 'John Doe', '2024-10-26', 2, 'Normal day OT 1.5x', 'For Salary', 'Pending', 'Manager A', '2024-10-06 08:33:42.000000', '2024-10-06 07:13:38.000000', 0),
(38, 'Jane Smith', '2024-10-20', 3, 'Normal day OT 1.5x', 'For Success Message', 'Rejected', 'HR Team', '2024-10-06 08:21:01.000000', '2024-10-06 07:21:46.000000', 1),
(40, 'John Doe', '2024-10-26', 5, 'Normal day OT 1.5x', 'Testing', 'Rejected', 'HR Team', '2024-10-06 08:20:47.000000', '2024-10-06 07:28:09.000000', 1),
(45, 'Jane Smith', '2024-10-22', 5, 'Normal day OT 1.5x', 'For Development', 'Pending', 'HR Team', '2024-10-06 08:36:14.000000', '2024-10-06 08:35:39.000000', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `overtime`
--
ALTER TABLE `overtime`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `overtime`
--
ALTER TABLE `overtime`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
