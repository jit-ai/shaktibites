<?php
session_start();
include '../includes/config.php';

$host = 'localhost';
$db   = 'shakti_bites';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        header('Location: ../setup');
        exit;
    }
    die("Database connection error: " . htmlspecialchars($e->getMessage()));
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login');
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

$categoriesStmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $categoriesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $label = $_POST['label'] ?? '';
    $label_class = $_POST['label_class'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image = $_POST['image'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, sku = ?, category_id = ?, label = ?, label_class = ?, stock = ?, image = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $sku, $category_id, $label, $label_class, $stock, $image, $is_active, $product_id]);
        $success = "Product updated successfully!";
        header('Location: products');
        exit;
    } catch (Exception $e) {
        $error = "Failed to update product: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="h4">Edit Product: <?php echo htmlspecialchars($product['name']); ?></h2>
    </div>
    <div class="col-auto">
        <a href="products" class="btn btn-outline-admin-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Products
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger" style="border-radius: var(--radius-card);"><?php echo htmlspecialchars($error); ?></div>
<?php elseif (isset($success)): ?>
    <div class="alert alert-success" style="border-radius: var(--radius-card);"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Product Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="sku" class="form-label">SKU *</label>
                    <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label">Price (₹) *</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="stock" class="form-label">Stock Quantity *</label>
                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="label" class="form-label">Label (Optional)</label>
                    <input type="text" class="form-control" id="label" name="label" value="<?php echo htmlspecialchars($product['label']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="label_class" class="form-label">Label Class (Optional)</label>
                    <input type="text" class="form-control" id="label_class" name="label_class" value="<?php echo htmlspecialchars($product['label_class']); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Image Filename</label>
                <input type="text" class="form-control" id="image" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" placeholder="e.g., product1.PNG">
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_active">Active Product</label>
            </div>
            
            <button type="submit" name="update_product" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Update Product
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
