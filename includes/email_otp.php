<?php
/**
 * Kitchen Queen — professional OTP / login-verify email template.
 * Table + inline CSS so Gmail, Outlook, Apple Mail, and Yahoo all render cleanly.
 */

if (!function_exists('kq_client_ip')) {
    function kq_client_ip(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (empty($_SERVER[$key])) {
                continue;
            }
            $raw = (string) $_SERVER[$key];
            $first = trim(explode(',', $raw)[0]);
            if (filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        return 'Unknown';
    }
}

if (!function_exists('kq_describe_device')) {
    function kq_describe_device(?string $ua = null): string
    {
        $ua = $ua ?? (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
        if ($ua === '') {
            return 'Unknown device';
        }

        $os = 'Unknown OS';
        if (stripos($ua, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (stripos($ua, 'Android') !== false) {
            $os = 'Android';
        } elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false || stripos($ua, 'iOS') !== false) {
            $os = 'iOS';
        } elseif (stripos($ua, 'Mac OS') !== false || stripos($ua, 'Macintosh') !== false) {
            $os = 'macOS';
        } elseif (stripos($ua, 'Linux') !== false) {
            $os = 'Linux';
        }

        $browser = 'Browser';
        if (stripos($ua, 'Edg/') !== false || stripos($ua, 'Edge/') !== false) {
            $browser = 'Edge';
        } elseif (stripos($ua, 'OPR/') !== false || stripos($ua, 'Opera') !== false) {
            $browser = 'Opera';
        } elseif (stripos($ua, 'Chrome') !== false && stripos($ua, 'Chromium') === false) {
            $browser = 'Chrome';
        } elseif (stripos($ua, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($ua, 'Safari') !== false) {
            $browser = 'Safari';
        }

        return $browser . ' ' . $os;
    }
}

if (!function_exists('kq_guess_location')) {
    function kq_guess_location(string $ip): string
    {
        if ($ip === '' || $ip === 'Unknown' || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return 'Unknown';
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'Local network';
        }
        // Keep this offline-safe. Hosting IPs in Pakistan will still show the IP row.
        return 'Detected from sign-in';
    }
}

if (!function_exists('kq_format_otp')) {
    function kq_format_otp(string $code): string
    {
        $digits = preg_replace('/\D+/', '', $code) ?? '';
        if (strlen($digits) === 6) {
            return substr($digits, 0, 3) . ' ' . substr($digits, 3, 3);
        }
        return $code;
    }
}

if (!function_exists('kq_otp_email_html')) {
    /**
     * @param array{
     *   code:string,
     *   minutes?:int,
     *   device?:string,
     *   location?:string,
     *   ip?:string,
     *   time?:string,
     *   site_url?:string,
     *   purpose?:string
     * } $data
     */
    function kq_otp_email_html(array $data): string
    {
        $code     = (string) ($data['code'] ?? '');
        $pretty   = htmlspecialchars(kq_format_otp($code), ENT_QUOTES, 'UTF-8');
        $minutes  = (int) ($data['minutes'] ?? 10);
        $device   = htmlspecialchars((string) ($data['device'] ?? kq_describe_device()), ENT_QUOTES, 'UTF-8');
        $ip       = htmlspecialchars((string) ($data['ip'] ?? kq_client_ip()), ENT_QUOTES, 'UTF-8');
        $location = htmlspecialchars((string) ($data['location'] ?? kq_guess_location((string) ($data['ip'] ?? kq_client_ip()))), ENT_QUOTES, 'UTF-8');
        $tz       = new DateTimeZone('Asia/Karachi');
        $time     = htmlspecialchars((string) ($data['time'] ?? (new DateTime('now', $tz))->format('M j, g:i A')), ENT_QUOTES, 'UTF-8');
        $site     = htmlspecialchars((string) ($data['site_url'] ?? 'https://kitchenqueenoffical.com/signin.php'), ENT_QUOTES, 'UTF-8');
        $purpose  = (string) ($data['purpose'] ?? 'login');
        $title    = $purpose === 'order' ? 'Verify your order' : 'Verify your login';
        $cta      = $purpose === 'order' ? 'Open checkout' : 'Verify this login';

        return '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Kitchen Queen — Verify your login</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    Kitchen Queen verification code: ' . $pretty . '. Valid for ' . $minutes . ' minutes.
  </div>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f5f7;padding:28px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;width:100%;">
          <tr>
            <td style="padding:0 8px 18px 8px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td width="36" height="36" align="center" valign="middle" style="background:#111111;border-radius:10px;color:#ffc107;font-weight:800;font-size:13px;letter-spacing:0.4px;">KQ</td>
                  <td style="padding-left:10px;font-size:18px;font-weight:700;color:#111111;letter-spacing:-0.2px;">Kitchen Queen</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:18px;border:1px solid #ececec;overflow:hidden;">
                <tr>
                  <td style="height:6px;background:#ffc107;font-size:0;line-height:0;">&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding:28px 28px 8px 28px;">
                    <h1 style="margin:0 0 10px 0;font-size:26px;line-height:1.25;color:#111111;font-weight:700;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>
                    <p style="margin:0;font-size:14px;line-height:1.6;color:#5f6368;">We detected a new sign-in to your Kitchen Queen account. If this was you, confirm below to continue.</p>
                  </td>
                </tr>
                <tr>
                  <td style="padding:20px 28px 8px 28px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fafafa;border:1px solid #eeeeee;border-radius:12px;">
                      <tr>
                        <td style="padding:14px 16px;border-bottom:1px solid #eeeeee;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td style="font-size:11px;letter-spacing:1.1px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">Device</td>
                              <td align="right" style="font-size:14px;color:#111111;font-weight:600;">' . $device . '</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:14px 16px;border-bottom:1px solid #eeeeee;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td style="font-size:11px;letter-spacing:1.1px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">Location</td>
                              <td align="right" style="font-size:14px;color:#111111;font-weight:600;">' . $location . '</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:14px 16px;border-bottom:1px solid #eeeeee;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td style="font-size:11px;letter-spacing:1.1px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">IP Address</td>
                              <td align="right" style="font-size:14px;color:#111111;font-weight:600;">' . $ip . '</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:14px 16px;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td style="font-size:11px;letter-spacing:1.1px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">Time</td>
                              <td align="right" style="font-size:14px;color:#111111;font-weight:600;">' . $time . '</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding:22px 28px 6px 28px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td align="center" bgcolor="#111111" style="border-radius:10px;">
                          <a href="' . $site . '" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.2px;">' . htmlspecialchars($cta, ENT_QUOTES, 'UTF-8') . '</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding:16px 28px 0 28px;">
                    <p style="margin:0;font-size:13px;color:#8a8a8a;">or enter this OTP code manually</p>
                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding:10px 28px 0 28px;">
                    <p style="margin:0;font-size:38px;line-height:1.2;letter-spacing:8px;font-weight:800;color:#111111;font-family:Arial,Helvetica,sans-serif;">' . $pretty . '</p>
                    <p style="margin:8px 0 0 0;font-size:13px;color:#8a8a8a;">Expires in ' . $minutes . ' minutes</p>
                  </td>
                </tr>
                <tr>
                  <td style="padding:22px 28px 8px 28px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff8e8;border:1px solid #f3e2b3;border-radius:10px;">
                      <tr>
                        <td style="padding:14px 16px;font-size:13px;line-height:1.55;color:#7a5b12;">
                          If you did not request this code, you can ignore this email.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:18px 28px 24px 28px;font-size:12px;line-height:1.6;color:#8a8a8a;border-top:1px solid #f0f0f0;">
                    You are receiving this because of a login attempt on your Kitchen Queen account.<br>
                    Home-cooked desi food · Karachi
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:18px 8px 0 8px;font-size:12px;color:#9aa0a6;">
              Kitchen Queen Official · kitchenqueenofficial.com
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
    }
}

if (!function_exists('kq_otp_email_text')) {
    function kq_otp_email_text(array $data): string
    {
        $code    = kq_format_otp((string) ($data['code'] ?? ''));
        $minutes = (int) ($data['minutes'] ?? 10);
        $device  = (string) ($data['device'] ?? kq_describe_device());
        $ip      = (string) ($data['ip'] ?? kq_client_ip());
        $time    = (string) ($data['time'] ?? (new DateTime('now', new DateTimeZone('Asia/Karachi')))->format('M j, g:i A'));

        return "Kitchen Queen verification code: {$code}\n\n"
            . "Device: {$device}\n"
            . "IP: {$ip}\n"
            . "Time: {$time}\n\n"
            . "This code is valid for {$minutes} minutes.\n"
            . "If you did not request this, you can ignore this email.";
    }
}

if (!function_exists('kq_h')) {
    function kq_h($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('kq_order_email_text')) {
    function kq_order_email_text(array $order, array $items): string
    {
        $lines = [];
        foreach ($items as $it) {
            $lines[] = '- ' . ($it['name'] ?? 'Item') . ' x' . ($it['qty'] ?? 1) . ' = Rs ' . number_format((int) ($it['line_total'] ?? 0));
        }
        $pay = strtoupper((string) ($order['payment_method'] ?? ''));
        return "Naya order aaya hai!\n\n"
            . "Order: " . ($order['order_no'] ?? '') . "\n"
            . "Customer: " . ($order['customer_name'] ?? '') . "\n"
            . "Phone: " . ($order['customer_phone'] ?? '') . "\n"
            . "Email: " . ($order['customer_email'] ?: '-') . "\n"
            . "Type: " . ($order['order_type'] ?? '') . "\n"
            . "Area: " . ($order['area_name'] ?: '-') . "\n"
            . "Address: " . trim(($order['address_line'] ?? '') . ' ' . ($order['address_note'] ?? '')) . "\n"
            . "Payment: {$pay} (" . ($order['payment_status'] ?? '') . ")\n"
            . "Total: Rs " . number_format((int) ($order['total'] ?? 0)) . "\n\n"
            . "Items:\n" . implode("\n", $lines) . "\n";
    }
}

if (!function_exists('kq_order_email_html')) {
    function kq_order_email_html(array $order, array $items, string $audience = 'admin'): string
    {
        $orderNo = kq_h($order['order_no'] ?? 'KQ-NEW');
        $name    = kq_h($order['customer_name'] ?? '-');
        $phone   = kq_h($order['customer_phone'] ?? '-');
        $email   = kq_h($order['customer_email'] ?: '-');
        $type    = kq_h($order['order_type'] ?? '-');
        $area    = kq_h($order['area_name'] ?: '-');
        $address = kq_h(trim(($order['address_line'] ?? '') . ' ' . ($order['address_note'] ?? '')));
        $pay     = kq_h(strtoupper((string) ($order['payment_method'] ?? '')));
        $pstat   = kq_h($order['payment_status'] ?? '');
        $total   = number_format((int) ($order['total'] ?? 0));
        $title   = $audience === 'customer' ? 'Thank you for your order' : 'New order received';
        $intro   = $audience === 'customer'
            ? 'We received your Kitchen Queen order. Our kitchen is preparing it now.'
            : 'A new order just landed. Review the details below and handle it from the dashboard.';

        $rows = '';
        foreach ($items as $it) {
            $rows .= '<tr>'
                . '<td style="padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;color:#111;">' . kq_h($it['name'] ?? 'Item') . '</td>'
                . '<td align="center" style="padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;color:#555;">x' . kq_h($it['qty'] ?? 1) . '</td>'
                . '<td align="right" style="padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;color:#111;font-weight:700;">Rs ' . number_format((int) ($it['line_total'] ?? 0)) . '</td>'
                . '</tr>';
        }

        return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f5f7;padding:28px 12px;">
    <tr><td align="center">
      <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;width:100%;">
        <tr><td style="padding:0 8px 18px 8px;">
          <table role="presentation" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td width="36" height="36" align="center" valign="middle" style="background:#111111;border-radius:10px;color:#ffc107;font-weight:800;font-size:13px;">KQ</td>
              <td style="padding-left:10px;font-size:18px;font-weight:700;color:#111111;">Kitchen Queen</td>
            </tr>
          </table>
        </td></tr>
        <tr><td>
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:18px;border:1px solid #ececec;overflow:hidden;">
            <tr><td style="height:6px;background:#ffc107;font-size:0;line-height:0;">&nbsp;</td></tr>
            <tr><td style="padding:28px 28px 8px 28px;">
              <p style="margin:0 0 6px 0;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">Order ' . $orderNo . '</p>
              <h1 style="margin:0 0 10px 0;font-size:26px;line-height:1.25;color:#111111;">' . $title . '</h1>
              <p style="margin:0;font-size:14px;line-height:1.6;color:#5f6368;">' . $intro . '</p>
            </td></tr>
            <tr><td style="padding:20px 28px 8px 28px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fafafa;border:1px solid #eeeeee;border-radius:12px;">
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Customer <b style="float:right;color:#111;">' . $name . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Phone <b style="float:right;color:#111;">' . $phone . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Email <b style="float:right;color:#111;">' . $email . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Type <b style="float:right;color:#111;">' . $type . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Area <b style="float:right;color:#111;">' . $area . '</b></td></tr>
                <tr><td style="padding:12px 16px;font-size:13px;color:#8a8a8a;">Payment <b style="float:right;color:#111;">' . $pay . ' · ' . $pstat . '</b></td></tr>
              </table>
              <p style="margin:12px 0 0 0;font-size:13px;color:#555;">' . $address . '</p>
            </td></tr>
            <tr><td style="padding:16px 28px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">' . $rows . '
                <tr>
                  <td style="padding:14px 0 0 0;font-size:15px;font-weight:800;color:#111;">Total</td>
                  <td></td>
                  <td align="right" style="padding:14px 0 0 0;font-size:15px;font-weight:800;color:#111;">Rs ' . $total . '</td>
                </tr>
              </table>
            </td></tr>
          </table>
        </td></tr>
        <tr><td align="center" style="padding:18px 8px 0 8px;font-size:12px;color:#9aa0a6;">Kitchen Queen Official · Karachi</td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
    }
}

if (!function_exists('kq_pay_is_easypaisa')) {
    function kq_pay_is_easypaisa(array $order): bool
    {
        return strtolower((string) ($order['payment_method'] ?? '')) === 'easypaisa';
    }
}

if (!function_exists('kq_admin_order_email_text')) {
    function kq_admin_order_email_text(array $order, array $items): string
    {
        $orderNo = (string) ($order['order_no'] ?? '');
        $easypaisa = kq_pay_is_easypaisa($order);
        $lines = [];
        foreach ($items as $it) {
            $lines[] = ($it['name'] ?? 'Item') . ' x' . ($it['qty'] ?? 1) . ' = Rs. ' . number_format((int) ($it['line_total'] ?? 0));
        }
        $banner = $easypaisa
            ? "[ ACTION REQUIRED: EASYPAISA PAYMENT PENDING ]\nCustomer has selected EasyPaisa. Please verify payment in your account.\nGo to the admin dashboard to approve the payment to process this order.\n"
            : "[ CASH ON DELIVERY ]\nNo payment approval needed. Prepare and dispatch this order.\n";
        return "KITCHEN QUEEN\n-- New Order Notification --\n\n"
            . $banner . "\n"
            . "ORDER SUMMARY\n"
            . "Order ID:        #{$orderNo}\n"
            . "Customer Name:   " . ($order['customer_name'] ?? '-') . "\n"
            . "Phone Number:    " . ($order['customer_phone'] ?? '-') . "\n"
            . "Delivery Area:   " . trim(($order['area_name'] ?? '') . ' ' . ($order['address_line'] ?? '')) . "\n"
            . "Payment Method:  " . ($easypaisa ? 'EasyPaisa' : 'Cash on Delivery') . "\n\n"
            . "ITEMS ORDERED\n"
            . implode("\n", $lines) . "\n"
            . "TOTAL: Rs. " . number_format((int) ($order['total'] ?? 0)) . "\n";
    }
}

if (!function_exists('kq_admin_order_email_html')) {
    function kq_admin_order_email_html(array $order, array $items): string
    {
        $orderNo = kq_h($order['order_no'] ?? 'KQ-NEW');
        $name = kq_h($order['customer_name'] ?? '-');
        $phone = kq_h($order['customer_phone'] ?? '-');
        $area = kq_h(trim(($order['area_name'] ?? '') . (($order['address_line'] ?? '') !== '' ? ' — ' . $order['address_line'] : '')));
        $easypaisa = kq_pay_is_easypaisa($order);
        $pay = $easypaisa ? 'EasyPaisa' : 'Cash on Delivery';
        $total = number_format((int) ($order['total'] ?? 0));
        $dash = 'https://dashboard.kitchenqueenoffical.com/orders.php';
        $rows = '';
        foreach ($items as $it) {
            $rows .= '<tr>'
                . '<td style="padding:10px 0;border-bottom:1px solid #ece7d8;font-size:14px;color:#111;">' . kq_h($it['name'] ?? 'Item') . '</td>'
                . '<td align="center" style="padding:10px 0;border-bottom:1px solid #ece7d8;font-size:14px;color:#555;">' . kq_h($it['qty'] ?? 1) . '</td>'
                . '<td align="right" style="padding:10px 0;border-bottom:1px solid #ece7d8;font-size:14px;color:#111;font-weight:700;">Rs. ' . number_format((int) ($it['line_total'] ?? 0)) . '</td>'
                . '</tr>';
        }
        $alert = $easypaisa
            ? '<tr><td style="padding:0 28px 8px 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff4e5;border:1px solid #f0c36d;border-radius:12px;">
                  <tr><td style="padding:14px 16px;">
                    <p style="margin:0 0 6px 0;font-size:13px;font-weight:800;color:#8a5a00;">⚠️ ACTION REQUIRED: EASYPAISA PAYMENT PENDING</p>
                    <p style="margin:0;font-size:13px;line-height:1.55;color:#6b4b10;">Customer has selected EasyPaisa. Please verify payment in your account. Go to the admin dashboard to approve the payment to process this order.</p>
                  </td></tr>
                </table>
              </td></tr>
              <tr><td align="center" style="padding:8px 28px 18px 28px;">
                <a href="' . $dash . '" style="display:inline-block;background:#111;color:#ffc107;text-decoration:none;font-weight:800;font-size:13px;letter-spacing:.6px;padding:12px 22px;border-radius:10px;">OPEN ADMIN DASHBOARD</a>
              </td></tr>'
            : '<tr><td style="padding:0 28px 18px 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3faf3;border:1px solid #cfe8cf;border-radius:12px;">
                  <tr><td style="padding:14px 16px;font-size:13px;line-height:1.55;color:#1f6b2d;">
                    Cash on Delivery order received. No payment approval needed.
                  </td></tr>
                </table>
              </td></tr>';

        return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kitchen Queen — New Order ' . $orderNo . '</title></head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">New Kitchen Queen order ' . $orderNo . ' — ' . ($easypaisa ? 'EasyPaisa approval required' : 'Cash on Delivery') . '</div>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f5f7;padding:28px 12px;">
    <tr><td align="center">
      <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;width:100%;">
        <tr><td style="padding:0 8px 18px 8px;">
          <table role="presentation" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td width="36" height="36" align="center" valign="middle" style="background:#111111;border-radius:10px;color:#ffc107;font-weight:800;font-size:13px;">KQ</td>
              <td style="padding-left:10px;font-size:18px;font-weight:700;color:#111111;">Kitchen Queen</td>
            </tr>
          </table>
        </td></tr>
        <tr><td>
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:18px;border:1px solid #ececec;overflow:hidden;">
            <tr><td style="height:6px;background:#ffc107;font-size:0;line-height:0;">&nbsp;</td></tr>
            <tr><td style="padding:26px 28px 10px 28px;">
              <p style="margin:0 0 6px 0;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">New Order Notification</p>
              <h1 style="margin:0 0 6px 0;font-size:26px;line-height:1.25;color:#111111;">Order #' . $orderNo . '</h1>
              <p style="margin:0;font-size:14px;line-height:1.6;color:#5f6368;">A new order just landed in the kitchen.</p>
            </td></tr>
            ' . $alert . '
            <tr><td style="padding:0 28px 8px 28px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fafafa;border:1px solid #eeeeee;border-radius:12px;">
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Order ID <b style="float:right;color:#111;">#' . $orderNo . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Customer Name <b style="float:right;color:#111;">' . $name . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Phone Number <b style="float:right;color:#111;">' . $phone . '</b></td></tr>
                <tr><td style="padding:12px 16px;border-bottom:1px solid #eeeeee;font-size:13px;color:#8a8a8a;">Delivery Area <b style="float:right;color:#111;">' . $area . '</b></td></tr>
                <tr><td style="padding:12px 16px;font-size:13px;color:#8a8a8a;">Payment Method <b style="float:right;color:#111;">' . $pay . '</b></td></tr>
              </table>
            </td></tr>
            <tr><td style="padding:16px 28px 8px 28px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#8a8a8a;font-weight:700;">Items Ordered</td></tr>
            <tr><td style="padding:0 28px 8px 28px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding:0 0 8px 0;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#8a8a8a;">Item</td>
                  <td align="center" style="padding:0 0 8px 0;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#8a8a8a;">Qty</td>
                  <td align="right" style="padding:0 0 8px 0;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#8a8a8a;">Price</td>
                </tr>
                ' . $rows . '
                <tr>
                  <td style="padding:14px 0 0 0;font-size:15px;font-weight:800;color:#111;">Total</td>
                  <td></td>
                  <td align="right" style="padding:14px 0 0 0;font-size:15px;font-weight:800;color:#111;">Rs. ' . $total . '</td>
                </tr>
              </table>
            </td></tr>
            <tr><td style="padding:8px 28px 24px 28px;font-size:12px;line-height:1.6;color:#8a8a8a;">Kitchen Queen Orders</td></tr>
          </table>
        </td></tr>
        <tr><td align="center" style="padding:18px 8px 0 8px;font-size:12px;color:#9aa0a6;">Kitchen Queen Official · Karachi</td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
    }
}
