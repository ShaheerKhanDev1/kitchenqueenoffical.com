<?php
$pageTitle = 'Reviews · Kitchen Queen';
$pageDescription = 'Customer reviews.';
$pageId = 'reviews';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main rv-page">
  <h1 class="kq-page-title">Customer Reviews</h1>
  <p class="kq-page-sub">Real orders. Real plates. Honest words from Karachi tables.</p>

  <section class="rv-hero" aria-label="Overall rating">
    <div class="rv-score">
      <div class="rv-score-num" id="kqRvScore">4.8</div>
      <div class="rv-score-stars" id="kqRvScoreStars" aria-hidden="true"></div>
      <p class="rv-score-meta" id="kqRvCount">Based on live customer reviews</p>
    </div>
    <div class="rv-bars" id="kqRvBars"></div>
    <div class="rv-trust">
      <i class="fa-solid fa-shield-halved"></i>
      <span>100% Verified Customer Reviews</span>
    </div>
  </section>

  <section class="rv-write-wrap" id="write">
    <form class="kq-form-card rv-form" id="kqReviewForm" novalidate>
      <div class="rv-form-head">
        <h2>Write a Review</h2>
        <p>Share how your order tasted — and add a photo if something was off, or if it was perfect.</p>
      </div>

      <div class="rv-gate" id="kqReviewGate">
        <div class="rv-gate-card">
          <span class="rv-gate-ico"><i class="fa-solid fa-lock"></i></span>
          <h3>Please Sign In to leave a review</h3>
          <p>Reviews are only accepted from signed-in customers so every rating stays verified.</p>
          <button type="button" class="kq-btn" id="kqRvSignIn"><i class="fa-solid fa-user"></i> Sign In</button>
        </div>
      </div>

      <div class="kq-form-2col">
        <div class="kq-field">
          <label for="rvName"><i class="fa-solid fa-user"></i> CUSTOMER NAME</label>
          <input type="text" id="rvName" data-required placeholder="Your name" autocomplete="name">
          <span class="kq-req-badge">Please enter your name</span>
        </div>
        <div class="kq-field">
          <label for="rvOrder"><i class="fa-solid fa-receipt"></i> ORDER ID</label>
          <input type="text" id="rvOrder" data-required placeholder="KQ-184392" autocomplete="off">
          <span class="kq-req-badge">Enter your order ID</span>
        </div>
      </div>

      <div class="kq-field">
        <label for="rvDish"><i class="fa-solid fa-bowl-food"></i> DISH ORDERED</label>
        <select id="rvDish" data-required>
          <option value="">Select the item you ordered</option>
        </select>
        <span class="kq-req-badge">Please select a dish</span>
      </div>

      <div class="kq-field">
        <label><i class="fa-solid fa-star"></i> YOUR RATING</label>
        <div class="rv-star-pick" id="kqRvStars" role="radiogroup" aria-label="Star rating">
          <button type="button" class="rv-star-btn" data-star="1" aria-label="1 star"><i class="fa-solid fa-star"></i></button>
          <button type="button" class="rv-star-btn" data-star="2" aria-label="2 stars"><i class="fa-solid fa-star"></i></button>
          <button type="button" class="rv-star-btn" data-star="3" aria-label="3 stars"><i class="fa-solid fa-star"></i></button>
          <button type="button" class="rv-star-btn" data-star="4" aria-label="4 stars"><i class="fa-solid fa-star"></i></button>
          <button type="button" class="rv-star-btn" data-star="5" aria-label="5 stars"><i class="fa-solid fa-star"></i></button>
        </div>
        <input type="hidden" id="rvRating" value="">
        <span class="kq-req-badge" id="rvRatingErr">Please tap a star rating</span>
      </div>

      <div class="kq-field">
        <label for="rvText"><i class="fa-solid fa-comment-dots"></i> YOUR REVIEW</label>
        <textarea id="rvText" rows="4" data-required placeholder="How was the taste, packing and delivery? If there was a problem, tell us clearly."></textarea>
        <span class="kq-req-badge">Please write your review</span>
      </div>

      <div class="kq-field">
        <label><i class="fa-solid fa-camera"></i> PHOTO (OPTIONAL)</label>
        <label class="rv-upload" for="rvPhotos">
          <input type="file" id="rvPhotos" accept="image/*" multiple>
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <b>Upload food or issue photo</b>
          <small>Up to 3 images · JPG or PNG · helpful if packing or quality was wrong</small>
        </label>
        <div class="rv-previews" id="rvPreviews"></div>
      </div>

      <button class="kq-btn rv-submit" type="submit"><i class="fa-solid fa-paper-plane"></i> Submit Review</button>
    </form>
  </section>

  <section class="rv-list-wrap">
    <div class="rv-toolbar">
      <div class="rv-filters">
        <label class="rv-select-wrap">
          <span>Sort by</span>
          <select id="kqRvSort">
            <option value="latest">Latest First</option>
            <option value="high">Highest Rated</option>
            <option value="low">Lowest Rated</option>
          </select>
        </label>
        <label class="rv-select-wrap">
          <span>Dish</span>
          <select id="kqRvDishFilter">
            <option value="">All dishes</option>
          </select>
        </label>
        <label class="rv-check">
          <input type="checkbox" id="kqRvPhotosOnly">
          <span>With Photos Only</span>
        </label>
      </div>
      <p class="rv-showing" id="kqRvShowing"></p>
    </div>
    <div class="rv-list" id="kqRvList"></div>
  </section>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
