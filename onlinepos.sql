-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 09, 2025 at 04:16 PM
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
(2, 'Alh Abubakar', '09012345678', 'habu@test.com', '106 Jefferson St,Weehawken, New Jersey(NJ), 07086', 1876203.75, '2025-12-04 11:19:39', '2025-12-05 17:34:42', 1, 0),
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
(2, NULL, 'Zoom', 'fsdhfhhhf', 1, 1, '8901057335522', 46.65, 88.00, 20, 10, 0, 'support', '2025-12-09 12:37:44', 'support', '2023-07-30 11:06:53', 1, NULL),
(3, NULL, 'Zoom1', 'fjsjjfjjff', 1, 1, '1234567890', 15.00, 20.00, 19, 10, 0, 'support', '2025-12-08 15:54:13', 'support', '2023-07-30 11:06:54', 1, NULL),
(4, NULL, 'Zuma2', 'sffsjfjd', 1, 1, '80', 23.58, 23.58, 35, 10, 0, 'support', '2025-12-08 15:54:13', 'support', '2023-07-30 11:06:55', 1, NULL),
(5, NULL, 'Zuma5uu1', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 37, 10, 0, 'support', '2025-12-08 15:54:13', 'support', '2023-07-30 11:06:56', 1, NULL),
(6, NULL, 'Zuma5uu2', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 35, 10, 0, 'support', '2025-11-26 09:28:11', 'support', '2023-07-30 11:06:57', 1, NULL),
(7, NULL, 'Zuma5uu3', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 42, 10, 0, 'support', '2025-04-12 16:50:45', 'support', '2023-07-30 11:06:58', 0, NULL),
(8, NULL, 'Zuma5uu4', 'dggdhhshs', 1, 1, '80', 23.58, 44.00, 30, 50, 0, 'support', '2025-12-05 11:42:02', 'support', '2023-07-30 11:06:59', 1, NULL),
(35002, NULL, 'Lenovo T460', 'Lenovo laptop', 3, 1, '0987654321', 95000.00, 100000.00, 74, 10, 0, 'support', '2025-12-09 13:35:45', 'support', '2025-12-05 10:18:47', 1, NULL),
(35003, NULL, 'Iphone XR', 'iphone bramd', 3, 1, '11223344', 80000.00, 95000.00, 12, 5, 0, NULL, '2025-12-09 13:35:45', 'support', '2025-12-07 03:07:38', 1, NULL),
(35004, NULL, 'Power Bank', '20000mAh', 3, 1, '123456777', 8700.00, 10000.00, 189, 10, 0, NULL, '2025-12-09 13:35:45', 'support', '2025-12-08 01:38:41', 1, NULL),
(35005, NULL, 'Wireless mouse', 'wireless mouse and free mouse pad', 3, 2, '76767648', 4300.00, 4500.00, 89, 20, 0, NULL, '2025-12-09 13:35:45', 'support', '2025-12-09 11:21:50', 1, NULL);

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
(2, '080425183100', 'Customer', 'CASH', NULL, 140.97, 0.00, 140.97, 150.00, 9.03, 5, NULL, '2025-07-13 09:17:47', 'support', '2025-04-08 06:31:00'),
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
(2, 'siit partners', 'suppliers', '0987654321', 'test@test.com', 'maitangaran house', 'babba', '2025-12-04 00:18:17', '', '2025-12-03 23:16:51');

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
(1, 'store_name', 'S & I IT partners LTD', 'text', 'Store name', 'support', '2025-12-09 16:08:53'),
(2, 'store_address', 'No. 42, Flat 9, Mai Tangaran House, Zoo Road,kano state.', 'text', 'Store address', 'support', '2025-12-09 16:08:53'),
(3, 'store_phone', '+234 812 499 0409', 'text', 'Store phone number', 'support', '2025-12-09 16:08:53'),
(4, 'store_email', 'info@siitpartners.com', 'text', 'Store email', 'support', '2025-12-09 16:08:53'),
(5, 'tax_rate', '0', 'number', 'Default tax rate percentage', 'support', '2025-12-09 16:08:53'),
(6, 'currency_symbol', '₦', 'text', 'Currency symbol', 'support', '2025-12-09 16:08:53'),
(7, 'currency_code', 'NGN', 'text', 'Currency code', 'support', '2025-12-09 16:08:53'),
(8, 'receipt_footer', 'Thank you for your business!', 'text', 'Receipt footer message', 'support', '2025-12-09 16:08:53'),
(9, 'session_timeout', '30', 'number', 'Session timeout in minutes', 'support', '2025-12-09 16:08:53'),
(10, 'low_stock_threshold', '10', 'number', 'Default low stock alert threshold', 'support', '2025-12-09 16:08:53');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
