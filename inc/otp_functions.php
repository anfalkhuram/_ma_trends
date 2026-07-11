<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_functions.php';

/**
 * Generates a random 6-digit OTP
 */
function generateOTP() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Stores the OTP in the database and sends the email
 */
function storeAndSendOTP($conn, $email, $role, $purpose) {
    // Invalidate previous active OTPs for this email and purpose
    invalidateOTP($conn, $email, $purpose);
    
    $otp = generateOTP();
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    $safeEmail = mysqli_real_escape_string($conn, $email);
    $safeRole = mysqli_real_escape_string($conn, $role);
    $safePurpose = mysqli_real_escape_string($conn, $purpose);
    
    $sql = "INSERT INTO otp_verifications (email, role, purpose, otp_code, expires_at) 
            VALUES ('$safeEmail', '$safeRole', '$safePurpose', '$otp', '$expires_at')";
            
    try {
        if (mysqli_query($conn, $sql)) {
            // Send email
            $purposeText = "";
            if ($purpose === 'signup_verification') {
                $purposeText = "verifying your new account";
            } elseif ($purpose === 'login_verification') {
                $purposeText = "logging into your account";
            } elseif ($purpose === 'forgot_password') {
                $purposeText = "resetting your password";
            }
            
            $subject = "Your MATrends OTP Code";
            $data = [
                'otp' => $otp,
                'purposeText' => $purposeText
            ];
            
            return sendTransactionalEmail($email, $subject, 'otp_email', $data, $role);
        }
    } catch (Throwable $e) {
        // Log the error secretly and return false so login.php can catch it
        error_log("OTP Insert Error: " . $e->getMessage());
    }
    return false;
}

/**
 * Verifies the OTP
 * Returns array: ['success' => bool, 'message' => string]
 */
function verifyOTP($conn, $email, $purpose, $submittedOtp) {
    $safeEmail = mysqli_real_escape_string($conn, $email);
    $safePurpose = mysqli_real_escape_string($conn, $purpose);
    $safeOtp = mysqli_real_escape_string($conn, $submittedOtp);
    
    $sql = "SELECT * FROM otp_verifications 
            WHERE email = '$safeEmail' 
            AND purpose = '$safePurpose' 
            AND is_used = 0 
            ORDER BY id DESC LIMIT 1";
            
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if expired
        if (strtotime($row['expires_at']) < time()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }
        
        // Check attempts limit
        if ($row['attempts'] >= 5) {
            return ['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP.'];
        }
        
        // Verify code
        if ($row['otp_code'] === $safeOtp) {
            // Mark as used
            $id = $row['id'];
            mysqli_query($conn, "UPDATE otp_verifications SET is_used = 1 WHERE id = $id");
            return ['success' => true, 'message' => 'OTP verified successfully.'];
        } else {
            // Increment attempts
            $id = $row['id'];
            $newAttempts = $row['attempts'] + 1;
            mysqli_query($conn, "UPDATE otp_verifications SET attempts = $newAttempts WHERE id = $id");
            return ['success' => false, 'message' => 'Incorrect OTP.'];
        }
    }
    
    return ['success' => false, 'message' => 'No active OTP found.'];
}

/**
 * Invalidates previous OTPs
 */
function invalidateOTP($conn, $email, $purpose) {
    $safeEmail = mysqli_real_escape_string($conn, $email);
    $safePurpose = mysqli_real_escape_string($conn, $purpose);
    
    $sql = "UPDATE otp_verifications SET is_used = 1 
            WHERE email = '$safeEmail' AND purpose = '$safePurpose' AND is_used = 0";
    try {
        mysqli_query($conn, $sql);
    } catch (Throwable $e) {
        // Silently ignore to prevent stopping flow
    }
}

/**
 * Checks if resend is allowed (60 seconds cooldown)
 */
function canResendOTP($conn, $email, $purpose) {
    $safeEmail = mysqli_real_escape_string($conn, $email);
    $safePurpose = mysqli_real_escape_string($conn, $purpose);
    
    $sql = "SELECT created_at FROM otp_verifications 
            WHERE email = '$safeEmail' 
            AND purpose = '$safePurpose' 
            ORDER BY id DESC LIMIT 1";
            
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $timePassed = time() - strtotime($row['created_at']);
        if ($timePassed < 60) {
            return false;
        }
    }
    return true;
}
?>
