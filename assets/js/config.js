(function () {
  var host = location.hostname;
  var local = host === "localhost" || host === "127.0.0.1";

  if (local) {
    var parts = location.pathname.split("/").filter(Boolean);
    var folder = parts.length ? "/" + parts[0] : "";
    window.KQ_API_BASE = folder + "/api";
    window.KQ_SITE_ORIGIN = folder || location.origin;
    return;
  }

  // Live site: ALWAYS call this same domain's /api
  // Dashboard has no API unless /api is copied there.
  // OTP + settings must hit kitchenqueenoffical.com/api (or official spelling).
  window.KQ_API_BASE = location.origin + "/api";
  window.KQ_SITE_ORIGIN = location.origin;
})();
