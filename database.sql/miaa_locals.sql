CREATE DATABASE IF NOT EXISTS `miaa_locals` CHARACTER SET latin1 COLLATE latin1_swedish_ci;

USE `miaa_locals_db`;
-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 01:32 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `full_name`, `username`, `password`, `created_at`) VALUES
(1, 'Mark  John Guillermo', 'admin', '$2y$10$McuYwaZmH8X1uNUOxHFUY.El9KuPt7P2p/0cMQW7GUujjXJy05GFu', '2025-03-05 08:27:08'),
(2, 'Mark John Ignacio Guillermo', 'adminmisd', '$2y$10$9onQUkXE6E/xRmrVv868duu5/ziHjE0Pz2qi8TI/jy9cccZ2CDX3u', '2025-03-05 14:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `employee_type`
--

CREATE TABLE `employee_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee_type`
--

INSERT INTO `employee_type` (`id`, `type_name`) VALUES
(2, 'JOB ORDER'),
(3, 'OJT'),
(1, 'ORGANIC');

-- --------------------------------------------------------

--
-- Table structure for table `locals`
--

CREATE TABLE `locals` (
  `input_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `local` varchar(50) DEFAULT NULL,
  `office` varchar(100) DEFAULT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_edited` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `locals`
--

INSERT INTO `locals` (`input_id`, `employee_id`, `local`, `office`, `contact_name`, `full_name`, `date_added`, `date_edited`) VALUES
(1, NULL, '9390', 'BUILDINGS DIVISION', 'hehhs', NULL, '2025-03-19 10:10:12', '2025-03-19 10:10:12'),
(6, '7565', '3454', 'RESCUE & FIREFIGHTING DIVISION', 'DWND', 'Jerson U. Tomines', '2025-03-18 14:39:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `miaalocals_user`
--

CREATE TABLE `miaalocals_user` (
  `employee_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `employee_type` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_office` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `miaalocals_user`
--

INSERT INTO `miaalocals_user` (`employee_id`, `full_name`, `employee_type`, `username`, `password`, `user_office`, `date_created`, `status`) VALUES
(745, 'Mark John I. Guillermo', 'OJT', 'mj2218', '$2y$10$KxFn842/VGPJrqRGZ5pVRup5MxZLrqvpslLxlsxl16lbWon5o1Tz6', 'MANAGEMENT INFORMATION SYSTEM DIVISION', '2025-03-30 10:22:34', 'inactive'),
(1234, 'Mark John Guillermo', 'JOB ORDER', 'mj2203', '$2y$10$GOfcUNxhJF0oW5x7Fj8VzO8zswMTYlZLKuP1T2g4qZOMu3JaKdwlG', 'MANAGEMENT INFORMATION SYSTEM DIVISION', '2025-05-23 23:50:32', 'active'),
(60172, 'FROILAN ONGCHANGCO', 'JOB ORDER', 'froilan.ongchangco', '$2y$10$WjkiNRy9M1nRRW.rLTVjS.UffiUhKQUopZm/4H1u7JgWZ0bQIRuVm', 'MANAGEMENT INFORMATION SYSTEM DIVISION', '2025-03-24 01:05:46', 'inactive'),
(60174, 'ROMEL MANALLO', 'JOB ORDER', 'rmlmanallo66', '$2y$10$f3bEuwiSSPrek74//.kylOL83eIcNFOiWKnxA3rBWXMr3UED84LNe', 'MANAGEMENT INFORMATION SYSTEM DIVISION', '2025-03-26 01:31:35', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `miaalocals_user_inputs`
--

CREATE TABLE `miaalocals_user_inputs` (
  `input_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `local` varchar(255) DEFAULT NULL,
  `office` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `user_office` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `miaalocals_user_inputs`
--

INSERT INTO `miaalocals_user_inputs` (`input_id`, `employee_id`, `local`, `office`, `contact_name`, `full_name`, `user_office`, `date_added`, `date_edited`) VALUES
(56, 0, '3454', 'MANAGEMENT INFORMATION SYSTEM DIVISION', 'MISD TECH', 'ADMIN', NULL, '2025-05-24 05:49:24', '2025-05-24 05:49:24'),
(57, 1234, '8080', 'CASHIERING DIVISION', 'BABYW', 'Mark John Guillermo', 'MANAGEMENT INFORMATION SYSTEM DIVISION', '2025-05-24 05:51:03', '2025-05-23 23:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `office_list`
--

CREATE TABLE `office_list` (
  `id` int(11) NOT NULL,
  `office_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `office_list`
--

INSERT INTO `office_list` (`id`, `office_name`) VALUES
(1, 'ACCOUNTING DIVISION'),
(2, 'ADMINISTRATIVE DEPARTMENT'),
(3, 'AIRPORT GROUND OPERATIONS DIVISION'),
(4, 'AIRPORT GROUNDS OPERATIONS COMPLIANCE MONITORING DIVISION'),
(5, 'AIRPORT OPERATIONS DEPARTMENT'),
(6, 'AIRPORT POLICE DEPARTMENT'),
(7, 'AIRPORT SECURITY INSPECTORATE OFFICE'),
(8, 'AIRPORT TERMINAL OPERATIONS COMPLIANCE MONITORING DIVISION'),
(9, 'AIRSIDE POLICE DIVISION'),
(10, 'BUDGET DIVISION'),
(11, 'BUILDINGS DIVISION'),
(12, 'BUSINESS & INVESTMENT DEVELOPMENT DIVISION'),
(13, 'CASHIERING DIVISION'),
(14, 'CIVIL WORKS DEPARTMENT'),
(15, 'CIVIL WORKS DIVISION'),
(16, 'COLLECTION DIVISION'),
(17, 'COMMERCIAL SERVICES DEPARTMENT'),
(18, 'CONCESSIONAIRES'),
(19, 'CONCESSIONS MANAGEMENT DIVISION'),
(20, 'CORPORATE MANAGEMENT'),
(21, 'CORPORATE MANAGEMENT SERVICES DEPARTMENT'),
(22, 'DESIGN & PLANNING DIVISION'),
(23, 'DOMESTIC TERMINAL DIVISION'),
(24, 'DOMESTIC TERMINAL OPERATIONS DIVISION'),
(25, 'ELECTRICAL DIVISION'),
(26, 'ELECTRO-MECHANICAL DEPARTMENT'),
(27, 'ELECTRONICS & COMMUNICATIONS DIVISION'),
(28, 'EMERGENCY SERVICES DEPARTMENT'),
(29, 'ENGINEERING DEPARTMENT'),
(30, 'FINANCE DEPARTMENT'),
(31, 'GENERAL AVIATION DIVISION'),
(32, 'GENERAL AVIATION OPERATIONS DIVISION'),
(33, 'GENERAL SERVICES DIVISION'),
(34, 'HUMAN RESOURCE AND DEVELOPMENT DIVISION'),
(35, 'ID & PASS CONTROL DIVISION'),
(36, 'INTELLIGENCE & ID PASS CONTROL DEPARTMENT'),
(37, 'INTELLIGENCE AND INVESTIGATION DIVISION'),
(38, 'INTERNAL AUDIT SERVICES OFFICE'),
(39, 'INTERNATIONAL CARGO OPERATIONS DIVISION'),
(40, 'INTERNATIONAL CARGO TERMINAL DIVISION'),
(41, 'INTERNATIONAL PASSENGER TERMINAL DIVISION'),
(42, 'INTERNATIONAL TERMINAL OPERATIONS DIVISION'),
(43, 'LANDSIDE POLICE DIVISION'),
(44, 'LEGAL OFFICE'),
(45, 'MANAGEMENT INFORMATION SYSTEM DIVISION'),
(46, 'MECHANICAL DIVISION'),
(47, 'MEDICAL DIVISION'),
(48, 'NAIA IADITG'),
(49, 'NAIA TFATP'),
(50, 'OFFICE OF THE AGM FOR DEVELOPMENT & CORPORATE AFFAIRS'),
(51, 'OFFICE OF THE AGM FOR ENGINEERING'),
(52, 'OFFICE OF THE AGM FOR FINANCE AND ADMINISTRATION'),
(53, 'OFFICE OF THE AGM FOR OPERATIONS AND SAFETY STANDARDS COMPLIANCE'),
(54, 'OFFICE OF THE AGM FOR SECURITY AND EMERGENCY SERVICES'),
(55, 'OFFICE OF THE ASSISTANT TERMINAL MANAGER - T1'),
(56, 'OFFICE OF THE ASSISTANT TERMINAL MANAGER - T2'),
(57, 'OFFICE OF THE ASSISTANT TERMINAL MANAGER - T3'),
(58, 'OFFICE OF THE ASSISTANT TERMINAL MANAGER - T4'),
(59, 'OFFICE OF THE CORPORATE BOARD SECRETARY'),
(60, 'OFFICE OF THE GENERAL MANAGER'),
(61, 'OFFICE OF THE SENIOR ASSISTANT GENERAL MANAGER'),
(62, 'PAVEMENTS AND GROUNDS DIVISION'),
(63, 'PERSONNEL DIVISION'),
(64, 'PLANS AND PROGRAMS DIVISION'),
(65, 'POLICE DETECTION AND REACTION DIVISION'),
(66, 'POLICE INTELLIGENCE AND INVESTIGATION DIVISION'),
(67, 'PREQUALIFICATION BID AND AWARD COMMITTEE'),
(68, 'PROCUREMENT DIVISION'),
(69, 'PROJECT MANAGEMENT OFFICE'),
(70, 'PROPERTY MANAGEMENT DIVISION'),
(71, 'PUBLIC AFFAIRS AND PROTOCOLS OFFICE'),
(72, 'RESCUE & FIREFIGHTING DIVISION'),
(73, 'SAFETY MANAGEMENT SYSTEMS OFFICE'),
(74, 'VITAL SERVICES DIVISION');

-- --------------------------------------------------------

--
-- Table structure for table `user_approval`
--

CREATE TABLE `user_approval` (
  `employee_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `employee_type` varchar(100) NOT NULL,
  `user_office` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `employee_type`
--
ALTER TABLE `employee_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `locals`
--
ALTER TABLE `locals`
  ADD PRIMARY KEY (`input_id`);

--
-- Indexes for table `miaalocals_user`
--
ALTER TABLE `miaalocals_user`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `miaalocals_user_inputs`
--
ALTER TABLE `miaalocals_user_inputs`
  ADD PRIMARY KEY (`input_id`);

--
-- Indexes for table `office_list`
--
ALTER TABLE `office_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `office_name` (`office_name`);

--
-- Indexes for table `user_approval`
--
ALTER TABLE `user_approval`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee_type`
--
ALTER TABLE `employee_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `locals`
--
ALTER TABLE `locals`
  MODIFY `input_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `miaalocals_user`
--
ALTER TABLE `miaalocals_user`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60175;

--
-- AUTO_INCREMENT for table `miaalocals_user_inputs`
--
ALTER TABLE `miaalocals_user_inputs`
  MODIFY `input_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `office_list`
--
ALTER TABLE `office_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
