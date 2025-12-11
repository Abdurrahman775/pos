<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
require("logger.php");

// Initialize variables
$success = '';
$error = '';
$errorMessages = [];

$token = base64_decode($_GET['token']);
$product_id = urldecode($token);

$sql = "SELECT * FROM products WHERE id= :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $product_id, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

// Get all categories
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_query = $dbh->prepare($cat_sql);
$cat_query->execute();
$categories = $cat_query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $product_name = ucfirst(trim($_POST['product_name']));
    $description = (trim($_POST['description']));
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1;
    $barcode = !empty(trim($_POST['barcode'])) ? (trim($_POST['barcode'])) : NULL;
    $cost_price = strtolower(trim($_POST['cost_price']));
    $selling_price = strtolower(trim($_POST['selling_price']));
    $qty = strtolower(trim($_POST['qty']));
    $low_stock_alert = (trim($_POST['low_stock_alert']));

    // Validate Product Name
    if (empty($product_name)) {
        $errorMessages[] = "Product name is required.";
    } elseif (!preg_match('/^[A-Za-z0-9 ]+$/', $product_name)) {
        $errorMessages[] = "Invalid Product Name. Only letters and numbers are allowed.";
    }

    // Validate Description
    if (empty($description)) {
        $errorMessages[] = "Description is required.";
    }

    // Validate Category
    if (empty($category_id) || $category_id <= 0) {
        $errorMessages[] = "Please select a valid category.";
    }

    // Validate Barcode - only check if different from original
    if (!empty($barcode) && $barcode != $result['barcode']) {
        $barcodeSQL = "SELECT id FROM products WHERE barcode = :barcode AND id != :id";
        $barcodeQuery = $dbh->prepare($barcodeSQL);
        $barcodeQuery->bindParam(':barcode', $barcode, PDO::PARAM_STR);
        $barcodeQuery->bindParam(':id', $product_id, PDO::PARAM_INT);
        $barcodeQuery->execute();
        if ($barcodeQuery->rowCount() > 0) {
            $errorMessages[] = "Barcode already exists. Please use a unique barcode.";
        }
    }

    // Validate Cost Price
    if (empty($cost_price) || !is_numeric($cost_price) || $cost_price < 0) {
        $errorMessages[] = "Invalid Cost Price. Please enter a numeric value greater than or equal to 0.";
    }

    // Validate Selling Price
    if (empty($selling_price) || !is_numeric($selling_price) || $selling_price <= 0) {
        $errorMessages[] = "Invalid Selling Price. Please enter a numeric value greater than 0.";
    }

    // Validate Quantity
    if (empty($qty) || !is_numeric($qty) || $qty < 0) {
        $errorMessages[] = "Invalid Quantity. Please enter a numeric value greater than or equal to 0.";
    }

    // Validate Low Stock Alert
    if (empty($low_stock_alert) || !is_numeric($low_stock_alert) || $low_stock_alert < 0) {
        $errorMessages[] = "Invalid Low Stock Alert. Please enter a numeric value greater than or equal to 0.";
    }

    if (empty($errorMessages)) {
        try {
            $sql = "UPDATE products SET name= :product_name, description= :description, barcode= :barcode, cost_price= :cost_price, selling_price= :selling_price, qty_in_stock= :qty_in_stock, low_stock_alert= :low_stock_alert, category_id= :category_id, updated_by= :updated_by WHERE id= :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':barcode', $barcode, PDO::PARAM_STR);
            $query->bindParam(':cost_price', $cost_price, PDO::PARAM_STR);
            $query->bindParam(':selling_price', $selling_price, PDO::PARAM_STR);
            $query->bindParam(':qty_in_stock', $qty, PDO::PARAM_INT);
            $query->bindParam(':low_stock_alert', $low_stock_alert, PDO::PARAM_INT);
            $query->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $query->bindParam(':updated_by', $admin, PDO::PARAM_STR);
            $query->bindParam(':id', $product_id, PDO::PARAM_INT);
            $query->execute();

            if ($query) {
                // Log the activity
                log_activity($dbh, 'UPDATE_PRODUCT', "Updated product: $product_name (ID: $product_id)");
                
                $success = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'Record updated successfully', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-success btn-sm' } } }); });</script>";
                // Refresh product data
                $sql = "SELECT * FROM products WHERE id= :id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $product_id, PDO::PARAM_INT);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'ERROR! Try again', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
            }
        } catch (PDOException $e) {
            $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'System Error! " . addslashes($e->getMessage()) . "', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
        }
    } else {
        // Display validation errors
        $errorList = "<ul>";
        foreach ($errorMessages as $msg) {
            $errorList .= "<li>" . $msg . "</li>";
        }
        $errorList .= "</ul>";
        $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', title: 'Validation Error', message: '{$errorList}', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Point of Sale System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Staff Management System" name="description" />
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
    <link href="template/plugins/datepicker/bootstrap-datepicker.standalone.min.css" rel="stylesheet" type="text/css" />
    <!-- jQuery  -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/jquery-ui.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="template/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="template/plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="template/assets/js/app.js" defer></script>
    <script>
        $(document).ready(function() {
            $.validator.addMethod(
                "alphanumeric",
                function(value, element) {
                    return /^[A-Za-z0-9 ]+$/.test(value);
                },
                "Only letters and numbers are allowed"
            );

            $('#form1').validate({
                rules: {
                    product_name: {
                        required: true,
                        alphanumeric: true,
                        minlength: 2
                    },
                    description: {
                        required: true,
                        minlength: 3
                    },
                    category_id: {
                        required: true
                    },
                    cost_price: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    selling_price: {
                        required: true,
                        number: true,
                        min: 0.01
                    },
                    qty: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    low_stock_alert: {
                        required: true,
                        number: true,
                        min: 0
                    }
                },
                messages: {
                    product_name: {
                        required: "Product name is required",
                        alphanumeric: "Only letters and numbers are allowed",
                        minlength: "Product name must be at least 2 characters long"
                    },
                    description: {
                        required: "Description is required",
                        minlength: "Description must be at least 3 characters long"
                    },
                    category_id: {
                        required: "Please select a category"
                    },
                    cost_price: {
                        required: "Cost price is required",
                        number: "Cost price must be a number",
                        min: "Cost price cannot be negative"
                    },
                    selling_price: {
                        required: "Selling price is required",
                        number: "Selling price must be a number",
                        min: "Selling price must be greater than 0"
                    },
                    qty: {
                        required: "Quantity is required",
                        number: "Quantity must be a number",
                        min: "Quantity cannot be negative"
                    },
                    low_stock_alert: {
                        required: "Low stock alert is required",
                        number: "Low stock alert must be a number",
                        min: "Low stock alert cannot be negative"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#save').on('click', function(e) {
                e.preventDefault();
                if ($('#form1').valid()) {
                    bootbox.confirm({
                        centerVertical: true,
                        size: "small",
                        title: "",
                        message: "Are you sure to save these changes?",
                        buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> No',
                                className: 'btn btn-danger btn-sm'
                            },
                            confirm: {
                                label: '<i class="fa fa-check"></i> Yes',
                                className: 'btn btn-success btn-sm'
                            }
                        },
                        callback: function(output) {
                            if (output) {
                                $('#form1').submit();
                            }
                        }
                    });
                }
            });
        });
    </script>
</head>

<body class="dark-sidenav">
    <?php
    // Displaying Notifications
    echo "{$success} {$error}";
    ?>
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar">
            <?php require('template/top_nav_admin.php'); ?>
        </div>

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Edit Product</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Configuration</a></li>
                                        <li class="breadcrumb-item"><a href="all_products.php">Products</a></li>
                                        <li class="breadcrumb-item active">Edit Product</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- body here -->

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <p class="text-muted mb-0">All fields make with asterisk(<span class="text-danger">*</span>) are mandatory.</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form name="form1" id="form1" method="post" action="#" autocomplete="off">
                                            <div class="form-group">
                                                <label>Product Name <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="product_name" id="product_name" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['product_name']) : htmlspecialchars($result['name']); ?>" placeholder="Enter Product Name" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Description <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="description" id="description" placeholder="Enter Description" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['description']) : htmlspecialchars($result['description']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Category <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control form-control-sm" name="category_id" id="category_id" required>
                                                        <option value="">-- Select Category --</option>
                                                        <?php
                                                        foreach ($categories as $cat) {
                                                            $cat_id = !empty($_POST) ? intval($_POST['category_id']) : intval($result['category_id']);
                                                            $selected = ($cat['id'] == $cat_id) ? 'selected' : '';
                                                            echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Barcode</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="barcode" id="barcode" placeholder="Enter Barcode (Optional)" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['barcode']) : htmlspecialchars($result['barcode'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Cost Price <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="number" class="form-control form-control-sm" name="cost_price" id="cost_price" placeholder="Enter Cost Price" step="0.01" min="0" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['cost_price']) : htmlspecialchars($result['cost_price']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Selling Price <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="number" class="form-control form-control-sm" name="selling_price" id="selling_price" placeholder="Enter Selling Price" step="0.01" min="0.01" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['selling_price']) : htmlspecialchars($result['selling_price']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Quantity in Stock <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="number" class="form-control form-control-sm" name="qty" id="qty" placeholder="Enter Quantity" min="0" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['qty']) : htmlspecialchars($result['qty_in_stock']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Low Stock Alert <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="number" class="form-control form-control-sm" name="low_stock_alert" id="low_stock_alert" placeholder="Enter Low Stock Alert Level" min="0" value="<?php echo !empty($_POST) ? htmlspecialchars($_POST['low_stock_alert']) : htmlspecialchars($result['low_stock_alert']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-12 mt-2 p-0">
                                                    <button type="submit" name="save" id="save" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-content-save-outline mr-2"></i>Save Changes</button>
                                                    <a href="all_products.php" class="btn btn-secondary waves-effect waves-light"><i class="mdi mdi-arrow-left mr-2"></i>Back</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <p class="text-muted mb-0"><i class="fa fa-lightbulb"></i> Hint</p>
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?> <span class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
            </footer>
        </div>
    </div>
</body>

</html>