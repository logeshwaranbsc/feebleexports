<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;

use App\Services\RecaptchaService;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            Response::redirect('/admin');
        }

        View::render('admin/login', [
            'title' => 'Admin Login - FEEBLE EXPORTS',
            'error' => $_SESSION['login_error'] ?? null
        ], false);

        unset($_SESSION['login_error']);
    }

    public function login(): void
    {
        $recaptchaToken = $_POST['g-recaptcha-response'] ?? null;
        if ($recaptchaToken !== null) {
            $recaptchaResult = RecaptchaService::verify($recaptchaToken);
            if (!$recaptchaResult['success']) {
                Auth::startSession();
                $_SESSION['login_error'] = $recaptchaResult['message'];
                Response::redirect('/admin/login');
                return;
            }
        }

        $identifier = $_POST['email'] ?? $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($identifier, $password)) {
            Response::redirect('/admin');
        } else {
            Auth::startSession();
            $_SESSION['login_error'] = 'Invalid email/username or password. Please try again.';
            Response::redirect('/admin/login');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect('/admin/login');
    }
}
