-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2025 at 10:05 AM
-- Server version: 8.0.44-0ubuntu0.24.04.2
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
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `task_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text,
  `details_json` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `task_id`, `project_id`, `action_type`, `description`, `details_json`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, NULL, 1, 'project_created', 'Created new project: E-commerce Platform', NULL, '192.168.1.100', NULL, '2025-11-18 15:38:08'),
(2, 1, 1, 1, 'task_created', 'Created task: E-commerce Dashboard', NULL, '192.168.1.100', NULL, '2025-11-18 15:38:08'),
(3, 1, 1, 1, 'task_assigned', 'Assigned frontend role to Frontend Developer', NULL, '192.168.1.100', NULL, '2025-11-18 15:38:08'),
(4, 2, 1, 1, 'task_started', 'Started working on E-commerce Dashboard', NULL, '192.168.1.101', NULL, '2025-11-18 15:38:08'),
(5, 2, 1, 1, 'task_completed', 'Completed frontend development for E-commerce Dashboard', NULL, '192.168.1.101', NULL, '2025-11-18 15:38:08'),
(6, 1, 1, 1, 'shares_calculated', 'Calculated share distribution for E-commerce Dashboard', NULL, '192.168.1.100', NULL, '2025-11-18 15:38:08'),
(7, 1, NULL, NULL, 'user_registered', 'New user registered: Senior Frontend', NULL, '192.168.1.100', NULL, '2025-11-18 15:38:08'),
(8, 5, 8, NULL, 'task_created', 'Created new task: patoosh AI', NULL, NULL, NULL, '2025-11-27 16:05:37'),
(9, 1, 9, NULL, 'task_created', 'Created new task: patoosh AI', NULL, NULL, NULL, '2025-11-27 16:10:26'),
(10, 1, 11, NULL, 'task_created', 'Created new task: patoosh AI', NULL, NULL, NULL, '2025-11-27 16:15:03'),
(11, 1, 12, NULL, 'task_created', 'Created new task: Samira Tea', NULL, NULL, NULL, '2025-11-28 15:09:07'),
(12, 1, 12, NULL, 'task_updated', 'Updated task details and assignments for: Samira Tea', NULL, NULL, NULL, '2025-11-28 15:17:40'),
(13, 1, 12, NULL, 'task_updated', 'Updated task details and assignments for: Samira Tea', NULL, NULL, NULL, '2025-11-28 15:17:52'),
(22, NULL, 11, NULL, 'task_status_changed', 'Task status changed from pending to in_progress', NULL, NULL, NULL, '2025-11-28 15:23:05'),
(23, 2, 11, NULL, 'task_started', 'Started working on task', NULL, NULL, NULL, '2025-11-28 15:23:05'),
(24, 2, 4, NULL, 'subtask_completed', 'Marked individual part as complete', NULL, NULL, NULL, '2025-11-28 15:23:56'),
(25, NULL, 12, NULL, 'task_status_changed', 'Task status changed from pending to in_progress', NULL, NULL, NULL, '2025-11-28 15:24:17'),
(26, 2, 12, NULL, 'task_started', 'Started working on task', NULL, NULL, NULL, '2025-11-28 15:24:17'),
(27, NULL, 12, NULL, 'task_status_changed', 'Task status changed from in_progress to completed', NULL, NULL, NULL, '2025-11-28 15:24:31'),
(28, 2, 12, NULL, 'task_completed', 'Task fully completed by all assignees', NULL, NULL, NULL, '2025-11-28 15:24:31'),
(29, 1, 13, NULL, 'task_created', 'Created new task: test task', NULL, NULL, NULL, '2025-11-28 15:39:44'),
(30, NULL, 13, NULL, 'task_status_changed', 'Task status changed from pending to in_progress', NULL, NULL, NULL, '2025-11-28 15:40:13'),
(31, 2, 13, NULL, 'task_started', 'Started working on task', NULL, NULL, NULL, '2025-11-28 15:40:13'),
(32, NULL, 13, NULL, 'task_status_changed', 'Task status changed from in_progress to completed', NULL, NULL, NULL, '2025-11-28 15:40:26'),
(33, 2, 13, NULL, 'task_completed', 'Task fully completed by all assignees', NULL, NULL, NULL, '2025-11-28 15:40:26'),
(34, NULL, 13, NULL, 'shares_calculated', 'Calculated share distribution for task', NULL, NULL, NULL, '2025-11-28 15:40:26'),
(35, 1, 14, NULL, 'task_created', 'Created new task: test task', NULL, NULL, NULL, '2025-11-28 16:08:51'),
(36, 1, 15, NULL, 'task_created', 'Created new task: test task', NULL, NULL, NULL, '2025-11-28 16:13:42'),
(37, NULL, 15, NULL, 'task_status_changed', 'Task status changed from pending to in_progress', NULL, NULL, NULL, '2025-11-28 17:23:41'),
(38, 2, 15, NULL, 'task_started', 'Started working on task', NULL, NULL, NULL, '2025-11-28 17:23:41'),
(39, NULL, 15, NULL, 'shares_calculated', 'Calculated share distribution (Status: pending)', NULL, NULL, NULL, '2025-11-28 17:23:41'),
(40, NULL, 15, NULL, 'task_status_changed', 'Task status changed from in_progress to completed', NULL, NULL, NULL, '2025-11-28 17:24:50'),
(41, 2, 15, NULL, 'task_completed', 'Task fully completed by all assignees', NULL, NULL, NULL, '2025-11-28 17:24:50'),
(42, NULL, 15, NULL, 'shares_calculated', 'Calculated share distribution (Status: calculated)', NULL, NULL, NULL, '2025-11-28 17:24:50');

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
(1, 'support', '$2y$10$Eckg59ury3rFuwgj3LsOfendRB4sJRrspmmpcbpxuBTvXcTJGHibW', 'Saad', 'Salim', 'Ahmad', '08098115556', 'muhammadsalim2007@gmail.com', 2, '$2y$11$368394ef5b14ee1d54878upYfeARMxnKMJr11e9/N53KkfpeCvAI.', 1, 0, 0, 0, 0, 1, 'system', '2025-12-11 19:18:35', 'support', '2019-09-02 09:00:00', 1),
(4, 'cashier', '$2y$10$CyL9wEikeFgv08dXt1lvge.4Fi1iWGbvpTMUZ9jV11iCgGCnbVlE6', 'LABARAN', 'ADAM', NULL, NULL, 'adam@gmail.com', NULL, NULL, 3, 0, 0, 0, 0, 0, NULL, '2025-12-11 19:21:53', 'support', '2025-12-05 11:03:31', 1),
(5, 'abdurrahman775', '$2y$10$rcygF8NvgJtWe/m/0tw4T.tE/TeyTEothjXriEMOhBIyTZvPDC0YW', 'ALHASSAN', 'ABDURRAHMAN', NULL, NULL, 'abdurrahmanalhssan775@gmail.com', NULL, NULL, 2, 0, 0, 0, 0, 0, NULL, '2025-12-11 19:24:46', 'support', '2025-12-11 05:03:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `id` int NOT NULL,
  `username` varchar(32) DEFAULT NULL,
  `ActionType` varchar(50) NOT NULL,
  `Description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `auditlog`
