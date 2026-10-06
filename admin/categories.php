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
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');

        if ($name === '') {
            $error = 'Category name is required.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO categories (name, description, image, is_active) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $name,
                    $description !== '' ? $description : null,
                    $image !== '' ? $image : null,
                    isset($_POST['is_active']) ? 1 : 0,
                ]);
                header('Location: categories');
                exit;
            } catch (PDOException $e) {
                $error = $e->getCode() === '23000'
                    ? 'A category with that name already exists.'
                    : 'Failed to add category: ' . $e->getMessage();
            }
        }
    }

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
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if ($name === '') {
            $error = 'Category name is required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, image = ?, is_active = ? WHERE id = ?");
                $stmt->execute([
                    $name,
                    $description !== '' ? $description : null,
                    $image !== '' ? $image : null,
                    $is_active,
                    $categoryId,
                ]);
                header('Location: categories');
                exit;
            } catch (PDOException $e) {
                $error = "Failed to update category: " . $e->getMessage();
            }
        }
    }
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$totalCategories = $totalStmt->fetchColumn();
$totalPages = (int) max(1, ceil($totalCategories / $limit));
if ($page > $totalPages) $page = $totalPages;

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

<div class="admin-page-header">
    <div>
        <p class="admin-eyebrow">Catalog</p>
        <h1 class="admin-page-title">Categories</h1>
        <p class="admin-page-subtitle">Groups used to organise products. Inactive categories are hidden when adding or editing a product.</p>
    </div>
    <div class="admin-page-actions">
        <span class="admin-count-chip"><?php echo number_format((int) $totalCategories); ?> total</span>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </button>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle admin-table admin-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="admin-empty">
                                    <i class="bi bi-tag admin-empty-icon"></i>
                                    <p class="admin-empty-title">No categories found</p>
                                    <p class="admin-empty-text">Create a category to start grouping products.</p>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="admin-cell-strong"><?php echo (int) $category['id']; ?></td>
                                <td class="admin-cell-strong"><?php echo htmlspecialchars($category['name']); ?></td>
                                <td class="admin-cell-muted"><?php echo htmlspecialchars($category['description'] ?: 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($category['image'])): ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($category['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                             class="admin-thumb">
                                    <?php else: ?>
                                        <span class="admin-thumb-empty"><i class="bi bi-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="category_id" value="<?php echo (int) $category['id']; ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $category['is_active'] ? 'btn-outline-admin-success' : 'btn-outline-admin-secondary'; ?>"
                                                title="<?php echo $category['is_active'] ? 'Deactivate category' : 'Activate category'; ?>">
                                            <i class="bi bi-<?php echo $category['is_active'] ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                            <span class="visually-hidden">Toggle category status</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="admin-cell-muted admin-nowrap"><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-admin-primary" title="Edit category"
                                                data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo (int) $category['id']; ?>">
                                            <i class="bi bi-pencil"></i><span class="visually-hidden">Edit category</span>
                                        </button>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="category_id" value="<?php echo (int) $category['id']; ?>">
                                            <button type="submit" name="delete_category" class="btn btn-sm btn-outline-admin-danger" 
                                                    onclick="return confirm('Delete &quot;<?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>&quot;? Products in it will become uncategorised.');">
                                                <i class="bi bi-trash"></i><span class="visually-hidden">Delete category</span>
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

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalTitle" aria-hidden="true">
    <div class="modal-dialog admin-modal">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalTitle">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                        <input type="text" class="form-control" id="image" name="image" placeholder="e.g. combo.jpg">
                    </div>
                    <div class="mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($categories as $category): ?>
<div class="modal fade" id="editCategoryModal<?php echo (int) $category['id']; ?>" tabindex="-1" aria-labelledby="editCategoryTitle<?php echo (int) $category['id']; ?>" aria-hidden="true">
    <div class="modal-dialog admin-modal">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="category_id" value="<?php echo (int) $category['id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryTitle<?php echo (int) $category['id']; ?>">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="edit-name-<?php echo (int) $category['id']; ?>">Category Name</label>
                        <input type="text" class="form-control" id="edit-name-<?php echo (int) $category['id']; ?>" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit-description-<?php echo (int) $category['id']; ?>">Description</label>
                        <textarea class="form-control" id="edit-description-<?php echo (int) $category['id']; ?>" name="description" rows="3"><?php echo htmlspecialchars((string) ($category['description'] ?? '')); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit-image-<?php echo (int) $category['id']; ?>">Image Filename</label>
                        <input type="text" class="form-control" id="edit-image-<?php echo (int) $category['id']; ?>" name="image" value="<?php echo htmlspecialchars((string) ($category['image'] ?? '')); ?>" placeholder="e.g. combo.jpg">
                    </div>
                    <div class="mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit-active-<?php echo (int) $category['id']; ?>" name="is_active" <?php echo $category['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="edit-active-<?php echo (int) $category['id']; ?>">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_category" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>
