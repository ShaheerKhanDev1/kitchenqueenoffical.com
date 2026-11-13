(function () {
  "use strict";

  /* ---------------- 1. DATA ---------------- */
  const IMG = "images/";

  const DISHES = [
    /* --- DEALS --- */
    { id: "d1", name: "Friday Family Deal", cat: "deals", price: 1250, was: 1899, serves: 2, highlightServes: true, img: "chicken biryani.webp", tag: "DEAL", hot: true, desc: "Chicken Biryani (2 servings) + Salad + Raita + 1.5L Cold Drink." },
    { id: "d2", name: "Ghar Ka Khana Box", cat: "deals", price: 1250, was: 1290, serves: 2, highlightServes: true, img: "Daal Chawal.jpg", tag: "DEAL", hot: true, desc: "Any daal + any salan + white rice + 4 roti — a full desi dastarkhwan." },

    /* --- DAAL & SABZI --- */
    { id: "d3", name: "Yellow Daal chawal with 1 Shami", cat: "daal", price: 450, was: 380, serves: 1, img: "Yellow daal.webp", tag: "BESTSELLER", hot: true, desc: "Ghee-tarka moong daal served with one hand-pressed beef shami kabab." },
    { id: "d4", name: "Lauki Ki Sabzi", cat: "daal", price: 260, serves: 1, img: "Lauki-Sabzi.webp", tag: "", desc: "Bottle gourd slow-cooked in tomato, garlic and desi masala — light and homely." },
    { id: "d5", name: "Mong Masoor Ki Daal", cat: "daal", price: 280, serves: 1, img: "Moong masoor daal.webp", tag: "", desc: "Moong and masoor cooked together, finished with a sizzling zeera tarka." },
    { id: "d6", name: "Daal Palak", cat: "daal", price: 300, serves: 1, img: "Daal-Palak.jpg", tag: "", desc: "Lentils folded through fresh spinach with garlic and green chilli." },
    { id: "d7", name: "Kali Daal", cat: "daal", price: 300, was: 400, serves: 1, img: "kali daal.webp", tag: "", desc: "Daal maash simmered overnight on low flame with butter and cream." },
    { id: "d8", name: "Bhindi Sabzi", cat: "daal", price: 320, serves: 2, img: "bhindi sabzi.webp", tag: "", desc: "Crisp okra bhujia with onion, tomato and hand-crushed spice — never slimy." },

    /* --- CHICKEN --- */
    { id: "d9", name: "Chicken Karahi", cat: "chicken", price: 850, was: 999, serves: 2, img: "karahi.jpg", tag: "CHEF SPECIAL", hot: true, desc: "Wok-tossed chicken in tomato, ginger and green chilli, finished with coriander." },
    { id: "d10", name: "Chicken Chana Daal", cat: "chicken", price: 450, serves: 2, img: "chicken chana.webp", tag: "", desc: "Chicken slow-cooked with chana daal until the gravy turns thick and glossy." },
    { id: "d11", name: "Chicken Aalo Salan", cat: "chicken", price: 450, serves: 2, img: "chicken-aloo-salan.webp", tag: "", desc: "Everyday Karachi favourite — chicken and potato in a light red salan." },

    /* --- BEEF & MUTTON --- */
    { id: "d12", name: "Beef Bhuna Ghosht", cat: "beef", price: 600, was: 899, serves: 2, img: "bhuna gohst.webp", tag: "BESTSELLER", hot: true, desc: "Beef bhunoed for an hour until the masala clings to every piece. No gravy, all flavour." },
    { id: "d13", name: "Chicken Qourma", cat: "chicken", price: 450, was: 850, serves: 2, img: "chicken qourma.webp", tag: "NEW", hot: true, desc: "Slow-cooked chicken in a silky fried-onion and yogurt qourma — festive, homely and made for roti." },
    { id: "d14", name: "Arvi Ghosht", cat: "beef", price: 450, serves: 2, img: "Arvi.webp", tag: "", desc: "Tender mutton and arvi in a rich brown gravy — a Sunday classic." },
    { id: "d15", name: "Aalu Ghosht", cat: "beef", price: 450, serves: 2, img: "Aloo gohst.webp", tag: "", desc: "Mutton and potato simmered together in traditional desi masala." },
    { id: "d16", name: "Qeema Aalu", cat: "beef", price: 450, serves: 2, img: "keema-Aaloo.webp", tag: "", desc: "Minced beef bhuna with soft potato cubes, whole spice and green chilli." },

    /* --- HALEEM --- */
    { id: "d17", name: "Beef Haleem", cat: "haleem", price: 480, was: 460, serves: 2, img: "beef haleem.webp", tag: "BESTSELLER", hot: true, desc: "Seven-hour stone-ground wheat and lentils with shredded beef and crisp onion." },
    { id: "d18", name: "Chicken Haleem", cat: "haleem", price: 480, serves: 2, img: "chicken haleem.webp", tag: "", hot: true, desc: "Lighter haleem with shredded chicken, ginger julienne and lemon on the side." },

    /* --- RICE --- */
    { id: "d19", name: "White Rice", cat: "rice", price: 250, serves: 1, img: "plain-white-rice.jpg", tag: "", desc: "Steamed long-grain basmati, separated grain by grain." },
    { id: "d20", name: "Daal Chawal", cat: "rice", price: 350, was: 380, serves: 1, img: "Daal Chawal.jpg", tag: "BESTSELLER", hot: true, desc: "The ultimate comfort plate — tarka daal poured over hot white rice." },
    { id: "d21", name: "Chicken Biryani", cat: "rice", price: 350, was: 520, serves: 1, img: "chicken biryani.webp", tag: "", hot: true, desc: "Basmati layered with masala chicken, kewra, aloo and fried onion." },

    /* --- SIDES & RAITA --- */
    { id: "d22", name: "Egg Kharaiza (Khagina)", cat: "sides", price: 300, serves: 1, img: "khagina.webp", tag: "NEW", desc: "Desi scrambled eggs with tomato, onion and green chilli — breakfast any time." },
    { id: "d23", name: "Shami Kabab (6 pcs)", cat: "sides", price: 500, serves: 2, img: "shami.webp", tag: "", desc: "Melt-in-mouth beef and chana daal patties, pan-seared in ghee." },
    { id: "d24", name: "Dahi Phulki with Tarka", cat: "sides", price: 280, serves: 2, img: "dahi phulki.webp", tag: "", desc: "Soft phulki in whisked yoghurt with a red chilli and curry-leaf tarka." },
    { id: "d25", name: "Sweet Dahi Phulki", cat: "sides", price: 280, serves: 2, img: "Sweet phulki.webp", tag: "", desc: "Sweetened yoghurt, soft phulki and mint — cooling and mildly sweet." },
    { id: "d26", name: "Baingan Ka Raita", cat: "sides", price: 280, serves: 2, img: "baingan raita.webp", tag: "", desc: "Roasted aubergine whisked into yoghurt with zeera and black salt." },
    { id: "d27", name: "Tandoori Naan (4 pcs)", cat: "sides", price: 200, serves: 2, img: "naan.jpg", tag: "", desc: "Fresh from the clay oven, brushed with butter." },
    { id: "d32", name: "Home Made chapati(4 pcs)", cat: "sides", price: 120, serves: 2, img: "chapati.webp", tag: "", desc: "Soft homemade whole-wheat chapati, cooked on tawa and served hot." },

    /* --- BEVERAGES --- */
    { id: "d28", name: "Cold Drinks", cat: "drinks", price: 150, serves: 1, img: "pepsi.avif", tag: "", desc: "345ml chilled bottle — your choice of flavour." },
    { id: "d29", name: "Lassi", cat: "drinks", price: 250, serves: 1, img: "lassi.png", tag: "", desc: "Hand-churned yoghurt lassi — sweet or namkeen, served ice cold." },
    { id: "d30", name: "Mango Milk Shake", cat: "drinks", price: 250, was: 260, serves: 1, img: "mango.webp", tag: "NEW", hot: true, desc: "Thick shake blended with fresh Sindhri mango pulp and chilled milk." },

    /* --- DESSERT --- */
    { id: "d31", name: "Shahi Kheer", cat: "dessert", price: 180, serves: 2, img: "kheer.webp", tag: "", desc: "Slow-reduced rice pudding with cardamom, pistachio and almond flakes." },

    /* --- DRINKS EXTRA --- */
    { id: "d35", name: "1.5L Cold Drink", cat: "drinks", price: 250, serves: 4, img: "pepsi 1.5 L.webp", tag: "", desc: "1.5 litre chilled bottle, as listed on the Wow Deals banner." },

    /* --- SPECIAL DEALS (Sunday only) --- */
    { id: "d36", name: "Wow Deal", cat: "special", price: 1250, was: 1899, serves: 2, sundayOnly: true, img: "chicken biryani.webp", tag: "SUNDAY", hot: true, desc: "1 Chicken Biryani (serves 1) + 1 Chicken Karahi (serves 1) + 2 Naan + Salad + Raita + 1L Pepsi." },
    { id: "d37", name: "Weekend Deal", cat: "special", price: 1450, was: 1999, serves: 2, sundayOnly: true, img: "chicken qourma.webp", tag: "SUNDAY", hot: true, desc: "1 Beef Biryani (serves 1) + 1 Chicken Qourma (serves 1) + 2 Naan + Salad + Raita." }
  ];

  const CATEGORIES = [
    { id: "hot", label: "Trending!", icon: "fa-fire" },
    { id: "deals", label: "Deals", icon: "fa-tags" },
    { id: "special", label: "Special Deals", icon: "fa-star" },
    { id: "daal", label: "Daal & Sabzi", icon: "fa-bowl-food" },
    { id: "chicken", label: "Chicken", icon: "fa-drumstick-bite" },
    { id: "beef", label: "Beef & Mutton", icon: "fa-fire-burner" },
    { id: "haleem", label: "Haleem", icon: "fa-mortar-pestle" },
    { id: "rice", label: "Rice", icon: "fa-bowl-rice" },
    { id: "sides", label: "Sides & Raita", icon: "fa-plate-wheat" },
    { id: "drinks", label: "Beverages", icon: "fa-bottle-water" },
    { id: "dessert", label: "Dessert", icon: "fa-ice-cream" }
  ];

  const BANNER_FEATURED = ["d21", "d20", "d17", "d16", "d1", "d9", "d32", "d27", "d28", "d35"];

  const WEEK = [
    { day: "Monday", ids: ["d21", "d20", "d8"] },
    { day: "Tuesday", ids: ["d9", "d5", "d27"] },
    { day: "Wednesday", ids: ["d17", "d11", "d19"] },
    { day: "Thursday", ids: ["d16", "d7", "d32"] },
    { day: "Friday", ids: ["d1", "d13", "d28"] },
    { day: "Saturday", ids: ["d12", "d4", "d23"] },
    { day: "Sunday", ids: ["d10", "d6", "d31"] }
  ];

  const BLOG = [
    { img: "beef haleem.webp", cat: "TRADITION", title: "Why Slow-Cooked Haleem Tastes Better", text: "Seven hours on a low flame breaks down wheat and meat into one silky texture — a shortcut simply cannot replicate it. Our chefs stir by hand every twenty minutes, which is why the grain never sticks and the flavour reaches the very centre of every spoon." },
    { img: "karahi.jpg", cat: "TECHNIQUE", title: "The Secret to a Restaurant-Style Karahi", text: "High heat, a heavy wok and tomatoes added in two stages. The first batch melts into the base, the second stays chunky for freshness. We finish with hand-crushed black pepper and julienne ginger off the flame." },
    { img: "chicken biryani.webp", cat: "HERITAGE", title: "Karachi Biryani vs. The Rest of Pakistan", text: "Karachi biryani is bolder — potatoes, more chilli, and a distinct sourness from dried plum. This is the recipe our founder inherited from her grandmother in Nazimabad, unchanged for three generations." },
    { img: "kheer.webp", cat: "DESSERT", title: "Kheer: Patience Is the Only Ingredient", text: "Full-fat milk reduced to a third of its volume, stirred constantly so the bottom never catches. Rice goes in only once the milk turns ivory, and cardamom right at the end to keep the aroma alive." },
    { img: "shami.webp", cat: "KITCHEN NOTES", title: "How We Keep Our Shami Kababs Soft", text: "Beef and chana daal must be cooked until every drop of water evaporates. Grinding warm — never hot, never cold — is what gives that melting bite people remember." },
    { img: "naan.jpg", cat: "BREADS", title: "Clay Oven Naan at Home Temperatures", text: "A screaming-hot cast iron pan and a lightly wet dough surface mimic a tandoor closely enough. The key is not to roll it thin — naan should puff, not crisp." }
  ];

  const UPSELL = ["d27", "d26", "d28", "d31"];

  const ADDONS = {
    portion: [
      { label: "Half (1–2 people)", price: 0 },
      { label: "Full (3–4 people)", price: 350 },
      { label: "Family (6–8 people)", price: 850 }
    ],
    spice: [
      { label: "Mild", price: 0 },
      { label: "Medium (Desi)", price: 0 },
      { label: "Extra Hot", price: 0 }
    ],
    extras: [
      { label: "Extra Raita", price: 50 },
      { label: "Extra Naan", price: 80 },
      { label: "Extra Salad", price: 80 },
      { label: "Disposable Cutlery", price: 20 }
    ]
  };

  const PROMOS = {};
  const DELIVERY_FEE = 99;
  const FREE_DELIVERY_OVER = 1500;
  const EASYPAISA = { name: "Muhammad Sami Khan", number: "0345-2269366" };

  const DESC_ONLY = new Set(["d19", "d27", "d28", "d29", "d30", "d31", "d23", "d24", "d25", "d26", "d32"]);
  const RICE_COMBO = new Set(["d1", "d2", "d3", "d20", "d21", "d36", "d37"]);

  function dishCustomize(d) {
    if (!d) return { spice: false, extras: false };
    if (DESC_ONLY.has(d.id) || d.cat === "drinks" || d.cat === "dessert") return { spice: false, extras: false };
    if (RICE_COMBO.has(d.id) || (d.cat === "rice" && d.id !== "d19") || d.cat === "deals" || d.cat === "special") return { spice: true, extras: true };
    // Enable add-ons for salan / main items (chicken, beef, daal, etc.)
    if (["chicken", "beef", "daal", "salan", "curry", "sides"].includes(d.cat)) return { spice: true, extras: true };
    return { spice: true, extras: false };
  }

  function servesLabel(d) {
    const n = Number(d && d.serves);
    if (!n) return "";
    return n === 1 ? "Serves 1" : "Serves " + n;
  }

  const AREAS = [
    "Azizabad", "Azizabad Block 8", "Bahadurabad", "Buffer Zone", "Buffer Zone 14/A", "Buffer Zone 15-A/1",
    "Buffer Zone 15-A/2", "Buffer Zone 15-A/3", "Buffer Zone 15-A/4", "Buffer zone sector 15A", "Bufferzone - Sector 15-A/1",
    "Bufferzone - Sector 15-A/2", "Bufferzone - Sector 15-A/3", "Bufferzone - Sector 15-A/4", "Bufferzone - Sector 15-A/5",
    "Bufferzone - Sector 15-B", "Bufferzone - Sector 16-A", "Bufferzone - Sector 16-A/4", "Dastageer Block 9",
    "Dhoraji Society", "Tariq Road", "Essa Nagri", "Federal B Area Block 1", "Federal B Area Block 2", "Federal B Area Block 3",
    "Federal B Area Block 4", "Federal B Area Block 5", "Federal B Area Block 6", "Federal B Area Block 7", "Federal B Area Block 8",
    "Federal B Area Block 9", "Federal B Area Block 10", "Federal B Area Block 11", "Federal B Area Block 12", "Federal B Area Block 13",
    "Federal B Area Block 14", "Federal B Area Block 15", "Federal B Area Block 16", "Federal B Area Block 17", "Federal B Area Block 18",
    "Federal B Area Block 19", "Federal B Area Block 22", "Gulistan-e-Jauhar - Block 1", "Gulistan-e-Jauhar - Block 2",
    "Gulistan-e-Jauhar - Block 3", "Gulistan-e-Jauhar - Block 4", "Gulistan-e-Jauhar - Block 5", "Gulistan-e-Jauhar - Block 6",
    "Gulistan-e-Jauhar - Block 7", "Gulistan-e-Jauhar - Block 8", "Gulistan-e-Jauhar - Block 9", "Gulistan-e-Jauhar - Block 10",
    "Gulistan-e-Jauhar - Block 11", "Gulistan-e-Jauhar - Block 12", "Gulistan-e-Jauhar - Block 13-B", "Gulistan-e-Jauhar - Block 16-A",
    "Gulistan-e-Jauhar - Block 20", "Gulshan e Iqbal Block 2", "Gulshan e Iqbal Block 3", "Gulshan e Iqbal Block 4",
    "Gulshan e Iqbal Block 6", "Gulshan e Iqbal Block 7", "Gulshan e Iqbal Block 9", "Gulshan e Iqbal Block 10-A",
    "Gulshan e Iqbal Block 11", "Gulshan e Iqbal Block 13", "Gulshan e Iqbal Block 13 C", "Gulshan e Iqbal Block 13 E",
    "Gulshan e Iqbal Block 13/G", "Gulshan e Iqbal Block 13/B", "Gulshan e Iqbal Block 14", "Gulshan e Iqbal Block 15",
    "Gulshan e Iqbal Block 16", "Gulshan e Iqbal Block 17", "Gulshan e Iqbal Block 18", "Gulshan e Iqbal Block 19",
    "Gulshan e Iqbal Block 20", "Gulshan e Shameem", "Karimabad", "Hussainabad", "Liaquatabad Block 1", "Liaquatabad Block 2",
    "Liaquatabad No 5", "Liaquatabad No 10", "Nazimabad Block 1", "Nazimabad Block 2", "Nazimabad Block 3", "Nazimabad Block 4",
    "Nazimabad Block 5", "Nazimabad Number 7", "North Karachi", "North Karachi - Sector 5-C", "North Karachi - Sector 8",
    "North Karachi - Sector 11A", "North Karachi - Sector 15-A/1", "North Nazimabad - Block A", "North Nazimabad - Block B",
    "North Nazimabad - Block C", "North Nazimabad - Block D", "North Nazimabad - Block E", "North Nazimabad - Block F",
    "North Nazimabad - Block G", "North Nazimabad - Block H", "North Nazimabad - Block I", "North Nazimabad - Block J",
    "North Nazimabad - Block K", "North Nazimabad - Block L", "North Nazimabad - Block M", "North Nazimabad - Block N",
    "North Nazimabad - Block R", "North Nazimabad - Block S", "Sakhi Hasan Chowrangi", "Sharifabad", "Water Pump", "Yaseenabad"
  ];

  const PAGES = [
    { file: "index.php", label: "Home", icon: "fa-house" },
    { file: "menu.php", label: "Weekly Menu", icon: "fa-book-open" },
    { file: "meal-builder.php", label: "Meal Builder", icon: "fa-wand-magic-sparkles" },
    { file: "live-counter.php", label: "Live Counter", icon: "fa-tower-broadcast" },
    { file: "catering.php", label: "Catering", icon: "fa-champagne-glasses" },
    { file: "location.php", label: "Location", icon: "fa-location-dot" },
    { file: "blog.php", label: "Kitchen Journal", icon: "fa-newspaper" },
    { file: "contact.php", label: "Contact", icon: "fa-headset" },
    { file: "reviews.php", label: "Reviews", icon: "fa-star" },
    { file: "my_orders.php", label: "My Orders", icon: "fa-bag-shopping" }
  ];

  const RAIL_FILES = ["index.php", "menu.php", "location.php", "meal-builder.php"];
  const RAIL_PAGES = RAIL_FILES.map((f) => PAGES.find((p) => p.file === f)).filter(Boolean);
  const DRAWER_PAGES = PAGES.filter((p) => !RAIL_FILES.includes(p.file));

  window.KQ = window.KQ || {};

  /* ---------------- 2. STATE ---------------- */
  const LS = {
    get(k, f) { try { return JSON.parse(localStorage.getItem("kq_" + k)) ?? f; } catch (e) { return f; } },
    set(k, v) { try { localStorage.setItem("kq_" + k, JSON.stringify(v)); } catch (e) { /* ignore */ } }
  };

  const state = {
    cart: LS.get("cart", []),
    address: LS.get("address", null),
    promo: LS.get("promo", null),
    theme: LS.get("theme", "light"),
    user: LS.get("user", null)
  };

  const LIVE = {
    storeOnline: true,
    storeMessage: "Website is currently unavailable",
    freeOver: FREE_DELIVERY_OVER,
    easypaisa: { ...EASYPAISA },
    fees: {},
    stock: {},
    ready: false
  };

  const money = (n) => "Rs " + Number(n).toLocaleString("en-PK");
  const dish = (id) => DISHES.find((d) => d.id === id);
  const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));

  function validate(form) {
    let valid = true;
    form.querySelectorAll("[required], [data-required]").forEach((i) => {
      if (!i.value.trim()) { i.classList.add("invalid"); valid = false; }
      else { i.classList.remove("invalid"); }
    });
    return valid;
  }

  /* ---------------- 3. SHELL ---------------- */
  function currentPage() {
    const p = location.pathname.split("/").pop();
    return p && p.length ? p : "index.php";
  }

  function renderShell() {
    const active = currentPage();
    const rail = document.getElementById("kqRail");
    const header = document.getElementById("kqHeader");
    if (rail) {
      rail.className = "kq-rail";
      rail.innerHTML = `
        <button class="kq-rail-btn" id="kqMenuBtn" aria-label="Open navigation"><i class="fa-solid fa-bars"></i></button>
        <div class="kq-rail-mid">
          ${RAIL_PAGES.map((p) => `<a class="kq-rail-btn${p.file === active ? " active" : ""}" href="${p.file}" title="${p.label}" aria-label="${p.label}"><i class="fa-solid ${p.icon}"></i></a>`).join("")}
          <button class="kq-rail-btn" id="kqUserBtn" title="Sign In" aria-label="Sign in"><i class="fa-solid fa-user"></i></button>
          <button class="kq-rail-btn" id="kqCartBtn" title="Basket" aria-label="Open cart">
            <i class="fa-solid fa-basket-shopping"></i>
            <span class="kq-cart-badge" id="kqCartBadge">0</span>
          </button>
        </div>
        <button class="kq-rail-btn kq-theme-toggle" id="kqThemeBtn" title="Dark Mode" aria-label="Toggle dark mode"><i class="fa-solid fa-moon"></i></button>`;
    }

    let dock = document.getElementById("kqDock");
    if (!dock) {
      dock = document.createElement("nav");
      dock.id = "kqDock";
      document.body.appendChild(dock);
    }
    dock.className = "kq-dock";
    dock.setAttribute("aria-label", "Mobile navigation");
    dock.innerHTML = `
      <a class="kq-dock-btn${active === "index.php" ? " active" : ""}" href="index.php" aria-label="Home"><i class="fa-solid fa-house"></i></a>
      <a class="kq-dock-btn${active === "menu.php" ? " active" : ""}" href="menu.php" aria-label="Menu"><i class="fa-solid fa-utensils"></i></a>
      <button class="kq-dock-fab" id="kqDockCart" aria-label="Open basket">
        <i class="fa-solid fa-bag-shopping"></i>
        <span class="kq-cart-badge" id="kqDockBadge">0</span>
      </button>
      <a class="kq-dock-btn${active === "location.php" ? " active" : ""}" href="location.php" aria-label="Locations"><i class="fa-solid fa-location-dot"></i></a>
      <button class="kq-dock-btn" id="kqDockMenu" aria-label="Open navigation"><i class="fa-solid fa-bars"></i></button>`;

    let hours = document.getElementById("kqHoursBar");
    if (document.body.classList.contains("kq-checkout-page")) {
      if (hours) hours.remove();
    } else {
      if (!hours) {
        hours = document.createElement("div");
        hours.id = "kqHoursBar";
        document.body.prepend(hours);
      }
      hours.className = "kq-hours-bar";
      hours.innerHTML = `<div class="kq-marquee" aria-hidden="false">
          <span>For any queries, our working hours 12PM to 12AM — call us at (033) 62465676 or WhatsApp 92-3362465676</span>
          <span>For any queries, our working hours 12PM to 12AM — call us at (033) 62465676 or WhatsApp 92-3362465676</span>
        </div>`;
    }

    if (header) {
      header.className = "kq-header";
      header.innerHTML = `
        <a class="kq-brand" href="index.php">
          <span class="kq-brand-icon"><i class="fa-solid fa-crown"></i></span>
          Kitchen <b>Queen</b>
        </a>
        <button class="kq-location-pill" id="kqLocBtn">
          <i class="fa-solid fa-location-dot"></i>
          <span class="pill-text">
            <small>Deliver to</small>
            <span id="kqLocLabel">Select your area</span>
          </span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>`;
    }

    const extras = document.getElementById("kqShellExtras");
    if (extras) extras.innerHTML = shellExtrasHTML();

    const foot = document.getElementById("kqFooter");
    if (foot) {
      foot.className = "kq-footer";
      foot.innerHTML = `
        <div class="kq-footer-inner">
          <div class="kq-footer-brand">
            <a class="kq-brand" href="index.php"><span class="kq-brand-icon"><i class="fa-solid fa-crown"></i></span>Kitchen <b>Queen</b></a>
            <p>Authentic home-cooked Desi food, delivered across Karachi. Taste the perfection in every bite.</p>
          </div>
          <div class="kq-footer-col">
            <h4>COMPANY</h4>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="my_orders.php">My Account</a>
            <a href="privacy.php">Privacy Policy</a>
          </div>
          <div class="kq-footer-col">
            <h4>SUPPORT</h4>
            <a href="contact.php">Contact Us</a>
            <a href="reviews.php">Feedback</a>
            <a href="location.php">Our Locations</a>
          </div>
          <div class="kq-footer-col">
            <h4>SOCIAL</h4>
            <div class="kq-social-row">
              <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
              <a href="tel:+923362465676" aria-label="Call"><i class="fa-solid fa-phone"></i></a>
            </div>
            <p class="kq-footer-quote">“Wherever your craving calls — we’re already there, one perfect bite away.”</p>
          </div>
        </div>
        <div class="kq-footer-app">
          <div>
            <h3>Order faster with our App</h3>
            <p>Download the Kitchen Queen App for exclusive deals, real-time order tracking, and seamless reordering.</p>
          </div>
          <div class="kq-app-btns">
            <a href="#" class="kq-app-btn"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" height="40"></a>
            <a href="#" class="kq-app-btn"><img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store" height="40"></a>
          </div>
        </div>
        <div class="kq-footer-bottom">
          <p>&copy; ${new Date().getFullYear()} Kitchen Queen, Karachi. All rights reserved.</p>
          <span>CREATED AND MANAGED WITH CARE</span>
        </div>`;
    }

    bindShell(active);
    applyTheme();
    renderUserUI();
    renderCart();
    renderLocLabel();
  }

  function shellExtrasHTML() {
    const active = currentPage();
    return `
      <div class="kq-overlay" id="kqOverlay"></div>

      <aside class="kq-nav-drawer" id="kqNavDrawer" aria-label="Main navigation">
        <div class="kq-drawer-top">
          <button class="kq-drawer-user" id="kqDrawerUser">
            <span class="kq-avatar"><i class="fa-solid fa-user"></i></span>
            <span class="kq-drawer-user-txt">
              <b id="kqDrawerUserName">Sign In</b>
              <small id="kqDrawerUserSub">TAP TO LOGIN</small>
            </span>
          </button>
          <button class="kq-x-btn" id="kqNavClose" aria-label="Close navigation"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="kq-drawer-scroll">
          <div class="kq-nav-group">
            ${DRAWER_PAGES.map((p) => `<a class="kq-nav-link${p.file === active ? " active" : ""}" href="${p.file}"><i class="fa-solid ${p.icon}"></i><span>${p.label}</span><i class="fa-solid fa-chevron-right chev"></i></a>`).join("")}
          </div>
          <div class="kq-nav-group">
            <a class="kq-nav-link" href="checkout.php"><i class="fa-solid fa-receipt"></i><span>Checkout</span><i class="fa-solid fa-chevron-right chev"></i></a>
            <a class="kq-nav-link" href="signin.php"><i class="fa-solid fa-right-to-bracket"></i><span>Sign In / Register</span><i class="fa-solid fa-chevron-right chev"></i></a>
            <a class="kq-nav-link" href="my_orders.php"><i class="fa-solid fa-bag-shopping"></i><span>My Orders</span><i class="fa-solid fa-chevron-right chev"></i></a>
          </div>

          <button class="kq-mode-card" id="kqDrawerTheme">
            <span class="kq-mode-ico"><i class="fa-solid fa-sun"></i></span>
            <span class="kq-mode-txt"><b>Dark Mode</b><small id="kqModeState">DISABLED</small></span>
            <span class="kq-switch"><span class="knob"></span></span>
          </button>

          <p class="kq-nav-heading">SOCIAL NETWORK</p>
          <div class="kq-social-row">
            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="tel:+923362465676" aria-label="Call"><i class="fa-solid fa-phone"></i></a>
          </div>
        </div>

        <div class="kq-drawer-foot">
          <button class="kq-btn kq-btn-soft" id="kqDrawerSignIn"><i class="fa-solid fa-user"></i> <span id="kqDrawerSignInTxt">Sign In</span></button>
          <p class="kq-app-ver">KITCHEN QUEEN APP V2.4.0</p>
        </div>
      </aside>

      <aside class="kq-cart-drawer" id="kqCartDrawer" aria-label="Your basket">
        <div class="kq-drawer-head">
          <h3>YOUR BASKET</h3>
          <button class="kq-x-btn" id="kqCartClose" aria-label="Close basket"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="kq-cart-body" id="kqCartBody"></div>
        <div class="kq-cart-foot">
          <button class="kq-btn" id="kqCheckoutBtn"><i class="fa-solid fa-arrow-right"></i> Proceed to Checkout</button>
        </div>
      </aside>

      <div class="kq-modal" id="kqCustModal" role="dialog" aria-modal="true">
        <div class="kq-modal-box" id="kqCustBox"></div>
      </div>

      <div class="kq-modal kq-loc-modal" id="kqLocModal" role="dialog" aria-modal="true">
        <div class="kq-modal-box kq-loc-box kq-loc-gate">
          <button class="kq-x-btn kq-loc-close" id="kqLocClose" aria-label="Close" hidden><i class="fa-solid fa-xmark"></i></button>
          <div class="kq-loc-logo"><span class="kq-brand-icon"><i class="fa-solid fa-crown"></i></span></div>
          <h3 class="kq-loc-gate-title">Select your order type</h3>
          <div class="kq-loc-type">
            <button type="button" class="on" id="kqDeliveryOnly">Delivery</button>
          </div>
          <p class="kq-loc-gate-sub">Please select your location</p>
          <button class="kq-use-loc" id="kqUseLoc" type="button"><i class="fa-solid fa-location-crosshairs"></i> Use Current Location</button>
          <div class="kq-loc-alert" id="kqLocAlert" hidden></div>
          <label class="kq-loc-label">Select City / Region</label>
          <div class="kq-loc-field" id="kqCityBtn"><b id="kqCityVal">Karachi</b></div>
          <label class="kq-loc-label">Select Area / Sub Region</label>
          <button class="kq-loc-field kq-loc-area-btn" id="kqAreaBtn" type="button">
            <span id="kqAreaVal">Select Area / Sub Region</span>
            <i class="fa-solid fa-chevron-down"></i>
          </button>
          <input type="hidden" id="kqLocPhone">
          <input type="hidden" id="kqAddrDetail">
          <button class="kq-loc-select-btn" id="kqSaveAddr" type="button" disabled>Select</button>
          <div class="kq-loc-sheet" id="kqLocSheet" hidden>
            <div class="kq-loc-sheet-head">
              <input type="search" id="kqAreaSearch" placeholder="Search area">
              <button class="kq-x-btn" id="kqLocSheetClose" type="button" aria-label="Close list"><i class="fa-solid fa-chevron-down"></i></button>
            </div>
            <div class="kq-loc-sheet-list" id="kqLocSheetList"></div>
          </div>
        </div>
      </div>

      <div class="kq-modal" id="kqAuthModal" role="dialog" aria-modal="true">
        <div class="kq-modal-box kq-auth-box" id="kqAuthBox"></div>
      </div>

      <div class="kq-toast-wrap" id="kqToasts"></div>`;
  }

  function bindShell(active) {
    const $ = (id) => document.getElementById(id);
    const overlay = $("kqOverlay");
    const nav = $("kqNavDrawer");
    const cart = $("kqCartDrawer");

    const closeAll = (force) => {
      const locOpen = document.getElementById("kqLocModal")?.classList.contains("show");
      if (locOpen && !state.address && !force) return;
      [nav, cart].forEach((el) => el && el.classList.remove("show"));
      document.querySelectorAll(".kq-modal").forEach((m) => {
        if (m.id === "kqLocModal" && !state.address && !force) return;
        m.classList.remove("show");
      });
      const stillLoc = document.getElementById("kqLocModal")?.classList.contains("show");
      if (!stillLoc) {
        overlay && overlay.classList.remove("show");
        document.body.style.overflow = "";
      }
    };
    KQ.closeAll = closeAll;

    const open = (el) => {
      if (!el) return;
      closeAll();
      el.classList.add("show");
      overlay && overlay.classList.add("show");
      document.body.style.overflow = "hidden";
    };
    KQ.open = open;

    $("kqMenuBtn") && $("kqMenuBtn").addEventListener("click", () => open(nav));
    $("kqUserBtn") && $("kqUserBtn").addEventListener("click", () => openAuth());
    $("kqDockMenu") && $("kqDockMenu").addEventListener("click", () => open(nav));
    $("kqNavClose") && $("kqNavClose").addEventListener("click", closeAll);
    $("kqDockCart") && $("kqDockCart").addEventListener("click", () => { renderCart(); open(cart); });
    $("kqDrawerUser") && $("kqDrawerUser").addEventListener("click", () => openAuth());
    $("kqDrawerSignIn") && $("kqDrawerSignIn").addEventListener("click", () => {
      if (state.user) { signOut(); return; }
      openAuth();
    });
    $("kqDrawerTheme") && $("kqDrawerTheme").addEventListener("click", () => {
      state.theme = state.theme === "dark" ? "light" : "dark";
      LS.set("theme", state.theme);
      applyTheme();
    });
    $("kqCartBtn") && $("kqCartBtn").addEventListener("click", () => { renderCart(); open(cart); });
    $("kqCartClose") && $("kqCartClose").addEventListener("click", closeAll);
    overlay && overlay.addEventListener("click", closeAll);
    document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeAll(); });
    document.addEventListener("click", (e) => { if (e.target.closest("[data-auth-close]")) closeAll(); });

    $("kqThemeBtn") && $("kqThemeBtn").addEventListener("click", () => {
      state.theme = state.theme === "dark" ? "light" : "dark";
      LS.set("theme", state.theme);
      applyTheme();
      toast(state.theme === "dark" ? "Dark mode on" : "Light mode on", "fa-circle-half-stroke");
    });

    $("kqLocBtn") && $("kqLocBtn").addEventListener("click", openLocation);
    $("kqCheckoutBtn") && $("kqCheckoutBtn").addEventListener("click", () => {
      if (!state.cart.length) return toast("Your basket is empty", "fa-basket-shopping");
      if (!requireSignIn("Please sign in to confirm your order")) {
        pendingCheckout = true;
        return;
      }
      location.href = "checkout.php";
    });

    document.addEventListener("click", (e) => {
      const link = e.target.closest('a[href="checkout.php"]');
      if (!link) return;
      if (state.user) return;
      e.preventDefault();
      pendingCheckout = true;
      requireSignIn("Please sign in to confirm your order");
    });

    document.addEventListener("click", (e) => {
      const addBtn = e.target.closest("[data-add]");
      if (addBtn) {
        const id = addBtn.getAttribute("data-add");
        const d = dish(id);
        if (d && LIVE.stock[d.id] === false) {
          toast("This item is out of stock", "fa-ban");
          return;
        }
        openCustomizer(id);
        return;
      }
      const quick = e.target.closest("[data-quickadd]");
      if (quick) { addToCart(quick.getAttribute("data-quickadd"), {}, 1); }
    });

    void active;
  }

  function applyTheme() {
    const t = state.theme === "dark" ? "dark" : "light";
    document.documentElement.setAttribute("data-theme", t);
    document.documentElement.style.colorScheme = t;
    if (document.body) document.body.setAttribute("data-theme", t);
    const btn = document.querySelector("#kqThemeBtn i");
    if (btn) btn.className = t === "dark" ? "fa-solid fa-sun" : "fa-solid fa-moon";
    const card = document.getElementById("kqDrawerTheme");
    if (card) {
      card.classList.toggle("on", t === "dark");
      const lbl = document.getElementById("kqModeState");
      if (lbl) lbl.textContent = t === "dark" ? "ENABLED" : "DISABLED";
      const ico = card.querySelector(".kq-mode-ico i");
      if (ico) ico.className = t === "dark" ? "fa-solid fa-moon" : "fa-solid fa-sun";
    }
  }
  document.documentElement.setAttribute("data-theme", state.theme === "dark" ? "dark" : "light");
  document.documentElement.style.colorScheme = state.theme === "dark" ? "dark" : "light";

  /* ---------------- 3b. AUTH (sign in + OTP) ---------------- */
  function renderUserUI() {
    const name = document.getElementById("kqDrawerUserName");
    const sub = document.getElementById("kqDrawerUserSub");
    const btn = document.getElementById("kqDrawerSignInTxt");
    if (name) name.textContent = state.user ? state.user.name : "Sign In";
    if (sub) sub.textContent = state.user ? state.user.phone : "TAP TO LOGIN";
    if (btn) btn.textContent = state.user ? "Sign Out" : "Sign In";
  }

  function signOut() {
    state.user = null;
    LS.set("user", null);
    if (window.KQApi) KQApi.setToken("");
    renderUserUI();
    toast("Signed out", "fa-right-from-bracket");
    document.dispatchEvent(new CustomEvent("kq:auth"));
  }

  let otpTimer = null;

  function authStepOne(mount, prefill) {
    const p = prefill || {};
    mount.innerHTML = `
      <button class="kq-auth-x" data-auth-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      <div class="kq-auth-brand"><span class="kq-brand-icon"><i class="fa-solid fa-crown"></i></span></div>
      <h3 class="kq-auth-title">SIGN IN TO CONTINUE</h3>
      <p class="kq-auth-sub">Returning users can continue with phone only.<br>New users should add name and email.</p>
      <div class="kq-auth-field">
        <label>FULL NAME</label>
        <div class="kq-auth-input"><i class="fa-solid fa-user"></i><input type="text" id="kqAuthName" placeholder="e.g. John Doe" value="${esc(p.name || "")}"></div>
      </div>
      <div class="kq-auth-field">
        <label>EMAIL</label>
        <div class="kq-auth-input"><i class="fa-solid fa-at"></i><input type="email" id="kqAuthEmail" placeholder="e.g. you@gmail.com" value="${esc(p.email || "")}"></div>
      </div>
      <div class="kq-auth-field">
        <label>MOBILE NUMBER</label>
        <div class="kq-auth-input"><input type="tel" id="kqAuthPhone" placeholder="e.g. 03001234567" value="${esc(p.phone || "")}"><i class="fa-solid fa-mobile-screen"></i></div>
      </div>
      <div class="kq-auth-error" id="kqAuthErr"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
      <button class="kq-auth-btn" id="kqAuthNext" disabled>GET VERIFICATION CODE <i class="fa-solid fa-arrow-right"></i></button>
      <p class="kq-auth-note"><i class="fa-solid fa-shield-halved"></i> 100% SECURE LOGIN &middot; NO SPAM</p>`;

    const name = mount.querySelector("#kqAuthName");
    const email = mount.querySelector("#kqAuthEmail");
    const phone = mount.querySelector("#kqAuthPhone");
    const next = mount.querySelector("#kqAuthNext");
    const err = mount.querySelector("#kqAuthErr");

    const phoneOk = (v) => {
      const d = String(v || "").replace(/[\s-]/g, "");
      return /^0\d{10}$/.test(d) || /^3\d{9}$/.test(d) || /^\+?92\d{10}$/.test(d);
    };
    const emailOk = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

    const sync = () => { next.disabled = !phoneOk(phone.value); };
    [name, email, phone].forEach((i) => i.addEventListener("input", () => { err.classList.remove("show"); sync(); }));
    sync();

    next.addEventListener("click", async () => {
      if (!phoneOk(phone.value)) {
        err.querySelector("span").textContent = "Enter a valid 11-digit mobile number (03xxxxxxxxx)";
        err.classList.add("show");
        return;
      }
      if (!name.value.trim() || name.value.trim().length < 2) {
        err.querySelector("span").textContent = "Please enter your full name";
        err.classList.add("show");
        return;
      }
      if (!emailOk(email.value)) {
        err.querySelector("span").textContent = "Please enter a valid email for the OTP";
        err.classList.add("show");
        return;
      }
      const data = { name: name.value.trim(), email: email.value.trim(), phone: phone.value.trim() };
      next.disabled = true;
      const oldHtml = next.innerHTML;
      next.innerHTML = "SENDING CODE...";
      if (!window.KQApi) {
        err.querySelector("span").textContent = "Login service unavailable. Please reload and retry.";
        err.classList.add("show");
        next.disabled = false;
        next.innerHTML = oldHtml;
        return;
      }
      KQApi.requestOtp({ phone: data.phone, purpose: "login", name: data.name, email: data.email }).then((res) => {
        if (res && res.data && res.data.dev_code) data.dev_code = res.data.dev_code;
        if (mount.contains(next)) authStepTwo(mount, data);
      }).catch((e) => {
        if (!mount.contains(next)) return;
        err.querySelector("span").textContent = (e && e.message) ? e.message : "Could not send code";
        err.classList.add("show");
      }).finally(() => {
        next.disabled = false;
        next.innerHTML = oldHtml;
      });
    });
  }

  function authStepTwo(mount, data) {
    mount.innerHTML = `
      <button class="kq-auth-back" id="kqAuthBack"><i class="fa-solid fa-chevron-left"></i> BACK</button>
      <button class="kq-auth-x" data-auth-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      <div class="kq-auth-lock"><i class="fa-solid fa-lock"></i></div>
      <h3 class="kq-auth-title">VERIFY EMAIL</h3>
      <p class="kq-auth-sub">Code requested for <b>${esc(data.email || data.email)}</b> <button class="kq-auth-edit" id="kqAuthEdit" aria-label="Edit number"><i class="fa-solid fa-pen"></i></button></p>
      <div class="kq-otp-row">
        ${[0, 1, 2, 3, 4, 5].map(() => `<input class="kq-otp" type="tel" inputmode="numeric" maxlength="1">`).join("")}
      </div>
      <div class="kq-auth-error" id="kqOtpErr"><i class="fa-solid fa-circle-exclamation"></i><span>Invalid Verification Code</span></div>
      <p class="kq-auth-resend">Resend in <b id="kqResend">00:45</b></p>
      <button class="kq-auth-btn" id="kqOtpVerify" disabled>VERIFY &amp; LOGIN <i class="fa-solid fa-circle-check"></i></button>`;

    const boxes = Array.from(mount.querySelectorAll(".kq-otp"));
    const verify = mount.querySelector("#kqOtpVerify");
    const err = mount.querySelector("#kqOtpErr");
    const code = () => boxes.map((b) => b.value).join("");

    boxes[0].focus();
    boxes.forEach((b, i) => {
      b.addEventListener("input", () => {
        b.value = b.value.replace(/\D/g, "");
        err.classList.remove("show");
        if (b.value && boxes[i + 1]) boxes[i + 1].focus();
        verify.disabled = code().length !== 6;
      });
      b.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !b.value && boxes[i - 1]) boxes[i - 1].focus();
      });
    });

    mount.querySelector("#kqAuthBack").addEventListener("click", () => { clearInterval(otpTimer); authStepOne(mount, data); });
    mount.querySelector("#kqAuthEdit").addEventListener("click", () => { clearInterval(otpTimer); authStepOne(mount, data); });

    let left = 45;
    const label = mount.querySelector("#kqResend");
    clearInterval(otpTimer);
    otpTimer = setInterval(() => {
      left -= 1;
      if (left <= 0) {
        clearInterval(otpTimer);
        label.innerHTML = `<button class="kq-auth-resend-btn" type="button">Resend code</button>`;
        label.querySelector("button").addEventListener("click", async () => {
          const btn = label.querySelector("button");
          if (btn) {
            btn.disabled = true;
            btn.textContent = "Sending...";
          }
          try {
            if (!window.KQApi) throw new Error("Login service unavailable. Please reload.");
            await KQApi.requestOtp({ phone: data.phone, purpose: "login", name: data.name, email: data.email });
            authStepTwo(mount, data);
          } catch (e) {
            if (btn) {
              btn.disabled = false;
              btn.textContent = "Resend code";
            }
            err.querySelector("span").textContent = (e && e.message) ? e.message : "Could not resend code";
            err.classList.add("show");
          }
        });
        return;
      }
      label.textContent = "00:" + String(left).padStart(2, "0");
    }, 1000);

    verify.addEventListener("click", async () => {
      verify.disabled = true;
      try {
        if (window.KQApi) {
          const res = await KQApi.verifyOtp({ phone: data.phone, code: code(), name: data.name, email: data.email });
          const payload = res.data || {};
          if (payload.token) KQApi.setToken(payload.token);
          state.user = payload.user || { name: data.name, email: data.email, phone: data.phone };
        } else if (code() !== "111111") {
          throw new Error("Invalid Verification Code");
        } else {
          state.user = { name: data.name, email: data.email, phone: data.phone };
        }
      } catch (e) {
        err.querySelector("span").textContent = (e && e.message) ? e.message : "Invalid Verification Code";
        err.classList.add("show");
        boxes.forEach((b) => b.classList.add("bad"));
        setTimeout(() => boxes.forEach((b) => b.classList.remove("bad")), 700);
        verify.disabled = false;
        return;
      }
      clearInterval(otpTimer);
      LS.set("user", state.user);
      renderUserUI();
      const inline = document.getElementById("kqAuthInline");
      if (inline) { renderSignedIn(inline); } else { KQ.closeAll(); }
      toast("Welcome, " + (state.user.name || "Guest").split(" ")[0] + "!", "fa-check", "success");
      document.dispatchEvent(new CustomEvent("kq:auth"));
      if (pendingCheckout && !/checkout\.html$/i.test(location.pathname)) {
        pendingCheckout = false;
        try { sessionStorage.removeItem("kq_pending_checkout"); } catch (err) {}
        location.href = "checkout.php";
      }
    });
  }

  function renderSignedIn(mount) {
    mount.innerHTML = `
      <button class="kq-auth-x" data-auth-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      <div class="kq-auth-lock done"><i class="fa-solid fa-check"></i></div>
      <h3 class="kq-auth-title">YOU ARE SIGNED IN</h3>
      <p class="kq-auth-sub">${esc(state.user.name)}<br>${esc(state.user.phone)} &middot; ${esc(state.user.email)}</p>
      <button class="kq-auth-btn" id="kqSignOut">SIGN OUT <i class="fa-solid fa-right-from-bracket"></i></button>`;
    mount.querySelector("#kqSignOut").addEventListener("click", () => { signOut(); openAuthInline(mount); });
  }

  function openAuthInline(mount) {
    if (state.user) renderSignedIn(mount);
    else authStepOne(mount, {});
  }

  function openAuth() {
    const modal = document.getElementById("kqAuthModal");
    const box = document.getElementById("kqAuthBox");
    if (!modal || !box) {
      try { sessionStorage.setItem("kq_pending_checkout", pendingCheckout ? "1" : ""); } catch (err) {}
      location.href = "signin.php";
      return;
    }
    if (state.user) renderSignedIn(box);
    else authStepOne(box, {});
    box.querySelectorAll("[data-auth-close]").forEach((b) => b.addEventListener("click", () => KQ.closeAll()));
    KQ.open(modal);
  }

  let pendingCheckout = false;
  try { pendingCheckout = sessionStorage.getItem("kq_pending_checkout") === "1"; } catch (err) {}

  function requireSignIn(msg) {
    if (state.user) return true;
    toast(msg || "Please sign in to confirm your order", "fa-right-to-bracket");
    openAuth();
    return false;
  }

  document.addEventListener("kq:auth", () => {
    if (!state.user || !pendingCheckout) return;
    pendingCheckout = false;
    try { sessionStorage.removeItem("kq_pending_checkout"); } catch (err) {}
    if (!/checkout\.html$/i.test(location.pathname)) location.href = "checkout.php";
  });

  /* ---------------- 4. TOASTS ---------------- */
  function toast(msg, icon, cls) {
    const wrap = document.getElementById("kqToasts");
    if (!wrap) return;
    const el = document.createElement("div");
    el.className = "kq-toast " + (cls || "");
    el.innerHTML = `<i class="fa-solid ${icon || "fa-circle-info"}"></i><span>${esc(msg)}</span>`;
    wrap.appendChild(el);
    setTimeout(() => { el.style.opacity = "0"; el.style.transform = "translateY(12px)"; }, 2200);
    setTimeout(() => el.remove(), 2700);
  }

  /* ---------------- 5. LOCATION ---------------- */
  const locDraft = { city: "Karachi", area: "", mode: "delivery", phone: "", detail: "" };

  function locAlert(msg) {
    const el = document.getElementById("kqLocAlert");
    if (!el) return;
    if (!msg) { el.hidden = true; el.textContent = ""; return; }
    el.hidden = false;
    el.textContent = msg;
  }

  function locSheet(title, items, onPick) {
    const sheet = document.getElementById("kqLocSheet");
    const list = document.getElementById("kqLocSheetList");
    if (!sheet || !list) return;
    const paint = (arr) => {
      list.innerHTML = arr.map((i) => `<button type="button" data-val="${esc(i)}"><span>${esc(i)}</span><small>~ eta 60 min.</small></button>`).join("");
      list.querySelectorAll("[data-val]").forEach((b) => b.addEventListener("click", () => {
        onPick(b.getAttribute("data-val"));
        sheet.hidden = true;
      }));
    };
    paint(items);
    const search = document.getElementById("kqAreaSearch");
    if (search) {
      search.value = "";
      search.oninput = () => {
        const q = search.value.trim().toLowerCase();
        paint(q ? items.filter((i) => i.toLowerCase().includes(q)) : items);
      };
    }
    sheet.hidden = false;
  }

  function syncLocUI() {
    const c = document.getElementById("kqCityVal");
    const a = document.getElementById("kqAreaVal");
    const cur = document.getElementById("kqLocCurrent");
    if (c) c.textContent = locDraft.city;
    if (a) a.textContent = locDraft.area || "Select Area / Sub Region";
    if (cur) cur.textContent = locDraft.area ? locDraft.area + ", " + locDraft.city : locDraft.city;
    const save = document.getElementById("kqSaveAddr");
    if (save) {
      save.disabled = !locDraft.area;
      save.classList.toggle("ready", !!locDraft.area);
    }
  }

  function openLocation() {
    if (state.address) {
      locDraft.city = state.address.city || "Karachi";
      locDraft.area = state.address.area || "";
      locDraft.detail = state.address.detail || "";
      locDraft.phone = state.address.phone || "";
      locDraft.mode = state.address.mode || "delivery";
    }
    locAlert("");
    const det = document.getElementById("kqAddrDetail");
    const ph = document.getElementById("kqLocPhone");
    if (det) det.value = locDraft.detail;
    if (ph) ph.value = locDraft.phone;
    const sheet = document.getElementById("kqLocSheet");
    if (sheet) sheet.hidden = true;
    syncLocUI();

    const on = (id, fn) => { const el = document.getElementById(id); if (el) el.onclick = fn; };
    const closeBtn = document.getElementById("kqLocClose");
    if (closeBtn) closeBtn.hidden = !state.address;
    on("kqCityBtn", () => {});
    on("kqAreaBtn", () => locSheet("Select area", AREAS, (v) => { locDraft.area = v; locAlert(""); syncLocUI(); }));
    on("kqLocSheetClose", () => { if (sheet) sheet.hidden = true; });
    on("kqLocClose", () => { if (state.address) KQ.closeAll(true); });
    on("kqUseLoc", () => {
      if (!navigator.geolocation) return locAlert("Location is not supported on this device. Please select manually.");
      locAlert("Detecting your location…");
      navigator.geolocation.getCurrentPosition(
        () => { locDraft.city = "Karachi"; locDraft.area = AREAS[0]; locAlert(""); syncLocUI(); toast("Location detected", "fa-location-crosshairs", "success"); },
        () => locAlert("Location permission denied. Please select manually.")
      );
    });

    locDraft.mode = "delivery";

    on("kqSaveAddr", () => {
      if (!locDraft.area) return locAlert("Please select your area to continue.");
      state.address = {
        city: locDraft.city,
        area: locDraft.area,
        detail: (det && det.value.trim()) || "",
        phone: (ph && ph.value.trim()) || "",
        mode: locDraft.mode
      };
      LS.set("address", state.address);
      renderLocLabel();
      KQ.closeAll();
      toast("Delivery location saved", "fa-check", "success");
      document.dispatchEvent(new CustomEvent("kq:address"));
    });

    KQ.open(document.getElementById("kqLocModal"));
  }

  function renderLocLabel() {
    const el = document.getElementById("kqLocLabel");
    if (!el) return;
    el.textContent = state.address ? (state.address.detail ? state.address.detail + ", " : "") + state.address.area : "Select your area";
  }

  /* ---------------- 6. CUSTOMIZER ---------------- */
  function isSunday() {
    return new Date().getDay() === 0;
  }

  function openCustomizer(id, editIndex) {
    const d = dish(id);
    if (!d) return;
    if (LIVE.stock[d.id] === false) {
      toast("This item is out of stock", "fa-ban");
      return;
    }
    if (d.sundayOnly && !isSunday()) {
      toast("This deal is only available on Sunday", "fa-calendar-day");
    }
    const existing = typeof editIndex === "number" ? state.cart[editIndex] : null;
    const box = document.getElementById("kqCustBox");
    const cfg = dishCustomize(d);
    const serveTxt = servesLabel(d);
    box.className = "kq-modal-box kq-cust-box";

    // Define bread options first so they can be used in extras
    const breadCats = new Set(["daal", "chicken", "beef", "salan", "curry"]);
    const needsBread = breadCats.has(d.cat) || !!d.addons;
    const breadOpts = DISHES.filter((x) => x.cat === "sides" || /naan|roti/i.test(x.name)).map((x) => ({ label: x.name, price: x.price }));
    const breadBlock = needsBread && breadOpts.length ? `
          <div class="kq-opt-group">
            <div class="kq-opt-label">SELECT BREAD / ACCOMPANIMENT</div>
            ${breadOpts.map((o, i) => optRow("radio", "bread", o, existing && existing.extras && existing.extras.some((e) => e.label === o.label) ? true : i === 0)).join("")}
          </div>` : "";

    const spiceBlock = cfg.spice ? `
          <div class="kq-opt-group">
            <div class="kq-opt-label">SPICE LEVEL</div>
            ${ADDONS.spice.map((o, i) => optRow("radio", "spice", o, existing ? existing.spice === o.label : i === 1)).join("")}
          </div>` : "";
    const extraBlock = cfg.extras ? `
          <div class="kq-opt-group">
            <div class="kq-opt-label">ADD EXTRAS <span class="kq-opt-badge">OPTIONAL</span></div>
            ${breadBlock}
            ${ADDONS.extras.map((o) => optRow("checkbox", "extra", o, existing && existing.extras ? existing.extras.some((e) => e.label === o.label) : false)).join("")}
          </div>` : "";
    const daalOpts = DISHES.filter((x) => x.cat === "daal");
    const salanOpts = DISHES.filter((x) => x.cat === "chicken" || x.cat === "beef");
    const boxPick = (id === "d2");
    const daalBlock = boxPick && daalOpts.length ? `
          <div class="kq-opt-group">
            <div class="kq-opt-label">CHOOSE DAAL</div>
            ${daalOpts.map((o, i) => optRow("radio", "daal", { label: o.name, price: 0 }, existing && existing.daal ? existing.daal === o.name : i === 0)).join("")}
          </div>` : "";
    const salanBlock = boxPick && salanOpts.length ? `
          <div class="kq-opt-group">
            <div class="kq-opt-label">CHOOSE SALAN</div>
            ${salanOpts.map((o, i) => optRow("radio", "salan", { label: o.name, price: 0 }, existing && existing.salan ? existing.salan === o.name : i === 0)).join("")}
          </div>` : "";
    box.innerHTML = `
      <button class="kq-x-btn kq-cust-close" id="kqCustClose" type="button" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      <div class="kq-cust-grid${!cfg.spice && !cfg.extras && !boxPick ? " desc-only" : ""}">
        <div class="kq-cust-left">
          <img src="${IMG + d.img}" alt="${esc(d.name)}">
          <h3>${esc(d.name)}</h3>
          <span class="base-price">${money(d.price)}</span>
          ${serveTxt ? `<span class="kq-serve-pill${d.highlightServes ? " highlight" : ""}"><i class="fa-solid fa-utensils"></i> ${serveTxt}</span>` : ""}
          <p>${esc(d.desc)}</p>
        </div>
        <div class="kq-cust-right">
          ${spiceBlock}
          ${daalBlock}
          ${salanBlock}
          ${extraBlock}
          <div class="kq-field">
            <label><i class="fa-solid fa-pen"></i> Special instructions</label>
            <textarea id="kqNote" placeholder="e.g. kam mirch, extra gravy" style="min-height:70px">${existing ? esc(existing.note || "") : ""}</textarea>
          </div>
          <div class="kq-cust-alert" id="kqCustAlert"><i class="fa-solid fa-triangle-exclamation"></i><span>Please complete the required choices.</span></div>
          <div class="kq-cust-foot">
            <div class="kq-qty">
              <button type="button" data-q="-1">&minus;</button><span id="kqCustQty">${existing ? existing.qty : 1}</span><button type="button" data-q="1">+</button>
            </div>
            <button class="kq-btn" id="kqCustAdd"><i class="fa-solid fa-basket-shopping"></i> <span id="kqCustAddTxt"></span></button>
          </div>
        </div>
      </div>`;

    let qty = existing ? existing.qty : 1;

    const calc = () => {
      let p = d.price;
      box.querySelectorAll('input[name="extra"]:checked, input[name="bread"]:checked').forEach((c) => { p += Number(c.dataset.price) || 0; });
      return p;
    };
    const sync = () => {
      box.querySelectorAll(".kq-choice").forEach((c) => c.classList.toggle("selected", c.querySelector("input").checked));
      box.querySelector("#kqCustQty").textContent = qty;
      box.querySelector("#kqCustAddTxt").textContent = (existing ? "Update — " : "Add — ") + money(calc() * qty);
    };

    const closeBtn = box.querySelector("#kqCustClose");
    if (closeBtn) closeBtn.addEventListener("click", () => KQ.closeAll());

    box.querySelectorAll("input").forEach((i) => i.addEventListener("change", sync));
    box.querySelectorAll("[data-q]").forEach((b) => b.addEventListener("click", () => {
      qty = Math.max(1, qty + Number(b.dataset.q));
      sync();
    }));
    box.querySelector("#kqCustAdd").addEventListener("click", () => {
      if (d.sundayOnly && !isSunday()) {
        return toast("This deal is only available on Sunday", "fa-calendar-day");
      }
      const spice = box.querySelector('input[name="spice"]:checked');
      if (cfg.spice && !spice) { box.querySelector("#kqCustAlert").classList.add("show"); return; }
      if (boxPick) {
        const daal = box.querySelector('input[name="daal"]:checked');
        const salan = box.querySelector('input[name="salan"]:checked');
        if (!daal || !salan) { box.querySelector("#kqCustAlert").classList.add("show"); return; }
      }
      const extras = Array.from(box.querySelectorAll('input[name="extra"]:checked, input[name="bread"]:checked')).map((c) => ({ label: c.value, price: Number(c.dataset.price) }));
      const daalVal = (box.querySelector('input[name="daal"]:checked') || {}).value || "";
      const salanVal = (box.querySelector('input[name="salan"]:checked') || {}).value || "";
      if (daalVal) extras.unshift({ label: "Daal: " + daalVal, price: 0 });
      if (salanVal) extras.unshift({ label: "Salan: " + salanVal, price: 0 });
      const item = {
        id: d.id, name: d.name, img: d.img, base: d.price,
        portion: serveTxt || "",
        spice: spice ? spice.value : "",
        daal: daalVal, salan: salanVal,
        extras,
        note: box.querySelector("#kqNote").value.trim(),
        unit: calc(), qty
      };
      if (existing) { state.cart[editIndex] = item; toast("Item updated", "fa-check", "success"); }
      else { state.cart.push(item); toast(d.name + " added to basket", "fa-check", "success"); }
      persistCart();
      KQ.closeAll();
      KQ.open(document.getElementById("kqCartDrawer"));
    });

    sync();
    KQ.open(document.getElementById("kqCustModal"));
  }

  function optRow(type, name, o, checked) {
    return `<label class="kq-choice${checked ? " selected" : ""}">
      <span class="choice-left">
        <input type="${type}" name="${name}" value="${esc(o.label)}" data-price="${o.price}" ${checked ? "checked" : ""}>
        <span>${esc(o.label)}</span>
      </span>
      <span class="choice-price">${o.price ? "+" + money(o.price) : "Free"}</span>
    </label>`;
  }

  /* ---------------- 7. CART ---------------- */
  function persistCart() { LS.set("cart", state.cart); renderCart(); document.dispatchEvent(new CustomEvent("kq:cart")); }

  function addToCart(id, opts, qty) {
    const d = dish(id);
    if (!d) return;
    if (LIVE.stock[d.id] === false) {
      return toast("Out of stock", "fa-ban");
    }
    if (!LIVE.storeOnline) {
      return toast(LIVE.storeMessage, "fa-store-slash");
    }
    if (d.sundayOnly && !isSunday()) {
      return toast("This deal is only available on Sunday", "fa-calendar-day");
    }
    state.cart.push({
      id: d.id, name: d.name, img: d.img, base: d.price,
      portion: (opts && opts.portion) || servesLabel(d),
      spice: (opts && opts.spice) || "",
      extras: [], note: "", unit: d.price, qty: qty || 1
    });
    persistCart();
    toast(d.name + " added to basket", "fa-check", "success");
  }

  function areaFee() {
    const area = state.address && state.address.area;
    if (!area) return DELIVERY_FEE;
    if (LIVE.fees[area] != null) return Number(LIVE.fees[area]);
    return DELIVERY_FEE;
  }

  function totals() {
    const sub = state.cart.reduce((s, i) => s + i.unit * i.qty, 0);
    const discount = state.promo && PROMOS[state.promo] ? Math.round(sub * PROMOS[state.promo]) : 0;
    const cap = LIVE.freeOver ?? FREE_DELIVERY_OVER;
    const pickup = state.address && state.address.mode === "pickup";
    const delivery = sub === 0 || pickup ? 0 : (sub - discount >= cap && cap > 0 ? 0 : areaFee());
    return { sub, discount, delivery, total: sub - discount + delivery };
  }

  function renderCart() {
    const count = state.cart.reduce((s, i) => s + i.qty, 0);
    ["kqCartBadge", "kqDockBadge"].forEach((id) => {
      const badge = document.getElementById(id);
      if (badge) { badge.textContent = count; badge.style.display = count ? "flex" : "none"; }
    });

    const body = document.getElementById("kqCartBody");
    if (!body) return;
    if (!state.cart.length) {
      body.innerHTML = `<div class="kq-cart-empty">
        <i class="fa-solid fa-basket-shopping"></i>
        <p>Your basket is empty.<br>Add something delicious!</p>
        <a class="kq-btn kq-btn-outline" href="index.php"><i class="fa-solid fa-utensils"></i> Browse Menu</a>
      </div>`;
      return;
    }
    const t = totals();
    body.innerHTML = `
      ${state.cart.map((i, idx) => `
        <div class="kq-cart-item">
          <img src="${IMG + i.img}" alt="${esc(i.name)}">
          <div class="kq-ci-info">
            <p class="kq-ci-name">${esc(i.name)}</p>
            <p class="kq-ci-meta">${[i.portion, i.spice].filter(Boolean).map(esc).join(" · ")}${i.extras && i.extras.length ? "<br>+ " + i.extras.map((e) => esc(e.label)).join(", ") : ""}${i.note ? "<br><em>“" + esc(i.note) + "”</em>" : ""}</p>
            <div class="kq-ci-row">
              <div class="kq-qty"><button data-cq="${idx}:-1">&minus;</button><span>${i.qty}</span><button data-cq="${idx}:1">+</button></div>
              <span class="kq-ci-price">${money(i.unit * i.qty)}</span>
            </div>
            <div class="kq-ci-actions">
              <button class="kq-ci-edit" data-cedit="${idx}"><i class="fa-solid fa-pen"></i> Edit</button>
              <button class="kq-ci-del" data-cdel="${idx}"><i class="fa-solid fa-trash"></i> Remove</button>
            </div>
          </div>
        </div>`).join("")}

      <p class="kq-upsell-title">GOES WELL WITH</p>
      <div class="kq-upsell-row">
        ${UPSELL.map(dish).filter(Boolean).map((d) => `
          <div class="kq-upsell-card">
            <img src="${IMG + d.img}" alt="${esc(d.name)}">
            <p>${esc(d.name)}</p>
            <button data-quickadd="${d.id}">+ ${money(d.price)}</button>
          </div>`).join("")}
      </div>

      <div class="kq-promo-row" hidden>
        <input type="text" id="kqPromoInput" placeholder="Promo code" value="" disabled>
        <button class="kq-btn kq-btn-dark" id="kqPromoBtn" type="button" disabled>APPLY</button>
      </div>

      <div class="kq-bill">
        <div class="kq-bill-row"><span>Subtotal</span><span>${money(t.sub)}</span></div>
        ${t.discount ? `<div class="kq-bill-row"><span>Discount (${state.promo})</span><span style="color:var(--green)">- ${money(t.discount)}</span></div>` : ""}
        <div class="kq-bill-row"><span>Delivery</span><span class="${t.delivery ? "" : "free"}">${t.delivery ? money(t.delivery) : "FREE"}</span></div>
        <div class="kq-bill-row total"><span>Total</span><span>${money(t.total)}</span></div>
      </div>`;

    body.querySelectorAll("[data-cq]").forEach((b) => b.addEventListener("click", () => {
      const [idx, delta] = b.dataset.cq.split(":").map(Number);
      state.cart[idx].qty += delta;
      if (state.cart[idx].qty <= 0) state.cart.splice(idx, 1);
      persistCart();
    }));
    body.querySelectorAll("[data-cdel]").forEach((b) => b.addEventListener("click", () => {
      state.cart.splice(Number(b.dataset.cdel), 1);
      persistCart();
      toast("Item removed", "fa-trash");
    }));
    body.querySelectorAll("[data-cedit]").forEach((b) => b.addEventListener("click", () => {
      const idx = Number(b.dataset.cedit);
      openCustomizer(state.cart[idx].id, idx);
    }));
    const pBtn = document.getElementById("kqPromoBtn");
    if (pBtn) pBtn.addEventListener("click", () => {
      toast("Promo codes are not active yet", "fa-tag");
    });
  }

  /* ---------------- 8. CARD RENDERERS ---------------- */
  function dishCard(d) {
    const oos = LIVE.stock[d.id] === false;
    return `<article class="kq-card${oos ? " kq-oos" : ""}" data-add="${d.id}" style="cursor:pointer">
      <div class="kq-card-img">
        <img src="${/^https?:|\//.test(String(d.img || "")) ? d.img : IMG + d.img}" alt="${esc(d.name)}" loading="lazy">
        ${d.tag ? `<span class="kq-deal-tag${d.tag === "DEAL" ? " red" : ""}">${d.tag}</span>` : ""}
        ${oos ? `<span class="kq-deal-tag red">OUT OF STOCK</span>` : ""}
      </div>
      <div class="kq-card-body">
        <h3 class="kq-card-title">${esc(d.name)}</h3>
        <p class="kq-card-desc">${esc(d.desc)}</p>
        <div class="kq-card-foot">
          <span class="kq-price">${money(d.price)}</span>
          <button class="kq-add-btn" data-add="${d.id}" aria-label="Add ${esc(d.name)}"><i class="fa-solid fa-plus"></i></button>
        </div>
      </div>
    </article>`;
  }

  /* ---------------- 9. PAGE MODULES ---------------- */
  const pages = {
    index() {
      const cats = document.getElementById("kqCats");
      const grid = document.getElementById("kqGrid");
      if (!grid) return;
      let active = "all";
      let notice = document.getElementById("kqSpecialNotice");
      if (!notice) {
        notice = document.createElement("div");
        notice.id = "kqSpecialNotice";
        notice.className = "kq-special-notice";
        notice.hidden = true;
        notice.innerHTML = `<i class="fa-solid fa-calendar-day"></i><div><b>Sunday only</b><p>These special deals are available on Sunday only. Please order regular items from the menu on other days.</p></div>`;
        grid.parentNode.insertBefore(notice, grid);
      }
      const draw = () => {
        notice.hidden = active !== "special";
        grid.innerHTML = DISHES.filter((d) => {
          if (d.cat === "special" && active !== "special") return false;
          if (active === "all") return true;
          if (active === "hot") return !!d.hot;
          return d.cat === active;
        }).map(dishCard).join("");
      };
      const bannerGrid = document.getElementById("kqBannerGrid");
      if (bannerGrid) {
        bannerGrid.innerHTML = BANNER_FEATURED.map((id) => dish(id)).filter(Boolean).map(dishCard).join("");
      }
      const hero = document.getElementById("heroCarousel");
      if (hero && window.bootstrap) {
        bootstrap.Carousel.getOrCreateInstance(hero, { interval: 4000, ride: "carousel", wrap: true, pause: "hover" });
      }
      if (cats) {
        const pills = [{ id: "all", label: "All", icon: "fa-utensils" }, ...CATEGORIES];
        cats.innerHTML = pills.map((c) => `<button class="kq-cat-pill${c.id === "all" ? " active" : ""}" data-cat="${c.id}"><i class="fa-solid ${c.icon}"></i> ${c.label}</button>`).join("");
        cats.querySelectorAll("[data-cat]").forEach((b) => b.addEventListener("click", () => {
          cats.querySelectorAll(".kq-cat-pill").forEach((x) => x.classList.remove("active"));
          b.classList.add("active");
          active = b.dataset.cat;
          draw();
        }));
      }
      draw();
      window.kqDrawMenu = draw;
    },

    menu() {
      const wrap = document.getElementById("kqWeek");
      if (!wrap) return;
      const today = new Date().toLocaleDateString("en-US", { weekday: "long" });
      const slots = ["MAIN", "SECOND", "SIDE"];
      wrap.innerHTML = `
        <article class="kq-brochure">
          <header class="kq-brochure-cover">
            <div>
              <span class="kq-brochure-kicker"><i class="fa-solid fa-crown"></i> KITCHEN QUEEN · KARACHI</span>
              <h1>Weekly Menu Brochure</h1>
              <p>Seven days of ghar ka khana — the same plates, photos and prices as the website. Cooked in the morning, delivered hot.</p>
              <button class="kq-btn kq-btn-outline kq-brochure-print" id="kqPrint" type="button"><i class="fa-solid fa-print"></i> Print Brochure</button>
            </div>
            <div class="kq-brochure-cover-meta">
              <b>This week</b>
              <span>${new Date().toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })}</span>
              <small>North Nazimabad · 12PM – 12AM</small>
            </div>
          </header>
          <div class="kq-brochure-days">
            ${WEEK.map((w) => {
              const meals = (w.ids || []).map((id) => dish(id)).filter(Boolean);
              return `<section class="kq-brochure-day${w.day === today ? " is-today" : ""}">
                <div class="kq-brochure-dayname">
                  <span>${w.day.slice(0, 3).toUpperCase()}</span>
                  <b>${w.day}</b>${w.day === today ? "<em>TODAY</em>" : ""}
                </div>
                <div class="kq-brochure-meals">
                  ${meals.map((m, i) => `
                    <article class="kq-brochure-meal">
                      <div class="kq-brochure-photo">
                        <img src="${IMG + m.img}" alt="${esc(m.name)}">
                        <span>${slots[i] || "PLATE"}</span>
                      </div>
                      <div class="kq-brochure-info">
                        <h3>${esc(m.name)}</h3>
                        <p>${esc(m.desc)}</p>
                        <div class="kq-brochure-foot">
                          <strong>${money(m.price)}</strong>
                          <button type="button" class="kq-add-btn" data-add="${m.id}" aria-label="Add ${esc(m.name)}"><i class="fa-solid fa-plus"></i></button>
                        </div>
                      </div>
                    </article>`).join("")}
                </div>
              </section>`;
            }).join("")}
          </div>
          <footer class="kq-brochure-end">Prices match the live Kitchen Queen menu · Call 0336-2465676</footer>
        </article>`;
      const printBtn = document.getElementById("kqPrint");
      if (printBtn) printBtn.addEventListener("click", () => window.print());
    },

    blog() {
      const grid = document.getElementById("kqBlogGrid");
      if (!grid) return;
      grid.innerHTML = BLOG.map((b, i) => `
        <article class="kq-blog-card">
          <img src="${IMG + b.img}" alt="${esc(b.title)}" loading="lazy" onerror="this.onerror=null;this.src='images/chicken biryani.webp'">
          <div class="kq-blog-body">
            <div class="kq-blog-cat">${b.cat}</div>
            <h3>${esc(b.title)}</h3>
            <p>${esc(b.text.slice(0, 110))}…</p>
            <button class="kq-read-more" data-post="${i}">Read the full story <i class="fa-solid fa-arrow-right"></i></button>
          </div>
        </article>`).join("");
      grid.querySelectorAll("[data-post]").forEach((b) => b.addEventListener("click", () => {
        const p = BLOG[Number(b.dataset.post)];
        const box = document.getElementById("kqCustBox");
        box.className = "kq-modal-box kq-article-box";
        box.innerHTML = `
          <img src="${IMG + p.img}" alt="${esc(p.title)}" onerror="this.onerror=null;this.src='images/chicken biryani.webp'">
          <div class="kq-article-content">
            <div class="kq-blog-cat">${p.cat}</div>
            <h3>${esc(p.title)}</h3>
            <p>${esc(p.text)}</p>
            <button class="kq-btn kq-btn-outline" id="kqArtClose"><i class="fa-solid fa-xmark"></i> Close</button>
          </div>`;
        KQ.open(document.getElementById("kqCustModal"));
        box.querySelector("#kqArtClose").addEventListener("click", KQ.closeAll);
      }));
    },

    "live-counter"() {
      const mealsEl = document.querySelector("[data-live=meals]");
      const kitchenEl = document.querySelector("[data-live=kitchen]");
      const doneEl = document.querySelector("[data-live=completed]");
      const feed = document.getElementById("kqFeed");
      const ago = (iso) => {
        const t = new Date(String(iso || "").replace(" ", "T"));
        if (isNaN(t)) return "now";
        const m = Math.max(0, Math.round((Date.now() - t.getTime()) / 60000));
        if (m < 1) return "just now";
        if (m < 60) return m + " min ago";
        const h = Math.floor(m / 60);
        return h + "h ago";
      };
      const badge = (st) => {
        const s = String(st || "").toLowerCase();
        if (["preparing", "ready", "accepted"].includes(s)) return { cls: "kq-st-prep", text: "Preparing" };
        if (["completed", "delivered", "out_for_delivery"].includes(s)) return { cls: "kq-st-done", text: "Delivered" };
        return { cls: "kq-st-wait", text: "Pending" };
      };
      const dishImg = (name) => {
        const d = DISHES.find((x) => String(x.name).toLowerCase() === String(name || "").toLowerCase());
        return IMG + (d ? d.img : "chicken biryani.webp");
      };
      const setNum = (el, n) => { if (el) el.textContent = Number(n || 0).toLocaleString("en-PK"); };
      const paintFeed = (rows) => {
        if (!feed) return;
        if (!rows.length) {
          feed.innerHTML = `<p style="color:var(--muted);margin:0">No live orders yet. New orders will appear here.</p>`;
          return;
        }
        feed.innerHTML = rows.slice(0, 10).map((r) => {
          const b = badge(r.status);
          return `<article class="kq-live-card">
            <img src="${dishImg(r.item)}" alt="${esc(r.item)}">
            <div>
              <p class="kq-live-name">${esc(r.name)} · ${esc(r.item)}</p>
              <p class="kq-live-meta">${esc(r.qty ? ("x" + r.qty + " · ") : "")}${esc(r.area || "Karachi")} · ${esc(ago(r.placed_at))}</p>
            </div>
            <span class="kq-status-badge ${b.cls}">${b.text}</span>
          </article>`;
        }).join("");
      };
      const load = async () => {
        if (!window.KQApi) return;
        try {
          const res = await KQApi.live();
          const d = (res && res.data) || {};
          const feedRows = d.feed || [];
          const kitchen = feedRows.filter((r) => /pending|preparing|ready|accepted|confirmed|awaiting/i.test(String(r.status || ""))).length;
          const visitsEl = document.querySelector("[data-live='visits']");
          const ordersEl = document.querySelector("[data-live='orders']");
          const bounceEl = document.querySelector("[data-live='bouncers']");
          const rateEl = document.querySelector("[data-live='rate']");
          if (visitsEl) setNum(visitsEl, d.visits);
          if (ordersEl) setNum(ordersEl, d.orders != null ? d.orders : d.orders_today);
          if (bounceEl) setNum(bounceEl, d.bouncers);
          if (rateEl) rateEl.textContent = Number(d.conversion_rate || 0) + "%";
          setNum(mealsEl, d.meals_today);
          setNum(kitchenEl, d.kitchen || kitchen);
          setNum(doneEl, d.families || d.completed || 0);
          paintFeed(feedRows);
        } catch (e) {
          if (feed) feed.innerHTML = `<p style="color:var(--muted);margin:0">Live feed unavailable.</p>`;
        }
      };
      load();
      setInterval(load, 8000);
    },

    contact() {
      const form = document.getElementById("kqContactForm");
      if (!form) return;
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!validate(form)) return;
        form.reset();
        toast("Message sent — we'll reply within an hour!", "fa-paper-plane", "success");
      });
    },

    catering() {
      const form = document.getElementById("kqCaterForm");
      if (!form) return;
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!validate(form)) return;
        const guests = Number(form.querySelector("#guests").value || 0);
        toast("Quote request received for " + guests + " guests!", "fa-champagne-glasses", "success");
        form.reset();
      });
    },

    "meal-builder"() {
      const STEPS = [
        { key: "rice", name: "Rice / Bread", opts: [["chicken biryani", 350, "chicken biryani.webp"], ["sweet Dahi Phulki", 280, "Sweet phulki.webp"], ["Tandoori Naan (4 pcs)", 200, "naan.jpg"], ["Plain white rice", 250, "plain-white-rice.jpg"]] },
        { key: "main", name: "Main Dish", opts: [["Chicken Karahi", 850, "karahi.jpg"], ["Beef Haleem", 480, "beef haleem.webp"], ["Aloo Qeema", 450, "keema-Aaloo.webp"], ["Aloo Gosht", 450, "Aloo gohst.webp"]] },
        { key: "side", name: "Side", opts: [["Shami Kabab (6 pcs)", 500, "shami.webp"], ["Mint Raita", 90, "baingan raita.webp"], ["Green Salad", 70, "Lauki-Sabzi.webp"], ["Daal Chawal", 350, "Daal Chawal.jpg"]] },
        { key: "sweet", name: "Dessert & Drink", opts: [["Desi Kheer", 180, "kheer.webp"], ["Cold Drink", 150, "pepsi.avif"], ["Kheer + Drink", 330, "kheer.webp"], ["Chicken Qourma", 450, "chicken qourma.webp"]] }
      ];
      const tabs = document.getElementById("kqSteps");
      const panel = document.getElementById("kqStepPanel");
      const trayItems = document.getElementById("kqTrayItems");
      const trayTotal = document.getElementById("kqTrayTotal");
      const plateCount = document.getElementById("kqPlateCount");
      if (!panel) return;
      let step = 0;
      const picks = {};

      const drawTray = () => {
        const chosen = STEPS.filter((s) => picks[s.key]);
        trayItems.innerHTML = chosen.length
          ? chosen.map((s) => `<div class="kq-tray-item"><span>${esc(picks[s.key].label)}</span><span>${money(picks[s.key].price)} <button data-clear="${s.key}"><i class="fa-solid fa-xmark"></i></button></span></div>`).join("")
          : `<p style="color:var(--muted);font-size:.8rem;text-align:center;margin:0">Nothing on your thali yet.</p>`;
        const total = chosen.reduce((s, x) => s + picks[x.key].price, 0);
        trayTotal.textContent = money(total);
        plateCount.innerHTML = `<b>${chosen.length}</b><small>OF 4</small>`;
        trayItems.querySelectorAll("[data-clear]").forEach((b) => b.addEventListener("click", () => { delete picks[b.dataset.clear]; drawTray(); drawStep(); }));
      };

      const drawStep = () => {
        tabs.innerHTML = STEPS.map((s, i) => `<button class="kq-step-tab${i === step ? " active" : ""}${picks[s.key] ? " done" : ""}" data-step="${i}"><span class="step-no">STEP ${i + 1}</span><span class="step-name">${s.name}</span></button>`).join("");
        tabs.querySelectorAll("[data-step]").forEach((b) => b.addEventListener("click", () => { step = Number(b.dataset.step); drawStep(); }));
        const s = STEPS[step];
        panel.innerHTML = `
          <div class="kq-opt-grid">
            ${s.opts.map((o) => `<button class="kq-build-opt${picks[s.key] && picks[s.key].label === o[0] ? " selected" : ""}" data-pick="${esc(o[0])}|${o[1]}">
              <img src="${IMG + o[2]}" alt="${esc(o[0])}" loading="lazy">
              <b>${esc(o[0])}</b><span>${o[1] ? money(o[1]) : "Free"}</span>
            </button>`).join("")}
          </div>
          <div class="kq-build-nav">
            <button class="kq-btn kq-btn-outline" id="kqPrev" ${step === 0 ? "disabled" : ""}><i class="fa-solid fa-arrow-left"></i> Back</button>
            <button class="kq-btn" id="kqNext">${step === STEPS.length - 1 ? "Finish" : "Next"} <i class="fa-solid fa-arrow-right"></i></button>
          </div>`;
        panel.querySelectorAll("[data-pick]").forEach((b) => b.addEventListener("click", () => {
          const [label, price] = b.dataset.pick.split("|");
          picks[s.key] = { label, price: Number(price) };
          drawTray(); drawStep();
        }));
        panel.querySelector("#kqPrev").addEventListener("click", () => { if (step > 0) { step--; drawStep(); } });
        panel.querySelector("#kqNext").addEventListener("click", () => {
          if (step < STEPS.length - 1) { step++; drawStep(); }
          else toast("Thali ready — add it to your basket!", "fa-wand-magic-sparkles", "success");
        });
      };

      document.getElementById("kqTrayAdd").addEventListener("click", () => {
        const chosen = STEPS.filter((s) => picks[s.key]);
        if (chosen.length < 2) return toast("Pick at least 2 items for a thali", "fa-triangle-exclamation");
        const total = chosen.reduce((s, x) => s + picks[x.key].price, 0);
        state.cart.push({
          id: "custom", name: "Custom Thali", img: "chicken biryani.webp", base: total,
          portion: "Custom Thali", spice: "Medium (Desi)",
          extras: chosen.map((s) => ({ label: picks[s.key].label, price: 0 })),
          note: "", unit: total, qty: 1
        });
        persistCart();
        toast("Custom Thali added to basket", "fa-check", "success");
        KQ.open(document.getElementById("kqCartDrawer"));
      });

      drawStep(); drawTray();
    },

    checkout() {
      const wrap = document.getElementById("kqOrderLines");
      const form = document.getElementById("kqCheckoutForm");
      if (!wrap) return;

      let selectedPay = "cod";
      let lastOrder = null;
      let epSlipData = "";
      let placing = false;
      let quote = null;
      let quoteVersion = 0;
      let quoteTimer;
      let requestKey = sessionStorage.getItem('kq_checkout_key') || crypto.randomUUID();
      sessionStorage.setItem('kq_checkout_key', requestKey);
      const payload = () => ({
        order_type: state.address?.mode === 'pickup' ? 'pickup' : 'delivery',
        area_name: state.address?.area || '',
        items: state.cart.map(i => ({menu_item_id:i.id,qty:i.qty,spice:i.spice || '',extras:i.extras || []}))
      });
      const refreshQuote = async () => {
        const version = ++quoteVersion;
        quote = null;
        if (!state.cart.length) return;
        document.getElementById('kqConfirmOrder').disabled = true;
        try {
          const res = await KQApi.quote(payload());
          if (version !== quoteVersion) return;
          quote = res.data;
          draw();
          document.getElementById('kqConfirmOrder').disabled = placing;
        } catch(e) {
          if (version !== quoteVersion) return;
          const message = document.createElement('p'); message.className='kq-quote-error';
          message.setAttribute('role','alert'); message.textContent=e.message;
          wrap.querySelector('.kq-quote-error')?.remove(); wrap.appendChild(message);
        }
      };
      const scheduleQuote = () => {clearTimeout(quoteTimer);quoteTimer=setTimeout(refreshQuote,150);};

      const readSlip = (file, cb) => {
        if (!file) return cb("");
        if (!/^image\//.test(file.type)) return toast("Please upload a payment screenshot image", "fa-triangle-exclamation");
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
          const max = 900;
          let w = img.width, h = img.height;
          if (w > max || h > max) {
            const r = Math.min(max / w, max / h);
            w = Math.round(w * r); h = Math.round(h * r);
          }
          const c = document.createElement("canvas");
          c.width = w; c.height = h;
          c.getContext("2d").drawImage(img, 0, 0, w, h);
          URL.revokeObjectURL(url);
          cb(c.toDataURL("image/jpeg", 0.72));
        };
        img.onerror = () => { URL.revokeObjectURL(url); toast("Could not read screenshot", "fa-triangle-exclamation"); };
        img.src = url;
      };

      const slipInput = document.getElementById("epSlip");
      const slipPreview = document.getElementById("epSlipPreview");
      if (slipInput) {
        slipInput.addEventListener("change", () => {
          const f = slipInput.files && slipInput.files[0];
          readSlip(f, (data) => {
            epSlipData = data;
            if (slipPreview) {
              if (data) {
                slipPreview.hidden = false;
                slipPreview.querySelector("img").src = data;
              } else {
                slipPreview.hidden = true;
              }
            }
            slipInput.closest(".kq-ep-upload")?.classList.toggle("has-file", !!data);
          });
        });
      }
      const copyBtn = document.getElementById("epCopy");
      if (copyBtn) {
        copyBtn.addEventListener("click", async () => {
          try {
            await navigator.clipboard.writeText(EASYPAISA.number.replace(/-/g, ""));
            toast("EasyPaisa number copied", "fa-copy", "success");
          } catch (err) {
            toast(EASYPAISA.number, "fa-mobile-screen");
          }
        });
      }

      const draw = () => {
        const t = quote ? {sub:quote.subtotal,discount:quote.discount,delivery:quote.fee,total:quote.total,tax:quote.tax} : totals();
        wrap.innerHTML = state.cart.length
          ? state.cart.map((i) => `<div class="bp-sum-item">
                <img src="${IMG + (i.img || "chicken biryani.webp")}" alt="">
                <div>
                  <b>${i.qty}x ${esc(i.name)}</b>
                  <small>${esc([i.portion, i.spice].filter(Boolean).join(", ") || "Home-cooked")}</small>
                </div>
                <span class="price">${money(quote?.rows?.[state.cart.indexOf(i)]?.line_total ?? i.unit * i.qty)}</span>
              </div>`).join("") +
            `<div class="bp-bill">
              <div class="bp-bill-row"><span>Items Total</span><span>${money(t.sub)}</span></div>
              ${t.discount ? `<div class="bp-bill-row"><span>Discount</span><span>- ${money(t.discount)}</span></div>` : ""}
              <div class="bp-bill-row"><span>Delivery Fee</span><span>${t.delivery ? money(t.delivery) : "Free"}</span></div>
              ${t.tax ? `<div class="bp-bill-row"><span>Tax</span><span>${money(t.tax)}</span></div>` : ""}<div class="bp-bill-row total"><span>TOTAL PAYABLE</span><span>${money(t.total)}</span></div>
            </div>`
          : `<p style="color:#888;font-size:.85rem">Your basket is empty. <a href="index.php" style="color:#e0a800;font-weight:700">Browse the menu →</a></p>`;
      };
      draw();
      document.addEventListener("kq:cart", () => {draw();scheduleQuote();});
      document.addEventListener("kq:address", scheduleQuote);
      scheduleQuote();

      const epBox = document.getElementById("kqEasyPaisaFields");
      const payBtns = document.querySelectorAll(".kq-pay-opt, .bp-pay");
      payBtns.forEach((p) => p.addEventListener("click", () => {
        payBtns.forEach((x) => {x.classList.remove("selected");x.setAttribute("aria-pressed","false");});
        p.classList.add("selected");p.setAttribute("aria-pressed","true");
        selectedPay = p.dataset.pay || "cod";
        if (epBox) epBox.hidden = selectedPay !== "easypaisa";
      }));
      const outlet = document.getElementById("kqOutletTag");
      const aptInput = form.querySelector("#apt");
      const streetInput = form.querySelector("#street");
      const addrHidden = form.querySelector("#address");

      const fillAddressFields = () => {
        if (outlet) outlet.textContent = ((state.address && state.address.area) || "KARACHI").toUpperCase();
        if (state.address) {
          const detail = state.address.detail || "";
          if (aptInput && !aptInput.value) {
            const parts = detail.split(",");
            if (parts.length > 1) {
              aptInput.value = parts[0].trim();
              if (streetInput && !streetInput.value) streetInput.value = parts.slice(1).join(",").trim();
            } else if (streetInput && !streetInput.value) {
              streetInput.value = detail;
            }
          }
          if (addrHidden) {
            const apt = (aptInput && aptInput.value.trim()) || "";
            const street = (streetInput && streetInput.value.trim()) || detail;
            const area = state.address.area || "Karachi";
            addrHidden.value = [apt, street, area].filter(Boolean).join(", ");
          }
        } else if (addrHidden) {
          addrHidden.value = "";
        }
      };
      fillAddressFields();
      document.addEventListener("kq:address", fillAddressFields);

      const editMap = document.getElementById("kqEditMap");
      const mapBar = document.getElementById("kqMapBar");
      if (editMap) editMap.addEventListener("click", () => openLocation());
      if (mapBar) {
        mapBar.addEventListener("click", () => openLocation());
        mapBar.addEventListener("keydown", (ev) => {
          if (ev.key === "Enter" || ev.key === " ") { ev.preventDefault(); openLocation(); }
        });
      }

      const fillUserFields = () => {
        if (!state.user) return;
        const n = form.querySelector("#fullname");
        const ph = form.querySelector("#phone");
        const em = form.querySelector("#email");
        if (n && !n.value) n.value = state.user.name || "";
        if (ph && !ph.value) ph.value = state.user.phone || "";
        if (em && !em.value) em.value = state.user.email || "";
      };
      fillUserFields();
      document.addEventListener("kq:auth", fillUserFields);

      form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!state.cart.length) return toast("Your basket is empty", "fa-basket-shopping");
        if (!state.user) {
          pendingCheckout = true;
          return requireSignIn("Please sign in to confirm your order");
        }
        if (!validate(form)) return;
        if (selectedPay === "easypaisa") {
          if (!epSlipData) {
            document.querySelector(".kq-ep-upload")?.classList.add("invalid");
            return toast("Please upload your EasyPaisa payment screenshot", "fa-triangle-exclamation");
          }
        }
        placeOrder();
      });

      async function placeOrder() {
        if (placing || !quote) return;
        placing = true;
        const button = document.getElementById('kqConfirmOrder');
        button.disabled = true; button.textContent = 'Placing your order…';
        try {
          if (!window.KQApi) throw new Error('Connection unavailable. Please reload.');
          const address = [form.querySelector('#apt')?.value.trim(),form.querySelector('#street')?.value.trim(),state.address?.area].filter(Boolean).join(', ');
          const res = await KQApi.placeOrder({...payload(), request_key:requestKey,
            customer_name:form.querySelector('#fullname').value.trim(), customer_phone:form.querySelector('#phone').value.trim(),
            customer_email:form.querySelector('#email').value.trim(), address_line:address,
            notes:form.querySelector('#note')?.value.trim() || '', payment_method:selectedPay,
            screenshot_base64:selectedPay === 'easypaisa' ? epSlipData : '', expected_total:quote.total
          });
          const order = res.data;
          if (!order?.id) throw new Error('Order response unavailable. Retry to recover your order.');
          lastOrder = {id:order.id,orderNo:order.order_no,phone:order.customer_phone};
          localStorage.setItem('kq_last_order',JSON.stringify(lastOrder));
          sessionStorage.removeItem('kq_checkout_key');
          history.replaceState(null,'','checkout.php?order='+encodeURIComponent(order.order_no));
          state.cart=[];state.promo=null;persistCart();
          showOrder(order);
        } catch(e) { toast(e.message || 'Could not place order','fa-triangle-exclamation'); }
        finally {placing=false;button.disabled=!quote;button.textContent='CONFIRM ORDER';}
      }
      let orderTimer;
      function showOrder(order) {
        clearTimeout(orderTimer);
        document.querySelector('.bp-checkout').hidden=true;
        const stage=document.getElementById('kqReceiptStage');stage.hidden=false;
        const rejected=['payment_rejected','cancelled'].includes(order.status);
        const ready=order.receipt_ready && !rejected;
        if (!ready) {
          stage.innerHTML='<section class="kq-approval" aria-live="polite"><p class="kq-approval-kicker">'+esc(order.order_no)+'</p><h1>'+(rejected?'Payment or order not approved':'Waiting for admin approval')+'</h1><p>'+(rejected?'Your receipt has not been issued. Please contact Kitchen Queen about your order.':'Your payment screenshot has been submitted. Your receipt will appear here automatically once the admin approves it.')+'</p><p><strong>'+money(order.total)+'</strong> · EasyPaisa</p><p id="approvalConnection">'+(rejected?'':'Checking payment status…')+'</p><a class="kq-btn" href="my_orders.php">My orders</a></section>';
          if (!rejected) pollOrder(order.order_no);
          return;
        }
        const items=order.items.map(i=>'<tr><td>'+i.qty+' × '+esc(i.name)+'<small>'+esc([i.spice,i.extras].filter(Boolean).join(' · '))+'</small></td><td>'+money(i.line_total)+'</td></tr>').join('');
        stage.innerHTML='<section class="kq-order-receipt"><p class="kq-approval-kicker">KITCHEN QUEEN · KARACHI</p><h1>Order receipt</h1><p>'+esc(order.order_no)+' · '+esc(order.placed_at)+'</p><p class="kq-receipt-confirmed">'+(order.payment_method==='cod'?'Confirmed · Pay on delivery':'Payment approved')+'</p><h2>Customer & delivery</h2><p>'+esc(order.customer_name)+'<br>'+esc(order.customer_email)+'<br>'+esc(order.customer_phone)+'<br>'+esc(order.order_type==='pickup'?'Store pickup':order.address_line)+'</p><table><thead><tr><th>Your order</th><th>Amount</th></tr></thead><tbody>'+items+'</tbody></table><dl>'+[['Subtotal',order.subtotal],['Discount',-order.discount],['Delivery',order.delivery_fee],['Tax',order.tax],['Total',order.total]].map(([label,value])=>'<div><dt>'+label+'</dt><dd>'+money(value)+'</dd></div>').join('')+'</dl><p>Thank you for ordering with Kitchen Queen.</p><div class="kq-receipt-buttons"><button type="button" class="kq-btn" id="printReceipt">Print receipt</button><a class="kq-btn kq-btn-outline" href="track.php?order='+encodeURIComponent(order.order_no)+'">Track order</a></div></section>';
        document.getElementById('printReceipt').onclick=()=>window.print();
      }
      function pollOrder(no) {
        orderTimer=setTimeout(async()=>{
          if(document.hidden){pollOrder(no);return;}
          try {const res=await KQApi.getOrder(no);showOrder(res.data);}
          catch(e){const el=document.getElementById('approvalConnection');if(el)el.textContent='Connection interrupted. Retrying…';orderTimer=setTimeout(()=>pollOrder(no),3000);}
        },750);
      }
      const resumeNo=new URLSearchParams(location.search).get('order');
      if(resumeNo && window.KQApi) {
        document.querySelector('.bp-checkout').hidden=true;
        const stage=document.getElementById('kqReceiptStage');stage.hidden=false;stage.textContent='Loading your order…';
        KQApi.getOrder(resumeNo).then(r=>showOrder(r.data)).catch(e=>{stage.textContent=e.message+' Please sign in and reopen your order from My orders.';});
      }

      const cancelBtn = document.getElementById("kqCancelOrderBtn");
      if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
          if (!lastOrder) return toast("No active order to cancel", "fa-ban");
          if (!confirm(`Cancel order ${lastOrder.orderNo}? This cannot be undone.`)) return;
          if (window.KQApi && lastOrder.id) {
            KQApi.cancelOrder(lastOrder.id, lastOrder.phone).catch(() => {});
          }
          try { localStorage.removeItem("kq_last_order"); } catch (e) {}
          toast("Order cancelled", "fa-ban");
          location.href = "index.php";
        });
      }
    },

    track() {
      const root = document.getElementById("kqTrack");
      if (!root) return;
      const params = new URLSearchParams(location.search);
      const orderNo = params.get("order") || "";
      const input = document.getElementById("kqTrackInput");
      if (input && orderNo) input.value = orderNo;
      const paint = (o) => {
        if (!o) { root.innerHTML = "<p>Order not found.</p>"; return; }
        const st = String(o.status || o.simple_status || "pending").toLowerCase();
        const steps = [
          { label: "Order placed", on: true },
          { label: "Confirmation call", on: ["call_pending","preparing","ready","out_for_delivery","delivered","completed"].includes(st) },
          { label: "Kitchen preparing", on: ["preparing","ready","out_for_delivery","delivered","completed"].includes(st) },
          { label: "Out for delivery", on: ["out_for_delivery","delivered","completed"].includes(st) },
          { label: "Delivered", on: ["delivered","completed"].includes(st) }
        ];
        const active = Math.max(0, steps.filter((s) => s.on).length - 1);
        root.innerHTML = `<div class="kq-track-card">
          <div class="kq-track-head"><div><b>#${esc(o.order_no || orderNo)}</b><small>${esc(o.placed_at || "")}</small></div><span class="kq-eta">ETA 40-55 min</span></div>
          <div class="kq-track-line">
            ${steps.map((s, i) => `<div class="kq-track-step${i < active ? " done" : ""}${i === active ? " active" : ""}"><span class="dot"></span><div><b>${s.label}</b><small>${i < active ? "Done" : i === active ? "Live" : "Pending"}</small></div></div>`).join("")}
          </div>
          <div class="kq-track-now">Current status: <b>${esc(st.replace(/_/g, " "))}</b></div>
        </div>`;
      };
      async function load(no) {
        if (!no) return;
        try {
          const phone = (state.user && state.user.phone) || "";
          const res = await KQApi.getOrder(no, phone);
          paint((res && res.data) || null);
        } catch (e) { root.innerHTML = "<p>" + esc(e.message || "Could not load order") + "</p>"; }
      }
      const go = document.getElementById("kqTrackGo");
      if (go) go.addEventListener("click", () => load(input.value.trim()));
      if (orderNo) load(orderNo);
      setInterval(() => { if (input && input.value.trim()) load(input.value.trim()); }, 12000);
    },

    reviews() {
      const form = document.getElementById("kqReviewForm");
      const gate = document.getElementById("kqReviewGate");
      const signInBtn = document.getElementById("kqRvSignIn");
      const listEl = document.getElementById("kqRvList");
      const scoreEl = document.getElementById("kqRvScore");
      const scoreStarsEl = document.getElementById("kqRvScoreStars");
      const countEl = document.getElementById("kqRvCount");
      const barsEl = document.getElementById("kqRvBars");
      const sortEl = document.getElementById("kqRvSort");
      const dishFilterEl = document.getElementById("kqRvDishFilter");
      const photosOnlyEl = document.getElementById("kqRvPhotosOnly");
      const showingEl = document.getElementById("kqRvShowing");
      const dishSelect = document.getElementById("rvDish");
      const starsWrap = document.getElementById("kqRvStars");
      const ratingInput = document.getElementById("rvRating");
      const nameInput = document.getElementById("rvName");
      const orderInput = document.getElementById("rvOrder");
      if (!form) return;

      let allReviews = [];
      let selectedStars = 0;

      try {
        const params = new URLSearchParams(location.search);
        let ord = params.get("order");
        if (!ord) {
          try {
            const last = JSON.parse(localStorage.getItem("kq_last_order") || "null");
            if (last && last.orderNo) ord = last.orderNo;
          } catch (e2) {}
        }
        if (ord && orderInput) orderInput.value = ord;
      } catch (e) {}

      if (dishSelect) {
        const opts = DISHES.map((d) => `<option value="${esc(d.name)}">${esc(d.name)}</option>`).join("");
        dishSelect.innerHTML = `<option value="">Select the item you ordered</option>` + opts;
      }
      if (dishFilterEl) {
        const opts = DISHES.map((d) => `<option value="${esc(d.name)}">${esc(d.name)}</option>`).join("");
        dishFilterEl.innerHTML = `<option value="">All dishes</option>` + opts;
      }

      function starsHtml(n) {
        const full = Math.round(Number(n) || 0);
        return Array.from({ length: 5 }, (_, i) =>
          `<i class="fa-solid fa-star${i < full ? "" : " dim"}"></i>`
        ).join("");
      }

      function unlockForm() {
        if (gate) gate.hidden = true;
        if (nameInput && state.user && state.user.name && !nameInput.value) {
          nameInput.value = state.user.name;
        }
      }

      function lockForm() {
        if (gate) gate.hidden = false;
      }

      function syncAuthGate() {
        if (state.user || (window.KQApi && KQApi.token())) {
          unlockForm();
        } else {
          lockForm();
        }
      }

      syncAuthGate();
      document.addEventListener("kq:auth", syncAuthGate);

      if (signInBtn) {
        signInBtn.addEventListener("click", () => {
          if (typeof openAuth === "function") openAuth();
          else location.href = "signin.php";
        });
      }

      if (starsWrap) {
        starsWrap.querySelectorAll(".rv-star-btn").forEach((btn) => {
          btn.addEventListener("click", () => {
            selectedStars = Number(btn.dataset.star) || 0;
            if (ratingInput) ratingInput.value = String(selectedStars);
            starsWrap.querySelectorAll(".rv-star-btn").forEach((b) => {
              const s = Number(b.dataset.star) || 0;
              b.classList.toggle("on", s <= selectedStars);
            });
            form.classList.remove("rv-need-stars");
          });
        });
      }

      const photoInput = document.getElementById("rvPhotos");
      const previews = document.getElementById("rvPreviews");
      if (photoInput && previews) {
        photoInput.addEventListener("change", () => {
          previews.innerHTML = "";
          Array.from(photoInput.files || []).slice(0, 3).forEach((f) => {
            if (!/^image\//.test(f.type)) return;
            const url = URL.createObjectURL(f);
            const img = document.createElement("img");
            img.src = url;
            img.alt = "preview";
            img.style.cssText = "width:72px;height:72px;object-fit:cover;border-radius:10px;margin:4px;";
            previews.appendChild(img);
          });
        });
      }

      function renderReviews() {
        if (!listEl) return;
        let rows = allReviews.slice();
        const sort = (sortEl && sortEl.value) || "latest";
        if (sort === "high") rows.sort((a, b) => (b.stars || 0) - (a.stars || 0));
        else if (sort === "low") rows.sort((a, b) => (a.stars || 0) - (b.stars || 0));
        else rows.sort((a, b) => (b.id || 0) - (a.id || 0));

        const dishF = (dishFilterEl && dishFilterEl.value) || "";
        if (dishF) {
          rows = rows.filter((r) => String(r.text || "").toLowerCase().includes(dishF.toLowerCase()));
        }

        if (showingEl) showingEl.textContent = rows.length ? `Showing ${rows.length} review${rows.length === 1 ? "" : "s"}` : "No reviews yet";

        if (!rows.length) {
          listEl.innerHTML = `<p style="color:var(--muted);padding:24px 0;text-align:center">No reviews to show yet. Be the first!</p>`;
          return;
        }

        listEl.innerHTML = rows.map((r) => {
          const when = r.created_at ? new Date(r.created_at).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" }) : "";
          return `
            <article class="rv-card">
              <div class="rv-card-head">
                <div class="rv-avatar">${esc((r.name || "?").charAt(0).toUpperCase())}</div>
                <div>
                  <b class="rv-card-name">${esc(r.name || "Customer")}</b>
                  <div class="rv-card-stars">${starsHtml(r.stars)}</div>
                </div>
                <time class="rv-card-date">${esc(when)}</time>
              </div>
              <p class="rv-card-text">${esc(r.text || "")}</p>
              ${r.reply ? `<div class="rv-reply"><b>Kitchen Queen</b><p>${esc(r.reply)}</p></div>` : ""}
            </article>`;
        }).join("");
      }

      function updateSummary() {
        const n = allReviews.length;
        const avg = n ? (allReviews.reduce((s, r) => s + (Number(r.stars) || 0), 0) / n) : 0;
        if (scoreEl) scoreEl.textContent = n ? avg.toFixed(1) : "—";
        if (scoreStarsEl) scoreStarsEl.innerHTML = starsHtml(avg);
        if (countEl) countEl.textContent = n ? `Based on ${n} customer review${n === 1 ? "" : "s"}` : "No reviews yet";
        if (barsEl) {
          const counts = [0, 0, 0, 0, 0];
          allReviews.forEach((r) => {
            const s = Math.min(5, Math.max(1, Math.round(Number(r.stars) || 0)));
            counts[s - 1]++;
          });
          barsEl.innerHTML = [5, 4, 3, 2, 1].map((s) => {
            const c = counts[s - 1];
            const pct = n ? Math.round((c / n) * 100) : 0;
            return `<div class="rv-bar-row"><span>${s}★</span><div class="rv-bar"><i style="width:${pct}%"></i></div><span>${c}</span></div>`;
          }).join("");
        }
      }

      async function loadReviews() {
        if (!window.KQApi) {
          if (listEl) listEl.innerHTML = `<p style="color:var(--muted)">Reviews unavailable.</p>`;
          return;
        }
        try {
          const res = await KQApi.reviews();
          allReviews = (res && res.data && res.data.reviews) || (res && res.reviews) || [];
          updateSummary();
          renderReviews();
        } catch (e) {
          if (listEl) listEl.innerHTML = `<p style="color:var(--muted)">Could not load reviews.</p>`;
        }
      }

      if (sortEl) sortEl.addEventListener("change", renderReviews);
      if (dishFilterEl) dishFilterEl.addEventListener("change", renderReviews);
      if (photosOnlyEl) photosOnlyEl.addEventListener("change", renderReviews);

      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (!state.user && !(window.KQApi && KQApi.token())) {
          lockForm();
          toast("Please sign in to leave a review", "fa-lock");
          if (typeof openAuth === "function") openAuth();
          return;
        }
        const name = (nameInput && nameInput.value || "").trim();
        const orderNo = (orderInput && orderInput.value || "").trim();
        const dish = (dishSelect && dishSelect.value || "").trim();
        const textEl = document.getElementById("rvText");
        const text = (textEl && textEl.value || "").trim();
        const stars = selectedStars || Number(ratingInput && ratingInput.value) || 0;

        let ok = true;
        form.querySelectorAll("[data-required]").forEach((el) => {
          const empty = !String(el.value || "").trim();
          el.classList.toggle("invalid", empty);
          if (empty) ok = false;
        });
        if (stars < 1 || stars > 5) {
          form.classList.add("rv-need-stars");
          ok = false;
        } else {
          form.classList.remove("rv-need-stars");
        }
        if (!ok) {
          if (!orderNo) toast("Please enter your order ID (e.g. KQ-1)", "fa-receipt");
          else if (stars < 1) toast("Please select a star rating", "fa-star");
          else toast("Please fill all required fields", "fa-triangle-exclamation");
          return;
        }

        const btn = form.querySelector(".rv-submit");
        if (btn) btn.disabled = true;
        try {
          if (!window.KQApi) throw new Error("API unavailable");
          await KQApi.addReview({
            name,
            order_no: orderNo,
            stars,
            text: dish ? (dish + ": " + text) : text
          });
          toast("Thank you! Your review was submitted.", "fa-check", "success");
          form.reset();
          selectedStars = 0;
          if (ratingInput) ratingInput.value = "";
          if (starsWrap) starsWrap.querySelectorAll(".rv-star-btn").forEach((b) => b.classList.remove("on"));
          if (previews) previews.innerHTML = "";
          if (nameInput && state.user && state.user.name) nameInput.value = state.user.name;
          try {
            const params = new URLSearchParams(location.search);
            const ord = params.get("order");
            if (ord && orderInput) orderInput.value = ord;
          } catch (err) {}
          await loadReviews();
        } catch (err) {
          toast((err && err.message) || "Could not submit review", "fa-triangle-exclamation");
        } finally {
          if (btn) btn.disabled = false;
        }
      });

      loadReviews();
    },

    signin() {
      const mount = document.getElementById("kqAuthInline");
      if (mount) openAuthInline(mount);
    },

    my_orders() {
      const list = document.getElementById("kqMyOrders");
      const empty = document.getElementById("kqMyOrdersEmpty");
      const gate = document.getElementById("kqMyOrdersGate");
      if (!list) return;

      const badge = (st) => {
        const s = String(st || "pending").toLowerCase();
        if (s === "preparing" || s === "ready") return { cls: "kq-st-prep", text: "👨‍🍳 Preparing" };
        if (s === "delivered" || s === "completed" || s === "out_for_delivery") return { cls: "kq-st-done", text: "🎉 Delivered" };
        if (s === "cancelled" || s === "payment_rejected") return { cls: "kq-st-cancel", text: "Cancelled" };
        return { cls: "kq-st-wait", text: "⏳ Pending" };
      };

      const canCancel = (o) => {
        const st = String(o.simple_status || o.status || "").toLowerCase();
        if (["cancelled", "delivered", "completed", "out_for_delivery", "preparing", "ready"].includes(st)) return false;
        const ts = Date.parse(String(o.placed_at || o.created_at || "").replace(" ", "T"));
        if (!ts) return false;
        return (Date.now() - ts) <= 5 * 60 * 1000;
      };
      const digits = (v) => String(v || "").replace(/\D+/g, "");
      const last10 = (v) => {
        const d = digits(v);
        return d.length >= 10 ? d.slice(-10) : d;
      };
      const mineOnly = (orders) => {
        const u = state.user || {};
        const phone = last10(u.phone);
        const email = String(u.email || "").trim().toLowerCase();
        const uid = String(u.id || "");
        if (!uid && !phone && !email) return [];
        return (orders || []).filter((o) => {
          const ou = String(o.user_id || "");
          const oe = String(o.customer_email || o.email || "").trim().toLowerCase();
          const op = last10(o.customer_phone || o.phone);
          if (uid && ou && uid === ou && ou !== "0") return true;
          if (email && oe && email === oe) return true;
          if (phone && op && phone.length >= 10 && phone === op) return true;
          return false;
        });
      };
      const paint = (orders) => {
        if (!orders.length) {
          list.innerHTML = "";
          if (empty) empty.hidden = false;
          return;
        }
        if (empty) empty.hidden = true;
        list.innerHTML = orders.map((o) => {
          const st = o.simple_status || o.status;
          const b = badge(st);
          const when = String(o.placed_at || "").replace("T", " ").slice(0, 16);
          const items = (o.items || []).map((i) => `${i.qty}× ${esc(i.name)}`).join(", ");
          const showCancel = canCancel(o);
          return `<article class="kq-order-card" data-id="${o.id}">
            <div class="kq-order-top">
              <div>
                <b class="kq-order-no">${esc(o.order_no)}</b>
                <small>${esc(when)}</small>
              </div>
              <span class="kq-status-badge ${b.cls}">${b.text}</span>
            </div>
            <p class="kq-order-items">${items || "Order items"}</p>
            <div class="kq-order-foot">
              <span>${esc(o.payment_method === "easypaisa" ? "EasyPaisa" : "Cash on Delivery")} · ${esc(o.address_line || o.area_name || "")}</span>
              <strong>${money(o.total)}</strong>
            </div>
            <div class="kq-order-actions">
              <a class="kq-btn kq-btn-outline" href="track.php?order=${encodeURIComponent(o.order_no)}">Track order</a>
              <button class="kq-btn kq-btn-outline" type="button" data-reorder="${o.id}">Re-order</button>
            </div>
            ${showCancel ? `<button class="kq-btn kq-btn-outline kq-cancel-order" type="button" data-cancel="${o.id}">Cancel order</button>` : ""}
          </article>`;
        }).join("");
        list.querySelectorAll("[data-reorder]").forEach((btn) => btn.addEventListener("click", () => {
          const order = orders.find((x) => String(x.id) === btn.getAttribute("data-reorder"));
          if (!order || !order.items) return;
          order.items.forEach((i) => {
            state.cart.push({ id: i.menu_item_id || i.name, name: i.name, img: "", base: Number(i.unit_price || 0), unit: Number(i.unit_price || 0), qty: Number(i.qty || 1), extras: i.extras ? [{ label: i.extras, price: 0 }] : [], spice: i.spice || "", portion: "", note: "" });
          });
          persistCart();
          toast("Items added to cart", "fa-check", "success");
          location.href = "checkout.php";
        }));
        list.querySelectorAll("[data-cancel]").forEach((btn) => btn.addEventListener("click", async () => {
          if (!confirm("Cancel this order? Only allowed within 5 minutes.")) return;
          try {
            await KQApi.cancelOrder(btn.getAttribute("data-cancel"), (state.user && state.user.phone) || "");
            toast("Order cancelled", "fa-ban", "success");
            load();
          } catch (e) {
            toast(e.message || "Could not cancel", "fa-ban");
          }
        }));
      };

      async function load() {
        if (!state.user && !(window.KQApi && KQApi.token())) {
          if (gate) gate.hidden = false;
          return;
        }
        if (gate) gate.hidden = true;
        try {
          const res = await KQApi.myOrders();
          const raw = (res && res.data && res.data.orders) || (res && res.orders) || [];
          paint(mineOnly(raw));
        } catch (e) {
          if (gate) gate.hidden = false;
          list.innerHTML = `<p class="kq-page-sub">${esc(e.message || "Please sign in to see orders.")}</p>`;
        }
      }

      const signBtn = document.getElementById("kqMyOrdersSignIn");
      if (signBtn) signBtn.addEventListener("click", () => openAuth());
      document.addEventListener("kq:auth", load);
      load();
      setInterval(load, 8000);
    }
  };

  function showFirstLoader(then) {
    document.getElementById('kqLoader')?.remove();
    then();
  }

  async function restoreSession() {
    if (!window.KQApi || !KQApi.token()) return;
    try {
      const res = await KQApi.me();
      const u = (res && res.data) || res;
      if (u && (u.phone || u.name)) {
        state.user = { id: u.id, name: u.name, phone: u.phone, email: u.email };
        LS.set("user", state.user);
        renderUserUI();
        document.dispatchEvent(new CustomEvent("kq:auth"));
      }
    } catch (e) {}
  }

  /* ---------------- 10. INITIALIZATION ---------------- */
  function withinHours() {
    try {
      const parts = new Intl.DateTimeFormat("en-GB", { timeZone: "Asia/Karachi", hour: "2-digit", hour12: false }).formatToParts(new Date());
      const hour = Number((parts.find((x) => x.type === "hour") || {}).value);
      return hour >= 12 || hour === 0; /* 12PM to 12AM */
    } catch (e) { return true; }
  }

  async function hydrateLive() {
    if (!window.KQApi) return;
    const [settingsResult,menuResult,areasResult] = await Promise.allSettled([KQApi.settings(),KQApi.menu(),KQApi.areas()]);
    const settingsPromise=Promise.resolve(settingsResult.value), menuPromise=Promise.resolve(menuResult.value), areasPromise=Promise.resolve(areasResult.value);
    try {
      const res = await settingsPromise;
      const s = (res && res.data) || {};
      LIVE.storeOnline = !(s.store_online === false || s.store_online === 0 || s.store_online === "0" || s.store_online === "false");
      LIVE.storeMessage = s.store_unavailable_message || LIVE.storeMessage;
      if (s.free_delivery_over != null) LIVE.freeOver=Number(s.free_delivery_over);
      if (s.easypaisa_name) LIVE.easypaisa.name = s.easypaisa_name;
      if (s.easypaisa_number) LIVE.easypaisa.number = s.easypaisa_number;
    } catch (e) {}
    try {
      const res = await menuPromise;
      const items = (res && res.data && res.data.items) || [];
      items.forEach((it) => {
        const match = DISHES.find((d) => String(d.name).toLowerCase() === String(it.name || "").toLowerCase() || String(d.id) === String(it.id));
        if (match) {
          match.id=String(it.id);
          LIVE.stock[match.id] = it.is_available !== false;
          if (it.price) match.price = Number(it.price);
          return;
        }
        DISHES.push({
          id: String(it.id),
          name: it.name,
          cat: String(it.category || "special").toLowerCase(),
          price: Number(it.price || 0),
          serves: Number(it.serves || 1),
          img: String(it.image || "").split("/images/").pop(),
          tag: it.tag || "NEW",
          hot: !!it.is_hot,
          desc: it.description || "",
          addons: !!it.extras_option
        });
        LIVE.stock[String(it.id)] = it.is_available !== false;
      });
      if (typeof window.kqDrawMenu === "function") window.kqDrawMenu();
    } catch (e) {}
    try {
      const areas = await areasPromise;
      const activeAreas=(areas && areas.data && areas.data.areas) || [];
      if(areas?.data) AREAS.splice(0,AREAS.length,...activeAreas.map(a=>a.name));
      activeAreas.forEach((a) => {
        if (a && a.name) LIVE.fees[a.name] = Number(a.delivery_fee || 0);
      });
    } catch (e) {}
    if (!LIVE.storeOnline || !withinHours()) {
      document.body.classList.add("kq-store-offline");
    }
    document.querySelectorAll(".kq-card").forEach((card) => {
      const btn = card.querySelector("[data-add]");
      if (btn && LIVE.stock[btn.getAttribute("data-add")] === false) {
        card.classList.add("kq-oos");
        btn.disabled = true;
      }
    });
  }

  function mountScrollTop() {
    if (document.getElementById("kqTopBtn")) return;
    const btn = document.createElement("button");
    btn.id = "kqTopBtn";
    btn.type = "button";
    btn.setAttribute("aria-label", "Back to top");
    btn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
    document.body.appendChild(btn);
    const sync = () => btn.classList.toggle("show", window.scrollY > 420);
    window.addEventListener("scroll", sync, { passive: true });
    btn.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
    sync();
    // WhatsApp floating button removed as requested
  }

  document.addEventListener("DOMContentLoaded", () => {
    renderShell();
    const pageKey = currentPage().replace(/\.php$/, "").replace(/\.html$/, "");
    if (pages[pageKey]) pages[pageKey]();
    const inline = document.getElementById("kqAuthInline");
    if (inline) openAuthInline(inline);
    restoreSession();
    hydrateLive();
    mountScrollTop();
    if (window.KQApi && typeof KQApi.trackVisit === "function") {
      KQApi.trackVisit({ path: location.pathname }).catch(() => {});
    }
    showFirstLoader(() => {
      if (!state.address) openLocation();
    });
  });
})();