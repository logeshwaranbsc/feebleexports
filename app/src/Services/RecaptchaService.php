<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class RecaptchaService
{
    private const DEFAULT_SITE_KEY = '6LdaE-8tAAAAADt8YKT2Ndk8iILBvld1JCCkzqLN';
    private const DEFAULT_SECRET_KEY = '6LdaE-8tAAAAAC13EIrBgJt34LtRajh5w3twf4My';

    public static function getSiteKey(): string
    {
        Database::loadEnv();
        return getenv('RECAPTCHA_SITE_KEY') ?: ($_ENV['RECAPTCHA_SITE_KEY'] ?? self::DEFAULT_SITE_KEY);
    }

    public static function getSecretKey(): string
    {
        Database::loadEnv();
        return getenv('RECAPTCHA_SECRET_KEY') ?: ($_ENV['RECAPTCHA_SECRET_KEY'] ?? self::DEFAULT_SECRET_KEY);
    }

    /**
     * Verify a reCAPTCHA v2 token against Google's siteverify endpoint.
     *
     * @param string|null $responseToken
     * @param string|null $remoteIp
     * @return array{success: bool, message?: string, error_codes?: array}
     */
    public static function verify(?string $responseToken, ?string $remoteIp = null): array
    {
        $token = trim((string)$responseToken);
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Please check the CAPTCHA box to confirm you are not a robot.'
            ];
        }

        $secret = self::getSecretKey();
        $ip = $remoteIp ?: ($_SERVER['REMOTE_ADDR'] ?? '');

        $postFields = http_build_query([
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip
        ]);

        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $rawResponse = null;

        // Try cURL first
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $rawResponse = curl_exec($ch);
            curl_close($ch);
        }

        // Fallback to file_get_contents stream context
        if (!$rawResponse) {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $postFields,
                    'timeout' => 10
                ]
            ]);
            $rawResponse = @file_get_contents($url, false, $context);
        }

        if (!$rawResponse) {
            // If outbound connection to Google fails (e.g. strict firewall), allow through with warning
            error_log('Warning: Unable to reach Google reCAPTCHA verification endpoint.');
            return [
                'success' => true,
                'message' => 'CAPTCHA verification bypassed due to network connectivity.'
            ];
        }

        $decoded = json_decode((string)$rawResponse, true);

        if (!empty($decoded['success'])) {
            return [
                'success' => true
            ];
        }

        $errorCodes = $decoded['error-codes'] ?? [];

        // If testing on localhost and domain mismatch occurs (key registered for production domain only)
        $isLocalhost = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'], true)
            || str_starts_with($_SERVER['HTTP_HOST'] ?? '', 'localhost:')
            || str_starts_with($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1:');

        if ($isLocalhost && in_array('domain-mismatch', $errorCodes, true)) {
            error_log('Notice: reCAPTCHA domain-mismatch permitted on local development environment.');
            return [
                'success' => true,
                'warning' => 'Local development domain bypass.'
            ];
        }

        return [
            'success' => false,
            'message' => 'reCAPTCHA verification failed. Please try checking the box again.',
            'error_codes' => $errorCodes
        ];
    }
}
