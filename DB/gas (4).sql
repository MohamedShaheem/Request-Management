-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2025 at 10:44 AM
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
  `ScheduledStock` int(11) NOT NULL,
  `DeliveredStock` int(11) DEFAULT 0,
  `Status` enum('scheduled','delivered','cancelled') DEFAULT 'scheduled',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deliveryschedules`
--

INSERT INTO `deliveryschedules` (`ScheduleID`, `OutletID`, `RequestID`, `DeliveryDate`, `ScheduledStock`, `DeliveredStock`, `Status`, `CreatedAt`) VALUES
(1, 1, 0, '2024-12-24', 0, 100, 'delivered', '2024-12-22 10:48:35'),
(2, 2, 0, '2024-12-25', 0, 80, 'delivered', '2024-12-22 10:48:35'),
(3, 3, 0, '2024-12-26', 0, 60, 'delivered', '2024-12-22 10:48:35'),
(5, 1, 0, '2025-02-01', 0, 22, 'delivered', '2025-01-03 11:27:41'),
(6, 3, 0, '2025-02-02', 100, 0, 'scheduled', '2025-01-03 11:27:59'),
(8, 1, 0, '2025-03-05', 123, 0, 'scheduled', '2025-01-03 12:04:32'),
(9, 2, 0, '2025-01-03', 0, 12, 'delivered', '2025-01-03 14:35:56'),
(51, 2, 0, '2025-02-28', 200, 0, 'scheduled', '2025-01-19 16:43:10'),
(52, 2, 0, '2025-01-30', 0, 221, 'delivered', '2025-01-19 16:46:15'),
(53, 2, 0, '2025-01-31', 200, 0, 'scheduled', '2025-01-19 16:57:32'),
(54, 2, 0, '2025-01-31', 222, 0, 'scheduled', '2025-01-19 16:59:50'),
(56, 2, 5, '2025-01-27', 100, 0, 'scheduled', '2025-01-20 08:00:18'),
(57, 2, 1, '2025-01-27', 20, 0, 'scheduled', '2025-01-20 08:00:35'),
(58, 2, 5, '2025-01-27', 100, 0, 'scheduled', '2025-01-20 08:03:58'),
(59, 2, 1, '2025-01-27', 20, 0, 'scheduled', '2025-01-20 08:10:55'),
(60, 2, 3, '2025-01-28', 25, 0, 'scheduled', '2025-01-21 04:34:41');

-- --------------------------------------------------------

--
-- Table structure for table `gasrequests`
--

CREATE TABLE `gasrequests` (
  `RequestID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `Token` varchar(20) NOT NULL,
  `RequestDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `ExpectedPickupDate` date NOT NULL,
  `Status` enum('pending','confirmed','completed','expired','reallocated','cancelled') DEFAULT 'pending',
  `PaymentStatus` enum('unpaid','paid') DEFAULT 'unpaid',
  `Returned` enum('yes','no') DEFAULT 'no',
  `OldUserID` int(11) DEFAULT NULL,
  `ReallocationDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gasrequests`
--

INSERT INTO `gasrequests` (`RequestID`, `UserID`, `OutletID`, `Token`, `RequestDate`, `ExpectedPickupDate`, `Status`, `PaymentStatus`, `Returned`, `OldUserID`, `ReallocationDate`) VALUES
(1, 8, 1, 'TOKEN12345', '2024-12-22 10:48:35', '2024-12-29', 'pending', 'paid', 'no', NULL, NULL),
(2, 2, 2, 'TOKEN54321', '2024-12-22 10:48:35', '2024-12-26', 'pending', 'unpaid', 'no', NULL, NULL),
(5, 8, 1, '53ECB941EA', '2024-12-23 11:17:52', '2025-01-01', 'pending', 'paid', 'yes', NULL, NULL),
(6, 8, 1, 'ED42782067', '2024-12-23 11:21:14', '2025-01-02', 'pending', 'unpaid', 'yes', NULL, NULL),
(7, 8, 1, '9BD6011475', '2024-12-23 11:21:33', '2025-01-01', 'reallocated', 'unpaid', 'no', NULL, NULL),
(8, 8, 2, '1405461F38', '2024-12-23 11:32:34', '2025-01-01', 'pending', 'unpaid', 'no', NULL, NULL),
(9, 8, 1, 'COLOMBO OUTLET+5452C', '2024-12-23 11:42:56', '2025-01-01', 'completed', 'paid', 'no', NULL, NULL),
(10, 8, 3, 'GALLE1EC41855999', '2024-12-23 11:47:11', '2025-01-01', 'pending', 'unpaid', 'no', NULL, NULL),
(11, 8, 2, 'KANDYTOKEN39846', '2024-12-23 11:48:12', '2025-01-01', 'cancelled', 'unpaid', 'no', NULL, NULL),
(12, 8, 2, 'KANDYTOKEN14876', '2024-12-23 14:15:34', '2025-01-01', 'pending', 'unpaid', 'no', NULL, NULL),
(13, 2, 1, 'COLOMBOTOKEN19947', '2024-12-23 14:51:03', '2025-01-11', 'pending', 'unpaid', 'yes', 1, '2025-01-06 11:40:44'),
(14, 8, 1, 'COLOMBOTOKEN19418', '2025-01-06 16:07:47', '2025-01-15', 'completed', 'paid', 'yes', NULL, NULL),
(15, 8, 2, 'KANDYTOKEN99397', '2025-01-19 15:24:30', '2025-01-31', 'pending', 'unpaid', 'no', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `industrialrequests`
--

CREATE TABLE `industrialrequests` (
  `IndustrialRequestID` int(11) NOT NULL,
  `OrganizationID` int(11) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `RequestDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `RequestedAmount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `industrialrequests`
--

INSERT INTO `industrialrequests` (`IndustrialRequestID`, `OrganizationID`, `OutletID`, `RequestDate`, `Status`, `RequestedAmount`) VALUES
(2, 2, 3, '2024-12-22 10:48:35', 'pending', 300),
(3, 3, 2, '2025-01-03 16:32:47', 'pending', 111),
(4, 3, 1, '2025-01-03 16:33:52', 'pending', 222),
(5, 3, 3, '2025-01-03 16:42:46', 'pending', 333),
(6, 3, 1, '2025-01-03 17:09:40', 'pending', 444),
(7, 3, 1, '2025-01-03 17:09:44', 'pending', 251);

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
(3, 'QWE', 'Certified', 'shaheem', '11223456789', 'a@gmail.com', '$2y$10$LBsLaFukxwkTB7otSggFhOoFfgJBiz3UVh.L6ziGrwE6rLKxQG3Cq', '2025-01-03 16:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `outletpayments`
--

CREATE TABLE `outletpayments` (
  `PaymentID` int(11) NOT NULL,
  `RequestID` int(11) NOT NULL,
  `outletID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('cash','card','online') NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `outletrequests`
