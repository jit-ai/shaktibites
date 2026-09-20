<?php
$page_title = 'Privacy Policy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? 'Shakti Bites - ' . $page_title : 'Privacy Policy - Shakti Bites'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section class="auth-hero">
    <div class="container">
        <h1 class="auth-hero-title">Privacy Policy</h1>
        <p class="auth-hero-sub">How we collect, use, and protect your personal information</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="auth-card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">1. Information We Collect</h5>
                        <p class="text-muted small mb-4">We collect personal information such as name, email, phone number, and address when you register or place an order.</p>

                        <h5 class="fw-bold mb-3">2. How We Use Your Information</h5>
                        <p class="text-muted small mb-4">Your information is used to process orders, improve our services, and communicate with you about products and offers.</p>

                        <h5 class="fw-bold mb-3">3. Data Protection</h5>
                        <p class="text-muted small mb-4">We implement security measures to protect your personal data from unauthorized access or disclosure.</p>

                        <h5 class="fw-bold mb-3">4. Cookies</h5>
                        <p class="text-muted small mb-4">We use cookies to enhance your browsing experience. You can disable cookies in your browser settings.</p>

                        <h5 class="fw-bold mb-3">5. Third-Party Services</h5>
                        <p class="text-muted small mb-4">We do not sell or share your personal information with third parties except as necessary to fulfill your order.</p>

                        <div class="text-center mt-4">
                            <a href="register.php" class="auth-btn auth-btn-primary" style="max-width: 250px; margin: 0 auto;">
                                <i class="bi bi-arrow-left"></i> Back to Register
                            </a>
                        </div>
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
