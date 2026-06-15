-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 07:13 AM
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
-- Table structure for table `employee_module_access`
--

CREATE TABLE `employee_module_access` (
  `id` int(255) NOT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `module_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_module_access`
--

INSERT INTO `employee_module_access` (`id`, `employee_id`, `module_name`) VALUES
(6, '23', 'All Employees'),
(7, '23', 'Holidays'),
(8, '23', 'Leaves (Admin)'),
(9, '23', 'Leaves (Employee)'),
(10, '23', 'Leave Settings'),
(11, '23', 'Attendance (Admin)'),
(12, '23', 'Attendance (Employee)'),
(13, '23', 'Departments'),
(14, '23', 'Designations'),
(15, '23', 'Timesheet'),
(16, '23', 'Shift & Schedule'),
(17, '23', 'Overtime'),
(18, '23', 'Clients'),
(19, '23', 'Projects'),
(20, '23', 'Tasks'),
(21, '23', 'Task Board'),
(22, '23', 'Tickets'),
(23, '23', 'Estimates'),
(24, '23', 'Invoices'),
(25, '23', 'Payments'),
(26, '23', 'Expenses'),
(27, '23', 'Provident Fund'),
(28, '23', 'Taxes'),
(29, '23', 'Categories');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee_module_access`
--
ALTER TABLE `employee_module_access`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_module_access`
--
ALTER TABLE `employee_module_access`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