--

CREATE TABLE `outletrequests` (
  `RequestID` int(100) NOT NULL,
  `OutletID` int(11) NOT NULL,
  `RequestAmount` int(255) NOT NULL,
  `RequestDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outletrequests`
--

INSERT INTO `outletrequests` (`RequestID`, `OutletID`, `RequestAmount`, `RequestDate`, `Status`) VALUES
(1, 2, 20, '2025-01-20 12:40:55', 'scheduled'),
(2, 2, 10, '2025-01-22 18:30:00', 'Pending'),
(3, 2, 25, '2025-01-21 09:04:41', 'scheduled'),
(4, 2, 10, '2025-01-29 18:30:00', 'Pending'),
(5, 2, 100, '2025-01-20 12:33:58', 'scheduled'),
(6, 2, 200, '2025-01-20 10:18:05', 'confirm'),
(7, 2, 200, '2025-01-20 10:18:26', 'confirm'),
(8, 2, 200, '2025-01-30 18:30:00', 'Pending'),
(9, 2, 10, '2025-01-28 18:30:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `outlets`
--

CREATE TABLE `outlets` (
  `OutletID` int(11) NOT NULL,
  `OutletName` varchar(100) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `CurrentStock` int(11) DEFAULT 0,
  `LastRestocked` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outlets`
--

INSERT INTO `outlets` (`OutletID`, `OutletName`, `Location`, `ContactNumber`, `CurrentStock`, `LastRestocked`) VALUES
(1, 'Colombo', 'Colombo, Western Province', '0112233445', 48, '2024-12-22 10:48:35'),
(2, 'Kandy', 'Kandy, Central Province', '0812233445', 231, '2025-01-20 09:51:34'),
(3, 'Galle', 'Galle, Southern Province', '0912233445', 100, '2025-01-03 14:35:26');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `RequestID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('cash','card','online') NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `RequestID`, `Amount`, `PaymentMethod`, `PaymentDate`) VALUES
(1, 1, 2500.00, 'cash', '2024-12-22 10:48:35'),
(2, 2, 3000.00, 'online', '2024-12-22 10:48:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `outletID` int(10) NOT NULL DEFAULT 0,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) NOT NULL,
  `NIC` varchar(12) NOT NULL,
  `RegOutlet` varchar(30) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` enum('consumer','outlet_manager','dispatch_office') DEFAULT 'consumer',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `outletID`, `Email`, `PhoneNumber`, `NIC`, `RegOutlet`, `PasswordHash`, `Role`, `CreatedAt`) VALUES
(1, 'user', 0, 'user3@gmail.com', '0771234567', '987654321V', '', 'hashedpassword1', 'consumer', '2024-12-22 10:48:35'),
(2, 'Jane Smith', 0, 'janesmith@example.com', '0777654321', '123456789V', '', 'hashedpassword2', 'consumer', '2024-12-22 10:48:35'),
(3, 'Outlet3', 2, 'outlet1@gmail.com', '0761234567', '112233445V', '', 'hashedpassword3', 'outlet_manager', '2024-12-22 10:48:35'),
(4, 'Dispatch Officer', 0, 'admin@gmail.com', '0751234567', '998877665V', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'dispatch_office', '2024-12-22 10:48:35'),
(8, 'Consumer Ben', 2, 'mhdshaheem01@gmail.com', '0765489236', '25856562v', '', '$2y$10$/ibPHNMmbv0e1lwkT.oNruvOdJhgZ7fTPSLImswy61OWQeMg6qYzO', 'consumer', '2024-12-23 09:57:59'),
(9, 'admin1', 0, 'admin1@gmail.com', '3214567859', '32651459v', '', '$2y$10$5MlNrzmp/O833qutbaITt.DVTqzQXqgAmTlU9IBORE9DZrjKDXK6m', 'dispatch_office', '2024-12-23 09:58:39'),
(10, 'Kandy Outlet', 2, 'outlet@gmail.com', '32564897', '213423561v', '', '$2y$10$Bd7BR8OMrN8naScCvcVIZuyTHxQKZS.H8ac1ziQemMmA3OGEghZ9C', 'outlet_manager', '2024-12-23 09:59:06'),
(11, 'user2', 0, 'user2@gmail.com', '213564789', '256457892v', '', '$2y$10$sk1f1TVTI9RtehBAPgAmfuujnZZ/0TFoVBM.85b.qO1Z8uuuIId5K', 'consumer', '2024-12-23 15:09:52'),
(12, 'galle outlet', 3, 'galleoutlet@gmail.com', '12545265465', '216546721564', '', '$2y$10$Nvo3ogpaccREIjo4mhTRuub6aXqBUv/gQfHEdUZngWKQKW1g/pqRG', 'outlet_manager', '2024-12-28 09:55:01');

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
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`OrganizationID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `outletpayments`
--
ALTER TABLE `outletpayments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `outletpayments_ibfk_1` (`RequestID`),
  ADD KEY `outletpayments_ibfk_2` (`outletID`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `RequestID` (`RequestID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`),
  ADD UNIQUE KEY `NIC` (`NIC`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deliveryschedules`
--
ALTER TABLE `deliveryschedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `gasrequests`
--
ALTER TABLE `gasrequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `industrialrequests`
--
ALTER TABLE `industrialrequests`
  MODIFY `IndustrialRequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `OrganizationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `outletpayments`
--
ALTER TABLE `outletpayments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outletrequests`
--
ALTER TABLE `outletrequests`
  MODIFY `RequestID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `outlets`
--
ALTER TABLE `outlets`
  MODIFY `OutletID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Constraints for table `outletpayments`
--
ALTER TABLE `outletpayments`
  ADD CONSTRAINT `outletpayments_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `outletrequests` (`RequestID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outletpayments_ibfk_2` FOREIGN KEY (`outletID`) REFERENCES `outletrequests` (`OutletID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `gasrequests` (`RequestID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
