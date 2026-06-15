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
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `department_id` varchar(255) DEFAULT NULL,
  `created_it` date NOT NULL DEFAULT current_timestamp(),
  `updated_it` date NOT NULL DEFAULT current_timestamp(),
  `deleted_at` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `designation`
--

INSERT INTO `designation` (`id`, `designation`, `department_id`, `created_it`, `updated_it`, `deleted_at`) VALUES
<<<<<<< HEAD
(1, 'Web Designer', '1', '2024-10-04', '2024-10-04', '0'),
(2, 'Web Developer', '1', '2024-10-04', '2024-10-04', '0'),
(3, 'Android Developer', '1', '2024-10-04', '2024-10-04', '0'),
(4, 'IOS Developer', '2', '2024-10-04', '2024-10-04', '0'),
(11, 'Developer', '5', '2024-10-05', '2024-10-05', '0');
=======
(1, 'Web Designer', '1', '2024-10-05', '2024-10-05', '0'),
(2, 'Web Developer', '1', '2024-10-05', '2024-10-05', '0'),
(3, 'Android Developer', '2', '2024-10-05', '2024-10-05', '0'),
(4, 'IOS Developer', '2', '2024-10-05', '2024-10-05', '0'),
(5, 'UI Designer', '2', '2024-10-05', '2024-10-05', '0'),
(6, 'UX Designer', '2', '2024-10-05', '2024-10-05', '0'),
(7, 'IT Technician', '2', '2024-10-05', '2024-10-05', '0'),
(8, 'Product Manager', '3', '2024-10-05', '2024-10-05', '0'),
(9, 'SEO Analyst', '4', '2024-10-05', '2024-10-05', '0'),
(10, 'Front End Designer', '5', '2024-10-05', '2024-10-05', '0');
>>>>>>> 2b38145beb90cc6a5358fc64a4ef1693e5f2a7d2

--
-- Indexes for dumped tables
--

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
<<<<<<< HEAD
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
=======
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
>>>>>>> 2b38145beb90cc6a5358fc64a4ef1693e5f2a7d2
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
