<?php
$pageTitle = 'Contact · Kitchen Queen';
$pageDescription = 'Contact Kitchen Queen.';
$pageId = 'contact';
require __DIR__ . "/includes/header.php";
?>

<main class="kq-main">
  <h1 class="kq-page-title">Get in Touch</h1>
  <p class="kq-page-sub">Orders, feedback or a special request — we're listening.</p>

  <div class="kq-contact-grid">
    <div>
      <div class="kq-info-card">
        <div class="kq-info-row"><span class="ico"><i class="fa-solid fa-phone"></i></span><span><b>Phone</b><span>0336-2465676</span></span></div>
        <div class="kq-info-row"><span class="ico"><i class="fa-brands fa-whatsapp"></i></span><span><b>WhatsApp</b><span>0336-2465676 — fastest reply</span></span></div>
        <div class="kq-info-row"><span class="ico"><i class="fa-solid fa-envelope"></i></span><span><b>Email</b><span>shaheer101wwe@shaheerwe.com</span></span></div>
        <div class="kq-info-row"><span class="ico"><i class="fa-solid fa-location-dot"></i></span><span><b>Kitchen</b><span>Block D, North Nazimabad, Karachi</span></span></div>
        <div class="kq-contact-actions">
          <a class="kq-btn kq-btn-whatsapp" href="https://wa.me/923362465676"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
          <a class="kq-btn kq-btn-outline" href="tel:03362465676"><i class="fa-solid fa-phone"></i> Call Now</a>
        </div>
      </div>
    </div>

    <form class="kq-form-card" id="kqContactForm" novalidate>
      <h3>Send a Message</h3>
      <div class="kq-form-2col">
        <div class="kq-field">
          <label for="name"><i class="fa-solid fa-user"></i> Name</label>
          <input type="text" id="name" data-required placeholder="Your name">
          <span class="kq-req-badge">Please enter your name</span>
        </div>
        <div class="kq-field">
          <label for="phone"><i class="fa-solid fa-phone"></i> Phone</label>
          <input type="tel" id="phone" data-required data-phone placeholder="03xx-xxxxxxx">
          <span class="kq-req-badge">Enter a valid Pakistani number</span>
        </div>
      </div>
      <div class="kq-field">
        <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
        <input type="email" id="email" data-required placeholder="you@example.com">
        <span class="kq-req-badge">Enter a valid email</span>
      </div>
      <div class="kq-field">
        <label for="msg"><i class="fa-solid fa-comment-dots"></i> Message</label>
        <textarea id="msg" rows="5" data-required placeholder="How can we help?"></textarea>
        <span class="kq-req-badge">Please write a message</span>
      </div>
      <button class="kq-btn" type="submit" style="width:100%"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
    </form>
  </div>
</main>

<?php require __DIR__ . "/includes/footer.php"; ?>
