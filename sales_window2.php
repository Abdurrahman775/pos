<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
require("template/plugins/fpdf/fpdf.php");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $product_name = ucfirst(trim($_POST['product_name']));
    $description = trim($_POST['description']);
    $barcode = !empty(trim($_POST['barcode'])) ? trim($_POST['barcode']) : NULL;
    $price = strtolower(trim($_POST['price']));
    $qty = strtolower(trim($_POST['qty']));
    $low_stock_alert = trim($_POST['low_stock_alert']);

    $errorMessages = [];

    // Validate Product Name
    if (empty($product_name)) {
        $errorMessages[] = "Product Name is required.";
    } elseif (!preg_match('/^[A-Za-z0-9\s]+$/', $product_name)) {
        $errorMessages[] = "Invalid Product Name. Only letters and numbers are allowed.";
    } elseif(val_product_name($dbh, $product_name) != 0) {
        $errorMessages[] = "Product Exists";
    }

    // Validate Description
    if (empty($description)) {
        $errorMessages[] = "Description is required.";
    }

    // Validate Price
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errorMessages[] = "Invalid Price. Please enter a numeric value greater than 0.";
    }

    // Validate Quantity
    if (empty($qty) || !is_numeric($qty) || $qty <= 0) {
        $errorMessages[] = "Invalid Quantity. Please enter a numeric value greater than 0.";
    }

    // Validate Low Stock Alert
    if (empty($low_stock_alert) || !is_numeric($low_stock_alert) || $low_stock_alert < 0) {
        $errorMessages[] = "Invalid Low Stock Alert. Please enter a numeric value greater than or equal to 0.";
    }

    if (empty($errorMessages)) {
        try {
            $sql = "INSERT INTO products (product_name, description, barcode, price, qty_in_stock, low_stock_alert, reg_by, reg_date) VALUES (:product_name, :description, :barcode, :price, :qty_in_stock, :low_stock_alert, :reg_by, :reg_date)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':barcode', $barcode, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':qty_in_stock', $qty, PDO::PARAM_INT);
            $query->bindParam(':low_stock_alert', $low_stock_alert, PDO::PARAM_INT);
            $query->bindParam(':reg_by', $admin, PDO::PARAM_STR);
            $query->bindParam(':reg_date', $now, PDO::PARAM_STR);
            $query->execute();

            if ($query == TRUE) {
                $barcode = !empty($barcode) ? $barcode : null;
                $success = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'Record saved', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-success btn-sm' } } }); });</script>";
            } else {
                $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'ERROR! Try again', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
            }
        } catch (PDOException $e) {
            echo $err = $e->getMessage();
            $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'System Error!', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Point of Sale System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Point of Sales System" name="description" />
    <meta content="S & I IT Partners Ltd" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 
    <!-- jQuery  -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/jquery-ui.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="template/plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="template/assets/js/app.js" defer></script>
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script>
    let cart = [];

    function addToCart(productId, productName, productPrice) {
        // Check if product already in cart
        let productInCart = cart.find(product => product.id === productId);
        if (productInCart) {
            productInCart.quantity++;
        } else {
            cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
        }

        // Update Cart Display
        updateCartDisplay();
    }

    function updateCartDisplay() {
        const cartContainer = document.getElementById('cart-items');
        cartContainer.innerHTML = '';

        cart.forEach(product => {
            cartContainer.innerHTML += `<p>${product.name} x${product.quantity} - $${(product.price * product.quantity).toFixed(2)}</p>`;
        });

        document.getElementById('checkout-btn').style.display = cart.length > 0 ? 'block' : 'none';
    }

    function checkout() {
        if (cart.length === 0) return;

        // Prepare data for the backend
        let cartData = {
            cart: cart,
            totalAmount: cart.reduce((total, product) => total + (product.price * product.quantity), 0)
        };

        // Send data to the server via AJAX
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(cartData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Checkout successful!');
                cart = [];
                updateCartDisplay();
            } else {
                alert('Checkout failed. Please try again.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productName = this.dataset.name;
                const productPrice = this.dataset.price;

                addToCart(productId, productName, productPrice);
            });
        });

        $("#datatable").DataTable({
            "responsive": true,
            "ordering": false,
            "pageLength": 10,
            "language": {
                search: "_INPUT_",
                searchPlaceholder: "Search Product"
            },                
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "load_products.php", // Server script to load product data
                type: "POST",
            },
            "columns": [
                { "data": "id" },
                { "data": "product_name" },
                { "data": "price" },
                {
                    "data": null,
                    "defaultContent": '<button class="btn-add-to-cart btn btn-sm btn-primary">Add to Cart</button>'
                }
            ]
        });
    });
    </script>
</head>
<body>
    <div class="container">
        <h2>Product List</h2>
        <table id="datatable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>

        <h2>Cart</h2>
        <div id="cart-items"></div>
        <button id="checkout-btn" onclick="checkout()" style="display:none;">Checkout</button>
    </div>
</body>
</html>
