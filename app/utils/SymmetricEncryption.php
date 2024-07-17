<?php

namespace App\Utils;

use Exception;

class SymmetricEncryption {
    private $key;
    private $cipher = 'AES-256-CBC';

    public function __construct() {
        $this->key = getenv('ENCRYPTION_KEY');
        if ($this->key === false) {
            throw new Exception('Encryption key not found in environment variables');
        }
    }

    public function encryptString(string $data): string {
        $encryptedData = openssl_encrypt($data, $this->cipher, $this->key);
        if ($encryptedData === false) {
            throw new Exception('Encryption failed');
        }
        return base64_encode($encryptedData);
    }

    public function decryptString(string $data): string {
        $encryptedData = base64_decode($data);
        $decryptedData = openssl_decrypt($encryptedData, $this->cipher, $this->key);
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
