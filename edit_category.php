<?php
/**
 * Edit Category Page
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

// Get category ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_categories.php");
    exit();
}

$cat_id = $_GET['id'];

// Get category details
$sql = "SELECT * FROM categories WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $cat_id, PDO::PARAM_INT);
$query->execute();
$category = $query->fetch(PDO::FETCH_ASSOC);

if(!$category) {
    header("Location: manage_categories.php");
    exit();
}

// Handle update category
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    
    if(empty($name)) {
        $error = 'Category name is required';
    } elseif($parent_id == $cat_id) {
        $error = 'Category cannot be its own parent';
    } else {
        try {
            $sql = "UPDATE categories SET name = :name, description = :description, parent_id = :parent_id, updated_by = :updated_by WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
            $query->bindParam(':id', $cat_id, PDO::PARAM_INT);
            
            if($query->execute()) {
                log_activity($dbh, 'UPDATE_CATEGORY', "Updated category: $name");
                $success = 'Category updated successfully!';
                // Refresh category data
                $category['name'] = $name;
                $category['description'] = $description;
                $category['parent_id'] = $parent_id;
            }
        } catch(PDOException $e) {
            $error = 'Error updating category: ' . $e->getMessage();
        }
    }
}

// Get all categories for parent selection
$sql = "SELECT * FROM categories WHERE is_active = 1 AND id != :id ORDER BY name ASC";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $cat_id, PDO::PARAM_INT);
$query->execute();
$all_categories = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Edit Category | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
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
                            <h4 class="page-title">Edit Category</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item"><a href="manage_categories.php">Categories</a></li>
                                <li class="breadcrumb-item active">Edit Category</li>
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
                    <div class="col-lg-6 offset-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Edit Category: <?php echo htmlspecialchars($category['name']); ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="name">Category Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="parent_id">Parent Category (Optional)</label>
                                        <select class="form-control" id="parent_id" name="parent_id">
                                            <option value="">-- No Parent (Main Category) --</option>
                                            <?php foreach($all_categories as $cat): ?>
                                                <?php if(empty($cat['parent_id'])): // Only show main categories ?>
                                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($category['parent_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Leave blank to create a main category</small>
                                    </div>
                                    
                                    <div class="form-group mb-0">
                                        <button type="submit" name="update_category" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Update Category
                                        </button>
                                        <a href="manage_categories.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i> Back
                                        </a>
                                    </div>
                                </form>
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
    <script src="template/assets/js/app.js"></script>
</body>
</html>
