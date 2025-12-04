<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/customer_cart.php");

// Set header for JSON response
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$admin = isset($_SESSION['pos_admin']) ? $_SESSION['pos_admin'] : 'system';

// Initialize cart if not set
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action == 'add_by_barcode') {
    $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : '';
    
    if (!empty($barcode)) {
        // Search for product by barcode
        $sql = "SELECT id, name, qty_in_stock FROM products WHERE barcode = :barcode AND is_active = 1 LIMIT 1";
        $query = $dbh->prepare($sql);
        $query->bindParam(':barcode', $barcode, PDO::PARAM_STR);
        $query->execute();
        $product = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            if ($product['qty_in_stock'] > 0) {
                addtocart($product['id']);
                $response = [
                    'status' => 'success', 
                    'message' => 'Product added to cart',
                    'product_name' => $product['name']
                ];
            } else {
                $response = ['status' => 'error', 'message' => 'Product is out of stock'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Product not found'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Barcode is empty'];
    }
} elseif ($action == 'add') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if ($product_id > 0) {
        addtocart($product_id);
        $response = ['status' => 'success', 'message' => 'Product added to cart'];
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid product ID'];
    }
} elseif ($action == 'delete') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if ($product_id > 0) {
        remove_product($product_id);
        $response = ['status' => 'success', 'message' => 'Product removed from cart'];
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid product ID'];
    }
} elseif ($action == 'clear') {
    unset($_SESSION['cart']);
    $response = ['status' => 'success', 'message' => 'Cart cleared'];
} elseif ($action == 'update') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    
    if ($product_id > 0) {
        $max = count($_SESSION['cart']);
        $updated = false;
        $msg = '';
        
        for ($i = 0; $i < $max; $i++) {
            if ($_SESSION['cart'][$i]['product_id'] == $product_id) {
                $db_quantity = get_product_quantity($dbh, $product_id);
                
                if ($quantity > $db_quantity) {
                    $msg = convert_product_id($dbh, $product_id) . " quantity exceeded! Available is " . $db_quantity;
                    $response = ['status' => 'error', 'message' => $msg];
                } elseif ($quantity > 0 && $quantity <= 999) {
                    $_SESSION['cart'][$i]['quantity'] = $quantity;
                    $response = ['status' => 'success', 'message' => 'Cart updated'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Product quantity can not be zero (0)'];
                }
                $updated = true;
                break;
            }
        }
        
        if (!$updated && empty($msg)) {
             $response = ['status' => 'error', 'message' => 'Product not found in cart'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid parameters'];
    }
} elseif ($action == 'finalise') {
    $customer = isset($_POST['customer']) ? $_POST['customer'] : '';
    if ($customer == '') {
        $customer = "Customer";
    }
    $payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : '';
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
    $total = isset($_POST['hidden_order_total']) ? floatval($_POST['hidden_order_total']) : 0;
    $items_count = isset($_POST['hidden_items_count']) ? intval($_POST['hidden_items_count']) : 0;
    $cash_received = isset($_POST['cash_received']) ? floatval($_POST['cash_received']) : 0;
    $cash_change = isset($_POST['hidden_cash_change']) ? floatval($_POST['hidden_cash_change']) : 0;
    $payment_ref = isset($_POST['payment_ref']) ? $_POST['payment_ref'] : NULL;

    if ($payment_type == strtoupper("POS")) {
        // User requested to keep these fields for POS
        // $cash_received = NULL;
        // $cash_change = NULL;
    } else {
        $payment_ref = NULL;
    }

    $order_id = date("dmyHis");
    $actual_total = 0;

    try {
        $dbh->beginTransaction(); // Turn off autocommit mode
        $saleSQL = "insert into sales (order_id, product_id, unit_price, quantity, total, reg_by, reg_date) values (:order_id, :product_id, :unit_price, :quantity, :total, :reg_by, :reg_date)";
        $items = count($_SESSION['cart']);
        for ($i = 0; $i < $items; $i++) {
            $item_product_id = $_SESSION['cart'][$i]['product_id'];
            $item_quantity = $_SESSION['cart'][$i]['quantity'];
            $item_unit_price = get_product_price($dbh, $item_product_id);
            $item_total = $item_unit_price * $item_quantity;
            $actual_total += $item_total;

            $saleQuery = $dbh->prepare($saleSQL);
            $saleQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
            $saleQuery->bindParam(':product_id', $item_product_id, PDO::PARAM_INT);
            $saleQuery->bindParam(':unit_price', $item_unit_price, PDO::PARAM_STR);
            $saleQuery->bindParam(':quantity', $item_quantity, PDO::PARAM_INT);
            $saleQuery->bindParam(':total', $item_total, PDO::PARAM_STR);
            $saleQuery->bindParam(':reg_by', $admin, PDO::PARAM_STR);
            $saleQuery->bindParam(':reg_date', $now, PDO::PARAM_STR);
            $saleQuery->execute();
        }

        $updateSQL = "update products set qty_in_stock= (qty_in_stock - :quantity), updated_by= :updated_by where id= :product_id";
        $update_items = count($_SESSION['cart']);
        for ($x = 0; $x < $update_items; $x++) {
            $update_product_id = $_SESSION['cart'][$x]['product_id'];
            $update_quantity = $_SESSION['cart'][$x]['quantity'];

            $updateQuery = $dbh->prepare($updateSQL);
            $updateQuery->bindParam(':product_id', $update_product_id, PDO::PARAM_INT);
            $updateQuery->bindParam(':quantity', $update_quantity, PDO::PARAM_INT);
            $updateQuery->bindParam(':updated_by', $admin, PDO::PARAM_STR);
            $updateQuery->execute();
        }

        $summarySQL = "insert into sales_summary (order_id, customer, payment_type, payment_ref, actual_total, discount, total, cash_received, cash_change, items_count, reg_by, reg_date) values (:order_id, :customer, :payment_type, :payment_ref, :actual_total, :discount, :total, :cash_received, :cash_change, :items_count, :reg_by, :reg_date)";
        $summaryQuery = $dbh->prepare($summarySQL);
        $summaryQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $summaryQuery->bindParam(':customer', $customer, PDO::PARAM_STR);
        $summaryQuery->bindParam(':payment_type', $payment_type, PDO::PARAM_STR);
        $summaryQuery->bindParam(':payment_ref', $payment_ref, PDO::PARAM_STR);
        $summaryQuery->bindParam(':actual_total', $actual_total, PDO::PARAM_STR);
        $summaryQuery->bindParam(':discount', $discount, PDO::PARAM_STR);
        $summaryQuery->bindParam(':total', $total, PDO::PARAM_STR);
        $summaryQuery->bindParam(':cash_received', $cash_received, PDO::PARAM_STR);
        $summaryQuery->bindParam(':cash_change', $cash_change, PDO::PARAM_STR);
        $summaryQuery->bindParam(':items_count', $items_count, PDO::PARAM_INT);
        $summaryQuery->bindParam(':reg_by', $admin, PDO::PARAM_STR);
        $summaryQuery->bindParam(':reg_date', $now, PDO::PARAM_STR);
        $summaryQuery->execute();

        if ($payment_type == strtoupper("CASH")) {
            $cashSQL = "update accounts set balance= (balance + :total) where account_type= 'CASH'";
            $cashQuery = $dbh->prepare($cashSQL);
            $cashQuery->bindParam(':total', $total, PDO::PARAM_STR);
            $cashQuery->execute();
        } else if ($payment_type == strtoupper("POS")) {
            $posSQL = "update accounts set balance= (balance + :total) where account_type= 'POS'";
            $posQuery = $dbh->prepare($posSQL);
            $posQuery->bindParam(':total', $total, PDO::PARAM_STR);
            $posQuery->execute();
        }

        if ($dbh->commit() == TRUE) {
            unset($_SESSION['cart']);
            $response = [
                'status' => 'success', 
                'message' => 'Record saved',
                'order_id' => $order_id,
                'token' => base64_encode($order_id)
            ];
        } else {
            $dbh->rollback();
            $response = ['status' => 'error', 'message' => 'Transaction failed'];
        }
    } catch (PDOException $e) {
        $dbh->rollback();
        $response = ['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()];
    }
} elseif ($action == 'get_cart_html') {
    // Generate HTML for the cart table
    ob_start();
    ?>
    <table id="datatable" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <?php
        if (is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            echo "<thead><tr bgcolor=\"#FFFFFF\" style=\"font-weight:bold\">
            <th class=\"text-head-center\">#</th>
            <th class=\"text-head-left\">Name</th>
            <th class=\"text-head-left\">Unit Price</th>
            <th class=\"text-head-left\">Qty</th>
            <th class=\"text-head-left\">Amount</th>
            <th class=\"text-head-center\"><a href=\"javascript:clearCart()\" title=\"Clear Customer Cart\">Del</a></th>
            </tr></thead>";
            $max = count($_SESSION['cart']);
            for ($i = 0; $i < $max; $i++) {
                $product_id = $_SESSION['cart'][$i]['product_id'];
                $quantity = $_SESSION['cart'][$i]['quantity'];
                $product_name = convert_product_id($dbh, $product_id);
                if ($quantity == 0) continue;
        ?>
                <tr bgcolor="#FFFFFF">
                    <td width="6%" align="center"><?php echo $i + 1; ?></td>
                    <td width="37%"><?php echo $product_name; ?></td>
                    <td width="21%" align="right"><?php echo get_currency($dbh) . number_format(get_product_price($dbh, $product_id), 2); ?></td>
                    <td width="10%"><input type="number" name="product<?php echo $product_id; ?>" value="<?php echo $quantity; ?>" onChange="update_cart(<?php echo $product_id; ?>, this.value)" style="width:40px !important; height:25px" /></td>
                    <td width="17%" align="right"><?php echo get_currency($dbh) . number_format(get_product_price($dbh, $product_id) * $quantity, 2); ?></td>
                    <td width="9%" align="center"><a href="javascript:del(<?php echo $product_id; ?>)" title="Delete Item"><i class="fa fa-trash fa-lg"></i></a></td>
                </tr>
            <?php
            }
            ?>
            <tr bgcolor="#FFFFFF">
                <td colspan="3" width="64%" style="font-weight:bold;">TOTAL</td>
                <td width="10%" align="center" style="font-weight:bold;"><?php echo get_quantity_total($dbh); ?></td>
                <td width="17%" align="right" style="font-weight:bold;"><?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?></td>
                <td width="9%" align="center">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">
                    <div style="font-weight:bold;"><span style=" display:inline-table; width:120px;">Payment</span> : <input type="radio" name="payment_type" value="CASH" id="payment_type_0" class="cash" checked>Cash
                        <input type="radio" name="payment_type" value="POS" id="payment_type_1" class="pos">POS
                    </div>
                    <div id="amount_collected" style="font-weight:bold; margin-top:10px;"><span style="display:inline-table; width:120px;">Amount Received</span> : <input type="text" name="cash_received" id="cash_received" style="height:20px !important; width:150px !important;" onkeypress="return numberOnly(event)"></div>
                    <div id="ref_no" style="font-weight:bold; margin-top:10px; display:none;"><span style="display:inline-table; width:120px;">Ref. No.</span> : <input type="text" name="payment_ref" id="payment_ref" style="height:20px !important; width:150px !important;"></div>
                    <div style="font-weight:bold; margin-top:10px;"><span style=" display:inline-table; width:120px;">Name</span> : <input type="text" name="customer" id="customer" value="Customer" style="height:20px !important; width:150px !important;"></div>
                    <div style="font-weight:bold; margin-top:10px;"><span style=" display:inline-table; width:120px;">Discount</span> : <input type="text" id="discount" name="discount" value="0" style="height:20px !important; width:150px !important;" onkeypress="return numberOnly(event)" /></div>
                    <div style="font-weight:bold; margin-top:10px;"><span style=" display:inline-table; width:120px;">Cash Change</span> : <span id="display_cash_change">N0.00</span></div>
                    <div style="font-size:larger; font-weight:bold; margin-top:10px;"><span style="display:inline-table; width:120px; color:#F00;">Order Total</span> : <span id="display_order_total"><?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?></span></div>
                    <div style="text-align: center;"><button id="finaliseTrigger" style="width: 150px; height: 40px;">Place Order</button></div>
                </td>
            </tr>
        <?php
        } else {
            echo "<tr bgColor='#FFFFFF'><td align=\"center\" style=\"color:#F00;\">Customer cart is empty!</td>";
        }
        ?>
    </table>
    <input type="hidden" id="hidden_order_total" name="hidden_order_total" value="<?php echo get_order_total($dbh); ?>" />
    <input type="hidden" id="hidden_cash_change" name="hidden_cash_change" />
    <input type="hidden" id="hidden_items_count" name="hidden_items_count" value="<?php echo get_quantity_total($dbh); ?>" />
    <input type="hidden" name="product_id" />
    <input type="hidden" name="command" />
    <?php
    $html = ob_get_clean();
    
    $response = [
        'status' => 'success',
        'html' => $html,
        'totals' => [
            'items_count' => get_quantity_total($dbh),
            'order_total' => get_order_total($dbh),
            'formatted_total' => get_currency($dbh) . number_format(get_order_total($dbh), 2)
        ]
    ];
}

echo json_encode($response);
?>
