<?php
/**
 * The storefront catalogue is kept in one place so listing, cart and product
 * pages always show the same product, price and product-specific details.
 *
 * Single products use ids 1-3 and combo packs use ids 4-6. The ids match the
 * `products` table primary keys, so a cart line item can be written to
 * `order_items` with the same id it was ordered from.
 */

/**
 * Single flavour products. Ids here are also the slugs served by product.php,
 * so this list must stay limited to the individual boxes.
 */
function shakti_catalog(): array
{
    return [
        1 => [
            'id' => 1, 'type' => 'single', 'sku' => 'SB-PROD-001', 'category' => 'Energy Bites',
            'name' => 'Peanut Jaggery Power Bites', 'price' => 249, 'image' => 'Peanut-product.png',
            'slug' => 'peanut-jaggery-power-bites',
            'label' => 'Everyday Energy', 'label_class' => 'label-everyday', 'accent' => 'peanut',
            'short' => 'Instant energy with no sugar crash.',
            'description' => 'A satisfying blend of roasted peanuts, jaggery and dates that gives you clean, steady energy. Every bite is made for busy days, workouts and guilt-free chai-time snacking.',
            'ingredients' => ['Roasted peanuts', 'Natural jaggery', 'Dates', 'Pea protein', 'Desi ghee'],
            'benefits' => ['10g protein per laddoo', 'No refined sugar', 'Naturally energising', 'Made in India'],
            'occasion' => ['Pre & post workout', '4 PM energy boost', 'Travel-friendly snack'],
            'review' => 'Perfect for my workout — energy crash bilkul nahi hota.',
        ],
        2 => [
            'id' => 2, 'type' => 'single', 'sku' => 'SB-PROD-002', 'category' => 'Energy Bites',
            'name' => 'Almond Cacao Power Bites', 'price' => 279, 'image' => 'Almond-product.png',
            'slug' => 'almond-cacao-power-bites',
            'label' => 'Best Seller', 'label_class' => 'label-bestseller', 'accent' => 'cacao',
            'short' => 'Chocolatey protein, made with real ingredients.',
            'description' => 'Rich almonds and real cacao make this a chocolate snack you can feel good about. Naturally sweetened with dates, it satisfies cravings while keeping your energy steady.',
            'ingredients' => ['Almonds', 'Dates', 'Pea protein', 'Raw cacao', 'Desi ghee'],
            'benefits' => ['10g protein per laddoo', 'No artificial sweeteners', 'Real cacao goodness', 'No refined sugar'],
            'occasion' => ['Gym fuel', 'Office snack', 'Healthy dessert alternative'],
            'review' => 'Chocolate craving sorted, without feeling heavy afterwards.',
        ],
        3 => [
            'id' => 3, 'type' => 'single', 'sku' => 'SB-PROD-003', 'category' => 'Energy Bites',
            'name' => 'Dry Fruit Cardamom Bites', 'price' => 299, 'image' => 'Dryfruit-product.png',
            'slug' => 'dry-fruit-cardamom-bites',
            'label' => 'Premium Pick', 'label_class' => 'label-premium', 'accent' => 'dryfruit',
            'short' => 'Royal dry fruits and cardamom in every bite.',
            'description' => 'Premium cashews, almonds and aromatic cardamom come together for a rich traditional laddoo with modern nutrition. A clean, indulgent pick-me-up for any time of day.',
            'ingredients' => ['Almonds', 'Cashews', 'Cardamom', 'Dates', 'Pea protein'],
            'benefits' => ['10g protein per laddoo', 'No preservatives', 'Rich dry fruits', 'No refined sugar'],
            'occasion' => ['Family snacking', 'Festive gifting', 'On-the-go energy'],
            'review' => 'Premium taste, clean ingredients and very filling.',
        ],
    ];
}

/**
 * Multi-box combo packs. Prices and copy here are the same values the combo
 * page displays, so the amount a customer sees is the amount that is charged.
 */
