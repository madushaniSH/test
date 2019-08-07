-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2019 at 07:42 AM
-- Server version: 10.4.6-MariaDB
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
  `account_latest_login_date_time` datetime DEFAULT NULL,
  `account_current_active` tinyint(1) NOT NULL DEFAULT 0,
  `account_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_password_reset_request` tinyint(1) NOT NULL DEFAULT 0,
  `account_password_reset_identity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_dob` date DEFAULT NULL,
  `account_user_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_home_contact_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_contact_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_join_date` date NOT NULL DEFAULT current_timestamp(),
  `account_personal_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_profile_picture_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\avatar\\default-avatar.jpg',
  `account_transport_method` enum('own','office') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'own',
  `account_bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_bank_branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_gid`, `account_nic`, `account_email`, `account_latest_login_date_time`, `account_current_active`, `account_password`, `account_password_reset_request`, `account_password_reset_identity`, `account_first_name`, `account_last_name`, `account_dob`, `account_user_address`, `account_permanent_address`, `account_home_contact_no`, `account_contact_no`, `account_join_date`, `account_personal_email`, `account_profile_picture_location`, `account_transport_method`, `account_bank_name`, `account_bank_branch`, `account_bank_account_number`) VALUES
(1, 'G1485', 'N1485', 'g14863.malika@gmail.com', '2019-08-07 13:01:32', 0, '$2y$10$1oASi9Gdgns.3SMQPK7S7ehhIIVRt1eg3Nbix2H3cBVs42c6rNTxK', 0, NULL, 'Mason', 'Jason', '1999-08-30', 'No 54 / 5 Streety Street, Towny Town', NULL, '112254512', '0', '2019-07-17', NULL, 'images/user/1/uploads/profile/c4d.gif', 'own', NULL, NULL, NULL),
(2, 'G1234', 'N1234', 'g1234.lana@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$u2BrR2YAKyJ6iVrSGUEjcuBKi9LN/gAtBnsdAH5p1TKLUBg8MRyI2', 0, NULL, 'Lana', 'Banana', '1999-09-29', 'No 1234 Red Street, Blue Town', NULL, '10012151', '0', '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg', 'own', NULL, NULL, NULL),
(3, 'G4567', 'N4567', 'g4567.karl@gmail.com', '2019-07-26 12:53:45', 0, '$2y$10$5Fyos2jbesiltKrjfMsT8exsVz.e/fTZOlGEnD8t8S1ie41l9eC/m', 0, NULL, 'Karl', 'Nein', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/3/uploads/profile/104319.jpg', 'own', NULL, NULL, NULL),
(4, 'G9999', 'N9999', 'g999.flora@gmail.com', '2019-07-25 20:07:19', 0, '$2y$10$ewg16ZSYdtp3ZHsHiwH.C.bAJJgGFhuoI9yaMcdjeqVTFs/vqKPQm', 0, NULL, 'Flora', 'Fauna', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/4/uploads/profile/Flora_content_img.jpg', 'own', NULL, NULL, NULL),
(5, 'G9809', 'N9809', 'g9809@gmail.com', '2019-07-19 18:15:44', 0, '$2y$10$rVYx7G7MZUYf.25bI/z0TeBp81z/1Tw8E1T.Y6WCsRLdUI0MWvv2a', 0, NULL, 'Trebl', 'Bass', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/5/uploads/profile/61wcEoXVcuL._SX425_.jpg', 'own', NULL, NULL, NULL),
(6, 'G2312', 'N2312', 'g2312.mary@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$cpWkESBcOFk8KE41UdCxI.Dr4NRChat1AN5STdbCBM4gOcmbeQOiS', 0, NULL, 'Mary', 'Sad', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg', 'own', NULL, NULL, NULL),
(7, 'G0001', 'N0001', 'g0001.ping@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$KWpkrGfQsN62VxoVmDTLeO2Ja028fjD./nF.vcvhwhVJFqm8ymdCK', 0, NULL, 'Ping', 'Pong', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg', 'own', NULL, NULL, NULL),
(8, 'G7891', 'N7891', 'g7891.thunder@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$6T9EQnrdlKZqpRz/svXtA.XjaA.ud9TMsx.wdtLUDTm24HFmmQrle', 0, NULL, 'Thunder', 'Cracker', NULL, NULL, NULL, NULL, NULL, '2019-07-18', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg', 'own', NULL, NULL, NULL),
(9, 'G5555', 'N5555', 'g5555.proper@gmail.com', '2019-07-18 16:14:12', 0, '$2y$10$4mluEnkPFjuTnV7M5WBCAuQJguH5z4uJfQ2VfmGmlepk8WfXbwO5e', 0, NULL, 'Proper', 'Test', NULL, NULL, NULL, NULL, NULL, '2019-07-18', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg', 'own', NULL, NULL, NULL),
(10, 'G8090', 'N8090', 'g8090.ginger@gmail.com', '2019-08-05 14:51:46', 0, '$2y$10$uoM.QzuKXQQAbPdCXs4vyurrZ4WuArRL46r9Yb2zR6xKWuyaUJ6kW', 0, NULL, 'Ginger', 'Barl', NULL, NULL, NULL, NULL, NULL, '2019-07-19', NULL, 'images/user/10/uploads/profile/giphy (1).gif', 'own', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
