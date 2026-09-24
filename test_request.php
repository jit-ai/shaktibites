<?php
$_GET['id'] = '1';
require_once __DIR__ . '/includes/catalog.php';
$catalog = shakti_catalog();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || !isset($catalog[$id])) { $id = 1; }
$product = $catalog[$id];

echo "ID: " . $id . "\n";
echo "accent: " . $product['accent'] . "\n";
echo "short: " . $product['short'] . "\n";
echo "benefits: " . implode(', ', $product['benefits']) . "\n";

if (isset($product['accent'])) {
    echo "main class: flavour-page flavour-page--" . htmlspecialchars($product['accent']) . "\n";
} else {
    echo "accent NOT SET\n";
}

if (isset($product['short'])) {
    echo "short IS SET\n";
} else {
    echo "short NOT SET\n";
}

if (isset($product['benefits'])) {
    echo "benefits IS SET\n";
} else {
    echo "benefits NOT SET\n";
}
