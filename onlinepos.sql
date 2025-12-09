-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 09, 2025 at 01:49 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onlinepos`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_type` varchar(4) NOT NULL,
  `balance` double NOT NULL,
  `setup` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_type`, `balance`, `setup`, `updated_by`, `last_update`, `reg_by`, `reg_date`) VALUES
('CASH', 2056.71, 0, NULL, NULL, 'system', '2017-09-09 00:00:00'),
('POS', 964.04, 1, NULL, NULL, 'system', '2017-09-09 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` text NOT NULL,
  `sname` varchar(32) NOT NULL,
  `fname` varchar(32) NOT NULL,
  `mname` varchar(32) DEFAULT NULL,
  `mobile` varchar(16) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `question_id` int DEFAULT NULL,
  `q_answer` text,
  `role_id` int NOT NULL,
  `acct_attempt` int NOT NULL DEFAULT '0',
  `acct_lock` tinyint NOT NULL DEFAULT '0',
  `acct_block` tinyint NOT NULL DEFAULT '0',
  `acct_otp` tinyint NOT NULL DEFAULT '0',
  `acct_activation` tinyint NOT NULL DEFAULT '0',
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `sname`, `fname`, `mname`, `mobile`, `email`, `question_id`, `q_answer`, `role_id`, `acct_attempt`, `acct_lock`, `acct_block`, `acct_otp`, `acct_activation`, `updated_by`, `last_update`, `reg_by`, `reg_date`, `is_active`) VALUES
(1, 'support', '$2y$10$Ju2MMnVXZjgReeMrYFPtk.PF2LwbHCMhg7o00sbr.KNFZ21E8Ux16', 'Saad', 'Salim', 'Ahmad', '08098115556', 'muhammadsalim2007@gmail.com', 2, '$2y$11$368394ef5b14ee1d54878upYfeARMxnKMJr11e9/N53KkfpeCvAI.', 1, 0, 0, 0, 0, 1, 'system', '2025-12-05 14:00:03', 'support', '2019-09-02 09:00:00', 1),
(4, 'cashier', '$2y$11$3891dcd1b7b325d089e83uLp4aLbFsMqlj/wCeqvOsQ4k7glpBUuu', 'LABARAN', 'ADAM', NULL, NULL, 'adam@gmail.com', NULL, NULL, 3, 0, 0, 0, 0, 0, NULL, NULL, 'support', '2025-12-05 11:03:31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `id` int NOT NULL,
  `username` varchar(32) DEFAULT NULL,
  `ActionType` varchar(50) NOT NULL,
  `Description` text,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text,
  `parent_id` int DEFAULT NULL,
  `updated_by` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `updated_by`, `last_update`, `reg_by`, `reg_date`, `is_active`) VALUES
