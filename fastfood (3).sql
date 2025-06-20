-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 05:51 AM
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
-- Database: `fastfood`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_ID` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `contact_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_ID`, `first_name`, `last_name`, `email`, `username`, `contact_number`) VALUES
(1, 'cielo', 'santos', 'wwwww', 'admin1', 33333);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `birthdate` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `first_name`, `last_name`, `email`, `phone_number`, `street`, `city`, `postal_code`, `registration_date`, `birthdate`, `user_id`) VALUES
(4, 'San Juan Vincente', 'Dela Cruz', 'juan.delacruz@example.com', '09171234567', '123 Rizal Street', 'Quezon City', '1100', '2025-06-15 14:52:00', '1990-05-21', 4),
(5, 'Maria', 'Santos', 'maria.santos@example.com', '09987654321', '456 Mabini Avenue', 'Makati', '1226', '2025-06-15 14:53:00', '1988-09-15', NULL),
(6, 'Carlos', 'Reyes', 'carlos.reyes@example.com', '09256473829', '789 Bonifacio Road', 'Pasig', '1600', '2025-06-15 14:54:00', '1995-12-03', NULL),
(7, 'Anna', 'Garcia', 'anna.garcia@example.com', '09324681357', '101 Katipunan Street', 'Manila', '1000', '2025-06-15 14:55:00', '1982-07-30', NULL),
(8, 'Mark', 'Luz', 'mark.luz@example.com', '09413579246', '202 Aguinaldo Blvd', 'Taguig', '1630', '2025-06-15 14:56:00', '1998-03-14', NULL),
(11, 'Greg', 'Lazarte', 'greglazarte36@gmail.com', '0928', '1728', 'manila', '1009', '2025-06-20 09:59:13', '2005-07-26', 35),
(12, 'greg', 'greg', 'greg1@gmail.com', '213', '123 Rizal Street', 'Quezon City', '1009', '2025-06-20 10:08:09', '1222-12-12', 36),
(13, 'greg', 'lazarte', 'gregpogi@gmail.com', '0928', '1718 RD1', 'manila', '1009', '2025-06-20 10:34:49', '2005-07-26', 37);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `shift_timing` varchar(20) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `first_name`, `last_name`, `contact_number`, `role`, `shift_timing`, `user_id`) VALUES
(23, 'cielo', 'aguilar', 915999999, 'employee', '8:00 AM - 6:00 PM', 0),
(24, 'Daniel', 'Perez', 2147483647, 'Order Processor', '09:00:00.000000', 0),
(25, 'Sofia', 'Ramirez', 2147483647, 'Customer Support Representative', '13:30:00.000000', 0),
(26, 'Lucas', 'Torres', 2147483647, 'Kitchen Staff', '11:45:00.000000', 0),
(27, 'Isabelle', 'Martinez', 2147483647, 'Delivery Dispatcher', '17:00:00.000000', 0),
(28, 'Nathan', 'Fernandez', 2147483647, 'Food Packer', '21:30:00.000000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `Inventory_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Stock_Status` varchar(255) NOT NULL,
  `Last_Updated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Inventory_ID`, `Product_ID`, `Quantity`, `Stock_Status`, `Last_Updated`) VALUES
(1, 1, 26, 'In Stock', '2025-06-15'),
(2, 2, 40, 'In Stock', '2025-06-15'),
(3, 3, 60, 'In Stock', '2025-06-15'),
(4, 4, 45, 'In Stock', '2025-06-15'),
(5, 5, 100, 'In Stock', '2025-06-15'),
(6, 6, 80, 'In Stock', '2025-06-15'),
(7, 7, 30, 'In Stock', '2025-06-15'),
(8, 8, 35, 'In Stock', '2025-06-15'),
(9, 9, 200, 'In Stock', '2025-06-15'),
(10, 10, 200, 'In Stock', '2025-06-15'),
(11, 11, 150, 'In Stock', '2025-06-15'),
(12, 12, 100, 'In Stock', '2025-06-15'),
(13, 13, 100, 'In Stock', '2025-06-15'),
(14, 14, 80, 'In Stock', '2025-06-15');

--
-- Triggers `inventory`
--
DELIMITER $$
CREATE TRIGGER `inventory_stock_status_update` BEFORE UPDATE ON `inventory` FOR EACH ROW BEGIN
  IF NEW.Quantity = 0 THEN
    SET NEW.Stock_Status = 'Out of Stock';
  ELSEIF NEW.Quantity < 25 THEN
    SET NEW.Stock_Status = 'Low on Stock';
  ELSE
    SET NEW.Stock_Status = 'In Stock';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_Type` enum('admin','employee','customer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `preparation_time` int(11) DEFAULT NULL,
  `images` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`item_id`, `item_name`, `description`, `category`, `price`, `availability`, `preparation_time`, `images`) VALUES
