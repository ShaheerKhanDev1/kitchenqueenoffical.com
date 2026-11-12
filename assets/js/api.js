/* Kitchen Queen public API client */
(function () {
  const BASE = (window.KQ_API_BASE || "").replace(/\/+$/, "");
  const TOKEN_KEY = "kq_customer_token";

  function token() {
    try { return localStorage.getItem(TOKEN_KEY) || ""; } catch { return ""; }
  }
  function setToken(t) {
    try {
      if (t) localStorage.setItem(TOKEN_KEY, t);
      else localStorage.removeItem(TOKEN_KEY);
    } catch (e) {}
  }

  async function req(path, opts) {
    const headers = Object.assign({ Accept: "application/json" }, (opts && opts.headers) || {});
    const t = token();
    if (t) headers["Authorization"] = "Bearer " + t;
    if (opts && opts.body && !(opts.body instanceof FormData) && !headers["Content-Type"]) {
      headers["Content-Type"] = "application/json";
    }
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 20000);
    let res;
    try { res = await fetch(BASE + path, Object.assign({}, opts, { headers, signal:controller.signal, cache:'no-store' })); }
    finally { clearTimeout(timeout); }
    const text = await res.text();
    let json = null;
    try { json = text ? JSON.parse(text) : null; } catch (e) { json = null; }
    if (!res.ok || !json || json.ok === false) {
      const err = new Error((json && json.message) || ("Request failed (" + res.status + ")"));
      err.status = res.status;
      err.payload = json;
      throw err;
    }
    return json;
  }

  window.KQApi = {
    base: BASE,
    token, setToken,
    settings() { return req("/settings/public"); },
    live() { return req("/live"); },
    trackVisit(body) { return req("/track/visit", { method: "POST", body: JSON.stringify(body || {}) }); },
    areas() { return req("/areas"); },
    menu() { return req("/menu"); },
    reviews() { return req("/reviews"); },
    requestOtp(body) { return req("/auth/otp/request", { method: "POST", body: JSON.stringify(body) }); },
    verifyOtp(body) { return req("/auth/otp/verify", { method: "POST", body: JSON.stringify(body) }); },
    me() { return req("/auth/me"); },
    validatePromo(code, subtotal) {
      return req("/promo/validate", { method: "POST", body: JSON.stringify({ code, subtotal }) });
    },
    quote(payload) { return req("/orders/quote", {method:"POST",body:JSON.stringify(payload)}); },
    placeOrder(payload) {
      return req("/orders", { method: "POST", body: JSON.stringify(payload) });
    },
    myOrders() { return req("/orders/mine"); },
    getOrder(orderNo, phone) {
      return req("/orders/" + encodeURIComponent(orderNo) + (phone ? ("?phone=" + encodeURIComponent(phone)) : ""));
    },
    cancelOrder(id, phone) {
      return req("/orders/" + id + "/cancel", { method: "POST", body: JSON.stringify({ phone }) });
    },
    addReview(payload) {
      return req("/reviews", { method: "POST", body: JSON.stringify(payload) });
    }
  };
})();
