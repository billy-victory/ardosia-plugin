<?php

function pps_send_quote_email() {
    error_log('pps_send_quote_email: Handler called');
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $quote_raw = isset($_POST['quote']) ? $_POST['quote'] : '';
    $postcode = isset($_POST['postcode']) ? sanitize_text_field($_POST['postcode']) : '';
    
    error_log('pps_send_quote_email: Raw email: ' . print_r($email, true));
    error_log('pps_send_quote_email: Raw quote: ' . print_r($quote_raw, true));
    error_log('pps_send_quote_email: Postcode: ' . print_r($postcode, true));
    
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
    
    // Save quote data to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'pps_quotes';
    
    $data = array(
        'email' => $email,
        'postcode' => $postcode,
        'paving_type' => isset($quote['pavingType']) ? $quote['pavingType'] : '',
        'size_option' => isset($quote['sizeOption']) ? $quote['sizeOption'] : '',
        'size_detail' => isset($quote['sizeDetail']) ? $quote['sizeDetail'] : '',
        'area' => isset($quote['area']) ? floatval($quote['area']) : 0,
        'price_per_sqm' => isset($quote['pricePerSqm']) ? floatval($quote['pricePerSqm']) : 0,
        'total_cost' => isset($quote['totalCost']) ? floatval($quote['totalCost']) : 0,
        'quote_data' => $quote_raw
    );
    
    $result = $wpdb->insert($table_name, $data);
    
    if ($result === false) {
        error_log('pps_send_quote_email: Failed to save quote to database: ' . $wpdb->last_error);
        // Continue with email sending even if database save fails
    } else {
        error_log('pps_send_quote_email: Quote saved to database with ID: ' . $wpdb->insert_id);
    }

    // Compose email
    $subject = 'Your Slate Products Quote';
    $message = "Thank you for using our calculator. Here is your quote:\n\n";
    $message .= "Product: " . ($quote['pavingType'] ?? '-') . "\n";
    $message .= "Size Option: " . ($quote['sizeOption'] ?? '-') . "\n";
    $message .= "Size Details: " . ($quote['sizeDetail'] ?? '-') . "\n";
    $message .= "Area: " . ($quote['area'] ?? '-') . " m²\n";
    $message .= "Price per m²: £" . number_format($quote['pricePerSqm'] ?? 0, 2) . "\n";
    $message .= "Total Cost: £" . number_format($quote['totalCost'] ?? 0, 2) . "\n";
    
    // Add postcode information if provided
    if (!empty($postcode)) {
        $message .= "Delivery Postcode: " . $postcode . "\n";
    }
    
    $message .= "\nAll prices shown are before VAT & delivery.\n";
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
    
    // Send notification email to admin
    $admin_email = 'enquiries@ardosiaslate.co.uk'; // Change this to the email that should receive notifications
    $admin_subject = 'New Quote Request from ' . $email;
    $admin_message = "A new quote has been requested.\n\n";
    $admin_message .= "Customer Email: " . $email . "\n";
    if (!empty($postcode)) {
        $admin_message .= "Delivery Postcode: " . $postcode . "\n";
    }
    $admin_message .= "\nQuote Details:\n";
    $admin_message .= "Product: " . ($quote['pavingType'] ?? '-') . "\n";
    $admin_message .= "Size Option: " . ($quote['sizeOption'] ?? '-') . "\n";
    $admin_message .= "Size Details: " . ($quote['sizeDetail'] ?? '-') . "\n";
    $admin_message .= "Area: " . ($quote['area'] ?? '-') . " m²\n";
    $admin_message .= "Price per m²: £" . number_format($quote['pricePerSqm'] ?? 0, 2) . "\n";
    $admin_message .= "Total Cost: £" . number_format($quote['totalCost'] ?? 0, 2) . "\n\n";
    $admin_message .= "View all quotes in your WordPress admin dashboard under 'Ardosia Quotes'.";
    
    $admin_sent = wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    error_log('pps_send_quote_email: Admin notification email sent: ' . var_export($admin_sent, true));
    
    if ($sent) {
        error_log('pps_send_quote_email: Success');
        wp_send_json_success(['message' => 'Quote email sent.']);
    } else {
        error_log('pps_send_quote_email: Failed to send email');
        wp_send_json_error(['message' => 'Failed to send email.']);
    }
}