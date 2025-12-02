-- POS System Database Updates (MySQL Compatible Version)
-- This file contains schema updates to align with POS requirements

-- ========================================
-- 1. CREATE CUSTOMERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `total_purchases` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`customer_id`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 2. CREATE TRANSACTIONS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` INT NOT NULL AUTO_INCREMENT,
  `transaction_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT NOT NULL,
  `customer_id` INT DEFAULT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` VARCHAR(50) NOT NULL,
  `amount_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `change_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('completed', 'void', 'refunded', 'held') NOT NULL DEFAULT 'completed',
  `notes` TEXT DEFAULT NULL,
  `created_by` VARCHAR(32) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_customer_id` (`customer_id`),
  INDEX `idx_transaction_date` (`transaction_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 3. CREATE TRANSACTION_ITEMS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `transaction_items` (
  `item_id` INT NOT NULL AUTO_INCREMENT,
  `transaction_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `line_total` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  INDEX `idx_transaction_id` (`transaction_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 4. CREATE STOCK_MOVEMENTS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `movement_type` ENUM('sale', 'purchase', 'adjustment', 'return') NOT NULL,
  `quantity` INT NOT NULL,
  `reference_id` INT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_by` VARCHAR(32) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_movement_type` (`movement_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 5. CREATE EMPLOYEE_ATTENDANCE TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `employee_attendance` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `clock_in` DATETIME NOT NULL,
  `clock_out` DATETIME DEFAULT NULL,
  `work_hours` DECIMAL(5,2) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_clock_in` (`clock_in`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 6. CREATE SYSTEM_SETTINGS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `setting_type` ENUM('text', 'number', 'boolean', 'json') NOT NULL DEFAULT 'text',
  `description` VARCHAR(255) DEFAULT NULL,
  `updated_by` VARCHAR(32) DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 7. INSERT DEFAULT SYSTEM SETTINGS
-- ========================================
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('store_name', 'BasicPOS System', 'text', 'Store name'),
('store_address', '', 'text', 'Store address'),
('store_phone', '', 'text', 'Store phone number'),
('store_email', '', 'text', 'Store email'),
('tax_rate', '7.5', 'number', 'Default tax rate percentage'),
('currency_symbol', '₦', 'text', 'Currency symbol'),
('currency_code', 'NGN', 'text', 'Currency code'),
('receipt_footer', 'Thank you for your business!', 'text', 'Receipt footer message'),
('session_timeout', '30', 'number', 'Session timeout in minutes'),
('low_stock_threshold', '10', 'number', 'Default low stock alert threshold');

-- ========================================
-- 8. CREATE HELD_TRANSACTIONS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `held_transactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `customer_id` INT DEFAULT NULL,
  `cart_data` TEXT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 9. CREATE DISCOUNT_RULES TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `discount_rules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `discount_type` ENUM('percentage', 'fixed') NOT NULL,
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_purchase_amount` DECIMAL(10,2) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `start_date` DATE DEFAULT NULL,
  `end_date` DATE DEFAULT NULL,
  `created_by` VARCHAR(32) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 10. CREATE REFUNDS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `refunds` (
  `refund_id` INT NOT NULL AUTO_INCREMENT,
  `transaction_id` INT NOT NULL,
  `refund_amount` DECIMAL(10,2) NOT NULL,
  `refund_reason` TEXT DEFAULT NULL,
  `authorized_by` VARCHAR(32) NOT NULL,
  `processed_by` VARCHAR(32) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refund_id`),
  INDEX `idx_transaction_id` (`transaction_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 11. UPDATE PRODUCTS TABLE
-- Note: Adding columns only if they don't exist
-- ========================================

-- Add sku column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'products' 
AND COLUMN_NAME = 'sku';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE products ADD COLUMN sku VARCHAR(50) DEFAULT NULL AFTER id',
  'SELECT "Column sku already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add image_url column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'products' 
AND COLUMN_NAME = 'image_url';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE products ADD COLUMN image_url VARCHAR(255) DEFAULT NULL',
  'SELECT "Column image_url already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes if not exist
ALTER TABLE products ADD INDEX idx_barcode (barcode);
ALTER TABLE products ADD INDEX idx_category_id (category_id);
ALTER TABLE products ADD INDEX idx_is_active (is_active);

-- Rename columns (check if old column exists first)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'products' 
AND COLUMN_NAME = 'cost_price';

SET @query = IF(@col_exists > 0, 
  'ALTER TABLE products CHANGE COLUMN cost_price purchase_price DECIMAL(10,2) NOT NULL',
  'SELECT "Column cost_price does not exist or already renamed" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'products' 
AND COLUMN_NAME = 'qty_in_stock';

SET @query = IF(@col_exists > 0, 
  'ALTER TABLE products CHANGE COLUMN qty_in_stock stock_quantity INT NOT NULL DEFAULT 0',
  'SELECT "Column qty_in_stock does not exist or already renamed" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'products' 
AND COLUMN_NAME = 'low_stock_alert';

SET @query = IF(@col_exists > 0, 
  'ALTER TABLE products CHANGE COLUMN low_stock_alert min_stock_level INT NOT NULL DEFAULT 10',
  'SELECT "Column low_stock_alert does not exist or already renamed" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 12. UPDATE CATEGORIES TABLE
-- ========================================

-- Add parent_id column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'categories' 
AND COLUMN_NAME = 'parent_id';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE categories ADD COLUMN parent_id INT DEFAULT NULL AFTER id',
  'SELECT "Column parent_id already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add description column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'categories' 
AND COLUMN_NAME = 'description';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE categories ADD COLUMN description TEXT DEFAULT NULL AFTER name',
  'SELECT "Column description already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 13. UPDATE SUPPLIERS TABLE
-- ========================================

-- Add is_active column to suppliers if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'suppliers' 
AND COLUMN_NAME = 'is_active';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE suppliers ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1',
  'SELECT "Column is_active already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 14. UPDATE AUDITLOG TABLE
-- ========================================

-- Add ip_address column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'auditlog' 
AND COLUMN_NAME = 'ip_address';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE auditlog ADD COLUMN ip_address VARCHAR(45) DEFAULT NULL AFTER username',
  'SELECT "Column ip_address already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add user_agent column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'onlinepos' 
AND TABLE_NAME = 'auditlog' 
AND COLUMN_NAME = 'user_agent';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE auditlog ADD COLUMN user_agent TEXT DEFAULT NULL AFTER ip_address',
  'SELECT "Column user_agent already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- END OF DATABASE UPDATE
-- ========================================

SELECT 'Database update completed successfully!' AS Status;
