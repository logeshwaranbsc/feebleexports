<?php

namespace App\Models;

class Quote
{
    private static string $storagePath = BASE_PATH . '/storage/quotes.json';

    public static function save(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Full Name is required.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid Email Address is required.';
        }

        if (empty($data['product'])) {
            $errors['product'] = 'Please select a product category.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $existing = file_exists(self::$storagePath) 
            ? json_decode(file_get_contents(self::$storagePath), true) ?: [] 
            : [];

        $record = [
            'id' => 'Q-' . uniqid(),
            'name' => htmlspecialchars(trim($data['name'])),
            'email' => htmlspecialchars(trim($data['email'])),
            'phone' => htmlspecialchars(trim($data['phone'] ?? '')),
            'country' => htmlspecialchars(trim($data['country'] ?? '')),
            'product' => htmlspecialchars(trim($data['product'])),
            'quantity' => htmlspecialchars(trim($data['quantity'] ?? '100')),
            'custom_size' => htmlspecialchars(trim($data['custom_size'] ?? '')),
            'notes' => htmlspecialchars(trim($data['notes'] ?? '')),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $existing[] = $record;
        file_put_contents(self::$storagePath, json_encode($existing, JSON_PRETTY_PRINT));

        return [
            'success' => true,
            'message' => 'Thank you! Your quote request has been received. Our export team will get back to you within 24 hours.',
            'quote_id' => $record['id']
        ];
    }
}
