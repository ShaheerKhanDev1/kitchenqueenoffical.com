<?php
// KQ-MAILER-V3
declare(strict_types=1);

namespace App\Services;

final class Mailer
{
    private static string $lastError = '';

    public static function lastError(): string
    {
        return self::$lastError;
    }

    public static function send(string $to, string $subject, string $text, ?string $html = null): bool
    {
        self::$lastError = '';
        $to = trim($to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = 'Email invalid';
            return false;
        }

        $cfg = $GLOBALS['KQ_CONFIG']['mail'] ?? [];
        $from = (string) ($cfg['user'] ?? 'otp@kitchenqueenofficial.com');
        $fromName = (string) ($cfg['from_name'] ?? 'Kitchen Queen');
        $html = $html ?: nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$from}>\r\n";

        $ok = @mail($to, $subject, $html, $headers);
        if (!$ok) {
            self::$lastError = 'PHP mail() failed. cPanel me domain email banao.';
        }
        return $ok;
    }

    public static function sendOtp(string $email, string $code, array $meta = []): bool
    {
        $mins = (int) ($GLOBALS['KQ_CONFIG']['otp']['ttl_minutes'] ?? 10);
        $subject = 'Kitchen Queen verification code: ' . $code;
        $html = '<div style="font-family:Arial,sans-serif;padding:24px">'
            . '<h2>Kitchen Queen</h2>'
            . '<p>Your verification code:</p>'
            . '<p style="font-size:32px;letter-spacing:6px;font-weight:700">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p>Valid for ' . $mins . ' minutes.</p>'
            . '</div>';
        return self::send($email, $subject, $code, $html);
    }

    public static function sendNewOrder(array $order, array $items): bool
    {
        $to = (string) ($GLOBALS['KQ_CONFIG']['mail']['admin_to'] ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $no = (string) ($order['order_no'] ?? '');
        return self::send($to, 'New order ' . $no, 'New order ' . $no, '<p>New order ' . htmlspecialchars($no, ENT_QUOTES, 'UTF-8') . '</p>');
    }

    public static function adminInbox(): string
    {
        $to = (string) ($GLOBALS['KQ_CONFIG']['mail']['admin_to'] ?? '');
        return filter_var($to, FILTER_VALIDATE_EMAIL) ? $to : '';
    }
}
