<?php
$pageTitle = 'Kitchen Queen — Home-Cooked Desi Food Delivery in Karachi';
$pageDescription = 'Order authentic home-cooked Pakistani desi food in Karachi.';
$pageId = 'index';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">

  <div id="heroCarousel" class="carousel slide carousel-fade kq-hero" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="hover" data-bs-wrap="true">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="kq-hero-slide">
          <img src="images/banners/kq-combo-full.webp" alt="Kitchen Queen family meal combos">
        </div>
      </div>
      <div class="carousel-item">
        <div class="kq-hero-slide">
          <img src="images/banners/kq-wow-deals-full.webp" alt="Kitchen Queen wow deals starting from Rs. 399">
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Previous</span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Next</span></button>
  </div>

  <div class="kq-cats" id="kqCats"></div>

  <div class="kq-section-head">
    <h2><i class="fa-solid fa-bowl-food"></i> Today's Kitchen</h2>
    <a class="kq-read-more" href="menu.php">Weekly menu <i class="fa-solid fa-arrow-right"></i></a>
  </div>

  <div class="kq-grid" id="kqGrid"></div>

</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
