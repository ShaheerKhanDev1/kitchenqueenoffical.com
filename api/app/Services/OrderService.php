<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Response;
use App\Core\Validator;

final class OrderService
{
    public const STATUSES = [
        'awaiting_payment',
        'confirmed',
        'accepted',
        'preparing',
        'ready',
        'out_for_delivery',
        'completed',
        'cancelled',
        'payment_rejected',
        'pending',
        'delivered',
        'call_pending',
    ];

    public static function nextOrderNo(): string
    {
        $row = Database::one("SELECT value FROM settings WHERE `key` = 'order_sequence' FOR UPDATE");
        $last = Database::one("SELECT MAX(CAST(SUBSTRING_INDEX(order_no, '-', -1) AS UNSIGNED)) n FROM orders");
        $n = max((int)($row['value'] ?? 99)+1, (int)($last['n'] ?? 99)+1, 100);
        Settings::set('order_sequence', $n);
        return ($GLOBALS['KQ_CONFIG']['order']['prefix'] ?? 'KQ-') . $n;
    }

    public static function quote(array $in): array {
        $type = strtolower((string)($in['order_type'] ?? 'delivery'));
        if (!in_array($type, ['delivery','pickup'], true)) Response::error('Invalid order type', 422);
        $itemsIn = $in['items'] ?? [];
        if (!is_array($itemsIn) || !$itemsIn || count($itemsIn)>100) Response::error('Invalid basket', 422);
        $area = null;
        $areaName = '';
        $fee = 0;
        if ($type === 'delivery') {
            $areaName = trim((string) ($in['area_name'] ?? ''));
            $areaId = $in['area_id'] ?? null;
            if ($areaId) {
                $area = Database::one('SELECT * FROM areas WHERE id = :id AND is_active = 1', ['id' => (int) $areaId]);
            } elseif ($areaName !== '') {
                $area = Database::one('SELECT * FROM areas WHERE name = :n AND is_active = 1', ['n' => $areaName]);
            }
            if (!$area) {
                Response::error('Please select an available delivery area.', 422);
            }
            $fee = (int) ($area['delivery_fee'] ?? 99);
            $areaName = (string) ($area['name'] ?? $areaName);
        }

        $subtotal = 0;
        $rows = [];
        foreach ($itemsIn as $line) {
            $mid = (string) ($line['menu_item_id'] ?? $line['id'] ?? '');
            $qty = filter_var($line['qty'] ?? null, FILTER_VALIDATE_INT);
            if (!$qty || $qty < 1 || $qty > 99) Response::error('Quantity must be 1–99', 422);
            $item = Database::one('SELECT * FROM menu_items WHERE id = :id', ['id' => $mid]);
            if ($mid === 'custom') {
                $price = Pricing::thali($line['extras'] ?? []);
                $item = ['id'=>'custom', 'name'=>'Custom Thali', 'price'=>$price, 'is_available'=>1];
            }
            if (!$item) Response::error('Menu item unavailable. Please refresh your basket.', 422);
            if (!(int) $item['is_available']) {
                Response::error($item['name'] . ' is out of stock', 409);
            }
            $unit = (int) $item['price'] + ($mid === 'custom' ? 0 : Pricing::extras($line['extras'] ?? []));
            $lineTotal = $unit * $qty;
            $subtotal += $lineTotal;
            $spice = isset($line['spice']) ? substr((string) $line['spice'], 0, 30) : null;
            $extras = $line['extras'] ?? null;
            if (is_array($extras)) {
                $extras = implode(', ', array_map(static fn($e) => is_array($e) ? ($e['label'] ?? '') : (string) $e, $extras));
            }
            $rows[] = [
                'menu_item_id' => $item['id'],
                'name' => $item['name'],
                'qty' => $qty,
                'unit_price' => $unit,
                'spice' => $spice ?: null,
                'extras' => $extras ? substr((string) $extras, 0, 120) : null,
                'line_total' => $lineTotal,
            ];
        }

        $promoCode = '';
        $discount = 0;

        $freeOver = (int) Settings::get('free_delivery_over', 1500);
        if ($type === 'pickup') {
            $fee = 0;
        } elseif ($freeOver > 0 && ($subtotal - $discount) >= $freeOver) {
            $fee = 0;
        }

        $tax = (int) round(($subtotal - $discount) * max(0, (float)Settings::get('tax_percent', 0)) / 100);
        $total = $subtotal - $discount + $fee + $tax;

        return compact('rows','subtotal','discount','fee','tax','total','area','areaName','promoCode');
    }

