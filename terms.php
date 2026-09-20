<?php
$page_title = 'Terms & Conditions';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? 'Shakti Bites - ' . $page_title : 'Terms & Conditions - Shakti Bites'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=20260920-profile-fix">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section class="auth-hero">
    <div class="container">
        <h1 class="auth-hero-title">Terms & Conditions</h1>
        <p class="auth-hero-sub">Please read these terms carefully before using our services</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="auth-card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">1. Acceptance of Terms</h5>
                        <p class="text-muted small mb-4">By accessing or using Shakti Bites services, you agree to be bound by these Terms and Conditions.</p>

                        <h5 class="fw-bold mb-3">2. Use of Services</h5>
                        <p class="text-muted small mb-4">You agree to use our products and services only for lawful purposes and in accordance with these terms.</p>

                        <h5 class="fw-bold mb-3">3. Product Information</h5>
                        <p class="text-muted small mb-4">We strive to provide accurate product information. However, we do not warrant that product descriptions or pricing is accurate or complete.</p>

                        <h5 class="fw-bold mb-3">4. Orders & Payment</h5>
                        <p class="text-muted small mb-4">All orders are subject to acceptance and availability. We reserve the right to refuse any order.</p>

                        <h5 class="fw-bold mb-3">5. Limitation of Liability</h5>
                        <p class="text-muted small mb-4">Shakti Bites shall not be liable for any indirect, incidental, special, or consequential damages.</p>

                        <div class="text-center mt-4">
                            <a href="register" class="auth-btn auth-btn-primary" style="max-width: 250px; margin: 0 auto;">
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
