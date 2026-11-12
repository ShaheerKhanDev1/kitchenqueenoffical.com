<?php
$pageTitle = 'Weekly Menu · Kitchen Queen';
$pageDescription = "This week's home-cooked menu.";
$pageId = 'menu';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <div class="kq-menu-hero">
    <h1 class="kq-page-title">The Weekly Menu</h1>
    <p class="kq-page-sub">A fresh rotation every single day — cooked in the morning, delivered by lunch.</p>
  </div>

  <div class="kq-section-head">
    <h2><i class="fa-solid fa-calendar-week"></i> This Week in the Kitchen</h2>
    <button class="kq-btn kq-btn-outline" id="kqPrint"><i class="fa-solid fa-print"></i> Print Brochure</button>
  </div>

  <div id="kqWeek"></div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
