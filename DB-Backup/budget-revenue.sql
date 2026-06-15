-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2024 at 08:04 AM
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
-- Table structure for table `budget-revenue`
--

CREATE TABLE `budget-revenue` (
  `id` int(244) NOT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `categories` varchar(255) DEFAULT NULL,
  `sub-categories` varchar(255) DEFAULT NULL,
  `amount` int(25) DEFAULT NULL,
  `revenue-date` date NOT NULL DEFAULT current_timestamp(),
  `img` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget-revenue`
--

INSERT INTO `budget-revenue` (`id`, `Notes`, `categories`, `sub-categories`, `amount`, `revenue-date`, `img`, `created_at`, `updated_at`) VALUES
(1, 'Test', '7', '7', 2520, '2024-10-04', 'admin/assets/img/revenue/1728971676.png', '2024-10-15', '2024-10-15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budget-revenue`
--
ALTER TABLE `budget-revenue`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budget-revenue`
--
ALTER TABLE `budget-revenue`
  MODIFY `id` int(244) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
