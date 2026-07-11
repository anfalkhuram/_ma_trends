<?php
require_once('inc/config.php');
require_once('inc/otp_functions.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$email = $_POST['email'] ?? '';
$purpose = $_POST['purpose'] ?? '';

if (!$email || !$purpose) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
    exit();
}

// Ensure the email exists based on purpose
$safeEmail = mysqli_real_escape_string($conn, $email);
$userResult = mysqli_query($conn, "SELECT status FROM users WHERE email = '$safeEmail' LIMIT 1");

if (!$userResult || mysqli_num_rows($userResult) === 0) {
    if ($purpose !== 'signup_verification') {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit();
    }
}

$role = 'user';
if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userRow = mysqli_fetch_assoc($userResult);
    if ($userRow['status'] === 'admin') {
        $role = 'admin';
    }
}

if (!canResendOTP($conn, $email, $purpose)) {
    echo json_encode(['success' => false, 'message' => 'Please wait 60 seconds before requesting another OTP.']);
    exit();
}

if (storeAndSendOTP($conn, $email, $role, $purpose)) {
    echo json_encode(['success' => true, 'message' => 'OTP resent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
}
?>
