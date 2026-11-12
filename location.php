<?php
$pageTitle = 'Location · Kitchen Queen';
$pageDescription = 'Delivery areas in Karachi.';
$pageId = 'location';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">Where We Deliver</h1>
  <p class="kq-page-sub">Hot food, 35–50 minutes, across most of Karachi.</p>

  <div class="kq-loc-grid">
    <div>
      <div class="kq-info-card">
        <div class="kq-info-row">
          <span class="ico"><i class="fa-solid fa-location-dot"></i></span>
          <span><b>Your saved address</b><span id="kqCurrentArea">No address saved yet</span></span>
        </div>
        <button class="kq-btn kq-btn-outline" id="kqChangeArea" style="width:100%;margin-top:12px"><i class="fa-solid fa-pen"></i> Change Delivery Area</button>
      </div>

      <div class="kq-section-head"><h2><i class="fa-solid fa-map"></i> Serviced Zones</h2></div>
      <div class="kq-info-card" id="kqZones"></div>
    </div>

    <aside>
      <div class="kq-mini-map">
        <div class="kq-map-frame">
          <iframe title="Kitchen Queen Karachi location" src="https://www.openstreetmap.org/export/embed.html?bbox=66.95%2C24.79%2C67.20%2C24.95&amp;layer=mapnik" loading="lazy"></iframe>
        </div>
      </div>
      <div class="kq-info-card" style="margin-top:16px">
        <div class="kq-info-row"><span class="ico"><i class="fa-solid fa-clock"></i></span><span><b>Kitchen Hours</b><span>Daily 12:00 PM – 12:00 AM</span></span></div>
        <div class="kq-info-row"><span class="ico"><i class="fa-solid fa-motorcycle"></i></span><span><b>Delivery Fee</b><span>Rs 99 — free on orders above Rs 1,500</span></span></div>
        <div class="kq-info-row"><span class="ico"><i class="fa-solid fa-phone"></i></span><span><b>Order by Phone</b><span>0336-2465676</span></span></div>
      </div>
    </aside>
  </div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
