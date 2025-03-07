-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2025 at 07:16 AM
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
  `profile_picture` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idno`, `firstname`, `lastname`, `middlename`, `course`, `year`, `email`, `username`, `password`, `profile_picture`) VALUES
(0, '', '', '', '', 0, '', '', '', ''),
(22597959, 'jane', 'lopez', 'l', 'bsit', 3, 'jane@gmail.com', 'jane', '123', 'uploads/476420008_939257027968389_1496962472575981445_n.jpg'),
(22670293, 'asd', 'asdasd', 'asd', 'asdsd', 2323, '2323@gmail.com', 'mat', '$2y$10$akT.Z6oXqlrJwTnBJkXGmuPwdxD3gQuTZ1e0ecuYGmn', ''),
(22670294, 'marlou', 'tadlip', 'c', 'bsitz', 2, 'm@gmail.com', 'mar', '$2y$10$jT9O3vxqNhLyBJTfHl58rOCdj4eHneyIKZtB5Rg3E6thFEL3Cnvu6', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`idno`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
