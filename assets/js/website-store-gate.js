/**
 * Store gate: admin offline toggle + working hours 12PM–12AM.
 * When closed: allow full browsing, but block ordering actions.
 */
(function () {
  const BASE = (window.KQ_API_BASE || (location.origin + "/api")).replace(/\/+$/, "");
  const ENDPOINTS = [BASE + "/settings/public", BASE + "/store-status", BASE + "/settings"];

  function offlineFlag(value) {
    return value === false || value === 0 || value === "0" || value === "false" || value === "off" || value === "offline";
  }
  function withinHours() {
    try {
      const parts = new Intl.DateTimeFormat("en-GB", { timeZone: "Asia/Karachi", hour: "2-digit", hour12: false }).formatToParts(new Date());
      const hour = Number((parts.find((x) => x.type === "hour") || {}).value);
      return hour >= 12;
    } catch (e) { return true; }
  }
  function pick(data) {
    if (!data) return { on: true, msg: "" };
    const src = data.data || data.settings || data;
    return {
      on: !offlineFlag(src.store_online),
      msg: src.store_unavailable_message || src.unavailable_message || "Kitchen Queen is currently closed"
    };
  }
  function paint(on, msg) {
    const hoursOk = withinHours();
    const allow = on && hoursOk;
    let banner = document.getElementById("kq-store-banner");
    document.body.classList.toggle("kq-store-offline", !allow);
    if (allow) {
      if (banner) banner.remove();
      return;
    }
    const text = hoursOk ? String(msg || "Kitchen Queen is currently closed for orders") : "Kitchen Queen is closed right now. Working hours are 12PM to 12AM. You can browse the menu but cannot place orders.";
    if (!banner) {
      banner = document.createElement("div");
      banner.id = "kq-store-banner";
      banner.setAttribute("role", "status");
      banner.style.cssText = "position:sticky;top:0;z-index:9999;background:linear-gradient(90deg,#1a1408,#2a1f0a);color:#fff;padding:12px 16px;text-align:center;font-family:Poppins,system-ui,sans-serif;font-size:14px;border-bottom:2px solid #ffc107;box-shadow:0 4px 12px rgba(0,0,0,.15);";
      document.body.prepend(banner);
    }
    banner.innerHTML = '<i class="fa-solid fa-clock" style="color:#ffc107;margin-right:8px"></i> <strong>Closed for orders</strong> — ' + text.replace(/[<>]/g, "");
  }
  async function check() {
    let on = true, msg = "Kitchen Queen is currently closed";
    for (const url of ENDPOINTS) {
      try {
        const res = await fetch(url, { headers: { Accept: "application/json" } });
        if (!res.ok) continue;
        const json = await res.json();
        const state = pick(json);
        on = state.on;
        msg = state.msg;
        break;
      } catch (e) {}
    }
    paint(on, msg);
  }
  // Only block order-related actions when offline
  document.addEventListener("click", function (e) {
    if (!document.body.classList.contains("kq-store-offline")) return;
    const act = e.target.closest("[data-add], .kq-add-btn, #kqCustAdd, .kq-checkout-btn, [data-checkout], button[type='submit']");
    if (!act) return;
    // Allow non-order buttons
    if (act.closest("#kq-store-banner") || act.closest(".kq-nav, .kq-footer, #kqHeader")) return;
    e.preventDefault();
    e.stopPropagation();
    alert("Kitchen Queen is currently closed. You can browse the menu but cannot place orders right now.");
  }, true);
  check();
  setInterval(check, 15000);
})();
