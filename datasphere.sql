-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 01:12 AM
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
-- Database: `datasphere`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedbackID`, `userID`, `content`, `category`, `rating`, `timestamp`, `status`) VALUES
(68, 4, 'kl', 'performance', NULL, '2025-04-26 16:46:57', 'pending'),
(69, 4, 'll', 'content', NULL, '2025-04-26 16:47:03', 'pending'),
(70, 4, 'lloo', 'other', NULL, '2025-04-26 16:47:13', 'responded'),
(71, 4, 'ss', 'bug', NULL, '2025-04-26 16:47:18', 'pending'),
(72, 5, 'lloo', 'performance', NULL, '2025-04-26 16:58:12', 'pending'),
(73, 5, 'qqw', 'other', NULL, '2025-04-26 16:58:20', 'pending'),
(74, 5, 'aa', 'feature', NULL, '2025-04-26 16:58:25', 'responded'),
(75, 5, 'ee', 'performance', NULL, '2025-04-26 16:58:31', 'pending'),
(76, 5, 'iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii', 'content', NULL, '2025-04-26 20:42:07', 'pending'),
(77, 5, 'ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo', 'ui', NULL, '2025-04-26 21:39:11', 'pending');

-- --------------------------------------------------------

-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notificationID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `feedbackID` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notificationID`, `userID`, `feedbackID`, `message`, `timestamp`) VALUES
(1, 1, 1, 'New feedback submitted by Sinam Kk', '2025-04-05 17:24:34'),
(3, 1, 17, 'New feedback submitted by Sinam Kk', '2025-04-06 02:25:54'),
(4, 1, 18, 'New feedback submitted by Sinam Kk', '2025-04-07 15:43:21'),
(5, 1, 19, 'New feedback submitted by Sinam Ssssss', '2025-04-07 15:47:17'),
(11, 1, 25, 'New feedback submitted by SS k', '2025-04-08 01:11:43'),
(12, 1, 26, 'New feedback submitted by SS k', '2025-04-08 01:15:28'),
(13, 1, 27, 'New feedback submitted by SS k', '2025-04-08 01:15:42'),
(14, 1, 28, 'New feedback submitted by SS k', '2025-04-08 01:15:57'),
(15, 1, 29, 'New feedback submitted by SS k', '2025-04-08 01:16:07'),
(16, 1, 30, 'New feedback submitted by SS k', '2025-04-08 01:16:16'),
(17, 1, 31, 'New feedback submitted by SS k', '2025-04-08 01:16:24'),
(18, 1, 32, 'New feedback submitted by SS k', '2025-04-08 01:16:39'),
(19, 1, 33, 'New feedback submitted by Sinam kk', '2025-04-08 02:56:17'),
(20, 1, 34, 'New feedback submitted by Sun Koi', '2025-04-11 14:09:55'),
(21, 1, 35, 'New feedback submitted by Sun Koiiiiiiiiiiiiiii', '2025-04-11 22:03:29'),
(22, 1, 36, 'New feedback submitted by Sun K', '2025-04-13 20:17:06'),
(23, 1, 37, 'New feedback submitted by Sun Kai', '2025-04-14 13:52:00'),
(24, 1, 38, 'New feedback submitted by Sun Kaiiiii', '2025-04-14 14:01:43'),
(25, 1, 39, 'New feedback submitted by Sun Kaiiiii', '2025-04-14 14:08:11'),
(26, 1, 40, 'New feedback submitted by Sun Kaiiiii', '2025-04-14 14:13:18'),
(27, 1, 41, 'New feedback submitted by Sun Ko', '2025-04-14 14:23:04'),
(28, 1, 42, 'New feedback submitted by Sun Ko', '2025-04-14 14:43:19'),
(29, 1, 43, 'New feedback submitted by Cynthia Keza', '2025-04-14 14:45:07'),
(30, 1, 44, 'New feedback submitted by Herve Cyiza', '2025-04-14 14:46:01'),
(31, 1, 45, 'New feedback submitted by Claire Mbabazi', '2025-04-14 14:47:31'),
(32, 1, 46, 'New feedback submitted by Sun Ko', '2025-04-20 18:27:53'),
(33, 6, 40, 'Your feedback has received a response', '2025-04-23 23:31:50'),
(34, 7, 44, 'Your feedback has received a response', '2025-04-23 23:39:45'),
(35, 6, 42, 'Your feedback has received a response', '2025-04-23 23:40:36'),
(36, 6, 46, 'Your feedback has received a response', '2025-04-23 23:45:52'),
(37, 9, 45, 'Your feedback has received a response', '2025-04-23 23:47:14'),
(38, 9, 45, 'Your feedback has received a response', '2025-04-24 00:04:15'),
(39, 1, 47, 'New feedback submitted by dada Igi', '2025-04-24 00:40:35'),
(40, 1, 48, 'New feedback submitted by dada Igi', '2025-04-24 00:40:49'),
(41, 7, 48, 'Your feedback has received a response', '2025-04-24 00:41:21'),
(42, 7, 47, 'Your feedback has received a response', '2025-04-24 01:03:51'),
(43, 1, 49, 'New feedback submitted by dada Igi', '2025-04-24 01:04:18'),
(44, 1, 50, 'New feedback submitted by dada Igi', '2025-04-24 01:04:28'),
(45, 7, 50, 'Your feedback has received a response', '2025-04-24 01:04:51'),
(46, 7, 49, 'Your feedback has received a response', '2025-04-24 01:05:16'),
(47, 1, 51, 'New feedback submitted by dada Igi', '2025-04-26 13:52:28'),
(48, 1, 52, 'New feedback submitted by dada Igiran', '2025-04-26 15:10:03'),
(49, 1, 53, 'New feedback submitted by dada Igiran', '2025-04-26 15:10:19'),
(50, 1, 54, 'New feedback submitted by dada Igiran', '2025-04-26 15:10:32'),
(51, 1, 55, 'New feedback submitted by dada Igiran', '2025-04-26 15:37:19'),
(52, 1, 56, 'New feedback submitted by dada Igiran', '2025-04-26 15:38:03'),
(53, 1, 57, 'New feedback submitted by dada Igiran', '2025-04-26 15:39:35'),
(54, 1, 58, 'New feedback submitted by dada Igiran', '2025-04-26 15:40:20'),
(55, 1, 59, 'New feedback submitted by dada Igiran', '2025-04-26 15:40:25'),
(56, 1, 60, 'New feedback submitted by dada Igiran', '2025-04-26 15:40:29'),
(57, 1, 61, 'New feedback submitted by dada Igiran', '2025-04-26 15:40:33'),
(58, 1, 62, 'New feedback submitted by dada Igiran', '2025-04-26 15:40:36'),
(59, 1, 63, 'New feedback submitted by dada Igiran', '2025-04-26 15:41:16'),
(60, 1, 64, 'New feedback submitted by dada Igiran', '2025-04-26 15:41:25'),
(61, 1, 65, 'New feedback submitted by dada Igiran', '2025-04-26 15:41:29'),
(62, 1, 66, 'New feedback submitted by dada Igiran', '2025-04-26 15:41:37'),
(63, 1, 67, 'New feedback submitted by dada Igiran', '2025-04-26 15:41:43'),
(64, 7, 67, 'Your feedback has received a response', '2025-04-26 15:42:29'),
(65, 7, 64, 'Your feedback has received a response', '2025-04-26 15:48:05'),
(66, 1, 68, 'New feedback submitted by s', '2025-04-26 16:46:57'),
(67, 1, 69, 'New feedback submitted by s', '2025-04-26 16:47:03'),
(68, 1, 70, 'New feedback submitted by s', '2025-04-26 16:47:13'),
(69, 1, 71, 'New feedback submitted by s', '2025-04-26 16:47:18'),
(70, 1, 72, 'New feedback submitted by sa', '2025-04-26 16:58:12'),
(71, 1, 73, 'New feedback submitted by sa', '2025-04-26 16:58:20'),
(72, 1, 74, 'New feedback submitted by sa', '2025-04-26 16:58:25'),
(73, 1, 75, 'New feedback submitted by sa', '2025-04-26 16:58:31'),
(74, 4, 70, 'Your feedback has received a response', '2025-04-26 17:04:24'),
(75, 4, 70, 'Your feedback has received a response', '2025-04-26 17:12:21'),
(76, 5, 74, 'Your feedback has received a response', '2025-04-26 20:40:01'),
(77, 1, 76, 'New feedback submitted by sa', '2025-04-26 20:42:07'),
(78, 1, 77, 'New feedback submitted by sa', '2025-04-26 21:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `response`
--

CREATE TABLE `response` (
  `responseID` int(11) NOT NULL,
  `feedbackID` int(11) DEFAULT NULL,
  `adminID` int(11) DEFAULT NULL,
  `responseText` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `response`
--

INSERT INTO `response` (`responseID`, `feedbackID`, `adminID`, `responseText`, `timestamp`) VALUES
(1, 40, 4, 'Great that it went well. Thanks for the feedback.', '2025-04-23 23:31:50'),
(2, 44, 4, 'hhh', '2025-04-23 23:39:45'),
(3, 42, 4, 'Well noted. Thanks!', '2025-04-23 23:40:36'),
(4, 46, 4, 'well noted, Thanks, Sun Ko!', '2025-04-23 23:45:52'),
(5, 45, 4, 'Thanks for the feedback, enjoy the seamlessness of the features!', '2025-04-23 23:47:14'),
(6, 45, 4, 'kjhghjk', '2025-04-24 00:04:15'),
(7, 48, 4, 'being handled! Thanks for feedback.', '2025-04-24 00:41:21'),
(8, 47, 4, 'sorry', '2025-04-24 01:03:51'),
(9, 50, 4, 'sorry', '2025-04-24 01:04:51'),
(10, 49, 4, 'sorry, will be worked on.', '2025-04-24 01:05:16'),
(11, 67, 4, 'Thanks for the feedback. will work on it', '2025-04-26 15:42:29'),
(12, 64, 4, 'tHanks\r\n', '2025-04-26 15:48:05'),
(13, 70, 5, 'thanks', '2025-04-26 17:04:24'),
(14, 70, 5, 'kk', '2025-04-26 17:12:21'),
(15, 74, 5, 'oo', '2025-04-26 20:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('Admin','Customer') NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `username`, `email`, `role`, `password`) VALUES
(1, 'Del Ishim', 'del@gmail.com', 'Admin', '$2y$10$k.Z46YCjmRBS5P3cNEj9G.TRO3Hgzjz8fAsKKZ8kfRFoXH/5Uxa5y'),
(2, 'Dada', 'd@gmail.com', 'Customer', '$2y$10$DjxaeOHrTwfznfjlWbgTMOJHKviDtRZaqi8Aa.OD6sgki4Nasj9.a'),
(3, 'Kaka', 'k@gmail.com', 'Admin', '$2y$10$ZLhFBZFkbsRvpFT.5oJ2JeyDTbk.NpFCJkYQ89Sj.zG6KaDziuWAq'),
(4, 's', 's@gmail.com', 'Customer', '$2y$10$Jg8gRUFFZP4XdUBHnaLmGO0ZoaUMWqG5.WVHcvJmDZM7z7bm4dKgC'),
(5, 'sassss', 'sa@gmail.com', 'Admin', '$2y$10$V0RwRnc7TQ19VwyGsjA2VuW7JXgc0zcCxVc3FtNlZvGbB4SPUODCm'),
(6, 'kk', 'k0@gmail.com', 'Admin', '$2y$10$7VLWOfqneTUr7p50xQSUmOtqdhSp4gAA5twoqLkFY1J7ud9ROQJiC'),
(7, 'ki', 'ki@gmail.com', 'Admin', '$2y$10$vlxR7.q2Jivqhv3aC4VIeONjspizszRTbDBHnejAdOci6nLU/nPZG'),
(8, 'ka', 'ka@gmail.com', 'Customer', '$2y$10$U0mks1hbRxPwwkrsehlpu.XI2MjqJs4n5bLOD8M6mBMucG7sqcem.'),
(11, 'vv', 'v@gmail.com', 'Customer', '$2y$10$OaB2Lq8pRdOurMXwSOayj.sNnJsQvT9EWxTrceASV3BHHg9cxpu/.'),
(12, 'll', 'l@gmail.com', 'Customer', '$2y$10$xF8vOFhL/jqjOge0XfbrEemNEPy8tfFT8uI0f4N0VOxoiRjQdHRB6'),
(13, 'cc', 'cc@gmail.com', 'Customer', '$2y$10$Tj1jmgDUTjvM3kO9HMamLeWTK28AREpwDM0sgz9x5V7FRcjIUsOv6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notificationID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `notification_ibfk_2` (`feedbackID`);

--
-- Indexes for table `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`responseID`),
  ADD KEY `feedbackID` (`feedbackID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `response`
--
ALTER TABLE `response`
  MODIFY `responseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
