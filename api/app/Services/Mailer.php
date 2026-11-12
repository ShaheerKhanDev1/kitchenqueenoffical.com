<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

$lib = dirname(__DIR__) . '/Lib/PHPMailer';
if (!class_exists(PHPMailer::class) && is_file($lib . '/PHPMailer.php')) {
    require_once $lib . '/Exception.php';
    require_once $lib . '/PHPMailer.php';
    require_once $lib . '/SMTP.php';
}

final class Mailer
{
    private static string $lastError = '';
    private static string $lastRoute = '';
    private static string $smtpReason = 'unknown';

    public static function lastError(): string
    {
        return self::$lastError;
    }

    public static function lastRoute(): string
    {
        return self::$lastRoute;
    }

    private static function log(string $line): void
    {
        $dirs = [
            dirname(__DIR__, 2) . '/storage',
            dirname(__DIR__, 3) . '/storage',
        ];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            if (is_dir($dir) && is_writable($dir)) {
                @file_put_contents(
                    $dir . '/mail.log',
                    date('Y-m-d H:i:s') . ' ' . $line . "\n",
                    FILE_APPEND
                );
                return;
            }
        }
    }

    private static function loadTemplate(): void
    {
        $candidates = [
            dirname(__DIR__, 3) . '/includes/email_otp.php',
            dirname(__DIR__, 4) . '/kitchenqueenoffical.com/includes/email_otp.php',
            dirname(__DIR__, 4) . '/dashboard.kitchenqueenoffical.com/includes/email_otp.php',
        ];
        foreach ($candidates as $path) {
            if (is_file($path)) {
                require_once $path;
                return;
            }
        }
    }

    private static function mailCfg(): array
    {
        $cfg = $GLOBALS['KQ_CONFIG']['mail'] ?? [];
        $roots = [
            dirname(__DIR__, 3) . '/mail_config.php',
            dirname(__DIR__, 4) . '/kitchenqueenoffical.com/mail_config.php',
            dirname(__DIR__, 4) . '/dashboard.kitchenqueenoffical.com/mail_config.php',
        ];
        foreach ($roots as $path) {
            if (is_file($path)) {
                $extra = require $path;
                if (is_array($extra)) {
                    $cfg = array_merge($cfg, $extra);
                }
                break;
            }
        }
        foreach (['pass', 'admin_pass'] as $key) {
            if (!empty($cfg[$key])) {
                $cfg[$key] = preg_replace('/\s+/', '', (string) $cfg[$key]);
            }
        }
        if (empty($cfg['domain_from'])) {
            $cfg['domain_from'] = 'noreply@kitchenqueenoffical.com';
        }
        return $cfg;
    }

    // Store only fixed diagnostic labels: never SMTP transcripts, credentials or OTPs.
    private static function classifySmtpError(string $message): string
    {
        $message = strtolower($message);
        foreach ([
            'dns_lookup_failed' => ['getaddrinfo', 'php_network_getaddresses', 'name or service not known', 'temporary failure in name resolution'],
            'connection_refused' => ['connection refused'],
            'network_unreachable' => ['network is unreachable', 'no route to host'],
            'tls_failed' => ['certificate verify failed', 'enable crypto', 'ssl operation failed', 'starttls failed'],
            'authentication_failed' => ['could not authenticate', 'authentication failed', 'username and password not accepted', 'application-specific password'],
            'timeout' => ['timed out', 'timed-out', 'timeout'],
            'connection_failed' => ['could not connect', 'failed to connect', 'connect() failed'],
        ] as $reason => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($message, $pattern) !== false) return $reason;
            }
        }
        return 'unknown';
    }

    private static function sendVia(array $opts, string $to, string $from, string $fromName, string $subject, string $html, string $text): bool
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Timeout = (int) ($opts['timeout'] ?? 15);
        // Timelimit belongs to SMTP, not PHPMailer.
        $mail->getSMTPInstance()->Timelimit = $mail->Timeout;
        self::$smtpReason = 'unknown';
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = static function (string $message, int $level): void {
            // Ignore client commands (including AUTH data and message bodies).
            if (strpos($message, 'CLIENT -> SERVER:') === 0) return;
            $reason = self::classifySmtpError($message);
            if ($reason !== 'unknown' && ($reason !== 'connection_failed' || self::$smtpReason === 'unknown')) {
                self::$smtpReason = $reason;
            }
        };
        $mail->SMTPKeepAlive = false;
        $mail->Host = $opts['host'];
        $mail->Port = (int) $opts['port'];
        $mail->SMTPAuth = !empty($opts['auth']);
        if ($mail->SMTPAuth) {
            $mail->Username = (string) $opts['user'];
            $mail->Password = (string) $opts['pass'];
        }
        $secure = (string) ($opts['secure'] ?? '');
        if ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($secure === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
        }
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'quoted-printable';
        $mail->Hostname = 'kitchenqueenoffical.com';
        $mail->Sender = $from;
        $mail->setFrom($from, $fromName);
        $mail->addReplyTo($from, $fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $html;
        $mail->AltBody = $text;
        $mail->Priority = 1;
        try {
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            $detail = $mail->getSMTPInstance()->getError();
            $reason = self::classifySmtpError($e->getMessage() . ' ' . $mail->ErrorInfo . ' ' . json_encode($detail));
            if ($reason !== 'unknown' && ($reason !== 'connection_failed' || self::$smtpReason === 'unknown')) {
                self::$smtpReason = $reason;
            }
            $code = (string) ($detail['smtp_code'] ?? '');
            $code = preg_match('/^[0-9]{3}$/', $code) ? $code : 'none';
            self::log('SMTP diagnostic version=2026-10-29 reason=' . self::$smtpReason
                . ' smtp_code=' . $code . ' port=' . (int) $opts['port']
                . ' tls=' . (in_array($secure, ['tls', 'ssl'], true) ? $secure : 'off')
                . ' timeout_seconds=' . $mail->Timeout);
            throw $e;
        }
    }

    private static function sendNative(string $to, string $from, string $fromName, string $subject, string $html, string $text, string $replyTo): bool
    {
        if (!function_exists('mail') || !class_exists(PHPMailer::class)) {
            self::log('MAIL transport=hosting reason=mail_or_library_unavailable');
            return false;
        }
        $start = microtime(true);
        try {
            $mail = new PHPMailer(true);
            $mail->isMail();
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'quoted-printable';
            $mail->setFrom($from, $fromName);
            $mail->Sender = $from;
            if (filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $mail->addReplyTo($replyTo, $fromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = $text;
            if (!$mail->send()) return false;
            self::log('MAIL accepted transport=hosting elapsed_ms=' . (int)((microtime(true)-$start)*1000));
            return true;
        } catch (\Throwable $e) {
            // No recipient, body, subject, OTP or credentials in logs.
            self::log('MAIL failed transport=hosting reason=local_handoff_failed elapsed_ms=' . (int)((microtime(true)-$start)*1000));
            return false;
        }
    }


    // HTTPS API only when enabled; never silently route a rejected/uncertain send
    // back through the delayed hosting relay (which could also duplicate mail).
    private static function sendBrevo(array $cfg, string $to, string $fromName, string $subject, string $html, string $replyTo): bool
    {
        self::$lastRoute = 'brevo-api';
        $key = trim((string)($cfg['api_key'] ?? ''));
        if ($key === '' || $key === 'PASTE_BREVO_API_KEY_HERE' || preg_match('/\s/', $key)) {
            self::$lastError = 'Brevo API key configure karein';
            self::log('BREVO failed reason=key_missing_or_invalid');
            return false;
        }
        if (!function_exists('curl_init')) {
            self::$lastError = 'Hosting par PHP cURL enable karein';
            self::log('BREVO failed reason=curl_missing');
            return false;
        }
        $sender = (string)($cfg['sender_email'] ?? 'noreply@kitchenqueenoffical.com');
        if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = 'Brevo sender email invalid';
            return false;
        }
        $payload = [
            'sender' => ['email' => $sender, 'name' => $fromName],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'htmlContent' => $html,
        ];
        if (filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $payload['replyTo'] = ['email' => $replyTo, 'name' => $fromName];
        }
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) {
            self::$lastError = 'Email content encode nahi hua';
            return false;
        }
        $start = microtime(true);
        $ch = null;
        try {
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            if ($ch === false) throw new \RuntimeException('curl_init');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['accept: application/json', 'content-type: application/json', 'api-key: ' . $key],
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_FOLLOWLOCATION => false,
            ]);
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = is_string($body) ? json_decode($body, true) : null;
            if ($errno === 0 && $status === 201 && is_array($result) && !empty($result['messageId'])) {
                self::log('BREVO accepted status=201 elapsed_ms=' . (int)((microtime(true)-$start)*1000));
                return true;
            }
            // Never log API key, raw response, subject, OTP or recipient.
            $reason = $errno !== 0 ? 'network_or_tls' : 'provider_rejected';
            if ($status === 401) $reason = 'invalid_api_key';
            elseif ($status === 403) $reason = 'permissions_ip_or_account';
            elseif ($status === 429) $reason = 'rate_limited';
            self::log('BREVO failed reason=' . $reason . ' status=' . $status . ' curl_errno=' . $errno
                . ' elapsed_ms=' . (int)((microtime(true)-$start)*1000));
            self::$lastError = 'Email request failed; Brevo logs aur api/storage/mail.log check karein';
            return false;
        } catch (\Throwable $e) {
            self::log('BREVO failed reason=client_error');
            self::$lastError = 'Brevo connection failed';
            return false;
        } finally {
            if ($ch !== null && $ch !== false) curl_close($ch);
        }
    }

    public static function send(string $to, string $subject, string $text, ?string $html = null, array $opts = []): bool
    {
        self::$lastError = '';
        self::$lastRoute = '';
        $to = trim($to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = 'Email address invalid hai';
            return false;
        }

        $cfg = self::mailCfg();
        $html = $html ?: nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
        $fromName = (string) ($opts['from_name'] ?? $cfg['from_name'] ?? 'Kitchen Queen');
        $domainFrom = (string) ($cfg['domain_from'] ?? 'noreply@kitchenqueenoffical.com');
        $account = $opts['account'] ?? 'customer';
        $user = (string)($account === 'admin' ? ($cfg['admin_user'] ?? $cfg['user'] ?? '') : ($cfg['user'] ?? ''));
        $pass = (string)($account === 'admin' ? ($cfg['admin_pass'] ?? $cfg['pass'] ?? '') : ($cfg['pass'] ?? ''));
        $brevoPath = __DIR__ . '/brevo_config.php';
        if (is_file($brevoPath)) {
            $brevo = require $brevoPath;
            if (is_array($brevo) && !empty($brevo['enabled'])) {
                return self::sendBrevo($brevo, $to, $fromName, $subject, $html, $user);
            }
        }
        // Restore the original hosting transport. Do not wait for an external SMTP timeout first.
        // A successful handoff means queued by the host, not confirmed inbox delivery.
        if (self::sendNative($to, $domainFrom, $fromName, $subject, $html, $text, $user)) {
            self::$lastRoute = 'php-mail';
            return true;
        }
        $route = ['host'=>$cfg['host'] ?? 'smtp.gmail.com', 'port'=>$cfg['port'] ?? 587, 'secure'=>$cfg['secure'] ?? 'tls', 'auth'=>true, 'user'=>$user, 'pass'=>$pass, 'timeout'=>15];
        if ($user !== '' && $pass !== '' && class_exists(PHPMailer::class)) {
            $start = microtime(true);
            try {
                self::sendVia($route, $to, $user, $fromName, $subject, $html, $text);
                self::$lastRoute = $route['host'];
                self::log('SMTP accepted elapsed_ms=' . (int)((microtime(true)-$start)*1000));
                return true;
            } catch (\Throwable $e) {
                self::$lastError = 'SMTP delivery failed';
                self::log('SMTP failed elapsed_ms=' . (int)((microtime(true)-$start)*1000));
            }
        }
        if (self::$lastError === '') {
            self::$lastError = 'Mail configuration or library unavailable';
            self::log('SMTP diagnostic reason=' . (!class_exists(PHPMailer::class) ? 'phpmailer_missing' : 'credentials_missing'));
        }
        self::log('FAIL ' . self::$lastError);
        return false;
    }

    public static function sendOtp(string $email, string $code, array $meta = []): bool
    {
        self::loadTemplate();
        $mins = (int) ($GLOBALS['KQ_CONFIG']['otp']['ttl_minutes'] ?? 10);
        $payload = array_merge([
            'code' => $code,
            'minutes' => $mins,
            'site_url' => 'https://kitchenqueenoffical.com/signin.php',
        ], $meta);
        $subject = 'Kitchen Queen verification code: ' . $code;
        if (function_exists('kq_otp_email_html')) {
            $text = kq_otp_email_text($payload);
            $html = kq_otp_email_html($payload);
        } else {
            $text = "Your Kitchen Queen verification code is {$code}. Valid for {$mins} minutes.";
            $html = '<p>Your Kitchen Queen verification code is <b>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</b></p>';
        }
        return self::send($email, $subject, $text, $html, ['account' => $meta['account'] ?? 'customer', 'priority' => '1', 'gmail_first' => true]);
    }

    public static function sendNewOrder(array $order, array $items): bool
    {
        self::loadTemplate();
        $to = self::adminInbox();
        if ($to === '') {
            self::$lastError = 'Admin inbox missing';
            return false;
        }
        $pay = strtolower((string) ($order['payment_method'] ?? ''));
        $easypaisa = $pay === 'easypaisa';
        $orderNo = (string) ($order['order_no'] ?? '');
        $subject = $easypaisa
            ? 'ACTION REQUIRED: EasyPaisa Order #' . $orderNo . ' Received'
            : 'New Order #' . $orderNo . ' Received — Kitchen Queen';
        $text = function_exists('kq_admin_order_email_text')
            ? kq_admin_order_email_text($order, $items)
            : (function_exists('kq_order_email_text') ? kq_order_email_text($order, $items) : ('New order ' . $orderNo));
        $html = function_exists('kq_admin_order_email_html')
            ? kq_admin_order_email_html($order, $items)
            : (function_exists('kq_order_email_html') ? kq_order_email_html($order, $items, 'admin') : null);
        return self::send($to, $subject, $text, $html, [
            'account' => 'admin',
            'from_name' => 'Kitchen Queen Orders',
            'gmail_first' => true,
            'priority' => '1',
        ]);
    }

    public static function sendCustomerOrder(array $order, array $items, string $kind = 'customer_order'): bool {
        $to = (string)($order['customer_email'] ?? '');
        $pending = $order['payment_method'] === 'easypaisa' && $order['payment_status'] !== 'approved';
        $title = $kind === 'payment_rejected' ? 'Payment needs attention' : ($pending ? 'Order received — awaiting payment approval' : 'Your order is confirmed');
        $e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        $money = static fn($v) => 'Rs ' . number_format((float)$v);
        $rows = '';
        foreach ($items as $i) $rows .= '<tr><td style="padding:12px 0;border-bottom:1px solid #eee">'.$e($i['qty']).' × '.$e($i['name']).'<br><small>'.$e($i['extras'] ?? '').'</small></td><td style="text-align:right;border-bottom:1px solid #eee">'.$money($i['line_total']).'</td></tr>';
        foreach (['subtotal'=>'Subtotal','discount'=>'Discount','delivery_fee'=>'Delivery','tax'=>'Tax','total'=>'Total amount'] as $key=>$label) $rows .= '<tr><td style="padding:8px 0">'.$label.'</td><td style="text-align:right;font-weight:700">'.($key==='discount'?'− ':'').$money($order[$key] ?? 0).'</td></tr>';
        $address = $order['order_type']==='pickup' ? 'Store pickup' : trim($order['address_line'].' '.$order['area_name']);
        $html = '<!doctype html><html><body style="margin:0;background:#f5f3ed;font-family:Arial,sans-serif;color:#292c25"><table role="presentation" width="100%"><tr><td align="center" style="padding:24px 12px"><table role="presentation" width="600" style="max-width:100%;background:#fff"><tr><td style="background:#263c30;color:#fff;padding:28px;font-size:24px;font-weight:bold">KITCHEN QUEEN</td></tr><tr><td style="padding:28px"><p style="color:#6d734f">ORDER '.$e($order['order_no']).'</p><h1 style="font-size:25px">'.$e($title).'</h1><p>Hello '.$e($order['customer_name']).', thank you for your order.</p><table width="100%" cellpadding="0" cellspacing="0">'.$rows.'</table><h2 style="font-size:16px">Billing & delivery details</h2><p>'.$e($order['customer_name']).'<br>'.$e($address).'<br>'.$e($order['customer_phone']).'<br>'.$e($to).'</p><p>Payment: '.$e(strtoupper($order['payment_method'])).' · '.$e($order['payment_status']).'</p><p style="padding:16px;background:#f5f3ed">'.($pending ? 'Your receipt becomes available after the admin verifies your payment. This message acknowledges your order; it is not a payment receipt.' : ($order['payment_method']==='cod' ? 'Please pay the total amount on delivery.' : 'Your payment has been approved. Open your order to view the receipt.')).'</p><a href="https://kitchenqueenoffical.com/checkout.php?order='.$e(rawurlencode($order['order_no'])).'" style="display:inline-block;background:#263c30;color:#fff;padding:14px 22px;text-decoration:none">View your order</a><p style="font-size:12px;color:#666">Kitchen Queen · Karachi</p></td></tr></table></td></tr></table></body></html>';
        $text = $title.' | '.$order['order_no'].' | Total '.$money($order['total']).' | '.$address;
        return self::send($to, $title.' · '.$order['order_no'], $text, $html, ['account'=>'customer']);
    }

    public static function adminInbox(): string
    {
        $cfg = self::mailCfg();
        try {
            $fromSettings = (string) Settings::get('admin_email', '');
            if (filter_var($fromSettings, FILTER_VALIDATE_EMAIL)) {
                return $fromSettings;
            }
        } catch (\Throwable $e) {
        }
        $configured = (string) ($cfg['admin_to'] ?? '');
        if (filter_var($configured, FILTER_VALIDATE_EMAIL)) {
            return $configured;
        }
        $fallback = (string) ($cfg['admin_user'] ?? $cfg['user'] ?? '');
        return filter_var($fallback, FILTER_VALIDATE_EMAIL) ? $fallback : '';
    }
}
