<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Blog;

class DashboardController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $enquiryStats = Enquiry::getStats();
        $recentEnquiries = array_slice(Enquiry::getAll(), 0, 5);
        $totalProducts = count(Product::getAll());
        $totalBlogs = count(Blog::getAll());

        View::render('admin/dashboard', [
            'title' => 'Dashboard - FEEBLE EXPORTS Admin',
            'currentPage' => 'dashboard',
            'enquiryStats' => $enquiryStats,
            'recentEnquiries' => $recentEnquiries,
            'totalProducts' => $totalProducts,
            'totalBlogs' => $totalBlogs,
            'admin' => Auth::user()
        ], true, 'admin');
    }
}
