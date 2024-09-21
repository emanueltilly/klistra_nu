<?php
class TransportEncryption
{
    // Encrypt function using the session key
    function encryptJSON($jsonData) {
        // Check if the session key is set
        if (!isset($_SESSION['session_transport_token'])) {
            error_log("Encryption key is not set in session.");
            return null;
        }

        $key = $_SESSION['session_transport_token']; // Use session key
        $iv = "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10"; // Static IV

        $jsonStr = json_encode($jsonData);
        $encryptedData = openssl_encrypt($jsonStr, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encryptedData);
    }

    // Decrypt function using the session key
    function decryptJSON($encryptedBase64) {
        // Check if the session key is set
        if (!isset($_SESSION['session_transport_token'])) {
            error_log("Encryption key is not set in session.");
            return null;
        }
    
        $key = $_SESSION['session_transport_token']; // Use session key
        $iv = "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10"; // Static IV
    
        // Decrypt the base64-encoded string
        $encryptedData = base64_decode($encryptedBase64);
        $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    
        // Decode JSON string into an object (by omitting the second parameter)
        return json_decode($decryptedData); // Returns an object
    }
    
}