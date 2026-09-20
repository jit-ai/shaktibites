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
    } else {
        die("Database connection error. Please contact administrator.");
    }
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        header('Location: products');
        exit;
    }
    
    if (isset($_POST['toggle_status']) && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        $stmt = $pdo->prepare("SELECT is_active FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if ($product) {
            $newStatus = $product['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $productId]);
        }
        header('Location: products');
        exit;
    }
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

$productsStmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, p.image, p.label, p.label_class, 
           p.is_active, p.stock, p.sku, p.created_at,
           c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC 
    LIMIT ? OFFSET ?
");
$productsStmt->execute([$limit, $offset]);
$products = $productsStmt->fetchAll();

$categoriesStmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $categoriesStmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="h4">Manage Products</h2>
    </div>
    <div class="col-auto">
        <a href="product-add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Product
        </a>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table admin-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>SKU</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">No products found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-admin-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                            <i class="bi bi-<?php echo $product['is_active'] ? 'check-circle' : 'x-circle'; ?>"></i>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="product-edit?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-admin-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" name="delete_product" class="btn btn-sm btn-outline-admin-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center admin-pagination">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
