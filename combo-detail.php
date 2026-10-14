<?php
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/combo-builder.php';
$plans = shakti_builder_plans();
$builderBoxes = isset($_GET['boxes']) ? filter_input(INPUT_GET, 'boxes', FILTER_VALIDATE_INT) : 3;
if (!$builderBoxes || !isset($plans[$builderBoxes])) {
    http_response_code(404);
    $page_title = 'Combo not found';
    include 'includes/header.php';
    echo '<main class="container py-5"><h1>Combo not found</h1><p>Please choose a 3, 6 or 9 box combo.</p><a href="combo">View all combos</a></main>';
    include 'includes/footer.php';
    exit;
}
$builderPlan = $plans[$builderBoxes];
$page_title = 'Build Your ' . $builderPlan['title'] . ' Combo';
include 'includes/header.php';
if (empty($_SESSION['combo_token'])) $_SESSION['combo_token'] = bin2hex(random_bytes(32));
?>
<main class="combo-detail-page">
  <div class="container">
    <nav class="builder-breadcrumb" aria-label="Breadcrumb"><a href="combo">Combos</a><span aria-hidden="true"> / </span><span><?php echo $builderBoxes; ?> Box Combo</span></nav>
    <?php include 'includes/combo-builder-form.php'; ?>
  </div>
</main>
<script src="assets/js/combo-builder.js?v=20261008-detail"></script>
<?php include 'includes/footer.php'; ?>
