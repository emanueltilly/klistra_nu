<?php
class Encryption
{
    private $cipher = "AES-256-CBC";
    private $key;
    private $iv;

    public function __construct($key)
    {
        $this->key = hash("sha256", $key, true);
        $this->iv = random_bytes(16);
    }

    public function encrypt($data)
    {
        $encrypted = openssl_encrypt(
            $data,
            $this->cipher,
            $this->key,
            0,
            $this->iv
        );
        $encoded = base64_encode($encrypted . "::" . $this->iv);
        return $encoded;
    }

    public function decrypt($data)
    {
        $decoded = base64_decode($data);
        list($encrypted, $iv) = explode("::", $decoded, 2);
        $decrypted = openssl_decrypt(
            $encrypted,
            $this->cipher,
            $this->key,
            0,
            $iv
        );
        return $decrypted;
    }
}
