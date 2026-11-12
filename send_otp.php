<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function json_exit(array $payload, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_exit(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

$user_email = trim((string) ($_POST['email'] ?? ''));
if ($user_email === '' || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    json_exit(['status' => 'error', 'message' => 'Please enter a valid email address']);
}

$otp = (string) random_int(100000, 999999);
$_SESSION['stored_otp'] = $otp;
$_SESSION['otp_email'] = $user_email;
$_SESSION['otp_expires'] = time() + 600;
$_SESSION['otp_sent_at'] = time();

$root = __DIR__ . '/api';
$GLOBALS['KQ_CONFIG'] = require $root . '/config/config.php';
require_once $root . '/app/Services/Mailer.php';

$ok = App\Services\Mailer::sendOtp($user_email, $otp);
if ($ok) {
    json_exit(['status' => 'success', 'message' => 'OTP sent to your email. Check Inbox and Spam.']);
}
json_exit([
    'status' => 'error',
    'message' => 'Email could not be sent.',
    'detail' => App\Services\Mailer::lastError(),
], 500);
