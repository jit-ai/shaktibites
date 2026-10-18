<?php
/** Editorial content supplied for the three product pages. */
function shakti_product_page_copy(int $id): array
{
    $storage = '30 days from packing. Store in an airtight container in a cool, dry place. Refrigerate in hot or humid weather.';
    $delivery = 'Free delivery on orders of ₹499 or more; ₹50 shipping below ₹499. COD is available for a ₹50 fee on a single-box order. Prepaid orders save ₹15 at the single-box tier, ₹30 at 3 boxes, ₹50 at 6 boxes and ₹75 at 9 boxes.';
    $sugar = 'No refined sugar. It has natural sugars from dates and jaggery.';
    $pages = [
        1 => [
            'headline' => 'Peanut Jaggery Protein Laddu – 10g Protein per Laddu',
            'subline' => 'Made with peanuts, dates and jaggery. No refined sugar. Ingredients you can recognise.',
            'ticks' => ['10g protein per laddu', 'No refined sugar', 'Sweetened with dates & jaggery', '6 laddus per box'],
            'comparison' => ['Often made with refined sugar', 'Often made with maida', 'Protein is usually low', 'Some use artificial sweeteners'],
            'advantages' => ['10g protein per laddu', 'No refined sugar', 'Sweetened with dates & jaggery', 'No artificial sweeteners'],
            'about' => 'Peanut Jaggery Power Bites are protein laddus made with peanuts, dates, jaggery, pea protein, sesame seeds and desi ghee. Each laddu has 10g of protein and no refined sugar. Each box has 6 laddus (210g). They are easy to carry and ready to eat, so they work for the morning, before or after a workout, at the office, or as a family snack.',
            'taste' => 'Nutty, mildly sweet from jaggery, soft with a light sesame crunch.',
            'allergens' => 'Contains peanuts, sesame and milk (ghee).',
            'title' => 'Peanut Protein Laddu – High Protein, 10g | Shakti Bites',
            'description' => 'Buy Peanut Jaggery protein laddu from Shakti Bites. High protein, 10g per laddu, made with peanuts, dates and jaggery. No refined sugar. ₹249.',
            'faqs' => [
                ['How much protein is in one laddu?', '10g.'], ['Does it contain sugar?', $sugar],
                ['What is it made of?', 'Peanuts, dates, jaggery, pea protein, sesame seeds and desi ghee.'],
                ['Is it safe for allergies?', 'No. It contains peanuts, sesame and milk.'],
                ['Can kids eat it?', 'Children who have no nut, sesame or milk allergy can enjoy it.'],
                ['Shelf life and storage?', $storage], ['Delivery and COD?', $delivery],
            ],
        ],
        2 => [
            'headline' => 'Almond Cacao Protein Laddu – 10g Protein, Real Cacao Taste',
            'subline' => 'A high protein chocolate laddu made with almonds, peanuts and dates. No refined sugar.',
            'ticks' => ['10g protein per laddu', 'No refined sugar', 'Made with almonds & real cacao', '6 laddus per box'],
            'comparison' => ['Often made with refined sugar', 'Often made with maida', 'Some use chocolate flavouring', 'Protein is usually low'],
            'advantages' => ['10g protein per laddu', 'Made with cocoa powder', 'No refined sugar', 'No artificial sweeteners'],
            'about' => 'Almond Cacao Power Bites are high protein chocolate laddus made with pea protein, almonds, peanuts, dates, jaggery, cocoa powder, cardamom and desi ghee. Each laddu has 10g of protein and no refined sugar. Each box has 6 laddus (210g). Enjoy them as a snack in the morning, before or after a workout, at the office, or as a family treat.',
            'taste' => 'Chocolatey and nutty, mildly sweet from dates and jaggery, with a light hint of cardamom.',
            'allergens' => 'Contains peanuts, almonds and milk (ghee).',
            'title' => 'Almond Cacao Protein Laddu – High Protein | Shakti Bites',
            'description' => 'Buy Almond Cacao protein laddu, a high protein chocolate snack. 10g protein per laddu, made with almonds and dates. No refined sugar. ₹299.',
            'faqs' => [
                ['How much protein is in one laddu?', 'About 12g per 35g laddu. We promise a minimum of 10g.'],
                ['Does it taste like chocolate?', 'It has a chocolatey taste from cocoa powder, but it is not sweet like milk chocolate.'],
                ['Does it contain sugar?', $sugar], ['Is it safe for allergies?', 'No. It contains peanuts, almonds and milk.'],
                ['Shelf life and storage?', $storage], ['Delivery and COD?', $delivery],
            ],
        ],
        3 => [
            'headline' => 'Dry Fruit Cardamom Protein Laddu – 10g Protein, Rich Dry Fruits',
            'subline' => 'Made with cashews, almonds, pistachios and cardamom. Rich, royal taste. No refined sugar.',
            'ticks' => ['10g protein per laddu', 'No refined sugar', 'Made with cashew, almond & pistachio', '6 laddus per box'],
            'comparison' => ['Often made with refined sugar', 'Often made with maida', 'Dry fruit content varies', 'Protein is usually low'],
            'advantages' => ['10g protein per laddu', 'Made with cashew, almond & pistachio', 'No refined sugar', 'No artificial preservatives'],
            'about' => 'Dry Fruit Cardamom Power Bites are protein laddus made with cashews, almonds, pistachios, dates, jaggery, pea protein, cardamom and desi ghee. Each laddu has 10g of protein and no refined sugar. Each box has 6 laddus (210g). Enjoy them as a rich snack at home, at the office, or as a festive gift.',
            'taste' => 'Rich and nutty, mildly sweet from dates and jaggery, with the aroma of cardamom.',
            'allergens' => 'Contains cashews, almonds, pistachios and milk (ghee).',
            'title' => 'Dry Fruit Protein Laddu – High Protein | Shakti Bites',
            'description' => 'Buy Dry Fruit Cardamom protein laddu from Shakti Bites. High protein, 10g per laddu, with cashew, almond and pistachio. No refined sugar. ₹379.',
            'faqs' => [
                ['How much protein is in one laddu?', '10g.'], ['Does it contain sugar?', $sugar],
                ['Is it safe for allergies?', 'No. It contains cashews, almonds, pistachios and milk.'],
                ['Can I gift it?', 'Yes. It is a good festive gift, and combos make it easy to send more than one box.'],
                ['Shelf life and storage?', $storage], ['Delivery and COD?', $delivery],
            ],
        ],
    ];
    return $pages[$id];
}
