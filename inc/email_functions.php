<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Assuming Composer is used
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/config.php';

/**
 * Render an email template using the base_template layout
 */
function renderEmailTemplate($templateName, $data) {
    // Extract data variables into the local scope
    extract($data);
    
    // Start buffering the specific template content
    ob_start();
    $templatePath = __DIR__ . '/email_templates/' . $templateName . '.php';
    if (file_exists($templatePath)) {
        require $templatePath;
    } else {
        echo "<p>Template $templateName not found.</p>";
    }
    $emailContent = ob_get_clean();
    
    // Wrap it in the base template
    ob_start();
    require __DIR__ . '/email_templates/base_template.php';
    return ob_get_clean();
}

/**
 * Send a transactional email and log it
 */
function sendTransactionalEmail($toEmail, $subject, $templateName, $data, $role = 'user', $orderId = null) {
    global $conn;
    
    $mail = new PHPMailer(true);
    $status = 'failed';
    $errorMessage = null;

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = renderEmailTemplate($templateName, $data);
        
        // Simple plain text fallback (strip tags but keep spacing)
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</h1>', '</h2>', '</p>', '</td>', '</tr>'], "\n", $mail->Body));

        $mail->send();
        $status = 'sent';
        $result = true;
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo;
        $result = false;
    }
    
    // Log the email
    if (isset($conn)) {
        try {
            $safeEmail = mysqli_real_escape_string($conn, $toEmail);
            $safeRole = mysqli_real_escape_string($conn, $role);
            $safeType = mysqli_real_escape_string($conn, $templateName);
            $safeSubject = mysqli_real_escape_string($conn, $subject);
            $safeOrderId = $orderId ? (int)$orderId : 'NULL';
            $safeStatus = mysqli_real_escape_string($conn, $status);
            $safeError = $errorMessage ? "'" . mysqli_real_escape_string($conn, $errorMessage) . "'" : 'NULL';
            
            $sql = "INSERT INTO email_logs (recipient_email, recipient_role, email_type, subject, related_order_id, status, error_message, sent_at)
                    VALUES ('$safeEmail', '$safeRole', '$safeType', '$safeSubject', $safeOrderId, '$safeStatus', $safeError, NOW())";
            mysqli_query($conn, $sql);
        } catch (Throwable $e) {
            // Silently ignore email_logs insert errors (e.g. table not found) so it doesn't break the flow
        }
    }
    
    return $result;
}
?>
