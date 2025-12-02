<?php
/**
 * POS System Utility Functions
 * Extended functions for the POS system operations
 */

/**
 * Get system setting value
 * 
 * @param PDO $dbh Database connection
 * @param string $key Setting key
 * @param mixed $default Default value if setting not found
 * @return mixed Setting value
 */
function get_setting($dbh, $key, $default = null) {
	try {
		$sql = "SELECT setting_value FROM system_settings WHERE setting_key = :key LIMIT 1";
		$query = $dbh->prepare($sql);
		$query->bindParam(':key', $key, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		
		return $result ? $result['setting_value'] : $default;
	} catch (PDOException $e) {
		return $default;
	}
}

/**
 * Update system setting
 * 
 * @param PDO $dbh Database connection
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @param string $updated_by Username of who updated the setting
 * @return bool Success status
 */
function update_setting($dbh, $key, $value, $updated_by = null) {
	try {
		$sql = "UPDATE system_settings SET setting_value = :value, updated_by = :updated_by, updated_at = NOW() 
				WHERE setting_key = :key";
		$query = $dbh->prepare($sql);
		$query->bindParam(':value', $value, PDO::PARAM_STR);
		$query->bindParam(':updated_by', $updated_by, PDO::PARAM_STR);
		$query->bindParam(':key', $key, PDO::PARAM_STR);
		return $query->execute();
	} catch (PDOException $e) {
		return false;
	}
}

/**
 * Calculate tax amount
 * 
 * @param PDO $dbh Database connection
 * @param float $amount Amount to calculate tax on
 * @return float Tax amount
 */
function calculate_tax($dbh, $amount) {
	$tax_rate = get_setting($dbh, 'tax_rate', 0);
	return round(($amount * $tax_rate) / 100, 2);
}

/**
 * Apply discount to amount
 * 
 * @param float $amount Original amount
 * @param float $discount_value Discount value
 * @param string $discount_type 'percentage' or 'fixed'
 * @return float Discounted amount
 */
function apply_discount($amount, $discount_value, $discount_type = 'percentage') {
	if ($discount_type == 'percentage') {
		$discount = ($amount * $discount_value) / 100;
	} else {
		$discount = $discount_value;
	}
	
	return round($amount - $discount, 2);
}

/**
 * Format currency
 * 
 * @param PDO $dbh Database connection
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function format_currency($dbh, $amount) {
	$symbol = get_setting($dbh, 'currency_symbol', '₦');
	return $symbol . number_format($amount, 2);
}

/**
 * Check if product has sufficient stock
 * 
 * @param PDO $dbh Database connection
 * @param int $product_id Product ID
 * @param int $quantity Quantity to check
 * @return bool True if sufficient stock available
 */
function check_stock($dbh, $product_id, $quantity) {
	try {
		$sql = "SELECT stock_quantity FROM products WHERE id = :product_id AND is_active = 1";
		$query = $dbh->prepare($sql);
		$query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		
		return $result && $result['stock_quantity'] >= $quantity;
	} catch (PDOException $e) {
		return false;
	}
}

/**
 * Update product stock
 * 
 * @param PDO $dbh Database connection
 * @param int $product_id Product ID
 * @param int $quantity Quantity to add (positive) or remove (negative)
 * @param string $movement_type Type of movement (sale, purchase, adjustment, return)
 * @param int $reference_id Reference ID (transaction_id, purchase_id, etc.)
 * @param string $created_by Username
 * @return bool Success status
 */
function update_stock($dbh, $product_id, $quantity, $movement_type, $reference_id = null, $created_by = null) {
	try {
		$dbh->beginTransaction();
		
		// Update product stock
		$sql = "UPDATE products SET stock_quantity = stock_quantity + :quantity WHERE id = :product_id";
		$query = $dbh->prepare($sql);
		$query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
		$query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
		$query->execute();
		
		// Log stock movement
		$sql = "INSERT INTO stock_movements (product_id, movement_type, quantity, reference_id, created_by, created_at) 
				VALUES (:product_id, :movement_type, :quantity, :reference_id, :created_by, NOW())";
		$query = $dbh->prepare($sql);
		$query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
		$query->bindParam(':movement_type', $movement_type, PDO::PARAM_STR);
		$query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
		$query->bindParam(':reference_id', $reference_id, PDO::PARAM_INT);
		$query->bindParam(':created_by', $created_by, PDO::PARAM_STR);
		$query->execute();
		
		$dbh->commit();
		return true;
	} catch (PDOException $e) {
		$dbh->rollBack();
		return false;
	}
}

/**
 * Get low stock products
 * 
 * @param PDO $dbh Database connection
 * @return array Array of products below minimum stock level
 */
function get_low_stock_products($dbh) {
	try {
		$sql = "SELECT p.*, c.name as category_name 
				FROM products p 
				LEFT JOIN categories c ON p.category_id = c.id 
				WHERE p.stock_quantity <= p.min_stock_level AND p.is_active = 1 
				ORDER BY p.stock_quantity ASC";
		$query = $dbh->prepare($sql);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		return [];
	}
}

/**
 * Get low stock count
 * 
 * @param PDO $dbh Database connection
 * @return int Number of products below minimum stock
 */
function get_low_stock_count($dbh) {
	try {
		$sql = "SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock_level AND is_active = 1";
		$query = $dbh->prepare($sql);
		$query->execute();
		return $query->fetchColumn();
	} catch (PDOException $e) {
		return 0;
	}
}

/**
 * Generate unique transaction ID
 * 
 * @return string Unique transaction ID
 */
function generate_transaction_id() {
	// Format: DDMMYYHHMMSS (e.g., 021224153045)
	return date('dmyHis');
}

/**
 * Get today's sales total
 * 
 * @param PDO $dbh Database connection
 * @return float Total sales for today
 */
function get_today_sales($dbh) {
	try {
		$sql = "SELECT COALESCE(SUM(total_amount), 0) as total 
				FROM transactions 
				WHERE DATE(transaction_date) = CURDATE() AND status = 'completed'";
		$query = $dbh->prepare($sql);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		return $result['total'];
	} catch (PDOException $e) {
		return 0;
	}
}

/**
 * Get today's items sold
 * 
 * @param PDO $dbh Database connection
 * @return int Number of items sold today
 */
function get_today_items_sold($dbh) {
	try {
		$sql = "SELECT COALESCE(SUM(ti.quantity), 0) as total 
				FROM transaction_items ti
				INNER JOIN transactions t ON ti.transaction_id = t.transaction_id
				WHERE DATE(t.transaction_date) = CURDATE() AND t.status = 'completed'";
		$query = $dbh->prepare($sql);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		return $result['total'];
	} catch (PDOException $e) {
		return 0;
	}
}

/**
 * Get top selling products
 * 
 * @param PDO $dbh Database connection
 * @param int $limit Number of products to return
 * @param string $period 'today', 'week', 'month'
 * @return array Top selling products
 */
function get_top_selling_products($dbh, $limit = 5, $period = 'today') {
	try {
		$date_condition = '';
		switch($period) {
			case 'today':
				$date_condition = "DATE(t.transaction_date) = CURDATE()";
				break;
			case 'week':
				$date_condition = "YEARWEEK(t.transaction_date) = YEARWEEK(NOW())";
				break;
			case 'month':
				$date_condition = "YEAR(t.transaction_date) = YEAR(NOW()) AND MONTH(t.transaction_date) = MONTH(NOW())";
				break;
			default:
				$date_condition = "DATE(t.transaction_date) = CURDATE()";
		}
		
		$sql = "SELECT p.name, p.selling_price, SUM(ti.quantity) as total_sold, SUM(ti.line_total) as revenue
				FROM transaction_items ti
				INNER JOIN transactions t ON ti.transaction_id = t.transaction_id
				INNER JOIN products p ON ti.product_id = p.id
				WHERE $date_condition AND t.status = 'completed'
				GROUP BY ti.product_id, p.name, p.selling_price
				ORDER BY total_sold DESC
				LIMIT :limit";
		
		$query = $dbh->prepare($sql);
		$query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		return [];
	}
}

/**
 * Get customer by phone number
 * 
 * @param PDO $dbh Database connection
 * @param string $phone Phone number
 * @return array|null Customer data or null if not found
 */
function get_customer_by_phone($dbh, $phone) {
	try {
		$sql = "SELECT * FROM customers WHERE phone = :phone AND is_active = 1 LIMIT 1";
		$query = $dbh->prepare($sql);
		$query->bindParam(':phone', $phone, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		return null;
	}
}
?>
