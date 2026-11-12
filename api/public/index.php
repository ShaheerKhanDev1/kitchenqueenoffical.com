<?php
declare(strict_types=1);

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Services\Mailer;
use App\Services\OrderService;
use App\Services\Settings;
use App\Services\WhatsApp;

$root = dirname(__DIR__);
$GLOBALS['KQ_CONFIG'] = require $root . '/config/config.php';
date_default_timezone_set($GLOBALS['KQ_CONFIG']['timezone'] ?? 'Asia/Karachi');

spl_autoload_register(static function (string $class) use ($root): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }
    $rel = str_replace('\\', '/', substr($class, 4));
    $file = $root . '/app/' . $rel . '.php';
    if (is_file($file)) {
        require $file;
    }
});

Response::cors();

try {
    kq_migrate();
    App\Services\Upgrade::run();
} catch (Throwable $e) {
    error_log('KQ migration failed: '.$e->getMessage());
    Response::error('Database upgrade required. Please contact the store.',503);
}

$method = Request::method();
$path = Request::path();
if ($path === '/') {
    $path = '/health';
}

if (defined('KQ_BOOTSTRAP_ONLY')) { return; }
try {
    route($method, $path);
} catch (Throwable $e) {
    error_log('KQ API: '.$e->getMessage());
    Response::error('Server error', 500, [
        'reference' => 'Check the private server error log',
    ]);
}

function kq_migrate(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        $row = Database::one("SELECT value FROM settings WHERE `key` = 'schema_version'");
        if ($row && (string) $row['value'] === '2026-09-25-email-live') {
            return;
        }
    } catch (Throwable $e) {
        // settings table may not exist on first boot
    }
    try {
        Database::query("ALTER TABLE otp_codes MODIFY phone VARCHAR(120) NOT NULL");
    } catch (Throwable $e) {
    }
    try {
        Database::query(
            "INSERT IGNORE INTO settings (`key`, value, updated_at) VALUES
             ('admin_email', 'adminkitchenqueen1@gmail.com', NOW()),
             ('admin_whatsapp', '923362465676', NOW()),
             ('wa_admin_number', '923362465676', NOW()),
             ('wa_enabled', '0', NOW()),
             ('wa_utility_template', 'order_payment_alert', NOW()),
             ('wa_otp_template', 'order_payment_alert', NOW()),
             ('wa_template_lang', 'en', NOW())"
        );
    } catch (Throwable $e) {
    }
    try {
        Database::query(
            "ALTER TABLE orders MODIFY status ENUM(
                'awaiting_payment','confirmed','accepted','preparing','ready',
                'out_for_delivery','completed','cancelled','payment_rejected',
                'pending','delivered','call_pending'
            ) NOT NULL DEFAULT 'pending'"
        );
    } catch (Throwable $e) {
        // already applied or older MySQL wording
    }
    try {
        Database::query(
            "UPDATE orders SET status = 'pending' WHERE status IN ('confirmed','accepted')"
        );
        Database::query(
            "UPDATE orders SET status = 'delivered' WHERE status IN ('completed','out_for_delivery')"
        );
        Database::query(
            "UPDATE orders SET status = 'preparing' WHERE status = 'ready'"
        );
    } catch (Throwable $e) {
    }
    try {
        Database::query(
            "CREATE TABLE IF NOT EXISTS visits (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                ip VARCHAR(45) NOT NULL,
                visit_date DATE NOT NULL,
                visited_at DATETIME NOT NULL,
                user_agent VARCHAR(255) NULL,
                path VARCHAR(180) NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_ip_date (ip, visit_date),
                KEY idx_visit_date (visit_date)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    } catch (Throwable $e) {
    }
    try {
        Database::query(
            "INSERT INTO settings (`key`, value, updated_at) VALUES ('admin_email', 'adminkitchenqueen1@gmail.com', NOW())
             ON DUPLICATE KEY UPDATE value = 'adminkitchenqueen1@gmail.com', updated_at = VALUES(updated_at)"
        );
        Database::query(
            "INSERT INTO settings (`key`, value, updated_at) VALUES ('wa_enabled', '0', NOW())
             ON DUPLICATE KEY UPDATE value = '0', updated_at = VALUES(updated_at)"
        );
        Database::query(
            "INSERT INTO settings (`key`, value, updated_at) VALUES ('schema_version', '2026-09-25-email-live', NOW())
             ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = VALUES(updated_at)"
        );
    } catch (Throwable $e) {
    }
}

    function kq_finish_response(array $payload, int $code = 200): void
{
    http_response_code($code);
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Connection: close');
    }
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!headers_sent()) {
        header('Content-Length: ' . strlen($json));
    }
    echo $json;
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } elseif (function_exists('litespeed_finish_request')) {
        litespeed_finish_request();
    } else {
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        @flush();
    }
    ignore_user_abort(true);
}

