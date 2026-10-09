<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;

class ProfileController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $adminUser = Auth::getAdminUser();

        View::render('admin/profile/index', [
            'title' => 'Admin Profile - FEEBLE EXPORTS Admin',
            'currentPage' => 'profile',
            'adminUser' => $adminUser,
            'admin' => Auth::user(),
            'errors' => $_SESSION['form_errors'] ?? []
        ], true, 'admin');

        unset($_SESSION['form_errors']);
    }

    public function update(): void
    {
        Auth::requireAdmin();

        $currentAdmin = Auth::getAdminUser();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Full Name is required.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid Email address is required.';
        }

        // Password change handling
        if (!empty($newPassword)) {
            if (empty($currentPassword) || !password_verify($currentPassword, $currentAdmin['password'] ?? '')) {
                $errors['current_password'] = 'Current password is incorrect.';
            }
            if (strlen($newPassword) < 6) {
                $errors['new_password'] = 'New password must be at least 6 characters.';
            }
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'New passwords do not match.';
            }
        }

        if (!empty($errors)) {
            Auth::startSession();
            $_SESSION['form_errors'] = $errors;
            Response::redirect('/admin/profile');
            return;
        }

        $currentAdmin['name'] = htmlspecialchars($name);
        $currentAdmin['email'] = htmlspecialchars($email);
        if (!empty($username)) {
            $currentAdmin['username'] = htmlspecialchars($username);
        }

        if (!empty($newPassword)) {
            $currentAdmin['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        Auth::saveAdminUser($currentAdmin);

        Auth::startSession();
        $_SESSION['admin_user']['name'] = $currentAdmin['name'];
        $_SESSION['admin_user']['email'] = $currentAdmin['email'];
        $_SESSION['flash_success'] = 'Profile updated successfully!';

        Response::redirect('/admin/profile');
    }
}
