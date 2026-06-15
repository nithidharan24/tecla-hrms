-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 04:09 PM
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
-- Table structure for table `goal_tracks`
--

CREATE TABLE `goal_tracks` (
  `id` int(11) NOT NULL,
  `goal` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `achievement` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `progress` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `goal_tracks`
--

INSERT INTO `goal_tracks` (`id`, `goal`, `subject`, `achievement`, `description`, `progress`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, '2', 'Increase Production', 'Reach 1 Lakh Product', 'This is the statement given by production manager', 35, '2024-10-09', '2024-10-31', 'active', '2024-10-09 14:14:29', '2024-10-09 14:14:29', 0),
(4, '3', 'Inrease Sales', 'Reach 1 Lakh Sales in this Month', 'Given By Manager', 55, '2024-10-09', '2024-10-24', 'active', '2024-10-09 14:18:07', '2024-10-09 14:18:07', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `goal_tracks`
--
ALTER TABLE `goal_tracks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `goal_tracks`
--
ALTER TABLE `goal_tracks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