    public static function place(array $in, ?array $user): array
    {
        if (!Settings::storeOnline()) {
            Response::error((string) Settings::get('store_unavailable_message', 'Website is currently unavailable'), 403);
        }

        Validator::require($in, ['customer_name', 'customer_phone', 'order_type', 'payment_method', 'items']);
        $name = trim((string) $in['customer_name']);
        $phone = Validator::phone((string) $in['customer_phone']);
        $email = Validator::email($in['customer_email'] ?? ($user['email'] ?? null));
        $type = strtolower((string) $in['order_type']);
        $pay = strtolower((string) $in['payment_method']);
        if (!in_array($type, ['delivery', 'pickup'], true)) {
            Response::error('order_type must be delivery or pickup', 422);
        }
        if (!in_array($pay, ['cod', 'easypaisa'], true)) {
            Response::error('payment_method must be cod or easypaisa', 422);
        }
        $itemsIn = $in['items'];
        if (!is_array($itemsIn) || !$itemsIn) {
            Response::error('Cart is empty', 422);
        }

        if ($name === '' || !$email) Response::error('Name and valid confirmation email required', 422);
        if ($type === 'delivery' && trim((string)($in['address_line'] ?? '')) === '') Response::error('Delivery address required', 422);
        $existingKey = hash('sha256', $user['id'].':'.(string)($in['request_key'] ?? ''));
        $existingRequest = Database::one('SELECT order_id FROM checkout_requests WHERE request_key = :k', ['k'=>$existingKey]);
        if (!empty($existingRequest['order_id'])) return self::present(Database::one('SELECT * FROM orders WHERE id = :id', ['id'=>$existingRequest['order_id']]));
        extract(self::quote($in));
        if (isset($in['expected_total']) && (int)$in['expected_total'] !== $total) Response::error('Prices changed. Refresh checkout before paying.',409);
        $requestKey = (string)($in['request_key'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9-]{16,80}$/', $requestKey)) Response::error('Checkout request key required', 422);
        $requestKey = hash('sha256', $user['id'] . ':' . $requestKey);
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            Database::query('INSERT IGNORE INTO checkout_requests (request_key,user_id,created_at) VALUES (:k,:u,NOW())', ['k'=>$requestKey,'u'=>$user['id']]);
            $request = Database::one('SELECT * FROM checkout_requests WHERE request_key = :k FOR UPDATE', ['k'=>$requestKey]);
            if (!empty($request['order_id'])) {
                $existing = Database::one('SELECT * FROM orders WHERE id = :id', ['id'=>$request['order_id']]);
                $pdo->commit();
                return self::present($existing);
            }
            $now = date('Y-m-d H:i:s');
            $status = $pay === 'easypaisa' ? 'awaiting_payment' : 'pending';
            $pstatus = $pay === 'easypaisa' ? 'pending' : 'unpaid';
            $orderNo = self::nextOrderNo();

            $orderId = Database::insert('orders', [
                'order_no' => $orderNo,
                'user_id' => $user['id'] ?? null,
                'customer_name' => $name,
                'customer_phone' => $phone,
                'customer_email' => $email,
                'order_type' => $type,
                'area_id' => $area['id'] ?? null,
                'area_name' => $areaName,
                'address_line' => substr((string) ($in['address_line'] ?? ''), 0, 255),
                'address_note' => substr((string) ($in['address_note'] ?? $in['notes'] ?? ''), 0, 255) ?: null,
                'status' => $status,
                'payment_method' => $pay,
                'payment_status' => $pstatus,
                'screenshot_id' => null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $fee,
                'tax' => $tax,
                'total' => $total,
                'promo_code' => $promoCode !== '' ? $promoCode : null,
                'notes' => isset($in['notes']) ? (string) $in['notes'] : null,
                'admin_note' => null,
                'store_was_online' => 1,
                'placed_at' => $now,
                'confirmed_at' => $pay === 'cod' ? $now : null,
                'cancelled_at' => null,
                'updated_at' => $now,
            ]);

            foreach ($rows as $r) {
                $r['order_id'] = $orderId;
                Database::insert('order_items', $r);
            }

            self::logStatus($orderId, null, $status, 'system', null, 'Order placed');

            $slipId = null;
            if ($pay === 'easypaisa') {
                $slipId = self::storeSlip($orderId, $in);
                Database::update('orders', ['screenshot_id' => $slipId, 'updated_at' => $now], 'id = :id', ['id' => $orderId]);
            }

            Database::update('checkout_requests', ['order_id'=>$orderId], 'request_key = :k', ['k'=>$requestKey]);
            MailQueue::enqueue($orderId, 'admin_order');
            MailQueue::enqueue($orderId, 'customer_order');
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Response::error('Could not place order', 500, ['reference' => 'Check server log']);
        }

        $order = Database::one('SELECT * FROM orders WHERE id = :id', ['id' => $orderId]);
        $items = Database::all('SELECT * FROM order_items WHERE order_id = :id', ['id' => $orderId]);
        try {
            WhatsApp::queueDashboardAlerts($order ?: [], $items, $pay === 'easypaisa');
        } catch (\Throwable $e) {
            error_log('KQ dashboard alert queue failed: ' . $e->getMessage());
        }
        return self::present($order, $items);
    }

