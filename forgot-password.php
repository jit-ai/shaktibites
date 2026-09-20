<?php
$page_title = 'Forgot Password';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? 'Shakti Bites - ' . $page_title : 'Forgot Password - Shakti Bites'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=20260920-profile-fix">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section class="auth-hero">
    <div class="container">
        <h1 class="auth-hero-title">Reset Password</h1>
        <p class="auth-hero-sub">Enter your email and we'll send you a reset link</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                <div class="auth-card">
                    <div class="card-body text-center">
                        <div class="auth-brand">
                            <img src="assets/images/logo.PNG" alt="Shakti Bites" class="img-fluid">
                            <div class="auth-brand-text">Shakti Bites</div>
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2 text-start" role="alert">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>Enter the email associated with your account and we'll send you a link to reset your password.</div>
                        </div>

                        <form method="POST" action="">
                            <div class="auth-icon-input">
                                <input type="email" class="form-control" placeholder="Email address" required autofocus>
                                <i class="bi bi-envelope input-icon"></i>
                            </div>

                            <button type="submit" class="auth-btn auth-btn-primary">
                                <i class="bi bi-send"></i> Send Reset Link
                            </button>
                        </form>

                        <p class="auth-footer-text" style="margin-top: 20px;">
                            <a href="login"><i class="bi bi-arrow-left"></i> Back to Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
