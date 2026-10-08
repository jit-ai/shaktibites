<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$page_title = 'Login';

// Initialize database connection
try {
    $pdo = getPDO();
} catch (\Throwable $e) {
    if ($e instanceof \PDOException
        && strpos($e->getMessage(), 'Unknown database') !== false) {
        header('Location: setup');
        exit;
    }
    die(db_connection_error_message($e));
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];

            if ($user['is_admin']) {
                header('Location: admin/dashboard');
                exit;
            }

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
            }

            $redirect = $_SESSION['redirect_url'] ?? './';
            unset($_SESSION['redirect_url']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shakti Bites</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/style.css?v=20261006-auth-refresh">
</head>
<body class="account-entry-page">
<?php include 'includes/navbar.php'; ?>

<!-- Login Section -->
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 auth-form-column">
                <div class="auth-card">
                    <div class="card-body">
                        <!-- Brand -->
                        <div class="auth-brand">
                            <img src="assets/images/logo.PNG" alt="Shakti Bites" class="img-fluid">
                            <h1 class="auth-brand-text">Welcome back</h1>
                            <p class="auth-brand-tagline">Sign in to your Shakti Bites account.</p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <!-- Email -->
                            <label class="auth-field-label" for="email">Email address</label>
                            <div class="auth-icon-input">
                                <input type="email" class="form-control" id="email" autocomplete="email" name="email"
                                       placeholder="Email address" required autofocus
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <i class="bi bi-envelope input-icon"></i>
                            </div>

                            <!-- Password -->
                            <label class="auth-field-label" for="password">Password</label>
                            <div class="auth-icon-input auth-password-wrapper">
                                <input type="password" class="form-control" id="password" autocomplete="current-password" name="password"
                                       placeholder="Password" required>
                                <i class="bi bi-lock input-icon"></i>
                                <button type="button" class="auth-toggle-password" data-target="password" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            <!-- Options -->
                            <div class="auth-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember" style="font-size:13px; color:var(--text-muted); cursor:pointer;">
                                        Remember me
                                    </label>
                                </div>
                                <a href="forgot-password">Forgot Password?</a>
                            </div>

                            <!-- Submit -->
                            <button type="submit" name="login" class="auth-btn auth-btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        </form>

                        <!-- Footer -->
                        <p class="auth-footer-text">
                            Don't have an account? <a href="register">Create Account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.auth-toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
});
</script>
</body>
</html>
