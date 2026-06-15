-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 06:19 PM
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
-- Table structure for table `trainings`
--

CREATE TABLE `trainings` (
  `id` int(11) NOT NULL,
  `training_type_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `employees` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `training_cost` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainings`
--

INSERT INTO `trainings` (`id`, `training_type_id`, `trainer_id`, `employees`, `training_cost`, `start_date`, `end_date`, `description`, `status`, `created_at`, `updated_at`) VALUES
(4, 2, 10, '3', 400.00, '2024-10-11', '2024-10-15', 'testing', 'Inactive', '2024-10-15 16:10:09', '2024-10-19 09:31:10'),
(5, 2, 2, '3', 500.00, '2024-10-20', '2024-10-20', 'tesing', 'Active', '2024-10-15 16:11:35', '2024-10-16 03:58:43'),
(6, 2, 4, '1', 900.00, '2024-10-19', '2024-10-12', 'Testing', 'Inactive', '2024-10-15 16:16:54', '2024-10-16 03:58:53'),
(8, 2, 2, '2', 400.00, '2024-10-13', '2024-10-12', 'Testing', 'Active', '2024-10-15 16:23:30', '2024-10-19 09:31:30'),
(9, 2, 2, '2', 400.00, '2024-10-19', '2024-10-31', 'Testing', 'Active', '2024-10-15 16:28:04', '2024-10-16 03:59:19'),
(10, 4, 2, '2', 900.00, '2024-10-04', '2024-10-10', 'Testing', 'Active', '2024-10-15 16:37:22', '2024-10-16 03:59:26'),
(11, 2, 2, '1', 900.00, '2024-10-19', '2024-10-31', 'Testing', 'Active', '2024-10-15 16:38:50', '2024-10-16 03:59:46'),
(15, 4, 2, '3', 400.00, '2024-10-04', '2024-10-11', 'Good Bye', 'Inactive', '2024-10-15 18:10:30', '2024-10-15 18:10:30'),
(16, 5, 10, '3', 1000.00, '2024-10-05', '2024-10-31', 'Testing', 'Inactive', '2024-10-16 05:22:14', '2024-10-16 05:22:14'),
(17, 2, 2, '2', 1000.00, '2024-10-20', '2024-10-31', 'Testing', 'Active', '2024-10-19 09:28:17', '2024-10-19 09:28:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