--

INSERT INTO `auditlog` (`id`, `username`, `ActionType`, `Description`, `ip_address`, `user_agent`, `Timestamp`) VALUES
(19, 'guest', 'UPDATE_CUSTOMER', 'Updated customer: Test Customer (ID: 999)', '0.0.0.0', 'Unknown', '2025-12-09 18:50:21'),
(63, 'abdurrahman775', 'LOGOUT', 'User logged out', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 10:56:14'),
(64, 'support', 'LOGOUT', 'User logged out', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 10:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `backup_logs`
--

CREATE TABLE `backup_logs` (
  `id` int NOT NULL,
  `backup_type` enum('auto','manual') NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `status` enum('success','failed') NOT NULL,
  `error_message` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Table structure for table `clans`
--

CREATE TABLE `clans` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clans`
--

INSERT INTO `clans` (`id`, `name`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Team 8', 'We specialize in full stack development and mobile App development with latest technology and frme works.', 2, '2025-11-27 12:37:41', '2025-11-27 12:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `clan_members`
--

CREATE TABLE `clan_members` (
  `id` int NOT NULL,
  `clan_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('leader','co_leader','member') DEFAULT 'member',
  `joined_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clan_members`
--

INSERT INTO `clan_members` (`id`, `clan_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 1, 2, 'leader', '2025-11-27 12:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
(2, 'Alh Abubakar', '09012345678', 'habu@test.com', '106 Jefferson St,Weehawken, New Jersey(NJ), 07086', 1876203.75, '2025-12-04 11:19:39', '2025-12-05 20:34:42', 1, 0),
(12, 'mal Habu', '08145919457', 'malhabu@gmail.com', 'jakara', 0.00, '2025-12-09 11:56:22', NULL, 1, 0),
(13, 'Abdulkarim Abdulmutallib', '098765438', 'ak@gmail.com', 'Maahad Link', 0.00, '2025-12-09 12:49:26', NULL, 1, 0),
(18, 'hassan', '08145919419', 'hssan775@gmail.com', 'Maahad Link', 0.00, '2025-12-09 14:31:17', NULL, 1, 0),
(19, 'hassan', '08145919419', 'hssan775@gmail.com', 'Maahad Link', 0.00, '2025-12-09 14:31:25', NULL, 1, 0),
(20, 'Abdurrahman', '0814594345', 'abdurrahmanssan775@gmail.com', 'Maahad Link', 0.00, '2025-12-09 14:40:44', NULL, 1, 0),
(21, 'yahaya', '09876578', 'yy@gmail.com', 'wertyukl,mnbvcfg', 0.00, '2025-12-13 13:06:31', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `discount_rules`
--

CREATE TABLE `discount_rules` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_purchase_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `cart_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `join_requests`
--

CREATE TABLE `join_requests` (
  `id` int NOT NULL,
  `clan_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('pending','accepted','rejected','cancelled') DEFAULT 'pending',
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `join_requests`
--

INSERT INTO `join_requests` (`id`, `clan_id`, `user_id`, `status`, `requested_at`, `updated_at`) VALUES
(1, 1, 3, 'rejected', '2025-11-27 12:49:53', '2025-11-28 13:03:20'),
(2, 1, 1, 'rejected', '2025-11-27 18:03:03', '2025-11-28 13:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempt_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `attempt_time`, `success`) VALUES
(37, 'support', '::1', '2025-12-15 17:58:32', 1),
(38, 'abdurrahman775', '::1', '2025-12-16 10:55:56', 1),
(39, 'support', '::1', '2025-12-16 10:56:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error','payment') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `related_id` int DEFAULT NULL,
  `related_type` enum('task','project','payment','system') DEFAULT 'system',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `related_id`, `related_type`, `created_at`, `read_at`) VALUES
(1, 2, 'New Task Assignment', 'You have been assigned to E-commerce Dashboard', 'info', 1, 1, 'task', '2025-11-18 15:38:07', NULL),
(2, 3, 'New Task Assignment', 'You have been assigned to E-commerce Dashboard', 'info', 1, 1, 'task', '2025-11-18 15:38:07', NULL),
(3, 2, 'Payment Received', 'Your share payment of $1200.00 has been processed', 'success', 1, 1, 'payment', '2025-11-18 15:38:07', NULL),
(4, 3, 'Payment Received', 'Your share payment of $1200.00 has been processed', 'success', 1, 1, 'payment', '2025-11-18 15:38:07', NULL),
(5, 2, 'Task Deadline Approaching', 'Payment Integration task is due in 3 days', 'warning', 1, 3, 'task', '2025-11-18 15:38:07', '2025-11-28 15:59:21'),
(6, 8, 'New Task Assignment', 'You have been assigned to Payment Integration', 'info', 0, 3, 'task', '2025-11-18 15:38:07', NULL),
(7, 1, 'Task Started', 'Frontend Developer started working on task: patoosh AI', 'info', 1, 11, 'task', '2025-11-28 15:23:05', NULL),
(8, 1, 'Task Started', 'Frontend Developer started working on task: Samira Tea', 'info', 1, 12, 'task', '2025-11-28 15:24:17', NULL),
(9, 1, 'Task Completed', 'Task \'Samira Tea\' has been completed by all assignees', 'success', 1, 12, 'task', '2025-11-28 15:24:31', NULL),
(10, 1, 'Task Started', 'Frontend Developer started working on task: test task', 'info', 1, 13, 'task', '2025-11-28 15:40:13', NULL),
(11, 1, 'Task Completed', 'Task \'test task\' has been completed by all assignees', 'success', 1, 13, 'task', '2025-11-28 15:40:26', NULL),
(12, 2, 'New Task Assigned', 'You have been assigned to task \'test task\'', 'info', 1, 14, 'task', '2025-11-28 16:08:51', '2025-11-28 16:14:32'),
(13, 2, 'New Task Assigned', 'You have been assigned to task \'test task\'', 'info', 1, 15, 'task', '2025-11-28 16:13:42', '2025-11-28 16:14:32'),
(14, 1, 'Task Started', 'Frontend Developer started working on task: test task', 'info', 0, 15, 'task', '2025-11-28 17:23:41', NULL),
(15, 1, 'Task Completed', 'Task \'test task\' has been completed by all assignees', 'success', 0, 15, 'task', '2025-11-28 17:24:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `task_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('bank_transfer','paypal','stripe','cash') DEFAULT 'bank_transfer',
  `status` enum('pending','processing','completed','failed','cancelled') DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `task_id`, `amount`, `payment_method`, `status`, `payment_date`, `reference_number`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1200.00, 'bank_transfer', 'completed', '2023-10-15', 'PAY-001234', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(2, 3, 1, 1200.00, 'bank_transfer', 'completed', '2023-10-15', 'PAY-001235', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(3, 4, 1, 1200.00, 'bank_transfer', 'completed', '2023-10-15', 'PAY-001236', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(4, 5, 1, 250.00, 'bank_transfer', 'completed', '2023-10-15', 'PAY-001237', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(5, 2, 2, 768.00, 'bank_transfer', 'completed', '2023-10-20', 'PAY-001238', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(6, 8, 7, 725.00, 'paypal', 'completed', '2023-10-10', 'PAY-001239', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07');

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
(2, NULL, 'Zoom', 'fsdhfhhhf', 1, 1, '8901057335522', 46.65, 88.00, 20, 10, 0, 'support', '2025-12-09 15:37:44', 'support', '2023-07-30 11:06:53', 1, NULL),
(3, NULL, 'Zoom1', 'fjsjjfjjff', 1, 1, '1234567890', 15.00, 20.00, 19, 10, 0, 'support', '2025-12-08 18:54:13', 'support', '2023-07-30 11:06:54', 1, NULL),
(4, NULL, 'Zuma2', 'sffsjfjd', 1, 1, '80', 23.58, 23.58, 35, 10, 0, 'support', '2025-12-08 18:54:13', 'support', '2023-07-30 11:06:55', 1, NULL),
(5, NULL, 'Zuma5uu1', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 37, 10, 0, 'support', '2025-12-08 18:54:13', 'support', '2023-07-30 11:06:56', 1, NULL),
(6, NULL, 'Zuma5uu2', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 35, 10, 0, 'support', '2025-11-26 12:28:11', 'support', '2023-07-30 11:06:57', 1, NULL),
(7, NULL, 'Zuma5uu3', 'dggdhhshs', 1, 1, '80', 23.58, 23.58, 42, 10, 0, 'support', '2025-04-12 19:50:45', 'support', '2023-07-30 11:06:58', 0, NULL),
(8, NULL, 'Zuma5uu4', 'dggdhhshs', 1, 1, '80', 23.58, 44.00, 30, 50, 0, 'support', '2025-12-05 14:42:02', 'support', '2023-07-30 11:06:59', 1, NULL),
(35002, NULL, 'Lenovo T460', 'Lenovo laptop', 3, 1, '0987654321', 95000.00, 100000.00, 71, 10, 0, 'support', '2025-12-15 17:03:06', 'support', '2025-12-05 10:18:47', 1, NULL),
(35003, NULL, 'Iphone XR', 'iphone bramd', 3, 1, '11223344', 80000.00, 95000.00, 6, 5, 0, NULL, '2025-12-13 12:19:33', 'support', '2025-12-07 03:07:38', 1, NULL),
(35004, NULL, 'Power Bank', '20000mAh', 3, 1, '123456777', 8700.00, 10000.00, 180, 10, 0, NULL, '2025-12-13 12:28:30', 'support', '2025-12-08 01:38:41', 1, NULL),
(35005, NULL, 'Wireless mouse', 'wireless mouse with free mouse pad', 3, 2, '76767648', 4300.00, 4500.00, 89, 20, 0, 'support', '2025-12-11 23:38:33', 'support', '2025-12-09 11:21:50', 1, NULL);

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
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `client_name` varchar(100) DEFAULT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `status` enum('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
  `total_budget` decimal(12,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `client_name`, `client_email`, `status`, `total_budget`, `start_date`, `end_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'E-commerce Platform', 'Full-stack e-commerce solution with React frontend and Node.js backend', 'ShopEasy Inc', 'contact@shopeasy.com', 'active', 25000.00, '2023-09-01', '2023-12-15', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(2, 'Mobile Banking App', 'Secure mobile banking application with biometric authentication', 'SecureBank', 'projects@securebank.com', 'active', 35000.00, '2023-10-01', '2024-02-28', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(3, 'CRM System', 'Customer relationship management system with analytics dashboard', 'BizGrowth LLC', 'info@bizgrowth.com', 'completed', 18000.00, '2023-07-01', '2023-10-30', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(4, 'Healthcare Portal', 'Patient management portal for healthcare providers', 'MediCare Group', 'tech@medicare.com', 'on_hold', 22000.00, '2023-11-01', '2024-01-31', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07');

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
  `refund_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `authorized_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(2, '080425183100', 'Customer', 'CASH', NULL, 140.97, 0.00, 140.97, 150.00, 9.03, 5, NULL, '2025-07-13 12:17:47', 'support', '2025-04-08 06:31:00'),
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
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `category` enum('company','workers') NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `is_editable` tinyint(1) DEFAULT '1',
  `color` varchar(7) DEFAULT '#3B82F6',
  `description` text,
  `case_type` enum('with_design','without_design','both') DEFAULT 'both',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `category`, `field_name`, `display_name`, `percentage`, `is_editable`, `color`, `description`, `case_type`, `created_at`, `updated_at`) VALUES
(1, 'company', 'company', 'Company Share', 5.00, 1, '#3B82F6', 'Percentage allocated to company reinvestment', 'both', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(2, 'company', 'work_finder', 'Work Finder', 3.00, 1, '#10B981', 'Percentage for the person who found the work', 'both', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(3, 'company', 'non_participant', 'Non-Participant', 5.00, 1, '#8B5CF6', 'Percentage for non-participating members', 'both', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(4, 'company', 'ceo', 'CEO Share', 5.00, 1, '#F59E0B', 'Percentage for CEO compensation', 'both', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(5, 'company', 'tools_others', 'Tools & Others', 5.00, 1, '#EF4444', 'Percentage for tools and operational costs', 'both', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(6, 'company', 'workers', 'Workers Total', 77.00, 1, '#06B6D4', 'Total percentage distributed among workers', 'both', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(7, 'workers', 'design', 'Design Team', 5.00, 1, '#8B5CF6', 'Percentage for design role in projects with design', 'with_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(8, 'workers', 'frontend', 'Frontend Team', 24.00, 1, '#3B82F6', 'Percentage for frontend development', 'with_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(9, 'workers', 'backend', 'Backend Team', 24.00, 1, '#10B981', 'Percentage for backend development', 'with_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(10, 'workers', 'database', 'Database Team', 24.00, 1, '#F59E0B', 'Percentage for database development', 'with_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(11, 'workers', 'frontend_no_design', 'Frontend Team (No Design)', 25.00, 1, '#3B82F6', 'Percentage for frontend in projects without design', 'without_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(12, 'workers', 'backend_no_design', 'Backend Team (No Design)', 25.00, 1, '#10B981', 'Percentage for backend in projects without design', 'without_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07'),
(13, 'workers', 'database_no_design', 'Database Team (No Design)', 25.00, 1, '#F59E0B', 'Percentage for database in projects without design', 'without_design', '2025-11-18 15:38:07', '2025-11-18 15:38:07');

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` varchar(50) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `share_type` enum('company','work_finder','ceo','tools','non_participant','worker') NOT NULL,
  `status` enum('pending','calculated','paid','cancelled') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shares`
--

INSERT INTO `shares` (`id`, `task_id`, `user_id`, `role`, `percentage`, `amount`, `share_type`, `status`, `paid_at`, `created_at`) VALUES
(1, 1, 1, 'admin', 5.00, 250.00, 'company', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(2, 1, 7, 'work_finder', 3.00, 150.00, 'work_finder', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(3, 1, 6, 'ceo', 5.00, 250.00, 'ceo', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(4, 1, 1, 'tools', 5.00, 250.00, 'tools', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(5, 1, 5, 'design', 5.00, 250.00, 'worker', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(6, 1, 2, 'frontend', 24.00, 1200.00, 'worker', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(7, 1, 3, 'backend', 24.00, 1200.00, 'worker', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(8, 1, 4, 'database', 24.00, 1200.00, 'worker', 'paid', '2023-10-14 23:00:00', '2025-11-18 15:38:07'),
(9, 2, 1, 'admin', 5.00, 160.00, 'company', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(10, 2, 7, 'work_finder', 3.00, 96.00, 'work_finder', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(11, 2, 6, 'ceo', 5.00, 160.00, 'ceo', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(12, 2, 1, 'tools', 5.00, 160.00, 'tools', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(13, 2, 5, 'design', 5.00, 160.00, 'worker', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(14, 2, 2, 'frontend', 24.00, 768.00, 'worker', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(15, 2, 3, 'backend', 24.00, 768.00, 'worker', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(16, 2, 4, 'database', 24.00, 768.00, 'worker', 'paid', '2023-10-19 23:00:00', '2025-11-18 15:38:07'),
(17, 7, 1, 'admin', 5.00, 145.00, 'company', 'paid', '2023-10-09 23:00:00', '2025-11-18 15:38:07'),
(18, 7, 7, 'work_finder', 3.00, 87.00, 'work_finder', 'paid', '2023-10-09 23:00:00', '2025-11-18 15:38:07'),
(19, 7, 6, 'ceo', 5.00, 145.00, 'ceo', 'paid', '2023-10-09 23:00:00', '2025-11-18 15:38:07'),
(20, 7, 1, 'tools', 5.00, 145.00, 'tools', 'paid', '2023-10-09 23:00:00', '2025-11-18 15:38:07'),
(21, 7, 8, 'frontend', 25.00, 725.00, 'worker', 'paid', '2023-10-09 23:00:00', '2025-11-18 15:38:07'),
(22, 7, 9, 'backend', 25.00, 725.00, 'worker', 'paid', '2023-10-09 23:00:00', '2025-11-18 15:38:07'),
(23, 13, 1, 'admin', 5.00, 5025.00, 'company', 'calculated', NULL, '2025-11-28 15:40:26'),
(24, 13, 6, 'ceo', 5.00, 5025.00, 'ceo', 'calculated', NULL, '2025-11-28 15:40:26'),
(25, 13, 7, 'work_finder', 3.00, 3015.00, 'work_finder', 'calculated', NULL, '2025-11-28 15:40:26'),
(26, 13, 1, 'tools', 5.00, 5025.00, 'tools', 'calculated', NULL, '2025-11-28 15:40:26'),
(27, 13, 1, 'non_participant', 5.00, 5025.00, 'non_participant', 'calculated', NULL, '2025-11-28 15:40:26'),
(28, 13, 2, 'frontend', 25.00, 25125.00, 'worker', 'calculated', NULL, '2025-11-28 15:40:26'),
(35, 15, 1, 'admin', 5.00, 5025.00, 'company', 'calculated', NULL, '2025-11-28 17:24:50'),
(36, 15, 6, 'ceo', 5.00, 5025.00, 'ceo', 'calculated', NULL, '2025-11-28 17:24:50'),
(37, 15, 7, 'work_finder', 3.00, 3015.00, 'work_finder', 'calculated', NULL, '2025-11-28 17:24:50'),
(38, 15, 1, 'tools', 5.00, 5025.00, 'tools', 'calculated', NULL, '2025-11-28 17:24:50'),
(39, 15, 1, 'non_participant', 5.00, 5025.00, 'non_participant', 'calculated', NULL, '2025-11-28 17:24:50'),
(40, 15, 2, 'frontend', 77.00, 77385.00, 'worker', 'calculated', NULL, '2025-11-28 17:24:50');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `movement_type` enum('sale','purchase','adjustment','return') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(2, 'siit partners', 'suppliers', '0987654321', 'test@test.com', 'maitangaran house', 'babba', '2025-12-04 03:18:17', '', '2025-12-03 23:16:51'),
(3, 'babba', 'Abdurrahman Alhassan', '08145919419', 'abdurrahmanalhssan775@gmail.com', 'Maahad Link', 'support', '2025-12-13 12:45:39', 'support', '2025-12-13 12:45:39');

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
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','number','boolean','json') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'store_name', 'S & I IT partners LTD', 'text', 'Store name', 'support', '2025-12-11 23:47:30'),
(2, 'store_address', 'No. 42, Flat 9, Mai Tangaran House, Zoo Road,kano state.', 'text', 'Store address', 'support', '2025-12-11 23:47:30'),
(3, 'store_phone', '+234 812 499 0409', 'text', 'Store phone number', 'support', '2025-12-11 23:47:30'),
(4, 'store_email', 'info@siitpartners.com', 'text', 'Store email', 'support', '2025-12-11 23:47:30'),
(5, 'tax_rate', '0', 'number', 'Default tax rate percentage', 'support', '2025-12-11 23:47:30'),
(6, 'currency_symbol', '₦', 'text', 'Currency symbol', 'support', '2025-12-11 23:47:30'),
(7, 'currency_code', 'NGN', 'text', 'Currency code', 'support', '2025-12-11 23:47:30'),
(8, 'receipt_footer', 'Thank you for your patronage.', 'text', 'Receipt footer message', 'support', '2025-12-11 23:47:30'),
(9, 'session_timeout', '30', 'number', 'Session timeout in minutes', 'support', '2025-12-11 23:47:30'),
(10, 'low_stock_threshold', '10', 'number', 'Default low stock alert threshold', 'support', '2025-12-11 23:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `project_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `total_amount` decimal(10,2) NOT NULL,
  `case_type` enum('with_design','without_design') NOT NULL,
  `status` enum('pending','in_progress','review','completed','cancelled') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `assigned_to_clan_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `name`, `description`, `total_amount`, `case_type`, `status`, `priority`, `due_date`, `completed_at`, `created_by`, `created_at`, `updated_at`, `assigned_to_clan_id`) VALUES
(1, 1, 'E-commerce Dashboard', 'Admin dashboard for product and order management', 5000.00, 'with_design', 'completed', 'high', '2023-10-15', '2023-10-13 23:00:00', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(2, 1, 'Product Catalog', 'Product listing and search functionality', 3200.00, 'with_design', 'completed', 'medium', '2023-10-20', '2023-10-17 23:00:00', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(3, 1, 'Payment Integration', 'Stripe and PayPal payment gateway integration', 2800.00, 'without_design', 'in_progress', 'high', '2023-11-30', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(4, 2, 'Mobile App UI', 'Banking app user interface design and implementation', 4500.00, 'with_design', 'in_progress', 'high', '2023-11-20', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(5, 2, 'Security Module', 'Biometric authentication and security features', 6200.00, 'without_design', 'pending', 'urgent', '2023-12-10', NULL, 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(6, 3, 'CRM Dashboard', 'Analytics and reporting dashboard', 3800.00, 'with_design', 'completed', 'medium', '2023-09-30', '2023-09-27 23:00:00', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(7, 3, 'Customer Management', 'Customer profile and interaction tracking', 2900.00, 'without_design', 'completed', 'medium', '2023-10-10', '2023-10-07 23:00:00', 1, '2025-11-18 15:38:07', '2025-11-18 15:38:07', NULL),
(8, 2, 'patoosh AI', 'create an AI for Patoosh restuarent', 500000.00, 'with_design', 'pending', 'urgent', '2025-11-29', NULL, 1, '2025-11-27 16:05:37', '2025-11-27 16:05:37', NULL),
(9, 2, 'patoosh AI', 'create an AI for Patoosh restuarent', 500000.00, 'with_design', 'pending', 'urgent', '2025-11-29', NULL, 1, '2025-11-27 16:10:26', '2025-11-27 16:10:26', NULL),
(10, NULL, 'Test Task Manual', 'Test Description', 100.00, 'without_design', 'pending', 'low', NULL, NULL, 1, '2025-11-27 16:12:37', '2025-11-27 16:12:37', NULL),
(11, 2, 'patoosh AI', 'create an AI for Patoosh restuarent', 500000.00, 'without_design', 'in_progress', 'urgent', '2025-11-28', NULL, 1, '2025-11-27 16:15:03', '2025-11-28 15:23:05', NULL),
(12, 1, 'Samira Tea', 'build an e-commerce for samira tea', 10000.00, 'without_design', 'completed', 'high', '2025-11-29', '2025-11-28 15:24:31', 1, '2025-11-28 15:09:07', '2025-11-28 15:24:31', NULL),
(13, 1, 'test task', 'hellllo', 100500.00, 'without_design', 'completed', 'medium', '2025-12-01', '2025-11-28 15:40:26', 1, '2025-11-28 15:39:44', '2025-11-28 15:40:26', NULL),
(14, 1, 'test task', 'hellllo', 100500.00, 'without_design', 'pending', 'medium', '2025-12-01', NULL, 1, '2025-11-28 16:08:51', '2025-11-28 16:08:51', NULL),
(15, 1, 'test task', 'hellllo', 100500.00, 'without_design', 'completed', 'medium', '2025-12-01', '2025-11-28 17:24:50', 1, '2025-11-28 16:13:42', '2025-11-28 17:24:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_assignments`
--

CREATE TABLE `task_assignments` (
  `id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('frontend','backend','database','design') NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `hours_spent` decimal(5,2) DEFAULT '0.00',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `task_assignments`
--

INSERT INTO `task_assignments` (`id`, `task_id`, `user_id`, `role`, `assigned_at`, `completed_at`, `hours_spent`, `notes`) VALUES
(1, 1, 2, 'frontend', '2023-09-01 00:00:00', '2023-10-13 23:00:00', 24.50, 'Completed all frontend components'),
(2, 1, 3, 'backend', '2023-09-01 00:00:00', '2023-10-13 23:00:00', 18.00, 'API development and integration'),
(3, 1, 4, 'database', '2023-09-01 00:00:00', '2023-10-13 23:00:00', 12.50, 'Database schema and queries'),
(4, 1, 5, 'design', '2023-09-01 00:00:00', '2023-10-13 23:00:00', 16.00, 'UI/UX design and prototyping'),
(5, 2, 2, 'frontend', '2023-09-15 00:00:00', '2023-10-17 23:00:00', 20.00, 'Product listing and search features'),
(6, 2, 3, 'backend', '2023-09-15 00:00:00', '2023-10-17 23:00:00', 15.50, 'Product API endpoints'),
(7, 2, 4, 'database', '2023-09-15 00:00:00', '2023-10-17 23:00:00', 10.00, 'Product database optimization'),
(8, 2, 5, 'design', '2023-09-15 00:00:00', '2023-10-17 23:00:00', 12.00, 'Product page designs'),
(9, 3, 8, 'frontend', '2023-10-01 00:00:00', NULL, 8.00, 'Payment form components'),
(10, 3, 9, 'backend', '2023-10-01 00:00:00', NULL, 22.00, 'Payment gateway integration'),
(11, 4, 2, 'frontend', '2023-10-01 00:00:00', '2025-11-28 15:23:56', 15.00, 'React Native components'),
(12, 4, 5, 'design', '2023-10-01 00:00:00', NULL, 25.00, 'Mobile app design system'),
(13, 5, 9, 'backend', '2023-10-15 00:00:00', NULL, 0.00, 'Not started yet'),
(14, 5, 4, 'database', '2023-10-15 00:00:00', NULL, 0.00, 'Not started yet'),
(15, 6, 2, 'frontend', '2023-07-01 00:00:00', '2023-09-27 23:00:00', 22.00, 'Dashboard components and charts'),
(16, 6, 3, 'backend', '2023-07-01 00:00:00', '2023-09-27 23:00:00', 16.50, 'Analytics API'),
(17, 6, 5, 'design', '2023-07-01 00:00:00', '2023-09-27 23:00:00', 14.00, 'Dashboard design'),
(18, 7, 8, 'frontend', '2023-07-15 00:00:00', '2023-10-07 23:00:00', 18.50, 'Customer profile interface'),
(19, 7, 9, 'backend', '2023-07-15 00:00:00', '2023-10-07 23:00:00', 20.00, 'Customer management API'),
(20, 8, 10, 'frontend', '2025-11-27 16:05:37', NULL, 0.00, NULL),
(21, 8, 4, 'database', '2025-11-27 16:05:37', NULL, 0.00, NULL),
(22, 8, 9, 'backend', '2025-11-27 16:05:37', NULL, 0.00, NULL),
(23, 8, 8, 'frontend', '2025-11-27 16:05:37', NULL, 0.00, NULL),
(24, 8, 5, 'design', '2025-11-27 16:05:37', NULL, 0.00, NULL),
(25, 9, 10, 'frontend', '2025-11-27 16:10:26', NULL, 0.00, NULL),
(26, 9, 4, 'database', '2025-11-27 16:10:26', NULL, 0.00, NULL),
(27, 9, 9, 'backend', '2025-11-27 16:10:26', NULL, 0.00, NULL),
(28, 9, 5, 'design', '2025-11-27 16:10:26', NULL, 0.00, NULL),
(29, 11, 10, 'frontend', '2025-11-27 16:15:03', NULL, 0.00, NULL),
(30, 11, 3, 'backend', '2025-11-27 16:15:03', NULL, 0.00, NULL),
(31, 11, 4, 'database', '2025-11-27 16:15:03', NULL, 0.00, NULL),
(32, 11, 2, 'frontend', '2025-11-27 16:15:03', '2025-11-28 15:10:12', 0.00, NULL),
(36, 12, 2, 'frontend', '2025-11-28 15:17:52', '2025-11-28 15:24:31', 0.00, NULL),
(37, 13, 2, 'frontend', '2025-11-28 15:39:44', '2025-11-28 15:40:26', 0.00, NULL),
(38, 14, 2, 'frontend', '2025-11-28 16:08:51', NULL, 0.00, NULL),
(39, 15, 2, 'frontend', '2025-11-28 16:13:42', '2025-11-28 17:24:50', 0.00, NULL);

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
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `change_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('completed','void','refunded','held') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(6789033, '2025-12-09 13:35:45', 1, 12, 209550.00, 0.00, 0.00, 209550.00, 'POS', 0.00, 0.00, 'completed', 'Token: txn_693817a16ea9b4.06836184', 'support', '2025-12-09 13:35:45'),
(6789034, '2025-12-09 18:38:23', 1, NULL, 95000.00, 0.00, 0.00, 95000.00, 'CASH', 95000.00, 0.00, 'completed', 'Token: txn_69385e8feab1c4.59568364 | Customer: Walk-in', 'support', '2025-12-09 18:38:23'),
(6789035, '2025-12-09 18:43:19', 1, NULL, 95000.00, 0.00, 0.00, 95000.00, 'CASH', 95000.00, 0.00, 'completed', 'Token: txn_69385fb7a57da1.03735294 | Customer: Walk-in', 'support', '2025-12-09 18:43:19'),
(6789036, '2025-12-11 18:22:22', 4, NULL, 10000.00, 0.00, 0.00, 10000.00, 'CASH', 10000.00, 0.00, 'completed', 'Token: txn_693afdce868073.07584911 | Customer: Walk-in', 'cashier', '2025-12-11 18:22:22'),
(6789037, '2025-12-11 18:23:21', 5, NULL, 95000.00, 0.00, 0.00, 95000.00, 'CASH', 95000.00, 0.00, 'completed', 'Token: txn_693afe09da9e96.30997424 | Customer: Walk-in', 'abdurrahman775', '2025-12-11 18:23:21'),
(6789038, '2025-12-12 13:55:06', 5, NULL, 95000.00, 0.00, 0.00, 95000.00, 'CASH', 95000.00, 0.00, 'completed', 'Token: txn_693c10aa979c89.38256791 | Customer: Walk-in', 'abdurrahman775', '2025-12-12 13:55:06'),
(6789039, '2025-12-12 13:55:16', 4, NULL, 50000.00, 0.00, 0.00, 50000.00, 'CASH', 50000.00, 0.00, 'completed', 'Token: txn_693c10b411d856.75036565 | Customer: Walk-in', 'cashier', '2025-12-12 13:55:16'),
(6789040, '2025-12-13 11:53:43', 1, NULL, 100000.00, 0.00, 0.00, 100000.00, 'MIXED', 0.00, 0.00, 'completed', 'Token: txn_693d45b78c9640.42760784 | Customer: Walk-in', 'support', '2025-12-13 11:53:43'),
(6789041, '2025-12-13 11:55:23', 1, NULL, 95000.00, 0.00, 0.00, 95000.00, 'MIXED', 0.00, 0.00, 'completed', 'Token: txn_693d461b3b8f13.33066741 | Customer: Walk-in', 'support', '2025-12-13 11:55:23'),
(6789042, '2025-12-13 12:19:33', 1, NULL, 95000.00, 0.00, 0.00, 95000.00, 'MIXED', 8000.00, 0.00, 'completed', 'Token: txn_693d4bc5b75198.25953041 | Customer: Test Mixed Payment | POS Ref: TEST001 | POS Amount: 5,000.00 | Cash Amount: 3,000.00', 'support', '2025-12-13 12:19:33'),
(6789043, '2025-12-13 12:26:32', 1, NULL, 20000.00, 0.00, 0.00, 20000.00, 'MIXED', 20000.00, 0.00, 'completed', 'Token: txn_693d4d689729d4.23164704 | Customer: Walk-in | POS Ref: 234 | POS Amount: 18,000.00 | Cash Amount: 2,000.00', 'support', '2025-12-13 12:26:32'),
(6789044, '2025-12-13 12:27:39', 1, NULL, 100000.00, 0.00, 0.00, 100000.00, 'MIXED', 100000.00, 0.00, 'completed', 'Token: txn_693d4dab470c54.60436828 | Customer: Walk-in | POS Ref: 23456 | POS Amount: 95,000.00 | Cash Amount: 5,000.00', 'support', '2025-12-13 12:27:39'),
(6789045, '2025-12-13 12:28:30', 1, NULL, 10000.00, 0.00, 0.00, 10000.00, 'CASH', 10000.00, 0.00, 'completed', 'Token: txn_693d4dde3cea36.38545782 | Customer: Walk-in', 'support', '2025-12-13 12:28:30'),
(6789046, '2025-12-15 18:03:06', 1, NULL, 100000.00, 0.00, 0.00, 100000.00, 'MIXED', 100000.00, 0.00, 'completed', 'Token: txn_69403f4a5b9bd4.54293094 | Customer: eefefefe | POS Ref: 5005555 | POS Amount: 50,000.00 | Cash Amount: 50,000.00', 'support', '2025-12-15 18:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `item_id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(77, 6789033, 1, 'Ayaba', 1, 50.00, 0.00, 50.00),
(78, 6789034, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(79, 6789035, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(80, 6789036, 35004, 'Power Bank', 1, 10000.00, 0.00, 10000.00),
(81, 6789037, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(82, 6789038, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(83, 6789039, 35004, 'Power Bank', 5, 10000.00, 0.00, 50000.00),
(84, 6789040, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(85, 6789041, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(86, 6789042, 35003, 'Iphone XR', 1, 95000.00, 0.00, 95000.00),
(87, 6789043, 35004, 'Power Bank', 2, 10000.00, 0.00, 20000.00),
(88, 6789044, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00),
(89, 6789045, 35004, 'Power Bank', 1, 10000.00, 0.00, 10000.00),
(90, 6789046, 35002, 'Lenovo T460', 1, 100000.00, 0.00, 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','frontend','backend','database','design','ceo','work_finder','non_participant','tools') NOT NULL,
  `avatar_initials` varchar(2) DEFAULT NULL,
  `color_scheme` varchar(7) DEFAULT '#3B82F6',
  `is_active` tinyint(1) DEFAULT '1',
  `hourly_rate` decimal(10,2) DEFAULT '0.00',
  `join_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `avatar_initials`, `color_scheme`, `is_active`, `hourly_rate`, `join_date`, `created_at`, `updated_at`, `avatar`) VALUES
(1, 'Admin User', 'admin@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'admin', 'AU', '#EF4444', 1, 0.00, '2023-01-01', '2025-11-18 15:38:07', '2025-11-28 10:50:04', 'uploads/avatars/avatar_1_1764323404.jpg'),
(2, 'Frontend Developer', 'frontend@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'frontend', 'FD', '#3B82F6', 1, 45.00, '2023-01-15', '2025-11-18 15:38:07', '2025-11-28 14:41:49', 'uploads/avatars/avatar_2_1764337309.png'),
(3, 'Backend Developer', 'backend@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'backend', 'BD', '#10B981', 0, 50.00, '2023-02-20', '2025-11-18 15:38:07', '2025-11-28 17:37:56', NULL),
(4, 'Database Developer', 'database@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'database', 'DD', '#F59E0B', 1, 48.00, '2023-03-10', '2025-11-18 15:38:07', '2025-11-18 16:22:07', NULL),
(5, 'UI/UX Designer', 'design@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'design', 'UD', '#8B5CF6', 1, 42.00, '2023-04-05', '2025-11-18 15:38:07', '2025-11-18 16:22:11', NULL),
(6, 'CEO', 'ceo@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'ceo', 'CE', '#F59E0B', 1, 0.00, '2023-01-01', '2025-11-18 15:38:07', '2025-11-18 16:22:15', NULL),
(7, 'Work Finder', 'finder@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'work_finder', 'WF', '#10B981', 1, 0.00, '2023-01-01', '2025-11-18 15:38:07', '2025-11-18 16:22:19', NULL),
(8, 'Senior Frontend', 'senior.frontend@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'frontend', 'SF', '#3B82F6', 1, 55.00, '2023-05-15', '2025-11-18 15:38:07', '2025-11-27 17:53:57', NULL),
(9, 'Senior Backend', 'senior.backend@lunobyte.com', '$2y$10$et.jlLFy9QsDvRizhFXth.oZPFuTEd7YvDrVbumCNsjv6zGKLnefK', 'backend', 'SB', '#10B981', 1, 60.00, '2023-06-20', '2025-11-18 15:38:07', '2025-11-18 16:22:28', NULL),
(10, 'Abdurrahman Alhassan', 'abdurrahmanalhssan775@gmail.com', '$2y$10$xPlIstXc0NJ3btKqT1Bz1u.7S7sqhFhBgopGHFRzCli3m28r696Z.', 'frontend', 'AA', '#3B82F6', 1, 0.00, '2025-11-18', '2025-11-18 17:00:37', '2025-11-18 17:01:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `idx_activity_logs_user_id` (`user_id`),
  ADD KEY `idx_activity_logs_created_at` (`created_at`);

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
-- Indexes for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `reg_by` (`reg_by`);

--
-- Indexes for table `clans`
--
ALTER TABLE `clans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `clan_members`
--
ALTER TABLE `clan_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_clan_member` (`clan_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `join_requests`
--
ALTER TABLE `join_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clan_id` (`clan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_ip` (`ip_address`),
  ADD KEY `idx_time` (`attempt_time`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_id` (`user_id`),
  ADD KEY `idx_notifications_is_read` (`is_read`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `created_by` (`created_by`);

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
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shares_user_id` (`user_id`),
  ADD KEY `idx_shares_task_id` (`task_id`),
  ADD KEY `idx_shares_status` (`status`);

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
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_tasks_status` (`status`),
  ADD KEY `idx_tasks_due_date` (`due_date`),
  ADD KEY `tasks_ibfk_3` (`assigned_to_clan_id`);

--
-- Indexes for table `task_assignments`
--
ALTER TABLE `task_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_task_user` (`task_id`,`user_id`),
  ADD KEY `idx_task_assignments_user_id` (`user_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clans`
--
ALTER TABLE `clans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clan_members`
--
ALTER TABLE `clan_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- AUTO_INCREMENT for table `join_requests`
--
ALTER TABLE `join_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `task_assignments`
--
ALTER TABLE `task_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6789047;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `activity_logs_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD CONSTRAINT `backup_logs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `admins` (`username`),
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`reg_by`) REFERENCES `admins` (`username`);

--
-- Constraints for table `clans`
--
ALTER TABLE `clans`
  ADD CONSTRAINT `clans_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `clan_members`
--
ALTER TABLE `clan_members`
  ADD CONSTRAINT `clan_members_ibfk_1` FOREIGN KEY (`clan_id`) REFERENCES `clans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clan_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `join_requests`
--
ALTER TABLE `join_requests`
  ADD CONSTRAINT `join_requests_ibfk_1` FOREIGN KEY (`clan_id`) REFERENCES `clans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `join_requests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `shares_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`assigned_to_clan_id`) REFERENCES `clans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_assignments`
--
ALTER TABLE `task_assignments`
  ADD CONSTRAINT `task_assignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `task_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
