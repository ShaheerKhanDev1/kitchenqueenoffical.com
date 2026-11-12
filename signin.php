<?php
$pageTitle = 'Sign In · Kitchen Queen';
$pageDescription = 'Sign in to Kitchen Queen.';
$pageId = 'signin';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">Sign in to Kitchen Queen</h1>
  <p class="kq-page-sub">One number, faster checkout, saved addresses and order history.</p>

  <div class="kq-auth-page">
    <div class="kq-modal-box kq-auth-box" id="kqAuthInline">
      <div class="kq-auth-brand"><span class="kq-brand-icon"><i class="fa-solid fa-crown"></i></span></div>
      <h3 class="kq-auth-title">SIGN IN TO CONTINUE</h3>
      <p class="kq-auth-sub">Enter your mobile number. New users should also add name and email.</p>
      <div class="kq-auth-field">
        <label>FULL NAME</label>
        <div class="kq-auth-input"><i class="fa-solid fa-user"></i><input type="text" id="kqAuthName" placeholder="e.g. Ali Khan" autocomplete="name"></div>
      </div>
      <div class="kq-auth-field">
        <label>EMAIL</label>
        <div class="kq-auth-input"><i class="fa-solid fa-at"></i><input type="email" id="kqAuthEmail" placeholder="e.g. you@gmail.com" autocomplete="email"></div>
      </div>
      <div class="kq-auth-field">
        <label>MOBILE NUMBER</label>
        <div class="kq-auth-input"><input type="tel" id="kqAuthPhone" placeholder="e.g. 03001234567" autocomplete="tel"><i class="fa-solid fa-mobile-screen"></i></div>
      </div>
      <div class="kq-auth-error" id="kqAuthErr"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
      <button class="kq-auth-btn" id="kqAuthNext" type="button">GET VERIFICATION CODE <i class="fa-solid fa-arrow-right"></i></button>
      <p class="kq-auth-note"><i class="fa-solid fa-shield-halved"></i> 100% SECURE LOGIN</p>
    </div>
  </div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
