<?php
$pageTitle = 'Order History · Kitchen Queen';
$pageDescription = 'Track your Kitchen Queen order status.';
$pageId = 'my_orders';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">Order History</h1>
  <p class="kq-page-sub">Your orders, totals and live status. Active orders can be tracked.</p>

  <div class="kq-orders-gate" id="kqMyOrdersGate" hidden>
    <p>Please sign in to see your order history and live status.</p>
    <button class="kq-btn" type="button" id="kqMyOrdersSignIn"><i class="fa-solid fa-right-to-bracket"></i> Sign In</button>
  </div>

  <div class="kq-orders-wrap" id="kqMyOrders"></div>
  <p class="kq-page-sub" id="kqMyOrdersEmpty" hidden>No orders yet. After checkout, your live status will appear here.</p>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
