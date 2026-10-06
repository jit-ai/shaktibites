<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo = getPDO();
} catch (\Throwable $e) {
    if ($e instanceof \PDOException
        && strpos($e->getMessage(), 'Unknown database') !== false) {
        header('Location: ../setup');
        exit;
    }
    die(db_connection_error_message($e));
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login');
    exit;
}

$categoriesStmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $categoriesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    // The category select defaults to an empty option, and an empty string
    // would be stored as 0 and break the categories foreign key.
    $category_id = ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null;
    $label = $_POST['label'] ?? '';
    $label_class = $_POST['label_class'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image = $_POST['image'] ?? '';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, sku, category_id, label, label_class, stock, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$name, $description, $price, $sku, $category_id, $label, $label_class, $stock, $image]);
        $success = "Product added successfully!";
        header('Location: products');
        exit;
    } catch (Exception $e) {
        $error = "Failed to add product: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="admin-page-header">
    <div>
        <p class="admin-eyebrow">Catalog</p>
        <h1 class="admin-page-title">Add New Product</h1>
        <p class="admin-page-subtitle">Create a single box or a combo pack. Price and name here feed the admin catalog record.</p>
    </div>
    <div class="admin-page-actions">
        <a href="products" class="btn btn-outline-admin-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Products
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php elseif (isset($success)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="admin-form-section">
                <p class="admin-form-legend">Basics</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. 2 Box Combo" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sku" class="form-label">SKU *</label>
                        <input type="text" class="form-control" id="sku" name="sku" placeholder="e.g. SB-COMBO-001" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Short description shown on the product"></textarea>
                </div>
            </div>

            <div class="admin-form-section">
                <p class="admin-form-legend">Pricing &amp; Stock</p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label">Price (₹) *</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stock" class="form-label">Stock Quantity *</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" value="0" required>
                    </div>
                </div>
            </div>

            <div class="admin-form-section">
                <p class="admin-form-legend">Presentation</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="label" class="form-label">Label (Optional)</label>
                        <input type="text" class="form-control" id="label" name="label" placeholder="e.g. Best Seller">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="label_class" class="form-label">Label Class (Optional)</label>
                        <input type="text" class="form-control" id="label_class" name="label_class" placeholder="e.g. label-bestseller">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Image Filename</label>
                    <input type="text" class="form-control" id="image" name="image" placeholder="e.g. Peanut-product.png">
                    <div class="form-text">Stored in <code>assets/images/</code> on the storefront.</div>
                </div>
            </div>

            <div class="admin-form-footer">
                <button type="submit" name="add_product" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Product
                </button>
                <a href="products" class="btn btn-outline-admin-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
