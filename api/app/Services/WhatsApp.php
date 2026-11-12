<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class WhatsApp
{
    private static string $lastError = '';
    private static string $lastResponse = '';

    public static function lastError(): string
    {
        return self::$lastError;
    }

    public static function lastResponse(): string
    {
        return self::$lastResponse;
    }

    public static function cfg(): array
    {
        $cfg = $GLOBALS['KQ_CONFIG']['whatsapp'] ?? [];
        $keys = [
            'enabled',
            'phone_number_id',
            'access_token',
            'graph_version',
            'utility_template',
            'template_lang',
            'otp_template',
            'otp_template_lang',
            'otp_template_button',
            'admin_number',
        ];
        foreach ($keys as $key) {
            try {
                $fromDb = Settings::get('wa_' . $key, null);
            } catch (\Throwable $e) {
                $fromDb = null;
            }
            if ($fromDb === null || $fromDb === '') {
                continue;
            }
            if ($key === 'enabled' || $key === 'otp_template_button') {
                $cfg[$key] = $fromDb === true || $fromDb === 1 || $fromDb === '1' || $fromDb === 'true';
            } else {
                $cfg[$key] = (string) $fromDb;
            }
        }
        if (empty($cfg['admin_number'])) {
            try {
                $adminWa = Settings::get('admin_whatsapp', '');
            } catch (\Throwable $e) {
                $adminWa = '';
            }
            if ($adminWa) {
                $cfg['admin_number'] = (string) $adminWa;
            }
        }
        if (empty($cfg['admin_number'])) {
            $cfg['admin_number'] = '';
        }
        if (empty($cfg['graph_version'])) {
            $cfg['graph_version'] = 'v21.0';
        }
        if (empty($cfg['utility_template'])) {
            $cfg['utility_template'] = 'order_payment_alert';
        }
        if (empty($cfg['template_lang'])) {
            $cfg['template_lang'] = 'en';
        }
        if (empty($cfg['otp_template'])) {
            $cfg['otp_template'] = 'order_payment_alert';
        }
        if (empty($cfg['otp_template_lang'])) {
            $cfg['otp_template_lang'] = $cfg['template_lang'] ?? 'en';
        }
        return $cfg;
    }

    public static function ready(?array $cfg = null): bool
    {
        $cfg = $cfg ?? self::cfg();
        return !empty($cfg['enabled'])
            && !empty($cfg['access_token'])
            && !empty($cfg['phone_number_id']);
    }

    public static function normalizePhone(string $phone): string
    {
        $to = preg_replace('/\D+/', '', $phone) ?? '';
        if ($to === '') {
            return '';
        }
        if (str_starts_with($to, '00')) {
            $to = substr($to, 2);
        }
        if (str_starts_with($to, '0')) {
            $to = '92' . substr($to, 1);
        }
        if (strlen($to) === 10 && str_starts_with($to, '3')) {
            $to = '92' . $to;
        }
        return $to;
    }

    public static function adminPhone(?array $admin = null): string
    {
        $cfg = self::cfg();
        $candidates = [
            (string) ($admin['phone'] ?? ''),
            (string) ($cfg['admin_number'] ?? ''),
            '',
        ];
        foreach ($candidates as $raw) {
            $n = self::normalizePhone($raw);
            if ($n !== '') {
                return $n;
            }
        }
        return '';
    }

    public static function sendOtp(string $phone, string $code): bool
    {
        self::$lastError = '';
        $cfg = self::cfg();
        if (!self::ready($cfg)) {
            self::$lastError = 'WhatsApp Cloud API is not configured (enable + phone_number_id + access_token).';
            return false;
        }
        $to = self::normalizePhone($phone);
        if ($to === '') {
            $to = self::adminPhone();
        }
        if ($to === '') {
            self::$lastError = 'Admin WhatsApp number missing.';
            return false;
        }

        $template = trim((string) ($cfg['otp_template'] ?? 'order_payment_alert'));
        $lang = trim((string) ($cfg['otp_template_lang'] ?? 'en'));

        // Existing approved template:
        // Hi {{1}}, thank you for your order #{{2}} of amount {{3}} placed via {{4}}.
        $components = [[
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => 'Admin'],
                ['type' => 'text', 'text' => $code],
                ['type' => 'text', 'text' => $code],
                ['type' => 'text', 'text' => 'Admin Login OTP'],
            ],
        ]];
        if (!empty($cfg['otp_template_button'])) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => [['type' => 'text', 'text' => $code]],
            ];
        }

        $ok = self::postMessage($cfg, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => ['code' => $lang],
                'components' => $components,
            ],
        ]);
        if ($ok) {
            return true;
        }

        // 24-hour session window fallback
        return self::postMessage($cfg, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => 'Kitchen Queen admin code: ' . $code . '. Valid for 10 minutes. Do not share this code.',
            ],
        ]);
    }

    public static function queue(int $orderId, string $event, string $channel, string $text, array $extra = []): int
    {
        return Database::insert('notifications', [
            'order_id' => $orderId,
            'channel' => $channel,
            'event' => $event,
            'payload' => json_encode(['text' => $text] + $extra, JSON_UNESCAPED_UNICODE),
            'status' => 'queued',
            'seen_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function queueDashboardAlerts(array $order, array $items, bool $easypaisa): void
    {
        $lines = [];
        foreach ($items as $it) {
            $lines[] = ($it['name'] ?? 'Item') . ' (' . ($it['qty'] ?? 1) . ')';
        }
        $itemText = implode(', ', $lines);
        $addr = trim(($order['address_line'] ?? '') . ' ' . ($order['area_name'] ?? ''));
        if ($easypaisa) {
            $text = "Naya Order Aaya Hai! (EasyPaisa — slip check karein)\n"
                . "Order #{$order['order_no']}\n"
                . "Payment: EasyPaisa (pending approval)\n"
                . "Items: {$itemText}\n"
                . "Total: Rs " . number_format((int) $order['total']) . "\n"
                . "Customer: {$order['customer_name']}\n"
                . "Contact: {$order['customer_phone']}\n"
                . "Address: {$addr}\n"
                . "Dashboard se payment approve/reject karein.";
            $event = 'payment_pending';
        } else {
            $fee = (int) $order['delivery_fee'];
            $text = "Naya Order Aaya Hai!\n"
                . "Order #{$order['order_no']}\n"
                . "Payment: COD\n"
                . "Items: {$itemText}\n"
                . "Total: Rs " . number_format((int) $order['total']) . "\n"
                . "Delivery: Rs {$fee}" . ($order['area_name'] ? " ({$order['area_name']})" : '') . "\n"
                . "Customer: {$order['customer_name']}\n"
                . "Contact: {$order['customer_phone']}\n"
                . "Address: {$addr}";
            $event = 'new_order';
        }
        self::queue((int) $order['id'], $event, 'sound', $text);
        if ($easypaisa) {
            self::queue((int) $order['id'], 'payment_pending', 'dashboard_popup', $text, [
                'order_no' => $order['order_no'] ?? '',
            ]);
        }
    }

    public static function notifyNewOrder(array $order, array $items, bool $easypaisa): void
    {
        self::queueDashboardAlerts($order, $items, $easypaisa);
    }

    public static function notifyApproved(array $order): void
    {
        // Admin order alerts now go by email, not WhatsApp utility templates.
    }

    public static function flushWhatsApp(): void
    {
        $cfg = self::cfg();
        $rows = Database::all(
            "SELECT * FROM notifications WHERE channel = 'whatsapp' AND status = 'queued' ORDER BY id ASC LIMIT 20"
        );
        foreach ($rows as $row) {
            if (!self::ready($cfg)) {
                continue;
            }
            $ok = self::sendCloud($row);
            Database::update(
                'notifications',
                ['status' => $ok ? 'sent' : 'failed'],
                'id = :id',
                ['id' => $row['id']]
            );
        }
    }

    private static function sendCloud(array $row): bool
    {
        $cfg = self::cfg();
        $to = self::adminPhone();
        $payload = json_decode((string) $row['payload'], true) ?: [];
        $template = trim((string) ($cfg['utility_template'] ?? 'order_payment_alert'));
        $lang = trim((string) ($cfg['template_lang'] ?? 'en'));

        $name = substr((string) ($payload['customer_name'] ?? 'Customer'), 0, 60);
        $orderNo = substr((string) ($payload['order_no'] ?? ''), 0, 40);
        $amount = substr((string) ($payload['amount'] ?? ''), 0, 20);
        $payment = substr((string) ($payload['payment'] ?? 'Cash on Delivery'), 0, 40);
        if ($orderNo === '') {
            $orderNo = 'KQ';
        }
        if ($amount === '') {
            $amount = '0';
        }

        $ok = self::postMessage($cfg, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => ['code' => $lang],
                'components' => [[
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $name !== '' ? $name : 'Customer'],
                        ['type' => 'text', 'text' => $orderNo],
                        ['type' => 'text', 'text' => $amount],
                        ['type' => 'text', 'text' => $payment],
                    ],
                ]],
            ],
        ]);
        if ($ok) {
            return true;
        }

        $text = (string) ($payload['text'] ?? '');
        if ($text === '') {
            return false;
        }
        return self::postMessage($cfg, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => substr($text, 0, 4000)],
        ]);
    }

    private static function postMessage(array $cfg, array $body): bool
    {
        self::$lastResponse = '';
        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            $cfg['graph_version'] ?: 'v21.0',
            $cfg['phone_number_id']
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $cfg['access_token'],
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
        ]);
        $res = curl_exec($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $cerr = curl_error($ch);
        curl_close($ch);
        self::$lastResponse = is_string($res) ? $res : '';
        if ($cerr) {
            self::$lastError = $cerr;
            return false;
        }
        if ($http < 200 || $http >= 300) {
            $decoded = json_decode(self::$lastResponse, true);
            $msg = $decoded['error']['message'] ?? self::$lastResponse;
            self::$lastError = 'HTTP ' . $http . ' ' . $msg;
            return false;
        }
        return is_string($res) && $res !== '';
    }
}
