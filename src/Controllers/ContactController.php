<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Contact;

class ContactController
{
    public function submit(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $result = Contact::save($input);

        if ($result['success']) {
            Response::json($result, 200);
        } else {
            Response::json($result, 422);
        }
    }
}
