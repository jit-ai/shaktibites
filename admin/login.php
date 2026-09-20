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

if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header('Location: dashboard');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1 AND is_active = 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['is_admin'] = $admin['is_admin'];
            
            header('Location: dashboard');
            exit;
        } else {
            $error = 'Invalid admin credentials or account is inactive';
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
    <title>Admin Login - Shakti Bites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .login-card {
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin: 2rem auto;
        }
        .login-header {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: var(--white);
            padding: 2rem;
            text-align: center;
        }
        .login-header img {
            max-height: 50px;
            filter: brightness(0) invert(1);
            margin-bottom: 10px;
        }
        .login-header h3 {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .login-header p {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
            margin-top: 8px;
        }
        .login-body {
            padding: 2rem;
        }
        .login-body .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text);
        }
        .login-body .form-control {
            border: 2px solid rgba(0,0,0,0.08);
            border-radius: var(--radius-pill);
            padding: 12px 18px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .login-body .form-control:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 4px rgba(224, 123, 42, 0.12);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: var(--white);
            border: none;
            border-radius: var(--radius-pill);
            font-weight: 700;
            font-size: 1.05rem;
            padding: 14px;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, var(--orange-dark), var(--orange));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(224, 123, 42, 0.45);
            color: var(--white);
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.06);
        }
        .login-footer a {
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .login-footer a:hover {
            color: var(--orange-dark);
            text-decoration: underline;
        }
        .login-divider {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin: 1.2rem 0;
            position: relative;
            text-align: center;
        }
        .login-divider::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 1px;
            background: rgba(0,0,0,0.08);
        }
        .login-divider span {
            background: var(--white);
            padding: 0 12px;
            position: relative;
        }
    </style>
</head>
<body>
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="login-card" style="width: 100%; max-width: 420px;">
        <div class="login-header">
            <img src="../assets/images/logo.PNG" alt="Shakti Bites">
            <h3 class="mb-0">Admin Panel</h3>
            <p>Sign in to manage your store</p>
        </div>
        <div class="login-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="border-radius: var(--radius-card);"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" name="admin_login" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>
            
            <div class="login-footer">
                <p class="mb-2"><a href="../login"><i class="bi bi-person-circle me-1"></i> Customer Login</a></p>
                <p><a href="../register"><i class="bi bi-person-plus me-1"></i> Create Account</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
