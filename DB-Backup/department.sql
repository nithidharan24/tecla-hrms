-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
<<<<<<< HEAD
-- Generation Time: Oct 16, 2024 at 08:05 AM
=======
-- Generation Time: Oct 16, 2024 at 06:37 AM
>>>>>>> 2b38145beb90cc6a5358fc64a4ef1693e5f2a7d2
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
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department`, `created_at`, `updated_at`) VALUES
<<<<<<< HEAD
(1, 'Web Development', '2024-10-04', '2024-10-04'),
(2, 'Application Development', '2024-10-05', '2024-10-05'),
(3, 'IT Management', '2024-10-05', '2024-10-05'),
(4, 'Marketing', '2024-10-05', '2024-10-05'),
(5, 'Support Management', '2024-10-05', '2024-10-05'),
(6, 'Accounts Management', '2024-10-05', '2024-10-05');
=======
(1, 'App Development', '2024-10-04', '2024-10-04'),
(2, 'Web Design', '2024-10-05', '2024-10-05'),
(3, 'Application Development', '2024-10-05', '2024-10-05'),
(4, 'IT Management', '2024-10-05', '2024-10-05'),
(5, 'Accounts Management ', '2024-10-05', '2024-10-05'),
(6, 'Support Management', '2024-10-05', '2024-10-05'),
(7, 'Marketing', '2024-10-05', '2024-10-05');
>>>>>>> 2b38145beb90cc6a5358fc64a4ef1693e5f2a7d2

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
<<<<<<< HEAD
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
=======
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
>>>>>>> 2b38145beb90cc6a5358fc64a4ef1693e5f2a7d2
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
