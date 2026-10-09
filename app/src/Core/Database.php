<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;
    private static bool $connectionAttempted = false;

    public static function loadEnv(): void
    {
        $envFile = BASE_PATH . '/.env';
        if (!file_exists($envFile) && file_exists(dirname(BASE_PATH) . '/.env')) {
            $envFile = dirname(BASE_PATH) . '/.env';
        }
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) {
                    continue;
                }
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $name = trim($parts[0]);
                    $value = trim($parts[1], " \t\n\r\0\x0B\"'");
                    $_ENV[$name] = $value;
                    putenv("{$name}={$value}");
                }
            }
        }
    }

    public static function isConnected(): bool
    {
        return self::getConnection() !== null;
    }

    public static function getConnection(): ?PDO
    {
        if (self::$connectionAttempted) {
            return self::$pdo;
        }

        self::$connectionAttempted = true;
        self::loadEnv();

        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '5432';
        $dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'postgres';
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'postgres';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

        if (str_contains($host, 'YOUR_SUPABASE_ID')) {
            self::$pdo = null;
            return null;
        }

        // Try connecting with standard PDO settings
        $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'], true);
        $sslMode = $isLocal ? '' : ';sslmode=require';

        try {
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}{$sslMode}";
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return self::$pdo;
        } catch (PDOException $e) {
            // If SSL failed for remote, retry without explicit SSL mode
            if (!$isLocal) {
                try {
                    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                    self::$pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    return self::$pdo;
                } catch (PDOException $e2) {
                    error_log('Database connection failed: ' . $e2->getMessage());
                }
            }
            error_log('Database connection failed: ' . $e->getMessage());
            self::$pdo = null;
            return null;
        }
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $pdo = self::getConnection();
        if (!$pdo) return [];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $pdo = self::getConnection();
        if (!$pdo) return null;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $pdo = self::getConnection();
        if (!$pdo) return false;
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
