<?php
/**
 * Admin navigation. $currentPage is resolved once in header.php so the active
 * item can never disagree with the topbar section label.
 */
$adminNavGroups = [
    [
        'label' => 'Overview',
        'items' => [
            ['page' => 'dashboard', 'href' => 'dashboard', 'icon' => 'bi-speedometer2', 'text' => 'Dashboard'],
        ],
    ],
    [
        'label' => 'Sales',
        'items' => [
            ['page' => 'orders',        'href' => 'orders',        'icon' => 'bi-receipt', 'text' => 'Orders'],
            ['page' => 'order-details', 'href' => 'orders',        'icon' => 'bi-list-check', 'text' => 'Order Details'],
        ],
    ],
    [
        'label' => 'Catalog',
        'items' => [
            ['page' => 'products',      'href' => 'products',      'icon' => 'bi-box',  'text' => 'Products'],
            ['page' => 'product-add',   'href' => 'products',      'icon' => 'bi-plus-square', 'text' => 'Add Product'],
            ['page' => 'product-edit',  'href' => 'products',      'icon' => 'bi-pencil-square', 'text' => 'Edit Product'],
            ['page' => 'categories',    'href' => 'categories',    'icon' => 'bi-tag',  'text' => 'Categories'],
        ],
    ],
    [
        'label' => 'People',
        'items' => [
            ['page' => 'users', 'href' => 'users', 'icon' => 'bi-people', 'text' => 'Users'],
        ],
    ],
];
?>
<!-- Sidebar -->
<div id="sidebar-wrapper">
    <div class="sidebar-heading">
        <img src="../../assets/images/logo.PNG" alt="Shakti Bites">
        <h4>Admin Panel</h4>
    </div>

    <?php foreach ($adminNavGroups as $group): ?>
        <div class="sidebar-section"><?php echo htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="list-group list-group-flush">
            <?php foreach ($group['items'] as $item): ?>
                <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="list-group-item list-group-item-action<?php echo $currentPage === $item['page'] ? ' active' : ''; ?>">
                    <i class="bi <?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="sidebar-foot">
        <a href="../index">
            <i class="bi bi-arrow-left-circle" aria-hidden="true"></i> Back to website
        </a>
    </div>
</div>
<!-- /#sidebar-wrapper -->