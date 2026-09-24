<?php
/**
 * The storefront catalogue is kept in one place so listing, cart and product
 * pages always show the same product, price and product-specific details.
 */
function shakti_catalog(): array
{
    return [
        1 => [
            'name' => 'Peanut Jaggery Power Bites', 'price' => 249, 'image' => 'product1.PNG',
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
            'name' => 'Almond Cacao Power Bites', 'price' => 279, 'image' => 'product2.PNG',
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
            'name' => 'Dry Fruit Cardamom Bites', 'price' => 299, 'image' => 'product3.PNG',
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
