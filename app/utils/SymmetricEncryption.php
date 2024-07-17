<?php

namespace App\Utils;

use Exception;

class SymmetricEncryption {
    private $key;
    private $cipher = 'AES-256-CBC';
    private $ivLength;

    public function __construct() {
        $this->key = getenv('ENCRYPTION_KEY');
        if ($this->key === false) {
            throw new Exception('Encryption key not found in environment variables');
        }
        $this->ivLength = openssl_cipher_iv_length($this->cipher);
    }

    public function encryptString(string $data): string {
        $iv = openssl_random_pseudo_bytes($this->ivLength);
        $encryptedData = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        if ($encryptedData === false) {
            throw new Exception('Encryption failed');
        }
        return base64_encode($iv . $encryptedData);
    }

    public function decryptString(string $data): string {
        $data = base64_decode($data);
        $iv = substr($data, 0, $this->ivLength);
        $encryptedData = substr($data, $this->ivLength);
        $decryptedData = openssl_decrypt($encryptedData, $this->cipher, $this->key, 0, $iv);
        if ($decryptedData === false) {
            throw new Exception('Decryption failed');
        }
        return $decryptedData;
    }

    public function encryptArray(array $data): array {
        return array_map([$this, 'encryptString'], $data);
    }

    public function decryptArray(array $data): array {
        return array_map([$this, 'decryptString'], $data);
    }
}
