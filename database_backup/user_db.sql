-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2019 at 02:58 PM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `account_gid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_nic` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_latest_login_date_time` datetime NOT NULL,
  `account_current_active` tinyint(1) NOT NULL DEFAULT 0,
  `account_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_password_reset_request` tinyint(1) NOT NULL DEFAULT 0,
  `account_password_reset_identity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_dob` date DEFAULT NULL,
  `account_user_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_home_contact_no` int(15) DEFAULT NULL,
  `account_contact_no` int(15) DEFAULT NULL,
  `account_join_date` date NOT NULL DEFAULT current_timestamp(),
  `account_personal_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_profile_picture_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\avatar\\default-avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_gid`, `account_nic`, `account_email`, `account_latest_login_date_time`, `account_current_active`, `account_password`, `account_password_reset_request`, `account_password_reset_identity`, `account_first_name`, `account_last_name`, `account_dob`, `account_user_address`, `account_permanent_address`, `account_home_contact_no`, `account_contact_no`, `account_join_date`, `account_personal_email`, `account_profile_picture_location`) VALUES
(1, 'G1485', 'N1485', 'g14863.malika@gmail.com', '2019-07-19 18:28:05', 1, '$2y$10$HL3OjkotwEX8HMdHAj75bOyGYPxu6xmZZJwVrD4nZy.6g4rVi5ZuK', 0, NULL, 'Mason', 'Jason', '1999-08-30', 'No 54 / 5 Streety Street, Towny Town', NULL, 112254512, 0, '2019-07-17', NULL, 'images/user/1/uploads/profile/c4d.gif'),
(2, 'G1234', 'N1234', 'g1234.lana@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$u2BrR2YAKyJ6iVrSGUEjcuBKi9LN/gAtBnsdAH5p1TKLUBg8MRyI2', 0, NULL, 'Lana', 'Banana', '1999-09-29', 'No 1234 Red Street, Blue Town', NULL, 10012151, 0, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(3, 'G4567', 'N4567', 'g4567.karl@gmail.com', '2019-07-19 18:11:05', 0, '$2y$10$5Fyos2jbesiltKrjfMsT8exsVz.e/fTZOlGEnD8t8S1ie41l9eC/m', 0, NULL, 'Karl', 'Nein', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/3/uploads/profile/104319.jpg'),
(4, 'G9999', 'N9999', 'g999.flora@gmail.com', '2019-07-19 18:14:20', 0, '$2y$10$ewg16ZSYdtp3ZHsHiwH.C.bAJJgGFhuoI9yaMcdjeqVTFs/vqKPQm', 0, NULL, 'Flora', 'Fauna', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/4/uploads/profile/Flora_content_img.jpg'),
(5, 'G9809', 'N9809', 'g9809@gmail.com', '2019-07-19 18:15:44', 0, '$2y$10$rVYx7G7MZUYf.25bI/z0TeBp81z/1Tw8E1T.Y6WCsRLdUI0MWvv2a', 0, NULL, 'Trebl', 'Bass', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/5/uploads/profile/61wcEoXVcuL._SX425_.jpg'),
(6, 'G2312', 'N2312', 'g2312.mary@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$cpWkESBcOFk8KE41UdCxI.Dr4NRChat1AN5STdbCBM4gOcmbeQOiS', 0, NULL, 'Mary', 'Sad', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(7, 'G0001', 'N0001', 'g0001.ping@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$KWpkrGfQsN62VxoVmDTLeO2Ja028fjD./nF.vcvhwhVJFqm8ymdCK', 0, NULL, 'Ping', 'Pong', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(8, 'G7891', 'N7891', 'g7891.thunder@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$6T9EQnrdlKZqpRz/svXtA.XjaA.ud9TMsx.wdtLUDTm24HFmmQrle', 0, NULL, 'Thunder', 'Cracker', NULL, NULL, NULL, NULL, NULL, '2019-07-18', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(9, 'G5555', 'N5555', 'g5555.proper@gmail.com', '2019-07-18 16:14:12', 0, '$2y$10$4mluEnkPFjuTnV7M5WBCAuQJguH5z4uJfQ2VfmGmlepk8WfXbwO5e', 0, NULL, 'Proper', 'Test', NULL, NULL, NULL, NULL, NULL, '2019-07-18', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(10, 'G8090', 'N8090', 'g8090.ginger@gmail.com', '2019-07-19 18:27:47', 0, '$2y$10$uoM.QzuKXQQAbPdCXs4vyurrZ4WuArRL46r9Yb2zR6xKWuyaUJ6kW', 0, NULL, 'Ginger', 'Barl', NULL, NULL, NULL, NULL, NULL, '2019-07-19', NULL, 'images/user/10/uploads/profile/giphy (1).gif');

-- --------------------------------------------------------

--
-- Table structure for table `account_designations`
--

CREATE TABLE `account_designations` (
  `account_designation_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_designations`
--

INSERT INTO `account_designations` (`account_designation_id`, `account_id`, `designation_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 4),
(7, 7, 3),
(8, 8, 3),
(9, 9, 5),
(10, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `designation_id` int(11) NOT NULL,
  `designation_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`designation_id`, `designation_name`) VALUES
(1, 'Admin'),
(2, 'Supervisor'),
(3, 'SRT'),
(4, 'SRT Analyst'),
(5, 'ODA');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `account_designations`
--
ALTER TABLE `account_designations`
  ADD PRIMARY KEY (`account_designation_id`),
  ADD KEY `ACCOUNT_ID` (`account_id`),
  ADD KEY `DESIGNATION_ID` (`designation_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`designation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `account_designations`
--
ALTER TABLE `account_designations`
  MODIFY `account_designation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `designation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_designations`
--
ALTER TABLE `account_designations`
  ADD CONSTRAINT `ACCOUNT_ID` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  ADD CONSTRAINT `DESIGNATION_ID` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`designation_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
