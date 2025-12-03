<?php

/**
 * Held Transactions Page
 * View and retrieve incomplete transactions
 */
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");

// Handle retrieve action
if (isset($_POST['retrieve_id'])) {
    $held_id = intval($_POST['retrieve_id']);

    // Get held transaction
    $sql = "SELECT * FROM held_transactions WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $held_id, PDO::PARAM_INT);
    $query->execute();
    $held = $query->fetch(PDO::FETCH_ASSOC);

    if ($held) {
        // Delete from held transactions
        $sql = "DELETE FROM held_transactions WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $held_id, PDO::PARAM_INT);
        $query->execute();

        // Redirect to sales window with cart data
        $_SESSION['retrieve_cart'] = $held['cart_data'];
        $_SESSION['retrieve_customer_id'] = $held['customer_id'];
        header("Location: sales_window.php");
        exit();
    }
}

// Handle delete action
if (isset($_POST['delete_id'])) {
    $held_id = intval($_POST['delete_id']);
    $sql = "DELETE FROM held_transactions WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $held_id, PDO::PARAM_INT);
    $query->execute();
    $success = "Transaction deleted successfully";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Held Transactions | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="datatables/datatables.min.css" rel="stylesheet" type="text/css" />
</head>

<body class="dark-sidenav">
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Held Transactions</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="sales_window.php">POS</a></li>
                                <li class="breadcrumb-item active">Held Transactions</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">Incomplete Transactions</h5>
                                    </div>
                                    <div class="col-auto">
                                        <a href="sales_window.php" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> New Sale
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="heldTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Cashier</th>
                                                <th>Customer</th>
                                                <th>Items</th>
                                                <th>Held At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT ht.*, a.fname, a.sname, c.name as customer_name
                                                    FROM held_transactions ht
                                                    LEFT JOIN admins a ON ht.cashier_id = a.id
                                                    LEFT JOIN customers c ON ht.customer_id = c.id
                                                    ORDER BY ht.held_at DESC";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $held_transactions = $query->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($held_transactions as $held):
                                                $cart_data = json_decode($held['cart_data'], true);
                                                $item_count = count($cart_data);
                                            ?>
                                                <tr>
                                                    <td><?php echo $held['id']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($held['transaction_name']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($held['fname'] . ' ' . $held['sname']); ?></td>
                                                    <td><?php echo $held['customer_name'] ? htmlspecialchars($held['customer_name']) : '<em>Walk-in</em>'; ?></td>
                                                    <td><?php echo $item_count; ?> item(s)</td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($held['held_at'])); ?></td>
                                                    <td>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="retrieve_id" value="<?php echo $held['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-success" title="Retrieve">
                                                                <i class="fas fa-undo"></i> Retrieve
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-sm btn-info" onclick="viewCart(<?php echo htmlspecialchars($held['id']); ?>, <?php echo htmlspecialchars($held['cart_data']); ?>)" title="View Items">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this held transaction?');">
                                                            <input type="hidden" name="delete_id" value="<?php echo $held['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?>
            </footer>
        </div>
    </div>

    <!-- View Cart Modal -->
    <div class="modal fade" id="viewCartModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cart Items</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="cartItemsView">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="datatables/datatables.min.js"></script>
    <script src="template/assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            $('#heldTable').DataTable({
                pageLength: 25,
                order: [
                    [5, 'desc']
                ]
            });
        });

        function viewCart(heldId, cartData) {
            let html = '<ul class="list-group">';
            cartData.forEach(item => {
                html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small>₦${item.price.toFixed(2)} each</small>
                    </div>
                    <span class="badge badge-primary badge-pill">Qty: ${item.quantity}</span>
                </li>
            `;
            });
            html += '</ul>';

            $('#cartItemsView').html(html);
            $('#viewCartModal').modal('show');
        }
    </script>
</body>

</html>