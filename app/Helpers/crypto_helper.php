<?php

/**
 * Global Crypto Helper (CodeIgniter 4)
 * 
 * Provides global encrypt_data() and decrypt_data() functions.
 * Load using helper('crypto');
 */

if (!function_exists('encrypt_data')) {
    /**
     * Enkripsi data (string/array/object) menjadi ciphertext aman
     *
     * @param mixed $data
     * @param string|null $secretKey
     * @return string
     */
    function encrypt_data(mixed $data, ?string $secretKey = null): string
    {
        if ($data === null || $data === '') {
            return '';
        }

        $key = $secretKey ?? 'MTF_HC_HELPDESK_SECURE_STORAGE_KEY_2026_#9981';
        $payload = is_array($data) || is_object($data) ? json_encode($data) : (string) $data;

        $salt = bin2hex(random_bytes(4));
        $combinedKey = $salt . $key . $salt;
        $keyLen = strlen($combinedKey);
        $payloadLen = strlen($payload);

        $output = '';
        for ($i = 0; $i < $payloadLen; $i++) {
            $keyByte = ord($combinedKey[$i % $keyLen]);
            $plainByte = ord($payload[$i]);
            $cipherByte = ($plainByte ^ $keyByte ^ (($i * 31 + 17) & 0xff)) & 0xff;
            $output .= chr($cipherByte);
        }

        return 'enc::' . $salt . '::' . base64_encode($output);
    }
}

if (!function_exists('decrypt_data')) {
    /**
     * Dekripsi ciphertext kembali ke data aslinya (array jika JSON, atau string)
     *
     * @param string $cipherText
     * @param string|null $secretKey
     * @return mixed
     */
    function decrypt_data(string $cipherText, ?string $secretKey = null): mixed
    {
        if (empty($cipherText)) {
            return null;
        }

        $key = $secretKey ?? 'MTF_HC_HELPDESK_SECURE_STORAGE_KEY_2026_#9981';

        $parts = explode('::', $cipherText);
        if (count($parts) !== 3 || $parts[0] !== 'enc') {
            $json = json_decode($cipherText, true);
            return $json !== null ? $json : $cipherText;
        }

        $salt = $parts[1];
        $binary = base64_decode($parts[2]);
        if ($binary === false) {
            return null;
        }

        $combinedKey = $salt . $key . $salt;
        $keyLen = strlen($combinedKey);
        $binLen = strlen($binary);

        $output = '';
        for ($i = 0; $i < $binLen; $i++) {
            $keyByte = ord($combinedKey[$i % $keyLen]);
            $cipherByte = ord($binary[$i]);
            $plainByte = ($cipherByte ^ $keyByte ^ (($i * 31 + 17) & 0xff)) & 0xff;
            $output .= chr($plainByte);
        }

        $json = json_decode($output, true);
        return $json !== null ? $json : $output;
    }
}
