<?php

function pps_send_quote_email() {
    error_log('pps_send_quote_email: Handler called');
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $quote_raw = isset($_POST['quote']) ? $_POST['quote'] : '';
    error_log('pps_send_quote_email: Raw email: ' . print_r($email, true));
    error_log('pps_send_quote_email: Raw quote: ' . print_r($quote_raw, true));
    $quote = $quote_raw ? json_decode(stripslashes($quote_raw), true) : [];
    error_log('pps_send_quote_email: Decoded quote: ' . print_r($quote, true));

    if (!$email || !is_email($email)) {
        error_log('pps_send_quote_email: Invalid email address');
        wp_send_json_error(['message' => 'Invalid email address.']);
    }
    if (empty($quote) || !is_array($quote)) {
        error_log('pps_send_quote_email: Quote data missing or invalid');
        wp_send_json_error(['message' => 'Quote data missing.']);
    }

    // Compose email
    $subject = 'Your Slate Products Quote';
    $message = "Thank you for using our calculator. Here is your quote:\n\n";
    $message .= "Product: " . ($quote['pavingType'] ?? '-') . "\n";
    $message .= "Size Option: " . ($quote['sizeOption'] ?? '-') . "\n";
    $message .= "Size Details: " . ($quote['sizeDetail'] ?? '-') . "\n";
    $message .= "Area: " . ($quote['area'] ?? '-') . " m²\n";
    $message .= "Price per m²: £" . number_format($quote['pricePerSqm'] ?? 0, 2) . "\n";
    $message .= "Total Cost: £" . number_format($quote['totalCost'] ?? 0, 2) . "\n\n";
    $message .= "All prices shown are before VAT & delivery.\n";
    $message .= "If you require anything outside of our standard options please contact us to discuss further.";

    $sender_email = 'enquiries@ardosiaslate.co.uk';
    $sender_name = 'Ardosia Slate'; // Optional: Set a sender name
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $sender_name . ' <' . $sender_email . '>',
        'Reply-To: ' . $sender_email // Optional: Set reply-to
    ];

    error_log('pps_send_quote_email: Sending email to ' . $email . ' from ' . $sender_email);
    error_log('pps_send_quote_email: Email subject: ' . $subject);
    error_log('pps_send_quote_email: Email message: ' . $message);

    $sent = wp_mail($email, $subject, $message, $headers);
    error_log('pps_send_quote_email: wp_mail() returned: ' . var_export($sent, true));
    if ($sent) {
        error_log('pps_send_quote_email: Success');
        wp_send_json_success(['message' => 'Quote email sent.']);
    } else {
        error_log('pps_send_quote_email: Failed to send email');
        wp_send_json_error(['message' => 'Failed to send email.']);
    }
}