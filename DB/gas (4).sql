-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2025 at 06:25 AM
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
-- Database: `gas`
--

-- --------------------------------------------------------

--
-- Table structure for table `deliveryschedules`
--

CREATE TABLE `deliveryschedules` (
  `ScheduleID` int(11) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `RequestID` int(16) NOT NULL,
  `DeliveryDate` date NOT NULL,
  `GasType` varchar(100) NOT NULL,
  `ScheduledStock` int(11) NOT NULL,
  `DeliveredStock` int(11) DEFAULT 0,
  `Status` enum('scheduled','delivered','cancelled') DEFAULT 'scheduled',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deliveryschedules`
--

INSERT INTO `deliveryschedules` (`ScheduleID`, `OutletID`, `RequestID`, `DeliveryDate`, `GasType`, `ScheduledStock`, `DeliveredStock`, `Status`, `CreatedAt`) VALUES
(73, 2, 1, '2025-02-17', '', 20, 0, 'scheduled', '2025-02-10 11:05:49'),
(74, 1, 11, '2025-02-17', '2.3', 100, 0, 'scheduled', '2025-02-10 11:09:03'),
(75, 3, 12, '2025-02-17', '12.5', 110, 0, 'scheduled', '2025-02-10 11:09:35'),
(76, 2, 15, '2025-02-17', '12.5', 0, 120, 'delivered', '2025-02-10 11:12:10'),
(77, 2, 21, '2025-02-21', '22.5 Kg', 0, 10, 'delivered', '2025-02-14 03:36:32'),
(78, 2, 20, '2025-02-21', '5 Kg', 120, 0, 'scheduled', '2025-02-14 09:15:39'),
(79, 2, 16, '2025-02-21', '22.5 Kg', 0, 500, 'delivered', '2025-02-14 11:09:05'),
(80, 3, 14, '2025-02-21', '5', 120, 0, 'scheduled', '2025-02-14 11:13:40'),
(81, 3, 14, '2025-02-21', '5', 0, 120, 'delivered', '2025-02-14 11:13:52'),
(82, 2, 22, '2025-02-21', '22.5 Kg', 0, 500, 'delivered', '2025-02-14 11:17:04'),
(83, 2, 23, '2025-02-21', '5 Kg', 0, 250, 'delivered', '2025-02-14 11:18:22'),
(84, 1, 13, '2025-02-21', '22.5', 0, 65, 'delivered', '2025-02-14 12:50:11'),
(85, 1, 19, '2025-02-21', '22.5 Kg', 0, 120, 'delivered', '2025-02-14 12:51:08'),
(86, 4, 24, '2025-02-21', '22.5 Kg', 125, 0, 'scheduled', '2025-02-14 12:53:01'),
(87, 4, 25, '2025-02-21', '22.5 Kg', 75, 0, 'scheduled', '2025-02-14 12:54:37'),
(88, 4, 26, '2025-02-21', '12.5 Kg', 50, 0, 'scheduled', '2025-02-14 12:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `gasrequests`
--

CREATE TABLE `gasrequests` (
  `RequestID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `Token` varchar(20) NOT NULL,
  `GasType` varchar(10) NOT NULL,
  `RequestDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `ExpectedPickupDate` date NOT NULL,
  `Status` enum('pending','confirmed','completed','expired','reallocated','cancelled') DEFAULT 'pending',
  `PaymentStatus` enum('unpaid','paid') DEFAULT 'unpaid',
  `PaymentAmount` varchar(10) NOT NULL,
  `Returned` enum('yes','no') DEFAULT 'no',
  `OldUserID` int(11) DEFAULT NULL,
  `ReallocationDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gasrequests`
--

INSERT INTO `gasrequests` (`RequestID`, `UserID`, `OutletID`, `Token`, `GasType`, `RequestDate`, `ExpectedPickupDate`, `Status`, `PaymentStatus`, `PaymentAmount`, `Returned`, `OldUserID`, `ReallocationDate`) VALUES
(1, 8, 3, 'TOKEN12345', '12.5', '2024-12-22 10:48:35', '2024-12-29', 'pending', 'paid', '', 'no', NULL, NULL),
(2, 2, 4, 'TOKEN54321', '12.5', '2024-12-22 10:48:35', '2024-12-26', 'pending', 'unpaid', '', 'no', NULL, NULL),
(5, 8, 1, '53ECB941EA', '12.5', '2024-12-23 11:17:52', '2025-01-01', 'pending', 'paid', '', 'yes', NULL, NULL),
(6, 8, 1, 'ED42782067', '12.5', '2024-12-23 11:21:14', '2025-01-02', 'pending', 'unpaid', '', 'yes', NULL, NULL),
(7, 8, 1, '9BD6011475', '12.5', '2024-12-23 11:21:33', '2025-01-01', 'reallocated', 'unpaid', '', 'no', NULL, NULL),
(8, 8, 2, '1405461F38', '12.5', '2024-12-23 11:32:34', '2025-01-01', 'confirmed', 'paid', '', 'yes', NULL, NULL),
(9, 8, 1, 'COLOMBO OUTLET+5452C', '12.5', '2024-12-23 11:42:56', '2025-01-01', 'completed', 'paid', '', 'no', NULL, NULL),
(11, 17, 2, 'KANDYTOKEN39846', '12.5', '2024-12-23 11:48:12', '2025-01-01', 'pending', 'unpaid', '', 'no', NULL, NULL),
(12, 24, 2, 'KANDYTOKEN14876', '12.5', '2024-12-23 14:15:34', '2025-02-19', 'pending', 'paid', '', 'no', 27, '2025-02-14 12:06:34'),
(13, 2, 1, 'COLOMBOTOKEN19947', '12.5', '2024-12-23 14:51:03', '2025-01-11', 'pending', 'paid', '5500', 'yes', 1, '2025-01-06 11:40:44'),
(14, 8, 1, 'COLOMBOTOKEN19418', '12.5', '2025-01-06 16:07:47', '2025-01-15', 'completed', 'paid', '', 'yes', NULL, NULL),
(16, 8, 2, 'KANDYTOKEN84899', '22.5', '2025-01-26 09:42:00', '2025-02-13', 'cancelled', 'unpaid', '', 'no', 1, '2025-02-08 04:45:29'),
(17, 2, 1, 'COLOMBOTOKEN86321', '22.5', '2025-01-26 10:07:42', '2025-01-31', 'pending', 'paid', '7500', 'no', NULL, NULL),
(19, 25, 2, 'KANDYTOKEN88612', '12.5', '2025-02-08 07:23:21', '2025-02-18', 'pending', 'unpaid', '', 'no', 2, '2025-02-13 04:56:20'),
(21, 23, 2, 'KANDYTOKEN53885', '12.5', '2025-02-08 09:02:52', '2025-02-18', 'pending', 'unpaid', '', 'yes', NULL, NULL),
(23, 21, 1, 'COLOMBOTOKEN96429', '5', '2025-02-12 07:56:27', '2025-02-19', 'pending', 'paid', '3500', 'no', NULL, NULL),
(24, 20, 1, 'COLOMBOTOKEN24200', '12.5', '2025-02-12 09:23:22', '2025-02-14', 'pending', 'paid', '5500', 'no', NULL, NULL),
(25, 22, 2, 'KANDYTOKEN52979', '5', '2025-02-13 16:26:20', '2025-02-17', 'completed', 'paid', '3500', 'yes', NULL, NULL),
(26, 28, 2, 'OUTLETTOKEN68774', '12.5', '2025-02-14 09:38:07', '2025-02-18', 'pending', 'unpaid', '', 'no', NULL, NULL),
(27, 33, 2, 'OUTLETTOKEN19431', '2.3', '2025-02-14 09:53:59', '2025-02-27', 'pending', 'unpaid', '', 'no', NULL, NULL),
(28, 35, 2, 'OUTLETTOKEN88550', '12.5', '2025-02-14 09:55:35', '2025-02-24', 'pending', 'unpaid', '', 'no', NULL, NULL),
(29, 36, 2, 'OUTLETTOKEN73119', '12.5', '2025-02-14 09:59:07', '2025-02-24', 'completed', 'paid', '5500', 'yes', NULL, NULL),
(30, 37, 1, 'OUTLETTOKEN72124', '2.3', '2025-02-14 10:05:31', '2025-02-26', 'pending', 'unpaid', '', 'no', NULL, NULL),
(31, 38, 1, 'OUTLETTOKEN75622', '12.5', '2025-02-14 10:07:34', '2025-02-28', 'pending', 'unpaid', '', 'no', NULL, NULL),
(32, 39, 1, 'OUTLETTOKEN64151', '12.5', '2025-02-14 10:09:26', '2025-02-19', 'pending', 'unpaid', '', 'no', NULL, NULL),
(33, 40, 1, 'P-TOKEN65453', '12.5', '2025-02-14 10:17:19', '2025-02-16', 'pending', 'unpaid', '', 'no', NULL, NULL),
(36, 8, 2, 'KANDYTOKEN89647', '12.5', '2025-02-14 14:38:20', '2025-02-26', 'reallocated', 'unpaid', '', 'no', NULL, NULL),
(37, 8, 2, 'KANDYTOKEN51198', '5', '2025-02-14 14:40:24', '2025-02-20', 'completed', 'paid', '3500', 'yes', NULL, NULL),
(38, 2, 2, 'KANDYTOKEN50963', '12.5', '2025-02-14 15:49:58', '2025-02-24', 'completed', 'paid', '5500', 'yes', NULL, NULL),
(39, 41, 2, 'KP-TOKEN63287', '12.5', '2025-02-14 16:51:17', '2025-02-24', 'completed', 'paid', '5550', 'yes', NULL, NULL),
(40, 42, 2, 'KP-TOKEN97194', '5', '2025-02-14 16:52:38', '2025-02-17', 'pending', 'unpaid', '', 'no', NULL, NULL),
(41, 43, 2, 'KP-TOKEN79156', '12.5', '2025-02-15 06:50:05', '2025-02-24', 'pending', 'unpaid', '', 'no', NULL, NULL),
(42, 44, 2, 'KP-TOKEN19877', '5', '2025-02-15 06:50:40', '2025-02-28', 'reallocated', 'unpaid', '', 'no', NULL, NULL),
(43, 11, 2, 'KANDYTOKEN93892', '12.5', '2025-02-15 08:36:44', '2025-02-24', 'completed', 'paid', '5550', 'yes', NULL, NULL),
(44, 11, 2, 'KANDYTOKEN15106', '5', '2025-02-15 08:42:50', '2025-02-24', 'pending', 'unpaid', '', 'no', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `industrialrequests`
--

CREATE TABLE `industrialrequests` (
  `IndustrialRequestID` int(11) NOT NULL,
  `OrganizationID` int(11) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `RequestDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `ExpectedPickupDate` date DEFAULT NULL,
  `PaymentStatus` varchar(10) NOT NULL DEFAULT 'Unpaid',
  `PaymentAmount` varchar(100) NOT NULL,
  `Status` enum('pending','completed','confirmed','Delivered','cancelled') DEFAULT 'pending',
  `RequestedAmount` int(11) NOT NULL,
  `GasType` varchar(100) NOT NULL,
  `Token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `industrialrequests`
--

INSERT INTO `industrialrequests` (`IndustrialRequestID`, `OrganizationID`, `OutletID`, `RequestDate`, `ExpectedPickupDate`, `PaymentStatus`, `PaymentAmount`, `Status`, `RequestedAmount`, `GasType`, `Token`) VALUES
(2, 2, 3, '2024-12-22 10:48:35', NULL, 'paid', '1050000', 'pending', 300, '5', 'BTOKEN72317'),
(3, 1, 2, '2025-01-03 16:32:47', NULL, 'paid', '388500', 'pending', 111, '5', 'BTOKEN72365'),
(4, 3, 1, '2025-01-03 16:33:52', NULL, 'paid', '777000', 'Delivered', 222, '5', 'BTOKEN724897'),
(5, 2, 3, '2025-01-03 16:42:46', NULL, 'paid', '1831500', 'pending', 333, '12.5', 'BTOKEN72456'),
(6, 3, 1, '2025-01-03 17:09:40', NULL, 'paid', '2442000', 'Delivered', 444, '12.5', 'BTOKEN98746'),
(12, 3, 4, '2025-02-13 16:44:57', '2025-02-18', 'Unpaid', '', 'pending', 10, '12.5', 'BTOKEN80886'),
(13, 3, 2, '2025-02-13 16:54:50', '2025-02-26', 'Unpaid', '', 'pending', 12, '5', 'BTOKEN72718');

-- --------------------------------------------------------

--
-- Table structure for table `mainstock`
--

CREATE TABLE `mainstock` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mainstock`
--

INSERT INTO `mainstock` (`id`, `type`, `quantity`, `last_updated`) VALUES
(1, '22.5 Kg', 700, '2025-02-15 08:42:05'),
(2, '12.5 Kg', 980, '2025-02-14 18:00:15'),
(3, '5 Kg', 650, '2025-02-14 15:48:04'),
(4, '2.3 Kg', 1150, '2025-02-13 14:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `OrganizationID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Certification` varchar(255) DEFAULT NULL,
  `ContactPerson` varchar(100) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `RegistrationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`OrganizationID`, `Name`, `Certification`, `ContactPerson`, `ContactNumber`, `Email`, `password`, `RegistrationDate`) VALUES
(1, 'ABC Industries', 'Certified', 'Peter Johnson', '0111122233', 'contact@abcindustries.com', '', '2024-12-22 10:48:35'),
(2, 'XYZ Enterprises', 'Certified', 'Susan Lee', '0111144455', 'info@xyzenterprises.com', '', '2024-12-22 10:48:35'),
(3, 'QWE Industries', 'Certified', 'william', '11223456789', 'a@gmail.com', '$2y$10$LBsLaFukxwkTB7otSggFhOoFfgJBiz3UVh.L6ziGrwE6rLKxQG3Cq', '2025-01-03 16:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `outletrequests`
--

CREATE TABLE `outletrequests` (
  `RequestID` int(100) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `GasType` varchar(50) NOT NULL,
  `RequestAmount` int(255) NOT NULL,
  `RequestDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outletrequests`
--

INSERT INTO `outletrequests` (`RequestID`, `OutletID`, `GasType`, `RequestAmount`, `RequestDate`, `Status`) VALUES
(1, 2, '', 20, '2025-02-10 15:35:49', 'scheduled'),
(2, 2, '', 10, '2025-02-08 06:58:04', 'scheduled'),
(3, 2, '', 25, '2025-02-08 09:10:19', 'scheduled'),
(10, 2, '5', 25, '2025-01-26 09:17:10', 'scheduled'),
(11, 1, '2.3', 100, '2025-02-10 15:39:03', 'scheduled'),
(12, 3, '12.5', 110, '2025-02-10 15:39:35', 'scheduled'),
(13, 1, '22.5', 65, '2025-02-14 17:20:11', 'scheduled'),
(14, 3, '5', 120, '2025-02-14 15:43:40', 'scheduled'),
(15, 2, '12.5', 120, '2025-02-10 15:42:10', 'scheduled'),
(16, 2, '22.5 Kg', 500, '2025-02-14 15:39:05', 'scheduled'),
(17, 2, '12.5 Kg', 1000, '2025-02-14 17:05:57', 'cancelled'),
(18, 2, '22.5 Kg', 1000, '2025-02-19 18:30:00', 'Pending'),
(19, 1, '22.5 Kg', 120, '2025-02-14 17:21:08', 'scheduled'),
(20, 2, '5 Kg', 120, '2025-02-14 13:45:39', 'scheduled'),
(21, 2, '22.5 Kg', 10, '2025-02-14 08:06:32', 'scheduled'),
(22, 2, '22.5 Kg', 500, '2025-02-14 15:47:04', 'scheduled'),
(23, 2, '5 Kg', 250, '2025-02-14 15:48:22', 'scheduled'),
(24, 4, '22.5 Kg', 125, '2025-02-14 17:23:01', 'scheduled'),
(25, 4, '22.5 Kg', 75, '2025-02-14 17:24:37', 'scheduled'),
(26, 4, '12.5 Kg', 50, '2025-02-14 17:25:39', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `outlets`
--

CREATE TABLE `outlets` (
  `OutletID` int(11) NOT NULL,
  `OutletName` varchar(100) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `22.5` int(255) NOT NULL,
  `12.5` int(255) NOT NULL,
  `5` int(255) NOT NULL,
  `2.3` int(255) NOT NULL,
  `LastRestocked` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outlets`
--

INSERT INTO `outlets` (`OutletID`, `OutletName`, `Location`, `Email`, `ContactNumber`, `22.5`, `12.5`, `5`, `2.3`, `LastRestocked`) VALUES
(1, 'Colombo', 'Colombo, Western Province', '', '0112233445', 475, 499, 499, 500, '2025-02-14 17:21:36'),
(2, 'Kandy', 'Kandy, Central Province', 'moha@gmail.com', '0812233445', 1510, 116, 435, 200, '2025-02-14 15:48:49'),
(3, 'Galle', 'Galle, Southern Province', '', '0912233445', 500, 500, 370, 100, '2025-02-14 15:44:16'),
(4, 'Mawanella', 'Mawanella', 'mwoutlet@gmail.com', '21542365456', 500, 190, 303, 500, '2025-02-05 03:45:52'),
(5, 'Ratnapura', 'Ratnapura', 'ratnapura@gmail.com', '12346465464', 100, 100, 100, 100, '2025-02-14 06:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `outletstockprice`
--

CREATE TABLE `outletstockprice` (
  `id` int(11) NOT NULL,
  `Type` varchar(10) NOT NULL,
  `Price` int(100) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outletstockprice`
--

INSERT INTO `outletstockprice` (`id`, `Type`, `Price`, `last_updated`) VALUES
(1, '22.5', 7550, '2025-02-14 08:18:38'),
(2, '12.5', 5550, '2025-02-14 08:18:38'),
(3, '5', 3500, '2025-02-14 08:18:38'),
(4, '2.3', 1500, '2025-02-14 08:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `UserType` varchar(10) NOT NULL,
  `RequestID` int(11) NOT NULL,
  `UserID` int(100) NOT NULL,
  `OutletID` int(100) NOT NULL,
  `GasType` varchar(100) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('cash','card','online') NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `UserType`, `RequestID`, `UserID`, `OutletID`, `GasType`, `Amount`, `PaymentMethod`, `PaymentDate`) VALUES
(1, 'consumer', 1, 0, 2, '', 2500.00, 'cash', '2024-12-22 10:48:35'),
(2, 'consumer', 2, 0, 2, '', 3000.00, 'cash', '2024-12-22 10:48:35'),
(3, 'consumer', 17, 2, 1, '22.5', 7500.00, 'cash', '2025-02-13 15:06:35'),
(4, 'business', 6, 0, 1, '12.5', 2442000.00, 'cash', '2025-02-13 15:37:11'),
(5, 'business', 5, 2, 3, '12.5', 1831500.00, 'cash', '2025-02-13 16:02:55'),
(6, 'consumer', 25, 22, 2, '5', 3500.00, 'cash', '2025-02-14 13:44:40'),
(7, 'consumer', 37, 8, 2, '5', 3500.00, 'cash', '2025-02-14 15:58:37'),
(8, 'consumer', 38, 2, 2, '12.5', 5500.00, 'cash', '2025-02-14 16:04:06'),
(9, 'consumer', 29, 36, 2, '12.5', 5500.00, 'cash', '2025-02-14 16:08:36'),
(10, 'consumer', 39, 41, 2, '12.5', 5550.00, 'cash', '2025-02-14 17:27:10'),
(11, 'consumer', 43, 11, 2, '12.5', 5550.00, 'cash', '2025-02-15 08:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `outletID` int(10) NOT NULL DEFAULT 0,
  `Email` varchar(100) NOT NULL DEFAULT 'No Email',
  `PhoneNumber` varchar(15) NOT NULL,
  `NIC` varchar(12) NOT NULL,
  `RegOutlet` varchar(30) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` enum('consumer','outlet_manager','dispatch_office','physical_consumer') DEFAULT 'consumer',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `outletID`, `Email`, `PhoneNumber`, `NIC`, `RegOutlet`, `PasswordHash`, `Role`, `CreatedAt`) VALUES
(1, 'user', 0, 'user3@gmail.com', '0771234567', '987654321V', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'consumer', '2024-12-22 10:48:35'),
(2, 'Nabeel', 2, 'nabeel@gmail.com', '0777654321', '123456789V', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'consumer', '2024-12-22 10:48:35'),
(3, 'William', 1, 'a@gmail.com', '11223456789', '112233445V', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'outlet_manager', '2024-12-22 10:48:35'),
(4, 'Dispatch Officer', 0, 'admin@gmail.com', '0751234567', '998877665V', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'dispatch_office', '2024-12-22 10:48:35'),
(8, 'Shaheem', 2, 'mhd01@gmail.com', '0765489236', '25856562v', '', '$2y$10$/ibPHNMmbv0e1lwkT.oNruvOdJhgZ7fTPSLImswy61OWQeMg6qYzO', 'consumer', '2024-12-23 09:57:59'),
(9, 'admin1', 0, 'admin1@gmail.com', '3214567859', '32651459v', '', '$2y$10$5MlNrzmp/O833qutbaITt.DVTqzQXqgAmTlU9IBORE9DZrjKDXK6m', 'dispatch_office', '2024-12-23 09:58:39'),
(10, 'Kandy Outlet', 2, 'moh02@gmail.com', '32564897', '213423561v', '', '$2y$10$Bd7BR8OMrN8naScCvcVIZuyTHxQKZS.H8ac1ziQemMmA3OGEghZ9C', 'outlet_manager', '2024-12-23 09:59:06'),
(11, 'muadh', 2, 'ah@gmail.com', '213564789', '256457892v', '', '$2y$10$sk1f1TVTI9RtehBAPgAmfuujnZZ/0TFoVBM.85b.qO1Z8uuuIId5K', 'consumer', '2024-12-23 15:09:52'),
(12, 'galle outlet', 3, 'galleoutlet@gmail.com', '12545265465', '216546721564', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'outlet_manager', '2024-12-28 09:55:01'),
(15, 'white', 1, 'white@gmail.com', '12464651321', '644256421v', '', '$2y$10$uq48TfZpHnw3krKTbRcs0OdtUfyZ7t5SmZDeCOOlqegXe1LFbHpBe', 'consumer', '2025-02-07 15:12:04'),
(16, 'black', 3, 'black@gmail.com', '764894664', '456724616v', '', '$2y$10$F.JM9NzWuF52CxldA/CFXeg5GIvf1N7uc0yoR2yJPuGuBAVqSSqAi', 'consumer', '2025-02-07 15:12:45'),
(17, 'Messi', 2, 'mh@gmail.com', '45654684412', '786454664v', '', '$2y$10$b/M20f9Ta9hvrx1tetvkgO30cPbvhFNCrQgJ27fpARpq5iozx6IIG', 'consumer', '2025-02-07 15:15:55'),
(18, 'ronaldo', 1, 'ronaldo@gmail.com', '456715654', '746846784v', '', '$2y$10$fAJCEOKPFdfE4OTO2eZ6bulEwHb0lbGFcVBuIEGO7DrluYXe8m.eK', 'consumer', '2025-02-07 15:17:27'),
(19, 'zaltan', 1, 'zaltan@gmail.com', '216465121', '64654213v', '', '$2y$10$ApB9p6TQsw4wwbPOTp.Xo.L6RW.HXiCtgXhenQjYHDSpnKKANNzZ2', 'consumer', '2025-02-07 15:20:24'),
(20, 'rashford', 1, 'rashford@gmail.com', '546578126', '686568623v', '', '$2y$10$2yZxnEmbLxF33uy2de.SmOW2Nx5bjSWL5rVvZncoo5IhzJrFcTPfC', 'consumer', '2025-02-07 15:21:29'),
(21, 'mbappe', 1, 'mbappe@gmail.com', '1654561234', '465651231v', '', '$2y$10$wz5PuGrxkbSuWGuj3YzFp.M0git3Cj/tBraKhk83NJodzobMIYYyG', 'consumer', '2025-02-07 15:23:39'),
(22, 'halaand', 2, 'mhd321@gmail.com', '874597212', '897564872v', '', '$2y$10$Vwnew0rPk1k5nKLMV3qrT.hUh20kvMVFUGckW8k2XnSIA/oXse7V.', 'consumer', '2025-02-07 15:26:44'),
(23, 'mohamedsafwan', 2, 'mohamed@gmail.com', '0765544321', '6442576421v', '', '$2y$10$540ptZGTWwJoT4g72jiEweO5e0iP.FULHMKh4IXfusq6E6ApqGGTm', 'consumer', '2025-02-08 09:01:33'),
(24, 'Messi Jr', 2, 'No Email', '6458786456', '644256421x', '', 'None', 'physical_consumer', '2025-02-11 09:39:38'),
(25, 'Neymar Jr', 2, 'No Email', '314231324', '12654652123v', '', 'None', 'physical_consumer', '2025-02-11 10:03:13'),
(26, 'Mawanella Outlet', 4, 'walter@gmail.com', '12654561324', '16345457845v', '', '$2y$10$uDbqOcY/PdsXs5.7WTNEJOz1GrGUOCbuNJms8t/RPBvyNOr1eXUIO', 'outlet_manager', '2025-02-12 04:33:43'),
(27, 'Sajath', 2, 'No Email', '1354564654', '254684845v', '', 'None', 'physical_consumer', '2025-02-13 09:27:25'),
(28, 'khabib', 2, 'No Email', '65446546', '454564564x', '', '', 'physical_consumer', '2025-02-14 09:38:07'),
(33, 'Dustin', 2, 'No Email', '646546546', '45646546v', '', '', 'physical_consumer', '2025-02-14 09:53:59'),
(35, 'justin', 2, 'No Email', '64654646', '23465465v', '', '', 'physical_consumer', '2025-02-14 09:55:35'),
(36, 'Drake', 2, 'No Email', '4654654646', '464564564c', '', '', 'physical_consumer', '2025-02-14 09:59:07'),
(37, 'Carlos', 1, 'No Email', '264564164', '64894564v', '', '', 'physical_consumer', '2025-02-14 10:05:31'),
(38, 'moicano', 1, 'No Email', '214654654', '564564654v', '', '', 'physical_consumer', '2025-02-14 10:07:34'),
(39, 'Tina', 1, 'No Email', '564564654', '64564654x', '', '', 'physical_consumer', '2025-02-14 10:09:26'),
(40, 'Faathik', 1, 'No Email', '134564654', '134564664v', '', '', 'physical_consumer', '2025-02-14 10:17:19'),
(41, 'Vijay', 2, 'No Email', '1324564651', '', '', '', 'physical_consumer', '2025-02-14 16:51:17'),
(42, 'Suriya', 2, 'No Email', '654657412', 'No NIC', '', '', 'physical_consumer', '2025-02-14 16:52:38'),
(43, 'Muadh Ahamed', 2, 'No Email', '12456456421', '200207302477', '', 'None', 'physical_consumer', '2025-02-15 06:38:10'),
(44, 'trump', 2, 'No Email', '1231564122', '544684654a', '', '', 'physical_consumer', '2025-02-15 06:50:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `deliveryschedules`
--
ALTER TABLE `deliveryschedules`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `OutletID` (`OutletID`);

--
-- Indexes for table `gasrequests`
--
ALTER TABLE `gasrequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD UNIQUE KEY `Token` (`Token`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `OutletID` (`OutletID`);

--
-- Indexes for table `industrialrequests`
--
ALTER TABLE `industrialrequests`
  ADD PRIMARY KEY (`IndustrialRequestID`),
  ADD KEY `OrganizationID` (`OrganizationID`),
  ADD KEY `OutletID` (`OutletID`);

--
-- Indexes for table `mainstock`
--
ALTER TABLE `mainstock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`OrganizationID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `outletrequests`
--
ALTER TABLE `outletrequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `OutletID` (`OutletID`);

--
-- Indexes for table `outlets`
--
ALTER TABLE `outlets`
  ADD PRIMARY KEY (`OutletID`);

--
-- Indexes for table `outletstockprice`
--
ALTER TABLE `outletstockprice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `RequestID` (`RequestID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `OutletID` (`OutletID`),
  ADD KEY `GasType` (`GasType`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`),
  ADD UNIQUE KEY `NIC` (`NIC`),
  ADD KEY `Email` (`Email`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deliveryschedules`
--
ALTER TABLE `deliveryschedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `gasrequests`
--
ALTER TABLE `gasrequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `industrialrequests`
--
ALTER TABLE `industrialrequests`
  MODIFY `IndustrialRequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mainstock`
--
ALTER TABLE `mainstock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `OrganizationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `outletrequests`
--
ALTER TABLE `outletrequests`
  MODIFY `RequestID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `outlets`
--
ALTER TABLE `outlets`
  MODIFY `OutletID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `outletstockprice`
--
ALTER TABLE `outletstockprice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `deliveryschedules`
--
ALTER TABLE `deliveryschedules`
  ADD CONSTRAINT `deliveryschedules_ibfk_1` FOREIGN KEY (`OutletID`) REFERENCES `outlets` (`OutletID`) ON DELETE CASCADE;

--
-- Constraints for table `gasrequests`
--
ALTER TABLE `gasrequests`
  ADD CONSTRAINT `gasrequests_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `gasrequests_ibfk_2` FOREIGN KEY (`OutletID`) REFERENCES `outlets` (`OutletID`) ON DELETE CASCADE;

--
-- Constraints for table `industrialrequests`
--
ALTER TABLE `industrialrequests`
  ADD CONSTRAINT `industrialrequests_ibfk_1` FOREIGN KEY (`OrganizationID`) REFERENCES `organizations` (`OrganizationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `industrialrequests_ibfk_2` FOREIGN KEY (`OutletID`) REFERENCES `outlets` (`OutletID`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `gasrequests` (`RequestID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
