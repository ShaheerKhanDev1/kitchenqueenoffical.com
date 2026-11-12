<?php
$pageTitle = 'Live Counter · Kitchen Queen';
$pageDescription = 'Live kitchen counter.';
$pageId = 'live-counter';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">Live from the Counter</h1>
  <p class="kq-page-sub"><span class="kq-live-dot"></span> Real-time unique traffic and conversions from our Karachi kitchen.</p>

  <div class="kq-stats kq-stats-4">
    <div class="kq-stat-card"><i class="fa-solid fa-users"></i><div class="kq-stat-num" data-live="visits">0</div><div class="kq-stat-label">Total Traffic (Visits)</div></div>
    <div class="kq-stat-card"><i class="fa-solid fa-bag-shopping"></i><div class="kq-stat-num" data-live="orders">0</div><div class="kq-stat-label">Total Conversions (Orders)</div></div>
    <div class="kq-stat-card"><i class="fa-solid fa-person-walking-arrow-right"></i><div class="kq-stat-num" data-live="bouncers">0</div><div class="kq-stat-label">Bouncers / Browsers</div></div>
    <div class="kq-stat-card"><i class="fa-solid fa-percent"></i><div class="kq-stat-num" data-live="rate">0%</div><div class="kq-stat-label">Conversion Rate</div></div>
  </div>

  <div class="kq-section-head"><h2><i class="fa-solid fa-tower-broadcast"></i> Live Order Feed</h2></div>
  <div class="kq-info-card"><div id="kqFeed" class="kq-live-feed"></div></div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
