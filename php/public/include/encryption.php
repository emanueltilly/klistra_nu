<?php
class Encryption
{
    private $cipher = "AES-256-GCM";
    private $key;

    public function __construct($key)
    {
        $this->key = hash("sha256", $key, true);
    }

    public function encrypt($data)
    {
        $iv = random_bytes(16);
        $tag = '';
        $encrypted = openssl_encrypt(
            $data,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        $encoded = base64_encode($encrypted . "::" . $iv . "::" . $tag);
        return $encoded;
    }

    public function decrypt($data)
    {
        $decoded = base64_decode($data);
        list($encrypted, $iv, $tag) = explode("::", $decoded, 3);
        $decrypted = openssl_decrypt(
            $encrypted,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        return $decrypted;
    }
}
