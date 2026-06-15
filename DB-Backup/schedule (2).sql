-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 10:56 AM
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
  `name` varchar(255) DEFAULT NULL,
  `selectdate1` date DEFAULT NULL,
  `selecttime1` time(6) DEFAULT NULL,
  `selectdate2` date DEFAULT NULL,
  `selecttime2` time(6) DEFAULT NULL,
  `selectdate3` date DEFAULT NULL,
  `selecttime3` time(6) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `name`, `selectdate1`, `selecttime1`, `selectdate2`, `selecttime2`, `selectdate3`, `selecttime3`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1', '2024-11-03', '20:59:00.000000', '2024-11-04', '20:59:00.000000', '2024-11-06', '21:59:00.000000', '2024-11-03 15:30:06', '2024-11-03 15:30:06', 0),
(4, '2', '2024-11-03', '21:11:00.000000', '2024-11-12', '21:13:00.000000', '2024-11-15', '13:12:00.000000', '2024-11-03 15:42:10', '2024-11-03 15:42:10', 0),
(5, '3', '2024-11-20', '13:12:00.000000', '2024-11-23', '12:12:00.000000', '2024-11-30', '22:12:00.000000', '2024-11-03 15:42:37', '2024-11-03 15:42:37', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
