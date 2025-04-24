-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2025 at 02:53 AM
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
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `timestamp` datetime DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedbackID`, `userID`, `content`, `rating`, `timestamp`, `status`) VALUES
(47, 7, 'malfunctionality', 2, '2025-04-24 00:40:35', 'pending'),
(48, 7, 'usability issues', 1, '2025-04-24 00:40:49', 'responded');

-- --------------------------------------------------------

--
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
(41, 7, 48, 'Your feedback has received a response', '2025-04-24 00:41:21');

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
(7, 48, 4, 'being handled! Thanks for feedback.', '2025-04-24 00:41:21');

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
(0, 'Koko Wawa', 'koko@gmail.com', 'Admin', '$2y$10$gn36ilM1w96XqUGQNPvOleHN/Xl2.x1FF/zKO8.n7ZyUIXRKrsMOe'),
(4, 'Del Ish', 'delice@gmail.com', 'Admin', '$2y$10$kP0XToxyMECwra7CzcHs7eWOiBF3ygPdBPMXzTSlGtpr0y915GdvO'),
(6, 'Kirikou Abba', 'kirikou@gmail.com', 'Customer', '$2y$10$Smc4568hEQBJVQqVoDHqSu7t/RYD/o/x0m.WDoVIEiY.2hm08l.dm'),
(7, 'dada Igi', 'dada@gmail.com', 'Customer', '$2y$10$jnbqqnTmsaCyEpVGqMQJF.HiKLJsOW/dBEW8/vZWgIkHEO.EGcDE6');

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
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `response`
--
ALTER TABLE `response`
  MODIFY `responseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
