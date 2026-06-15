-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 04:08 PM
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
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `client_id` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `user_name`, `email`, `password`, `client_id`, `phone`, `company_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 'Rajesh', 'k', 'rajesh', 'rajesh@gmail.com', '$2y$10$YORXIPulG7/xbygOomRSmee/G3AUqClY8xUicyQKyDt3zt4gRYLua', 'CLT-0003', '09790412538', 'Tecla', 'active', '2024-10-08 08:13:30', '2024-10-08 08:13:30', 0),
(8, 'Prem', 'S', 'prems', 'prems@gmail.com', '$2y$10$S18qgAhf9NMLjO.w9NK3eewp8olAfW7yqgpaCIB.mn5UpiuNj/Xxa', 'CLT-0004', '90876543221', 'Pr3', 'active', '2024-10-12 01:54:44', '2024-10-12 01:54:44', 0),
(11, 'Mohamed', 'Riyasdeen', 'amriyas', 'unikin1517@gmail.com', '$2y$10$43sWJDknUSGvW9Py6tO1KubWYMYHWROkSv6eC.lNs5Rqji/ayIsg2', 'CLT-0005', '06385462105', 'Refo', 'active', '2024-10-12 03:55:44', '2024-10-12 03:55:44', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_id` (`client_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
