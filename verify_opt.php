<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['user_otp'] ?? '');

    if (isset($_SESSION['stored_otp']) && $entered_otp == $_SESSION['stored_otp']) {
        unset($_SESSION['stored_otp']); // OTP clear
        echo json_encode(['status' => 'success', 'message' => 'Email successfully verify ho gaya!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ghalat OTP! Dubara koshish karein.']);
    }
}
exit;
?>