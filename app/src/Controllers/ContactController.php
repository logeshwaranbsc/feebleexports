<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Contact;
use App\Services\RecaptchaService;

class ContactController
{
    public function submit(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        // Verify reCAPTCHA token
        $recaptchaToken = $input['g-recaptcha-response'] ?? null;
        $recaptchaResult = RecaptchaService::verify($recaptchaToken);
        if (!$recaptchaResult['success']) {
            Response::json([
                'success' => false,
                'errors' => ['captcha' => $recaptchaResult['message']]
            ], 422);
            return;
        }

        $result = Contact::save($input);

        if ($result['success']) {
            Response::json($result, 200);
        } else {
            Response::json($result, 422);
        }
    }
}
