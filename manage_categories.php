<?php
/**
 * Category Management Page
 * Manage product categories and subcategories
 */
require("config.php");
require("include/functions.php");
require("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require categories permission
require_permission('categories');

$error = '';
$success = '';

// Handle add category
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    
    if(empty($name)) {
        $error = 'Category name is required';
    } else {
        try {
            $sql = "INSERT INTO categories (name, description, parent_id, reg_by, reg_date) 
                    VALUES (:name, :description, :parent_id, :reg_by, NOW())";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $query->bindParam(':reg_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
            
            if($query->execute()) {
                log_activity($dbh, 'ADD_CATEGORY', "Added category: $name");
                $success = 'Category added successfully!';
            }
        } catch(PDOException $e) {
            $error = 'Error adding category: ' . $e->getMessage();
        }
    }
}

// Handle delete category
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $cat_id = $_GET['delete'];
    try {
        // Check if category has products
        $sql = "SELECT COUNT(*) FROM products WHERE category_id = :cat_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $query->execute();
        $count = $query->fetchColumn();
        
        if($count > 0) {
            $error = "Cannot delete category. It has $count product(s) associated with it.";
        } else {
            $sql = "UPDATE categories SET is_active = 0 WHERE id = :cat_id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
            if($query->execute()) {
                log_activity($dbh, 'DELETE_CATEGORY', "Deleted category ID: $cat_id");
                $success = 'Category deleted successfully!';
            }
        }
    } catch(PDOException $e) {
        $error = 'Error deleting category: ' . $e->getMessage();
    }
}

// Get all categories
$sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC";
$query = $dbh->prepare($sql);
$query->execute();
$categories = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Manage Categories | POS System</title>
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
    <div class="left-sidenav">
        <div class="brand"><?php require('template/brand_admin.php'); ?></div>
        <div class="menu-content h-100" data-simplebar><?php require('include/menus.php'); ?></div>
    </div>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Manage Categories</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item active">Categories</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Add New Category</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="name">Category Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="parent_id">Parent Category (Optional)</label>
                                        <select class="form-control" id="parent_id" name="parent_id">
                                            <option value="">-- No Parent (Main Category) --</option>
                                            <?php foreach($categories as $cat): ?>
                                                <?php if(empty($cat['parent_id'])): // Only show main categories ?>
                                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Leave blank to create a main category</small>
                                    </div>
                                    
                                    <button type="submit" name="add_category" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus mr-1"></i> Add Category
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">All Categories</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="categoriesTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Type</th>
                                                <th>Products</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($categories as $category): 
                                                // Count products in this category
                                                $sql = "SELECT COUNT(*) FROM products WHERE category_id = :cat_id AND is_active = 1";
                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':cat_id', $category['id'], PDO::PARAM_INT);
                                                $query->execute();
                                                $product_count = $query->fetchColumn();
                                            ?>
                                            <tr>
                                                <td><?php echo $category['id']; ?></td>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td><?php echo htmlspecialchars($category['description'] ?? '-'); ?></td>
                                                <td>
                                                    <?php if(empty($category['parent_id'])): ?>
                                                        <span class="badge badge-primary">Main Category</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Subcategory</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $product_count; ?></td>
                                                <td>
                                                    <a href="edit_category.php?id=<?php echo $category['id']; ?>" 
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $category['id']; ?>" 
                                                       class="btn btn-sm btn-danger" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this category?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
    
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="datatables/datatables.min.js"></script>
    <script src="template/assets/js/app.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#categoriesTable').DataTable({
            pageLength: 25
        });
    });
    </script>
</body>
</html>
