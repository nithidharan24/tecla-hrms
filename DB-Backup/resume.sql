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
-- Table structure for table `resume`
--

CREATE TABLE `resume` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `job_type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `add_resume` varchar(5000) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` int(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resume`
--

INSERT INTO `resume` (`id`, `name`, `job_title`, `department`, `job_type`, `status`, `start_date`, `end_date`, `add_resume`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 'hugo rune', '1', 'Web Development', 'Full Time', 'Open', '2024-11-03', '2024-11-04', 'resumes/MxRZU2d2N93JVXmkkOev2HfZNIo76DdZCGOKi4wG.pdf', '2024-11-04 07:37:46', 0, '2024-11-04 07:37:46'),
(2, 'riyaz deen', '2', 'Application Development', 'Full Time', 'Open', '2024-11-07', '2024-12-07', 'resumes/QORxXHOmWR6wKTTVYeDozd3DlCedwCnv6POMkGfu.pdf', '2024-11-04 07:38:34', 0, '2024-11-04 07:38:34'),
(3, 'prem s', '3', 'Marketing', 'Part Time', 'Open', '2024-11-04', '2024-11-04', 'resumes/gTeXUCEOEghryQ4IvDvIdjS4ZmL6qk9rxcJGcDK5.pdf', '2024-11-04 07:38:51', 0, '2024-11-04 07:38:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resume`
--
ALTER TABLE `resume`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `resume`
--
ALTER TABLE `resume`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
