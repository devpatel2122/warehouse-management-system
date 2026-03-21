<?php
/**
 * SMS Helper Utility
 */

/**
 * Sends an SMS message to a phone number.
 * For now, this is a mock implementation.
 * You can replace this with Twilio, Nexmo, etc.
 */
function sendSMS($phone, $message) {
    // Implement real API call here
    // Example for Twilio:
    /*
    $sid = "YOUR_ACCOUNT_SID";
    $token = "YOUR_AUTH_TOKEN";
    $twilio_number = "YOUR_TWILIO_NUMBER";
    // call Twilio API
    */

    // Log the SMS to a file for debugging/mocking
    $log = date('Y-m-d H:i:s') . " | To: $phone | Message: $message" . PHP_EOL;
    return file_put_contents(__DIR__ . '/../sms_logs.txt', $log, FILE_APPEND) !== false;
}
