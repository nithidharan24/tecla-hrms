-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 10:55 AM
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
-- Table structure for table `managejobs`
--

CREATE TABLE `managejobs` (
  `id` int(11) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `job_location` varchar(255) DEFAULT NULL,
  `vacancies` int(11) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `salary_from` int(255) DEFAULT NULL,
  `salary_to` int(255) DEFAULT NULL,
  `job_type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` int(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managejobs`
--

INSERT INTO `managejobs` (`id`, `job_title`, `department`, `job_location`, `vacancies`, `experience`, `age`, `salary_from`, `salary_to`, `job_type`, `status`, `start_date`, `end_date`, `description`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 'Wen designer', 'Web Development', 'chennai', 7, '1year', 20, 20000, 50000, 'Full Time', 'Open', '2024-11-03', '2024-11-04', 'qwerwegtr', '2024-11-03 12:23:04', 0, '2024-11-03 12:23:04'),
(2, 'Android designer', 'Application Development', 'chennai', 45, '1year', 24, 30000, 70000, 'Full Time', 'Open', '2024-11-07', '2024-12-07', 'sfjhnghkjhu', '2024-11-03 12:24:13', 0, '2024-11-03 12:24:13'),
(3, 'Advertising', 'Marketing', 'chennai', 15, '0years', 20, 12000, 40000, 'Part Time', 'Open', '2024-11-04', '2024-11-04', 'fbgfngjukhul', '2024-11-03 12:26:25', 0, '2024-11-03 12:26:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `managejobs`
--
ALTER TABLE `managejobs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `managejobs`
--
ALTER TABLE `managejobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