function shakti_combo_catalog(): array
{
    return [
        4 => [
            'id' => 4, 'type' => 'combo', 'sku' => 'SB-COMBO-001', 'category' => 'Combo Packs',
            'name' => '2 Box Combo', 'price' => 549, 'image' => 'Combo-product-3.png',
            'slug' => 'two-box-combo',
            'label' => 'Good to Start', 'label_class' => 'label-everyday', 'accent' => 'peanut',
            'tag' => 'Good to Start', 'tone' => 'starter', 'alt' => 'Two box snack combo',
            'short' => 'Two everyday boxes of clean protein energy in one combo.',
            'description' => 'A two box starter combo of Peanut Jaggery Power Bites and Almond Cacao Power Bites at combo pricing.',
            'saving' => 50, 'boxes' => 2, 'includes' => [1, 2],
            'benefits' => ['2 full boxes', 'Two different flavours', 'Same clean ingredients', 'No refined sugar'],
            'ingredients' => ['Roasted peanuts', 'Almonds', 'Natural jaggery', 'Dates', 'Pea protein'],
        ],
        5 => [
            'id' => 5, 'type' => 'combo', 'sku' => 'SB-COMBO-002', 'category' => 'Combo Packs',
            'name' => '3 Box Combo', 'price' => 799, 'image' => 'Combo-product-3.png',
            'slug' => 'three-box-combo',
            'label' => 'Most Popular', 'label_class' => 'label-bestseller', 'accent' => 'cacao',
            'tag' => 'Most Popular', 'tone' => 'popular', 'alt' => 'Three flavour combo of Shakti Bites',
            'short' => 'All three flavours packed together - our most popular combo.',
            'description' => 'The full Shakti Bites range in one combo: Peanut Jaggery, Almond Cacao and Dry Fruit Cardamom Power Bites.',
            'saving' => 100, 'boxes' => 3, 'includes' => [1, 2, 3],
            'benefits' => ['All 3 flavours', '3 full boxes', 'Great for the family', 'No refined sugar'],
            'ingredients' => ['Roasted peanuts', 'Almonds', 'Cashews', 'Dates', 'Pea protein'],
        ],
        6 => [
            'id' => 6, 'type' => 'combo', 'sku' => 'SB-COMBO-003', 'category' => 'Combo Packs',
            'name' => '4 Box Combo', 'price' => 1199, 'image' => 'Combo-product-6.png',
            'slug' => 'four-box-combo',
            'label' => 'Best Value', 'label_class' => 'label-premium', 'accent' => 'dryfruit',
            'tag' => 'Best Value', 'tone' => 'value', 'alt' => 'Assorted Shakti Bites value combo',
            'short' => 'Stock up with four boxes and save the most.',
            'description' => 'Our biggest value combo with four boxes: Peanut Jaggery (2 boxes), Almond Cacao and Dry Fruit Cardamom Power Bites.',
            'saving' => 100, 'boxes' => 4, 'includes' => [1, 2, 3, 1],
            'benefits' => ['4 full boxes', 'Every flavour included', 'Best combo price', 'No refined sugar'],
            'ingredients' => ['Roasted peanuts', 'Almonds', 'Cashews', 'Cardamom', 'Pea protein'],
        ],
    ];
}

/**
 * Every item a customer can put in the cart: single boxes and combo packs.
 */
function shakti_orderable_catalog(): array
{
    return shakti_catalog() + shakti_combo_catalog();
}

function shakti_catalog_entry($id): ?array
{
    $catalog = shakti_orderable_catalog();
    $entry = $catalog[$id] ?? null;
    return is_array($entry) ? $entry : null;
}

/**
 * Trimmed catalogue used by the cart, navbar preview and order pipeline, where
 * only the name, price, image and kind of an item are needed.
 */
function shakti_cart_catalog(): array
{
    $items = [];
    foreach (shakti_orderable_catalog() as $id => $entry) {
        $items[$id] = [
            'name'  => (string) $entry['name'],
            'price' => (float) $entry['price'],
            'image' => (string) $entry['image'],
            'type'  => (string) $entry['type'],
            'slug'  => (string) $entry['slug'],
        ];
    }
    return $items;
}

/**
 * Catalogue rows used to keep the `products` table in sync, so every cart item
 * has a matching product row that `order_items.product_id` can reference.
 */
function shakti_catalog_product_rows(): array
{
    $rows = [];
    foreach (shakti_orderable_catalog() as $id => $entry) {
        $rows[$id] = [
            'id' => (int) $id,
            'name' => (string) $entry['name'],
            'description' => (string) ($entry['description'] ?? ''),
            'price' => (float) $entry['price'],
            'image' => (string) $entry['image'],
            'label' => (string) ($entry['label'] ?? ''),
            'label_class' => (string) ($entry['label_class'] ?? ''),
            'category' => (string) ($entry['category'] ?? ''),
            'sku' => (string) $entry['sku'],
            'stock' => 100,
        ];
    }
    return $rows;
}