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

require_once __DIR__ . '/../includes/catalog.php';
$comboProductIds = array_keys(shakti_combo_catalog());

include 'includes/header.php';
?>

<div class="admin-page-header">
    <div>
        <p class="admin-eyebrow">Catalog</p>
        <h1 class="admin-page-title">Products</h1>
        <p class="admin-page-subtitle">Every box and combo pack a customer can order. Deactivate an item to hide it from the storefront.</p>
    </div>
    <div class="admin-page-actions">
        <span class="admin-count-chip"><?php echo number_format((int) $totalProducts); ?> total</span>
        <a href="product-add" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle admin-table admin-table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Price</th>
                            <th>SKU</th>
                            <th class="text-end">Stock</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="10">
                                    <div class="admin-empty">
                                        <i class="bi bi-box admin-empty-icon"></i>
                                        <p class="admin-empty-title">No products found</p>
                                        <p class="admin-empty-text">Add your first product to start selling.</p>
                                        <a href="product-add" class="btn btn-primary btn-sm">Add Product</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <?php $isCombo = in_array((int) $product['id'], $comboProductIds, true); ?>
                                <tr>
                                    <td class="admin-cell-strong"><?php echo (int) $product['id']; ?></td>
                                    <td>
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="admin-thumb">
                                        <?php else: ?>
                                            <span class="admin-thumb-empty"><i class="bi bi-image"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="admin-cell-strong"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <?php if ($isCombo): ?>
                                            <span class="status-pill status-accent">Combo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="admin-cell-muted"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td class="text-end admin-amount">₹<?php echo number_format((float) $product['price'], 2); ?></td>
                                    <td class="admin-cell-muted"><?php echo htmlspecialchars((string) ($product['sku'] ?? '—')); ?></td>
                                    <td class="text-end admin-amount"><?php echo (int) $product['stock']; ?></td>
                                    <td>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                            <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $product['is_active'] ? 'btn-outline-admin-success' : 'btn-outline-admin-secondary'; ?>"
                                                    title="<?php echo $product['is_active'] ? 'Deactivate product' : 'Activate product'; ?>">
                                                <i class="bi bi-<?php echo $product['is_active'] ? 'check-circle' : 'x-circle'; ?>"></i>
                                                <span class="visually-hidden">Toggle product status</span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="admin-cell-muted admin-nowrap"><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="product-edit?id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-outline-admin-primary" title="Edit product">
                                                <i class="bi bi-pencil"></i><span class="visually-hidden">Edit product</span>
                                            </a>
                                            <form method="POST" action="" class="d-inline">
                                                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                <button type="submit" name="delete_product" class="btn btn-sm btn-outline-admin-danger" 
                                                        onclick="return confirm('Delete &quot;<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>&quot;? This cannot be undone.');">
                                                    <i class="bi bi-trash"></i><span class="visually-hidden">Delete product</span>
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