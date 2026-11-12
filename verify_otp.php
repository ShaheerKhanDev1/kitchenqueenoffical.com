<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function json_exit(array $payload, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_exit(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

$entered_otp = trim((string)($_POST['user_otp'] ?? ''));
if ($entered_otp === '' || !preg_match('/^\d{6}$/', $entered_otp)) {
    json_exit(['status' => 'error', 'message' => '6 digit OTP likhein']);
}

if (!isset($_SESSION['stored_otp'], $_SESSION['otp_expires'])) {
    json_exit(['status' => 'error', 'message' => 'Pehle naya OTP bhejein.']);
}

if (time() > (int)$_SESSION['otp_expires']) {
    unset($_SESSION['stored_otp'], $_SESSION['otp_expires'], $_SESSION['otp_email']);
    json_exit(['status' => 'error', 'message' => 'OTP expire ho gaya. Naya OTP bhejein.']);
}

if (!hash_equals((string)$_SESSION['stored_otp'], $entered_otp)) {
    json_exit(['status' => 'error', 'message' => 'Ghalat OTP! Dubara koshish karein.']);
}

$verified_email = $_SESSION['otp_email'] ?? null;
unset($_SESSION['stored_otp'], $_SESSION['otp_expires']);
$_SESSION['email_verified'] = true;
$_SESSION['verified_email'] = $verified_email;

json_exit([
    'status'  => 'success',
    'message' => 'Email successfully verify ho gaya!',
    'email'   => $verified_email,
]);
