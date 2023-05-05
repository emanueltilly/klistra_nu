<?php
class GUID
{
    public function New()
    {
        $prefix = "user_"; // You can customize the prefix to your needs
        $unique_id = uniqid($prefix, true); // Generate a unique ID with more entropy
        $random = random_bytes(10); // Generate a random 10-byte string
        $hash = hash("sha256", $unique_id . $random); // Hash the unique ID and random string using SHA-256
        return $hash;
    }
}
