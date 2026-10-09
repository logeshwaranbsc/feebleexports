<?php

namespace App\Models;

use App\Core\Database;

class Enquiry
{
    private static string $messagesPath = BASE_PATH . '/storage/messages.json';
    private static string $quotesPath = BASE_PATH . '/storage/quotes.json';

    public static function getAll(): array
    {
        $all = [];

        if (Database::isConnected()) {
            $messages = Database::fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");
            foreach ($messages as $m) {
                $m['type'] = 'contact';
                $m['type_label'] = 'Contact Inquiry';
                $m['status'] = $m['status'] ?? 'new';
                $all[] = $m;
            }

            $quotes = Database::fetchAll("SELECT * FROM quote_requests ORDER BY created_at DESC");
            foreach ($quotes as $q) {
                $q['type'] = 'quote';
                $q['type_label'] = 'Quote Request';
                $q['status'] = $q['status'] ?? 'new';
                $all[] = $q;
            }

            usort($all, function ($a, $b) {
                return strtotime($b['created_at'] ?? 'now') <=> strtotime($a['created_at'] ?? 'now');
            });

            if (!empty($all)) {
                return $all;
            }
        }

        // Fallback to local file storage
        if (file_exists(self::$messagesPath)) {
            $messages = json_decode(file_get_contents(self::$messagesPath), true) ?: [];
            foreach ($messages as $m) {
                $m['type'] = 'contact';
                $m['type_label'] = 'Contact Inquiry';
                $m['status'] = $m['status'] ?? 'new';
                $all[] = $m;
            }
        }

        if (file_exists(self::$quotesPath)) {
            $quotes = json_decode(file_get_contents(self::$quotesPath), true) ?: [];
            foreach ($quotes as $q) {
                $q['type'] = 'quote';
                $q['type_label'] = 'Quote Request';
                $q['status'] = $q['status'] ?? 'new';
                $all[] = $q;
            }
        }

        usort($all, function ($a, $b) {
            return strtotime($b['created_at'] ?? 'now') <=> strtotime($a['created_at'] ?? 'now');
        });

        return $all;
    }

    public static function getById(string $id): ?array
    {
        if (Database::isConnected()) {
            if (str_starts_with($id, 'MSG-')) {
                $m = Database::fetch("SELECT * FROM contact_messages WHERE id = :id", ['id' => $id]);
                if ($m) {
                    $m['type'] = 'contact';
                    $m['type_label'] = 'Contact Inquiry';
                    return $m;
                }
            } else if (str_starts_with($id, 'Q-')) {
                $q = Database::fetch("SELECT * FROM quote_requests WHERE id = :id", ['id' => $id]);
                if ($q) {
                    $q['type'] = 'quote';
                    $q['type_label'] = 'Quote Request';
                    return $q;
                }
            }
        }

        $all = self::getAll();
        foreach ($all as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    public static function updateStatus(string $id, string $status): bool
    {
        $allowed = ['new', 'contacted', 'closed'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        if (Database::isConnected()) {
            if (str_starts_with($id, 'MSG-')) {
                return Database::execute("UPDATE contact_messages SET status = :status, updated_at = NOW() WHERE id = :id", ['status' => $status, 'id' => $id]);
            } else if (str_starts_with($id, 'Q-')) {
                return Database::execute("UPDATE quote_requests SET status = :status, updated_at = NOW() WHERE id = :id", ['status' => $status, 'id' => $id]);
            }
        }

        if (str_starts_with($id, 'MSG-')) {
            return self::updateStatusInFile(self::$messagesPath, $id, $status);
        } else if (str_starts_with($id, 'Q-')) {
            return self::updateStatusInFile(self::$quotesPath, $id, $status);
        }

        return false;
    }

    private static function updateStatusInFile(string $filePath, string $id, string $status): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $items = json_decode(file_get_contents($filePath), true) ?: [];
        $updated = false;

        foreach ($items as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = $status;
                $item['updated_at'] = date('Y-m-d H:i:s');
                $updated = true;
                break;
            }
        }

        if ($updated) {
            file_put_contents(self::$storagePath, json_encode($items, JSON_PRETTY_PRINT));
        }

        return $updated;
    }

    public static function delete(string $id): bool
    {
        if (Database::isConnected()) {
            if (str_starts_with($id, 'MSG-')) {
                Database::execute("DELETE FROM contact_messages WHERE id = :id", ['id' => $id]);
            } else if (str_starts_with($id, 'Q-')) {
                Database::execute("DELETE FROM quote_requests WHERE id = :id", ['id' => $id]);
            }
        }

        if (str_starts_with($id, 'MSG-')) {
            return self::deleteFromFile(self::$messagesPath, $id);
        } else if (str_starts_with($id, 'Q-')) {
            return self::deleteFromFile(self::$quotesPath, $id);
        }

        return false;
    }

    private static function deleteFromFile(string $filePath, string $id): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $items = json_decode(file_get_contents($filePath), true) ?: [];
        $filtered = array_values(array_filter($items, fn($item) => $item['id'] !== $id));

        file_put_contents($filePath, json_encode($filtered, JSON_PRETTY_PRINT));
        return true;
    }

    public static function getStats(): array
    {
        $all = self::getAll();
        $total = count($all);
        $new = 0;
        $contacted = 0;
        $closed = 0;
        $quotesCount = 0;
        $contactCount = 0;

        foreach ($all as $item) {
            $status = $item['status'] ?? 'new';
            if ($status === 'new') $new++;
            elseif ($status === 'contacted') $contacted++;
            elseif ($status === 'closed') $closed++;

            if (($item['type'] ?? '') === 'quote') $quotesCount++;
            else $contactCount++;
        }

        return [
            'total' => $total,
            'new' => $new,
            'contacted' => $contacted,
            'closed' => $closed,
            'quotes' => $quotesCount,
            'contacts' => $contactCount
        ];
    }
}
