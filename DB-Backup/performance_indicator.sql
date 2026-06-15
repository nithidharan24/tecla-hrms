-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2024 at 01:05 PM
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
-- Table structure for table `performance_indicator`
--

CREATE TABLE `performance_indicator` (
  `id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL,
  `technical` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`technical`)),
  `organizational` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`organizational`)),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_indicator`
--

INSERT INTO `performance_indicator` (`id`, `designation_id`, `technical`, `organizational`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 5, '{\"customer_experience\":\"intermediate\",\"marketing\":\"beginner\",\"management\":\"advanced\",\"administration\":\"expert\",\"presentation_skill\":\"beginner\",\"quality_of_work\":\"intermediate\",\"efficiency\":\"advanced\"}', '{\"integrity\":\"beginner\",\"professionalism\":\"intermediate\",\"team_work\":\"advanced\",\"critical_thinking\":\"expert\",\"conflict_management\":\"beginner\",\"attendance\":\"intermediate\",\"ability_to_meet_deadline\":\"advanced\"}', 'active', 0, '2024-10-21 02:12:30', '2024-10-21 05:25:52'),
(2, 1, '{\"customer_experience\":\"none\",\"marketing\":\"none\",\"management\":\"none\",\"administration\":\"none\",\"presentation_skill\":\"none\",\"quality_of_work\":\"none\",\"efficiency\":\"none\"}', '{\"integrity\":\"none\",\"professionalism\":\"none\",\"team_work\":\"none\",\"critical_thinking\":\"none\",\"conflict_management\":\"none\",\"attendance\":\"none\",\"ability_to_meet_deadline\":\"none\"}', 'active', 0, '2024-10-21 02:14:53', '2024-10-21 22:38:34'),
(3, 4, '{\"customer_experience\":\"none\",\"marketing\":\"none\",\"management\":\"none\",\"administration\":\"none\",\"presentation_skill\":\"none\",\"quality_of_work\":\"none\",\"efficiency\":\"none\"}', '{\"integrity\":\"none\",\"professionalism\":\"none\",\"team_work\":\"none\",\"critical_thinking\":\"none\",\"conflict_management\":\"none\",\"attendance\":\"none\",\"ability_to_meet_deadline\":\"none\"}', 'active', 0, '2024-10-21 02:34:42', '2024-10-21 03:27:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `performance_indicator`
--
ALTER TABLE `performance_indicator`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `performance_indicator`
--
ALTER TABLE `performance_indicator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