(1, 'Beverages', '', NULL, 'support', '2025-12-05 11:40:21', 'support', '2025-04-12 14:32:34', 1),
(2, 'drinks', 'soft drinks', NULL, NULL, '2025-12-04 01:08:12', 'support', '2025-12-04 01:08:12', 1),
(3, 'electronics', 'All electronics gadgets', NULL, NULL, '2025-12-05 11:10:42', 'support', '2025-12-05 11:10:42', 1),
(4, 'Energy Drinks', 'jsjsjjs', 2, NULL, '2025-12-08 14:56:24', 'support', '2025-12-08 14:56:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `total_purchases` decimal(10,2) DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `del_status` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `phone`, `email`, `address`, `total_purchases`, `created_at`, `updated_at`, `is_active`, `del_status`) VALUES
(1, 'Alh yahaya', '09012345678', 'habu@test.com', '106 Jefferson St,Weehawken, New Jersey(NJ), 07086', 107501.40, '2025-12-04 11:19:32', '2025-12-08 13:19:18', 1, 0),
(2, 'Alh Abubakar', '09012345678', 'habu@test.com', '106 Jefferson St,Weehawken, New Jersey(NJ), 07086', 1876203.75, '2025-12-04 11:19:39', '2025-12-05 16:34:42', 1, 0),
(12, 'mal Habu', '08145919457', 'malhabu@gmail.com', 'jakara', 0.00, '2025-12-09 11:56:22', NULL, 1, 0),
(13, 'Abdulkarim Abdulmutallib', '098765438', 'ak@gmail.com', 'Maahad Link', 0.00, '2025-12-09 12:49:26', NULL, 1, 0),
(18, 'hassan', '08145919419', 'hssan775@gmail.com', 'Maahad Link', 0.00, '2025-12-09 14:31:17', NULL, 1, 0),
(19, 'hassan', '08145919419', 'hssan775@gmail.com', 'Maahad Link', 0.00, '2025-12-09 14:31:25', NULL, 1, 0),
(20, 'Abdurrahman', '0814594345', 'abdurrahmanssan775@gmail.com', 'Maahad Link', 0.00, '2025-12-09 14:40:44', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `discount_rules`
--

CREATE TABLE `discount_rules` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_purchase_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `held_transactions`
--

CREATE TABLE `held_transactions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `cart_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `updated_by` varchar(32) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `updated_by`, `last_update`, `reg_by`, `reg_date`) VALUES
(1, 'cash', '', '2025-04-12 18:24:20', '', '0000-00-00 00:00:00'),
(2, 'POS', '', '2025-04-12 18:24:20', '', '0000-00-00 00:00:00'),
(3, 'Bank Transfer', '', '2025-04-12 18:24:20', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `price_history`
--

CREATE TABLE `price_history` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` datetime DEFAULT CURRENT_TIMESTAMP,
  `reg_by` varchar(32) DEFAULT NULL,
  `reg_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` varchar(255) NOT NULL,
  `category_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `barcode` varchar(20) DEFAULT NULL,
  `cost_price` double(10,2) NOT NULL,
  `selling_price` double(10,2) NOT NULL,
  `qty_in_stock` int NOT NULL DEFAULT '0',
  `low_stock_alert` int NOT NULL,
  `hasBatches` tinyint(1) NOT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `category_id`, `supplier_id`, `barcode`, `cost_price`, `selling_price`, `qty_in_stock`, `low_stock_alert`, `hasBatches`, `updated_by`, `last_update`, `reg_by`, `reg_date`, `is_active`, `image_url`) VALUES
(1, NULL, 'Ayaba', 'dggdhhshs', 1, 1, '6971552590605', 30.00, 50.00, 86, 10, 0, 'support', '2025-12-09 12:35:45', 'support', '2023-07-30 11:06:52', 1, NULL),
(2, NULL, 'Zoom', 'fsdhfhhhf', 1, 1, '8901057335522', 46.65, 88.00, 20, 10, 0, 'support', '2025-12-09 11:37:44', 'support', '2023-07-30 11:06:53', 1, NULL),
(3, NULL, 'Zoom1', 'fjsjjfjjff', 1, 1, '1234567890', 15.00, 20.00, 19, 10, 0, 'support', '2025-12-08 14:54:13', 'support', '2023-07-30 11:06:54', 1, NULL),
(4, NULL, 'Zuma2', 'sffsjfjd', 1, 1, '80', 23.58, 23.58, 35, 10, 0, 'support', '2025-12-08 14:54:13', 'support', '2023-07-30 11:06:55', 1, NULL),
(5, NULL, 'Zuma5uu1', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 37, 10, 0, 'support', '2025-12-08 14:54:13', 'support', '2023-07-30 11:06:56', 1, NULL),
(6, NULL, 'Zuma5uu2', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 35, 10, 0, 'support', '2025-11-26 08:28:11', 'support', '2023-07-30 11:06:57', 1, NULL),
(7, NULL, 'Zuma5uu3', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 42, 10, 0, 'support', '2025-04-12 15:50:45', 'support', '2023-07-30 11:06:58', 0, NULL),
(8, NULL, 'Zuma5uu4', 'dggdhhshs', 1, 1, '80', 23.58, 44.00, 30, 50, 0, 'support', '2025-12-05 10:42:02', 'support', '2023-07-30 11:06:59', 1, NULL),
(9, NULL, 'Zuma5uu5', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 37, 10, 0, 'support', '2025-12-04 01:17:02', 'support', '0000-00-00 00:00:00', 1, NULL),
(10, NULL, 'Zuma5uu6', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 30, 10, 0, 'support', '2025-12-04 12:03:10', 'support', '0000-00-00 00:00:00', 1, NULL),
(11, NULL, 'Zuma5uu7', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 43, 10, 0, 'support', '2025-11-26 08:27:17', 'support', '0000-00-00 00:00:00', 1, NULL),
(12, NULL, 'Zuma5uu8', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-12-04 01:24:32', 'support', '0000-00-00 00:00:00', 1, NULL),
(13, NULL, 'Zuma5uu9', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-12-04 01:24:32', 'support', '0000-00-00 00:00:00', 1, NULL),
(14, NULL, 'Zuma5uu10', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, NULL, '2025-12-08 14:54:13', 'support', '0000-00-00 00:00:00', 1, NULL),
(15, NULL, 'Zuma5uu11', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(16, NULL, 'Zuma5uu12', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(17, NULL, 'Zuma5uu13', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(18, NULL, 'Zuma5uu14', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(19, NULL, 'Zuma5uu15', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(20, NULL, 'Zuma5uu16', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-07-14 16:05:09', 'support', '0000-00-00 00:00:00', 1, NULL),
(21, NULL, 'Zuma5uu17', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(22, NULL, 'Zuma5uu18', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(23, NULL, 'Zuma5uu19', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(24, NULL, 'Zuma5uu20', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 5, 10, 0, NULL, '2025-12-04 00:16:51', 'support', '0000-00-00 00:00:00', 1, NULL),
(25, NULL, 'Zuma5uu21', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 7, 10, 0, NULL, '2025-12-04 00:16:54', 'support', '0000-00-00 00:00:00', 1, NULL),
(26, NULL, 'Zuma5uu22', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(27, NULL, 'Zuma5uu23', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(28, NULL, 'Zuma5uu24', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(29, NULL, 'Zuma5uu25', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(30, NULL, 'Zuma5uu26', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-07-14 16:04:55', 'support', '0000-00-00 00:00:00', 1, NULL),
(31, NULL, 'Zuma5uu27', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(32, NULL, 'Zuma5uu28', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(33, NULL, 'Zuma5uu29', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(34, NULL, 'Zuma5uu30', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(35, NULL, 'Zuma5uu31', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(36, NULL, 'Zuma5uu32', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(37, NULL, 'Zuma5uu33', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-11-26 08:27:17', 'support', '0000-00-00 00:00:00', 1, NULL),
(38, NULL, 'Zuma5uu34', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(39, NULL, 'Zuma5uu35', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(40, NULL, 'Zuma5uu36', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(41, NULL, 'Zuma5uu37', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(42, NULL, 'Zuma5uu38', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(43, NULL, 'Zuma5uu39', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(44, NULL, 'Zuma5uu40', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(45, NULL, 'Zuma5uu41', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(46, NULL, 'Zuma5uu42', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(47, NULL, 'Zuma5uu43', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(48, NULL, 'Zuma5uu44', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(49, NULL, 'Zuma5uu45', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-12-01 13:34:33', 'support', '0000-00-00 00:00:00', 1, NULL),
(50, NULL, 'Zuma5uu46', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 43, 10, 0, 'support', '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(51, NULL, 'Zuma5uu47', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(52, NULL, 'Zuma5uu48', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(53, NULL, 'Zuma5uu49', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(54, NULL, 'Zuma5uu50', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(55, NULL, 'Zuma5uu51', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(56, NULL, 'Zuma5uu52', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(57, NULL, 'Zuma5uu53', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(58, NULL, 'Zuma5uu54', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(59, NULL, 'Zuma5uu55', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(60, NULL, 'Zuma5uu56', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(61, NULL, 'Zuma5uu57', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(62, NULL, 'Zuma5uu58', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(63, NULL, 'Zuma5uu59', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(64, NULL, 'Zuma5uu60', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(65, NULL, 'Zuma5uu61', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(66, NULL, 'Zuma5uu62', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(67, NULL, 'Zuma5uu63', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(68, NULL, 'Zuma5uu64', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(69, NULL, 'Zuma5uu65', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(70, NULL, 'Zuma5uu66', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(71, NULL, 'Zuma5uu67', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(72, NULL, 'Zuma5uu68', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(73, NULL, 'Zuma5uu69', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(74, NULL, 'Zuma5uu70', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(75, NULL, 'Zuma5uu71', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(76, NULL, 'Zuma5uu72', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(77, NULL, 'Zuma5uu73', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(78, NULL, 'Zuma5uu74', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(79, NULL, 'Zuma5uu75', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(80, NULL, 'Zuma5uu76', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(81, NULL, 'Zuma5uu77', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(82, NULL, 'Zuma5uu78', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(83, NULL, 'Zuma5uu79', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(84, NULL, 'Zuma5uu80', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(85, NULL, 'Zuma5uu81', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(86, NULL, 'Zuma5uu82', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(87, NULL, 'Zuma5uu83', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(88, NULL, 'Zuma5uu84', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(89, NULL, 'Zuma5uu85', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(90, NULL, 'Zuma5uu86', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(91, NULL, 'Zuma5uu87', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(92, NULL, 'Zuma5uu88', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(93, NULL, 'Zuma5uu89', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(94, NULL, 'Zuma5uu90', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(95, NULL, 'Zuma5uu91', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(96, NULL, 'Zuma5uu92', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(97, NULL, 'Zuma5uu93', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(98, NULL, 'Zuma5uu94', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(99, NULL, 'Zuma5uu95', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(100, NULL, 'Zuma5uu96', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(101, NULL, 'Zuma5uu97', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(102, NULL, 'Zuma5uu98', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(103, NULL, 'Zuma5uu99', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(104, NULL, 'ZUMA5UU100', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, 'support', '2025-12-08 14:54:13', 'support', '0000-00-00 00:00:00', 1, NULL),
(105, NULL, 'Zuma5uu101', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 44, 10, 0, NULL, '2025-12-07 15:15:17', 'support', '0000-00-00 00:00:00', 1, NULL),
(106, NULL, 'Zuma5uu102', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(107, NULL, 'Zuma5uu103', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(108, NULL, 'Zuma5uu104', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(109, NULL, 'Zuma5uu105', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(110, NULL, 'Zuma5uu106', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(111, NULL, 'Zuma5uu107', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(112, NULL, 'Zuma5uu108', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(113, NULL, 'Zuma5uu109', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(114, NULL, 'Zuma5uu110', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(115, NULL, 'Zuma5uu111', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(116, NULL, 'Zuma5uu112', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(117, NULL, 'Zuma5uu113', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(118, NULL, 'Zuma5uu114', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(119, NULL, 'Zuma5uu115', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(120, NULL, 'Zuma5uu116', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(121, NULL, 'Zuma5uu117', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(122, NULL, 'Zuma5uu118', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(123, NULL, 'Zuma5uu119', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(124, NULL, 'Zuma5uu120', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(125, NULL, 'Zuma5uu121', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(126, NULL, 'Zuma5uu122', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(127, NULL, 'Zuma5uu123', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(128, NULL, 'Zuma5uu124', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(129, NULL, 'Zuma5uu125', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(130, NULL, 'Zuma5uu126', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(131, NULL, 'Zuma5uu127', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(132, NULL, 'Zuma5uu128', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(133, NULL, 'Zuma5uu129', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(134, NULL, 'Zuma5uu130', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(135, NULL, 'Zuma5uu131', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(136, NULL, 'Zuma5uu132', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(137, NULL, 'Zuma5uu133', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(138, NULL, 'Zuma5uu134', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(139, NULL, 'Zuma5uu135', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(140, NULL, 'Zuma5uu136', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(141, NULL, 'Zuma5uu137', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(142, NULL, 'Zuma5uu138', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(143, NULL, 'Zuma5uu139', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(144, NULL, 'Zuma5uu140', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(145, NULL, 'Zuma5uu141', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(146, NULL, 'Zuma5uu142', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(147, NULL, 'Zuma5uu143', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(148, NULL, 'Zuma5uu144', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(149, NULL, 'Zuma5uu145', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(150, NULL, 'Zuma5uu146', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(151, NULL, 'Zuma5uu147', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(152, NULL, 'Zuma5uu148', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(153, NULL, 'Zuma5uu149', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(154, NULL, 'Zuma5uu150', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(155, NULL, 'Zuma5uu151', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(156, NULL, 'Zuma5uu152', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(157, NULL, 'Zuma5uu153', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(158, NULL, 'Zuma5uu154', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(159, NULL, 'Zuma5uu155', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(160, NULL, 'Zuma5uu156', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(161, NULL, 'Zuma5uu157', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(162, NULL, 'Zuma5uu158', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(163, NULL, 'Zuma5uu159', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(164, NULL, 'Zuma5uu160', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(165, NULL, 'Zuma5uu161', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(166, NULL, 'Zuma5uu162', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(167, NULL, 'Zuma5uu163', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(168, NULL, 'Zuma5uu164', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(169, NULL, 'Zuma5uu165', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(170, NULL, 'Zuma5uu166', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(171, NULL, 'Zuma5uu167', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(172, NULL, 'Zuma5uu168', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(173, NULL, 'Zuma5uu169', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(174, NULL, 'Zuma5uu170', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(175, NULL, 'Zuma5uu171', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(176, NULL, 'Zuma5uu172', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(177, NULL, 'Zuma5uu173', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(178, NULL, 'Zuma5uu174', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(179, NULL, 'Zuma5uu175', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(180, NULL, 'Zuma5uu176', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(181, NULL, 'Zuma5uu177', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(182, NULL, 'Zuma5uu178', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(183, NULL, 'Zuma5uu179', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(184, NULL, 'Zuma5uu180', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(185, NULL, 'Zuma5uu181', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(186, NULL, 'Zuma5uu182', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(187, NULL, 'Zuma5uu183', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(188, NULL, 'Zuma5uu184', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(189, NULL, 'Zuma5uu185', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(190, NULL, 'Zuma5uu186', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(191, NULL, 'Zuma5uu187', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(192, NULL, 'Zuma5uu188', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(193, NULL, 'Zuma5uu189', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(194, NULL, 'Zuma5uu190', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(195, NULL, 'Zuma5uu191', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(196, NULL, 'Zuma5uu192', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(197, NULL, 'Zuma5uu193', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(198, NULL, 'Zuma5uu194', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(199, NULL, 'Zuma5uu195', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(200, NULL, 'Zuma5uu196', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(201, NULL, 'Zuma5uu197', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(202, NULL, 'Zuma5uu198', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(203, NULL, 'Zuma5uu199', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(204, NULL, 'Zuma5uu200', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(205, NULL, 'Zuma5uu201', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(206, NULL, 'Zuma5uu202', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(207, NULL, 'Zuma5uu203', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(208, NULL, 'Zuma5uu204', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(209, NULL, 'Zuma5uu205', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(210, NULL, 'Zuma5uu206', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(211, NULL, 'Zuma5uu207', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(212, NULL, 'Zuma5uu208', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(213, NULL, 'Zuma5uu209', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(214, NULL, 'Zuma5uu210', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(215, NULL, 'Zuma5uu211', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(216, NULL, 'Zuma5uu212', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(217, NULL, 'Zuma5uu213', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(218, NULL, 'Zuma5uu214', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(219, NULL, 'Zuma5uu215', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(220, NULL, 'Zuma5uu216', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(221, NULL, 'Zuma5uu217', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(222, NULL, 'Zuma5uu218', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(223, NULL, 'Zuma5uu219', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(224, NULL, 'Zuma5uu220', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(225, NULL, 'Zuma5uu221', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(226, NULL, 'Zuma5uu222', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(227, NULL, 'Zuma5uu223', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(228, NULL, 'Zuma5uu224', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(229, NULL, 'Zuma5uu225', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(230, NULL, 'Zuma5uu226', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(231, NULL, 'Zuma5uu227', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(232, NULL, 'Zuma5uu228', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(233, NULL, 'Zuma5uu229', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(234, NULL, 'Zuma5uu230', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(235, NULL, 'Zuma5uu231', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(236, NULL, 'Zuma5uu232', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(237, NULL, 'Zuma5uu233', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(238, NULL, 'Zuma5uu234', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(239, NULL, 'Zuma5uu235', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(240, NULL, 'Zuma5uu236', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(241, NULL, 'Zuma5uu237', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(242, NULL, 'Zuma5uu238', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(243, NULL, 'Zuma5uu239', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(244, NULL, 'Zuma5uu240', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(245, NULL, 'Zuma5uu241', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(246, NULL, 'Zuma5uu242', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(247, NULL, 'Zuma5uu243', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(248, NULL, 'Zuma5uu244', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(249, NULL, 'Zuma5uu245', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(250, NULL, 'Zuma5uu246', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(251, NULL, 'Zuma5uu247', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(252, NULL, 'Zuma5uu248', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(253, NULL, 'Zuma5uu249', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(254, NULL, 'Zuma5uu250', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(255, NULL, 'Zuma5uu251', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(256, NULL, 'Zuma5uu252', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(257, NULL, 'Zuma5uu253', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(258, NULL, 'Zuma5uu254', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(259, NULL, 'Zuma5uu255', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(260, NULL, 'Zuma5uu256', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(261, NULL, 'Zuma5uu257', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(262, NULL, 'Zuma5uu258', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(263, NULL, 'Zuma5uu259', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(264, NULL, 'Zuma5uu260', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(265, NULL, 'Zuma5uu261', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(266, NULL, 'Zuma5uu262', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(267, NULL, 'Zuma5uu263', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(268, NULL, 'Zuma5uu264', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(269, NULL, 'Zuma5uu265', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(270, NULL, 'Zuma5uu266', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(271, NULL, 'Zuma5uu267', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(272, NULL, 'Zuma5uu268', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(273, NULL, 'Zuma5uu269', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(274, NULL, 'Zuma5uu270', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(275, NULL, 'Zuma5uu271', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(276, NULL, 'Zuma5uu272', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(277, NULL, 'Zuma5uu273', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(278, NULL, 'Zuma5uu274', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(279, NULL, 'Zuma5uu275', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(280, NULL, 'Zuma5uu276', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(281, NULL, 'Zuma5uu277', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(282, NULL, 'Zuma5uu278', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(283, NULL, 'Zuma5uu279', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(284, NULL, 'Zuma5uu280', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(285, NULL, 'Zuma5uu281', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(286, NULL, 'Zuma5uu282', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(287, NULL, 'Zuma5uu283', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(288, NULL, 'Zuma5uu284', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(289, NULL, 'Zuma5uu285', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(290, NULL, 'Zuma5uu286', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(291, NULL, 'Zuma5uu287', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(292, NULL, 'Zuma5uu288', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(293, NULL, 'Zuma5uu289', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(294, NULL, 'Zuma5uu290', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(295, NULL, 'Zuma5uu291', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(296, NULL, 'Zuma5uu292', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(297, NULL, 'Zuma5uu293', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(298, NULL, 'Zuma5uu294', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(299, NULL, 'Zuma5uu295', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(300, NULL, 'Zuma5uu296', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(301, NULL, 'Zuma5uu297', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(302, NULL, 'Zuma5uu298', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(303, NULL, 'Zuma5uu299', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(304, NULL, 'Zuma5uu300', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(305, NULL, 'Zuma5uu301', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(306, NULL, 'Zuma5uu302', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(307, NULL, 'Zuma5uu303', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(308, NULL, 'Zuma5uu304', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(309, NULL, 'Zuma5uu305', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(310, NULL, 'Zuma5uu306', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(311, NULL, 'Zuma5uu307', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(312, NULL, 'Zuma5uu308', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(313, NULL, 'Zuma5uu309', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(314, NULL, 'Zuma5uu310', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(315, NULL, 'Zuma5uu311', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(316, NULL, 'Zuma5uu312', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(317, NULL, 'Zuma5uu313', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(318, NULL, 'Zuma5uu314', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(319, NULL, 'Zuma5uu315', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(320, NULL, 'Zuma5uu316', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(321, NULL, 'Zuma5uu317', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(322, NULL, 'Zuma5uu318', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(323, NULL, 'Zuma5uu319', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(324, NULL, 'Zuma5uu320', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(325, NULL, 'Zuma5uu321', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(326, NULL, 'Zuma5uu322', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(327, NULL, 'Zuma5uu323', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(328, NULL, 'Zuma5uu324', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(329, NULL, 'Zuma5uu325', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(330, NULL, 'Zuma5uu326', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(331, NULL, 'Zuma5uu327', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(332, NULL, 'Zuma5uu328', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(333, NULL, 'Zuma5uu329', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(334, NULL, 'Zuma5uu330', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(335, NULL, 'Zuma5uu331', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(336, NULL, 'Zuma5uu332', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(337, NULL, 'Zuma5uu333', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(338, NULL, 'Zuma5uu334', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(339, NULL, 'Zuma5uu335', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL);
INSERT INTO `products` (`id`, `sku`, `name`, `description`, `category_id`, `supplier_id`, `barcode`, `cost_price`, `selling_price`, `qty_in_stock`, `low_stock_alert`, `hasBatches`, `updated_by`, `last_update`, `reg_by`, `reg_date`, `is_active`, `image_url`) VALUES
(340, NULL, 'Zuma5uu336', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(341, NULL, 'Zuma5uu337', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(342, NULL, 'Zuma5uu338', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(343, NULL, 'Zuma5uu339', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(344, NULL, 'Zuma5uu340', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(345, NULL, 'Zuma5uu341', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(346, NULL, 'Zuma5uu342', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(347, NULL, 'Zuma5uu343', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(348, NULL, 'Zuma5uu344', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(349, NULL, 'Zuma5uu345', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(350, NULL, 'Zuma5uu346', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(351, NULL, 'Zuma5uu347', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(352, NULL, 'Zuma5uu348', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(353, NULL, 'Zuma5uu349', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(354, NULL, 'Zuma5uu350', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(355, NULL, 'Zuma5uu351', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(356, NULL, 'Zuma5uu352', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(357, NULL, 'Zuma5uu353', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(358, NULL, 'Zuma5uu354', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(359, NULL, 'Zuma5uu355', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(360, NULL, 'Zuma5uu356', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 43, 10, 0, 'support', '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(361, NULL, 'Zuma5uu357', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(362, NULL, 'Zuma5uu358', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(363, NULL, 'Zuma5uu359', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(364, NULL, 'Zuma5uu360', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(365, NULL, 'Zuma5uu361', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(366, NULL, 'Zuma5uu362', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(367, NULL, 'Zuma5uu363', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(368, NULL, 'Zuma5uu364', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(369, NULL, 'Zuma5uu365', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(370, NULL, 'Zuma5uu366', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(371, NULL, 'Zuma5uu367', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(372, NULL, 'Zuma5uu368', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(373, NULL, 'Zuma5uu369', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(374, NULL, 'Zuma5uu370', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(375, NULL, 'Zuma5uu371', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(376, NULL, 'Zuma5uu372', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(377, NULL, 'Zuma5uu373', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(378, NULL, 'Zuma5uu374', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(379, NULL, 'Zuma5uu375', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(380, NULL, 'Zuma5uu376', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(381, NULL, 'Zuma5uu377', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(382, NULL, 'Zuma5uu378', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(383, NULL, 'Zuma5uu379', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(384, NULL, 'Zuma5uu380', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(385, NULL, 'Zuma5uu381', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(386, NULL, 'Zuma5uu382', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(387, NULL, 'Zuma5uu383', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(388, NULL, 'Zuma5uu384', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(389, NULL, 'Zuma5uu385', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(390, NULL, 'Zuma5uu386', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(391, NULL, 'Zuma5uu387', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(392, NULL, 'Zuma5uu388', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(393, NULL, 'Zuma5uu389', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(394, NULL, 'Zuma5uu390', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(395, NULL, 'Zuma5uu391', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(396, NULL, 'Zuma5uu392', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(397, NULL, 'Zuma5uu393', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(398, NULL, 'Zuma5uu394', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(399, NULL, 'Zuma5uu395', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(400, NULL, 'Zuma5uu396', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(401, NULL, 'Zuma5uu397', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(402, NULL, 'Zuma5uu398', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(403, NULL, 'Zuma5uu399', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(404, NULL, 'Zuma5uu400', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(405, NULL, 'Zuma5uu401', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(406, NULL, 'Zuma5uu402', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(407, NULL, 'Zuma5uu403', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(408, NULL, 'Zuma5uu404', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(409, NULL, 'Zuma5uu405', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(410, NULL, 'Zuma5uu406', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(411, NULL, 'Zuma5uu407', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(412, NULL, 'Zuma5uu408', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(413, NULL, 'Zuma5uu409', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(414, NULL, 'Zuma5uu410', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(415, NULL, 'Zuma5uu411', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(416, NULL, 'Zuma5uu412', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(417, NULL, 'Zuma5uu413', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(418, NULL, 'Zuma5uu414', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(419, NULL, 'Zuma5uu415', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(420, NULL, 'Zuma5uu416', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(421, NULL, 'Zuma5uu417', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(422, NULL, 'Zuma5uu418', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(423, NULL, 'Zuma5uu419', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(424, NULL, 'Zuma5uu420', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(425, NULL, 'Zuma5uu421', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(426, NULL, 'Zuma5uu422', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(427, NULL, 'Zuma5uu423', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(428, NULL, 'Zuma5uu424', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(429, NULL, 'Zuma5uu425', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(430, NULL, 'Zuma5uu426', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(431, NULL, 'Zuma5uu427', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(432, NULL, 'Zuma5uu428', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(433, NULL, 'Zuma5uu429', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(434, NULL, 'Zuma5uu430', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(435, NULL, 'Zuma5uu431', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(436, NULL, 'Zuma5uu432', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(437, NULL, 'Zuma5uu433', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(438, NULL, 'Zuma5uu434', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(439, NULL, 'Zuma5uu435', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(440, NULL, 'Zuma5uu436', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(441, NULL, 'Zuma5uu437', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(442, NULL, 'Zuma5uu438', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(443, NULL, 'Zuma5uu439', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(444, NULL, 'Zuma5uu440', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(445, NULL, 'Zuma5uu441', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(446, NULL, 'Zuma5uu442', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(447, NULL, 'Zuma5uu443', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(448, NULL, 'Zuma5uu444', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(449, NULL, 'Zuma5uu445', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(450, NULL, 'Zuma5uu446', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(451, NULL, 'Zuma5uu447', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(452, NULL, 'Zuma5uu448', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(453, NULL, 'Zuma5uu449', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(454, NULL, 'Zuma5uu450', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(455, NULL, 'Zuma5uu451', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(456, NULL, 'Zuma5uu452', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(457, NULL, 'Zuma5uu453', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(458, NULL, 'Zuma5uu454', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(459, NULL, 'Zuma5uu455', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(460, NULL, 'Zuma5uu456', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(461, NULL, 'Zuma5uu457', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(462, NULL, 'Zuma5uu458', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(463, NULL, 'Zuma5uu459', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(464, NULL, 'Zuma5uu460', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(465, NULL, 'Zuma5uu461', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(466, NULL, 'Zuma5uu462', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(467, NULL, 'Zuma5uu463', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(468, NULL, 'Zuma5uu464', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(469, NULL, 'Zuma5uu465', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(470, NULL, 'Zuma5uu466', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(471, NULL, 'Zuma5uu467', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(472, NULL, 'Zuma5uu468', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(473, NULL, 'Zuma5uu469', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(474, NULL, 'Zuma5uu470', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(475, NULL, 'Zuma5uu471', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(476, NULL, 'Zuma5uu472', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(477, NULL, 'Zuma5uu473', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(478, NULL, 'Zuma5uu474', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(479, NULL, 'Zuma5uu475', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(480, NULL, 'Zuma5uu476', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(481, NULL, 'Zuma5uu477', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(482, NULL, 'Zuma5uu478', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(483, NULL, 'Zuma5uu479', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(484, NULL, 'Zuma5uu480', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(485, NULL, 'Zuma5uu481', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(486, NULL, 'Zuma5uu482', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(487, NULL, 'Zuma5uu483', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(488, NULL, 'Zuma5uu484', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(489, NULL, 'Zuma5uu485', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(490, NULL, 'Zuma5uu486', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(491, NULL, 'Zuma5uu487', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(492, NULL, 'Zuma5uu488', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(493, NULL, 'Zuma5uu489', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(494, NULL, 'Zuma5uu490', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(495, NULL, 'Zuma5uu491', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(496, NULL, 'Zuma5uu492', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(497, NULL, 'Zuma5uu493', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(498, NULL, 'Zuma5uu494', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(499, NULL, 'Zuma5uu495', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(500, NULL, 'Zuma5uu496', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(501, NULL, 'Zuma5uu497', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(502, NULL, 'Zuma5uu498', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(503, NULL, 'Zuma5uu499', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(504, NULL, 'Zuma5uu500', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(505, NULL, 'Zuma5uu501', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(506, NULL, 'Zuma5uu502', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(507, NULL, 'Zuma5uu503', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(508, NULL, 'Zuma5uu504', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(509, NULL, 'Zuma5uu505', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(510, NULL, 'Zuma5uu506', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(511, NULL, 'Zuma5uu507', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(512, NULL, 'Zuma5uu508', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(513, NULL, 'Zuma5uu509', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(514, NULL, 'Zuma5uu510', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(515, NULL, 'Zuma5uu511', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(516, NULL, 'Zuma5uu512', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(517, NULL, 'Zuma5uu513', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(518, NULL, 'Zuma5uu514', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(519, NULL, 'Zuma5uu515', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(520, NULL, 'Zuma5uu516', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(521, NULL, 'Zuma5uu517', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(522, NULL, 'Zuma5uu518', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(523, NULL, 'Zuma5uu519', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(524, NULL, 'Zuma5uu520', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(525, NULL, 'Zuma5uu521', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(526, NULL, 'Zuma5uu522', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(527, NULL, 'Zuma5uu523', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(528, NULL, 'Zuma5uu524', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(529, NULL, 'Zuma5uu525', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(530, NULL, 'Zuma5uu526', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(531, NULL, 'Zuma5uu527', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(532, NULL, 'Zuma5uu528', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(533, NULL, 'Zuma5uu529', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(534, NULL, 'Zuma5uu530', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(535, NULL, 'Zuma5uu531', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(536, NULL, 'Zuma5uu532', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(537, NULL, 'Zuma5uu533', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(538, NULL, 'Zuma5uu534', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(539, NULL, 'Zuma5uu535', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(540, NULL, 'Zuma5uu536', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(541, NULL, 'Zuma5uu537', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(542, NULL, 'Zuma5uu538', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(543, NULL, 'Zuma5uu539', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(544, NULL, 'Zuma5uu540', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(545, NULL, 'Zuma5uu541', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(546, NULL, 'Zuma5uu542', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(547, NULL, 'Zuma5uu543', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(548, NULL, 'Zuma5uu544', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(549, NULL, 'Zuma5uu545', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(550, NULL, 'Zuma5uu546', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(551, NULL, 'Zuma5uu547', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(552, NULL, 'Zuma5uu548', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(553, NULL, 'Zuma5uu549', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(554, NULL, 'Zuma5uu550', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(555, NULL, 'Zuma5uu551', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(556, NULL, 'Zuma5uu552', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(557, NULL, 'Zuma5uu553', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(558, NULL, 'Zuma5uu554', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(559, NULL, 'Zuma5uu555', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(560, NULL, 'Zuma5uu556', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(561, NULL, 'Zuma5uu557', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(562, NULL, 'Zuma5uu558', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(563, NULL, 'Zuma5uu559', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(564, NULL, 'Zuma5uu560', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(565, NULL, 'Zuma5uu561', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(566, NULL, 'Zuma5uu562', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(567, NULL, 'Zuma5uu563', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(568, NULL, 'Zuma5uu564', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(569, NULL, 'Zuma5uu565', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(570, NULL, 'Zuma5uu566', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(571, NULL, 'Zuma5uu567', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(572, NULL, 'Zuma5uu568', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(573, NULL, 'Zuma5uu569', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(574, NULL, 'Zuma5uu570', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(575, NULL, 'Zuma5uu571', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(576, NULL, 'Zuma5uu572', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(577, NULL, 'Zuma5uu573', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(578, NULL, 'Zuma5uu574', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(579, NULL, 'Zuma5uu575', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(580, NULL, 'Zuma5uu576', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(581, NULL, 'Zuma5uu577', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(582, NULL, 'Zuma5uu578', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(583, NULL, 'Zuma5uu579', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(584, NULL, 'Zuma5uu580', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(585, NULL, 'Zuma5uu581', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(586, NULL, 'Zuma5uu582', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(587, NULL, 'Zuma5uu583', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(588, NULL, 'Zuma5uu584', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(589, NULL, 'Zuma5uu585', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(590, NULL, 'Zuma5uu586', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(591, NULL, 'Zuma5uu587', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(592, NULL, 'Zuma5uu588', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(593, NULL, 'Zuma5uu589', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(594, NULL, 'Zuma5uu590', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(595, NULL, 'Zuma5uu591', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(596, NULL, 'Zuma5uu592', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(597, NULL, 'Zuma5uu593', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(598, NULL, 'Zuma5uu594', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(599, NULL, 'Zuma5uu595', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(600, NULL, 'Zuma5uu596', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(601, NULL, 'Zuma5uu597', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(602, NULL, 'Zuma5uu598', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(603, NULL, 'Zuma5uu599', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(604, NULL, 'Zuma5uu600', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(605, NULL, 'Zuma5uu601', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(606, NULL, 'Zuma5uu602', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(607, NULL, 'Zuma5uu603', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(608, NULL, 'Zuma5uu604', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(609, NULL, 'Zuma5uu605', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(610, NULL, 'Zuma5uu606', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(611, NULL, 'Zuma5uu607', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(612, NULL, 'Zuma5uu608', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(613, NULL, 'Zuma5uu609', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(614, NULL, 'Zuma5uu610', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(615, NULL, 'Zuma5uu611', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(616, NULL, 'Zuma5uu612', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(617, NULL, 'Zuma5uu613', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(618, NULL, 'Zuma5uu614', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(619, NULL, 'Zuma5uu615', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(620, NULL, 'Zuma5uu616', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(621, NULL, 'Zuma5uu617', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(622, NULL, 'Zuma5uu618', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(623, NULL, 'Zuma5uu619', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(624, NULL, 'Zuma5uu620', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(625, NULL, 'Zuma5uu621', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(626, NULL, 'Zuma5uu622', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(627, NULL, 'Zuma5uu623', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(628, NULL, 'Zuma5uu624', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(629, NULL, 'Zuma5uu625', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(630, NULL, 'Zuma5uu626', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(631, NULL, 'Zuma5uu627', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(632, NULL, 'Zuma5uu628', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(633, NULL, 'Zuma5uu629', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(634, NULL, 'Zuma5uu630', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(635, NULL, 'Zuma5uu631', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(636, NULL, 'Zuma5uu632', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(637, NULL, 'Zuma5uu633', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(638, NULL, 'Zuma5uu634', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(639, NULL, 'Zuma5uu635', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(640, NULL, 'Zuma5uu636', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(641, NULL, 'Zuma5uu637', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(642, NULL, 'Zuma5uu638', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(643, NULL, 'Zuma5uu639', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(644, NULL, 'Zuma5uu640', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(645, NULL, 'Zuma5uu641', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(646, NULL, 'Zuma5uu642', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(647, NULL, 'Zuma5uu643', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(648, NULL, 'Zuma5uu644', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(649, NULL, 'Zuma5uu645', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(650, NULL, 'Zuma5uu646', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(651, NULL, 'Zuma5uu647', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(652, NULL, 'Zuma5uu648', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(653, NULL, 'Zuma5uu649', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(654, NULL, 'Zuma5uu650', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(655, NULL, 'Zuma5uu651', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(656, NULL, 'Zuma5uu652', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(657, NULL, 'Zuma5uu653', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(658, NULL, 'Zuma5uu654', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(659, NULL, 'Zuma5uu655', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(660, NULL, 'Zuma5uu656', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(661, NULL, 'Zuma5uu657', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(662, NULL, 'Zuma5uu658', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(663, NULL, 'Zuma5uu659', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(664, NULL, 'Zuma5uu660', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(665, NULL, 'Zuma5uu661', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(666, NULL, 'Zuma5uu662', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(667, NULL, 'Zuma5uu663', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(668, NULL, 'Zuma5uu664', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(669, NULL, 'Zuma5uu665', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(670, NULL, 'Zuma5uu666', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(671, NULL, 'Zuma5uu667', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(672, NULL, 'Zuma5uu668', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(673, NULL, 'Zuma5uu669', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(674, NULL, 'Zuma5uu670', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(675, NULL, 'Zuma5uu671', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(676, NULL, 'Zuma5uu672', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(677, NULL, 'Zuma5uu673', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL);
INSERT INTO `products` (`id`, `sku`, `name`, `description`, `category_id`, `supplier_id`, `barcode`, `cost_price`, `selling_price`, `qty_in_stock`, `low_stock_alert`, `hasBatches`, `updated_by`, `last_update`, `reg_by`, `reg_date`, `is_active`, `image_url`) VALUES
(678, NULL, 'Zuma5uu674', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(679, NULL, 'Zuma5uu675', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(680, NULL, 'Zuma5uu676', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(681, NULL, 'Zuma5uu677', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(682, NULL, 'Zuma5uu678', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(683, NULL, 'Zuma5uu679', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(684, NULL, 'Zuma5uu680', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(685, NULL, 'Zuma5uu681', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(686, NULL, 'Zuma5uu682', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(687, NULL, 'Zuma5uu683', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(688, NULL, 'Zuma5uu684', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(689, NULL, 'Zuma5uu685', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(690, NULL, 'Zuma5uu686', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(691, NULL, 'Zuma5uu687', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(692, NULL, 'Zuma5uu688', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(693, NULL, 'Zuma5uu689', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(694, NULL, 'Zuma5uu690', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(695, NULL, 'Zuma5uu691', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(696, NULL, 'Zuma5uu692', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(697, NULL, 'Zuma5uu693', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(698, NULL, 'Zuma5uu694', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(699, NULL, 'Zuma5uu695', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(700, NULL, 'Zuma5uu696', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(701, NULL, 'Zuma5uu697', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(702, NULL, 'Zuma5uu698', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(703, NULL, 'Zuma5uu699', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(704, NULL, 'Zuma5uu700', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(705, NULL, 'Zuma5uu701', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(706, NULL, 'Zuma5uu702', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(707, NULL, 'Zuma5uu703', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(708, NULL, 'Zuma5uu704', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(709, NULL, 'Zuma5uu705', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(710, NULL, 'Zuma5uu706', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(711, NULL, 'Zuma5uu707', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(712, NULL, 'Zuma5uu708', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(713, NULL, 'Zuma5uu709', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(714, NULL, 'Zuma5uu710', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(715, NULL, 'Zuma5uu711', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(716, NULL, 'Zuma5uu712', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(717, NULL, 'Zuma5uu713', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(718, NULL, 'Zuma5uu714', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(719, NULL, 'Zuma5uu715', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(720, NULL, 'Zuma5uu716', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(721, NULL, 'Zuma5uu717', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(722, NULL, 'Zuma5uu718', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(723, NULL, 'Zuma5uu719', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(724, NULL, 'Zuma5uu720', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(725, NULL, 'Zuma5uu721', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(726, NULL, 'Zuma5uu722', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(727, NULL, 'Zuma5uu723', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(728, NULL, 'Zuma5uu724', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(729, NULL, 'Zuma5uu725', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(730, NULL, 'Zuma5uu726', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(731, NULL, 'Zuma5uu727', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(732, NULL, 'Zuma5uu728', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(733, NULL, 'Zuma5uu729', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(734, NULL, 'Zuma5uu730', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(735, NULL, 'Zuma5uu731', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(736, NULL, 'Zuma5uu732', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(737, NULL, 'Zuma5uu733', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(738, NULL, 'Zuma5uu734', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(739, NULL, 'Zuma5uu735', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(740, NULL, 'Zuma5uu736', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(741, NULL, 'Zuma5uu737', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(742, NULL, 'Zuma5uu738', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(743, NULL, 'Zuma5uu739', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(744, NULL, 'Zuma5uu740', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(745, NULL, 'Zuma5uu741', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(746, NULL, 'Zuma5uu742', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(747, NULL, 'Zuma5uu743', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(748, NULL, 'Zuma5uu744', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(749, NULL, 'Zuma5uu745', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(750, NULL, 'Zuma5uu746', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(751, NULL, 'Zuma5uu747', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(752, NULL, 'Zuma5uu748', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(753, NULL, 'Zuma5uu749', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(754, NULL, 'Zuma5uu750', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(755, NULL, 'Zuma5uu751', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(756, NULL, 'Zuma5uu752', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(757, NULL, 'Zuma5uu753', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(758, NULL, 'Zuma5uu754', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(759, NULL, 'Zuma5uu755', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(760, NULL, 'Zuma5uu756', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(761, NULL, 'Zuma5uu757', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(762, NULL, 'Zuma5uu758', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(763, NULL, 'Zuma5uu759', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(764, NULL, 'Zuma5uu760', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(765, NULL, 'Zuma5uu761', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(766, NULL, 'Zuma5uu762', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(767, NULL, 'Zuma5uu763', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(768, NULL, 'Zuma5uu764', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(769, NULL, 'Zuma5uu765', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(770, NULL, 'Zuma5uu766', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(771, NULL, 'Zuma5uu767', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(772, NULL, 'Zuma5uu768', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(773, NULL, 'Zuma5uu769', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(774, NULL, 'Zuma5uu770', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(775, NULL, 'Zuma5uu771', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(776, NULL, 'Zuma5uu772', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(777, NULL, 'Zuma5uu773', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(778, NULL, 'Zuma5uu774', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(779, NULL, 'Zuma5uu775', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(780, NULL, 'Zuma5uu776', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(781, NULL, 'Zuma5uu777', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(782, NULL, 'Zuma5uu778', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(783, NULL, 'Zuma5uu779', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(784, NULL, 'Zuma5uu780', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(785, NULL, 'Zuma5uu781', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(786, NULL, 'Zuma5uu782', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(787, NULL, 'Zuma5uu783', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(788, NULL, 'Zuma5uu784', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(789, NULL, 'Zuma5uu785', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(790, NULL, 'Zuma5uu786', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(791, NULL, 'Zuma5uu787', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(792, NULL, 'Zuma5uu788', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(793, NULL, 'Zuma5uu789', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(794, NULL, 'Zuma5uu790', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(795, NULL, 'Zuma5uu791', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(796, NULL, 'Zuma5uu792', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(797, NULL, 'Zuma5uu793', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(798, NULL, 'Zuma5uu794', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(799, NULL, 'Zuma5uu795', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(800, NULL, 'Zuma5uu796', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(801, NULL, 'Zuma5uu797', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(802, NULL, 'Zuma5uu798', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(803, NULL, 'Zuma5uu799', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(804, NULL, 'Zuma5uu800', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(805, NULL, 'Zuma5uu801', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(806, NULL, 'Zuma5uu802', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(807, NULL, 'Zuma5uu803', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(808, NULL, 'Zuma5uu804', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(809, NULL, 'Zuma5uu805', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(810, NULL, 'Zuma5uu806', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(811, NULL, 'Zuma5uu807', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(812, NULL, 'Zuma5uu808', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(813, NULL, 'Zuma5uu809', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(814, NULL, 'Zuma5uu810', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(815, NULL, 'Zuma5uu811', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(816, NULL, 'Zuma5uu812', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(817, NULL, 'Zuma5uu813', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(818, NULL, 'Zuma5uu814', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(819, NULL, 'Zuma5uu815', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(820, NULL, 'Zuma5uu816', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(821, NULL, 'Zuma5uu817', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(822, NULL, 'Zuma5uu818', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(823, NULL, 'Zuma5uu819', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(824, NULL, 'Zuma5uu820', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(825, NULL, 'Zuma5uu821', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(826, NULL, 'Zuma5uu822', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(827, NULL, 'Zuma5uu823', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(828, NULL, 'Zuma5uu824', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(829, NULL, 'Zuma5uu825', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(830, NULL, 'Zuma5uu826', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(831, NULL, 'Zuma5uu827', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(832, NULL, 'Zuma5uu828', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(833, NULL, 'Zuma5uu829', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(834, NULL, 'Zuma5uu830', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(835, NULL, 'Zuma5uu831', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(836, NULL, 'Zuma5uu832', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(837, NULL, 'Zuma5uu833', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(838, NULL, 'Zuma5uu834', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(839, NULL, 'Zuma5uu835', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(840, NULL, 'Zuma5uu836', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(841, NULL, 'Zuma5uu837', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(842, NULL, 'Zuma5uu838', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(843, NULL, 'Zuma5uu839', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(844, NULL, 'Zuma5uu840', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(845, NULL, 'Zuma5uu841', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(846, NULL, 'Zuma5uu842', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(847, NULL, 'Zuma5uu843', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(848, NULL, 'Zuma5uu844', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(849, NULL, 'Zuma5uu845', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(850, NULL, 'Zuma5uu846', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(851, NULL, 'Zuma5uu847', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(852, NULL, 'Zuma5uu848', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(853, NULL, 'Zuma5uu849', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(854, NULL, 'Zuma5uu850', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(855, NULL, 'Zuma5uu851', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(856, NULL, 'Zuma5uu852', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(857, NULL, 'Zuma5uu853', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(858, NULL, 'Zuma5uu854', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(859, NULL, 'Zuma5uu855', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(860, NULL, 'Zuma5uu856', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(861, NULL, 'Zuma5uu857', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(862, NULL, 'Zuma5uu858', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(863, NULL, 'Zuma5uu859', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(864, NULL, 'Zuma5uu860', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(865, NULL, 'Zuma5uu861', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(866, NULL, 'Zuma5uu862', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(867, NULL, 'Zuma5uu863', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(868, NULL, 'Zuma5uu864', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(869, NULL, 'Zuma5uu865', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(870, NULL, 'Zuma5uu866', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(871, NULL, 'Zuma5uu867', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(872, NULL, 'Zuma5uu868', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(873, NULL, 'Zuma5uu869', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(874, NULL, 'Zuma5uu870', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(875, NULL, 'Zuma5uu871', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(876, NULL, 'Zuma5uu872', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(877, NULL, 'Zuma5uu873', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(878, NULL, 'Zuma5uu874', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(879, NULL, 'Zuma5uu875', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(880, NULL, 'Zuma5uu876', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(881, NULL, 'Zuma5uu877', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(882, NULL, 'Zuma5uu878', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(883, NULL, 'Zuma5uu879', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(884, NULL, 'Zuma5uu880', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(885, NULL, 'Zuma5uu881', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(886, NULL, 'Zuma5uu882', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(887, NULL, 'Zuma5uu883', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(888, NULL, 'Zuma5uu884', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(889, NULL, 'Zuma5uu885', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(890, NULL, 'Zuma5uu886', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(891, NULL, 'Zuma5uu887', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(892, NULL, 'Zuma5uu888', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(893, NULL, 'Zuma5uu889', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(894, NULL, 'Zuma5uu890', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(895, NULL, 'Zuma5uu891', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(896, NULL, 'Zuma5uu892', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(897, NULL, 'Zuma5uu893', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(898, NULL, 'Zuma5uu894', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(899, NULL, 'Zuma5uu895', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(900, NULL, 'Zuma5uu896', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(901, NULL, 'Zuma5uu897', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(902, NULL, 'Zuma5uu898', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(903, NULL, 'Zuma5uu899', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(904, NULL, 'Zuma5uu900', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(905, NULL, 'Zuma5uu901', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(906, NULL, 'Zuma5uu902', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(907, NULL, 'Zuma5uu903', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(908, NULL, 'Zuma5uu904', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(909, NULL, 'Zuma5uu905', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(910, NULL, 'Zuma5uu906', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(911, NULL, 'Zuma5uu907', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(912, NULL, 'Zuma5uu908', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(913, NULL, 'Zuma5uu909', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(914, NULL, 'Zuma5uu910', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(915, NULL, 'Zuma5uu911', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(916, NULL, 'Zuma5uu912', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(917, NULL, 'Zuma5uu913', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(918, NULL, 'Zuma5uu914', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(919, NULL, 'Zuma5uu915', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(920, NULL, 'Zuma5uu916', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(921, NULL, 'Zuma5uu917', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(922, NULL, 'Zuma5uu918', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(923, NULL, 'Zuma5uu919', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(924, NULL, 'Zuma5uu920', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(925, NULL, 'Zuma5uu921', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(926, NULL, 'Zuma5uu922', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(927, NULL, 'Zuma5uu923', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(928, NULL, 'Zuma5uu924', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(929, NULL, 'Zuma5uu925', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(930, NULL, 'Zuma5uu926', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(931, NULL, 'Zuma5uu927', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(932, NULL, 'Zuma5uu928', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(933, NULL, 'Zuma5uu929', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(934, NULL, 'Zuma5uu930', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(935, NULL, 'Zuma5uu931', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(936, NULL, 'Zuma5uu932', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(937, NULL, 'Zuma5uu933', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(938, NULL, 'Zuma5uu934', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(939, NULL, 'Zuma5uu935', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(940, NULL, 'Zuma5uu936', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(941, NULL, 'Zuma5uu937', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(942, NULL, 'Zuma5uu938', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(943, NULL, 'Zuma5uu939', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(944, NULL, 'Zuma5uu940', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(945, NULL, 'Zuma5uu941', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(946, NULL, 'Zuma5uu942', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(947, NULL, 'Zuma5uu943', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(948, NULL, 'Zuma5uu944', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(949, NULL, 'Zuma5uu945', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(950, NULL, 'Zuma5uu946', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(951, NULL, 'Zuma5uu947', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(952, NULL, 'Zuma5uu948', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(953, NULL, 'Zuma5uu949', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(954, NULL, 'Zuma5uu950', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(955, NULL, 'Zuma5uu951', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(956, NULL, 'Zuma5uu952', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(957, NULL, 'Zuma5uu953', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(958, NULL, 'Zuma5uu954', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(959, NULL, 'Zuma5uu955', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(960, NULL, 'Zuma5uu956', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(961, NULL, 'Zuma5uu957', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(962, NULL, 'Zuma5uu958', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(963, NULL, 'Zuma5uu959', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(964, NULL, 'Zuma5uu960', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(965, NULL, 'Zuma5uu961', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(966, NULL, 'Zuma5uu962', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(967, NULL, 'Zuma5uu963', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(968, NULL, 'Zuma5uu964', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(969, NULL, 'Zuma5uu965', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(970, NULL, 'Zuma5uu966', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(971, NULL, 'Zuma5uu967', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(972, NULL, 'Zuma5uu968', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(973, NULL, 'Zuma5uu969', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(974, NULL, 'Zuma5uu970', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(975, NULL, 'Zuma5uu971', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(976, NULL, 'Zuma5uu972', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(977, NULL, 'Zuma5uu973', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(978, NULL, 'Zuma5uu974', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(979, NULL, 'Zuma5uu975', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(980, NULL, 'Zuma5uu976', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 2, 10, 0, NULL, '2025-12-04 00:17:08', 'support', '0000-00-00 00:00:00', 1, NULL),
(981, NULL, 'Zuma5uu977', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(982, NULL, 'Zuma5uu978', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(983, NULL, 'Zuma5uu979', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(984, NULL, 'Zuma5uu980', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(985, NULL, 'Zuma5uu981', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(986, NULL, 'Zuma5uu982', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 4, 10, 0, NULL, '2025-12-04 00:17:27', 'support', '0000-00-00 00:00:00', 1, NULL),
(987, NULL, 'Zuma5uu983', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(988, NULL, 'Zuma5uu984', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(989, NULL, 'Zuma5uu985', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(990, NULL, 'Zuma5uu986', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(991, NULL, 'Zuma5uu987', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(992, NULL, 'Zuma5uu988', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 5, 10, 0, NULL, '2025-12-04 00:17:33', 'support', '0000-00-00 00:00:00', 1, NULL),
(993, NULL, 'Zuma5uu989', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(994, NULL, 'Zuma5uu990', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 1, 10, 0, NULL, '2025-12-04 00:17:38', 'support', '0000-00-00 00:00:00', 1, NULL),
(995, NULL, 'Zuma5uu991', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(996, NULL, 'Zuma5uu992', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(997, NULL, 'Zuma5uu993', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(998, NULL, 'Zuma5uu994', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(999, NULL, 'Zuma5uu995', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-04-12 15:50:45', 'support', '0000-00-00 00:00:00', 1, NULL),
(1000, NULL, 'Zuma5uu996', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 45, 10, 0, NULL, '2025-12-04 00:13:28', 'support', '0000-00-00 00:00:00', 0, NULL),
(35002, NULL, 'Lenovo T460', 'Lenovo laptop', 3, 1, '0987654321', 95000.00, 100000.00, 74, 10, 0, 'support', '2025-12-09 12:35:45', 'support', '2025-12-05 10:18:47', 1, NULL),
(35003, NULL, 'Iphone XR', 'iphone bramd', 3, 1, '11223344', 80000.00, 95000.00, 12, 5, 0, NULL, '2025-12-09 12:35:45', 'support', '2025-12-07 03:07:38', 1, NULL),
(35004, NULL, 'Power Bank', '20000mAh', 3, 1, '123456777', 8700.00, 10000.00, 189, 10, 0, NULL, '2025-12-09 12:35:45', 'support', '2025-12-08 01:38:41', 1, NULL),
(35005, NULL, 'Wireless mouse', 'wireless mouse and free mouse pad', 3, 2, '76767648', 4300.00, 4500.00, 89, 20, 0, NULL, '2025-12-09 12:35:45', 'support', '2025-12-09 11:21:50', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_batches`
--

CREATE TABLE `product_batches` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `batch_no` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `received_date` date NOT NULL,
  `production_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` int NOT NULL,
  `updated_by` varchar(32) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'unpaid',
  `purchase_date` date NOT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` text COLLATE utf8mb4_unicode_ci,
  `authorized_by` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_by` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int NOT NULL,
  `sale_id` int NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `return_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `return_type` enum('refund','replace') NOT NULL,
  `total_refund_amount` decimal(10,2) DEFAULT '0.00',
  `remarks` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `reg_by` int DEFAULT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `return_items`
--

CREATE TABLE `return_items` (
  `id` int NOT NULL,
  `return_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `reason` text,
  `refund_amount` decimal(10,2) DEFAULT '0.00',
  `replaced_with_product_id` int DEFAULT NULL,
  `replaced_quantity` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `order_id` varchar(16) NOT NULL,
  `product_id` int NOT NULL,
  `unit_price` double NOT NULL,
  `quantity` int NOT NULL,
  `total` double NOT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `product_id`, `unit_price`, `quantity`, `total`, `updated_by`, `last_update`, `reg_by`, `reg_date`) VALUES
(1, '070425210253', 2, 46.65, 1, 46.65, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(2, '070425210253', 3, 20, 1, 20, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(3, '070425210253', 4, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(4, '070425210253', 5, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(5, '070425210253', 6, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(6, '070425210253', 7, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(7, '070425210253', 8, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(8, '070425210253', 9, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(9, '070425210253', 11, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(10, '070425210253', 21, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-07 09:02:53'),
(11, '080425183100', 2, 46.65, 1, 46.65, NULL, NULL, 'support', '2025-04-08 06:31:00'),
(12, '080425183100', 6, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-08 06:31:00'),
(13, '080425183100', 67, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-08 06:31:00'),
(14, '080425183100', 8, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-08 06:31:00'),
(15, '080425183100', 10, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-04-08 06:31:00'),
(19, '130725081715', 1, 45.65, 3, 136.95, NULL, NULL, 'support', '2025-07-13 08:17:15'),
(20, '130725081715', 6, 23.58, 2, 47.16, NULL, NULL, 'support', '2025-07-13 08:17:15'),
(21, '130725081715', 8, 23.58, 10, 235.8, NULL, NULL, 'support', '2025-07-13 08:17:15'),
(24, '130725082642', 1, 45.65, 12, 547.8, NULL, NULL, 'support', '2025-07-13 08:26:42'),
(25, '130725082642', 3, 20, 13, 260, NULL, NULL, 'support', '2025-07-13 08:26:42'),
(26, '130725101436', 1, 45.65, 1, 45.65, NULL, NULL, 'support', '2025-07-13 10:14:36'),
(27, '130725101436', 3, 20, 1, 20, NULL, NULL, 'support', '2025-07-13 10:14:36'),
(28, '130725101436', 10, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-07-13 10:14:36'),
(29, '140725160455', 30, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-07-14 04:04:55'),
(30, '140725160455', 9, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-07-14 04:04:55'),
(31, '140725160509', 20, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-07-14 04:05:09'),
(32, '140725160509', 5, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-07-14 04:05:09'),
(33, '140725160509', 6, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-07-14 04:05:09'),
(34, '261125082717', 1, 45.65, 1, 45.65, NULL, NULL, 'support', '2025-11-26 08:27:17'),
(35, '261125082717', 6, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-11-26 08:27:17'),
(36, '261125082717', 11, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-11-26 08:27:17'),
(37, '261125082717', 37, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-11-26 08:27:17'),
(38, '261125082811', 5, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-11-26 08:28:11'),
(39, '261125082811', 6, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-11-26 08:28:11'),
(40, '261125082811', 8, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-11-26 08:28:11'),
(41, '011225133433', 49, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-01 01:34:33'),
(42, '011225133433', 4, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-01 01:34:33'),
(43, '011225133433', 8, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-01 01:34:33'),
(44, '011225133433', 5, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-01 01:34:33'),
(50, '041225004314', 1, 45.65, 2, 91.3, NULL, NULL, 'support', '2025-12-04 12:43:14'),
(51, '041225004314', 5, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-04 12:43:14'),
(52, '041225004846', 4, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-04 12:48:46'),
(53, '041225004846', 2, 46.65, 1, 46.65, NULL, NULL, 'support', '2025-12-04 12:48:46'),
(54, '041225005128', 1, 45.65, 5, 228.25, NULL, NULL, 'support', '2025-12-04 12:51:28'),
(55, '041225011702', 2, 46.65, 1, 46.65, NULL, NULL, 'support', '2025-12-04 01:17:02'),
(56, '041225011702', 4, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-04 01:17:02'),
(57, '041225011702', 9, 23.58, 3, 70.74, NULL, NULL, 'support', '2025-12-04 01:17:02'),
(58, '041225012431', 2, 46.65, 1, 46.65, NULL, NULL, 'support', '2025-12-04 01:24:31'),
(59, '041225012431', 12, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-04 01:24:31'),
(60, '041225012431', 13, 23.58, 1, 23.58, NULL, NULL, 'support', '2025-12-04 01:24:31'),
(61, '041225100749', 1, 45.65, 10, 456.5, NULL, NULL, 'support', '2025-12-04 10:07:49'),
(62, '041225103142', 1, 45.65, 1, 45.65, NULL, NULL, 'support', '2025-12-04 10:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `sales_summary`
--

CREATE TABLE `sales_summary` (
  `id` int NOT NULL,
  `order_id` varchar(16) NOT NULL,
  `customer` varchar(128) NOT NULL,
  `payment_type` varchar(16) NOT NULL,
  `payment_ref` varchar(32) DEFAULT NULL,
  `actual_total` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `cash_received` decimal(10,2) DEFAULT NULL,
  `cash_change` decimal(10,2) DEFAULT NULL,
  `items_count` int NOT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `sales_summary`
--

INSERT INTO `sales_summary` (`id`, `order_id`, `customer`, `payment_type`, `payment_ref`, `actual_total`, `discount`, `total`, `cash_received`, `cash_change`, `items_count`, `updated_by`, `last_update`, `reg_by`, `reg_date`) VALUES
(1, '070425210253', 'Customer', 'CASH', NULL, 255.29, 0.00, 255.29, 300.00, 44.71, 10, NULL, '2025-07-13 08:17:44', 'support', '2025-04-07 09:02:53'),
(2, '080425183100', 'Customer', 'CASH', NULL, 140.97, 0.00, 140.97, 150.00, 9.03, 5, NULL, '2025-07-13 08:17:47', 'support', '2025-04-08 06:31:00'),
(3, '130725081715', 'Customer', 'CASH', NULL, 419.91, 0.00, 419.91, 500.00, 80.09, 15, NULL, NULL, 'support', '2025-07-13 08:17:15'),
(4, '130725082642', 'Sani Yahaya', 'POS', '1244858834', 807.80, 7.80, 800.00, NULL, NULL, 25, NULL, NULL, 'support', '2025-07-13 08:26:42'),
(5, '130725101436', 'Tukur Usman', 'CASH', NULL, 89.23, 0.00, 89.23, 100.00, 10.77, 3, NULL, NULL, 'support', '2025-07-13 10:14:36'),
(6, '140725160455', 'Customer', 'CASH', NULL, 47.16, 0.00, 0.00, 0.00, 0.00, 2, NULL, NULL, 'support', '2025-07-14 04:04:55'),
(7, '140725160509', 'Customer', 'CASH', NULL, 70.74, 0.00, 0.00, 0.00, 0.00, 3, NULL, NULL, 'support', '2025-07-14 04:05:09'),
(8, '140725160514', 'Customer', 'CASH', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 3, NULL, NULL, 'support', '2025-07-14 04:05:14'),
(9, '140725160521', 'Customer', 'CASH', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 3, NULL, NULL, 'support', '2025-07-14 04:05:21'),
(10, '261125082717', 'Customer', 'CASH', NULL, 116.39, 0.00, 0.00, 0.00, 0.00, 4, NULL, NULL, 'support', '2025-11-26 08:27:17'),
(11, '261125082811', 'Customer', 'CASH', NULL, 70.74, 0.00, 70.74, 75.00, 4.26, 3, NULL, NULL, 'support', '2025-11-26 08:28:11'),
(12, '011225133433', 'Customer', 'CASH', NULL, 94.32, 0.00, 94.32, 100.00, 5.68, 4, NULL, NULL, 'support', '2025-12-01 01:34:33'),
(13, '041225004314', 'Customer', 'CASH', NULL, 114.88, 0.00, 114.88, 114.00, -0.88, 0, NULL, NULL, 'support', '2025-12-04 12:43:14'),
(14, '041225004846', 'Customer', 'POS', '765434', 70.23, 0.00, 70.23, 70.00, -0.23, 0, NULL, NULL, 'support', '2025-12-04 12:48:46'),
(15, '041225005128', 'Customer', 'CASH', NULL, 228.25, 0.00, 228.25, 229.00, 0.75, 0, NULL, NULL, 'support', '2025-12-04 12:51:28'),
(16, '041225011702', 'Customer', 'CASH', NULL, 140.97, 0.00, 140.97, 141.00, 0.03, 0, NULL, NULL, 'support', '2025-12-04 01:17:02'),
(17, '041225012431', 'Customer', 'POS', '654323454', 93.81, 0.00, 93.81, 94.00, 0.19, 0, NULL, NULL, 'support', '2025-12-04 01:24:31'),
(18, '041225100749', 'Customer', 'CASH', NULL, 456.50, 0.00, 456.50, 456.00, -0.50, 0, NULL, NULL, 'support', '2025-12-04 10:07:49'),
(19, '041225103142', 'Customer', 'CASH', NULL, 45.65, 0.00, 45.65, 46.00, 0.35, 0, NULL, NULL, 'support', '2025-12-04 10:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `movement_type` enum('sale','purchase','adjustment','return') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int NOT NULL,
  `field1` varchar(32) NOT NULL,
  `field2` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `supplier_name` varchar(150) NOT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `updated_by` varchar(32) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reg_by` varchar(32) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_name`, `phone`, `email`, `address`, `updated_by`, `last_update`, `reg_by`, `reg_date`) VALUES
(1, 'General Supplier', 'general', '0909988776', 'sdfghj@ddd.com', 'dfghj fghjkl; cvb vbnmkjhg', 'support', '2025-12-03 23:46:01', 'admin', '2025-12-03 18:46:04'),
(2, 'siit partners', 'suppliers', '0987654321', 'test@test.com', 'maitangaran house', 'babba', '2025-12-03 23:18:17', '', '2025-12-03 23:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `purchase_id` int DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method_id` int DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','number','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'store_name', 'LunoByte technology', 'text', 'Store name', 'support', '2025-12-09 09:20:55'),
(2, 'store_address', 'No. 42, Flat 9, Mai Tangaran House, Zoo Road,kano state.', 'text', 'Store address', 'support', '2025-12-09 09:20:55'),
(3, 'store_phone', '+234 812 499 0409', 'text', 'Store phone number', 'support', '2025-12-09 09:20:55'),
(4, 'store_email', 'info@lunobyte.com', 'text', 'Store email', 'support', '2025-12-09 09:20:55'),
(5, 'tax_rate', '0', 'number', 'Default tax rate percentage', 'support', '2025-12-09 09:20:55'),
(6, 'currency_symbol', '₦', 'text', 'Currency symbol', 'support', '2025-12-09 09:20:55'),
(7, 'currency_code', 'NGN', 'text', 'Currency code', 'support', '2025-12-09 09:20:55'),
(8, 'receipt_footer', 'Thank you for your business!', 'text', 'Receipt footer message', 'support', '2025-12-09 09:20:55'),
(9, 'session_timeout', '30', 'number', 'Session timeout in minutes', 'support', '2025-12-09 09:20:55'),
(10, 'low_stock_threshold', '10', 'number', 'Default low stock alert threshold', 'support', '2025-12-09 09:20:55'),
(35, 'company_logo', 'uploads/logo/logo_1765272055.png', 'text', NULL, 'support', '2025-12-09 09:20:55');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `change_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('completed','void','refunded','held') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `transaction_date`, `user_id`, `customer_id`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `payment_method`, `amount_paid`, `change_amount`, `status`, `notes`, `created_by`, `created_at`) VALUES
(1, '2025-12-04 13:34:36', 1, 1, 23.58, 1.77, 0.00, 25.35, 'CASH', 1000.00, 974.65, 'completed', 'Payment ref: REF123', '1', '2025-12-04 13:34:36'),
(2, '2025-12-04 13:35:13', 1, 1, 23.58, 1.77, 0.00, 25.35, 'CASH', 1000.00, 974.65, 'completed', 'Payment ref: REF123', '1', '2025-12-04 13:35:13'),
(3, '2025-12-04 13:40:51', 1, 1, 47.16, 3.54, 100.00, -49.30, 'CASH', 5000.00, 5049.30, 'completed', 'Payment ref: REF001', '1', '2025-12-04 13:40:51'),
(4, '2025-12-04 13:55:42', 1, 1, 23.58, 1.77, 0.00, 25.35, 'CASH', 1000.00, 974.65, 'completed', 'Payment ref: REF123', '1', '2025-12-04 13:55:42'),
(5, '2025-12-04 13:56:36', 1, 0, 69.23, 5.19, 0.00, 74.42, 'CASH', 70.00, -4.42, 'completed', NULL, 'support', '2025-12-04 13:56:36'),
(6, '2025-12-04 15:10:59', 1, 0, 139.46, 10.46, 0.00, 149.92, 'CASH', 140.00, -9.92, 'completed', NULL, 'support', '2025-12-04 15:10:59'),
(7, '2025-12-04 15:23:46', 1, 0, 45.65, 3.42, 0.00, 49.07, 'CASH', 50.00, 0.93, 'completed', NULL, 'support', '2025-12-04 15:23:46'),
(8, '2025-12-04 15:31:42', 1, 0, 45.65, 3.42, 0.00, 49.07, 'CASH', 50.00, 0.93, 'completed', NULL, 'support', '2025-12-04 15:31:42'),
(9, '2025-12-04 15:40:34', 1, 0, 47.16, 3.54, 0.00, 50.70, 'CASH', 50.00, -0.70, 'completed', NULL, 'support', '2025-12-04 15:40:34'),
(10, '2025-12-04 15:44:25', 1, 0, 23.58, 1.77, 0.00, 25.35, 'CASH', 20.00, -5.35, 'completed', NULL, 'support', '2025-12-04 15:44:25'),
(11, '2025-12-04 15:53:17', 1, 0, 23.58, 1.77, 0.00, 25.35, 'CASH', 20.00, -5.35, 'completed', NULL, 'support', '2025-12-04 15:53:17'),
(12, '2025-12-04 15:54:20', 1, 0, 47.16, 3.54, 0.00, 50.70, 'CASH', 40.00, -10.70, 'completed', NULL, 'support', '2025-12-04 15:54:20'),
(13, '2025-12-04 15:56:35', 1, 0, 70.74, 5.31, 0.00, 76.05, 'POS', 70.00, -6.05, 'completed', 'Payment ref: 98765', 'support', '2025-12-04 15:56:35'),
(14, '2025-12-04 16:00:20', 1, 0, 448.02, 33.60, 0.00, 481.62, 'CASH', 448.00, -33.62, 'completed', NULL, 'support', '2025-12-04 16:00:20'),
(15, '2025-12-04 16:02:35', 1, 0, 636.66, 47.75, 0.00, 684.41, 'CASH', 700.00, 15.59, 'completed', NULL, 'support', '2025-12-04 16:02:35'),
(16, '2025-12-04 16:41:48', 1, 0, 23.58, 1.77, 0.00, 25.35, 'CASH', 30.00, 4.65, 'completed', NULL, 'support', '2025-12-04 16:41:48'),
(17, '2025-12-04 17:03:12', 1, 0, 165.06, 12.38, 0.00, 177.44, 'CASH', 165.00, -12.44, 'completed', NULL, 'support', '2025-12-04 17:03:12'),
(18, '2025-12-04 17:04:13', 1, 0, 94.32, 7.07, 0.00, 101.39, 'CASH', 94.00, -7.39, 'completed', NULL, 'support', '2025-12-04 17:04:13'),
(19, '2025-12-04 17:08:17', 1, 0, 94.32, 7.07, 0.00, 101.39, 'CASH', 94.00, -7.39, 'completed', NULL, 'support', '2025-12-04 17:08:17'),
(20, '2025-12-04 17:11:29', 1, 0, 70.74, 5.31, 0.00, 76.05, 'CASH', 70.00, -6.05, 'completed', NULL, 'support', '2025-12-04 17:11:29'),
(21, '2025-12-04 17:20:37', 1, NULL, 93.30, 7.00, 0.00, 100.30, 'CASH', 93.00, -7.30, 'completed', NULL, 'support', '2025-12-04 17:20:37'),
(6789001, '2025-12-04 18:04:18', 1, 1, 100.00, 10.00, 5.00, 105.00, 'cash', 0.00, 0.00, 'completed', NULL, 'admin', '2025-12-04 18:04:18'),
(6789002, '2025-12-04 18:04:18', 1, 2, 200.00, 20.00, 10.00, 210.00, 'pos', 0.00, 0.00, 'completed', NULL, 'admin', '2025-12-04 18:04:18'),
(6789003, '2025-12-05 11:19:30', 1, 1, 100000.00, 7500.00, 0.00, 107500.00, 'CASH', 100000.00, -7500.00, 'completed', NULL, 'support', '2025-12-05 11:19:30'),
(6789004, '2025-12-05 12:55:07', 1, 2, 1000050.00, 75003.75, 50.00, 1075003.75, 'CASH', 1000000.00, -75003.75, 'completed', NULL, 'support', '2025-12-05 12:55:07'),
(6789005, '2025-12-05 12:56:48', 1, NULL, 47.16, 3.54, 0.00, 50.70, 'POS', 50.00, -0.70, 'completed', 'Payment ref: 09876567', 'support', '2025-12-05 12:56:48'),
(6789006, '2025-12-05 14:05:51', 1, NULL, 23.58, 1.77, 0.00, 25.35, 'CASH', 30.00, 4.65, 'completed', NULL, 'support', '2025-12-05 14:05:51'),
(6789007, '2025-12-05 14:14:59', 1, NULL, 50.00, 3.75, 0.00, 53.75, 'CASH', 50.00, -3.75, 'completed', NULL, 'support', '2025-12-05 14:14:59'),
(6789008, '2025-12-05 14:24:01', 1, NULL, 117.90, 8.84, 0.00, 126.74, 'POS', 118.00, -8.74, 'completed', 'Payment ref: 456789', 'support', '2025-12-05 14:24:01'),
(6789009, '2025-12-05 14:26:30', 1, NULL, 60.00, 4.50, 0.00, 64.50, 'POS', 60.00, -4.50, 'completed', 'Payment ref: 98765', 'support', '2025-12-05 14:26:30'),
(6789010, '2025-12-05 17:00:43', 1, NULL, 50.00, 375.00, 0.00, 425.00, 'CASH', 425.00, 0.00, 'completed', NULL, 'support', '2025-12-05 17:00:43'),
(6789011, '2025-12-05 17:32:59', 1, NULL, 100000.00, 700000.00, 0.00, 800000.00, 'CASH', 10000.00, -790000.00, 'completed', NULL, 'support', '2025-12-05 17:32:59'),
(6789012, '2025-12-05 17:34:42', 1, 2, 100150.00, 701050.00, 0.00, 801200.00, 'CASH', 100150.00, -701050.00, 'completed', NULL, 'support', '2025-12-05 17:34:42'),
(6789013, '2025-12-05 17:38:43', 1, NULL, 50.00, 350.00, 0.00, 400.00, 'CASH', 50.00, -350.00, 'completed', NULL, 'support', '2025-12-05 17:38:43'),
(6789014, '2025-12-05 17:54:17', 1, NULL, 50.00, 350.00, 0.00, 400.00, 'CASH', 50.00, -350.00, 'completed', NULL, 'support', '2025-12-05 17:54:17'),
(6789015, '2025-12-05 17:58:24', 1, NULL, 50.00, 350.00, 0.00, 400.00, 'CASH', 50.00, -350.00, 'completed', NULL, 'support', '2025-12-05 17:58:24'),
(6789016, '2025-12-05 18:08:37', 1, NULL, 50.00, 350.00, 0.00, 400.00, 'CASH', 50.00, -350.00, 'completed', NULL, 'support', '2025-12-05 18:08:37'),
(6789017, '2025-12-07 12:47:21', 0, 5, 100000.00, 0.00, 0.00, 100000.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_69356949479788.17932312', 'system', '2025-12-07 12:47:21'),
(6789018, '2025-12-07 12:49:21', 0, 6, 100000.00, 0.00, 0.00, 100000.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_693569c17e5d50.49508813', 'system', '2025-12-07 12:49:21'),
(6789019, '2025-12-07 12:50:50', 0, 0, 100000.00, 0.00, 0.00, 100000.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_69356a1ad8f906.55486099', 'system', '2025-12-07 12:50:50'),
(6789020, '2025-12-07 13:15:06', 1, 0, 100000.00, 0.00, 0.00, 100000.00, 'CASH', 100000.00, 0.00, 'completed', 'Token: txn_69356fcab74f34.60238212', 'support', '2025-12-07 13:15:06'),
(6789021, '2025-12-07 13:32:38', 1, 1, 100200.00, 0.00, 0.00, 100200.00, 'CASH', 100200.00, 0.00, 'completed', 'Token: txn_693573e61ad552.66014099', 'support', '2025-12-07 13:32:38'),
(6789022, '2025-12-07 15:16:37', 1, NULL, 264.00, 0.00, 0.00, 264.00, 'CASH', 264.00, 0.00, 'completed', 'Token: txn_69358c45a30aa2.13360982 | Customer: isa', 'support', '2025-12-07 15:16:37'),
(6789023, '2025-12-07 16:08:19', 1, 2, 95000.00, 0.00, 0.00, 95000.00, 'CASH', 95000.00, 0.00, 'completed', 'Token: txn_69359863a56a88.74952934', 'support', '2025-12-07 16:08:19'),
(6789024, '2025-12-07 16:15:17', 1, 0, 100023.58, 0.00, 0.00, 100023.58, 'POS', 0.00, 0.00, 'completed', 'Token: txn_69359a05ecd126.33114868', 'support', '2025-12-07 16:15:17'),
(6789025, '2025-12-08 09:37:54', 1, NULL, 195000.00, 0.00, 0.00, 195000.00, 'CASH', 195000.00, 0.00, 'completed', 'Token: txn_69368e622c2290.58912682 | Customer: Walk-in', 'support', '2025-12-08 09:37:54'),
(6789026, '2025-12-08 14:37:08', 1, 3, 1320.00, 0.00, 0.00, 1320.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_6936d4849eb085.21668188', 'support', '2025-12-08 14:37:08'),
(6789027, '2025-12-08 14:52:46', 1, 2, 200100.00, 0.00, 100.00, 200000.00, 'CASH', 200100.00, 100.00, 'completed', 'Token: txn_6936d82ed683c6.91624583', 'support', '2025-12-08 14:52:46'),
(6789028, '2025-12-08 15:14:34', 1, 1, 95000.00, 0.00, 0.00, 95000.00, 'CASH', 95000.00, 0.00, 'completed', 'Token: txn_6936dd4a338882.33005162', 'support', '2025-12-08 15:14:34'),
(6789029, '2025-12-08 15:19:07', 1, NULL, 50000.00, 0.00, 0.00, 50000.00, 'CASH', 50000.00, 0.00, 'completed', 'Token: txn_6936de5b0286c3.96039066 | Customer: Walk-in', 'support', '2025-12-08 15:19:07'),
(6789030, '2025-12-08 15:21:45', 1, 3, 50000.00, 0.00, 0.00, 50000.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_6936def9d75d97.75405307', 'support', '2025-12-08 15:21:45'),
(6789031, '2025-12-08 15:54:13', 1, 3, 195252.32, 0.00, 0.00, 195252.32, 'POS', 0.00, 0.00, 'completed', 'Token: txn_6936e6954c9ad1.17681106', 'support', '2025-12-08 15:54:13'),
(6789032, '2025-12-09 12:25:02', 1, 12, 90000.00, 0.00, 0.00, 90000.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_6938070e01a928.44445171', 'support', '2025-12-09 12:25:02'),
(6789033, '2025-12-09 13:35:45', 1, 12, 209550.00, 0.00, 0.00, 209550.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_693817a16ea9b4.06836184', 'support', '2025-12-09 13:35:45');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `item_id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`item_id`, `transaction_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `discount`, `line_total`) VALUES
(1, 2, 7, 'Zuma5uu3', 1, 23.58, 0.00, 23.58),
(2, 3, 7, 'Zuma5uu3', 2, 23.58, 0.00, 47.16),
(3, 4, 7, 'Zuma5uu3', 1, 23.58, 0.00, 23.58),
(4, 5, 1, 'Ayaba', 1, 45.65, 0.00, 45.65),
(5, 5, 4, 'Zuma2', 1, 23.58, 0.00, 23.58),
(6, 6, 1, 'Ayaba', 1, 45.65, 0.00, 45.65),
(7, 6, 2, 'Zoom', 1, 46.65, 0.00, 46.65),
(8, 6, 6, 'Zuma5uu2', 1, 23.58, 0.00, 23.58),
(9, 6, 8, 'Zuma5uu4', 1, 23.58, 0.00, 23.58),
(10, 7, 1, 'Ayaba', 1, 45.65, 0.00, 45.65),
(11, 8, 1, 'Ayaba', 1, 45.65, 0.00, 45.65),
(12, 9, 11, 'Zuma5uu7', 2, 23.58, 0.00, 47.16),
(13, 10, 6, 'Zuma5uu2', 1, 23.58, 0.00, 23.58),
(14, 11, 6, 'Zuma5uu2', 1, 23.58, 0.00, 23.58),
(15, 12, 8, 'Zuma5uu4', 2, 23.58, 0.00, 47.16),
(16, 13, 5, 'Zuma5uu1', 3, 23.58, 0.00, 70.74),
(17, 14, 10, 'Zuma5uu6', 19, 23.58, 0.00, 448.02),
(18, 15, 999, 'Zuma5uu995', 3, 23.58, 0.00, 70.74),
(19, 15, 997, 'Zuma5uu993', 2, 23.58, 0.00, 47.16),
(20, 15, 995, 'Zuma5uu991', 12, 23.58, 0.00, 282.96),
(21, 15, 993, 'Zuma5uu989', 10, 23.58, 0.00, 235.80),
(22, 16, 4, 'Zuma2', 1, 23.58, 0.00, 23.58),
(23, 17, 5, 'Zuma5uu1', 7, 23.58, 0.00, 165.06),
(24, 18, 6, 'Zuma5uu2', 4, 23.58, 0.00, 94.32),
(25, 19, 4, 'Zuma2', 4, 23.58, 0.00, 94.32),
(26, 20, 8, 'Zuma5uu4', 3, 23.58, 0.00, 70.74),
(27, 21, 2, 'Zoom', 2, 46.65, 0.00, 93.30),
(28, 6789003, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(29, 6789004, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(30, 6789004, 35002, 'Lenovo T460', 10, 100000.00, 0.00, 1000000.00),
(31, 6789005, 11, 'Zuma5uu7', 1, 23.58, 0.00, 23.58),
(32, 6789005, 10, 'Zuma5uu6', 1, 23.58, 0.00, 23.58),
(33, 6789006, 4, 'Zuma2', 1, 23.58, 0.00, 23.58),
(34, 6789007, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(35, 6789008, 4, 'Zuma2', 5, 23.58, 0.00, 117.90),
(36, 6789009, 3, 'Zoom1', 3, 20.00, 0.00, 60.00),
(37, 6789010, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(38, 6789011, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(39, 6789012, 1, 'Ayaba', 3, 50.00, 0.00, 150.00),
(40, 6789012, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(41, 6789013, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(42, 6789014, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(43, 6789015, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(44, 6789016, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(45, 6789017, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(46, 6789018, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(47, 6789019, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(48, 6789020, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(49, 6789021, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(50, 6789021, 1, 'Ayaba', 4, 50.00, 0.00, 200.00),
(51, 6789022, 2, 'Zoom', 3, 88.00, 0.00, 264.00),
(52, 6789023, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(53, 6789024, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(54, 6789024, 105, 'Zuma5uu101', 1, 23.58, 0.00, 23.58),
(55, 6789025, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(56, 6789025, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(57, 6789026, 2, 'Zoom', 15, 88.00, 0.00, 1320.00),
(58, 6789027, 35002, 'Lenovo T460', 2, 100000.00, 0.00, 200000.00),
(59, 6789027, 1, 'Ayaba', 2, 50.00, 0.00, 100.00),
(60, 6789028, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(61, 6789029, 35004, 'Power Bank', 5, 10000.00, 0.00, 50000.00),
(62, 6789030, 35004, 'Power Bank', 5, 10000.00, 0.00, 50000.00),
(63, 6789031, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(64, 6789031, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(65, 6789031, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(66, 6789031, 2, 'Zoom', 1, 88.00, 0.00, 88.00),
(67, 6789031, 3, 'Zoom1', 1, 20.00, 0.00, 20.00),
(68, 6789031, 4, 'Zuma2', 1, 23.58, 0.00, 23.58),
(69, 6789031, 5, 'Zuma5uu1', 1, 23.58, 0.00, 23.58),
(70, 6789031, 14, 'Zuma5uu10', 1, 23.58, 0.00, 23.58),
(71, 6789031, 104, 'ZUMA5UU100', 1, 23.58, 0.00, 23.58),
(72, 6789032, 35005, 'Wireless mouse', 20, 4500.00, 0.00, 90000.00),
(73, 6789033, 35005, 'Wireless mouse', 1, 4500.00, 0.00, 4500.00),
(74, 6789033, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(75, 6789033, 35004, 'Power Bank', 1, 10000.00, 0.00, 10000.00),
(76, 6789033, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(77, 6789033, 1, 'Ayaba', 1, 50.00, 0.00, 50.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_type`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`);

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `discount_rules`
--
ALTER TABLE `discount_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `held_transactions`
--
ALTER TABLE `held_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_barcode` (`barcode`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `reg_by` (`reg_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `return_items`
--
ALTER TABLE `return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `replaced_with_product_id` (`replaced_with_product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`);

--
-- Indexes for table `sales_summary`
--
ALTER TABLE `sales_summary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`),
  ADD KEY `idx_payment_method_id` (`payment_type`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_movement_type` (`movement_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_transaction_date` (`transaction_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `discount_rules`
--
ALTER TABLE `discount_rules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `held_transactions`
--
ALTER TABLE `held_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `price_history`
--
ALTER TABLE `price_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35006;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_items`
--
ALTER TABLE `return_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `sales_summary`
--
ALTER TABLE `sales_summary`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6789034;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `admins` (`username`),
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`reg_by`) REFERENCES `admins` (`username`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