(1, 'Cheeseburger', 'Beef patty, cheese, pickles, toasted bun.', 'Burgers', 99.00, 1, 10, 0x6368656573656275726765722e6a7067),
(2, 'Double Burger', 'Double beef, lettuce, tomato, special sauce.', 'Burgers', 149.00, 1, 10, 0x646f75626c652e6a7067),
(3, 'Fries', 'Crispy golden fries with salt.', 'Fries & Sides', 59.00, 1, 7, 0x66726965732e6a7067),
(4, 'Chicken Nuggets', 'Bite-sized and crispy with dip.', 'Fries & Sides', 89.00, 1, 8, 0x6e7567732e6a7067),
(5, 'Softdrinks', 'Choice of Coke, Sprite, or Root Beer.', 'Drinks', 40.00, 1, 3, 0x736f64612e6a7067),
(6, 'Iced tea', 'Chilled lemon iced tea.', 'Drinks', 45.00, 1, 3, 0x61797374692e6a7067),
(7, 'Sundae', 'Vanilla ice cream with chocolate drizzle.', 'Desserts', 59.00, 1, 5, 0x73756e6461652e6a7067),
(8, 'Apple Pie', 'Warm and crispy handheld pie.', 'Desserts', 49.00, 1, 6, 0x7069652e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `menu_product`
--

CREATE TABLE `menu_product` (
  `MenuProductID` int(11) NOT NULL,
  `Quantity_Used` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_product`
--

INSERT INTO `menu_product` (`MenuProductID`, `Quantity_Used`) VALUES
(1, 1),
(2, 2),
(3, 1),
(4, 3),
(5, 1),
(6, 4),
(7, 2),
(8, 1),
(9, 3),
(10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `customer_id`, `employee_id`, `order_date`, `order_status`, `total_amount`) VALUES
(5, 12, NULL, '2025-06-16 02:43:38', 'pending', 3456.00),
(6, 12, NULL, '2025-06-16 02:43:48', 'Rejected', 0.00),
(7, 12, NULL, '2025-06-16 02:43:53', 'Approved', 0.00),
(8, 4, NULL, '2025-06-19 09:14:46', 'Pending', 1205.10),
(9, 4, NULL, '2025-06-19 09:19:35', 'Pending', 1205.10),
(10, 4, NULL, '2025-06-19 09:21:06', 'Pending', 1205.10),
(11, 4, NULL, '2025-06-19 09:21:07', 'Pending', 1205.10),
(12, 4, NULL, '2025-06-19 09:21:07', 'Pending', 1205.10),
(13, 4, NULL, '2025-06-19 09:21:07', 'Pending', 1205.10),
(14, 4, NULL, '2025-06-19 09:21:07', 'Pending', 1205.10),
(15, 4, NULL, '2025-06-19 09:21:09', 'Pending', 1205.10),
(16, 4, NULL, '2025-06-19 09:23:47', 'Pending', 1205.10),
(17, 4, NULL, '2025-06-19 09:23:50', 'Pending', 1205.10),
(18, 4, NULL, '2025-06-19 09:26:00', 'Pending', 1205.10),
(19, 4, NULL, '2025-06-19 09:27:03', 'Pending', 1205.10),
(20, 4, NULL, '2025-06-19 09:27:20', 'Pending', 297.00),
(21, 4, NULL, '2025-06-19 09:30:19', 'Verified', 99.00),
(22, 4, NULL, '2025-06-19 09:39:57', 'Pending', 802.80),
(23, 4, NULL, '2025-06-19 09:42:55', 'Verified', 297.00),
(24, 4, NULL, '2025-06-19 10:37:43', 'Pending', 99.00),
(25, 4, NULL, '2025-06-19 10:47:06', 'Verified', 99.00),
(26, 4, NULL, '2025-06-19 11:06:22', 'Picked Up by Rider', 758.70),
(27, 4, NULL, '2025-06-19 11:10:07', 'Ready for Pick-up', 918.00),
(28, 4, NULL, '2025-06-19 12:29:28', 'cancelled', 492.00),
(29, 4, NULL, '2025-06-19 12:29:35', 'cancelled', 149.00),
(30, 4, NULL, '2025-06-19 12:39:47', 'cancelled', 420.00),
(31, 4, NULL, '2025-06-19 13:03:04', 'cancelled', 468.00),
(32, 4, NULL, '2025-06-19 13:07:51', 'cancelled', 49.00),
(33, 4, NULL, '2025-06-19 13:12:07', 'cancelled', 123.00),
(34, 4, NULL, '2025-06-19 13:14:53', 'Ready for Pick-up', 248.00),
(35, 4, NULL, '2025-06-19 14:40:19', 'Pending', 356.00),
(36, 4, NULL, '2025-06-19 14:42:16', 'Pending', 298.00),
(37, 4, NULL, '2025-06-20 03:24:44', 'Pending', 347.00),
(38, 4, NULL, '2025-06-20 03:27:59', 'pending', 356.00),
(39, 4, NULL, '2025-06-20 03:28:47', 'pending', 307.00),
(40, 4, NULL, '2025-06-20 03:30:53', 'pending', 284.80),
(41, 4, NULL, '2025-06-20 03:31:08', 'pending', 205.60),
(42, 4, NULL, '2025-06-20 03:34:37', 'pending', 356.00),
(43, 4, NULL, '2025-06-20 03:39:44', 'pending', 284.80),
(44, 4, NULL, '2025-06-20 03:41:51', 'pending', 356.00),
(45, 4, NULL, '2025-06-20 03:44:51', 'pending', 149.00),
(47, 12, NULL, '2025-06-20 04:11:31', 'pending', 387.20),
(48, 12, NULL, '2025-06-20 04:16:03', 'pending', 248.00),
(49, 12, NULL, '2025-06-20 04:18:49', 'pending', 356.00),
(50, 12, NULL, '2025-06-20 04:21:40', 'pending', 284.80),
(51, 12, NULL, '2025-06-20 04:22:46', 'pending', 385.20),
(53, 12, NULL, '2025-06-20 04:27:47', 'pending', 149.00),
(58, 13, NULL, '2025-06-20 04:51:32', 'pending', 317.60),
(59, 13, NULL, '2025-06-20 04:55:42', 'pending', 2086.00),
(60, 4, NULL, '2025-06-20 05:17:28', 'pending', 248.00),
(61, 4, NULL, '2025-06-20 05:17:45', 'pending', 248.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_cancellation`
--

CREATE TABLE `order_cancellation` (
  `cancellation_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `cancellation_reason` varchar(255) NOT NULL,
  `cancellation_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_cancellation`
--

INSERT INTO `order_cancellation` (`cancellation_id`, `order_id`, `cancellation_reason`, `cancellation_date`) VALUES
(1, 3, 'Customer changed mind', '2025-06-15 16:35:00'),
(2, 6, 'Item out of stock', '2025-06-15 17:20:00'),
(3, 9, 'Address unreachable', '2025-06-15 18:05:00'),
(4, 12, 'Payment issues', '2025-06-15 19:15:00'),
(5, 15, 'Delayed preparation', '2025-06-15 20:30:00'),
(6, 33, 'User cancelled', '2025-06-19 20:13:10'),
(7, 32, 'User cancelled', '2025-06-19 20:13:15'),
(8, 31, 'User cancelled', '2025-06-19 20:13:33'),
(9, 30, 'User cancelled', '2025-06-19 20:19:40'),
(10, 29, 'User cancelled', '2025-06-19 20:20:07'),
(11, 28, 'User cancelled', '2025-06-19 20:23:33');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `orderdetail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`orderdetail_id`, `order_id`, `item_id`, `quantity`, `subtotal`) VALUES
(17, 5, 1, 25, 2475.00),
(18, 5, 2, 5, 745.00),
(19, 5, 7, 4, 236.00),
(20, 19, 1, 6, 0.00),
(21, 19, 2, 5, 0.00),
(22, 20, 1, 3, 0.00),
(23, 21, 1, 1, 0.00),
(24, 22, 1, 4, 0.00),
(25, 22, 2, 3, 0.00),
(26, 22, 8, 1, 0.00),
(27, 23, 1, 3, 0.00),
(28, 24, 1, 1, 0.00),
(29, 25, 1, 1, 0.00),
(30, 26, 1, 4, 0.00),
(31, 26, 2, 3, 0.00),
(32, 27, 1, 4, 0.00),
(33, 27, 2, 2, 0.00),
(34, 27, 3, 1, 0.00),
(35, 27, 4, 2, 0.00),
(36, 27, 5, 1, 0.00),
(37, 27, 8, 1, 0.00),
(40, 29, 2, 1, 0.00),
(41, 30, 1, 1, 0.00),
(42, 30, 2, 1, 0.00),
(43, 30, 8, 1, 0.00),
(45, 31, 1, 1, 0.00),
(48, 32, 8, 1, 0.00),
(50, 34, 1, 1, 0.00),
(51, 34, 2, 1, 0.00),
(52, 35, 1, 1, 0.00),
(53, 35, 2, 1, 0.00),
(54, 35, 7, 1, 0.00),
(55, 35, 8, 1, 0.00),
(56, 36, 2, 2, 0.00),
(57, 37, 1, 2, 0.00),
(58, 37, 2, 1, 0.00),
(59, 38, 1, 1, 0.00),
(60, 38, 2, 1, 0.00),
(61, 38, 7, 1, 0.00),
(62, 38, 8, 1, 0.00),
(63, 39, 1, 1, 0.00),
(64, 39, 2, 1, 0.00),
(65, 39, 7, 1, 0.00),
(66, 40, 1, 1, 0.00),
(67, 40, 2, 1, 0.00),
(68, 40, 7, 1, 0.00),
(69, 40, 8, 1, 0.00),
(70, 41, 2, 1, 0.00),
(71, 41, 7, 1, 0.00),
(72, 41, 8, 1, 0.00),
(73, 42, 1, 1, 0.00),
(74, 42, 2, 1, 0.00),
(75, 42, 7, 1, 0.00),
(76, 42, 8, 1, 0.00),
(77, 43, 1, 1, 0.00),
(78, 43, 2, 1, 0.00),
(79, 43, 7, 1, 0.00),
(80, 43, 8, 1, 0.00),
(81, 44, 1, 1, 0.00),
(82, 44, 2, 1, 0.00),
(83, 44, 7, 1, 0.00),
(84, 44, 8, 1, 0.00),
(85, 45, 2, 1, 0.00),
(90, 47, 1, 1, 0.00),
(91, 47, 2, 1, 0.00),
(92, 47, 7, 4, 0.00),
(93, 48, 1, 1, 0.00),
(94, 48, 2, 1, 0.00),
(95, 49, 1, 1, 0.00),
(96, 49, 2, 1, 0.00),
(97, 49, 7, 1, 0.00),
(98, 49, 8, 1, 0.00),
(99, 50, 1, 1, 0.00),
(100, 50, 2, 1, 0.00),
(101, 50, 7, 1, 0.00),
(102, 50, 8, 1, 0.00),
(103, 51, 1, 1, 0.00),
(104, 51, 2, 1, 0.00),
(105, 51, 4, 1, 0.00),
(106, 51, 6, 2, 0.00),
(107, 51, 7, 1, 0.00),
(108, 51, 8, 1, 0.00),
(112, 53, 2, 1, 0.00),
(113, 58, 1, 1, 99.00),
(114, 58, 2, 2, 298.00),
(115, 59, 2, 14, 2086.00),
(116, 60, 1, 1, 99.00),
(117, 60, 2, 1, 149.00),
(118, 61, 1, 1, 99.00),
(119, 61, 2, 1, 149.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `payment_date` datetime NOT NULL,
  `discount` varchar(20) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `payment_method`, `payment_status`, `payment_date`, `discount`, `amount_paid`) VALUES
(1, 9, 'Cash on Delivery', 'Paid', '2025-06-19 09:19:35', 'NONE', 1205.10),
(2, 10, 'Cash on Delivery', 'Paid', '2025-06-19 09:21:06', 'NONE', 1205.10),
(3, 11, 'Cash on Delivery', 'Paid', '2025-06-19 09:21:07', 'NONE', 1205.10),
(4, 12, 'Cash on Delivery', 'Paid', '2025-06-19 09:21:07', 'NONE', 1205.10),
(5, 13, 'Cash on Delivery', 'Paid', '2025-06-19 09:21:07', 'NONE', 1205.10),
(6, 14, 'Cash on Delivery', 'Paid', '2025-06-19 09:21:07', 'NONE', 1205.10),
(7, 15, 'Cash on Delivery', 'Paid', '2025-06-19 09:21:09', 'NONE', 1205.10),
(8, 16, 'Cash on Delivery', 'Paid', '2025-06-19 09:23:47', 'NONE', 1205.10),
(9, 17, 'Cash on Delivery', 'Paid', '2025-06-19 09:23:50', 'NONE', 1205.10),
(10, 18, 'Cash on Delivery', 'Paid', '2025-06-19 09:26:00', 'NONE', 1205.10),
(11, 19, 'Cash on Delivery', 'Paid', '2025-06-19 09:27:03', 'NONE', 1205.10),
(12, 20, 'Cash on Delivery', 'Paid', '2025-06-19 09:27:20', 'NONE', 297.00),
(13, 21, 'Cash on Delivery', 'Paid', '2025-06-19 09:30:19', 'NONE', 99.00),
(14, 22, 'Cash on Delivery', 'Paid', '2025-06-19 09:39:57', 'NONE', 802.80),
(15, 23, 'Cash on Delivery', 'Paid', '2025-06-19 09:42:55', 'NONE', 297.00),
(16, 24, 'Cash on Delivery', 'Paid', '2025-06-19 10:37:43', 'NONE', 99.00),
(17, 25, 'Cash on Delivery', 'Paid', '2025-06-19 10:47:06', 'NONE', 99.00),
(18, 26, 'Credit Card', 'Paid', '2025-06-19 11:06:22', 'NONE', 758.70),
(19, 27, 'Cash on Delivery', 'Paid', '2025-06-19 11:10:08', 'NONE', 918.00),
(20, 28, 'Cash on Delivery', 'Paid', '2025-06-19 12:29:28', 'NONE', 492.00),
(21, 29, 'Cash on Delivery', 'Paid', '2025-06-19 12:29:35', 'NONE', 149.00),
(22, 30, 'Cash on Delivery', 'Paid', '2025-06-19 12:39:47', 'NONE', 420.00),
(23, 31, 'Cash on Delivery', 'Paid', '2025-06-19 13:03:04', 'NONE', 468.00),
(24, 32, 'Cash on Delivery', 'Paid', '2025-06-19 13:07:51', 'NONE', 49.00),
(25, 33, 'Cash on Delivery', 'Paid', '2025-06-19 13:12:07', 'NONE', 123.00),
(26, 34, 'Cash on Delivery', 'Paid', '2025-06-19 13:14:53', 'NONE', 248.00),
(27, 35, 'Cash on Delivery', 'Paid', '2025-06-19 14:40:19', 'NONE', 356.00),
(28, 36, 'Cash on Delivery', 'Paid', '2025-06-19 14:42:16', 'NONE', 298.00),
(29, 37, 'Cash on Delivery', 'Paid', '2025-06-20 03:24:44', 'NONE', 347.00),
(30, 38, 'Cash on Delivery', 'Paid', '2025-06-20 03:27:59', 'NONE', 356.00),
(31, 39, 'Cash on Delivery', 'Paid', '2025-06-20 03:28:47', 'NONE', 307.00),
(32, 40, 'Cash on Delivery', 'Paid', '2025-06-20 03:30:53', 'SENIOR20', 284.80),
(33, 41, 'Cash on Delivery', 'Paid', '2025-06-20 03:31:08', 'PWD20', 205.60),
(34, 42, 'Cash on Delivery', 'Paid', '2025-06-20 03:34:37', 'NONE', 356.00),
(35, 43, 'Cash on Delivery', 'Paid', '2025-06-20 03:39:44', 'PWD20', 284.80),
(36, 44, 'Cash on Delivery', 'Paid', '2025-06-20 03:41:51', 'NONE', 356.00),
(37, 45, 'Cash on Delivery', 'Paid', '2025-06-20 03:44:51', 'NONE', 149.00),
(39, 47, 'Cash on Delivery', 'Paid', '2025-06-20 04:11:31', 'PWD20', 387.20),
(40, 48, 'Cash on Delivery', 'Paid', '2025-06-20 04:16:03', 'NONE', 248.00),
(41, 49, 'Cash on Delivery', 'Paid', '2025-06-20 04:18:49', 'NONE', 356.00),
(42, 50, 'Cash on Delivery', 'Paid', '2025-06-20 04:21:40', 'SENIOR20', 284.80),
(43, 51, 'Cash on Delivery', 'Paid', '2025-06-20 04:22:46', 'SENIOR20', 385.20),
(45, 53, 'Cash on Delivery', 'Paid', '2025-06-20 04:27:47', 'NONE', 149.00),
(46, 58, 'Cash on Delivery', 'Paid', '2025-06-20 04:51:32', 'SENIOR20', 317.60),
(47, 59, 'Cash on Delivery', 'Paid', '2025-06-20 04:55:42', 'NONE', 2086.00),
(48, 60, 'Cash on Delivery', 'Paid', '2025-06-20 05:17:28', 'NONE', 248.00),
(49, 61, 'Cash on Delivery', 'Paid', '2025-06-20 05:17:45', 'NONE', 248.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Product_ID` int(11) NOT NULL,
  `Product_Name` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Unit` int(11) NOT NULL,
  `Category` varchar(255) NOT NULL,
  `Product_Status` varchar(255) NOT NULL,
  `Expiration_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`Product_ID`, `Product_Name`, `Description`, `Unit`, `Category`, `Product_Status`, `Expiration_Date`) VALUES
(1, 'Ground Beef', 'Fresh ground beef for patties', 50, 'Meat', 'Available', '2025-12-31'),
(2, 'Chicken Breast', 'Boneless chicken breast', 0, 'Meat', 'Available', '2025-12-31'),
(3, 'Burger Buns', 'Soft burger buns', 0, 'Bakery', 'Available', '2025-12-31'),
(4, 'Sliced Cheese', 'Processed cheese slices', 0, 'Dairy', 'Available', '2025-12-31'),
(5, 'Lettuce', 'Fresh lettuce heads', 0, 'Vegetable', 'Available', '2025-12-31'),
(6, 'Tomato', 'Fresh tomatoes', 0, 'Vegetable', 'Available', '2025-12-31'),
(7, 'Potato', 'Potatoes for fries', 0, 'Vegetable', 'Available', '2025-12-31'),
(8, 'Cooking Oil', 'Vegetable oil for frying', 0, 'Pantry', 'Available', '2025-12-31'),
(9, 'Onion', 'Fresh onions', 0, 'Vegetable', 'Available', '2025-12-31'),
(10, 'Pickles', 'Sliced pickles', 0, 'Pantry', 'Available', '2025-12-31'),
(11, 'Mayonnaise', 'Mayonnaise for dressing', 0, 'Condiment', 'Available', '2025-12-31'),
(12, 'Ketchup', 'Tomato ketchup', 0, 'Condiment', 'Available', '2025-12-31'),
(13, 'Mustard', 'Yellow mustard', 0, 'Condiment', 'Available', '2025-12-31'),
(14, 'Soda Syrup', 'Syrup for soft drinks', 0, 'Beverage', 'Available', '2025-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receipt_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `receipt_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`receipt_id`, `payment_id`, `receipt_date`) VALUES
(1, 1, '2025-06-19 09:19:35'),
(2, 2, '2025-06-19 09:21:06'),
(3, 3, '2025-06-19 09:21:07'),
(4, 4, '2025-06-19 09:21:07'),
(5, 5, '2025-06-19 09:21:07'),
(6, 6, '2025-06-19 09:21:07'),
(7, 7, '2025-06-19 09:21:09'),
(8, 8, '2025-06-19 09:23:47'),
(9, 9, '2025-06-19 09:23:50'),
(10, 10, '2025-06-19 09:26:00'),
(11, 11, '2025-06-19 09:27:03'),
(12, 12, '2025-06-19 09:27:20'),
(13, 13, '2025-06-19 09:30:19'),
(14, 14, '2025-06-19 09:39:57'),
(15, 15, '2025-06-19 09:42:55'),
(16, 16, '2025-06-19 10:37:43'),
(17, 17, '2025-06-19 10:47:06'),
(18, 18, '2025-06-19 11:06:22'),
(19, 19, '2025-06-19 11:10:08'),
(20, 20, '2025-06-19 12:29:28'),
(21, 21, '2025-06-19 12:29:35'),
(22, 22, '2025-06-19 12:39:47'),
(23, 23, '2025-06-19 13:03:04'),
(24, 24, '2025-06-19 13:07:51'),
(25, 25, '2025-06-19 13:12:07'),
(26, 26, '2025-06-19 13:14:53'),
(27, 27, '2025-06-19 14:40:19'),
(28, 28, '2025-06-19 14:42:16'),
(29, 29, '2025-06-20 03:24:44'),
(30, 30, '2025-06-20 03:27:59'),
(31, 31, '2025-06-20 03:28:47'),
(32, 32, '2025-06-20 03:30:53'),
(33, 33, '2025-06-20 03:31:08'),
(34, 34, '2025-06-20 03:34:37'),
(35, 35, '2025-06-20 03:39:44'),
(36, 36, '2025-06-20 03:41:51'),
(37, 37, '2025-06-20 03:44:51'),
(39, 39, '2025-06-20 04:11:31'),
(40, 40, '2025-06-20 04:16:03'),
(41, 41, '2025-06-20 04:18:49'),
(42, 42, '2025-06-20 04:21:40'),
(43, 43, '2025-06-20 04:22:46'),
(45, 45, '2025-06-20 04:27:47'),
(46, 46, '2025-06-20 04:51:32'),
(47, 47, '2025-06-20 04:55:42'),
(48, 48, '2025-06-20 05:17:28'),
(49, 49, '2025-06-20 05:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `Sales_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Sale_Date` date NOT NULL,
  `Total_Amount` int(11) NOT NULL,
  `Discount` int(11) NOT NULL,
  `Net_Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`Sales_ID`, `Order_ID`, `Sale_Date`, `Total_Amount`, `Discount`, `Net_Amount`) VALUES
(1, 1, '2025-06-15', 248, 20, 228),
(2, 2, '2025-06-15', 198, 0, 198),
(3, 4, '2025-06-15', 325, 50, 275),
(4, 5, '2025-06-15', 149, 10, 139),
(5, 7, '2025-06-15', 420, 30, 390);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','customer') NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `email`) VALUES
(1, 'employee1', '$2y$10$/3EWfMAcL5hXi3FO42xz/ewyJV54Jt6CVT5saVURzkbUnirbaFMQ6', 'employee', NULL),
(2, 'admin', '$2y$10$VEp/xU2DjgNq6F5KiwTL4etTaTkZpXxbMB406z6LoS14cM.MK5Eqe', 'admin', NULL),
(3, 'cielo', 'cielo', 'admin', NULL),
(4, 'eloy', '$2y$10$GdHPkh0ezFKSKZ.JZ5gT6u9thiEVphOJwyhFUKzttJJ8UlJs3ezyq', 'customer', NULL),
(7, 'eloyy', '4321', 'customer', NULL),
(8, 'cielo123', '$2y$10$fpKUJouDOGVvsXPG44dg1Ozkwqjil6J1fG3IBuzPnCIzmEhr0XuLK', 'customer', NULL),
(23, '', '$2y$10$yoL6rwqjK5zQIhan8gy.XOApvg2BM7O1HwwvTQGzsjwe.m71BdYrm', 'employee', NULL),
(24, 'admin01', 'securepassword123', 'admin', NULL),
(25, 'employee01', 'fastfoodemployee456', 'employee', NULL),
(26, 'employee02', 'deliverystaff789', 'employee', NULL),
(27, 'customer01', 'hungryuser321', 'customer', NULL),
(28, 'customer02', 'quickorder654', 'customer', NULL),
(29, 'wew', '$2y$10$wumCPtBgUERIT.12QUr1ueKXL.OKMl9KNMykNh7khYtlEsoi/AzYy', 'customer', 'wew@gmail.com'),
(30, 'waw', '$2y$10$8rywL/kmUbADMCpcy..VCuyQRAxUWOkFRogy8vokU7Lq/vJZ03x.S', 'customer', 'wew@gmail.com'),
(35, 'greg', '$2y$10$pEiVC4HlIzimyjolik6Pp.5x9kClXJe7kP/ZdZCykDCNId7qDPLda', 'customer', 'greglazarte36@gmail.com'),
(36, 'gregg', '$2y$10$.oS8IeCV3Ww2XzzQtZJee.XEx8Odt5P.5XUh8PlaHTvWkUbJLz5O6', 'customer', 'greg1@gmail.com'),
(37, 'gregpogi', '$2y$10$nrQJiFDx2EINaAkmALOQve61xRQs9wSQ5KZplfrDrk9QiuQ/yCjEq', 'customer', 'gregpogi@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_ID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_customer_user` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`Inventory_ID`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `menu_product`
--
ALTER TABLE `menu_product`
  ADD PRIMARY KEY (`MenuProductID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `fk_order_user` (`customer_id`);

--
-- Indexes for table `order_cancellation`
--
ALTER TABLE `order_cancellation`
  ADD PRIMARY KEY (`cancellation_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`orderdetail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Product_ID`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`Sales_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `Inventory_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `menu_product`
--
ALTER TABLE `menu_product`
  MODIFY `MenuProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `order_cancellation`
--
ALTER TABLE `order_cancellation`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `orderdetail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `Sales_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `fk_customer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu` (`item_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`);

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
