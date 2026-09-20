<?php
session_start();
include 'includes/config.php';

$page_title = 'Register';

// Initialize database connection
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
        header('Location: setup');
        exit;
    } else {
        die("Database connection error. Please contact administrator.");
    }
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit;
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $agree_terms = isset($_POST['agree_terms']) ? true : false;

    $errors = [];

    if (empty($name) || strlen(trim($name)) < 2) {
        $errors[] = 'Please enter your full name';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }

    if (empty($confirm_password)) {
        $errors[] = 'Please confirm your password';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    if (!$agree_terms) {
        $errors[] = 'You must agree to the Terms & Conditions';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered. Please use a different email or login.';
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $hashed_password, $phone, $address])) {
            $user_id = $pdo->lastInsertId();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['is_admin'] = 0;

            header('Location: ./');
            exit;
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }

    $old_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Shakti Bites</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/style.css?v=20260920-profile-fix">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<!-- Auth Hero Header -->
<section class="auth-hero">
    <div class="container">
        <h1 class="auth-hero-title">Join Shakti Bites</h1>
        <p class="auth-hero-sub">Create your account and start your healthy snacking journey</p>
    </div>
</section>

<!-- Register Section -->
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-5">
                <div class="auth-card">
                    <div class="card-body">
                        <!-- Brand -->
                        <div class="auth-brand">
                            <img src="assets/images/logo.PNG" alt="Shakti Bites" class="img-fluid">
                            <div class="auth-brand-text">Shakti Bites</div>
                            <p class="auth-brand-tagline">Fuel Your Day, Naturally</p>
                        </div>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                                <div>
                                    <ul class="mb-0 ps-3">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="registerForm">
                            <!-- Full Name -->
                            <div class="auth-icon-input">
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Full Name" required autofocus
                                       value="<?php echo isset($old_data['name']) ? htmlspecialchars($old_data['name']) : ''; ?>">
                                <i class="bi bi-person input-icon"></i>
                            </div>

                            <!-- Email -->
                            <div class="auth-icon-input">
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Email address" required
                                       value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>">
                                <i class="bi bi-envelope input-icon"></i>
                            </div>

                            <!-- Phone -->
                            <div class="auth-icon-input">
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       placeholder="Phone Number (Optional)"
                                       value="<?php echo isset($old_data['phone']) ? htmlspecialchars($old_data['phone']) : ''; ?>">
                                <i class="bi bi-telephone input-icon"></i>
                            </div>

                            <!-- Address -->
                            <div class="auth-icon-input">
                                <textarea class="form-control" id="address" name="address" rows="2"
                                          placeholder="Address (Optional)"><?php echo isset($old_data['address']) ? htmlspecialchars($old_data['address']) : ''; ?></textarea>
                                <i class="bi bi-geo-alt input-icon" style="top: 24px;"></i>
                            </div>

                            <!-- Password -->
                            <div class="auth-icon-input auth-password-wrapper">
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Create Password" required>
                                <i class="bi bi-lock input-icon"></i>
                                <button type="button" class="auth-toggle-password" data-target="password" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            <!-- Password Strength -->
                            <div class="auth-password-strength" id="passwordStrength">
                                <div class="auth-strength-bar" id="strength1"></div>
                                <div class="auth-strength-bar" id="strength2"></div>
                                <div class="auth-strength-bar" id="strength3"></div>
                                <div class="auth-strength-bar" id="strength4"></div>
                            </div>
                            <div class="auth-strength-text" id="strengthText" style="color: var(--text-muted);"></div>

                            <!-- Confirm Password -->
                            <div class="auth-icon-input auth-password-wrapper">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                       placeholder="Confirm Password" required>
                                <i class="bi bi-lock-fill input-icon"></i>
                                <button type="button" class="auth-toggle-password" data-target="confirm_password" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            <!-- Terms -->
                            <div class="auth-terms form-check">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms" style="cursor: pointer;">
                                    I agree to the <a href="terms">Terms & Conditions</a> and <a href="privacy">Privacy Policy</a>
                                </label>
                            </div>

                            <!-- Submit -->
                            <button type="submit" name="register" class="auth-btn auth-btn-primary" style="margin-top: 20px;">
                                <i class="bi bi-person-plus"></i> Create Account
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="auth-divider">
                            <span>or sign up with</span>
                        </div>

                        <!-- Social Signup -->
                        <div class="auth-social">
                            <a href="#" class="auth-social-btn google">
                                <i class="bi bi-google"></i> Google
                            </a>
                            <a href="#" class="auth-social-btn facebook">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                        </div>

                        <!-- Footer -->
                        <p class="auth-footer-text">
                            Already have an account? <a href="login">Sign In</a>
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

    // Password strength meter
    var passwordInput = document.getElementById('password');
    var strengthBars = [
        document.getElementById('strength1'),
        document.getElementById('strength2'),
        document.getElementById('strength3'),
        document.getElementById('strength4')
    ];
    var strengthText = document.getElementById('strengthText');

    passwordInput.addEventListener('input', function() {
        var val = this.value;
        var score = 0;

        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        var cappedScore = Math.min(score, 4);
        var labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        var colors = ['', 'var(--red-x)', 'var(--orange-light)', 'var(--orange)', 'var(--green)'];
        var classes = ['', 'weak', 'medium', 'medium', 'strong'];

        strengthBars.forEach(function(bar, index) {
            bar.className = 'auth-strength-bar';
            if (index < cappedScore && val.length > 0) {
                bar.classList.add(classes[cappedScore]);
            }
        });

        if (val.length === 0) {
            strengthText.textContent = '';
        } else {
            strengthText.textContent = labels[cappedScore] || '';
            strengthText.style.color = colors[cappedScore] || 'var(--text-muted)';
        }
    });
});
</script>
</body>
</html>
