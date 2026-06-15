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
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `code_snippets` varchar(255) DEFAULT NULL,
  `answer_explanation` varchar(255) DEFAULT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `question_image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `code_snippets`, `answer_explanation`, `video_link`, `question_image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'IS management has decided to rewrite a legacy customer relations system using fourth generation languages (4GLs). Which of the following risks is MOST often associated with system development using 4GLs?', 'design facilities', 'language subsets', 'Lack of portability', 'Inability to perform data', 'B', '<p class=\"long-word-to-break\">\r\n    AReallyLongWordThatDoesn\'tHaveSpacesSoTheBrowserCan\'tFigureOutWhereItShouldWrap  \r\n</p>', 'IS management has decided to rewrite a legacy customer relations system using f', 'https://www.devsamples.com/css/css-break-lines', 'images/pVdc6mtKpfJUMdZcfSChAt2PIq4tcOIiculc3L0G.jpg', '2024-11-03 22:24:48', '2024-11-03 22:24:48', 0),
(2, 'Which of the following would be the BEST method for ensuring that critical fields in a master record have been updated properly?', 'Inability to perform data', 'Lack of portability', 'language subsets', 'design facilities', 'D', '<p class=\"long-word-to-break\">\r\n    AReallyLongWordThatDoesn\'tHaveSpacesSoTheBrowserCan\'tFigureOutWhereItShouldWrap  \r\n</p>', 'IS management has decided to rewrite a legacy customer relations system using f', 'https://www.devsamples.com/css/css-break-lines', 'images/KtGlRKZQYCVEep7tXmA8NYdRt8vIkFwNREAtdSTb.jpg', '2024-11-03 22:26:17', '2024-11-03 22:26:17', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
