<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\Enquiry;

class EnquiryController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $allEnquiries = Enquiry::getAll();
        $statusFilter = $_GET['status'] ?? 'all';
        $typeFilter = $_GET['type'] ?? 'all';
        $searchQuery = trim($_GET['search'] ?? '');

        $filtered = array_filter($allEnquiries, function ($item) use ($statusFilter, $typeFilter, $searchQuery) {
            if ($statusFilter !== 'all' && ($item['status'] ?? 'new') !== $statusFilter) {
                return false;
            }
            if ($typeFilter !== 'all' && ($item['type'] ?? 'contact') !== $typeFilter) {
                return false;
            }
            if (!empty($searchQuery)) {
                $haystack = strtolower(($item['name'] ?? '') . ' ' . ($item['email'] ?? '') . ' ' . ($item['country'] ?? '') . ' ' . ($item['id'] ?? ''));
                if (strpos($haystack, strtolower($searchQuery)) === false) {
                    return false;
                }
            }
            return true;
        });

        View::render('admin/enquiries/index', [
            'title' => 'Enquiry Management - FEEBLE EXPORTS Admin',
            'currentPage' => 'enquiries',
            'enquiries' => array_values($filtered),
            'stats' => Enquiry::getStats(),
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'searchQuery' => $searchQuery,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function show(string $id): void
    {
        Auth::requireAdmin();

        $enquiry = Enquiry::getById($id);

        if (!$enquiry) {
            http_response_code(404);
            View::render('404', ['title' => 'Enquiry Not Found'], true, 'admin');
            return;
        }

        View::render('admin/enquiries/show', [
            'title' => 'Enquiry ' . $enquiry['id'] . ' - FEEBLE EXPORTS Admin',
            'currentPage' => 'enquiries',
            'enquiry' => $enquiry,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function updateStatus(string $id): void
    {
        Auth::requireAdmin();

        $status = $_POST['status'] ?? 'new';
        $success = Enquiry::updateStatus($id, $status);

        if ($success) {
            Auth::startSession();
            $_SESSION['flash_success'] = "Enquiry {$id} status updated to " . ucfirst($status) . ".";
        }

        Response::redirect('/admin/enquiries/' . $id);
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();

        Enquiry::delete($id);
        Auth::startSession();
        $_SESSION['flash_success'] = "Enquiry {$id} deleted successfully.";

        Response::redirect('/admin/enquiries');
    }
}
