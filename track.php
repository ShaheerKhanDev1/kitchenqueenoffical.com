<?php
$pageTitle = 'Track Order · Kitchen Queen';
$pageDescription = 'Live order tracking.';
$pageId = 'track';
require __DIR__ . "/includes/header.php";
?>
<main class="kq-main">
  <h1 class="kq-page-title">Track your order</h1>
  <p class="kq-page-sub">Live kitchen status. This page updates automatically.</p>
  <div style="display:flex;gap:8px;margin:12px 0 18px;flex-wrap:wrap">
    <input id="kqTrackInput" placeholder="Order ID e.g. KQ-6" style="flex:1;min-width:180px;border:1px solid #eee;border-radius:12px;padding:12px">
    <button class="kq-btn" type="button" id="kqTrackGo">Check</button>
  </div>
  <div id="kqTrack"></div>
</main>
<?php require __DIR__ . "/includes/footer.php"; ?>
