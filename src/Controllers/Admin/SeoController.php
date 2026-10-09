<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\Seo;

class SeoController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $seoData = Seo::getAll();

        View::render('admin/seo/index', [
            'title' => 'SEO Management - FEEBLE EXPORTS Admin',
            'currentPage' => 'seo',
            'seoData' => $seoData,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function update(): void
    {
        Auth::requireAdmin();

        $seoPayload = $_POST['seo'] ?? [];
        $success = Seo::saveAll($seoPayload);

        Auth::startSession();
        if ($success) {
            $_SESSION['flash_success'] = 'SEO settings updated successfully!';
        } else {
            $_SESSION['flash_error'] = 'Failed to update SEO settings. Please try again.';
        }

        Response::redirect('/admin/seo');
    }
}
