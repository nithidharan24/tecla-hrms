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
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(244) NOT NULL,
  `budget_title` varchar(255) DEFAULT NULL,
  `budget_type` enum('project','category') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `revenue_title` varchar(244) DEFAULT NULL,
  `revenue_amount` varchar(244) DEFAULT NULL,
  `total_revenue` int(255) DEFAULT NULL,
  `expenses_title` varchar(244) DEFAULT NULL,
  `expenses_amount` varchar(244) DEFAULT NULL,
  `total_expenses` int(255) DEFAULT NULL,
  `expected_profit` int(244) DEFAULT NULL,
  `tax_amount` int(255) DEFAULT NULL,
  `budget_amount` int(255) DEFAULT NULL,
  `updated_at` date NOT NULL DEFAULT current_timestamp(),
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `budget_title`, `budget_type`, `start_date`, `end_date`, `revenue_title`, `revenue_amount`, `total_revenue`, `expenses_title`, `expenses_amount`, `total_expenses`, `expected_profit`, `tax_amount`, `budget_amount`, `updated_at`, `created_at`) VALUES
(2, 'ragul', 'category', '2024-10-01', '2024-11-01', '[\"dhyer\",\"dhyer\"]', '[\"10000\",\"10000\"]', 20000, '[\"rdyrt\"]', '[\"5500\"]', 5500, 14500, 100, 14400, '2024-10-15', '2024-10-14'),
(3, 'rajesh', 'project', '2024-10-04', '2024-10-15', '[\"dhyer\"]', '[\"54444\"]', 54444, '[\"rdyrt\"]', '[\"20000\"]', 20000, 34444, 555, 33889, '2024-10-14', '2024-10-14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(244) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
