<?php

/**
 * Sends an email using PHP's `mail()` function.
 *
 * @param string $to        Recipient email address.
 * @param string $subject   Email subject.
 * @param string $message   Email body (can be plain text or HTML).
 * @param string $from      Sender email address (optional, but highly recommended).
 * @param string $from_name Sender name (optional).
 * @return bool            True on successful send, false on failure.
 */
function sendEmail(string $to, string $subject, string $message, string $from = null, string $from_name = null): bool
{
    // Validate input (basic checks)
    if (empty($to) || empty($subject) || empty($message)) {
        error_log("Error: Missing required parameters for sendEmail().");
        return false;
    }

    // Sanitize input (prevent header injection - important for security)
    $to = filter_var($to, FILTER_SANITIZE_EMAIL);
    $subject = filter_var($subject, FILTER_SANITIZE_STRING);  // Consider FILTER_SANITIZE_FULL_SPECIAL_CHARS if subject needs to allow HTML-like tags
    $message = str_replace(["\r", "\n"], '', $message);  // Remove carriage returns and newlines from message to avoid header injection in some cases.  Re-add them *within* the message body where [...]

    // Construct headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";  // Most common - use 'text/plain' for plaintext emails

    if ($from) {
        $from = filter_var($from, FILTER_SANITIZE_EMAIL);  // Sanitize sender email
        if ($from_name) {
            $from_name = filter_var($from_name, FILTER_SANITIZE_STRING);
            $headers .= "From: " . $from_name . " <" . $from . ">\r\n";
        } else {
            $headers .= "From: " . $from . "\r\n";
        }
    } else {
        // **IMPORTANT SECURITY NOTE:**  Always set a `From:` header!  If not,
        // many mail servers will reject the email or mark it as spam.
        // Ideally, this would be an email address associated with your domain.

        // Replace 'noreply@example.com' with a valid email address that you control.
        $headers .= "From: noreply@example.com\r\n"; // MANDATORY IF YOU DON'T PROVIDE A FROM ADDRESS
    }

    // Attempt to send the email
    try {
        $result = mail($to, $subject, $message, $headers);

        if ($result) {
            return true;
        } else {
            error_log("Error: mail() function failed to send email.  Check your server's mail configuration.");
            return false;
        }
    } catch (Exception $e) {
        error_log("Exception caught while sending email: " . $e->getMessage());
        return false;
    }
}

// --- Example Usage ---
// Replace these with your actual data
$recipientEmail = "shiva.gosula@finmkt.io";
$emailSubject = "Welcome to our website!";
$emailMessage = "<html><body><h1>Welcome!</h1><p>Thank you for registering on our website.</p></body></html>";
$senderEmail = "luan@finmkt.io";
$senderName = "Your Name";

// Send the email
if (sendEmail($recipientEmail, $emailSubject, $emailMessage, $senderEmail, $senderName)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}

?>
