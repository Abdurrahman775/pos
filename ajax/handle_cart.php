<?php
session_start();
require("../config.php");
require("../include/functions.php");

header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch ($action) {
        case 'add':
            $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            
            if ($product_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
                exit;
            }
            
            // Check product exists and has stock
            $sql = "SELECT name, selling_price, qty_in_stock FROM products WHERE id = :id AND is_active = 1";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $product_id, PDO::PARAM_INT);
            $query->execute();
            $product = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                exit;
            }
            
            if ($product['qty_in_stock'] <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Product out of stock']);
                exit;
            }
            
            // Check if product already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    // Check if incrementing would exceed stock
                    if ($item['quantity'] + 1 > $product['qty_in_stock']) {
                        echo json_encode(['status' => 'error', 'message' => 'Insufficient stock available']);
                        exit;
                    }
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product_id,
                    'quantity' => 1
                ];
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
            break;
            
        case 'add_by_barcode':
            $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : '';
            
            if (empty($barcode)) {
                echo json_encode(['status' => 'error', 'message' => 'Barcode cannot be empty']);
                exit;
            }
            
            // Find product by barcode
            $sql = "SELECT id, name, selling_price, qty_in_stock FROM products WHERE barcode = :barcode AND is_active = 1";
            $query = $dbh->prepare($sql);
            $query->bindParam(':barcode', $barcode, PDO::PARAM_STR);
            $query->execute();
            $product = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                exit;
            }
            
            if ($product['qty_in_stock'] <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Product out of stock']);
                exit;
            }
            
            $product_id = $product['id'];
            
            // Check if product already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    if ($item['quantity'] + 1 > $product['qty_in_stock']) {
                        echo json_encode(['status' => 'error', 'message' => 'Insufficient stock available']);
                        exit;
                    }
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product_id,
                    'quantity' => 1
                ];
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
            break;
            
        case 'update':
            $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
            
            if ($quantity < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
                exit;
            }
            
            // Check stock availability
            $sql = "SELECT qty_in_stock FROM products WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $product_id, PDO::PARAM_INT);
            $query->execute();
            $product = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                exit;
            }
            
            if ($quantity > $product['qty_in_stock']) {
                echo json_encode(['status' => 'error', 'message' => 'Insufficient stock. Available: ' . $product['qty_in_stock']]);
                exit;
            }
            
            // Update cart
            foreach ($_SESSION['cart'] as $key => &$item) {
                if ($item['product_id'] == $product_id) {
                    if ($quantity == 0) {
                        unset($_SESSION['cart'][$key]);
                    } else {
                        $item['quantity'] = $quantity;
                    }
                    break;
                }
            }
            
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            echo json_encode(['status' => 'success', 'message' => 'Cart updated']);
            break;
            
        case 'delete':
            $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $product_id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['status' => 'success', 'message' => 'Cart cleared']);
            break;
            
        case 'get_cart_html':
            // Generate cart HTML
            $html = '';
            
            // Cache currency symbol for performance
            $currency = get_currency($dbh);
            
            if (empty($_SESSION['cart'])) {
                $html = '<tr><td colspan="6" class="text-center text-danger">Customer cart is empty!</td></tr>';
            } else {
                // Cart items
                $subtotal = 0;
                $total_qty = 0;
                
                foreach ($_SESSION['cart'] as $index => $item) {
                    $product_id = $item['product_id'];
                    $quantity = $item['quantity'];
                    
                    // Get product details
                    $sql = "SELECT name, selling_price FROM products WHERE id = :id";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':id', $product_id, PDO::PARAM_INT);
                    $query->execute();
                    $product = $query->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$product) continue;
                    
                    $line_total = $product['selling_price'] * $quantity;
                    $subtotal += $line_total;
                    $total_qty += $quantity;
                    
                    $html .= '<tr>';
                    $html .= '<td>' . ($index + 1) . '</td>';
                    $html .= '<td>' . htmlspecialchars($product['name']) . '</td>';
                    $html .= '<td class="text-right">' . $currency . number_format($product['selling_price'], 2) . '</td>';
                    $html .= '<td><input type="number" min="1" value="' . $quantity . '" onchange="update_cart(' . $product_id . ', this.value)" style="width:60px; height:25px;" /></td>';
                    $html .= '<td class="text-right">' . $currency . number_format($line_total, 2) . '</td>';
                    $html .= '<td class="text-center"><a href="javascript:del(' . $product_id . ')" title="Delete Item"><i class="fa fa-trash text-danger"></i></a></td>';
                    $html .= '</tr>';
                }
                
                // Total row
                $html .= '<tr class="table-active">';
                $html .= '<td colspan="3" class="font-weight-bold">TOTAL</td>';
                $html .= '<td class="text-center font-weight-bold">' . $total_qty . '</td>';
                $html .= '<td class="text-right font-weight-bold">' . $currency . number_format($subtotal, 2) . '</td>';
                $html .= '<td class="text-center"><a href="javascript:clearCart()" title="Clear Cart"><i class="fa fa-times-circle text-danger"></i></a></td>';
                $html .= '</tr>';
                
                // Payment and checkout section
                $html .= '<tr><td colspan="6">';
                $html .= '<div class="mt-3">';
                $html .= '<div class="form-group"><label><strong>Payment Method:</strong></label><br>';
                $html .= '<input type="radio" name="payment_type" value="CASH" id="payment_cash" checked> Cash &nbsp;&nbsp;';
                $html .= '<input type="radio" name="payment_type" value="POS" id="payment_pos"> POS</div>';
                
                $html .= '<div id="cash_section" class="form-group">';
                $html .= '<label><strong>Amount Received:</strong></label>';
                $html .= '<input type="number" step="0.01" name="cash_received" id="cash_received" class="form-control" placeholder="Enter amount">';
                $html .= '</div>';
                
                $html .= '<div id="pos_section" class="form-group" style="display:none;">';
                $html .= '<label><strong>Reference Number:</strong></label>';
                $html .= '<input type="text" name="payment_ref" id="payment_ref" class="form-control" placeholder="Enter reference">';
                $html .= '</div>';
                
                $html .= '<div class="form-group"><label><strong>Customer Type:</strong></label><br>';
                $html .= '<input type="radio" name="customer_type" value="new" checked> New Customer &nbsp;&nbsp;';
                $html .= '<input type="radio" name="customer_type" value="existing"> Existing</div>';
                
                $html .= '<div id="existing_customer" class="form-group" style="display:none;">';
                $html .= '<label><strong>Select Customer:</strong></label>';
                $html .= '<input type="text" id="customer_search" class="form-control" placeholder="Search customer..." autocomplete="off">';
                $html .= '<input type="hidden" id="customer_id" name="customer_id">';
                $html .= '</div>';
                
                $html .= '<div id="new_customer" class="form-group">';
                $html .= '<label><strong>Customer Name:</strong></label>';
                $html .= '<input type="text" id="customer_name_new" name="customer_name_new" class="form-control" placeholder="Enter name">';
                $html .= '</div>';
                
                
                $html .= '<div class="form-group">';
                $html .= '<label><strong>Discount:</strong></label>';
                $html .= '<input type="number" step="0.01" name="discount" id="discount" value="0" class="form-control">';
                $html .= '</div>';
                
                $html .= '<div class="form-group">';
                $html .= '<label><strong>Change:</strong></label>';
                $html .= '<div id="display_cash_change" class="h4 text-success">' . $currency . '0.00</div>';
                $html .= '</div>';
                
                $html .= '<div class="form-group">';
                $html .= '<label class="text-danger"><strong>Order Total:</strong></label>';
                $html .= '<div id="display_order_total" class="h3 text-danger font-weight-bold">' . $currency . number_format($subtotal, 2) . '</div>';
                $html .= '<input type="hidden" id="hidden_order_total" value="' . $subtotal . '">';
                $html .= '<input type="hidden" id="hidden_cash_change" name="hidden_cash_change">';
                $html .= '</div>';
                
                $html .= '<div class="text-center">';
                $html .= '<button type="button" id="finaliseTrigger" class="btn btn-success btn-lg btn-block"><i class="fa fa-check-circle"></i> Place Order</button>';
                $html .= '</div>';
                
                $html .= '</div>';
                $html .= '</td></tr>';
            }
            
            echo json_encode(['status' => 'success', 'html' => $html]);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
