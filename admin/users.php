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
    if (isset($_POST['add_user'])) {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        $userError = null;
        if ($name === '') {
            $userError = 'Name is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $userError = 'A valid email address is required.';
        } elseif (strlen($password) < 6) {
            $userError = 'Password must be at least 6 characters long.';
        }

        if ($userError === null) {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, phone, password, is_admin, is_active)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $name,
                    $email,
                    $phone !== '' ? $phone : null,
                    password_hash($password, PASSWORD_DEFAULT),
                    isset($_POST['role']) && $_POST['role'] === 'admin' ? 1 : 0,
                    isset($_POST['is_active']) ? 1 : 0,
                ]);
                header('Location: users');
                exit;
            } catch (PDOException $e) {
                $userError = $e->getCode() === '23000'
                    ? 'That email address is already registered.'
                    : 'Failed to add user: ' . $e->getMessage();
            }
        }
    }

    if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        if ($userId != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        }
        header('Location: users');
        exit;
    }
    
    if (isset($_POST['toggle_status']) && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if ($user) {
            $newStatus = $user['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);
        }
        header('Location: users');
        exit;
    }
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $totalStmt->fetchColumn();
$totalPages = (int) max(1, ceil($totalUsers / $limit));
if ($page > $totalPages) $page = $totalPages;

$usersStmt = $pdo->prepare("
    SELECT id, name, email, phone, is_admin, is_active, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$usersStmt->execute([$limit, $offset]);
$users = $usersStmt->fetchAll();

include 'includes/header.php';
?>

<div class="admin-page-header">
    <div>
        <p class="admin-eyebrow">People</p>
        <h1 class="admin-page-title">Users</h1>
        <p class="admin-page-subtitle">Customer accounts and admin access. Deactivate an account to block sign-in without deleting history.</p>
    </div>
    <div class="admin-page-actions">
        <span class="admin-count-chip"><?php echo number_format((int) $totalUsers); ?> total</span>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus me-1"></i> Add User
        </button>
    </div>
</div>

<?php if (!empty($userError)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($userError); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle admin-table admin-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="admin-empty">
                                    <i class="bi bi-people admin-empty-icon"></i>
                                    <p class="admin-empty-title">No users found</p>
                                    <p class="admin-empty-text">Accounts created at checkout will appear here.</p>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="admin-cell-strong"><?php echo (int) $user['id']; ?></td>
                                <td class="admin-cell-strong"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td class="admin-cell-muted"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="admin-cell-muted"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="status-pill <?php echo $user['is_admin'] ? 'status-accent' : 'status-muted'; ?>">
                                        <?php echo $user['is_admin'] ? 'Admin' : 'Customer'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill <?php echo $user['is_active'] ? 'status-success' : 'status-danger'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="admin-cell-muted admin-nowrap"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                            <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $user['is_active'] ? 'btn-outline-admin-danger' : 'btn-outline-admin-success'; ?>"
                                                    title="<?php echo $user['is_active'] ? 'Deactivate account' : 'Activate account'; ?>">
                                                <i class="bi bi-<?php echo $user['is_active'] ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                                <span class="visually-hidden">Toggle account status</span>
                                            </button>
                                        </form>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" action="" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn btn-sm btn-outline-admin-danger" 
                                                        onclick="return confirm('Delete &quot;<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>&quot;? This cannot be undone.');">
                                                    <i class="bi bi-trash"></i><span class="visually-hidden">Delete user</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-admin-secondary" disabled title="You cannot delete your own account">
                                                <i class="bi bi-trash"></i><span class="visually-hidden">Delete user</span>
                                            </button>
                                        <?php endif; ?>
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

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalTitle" aria-hidden="true">
    <div class="modal-dialog admin-modal">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalTitle">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="10-digit mobile number">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                        <div class="form-text">Minimum 6 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active Account</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_user" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
