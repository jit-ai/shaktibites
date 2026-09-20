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
    if (isset($_POST['delete_category']) && isset($_POST['category_id'])) {
        $categoryId = (int)$_POST['category_id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        header('Location: categories');
        exit;
    }
    
    if (isset($_POST['toggle_status']) && isset($_POST['category_id'])) {
        $categoryId = (int)$_POST['category_id'];
        $stmt = $pdo->prepare("SELECT is_active FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch();
        if ($category) {
            $newStatus = $category['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE categories SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $categoryId]);
        }
        header('Location: categories');
        exit;
    }

    if (isset($_POST['update_category']) && isset($_POST['category_id'])) {
        $categoryId = (int)$_POST['category_id'];
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $image = $_POST['image'] ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, image = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $description, $image, $is_active, $categoryId]);
            header('Location: categories');
            exit;
        } catch (Exception $e) {
            $error = "Failed to update category: " . $e->getMessage();
        }
    }
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$totalCategories = $totalStmt->fetchColumn();
$totalPages = ceil($totalCategories / $limit);

$categoriesStmt = $pdo->prepare("
    SELECT id, name, description, image, is_active, created_at 
    FROM categories 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$categoriesStmt->execute([$limit, $offset]);
$categories = $categoriesStmt->fetchAll();

include 'includes/header.php';
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger" style="border-radius: var(--radius-card);"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="h4">Manage Categories</h2>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle me-1"></i> Add Category
        </button>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table admin-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No categories found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($category['image'])): ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($category['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-admin-<?php echo $category['is_active'] ? 'success' : 'secondary'; ?>">
                                            <i class="bi bi-<?php echo $category['is_active'] ? 'check-circle' : 'x-circle'; ?>"></i>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-admin-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $category['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" name="delete_category" class="btn btn-sm btn-outline-admin-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this category?');">
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

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog admin-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image Filename</label>
                        <input type="text" class="form-control" id="image" name="image" placeholder="e.g., category1.jpg">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php foreach ($categories as $category): ?>
<div class="modal fade" id="editCategoryModal<?php echo $category['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog admin-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Filename</label>
                        <input type="text" class="form-control" name="image" value="<?php echo htmlspecialchars($category['image']); ?>" placeholder="e.g., category1.jpg">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $category['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>

<script>
document.getElementById('addCategoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Category added successfully! (This is a demo - form not connected to backend)');
    const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
    if (modal) modal.hide();
    this.reset();
});

document.querySelectorAll('#editCategoryModal form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (confirm('Update this category?')) {
            this.submit();
        }
    });
});
</script>
</body>
</html>
