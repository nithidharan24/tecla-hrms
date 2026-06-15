-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 05:28 PM
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
-- Table structure for table `resignation`
--

CREATE TABLE `resignation` (
  `id` int(11) NOT NULL,
  `resignation_employee_id` int(11) NOT NULL,
  `notice_date` date NOT NULL,
  `resignation_date` date NOT NULL,
  `reason` varchar(255) NOT NULL,
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resignation`
--

INSERT INTO `resignation` (`id`, `resignation_employee_id`, `notice_date`, `resignation_date`, `reason`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 3, '2024-10-14', '2024-10-30', 'performance low', 0, '2024-10-15 05:26:40', '2024-10-15 05:45:01'),
(2, 1, '2024-10-13', '2024-11-08', 'poor performance', 0, '2024-10-15 05:57:13', '2024-10-15 05:57:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resignation`
--
ALTER TABLE `resignation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `resignation`
--
ALTER TABLE `resignation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