function route(string $method, string $path): void
{
    if ($method === 'GET' && $path === '/health') {
        Response::ok(['time' => date('c'), 'name' => 'Kitchen Queen API']);
    }

    if ($method === 'GET' && in_array($path, ['/settings/public', '/settings', '/public/settings', '/store', '/store-status'], true)) {
        try {
            Response::ok(Settings::public() + ['store_online' => Settings::storeOnline()]);
        } catch (Throwable $e) {
            Response::ok([
                'store_online' => true,
                'store_unavailable_message' => '',
                'store_name' => 'Kitchen Queen',
                'warning' => $e->getMessage(),
            ]);
        }
    }

    if ($method === 'GET' && $path === '/areas') {
        $rows = Database::all('SELECT id, name, delivery_fee, is_active, sort_order FROM areas WHERE is_active = 1 ORDER BY sort_order, name');
        Response::ok(['areas' => $rows]);
    }

    if ($method === 'GET' && $path === '/menu') {
        $rows = Database::all('SELECT * FROM menu_items WHERE id != \'custom\' ORDER BY sort_order, id');
        Response::ok(['items' => array_map('present_menu', $rows)]);
    }

    if ($method === 'GET' && $path === '/reviews') {
        $rows = Database::all(
            'SELECT id, name, stars, text, reply, replied_at, created_at, order_id
             FROM reviews WHERE is_visible = 1 AND is_approved = 1 ORDER BY id DESC LIMIT 100'
        );
        Response::ok(['reviews' => $rows]);
    }

    if ($method === 'GET' && $path === '/live') {
        public_live();
    }
    if ($method === 'POST' && in_array($path, ['/track/visit', '/track', '/visits'], true)) {
        public_track_visit();
    }

    if ($method === 'POST' && $path === '/auth/otp/request') {
        auth_otp_request();
    }
    if ($method === 'POST' && $path === '/auth/otp/verify') {
        auth_otp_verify();
    }
    if ($method === 'GET' && $path === '/auth/me') {
        $u = Auth::customer(true);
        Response::ok(present_user($u));
    }

    if ($method === 'POST' && $path === '/promo/validate') {
        Response::error('Promo codes are not active yet', 422);
    }

    if ($method === 'POST' && $path === '/orders/quote') {
        Response::ok(OrderService::quote(Request::json()));
    }

    if ($method === 'POST' && $path === '/orders') {
        $user = Auth::customer(true);
        $order = OrderService::place(Request::json() + $_POST, $user);
        kq_finish_response([
            'ok' => true,
            'message' => 'Order placed',
            'data' => $order,
        ], 201);
        App\Services\MailQueue::drain(2, (int) $order['id']);
        exit;
    }

    if ($method === 'GET' && $path === '/orders/mine') {
        $user = Auth::customer(true);
        $uid = (int) ($user['id'] ?? 0);
        $phone = trim((string) ($user['phone'] ?? ''));
        $email = strtolower(trim((string) ($user['email'] ?? '')));
        $safe = Database::all('SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC LIMIT 100', ['uid' => $uid]);
        Response::ok(['orders' => array_map([OrderService::class, 'present'], $safe)]);
    }

    if ($method === 'GET' && preg_match('#^/orders/([A-Za-z0-9\-]+)$#', $path, $m)) {
        $o = Database::one('SELECT * FROM orders WHERE order_no = :n OR id = :id', ['n' => $m[1], 'id' => (int) $m[1]]);
        if (!$o) {
            Response::error('Order not found', 404);
        }
        $user = Auth::customer(false);

        if (!($user && (int) $user['id'] === (int) $o['user_id'])) {
            Response::error('Order not found', 404);
        }
        Response::ok(OrderService::present($o));
    }

    if ($method === 'POST' && preg_match('#^/orders/(\d+)/cancel$#', $path, $m)) {
        $o = Database::one('SELECT * FROM orders WHERE id = :id', ['id' => (int) $m[1]]);
        if (!$o) {
            Response::error('Order not found', 404);
        }
        $user = Auth::customer(false);
        $phone = (string) Request::input('phone', $user['phone'] ?? '');
        if ($phone) {
            $phone = Validator::phone($phone);
        }
        if (!($user && (int) $user['id'] === (int) $o['user_id'])) {
            Response::error('Not allowed', 403);
        }
        $age = time() - strtotime((string) ($o['placed_at'] ?? $o['created_at'] ?? 'now'));
        if ($age > 5 * 60) {
            Response::error('Cancel window (5 minutes) has ended', 409);
        }
        if (!in_array($o['status'], ['awaiting_payment', 'confirmed', 'pending'], true)) {
            Response::error('Order can no longer be cancelled', 409);
        }
        $now = date('Y-m-d H:i:s');
        Database::update('orders', [
            'status' => 'cancelled',
            'cancelled_at' => $now,
            'updated_at' => $now,
        ], 'id = :id', ['id' => $o['id']]);
        OrderService::logStatus((int) $o['id'], $o['status'], 'cancelled', 'customer', null, 'Cancelled by customer');
        Response::ok(OrderService::present(Database::one('SELECT * FROM orders WHERE id = :id', ['id' => $o['id']])));
    }

    if ($method === 'POST' && $path === '/reviews') {
        $user = Auth::customer(false);
        $in = Request::json();
        Validator::require($in, ['name', 'stars', 'text']);
        $stars = Validator::intRange($in['stars'], 1, 5, 'stars');
        $orderId = null;
        $rawOrderNo = trim((string) ($in['order_no'] ?? $in['order'] ?? ''));
        if ($rawOrderNo !== '') {
            // Accept KQ-1, KQ1, kq-1, etc.
            $candidates = array_unique([
                $rawOrderNo,
                strtoupper($rawOrderNo),
                preg_replace('/^kq\-?/i', 'KQ-', $rawOrderNo),
                preg_replace('/^kq\-?/i', 'KQ-', strtoupper($rawOrderNo)),
            ]);
            foreach ($candidates as $candidate) {
                if ($candidate === '') {
                    continue;
                }
                $o = Database::one('SELECT id FROM orders WHERE order_no = :n', ['n' => $candidate]);
                if ($o) {
                    $orderId = (int) $o['id'];
                    break;
                }
            }
        }
        if (!$orderId && !empty($in['order_id'])) {
            $orderId = (int) $in['order_id'];
            $exists = Database::one('SELECT id FROM orders WHERE id = :id', ['id' => $orderId]);
            if (!$exists) {
                $orderId = null;
            }
        }
        if ($rawOrderNo === '' && !$orderId) {
            Response::error('Order ID is required. Please enter your order number (e.g. KQ-1)', 422);
        }
        if (!$orderId) {
            Response::error('This order number was not found. Place an order first, then review with that order number.', 422);
        }
        $id = Database::insert('reviews', [
            'order_id' => $orderId,
            'user_id' => $user['id'] ?? null,
            'name' => substr(trim((string) $in['name']), 0, 80),
            'stars' => $stars,
            'text' => (string) $in['text'],
            'reply' => null,
            'replied_at' => null,
            'is_approved' => 1,
            'is_visible' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Response::created(Database::one('SELECT * FROM reviews WHERE id = :id', ['id' => $id]), 'Review saved');
    }

    /* -------- admin -------- */
    if ($method === 'GET' && $path === '/admin/setup') {
        admin_setup_status();
    }
    if ($method === 'POST' && $path === '/admin/register') {
        admin_register();
    }
    if ($method === 'POST' && $path === '/admin/login') {
        admin_login();
    }
    if ($method === 'POST' && $path === '/admin/otp/request') {
        admin_otp_request();
    }
    if ($method === 'POST' && $path === '/admin/otp/verify') {
        admin_otp_verify();
    }
    if ($method === 'GET' && in_array($path, ['/admin/live-metrics', '/admin/live', '/admin/counters'], true)) {
        Auth::admin();
        admin_live_metrics();
    }
    if ($method === 'POST' && $path === '/admin/whatsapp/test') {
        $a = Auth::admin();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        WhatsApp::queue(0, 'test_order', 'whatsapp', 'Test order alert', [
            'customer_name' => 'Test Customer',
            'order_no' => 'TEST',
            'amount' => '1500',
            'payment' => 'Cash on Delivery',
        ]);
        WhatsApp::flushWhatsApp();
        $ok = WhatsApp::lastError() === '';
        Response::ok([
            'sent' => $ok || WhatsApp::ready(),
            'to' => mask_phone(WhatsApp::adminPhone($a)),
            'error' => WhatsApp::lastError(),
            'raw' => WhatsApp::lastResponse(),
            'ready' => WhatsApp::ready(),
        ], WhatsApp::lastError() === '' ? 'Order template sent to admin WhatsApp' : 'WhatsApp send failed');
    }
    if ($method === 'POST' && $path === '/admin/logout') {
        $a = Auth::admin();
        Database::query('DELETE FROM admin_sessions WHERE token = :t', ['t' => $a['token']]);
        Response::ok([], 'Logged out');
    }
    if ($method === 'GET' && $path === '/admin/me') {
        $a = Auth::admin();
        Response::ok(present_admin($a));
    }

    if ($method === 'GET' && $path === '/admin/orders') {
        Auth::admin();
        admin_orders();
    }
    if ($method === 'GET' && preg_match('#^/admin/orders/(\d+)$#', $path, $m)) {
        Auth::admin();
        $o = Database::one('SELECT * FROM orders WHERE id = :id', ['id' => (int) $m[1]]);
        if (!$o) {
            Response::error('Not found', 404);
        }
        Response::ok(OrderService::present($o));
    }
    if ($method === 'POST' && preg_match('#^/admin/orders/(\d+)/status$#', $path, $m)) {
        $admin = Auth::admin();
        $o = Database::one('SELECT * FROM orders WHERE id = :id', ['id' => (int) $m[1]]);
        if (!$o) {
            Response::error('Not found', 404);
        }
        $fresh = OrderService::changeStatus($o, (string) Request::input('status'), $admin, Request::input('note'));
        Response::ok(OrderService::present($fresh));
    }
    if ($method === 'POST' && preg_match('#^/admin/orders/(\d+)/payment$#', $path, $m)) {
        $admin = Auth::admin();
        $o = Database::one('SELECT * FROM orders WHERE id = :id', ['id' => (int) $m[1]]);
        if (!$o) {
            Response::error('Not found', 404);
        }
        $fresh = OrderService::decidePayment($o, (string) Request::input('decision'), $admin, Request::input('note'));
        kq_finish_response(['ok'=>true,'data'=>OrderService::present($fresh)]);
        App\Services\MailQueue::drain(2, (int)$fresh['id']);
        exit;
    }

    if ($method === 'GET' && preg_match('#^/admin/orders/(\d+)/slip$#', $path, $m)) {
        Auth::admin();
        $row = Database::one('SELECT u.* FROM uploads u JOIN orders o ON o.screenshot_id=u.id WHERE o.id=:id', ['id'=>(int)$m[1]]);
        if (!$row) Response::error('Slip not found',404);
        $file = $GLOBALS['KQ_CONFIG']['upload']['dir'].'/'.basename($row['file_path']);
        if (!is_file($file)) Response::error('Slip not found on this API host',404);
        header('Content-Type: '.$row['mime']); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff'); readfile($file); exit;
    }
    if ($method === 'GET' && $path === '/admin/notifications') {
        Auth::admin();
        $rows = Database::all("SELECT id, order_no, customer_name, total, placed_at FROM orders WHERE payment_method = 'easypaisa' AND payment_status = 'pending' AND status = 'awaiting_payment' ORDER BY id LIMIT 50");
        Response::ok(['orders' => $rows, 'count' => count($rows)]);
    }
    if ($method === 'POST' && preg_match('#^/admin/notifications/(\d+)/seen$#', $path, $m)) {
        Auth::admin();
        Database::update('notifications', [
            'status' => 'seen',
            'seen_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => (int) $m[1]]);
        Response::ok([]);
    }

    if ($method === 'POST' && preg_match('#^/admin/users/(\d+)$#', $path, $m)) {
        Auth::admin();
        $id = (int)$m[1];
        $u = Database::one('SELECT id FROM users WHERE id = :id', ['id'=>$id]);
        if (!$u) Response::error('Customer not found', 404);
        $active = Request::input('is_active');
        if (!in_array($active, [0,1,'0','1'], true)) Response::error('Invalid account status', 422);
        Database::update('users', ['is_active'=>(int)$active, 'updated_at'=>date('Y-m-d H:i:s')], 'id = :id', ['id'=>$id]);
        Response::ok(['id'=>$id, 'is_active'=>(int)$active]);
    }
    if ($method === 'GET' && $path === '/admin/menu') {
        Auth::admin();
        $rows = Database::all('SELECT * FROM menu_items WHERE id != \'custom\' ORDER BY sort_order, id');
        Response::ok(['items' => array_map('present_menu', $rows)]);
    }
    if ($method === 'GET' && $path === '/admin/categories') {
        Auth::admin();
        Response::ok(['categories' => Database::all('SELECT * FROM categories ORDER BY sort_order, id')]);
    }
    if ($method === 'POST' && $path === '/admin/menu') {
        Auth::admin();
        admin_menu_create();
    }
    if (in_array($method, ['PATCH', 'POST'], true) && preg_match('#^/admin/menu/([A-Za-z0-9_-]+)$#', $path, $m)) {
        Auth::admin();
        $item = Database::one('SELECT * FROM menu_items WHERE id = :id', ['id' => $m[1]]);
        if (!$item) {
            Response::error('Item not found', 404);
        }
        $in = Request::json();
        $patch = ['updated_at' => date('Y-m-d H:i:s')];
        if (array_key_exists('is_available', $in) || array_key_exists('stock', $in)) {
            $on = $in['is_available'] ?? $in['stock'];
            $patch['is_available'] = $on ? 1 : 0;
        }
        if (isset($in['price'])) {
            $patch['price'] = (int) $in['price'];
        }
        if (isset($in['was_price'])) {
            $patch['was_price'] = $in['was_price'] === null ? null : (int) $in['was_price'];
        }
        if (isset($in['name'])) {
            $patch['name'] = (string) $in['name'];
        }
        Database::update('menu_items', $patch, 'id = :id', ['id' => $item['id']]);
        Response::ok(present_menu(Database::one('SELECT * FROM menu_items WHERE id = :id', ['id' => $item['id']])));
    }

    if ($method === 'POST' && $path === '/admin/areas/surge') {
        Auth::admin();
        $in = Request::json();
        $percent = (float) ($in['percent'] ?? 0);
        $flat = (int) ($in['flat'] ?? 0);
        $rows = Database::all('SELECT id, delivery_fee FROM areas');
        foreach ($rows as $row) {
            $fee = (int) $row['delivery_fee'];
            if ($percent) $fee = (int) round($fee * (1 + $percent / 100));
            $fee += $flat;
            if ($fee < 0) $fee = 0;
            Database::update('areas', ['delivery_fee' => $fee, 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $row['id']]);
        }
        Response::ok(['areas' => Database::all('SELECT * FROM areas ORDER BY sort_order, name')]);
    }
    if ($method === 'GET' && $path === '/admin/areas') {
        Auth::admin();
        Response::ok(['areas' => Database::all('SELECT * FROM areas ORDER BY sort_order, name')]);
    }
    if ($method === 'POST' && $path === '/admin/areas') {
        Auth::admin();
        $in = Request::json();
        $name = trim((string) ($in['name'] ?? ''));
        $fee = (int) ($in['delivery_fee'] ?? 0);
        if ($name === '') {
            Response::error('Area name required', 422);
        }
        if ($fee < 0) $fee = 0;
        $now = date('Y-m-d H:i:s');
        $id = Database::insert('areas', [
            'name' => substr($name, 0, 120),
            'delivery_fee' => $fee,
            'is_active' => 1,
            'sort_order' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        Response::ok(Database::one('SELECT * FROM areas WHERE id = :id', ['id' => $id]), 'Zone added');
    }
    if (in_array($method, ['PATCH', 'POST'], true) && preg_match('#^/admin/areas/(\d+)$#', $path, $m)) {
        Auth::admin();
        $in = Request::json();
        $patch = ['updated_at' => date('Y-m-d H:i:s')];
        if (isset($in['delivery_fee'])) {
            $patch['delivery_fee'] = (int) $in['delivery_fee'];
        }
        if (array_key_exists('is_active', $in)) {
            $patch['is_active'] = $in['is_active'] ? 1 : 0;
        }
        if (isset($in['name'])) {
            $patch['name'] = (string) $in['name'];
        }
        Database::update('areas', $patch, 'id = :id', ['id' => (int) $m[1]]);
        Response::ok(Database::one('SELECT * FROM areas WHERE id = :id', ['id' => (int) $m[1]]));
    }

    if ($method === 'GET' && $path === '/admin/settings') {
        Auth::admin();
        Response::ok(Settings::all());
    }
    if (in_array($method, ['PUT', 'POST'], true) && $path === '/admin/settings') {
        Auth::admin();
        $in = Request::json();
        foreach ($in as $k => $v) {
            if (!is_string($k) || $k === '') {
                continue;
            }
            Settings::set($k, $v);
        }
        Response::ok(Settings::all(), 'Settings saved');
    }

    if ($method === 'GET' && $path === '/admin/reviews') {
        Auth::admin();
        Response::ok(['reviews' => Database::all('SELECT * FROM reviews ORDER BY id DESC')]);
    }
    if (in_array($method, ['PATCH', 'POST'], true) && preg_match('#^/admin/reviews/(\d+)$#', $path, $m)) {
        Auth::admin();
        $in = Request::json();
        $patch = [];
        if (isset($in['reply'])) {
            $patch['reply'] = (string) $in['reply'];
            $patch['replied_at'] = date('Y-m-d H:i:s');
        }
        if (array_key_exists('is_visible', $in)) {
            $patch['is_visible'] = $in['is_visible'] ? 1 : 0;
        }
        if (array_key_exists('is_approved', $in)) {
            $patch['is_approved'] = $in['is_approved'] ? 1 : 0;
        }
        if (!$patch) {
            Response::error('Nothing to update', 422);
        }
        Database::update('reviews', $patch, 'id = :id', ['id' => (int) $m[1]]);
        Response::ok(Database::one('SELECT * FROM reviews WHERE id = :id', ['id' => (int) $m[1]]));
    }

    if ($method === 'GET' && $path === '/admin/customers') {
        Auth::admin();
        Response::ok(['users' => Database::all('SELECT id, name, phone, email, is_active, created_at, last_login_at FROM users ORDER BY id DESC LIMIT 500')]);
    }

    if ($method === 'GET' && $path === '/admin/promo') {
        Auth::admin();
        Response::ok(['promo_codes' => Database::all('SELECT * FROM promo_codes ORDER BY id DESC')]);
    }
    if ($method === 'POST' && $path === '/admin/promo') {
        Auth::admin();
        $in = Request::json();
        Validator::require($in, ['code', 'discount_type', 'discount_value']);
        $id = Database::insert('promo_codes', [
            'code' => strtoupper(trim((string) $in['code'])),
            'discount_type' => $in['discount_type'] === 'flat' ? 'flat' : 'percent',
            'discount_value' => (int) $in['discount_value'],
            'min_subtotal' => (int) ($in['min_subtotal'] ?? 0),
            'is_active' => isset($in['is_active']) ? ($in['is_active'] ? 1 : 0) : 1,
            'expires_at' => $in['expires_at'] ?? null,
            'usage_limit' => $in['usage_limit'] ?? null,
        ]);
        Response::created(Database::one('SELECT * FROM promo_codes WHERE id = :id', ['id' => $id]));
    }

    if ($method === 'GET' && $path === '/admin/analytics') {
        Auth::admin();
        admin_analytics();
    }
    if ($method === 'GET' && $path === '/admin/finance') {
        Auth::admin();
        admin_finance();
    }

    if ($method === 'GET' && preg_match('#^/uploads/(.+)$#', $path, $m)) {
        Auth::admin();
        $rel = 'storage/uploads/' . basename($m[1]);
        $abs = dirname(__DIR__) . '/' . $rel;
        if (!is_file($abs)) {
            Response::error('File not found', 404);
        }
        $mime = mime_content_type($abs) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($abs));
        readfile($abs);
        exit;
    }

    Response::error('Not found', 404);
}

function present_menu(array $r): array
{
    return [
        'id' => $r['id'],
        'name' => $r['name'],
        'slug' => $r['slug'],
        'category' => $r['category'],
        'price' => (int) $r['price'],
        'was_price' => $r['was_price'] !== null ? (int) $r['was_price'] : null,
        'serves' => (int) $r['serves'],
        'description' => $r['description'],
        'image' => menu_image_url((string) $r['image']),
        'tag' => $r['tag'],
        'is_hot' => (bool) $r['is_hot'],
        'is_available' => (bool) $r['is_available'],
        'stock' => (bool) $r['is_available'],
        'spice_option' => (bool) $r['spice_option'],
        'extras_option' => (bool) $r['extras_option'],
        'sort_order' => (int) $r['sort_order'],
    ];
}

function menu_image_url(string $path): string
{
    $optimized = preg_replace('/\.(png|jpe?g)$/i', '.webp', $path);
    if (is_file(dirname(__DIR__, 2).'/'.ltrim($optimized,'/'))) $path = $optimized;
    if ($path === '') {
        return $path;
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443);
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'kitchenqueenofficial.com';
    $host = preg_replace('/^dashboard\./i', '', (string) $host);
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = preg_replace('#/api(?:/public)?(?:/index\.php)?$#i', '', $script) ?: '';
    $base = rtrim((string) $base, '/');
    return $scheme . '://' . $host . $base . '/' . ltrim($path, '/');
}

function present_user(array $u): array
{
    return [
        'id' => (int) $u['id'],
        'name' => $u['name'],
        'phone' => $u['phone'],
        'email' => $u['email'],
    ];
}

function present_admin(array $a): array
{
    return [
        'id' => (int) $a['id'],
        'name' => $a['name'],
        'email' => $a['email'],
        'phone' => $a['phone'],
        'role' => $a['role'],
    ];
}

function auth_otp_request(): void
{
    $in = Request::json();
    Validator::require($in, ['phone']);
    $phone = Validator::phone((string) $in['phone']);
    $email = Validator::email($in['email'] ?? null);
    if (!$email) {
        Response::error('Please enter a valid email to receive the OTP', 422);
    }
    $existing = Database::one('SELECT * FROM users WHERE phone = :p', ['p' => $phone]);
    if ($existing && (empty($existing['is_active']) || strtolower((string)$existing['email']) !== strtolower($email))) {
        Response::error('Use the email registered to this phone number.', 422);
    }
    $otpKey = customer_otp_key($phone, $email);
    otp_throttle($otpKey);
    $purpose = 'login';
    if (!in_array($purpose, ['login', 'admin_alert_test'], true)) {
        $purpose = 'login';
    }
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = password_hash($code, PASSWORD_DEFAULT);
    $ttl = (int) ($GLOBALS['KQ_CONFIG']['otp']['ttl_minutes'] ?? 10);
    Database::query(
        "UPDATE otp_codes SET used_at = NOW() WHERE phone = :p AND purpose = :purpose AND used_at IS NULL",
        ['p' => $otpKey, 'purpose' => $purpose]
    );
    Database::insert('otp_codes', [
        'phone' => $otpKey,
        'code_hash' => $hash,
        'purpose' => $purpose,
        'expires_at' => date('Y-m-d H:i:s', time() + $ttl * 60),
        'attempts' => 0,
        'used_at' => null,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
    $out = ['phone' => $phone, 'email' => $email, 'expires_in' => $ttl * 60, 'channel' => 'email'];
    if (!empty($GLOBALS['KQ_CONFIG']['otp']['dev_otp'])) {
        $out['dev_code'] = $code;
        $out['hint'] = 'Demo code 111111 also works.';
    }

    if (!Mailer::sendOtp($email, $code)) {
        Response::error('Email could not be sent. Please retry shortly.', 503);
    }
    Response::ok($out + ['mail_ok' => true], 'Verification email accepted by mail server.');
}

function auth_otp_verify(): void
{
    $in = Request::json();
    Validator::require($in, ['phone', 'code']);
    $phone = Validator::phone((string) $in['phone']);
    $email = Validator::email($in['email'] ?? null);
    if (!$email) Response::error('Email required', 422);
    $otpKey = customer_otp_key($phone, $email);
    $code = preg_replace('/\D+/', '', (string) $in['code']) ?? '';
    $rows = Database::all(
        "SELECT * FROM otp_codes WHERE phone = :p AND purpose = 'login' AND used_at IS NULL ORDER BY id DESC LIMIT 5",
        ['p' => $otpKey]
    );
    $demo = $GLOBALS['KQ_CONFIG']['otp']['demo_codes'] ?? [];
    $ok = false;
    $row = $rows[0] ?? null;
    foreach ($rows as $candidate) {
        if (strtotime((string) $candidate['expires_at']) < time()) {
            continue;
        }
        if ((int) $candidate['attempts'] >= (int) ($GLOBALS['KQ_CONFIG']['otp']['max_attempts'] ?? 5)) {
            continue;
        }
        Database::update('otp_codes', ['attempts' => (int) $candidate['attempts'] + 1], 'id = :id', ['id' => $candidate['id']]);
        if (password_verify($code, (string) $candidate['code_hash'])) {
            $ok = true;
            $row = $candidate;
            break;
        }
    }
    if (!$ok && $row && strtotime((string) $row['expires_at']) < time() && count($rows) === 1) {
        Response::error('OTP expired. Request a new code.', 422);
    }
    if (!$ok && in_array($code, $demo, true) && !empty($GLOBALS['KQ_CONFIG']['otp']['dev_otp'])) {
        $ok = true;
    }
    if (!$ok) {
        Response::error('Invalid verification code', 422);
    }
    if ($row) {
        $used = Database::update('otp_codes', ['used_at' => date('Y-m-d H:i:s')], 'id = :id AND used_at IS NULL', ['id' => $row['id']]);
        if (!$used) Response::error('Code already used', 409);
    }

    $name = trim((string) ($in['name'] ?? ''));
    $email = Validator::email($in['email'] ?? null);
    $user = Database::one('SELECT * FROM users WHERE phone = :p', ['p' => $phone]);
    $now = date('Y-m-d H:i:s');
    if (!$user) {
        if ($name === '' || strlen($name) < 2) {
            Response::error('Name is required for new accounts', 422);
        }
        $id = Database::insert('users', [
            'name' => substr($name, 0, 80),
            'phone' => $phone,
            'phone_e164' => Validator::e164($phone),
            'email' => $email,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
            'last_login_at' => $now,
        ]);
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $id]);
    } else {
        if (!(int)$user['is_active'] || strtolower((string)$user['email']) !== strtolower($email)) Response::error('Account unavailable', 403);
        $patch = ['last_login_at' => $now, 'updated_at' => $now];
        if ($name !== '') {
            $patch['name'] = substr($name, 0, 80);
        }
        if ($email) {
            $patch['email'] = $email;
        }
        Database::update('users', $patch, 'id = :id', ['id' => $user['id']]);
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $user['id']]);
    }
    $token = Auth::issueCustomerToken($user);
    Response::ok(['token' => $token, 'user' => present_user($user)], 'Signed in');
}

function admin_count(): int
{
    $row = Database::one('SELECT COUNT(*) AS c FROM admins');
    return (int) ($row['c'] ?? 0);
}

function admin_setup_status(): void
{
    $count = admin_count();
    Response::ok([
        'needs_setup' => $count === 0,
        'admin_count' => $count,
    ]);
}

function admin_register(): void
{
    if (admin_count() > 0) {
        Response::error('Admin account already exists. Please login instead.', 409);
    }

    $in = Request::json();
    Validator::require($in, ['email', 'password']);
    $email = Validator::email((string) ($in['email'] ?? ''));
    if (!$email) {
        Response::error('Email required', 422);
    }
    $password = (string) $in['password'];
    if (strlen($password) < 6) {
        Response::error('Password must be at least 6 characters', 422);
    }

    $name = trim((string) ($in['name'] ?? 'Kitchen Queen Admin'));
    if ($name === '') {
        $name = 'Kitchen Queen Admin';
    }

    $phone = null;
    $rawPhone = trim((string) ($in['phone'] ?? ''));
    if ($rawPhone !== '') {
        try {
            $phone = Validator::phone($rawPhone);
        } catch (Throwable $e) {
            $phone = substr($rawPhone, 0, 20);
        }
    }

    $now = date('Y-m-d H:i:s');
    $id = Database::insert('admins', [
        'name' => substr($name, 0, 80),
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        'phone' => $phone,
        'role' => 'owner',
        'is_active' => 1,
        'created_at' => $now,
    ]);
    $admin = Database::one('SELECT * FROM admins WHERE id = :id', ['id' => $id]);
    Response::created([
        'admin' => present_admin($admin),
        'needs_login' => true,
    ], 'Admin account created. Please sign in.');
}

function admin_login(): void
{
    $in = Request::json();
    $email = Validator::email((string) ($in['email'] ?? ''));
    $password = (string) ($in['password'] ?? '');
    if (!$email) {
        Response::error('Email required', 422);
    }
    if (admin_count() === 0) {
        Response::error('No admin account found. Please register first.', 401);
    }
    $admin = Database::one('SELECT * FROM admins WHERE email = :e', ['e' => $email]);
    if (!$admin || !(int) $admin['is_active']) {
        Response::error('Invalid admin credentials', 401);
    }
    if ($password === '' || !password_verify($password, $admin['password_hash'])) {
        Response::error('Invalid admin credentials', 401);
    }

    $otp = send_admin_otp($admin, true);
    if (!$otp['mail_ok']) Response::error('Email could not be sent. Please retry shortly.', 503);
    Response::ok(['needs_otp' => true, 'admin' => present_admin($admin), 'email' => $admin['email'], 'channel' => 'email', 'expires_in' => $otp['expires_in'], 'mail_ok' => true], 'Verification email accepted by mail server.');
}

function mask_phone(string $phone): string
{
    $d = preg_replace('/\D+/', '', $phone) ?? '';
    if (strlen($d) < 6) {
        return $phone !== '' ? $phone : '';
    }
    return substr($d, 0, 3) . str_repeat('x', max(0, strlen($d) - 5)) . substr($d, -2);
}

function admin_otp_key(string $email): string
{
    return 'a' . substr(md5(strtolower(trim($email))), 0, 16);
}

function send_admin_otp(array $admin, bool $sendMailNow = true): array
{
    $email = strtolower(trim((string) ($admin['email'] ?? '')));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response::error('Admin email missing. Register again with a valid email.', 422);
    }
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = password_hash($code, PASSWORD_BCRYPT);
    // store plain for later async send if needed
    $ttl = (int) ($GLOBALS['KQ_CONFIG']['otp']['ttl_minutes'] ?? 10);
    $otpKey = admin_otp_key($email);
    otp_throttle($otpKey);
    Database::query(
        "UPDATE otp_codes SET used_at = NOW() WHERE phone = :p AND purpose = 'login' AND used_at IS NULL",
        ['p' => $otpKey]
    );
    Database::insert('otp_codes', [
        'phone' => $otpKey,
        'code_hash' => $hash,
        'purpose' => 'login',
        'expires_at' => date('Y-m-d H:i:s', time() + $ttl * 60),
        'attempts' => 0,
        'used_at' => null,
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    $mailOk = false;
    if ($sendMailNow && !empty($GLOBALS['KQ_CONFIG']['mail']['enabled'])) {
        $mailOk = Mailer::sendOtp($email, $code, [
            'site_url' => 'https://dashboard.kitchenqueenoffical.com/auth/verify.php', 'account' => 'admin',
        ]);
    }

    $out = [
        'expires_in' => $ttl * 60,
        'sent' => $mailOk,
        'mail_ok' => $mailOk,
        'channel' => 'email',
        'phone' => mask_phone((string) ($admin['phone'] ?? '')),
        'message' => 'Password ok. OTP email par bheja ja raha hai.',
        'plain_code' => $code,
        'code' => $code,
        'mail_error' => $mailOk ? '' : Mailer::lastError(),
    ];
    if (!empty($GLOBALS['KQ_CONFIG']['otp']['dev_otp'])) {
        $out['dev_code'] = $code;
    }
    return $out;
}

function admin_otp_request(): void
{
    $in = Request::json();
    $email = Validator::email((string) ($in['email'] ?? ''));
    if (!$email) {
        Response::error('Email required', 422);
    }
    $admin = Database::one('SELECT * FROM admins WHERE email = :e AND is_active = 1', ['e' => $email]);
    if (!$admin) {
        Response::error('Admin not found', 404);
    }
    $otp = send_admin_otp($admin, true);
    if (!$otp['mail_ok']) Response::error('Email could not be sent. Please retry shortly.', 503);
    Response::ok(['email' => $admin['email'], 'channel' => 'email', 'expires_in' => $otp['expires_in'], 'mail_ok' => true], 'Verification email accepted by mail server.');
}

function admin_otp_verify(): void
{
    $in = Request::json();
    $email = Validator::email((string) ($in['email'] ?? ''));
    $code = preg_replace('/\D+/', '', (string) ($in['code'] ?? '')) ?? '';
    if (!$email || strlen($code) !== 6) {
        Response::error('Email and 6-digit OTP required', 422);
    }
    $admin = Database::one('SELECT * FROM admins WHERE email = :e AND is_active = 1', ['e' => $email]);
    if (!$admin) {
        Response::error('Admin not found', 401);
    }
    $lookup = admin_otp_key((string) $admin['email']);
    $row = Database::one(
        "SELECT * FROM otp_codes WHERE phone = :p AND purpose = 'login' AND used_at IS NULL ORDER BY id DESC LIMIT 1",
        ['p' => $lookup]
    );
    if (!$row) {
        Response::error('Request a new OTP first', 422);
    }
    if (strtotime((string) $row['expires_at']) < time()) {
        Response::error('OTP expired. Request a new code.', 422);
    }
    if ((int) $row['attempts'] >= (int) ($GLOBALS['KQ_CONFIG']['otp']['max_attempts'] ?? 5)) {
        Response::error('Too many attempts. Request a new code.', 429);
    }
    Database::update('otp_codes', ['attempts' => (int) $row['attempts'] + 1], 'id = :id', ['id' => $row['id']]);
    $ok = password_verify($code, $row['code_hash']);
    $demo = $GLOBALS['KQ_CONFIG']['otp']['demo_codes'] ?? [];
    if (!$ok && !empty($GLOBALS['KQ_CONFIG']['otp']['dev_otp']) && in_array($code, $demo, true)) {
        $ok = true;
    }
    if (!$ok) {
        Response::error('Invalid verification code', 422);
    }
    Database::update('otp_codes', ['used_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $row['id']]);
    $token = Auth::issueAdminSession((int) $admin['id']);
    Response::ok(['token' => $token, 'admin' => present_admin($admin)], 'Verified');
}

function kq_client_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }
        $first = trim(explode(',', (string) $_SERVER[$key])[0]);
        if (filter_var($first, FILTER_VALIDATE_IP)) {
            return $first;
        }
    }
    return '0.0.0.0';
}

function public_track_visit(): void
{
    $ip = kq_client_ip();
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');
    $ua = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    $in = Request::json();
    $path = substr((string) ($in['path'] ?? ($_SERVER['HTTP_REFERER'] ?? '/')), 0, 180);
    try {
        Database::query(
            "INSERT INTO visits (ip, visit_date, visited_at, user_agent, path)
             VALUES (:ip, :d, :t, :ua, :p)
             ON DUPLICATE KEY UPDATE path = VALUES(path)",
            ['ip' => $ip, 'd' => $today, 't' => $now, 'ua' => $ua, 'p' => $path]
        );
    } catch (Throwable $e) {
        try {
            $exists = Database::one(
                'SELECT id FROM visits WHERE ip = :ip AND visit_date = :d LIMIT 1',
                ['ip' => $ip, 'd' => $today]
            );
            if (!$exists) {
                Database::insert('visits', [
                    'ip' => $ip,
                    'visit_date' => $today,
                    'visited_at' => $now,
                    'user_agent' => $ua,
                    'path' => $path,
                ]);
            }
        } catch (Throwable $e2) {
        }
    }
    Response::ok(['tracked' => true, 'date' => $today]);
}

function live_metrics_payload(): array
{
    $today = date('Y-m-d');
    $visitsRow = Database::one(
        'SELECT COUNT(*) c FROM visits WHERE visit_date = :d',
        ['d' => $today]
    );
    $ordersRow = Database::one(
        "SELECT COUNT(*) c FROM orders
         WHERE DATE(placed_at) = :d AND status NOT IN ('cancelled','payment_rejected')",
        ['d' => $today]
    );
    $visits = (int) ($visitsRow['c'] ?? 0);
    $orders = (int) ($ordersRow['c'] ?? 0);
    $bouncers = max(0, $visits - $orders);
    $rate = $visits > 0 ? round(($orders / $visits) * 100, 1) : 0.0;
    return [
        'date' => $today,
        'visits' => $visits,
        'orders' => $orders,
        'bouncers' => $bouncers,
        'conversion_rate' => $rate,
    ];
}

function admin_live_metrics(): void
{
    Response::ok(live_metrics_payload());
}

function public_live(): void
{
    $today = date('Y-m-d');
    $meals = Database::one(
        "SELECT COALESCE(SUM(oi.qty),0) q
         FROM order_items oi
         JOIN orders o ON o.id = oi.order_id
         WHERE DATE(o.placed_at) = :d AND o.status NOT IN ('cancelled','payment_rejected')",
        ['d' => $today]
    );
    $ordersToday = Database::one(
        "SELECT COUNT(*) c FROM orders
         WHERE DATE(placed_at) = :d AND status NOT IN ('cancelled','payment_rejected')",
        ['d' => $today]
    );
    $riders = Database::one(
        "SELECT COUNT(*) c FROM orders WHERE status = 'out_for_delivery'"
    );
    $families = Database::one(
        "SELECT COUNT(*) c FROM orders WHERE status IN ('completed','delivered')"
    );
    $rows = Database::all(
        "SELECT o.customer_name, o.area_name, o.placed_at, o.status,
                (SELECT name FROM order_items WHERE order_id = o.id ORDER BY id ASC LIMIT 1) AS item_name
         FROM orders o
         WHERE o.status NOT IN ('cancelled','payment_rejected')
         ORDER BY o.placed_at DESC
         LIMIT 12"
    );
    $feed = [];
    foreach ($rows as $r) {
        $name = trim((string) ($r['customer_name'] ?? ''));
        $first = $name !== '' ? preg_split('/\s+/', $name)[0] : 'Guest';
        $feed[] = [
            'name' => $first,
            'area' => $r['area_name'] ?: 'Karachi',
            'item' => $r['item_name'] ?: 'an item',
            'status' => $r['status'],
            'placed_at' => $r['placed_at'],
        ];
    }
    $metrics = live_metrics_payload();
    Response::ok([
        'meals_today' => (int) ($meals['q'] ?? 0),
        'orders_today' => (int) ($ordersToday['c'] ?? 0),
        'riders' => (int) ($riders['c'] ?? 0),
        'families' => (int) ($families['c'] ?? 0),
        'avg_delivery_min' => 42,
        'feed' => $feed,
        'store_online' => Settings::storeOnline(),
        'visits' => $metrics['visits'],
        'orders' => $metrics['orders'],
        'bouncers' => $metrics['bouncers'],
        'conversion_rate' => $metrics['conversion_rate'],
    ]);
}

function admin_orders(): void
{
    $q = trim((string) Request::query('q', ''));
    $st = trim((string) Request::query('status', ''));
    $from = Request::query('from');
    $to = Request::query('to');
    $range = Request::query('range', 'today');
    $sql = 'SELECT * FROM orders WHERE 1=1';
    $p = [];
    if ($st !== '') {
        $map = [
            'Accepted' => 'accepted',
            'Preparing' => 'preparing',
            'Ready' => 'ready',
            'In Delivery' => 'out_for_delivery',
            'Completed' => 'completed',
            'Cancelled' => 'cancelled',
            'Awaiting Payment' => 'awaiting_payment',
        ];
        $sql .= ' AND status = :st';
        $p['st'] = $map[$st] ?? strtolower($st);
    }
    if ($q !== '') {
        $sql .= ' AND (order_no LIKE :q1 OR customer_name LIKE :q2 OR customer_phone LIKE :q3 OR customer_email LIKE :q4)';
        foreach (['q1','q2','q3','q4'] as $key) $p[$key] = '%' . $q . '%';
    }
    $today = date('Y-m-d');
    if (!$from && !$to) {
        if ($range === 'today') {
            $from = $today;
            $to = $today;
        } elseif ($range === 'yesterday') {
            $from = date('Y-m-d', strtotime('-1 day'));
            $to = $from;
        } elseif ($range === '7') {
            $from = date('Y-m-d', strtotime('-6 days'));
            $to = $today;
        } elseif ($range === '30') {
            $from = date('Y-m-d', strtotime('-29 days'));
            $to = $today;
        }
    }
    if ($from) {
        $sql .= ' AND DATE(placed_at) >= :df';
        $p['df'] = $from;
    }
    if ($to) {
        $sql .= ' AND DATE(placed_at) <= :dt';
        $p['dt'] = $to;
    }
    $sql .= ' ORDER BY placed_at DESC LIMIT 300';
    $rows = Database::all($sql, $p);
    $out = [];
    foreach ($rows as $o) {
        $out[] = OrderService::present($o);
    }
    Response::ok(['orders' => $out]);
}

function admin_analytics(): void
{
    $daily = Database::all(
        "SELECT DATE(placed_at) d, COALESCE(SUM(total),0) s, COUNT(*) c
         FROM orders WHERE status NOT IN ('cancelled','payment_rejected')
         GROUP BY DATE(placed_at) ORDER BY d DESC LIMIT 14"
    );
    $pay = Database::all(
        "SELECT payment_method, COUNT(*) c, COALESCE(SUM(total),0) s
         FROM orders WHERE status NOT IN ('cancelled','payment_rejected')
         GROUP BY payment_method"
    );
    $top = Database::all(
        "SELECT oi.name, SUM(oi.qty) qty, SUM(oi.line_total) revenue
         FROM order_items oi JOIN orders o ON o.id = oi.order_id
         WHERE o.status NOT IN ('cancelled','payment_rejected')
         GROUP BY oi.name ORDER BY qty DESC LIMIT 10"
    );
    $hours = Database::all(
        "SELECT HOUR(placed_at) h, COUNT(*) c FROM orders
         WHERE status NOT IN ('cancelled','payment_rejected')
         GROUP BY HOUR(placed_at)"
    );
    Response::ok(['daily' => $daily, 'payments' => $pay, 'top_items' => $top, 'hours' => $hours]);
}

function admin_finance(): void
{
    $done = Database::all(
        "SELECT * FROM orders WHERE status IN ('completed','delivered') ORDER BY placed_at DESC LIMIT 200"
    );
    $week = Database::one(
        "SELECT COALESCE(SUM(total),0) s, COUNT(*) c FROM orders
         WHERE status IN ('completed','delivered') AND placed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
    );
    $month = Database::one(
        "SELECT COALESCE(SUM(total),0) s, COUNT(*) c FROM orders
         WHERE status IN ('completed','delivered') AND placed_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
    );
    Response::ok([
        'week_total' => (int) $week['s'],
        'month_total' => (int) $month['s'],
        'completed_count' => count($done),
        'orders' => array_map(static fn($o) => OrderService::present($o), $done),
    ]);
}


function admin_menu_create(): void
{
    $in = $_POST ?: Request::json();
    $name = trim((string) ($in['name'] ?? $in['item_name'] ?? ''));
    $category = trim((string) ($in['category'] ?? $in['category_name'] ?? 'Salan / Curry'));
    $price = (int) ($in['price'] ?? 0);
    $description = trim((string) ($in['description'] ?? ''));
    if ($name === '' || $price <= 0) {
        Response::error('Dish name and price are required', 422);
    }
    $image = '';
    if (!empty($_FILES['image']['tmp_name'])) {
        $dir = dirname(__DIR__, 2) . '/images/uploads';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $ext = strtolower(pathinfo((string) $_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            Response::error('Image must be JPG, PNG or WEBP', 422);
        }
        $file = 'dish-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.jpg';
        $dest = $dir . '/' . $file;
        $raw = file_get_contents($_FILES['image']['tmp_name']);
        $img = $raw ? @imagecreatefromstring($raw) : false;
        if ($img) {
            $w = imagesx($img);
            $h = imagesy($img);
            $max = 1100;
            if ($w > $max) {
                $nh = (int) round($h * ($max / $w));
                $copy = imagecreatetruecolor($max, $nh);
                imagecopyresampled($copy, $img, 0, 0, 0, 0, $max, $nh, $w, $h);
                imagedestroy($img);
                $img = $copy;
            }
            imagejpeg($img, $dest, 78);
            imagedestroy($img);
        } else {
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
        }
        $image = 'images/uploads/' . $file;
    }
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-')) ?: ('dish-' . time());
    $id = substr($slug, 0, 24) . substr(bin2hex(random_bytes(2)), 0, 4);
    $addons = in_array(strtolower($category), ['salan / curry', 'daal & sabzi', 'daal', 'chicken', 'beef', 'salan', 'curry'], true) ? 1 : 0;
    Database::insert('menu_items', [
        'id' => $id,
        'name' => $name,
        'slug' => $slug,
        'category' => $category,
        'price' => $price,
        'was_price' => null,
        'serves' => (int) ($in['serves'] ?? 1),
        'description' => $description,
        'image' => $image,
        'tag' => 'NEW',
        'is_hot' => 0,
        'is_available' => 1,
        'spice_option' => $addons,
        'extras_option' => $addons,
        'sort_order' => 999,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $row = Database::one('SELECT * FROM menu_items WHERE id = :id', ['id' => $id]);
    Response::ok(present_menu($row ?: ['id' => $id, 'name' => $name, 'slug' => $slug, 'category' => $category, 'price' => $price, 'was_price' => null, 'serves' => 1, 'description' => $description, 'image' => $image, 'tag' => 'NEW', 'is_hot' => 0, 'is_available' => 1, 'spice_option' => $addons, 'extras_option' => $addons, 'sort_order' => 999]));
}

function customer_otp_key(string $phone, string $email): string { return 'customer:' . hash('sha256', $phone . '|' . strtolower($email)); }
function otp_throttle(string $key): void {
    $recent = Database::one("SELECT COUNT(*) n, MAX(created_at) latest FROM otp_codes WHERE phone = :p AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)", ['p' => $key]);
    if ((int)$recent['n'] >= 10 || (!empty($recent['latest']) && strtotime($recent['latest']) > time()-30)) Response::error('Please wait before requesting another code.', 429);
}
