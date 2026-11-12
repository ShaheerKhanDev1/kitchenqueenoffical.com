<?php
$pageTitle = 'Kitchen Journal · Kitchen Queen';
$pageDescription = 'Kitchen Queen journal.';
$pageId = 'blog';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">The Kitchen Journal</h1>
  <p class="kq-page-sub">Notes from our chefs on the food we grew up eating.</p>
  <div class="kq-blog-grid" id="kqBlogGrid"></div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
