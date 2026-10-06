<?php

namespace App\Models;

class Contact
{
    private static string $storagePath = BASE_PATH . '/storage/messages.json';

    public static function save(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Your Name is required.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid Email Address is required.';
        }

        if (empty($data['message'])) {
            $errors['message'] = 'Message content cannot be empty.';
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
            'id' => 'MSG-' . uniqid(),
            'name' => htmlspecialchars(trim($data['name'])),
            'email' => htmlspecialchars(trim($data['email'])),
            'phone' => htmlspecialchars(trim($data['phone'] ?? '')),
            'country' => htmlspecialchars(trim($data['country'] ?? '')),
            'message' => htmlspecialchars(trim($data['message'])),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $existing[] = $record;
        file_put_contents(self::$storagePath, json_encode($existing, JSON_PRETTY_PRINT));

        return [
            'success' => true,
            'message' => 'Thank you for reaching out to FEEBLE EXPORTS! We have received your message and will respond shortly.',
            'msg_id' => $record['id']
        ];
    }
}