    private static function storeSlip(int $orderId, array $in): int
    {
        $dir = $GLOBALS['KQ_CONFIG']['upload']['dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $bin = null;
        $mime = 'image/jpeg';
        if (!empty($_FILES['screenshot']['tmp_name'])) {
            $tmp = $_FILES['screenshot']['tmp_name'];
            $size = (int) $_FILES['screenshot']['size'];
            if ($size > $GLOBALS['KQ_CONFIG']['upload']['max_bytes']) {
                Response::error('Screenshot must be under 5MB', 422);
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = (string) finfo_file($finfo, $tmp);
            finfo_close($finfo);
            if (!in_array($mime, $GLOBALS['KQ_CONFIG']['upload']['mimes'], true)) {
                Response::error('Only image screenshots are allowed', 422);
            }
            $bin = file_get_contents($tmp);
        } elseif (!empty($in['screenshot_base64'])) {
            $raw = (string) $in['screenshot_base64'];
            if (preg_match('#^data:([^;]+);base64,(.+)$#', $raw, $m)) {
                $mime = $m[1];
                $bin = base64_decode($m[2], true);
            } else {
                $bin = base64_decode($raw, true);
            }
            if ($bin === false) {
                Response::error('Invalid screenshot data', 422);
            }
            if (strlen($bin) > $GLOBALS['KQ_CONFIG']['upload']['max_bytes']) {
                Response::error('Screenshot must be under 5MB', 422);
            }
            if (!in_array($mime, $GLOBALS['KQ_CONFIG']['upload']['mimes'], true)) {
                $mime = 'image/jpeg';
            }
        } else {
            Response::error('Please upload your EasyPaisa payment screenshot', 422);
        }
        $info = @getimagesizefromstring((string)$bin);
        if (!$info || !in_array($info['mime'], $GLOBALS['KQ_CONFIG']['upload']['mimes'], true)) Response::error('Invalid screenshot image', 422);
        $mime = $info['mime'];
        $ext = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
        $name = 'ep_' . $orderId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $rel = 'storage/uploads/' . $name;
        $abs = $dir . '/' . $name;
        file_put_contents($abs, $bin);
        return Database::insert('uploads', [
            'order_id' => $orderId,
            'kind' => 'easypaisa_slip',
            'file_path' => $rel,
            'mime' => $mime,
            'size_bytes' => strlen((string) $bin),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function logStatus(int $orderId, ?string $from, string $to, string $by, ?int $adminId, ?string $note = null): void
    {
        Database::insert('order_status_logs', [
            'order_id' => $orderId,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by' => $by,
            'admin_id' => $adminId,
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function simpleStatus(string $status): string
    {
        $status = strtolower($status);
        if (in_array($status, ['cancelled', 'payment_rejected'], true)) {
            return 'cancelled';
        }
        if (in_array($status, ['preparing', 'ready'], true)) {
            return 'preparing';
        }
        if (in_array($status, ['delivered', 'completed', 'out_for_delivery'], true)) {
            return 'delivered';
        }
        return 'pending';
    }

    public static function present(array $o, ?array $items = null): array
    {
        if ($items === null) {
            $items = Database::all('SELECT * FROM order_items WHERE order_id = :id', ['id' => $o['id']]);
        }
        $logs = Database::all('SELECT * FROM order_status_logs WHERE order_id = :id ORDER BY id ASC', ['id' => $o['id']]);
        $slip = null;
        if (!empty($o['screenshot_id'])) {
            $slip = Database::one('SELECT * FROM uploads WHERE id = :id', ['id' => $o['screenshot_id']]);
        }
        return [
            'id' => (int) $o['id'],
            'order_no' => $o['order_no'],
            'customer_name' => $o['customer_name'],
            'customer_phone' => $o['customer_phone'],
            'customer_email' => $o['customer_email'],
            'order_type' => $o['order_type'],
            'area_id' => $o['area_id'] ? (int) $o['area_id'] : null,
            'area_name' => $o['area_name'],
            'address_line' => $o['address_line'],
            'address_note' => $o['address_note'],
            'status' => $o['status'],
            'simple_status' => self::simpleStatus((string) $o['status']),
            'payment_method' => $o['payment_method'],
            'payment_status' => $o['payment_status'],
            'receipt_ready' => $o['payment_method'] !== 'easypaisa' || $o['payment_status'] === 'approved',
            'subtotal' => (int) $o['subtotal'],
            'discount' => (int) $o['discount'],
            'delivery_fee' => (int) $o['delivery_fee'],
            'tax' => (int) $o['tax'],
            'total' => (int) $o['total'],
            'promo_code' => $o['promo_code'],
            'notes' => $o['notes'],
            'admin_note' => $o['admin_note'],
            'placed_at' => $o['placed_at'],
            'confirmed_at' => $o['confirmed_at'],
            'cancelled_at' => $o['cancelled_at'],
            'items' => array_map(static fn($i) => [
                'id' => (int) $i['id'],
                'menu_item_id' => $i['menu_item_id'],
                'name' => $i['name'],
                'qty' => (int) $i['qty'],
                'unit_price' => (int) $i['unit_price'],
                'spice' => $i['spice'],
                'extras' => $i['extras'],
                'line_total' => (int) $i['line_total'],
            ], $items),
            'timeline' => $logs,
            'screenshot' => $slip ? [
                'id' => (int) $slip['id'],
                'url' => '/admin/orders/' . (int)$o['id'] . '/slip',
                'mime' => $slip['mime'],
            ] : null,
        ];
    }

    public static function changeStatus(array $order, string $to, array $admin, ?string $note = null): array
    {
        $to = strtolower($to);
        $map = [
            'pending' => 'pending',
            'accepted' => 'accepted',
            'preparing' => 'preparing',
            'ready' => 'preparing',
            'in delivery' => 'delivered',
            'out_for_delivery' => 'out_for_delivery',
            'call_pending' => 'call_pending',
            'delivered' => 'delivered',
            'completed' => 'delivered',
            'cancelled' => 'cancelled',
            'confirmed' => 'pending',
        ];
        $to = $map[$to] ?? $to;
        if (!in_array($to, self::STATUSES, true)) {
            Response::error('Invalid status', 422);
        }
        $from = $order['status'];
        if (in_array($from, ['cancelled','payment_rejected','completed','delivered'], true)) Response::error('This order is closed', 409);
        if (in_array($to, ['awaiting_payment','payment_rejected'], true)) Response::error('Use the payment review action', 422);
        if ($order['payment_method'] === 'easypaisa' && $order['payment_status'] !== 'approved' && $to !== 'cancelled') {
            Response::error('Approve EasyPaisa payment first', 409);
        }
        $now = date('Y-m-d H:i:s');
        $patch = ['status' => $to, 'updated_at' => $now];
        if ($to === 'cancelled') {
            $patch['cancelled_at'] = $now;
            $patch['admin_note'] = $note;
        }
        if (in_array($to, ['completed', 'delivered'], true) && $order['payment_method'] === 'cod') {
            $patch['payment_status'] = 'collected';
        }
        $changed = Database::update('orders', $patch, 'id = :id AND status = :old AND payment_status = :oldpay', ['id' => $order['id'], 'old'=>$from, 'oldpay'=>$order['payment_status']]);
        if (!$changed) Response::error('Order changed. Please refresh.',409);
        self::logStatus((int) $order['id'], $from, $to, 'admin', (int) $admin['id'], $note);
        return Database::one('SELECT * FROM orders WHERE id = :id', ['id' => $order['id']]);
    }

    public static function decidePayment(array $order, string $decision, array $admin, ?string $note = null): array {
        if (!in_array($decision, ['approve','reject'], true)) Response::error('Invalid payment decision', 422);
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $order = Database::one('SELECT * FROM orders WHERE id = :id FOR UPDATE', ['id'=>$order['id']]);
            if ($order['payment_method'] !== 'easypaisa' || $order['status'] !== 'awaiting_payment' || $order['payment_status'] !== 'pending') {
                $pdo->rollBack();
                Response::error('Payment already decided or order cancelled. Refresh orders.', 409);
            }
            $now = date('Y-m-d H:i:s');
            $status = $decision === 'approve' ? 'pending' : 'payment_rejected';
            $patch = ['payment_status'=>$decision === 'approve' ? 'approved' : 'rejected', 'status'=>$status, 'updated_at'=>$now, 'admin_note'=>$note];
            if ($decision === 'approve') $patch['confirmed_at'] = $now;
            Database::update('orders', $patch, 'id = :id', ['id'=>$order['id']]);
            self::logStatus((int)$order['id'], $order['status'], $status, 'admin', (int)$admin['id'], $note ?: 'Payment '.$decision);
            MailQueue::enqueue((int)$order['id'], $decision === 'approve' ? 'payment_approved' : 'payment_rejected');
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
        return Database::one('SELECT * FROM orders WHERE id = :id', ['id'=>$order['id']]);
    }
}
