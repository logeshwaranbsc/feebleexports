<?php

namespace App\Models;

use App\Core\Database;

class Media
{
    private static string $storagePath = BASE_PATH . '/storage/uploads.json';

    public static function getAll(): array
    {
        if (Database::isConnected()) {
            try {
                $items = Database::fetchAll("SELECT * FROM uploads ORDER BY created_at DESC");
                if (!empty($items)) {
                    return $items;
                }
            } catch (\Throwable $e) {
                // Ignore DB error if uploads table doesn't exist yet
            }
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists(self::$storagePath)) {
            return [];
        }

        $data = json_decode(file_get_contents(self::$storagePath), true);
        return is_array($data) ? $data : [];
    }

    public static function saveRecord(array $record): void
    {
        $id = 'UPL-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 4);
        $record['id'] = $id;

        if (Database::isConnected()) {
            try {
                $sql = "INSERT INTO uploads (id, name, url, filename, path, size, mime_type, storage, created_at) 
                        VALUES (:id, :name, :url, :filename, :path, :size, :mime_type, :storage, :created_at)";
                Database::execute($sql, [
                    'id' => $id,
                    'name' => $record['name'] ?? '',
                    'url' => $record['url'] ?? '',
                    'filename' => $record['filename'] ?? '',
                    'path' => $record['path'] ?? '',
                    'size' => $record['size'] ?? 0,
                    'mime_type' => $record['mime_type'] ?? '',
                    'storage' => $record['storage'] ?? 'local',
                    'created_at' => $record['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            } catch (\Throwable $e) {
                error_log("Failed to insert into uploads DB table: " . $e->getMessage());
            }
        }

        // Always save to JSON fallback
        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $items = file_exists(self::$storagePath)
            ? json_decode(file_get_contents(self::$storagePath), true) ?: []
            : [];

        array_unshift($items, $record);
        file_put_contents(self::$storagePath, json_encode($items, JSON_PRETTY_PRINT));
    }

    public static function deleteRecord(string $id): bool
    {
        if (Database::isConnected()) {
            try {
                Database::execute("DELETE FROM uploads WHERE id = :id", ['id' => $id]);
            } catch (\Throwable $e) {
                // proceed
            }
        }

        if (file_exists(self::$storagePath)) {
            $items = json_decode(file_get_contents(self::$storagePath), true) ?: [];
            $filtered = array_filter($items, fn($item) => ($item['id'] ?? '') !== $id);
            file_put_contents(self::$storagePath, json_encode(array_values($filtered), JSON_PRETTY_PRINT));
        }

        return true;
    }
}
