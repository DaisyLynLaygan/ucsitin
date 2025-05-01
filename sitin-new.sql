-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2025 at 03:36 PM
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
-- Database: `sitin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '123'),
('admin', '$2y$10$BO65np.g308mkiONtSMdv.jjFETOp6nfLf4S.7p0SS/'),
('admin', '$2y$10$yFyWvdBGnRZKcTSY64R64.8ij.AHifYZTVTJkdgZwKC');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date_posted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `date_posted`) VALUES
(1, 'jane', 'gwapa na pinakabuotan', '2025-03-07 04:06:52'),
(2, 'jane lopez', 'Jane is the best', '2025-03-07 04:08:37'),
(3, 'jane lopez', 'Jane is the best', '2025-03-07 04:09:11'),
(4, 'gela', 'gwapa', '2025-03-07 06:18:02'),
(5, 'gela', 'hi', '2025-03-07 06:25:11'),
(6, 'janee', 'working', '2025-03-21 11:29:47'),
(7, 'ccs student', 'hi eveyone', '2025-04-01 06:55:14'),
(8, 'ccs days', 'TIme to shine', '2025-04-05 14:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `year`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology', 4),
(2, 'BSBA', 'Bachelor of Science in Business Administration', 4),
(3, 'BSED', 'Bachelor of Secondary Education', 5),
(4, 'BSCS', 'Bachelor of Science in Computer Science', 4),
(5, 'ABCOMM', 'Bachelor of Arts in Communication', 4);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `sit_in_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `feedback_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `sit_in_id`, `feedback`, `feedback_date`) VALUES
(3, 12, 'I want to see her', '2025-04-06 14:57:23');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `lab` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `language` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sit_in`
--

CREATE TABLE `sit_in` (
  `id` int(11) NOT NULL,
  `idno` int(11) DEFAULT NULL,
  `purpose` text NOT NULL,
  `laboratory` varchar(100) NOT NULL,
  `sitin_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `sit_out_time` datetime DEFAULT NULL,
  `sit_in_time` datetime NOT NULL DEFAULT current_timestamp(),
  `feedback` text DEFAULT NULL,
  `feedback_status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in`
--

INSERT INTO `sit_in` (`id`, `idno`, `purpose`, `laboratory`, `sitin_time`, `sit_out_time`, `sit_in_time`, `feedback`, `feedback_status`) VALUES
(1, 22597959, 'pabugnaw ragud', '524', '2025-03-07 04:31:44', '2025-03-07 11:55:44', '2025-03-07 11:39:12', NULL, 'Pending'),
(2, 22597959, 'research', '530', '2025-03-07 05:07:10', '2025-03-07 12:08:55', '2025-03-07 12:07:10', NULL, 'Pending'),
(3, 21434758, 'networking', '544', '2025-03-07 05:08:26', '2025-03-07 13:42:47', '2025-03-07 12:08:26', NULL, 'Pending'),
(4, 22597959, 'kaon', 'ta', '2025-03-07 05:08:46', '2025-03-07 12:17:04', '2025-03-07 12:08:46', NULL, 'Pending'),
(5, 22597959, 'php', '524', '2025-03-07 05:16:56', '2025-03-07 12:17:01', '2025-03-07 12:16:56', NULL, 'Pending'),
(6, 22597959, 'java', '544', '2025-03-07 05:20:08', '2025-03-07 12:20:52', '2025-03-07 12:20:08', NULL, 'Pending'),
(7, 22597959, 'aza', 'saza', '2025-03-07 06:48:19', '2025-04-01 11:54:23', '2025-03-07 13:48:19', NULL, 'Pending'),
(8, 22597959, 'networking', '542', '2025-04-01 04:52:51', NULL, '2025-04-01 11:52:51', NULL, 'Pending'),
(9, 22597959, 'php', '530', '2025-04-01 09:53:00', NULL, '2025-04-01 16:53:00', NULL, 'Pending'),
(10, 12345678, 'networking', '524', '2025-04-01 09:53:12', '2025-04-01 16:53:20', '2025-04-01 16:53:12', NULL, 'Pending'),
(11, 22597959, 'java', '544', '2025-04-03 10:38:48', NULL, '2025-04-03 17:38:48', NULL, 'Pending'),
(12, 22610638, 'Database', '522', '2025-04-06 09:54:57', '2025-04-06 17:55:11', '2025-04-06 17:54:57', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `idno` int(11) NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `middlename` text NOT NULL,
  `course` text NOT NULL,
  `year` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(250) NOT NULL,
  `session_no` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idno`, `firstname`, `lastname`, `middlename`, `course`, `year`, `email`, `username`, `password`, `profile_picture`, `session_no`) VALUES
