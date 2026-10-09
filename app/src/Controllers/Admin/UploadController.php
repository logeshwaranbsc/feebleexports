<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Services\SupabaseStorageService;
use App\Models\Media;

class UploadController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Show Media & Supabase S3 Upload Manager page.
     */
    public function index(): void
    {
        $status = SupabaseStorageService::getStatus();
        $mediaList = Media::getAll();

        View::renderAdmin('admin/uploads/index', [
            'title' => 'Supabase S3 Media & File Manager - FEEBLE EXPORTS',
            'currentPage' => 'uploads',
            'status' => $status,
            'mediaList' => $mediaList,
            'admin' => Auth::user()
        ]);
    }

    /**
     * Standard POST form file upload handler.
     */
    public function upload(): void
    {
        if (empty($_FILES['file'])) {
            $_SESSION['flash_error'] = 'No file was selected for upload.';
            Response::redirect('/admin/uploads');
            return;
        }

        $folder = $_POST['folder'] ?? 'uploads';
        $res = SupabaseStorageService::uploadFile($_FILES['file'], $folder);

        if ($res['success']) {
            $msg = $res['message'];
            if (!empty($res['warning'])) {
                $msg .= ' (' . $res['warning'] . ')';
            }
            $_SESSION['flash_success'] = $msg;
        } else {
            $_SESSION['flash_error'] = $res['error'] ?? 'Upload failed.';
        }

        Response::redirect('/admin/uploads');
    }

    /**
     * AJAX JSON Upload Endpoint (Used by Product/Blog forms & inline uploader).
     */
    public function apiUpload(): void
    {
        ob_start();
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (empty($_FILES['file'])) {
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'error' => 'No file was uploaded.'
                ]);
                exit;
            }

            $folder = $_POST['folder'] ?? 'uploads';
            $res = SupabaseStorageService::uploadFile($_FILES['file'], $folder);

            ob_clean();
            echo json_encode($res);
            exit;
        } catch (\Throwable $e) {
            ob_clean();
            echo json_encode([
                'success' => false,
                'error' => 'Server Upload Error: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Delete upload record.
     */
    public function delete(): void
    {
        $id = $_POST['id'] ?? '';
        if ($id) {
            Media::deleteRecord($id);
            $_SESSION['flash_success'] = 'Media record removed.';
        }
        Response::redirect('/admin/uploads');
    }
}
