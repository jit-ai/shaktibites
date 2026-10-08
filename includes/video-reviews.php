<?php
$videoReviewGroups = [
    'peanut' => [
        ['PRSm9W9EwPY', 'Aniket', 'Taste is really good - much better compared to typical protein powder.'],
        ['Vabcbhmta6Q', 'Pooja', 'Yummy taste, loved by her daughter, and a better choice than everyday junk food.'],
        ['KboqetIn2d4', 'Vinay', 'Taste is absolutely shandaar! Something completely new for the market. Feels like it can really work.'],
    ],
    'cacao' => [
        ['NEFFqzZ16do', 'Ayush', 'Taste is amazing — protein-rich, healthy & something you can consume daily.'],
        ['N7MUtU-XzsU', 'Sourabh', '10/10 — Day & Night kabhi bhi kha sakte ho!'],
        ['qj7kdaccKAY', 'Gym Guy', 'Shakti Bites combines great taste with high protein — definitely worth it!'],
    ],
    'dryfruit' => [
        ['AVopTGPg7ks', 'Sourabh', 'Sourabh took the Shakti Bites blind taste test and liked the Dry Fruit flavour the most, though the other flavours tasted great too 😍'],
        ['HY5XZhLGzIA', 'Vandana', 'Amazing taste, a solid 9/10, and something everyone can enjoy— from kids to grandparents.'],
        ['aCrxkoBtMns', 'Arnika', 'It tastes so good, I want to take it to school and share it with my friends!'],
    ],
    'combo' => [
        ['FlfJcz9_2Qs', 'Amit Awasthi', 'The taste is incredible—the more people eat it, the more physiques we\'ll build.'],
        ['l6f82lpscgQ', 'Sourabh', '10/10 — Day & Night kabhi bhi kha sakte ho!'],
        ['HY5XZhLGzIA', 'Vandana', 'Amazing taste, a solid 9/10, and something everyone can enjoy— from kids to grandparents.'],
        ['PRSm9W9EwPY', 'Aniket', 'Taste is really good - much better compared to typical protein powder.'],
    ],
];
$videoReviews = $videoReviewGroups[$videoReviewGroup ?? 'peanut'] ?? $videoReviewGroups['peanut'];
?>
<section class="reviews-section" aria-labelledby="video-reviews-title">
  <div class="container">
    <h2 class="section-title text-center" id="video-reviews-title">Real Customers. Real Results.</h2>
    <p class="section-sub text-center">Hear what our customers have to say.</p>
    <div class="reviews-slider" id="reviewsSlider" role="region" aria-roledescription="carousel" aria-label="Customer video testimonials">
      <div class="reviews-track" id="reviewsTrack" tabindex="0" aria-label="Video reviews. Swipe or use the arrow keys to browse.">
        <?php foreach ($videoReviews as $reviewIndex => [$videoId, $reviewerName, $reviewQuote]): ?>
          <article class="review-card review-slide" aria-label="<?php echo ($reviewIndex + 1) . ' of ' . count($videoReviews) . ': ' . htmlspecialchars($reviewerName, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="review-card-inner">
              <div class="reviewer-avatar video-avatar">
                <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId, ENT_QUOTES, 'UTF-8'); ?>?rel=0&amp;playsinline=1" title="<?php echo htmlspecialchars($reviewerName . "'s Shakti Bites video review", ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
              </div>
              <p class="review-text">&ldquo;<?php echo htmlspecialchars($reviewQuote, ENT_QUOTES, 'UTF-8'); ?>&rdquo;</p>
              <div class="reviewer-name"><?php echo htmlspecialchars($reviewerName, ENT_QUOTES, 'UTF-8'); ?></div>
              <a class="review-video-link" href="https://youtube.com/shorts/<?php echo htmlspecialchars($videoId, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Watch on YouTube <span aria-hidden="true">&#8599;</span></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="reviews-nav">
        <button type="button" class="reviews-btn reviews-prev" id="reviewsPrev" aria-label="Previous reviews" aria-controls="reviewsTrack"><span aria-hidden="true">&#8592;</span></button>
        <div class="reviews-dots" id="reviewsDots" aria-label="Review pages"></div>
        <button type="button" class="reviews-btn reviews-next" id="reviewsNext" aria-label="Next reviews" aria-controls="reviewsTrack"><span aria-hidden="true">&#8594;</span></button>
      </div>
      <p class="reviews-status" id="reviewsStatus" role="status" aria-live="polite" aria-atomic="true"></p>
    </div>
  </div>
</section>
