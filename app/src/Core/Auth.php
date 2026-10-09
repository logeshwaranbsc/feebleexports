<?php

namespace App\Core;

class Auth
{
    private static string $storagePath = BASE_PATH . '/storage/admin.json';

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getAdminUser(): array
    {
        if (Database::isConnected()) {
            $user = Database::fetch("SELECT * FROM admin_users ORDER BY id ASC LIMIT 1");
            if ($user) {
                return $user;
            }
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists(self::$storagePath)) {
            $defaultAdmin = [
                'username' => 'admin',
                'name' => 'Kavimayil Venkatachalam',
                'email' => 'admin@feebleexports.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'Proprietress',
                'location' => 'Namakkal, Tamil Nadu',
                'created_at' => date('Y-m-d H:i:s')
            ];
            file_put_contents(self::$storagePath, json_encode($defaultAdmin, JSON_PRETTY_PRINT));
            return $defaultAdmin;
        }

        $user = json_decode(file_get_contents(self::$storagePath), true);
        return is_array($user) ? $user : [];
    }

    public static function saveAdminUser(array $userData): bool
    {
        if (Database::isConnected()) {
            $sql = "UPDATE admin_users SET name = :name, email = :email, username = :username, password = :password, updated_at = NOW() WHERE username = :orig_username OR email = :orig_email";
            $success = Database::execute($sql, [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'username' => $userData['username'] ?? 'admin',
                'password' => $userData['password'],
                'orig_username' => $userData['username'] ?? 'admin',
                'orig_email' => $userData['email']
            ]);
            if ($success) return true;
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents(self::$storagePath, json_encode($userData, JSON_PRETTY_PRINT)) !== false;
    }

    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    public static function requireAdmin(): void
    {
        if (!self::check()) {
            Response::redirect('/admin/login');
        }
    }

    public static function attempt(string $emailOrUsername, string $password): bool
    {
        self::startSession();
        $admin = self::getAdminUser();

        $identifier = trim($emailOrUsername);
        if (empty($identifier) || empty($password)) {
            return false;
        }

        $matchesIdentifier = (
            strcasecmp($admin['email'] ?? '', $identifier) === 0 ||
            strcasecmp($admin['username'] ?? '', $identifier) === 0
        );

        if ($matchesIdentifier && password_verify($password, $admin['password'] ?? '')) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = [
                'name' => $admin['name'] ?? 'Admin',
                'email' => $admin['email'] ?? 'admin@feebleexports.com',
                'username' => $admin['username'] ?? 'admin',
                'role' => $admin['role'] ?? 'Proprietress'
            ];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_user']);
        session_destroy();
    }

    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['admin_user'] ?? null;
    }
}
