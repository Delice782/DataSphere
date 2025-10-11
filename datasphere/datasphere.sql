     
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 06:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `datasphere`
DROP DATABASE IF EXISTS datasphere;
CREATE DATABASE datasphere;
USE datasphere;
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
(87, 15, 'I’ve been using your project management tool for the past month, and overall it has made task delegation much easier. However, I’ve noticed a few bugs that I’d like to report. For instance, when I assign tasks to team members, the notifications sometimes don’t reach them, causing confusion. Also, the calendar view could use some improvements, currently, it doesn’t sync well with external calendar apps like Google Calendar, which makes planning a bit difficult. I’d appreciate it if these issues could be resolved in the next update.', 'bug', NULL, '2025-04-27 13:53:53', 'pending'),
(88, 16, 'I just wanted to express my appreciation for the recent updates to the inventory management software. The integration with our existing systems was smooth, and the new reporting features provide much more detailed insights, which are invaluable for decision-making.', 'other', NULL, '2025-04-27 13:55:43', 'responded'),
(89, 17, 'The software works great! It’s fast, reliable, and the user interface is clean and simple to navigate. I’m very satisfied with it.', 'other', NULL, '2025-04-27 13:56:43', 'pending'),
(90, 18, 'The app is great, but it could use faster load times, especially when switching between different sections. A performance improvement would be much appreciated.', 'performance', NULL, '2025-04-27 13:59:17', 'pending'),
(92, 15, 'software processor', 'bug', NULL, '2025-04-27 14:34:46', 'pending'),
(93, 15, 'UI works better', 'other', NULL, '2025-04-27 14:34:57', 'responded'),
(94, 15, 'improvement', 'other', NULL, '2025-04-27 14:35:03', 'pending'),
(96, 16, 'besttttt software', 'other', NULL, '2025-04-27 14:36:35', 'pending'),
(98, 17, 'great service', 'other', NULL, '2025-04-27 14:38:58', 'responded'),
(100, 15, 'performance is on point', 'other', NULL, '2025-04-27 15:34:06', 'pending'),
(101, 15, 'content is well', 'content', NULL, '2025-04-27 15:34:14', 'pending');

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
(34, 7, 44, 'Your feedback has received a response', '2025-04-23 23:39:45'),
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
(76, 5, 74, 'Your feedback has received a response', '2025-04-26 20:40:01'),
(77, 1, 76, 'New feedback submitted by sa', '2025-04-26 20:42:07'),
(78, 1, 77, 'New feedback submitted by sa', '2025-04-26 21:39:11'),
(79, 5, 75, 'Your feedback has received a response', '2025-04-27 11:33:32'),
(80, 5, 76, 'Your feedback has received a response', '2025-04-27 11:43:28'),
(82, 1, 78, 'New feedback submitted by sassss', '2025-04-27 12:23:22'),
(83, 1, 79, 'New feedback submitted by sassss', '2025-04-27 12:23:33'),
(84, 1, 80, 'New feedback submitted by xx', '2025-04-27 12:26:22'),
(85, 1, 81, 'New feedback submitted by xx', '2025-04-27 12:26:26'),
(86, 1, 82, 'New feedback submitted by xx', '2025-04-27 12:26:43'),
(96, 1, 86, 'New feedback submitted by ddd', '2025-04-27 13:52:18'),
(97, 1, 87, 'New feedback submitted by Sinam', '2025-04-27 13:53:53'),
(98, 1, 88, 'New feedback submitted by Lydia', '2025-04-27 13:55:43'),
(99, 1, 89, 'New feedback submitted by Delice', '2025-04-27 13:56:43'),
(100, 1, 90, 'New feedback submitted by Titi', '2025-04-27 13:59:17'),
(101, 16, 88, 'Your feedback has received a response', '2025-04-27 14:01:03'),
(103, 1, 92, 'New feedback submitted by Sinam', '2025-04-27 14:34:46'),
(104, 1, 93, 'New feedback submitted by Sinam', '2025-04-27 14:34:57'),
(105, 1, 94, 'New feedback submitted by Sinam', '2025-04-27 14:35:03'),
(107, 1, 96, 'New feedback submitted by Lydia', '2025-04-27 14:36:35'),
(109, 1, 98, 'New feedback submitted by Delice', '2025-04-27 14:38:58'),
(115, 15, 93, 'Your feedback has received a response', '2025-04-27 15:02:28'),
(116, 15, 93, 'Your feedback has received a response', '2025-04-27 15:03:02'),
(117, 17, 98, 'Your feedback has received a response', '2025-04-27 15:04:01'),
(118, 1, 100, 'New feedback submitted by Sinam', '2025-04-27 15:34:06'),
(119, 1, 101, 'New feedback submitted by Sinam', '2025-04-27 15:34:14');

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
(25, 88, 5, 'Thanks for the appreciation!', '2025-04-27 14:01:03'),
(30, 93, 5, 'Thanks', '2025-04-27 15:02:28'),
(31, 93, 5, 'thanks', '2025-04-27 15:03:02'),
(32, 98, 5, 'Thanks', '2025-04-27 15:04:01');

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
(1, 'Del Ishimwe', 'del@gmail.com', 'Admin', '$2y$10$k.Z46YCjmRBS5P3cNEj9G.TRO3Hgzjz8fAsKKZ8kfRFoXH/5Uxa5y'),
(5, 'sasa vivine', 'sa@gmail.com', 'Admin', '$2y$10$V0RwRnc7TQ19VwyGsjA2VuW7JXgc0zcCxVc3FtNlZvGbB4SPUODCm'),
(13, 'caca', 'cc@gmail.com', 'Customer', '$2y$10$Tj1jmgDUTjvM3kO9HMamLeWTK28AREpwDM0sgz9x5V7FRcjIUsOv6'),
(15, 'Sinam', 'sinam@gmail.com', 'Customer', '$2y$10$yJenKVEgpxmV2yPZwK36QOO8a52tddxFYo.T8NG5Fw0d6jGdJ1JyK'),
(16, 'Lydia', 'darko@gmail.com', 'Customer', '$2y$10$XsEy0iEa/V.hO.n7TVnCBuVL9OjojhI/PqE6W./yGjSnOmXIHidf.'),
(17, 'Delice', 'delice@gmail.com', 'Customer', '$2y$10$AJekV1ssuAV4V.4wv.EZR.sIyI/7iaoYYCqCYp4VrEDYT7hfqJC.K'),
(18, 'Titi', 'titi@gmail.coom', 'Customer', '$2y$10$fUAMhyMyzCaDKKjh4wLsNO9YgohPqTkQCOjYghSahc8ApXTcVZ/um'),
(20, 'oo', 'oo@gmail.com', 'Admin', '$2y$10$obc0.8o/AjgYlmhHAPNfUu02mtj2UA5FKi6Vntivo1gbYe4cfW7Da'),
(21, 'Salome', 'sad@gmail.com', 'Customer', '$2y$10$BWsozE7aYYDYtrcSiAZjmOuCckQmS1GS4t9E3VumiTYWwIy1HvgOS'),
(23, 'swww', 'we@gmail.com', 'Admin', '$2y$10$V1CJSsWcxQT5cMtqXibaqOZ3xpEjYFKF6duxWf511dlAgOP0XIYT2');

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
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `response`
--
ALTER TABLE `response`
  MODIFY `responseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;
COMMIT;
