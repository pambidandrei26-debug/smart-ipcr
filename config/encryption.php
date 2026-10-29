<?php
/**
 * Encryption Helper for Sensitive Data
 * 
 * Used to encrypt/decrypt API keys and other sensitive data
 * stored in the database
 */

// Load encryption key from environment
require_once __DIR__ . '/database.php';

define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'ipcr_default_key_change_this_now');
define('ENCRYPTION_METHOD', 'AES-256-CBC');

/**
 * Encrypt sensitive data
 * 
 * @param string $data Plain text data to encrypt
 * @return string Base64 encoded encrypted data
 */
function encryptData(string $data): string {
    if (empty($data)) {
        return '';
    }
    
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    
    // Combine encrypted data and IV, then base64 encode
    return base64_encode($encrypted . '::' . $iv);
}

/**
 * Decrypt sensitive data
 * 
 * @param string $data Base64 encoded encrypted data
 * @return string Plain text decrypted data
 */
function decryptData(string $data): string {
    if (empty($data)) {
        return '';
    }
    
    try {
        // Decode and split
        $decoded = base64_decode($data);
        if ($decoded === false) {
            return '';
        }
        
        $parts = explode('::', $decoded, 2);
        if (count($parts) !== 2) {
            // Old format or invalid - return as is
            return $data;
        }
        
        list($encrypted, $iv) = $parts;
        $decrypted = openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        
        return $decrypted !== false ? $decrypted : '';
    } catch (Exception $e) {
        error_log('Decryption failed: ' . $e->getMessage());
        return '';
    }
}

/**
 * Check if data is already encrypted
 * 
 * @param string $data Data to check
 * @return bool True if encrypted format
 */
function isEncrypted(string $data): bool {
    if (empty($data)) {
        return false;
    }
    
    $decoded = base64_decode($data, true);
    if ($decoded === false) {
        return false;
    }
    
    return strpos($decoded, '::') !== false;
}
