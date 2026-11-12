<?php
$pageTitle = 'Checkout · Kitchen Queen';
$pageDescription = 'Confirm your Kitchen Queen order.';
$pageId = 'checkout';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main kq-checkout-main">
  <div class="bp-checkout">
    <div class="bp-topbar">
      <div class="bp-topbar-inner">
        <a class="bp-back" href="index.php" aria-label="Back to menu">
          <i class="fa-solid fa-chevron-left"></i>
        </a>
        <h1 class="bp-checkout-title">SECURE CHECKOUT</h1>
        <div class="bp-verified">
          <i class="fa-solid fa-shield-halved"></i>
          VERIFIED ENCRYPTION
        </div>
      </div>
    </div>

    <div class="bp-checkout-body">
      <div class="bp-checkout-grid">
        <form id="kqCheckoutForm" class="bp-checkout-form" novalidate>
          <section class="bp-card bp-card-personal">
            <div class="bp-card-head">
              <span class="bp-step">1</span>
              <h2>PERSONAL INFORMATION</h2>
            </div>
            <div class="bp-fields-row">
              <label class="bp-input">
                <i class="fa-regular fa-user"></i>
                <input type="text" id="fullname" data-required placeholder="Full Name" autocomplete="name">
              </label>
              <label class="bp-input">
                <i class="fa-solid fa-phone"></i>
                <input type="tel" id="phone" data-required data-phone placeholder="03xx-xxxxxxx" autocomplete="tel">
              </label>
            </div>
            <label class="bp-input">
              <i class="fa-regular fa-envelope"></i>
              <input type="email" id="email" data-required required placeholder="Email for order confirmation" autocomplete="email">
            </label>
          </section>

          <section class="bp-card">
            <div class="bp-card-head bp-card-head-split">
              <div class="bp-card-head-left">
                <span class="bp-step">2</span>
                <h2>DELIVERY ADDRESS</h2>
              </div>
              <button type="button" class="bp-edit-map" id="kqEditMap">EDIT MAP</button>
            </div>
            <div class="bp-mapbar" id="kqMapBar" role="button" tabindex="0">
              <span class="bp-outlet-tag" id="kqOutletTag">KARACHI</span>
              <span class="bp-map-pin" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
            </div>
            <label class="bp-input bp-input-plain">
              <input type="text" id="apt" placeholder="Apartment / House / Office No." autocomplete="address-line2">
            </label>
            <label class="bp-input bp-input-plain">
              <input type="text" id="street" data-required placeholder="Street Name / Landmarks" autocomplete="address-line1">
            </label>
            <input type="hidden" id="address">
          </section>

          <section class="bp-card">
            <div class="bp-card-head">
              <span class="bp-step">3</span>
              <h2>PAYMENT CHOICE</h2>
            </div>
            <div class="bp-pay-row">
              <button type="button" class="bp-pay selected" data-pay="cod" aria-pressed="true">
                <span class="bp-pay-dot"></span>
                <span class="bp-pay-ico"><i class="fa-solid fa-money-bill"></i></span>
                Cash on Delivery
              </button>
              <button type="button" class="bp-pay" data-pay="easypaisa" aria-pressed="false">
                <span class="bp-pay-dot"></span>
                <span class="bp-pay-ico"><i class="fa-solid fa-mobile-screen"></i></span>
                EasyPaisa
              </button>
            </div>

            <div id="kqEasyPaisaFields" class="kq-easypaisa-box" hidden>
              <div class="kq-ep-admin">
                <div class="kq-ep-badge"><i class="fa-solid fa-building-columns"></i> Send payment to</div>
                <p class="kq-ep-name">Muhammad Sami Khan</p>
                <div class="kq-ep-num-row">
                  <span class="kq-ep-num">0345-2269366</span>
                  <button type="button" class="kq-ep-copy" id="epCopy"><i class="fa-solid fa-copy"></i> Copy</button>
                </div>
              </div>
              <ol class="kq-ep-steps">
                <li>Send the order total to the EasyPaisa number above.</li>
                <li>Upload a clear screenshot of the payment receipt here.</li>
                <li>Place the order and wait for admin approval. Your order is confirmed only after the screenshot is verified.</li>
              </ol>
              <label class="kq-ep-upload" for="epSlip">
                <input type="file" id="epSlip" accept="image/*">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <b>Upload payment screenshot</b>
                <small>JPG, PNG or screenshot from EasyPaisa</small>
              </label>
              <div class="kq-ep-preview" id="epSlipPreview" hidden>
                <img alt="Payment screenshot preview">
              </div>
              <p class="kq-ep-note"><i class="fa-solid fa-hourglass-half"></i> After upload, please wait for admin approval before the kitchen starts cooking.</p>
            </div>
          </section>

          <section class="bp-card">
            <div class="bp-card-head">
              <span class="bp-step">4</span>
              <h2>SPECIAL INSTRUCTIONS</h2>
            </div>
            <label class="bp-input bp-input-plain">
              <input type="text" id="note" placeholder="Any special requests? (e.g. extra spicy, no onions...)">
            </label>
          </section>
        </form>

        <aside class="bp-summary-wrap">
          <div class="bp-summary">
            <h2 class="bp-summary-title">PAYMENT SUMMARY</h2>
            <div id="kqOrderLines"></div>
            <button class="bp-confirm" type="submit" form="kqCheckoutForm" id="kqConfirmOrder">
              CONFIRM ORDER <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <div class="kq-receipt-stage" id="kqReceiptStage" hidden>
    <div class="kq-printer">
      <div class="kq-printer-head">
        <div class="kq-print-status" id="kqPrintStatus"><span class="kq-live-dot"></span><span>PRINTING ORDER <b id="kqOrderNo">KQ-000000</b> · <b id="kqPrintTotal">Rs 0</b></span></div>
        <div class="kq-printer-slot"></div>
      </div>
      <div class="kq-paper-mask">
        <div class="kq-paper" id="kqPaper"></div>
      </div>
    </div>
    <div class="kq-receipt-actions" id="kqReceiptActions" hidden>
      <a href="track.php" class="kq-btn" id="kqCheckStatusBtn"><i class="fa-solid fa-route"></i> Track your order</a>
    </div>
    <div class="kq-review-nudge" id="kqReviewNudge" hidden></div>
  </div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