(0, '', '', '', '', 0, '', '', '', '', 0),
(123, 'jaa', 'loo', 'l', 'bshm', 1, 'jaa@gmail.com', 'jaa', '123', '', 0),
(1111, 'ivan', 'langomez', '', 'BSED', 3, 'ivanlangomez@gmail.com', 'ivan', '$2y$10$hXuOQl9y6rydAfRWEM8pjuseY7nJ5WDKikSIhERg0g3/GquDdaV86', '', 0),
(1234, 'Isaiah', 'Montemar', 'Fa', 'BSIT', 4, 'jherickisaimar@gmail.com', 'isaiah', '$2y$10$XWhweDc/sZpeoHBnFYAlceeXhE.S2nxKIslMe2SDqS6f1k4zCLu0u', '', 30),
(5432, 'Jon', 'James', 'Doe', 'BSIT', 4, 'jondoe@gmail.com', 'jon123', '$2y$10$0gMx6m92guS2V5W8lC64bugbhbCpRyx5S6UF5iAEQkANcUJhbhgtS', '', 0),
(12345678, 'don', 'pien', 'o', 'BSED', 4, 'do@gmail.com', 'do', '123', 'uploads/1.png', 0),
(18698175, 'kent', 'wenceslao', 'segara', 'bsit', 4, 'kent@gmail.com', 'ken123', '123456', '', 0),
(19835644, 'janila', 'Genabio', 'Jala', 'BSED', 4, 'janila31@gmail.com', 'jims123', '123', 'uploads/406097240_374340175273962_6284274697643423009_n.jpg', 0),
(21434758, 'John Maverick', 'Villarta', 'Peras', 'BSIT', 3, 'mverick_ggg@gmail.com', 'Maverick!@#$%^', 'Mav@614536', 'uploads/2x2__2_.jpg', 0),
(22597818, 'Princess', 'Villanueva', 'F', 'BSIT', 3, 'villa@gmail.com', 'Janila', '123', 'uploads/6876a0b8-0458-4ca2-a2c2-cbe20611b733.jpg', 0),
(22597959, 'janila', 'rep', 'l', 'BSIT', 2, 'jane@gmail.com', 'jane', '123', 'uploads/371866643_324329636712914_7245032133204022337_n.jpg', 0),
(22610638, 'jherick', 'Montemar', 'Fabular', 'BSIT', 0, 'jherickisamar@gmail.com', 'jherick', 'jherick123', '', 0),
(22670293, 'asd', 'asdasd', 'asd', 'asdsd', 2323, '2323@gmail.com', 'mat', '$2y$10$akT.Z6oXqlrJwTnBJkXGmuPwdxD3gQuTZ1e0ecuYGmn', '', 0),
(22670294, 'marlou', 'tadlip', 'c', 'bsitz', 2, 'm@gmail.com', 'mar', '$2y$10$jT9O3vxqNhLyBJTfHl58rOCdj4eHneyIKZtB5Rg3E6thFEL3Cnvu6', '', 0),
(22797819, 'dais', 'laygan', 'p', 'Select Course', 0, 'dais@gmail.com', 'dais', '123', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_sessions`
--

CREATE TABLE `student_sessions` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `logout` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_sessions`
--

INSERT INTO `student_sessions` (`id`, `username`, `date`, `time_in`, `logout`) VALUES
(1, 'testuser', '2025-03-21', '11:31:23', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `sit_in_id` (`sit_in_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sit_in`
--
ALTER TABLE `sit_in`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idno` (`idno`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`idno`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `student_sessions`
--
ALTER TABLE `student_sessions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sit_in`
--
ALTER TABLE `sit_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `student_sessions`
--
ALTER TABLE `student_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`sit_in_id`) REFERENCES `sit_in` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sit_in`
--
ALTER TABLE `sit_in`
  ADD CONSTRAINT `sit_in_ibfk_1` FOREIGN KEY (`idno`) REFERENCES `student` (`idno`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
