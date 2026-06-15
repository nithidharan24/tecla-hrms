-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 12:50 PM
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
-- Table structure for table `salary_settings`
--

CREATE TABLE `salary_settings` (
  `id` int(11) NOT NULL,
  `da_percentage` decimal(5,2) DEFAULT NULL,
  `hra_percentage` decimal(5,2) DEFAULT NULL,
  `pf_employee_share` decimal(5,2) DEFAULT NULL,
  `pf_organization_share` decimal(5,2) DEFAULT NULL,
  `esi_employee_share` decimal(5,2) DEFAULT NULL,
  `esi_organization_share` decimal(5,2) DEFAULT NULL,
  `tds_entries` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_settings`
--

INSERT INTO `salary_settings` (`id`, `da_percentage`, `hra_percentage`, `pf_employee_share`, `pf_organization_share`, `esi_employee_share`, `esi_organization_share`, `tds_entries`) VALUES
(1, 8.00, 7.00, 7.00, 8.00, 7.00, 7.00, '[{\"tds_salary_from\":\"50000\",\"tds_salary_to\":\"60000\",\"tds_percentage\":\"4\"},{\"tds_salary_from\":\"50000\",\"tds_salary_to\":\"40000\",\"tds_percentage\":\"7\"}]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `salary_settings`
--
ALTER TABLE `salary_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `salary_settings`
--
ALTER TABLE `salary_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
