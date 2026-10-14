<?php
session_start();
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/combo-builder.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!isset($_SESSION['combo_token']) || !hash_equals($_SESSION['combo_token'], (string) ($_POST['token'] ?? ''))) { http_response_code(403); exit('Please refresh the combo page and try again.'); }
try {
    $boxes = filter_input(INPUT_POST, 'boxes', FILTER_VALIDATE_INT);
    $selection = $_POST['flavours'] ?? [];
    if (!$boxes || !is_array($selection)) throw new InvalidArgumentException('Invalid combo.');
    $validated = [];
    foreach ($selection as $id => $qty) {
        $value = filter_var($qty, FILTER_VALIDATE_INT);
        if ($value === false) throw new InvalidArgumentException('Invalid quantity.');
        $validated[$id] = $value;
    }
    $combo = shakti_build_combo($boxes, $validated);
    $customId = null;
    foreach ($_SESSION['custom_combos'] ?? [] as $id => $existing) {
        if ($existing['boxes'] === $boxes && $existing['selection'] === $combo['selection']) { $customId = $id; break; }
    }
    if ($customId === null) {
        $customId = max(100000, (int) ($_SESSION['next_combo_id'] ?? 100000));
        $_SESSION['next_combo_id'] = $customId + 1;
        $_SESSION['custom_combos'][$customId] = ['boxes'=>$boxes,'selection'=>$combo['selection']];
    }
    $_SESSION['cart'][$customId] = min(99, (int) ($_SESSION['cart'][$customId] ?? 0) + 1);
    header('Location: cart'); exit;
} catch (InvalidArgumentException $e) { http_response_code(422); echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'); }
