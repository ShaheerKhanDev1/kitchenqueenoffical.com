<?php
$pageTitle = 'Meal Builder · Kitchen Queen';
$pageDescription = 'Build your thali.';
$pageId = 'meal-builder';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">Build Your Thali</h1>
  <p class="kq-page-sub">Four steps. Your combination. Cooked to order.</p>

  <div class="kq-builder">
    <div>
      <div class="kq-steps" id="kqSteps"></div>
      <div id="kqStepPanel"></div>
    </div>

    <aside class="kq-tray">
      <div class="kq-thali-plate"><div id="kqPlateCount"><b>0</b><small>OF 4</small></div></div>
      <div class="kq-tray-items" id="kqTrayItems"></div>
      <div class="kq-tray-total"><span>Thali total</span><span id="kqTrayTotal">Rs 0</span></div>
      <button class="kq-btn" id="kqTrayAdd"><i class="fa-solid fa-basket-shopping"></i> Add Thali to Basket</button>
    </aside>
  </div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
